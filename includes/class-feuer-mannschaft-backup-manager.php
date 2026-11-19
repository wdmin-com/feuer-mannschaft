<?php

if (! defined('ABSPATH')) {
    exit;
}

class Feuer_Mannschaft_Backup_Manager {
    const MAX_ARCHIVE_UPLOAD_BYTES = 104857600;
    const MAX_ARCHIVE_FILE_COUNT = 5000;
    const MAX_ARCHIVE_UNCOMPRESSED_BYTES = 268435456;
    const ARCHIVE_TOKEN_LENGTH = 16;

    private $logger;

    public function __construct($logger = null) {
        $this->logger = $logger;
    }

    public function register_hooks() {
        add_action('admin_init', array($this, 'ensure_storage'));
        add_action('admin_post_feuer_mannschaft_create_backup', array($this, 'handle_create_request'));
        add_action('admin_post_feuer_mannschaft_delete_backup', array($this, 'handle_delete_request'));
        add_action('admin_post_feuer_mannschaft_download_backup', array($this, 'handle_download_request'));
        add_action('admin_post_feuer_mannschaft_restore_backup', array($this, 'handle_restore_request'));
        add_action('admin_post_feuer_mannschaft_upload_backup', array($this, 'handle_upload_request'));
    }

    public function ensure_storage($force = false) {
        if (! $force && ! is_admin()) {
            return;
        }

        $directory = $this->get_backup_dir();

        if ('' === $directory) {
            return;
        }

        if (! file_exists($directory)) {
            wp_mkdir_p($directory);
        }

        $index_file = trailingslashit($directory) . 'index.php';
        if (! file_exists($index_file)) {
            file_put_contents($index_file, "<?php\n// Silence is golden.\n");
        }

        $htaccess_file = trailingslashit($directory) . '.htaccess';
        $htaccess_content = "Options -Indexes\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n";
        if (! file_exists($htaccess_file) || (string) file_get_contents($htaccess_file) !== $htaccess_content) {
            file_put_contents($htaccess_file, $htaccess_content);
        }

        $web_config_file = trailingslashit($directory) . 'web.config';
        $web_config_content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<configuration>\n  <system.webServer>\n    <security>\n      <requestFiltering>\n        <hiddenSegments>\n          <add segment=\"feuer-mannschaft-backups\" />\n        </hiddenSegments>\n      </requestFiltering>\n    </security>\n  </system.webServer>\n</configuration>\n";
        if (! file_exists($web_config_file) || (string) file_get_contents($web_config_file) !== $web_config_content) {
            file_put_contents($web_config_file, $web_config_content);
        }
    }

    public function get_archives() {
        $directory = $this->get_backup_dir();

        if (! is_dir($directory)) {
            return array();
        }

        $files = glob(trailingslashit($directory) . '*.zip');

        if (empty($files)) {
            return array();
        }

        rsort($files, SORT_NATURAL);

        $archives = array();

        foreach ($files as $file) {
            $archives[] = array(
                'name'         => wp_basename($file),
                'size'         => size_format((int) filesize($file), 2),
                'size_bytes'   => (int) filesize($file),
                'modified'     => filemtime($file),
                'modified_human' => wp_date(get_option('date_format') . ' ' . get_option('time_format'), (int) filemtime($file)),
            );
        }

        return $archives;
    }

    public function handle_create_request() {
        $this->verify_manage_options_request('feuer_mannschaft_create_backup');

        try {
            $archive_name = $this->create_archive();

            $this->redirect_with_notice(
                array(
                    'feuer_mannschaft_settings_notice' => 'backup_created',
                    'archive'                    => rawurlencode($archive_name),
                )
            );
        } catch (Exception $exception) {
            $this->redirect_with_notice(
                array(
                    'feuer_mannschaft_settings_notice'  => 'backup_failed',
                    'feuer_mannschaft_settings_message' => rawurlencode($exception->getMessage()),
                )
            );
        }
    }

    public function handle_delete_request() {
        $this->verify_manage_options_request('feuer_mannschaft_delete_backup');

        $filename = isset($_REQUEST['archive']) ? wp_unslash($_REQUEST['archive']) : '';
        $path     = $this->get_archive_path($filename);

        if (! $path) {
            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_missing'));
        }

        $deleted = unlink($path);

        if ($deleted && $this->logger instanceof Feuer_Mannschaft_Logger) {
            $this->logger->log(
                'backup_deleted',
                'backup',
                0,
                wp_basename($path),
                array(
                    array(
                        'message' => __('Archiv wurde aus dem Speicher entfernt.', 'feuer-mannschaft'),
                    ),
                )
            );
        }

        $this->redirect_with_notice(
            array(
                'feuer_mannschaft_settings_notice' => $deleted ? 'backup_deleted' : 'backup_failed',
            )
        );
    }

    public function handle_download_request() {
        $this->verify_manage_options_request('feuer_mannschaft_download_backup');

        $filename = isset($_GET['archive']) ? wp_unslash($_GET['archive']) : '';
        $path     = $this->get_archive_path($filename);

        if (! $path) {
            wp_die(esc_html__('Archiv wurde nicht gefunden.', 'feuer-mannschaft'));
        }

        if ($this->logger instanceof Feuer_Mannschaft_Logger) {
            $this->logger->log(
                'backup_downloaded',
                'backup',
                0,
                wp_basename($path),
                array(
                    array(
                        'message' => __('Archiv wurde über den geschützten Download ausgeliefert.', 'feuer-mannschaft'),
                    ),
                )
            );
        }

        nocache_headers();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . wp_basename($path) . '"');
        header('Content-Length: ' . (string) filesize($path));
        if (false === readfile($path)) {
            wp_die(esc_html__('Das Archiv konnte nicht ausgeliefert werden.', 'feuer-mannschaft'));
        }
        exit;
    }

    public function handle_upload_request() {
        $this->verify_manage_options_request('feuer_mannschaft_upload_backup');
        $this->ensure_storage();

        $upload_error = isset($_FILES['feuer_mannschaft_backup_upload']['error'])
            ? (int) $_FILES['feuer_mannschaft_backup_upload']['error']
            : UPLOAD_ERR_NO_FILE;
        $tmp_name = isset($_FILES['feuer_mannschaft_backup_upload']['tmp_name'])
            ? (string) $_FILES['feuer_mannschaft_backup_upload']['tmp_name']
            : '';
        $file_size = isset($_FILES['feuer_mannschaft_backup_upload']['size'])
            ? (int) $_FILES['feuer_mannschaft_backup_upload']['size']
            : 0;

        if (UPLOAD_ERR_NO_FILE === $upload_error || '' === $tmp_name || ! is_uploaded_file($tmp_name)) {
            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_upload_missing'));
        }

        if (UPLOAD_ERR_OK !== $upload_error) {
            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_upload_failed'));
        }

        $original_name = isset($_FILES['feuer_mannschaft_backup_upload']['name']) ? sanitize_file_name(wp_unslash($_FILES['feuer_mannschaft_backup_upload']['name'])) : 'feuer-mannschaft-upload.zip';
        $extension     = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if ('zip' !== $extension) {
            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_upload_invalid'));
        }

        if ($file_size <= 0) {
            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_upload_invalid'));
        }

        if ($file_size > self::MAX_ARCHIVE_UPLOAD_BYTES) {
            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_upload_too_large'));
        }

        try {
            $this->validate_archive_file($tmp_name);
        } catch (Exception $exception) {
            $this->redirect_with_notice(
                array(
                    'feuer_mannschaft_settings_notice'  => 'backup_upload_invalid',
                    'feuer_mannschaft_settings_message' => rawurlencode($exception->getMessage()),
                )
            );
        }

        $filename = sprintf(
            'feuer-mannschaft-upload-%1$s-%2$s.zip',
            gmdate('Ymd-His'),
            $this->generate_archive_token()
        );

        $path = trailingslashit($this->get_backup_dir()) . $filename;

        if (! move_uploaded_file($tmp_name, $path)) {
            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_upload_failed'));
        }

        if ($this->logger instanceof Feuer_Mannschaft_Logger) {
            $this->logger->log(
                'backup_uploaded',
                'backup',
                0,
                $filename,
                array(
                    array(
                        'message' => sprintf(__('Datei %s wurde in das Archivlager hochgeladen.', 'feuer-mannschaft'), $original_name),
                    ),
                )
            );
        }

        $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_uploaded'));
    }

    public function handle_restore_request() {
        $this->verify_manage_options_request('feuer_mannschaft_restore_backup');

        $filename = isset($_REQUEST['archive']) ? wp_unslash($_REQUEST['archive']) : '';
        $path     = $this->get_archive_path($filename);

        if (! $path) {
            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_missing'));
        }

        try {
            $this->validate_archive_file($path);
            $safety_archive = $this->create_archive();
            $components = isset($_POST['components'])
                ? array_values(array_intersect(array_map('sanitize_key', (array) wp_unslash($_POST['components'])), array('members', 'gallery', 'private_documents', 'mentions', 'settings', 'logs')))
                : array('members', 'gallery', 'private_documents', 'mentions', 'settings', 'logs');
            if (empty($components)) {
                throw new Exception(__('Wählen Sie mindestens einen Datenbereich für die Wiederherstellung aus.', 'feuer-mannschaft'));
            }

            $summary = $this->restore_archive($path, $components);

            if ($this->logger instanceof Feuer_Mannschaft_Logger) {
                $this->logger->log(
                    'backup_restored',
                    'backup',
                    0,
                    wp_basename($path),
                    array(
                        array(
                            'message' => sprintf(
                                __('Wiederhergestellt: %1$d Teilnehmer, %2$d Verknüpfungen, %3$d Logeinträge.', 'feuer-mannschaft'),
                                (int) $summary['members'],
                                (int) $summary['mentions'],
                                (int) $summary['logs']
                            ),
                        ),
                        array(
                            'message' => sprintf(__('Sicherungsarchiv vor Wiederherstellung: %s', 'feuer-mannschaft'), $safety_archive),
                        ),
                    )
                );
            }

            if (! empty($summary['failed_members']) || ! empty($summary['unmatched_members'])) {
                $this->redirect_with_notice(
                    array(
                        'feuer_mannschaft_settings_notice'  => 'backup_restored_partial',
                        'feuer_mannschaft_settings_message' => rawurlencode(
                            sprintf(
                                __('Teilweise wiederhergestellt: %1$d Profile importiert, %2$d Profile fehlgeschlagen oder nicht zugeordnet. Das Sicherheitsarchiv wurde erstellt.', 'feuer-mannschaft'),
                                (int) $summary['members'],
                                (int) $summary['failed_members'] + (int) $summary['unmatched_members']
                            )
                        ),
                    )
                );
            }

            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_restored'));
        } catch (Exception $exception) {
            $this->redirect_with_notice(
                array(
                    'feuer_mannschaft_settings_notice'  => 'backup_failed',
                    'feuer_mannschaft_settings_message' => rawurlencode($exception->getMessage()),
                )
            );
        }
    }

    public function create_archive() {
        $this->ensure_storage();

        $temp_directory = $this->create_temp_directory();
        $files_directory = trailingslashit($temp_directory) . 'files';

        wp_mkdir_p($files_directory);

        try {
            $manifest = $this->build_manifest($files_directory);
            $written = file_put_contents(
                trailingslashit($temp_directory) . 'manifest.json',
                wp_json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );

            if (false === $written) {
                throw new Exception('manifest.json konnte nicht geschrieben werden.');
            }

            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';

            $archive_name = 'feuer-mannschaft-backup-' . gmdate('Ymd-His') . '-' . $this->generate_archive_token() . '.zip';
            $archive_path = trailingslashit($this->get_backup_dir()) . $archive_name;
            $archive      = new PclZip($archive_path);
            $result       = $archive->create($temp_directory, PCLZIP_OPT_REMOVE_PATH, $temp_directory);

            if (0 === $result) {
                throw new Exception($archive->errorInfo(true));
            }

            if ($this->logger instanceof Feuer_Mannschaft_Logger) {
                $this->logger->log(
                    'backup_created',
                    'backup',
                    0,
                    $archive_name,
                    array(
                        array(
                            'message' => __('Archiv der Plugin-Daten wurde erstellt.', 'feuer-mannschaft'),
                        ),
                    )
                );
            }

            return $archive_name;
        } finally {
            $this->delete_directory($temp_directory);
        }
    }

    public function preview_archive($filename) {
        $archive_path = $this->get_archive_path($filename);
        if (! $archive_path) {
            return array('error' => __('Archiv wurde nicht gefunden.', 'feuer-mannschaft'));
        }

        $temp_directory = '';
        try {
            $this->validate_archive_file($archive_path);
            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
            $temp_directory = $this->create_temp_directory();
            $archive = new PclZip($archive_path);
            if (0 === $archive->extract(PCLZIP_OPT_PATH, $temp_directory)) {
                throw new Exception($archive->errorInfo(true));
            }
            $manifest_path = trailingslashit($temp_directory) . 'manifest.json';
            $manifest = file_exists($manifest_path) ? json_decode((string) file_get_contents($manifest_path), true) : null;
            if (! is_array($manifest)) {
                throw new Exception(__('Das Archiv enthält keine gültige Manifestdatei.', 'feuer-mannschaft'));
            }
            $this->validate_manifest_contents($manifest, trailingslashit($temp_directory) . 'files');

            $photos = 0;
            $private_documents = 0;
            foreach (isset($manifest['members']) && is_array($manifest['members']) ? $manifest['members'] : array() as $member) {
                $photos += isset($member['gallery']) && is_array($member['gallery']) ? count($member['gallery']) : 0;
                $private_documents += isset($member['private_documents']) && is_array($member['private_documents']) ? count($member['private_documents']) : 0;
            }

            return array(
                'name'     => wp_basename($archive_path),
                'members'  => isset($manifest['members']) && is_array($manifest['members']) ? count($manifest['members']) : 0,
                'photos'   => $photos,
                'private_documents' => $private_documents,
                'mentions' => isset($manifest['mentions']) && is_array($manifest['mentions']) ? count($manifest['mentions']) : 0,
                'logs'     => isset($manifest['logs']) && is_array($manifest['logs']) ? count($manifest['logs']) : 0,
                'settings' => ! empty($manifest['settings']) && is_array($manifest['settings']),
            );
        } catch (Exception $exception) {
            return array('error' => $exception->getMessage());
        } finally {
            if ($temp_directory) {
                $this->delete_directory($temp_directory);
            }
        }
    }

    public function restore_archive($archive_path, $components = array('members', 'gallery', 'private_documents', 'mentions', 'settings', 'logs')) {
        $components = array_values(array_intersect(array_map('sanitize_key', (array) $components), array('members', 'gallery', 'private_documents', 'mentions', 'settings', 'logs')));
        $restore_members  = in_array('members', $components, true);
        $restore_gallery  = in_array('gallery', $components, true);
        $restore_private_documents = in_array('private_documents', $components, true);
        $restore_mentions = in_array('mentions', $components, true);
        $restore_settings = in_array('settings', $components, true);
        $restore_logs     = in_array('logs', $components, true);
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';

        $temp_directory = $this->create_temp_directory();

        try {
            $archive = new PclZip($archive_path);
            $result  = $archive->extract(PCLZIP_OPT_PATH, $temp_directory);

            if (0 === $result) {
                throw new Exception($archive->errorInfo(true));
            }

            $manifest_path = trailingslashit($temp_directory) . 'manifest.json';

            if (! file_exists($manifest_path)) {
                throw new Exception(__('Im Archiv fehlt die Datei manifest.json.', 'feuer-mannschaft'));
            }

            $manifest = json_decode((string) file_get_contents($manifest_path), true);

            if (! is_array($manifest)) {
                throw new Exception(__('Das Archiv enthält eine ungültige manifest.json.', 'feuer-mannschaft'));
            }

            $this->validate_manifest_contents($manifest, trailingslashit($temp_directory) . 'files');

            $plugin = Feuer_Mannschaft_Plugin::instance();
            $restored_settings = null;

            if (isset($manifest['settings']) && is_array($manifest['settings']) && $plugin instanceof Feuer_Mannschaft_Plugin) {
                $restored_settings = $plugin->normalize_restored_settings($manifest['settings']);
            }

            $member_map          = array();
            $member_count        = 0;
            $failed_member_count = 0;
            $unmatched_member_count = 0;
            $mention_count       = 0;
            $restored_logs       = 0;

            $member_records  = isset($manifest['members']) && is_array($manifest['members']) ? $manifest['members'] : array();
            $mention_records = isset($manifest['mentions']) && is_array($manifest['mentions']) ? $manifest['mentions'] : array();
            $log_records     = isset($manifest['logs']) && is_array($manifest['logs']) ? $manifest['logs'] : array();

            foreach ($member_records as $member) {
                if ($restore_members && ! $restore_private_documents && isset($member['meta']) && is_array($member['meta'])) {
                    unset($member['meta']['_feuer_mannschaft_private_documents']);
                }
                $new_member_id = $restore_members
                    ? $this->restore_member_record($member)
                    : $this->find_existing_member_id($member['post']);

                if (! $new_member_id) {
                    if ($restore_members) {
                        $failed_member_count++;
                    } elseif ($restore_gallery || $restore_private_documents || $restore_mentions) {
                        $unmatched_member_count++;
                    }
                    continue;
                }

                $member_map[(int) $member['old_id']] = (int) $new_member_id;
                if ($restore_members) {
                    $member_count++;
                }
            }

            foreach ($restore_gallery ? $member_records : array() as $member) {
                if (empty($member_map[(int) $member['old_id']])) {
                    continue;
                }

                $this->restore_member_gallery($member_map[(int) $member['old_id']], $member, trailingslashit($temp_directory) . 'files');
            }

            foreach ($restore_private_documents ? $member_records : array() as $member) {
                $new_member_id = absint($member_map[(int) ($member['old_id'] ?? 0)] ?? 0);
                if ($new_member_id && array_key_exists('private_documents', $member)) { $this->restore_private_documents($new_member_id, $member, trailingslashit($temp_directory) . 'files'); }
            }

            foreach ($restore_mentions ? $mention_records : array() as $mention_record) {
                if ($this->restore_mention_record($mention_record, $member_map)) {
                    $mention_count++;
                }
            }

            if ($restore_logs && $this->logger instanceof Feuer_Mannschaft_Logger && ! empty($log_records)) {
                foreach ($log_records as $log_record) {
                    $details = isset($log_record['details']) && is_array($log_record['details']) ? $log_record['details'] : array();
                    $this->logger->log(
                        isset($log_record['action']) ? $log_record['action'] : 'backup_restored',
                        isset($log_record['entity_type']) ? $log_record['entity_type'] : 'backup',
                        isset($log_record['entity_id']) ? (int) $log_record['entity_id'] : 0,
                        isset($log_record['entity_label']) ? $log_record['entity_label'] : '',
                        $details,
                        isset($log_record['user_id']) ? (int) $log_record['user_id'] : 0,
                        isset($log_record['created_at']) ? $log_record['created_at'] : ''
                    );
                    $restored_logs++;
                }
            }

            if ($restore_settings && is_array($restored_settings)) {
                update_option(Feuer_Mannschaft_Plugin::OPTION_KEY, $restored_settings, false);
            }

            if ($plugin instanceof Feuer_Mannschaft_Plugin && method_exists($plugin, 'purge_plugin_cache')) {
                $plugin->purge_plugin_cache('backup_restored', array_values($member_map));
            }

            return array(
                'members'        => $member_count,
                'failed_members' => $failed_member_count,
                'unmatched_members' => $unmatched_member_count,
                'mentions'       => $mention_count,
                'logs'           => $restored_logs,
            );
        } finally {
            $this->delete_directory($temp_directory);
        }
    }

    private function build_manifest($files_directory) {
        $manifest = array(
            'generated_at' => current_time('mysql', true),
            'version'      => FEUER_MANNSCHAFT_VERSION,
            'site_url'     => home_url('/'),
            'settings'     => get_option(Feuer_Mannschaft_Plugin::OPTION_KEY, array()),
            'members'      => $this->export_members($files_directory),
            'mentions'     => $this->export_mentions(),
            'logs'         => $this->export_logs(),
        );

        return $manifest;
    }

    private function export_members($files_directory) {
        $posts = get_posts(
            array(
                'post_type'      => Feuer_Mannschaft_Plugin::POST_TYPE,
                'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'feuer_archived'),
                'posts_per_page' => -1,
                'orderby'        => 'ID',
                'order'          => 'ASC',
                'meta_query'     => array(
                    array(
                        'key'   => Feuer_Mannschaft_Plugin::PROFILE_META,
                        'value' => '1',
                    ),
                ),
            )
        );

        $members = array();

        foreach ($posts as $post) {
            if (! $post instanceof WP_Post) {
                continue;
            }

            $members[] = array(
                'old_id'       => (int) $post->ID,
                'post'         => array(
                    'post_author'   => (int) $post->post_author,
                    'post_date'     => $post->post_date,
                    'post_date_gmt' => $post->post_date_gmt,
                    'post_title'    => $post->post_title,
                    'post_name'     => $post->post_name,
                    'post_excerpt'  => $post->post_excerpt,
                    'post_content'  => $post->post_content,
                    'post_status'   => $post->post_status,
                ),
                'meta'         => $this->get_member_meta_export($post->ID),
                'category_ids' => wp_get_post_categories($post->ID, array('fields' => 'ids')),
                'categories'   => $this->export_member_categories($post->ID),
                'gallery'      => $this->export_gallery($post->ID, $files_directory),
                'private_documents' => $this->export_private_documents($post->ID, $files_directory),
            );
        }

        return $members;
    }

    private function export_member_categories($post_id) {
        $terms = get_the_terms($post_id, 'category');

        if (empty($terms) || is_wp_error($terms)) {
            return array();
        }

        $categories = array();

        foreach ($terms as $term) {
            if (! $term instanceof WP_Term) {
                continue;
            }

            $categories[] = array(
                'term_id' => (int) $term->term_id,
                'slug'    => (string) $term->slug,
                'name'    => (string) $term->name,
            );
        }

        return $categories;
    }

    private function export_gallery($post_id, $files_directory) {
        $gallery_ids = get_post_meta($post_id, Feuer_Mannschaft_Plugin::GALLERY_META, true);
        $gallery_ids = is_array($gallery_ids) ? array_map('absint', $gallery_ids) : array();
        $items       = array();

        foreach ($gallery_ids as $attachment_id) {
            $source_path = get_attached_file($attachment_id);

            if (! $source_path || ! file_exists($source_path)) {
                continue;
            }

            $relative_file = sprintf(
                '%1$d-%2$s',
                (int) $attachment_id,
                sanitize_file_name(wp_basename($source_path))
            );

            if (! copy($source_path, trailingslashit($files_directory) . $relative_file)) {
                continue;
            }

            $items[] = array(
                'old_attachment_id' => (int) $attachment_id,
                'file'              => $relative_file,
                'title'             => get_the_title($attachment_id),
                'caption'           => wp_get_attachment_caption($attachment_id),
                'description'       => get_post_field('post_content', $attachment_id),
                'alt'               => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
                'mime_type'         => get_post_mime_type($attachment_id),
            );
        }

        return $items;
    }

    private function export_private_documents($post_id, $files_directory) {
        $documents = (array) get_post_meta($post_id, '_feuer_mannschaft_private_documents', true);
        $private_dir = trailingslashit(dirname(untrailingslashit(ABSPATH))) . 'feuer-mannschaft-private';
        $items = array();
        foreach ($documents as $document) {
            if (! is_array($document) || empty($document['file'])) { continue; }
            $name = basename((string) $document['file']);
            if (! preg_match('/^[A-Za-z0-9_-]{16,64}\\.(?:pdf|jpe?g|png|webp)$/i', $name)) { continue; }
            $source = trailingslashit($private_dir) . $name;
            if (! is_readable($source) || 0 !== strpos(wp_normalize_path($source), wp_normalize_path($private_dir) . '/')) { continue; }
            $archive_name = 'private-' . absint($post_id) . '-' . wp_generate_password(12, false, false) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (! copy($source, trailingslashit($files_directory) . $archive_name)) { continue; }
            $document['file'] = $archive_name;
            $items[] = $document;
        }
        return $items;
    }

    private function restore_private_documents($member_id, $member, $files_directory) {
        $items = isset($member['private_documents']) && is_array($member['private_documents']) ? $member['private_documents'] : array();
        $directory = trailingslashit(dirname(untrailingslashit(ABSPATH))) . 'feuer-mannschaft-private';
        if (! is_dir($directory)) { wp_mkdir_p($directory); }
        if (! is_dir($directory) || ! wp_is_writable($directory)) { throw new Exception(__('Geschützter Speicher für Nachweise ist nicht beschreibbar.', 'feuer-mannschaft')); }
        if (! file_exists($directory . '/index.php')) { file_put_contents($directory . '/index.php', "<?php\n// Silence is golden.\n"); }
        if (! file_exists($directory . '/.htaccess')) { file_put_contents($directory . '/.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n"); }
        if (! file_exists($directory . '/web.config')) { file_put_contents($directory . '/web.config', '<?xml version="1.0"?><configuration><system.webServer><authorization><deny users="*" /></authorization></system.webServer></configuration>'); }
        foreach ((array) get_post_meta($member_id, '_feuer_mannschaft_private_documents', true) as $old) { if (is_array($old) && ! empty($old['file'])) { $old_path = trailingslashit($directory) . basename((string) $old['file']); if (is_file($old_path)) { wp_delete_file($old_path); } } }
        $restored = array();
        foreach ($items as $item) {
            if (! is_array($item) || empty($item['file'])) { continue; }
            $source_name = sanitize_file_name((string) $item['file']);
            if (! preg_match('/^private-[0-9]+-[A-Za-z0-9_-]{8,32}\\.(?:pdf|jpe?g|png|webp)$/i', $source_name)) { continue; }
            $source = trailingslashit($files_directory) . $source_name;
            if (! is_readable($source)) { throw new Exception(__('Ein interner Qualifikationsnachweis fehlt im Archiv.', 'feuer-mannschaft')); }
            $ext = strtolower(pathinfo($source_name, PATHINFO_EXTENSION)); $id = strtolower(wp_generate_password(32, false, false)); $target_name = $id . '.' . $ext;
            if (! copy($source, trailingslashit($directory) . $target_name)) { throw new Exception(__('Ein interner Qualifikationsnachweis konnte nicht wiederhergestellt werden.', 'feuer-mannschaft')); }
            $item['id'] = $id; $item['file'] = $target_name; $item['size'] = absint(filesize(trailingslashit($directory) . $target_name)); $restored[] = $item;
        }
        update_post_meta($member_id, '_feuer_mannschaft_private_documents', $restored);
    }

    private function export_mentions() {
        $settings   = get_option(Feuer_Mannschaft_Plugin::OPTION_KEY, array());
        $post_types = isset($settings['mention_post_types']) ? (array) $settings['mention_post_types'] : array('post');
        $records    = array();

        $posts = get_posts(
            array(
                'post_type'      => $post_types,
                'post_status'    => array('publish', 'private', 'draft', 'pending', 'future'),
                'posts_per_page' => -1,
                'orderby'        => 'ID',
                'order'          => 'ASC',
                'meta_query'     => array(
                    array(
                        'key'     => Feuer_Mannschaft_Mentions::META_KEY,
                        'compare' => 'EXISTS',
                    ),
                ),
            )
        );

        foreach ($posts as $post) {
            if (! $post instanceof WP_Post) {
                continue;
            }

            $selected = get_post_meta($post->ID, Feuer_Mannschaft_Mentions::META_KEY, true);
            $selected = is_array($selected) ? array_values(array_map('strval', $selected)) : array();

            if (empty($selected)) {
                continue;
            }

            $records[] = array(
                'post_id'            => (int) $post->ID,
                'post_type'          => $post->post_type,
                'post_name'          => $post->post_name,
                'post_title'         => $post->post_title,
                'mentioned_persons'  => $selected,
            );
        }

        return $records;
    }

    private function export_logs() {
        if (! $this->logger instanceof Feuer_Mannschaft_Logger) {
            return array();
        }

        $rows  = $this->logger->get_logs(array('limit' => 0));
        $items = array();

        foreach ($rows as $row) {
            $items[] = array(
                'created_at'   => $row->created_at,
                'user_id'      => (int) $row->user_id,
                'action'       => $row->action,
                'entity_type'  => $row->entity_type,
                'entity_id'    => (int) $row->entity_id,
                'entity_label' => $row->entity_label,
                'details'      => isset($row->details_array) ? $row->details_array : array(),
            );
        }

        return $items;
    }

    private function get_member_meta_export($post_id) {
        $all_meta = get_post_meta($post_id);
        $result   = array();

        foreach ($all_meta as $meta_key => $values) {
            if (Feuer_Mannschaft_Plugin::PROFILE_META === $meta_key || 0 === strpos($meta_key, Feuer_Mannschaft_Plugin::META_PREFIX)) {
                $result[$meta_key] = maybe_unserialize(isset($values[0]) ? $values[0] : '');
            }
        }

        return $result;
    }

    private function restore_member_record($member) {
        if (empty($member['post']) || ! is_array($member['post'])) {
            return 0;
        }

        $post_data   = $member['post'];
        $existing_id = $this->find_existing_member_id($post_data);

        $post_status = isset($post_data['post_status']) ? sanitize_key((string) $post_data['post_status']) : 'publish';
        if (in_array($post_status, array('feuer_mannschaft_arc', 'feuer_mannschaft_archived'), true)) {
            $post_status = Feuer_Mannschaft_Plugin::ARCHIVED_STATUS;
        }
        if (! in_array($post_status, array('publish', 'private', 'draft', 'pending', 'future', 'feuer_archived'), true)) {
            $post_status = 'draft';
        }

        $insert_data = array(
            'post_type'      => Feuer_Mannschaft_Plugin::POST_TYPE,
            'post_status'    => $post_status,
            'post_author'    => $this->sanitize_restored_author_id(isset($post_data['post_author']) ? $post_data['post_author'] : get_current_user_id()),
            'post_date'      => $this->sanitize_restored_mysql_date(isset($post_data['post_date']) ? $post_data['post_date'] : '', current_time('mysql')),
            'post_date_gmt'  => $this->sanitize_restored_mysql_date(isset($post_data['post_date_gmt']) ? $post_data['post_date_gmt'] : '', current_time('mysql', true)),
            'post_title'     => isset($post_data['post_title']) ? sanitize_text_field($post_data['post_title']) : '',
            'post_name'      => isset($post_data['post_name']) ? sanitize_title($post_data['post_name']) : '',
            'post_excerpt'   => isset($post_data['post_excerpt']) ? wp_kses_post($post_data['post_excerpt']) : '',
            'post_content'   => isset($post_data['post_content']) ? wp_kses_post($post_data['post_content']) : '',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        );

        if ($existing_id > 0) {
            $insert_data['ID'] = $existing_id;
            $post_id           = wp_update_post($insert_data, true);
        } else {
            $post_id = wp_insert_post($insert_data, true);
        }

        if (is_wp_error($post_id) || ! $post_id) {
            return 0;
        }

        update_post_meta($post_id, Feuer_Mannschaft_Plugin::PROFILE_META, 1);

        $member_meta = isset($member['meta']) && is_array($member['meta']) ? $member['meta'] : array();

        foreach ($member_meta as $meta_key => $meta_value) {
            $meta_key = (string) $meta_key;

            if (! $this->is_allowed_restored_member_meta_key($meta_key)) {
                continue;
            }

            $sanitized_meta = $this->sanitize_restored_member_meta_value($meta_key, $meta_value);

            if (null === $sanitized_meta) {
                delete_post_meta($post_id, $meta_key);
                continue;
            }

            update_post_meta($post_id, $meta_key, $sanitized_meta);
        }

        $category_ids = $this->resolve_member_category_ids($member);
        wp_set_post_categories($post_id, $category_ids, false);

        return (int) $post_id;
    }

    private function restore_member_gallery($member_id, $member, $files_directory) {
        $gallery_items = isset($member['gallery']) && is_array($member['gallery']) ? $member['gallery'] : array();
        $gallery_ids   = array();

        if (empty($gallery_items)) {
            $this->reset_member_gallery($member_id);
            $this->sync_member_default_thumbnail($member_id);
            return;
        }

        $plugin = class_exists('Feuer_Mannschaft_Plugin') ? Feuer_Mannschaft_Plugin::instance() : null;

        try {
            foreach ($gallery_items as $item) {
                if (empty($item['file'])) {
                    continue;
                }

                $source_path = trailingslashit($files_directory) . sanitize_file_name($item['file']);

                if (! file_exists($source_path)) {
                    throw new Exception(
                        sprintf(
                            __('Galeriedatei %s fehlt im Archiv.', 'feuer-mannschaft'),
                            sanitize_file_name((string) $item['file'])
                        )
                    );
                }

                $validated_file = $this->validate_gallery_source_file($source_path, $item);
                $source_path    = $validated_file['path'];
                $file_contents = file_get_contents($source_path);

                if (false === $file_contents) {
                    throw new Exception(
                        sprintf(
                            __('Galeriedatei %s konnte nicht gelesen werden.', 'feuer-mannschaft'),
                            sanitize_file_name((string) $item['file'])
                        )
                    );
                }

                if ($plugin instanceof Feuer_Mannschaft_Plugin) {
                    add_filter('upload_dir', array($plugin, 'filter_upload_dir'));
                }

                $upload = wp_upload_bits(wp_basename($source_path), null, (string) $file_contents);

                if ($plugin instanceof Feuer_Mannschaft_Plugin) {
                    remove_filter('upload_dir', array($plugin, 'filter_upload_dir'));
                }

                if (! empty($upload['error']) || empty($upload['file'])) {
                    throw new Exception(
                        sprintf(
                            __('Galeriedatei %s konnte nicht wiederhergestellt werden.', 'feuer-mannschaft'),
                            sanitize_file_name((string) $item['file'])
                        )
                    );
                }

                $attachment_id = wp_insert_attachment(
                    array(
                        'post_title'     => isset($item['title']) ? sanitize_text_field($item['title']) : wp_basename($source_path),
                        'post_excerpt'   => isset($item['caption']) ? sanitize_text_field($item['caption']) : '',
                        'post_content'   => isset($item['description']) ? wp_kses_post($item['description']) : '',
                        'post_mime_type' => $validated_file['mime_type'],
                        'post_status'    => 'inherit',
                    ),
                    $upload['file'],
                    $member_id
                );

                if (is_wp_error($attachment_id) || ! $attachment_id) {
                    throw new Exception(
                        sprintf(
                            __('Galeriedatei %s konnte nicht als Anhang gespeichert werden.', 'feuer-mannschaft'),
                            sanitize_file_name((string) $item['file'])
                        )
                    );
                }

                $metadata = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $metadata);

                if (! empty($item['alt'])) {
                    update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($item['alt']));
                }

                $gallery_ids[] = (int) $attachment_id;
            }
        } catch (Exception $exception) {
            foreach ($gallery_ids as $attachment_id) {
                wp_delete_attachment($attachment_id, true);
            }

            throw $exception;
        }

        if (! empty($gallery_ids)) {
            $this->reset_member_gallery($member_id);
            update_post_meta($member_id, Feuer_Mannschaft_Plugin::GALLERY_META, $gallery_ids);
            set_post_thumbnail($member_id, $gallery_ids[0]);
            delete_post_meta($member_id, Feuer_Mannschaft_Plugin::DEFAULT_IMAGE_MANAGED_META);
            delete_post_meta($member_id, Feuer_Mannschaft_Plugin::DEFAULT_IMAGE_OPT_OUT_META);
            return;
        }

        $this->reset_member_gallery($member_id);
        $this->sync_member_default_thumbnail($member_id);
    }

    private function restore_mention_record($record, $member_map) {
        if (empty($record['post_type']) || empty($record['mentioned_persons']) || ! is_array($record['mentioned_persons'])) {
            return false;
        }

        $target_post = get_page_by_path(isset($record['post_name']) ? $record['post_name'] : '', OBJECT, $record['post_type']);

        if (! $target_post instanceof WP_Post && ! empty($record['post_title'])) {
            $posts = get_posts(
                array(
                    'post_type'      => $record['post_type'],
                    'post_status'    => array('publish', 'private', 'draft', 'pending', 'future'),
                    'posts_per_page' => 20,
                    's'              => $record['post_title'],
                )
            );

            foreach ($posts as $candidate) {
                if ($candidate instanceof WP_Post && $candidate->post_title === $record['post_title']) {
                    $target_post = $candidate;
                    break;
                }
            }
        }

        if (! $target_post instanceof WP_Post) {
            return false;
        }

        $new_ids = array();

        foreach ($record['mentioned_persons'] as $old_member_id) {
            $old_member_id = absint($old_member_id);

            if (! empty($member_map[$old_member_id])) {
                $new_ids[] = (string) $member_map[$old_member_id];
            }
        }

        $new_ids = array_values(array_unique(array_filter($new_ids)));

        if (empty($new_ids)) {
            delete_post_meta($target_post->ID, Feuer_Mannschaft_Mentions::META_KEY);
        } else {
            update_post_meta($target_post->ID, Feuer_Mannschaft_Mentions::META_KEY, $new_ids);
        }

        return true;
    }

    private function find_existing_member_id($post_data) {
        $post_name = isset($post_data['post_name']) ? sanitize_title($post_data['post_name']) : '';

        if ('' !== $post_name) {
            $existing = get_posts(
                array(
                    'post_type'      => Feuer_Mannschaft_Plugin::POST_TYPE,
                    'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'feuer_archived'),
                    'posts_per_page' => 1,
                    'fields'         => 'ids',
                    'name'           => $post_name,
                    'meta_query'     => array(
                        array(
                            'key'   => Feuer_Mannschaft_Plugin::PROFILE_META,
                            'value' => '1',
                        ),
                    ),
                )
            );

            if (! empty($existing[0])) {
                return (int) $existing[0];
            }
        }

        if (! empty($post_data['post_title'])) {
            $existing = get_posts(
                array(
                    'post_type'      => Feuer_Mannschaft_Plugin::POST_TYPE,
                    'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'feuer_archived'),
                    'posts_per_page' => 20,
                    's'              => $post_data['post_title'],
                    'meta_query'     => array(
                        array(
                            'key'   => Feuer_Mannschaft_Plugin::PROFILE_META,
                            'value' => '1',
                        ),
                    ),
                )
            );

            foreach ($existing as $candidate_id) {
                $candidate = get_post($candidate_id);

                if ($candidate instanceof WP_Post && $candidate->post_title === $post_data['post_title']) {
                    return (int) $candidate->ID;
                }
            }
        }

        return 0;
    }

    private function resolve_member_category_ids($member) {
        $resolved_ids = array();
        $categories   = isset($member['categories']) && is_array($member['categories']) ? $member['categories'] : array();

        foreach ($categories as $category) {
            if (! is_array($category)) {
                continue;
            }

            $slug = isset($category['slug']) ? sanitize_title($category['slug']) : '';

            if ('' !== $slug) {
                $term = get_term_by('slug', $slug, 'category');

                if ($term instanceof WP_Term) {
                    $resolved_ids[] = (int) $term->term_id;
                    continue;
                }
            }

            $name = isset($category['name']) ? sanitize_text_field($category['name']) : '';

            if ('' !== $name) {
                $term = get_term_by('name', $name, 'category');

                if ($term instanceof WP_Term) {
                    $resolved_ids[] = (int) $term->term_id;
                }
            }
        }

        if (empty($resolved_ids) && empty($categories) && ! empty($member['category_ids']) && is_array($member['category_ids'])) {
            $resolved_ids = array_map('absint', $member['category_ids']);
        }

        return array_values(array_unique(array_filter($resolved_ids)));
    }

    private function reset_member_gallery($member_id) {
        // Detach existing media instead of deleting it: an attachment may also be used elsewhere.
        delete_post_meta($member_id, Feuer_Mannschaft_Plugin::GALLERY_META);
        delete_post_thumbnail($member_id);
        delete_post_meta($member_id, Feuer_Mannschaft_Plugin::DEFAULT_IMAGE_MANAGED_META);
    }

    private function sync_member_default_thumbnail($member_id) {
        if (! class_exists('Feuer_Mannschaft_Plugin')) {
            return;
        }

        $plugin = Feuer_Mannschaft_Plugin::instance();

        if ($plugin instanceof Feuer_Mannschaft_Plugin) {
            $plugin->sync_restored_member_default_thumbnail($member_id);
        }
    }

    private function verify_manage_options_request($nonce_action) {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('Sie haben keine Berechtigung fuer diese Aktion.', 'feuer-mannschaft'));
        }

        check_admin_referer($nonce_action);
    }

    private function redirect_with_notice($args) {
        wp_safe_redirect(
            add_query_arg(
                $args,
                admin_url('admin.php?page=feuer-mannschaft-settings&tab=archives')
            )
        );
        exit;
    }

    private function get_backup_dir() {
        $upload_dir = wp_upload_dir();

        if (empty($upload_dir['basedir'])) {
            return '';
        }

        return trailingslashit($upload_dir['basedir']) . Feuer_Mannschaft_Plugin::BACKUP_UPLOAD_SUBDIR;
    }

    private function get_archive_path($filename) {
        $filename  = sanitize_file_name(wp_basename((string) $filename));
        $directory = realpath($this->get_backup_dir());

        if ('' === $filename || 'zip' !== strtolower(pathinfo($filename, PATHINFO_EXTENSION)) || ! $directory) {
            return '';
        }

        $path = realpath(trailingslashit($directory) . $filename);

        if (! $path || 0 !== strpos($path, $directory) || ! file_exists($path)) {
            return '';
        }

        return $path;
    }

    private function create_temp_directory() {
        $upload_dir = wp_upload_dir();

        if (empty($upload_dir['basedir'])) {
            throw new Exception(__('Upload-Verzeichnis konnte nicht bestimmt werden.', 'feuer-mannschaft'));
        }

        $directory  = trailingslashit($upload_dir['basedir']) . Feuer_Mannschaft_Plugin::BACKUP_TEMP_PREFIX . wp_generate_uuid4();

        if (! wp_mkdir_p($directory)) {
            throw new Exception(__('Temporäres Verzeichnis konnte nicht erstellt werden.', 'feuer-mannschaft'));
        }

        return $directory;
    }

    private function delete_directory($directory) {
        if (! $directory || ! file_exists($directory)) {
            return;
        }

        $items = scandir($directory);

        if (! is_array($items)) {
            return;
        }

        foreach ($items as $item) {
            if ('.' === $item || '..' === $item) {
                continue;
            }

            $path = trailingslashit($directory) . $item;

            if (is_dir($path)) {
                $this->delete_directory($path);
                continue;
            }

            unlink($path);
        }

        rmdir($directory);
    }

    private function detect_mime_type($file_path) {
        if (function_exists('mime_content_type')) {
            return (string) mime_content_type($file_path);
        }

        $filetype = wp_check_filetype(wp_basename($file_path));

        return ! empty($filetype['type']) ? $filetype['type'] : 'application/octet-stream';
    }

    private function validate_archive_file($archive_path) {
        if (! is_string($archive_path) || '' === $archive_path || ! file_exists($archive_path)) {
            throw new Exception(__('Das Archiv wurde nicht gefunden.', 'feuer-mannschaft'));
        }

        require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';

        $archive = new PclZip($archive_path);
        $entries = $archive->listContent();

        if (0 === $entries) {
            throw new Exception($archive->errorInfo(true));
        }

        if (! is_array($entries) || empty($entries)) {
            throw new Exception(__('Das Archiv enthält keine lesbaren Daten.', 'feuer-mannschaft'));
        }

        if (count($entries) > self::MAX_ARCHIVE_FILE_COUNT) {
            throw new Exception(__('Das Archiv enthält zu viele Dateien.', 'feuer-mannschaft'));
        }

        $has_manifest = false;
        $total_size   = 0;

        foreach ($entries as $entry) {
            $filename = isset($entry['filename']) ? wp_normalize_path((string) $entry['filename']) : '';

            if ('' === $filename) {
                throw new Exception(__('Das Archiv enthält einen ungültigen Dateinamen.', 'feuer-mannschaft'));
            }

            if (preg_match('#(^|/)\.\.(?:/|$)#', $filename) || preg_match('#^(?:[a-zA-Z]:|/|\\\\)#', $filename)) {
                throw new Exception(__('Das Archiv enthält unsichere Pfadangaben.', 'feuer-mannschaft'));
            }

            if ('manifest.json' === ltrim($filename, './')) {
                $has_manifest = true;
            }

            $entry_size = isset($entry['size']) ? max(0, (int) $entry['size']) : 0;
            $total_size += $entry_size;

            if ($total_size > self::MAX_ARCHIVE_UNCOMPRESSED_BYTES) {
                throw new Exception(__('Das Archiv ist für die Wiederherstellung zu groß.', 'feuer-mannschaft'));
            }
        }

        if (! $has_manifest) {
            throw new Exception(__('Im Archiv fehlt die Datei manifest.json.', 'feuer-mannschaft'));
        }
    }

    private function validate_manifest_contents($manifest, $files_directory) {
        $members  = isset($manifest['members']) && is_array($manifest['members']) ? $manifest['members'] : array();
        $mentions = isset($manifest['mentions']) && is_array($manifest['mentions']) ? $manifest['mentions'] : array();
        $logs     = isset($manifest['logs']) && is_array($manifest['logs']) ? $manifest['logs'] : array();

        foreach ($members as $member) {
            if (! is_array($member) || empty($member['post']) || ! is_array($member['post'])) {
                throw new Exception(__('Das Archiv enthält einen ungültigen Teilnehmerdatensatz.', 'feuer-mannschaft'));
            }

            if (empty($member['old_id']) || ! is_numeric($member['old_id'])) {
                throw new Exception(__('Das Archiv enthält einen Teilnehmer ohne gültige Referenz-ID.', 'feuer-mannschaft'));
            }

            $gallery_items = isset($member['gallery']) && is_array($member['gallery']) ? $member['gallery'] : array();

            foreach ($gallery_items as $item) {
                if (empty($item['file'])) {
                    throw new Exception(__('Das Archiv enthält einen unvollständigen Galerieeintrag.', 'feuer-mannschaft'));
                }

                $source_path = trailingslashit($files_directory) . sanitize_file_name((string) $item['file']);

                if (! file_exists($source_path)) {
                    throw new Exception(
                        sprintf(
                            __('Galeriedatei %s fehlt im Archiv.', 'feuer-mannschaft'),
                            sanitize_file_name((string) $item['file'])
                        )
                    );
                }

                $this->validate_gallery_source_file($source_path, $item);
            }

            foreach ((array) ($member['private_documents'] ?? array()) as $document) {
                if (! is_array($document)) { throw new Exception(__('Das Archiv enthält einen ungültigen internen Nachweis.', 'feuer-mannschaft')); }
                $name = sanitize_file_name((string) ($document['file'] ?? ''));
                $source = trailingslashit($files_directory) . $name;
                $verified = is_readable($source) ? wp_check_filetype_and_ext($source, $name) : array();
                if (! preg_match('/^private-[0-9]+-[A-Za-z0-9_-]{8,32}\\.(?:pdf|jpe?g|png|webp)$/i', $name) || empty($verified['type']) || filesize($source) > 10 * MB_IN_BYTES) {
                    throw new Exception(__('Das Archiv enthält einen fehlenden oder ungültigen internen Nachweis.', 'feuer-mannschaft'));
                }
            }
        }

        foreach ($mentions as $record) {
            if (! is_array($record)) {
                throw new Exception(__('Das Archiv enthält einen ungültigen Erwähnungsdatensatz.', 'feuer-mannschaft'));
            }
        }

        foreach ($logs as $record) {
            if (! is_array($record)) {
                throw new Exception(__('Das Archiv enthält einen ungültigen Logdatensatz.', 'feuer-mannschaft'));
            }
        }
    }

    private function generate_archive_token() {
        return strtolower(wp_generate_password(self::ARCHIVE_TOKEN_LENGTH, false, false));
    }

    private function sanitize_restored_author_id($author_id) {
        $author_id = absint($author_id);

        if ($author_id > 0 && get_userdata($author_id)) {
            return $author_id;
        }

        return get_current_user_id();
    }

    private function sanitize_restored_mysql_date($value, $fallback) {
        $value = trim((string) $value);

        if (preg_match('/\A\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\z/', $value)) {
            return $value;
        }

        return $fallback;
    }

    private function is_allowed_restored_member_meta_key($meta_key) {
        if (Feuer_Mannschaft_Plugin::PROFILE_META === $meta_key || Feuer_Mannschaft_Plugin::GALLERY_META === $meta_key) {
            return false;
        }

        return in_array(
            $meta_key,
            array(
                Feuer_Mannschaft_Plugin::META_PREFIX . 'first_name',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'last_name',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'job_title',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'entry_date',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'admin_note',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'rank',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'function',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'education',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'lehrgang',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'contact_fields',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'custom_fields',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'sort_order',
                Feuer_Mannschaft_Plugin::META_PREFIX . 'description',
                Feuer_Mannschaft_Plugin::SERVICE_HISTORY_META,
                Feuer_Mannschaft_Plugin::ARCHIVE_REASON_META,
                Feuer_Mannschaft_Plugin::SERVICE_END_DATE_META,
                Feuer_Mannschaft_Plugin::META_PREFIX . 'legacy_source_id',
                Feuer_Mannschaft_Plugin::DEFAULT_IMAGE_MANAGED_META,
                Feuer_Mannschaft_Plugin::DEFAULT_IMAGE_OPT_OUT_META,
                Feuer_Mannschaft_Plugin::INVITE_URL_META,
                Feuer_Mannschaft_Plugin::EXCLUDE_FROM_COUNT_META,
            ),
            true
        );
    }

    private function sanitize_restored_member_meta_value($meta_key, $value) {
        switch ($meta_key) {
            case Feuer_Mannschaft_Plugin::META_PREFIX . 'first_name':
            case Feuer_Mannschaft_Plugin::META_PREFIX . 'last_name':
            case Feuer_Mannschaft_Plugin::META_PREFIX . 'job_title':
            case Feuer_Mannschaft_Plugin::META_PREFIX . 'rank':
                return sanitize_text_field((string) $value);

            case Feuer_Mannschaft_Plugin::META_PREFIX . 'entry_date':
                return preg_match('/\A\d{4}\z/', (string) $value) ? (string) absint($value) : '';

            case Feuer_Mannschaft_Plugin::META_PREFIX . 'admin_note':
            case Feuer_Mannschaft_Plugin::ARCHIVE_REASON_META:
                return sanitize_textarea_field((string) $value);

            case Feuer_Mannschaft_Plugin::SERVICE_END_DATE_META:
                return preg_match('/\A\d{4}-\d{2}-\d{2}\z/', (string) $value) ? (string) $value : null;

            case Feuer_Mannschaft_Plugin::META_PREFIX . 'description':
                return wp_kses_post((string) $value);

            case Feuer_Mannschaft_Plugin::META_PREFIX . 'sort_order':
            case Feuer_Mannschaft_Plugin::META_PREFIX . 'legacy_source_id':
                return absint($value);

            case Feuer_Mannschaft_Plugin::META_PREFIX . 'function':
            case Feuer_Mannschaft_Plugin::META_PREFIX . 'education':
            case Feuer_Mannschaft_Plugin::META_PREFIX . 'lehrgang':
                return $this->sanitize_restored_text_list($value);

            case Feuer_Mannschaft_Plugin::META_PREFIX . 'contact_fields':
            case Feuer_Mannschaft_Plugin::META_PREFIX . 'custom_fields':
            case Feuer_Mannschaft_Plugin::SERVICE_HISTORY_META:
                return $this->sanitize_restored_array($value);

            case Feuer_Mannschaft_Plugin::INVITE_URL_META:
                $url = esc_url_raw((string) $value, array('http', 'https', 'mailto', 'tel'));
                return '' === $url ? null : $url;

            case Feuer_Mannschaft_Plugin::DEFAULT_IMAGE_MANAGED_META:
            case Feuer_Mannschaft_Plugin::DEFAULT_IMAGE_OPT_OUT_META:
            case Feuer_Mannschaft_Plugin::EXCLUDE_FROM_COUNT_META:
                return empty($value) ? null : 1;
        }

        return null;
    }

    private function sanitize_restored_text_list($value) {
        $values = is_array($value) ? $value : array($value);
        $values = array_map(
            function ($item) {
                return sanitize_text_field((string) $item);
            },
            $values
        );

        return array_values(array_unique(array_filter($values, 'strlen')));
    }

    private function sanitize_restored_array($value) {
        if (! is_array($value)) {
            return array();
        }

        $sanitized = array();

        foreach ($value as $key => $item) {
            $key = sanitize_key((string) $key);

            if ('' === $key) {
                continue;
            }

            if (is_array($item)) {
                $sanitized[$key] = $this->sanitize_restored_array($item);
                continue;
            }

            $sanitized[$key] = sanitize_text_field((string) $item);
        }

        return $sanitized;
    }

    private function validate_gallery_source_file($source_path, $item = array()) {
        $allowed_mimes = array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png'          => 'image/png',
            'gif'          => 'image/gif',
            'webp'         => 'image/webp',
        );
        $filename = wp_basename($source_path);
        $filetype = wp_check_filetype($filename, $allowed_mimes);

        if (empty($filetype['ext']) || empty($filetype['type'])) {
            throw new Exception(sprintf(__('Galeriedatei %s hat keinen erlaubten Bildtyp.', 'feuer-mannschaft'), sanitize_file_name($filename)));
        }

        $checked = function_exists('wp_check_filetype_and_ext')
            ? wp_check_filetype_and_ext($source_path, $filename, $allowed_mimes)
            : array();

        if (is_array($checked) && (empty($checked['ext']) || empty($checked['type']))) {
            throw new Exception(sprintf(__('Galeriedatei %s stimmt nicht mit einem erlaubten Bildtyp ueberein.', 'feuer-mannschaft'), sanitize_file_name($filename)));
        }

        $detected_mime = $this->detect_mime_type($source_path);
        if (0 !== strpos($detected_mime, 'image/')) {
            throw new Exception(sprintf(__('Galeriedatei %s ist kein Bild.', 'feuer-mannschaft'), sanitize_file_name($filename)));
        }

        if (! empty($item['mime_type']) && sanitize_text_field((string) $item['mime_type']) !== $filetype['type']) {
            throw new Exception(sprintf(__('Galeriedatei %s hat unerwartete MIME-Metadaten.', 'feuer-mannschaft'), sanitize_file_name($filename)));
        }

        return array(
            'path'      => $source_path,
            'mime_type' => $filetype['type'],
        );
    }
}

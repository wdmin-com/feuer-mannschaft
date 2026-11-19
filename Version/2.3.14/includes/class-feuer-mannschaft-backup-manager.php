<?php

if (! defined('ABSPATH')) {
    exit;
}

class Feuer_Mannschaft_Backup_Manager {
    const MAX_ARCHIVE_UPLOAD_BYTES = 104857600;
    const MAX_ARCHIVE_FILE_COUNT = 5000;
    const MAX_ARCHIVE_UNCOMPRESSED_BYTES = 268435456;

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
        if (! file_exists($htaccess_file)) {
            file_put_contents($htaccess_file, "Options -Indexes\n<FilesMatch \"\\.(zip|json)$\">\nDeny from all\n</FilesMatch>\n");
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

        $filename = isset($_GET['archive']) ? wp_unslash($_GET['archive']) : '';
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
            wp_generate_password(6, false, false)
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

        $filename = isset($_GET['archive']) ? wp_unslash($_GET['archive']) : '';
        $path     = $this->get_archive_path($filename);

        if (! $path) {
            $this->redirect_with_notice(array('feuer_mannschaft_settings_notice' => 'backup_missing'));
        }

        try {
            $this->validate_archive_file($path);
            $summary = $this->restore_archive($path);

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

            $archive_name = 'feuer-mannschaft-backup-' . gmdate('Ymd-His') . '.zip';
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

    public function restore_archive($archive_path) {
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

            if (isset($manifest['settings']) && is_array($manifest['settings'])) {
                update_option(Feuer_Mannschaft_Plugin::OPTION_KEY, $manifest['settings'], false);
            }

            $member_map     = array();
            $member_count   = 0;
            $mention_count  = 0;
            $restored_logs  = 0;

            $member_records  = isset($manifest['members']) && is_array($manifest['members']) ? $manifest['members'] : array();
            $mention_records = isset($manifest['mentions']) && is_array($manifest['mentions']) ? $manifest['mentions'] : array();
            $log_records     = isset($manifest['logs']) && is_array($manifest['logs']) ? $manifest['logs'] : array();

            foreach ($member_records as $member) {
                $new_member_id = $this->restore_member_record($member);

                if (! $new_member_id) {
                    continue;
                }

                $member_map[(int) $member['old_id']] = (int) $new_member_id;
                $member_count++;
            }

            foreach ($member_records as $member) {
                if (empty($member_map[(int) $member['old_id']])) {
                    continue;
                }

                $this->restore_member_gallery($member_map[(int) $member['old_id']], $member, trailingslashit($temp_directory) . 'files');
            }

            foreach ($mention_records as $mention_record) {
                if ($this->restore_mention_record($mention_record, $member_map)) {
                    $mention_count++;
                }
            }

            if ($this->logger instanceof Feuer_Mannschaft_Logger && ! empty($log_records)) {
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

            return array(
                'members'  => $member_count,
                'mentions' => $mention_count,
                'logs'     => $restored_logs,
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
                'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'feuer_mannschaft_archived'),
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
            if (Feuer_Mannschaft_Plugin::PROFILE_META === $meta_key || Feuer_Mannschaft_Plugin::GALLERY_META === $meta_key || 0 === strpos($meta_key, Feuer_Mannschaft_Plugin::META_PREFIX)) {
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

        $insert_data = array(
            'post_type'      => Feuer_Mannschaft_Plugin::POST_TYPE,
            'post_status'    => isset($post_data['post_status']) ? $post_data['post_status'] : 'publish',
            'post_author'    => isset($post_data['post_author']) ? (int) $post_data['post_author'] : get_current_user_id(),
            'post_date'      => isset($post_data['post_date']) ? $post_data['post_date'] : current_time('mysql'),
            'post_date_gmt'  => isset($post_data['post_date_gmt']) ? $post_data['post_date_gmt'] : current_time('mysql', true),
            'post_title'     => isset($post_data['post_title']) ? $post_data['post_title'] : '',
            'post_name'      => isset($post_data['post_name']) ? $post_data['post_name'] : '',
            'post_excerpt'   => isset($post_data['post_excerpt']) ? $post_data['post_excerpt'] : '',
            'post_content'   => isset($post_data['post_content']) ? $post_data['post_content'] : '',
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
            update_post_meta($post_id, $meta_key, maybe_unserialize($meta_value));
        }

        $category_ids = $this->resolve_member_category_ids($member);
        wp_set_post_categories($post_id, $category_ids, false);

        return (int) $post_id;
    }

    private function restore_member_gallery($member_id, $member, $files_directory) {
        $gallery_items = isset($member['gallery']) && is_array($member['gallery']) ? $member['gallery'] : array();
        $gallery_ids   = array();

        $this->reset_member_gallery($member_id);

        if (empty($gallery_items)) {
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
                        'post_mime_type' => isset($item['mime_type']) && $item['mime_type']
                            ? sanitize_text_field($item['mime_type'])
                            : $this->detect_mime_type($source_path),
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

            delete_post_meta($member_id, Feuer_Mannschaft_Plugin::GALLERY_META);
            delete_post_thumbnail($member_id);

            throw $exception;
        }

        if (! empty($gallery_ids)) {
            update_post_meta($member_id, Feuer_Mannschaft_Plugin::GALLERY_META, $gallery_ids);
            set_post_thumbnail($member_id, $gallery_ids[0]);
            delete_post_meta($member_id, Feuer_Mannschaft_Plugin::DEFAULT_IMAGE_MANAGED_META);
            delete_post_meta($member_id, Feuer_Mannschaft_Plugin::DEFAULT_IMAGE_OPT_OUT_META);
            return;
        }

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
                    'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'feuer_mannschaft_archived'),
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
                    'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'feuer_mannschaft_archived'),
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
        $gallery_ids = get_post_meta($member_id, Feuer_Mannschaft_Plugin::GALLERY_META, true);
        $gallery_ids = is_array($gallery_ids) ? array_values(array_unique(array_map('absint', $gallery_ids))) : array();

        foreach ($gallery_ids as $attachment_id) {
            if ($attachment_id > 0) {
                wp_delete_attachment($attachment_id, true);
            }
        }

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
}

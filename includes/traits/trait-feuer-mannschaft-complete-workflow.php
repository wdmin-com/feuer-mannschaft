<?php
/** Workflow, readiness, Einsatzberichte relations and private member documents. */
if (! defined('ABSPATH')) { exit; }

trait Feuer_Mannschaft_Complete_Workflow {
    private $profile_audit_snapshots = array();
    private $workflow_status_updating = false;

    public function register_workflow_pages() {
        $parent = 'edit.php?post_type=' . Feuer_Mannschaft_Plugin::POST_TYPE;
        add_submenu_page($parent, 'Einsatzbereitschaft', 'Einsatzbereitschaft', 'manage_feuer_members', 'feuer-mannschaft-readiness', array($this, 'render_readiness_page'));
    }

    public function register_workflow_meta_box() {
        add_meta_box('feuer-member-workflow', 'Mitgliedsstatus, Einsatzberichte und Dokumente', array($this, 'render_workflow_meta_box'), Feuer_Mannschaft_Plugin::POST_TYPE, 'normal', 'default');
    }

    public function render_workflow_meta_box($post) {
        $state = sanitize_key((string) get_post_meta($post->ID, '_feuer_mannschaft_lifecycle', true));
        if (! $state) { $state = 'feuer_archived' === $post->post_status ? 'archived' : ('publish' === $post->post_status ? 'active' : 'onboarding'); }
        $documents = (array) get_post_meta($post->ID, '_feuer_mannschaft_private_documents', true);
        $state_labels = array('onboarding'=>'Aufnahme / Prüfung','active'=>'Aktiv','offboarding'=>'Austritt in Bearbeitung','archived'=>'Ausgeschieden / archiviert');
        $missing_fields = $this->missing_publication_fields($post->ID);
        $is_ready = empty($missing_fields);
        wp_nonce_field('feuer_member_workflow_' . $post->ID, 'feuer_member_workflow_nonce');
        ?>
        <input type="hidden" name="feuer_member_workflow_initial_state" value="<?php echo esc_attr($state); ?>">
        <div class="feuer-workflow-editor">
            <header class="feuer-workflow-header">
                <div>
                    <span class="feuer-workflow-kicker">STATUS UND FREIGABE</span>
                    <h3>Lebenszyklus des Profils</h3>
                    <p>Hier steuern Sie, ob das Profil intern vorbereitet, öffentlich angezeigt oder archiviert wird.</p>
                </div>
                <span class="feuer-workflow-status is-<?php echo esc_attr($state); ?>"><?php echo esc_html($state_labels[$state] ?? $state_labels['onboarding']); ?></span>
            </header>

            <div class="feuer-workflow-grid">
                <section class="feuer-workflow-card">
                    <label for="feuer-member-lifecycle"><strong>Mitgliedsphase</strong></label>
                    <select class="widefat" id="feuer-member-lifecycle" name="feuer_member_workflow[lifecycle]">
                        <?php foreach ($state_labels as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($state, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">„Aufnahme / Prüfung“ speichert als Entwurf. „Aktiv“ veröffentlicht das Profil. „Ausgeschieden / archiviert“ entfernt es aus der aktiven Mannschaft, ohne Daten zu löschen.</p>
                </section>

                <section class="feuer-workflow-card feuer-workflow-readiness <?php echo $is_ready ? 'is-ready' : 'has-missing'; ?>">
                    <div class="feuer-workflow-card-title">
                        <strong><?php echo $is_ready ? 'Bereit zur Veröffentlichung' : 'Noch nicht veröffentlichungsbereit'; ?></strong>
                        <span aria-hidden="true" class="dashicons <?php echo $is_ready ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
                    </div>
                    <?php if ($is_ready) : ?>
                        <p>Alle Pflichtangaben sind vorhanden. Das Profil kann auf „Aktiv“ gesetzt werden.</p>
                    <?php else : ?>
                        <p>Bitte ergänzen Sie zuerst:</p>
                        <ul><?php foreach ($missing_fields as $field) : ?><li><?php echo esc_html($field); ?></li><?php endforeach; ?></ul>
                    <?php endif; ?>
                </section>
            </div>

            <section class="feuer-workflow-card feuer-workflow-integration">
                <div>
                    <strong>Verknüpfung mit Feuer-Einsatzberichte</strong>
                    <p>Die Übernahme von Teilnehmern bleibt bewusst manuell. Namen werden niemals automatisch in Einsatzberichte übertragen.</p>
                </div>
                <?php if (class_exists('FEU_Einsatz_Mannschaft_Integration') && FEU_Einsatz_Mannschaft_Integration::is_available()) : ?>
                    <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=feu-einsatz-teilnehmer')); ?>">Teilnehmer-Verknüpfung öffnen</a>
                <?php else : ?>
                    <span class="feuer-workflow-muted">Integration nicht aktiviert</span>
                <?php endif; ?>
            </section>

            <section class="feuer-workflow-card feuer-workflow-documents">
                <div class="feuer-workflow-card-heading">
                    <div>
                        <strong>Interne Dokumente und Nachweise</strong>
                        <p>Nur berechtigte Redakteure können diese geschützten Dateien herunterladen.</p>
                    </div>
                    <span class="dashicons dashicons-lock" aria-hidden="true"></span>
                </div>
                <div class="feuer-workflow-document-form">
                    <label for="feuer-member-private-document"><span>Datei</span><input id="feuer-member-private-document" type="file" name="feuer_member_private_document" accept=".pdf,.jpg,.jpeg,.png,.webp"></label>
                    <label for="feuer-member-private-document-label"><span>Bezeichnung</span><input id="feuer-member-private-document-label" class="widefat" type="text" name="feuer_member_private_document_label" maxlength="120" placeholder="z. B. Lehrgangsnachweis"></label>
                    <label for="feuer-member-private-document-qualification"><span>Zuordnung</span><select id="feuer-member-private-document-qualification" class="widefat" name="feuer_member_private_document_qualification"><option value="">Allgemeiner Nachweis</option>
                        <?php $qualifications = (array) get_post_meta($post->ID, Feuer_Mannschaft_Plugin::META_PREFIX . 'qualification_expiries', true); ?>
                        <?php foreach ($qualifications as $qualification) : if (! is_array($qualification) || empty($qualification['title'])) { continue; } ?>
                            <option value="<?php echo esc_attr($qualification['title']); ?>"><?php echo esc_html($qualification['title']); ?></option>
                        <?php endforeach; ?>
                    </select></label>
                </div>
                <p class="description">Die Datei wird beim Speichern des Profils hochgeladen. Erlaubt: PDF, JPG, PNG und WEBP bis 10 MB.</p>
                <?php if ($documents) : ?>
                    <ul class="feuer-workflow-document-list">
                        <?php foreach ($documents as $document) : if (! is_array($document)) { continue; }
                            $document_id = sanitize_key($document['id'] ?? '');
                            $download = wp_nonce_url(add_query_arg(array('action'=>'feuer_complete_private_document','member_id'=>$post->ID,'document_id'=>$document_id), admin_url('admin-post.php')), 'feuer_private_document_' . $post->ID . '_' . $document_id);
                            $delete = wp_nonce_url(add_query_arg(array('action'=>'feuer_complete_delete_private_document','member_id'=>$post->ID,'document_id'=>$document_id), admin_url('admin-post.php')), 'feuer_delete_private_document_' . $post->ID . '_' . $document_id);
                            $caption = (string) ($document['label'] ?? 'Dokument'); if (! empty($document['qualification'])) { $caption .= ' · ' . (string) $document['qualification']; }
                        ?>
                            <li><span><a href="<?php echo esc_url($download); ?>"><?php echo esc_html($caption); ?></a><small><?php echo esc_html(size_format(absint($document['size'] ?? 0))); ?></small></span><a class="button-link-delete" href="<?php echo esc_url($delete); ?>">Entfernen</a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>
        <?php
    }

    public function save_workflow_data($post_id, $post) {
        if ($this->workflow_status_updating) { return; }
        if (! $post instanceof WP_Post || ! isset($_POST['feuer_member_workflow_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feuer_member_workflow_nonce'])), 'feuer_member_workflow_' . $post_id) || ! current_user_can('edit_post', $post_id) || wp_is_post_revision($post_id)) { return; }
        $input = isset($_POST['feuer_member_workflow']) ? (array) wp_unslash($_POST['feuer_member_workflow']) : array();
        $old_state = (string) get_post_meta($post_id, '_feuer_mannschaft_lifecycle', true);
        $state = sanitize_key($input['lifecycle'] ?? 'onboarding');
        if (! in_array($state, array('onboarding','active','offboarding','archived'), true)) { $state = 'onboarding'; }

        // The classic editor and this workflow box can both change the post
        // status. If the workflow select itself was not changed, follow the
        // status WordPress has just saved. This prevents a stale "onboarding"
        // value from immediately reverting a published profile to draft (and
        // prevents a stale "active" value from re-publishing a draft).
        $initial_state = sanitize_key((string) wp_unslash($_POST['feuer_member_workflow_initial_state'] ?? ''));
        $workflow_was_changed = '' !== $initial_state && $initial_state !== $state;
        if (! $workflow_was_changed) {
            $state = $this->workflow_state_for_post_status($post->post_status);
        }
        if ('archived' === $state) {
            if ($this->set_workflow_post_status($post_id, 'feuer_archived')) {
                if (! get_post_meta($post_id, '_feuer_mannschaft_archived_on', true)) { update_post_meta($post_id, '_feuer_mannschaft_archived_on', current_time('Y-m-d')); }
            } else {
                $state = $this->workflow_state_for_post_status(get_post_status($post_id));
                $this->set_workflow_status_error('Das Profil konnte nicht archiviert werden. Der bisherige Status bleibt erhalten.');
            }
        } elseif ('onboarding' === $state) {
            if ($this->set_workflow_post_status($post_id, 'draft')) {
                delete_post_meta($post_id, '_feuer_mannschaft_archived_on');
            } else {
                $state = $this->workflow_state_for_post_status(get_post_status($post_id));
                $this->set_workflow_status_error('Das Profil konnte nicht in den Entwurf verschoben werden. Der bisherige Status bleibt erhalten.');
            }
        } elseif ('active' === $state) {
            if ($this->set_workflow_post_status($post_id, 'publish')) {
                delete_post_meta($post_id, '_feuer_mannschaft_archived_on');
            } else {
                $state = $this->workflow_state_for_post_status(get_post_status($post_id));
                $message = 'feuer_archived' === get_post_status($post_id)
                    ? 'Das Profil konnte nicht reaktiviert werden. Es bleibt archiviert; prüfen Sie die Pflichtangaben.'
                    : 'Das Profil konnte wegen fehlender Pflichtangaben nicht veröffentlicht werden. Es bleibt im Entwurf.';
                $this->set_workflow_status_error($message);
            }
        } elseif ('offboarding' === $state) {
            if ($this->set_workflow_post_status($post_id, 'publish')) {
                delete_post_meta($post_id, '_feuer_mannschaft_archived_on');
            } else {
                $state = $this->workflow_state_for_post_status(get_post_status($post_id));
                $this->set_workflow_status_error('Der Austritt kann erst bearbeitet werden, wenn das Profil aktiv ist. Der bisherige Status bleibt erhalten.');
            }
        }
        if ('archived' === $state) {
            if (! get_post_meta($post_id, '_feuer_mannschaft_archived_on', true)) { update_post_meta($post_id, '_feuer_mannschaft_archived_on', current_time('Y-m-d')); }
        } else {
            delete_post_meta($post_id, '_feuer_mannschaft_archived_on');
        }
        update_post_meta($post_id, '_feuer_mannschaft_lifecycle', $state);
        if ($old_state !== $state && $this->logger instanceof Feuer_Mannschaft_Logger) {
            $this->logger->log('member_status_changed', 'member', $post_id, get_the_title($post_id), array(array('field'=>'Mitgliedsphase','from'=>$old_state ?: 'unbekannt','to'=>$state)));
        }
        $this->receive_private_document($post_id);
    }

    private function set_workflow_post_status($post_id, $status) {
        if (get_post_status($post_id) === $status) { return true; }
        $this->workflow_status_updating = true;
        $result = wp_update_post(array('ID' => $post_id, 'post_status' => $status), true);
        $this->workflow_status_updating = false;
        return ! is_wp_error($result) && (int) $result === (int) $post_id && get_post_status($post_id) === $status;
    }

    private function workflow_state_for_post_status($status) {
        if ('feuer_archived' === $status) { return 'archived'; }
        if ('publish' === $status) { return 'active'; }
        return 'onboarding';
    }

    private function set_workflow_status_error($message) {
        $validation_key = 'feuer_mannschaft_publish_validation_' . get_current_user_id();
        if (is_array(get_transient($validation_key))) { return; }
        set_transient('feuer_mannschaft_workflow_notice_' . get_current_user_id(), $message, 60);
    }

    private function private_document_directory() {
        $directory = trailingslashit(dirname(untrailingslashit(ABSPATH))) . 'feuer-mannschaft-private';
        if (! is_dir($directory)) { wp_mkdir_p($directory); }
        if (is_dir($directory)) {
            if (! file_exists($directory . '/index.php')) { file_put_contents($directory . '/index.php', "<?php\n// Silence is golden.\n"); }
            if (! file_exists($directory . '/.htaccess')) { file_put_contents($directory . '/.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n"); }
            if (! file_exists($directory . '/web.config')) { file_put_contents($directory . '/web.config', "<?xml version=\"1.0\"?><configuration><system.webServer><authorization><deny users=\"*\" /></authorization></system.webServer></configuration>"); }
        }
        return $directory;
    }

    private function receive_private_document($post_id) {
        if (empty($_FILES['feuer_member_private_document']['name'])) { return; }
        $file = $_FILES['feuer_member_private_document'];
        if (! empty($file['error']) || empty($file['tmp_name']) || ! is_uploaded_file($file['tmp_name']) || (int) $file['size'] > 10 * MB_IN_BYTES) { return; }
        $checked = wp_check_filetype_and_ext($file['tmp_name'], sanitize_file_name($file['name']));
        $allowed = array('pdf'=>'application/pdf','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp');
        $extension = strtolower((string) ($checked['ext'] ?? ''));
        if (! isset($allowed[$extension]) || empty($checked['type']) || ! in_array($checked['type'], $allowed, true)) { return; }
        $directory = $this->private_document_directory();
        if (! is_dir($directory) || ! wp_is_writable($directory)) { return; }
        $id = strtolower(wp_generate_password(32, false, false));
        $filename = $id . '.' . $extension;
        if (! move_uploaded_file($file['tmp_name'], trailingslashit($directory) . $filename)) { return; }
        $docs = (array) get_post_meta($post_id, '_feuer_mannschaft_private_documents', true);
        $docs[] = array('id'=>$id,'file'=>$filename,'label'=>sanitize_text_field(wp_unslash($_POST['feuer_member_private_document_label'] ?? pathinfo($file['name'], PATHINFO_FILENAME))),'qualification'=>sanitize_text_field(wp_unslash($_POST['feuer_member_private_document_qualification'] ?? '')),'mime'=>$allowed[$extension],'size'=>absint($file['size']),'created'=>current_time('mysql'),'user'=>get_current_user_id());
        update_post_meta($post_id, '_feuer_mannschaft_private_documents', $docs);
        if ($this->logger instanceof Feuer_Mannschaft_Logger) { $this->logger->log('member_document_added','member',$post_id,get_the_title($post_id),array(array('message'=>'Interner Nachweis hinzugefügt.'))); }
    }

    public function download_private_document() {
        $post_id = absint($_GET['member_id'] ?? 0); $doc_id = sanitize_key(wp_unslash($_GET['document_id'] ?? ''));
        check_admin_referer('feuer_private_document_' . $post_id . '_' . $doc_id);
        if (! $post_id || ! current_user_can('edit_post', $post_id)) { wp_die('Keine Berechtigung.', 403); }
        foreach ((array) get_post_meta($post_id, '_feuer_mannschaft_private_documents', true) as $doc) {
            if (! is_array($doc) || ! hash_equals((string) ($doc['id'] ?? ''), $doc_id)) { continue; }
            $path = trailingslashit($this->private_document_directory()) . basename((string) ($doc['file'] ?? ''));
            if (! is_readable($path) || 0 !== strpos(wp_normalize_path($path), wp_normalize_path($this->private_document_directory()) . '/')) { wp_die('Datei nicht gefunden.', 404); }
            nocache_headers(); header('X-Content-Type-Options: nosniff'); header('Content-Type: application/octet-stream'); header('Content-Length: ' . filesize($path)); header('Content-Disposition: attachment; filename="' . sanitize_file_name($doc['label'] ?? 'Nachweis') . '.' . pathinfo($path, PATHINFO_EXTENSION) . '"'); readfile($path); exit;
        }
        wp_die('Datei nicht gefunden.', 404);
    }

    public function delete_private_document() {
        $post_id = absint($_GET['member_id'] ?? 0); $doc_id = sanitize_key(wp_unslash($_GET['document_id'] ?? ''));
        check_admin_referer('feuer_delete_private_document_' . $post_id . '_' . $doc_id);
        if (! $post_id || ! current_user_can('edit_post', $post_id)) { wp_die('Keine Berechtigung.', 403); }
        $docs = (array) get_post_meta($post_id, '_feuer_mannschaft_private_documents', true); $kept = array();
        foreach ($docs as $doc) { if (is_array($doc) && hash_equals((string) ($doc['id'] ?? ''), $doc_id)) { $path = trailingslashit($this->private_document_directory()) . basename((string) ($doc['file'] ?? '')); if (is_file($path)) { wp_delete_file($path); } } else { $kept[] = $doc; } }
        update_post_meta($post_id, '_feuer_mannschaft_private_documents', $kept);
        if ($this->logger instanceof Feuer_Mannschaft_Logger) { $this->logger->log('member_document_removed','member',$post_id,get_the_title($post_id),array(array('message'=>'Interner Nachweis entfernt.'))); }
        wp_safe_redirect(get_edit_post_link($post_id, 'url')); exit;
    }

    public function workflow_admin_notices() {
        $key = 'feuer_mannschaft_workflow_notice_' . get_current_user_id(); $message = get_transient($key);
        if (! $message) { return; }
        delete_transient($key);
        printf('<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html($message));
    }

    public function capture_profile_before_save($post_id, $post, $update) {
        if ($this->workflow_status_updating || wp_is_post_revision($post_id) || ! $post instanceof WP_Post) { return; }
        $keys = array('first_name','last_name','rank','function','education','lehrgang_values','entry_date','job_title'); $snapshot = array('status'=>$post->post_status,'terms'=>wp_get_post_categories($post_id,array('fields'=>'names')));
        foreach ($keys as $key) { $snapshot[$key] = get_post_meta($post_id, Feuer_Mannschaft_Plugin::META_PREFIX . $key, true); }
        $this->profile_audit_snapshots[$post_id] = $snapshot;
    }

    public function audit_profile_after_save($post_id, $post, $update) {
        if ($this->workflow_status_updating || empty($this->profile_audit_snapshots[$post_id]) || ! $this->logger instanceof Feuer_Mannschaft_Logger) { return; }
        $before = $this->profile_audit_snapshots[$post_id]; unset($this->profile_audit_snapshots[$post_id]); $changes = array();
        foreach (array('first_name'=>'Vorname','last_name'=>'Nachname','rank'=>'Dienstgrad','function'=>'Funktion','education'=>'Qualifikation','lehrgang_values'=>'Lehrgänge','entry_date'=>'Eintritt','job_title'=>'Tätigkeit') as $key=>$label) { $after = get_post_meta($post_id, Feuer_Mannschaft_Plugin::META_PREFIX . $key, true); if (maybe_serialize($before[$key] ?? '') !== maybe_serialize($after)) { $changes[] = array('field'=>$label,'from'=>is_scalar($before[$key]??'')?(string)($before[$key]??''):'(geändert)','to'=>is_scalar($after)?(string)$after:'(geändert)'); } }
        if (($before['status'] ?? '') !== $post->post_status) { $changes[] = array('field'=>'WordPress-Status','from'=>$before['status'],'to'=>$post->post_status); }
        $after_terms = wp_get_post_categories($post_id,array('fields'=>'names'));
        if (array_values((array)($before['terms']??array())) !== array_values((array)$after_terms)) { $changes[] = array('field'=>'Kategorien','from'=>implode(', ',(array)($before['terms']??array())),'to'=>implode(', ',(array)$after_terms)); }
        if ($changes) { $this->logger->log('member_profile_changed','member',$post_id,get_the_title($post_id),$changes); }
    }

    public function audit_member_status_transition($new_status, $old_status, $post) {
        if (! $post instanceof WP_Post || Feuer_Mannschaft_Plugin::POST_TYPE !== $post->post_type || $new_status === $old_status || ! $this->logger instanceof Feuer_Mannschaft_Logger) { return; }
        $this->logger->log('member_status_changed','member',$post->ID,get_the_title($post),array(array('field'=>'WordPress-Status','from'=>$old_status,'to'=>$new_status)));
    }

    public function render_readiness_page() {
        if (! current_user_can('manage_feuer_members')) { return; }
        $members = $this->members(array('post_status'=>array('publish','draft','pending','private','feuer_archived')));
        echo '<div class="wrap feuer-complete-admin"><h1>Einsatzbereitschaft</h1><p>Übersicht für Planung und Datenpflege. Interne Qualifikationen und Fristen sind nur für berechtigte Mannschaft-Redakteure sichtbar.</p><div class="feuer-table-scroll"><table class="widefat striped"><thead><tr><th>Profil</th><th>Status</th><th>Abteilung</th><th>Gruppe</th><th>Fachgebiet</th><th>Dienstgrad</th><th>Funktion</th><th>Lehrgänge</th><th>Fristen</th><th>Nachweise</th><th>Profilprüfung</th><th>Archiviert am</th></tr></thead><tbody>';
        foreach ($members as $member) {
            $profile = $this->profile($member->ID); $terms = wp_get_post_terms($member->ID, 'feuer_department', array('fields'=>'names')); $groups = wp_get_post_terms($member->ID, 'feuer_group', array('fields'=>'names')); $specialties = wp_get_post_terms($member->ID, 'feuer_specialty', array('fields'=>'names')); $qualifications = (array) get_post_meta($member->ID, Feuer_Mannschaft_Plugin::META_PREFIX . 'qualification_expiries', true); $badges = array(); $dates = array();
            foreach ($qualifications as $qualification) { if (! is_array($qualification) || empty($qualification['title'])) { continue; } $title = sanitize_text_field($qualification['title']); $badges[] = $title; if (! empty($qualification['expires_on'])) { $dates[] = $title . ': ' . sanitize_text_field($qualification['expires_on']); } }
            $phase = (string) get_post_meta($member->ID, '_feuer_mannschaft_lifecycle', true); if (! $phase) { $phase = 'publish' === $member->post_status ? 'Aktiv' : ('feuer_archived' === $member->post_status ? 'Archiviert' : 'Aufnahme / Prüfung'); } else { $phase = array('onboarding'=>'Aufnahme / Prüfung','active'=>'Aktiv','offboarding'=>'Austritt in Bearbeitung','archived'=>'Ausgeschieden')[$phase] ?? $phase; }
            $archived_on = (string) get_post_meta($member->ID, '_feuer_mannschaft_archived_on', true);
            $reviewed = (string) get_post_meta($member->ID, Feuer_Mannschaft_Plugin::META_PREFIX . 'reviewed_on', true);
            $review_state = $reviewed ? wp_date('d.m.Y', strtotime($reviewed)) : 'Noch offen';
            if ($reviewed && strtotime($reviewed) < current_time('timestamp') - YEAR_IN_SECONDS) { $review_state .= ' · fällig'; }
            $document_markup = array();
            foreach ((array) get_post_meta($member->ID, '_feuer_mannschaft_private_documents', true) as $document) {
                if (! is_array($document)) { continue; }
                $doc_id = sanitize_key($document['id'] ?? ''); $label = sanitize_text_field($document['label'] ?? 'Nachweis');
                $url = wp_nonce_url(add_query_arg(array('action'=>'feuer_complete_private_document','member_id'=>$member->ID,'document_id'=>$doc_id), admin_url('admin-post.php')), 'feuer_private_document_' . $member->ID . '_' . $doc_id);
                $document_markup[] = '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
            }
            printf('<tr><td><a href="%1$s">%2$s</a></td><td>%3$s</td><td>%4$s</td><td>%5$s</td><td>%6$s</td><td>%7$s</td><td>%8$s</td><td>%9$s</td><td>%10$s</td><td>%11$s</td><td>%12$s</td><td>%13$s</td></tr>', esc_url(get_edit_post_link($member->ID)), esc_html(get_the_title($member)), esc_html($phase), esc_html(is_wp_error($terms)?'':implode(', ',$terms)), esc_html(is_wp_error($groups)?'':implode(', ',$groups)), esc_html(is_wp_error($specialties)?'':implode(', ',$specialties)), esc_html($profile['rank']??''), esc_html($profile['function']??''), esc_html(implode(', ',$badges)), esc_html(implode(', ',array_filter($dates))), $document_markup ? implode('<br>', $document_markup) : '—', esc_html($review_state), esc_html($archived_on));
        }
        echo '</tbody></table></div></div>';
    }
}

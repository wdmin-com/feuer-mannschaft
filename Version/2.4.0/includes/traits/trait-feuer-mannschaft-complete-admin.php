<?php
/** Admin screens and publication/profile relations. */
if (! defined('ABSPATH')) { exit; }

trait Feuer_Mannschaft_Complete_Admin {
    public function register_mention_visibility_box() {
        if (! current_user_can('edit_feuer_mentions')) { return; }
        $types = get_post_types(array('public' => true), 'names');
        unset($types[Feuer_Mannschaft_Plugin::POST_TYPE], $types['attachment']);
        foreach ($types as $type) {
            add_meta_box('feuer-hidden-profile-mentions', __('Sichtbarkeit der Profil-Erwähnungen', 'feuer-mannschaft'), array($this, 'render_mention_visibility_box'), $type, 'side', 'default');
        }
    }

    public function render_mention_visibility_box($post) {
        $linked = array_values(array_filter(array_map('absint', (array) get_post_meta($post->ID, Feuer_Mannschaft_Mentions::META_KEY, true))));
        $hidden = array_values(array_filter(array_map('absint', (array) get_post_meta($post->ID, self::HIDDEN_MENTIONS_META, true))));
        wp_nonce_field('feuer_hidden_mentions_' . $post->ID, 'feuer_hidden_mentions_nonce');
        echo '<p class="description">Der Beitrag bleibt verknüpft. Aktivierte Personen zeigen diesen Beitrag jedoch nicht im Profil.</p>';
        if (! $linked) { echo '<p>Erst Teilnehmer im Feld „Erwähnte Personen“ auswählen.</p>'; return; }
        foreach ($linked as $id) {
            printf('<label class="feuer-check-row"><input type="checkbox" name="feuer_hidden_mentions[]" value="%1$d" %2$s> %3$s</label>', $id, checked(in_array($id, $hidden, true), true, false), esc_html(get_the_title($id)));
        }
    }

    public function save_hidden_mentions($post_id, $post) {
        if (! $post instanceof WP_Post || Feuer_Mannschaft_Plugin::POST_TYPE === $post->post_type || wp_is_post_revision($post_id)) { return; }
        if (! isset($_POST['feuer_hidden_mentions_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feuer_hidden_mentions_nonce'])), 'feuer_hidden_mentions_' . $post_id) || ! current_user_can('edit_post', $post_id)) { return; }
        $linked = array_values(array_filter(array_map('absint', (array) get_post_meta($post_id, Feuer_Mannschaft_Mentions::META_KEY, true))));
        $requested = isset($_POST['feuer_hidden_mentions']) ? array_map('absint', (array) wp_unslash($_POST['feuer_hidden_mentions'])) : array();
        update_post_meta($post_id, self::HIDDEN_MENTIONS_META, array_values(array_intersect($linked, $requested)));
    }

    public function replace_admin_pages() {
        $parent = 'edit.php?post_type=' . Feuer_Mannschaft_Plugin::POST_TYPE;
        remove_submenu_page($parent, 'feuer-mannschaft-csv');
        remove_submenu_page($parent, 'feuer-mannschaft-quality');
        remove_submenu_page($parent, 'feuer-mannschaft-automation');
        add_submenu_page($parent, 'Import / Export', 'Import / Export', 'manage_feuer_members', 'feuer-mannschaft-csv', array($this, 'render_import_page'));
        add_submenu_page($parent, 'Datenqualität', 'Datenqualität', 'manage_feuer_members', 'feuer-mannschaft-quality', array($this, 'render_quality_page'));
        add_submenu_page($parent, 'Automatisierung', 'Automatisierung', 'manage_feuer_members', 'feuer-mannschaft-automation', array($this, 'render_automation_page'));
    }

    public function render_import_page() {
        if (! current_user_can('manage_feuer_members')) { return; }
        $preview = get_transient(self::IMPORT_TRANSIENT . get_current_user_id());
        $result = get_transient(self::IMPORT_TRANSIENT . get_current_user_id() . '_result');
        ?><div class="wrap feuer-complete-admin"><h1>Import / Export</h1>
        <?php $this->render_admin_result($result); ?>
        <div class="feuer-admin-cards"><section class="card"><h2>1. CSV prüfen</h2><p>UTF-8, maximal 5 MB und 2.000 Datensätze. Trennzeichen werden automatisch erkannt.</p><form enctype="multipart/form-data" method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>"><input type="hidden" name="action" value="feuer_complete_import_preview"><?php wp_nonce_field('feuer_complete_import_preview');?><input type="file" name="csv" accept=".csv,text/csv" required><?php submit_button('Vorschau erstellen','primary','submit',false);?></form></section>
        <section class="card"><h2>Vollständiger Export</h2><p>ZIP mit CSV, Beziehungen, Manifest und vorhandenen Bildern.</p><a class="button button-secondary" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=feuer_complete_export'),'feuer_complete_export'));?>">ZIP herunterladen</a><?php if(get_transient(self::IMPORT_ROLLBACK_TRANSIENT.get_current_user_id())):?><p><a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=feuer_complete_import_rollback'),'feuer_complete_import_rollback'));?>">Letzten Import zurückrollen</a></p><?php endif;?></section></div>
        <?php if (is_array($preview)) { $this->render_import_mapping($preview); } ?></div><?php
    }

    private function render_import_mapping($preview) {
        $targets = $this->import_targets();
        ?><section class="card feuer-import-preview"><h2>2. Zuordnung und Ausführung</h2><p><?php echo esc_html(count($preview['rows']));?> Datensätze erkannt. Die Vorschau verändert keine Daten.</p><form method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>"><input type="hidden" name="action" value="feuer_complete_import_execute"><?php wp_nonce_field('feuer_complete_import_execute');?><table class="widefat striped"><thead><tr><th>Zielfeld</th><th>CSV-Spalte</th></tr></thead><tbody><?php foreach($targets as $key=>$label):?><tr><th><?php echo esc_html($label);?></th><td><select name="mapping[<?php echo esc_attr($key);?>]"><option value="">— nicht importieren —</option><?php foreach($preview['header'] as $column):?><option value="<?php echo esc_attr($column);?>" <?php selected($this->auto_map_column($key,$preview['header']),$column);?>><?php echo esc_html($column);?></option><?php endforeach;?></select></td></tr><?php endforeach;?></tbody></table><p><label><input type="checkbox" name="dry_run" value="1" checked> Nur prüfen (Dry Run)</label></p><?php submit_button('Import prüfen / ausführen');?></form><h3>Beispieldaten</h3><div class="feuer-table-scroll"><table class="widefat striped"><thead><tr><?php foreach($preview['header'] as $h):?><th><?php echo esc_html($h);?></th><?php endforeach;?></tr></thead><tbody><?php foreach(array_slice($preview['rows'],0,10) as $row):?><tr><?php foreach($preview['header'] as $h):?><td><?php echo esc_html($row[$h]??'');?></td><?php endforeach;?></tr><?php endforeach;?></tbody></table></div></section><?php
    }

    public function render_quality_page() {
        if (! current_user_can('manage_feuer_members')) { return; }
        $audit = $this->build_integrity_audit();
        ?><div class="wrap feuer-complete-admin"><h1>Datenqualität und Integrität</h1><p><a class="button button-primary" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=feuer_complete_run_audit'),'feuer_complete_run_audit'));?>">Prüfung erneut ausführen</a> <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=feuer_complete_media_process'),'feuer_complete_media_process'));?>">Bildvarianten aktualisieren</a></p><div class="feuer-audit-score"><strong><?php echo esc_html($audit['score']);?>/100</strong><span>Qualitätswert · <?php echo esc_html($audit['profiles']);?> Profile · <?php echo esc_html($audit['issues']);?> Hinweise</span></div><table class="widefat striped"><thead><tr><th>Profil</th><th>Status</th><th>Hinweise</th><th></th></tr></thead><tbody><?php foreach($audit['rows'] as $row):?><tr><td><?php echo esc_html($row['name']);?></td><td><?php echo esc_html($row['score']);?>%</td><td><?php echo esc_html(implode(', ',$row['issues']));?></td><td><a href="<?php echo esc_url(get_edit_post_link($row['id']));?>">Bearbeiten</a></td></tr><?php endforeach;?></tbody></table><?php if($audit['broken_mentions']):?><div class="notice notice-warning inline"><p><?php echo esc_html($audit['broken_mentions']);?> ungültige Publikationsverknüpfungen gefunden.</p></div><?php endif;?><?php if(!empty($audit['legacy_profiles'])):?><div class="notice notice-warning inline"><p><?php echo esc_html($audit['legacy_profiles']);?> nicht migrierte Altprofile gefunden.</p></div><?php endif;?></div><?php
    }

    public function render_automation_page() {
        if (! current_user_can('manage_feuer_members')) { return; }
        $s = $this->automation_settings(); $audit = get_option(self::AUDIT_OPTION,array());
        ?><div class="wrap feuer-complete-admin"><h1>Automatisierung</h1><form method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>"><input type="hidden" name="action" value="feuer_complete_save_automation"><?php wp_nonce_field('feuer_complete_save_automation');?><table class="form-table"><tr><th><label for="feuer-auto-email">Empfänger</label></th><td><input id="feuer-auto-email" class="regular-text" type="email" name="email" value="<?php echo esc_attr($s['email']);?>"></td></tr><tr><th>Aufgaben</th><td><?php foreach(array('notify_errors'=>'Integritätsfehler melden','weekly_email'=>'Wochenbericht senden','weekly_backup'=>'Wöchentliches Backup','missing_photo'=>'Fehlende Fotos melden','anniversaries'=>'Dienstjubiläen melden','bulk_notifications'=>'Archivierung/Wiederherstellung melden') as $key=>$label):?><label class="feuer-check-row"><input type="checkbox" name="<?php echo esc_attr($key);?>" value="1" <?php checked($s[$key]);?>> <?php echo esc_html($label);?></label><?php endforeach;?></td></tr><tr><th>Log-Aufbewahrung</th><td><input type="number" min="7" max="3650" name="log_days" value="<?php echo esc_attr($s['log_days']);?>"> Tage</td></tr><tr><th>Maximale Profilbildgröße</th><td><input type="number" min="1" max="32" name="image_max_mb" value="<?php echo esc_attr($s['image_max_mb']);?>"> MB</td></tr></table><?php submit_button('Einstellungen speichern');?></form><p><strong>Letzte Integritätsprüfung:</strong> <?php echo !empty($audit['time'])?esc_html(wp_date('d.m.Y H:i',$audit['time'])):'noch nicht ausgeführt';?></p></div><?php
    }

    private function render_admin_result($result) {
        if (! is_array($result)) { return; }
        delete_transient(self::IMPORT_TRANSIENT . get_current_user_id() . '_result');
        printf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>', empty($result['error'])?'success':'error', esc_html($result['message']??''));
        if (! empty($result['details'])) { echo '<details><summary>Details</summary><ul>'; foreach($result['details'] as $detail) echo '<li>'.esc_html($detail).'</li>'; echo '</ul></details>'; }
    }
}

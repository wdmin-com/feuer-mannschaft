<?php
/**
 * Optional feature modules introduced in releases 2.3.75-2.3.83.
 *
 * The release gates make every historical package deterministic: a snapshot
 * only enables the modules documented for its version.
 */
if (! defined('ABSPATH')) {
    exit;
}

final class Feuer_Mannschaft_Toolkit {
    const CSV_ROLLBACK_PREFIX = 'feuer_mannschaft_csv_rollback_';
    const VISIBILITY_META = '_feuer_mannschaft_field_visibility';
    const IMAGE_FOCUS_META = '_feuer_mannschaft_image_focus';
    const SEO_META = '_feuer_mannschaft_seo';
    private static $instance;

    public static function instance() {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ($this->has('2.3.75')) {
            add_shortcode('feuer_member_directory', array($this, 'directory_shortcode'));
            add_action('wp_enqueue_scripts', array($this, 'directory_assets'));
        }
        if ($this->has('2.3.76')) {
            add_shortcode('feuer_member_qr', array($this, 'qr_shortcode'));
            add_shortcode('feuer_member_actions', array($this, 'member_actions_shortcode'));
            add_action('template_redirect', array($this, 'download_vcard'));
            add_action('template_redirect', array($this, 'print_profile'));
        }
        if ($this->has('2.3.77')) {
            add_action('init', array($this, 'install_roles'));
        }
        if ($this->has('2.3.78')) {
            add_action('add_meta_boxes', array($this, 'visibility_meta_box'));
            add_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($this, 'save_visibility'));
            add_filter('feuer_mannschaft_field_visible', array($this, 'filter_field_visibility'), 10, 3);
        }
        if ($this->has('2.3.79')) {
            add_action('admin_menu', array($this, 'csv_menu'), 30);
            add_action('admin_post_feuer_mannschaft_csv_export', array($this, 'csv_export'));
            add_action('admin_post_feuer_mannschaft_csv_import', array($this, 'csv_import'));
            add_action('admin_post_feuer_mannschaft_csv_rollback', array($this, 'csv_rollback'));
        }
        if ($this->has('2.3.80')) {
            add_action('admin_menu', array($this, 'quality_menu'), 31);
        }
        if ($this->has('2.3.81')) {
            add_action('add_meta_boxes', array($this, 'image_meta_box'));
            add_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($this, 'save_image_focus'));
            add_filter('wp_get_attachment_image_attributes', array($this, 'image_focus_attributes'), 10, 3);
            add_action('admin_post_feuer_mannschaft_regenerate_images', array($this, 'regenerate_images'));
        }
        if ($this->has('2.3.82')) {
            add_action('add_meta_boxes', array($this, 'seo_meta_box'));
            add_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($this, 'save_seo'));
            add_action('wp_head', array($this, 'seo_head'), 1);
            add_action('template_redirect', array($this, 'prepare_seo_head'));
            add_filter('wp_robots', array($this, 'robots'));
            add_shortcode('feuer_member_breadcrumbs', array($this, 'breadcrumbs_shortcode'));
        }
        if ($this->has('2.3.83')) {
            add_action('init', array($this, 'schedule_automation'));
            add_action('feuer_mannschaft_daily_audit', array($this, 'daily_audit'));
            add_action('feuer_mannschaft_weekly_report', array($this, 'weekly_report'));
            add_action('admin_menu', array($this, 'automation_menu'), 32);
            add_action('admin_post_feuer_mannschaft_save_automation', array($this, 'save_automation'));
        }
    }

    private function has($version) {
        return version_compare(FEUER_MANNSCHAFT_VERSION, $version, '>=');
    }

    private function members($args = array()) {
        return get_posts(wp_parse_args($args, array(
            'post_type' => Feuer_Mannschaft_Plugin::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        )));
    }

    private function profile($post_id) {
        $value = get_post_meta($post_id, Feuer_Mannschaft_Plugin::PROFILE_META, true);
        return is_array($value) ? $value : array();
    }

    public function directory_assets() {
        wp_enqueue_script('feuer-mannschaft-directory', FEUER_MANNSCHAFT_PLUGIN_URL . 'assets/js/member-directory.js', array(), FEUER_MANNSCHAFT_VERSION, true);
        wp_enqueue_style('feuer-mannschaft-directory', FEUER_MANNSCHAFT_PLUGIN_URL . 'assets/css/member-directory.css', array(), FEUER_MANNSCHAFT_VERSION);
    }

    public function directory_shortcode($atts) {
        $atts = shortcode_atts(array('show_filters' => '1'), $atts, 'feuer_member_directory');
        $members = $this->members();
        $departments = get_terms(array('taxonomy' => 'feuer_department', 'hide_empty' => true));
        $specialties = get_terms(array('taxonomy' => 'feuer_specialty', 'hide_empty' => true));
        $search_id = wp_unique_id('fm-search-');
        ob_start(); ?>
        <section class="fm-directory" data-fm-directory>
            <?php if ('1' === $atts['show_filters']) : ?>
            <form class="fm-directory__filters row g-2 mb-4" data-fm-filters role="search">
                <div class="col-12 col-md"><label class="form-label" for="<?php echo esc_attr($search_id); ?>">Suche</label><input class="form-control" id="<?php echo esc_attr($search_id); ?>" data-fm-search type="search" placeholder="Name, Dienstgrad oder Funktion"></div>
                <?php echo $this->term_select('department', 'Abteilung', $departments); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php echo $this->term_select('specialty', 'Fachgebiet', $specialties); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <div class="col-12 col-md"><label class="form-label">Sortierung</label><select class="form-select" data-fm-sort><option value="name">Name A-Z</option><option value="name-desc">Name Z-A</option><option value="rank">Dienstgrad</option></select></div>
                <div class="col-12"><button class="btn btn-outline-secondary btn-sm" type="reset">Filter zurücksetzen</button></div>
            </form>
            <?php endif; ?>
            <p class="fm-directory__count" aria-live="polite" data-fm-count></p>
            <div class="row g-4" data-fm-results>
            <?php foreach ($members as $member) :
                $profile = $this->profile($member->ID);
                $rank = $this->text_value($profile, 'rank');
                $function = $this->text_value($profile, 'function');
                $department = wp_get_post_terms($member->ID, 'feuer_department', array('fields' => 'slugs'));
                $specialty = wp_get_post_terms($member->ID, 'feuer_specialty', array('fields' => 'slugs'));
                $search = strtolower($member->post_title . ' ' . $rank . ' ' . $function);
            ?>
                <article class="col-12 col-sm-6 col-lg-4 fm-directory__item" data-name="<?php echo esc_attr(strtolower($member->post_title)); ?>" data-rank="<?php echo esc_attr(strtolower($rank)); ?>" data-search="<?php echo esc_attr($search); ?>" data-department="<?php echo esc_attr(implode(' ', $department)); ?>" data-specialty="<?php echo esc_attr(implode(' ', $specialty)); ?>">
                    <a class="card h-100 text-decoration-none fm-directory-card" href="<?php echo esc_url(get_permalink($member)); ?>">
                        <?php if (has_post_thumbnail($member)) : echo get_the_post_thumbnail($member, 'large', array('class' => 'card-img-top')); endif; ?>
                        <div class="card-img-overlay d-flex flex-column justify-content-end"><h3 class="h5 card-title"><?php echo esc_html($member->post_title); ?></h3><p class="card-text mb-0"><?php echo esc_html(trim($rank . ($rank && $function ? ' · ' : '') . $function)); ?></p></div>
                    </a>
                </article>
            <?php endforeach; ?>
            </div>
            <p class="alert alert-light mt-3" data-fm-empty hidden>Keine passenden Personen gefunden.</p>
        </section><?php
        return ob_get_clean();
    }

    private function term_select($key, $label, $terms) {
        if (is_wp_error($terms) || ! $terms) return '';
        $field_id = wp_unique_id('fm-' . sanitize_key($key) . '-');
        $html = '<div class="col-12 col-md"><label class="form-label" for="' . esc_attr($field_id) . '">' . esc_html($label) . '</label><select class="form-select" id="' . esc_attr($field_id) . '" data-fm-filter="' . esc_attr($key) . '"><option value="">Alle</option>';
        foreach ($terms as $term) $html .= '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
        return $html . '</select></div>';
    }

    private function text_value($profile, $key) {
        $value = isset($profile[$key]) ? $profile[$key] : '';
        if (is_array($value)) $value = implode(', ', array_filter(array_map('sanitize_text_field', $value)));
        return sanitize_text_field((string) $value);
    }

    public function qr_shortcode($atts) {
        return class_exists('Feuer_Mannschaft_Complete') ? Feuer_Mannschaft_Complete::instance()->qr_shortcode($atts) : '';
    }

    public function member_actions_shortcode($atts) {
        $id = absint(shortcode_atts(array('id' => get_the_ID()), $atts)['id']);
        if (Feuer_Mannschaft_Plugin::POST_TYPE !== get_post_type($id)) return '';
        $vcard = wp_nonce_url(add_query_arg(array('feuer_vcard' => $id), home_url('/')), 'feuer_vcard_' . $id);
        $print = add_query_arg(array('feuer_print' => $id), home_url('/'));
        return '<div class="btn-group feuer-member-actions"><a class="btn btn-outline-primary" href="' . esc_url($vcard) . '">vCard</a><a class="btn btn-outline-secondary" href="' . esc_url($print) . '" target="_blank" rel="noopener">Drucken</a></div>';
    }

    public function download_vcard() {
        if (empty($_GET['feuer_vcard'])) return;
        $id = absint($_GET['feuer_vcard']);
        if (! wp_verify_nonce(isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '', 'feuer_vcard_' . $id) || 'publish' !== get_post_status($id)) wp_die('Ungültige Anfrage.', 403);
        $profile = $this->profile($id); $name = get_the_title($id);
        $lines = array('BEGIN:VCARD', 'VERSION:3.0', 'FN:' . $this->vcard_escape($name), 'ORG:' . $this->vcard_escape(get_bloginfo('name')));
        foreach (array('function' => 'TITLE', 'email' => 'EMAIL', 'phone' => 'TEL') as $key => $type) {
            $source = in_array($key, array('email', 'phone'), true) && ! empty($profile['contact_fields'][$key]) ? $profile['contact_fields'][$key] : (isset($profile[$key]) ? $profile[$key] : '');
            $value = is_array($source) ? '' : sanitize_text_field((string) $source);
            if ($value && apply_filters('feuer_mannschaft_field_visible', true, $key, $id)) $lines[] = $type . ':' . $this->vcard_escape($value);
        }
        $lines[] = 'URL:' . get_permalink($id); $lines[] = 'END:VCARD';
        nocache_headers(); header('Content-Type: text/vcard; charset=utf-8'); header('Content-Disposition: attachment; filename="' . sanitize_file_name($name) . '.vcf"'); echo implode("\r\n", $lines); exit;
    }

    private function vcard_escape($value) { return str_replace(array('\\', ';', ',', "\r", "\n"), array('\\\\', '\\;', '\\,', '', '\\n'), $value); }

    public function print_profile() {
        if (empty($_GET['feuer_print'])) return;
        $id = absint($_GET['feuer_print']); if ('publish' !== get_post_status($id) || Feuer_Mannschaft_Plugin::POST_TYPE !== get_post_type($id)) wp_die('Profil nicht gefunden.', 404);
        $profile = $this->profile($id); nocache_headers(); ?><!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title><?php echo esc_html(get_the_title($id)); ?></title><style>body{font:16px/1.5 system-ui;max-width:780px;margin:40px auto;padding:20px}img{max-width:280px;height:auto;border-radius:12px}@media print{button{display:none}}</style></head><body><button onclick="print()">Drucken</button><article><?php echo get_the_post_thumbnail($id, 'large'); ?><h1><?php echo esc_html(get_the_title($id)); ?></h1><p><?php echo esc_html($this->text_value($profile, 'rank')); ?></p><p><?php echo esc_html($this->text_value($profile, 'function')); ?></p><div><?php echo wp_kses_post(wpautop(get_post_field('post_content', $id))); ?></div></article></body></html><?php exit;
    }

    public function install_roles() {
        if ('2.3.77' === get_option('feuer_mannschaft_roles_version')) {
            return;
        }
        $caps = array('read' => true, 'upload_files' => true, 'edit_feuer_member' => true, 'edit_feuer_members' => true, 'edit_published_feuer_members' => true, 'publish_feuer_members' => true, 'delete_feuer_member' => true, 'delete_feuer_members' => true, 'edit_feuer_mentions' => true);
        add_role('feuer_mannschaft_editor', 'Mannschaft-Redakteur', $caps);
        $admin = get_role('administrator'); if ($admin) foreach (array('manage_feuer_members','edit_feuer_member','read_feuer_member','delete_feuer_member','edit_feuer_members','edit_others_feuer_members','publish_feuer_members','read_private_feuer_members','delete_feuer_members','delete_private_feuer_members','delete_published_feuer_members','delete_others_feuer_members','edit_private_feuer_members','edit_published_feuer_members','edit_feuer_mentions','view_feuer_internal','delete_feuer_members_permanently') as $cap) $admin->add_cap($cap);
        update_option('feuer_mannschaft_roles_version', '2.3.77', false);
    }

    public function visibility_meta_box() { add_meta_box('feuer-field-visibility', 'Sichtbarkeit', array($this, 'render_visibility'), Feuer_Mannschaft_Plugin::POST_TYPE, 'side'); }
    public function render_visibility($post) {
        wp_nonce_field('feuer_visibility_' . $post->ID, 'feuer_visibility_nonce'); $saved = get_post_meta($post->ID, self::VISIBILITY_META, true); $saved = is_array($saved) ? $saved : array();
        foreach (array('email' => 'E-Mail', 'phone' => 'Telefon', 'service_history' => 'Dienstverlauf') as $field => $label) { $value = isset($saved[$field]) ? $saved[$field] : 'public'; echo '<p><label>' . esc_html($label) . '<select class="widefat" name="feuer_visibility[' . esc_attr($field) . ']"><option value="public"' . selected($value, 'public', false) . '>Öffentlich</option><option value="logged_in"' . selected($value, 'logged_in', false) . '>Angemeldet</option><option value="internal"' . selected($value, 'internal', false) . '>Intern</option><option value="hidden"' . selected($value, 'hidden', false) . '>Verborgen</option></select></label></p>'; }
    }
    public function save_visibility($post_id) {
        if (! isset($_POST['feuer_visibility_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feuer_visibility_nonce'])), 'feuer_visibility_' . $post_id) || ! current_user_can('edit_post', $post_id)) return;
        $raw = isset($_POST['feuer_visibility']) ? (array) wp_unslash($_POST['feuer_visibility']) : array(); $clean = array(); foreach ($raw as $key => $value) if (in_array($value, array('public','logged_in','internal','hidden'), true)) $clean[sanitize_key($key)] = $value; update_post_meta($post_id, self::VISIBILITY_META, $clean);
    }
    public function filter_field_visibility($visible, $field, $post_id) {
        $rules = get_post_meta($post_id, self::VISIBILITY_META, true); $level = is_array($rules) && isset($rules[$field]) ? $rules[$field] : 'public';
        if ('hidden' === $level) return false; if ('logged_in' === $level) return is_user_logged_in(); if ('internal' === $level) return current_user_can('view_feuer_internal'); return $visible;
    }

    public function csv_menu() { add_submenu_page('edit.php?post_type=' . Feuer_Mannschaft_Plugin::POST_TYPE, 'CSV Import/Export', 'CSV Import/Export', 'manage_feuer_members', 'feuer-mannschaft-csv', array($this, 'csv_page')); }
    public function csv_page() {
        if (! current_user_can('manage_feuer_members')) return; $rollback = get_transient(self::CSV_ROLLBACK_PREFIX . get_current_user_id()); ?>
        <div class="wrap"><h1>CSV Import/Export</h1><p>UTF-8 CSV mit den Spalten <code>id;slug;name;rank;function;department;group;specialty</code>.</p><a class="button button-primary" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=feuer_mannschaft_csv_export'), 'feuer_csv_export')); ?>">CSV exportieren</a><hr><form enctype="multipart/form-data" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="feuer_mannschaft_csv_import"><?php wp_nonce_field('feuer_csv_import'); ?><input type="file" name="csv" accept=".csv,text/csv" required><label><input type="checkbox" name="dry_run" value="1" checked> Nur prüfen (Dry Run)</label><?php submit_button('Import prüfen/ausführen', 'secondary', 'submit', false); ?></form><?php if ($rollback) : ?><p><a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=feuer_mannschaft_csv_rollback'), 'feuer_csv_rollback')); ?>">Letzten Import zurückrollen</a></p><?php endif; ?></div><?php
    }
    public function csv_export() {
        if (! current_user_can('manage_feuer_members') || ! check_admin_referer('feuer_csv_export')) wp_die('Nicht erlaubt.', 403); nocache_headers(); header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="mannschaft.csv"'); $out = fopen('php://output', 'w'); fwrite($out, "\xEF\xBB\xBF"); fputcsv($out, array('id','slug','name','rank','function','department','group','specialty'), ';'); foreach ($this->members(array('post_status' => array('publish','draft','feuer_archived'))) as $post) { $p = $this->profile($post->ID); fputcsv($out, array($post->ID,$post->post_name,$post->post_title,$this->text_value($p,'rank'),$this->text_value($p,'function'),$this->terms($post->ID,'feuer_department'),$this->terms($post->ID,'feuer_group'),$this->terms($post->ID,'feuer_specialty')), ';'); } fclose($out); exit;
    }
    private function terms($id, $taxonomy) { $terms = wp_get_post_terms($id, $taxonomy, array('fields' => 'names')); return is_wp_error($terms) ? '' : implode('|', $terms); }
    public function csv_import() {
        if (! current_user_can('manage_feuer_members') || ! check_admin_referer('feuer_csv_import') || empty($_FILES['csv']['tmp_name']) || ! is_uploaded_file($_FILES['csv']['tmp_name']) || ! empty($_FILES['csv']['error']) || absint($_FILES['csv']['size'] ?? 0) > 5 * MB_IN_BYTES) wp_die('Ungültiger oder zu großer Import (maximal 5 MB).', 400);
        $handle = fopen($_FILES['csv']['tmp_name'], 'r'); $header = fgetcsv($handle, 0, ';'); $header = array_map(function($v){ return sanitize_key(ltrim($v, "\xEF\xBB\xBF")); }, (array)$header); $required = array('id','slug','name','rank','function','department','group','specialty'); if (array_diff($required, $header)) wp_die('CSV-Kopfzeile ist unvollständig.', 400);
        $rows = array(); while (($values = fgetcsv($handle, 0, ';')) !== false) { if (count($values) === count($header)) $rows[] = array_combine($header, $values); if (count($rows) > 2000) wp_die('Maximal 2000 Zeilen pro Import.', 400); } fclose($handle);
        if (! empty($_POST['dry_run'])) { wp_die(esc_html(sprintf('%d Datensätze geprüft. Keine Änderungen vorgenommen.', count($rows))), 'CSV Dry Run', array('back_link' => true)); }
        $rollback = array(); foreach ($rows as $row) { $id = absint($row['id']); if (! $id && $row['slug']) { $existing = get_page_by_path(sanitize_title($row['slug']), OBJECT, Feuer_Mannschaft_Plugin::POST_TYPE); $id = $existing ? $existing->ID : 0; } $old = $id ? get_post($id, ARRAY_A) : null; $old_meta = $id ? $this->profile($id) : array(); $id = wp_insert_post(array('ID'=>$id,'post_type'=>Feuer_Mannschaft_Plugin::POST_TYPE,'post_status'=>'publish','post_title'=>sanitize_text_field($row['name']),'post_name'=>sanitize_title($row['slug'])), true); if (is_wp_error($id)) continue; $rollback[] = array('id'=>$id,'new'=>!$old,'post'=>$old,'profile'=>$old_meta); $profile = $this->profile($id); $profile['rank']=sanitize_text_field($row['rank']); $profile['function']=sanitize_text_field($row['function']); update_post_meta($id, Feuer_Mannschaft_Plugin::PROFILE_META, $profile); foreach (array('department'=>'feuer_department','group'=>'feuer_group','specialty'=>'feuer_specialty') as $column=>$taxonomy) wp_set_object_terms($id, array_filter(array_map('sanitize_text_field', explode('|',$row[$column]))), $taxonomy, false); }
        set_transient(self::CSV_ROLLBACK_PREFIX . get_current_user_id(), $rollback, DAY_IN_SECONDS); wp_safe_redirect(add_query_arg(array('post_type'=>Feuer_Mannschaft_Plugin::POST_TYPE,'page'=>'feuer-mannschaft-csv','imported'=>count($rollback)), admin_url('edit.php'))); exit;
    }
    public function csv_rollback() {
        if (! current_user_can('manage_feuer_members') || ! check_admin_referer('feuer_csv_rollback')) wp_die('Nicht erlaubt.', 403); $items = get_transient(self::CSV_ROLLBACK_PREFIX . get_current_user_id()); foreach ((array)$items as $item) { if ($item['new']) wp_delete_post($item['id'], true); else { wp_update_post($item['post']); update_post_meta($item['id'], Feuer_Mannschaft_Plugin::PROFILE_META, $item['profile']); } } delete_transient(self::CSV_ROLLBACK_PREFIX . get_current_user_id()); wp_safe_redirect(admin_url('edit.php?post_type=' . Feuer_Mannschaft_Plugin::POST_TYPE . '&page=feuer-mannschaft-csv&rolled_back=1')); exit;
    }

    public function quality_menu() { add_submenu_page('edit.php?post_type=' . Feuer_Mannschaft_Plugin::POST_TYPE, 'Datenqualität', 'Datenqualität', 'manage_feuer_members', 'feuer-mannschaft-quality', array($this, 'quality_page')); }
    public function quality_page() {
        if (! current_user_can('manage_feuer_members')) return; $rows = array(); foreach ($this->members(array('post_status'=>array('publish','draft','feuer_archived'))) as $post) { $p=$this->profile($post->ID); $missing=array(); if(!has_post_thumbnail($post))$missing[]='Foto'; if(!$this->text_value($p,'rank'))$missing[]='Dienstgrad'; if(!$this->text_value($p,'function'))$missing[]='Funktion'; if(!has_term('','feuer_department',$post))$missing[]='Abteilung'; $score=max(0,100-count($missing)*25); $rows[]=array($post,$score,$missing); } usort($rows,function($a,$b){return $a[1]<=>$b[1];}); ?><div class="wrap"><h1>Datenqualität</h1><?php if($this->has('2.3.81')):?><p><a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=feuer_mannschaft_regenerate_images'),'feuer_regenerate_images'));?>">Profilbilder neu erzeugen</a></p><?php endif;?><table class="widefat striped"><thead><tr><th>Person</th><th>Vollständigkeit</th><th>Fehlt</th><th></th></tr></thead><tbody><?php foreach($rows as $row):?><tr><td><?php echo esc_html($row[0]->post_title);?></td><td><progress max="100" value="<?php echo esc_attr($row[1]);?>"></progress> <?php echo esc_html($row[1]);?>%</td><td><?php echo esc_html(implode(', ',$row[2]));?></td><td><a href="<?php echo esc_url(get_edit_post_link($row[0]->ID));?>">Bearbeiten</a></td></tr><?php endforeach;?></tbody></table></div><?php
    }

    public function image_meta_box() { add_meta_box('feuer-image-focus', 'Bildfokus', array($this,'render_image_focus'), Feuer_Mannschaft_Plugin::POST_TYPE, 'side'); }
    public function render_image_focus($post) { $focus=get_post_meta($post->ID,self::IMAGE_FOCUS_META,true); $focus=is_array($focus)?$focus:array('x'=>50,'y'=>50); wp_nonce_field('feuer_image_focus_'.$post->ID,'feuer_image_focus_nonce'); echo '<p>Legt den sichtbaren Bildausschnitt in Karten fest.</p><label>X % <input type="number" min="0" max="100" name="feuer_image_focus[x]" value="'.esc_attr($focus['x']).'"></label> <label>Y % <input type="number" min="0" max="100" name="feuer_image_focus[y]" value="'.esc_attr($focus['y']).'"></label>'; }
    public function save_image_focus($post_id) { if(!isset($_POST['feuer_image_focus_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feuer_image_focus_nonce'])),'feuer_image_focus_'.$post_id)||!current_user_can('edit_post',$post_id))return; $raw=isset($_POST['feuer_image_focus'])?(array)wp_unslash($_POST['feuer_image_focus']):array(); update_post_meta($post_id,self::IMAGE_FOCUS_META,array('x'=>max(0,min(100,absint($raw['x']??50))),'y'=>max(0,min(100,absint($raw['y']??50))))); }
    public function image_focus_attributes($attr,$attachment,$size) { if(!empty($attachment->post_parent)&&Feuer_Mannschaft_Plugin::POST_TYPE===get_post_type($attachment->post_parent)){ $focus=get_post_meta($attachment->post_parent,self::IMAGE_FOCUS_META,true); if(is_array($focus))$attr['style']=trim(($attr['style']??'').' object-position:'.absint($focus['x']).'% '.absint($focus['y']).'%;'); } return $attr; }
    public function regenerate_images() { if(!current_user_can('manage_feuer_members')||!check_admin_referer('feuer_regenerate_images'))wp_die('Nicht erlaubt.',403); require_once ABSPATH.'wp-admin/includes/image.php'; $count=0; foreach($this->members(array('fields'=>'ids')) as $id){$attachment=get_post_thumbnail_id($id);$file=$attachment?get_attached_file($attachment):'';if($file&&file_exists($file)){wp_update_attachment_metadata($attachment,wp_generate_attachment_metadata($attachment,$file));$count++;}} wp_safe_redirect(admin_url('edit.php?post_type='.Feuer_Mannschaft_Plugin::POST_TYPE.'&page=feuer-mannschaft-quality&images='.$count));exit; }

    public function seo_meta_box(){add_meta_box('feuer-seo','SEO & Social',array($this,'render_seo'),Feuer_Mannschaft_Plugin::POST_TYPE,'normal');}
    public function render_seo($post){$seo=get_post_meta($post->ID,self::SEO_META,true);$seo=is_array($seo)?$seo:array();wp_nonce_field('feuer_seo_'.$post->ID,'feuer_seo_nonce');echo '<p><label>SEO-Titel<input class="widefat" maxlength="70" name="feuer_seo[title]" value="'.esc_attr($seo['title']??'').'"></label></p><p><label>Beschreibung<textarea class="widefat" maxlength="180" name="feuer_seo[description]">'.esc_textarea($seo['description']??'').'</textarea></label></p><p><label><input type="checkbox" name="feuer_seo[noindex]" value="1" '.checked(!empty($seo['noindex']),true,false).'> Nicht indexieren</label></p>';}
    public function save_seo($post_id){if(!isset($_POST['feuer_seo_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feuer_seo_nonce'])),'feuer_seo_'.$post_id)||!current_user_can('edit_post',$post_id))return;$raw=isset($_POST['feuer_seo'])?(array)wp_unslash($_POST['feuer_seo']):array();update_post_meta($post_id,self::SEO_META,array('title'=>sanitize_text_field($raw['title']??''),'description'=>sanitize_textarea_field($raw['description']??''),'noindex'=>empty($raw['noindex'])?0:1));}
    private function seo_managed_externally(){return defined('WPSEO_VERSION')||defined('RANK_MATH_VERSION')||class_exists('The_SEO_Framework\\Load');}
    public function prepare_seo_head(){if(is_singular(Feuer_Mannschaft_Plugin::POST_TYPE)&&!$this->seo_managed_externally())remove_action('wp_head','rel_canonical');}
    public function seo_head(){if(!is_singular(Feuer_Mannschaft_Plugin::POST_TYPE)||$this->seo_managed_externally())return;$id=get_queried_object_id();$seo=get_post_meta($id,self::SEO_META,true);$title=!empty($seo['title'])?$seo['title']:get_the_title($id);$description=!empty($seo['description'])?$seo['description']:wp_strip_all_tags(get_the_excerpt($id));$image=get_the_post_thumbnail_url($id,'large');echo '<link rel="canonical" href="'.esc_url(get_permalink($id)).'">' . "\n";echo '<meta property="og:type" content="profile"><meta property="og:title" content="'.esc_attr($title).'"><meta property="og:description" content="'.esc_attr($description).'">';if($image)echo '<meta property="og:image" content="'.esc_url($image).'">';$schema=array('@context'=>'https://schema.org','@type'=>'Person','name'=>get_the_title($id),'url'=>get_permalink($id),'image'=>$image,'description'=>$description);echo '<script type="application/ld+json">'.wp_json_encode($schema,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>' . "\n";}
    public function robots($robots){if(!is_singular(Feuer_Mannschaft_Plugin::POST_TYPE))return $robots;$seo=get_post_meta(get_queried_object_id(),self::SEO_META,true);if((is_array($seo)&&!empty($seo['noindex']))||'feuer_archived'===get_post_status()){$robots['noindex']=true;$robots['nofollow']=true;}return $robots;}
    public function breadcrumbs_shortcode(){if(!is_singular(Feuer_Mannschaft_Plugin::POST_TYPE))return '';$archive=get_post_type_archive_link(Feuer_Mannschaft_Plugin::POST_TYPE);return '<nav class="feuer-breadcrumbs" aria-label="Breadcrumb"><a href="'.esc_url(home_url('/')).'">Start</a> <span aria-hidden="true">›</span> <a href="'.esc_url($archive).'">Mannschaft</a> <span aria-hidden="true">›</span> <span aria-current="page">'.esc_html(get_the_title()).'</span></nav>';}

    public function schedule_automation(){if(!wp_next_scheduled('feuer_mannschaft_daily_audit'))wp_schedule_event(time()+HOUR_IN_SECONDS,'daily','feuer_mannschaft_daily_audit');if(!wp_next_scheduled('feuer_mannschaft_weekly_report'))wp_schedule_event(time()+DAY_IN_SECONDS,'weekly','feuer_mannschaft_weekly_report');}
    public function daily_audit(){
        $broken=0;$content_ids=get_posts(array('post_type'=>'any','post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids','meta_key'=>Feuer_Mannschaft_Mentions::META_KEY));
        foreach($content_ids as $content_id){$ids=get_post_meta($content_id,Feuer_Mannschaft_Mentions::META_KEY,true);foreach((array)$ids as $id)if(Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type(absint($id))||'trash'===get_post_status(absint($id)))$broken++;}
        update_option('feuer_mannschaft_last_audit',array('time'=>time(),'broken_mentions'=>$broken),'no');$settings=$this->automation_settings();if($broken>0&&!empty($settings['notify_errors']))$this->notify(sprintf('Mannschaft: %d defekte Verknüpfungen gefunden.',$broken),$settings);
    }
    public function weekly_report(){ $settings=$this->automation_settings();$members=$this->members();$missing=0;foreach($members as $member)if(!has_post_thumbnail($member))$missing++;if(!empty($settings['weekly_email']))$this->notify(sprintf('Wochenbericht Mannschaft: %d aktive Profile, %d ohne Foto.',count($members),$missing),$settings);if(!empty($settings['weekly_backup'])){$manager=new Feuer_Mannschaft_Backup_Manager();$manager->ensure_storage(true);$manager->create_archive();}}
    private function automation_settings(){return wp_parse_args((array)get_option('feuer_mannschaft_automation_settings',array()),array('notify_errors'=>0,'weekly_email'=>0,'weekly_backup'=>0,'email'=>get_option('admin_email')));}
    private function notify($message,$settings){$email=sanitize_email($settings['email']??'');if(is_email($email))wp_mail($email,'Mannschaft – Systembericht',$message);}
    public function automation_menu(){add_submenu_page('edit.php?post_type='.Feuer_Mannschaft_Plugin::POST_TYPE,'Automatisierung','Automatisierung','manage_feuer_members','feuer-mannschaft-automation',array($this,'automation_page'));}
    public function automation_page(){
        if(!current_user_can('manage_feuer_members'))return;
        $audit=get_option('feuer_mannschaft_last_audit',array());$settings=$this->automation_settings();
        echo '<div class="wrap"><h1>Automatisierung</h1><div class="notice notice-info inline"><p>Die tägliche Integritätsprüfung ist aktiv. E-Mails und Sicherungen sind Opt-in.</p></div><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="feuer_mannschaft_save_automation">';
        wp_nonce_field('feuer_save_automation');
        echo '<table class="form-table"><tr><th>E-Mail</th><td><input type="email" class="regular-text" name="email" value="'.esc_attr($settings['email']).'"></td></tr><tr><th>Aufgaben</th><td><label><input type="checkbox" name="notify_errors" value="1" '.checked($settings['notify_errors'],true,false).'> Fehler melden</label><br><label><input type="checkbox" name="weekly_email" value="1" '.checked($settings['weekly_email'],true,false).'> Wochenbericht senden</label><br><label><input type="checkbox" name="weekly_backup" value="1" '.checked($settings['weekly_backup'],true,false).'> Wöchentliche Sicherung erstellen</label></td></tr></table>';
        submit_button();
        echo '</form><table class="widefat"><tr><th>Letzte Prüfung</th><td>'.(!empty($audit['time'])?esc_html(wp_date('d.m.Y H:i',$audit['time'])):'Noch nicht ausgeführt').'</td></tr><tr><th>Defekte Verknüpfungen</th><td>'.esc_html($audit['broken_mentions']??0).'</td></tr><tr><th>Log-Bereinigung</th><td>über die vorhandene Aufbewahrungsrichtlinie</td></tr></table></div>';
    }
    public function save_automation(){if(!current_user_can('manage_feuer_members')||!check_admin_referer('feuer_save_automation'))wp_die('Nicht erlaubt.',403);update_option('feuer_mannschaft_automation_settings',array('notify_errors'=>empty($_POST['notify_errors'])?0:1,'weekly_email'=>empty($_POST['weekly_email'])?0:1,'weekly_backup'=>empty($_POST['weekly_backup'])?0:1,'email'=>sanitize_email(isset($_POST['email'])?wp_unslash($_POST['email']):'')),false);wp_safe_redirect(admin_url('edit.php?post_type='.Feuer_Mannschaft_Plugin::POST_TYPE.'&page=feuer-mannschaft-automation&saved=1'));exit;}
}

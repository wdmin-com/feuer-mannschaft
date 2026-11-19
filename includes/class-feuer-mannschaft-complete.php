<?php
/**
 * Completes the production feature set introduced in the 2.3.69-2.3.83 series.
 *
 * This layer intentionally uses WordPress APIs and the existing profile IDs.
 * It does not create a second profile store.
 */
if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/traits/trait-feuer-mannschaft-complete-admin.php';
require_once __DIR__ . '/traits/trait-feuer-mannschaft-complete-import.php';
require_once __DIR__ . '/traits/trait-feuer-mannschaft-complete-operations.php';
require_once __DIR__ . '/traits/trait-feuer-mannschaft-complete-workflow.php';

final class Feuer_Mannschaft_Complete {
    use Feuer_Mannschaft_Complete_Admin;
    use Feuer_Mannschaft_Complete_Import;
    use Feuer_Mannschaft_Complete_Operations;
    use Feuer_Mannschaft_Complete_Workflow;
    const ORG_META = '_feuer_mannschaft_org';
    const VISIBILITY_META = '_feuer_mannschaft_field_visibility';
    const MEDIA_META = '_feuer_mannschaft_media';
    const MEDIA_HISTORY_META = '_feuer_mannschaft_media_history';
    const MODERN_IMAGE_META = '_feuer_mannschaft_modern_variants';
    const SEO_META = '_feuer_mannschaft_seo';
    const HIDDEN_MENTIONS_META = '_feuer_mannschaft_hidden_mentions';
    const IMPORT_TRANSIENT = 'feuer_mannschaft_import_preview_';
    const IMPORT_ROLLBACK_TRANSIENT = 'feuer_mannschaft_import_rollback_';
    const SETTINGS_OPTION = 'feuer_mannschaft_complete_settings';
    const AUDIT_OPTION = 'feuer_mannschaft_complete_audit';
    private static $instance;
    private $logger;

    public static function instance() {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->logger = new Feuer_Mannschaft_Logger();
        $legacy = Feuer_Mannschaft_Toolkit::instance();
        remove_action('add_meta_boxes', array($legacy, 'visibility_meta_box'));
        remove_action('add_meta_boxes', array($legacy, 'image_meta_box'));
        remove_action('add_meta_boxes', array($legacy, 'seo_meta_box'));
        remove_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($legacy, 'save_visibility'));
        remove_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($legacy, 'save_image_focus'));
        remove_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($legacy, 'save_seo'));
        remove_action('init', array($legacy, 'install_roles'));
        remove_filter('wp_get_attachment_image_attributes', array($legacy, 'image_focus_attributes'), 10);
        remove_filter('feuer_mannschaft_field_visible', array($legacy, 'filter_field_visibility'), 10);
        remove_action('wp_head', array($legacy, 'seo_head'), 1);
        remove_action('template_redirect', array($legacy, 'prepare_seo_head'));
        remove_action('template_redirect', array($legacy, 'download_vcard'));
        remove_action('template_redirect', array($legacy, 'print_profile'));
        remove_action('feuer_mannschaft_daily_audit', array($legacy, 'daily_audit'));
        remove_action('feuer_mannschaft_weekly_report', array($legacy, 'weekly_report'));

        remove_shortcode('feuer_member_directory');
        remove_shortcode('feuer_member_qr');
        remove_shortcode('feuer_member_actions');
        remove_shortcode('feuer_org_chart');
        remove_shortcode('feuer_mannschaft_org_chart');
        add_shortcode('feuer_member_directory', array($this, 'directory_shortcode'));
        add_shortcode('feuer_member_qr', array($this, 'qr_shortcode'));
        add_shortcode('feuer_member_actions', array($this, 'member_actions_shortcode'));
        add_shortcode('feuer_org_chart', array($this, 'org_chart_shortcode'));
        add_shortcode('feuer_mannschaft_org_chart', array($this, 'org_chart_shortcode'));

        add_action('init', array($this, 'install_capabilities'), 12);
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'), 90);
        add_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($this, 'save_member_extensions'), 90, 2);
        add_action('add_meta_boxes', array($this, 'register_mention_visibility_box'), 95);
        add_action('save_post', array($this, 'save_hidden_mentions'), 95, 2);
        add_action('admin_menu', array($this, 'replace_admin_pages'), 100);
        add_action('admin_menu', array($this, 'register_workflow_pages'), 110);
        add_action('add_meta_boxes', array($this, 'register_workflow_meta_box'), 100);
        add_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($this, 'capture_profile_before_save'), 1, 3);
        add_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($this, 'save_workflow_data'), 110, 2);
        add_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($this, 'audit_profile_after_save'), 130, 3);
        add_action('transition_post_status', array($this, 'audit_member_status_transition'), 40, 3);
        add_action('admin_post_feuer_complete_private_document', array($this, 'download_private_document'));
        add_action('admin_post_feuer_complete_delete_private_document', array($this, 'delete_private_document'));
        add_action('admin_notices', array($this, 'workflow_admin_notices'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_filter('feuer_mannschaft_member_meta', array($this, 'filter_member_meta'), 20, 2);
        add_filter('feuer_mannschaft_field_visible', array($this, 'field_visible'), 20, 3);
        add_filter('wp_get_attachment_image_attributes', array($this, 'image_attributes'), 20, 3);
        add_filter('wp_get_attachment_image', array($this, 'modern_image_markup'), 20, 5);
        add_filter('feuer_mannschaft_visual_attachment_id', array($this, 'visual_attachment_id'), 20, 4);
        add_filter('wp_handle_upload_prefilter', array($this, 'limit_profile_image_upload'));
        add_action('delete_attachment', array($this, 'delete_modern_image_variants'));
        add_filter('wp_sitemaps_posts_query_args', array($this, 'sitemap_query'), 20, 2);
        add_filter('wp_robots', array($this, 'robots'), 30);
        add_action('template_redirect', array($this, 'prepare_seo'));
        add_action('template_redirect', array($this, 'download_vcard'));
        add_action('template_redirect', array($this, 'print_profile'));
        add_action('wp_head', array($this, 'seo_head'), 1);

        add_action('admin_post_feuer_complete_import_preview', array($this, 'import_preview'));
        add_action('admin_post_feuer_complete_import_execute', array($this, 'import_execute'));
        add_action('admin_post_feuer_complete_import_rollback', array($this, 'import_rollback'));
        add_action('admin_post_feuer_complete_export', array($this, 'export_archive'));
        add_action('admin_post_feuer_complete_media_process', array($this, 'process_media'));
        add_action('admin_post_feuer_complete_save_automation', array($this, 'save_automation'));
        add_action('admin_post_feuer_complete_run_audit', array($this, 'run_audit_request'));
        add_action('admin_post_feuer_complete_confirm_member_review', array($this, 'confirm_member_review'));
        add_action('save_post_' . Feuer_Mannschaft_Plugin::POST_TYPE, array($this, 'notify_new_member_for_review'), 120, 3);
        add_action('admin_notices', array($this, 'member_review_admin_notice'));
        add_action('admin_notices', array($this, 'publish_validation_notice'));

        add_action('feuer_mannschaft_daily_audit', array($this, 'run_integrity_audit'), 20);
        add_action('feuer_mannschaft_weekly_report', array($this, 'run_weekly_maintenance'), 20);
        add_action('feuer_mannschaft_weekly_report', array($this, 'run_profile_lifecycle_automation'), 30);
        add_action('transition_post_status', array($this, 'notify_bulk_or_restore'), 20, 3);
        add_action('transition_post_status', array($this, 'notify_new_member_for_review_status'), 25, 3);
        add_filter('wp_insert_post_data', array($this, 'prevent_incomplete_profile_publication'), 20, 4);
    }

    public function install_capabilities() {
        if (FEUER_MANNSCHAFT_VERSION === get_option('feuer_mannschaft_roles_version')) {
            return;
        }
        $all_caps = array(
            'manage_feuer_members', 'edit_feuer_member', 'read_feuer_member', 'delete_feuer_member',
            'edit_feuer_members', 'edit_others_feuer_members', 'publish_feuer_members',
            'read_private_feuer_members', 'delete_feuer_members', 'delete_private_feuer_members',
            'delete_published_feuer_members', 'delete_others_feuer_members', 'edit_private_feuer_members',
            'edit_published_feuer_members', 'edit_feuer_mentions', 'view_feuer_editor_fields',
            'view_feuer_internal', 'delete_feuer_members_permanently',
        );
        $administrator = get_role('administrator');
        if ($administrator) {
            foreach ($all_caps as $cap) $administrator->add_cap($cap);
        }
        $editor = get_role('feuer_mannschaft_editor');
        if (! $editor) {
            add_role('feuer_mannschaft_editor', 'Mannschaft-Redakteur', array('read'=>true,'upload_files'=>true));
            $editor = get_role('feuer_mannschaft_editor');
        }
        if ($editor) {
            foreach (array('read','upload_files','edit_feuer_member','edit_feuer_members','edit_published_feuer_members','publish_feuer_members','delete_feuer_member','delete_feuer_members','edit_feuer_mentions','view_feuer_editor_fields') as $cap) $editor->add_cap($cap);
        }
        $author = get_role('author');
        if ($author) $author->add_cap('edit_feuer_mentions');
        update_option('feuer_mannschaft_roles_version', FEUER_MANNSCHAFT_VERSION, false);
    }

    public function register_meta_boxes() {
        add_meta_box('feuer-complete-structure', 'Struktur und Darstellung', array($this, 'render_structure_box'), Feuer_Mannschaft_Plugin::POST_TYPE, 'side', 'high');
        add_meta_box('feuer-complete-visibility', 'Sichtbarkeit aller Felder', array($this, 'render_visibility_box'), Feuer_Mannschaft_Plugin::POST_TYPE, 'normal', 'default');
        add_meta_box('feuer-complete-media', 'Profil- und Kartenbilder', array($this, 'render_media_box'), Feuer_Mannschaft_Plugin::POST_TYPE, 'normal', 'default');
        add_meta_box('feuer-complete-seo', 'SEO und soziale Netzwerke', array($this, 'render_seo_box'), Feuer_Mannschaft_Plugin::POST_TYPE, 'normal', 'default');
    }

    public function render_structure_box($post) {
        $org = $this->org_data($post->ID);
        wp_nonce_field('feuer_complete_member_' . $post->ID, 'feuer_complete_member_nonce');
        ?>
        <div class="feuer-extension-intro"><strong>Position im Organigramm</strong><p>Bestimmt Rolle, Sortierung und Hierarchie in der Organisationsansicht.</p></div>
        <div class="feuer-extension-field-grid is-single-column">
            <label class="feuer-extension-field" for="feuer-org-role"><span>Rolle</span><select class="widefat" id="feuer-org-role" name="feuer_complete_org[role]"><?php foreach (array('member'=>'Mitglied','leader'=>'Leitung','deputy'=>'Stellvertretung') as $value=>$label):?><option value="<?php echo esc_attr($value);?>" <?php selected($org['role'],$value);?>><?php echo esc_html($label);?></option><?php endforeach;?></select><small>Legt die visuelle Ebene im Organigramm fest.</small></label>
            <label class="feuer-extension-field" for="feuer-org-order"><span>Reihenfolge</span><input class="widefat" id="feuer-org-order" type="number" min="-9999" max="9999" name="feuer_complete_org[order]" value="<?php echo esc_attr($org['order']);?>"><small>Kleinere Werte werden weiter oben angezeigt.</small></label>
            <label class="feuer-extension-field" for="feuer-org-parent"><span>Vorgesetztes Profil</span><select class="widefat" id="feuer-org-parent" name="feuer_complete_org[parent]"><option value="0">Keine übergeordnete Person</option><?php foreach($this->members(array('exclude'=>array($post->ID))) as $member):?><option value="<?php echo esc_attr($member->ID);?>" <?php selected($org['parent'],$member->ID);?>><?php echo esc_html($member->post_title);?></option><?php endforeach;?></select><small>Optional. Verknüpft dieses Profil mit einer übergeordneten Person.</small></label>
        </div>
        <?php
    }

    private function field_labels() {
        return array(
            'first_name'=>'Vorname','last_name'=>'Nachname','job_title'=>'Berufsbezeichnung','entry_date'=>'Eintritt','rank'=>'Dienstgrad','function'=>'Funktion','education'=>'Qualifikation','description'=>'Beschreibung','content'=>'Profiltext','profile_image'=>'Profilbild','contact_fields'=>'Alle Kontaktfelder','custom_fields'=>'Alle Zusatzfelder','function_values'=>'Funktionswerte','education_values'=>'Ausbildungswerte','email'=>'E-Mail','phone'=>'Telefon','instagram'=>'Instagram','facebook'=>'Facebook','telegram'=>'Telegram','whatsapp'=>'WhatsApp','link'=>'Link','service_history'=>'Dienstverlauf','gallery'=>'Galerie','member_posts'=>'Erwähnungen',
        );
    }

    private function visibility_levels() {
        return array('public'=>'Öffentlich','logged_in'=>'Angemeldete Benutzer','editor'=>'Redakteure','admin'=>'Administratoren','hidden'=>'Vollständig verborgen');
    }

    public function render_visibility_box($post) {
        $saved = get_post_meta($post->ID, self::VISIBILITY_META, true); $saved = is_array($saved) ? $saved : array();
        echo '<div class="feuer-extension-intro"><strong>Sichtbarkeit gezielt steuern</strong><p>Wählen Sie pro Datenbereich, wer die Angaben auf der Website sehen darf.</p></div><div class="feuer-visibility-grid">';
        foreach ($this->field_labels() as $key=>$label) { $level=$saved[$key]??'public'; echo '<label><span>'.esc_html($label).'</span><select name="feuer_complete_visibility['.esc_attr($key).']">'; foreach($this->visibility_levels() as $value=>$caption) echo '<option value="'.esc_attr($value).'"'.selected($level,$value,false).'>'.esc_html($caption).'</option>'; echo '</select></label>'; }
        echo '</div><p class="description feuer-extension-security-note"><span class="dashicons dashicons-shield" aria-hidden="true"></span> Die Zugriffsprüfung erfolgt serverseitig; verborgene Daten werden nicht an den Browser ausgeliefert.</p>';
    }

    public function render_media_box($post) {
        $media=$this->media_data($post->ID); $history=get_post_meta($post->ID,self::MEDIA_HISTORY_META,true); $history=is_array($history)?array_slice(array_reverse($history),0,10):array();
        ?>
        <div class="feuer-extension-intro"><strong>Bildausschnitt für Karte und Profil</strong><p>Wählen Sie getrennte Motive oder verwenden Sie dasselbe Bild. Der Fokus legt fest, welcher Bereich beim Zuschneiden sichtbar bleibt.</p></div>
        <div class="feuer-media-grid">
            <?php foreach(array('card_image'=>'Kartenbild','profile_image'=>'Profilbild') as $key=>$label):$image_id=absint($media[$key]);$src=$image_id?wp_get_attachment_image_url($image_id,'medium'):'';?><div class="feuer-media-picker" data-feuer-media-picker><strong><?php echo esc_html($label);?></strong><input type="hidden" name="feuer_complete_media[<?php echo esc_attr($key);?>]" value="<?php echo esc_attr($image_id);?>" data-feuer-media-id><div class="feuer-media-preview" data-feuer-media-preview><?php if($src):?><img src="<?php echo esc_url($src);?>" alt=""><?php endif;?></div><p><button type="button" class="button" data-feuer-media-select>Bild auswählen</button> <button type="button" class="button-link-delete" data-feuer-media-remove <?php echo $image_id?'':'hidden';?>>Entfernen</button></p></div><?php endforeach;?>
            <label>Horizontaler Fokus (%)<input type="number" min="0" max="100" name="feuer_complete_media[x]" value="<?php echo esc_attr($media['x']);?>"><small>0 = links, 50 = Mitte, 100 = rechts</small></label>
            <label>Vertikaler Fokus (%)<input type="number" min="0" max="100" name="feuer_complete_media[y]" value="<?php echo esc_attr($media['y']);?>"><small>0 = oben, 50 = Mitte, 100 = unten</small></label>
            <label>Seitenverhältnis<select name="feuer_complete_media[ratio]"><?php foreach(array('3/4','4/5','1/1','16/9') as $ratio):?><option <?php selected($media['ratio'],$ratio);?>><?php echo esc_html($ratio);?></option><?php endforeach;?></select><small>Format der Bildfläche in Karten und Profilen</small></label>
        </div>
        <p><button type="button" class="button" data-feuer-detect-face>Gesicht lokal erkennen</button> <span class="description" data-feuer-face-status>Verwendet die FaceDetector-API des Browsers. Fehlt die API, können Sie den Fokus manuell festlegen.</span></p>
        <?php if($history):?><details><summary>Änderungsverlauf</summary><ul><?php foreach($history as $item):?><li><?php echo esc_html(wp_date('d.m.Y H:i',(int)$item['time']).' — '.$item['kind'].' #'.$item['from'].' → #'.$item['to']);?></li><?php endforeach;?></ul></details><?php endif;
    }

    public function render_seo_box($post) {
        $seo=$this->seo_data($post->ID); $og_src=$seo['og_image']?wp_get_attachment_image_url($seo['og_image'],'medium'):''; ?>
        <div class="feuer-extension-intro"><strong>Darstellung in Suchmaschinen und sozialen Netzwerken</strong><p>Leere Felder werden automatisch aus Profilname und Beschreibung erzeugt.</p></div>
        <div class="feuer-extension-field-grid">
            <label class="feuer-extension-field" for="feuer-complete-seo-title"><span>SEO-Titel</span><input id="feuer-complete-seo-title" class="widefat" maxlength="70" name="feuer_complete_seo[title]" value="<?php echo esc_attr($seo['title']);?>"><small>Empfohlen: höchstens 60–70 Zeichen.</small></label>
            <label class="feuer-extension-field feuer-extension-field-full" for="feuer-complete-seo-description"><span>Meta-Beschreibung</span><textarea id="feuer-complete-seo-description" class="widefat" maxlength="180" name="feuer_complete_seo[description]" rows="3"><?php echo esc_textarea($seo['description']);?></textarea><small>Kurze Zusammenfassung für Suchergebnisse und geteilte Links.</small></label>
            <div class="feuer-extension-field feuer-extension-field-full feuer-media-picker" data-feuer-media-picker><span>Vorschaubild beim Teilen</span><input type="hidden" name="feuer_complete_seo[og_image]" value="<?php echo esc_attr($seo['og_image']);?>" data-feuer-media-id><div class="feuer-media-preview" data-feuer-media-preview><?php if($og_src):?><img src="<?php echo esc_url($og_src);?>" alt=""><?php endif;?></div><p><button type="button" class="button" data-feuer-media-select>Bild auswählen</button> <button type="button" class="button-link-delete" data-feuer-media-remove <?php echo $seo['og_image']?'':'hidden';?>>Entfernen</button></p><small>Optional. Ohne Auswahl wird das Profilbild verwendet.</small></div>
            <label class="feuer-extension-check feuer-extension-field-full"><input type="checkbox" name="feuer_complete_seo[noindex]" value="1" <?php checked($seo['noindex']);?>><span><strong>Nicht in Suchmaschinen anzeigen</strong><small>Setzt <code>noindex</code> und entfernt das Profil aus der WordPress-Sitemap.</small></span></label>
        </div><?php
    }

    public function save_member_extensions($post_id, $post) {
        if (! isset($_POST['feuer_complete_member_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feuer_complete_member_nonce'])), 'feuer_complete_member_'.$post_id) || ! current_user_can('edit_post',$post_id) || wp_is_post_revision($post_id)) return;
        $old_media=$this->media_data($post_id);
        $org=isset($_POST['feuer_complete_org'])?(array)wp_unslash($_POST['feuer_complete_org']):array();
        $org_parent=$this->sanitize_org_parent($post_id,absint($org['parent']??0));
        update_post_meta($post_id,self::ORG_META,array('role'=>in_array($org['role']??'',array('member','leader','deputy'),true)?$org['role']:'member','order'=>max(-9999,min(9999,intval($org['order']??0))),'parent'=>$org_parent));
        $raw_vis=isset($_POST['feuer_complete_visibility'])?(array)wp_unslash($_POST['feuer_complete_visibility']):array();$vis=array();foreach($this->field_labels() as $key=>$label){$level=sanitize_key($raw_vis[$key]??'public');$vis[$key]=array_key_exists($level,$this->visibility_levels())?$level:'public';}update_post_meta($post_id,self::VISIBILITY_META,$vis);
        $raw_media=isset($_POST['feuer_complete_media'])?(array)wp_unslash($_POST['feuer_complete_media']):array();$media=array('card_image'=>$this->valid_image_id($raw_media['card_image']??0),'profile_image'=>$this->valid_image_id($raw_media['profile_image']??0),'x'=>max(0,min(100,absint($raw_media['x']??50))),'y'=>max(0,min(100,absint($raw_media['y']??50))),'ratio'=>in_array($raw_media['ratio']??'',array('3/4','4/5','1/1','16/9'),true)?$raw_media['ratio']:'3/4');update_post_meta($post_id,self::MEDIA_META,$media);$this->append_media_history($post_id,$old_media,$media);
        $raw_seo=isset($_POST['feuer_complete_seo'])?(array)wp_unslash($_POST['feuer_complete_seo']):array();update_post_meta($post_id,self::SEO_META,array('title'=>sanitize_text_field($raw_seo['title']??''),'description'=>sanitize_textarea_field($raw_seo['description']??''),'og_image'=>$this->valid_image_id($raw_seo['og_image']??0),'noindex'=>empty($raw_seo['noindex'])?0:1));
    }

    private function append_media_history($post_id,$old,$new) {
        $history=get_post_meta($post_id,self::MEDIA_HISTORY_META,true);$history=is_array($history)?$history:array();foreach(array('card_image','profile_image') as $kind){if((int)$old[$kind]!== (int)$new[$kind])$history[]=array('time'=>time(),'user'=>get_current_user_id(),'kind'=>$kind,'from'=>(int)$old[$kind],'to'=>(int)$new[$kind]);}if(count($history)>50)$history=array_slice($history,-50);update_post_meta($post_id,self::MEDIA_HISTORY_META,$history);
    }

    public function filter_member_meta($meta,$post_id) {
        if (is_admin() || wp_doing_cron() || (defined('REST_REQUEST') && REST_REQUEST && current_user_can('edit_post',$post_id))) return $meta;
        foreach(array_keys($meta) as $key){if(!apply_filters('feuer_mannschaft_field_visible',true,$key,$post_id)){if(in_array($key,array('contact_fields','custom_fields','function_values','education_values'),true))$meta[$key]=array();else $meta[$key]='';}}
        if(isset($meta['contact_fields'])&&is_array($meta['contact_fields']))foreach(array_keys($meta['contact_fields']) as $key)if(!apply_filters('feuer_mannschaft_field_visible',true,$key,$post_id))unset($meta['contact_fields'][$key]);
        return $meta;
    }

    public function field_visible($visible,$field,$post_id) {
        $rules=get_post_meta(absint($post_id),self::VISIBILITY_META,true);$level=is_array($rules)&&isset($rules[$field])?$rules[$field]:'public';
        if('hidden'===$level)return false;if('logged_in'===$level)return is_user_logged_in();if('editor'===$level)return current_user_can('view_feuer_editor_fields')||current_user_can('manage_options');if('admin'===$level)return current_user_can('manage_options');return (bool)$visible;
    }

    public function enqueue_frontend_assets() {
        wp_register_script('feuer-qrcode-local', FEUER_MANNSCHAFT_PLUGIN_URL.'assets/vendor/qrcode-generator/qrcode.js', array(), '2.0.4', true);
        wp_register_script('feuer-mannschaft-complete', FEUER_MANNSCHAFT_PLUGIN_URL.'assets/js/complete.js', array('feuer-qrcode-local'), FEUER_MANNSCHAFT_VERSION, true);
        wp_localize_script('feuer-mannschaft-complete','feuerMannschaftComplete',array('restUrl'=>esc_url_raw(rest_url('feuer-mannschaft/v1/directory')),'nonce'=>wp_create_nonce('wp_rest'),'labels'=>array('loading'=>'Profile werden geladen …','empty'=>'Keine passenden Profile gefunden.','error'=>'Profile konnten nicht geladen werden.','download'=>'QR-Code herunterladen','profiles'=>'Profile')));
        wp_register_style('feuer-mannschaft-complete', FEUER_MANNSCHAFT_PLUGIN_URL.'assets/css/complete.css', array(), FEUER_MANNSCHAFT_VERSION);
    }

    public function enqueue_admin_assets($hook) {
        if (false===strpos((string)$hook,'feuer') && ! ('post.php'===$hook||'post-new.php'===$hook)) return;
        wp_enqueue_media();
        wp_enqueue_script('feuer-qrcode-local', FEUER_MANNSCHAFT_PLUGIN_URL.'assets/vendor/qrcode-generator/qrcode.js', array(), '2.0.4', true);
        wp_enqueue_script('feuer-mannschaft-complete-admin', FEUER_MANNSCHAFT_PLUGIN_URL.'assets/js/complete-admin.js', array('jquery','feuer-qrcode-local'), FEUER_MANNSCHAFT_VERSION, true);
        wp_enqueue_style('feuer-mannschaft-complete-admin', FEUER_MANNSCHAFT_PLUGIN_URL.'assets/css/complete-admin.css', array(), FEUER_MANNSCHAFT_VERSION);
    }

    public function register_rest_routes() {
        register_rest_route('feuer-mannschaft/v1','/directory',array('methods'=>WP_REST_Server::READABLE,'callback'=>array($this,'rest_directory'),'permission_callback'=>'__return_true','args'=>array(
            'search'=>array('sanitize_callback'=>'sanitize_text_field'),'department'=>array('sanitize_callback'=>'sanitize_title'),'group'=>array('sanitize_callback'=>'sanitize_title'),'specialty'=>array('sanitize_callback'=>'sanitize_title'),'rank'=>array('sanitize_callback'=>'sanitize_text_field'),'function'=>array('sanitize_callback'=>'sanitize_text_field'),'status'=>array('sanitize_callback'=>'sanitize_key'),'sort'=>array('sanitize_callback'=>'sanitize_key'),'page'=>array('sanitize_callback'=>'absint','default'=>1),'per_page'=>array('sanitize_callback'=>'absint','default'=>24),
        )));
    }

    public function rest_directory(WP_REST_Request $request) {
        $status=sanitize_key((string)$request['status']);$can_view_archive=current_user_can('view_feuer_internal')||current_user_can('manage_feuer_members');if(in_array($status,array('archived','all'),true)&&!$can_view_archive)return new WP_Error('feuer_mannschaft_forbidden_archive',__('Archivprofile sind nur für berechtigte Benutzer sichtbar.','feuer-mannschaft'),array('status'=>403));$post_status='archived'===$status?'feuer_archived':('all'===$status?array('publish','feuer_archived'):'publish');
        $tax_query=array();foreach(array('department'=>'feuer_department','group'=>'feuer_group','specialty'=>'feuer_specialty') as $param=>$taxonomy)if($request[$param])$tax_query[]=array('taxonomy'=>$taxonomy,'field'=>'slug','terms'=>$request[$param]);
        if(count($tax_query)>1)$tax_query['relation']='AND';
        $query=new WP_Query(array('post_type'=>Feuer_Mannschaft_Plugin::POST_TYPE,'post_status'=>$post_status,'posts_per_page'=>5001,'fields'=>'ids','no_found_rows'=>true,'update_post_meta_cache'=>true,'update_post_term_cache'=>false,'s'=>$request['search'],'tax_query'=>$tax_query));
        $directory_ids=array_slice($query->posts,0,5000);$items=array();foreach($directory_ids as $member_id){$member_id=absint($member_id);$member=get_post($member_id);if(!$member instanceof WP_Post)continue;$profile=$this->profile($member_id);$rank=apply_filters('feuer_mannschaft_field_visible',true,'rank',$member_id)?$this->text($profile['rank']??''):'';$function=apply_filters('feuer_mannschaft_field_visible',true,'function',$member_id)?$this->text($profile['function']??''):'';if($request['rank']&&false===stripos($rank,$request['rank']))continue;if($request['function']&&false===stripos($function,$request['function']))continue;$org=$this->org_data($member_id);$entry=apply_filters('feuer_mannschaft_field_visible',true,'entry_date',$member_id)?absint($profile['entry_date']??0):0;$media=$this->media_data($member_id);$name=$this->directory_member_name($member_id,$profile);$can_link=apply_filters('feuer_mannschaft_field_visible',true,'first_name',$member_id)&&apply_filters('feuer_mannschaft_field_visible',true,'last_name',$member_id);$items[]=array('id'=>$member_id,'name'=>$name,'url'=>$name&&$can_link&&'feuer_archived'!==$member->post_status?get_permalink($member_id):'','rank'=>$rank,'function'=>$function,'entry'=>$entry,'serviceYears'=>$entry?max(0,(int)wp_date('Y')-$entry):0,'order'=>(int)$org['order'],'status'=>'feuer_archived'===$member->post_status?'archived':'active','image'=>$this->member_image_url($member_id,'card'),'imagePosition'=>absint($media['x']).'% '.absint($media['y']).'%','ratio'=>in_array($media['ratio'],array('3/4','4/5','1/1','16/9'),true)?$media['ratio']:'3/4');}
        $sort=$request['sort']?:'order';usort($items,function($a,$b)use($sort){if('name'===$sort)return strnatcasecmp($a['name'],$b['name']);if('name-desc'===$sort)return strnatcasecmp($b['name'],$a['name']);if('rank'===$sort)return strnatcasecmp($a['rank'],$b['rank']);if('tenure'===$sort)return $b['serviceYears']<=>$a['serviceYears'];return $a['order']<=>$b['order']?:strnatcasecmp($a['name'],$b['name']);});
        $total=count($items);$per=max(1,min(100,absint($request['per_page'])));$page=max(1,absint($request['page']));$items=array_slice($items,($page-1)*$per,$per);
        return rest_ensure_response(array('items'=>$items,'total'=>$total,'pages'=>(int)ceil($total/$per),'page'=>$page,'truncated'=>count($query->posts)>5000));
    }

    private function directory_member_name($post_id,$profile) {
        unset($profile);
        return Feuer_Mannschaft_Plugin::instance()->get_member_display_name($post_id);
    }

    public function visible_member_name($post_id) {
        $name=Feuer_Mannschaft_Plugin::instance()->get_member_display_name(absint($post_id));
        return ''!==trim((string)$name)?$name:__('Mitglied','feuer-mannschaft');
    }

    public function directory_shortcode($atts) {
        $atts=shortcode_atts(array('per_page'=>24,'status'=>'active'),$atts,'feuer_member_directory');$can_view_archive=current_user_can('view_feuer_internal')||current_user_can('manage_feuer_members');$default_status=sanitize_key($atts['status']);if(!in_array($default_status,array('active','archived','all'),true)||(!$can_view_archive&&'active'!==$default_status))$default_status='active';$departments=get_terms(array('taxonomy'=>'feuer_department','hide_empty'=>true));$groups=get_terms(array('taxonomy'=>'feuer_group','hide_empty'=>true));$specialties=get_terms(array('taxonomy'=>'feuer_specialty','hide_empty'=>true));$ranks=$this->profile_values('rank');$functions=$this->profile_values('function');wp_enqueue_script('feuer-mannschaft-complete');wp_enqueue_style('feuer-mannschaft-complete');
        ob_start();?><section class="feuer-complete-directory" data-feuer-directory data-default-status="<?php echo esc_attr($default_status);?>" data-per-page="<?php echo esc_attr(max(1,min(100,absint($atts['per_page']))));?>"><form class="row g-3 feuer-complete-filters" data-directory-filters role="search"><div class="col-12 col-lg-4"><label class="form-label">Nach Namen suchen<input class="form-control" type="search" name="search" autocomplete="off"></label></div><?php echo $this->directory_select('department','Abteilung',$departments);echo $this->directory_select('group','Gruppe',$groups);echo $this->directory_select('specialty','Spezialisierung',$specialties);echo $this->value_select('rank','Dienstgrad',$ranks);echo $this->value_select('function','Funktion',$functions);?><div class="col-12 col-md-4 col-lg-3"><label class="form-label">Status<select class="form-select" name="status"><option value="active" <?php selected($default_status,'active');?>>Aktiv</option><?php if($can_view_archive):?><option value="archived" <?php selected($default_status,'archived');?>>Archiv</option><option value="all" <?php selected($default_status,'all');?>>Alle</option><?php endif;?></select></label></div><div class="col-12 col-md-4 col-lg-3"><label class="form-label">Sortierung<select class="form-select" name="sort"><option value="order">Vorgegebene Reihenfolge</option><option value="name">Name A–Z</option><option value="name-desc">Name Z–A</option><option value="rank">Dienstgrad</option><option value="tenure">Dienstzeit</option></select></label></div><div class="col-12 d-flex gap-2"><button class="btn btn-primary" type="submit">Anwenden</button><button class="btn btn-outline-secondary" type="reset">Zurücksetzen</button></div></form><div class="feuer-directory-status" aria-live="polite" data-directory-status></div><div class="row g-4" data-directory-results></div><nav aria-label="Mitgliederseiten"><div class="btn-group" data-directory-pagination></div></nav></section><?php return ob_get_clean();
    }

    private function directory_select($name,$label,$terms){if(is_wp_error($terms))$terms=array();$options=array();foreach($terms as $term)$options[$term->slug]=$term->name;return $this->select_markup($name,$label,$options);}
    private function value_select($name,$label,$values){$options=array();foreach($values as $value)$options[$value]=$value;return $this->select_markup($name,$label,$options);}
    private function select_markup($name,$label,$options){$html='<div class="col-12 col-md-4 col-lg-3"><label class="form-label">'.esc_html($label).'<select class="form-select" name="'.esc_attr($name).'"><option value="">Alle</option>';foreach($options as $value=>$caption)$html.='<option value="'.esc_attr($value).'">'.esc_html($caption).'</option>';return $html.'</select></label></div>';}
    private function profile_values($key){$out=array();foreach($this->members() as $member){if(!apply_filters('feuer_mannschaft_field_visible',true,$key,$member->ID))continue;$decoded=get_post_meta($member->ID,Feuer_Mannschaft_Plugin::META_PREFIX.$key,true);foreach((array)$decoded as $item)if(is_scalar($item)&&''!==trim((string)$item))$out[]=sanitize_text_field($item);}natcasesort($out);return array_values(array_unique($out));}

    public function qr_shortcode($atts) {
        $atts=shortcode_atts(array('id'=>get_the_ID(),'size'=>220,'download'=>'1'),$atts,'feuer_member_qr');$id=absint($atts['id']);if(Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type($id)||'publish'!==get_post_status($id))return '';wp_enqueue_script('feuer-mannschaft-complete');wp_enqueue_style('feuer-mannschaft-complete');$url=get_permalink($id);return '<figure class="feuer-local-qr" data-local-qr data-qr-value="'.esc_attr($url).'" data-qr-size="'.esc_attr(max(96,min(640,absint($atts['size'])))).'"><div data-qr-output></div><figcaption>'.esc_html($this->visible_member_name($id)).'</figcaption>'.('1'===$atts['download']?'<button class="btn btn-outline-secondary btn-sm" type="button" data-qr-download>QR-Code herunterladen</button>':'').'</figure>';
    }

    public function member_actions_shortcode($atts) {$id=absint(shortcode_atts(array('id'=>get_the_ID()),$atts,'feuer_member_actions')['id']);if(Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type($id)||'publish'!==get_post_status($id))return '';$vcard=wp_nonce_url(add_query_arg(array('feuer_vcard'=>$id),home_url('/')),'feuer_vcard_'.$id);$print=add_query_arg(array('feuer_print'=>$id),home_url('/'));return '<div class="btn-group feuer-member-actions"><a class="btn btn-outline-primary" href="'.esc_url($vcard).'">Kontaktkarte (.vcf)</a><a class="btn btn-outline-secondary" href="'.esc_url($print).'" target="_blank" rel="noopener">Druckansicht</a></div>';}

    public function org_chart_shortcode($atts) {
        unset($atts);$departments=get_terms(array('taxonomy'=>'feuer_department','hide_empty'=>true,'orderby'=>'name'));if(is_wp_error($departments)||!$departments)return '';wp_enqueue_style('feuer-mannschaft-complete');ob_start();?><section class="feuer-org-chart-complete" aria-label="Organisationsstruktur"><?php foreach($departments as $department):$ids=get_posts(array('post_type'=>Feuer_Mannschaft_Plugin::POST_TYPE,'post_status'=>'publish','posts_per_page'=>-1,'fields'=>'ids','tax_query'=>array(array('taxonomy'=>'feuer_department','field'=>'term_id','terms'=>$department->term_id))));usort($ids,array($this,'compare_org_members'));$parent_map=$this->safe_org_parent_map($ids);$children=array(0=>array());foreach($ids as $id)$children[$parent_map[absint($id)]??0][]=absint($id);?><details open><summary><strong><?php echo esc_html($department->name);?></strong><span><?php echo esc_html(count($ids));?></span></summary><?php echo $this->render_org_nodes($children[0]??array(),$children,array());?></details><?php endforeach;?></section><?php return ob_get_clean();
    }

    private function render_org_nodes($ids,$children,$visited){if(!$ids)return '';$html='<div class="feuer-org-branch">';foreach($ids as $id){$id=absint($id);if(isset($visited[$id]))continue;$next=$visited;$next[$id]=true;$org=$this->org_data($id);$groups=wp_get_post_terms($id,'feuer_group',array('fields'=>'names'));$html.='<div class="feuer-org-node"><article class="feuer-org-person feuer-org-role-'.esc_attr($org['role']).'"><span class="feuer-org-role">'.esc_html(array('leader'=>'Leitung','deputy'=>'Stellvertretung','member'=>'Mitglied')[$org['role']]??'Mitglied').'</span><a href="'.esc_url(get_permalink($id)).'">'.esc_html($this->visible_member_name($id)).'</a>';if(!is_wp_error($groups)&&$groups)$html.='<small>'.esc_html(implode(', ',$groups)).'</small>';$html.='</article>'.$this->render_org_nodes($children[$id]??array(),$children,$next).'</div>';}$html.='</div>';return $html;}

    public function compare_org_members($a,$b){$oa=$this->org_data($a);$ob=$this->org_data($b);$weight=array('leader'=>0,'deputy'=>1,'member'=>2);return ($weight[$oa['role']]<=>$weight[$ob['role']])?:($oa['order']<=>$ob['order'])?:strnatcasecmp(get_the_title($a),get_the_title($b));}

    private function member_image_url($id,$kind='card'){if(!apply_filters('feuer_mannschaft_field_visible',true,'profile_image',$id))return '';$media=$this->media_data($id);$attachment=absint('profile'===$kind?$media['profile_image']:$media['card_image']);if(!$attachment){$gallery=get_post_meta($id,Feuer_Mannschaft_Plugin::GALLERY_META,true);$attachment=is_array($gallery)&&$gallery?absint($gallery[0]):get_post_thumbnail_id($id);}return $attachment?(string)wp_get_attachment_image_url($attachment,'large'):'';}
    private function text($value){if(is_array($value))$value=implode(', ',$value);return sanitize_text_field((string)$value);}
    private function valid_image_id($value){$id=absint($value);return $id&&wp_attachment_is_image($id)?$id:0;}

    private function org_data($id){return wp_parse_args((array)get_post_meta($id,self::ORG_META,true),array('role'=>'member','order'=>0,'parent'=>0));}
    private function sanitize_org_parent($post_id,$parent){$post_id=absint($post_id);$parent=absint($parent);if(!$parent||$parent===$post_id||Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type($parent))return 0;$seen=array($post_id=>true);$cursor=$parent;while($cursor){if(isset($seen[$cursor]))return 0;$seen[$cursor]=true;$next=absint($this->org_data($cursor)['parent']);if($next===$cursor)return 0;$cursor=$next;}return $parent;}
    private function safe_org_parent_map($ids){$known=array_fill_keys(array_map('absint',$ids),true);$map=array();foreach($known as $id=>$unused){unset($unused);$parent=absint($this->org_data($id)['parent']);$map[$id]=isset($known[$parent])&&$parent!==$id?$parent:0;}foreach(array_keys($map) as $id){$seen=array($id=>true);$cursor=$map[$id];while($cursor){if(isset($seen[$cursor])){$map[$id]=0;break;}$seen[$cursor]=true;$cursor=$map[$cursor]??0;}}return $map;}
    private function media_data($id){return wp_parse_args((array)get_post_meta($id,self::MEDIA_META,true),array('card_image'=>0,'profile_image'=>0,'x'=>50,'y'=>50,'ratio'=>'3/4'));}
    private function seo_data($id){return wp_parse_args((array)get_post_meta($id,self::SEO_META,true),array('title'=>'','description'=>'','og_image'=>0,'noindex'=>0));}
    private function members($args=array()){return get_posts(wp_parse_args($args,array('post_type'=>Feuer_Mannschaft_Plugin::POST_TYPE,'post_status'=>'publish','posts_per_page'=>-1,'orderby'=>'title','order'=>'ASC')));}
    private function profile($id){$keys=array('first_name','last_name','job_title','entry_date','rank','function','education','sort_order','description','contact_fields','custom_fields');$out=array();foreach($keys as $key)$out[$key]=get_post_meta($id,Feuer_Mannschaft_Plugin::META_PREFIX.$key,true);return $out;}
}

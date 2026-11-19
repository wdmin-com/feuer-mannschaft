<?php
/** Media, SEO, integrity and scheduled operations. */
if (! defined('ABSPATH')) { exit; }

trait Feuer_Mannschaft_Complete_Operations {
    private $complete_image_context = array();

    public function visual_attachment_id($attachment_id,$post_id,$size,$class) {
        if(!apply_filters('feuer_mannschaft_field_visible',true,'profile_image',$post_id))return 0;
        $media=$this->media_data($post_id);$is_profile=false;
        if(is_singular(Feuer_Mannschaft_Plugin::POST_TYPE))$is_profile=true;
        if(false!==strpos((string)$class,'profile')||false!==strpos((string)$class,'single'))$is_profile=true;
        $selected=absint($is_profile?$media['profile_image']:$media['card_image']);if(!$selected)$selected=absint($attachment_id);if($selected)$this->complete_image_context[$selected]=absint($post_id);return $selected;
    }

    public function image_attributes($attr,$attachment,$size) {
        $attachment_id=$attachment instanceof WP_Post?absint($attachment->ID):0;$member_id=$this->complete_image_context[$attachment_id]??0;
        if(!$member_id && is_singular(Feuer_Mannschaft_Plugin::POST_TYPE))$member_id=get_queried_object_id();if(!$member_id)return $attr;
        $media=$this->media_data($member_id);$attr['style']=trim(($attr['style']??'').' object-position:'.absint($media['x']).'% '.absint($media['y']).'%;');$attr['data-feuer-ratio']=$media['ratio'];return $attr;
    }

    public function modern_image_markup($html,$attachment_id,$size,$icon,$attr) {
        unset($size,$icon,$attr);$attachment_id=absint($attachment_id);if(!$attachment_id||empty($this->complete_image_context[$attachment_id])||false!==stripos($html,'<picture'))return $html;$variants=get_post_meta($attachment_id,self::MODERN_IMAGE_META,true);if(!is_array($variants)||!$variants)return $html;$sources='';foreach(array('avif'=>'image/avif','webp'=>'image/webp') as $extension=>$mime)if(!empty($variants[$extension]['url'])&&!empty($variants[$extension]['path'])&&is_readable($variants[$extension]['path']))$sources.='<source type="'.esc_attr($mime).'" srcset="'.esc_url($variants[$extension]['url']).'">';return $sources?'<picture class="feuer-modern-image">'.$sources.$html.'</picture>':$html;
    }

    public function limit_profile_image_upload($file) {
        $post_id=absint($_REQUEST['post_id']??0);if(!$post_id||Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type($post_id))return $file;$settings=$this->automation_settings();$max=max(1,min(32,absint($settings['image_max_mb'])))*MB_IN_BYTES;if(!empty($file['size'])&&$file['size']>$max)$file['error']=sprintf('Profilbilder dürfen maximal %d MB groß sein.',(int)($max/MB_IN_BYTES));return $file;
    }

    public function delete_modern_image_variants($attachment_id) {$this->remove_modern_variant_files((array)get_post_meta(absint($attachment_id),self::MODERN_IMAGE_META,true));}

    private function remove_modern_variant_files($variants,$keep=array()) {$uploads=wp_upload_dir();if(!empty($uploads['error'])||empty($uploads['basedir']))return;$base=trailingslashit(wp_normalize_path($uploads['basedir']));$keep=array_map('wp_normalize_path',(array)$keep);foreach((array)$variants as $variant){$path=wp_normalize_path((string)($variant['path']??''));if(!$path||0!==strpos($path,$base)||in_array($path,$keep,true)||!preg_match('/-(?:webp|avif)\.(?:webp|avif)$/i',$path))continue;if(is_file($path))wp_delete_file($path);}}

    public function process_media() {
        $this->verify_complete_request('feuer_complete_media_process');require_once ABSPATH.'wp-admin/includes/image.php';$processed=0;$unsupported=array();$seen=array();
        $uploads=wp_upload_dir();$upload_base=empty($uploads['basedir'])?'':wp_normalize_path($uploads['basedir']);foreach($this->members(array('post_status'=>array('publish','draft','private','feuer_archived'))) as $member){$media=$this->media_data($member->ID);$gallery=(array)get_post_meta($member->ID,Feuer_Mannschaft_Plugin::GALLERY_META,true);$ids=array_filter(array_unique(array_merge(array(absint($media['card_image']),absint($media['profile_image']),get_post_thumbnail_id($member->ID)),$gallery)));foreach($ids as $id){if(isset($seen[$id]))continue;$seen[$id]=true;$file=get_attached_file($id);if(!$file||!is_readable($file)||!empty($uploads['error'])||!$upload_base||0!==strpos(wp_normalize_path($file),trailingslashit($upload_base)))continue;wp_update_attachment_metadata($id,wp_generate_attachment_metadata($id,$file));$old_variants=(array)get_post_meta($id,self::MODERN_IMAGE_META,true);$variants=array();foreach(array('image/webp'=>'webp','image/avif'=>'avif') as $mime=>$extension){if(!wp_image_editor_supports(array('mime_type'=>$mime))){$unsupported[$extension]=true;continue;}$editor=wp_get_image_editor($file);if(is_wp_error($editor))continue;$destination=preg_replace('/\.[^.]+$/','-'.$extension.'.'.$extension,$file);$saved=$editor->save($destination,$mime);if(!is_wp_error($saved)&&!empty($saved['path'])){$variant_path=wp_normalize_path($saved['path']);if(0===strpos($variant_path,trailingslashit($upload_base))){$variants[$extension]=array('path'=>$variant_path,'url'=>$uploads['baseurl'].substr($variant_path,strlen($upload_base)));$processed++;}}}$this->remove_modern_variant_files($old_variants,array_column($variants,'path'));if($variants)update_post_meta($id,self::MODERN_IMAGE_META,$variants);else delete_post_meta($id,self::MODERN_IMAGE_META);}}
        $details=$unsupported?array('Server unterstützt nicht: '.implode(', ',array_keys($unsupported))):array();$this->set_import_result(sprintf('%d moderne Bildvarianten erzeugt; Metadaten aller Profilbilder aktualisiert.',$processed),false,$details);wp_safe_redirect($this->complete_admin_url('feuer-mannschaft-quality'));exit;
    }

    public function download_vcard(){if(empty($_GET['feuer_vcard']))return;$id=absint($_GET['feuer_vcard']);$nonce=sanitize_text_field(wp_unslash($_GET['_wpnonce']??''));if(!wp_verify_nonce($nonce,'feuer_vcard_'.$id)||Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type($id)||'publish'!==get_post_status($id))wp_die('Ungültige Anfrage.',403);$p=$this->profile($id);$contacts=is_array($p['contact_fields']??null)?$p['contact_fields']:array();$lines=array('BEGIN:VCARD','VERSION:3.0','FN:'.$this->complete_vcard_escape($this->visible_member_name($id)),'ORG:'.$this->complete_vcard_escape(get_bloginfo('name')));$fields=array('function'=>array('TITLE',$p['function']??''),'email'=>array('EMAIL',$contacts['email']??''),'phone'=>array('TEL',$contacts['phone']??''));foreach($fields as $key=>$definition){$value=$this->text($definition[1]);if($value&&apply_filters('feuer_mannschaft_field_visible',true,$key,$id))$lines[]=$definition[0].':'.$this->complete_vcard_escape($value);}$lines[]='URL:'.get_permalink($id);$lines[]='END:VCARD';nocache_headers();header('Content-Type: text/vcard; charset=utf-8');header('Content-Disposition: attachment; filename="'.sanitize_file_name($this->visible_member_name($id)).'.vcf"');echo implode("\r\n",$lines);exit;}
    private function complete_vcard_escape($value){return str_replace(array('\\',';',',',"\r","\n"),array('\\\\','\\;','\\,','','\\n'),(string)$value);}
    public function print_profile(){if(empty($_GET['feuer_print']))return;$id=absint($_GET['feuer_print']);if(Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type($id)||'publish'!==get_post_status($id))wp_die('Profil nicht gefunden.',404);$p=$this->profile($id);$rank=apply_filters('feuer_mannschaft_field_visible',true,'rank',$id)?$this->text($p['rank']??''):'';$function=apply_filters('feuer_mannschaft_field_visible',true,'function',$id)?$this->text($p['function']??''):'';$content=apply_filters('feuer_mannschaft_field_visible',true,'content',$id)?apply_filters('the_content',get_post_field('post_content',$id)):'';$image=$this->member_image_url($id,'profile');nocache_headers();?><!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?php echo esc_html($this->visible_member_name($id));?></title><style>body{font:16px/1.55 system-ui;max-width:780px;margin:40px auto;padding:24px;color:#111827}img{width:280px;max-width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:16px}button{padding:.65rem 1rem}@media print{button{display:none}body{margin:0}}</style></head><body><button type="button" onclick="window.print()">Drucken</button><article><?php if($image):?><img src="<?php echo esc_url($image);?>" alt=""><?php endif;?><h1><?php echo esc_html($this->visible_member_name($id));?></h1><?php if($rank||$function):?><p><?php echo esc_html(trim($rank.($rank&&$function?' · ':'').$function));?></p><?php endif;?><div><?php echo wp_kses_post($content);?></div></article></body></html><?php exit;}

    public function sitemap_query($args,$post_type) {if(Feuer_Mannschaft_Plugin::POST_TYPE===$post_type)$args['post_status']='publish';return $args;}
    private function seo_managed_externally(){return defined('WPSEO_VERSION')||defined('RANK_MATH_VERSION')||defined('SEOPRESS_VERSION')||defined('AIOSEO_VERSION')||class_exists('AIOSEO\\Plugin\\Common\\Main')||class_exists('The_SEO_Framework\\Load');}
    public function prepare_seo(){if(is_singular(Feuer_Mannschaft_Plugin::POST_TYPE)&&!$this->seo_managed_externally())remove_action('wp_head','rel_canonical');}
    public function robots($robots){if(!is_singular(Feuer_Mannschaft_Plugin::POST_TYPE))return $robots;$id=get_queried_object_id();$seo=$this->seo_data($id);if(!empty($seo['noindex'])||'publish'!==get_post_status($id)){$robots['noindex']=true;$robots['nofollow']=true;}return $robots;}
    public function seo_head(){if(!is_singular(Feuer_Mannschaft_Plugin::POST_TYPE)||$this->seo_managed_externally())return;$id=get_queried_object_id();if('publish'!==get_post_status($id))return;$seo=$this->seo_data($id);$title=$seo['title']?:$this->visible_member_name($id);$content_visible=apply_filters('feuer_mannschaft_field_visible',true,'content',$id);$description=$seo['description']?:($content_visible?wp_trim_words(wp_strip_all_tags(get_post_field('post_content',$id)),30,'…'):'');$image_id=absint($seo['og_image']);$image=apply_filters('feuer_mannschaft_field_visible',true,'profile_image',$id)?($image_id?wp_get_attachment_image_url($image_id,'full'):$this->member_image_url($id,'profile')):'';$profile=$this->profile($id);$job_title=apply_filters('feuer_mannschaft_field_visible',true,'function',$id)?$this->text($profile['function']??''):'';$schema=array_filter(array('@context'=>'https://schema.org','@type'=>'Person','name'=>$this->visible_member_name($id),'url'=>get_permalink($id),'description'=>$description,'image'=>$image,'jobTitle'=>$job_title,'memberOf'=>array('@type'=>'Organization','name'=>get_bloginfo('name'),'url'=>home_url('/'))));echo '<link rel="canonical" href="'.esc_url(get_permalink($id)).'">'."\n".'<meta property="og:type" content="profile"><meta property="og:title" content="'.esc_attr($title).'">';if($description)echo '<meta property="og:description" content="'.esc_attr($description).'">';if($image)echo '<meta property="og:image" content="'.esc_url($image).'">';echo '<script type="application/ld+json">'.wp_json_encode($schema,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";}

    public function run_audit_request(){$this->verify_complete_request('feuer_complete_run_audit');$this->run_integrity_audit();wp_safe_redirect($this->complete_admin_url('feuer-mannschaft-quality'));exit;}
    public function run_integrity_audit(){$audit=$this->build_integrity_audit();update_option(self::AUDIT_OPTION,$audit,false);$s=$this->automation_settings();if(!empty($s['notify_errors'])&&$audit['issues']>0)$this->send_complete_mail('Integritätsprüfung',sprintf('%d Hinweise in %d Profilen. Qualitätswert: %d/100',$audit['issues'],$audit['profiles'],$audit['score']),$s);return $audit;}
    private function build_integrity_audit(){
        $rows=array();$total_issues=0;$names=array();$attachment_ids=array();foreach($this->members(array('post_status'=>array('publish','draft','private','feuer_archived'))) as $post){$p=$this->profile($post->ID);$issues=array();$normalized=remove_accents(strtolower(trim($post->post_title)));if(isset($names[$normalized]))$issues[]='Doppelter Name';else$names[$normalized]=$post->ID;$media=$this->media_data($post->ID);$image=absint($media['profile_image'])?:absint($media['card_image'])?:get_post_thumbnail_id($post->ID);if(!$image)$issues[]='Foto fehlt';elseif(!wp_attachment_is_image($image)||!get_attached_file($image))$issues[]='Bilddatei fehlt';else{$attachment_ids[]=$image;$file=get_attached_file($image);if($file&&filesize($file)>max(1,absint($this->automation_settings()['image_max_mb']))*MB_IN_BYTES)$issues[]='Bild zu groß';}if(!$this->text($p['rank']??''))$issues[]='Dienstgrad fehlt';if(!$this->text($p['function']??''))$issues[]='Funktion fehlt';if(!has_term('','feuer_department',$post))$issues[]='Abteilung fehlt';if(!$post->post_name)$issues[]='URL fehlt';$score=max(0,100-count($issues)*15);$total_issues+=count($issues);$rows[]=array('id'=>$post->ID,'name'=>$post->post_title,'score'=>$score,'issues'=>$issues);}
        $broken=0;$content_ids=get_posts(array('post_type'=>'any','post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids','meta_key'=>Feuer_Mannschaft_Mentions::META_KEY));foreach($content_ids as $content_id)foreach((array)get_post_meta($content_id,Feuer_Mannschaft_Mentions::META_KEY,true) as $id)if(Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type(absint($id))||'trash'===get_post_status(absint($id)))$broken++;global $wpdb;$legacy=(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type IN (%s,%s)",Feuer_Mannschaft_Plugin::LEGACY_POST_TYPE,Feuer_Mannschaft_Plugin::LEGACY_POST_TYPE_TRUNCATED));$total_issues+=$broken+$legacy;$profiles=count($rows);$score=$profiles?max(0,(int)round(array_sum(array_column($rows,'score'))/$profiles)-min(20,($broken+$legacy)*2)):100;usort($rows,function($a,$b){return $a['score']<=>$b['score'];});return array('time'=>time(),'profiles'=>$profiles,'issues'=>$total_issues,'broken_mentions'=>$broken,'legacy_profiles'=>$legacy,'score'=>$score,'rows'=>$rows);
    }

    private function automation_settings(){return wp_parse_args((array)get_option(self::SETTINGS_OPTION,array()),array('email'=>get_option('admin_email'),'notify_errors'=>0,'weekly_email'=>0,'weekly_backup'=>0,'missing_photo'=>0,'anniversaries'=>0,'bulk_notifications'=>0,'annual_review'=>0,'qualification_expiries'=>0,'service_end_reminders'=>0,'onboarding_notifications'=>0,'change_report'=>0,'log_days'=>90,'image_max_mb'=>8));}
    public function save_automation(){$this->verify_complete_request('feuer_complete_save_automation');$raw=wp_unslash($_POST);$settings=array('email'=>sanitize_email($raw['email']??''),'log_days'=>max(7,min(3650,absint($raw['log_days']??90))),'image_max_mb'=>max(1,min(32,absint($raw['image_max_mb']??8))));foreach(array('notify_errors','weekly_email','weekly_backup','missing_photo','anniversaries','bulk_notifications','annual_review','qualification_expiries','service_end_reminders','onboarding_notifications','change_report') as $key)$settings[$key]=empty($raw[$key])?0:1;update_option(self::SETTINGS_OPTION,$settings,false);$plugin_settings=(array)get_option(Feuer_Mannschaft_Plugin::OPTION_KEY,array());$plugin_settings['log_retention_days']=$settings['log_days'];update_option(Feuer_Mannschaft_Plugin::OPTION_KEY,$plugin_settings,false);wp_safe_redirect($this->complete_admin_url('feuer-mannschaft-automation',array('saved'=>1)));exit;}
    public function run_weekly_maintenance(){$audit=$this->run_integrity_audit();$s=$this->automation_settings();$messages=array(sprintf('%d aktive/gespeicherte Profile, Qualitätswert %d/100.', $audit['profiles'],$audit['score']));if(!empty($s['missing_photo'])){$missing=0;foreach($audit['rows'] as $row)if(in_array('Foto fehlt',$row['issues'],true))$missing++;$messages[]=sprintf('%d Profile ohne Foto.',$missing);}if(!empty($s['anniversaries'])){$year=(int)wp_date('Y');$hits=array();foreach($this->members() as $member){$entry=absint(get_post_meta($member->ID,Feuer_Mannschaft_Plugin::META_PREFIX.'entry_date',true));$years=$entry?$year-$entry:0;if($years>=5&&0===$years%5)$hits[]=$member->post_title.' ('.$years.')';}if($hits)$messages[]='Dienstjubiläen: '.implode(', ',$hits).'.';}if(!empty($s['weekly_backup'])){try{$manager=new Feuer_Mannschaft_Backup_Manager();$manager->ensure_storage(true);$manager->create_archive();$messages[]='Backup erstellt.';}catch(Exception $e){$messages[]='Backup fehlgeschlagen: '.$e->getMessage();}}if(!empty($s['weekly_email'])||!empty($s['missing_photo'])||!empty($s['anniversaries']))$this->send_complete_mail('Wochenbericht',implode("\n",$messages),$s);}

    public function run_profile_lifecycle_automation(){
        $settings=$this->automation_settings();$messages=array();$reminders=array();
        if(!empty($settings['annual_review'])){$due=$this->get_due_member_reviews();if($due){$messages[]="Jährliche Profilprüfung fällig:\n- ".implode("\n- ",array_column($due,'label'));foreach($due as $item)$reminders[]=array('id'=>$item['id'],'key'=>'review_reminded_on');}}
        if(!empty($settings['qualification_expiries'])){$due=$this->get_expiring_qualifications();if($due){$messages[]="Qualifikationen laufen innerhalb von 30 Tagen ab oder sind kürzlich abgelaufen:\n- ".implode("\n- ",array_column($due,'label'));foreach($due as $item)$reminders[]=array('id'=>$item['id'],'key'=>'qualification_expiry_reminders','token'=>$item['token']);}}
        if(!empty($settings['service_end_reminders'])){$due=$this->get_service_end_reminders();if($due){$messages[]="Dienstende prüfen; Archivierung muss anschließend manuell bestätigt werden:\n- ".implode("\n- ",array_column($due,'label'));foreach($due as $item)$reminders[]=array('id'=>$item['id'],'key'=>'service_end_reminded_on');}}
        if(!empty($settings['change_report']))$messages[]=$this->get_weekly_change_report();
        if(!$messages||!$this->send_complete_mail('Profilpflege und Änderungen',implode("\n\n",$messages),$settings))return;
        $today=current_time('Y-m-d');
        foreach($reminders as $reminder){$id=absint($reminder['id']);$key=sanitize_key($reminder['key']);if('qualification_expiry_reminders'===$key){$sent=(array)get_post_meta($id,Feuer_Mannschaft_Plugin::META_PREFIX.$key,true);$sent[sanitize_key($reminder['token'])]=$today;update_post_meta($id,Feuer_Mannschaft_Plugin::META_PREFIX.$key,$sent);}else{update_post_meta($id,Feuer_Mannschaft_Plugin::META_PREFIX.$key,$today);}}
    }

    private function get_due_member_reviews(){
        $now=current_time('timestamp');$due=array();
        foreach($this->members() as $member){$reviewed=(string)get_post_meta($member->ID,Feuer_Mannschaft_Plugin::META_PREFIX.'reviewed_on',true);$baseline=$reviewed?strtotime($reviewed):get_post_time('U',true,$member);if(!$baseline||$baseline>$now-DAY_IN_SECONDS*365)continue;$reminded=(string)get_post_meta($member->ID,Feuer_Mannschaft_Plugin::META_PREFIX.'review_reminded_on',true);if($reminded&&strtotime($reminded)>$now-DAY_IN_SECONDS*30)continue;$due[]=array('id'=>$member->ID,'label'=>$member->post_title.' — '.$this->member_edit_url($member->ID));}
        return $due;
    }

    private function get_expiring_qualifications(){
        $today=current_time('Y-m-d');$lower=wp_date('Y-m-d',current_time('timestamp')-DAY_IN_SECONDS*30);$upper=wp_date('Y-m-d',current_time('timestamp')+DAY_IN_SECONDS*30);$due=array();
        foreach($this->members() as $member){$items=(array)get_post_meta($member->ID,Feuer_Mannschaft_Plugin::META_PREFIX.'qualification_expiries',true);$sent=(array)get_post_meta($member->ID,Feuer_Mannschaft_Plugin::META_PREFIX.'qualification_expiry_reminders',true);foreach($items as $item){$date=(string)($item['expires_on']??'');$title=sanitize_text_field($item['title']??'');if(!$title||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)||$date<$lower||$date>$upper)continue;$token=md5($title.'|'.$date);if(!empty($sent[$token])&&strtotime($sent[$token])>current_time('timestamp')-DAY_IN_SECONDS*30)continue;$state=$date<$today?'abgelaufen':'läuft ab';$due[]=array('id'=>$member->ID,'token'=>$token,'label'=>$member->post_title.' — '.$title.' ('.$state.' am '.$date.') — '.$this->member_edit_url($member->ID));}}
        return $due;
    }

    private function get_service_end_reminders(){
        $today=current_time('Y-m-d');$upper=wp_date('Y-m-d',current_time('timestamp')+DAY_IN_SECONDS*30);$due=array();
        foreach($this->members() as $member){$date=(string)get_post_meta($member->ID,Feuer_Mannschaft_Plugin::SERVICE_END_DATE_META,true);if(!$date||$date>$upper)continue;$reminded=(string)get_post_meta($member->ID,Feuer_Mannschaft_Plugin::META_PREFIX.'service_end_reminded_on',true);if($reminded&&strtotime($reminded)>current_time('timestamp')-DAY_IN_SECONDS*30)continue;$state=$date<$today?'vorbei':'am '.$date;$due[]=array('id'=>$member->ID,'label'=>$member->post_title.' — Dienstende '.$state.'; Archivierung prüfen: '.$this->member_edit_url($member->ID));}
        return $due;
    }

    private function get_weekly_change_report(){
        $logger=new Feuer_Mannschaft_Logger();$logs=$logger->get_logs(array('date_from'=>gmdate('Y-m-d',time()-DAY_IN_SECONDS*7),'limit'=>500));$lines=array();
        $labels=array('member_created'=>'Profil angelegt','member_updated'=>'Profil geändert','member_deleted'=>'Profil gelöscht');
        foreach($logs as $log){if('member'!==$log->entity_type||!isset($labels[$log->action]))continue;$user=get_userdata(absint($log->user_id));$lines[]=sprintf('%s — %s — %s — %s',wp_date('d.m.Y H:i',strtotime($log->created_at.' UTC')),$log->entity_label,$labels[$log->action],$user?$user->display_name:'System');if(count($lines)>=100)break;}
        return "Profiländerungen der letzten 7 Tage:\n- ".($lines?implode("\n- ",$lines):'Keine Änderungen protokolliert.');
    }

    private function member_edit_url($post_id){return admin_url('post.php?post='.absint($post_id).'&action=edit');}

    private function missing_publication_fields($post_id, $raw = array(), $category = 0, $check_uploads = false) {
        $post_id = absint($post_id);
        $raw = is_array($raw) ? $raw : array();
        $missing = array();
        $first = sanitize_text_field($raw['first_name'] ?? get_post_meta($post_id, Feuer_Mannschaft_Plugin::META_PREFIX . 'first_name', true));
        $last = sanitize_text_field($raw['last_name'] ?? get_post_meta($post_id, Feuer_Mannschaft_Plugin::META_PREFIX . 'last_name', true));

        if (! $first || ! $last) { $missing[] = 'Vor- und Nachname'; }

        $rank = $raw['rank'] ?? get_post_meta($post_id, Feuer_Mannschaft_Plugin::META_PREFIX . 'rank', true);
        if (! $this->text($rank)) { $missing[] = 'Dienstgrad'; }

        $category = absint($category);
        if (! $category && $post_id) {
            $categories = wp_get_post_categories($post_id);
            $category = $categories ? absint($categories[0]) : 0;
        }
        $settings = (array) get_option(Feuer_Mannschaft_Plugin::OPTION_KEY, array());
        $allowed_categories = array_values(array_filter(array_map('absint', (array) ($settings['allowed_categories'] ?? array()))));
        if (! $category || ! term_exists($category, 'category') || ($allowed_categories && ! in_array($category, $allowed_categories, true))) { $missing[] = 'Kategorie'; }

        $photo_ids = $post_id
            ? array_merge(array(get_post_thumbnail_id($post_id)), (array) get_post_meta($post_id, Feuer_Mannschaft_Plugin::GALLERY_META, true))
            : array();
        if ($post_id && class_exists('Feuer_Mannschaft_Complete')) {
            $media = (array) get_post_meta($post_id, Feuer_Mannschaft_Complete::MEDIA_META, true);
            $photo_ids = array_merge($photo_ids, array(absint($media['profile_image'] ?? 0), absint($media['card_image'] ?? 0)));
        }
        $has_photo = false;
        foreach ($photo_ids as $photo_id) {
            if ($photo_id && wp_attachment_is_image(absint($photo_id))) { $has_photo = true; break; }
        }
        $default_image = absint($settings['default_profile_image_id'] ?? 0);
        $has_photo = $has_photo || ($default_image && wp_attachment_is_image($default_image));

        if ($check_uploads) {
            $file_data = $_FILES['feuer_mannschaft_gallery'] ?? array();
            $file_names = $file_data['name'] ?? array();
            $file_errors = $file_data['error'] ?? array();
            if (is_array($file_names)) {
                foreach ($file_names as $index => $file_name) {
                    if ($file_name && isset($file_errors[$index]) && UPLOAD_ERR_OK === (int) $file_errors[$index]) { $has_photo = true; break; }
                }
            }
        }
        if (! $has_photo) { $missing[] = 'Profilfoto'; }

        return array_values(array_unique($missing));
    }

    public function prevent_incomplete_profile_publication($data,$postarr,$unsanitized_postarr,$update){
        unset($unsanitized_postarr,$update);
        if(!is_admin()||Feuer_Mannschaft_Plugin::POST_TYPE!==($data['post_type']??'')||'publish'!==($data['post_status']??'')||!current_user_can('publish_feuer_members'))return $data;
        $post_id=absint($postarr['ID']??0);if($post_id&&'publish'===get_post_status($post_id))return $data;
        $raw=isset($_POST['feuer_mannschaft_meta'])?(array)wp_unslash($_POST['feuer_mannschaft_meta']):array();
        $category=isset($_POST['feuer_mannschaft_profile_category'])?absint(wp_unslash($_POST['feuer_mannschaft_profile_category'])):0;
        $missing=$this->missing_publication_fields($post_id,$raw,$category,true);
        if($missing){$data['post_status']='draft';set_transient('feuer_mannschaft_publish_validation_'.get_current_user_id(),$missing,5*MINUTE_IN_SECONDS);}
        return $data;
    }

    public function publish_validation_notice(){
        $key='feuer_mannschaft_publish_validation_'.get_current_user_id();$missing=get_transient($key);if(!is_array($missing)||!$missing)return;delete_transient($key);
        echo '<div class="notice notice-error is-dismissible"><p><strong>Profil bleibt Entwurf.</strong> Bitte ergänzen Sie vor der Veröffentlichung: '.esc_html(implode(', ',$missing)).'.</p></div>';
    }

    public function notify_new_member_for_review_status($new,$old,$post){
        if(!$post instanceof WP_Post||Feuer_Mannschaft_Plugin::POST_TYPE!==$post->post_type||'draft'!==$new||!in_array($old,array('auto-draft','new'),true))return;
        $settings=$this->automation_settings();if(empty($settings['onboarding_notifications'])||get_post_meta($post->ID,Feuer_Mannschaft_Plugin::META_PREFIX.'onboarding_notified',true))return;
        if($this->send_complete_mail('Neues Profil zur Prüfung',$post->post_title."\nBitte Profil vervollständigen und prüfen: ".$this->member_edit_url($post->ID),$settings))update_post_meta($post->ID,Feuer_Mannschaft_Plugin::META_PREFIX.'onboarding_notified',current_time('mysql'));
    }

    public function notify_new_member_for_review($post_id,$post,$update){
        unset($update);
        if(!$post instanceof WP_Post||Feuer_Mannschaft_Plugin::POST_TYPE!==$post->post_type||'draft'!==$post->post_status||wp_is_post_revision($post_id)||defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)return;
        $settings=$this->automation_settings();if(empty($settings['onboarding_notifications'])||get_post_meta($post_id,Feuer_Mannschaft_Plugin::META_PREFIX.'onboarding_notified',true))return;
        if($this->send_complete_mail('Neues Profil zur Prüfung',$post->post_title."\nBitte Profil vervollständigen und prüfen: ".$this->member_edit_url($post_id),$settings))update_post_meta($post_id,Feuer_Mannschaft_Plugin::META_PREFIX.'onboarding_notified',current_time('mysql'));
    }

    public function confirm_member_review(){
        $post_id=isset($_GET['member_id'])?absint(wp_unslash($_GET['member_id'])):0;check_admin_referer('feuer_complete_confirm_member_review_'.$post_id);
        if(!$post_id||Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type($post_id)||!current_user_can('edit_post',$post_id))wp_die('Nicht erlaubt.',403);
        update_post_meta($post_id,Feuer_Mannschaft_Plugin::META_PREFIX.'reviewed_on',current_time('Y-m-d'));delete_post_meta($post_id,Feuer_Mannschaft_Plugin::META_PREFIX.'review_reminded_on');
        $logger=new Feuer_Mannschaft_Logger();$logger->log('member_updated','member',$post_id,get_the_title($post_id),array(array('message'=>'Jährliche Profildatenprüfung bestätigt.')));
        wp_safe_redirect(add_query_arg('feuer_review_confirmed','1',get_edit_post_link($post_id,'url')));exit;
    }

    public function member_review_admin_notice(){if(empty($_GET['feuer_review_confirmed'])||'1'!==sanitize_text_field(wp_unslash($_GET['feuer_review_confirmed'])))return;echo '<div class="notice notice-success is-dismissible"><p>Die Profildaten wurden als aktuell bestätigt.</p></div>';}
    public function notify_bulk_or_restore($new,$old,$post){if(!$post instanceof WP_Post||Feuer_Mannschaft_Plugin::POST_TYPE!==$post->post_type||$new===$old)return;$s=$this->automation_settings();if(empty($s['bulk_notifications']))return;if(in_array($new,array('publish','feuer_archived','trash'),true))$this->send_complete_mail('Profilstatus geändert',sprintf('%s: %s → %s',$post->post_title,$old,$new),$s);}
    private function send_complete_mail($subject,$message,$settings){$email=sanitize_email($settings['email']??'');return is_email($email)?(bool)wp_mail($email,'Mannschaft – '.$subject,$message):false;}
}

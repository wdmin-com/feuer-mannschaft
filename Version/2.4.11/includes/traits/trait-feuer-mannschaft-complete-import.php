<?php
/** Validated CSV import, mapping, dry run, rollback and portable ZIP export. */
if (! defined('ABSPATH')) { exit; }

trait Feuer_Mannschaft_Complete_Import {
    private function import_targets() {
        return array('id'=>'Bestehende Profil-ID','slug'=>'URL-Kürzel','name'=>'Anzeigename *','first_name'=>'Vorname','last_name'=>'Nachname','rank'=>'Dienstgrad','function'=>'Funktion','entry_date'=>'Eintrittsjahr','department'=>'Abteilung','group'=>'Gruppe','specialty'=>'Fachgebiet','status'=>'Status','image_url'=>'Bild-URL','mentions'=>'Publikations-IDs');
    }

    private function auto_map_column($target,$header) {
        $aliases = array('name'=>array('name','title','fullname','vollständiger_name'),'function'=>array('function','position','funktion'),'rank'=>array('rank','dienstgrad'),'department'=>array('department','abteilung'),'group'=>array('group','gruppe'),'specialty'=>array('specialty','fachgebiet'),'entry_date'=>array('entry_date','eintritt','eintrittsjahr'),'image_url'=>array('image_url','photo','foto'));
        foreach($header as $column) if(in_array(sanitize_key($column),$aliases[$target]??array($target),true)) return $column;
        return '';
    }

    public function import_preview() {
        $this->verify_complete_request('feuer_complete_import_preview');
        $file = $_FILES['csv']??array();
        if(empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || !empty($file['error']) || absint($file['size']??0)>5*MB_IN_BYTES) wp_die('Ungültige CSV-Datei oder Dateigröße über 5 MB.',400);
        $mime = wp_check_filetype_and_ext($file['tmp_name'],sanitize_file_name($file['name']??'import.csv'));
        if(!in_array(strtolower(pathinfo($file['name']??'',PATHINFO_EXTENSION)),array('csv','txt'),true)) wp_die('Nur CSV-Dateien sind erlaubt.',400);
        $sample=(string)file_get_contents($file['tmp_name'],false,null,0,8192); $delimiter=$this->detect_delimiter($sample);
        $handle=fopen($file['tmp_name'],'rb'); if(!$handle) wp_die('CSV konnte nicht geöffnet werden.',400);
        $header=fgetcsv($handle,0,$delimiter); if(!$header){fclose($handle);wp_die('CSV ist leer.',400);} $header=array_map(function($v){return trim(sanitize_text_field(ltrim((string)$v,"\xEF\xBB\xBF")));},$header);
        if(count(array_unique($header))!==count($header)||in_array('',$header,true)){fclose($handle);wp_die('CSV enthält leere oder doppelte Spaltennamen.',400);}
        $rows=array();$line=1;$errors=array(); while(($values=fgetcsv($handle,0,$delimiter))!==false){$line++;if(count($rows)>=2000){$errors[]='Mehr als 2.000 Datenzeilen.';break;}if(count($values)!==count($header)){$errors[]='Zeile '.$line.': Spaltenzahl stimmt nicht.';continue;}$rows[]=array_combine($header,array_map('sanitize_text_field',$values));} fclose($handle);
        if(!$rows) wp_die('Keine gültigen Datenzeilen gefunden.',400);
        set_transient(self::IMPORT_TRANSIENT.get_current_user_id(),array('header'=>$header,'rows'=>$rows,'errors'=>$errors,'created'=>time()),30*MINUTE_IN_SECONDS);
        wp_safe_redirect($this->complete_admin_url('feuer-mannschaft-csv',array('preview'=>1))); exit;
    }

    public function import_execute() {
        $this->verify_complete_request('feuer_complete_import_execute');
        $preview=get_transient(self::IMPORT_TRANSIENT.get_current_user_id()); if(!is_array($preview)||empty($preview['rows'])) wp_die('Die Vorschau ist abgelaufen. Bitte CSV erneut hochladen.',400);
        $mapping=array(); foreach($this->import_targets() as $target=>$label){$column=sanitize_text_field(wp_unslash($_POST['mapping'][$target]??''));if($column && in_array($column,$preview['header'],true))$mapping[$target]=$column;}
        if(empty($mapping['name'])) wp_die('Das Zielfeld Anzeigename muss zugeordnet sein.',400);
        $dry=!empty($_POST['dry_run']);$errors=(array)($preview['errors']??array());$valid=array();$names=array();
        foreach($preview['rows'] as $index=>$row){$record=array();foreach($mapping as $target=>$column)$record[$target]=trim((string)($row[$column]??''));if(isset($record['status']))$record['status']=$this->normalize_import_status($record['status']);$line=$index+2;if(''===$record['name']){$errors[]='Zeile '.$line.': Name fehlt.';continue;}$identity=sanitize_title($record['slug']??$record['name']);if(isset($names[$identity])){$errors[]='Zeile '.$line.': doppelter Name/Slug im Import.';continue;}$record_errors=array();$resolution_error='';$this->resolve_import_member_id($record,$resolution_error);if($resolution_error)$record_errors[]=$resolution_error;if(!empty($record['image_url'])&&!wp_http_validate_url($record['image_url']))$record_errors[]='Bild-URL ist ungültig oder nicht sicher.';if(!empty($record['status'])&&!in_array($record['status'],array('publish','draft','private','feuer_archived'),true))$record_errors[]='Status ist ungültig.';if(!empty($record['entry_date'])&&(!ctype_digit((string)$record['entry_date'])||absint($record['entry_date'])<1800||absint($record['entry_date'])>(int)wp_date('Y')+1))$record_errors[]='Eintrittsjahr ist ungültig.';foreach(array_filter(array_map('absint',preg_split('/[|,]/',$record['mentions']??''))) as $content_id)if(!get_post($content_id)||Feuer_Mannschaft_Plugin::POST_TYPE===get_post_type($content_id))$record_errors[]='Publikations-ID '.$content_id.' ist ungültig.';if($record_errors){foreach($record_errors as $record_error)$errors[]='Zeile '.$line.': '.$record_error;continue;}$names[$identity]=true;$valid[]=$record;}
        if($dry){$this->set_import_result(sprintf('%d gültig, %d Fehler. Keine Daten geändert.',count($valid),count($errors)),!empty($errors),$errors);wp_safe_redirect($this->complete_admin_url('feuer-mannschaft-csv'));exit;}
        if($errors){$this->set_import_result('Import abgebrochen: Fehler müssen zuerst behoben werden.',true,$errors);wp_safe_redirect($this->complete_admin_url('feuer-mannschaft-csv'));exit;}
        $rollback=array();$imported=0;
        foreach($valid as $record){$resolution_error='';$id=$this->resolve_import_member_id($record,$resolution_error);if($resolution_error){$errors[]=$resolution_error;continue;}
            $rollback_index=count($rollback);$rollback[]=$this->snapshot_member($id);$rollback[$rollback_index]['mention_meta']=array();$rollback[$rollback_index]['created_attachments']=array();foreach(array_filter(array_map('absint',preg_split('/[|,]/',$record['mentions']??''))) as $content_id)$rollback[$rollback_index]['mention_meta'][$content_id]=get_post_meta($content_id,Feuer_Mannschaft_Mentions::META_KEY,true);$status=in_array($record['status']??'',array('draft','private','feuer_archived'),true)?$record['status']:'publish';$result=wp_insert_post(array('ID'=>$id,'post_type'=>Feuer_Mannschaft_Plugin::POST_TYPE,'post_status'=>$status,'post_title'=>sanitize_text_field($record['name']),'post_name'=>sanitize_title($record['slug']??$record['name'])),true);if(is_wp_error($result)){$errors[]=$result->get_error_message();array_pop($rollback);continue;}$id=absint($result);if(empty($rollback[$rollback_index]['exists']))$rollback[$rollback_index]['created_id']=$id;update_post_meta($id,Feuer_Mannschaft_Plugin::PROFILE_META,1);
            foreach(array('first_name','last_name','rank','function','entry_date') as $field)if(array_key_exists($field,$record))update_post_meta($id,Feuer_Mannschaft_Plugin::META_PREFIX.$field,sanitize_text_field($record[$field]));
            foreach(array('department'=>'feuer_department','group'=>'feuer_group','specialty'=>'feuer_specialty') as $column=>$taxonomy)if(isset($record[$column]))wp_set_object_terms($id,array_values(array_filter(array_map('sanitize_text_field',preg_split('/[|,]/',$record[$column])))),$taxonomy,false);
            if(!empty($record['mentions']))foreach(array_filter(array_map('absint',preg_split('/[|,]/',$record['mentions']))) as $content_id){$ids=array_map('absint',(array)get_post_meta($content_id,Feuer_Mannschaft_Mentions::META_KEY,true));if(get_post($content_id)&&!in_array($id,$ids,true)){ $ids[]=$id;update_post_meta($content_id,Feuer_Mannschaft_Mentions::META_KEY,array_values($ids));}}
            if(!empty($record['image_url'])&&wp_http_validate_url($record['image_url'])){$image_id=$this->sideload_member_image($record['image_url'],$id);if(!is_wp_error($image_id)){$rollback[$rollback_index]['created_attachments'][]=absint($image_id);set_post_thumbnail($id,$image_id);$media=$this->media_data($id);$media['card_image']=$image_id;$media['profile_image']=$image_id;update_post_meta($id,self::MEDIA_META,$media);}else{$errors[]='Bild für '.$record['name'].': '.$image_id->get_error_message();}}
            $imported++;
        }
        set_transient(self::IMPORT_ROLLBACK_TRANSIENT.get_current_user_id(),$rollback,DAY_IN_SECONDS);delete_transient(self::IMPORT_TRANSIENT.get_current_user_id());$this->set_import_result(sprintf('%d Profile importiert/aktualisiert.', $imported),!empty($errors),$errors);wp_safe_redirect($this->complete_admin_url('feuer-mannschaft-csv'));exit;
    }

    public function import_rollback() {
        $this->verify_complete_request('feuer_complete_import_rollback');$snapshots=get_transient(self::IMPORT_ROLLBACK_TRANSIENT.get_current_user_id());if(!is_array($snapshots))wp_die('Kein zurückrollbarer Import vorhanden.',404);
        foreach(array_reverse($snapshots) as $snapshot){foreach((array)($snapshot['mention_meta']??array()) as $content_id=>$value){if(''===$value||array()===$value)delete_post_meta(absint($content_id),Feuer_Mannschaft_Mentions::META_KEY);else update_post_meta(absint($content_id),Feuer_Mannschaft_Mentions::META_KEY,$value);}foreach((array)($snapshot['created_attachments']??array()) as $attachment_id)if(get_post(absint($attachment_id)))wp_delete_attachment(absint($attachment_id),true);if(empty($snapshot['exists'])){if(!empty($snapshot['created_id']))wp_delete_post(absint($snapshot['created_id']),true);continue;}$id=absint($snapshot['post']['ID']);wp_update_post($snapshot['post']);foreach(array_keys(get_post_meta($id)) as $key)delete_post_meta($id,$key);foreach($snapshot['meta'] as $key=>$values)foreach($values as $value)add_post_meta($id,$key,maybe_unserialize($value));foreach($snapshot['terms'] as $taxonomy=>$term_ids)wp_set_object_terms($id,array_map('absint',$term_ids),$taxonomy,false);}
        delete_transient(self::IMPORT_ROLLBACK_TRANSIENT.get_current_user_id());$this->set_import_result('Der letzte Import wurde zurückgerollt.',false,array());wp_safe_redirect($this->complete_admin_url('feuer-mannschaft-csv'));exit;
    }

    private function snapshot_member($id) {
        if(!$id)return array('exists'=>false,'created_id'=>0);
        $terms=array();foreach(array('feuer_department','feuer_group','feuer_specialty') as $taxonomy){$ids=wp_get_post_terms($id,$taxonomy,array('fields'=>'ids'));$terms[$taxonomy]=is_wp_error($ids)?array():$ids;}
        return array('exists'=>true,'post'=>get_post($id,ARRAY_A),'meta'=>get_post_meta($id),'terms'=>$terms);
    }

    private function sideload_member_image($url,$post_id){require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';return media_sideload_image(esc_url_raw($url),$post_id,null,'id');}

    private function resolve_import_member_id($record,&$error){$error='';$id=absint($record['id']??0);if($id&&Feuer_Mannschaft_Plugin::POST_TYPE!==get_post_type($id)){$error='Profil-ID '.$id.' gehört zu einem anderen Inhaltstyp.';return 0;}if($id)return $id;if(!empty($record['slug'])){$existing=get_page_by_path(sanitize_title($record['slug']),OBJECT,Feuer_Mannschaft_Plugin::POST_TYPE);if($existing)return absint($existing->ID);}$matches=get_posts(array('post_type'=>Feuer_Mannschaft_Plugin::POST_TYPE,'post_status'=>array('publish','draft','private','feuer_archived'),'title'=>sanitize_text_field($record['name']??''),'fields'=>'ids','posts_per_page'=>2,'no_found_rows'=>true));if(1===count($matches))return absint($matches[0]);if(count($matches)>1)$error='Name „'.sanitize_text_field($record['name']??'').'“ ist nicht eindeutig. Bitte Profil-ID oder URL-Kürzel angeben.';return 0;}

    public function export_archive() {
        $this->verify_complete_request('feuer_complete_export');if(!class_exists('ZipArchive'))wp_die('Die PHP-Erweiterung ZipArchive wird für den vollständigen Export benötigt.',500);
        $uploads=wp_upload_dir();$base=trailingslashit($uploads['basedir']).'feuer-mannschaft-exports';wp_mkdir_p($base);$token=wp_generate_password(12,false,false);$path=trailingslashit($base).'mannschaft-'.gmdate('Ymd-His').'-'.$token.'.zip';$zip=new ZipArchive();if(true!==$zip->open($path,ZipArchive::CREATE|ZipArchive::OVERWRITE))wp_die('Exportarchiv konnte nicht erstellt werden.',500);
        $csv=fopen('php://temp','w+');fwrite($csv,"\xEF\xBB\xBF");fputcsv($csv,array_keys($this->import_targets()),';');$manifest=array('version'=>FEUER_MANNSCHAFT_VERSION,'created'=>gmdate('c'),'site'=>home_url('/'),'profiles'=>0);$relations=array();
        foreach($this->members(array('post_status'=>array('publish','draft','private','feuer_archived'))) as $post){$profile=$this->profile($post->ID);$terms=array();foreach(array('department'=>'feuer_department','group'=>'feuer_group','specialty'=>'feuer_specialty') as $key=>$taxonomy){$names=wp_get_post_terms($post->ID,$taxonomy,array('fields'=>'names'));$terms[$key]=is_wp_error($names)?'':implode('|',$names);}$image_id=absint($this->media_data($post->ID)['profile_image'])?:get_post_thumbnail_id($post->ID);$image_url=$image_id?(string)wp_get_attachment_url($image_id):'';$mentioned=$this->content_ids_mentioning_member($post->ID);$relations[$post->ID]=$mentioned;fputcsv($csv,array($post->ID,$post->post_name,$post->post_title,$profile['first_name']??'',$profile['last_name']??'',$this->text($profile['rank']??''),$this->text($profile['function']??''),$profile['entry_date']??'',$terms['department'],$terms['group'],$terms['specialty'],$post->post_status,$image_url,implode('|',$mentioned)),';');if($image_id){$file=get_attached_file($image_id);if($file&&is_readable($file))$zip->addFile($file,'images/'.$post->ID.'-'.sanitize_file_name(wp_basename($file)));}$manifest['profiles']++;}
        rewind($csv);$zip->addFromString('members.csv',stream_get_contents($csv));fclose($csv);$zip->addFromString('relations.json',wp_json_encode($relations,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));$zip->addFromString('manifest.json',wp_json_encode($manifest,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));$zip->close();nocache_headers();header('Content-Type: application/zip');header('Content-Disposition: attachment; filename="feuer-mannschaft-export-'.gmdate('Y-m-d').'.zip"');header('Content-Length: '.filesize($path));readfile($path);unlink($path);exit;
    }

    private function detect_delimiter($sample){$counts=array(';'=>substr_count($sample,';'),','=>substr_count($sample,','),"\t"=>substr_count($sample,"\t"));arsort($counts);return (string)key($counts);}
    private function normalize_import_status($status){$status=sanitize_key((string)$status);return in_array($status,array('feuer_mannschaft_arc','feuer_mannschaft_archived'),true)?Feuer_Mannschaft_Plugin::ARCHIVED_STATUS:$status;}
    private function content_ids_mentioning_member($member_id){$member_id=absint($member_id);return get_posts(array('post_type'=>'any','post_status'=>'any','fields'=>'ids','posts_per_page'=>-1,'no_found_rows'=>true,'meta_query'=>array('relation'=>'OR',array('key'=>Feuer_Mannschaft_Mentions::META_KEY,'value'=>'"'.$member_id.'"','compare'=>'LIKE'),array('key'=>Feuer_Mannschaft_Mentions::META_KEY,'value'=>'i:'.$member_id.';','compare'=>'LIKE'),array('key'=>Feuer_Mannschaft_Mentions::META_KEY,'value'=>(string)$member_id,'compare'=>'='))));}
    private function verify_complete_request($nonce){if(!current_user_can('manage_feuer_members')||!check_admin_referer($nonce))wp_die('Nicht erlaubt.',403);}
    private function complete_admin_url($page,$args=array()){return add_query_arg(array_merge(array('post_type'=>Feuer_Mannschaft_Plugin::POST_TYPE,'page'=>$page),$args),admin_url('edit.php'));}
    private function set_import_result($message,$error,$details){set_transient(self::IMPORT_TRANSIENT.get_current_user_id().'_result',array('message'=>$message,'error'=>$error,'details'=>$details),5*MINUTE_IN_SECONDS);}
}

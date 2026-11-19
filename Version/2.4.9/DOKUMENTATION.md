# Feuer-Mannschaft Dokumentation

Automatisch erzeugt am 2026-11-02 00:50:46.

## Zweck

Diese Datei dokumentiert die aktuelle Projektstruktur, beschreibt die wichtigsten Dateien und listet alle im Quellcode gefundenen PHP-Funktionen mit Dateipfad, Zeile und Kurzbeschreibung auf.

## Dateikarte

| Pfad | Beschreibung |
| --- | --- |
| `.gitattributes` | Git-Attribute fuer Zeilenenden und Export-Verhalten bei GitHub-Archiven. |
| `.gitignore` | Git-Regeln fuer lokale und generierte Dateien, die nicht versioniert werden sollen. |
| `ANLEITUNG.md` | Anwenderdokumentation fuer Installation, Bedienung und Einsatz im WordPress-Backend. |
| `assets/css.zip` | Projektdatei des Plugins. |
| `assets/css/admin.css` | Styles fuer die Plugin-Administration, Einstellungen und Metaboxen. |
| `assets/css/complete.css` | Projektdatei des Plugins. |
| `assets/css/complete-admin.css` | Projektdatei des Plugins. |
| `assets/css/feuer-mannschaft.css` | Styles fuer Archiv-, Karten- und Profilansichten im Frontend. |
| `assets/css/member-directory.css` | Projektdatei des Plugins. |
| `assets/js/admin.js` | Admin-JavaScript fuer wiederholbare Felder, Uploads und Einstellungsoberflaechen. |
| `assets/js/blocks.js` | Projektdatei des Plugins. |
| `assets/js/complete.js` | Projektdatei des Plugins. |
| `assets/js/complete-admin.js` | Projektdatei des Plugins. |
| `assets/js/feuer-mannschaft.js` | Frontend-JavaScript fuer Slider, Modalansichten und Interaktionen. |
| `assets/js/member-directory.js` | Projektdatei des Plugins. |
| `assets/vendor/qrcode-generator/LICENSE` | Projektdatei des Plugins. |
| `assets/vendor/qrcode-generator/qrcode.js` | Projektdatei des Plugins. |
| `demo/feuer-mannschaft-demo-backup.zip` | Projektdatei des Plugins. |
| `demo/images/anna-beispiel.png` | Projektdatei des Plugins. |
| `demo/images/erik-platzhalter.png` | Projektdatei des Plugins. |
| `demo/images/leon-muster.png` | Projektdatei des Plugins. |
| `demo/images/sofia-demo.png` | Projektdatei des Plugins. |
| `demo/images/team-member-05.png` | Projektdatei des Plugins. |
| `demo/images/team-member-06.png` | Projektdatei des Plugins. |
| `demo/images/team-member-07.png` | Projektdatei des Plugins. |
| `demo/images/team-member-08.png` | Projektdatei des Plugins. |
| `demo/images/team-member-09.png` | Projektdatei des Plugins. |
| `demo/images/team-member-10.png` | Projektdatei des Plugins. |
| `demo/images/team-member-11.png` | Projektdatei des Plugins. |
| `demo/images/team-member-12.png` | Projektdatei des Plugins. |
| `demo/images/team-member-13.png` | Projektdatei des Plugins. |
| `demo/images/team-member-14.png` | Projektdatei des Plugins. |
| `demo/images/team-member-15.png` | Projektdatei des Plugins. |
| `demo/images/team-member-16.png` | Projektdatei des Plugins. |
| `demo/images/team-member-17.png` | Projektdatei des Plugins. |
| `demo/images/team-member-18.png` | Projektdatei des Plugins. |
| `demo/images/team-member-19.png` | Projektdatei des Plugins. |
| `demo/images/team-member-20.png` | Projektdatei des Plugins. |
| `demo/images/team-member-21.png` | Projektdatei des Plugins. |
| `demo/images/team-member-22.png` | Projektdatei des Plugins. |
| `demo/images/team-member-23.png` | Projektdatei des Plugins. |
| `demo/images/team-member-24.png` | Projektdatei des Plugins. |
| `demo/images/team-member-25.png` | Projektdatei des Plugins. |
| `demo/README.md` | Projektdatei des Plugins. |
| `DEPLOYMENT.md` | Anleitung fuer Release-Builds, Update-Bereitstellung und Serverablage. |
| `DOKUMENTATION.md` | Automatisch erzeugte Dokumentation mit Dateikarte und Funktionsindex. |
| `feuer-mannschaft.php` | Plugin-Bootstrap mit Konstanten, Aktivierungs-/Deaktivierungslogik und Laden der Kernklassen. |
| `HISTORIE.md` | Chronologische Aenderungshistorie aller Plugin-Versionen. |
| `includes/class-feuer-mannschaft-backup-manager.php` | Sichert und stellt Plugin-Daten, Medien und Einstellungen aus Archiven wieder her. |
| `includes/class-feuer-mannschaft-complete.php` | Projektdatei des Plugins. |
| `includes/class-feuer-mannschaft-logger.php` | Kapselt Log-Tabelle, Schema-Updates und Protokollschreibvorgaenge. |
| `includes/class-feuer-mannschaft-mentions.php` | Verwaltet Erwahnungen, Verknuepfungen und Frontend-Ausgabe dazu. |
| `includes/class-feuer-mannschaft-plugin.php` | Zentrale Plugin-Klasse fuer Backend, Frontend, Settings, Templates und Mitgliederdaten. |
| `includes/class-feuer-mannschaft-toolkit.php` | Projektdatei des Plugins. |
| `includes/class-feuer-mannschaft-updater.php` | Prueft remote auf neue Versionen und bindet Custom-Updates in WordPress ein. |
| `includes/traits/trait-feuer-mannschaft-complete-admin.php` | Projektdatei des Plugins. |
| `includes/traits/trait-feuer-mannschaft-complete-import.php` | Projektdatei des Plugins. |
| `includes/traits/trait-feuer-mannschaft-complete-operations.php` | Projektdatei des Plugins. |
| `index.php` | Projektdatei des Plugins. |
| `README.md` | GitHub-Startdokument mit Projektueberblick, Entwicklungshinweisen und Release-Ablauf. |
| `ROADMAP.md` | Projektdatei des Plugins. |
| `snippets/member-cards-bootstrap.html` | Projektdatei des Plugins. |
| `snippets/README.md` | Projektdatei des Plugins. |
| `templates/admin/action-link.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/dashboard-summary.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/member-categories.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/member-details.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/member-gallery.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/member-preview.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/member-submitbox.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/mention-fallback.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/mention-shortcodes.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/message.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/no-photo.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/notice.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/service-history.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/settings/archives.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/fields.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/general.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/health.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/logs.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/media.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/mentions.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/updates.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings-page.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/archive-feuer-mannschaft-member.php` | WordPress-Archiv-Wrapper fuer Mannschaftslisten. |
| `templates/frontend/admin-note.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/archived-members.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/contact-buttons.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/detail-tags.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/detail-value.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/member-link.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/member-placeholder.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/member-posts.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/member-standard-card.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/member-team-card.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/mentions-section.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/org-chart.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/service-timeline.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/page-feuer-mannschaft-profile.php` | Spezielle Seitenvorlage fuer Profilseiten oder Shortcode-Ausgaben. |
| `templates/profil/compact.php` | Frontend-Template fuer die Einzelansicht eines Mitglieds. |
| `templates/profil/dossier.php` | Frontend-Template fuer die Einzelansicht eines Mitglieds. |
| `templates/profil/magazin.php` | Frontend-Template fuer die Einzelansicht eines Mitglieds. |
| `templates/profil/standard.php` | Frontend-Template fuer die Einzelansicht eines Mitglieds. |
| `templates/single-feuer-mannschaft-member.php` | WordPress-Single-Wrapper fuer Einzelprofile. |
| `templates/uebersicht/cards.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/cards-modal.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/directory.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/grayscale-cards.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/list.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/member-card.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/member-row.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/spotlight.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/standard.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/team-grayscale.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/team-grid.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `tools/build-plugin-docs.ps1` | Erzeugt die Funktions- und Dateidokumentation automatisch aus dem Projektstand. |
| `tools/build-release-package.ps1` | Erstellt ein auslieferbares ZIP-Paket fuer Server oder GitHub-Releases. |
| `tools/create-demo-backup.ps1` | Projektdatei des Plugins. |
| `tools/create-version-snapshot.ps1` | Erzeugt einen Versions-Snapshot unter Version/<version>/ fuer den aktuellen Stand. |
| `uninstall.php` | Entfernt Plugin-Daten und bereinigt Inhalte waehrend der Deinstallation. |
| `update.json` | Remote-Manifest fuer die benutzerdefinierte WordPress-Updatepruefung. |

## PHP-Funktionsindex

Die folgenden Tabellen werden direkt aus den PHP-Dateien erzeugt. Dadurch bleiben Dateipfade und Zeilennummern fuer Wartung, Reviews und GitHub-Navigation nachvollziehbar.

### `feuer-mannschaft.php`

Plugin-Bootstrap mit Konstanten, Aktivierungs-/Deaktivierungslogik und Laden der Kernklassen.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 26 | global | `feuer_mannschaft_is_supported_php` | Interne Funktion fuer mannschaft is supported php. |
| 30 | global | `feuer_mannschaft_render_php_notice` | Interne Funktion fuer mannschaft render php notice. |
| 54 | global | `feuer_mannschaft_plugin` | Interne Funktion fuer mannschaft plugin. |
| 58 | global | `feuer_mannschaft_updater` | Interne Funktion fuer mannschaft updater. |
| 62 | global | `feuer_mannschaft_activate` | Interne Funktion fuer mannschaft activate. |
| 75 | global | `feuer_mannschaft_deactivate` | Interne Funktion fuer mannschaft deactivate. |
| 79 | global | `feuer_mannschaft_cleanup_removed_usage_registry` | Interne Funktion fuer mannschaft cleanup removed usage registry. |

### `includes/class-feuer-mannschaft-backup-manager.php`

Sichert und stellt Plugin-Daten, Medien und Einstellungen aus Archiven wieder her.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 15 | public | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 19 | public | `register_hooks` | Registriert hooks. |
| 28 | public | `ensure_storage` | Stellt sicher, dass storage vorhanden ist. |
| 61 | public | `get_archives` | Liest oder ermittelt archives. |
| 91 | public | `handle_create_request` | Verarbeitet create request. |
| 113 | public | `handle_delete_request` | Verarbeitet delete request. |
| 146 | public | `handle_download_request` | Verarbeitet download request. |
| 180 | public | `handle_upload_request` | Verarbeitet upload request. |
| 257 | public | `handle_restore_request` | Verarbeitet restore request. |
| 327 | public | `create_archive` | Erstellt archive. |
| 377 | public | `preview_archive` | Interne Funktion fuer archive. |
| 421 | public | `restore_archive` | Stellt archive wieder her. |
| 545 | private | `build_manifest` | Erzeugt manifest. |
| 559 | private | `export_members` | Exportiert members. |
| 605 | private | `export_member_categories` | Exportiert member categories. |
| 629 | private | `export_gallery` | Exportiert gallery. |
| 665 | private | `export_mentions` | Exportiert mentions. |
| 710 | private | `export_logs` | Exportiert logs. |
| 733 | private | `get_member_meta_export` | Liest oder ermittelt member meta export. |
| 746 | private | `restore_member_record` | Stellt member record wieder her. |
| 814 | private | `restore_member_gallery` | Stellt member gallery wieder her. |
| 926 | private | `restore_mention_record` | Stellt mention record wieder her. |
| 976 | private | `find_existing_member_id` | Interne Funktion fuer existing member id. |
| 1029 | private | `resolve_member_category_ids` | Loest member category ids auf. |
| 1067 | private | `reset_member_gallery` | Interne Funktion fuer member gallery. |
| 1074 | private | `sync_member_default_thumbnail` | Synchronisiert member default thumbnail. |
| 1086 | private | `verify_manage_options_request` | Interne Funktion fuer manage options request. |
| 1094 | private | `redirect_with_notice` | Interne Funktion fuer with notice. |
| 1104 | private | `get_backup_dir` | Liest oder ermittelt backup dir. |
| 1114 | private | `get_archive_path` | Liest oder ermittelt archive path. |
| 1131 | private | `create_temp_directory` | Erstellt temp directory. |
| 1147 | private | `delete_directory` | Entfernt directory. |
| 1176 | private | `detect_mime_type` | Interne Funktion fuer mime type. |
| 1186 | private | `validate_archive_file` | Interne Funktion fuer archive file. |
| 1239 | private | `validate_manifest_contents` | Interne Funktion fuer manifest contents. |
| 1288 | private | `generate_archive_token` | Interne Funktion fuer archive token. |
| 1292 | private | `sanitize_restored_author_id` | Bereinigt und validiert restored author id. |
| 1302 | private | `sanitize_restored_mysql_date` | Bereinigt und validiert restored mysql date. |
| 1312 | private | `is_allowed_restored_member_meta_key` | Prueft, ob allowed restored member meta key zutrifft. |
| 1346 | private | `sanitize_restored_member_meta_value` | Bereinigt und validiert restored member meta value. |
| 1394 | private | `sanitize_restored_text_list` | Bereinigt und validiert restored text list. |
| 1406 | private | `sanitize_restored_array` | Bereinigt und validiert restored array. |
| 1431 | private | `validate_gallery_source_file` | Interne Funktion fuer gallery source file. |

### `includes/class-feuer-mannschaft-complete.php`

Projektdatei des Plugins.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 33 | public static | `instance` | Liefert die Singleton-Instanz der Klasse. |
| 40 | private | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 105 | public | `install_capabilities` | Interne Funktion fuer capabilities. |
| 134 | public | `register_meta_boxes` | Registriert meta boxes. |
| 141 | public | `render_structure_box` | Rendert structure box. |
| 151 | private | `field_labels` | Interne Funktion fuer labels. |
| 157 | private | `visibility_levels` | Interne Funktion fuer levels. |
| 161 | public | `render_visibility_box` | Rendert visibility box. |
| 168 | public | `render_media_box` | Rendert media box. |
| 181 | public | `render_seo_box` | Rendert seo box. |
| 189 | public | `save_member_extensions` | Speichert member extensions. |
| 200 | private | `append_media_history` | Interne Funktion fuer media history. |
| 204 | public | `filter_member_meta` | Filtert oder passt member meta an. |
| 211 | public | `field_visible` | Interne Funktion fuer visible. |
| 216 | public | `enqueue_frontend_assets` | Laedt Assets oder Ressourcen fuer frontend assets. |
| 223 | public | `enqueue_admin_assets` | Laedt Assets oder Ressourcen fuer admin assets. |
| 231 | public | `register_rest_routes` | Registriert rest routes. |
| 237 | public | `rest_directory` | Interne Funktion fuer directory. |
| 248 | public | `directory_shortcode` | Interne Funktion fuer shortcode. |
| 253 | private | `directory_select` | Interne Funktion fuer select. |
| 254 | private | `value_select` | Interne Funktion fuer select. |
| 255 | private | `select_markup` | Interne Funktion fuer markup. |
| 256 | private | `profile_values` | Interne Funktion fuer values. |
| 258 | public | `qr_shortcode` | Interne Funktion fuer shortcode. |
| 262 | public | `member_actions_shortcode` | Interne Funktion fuer actions shortcode. |
| 264 | public | `org_chart_shortcode` | Interne Funktion fuer chart shortcode. |
| 268 | private | `render_org_nodes` | Rendert org nodes. |
| 270 | public | `compare_org_members` | Interne Funktion fuer org members. |
| 272 | private | `member_image_url` | Interne Funktion fuer image url. |
| 273 | private | `text` | Interne Funktion fuer text. |
| 274 | private | `valid_image_id` | Interne Funktion fuer image id. |
| 276 | private | `org_data` | Interne Funktion fuer data. |
| 277 | private | `sanitize_org_parent` | Bereinigt und validiert org parent. |
| 278 | private | `safe_org_parent_map` | Interne Funktion fuer org parent map. |
| 279 | private | `media_data` | Interne Funktion fuer data. |
| 280 | private | `seo_data` | Interne Funktion fuer data. |
| 281 | private | `members` | Interne Funktion fuer members. |
| 282 | private | `profile` | Interne Funktion fuer profile. |

### `includes/class-feuer-mannschaft-logger.php`

Kapselt Log-Tabelle, Schema-Updates und Protokollschreibvorgaenge.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 15 | public | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 22 | public | `maybe_install` | Fuehrt install nur bei Bedarf aus. |
| 32 | public | `install_table` | Interne Funktion fuer table. |
| 45 | private | `get_schema_sql` | Liest oder ermittelt schema sql. |
| 65 | private | `has_required_schema` | Prueft, ob required schema vorhanden ist. |
| 119 | public | `log` | Protokolliert log. |
| 157 | public | `get_logs` | Liest oder ermittelt logs. |
| 211 | public | `count_logs` | Interne Funktion fuer logs. |
| 236 | private | `append_log_filters` | Interne Funktion fuer log filters. |
| 253 | public | `get_action_choices` | Liest oder ermittelt action choices. |
| 261 | public | `prune_logs` | Interne Funktion fuer logs. |
| 271 | public | `get_user_choices` | Liest oder ermittelt user choices. |
| 295 | public | `get_action_label` | Liest oder ermittelt action label. |
| 315 | public | `format_details_for_display` | Formatiert details for display. |
| 353 | public | `get_table_name` | Liest oder ermittelt table name. |
| 357 | private | `table_exists` | Interne Funktion fuer exists. |
| 365 | private | `quote_table_name` | Interne Funktion fuer table name. |
| 369 | private | `normalize_details` | Normalisiert details. |
| 385 | private | `decode_details` | Interne Funktion fuer details. |

### `includes/class-feuer-mannschaft-mentions.php`

Verwaltet Erwahnungen, Verknuepfungen und Frontend-Ausgabe dazu.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 12 | public | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 16 | public | `register_hooks` | Registriert hooks. |
| 35 | public | `register_blocks` | Registriert blocks. |
| 116 | public | `register_acf_field_group` | Registriert acf field group. |
| 163 | public | `filter_acf_relationship_query` | Filtert oder passt acf relationship query an. |
| 180 | public | `filter_acf_prepare_field` | Filtert oder passt acf prepare field an. |
| 203 | public | `register_fallback_meta_boxes` | Registriert fallback meta boxes. |
| 223 | public | `register_shortcode_meta_boxes` | Registriert shortcode meta boxes. |
| 243 | public | `render_fallback_meta_box` | Rendert fallback meta box. |
| 266 | public | `render_shortcode_meta_box` | Rendert shortcode meta box. |
| 286 | public | `save_fallback_mentions` | Speichert fallback mentions. |
| 333 | public | `render_mentions_block` | Rendert mentions block. |
| 361 | public | `shortcode_member_link` | Rendert den Shortcode fuer member link. |
| 403 | public | `render_member_block` | Rendert member block. |
| 418 | public | `render_mentioned_members_block` | Rendert mentioned members block. |
| 426 | public | `render_member_posts_block` | Rendert member posts block. |
| 438 | public | `render_members_block` | Rendert members block. |
| 441 | public | `render_org_chart_block` | Rendert org chart block. |
| 444 | public | `render_service_history_block` | Rendert service history block. |
| 449 | public | `render_archived_members_block` | Rendert archived members block. |
| 453 | public | `shortcode_member_posts` | Rendert den Shortcode fuer member posts. |
| 463 | public | `shortcode_mentioned_members` | Rendert den Shortcode fuer mentioned members. |
| 473 | private | `render_mentioned_members` | Rendert mentioned members. |
| 512 | private | `render_member_posts` | Rendert member posts. |
| 529 | private | `resolve_block_post_id` | Loest block post id auf. |
| 537 | public | `is_editor_context` | Prueft, ob editor context zutrifft. |
| 558 | public | `get_editor_assets_data` | Liest oder ermittelt editor assets data. |
| 592 | public | `get_supported_post_types` | Liest oder ermittelt supported post types. |
| 622 | public | `is_enabled` | Prueft, ob enabled zutrifft. |
| 628 | public | `get_mentions_for_member` | Liest oder ermittelt mentions for member. |
| 702 | private | `get_member_choices` | Liest oder ermittelt member choices. |
| 720 | private | `build_shortcode_items` | Erzeugt shortcode items. |
| 745 | private | `get_member_link_shortcode_tags` | Liest oder ermittelt member link shortcode tags. |
| 752 | private | `format_member_ids_for_log` | Formatiert member ids for log. |
| 768 | private | `sanitize_selected_ids` | Bereinigt und validiert selected ids. |
| 794 | private | `normalize_selected_ids` | Normalisiert selected ids. |
| 810 | private | `get_current_post_id` | Liest oder ermittelt current post id. |
| 822 | private | `is_member_post` | Prueft, ob member post zutrifft. |
| 830 | private | `render_frontend_template` | Rendert frontend template. |

### `includes/class-feuer-mannschaft-plugin.php`

Zentrale Plugin-Klasse fuer Backend, Frontend, Settings, Templates und Mitgliederdaten.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 47 | public static | `instance` | Liefert die Singleton-Instanz der Klasse. |
| 55 | public static | `activate` | Fuehrt Aktivierungsroutinen des Plugins aus. |
| 66 | public static | `deactivate` | Fuehrt Deaktivierungsroutinen des Plugins aus. |
| 73 | public | `ensure_required_storage` | Stellt sicher, dass required storage vorhanden ist. |
| 78 | private | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 164 | public | `register_admin_menu` | Registriert admin menu. |
| 209 | public | `render_menu_placeholder` | Rendert menu placeholder. |
| 212 | public | `redirect_to_member_list` | Interne Funktion fuer to member list. |
| 217 | public | `register_post_status` | Registriert post status. |
| 239 | public | `migrate_archived_status` | Migriert archived status. |
| 267 | public | `register_member_taxonomies` | Registriert member taxonomies. |
| 310 | public | `disable_block_editor` | Deaktiviert block editor. |
| 318 | public | `disable_block_editor_for_post` | Deaktiviert block editor for post. |
| 326 | public | `filter_title_placeholder` | Filtert oder passt title placeholder an. |
| 334 | public | `display_post_states` | Zeigt post states an. |
| 348 | public | `filter_row_actions` | Filtert oder passt row actions an. |
| 386 | public | `filter_bulk_actions` | Filtert oder passt bulk actions an. |
| 399 | public | `handle_bulk_actions` | Verarbeitet bulk actions. |
| 428 | public | `register_meta_boxes` | Registriert meta boxes. |
| 470 | public | `render_service_history_meta_box` | Rendert service history meta box. |
| 479 | public | `add_post_form_enctype` | Fuegt post form enctype hinzu. |
| 485 | public | `render_details_meta_box` | Rendert details meta box. |
| 601 | public | `render_categories_meta_box` | Rendert categories meta box. |
| 614 | public | `render_gallery_meta_box` | Rendert gallery meta box. |
| 635 | public | `render_submitbox_actions` | Rendert submitbox actions. |
| 668 | public | `handle_archive_request` | Verarbeitet archive request. |
| 672 | public | `handle_unarchive_request` | Verarbeitet unarchive request. |
| 676 | public | `render_admin_notice` | Rendert admin notice. |
| 721 | public | `save_member` | Speichert member. |
| 1053 | public | `migrate_profiles_to_post_type` | Migriert profiles to post type. |
| 1090 | public | `register_settings` | Registriert settings. |
| 1098 | public | `sanitize_settings` | Bereinigt und validiert settings. |
| 1183 | public | `are_cache_purges_enabled` | Interne Funktion fuer cache purges enabled. |
| 1187 | public | `purge_plugin_cache` | Leert oder bereinigt plugin cache. |
| 1252 | public | `are_automatic_updates_enabled` | Interne Funktion fuer automatic updates enabled. |
| 1258 | public | `render_settings_page` | Rendert settings page. |
| 1357 | private | `get_health_checks` | Liest oder ermittelt health checks. |
| 1386 | private | `get_orphan_member_media` | Liest oder ermittelt orphan member media. |
| 1434 | private | `is_attachment_referenced_elsewhere` | Prueft, ob attachment referenced elsewhere zutrifft. |
| 1470 | public | `handle_cleanup_orphan_media_request` | Verarbeitet cleanup orphan media request. |
| 1489 | public | `schedule_log_pruning` | Interne Funktion fuer log pruning. |
| 1495 | public | `prune_logs` | Interne Funktion fuer logs. |
| 1500 | public | `handle_save_log_retention` | Verarbeitet save log retention. |
| 1521 | public | `handle_export_logs_request` | Verarbeitet export logs request. |
| 1558 | public | `enqueue_admin_assets` | Laedt Assets oder Ressourcen fuer admin assets. |
| 1604 | private | `get_admin_dashboard_summary` | Liest oder ermittelt admin dashboard summary. |
| 1653 | private | `register_member_post_type` | Registriert member post type. |
| 1682 | public | `enqueue_frontend_assets` | Laedt Assets oder Ressourcen fuer frontend assets. |
| 1734 | public | `tune_member_queries` | Optimiert member queries. |
| 1765 | public | `template_include` | Interne Funktion fuer include. |
| 1785 | public | `maybe_block_archived_member_access` | Fuehrt block archived member access nur bei Bedarf aus. |
| 1810 | public | `maybe_redirect_default_single_template_url` | Fuehrt redirect default single template url nur bei Bedarf aus. |
| 1829 | public | `shortcode_list` | Rendert den Shortcode fuer list. |
| 1853 | public | `shortcode_profile` | Rendert den Shortcode fuer profile. |
| 1892 | public | `shortcode_members` | Rendert den Shortcode fuer members. |
| 1920 | public | `shortcode_org_chart` | Rendert den Shortcode fuer org chart. |
| 1958 | public | `shortcode_service_history` | Rendert den Shortcode fuer service history. |
| 1968 | public | `shortcode_archived_members` | Rendert den Shortcode fuer archived members. |
| 1990 | public | `render_service_timeline` | Rendert service timeline. |
| 2006 | private | `get_service_history` | Liest oder ermittelt service history. |
| 2011 | private | `get_service_history_types` | Liest oder ermittelt service history types. |
| 2025 | private | `sanitize_service_history` | Bereinigt und validiert service history. |
| 2062 | public | `shortcode_count` | Rendert den Shortcode fuer count. |
| 2084 | public | `shortcode_cards` | Rendert den Shortcode fuer cards. |
| 2126 | public | `render_archive_page` | Rendert archive page. |
| 2139 | public | `render_single_page` | Rendert single page. |
| 2143 | public | `filter_admin_columns` | Filtert oder passt admin columns an. |
| 2167 | public | `render_admin_column` | Rendert admin column. |
| 2199 | public | `maybe_redirect_legacy_profile_url` | Fuehrt redirect legacy profile url nur bei Bedarf aus. |
| 2230 | public | `handle_update_check_request` | Verarbeitet update check request. |
| 2277 | private | `get_status_action_url` | Liest oder ermittelt status action url. |
| 2293 | private | `handle_status_request` | Verarbeitet status request. |
| 2322 | public | `handle_delete_request` | Verarbeitet delete request. |
| 2371 | public | `handle_permanent_delete_request` | Verarbeitet permanent delete request. |
| 2399 | private | `update_member_status` | Aktualisiert member status. |
| 2445 | public | `get_archive_markup` | Liest oder ermittelt archive markup. |
| 2474 | private | `get_archive_description` | Liest oder ermittelt archive description. |
| 2487 | public | `get_single_markup` | Liest oder ermittelt single markup. |
| 2539 | private | `get_archive_card_markup` | Liest oder ermittelt archive card markup. |
| 2567 | private | `get_archive_row_markup` | Liest oder ermittelt archive row markup. |
| 2591 | private | `get_archive_grid_markup` | Liest oder ermittelt archive grid markup. |
| 2599 | private | `get_member_standard_card_markup` | Liest oder ermittelt member standard card markup. |
| 2620 | private | `get_member_team_card_markup` | Liest oder ermittelt member team card markup. |
| 2641 | private | `get_members_query` | Liest oder ermittelt members query. |
| 2674 | private | `get_members_query_by_ids` | Liest oder ermittelt members query by ids. |
| 2705 | private | `get_filter_categories` | Liest oder ermittelt filter categories. |
| 2717 | private | `get_current_url_without_filter` | Liest oder ermittelt current url without filter. |
| 2724 | private | `map_legacy_display_to_template` | Ordnet legacy display to template zu. |
| 2735 | private | `get_requested_single_template` | Liest oder ermittelt requested single template. |
| 2743 | private | `resolve_single_template_slug` | Loest single template slug auf. |
| 2754 | private | `get_member_permalink` | Liest oder ermittelt member permalink. |
| 2786 | private | `get_selected_members_markup` | Liest oder ermittelt selected members markup. |
| 2817 | private | `render_template_file` | Rendert template file. |
| 2833 | private | `render_admin_template` | Rendert admin template. |
| 2850 | private | `render_frontend_template` | Rendert frontend template. |
| 2867 | public | `render_mentions_block` | Rendert mentions block. |
| 2871 | private | `get_admin_template_file` | Liest oder ermittelt admin template file. |
| 2878 | private | `get_frontend_template_file` | Liest oder ermittelt frontend template file. |
| 2885 | private | `get_settings_tabs` | Liest oder ermittelt settings tabs. |
| 2897 | private | `get_settings_tab` | Liest oder ermittelt settings tab. |
| 2904 | private | `get_settings_notice` | Liest oder ermittelt settings notice. |
| 2931 | private | `get_public_post_type_choices` | Liest oder ermittelt public post type choices. |
| 2956 | public | `maybe_reset_member_field_values` | Fuehrt reset member field values nur bei Bedarf aus. |
| 2986 | public | `maybe_sync_default_profile_images` | Fuehrt sync default profile images nur bei Bedarf aus. |
| 3000 | public | `maybe_sync_member_comments` | Fuehrt sync member comments nur bei Bedarf aus. |
| 3047 | public | `maybe_seed_default_field_options` | Fuehrt seed default field options nur bei Bedarf aus. |
| 3097 | private | `get_legacy_default_field_option_values` | Liest oder ermittelt legacy default field option values. |
| 3126 | private | `get_legacy_field_option_groups` | Liest oder ermittelt legacy field option groups. |
| 3151 | private | `legacy_sanitize_field_settings` | Interne Funktion fuer sanitize field settings. |
| 3213 | private | `legacy_sanitize_field_replacement_input` | Interne Funktion fuer sanitize field replacement input. |
| 3242 | private | `legacy_collect_removed_field_conflicts` | Interne Funktion fuer collect removed field conflicts. |
| 3289 | private | `legacy_apply_field_option_replacements` | Interne Funktion fuer apply field option replacements. |
| 3380 | private | `legacy_get_member_field_values_by_group` | Interne Funktion fuer get member field values by group. |
| 3400 | private | `legacy_set_member_field_values_by_group` | Interne Funktion fuer set member field values by group. |
| 3422 | private | `get_default_field_option_values` | Liest oder ermittelt default field option values. |
| 3454 | private | `get_fixed_field_option_groups` | Liest oder ermittelt fixed field option groups. |
| 3489 | private | `get_contact_field_definitions` | Liest oder ermittelt contact field definitions. |
| 3531 | private | `get_contact_field_settings` | Liest oder ermittelt contact field settings. |
| 3553 | private | `sanitize_contact_field_settings` | Bereinigt und validiert contact field settings. |
| 3561 | private | `get_enabled_contact_field_definitions` | Liest oder ermittelt enabled contact field definitions. |
| 3581 | private | `get_field_option_groups` | Liest oder ermittelt field option groups. |
| 3612 | private | `get_requested_field_settings` | Liest oder ermittelt requested field settings. |
| 3639 | private | `get_field_group_option_labels` | Liest oder ermittelt field group option labels. |
| 3654 | private | `sanitize_field_settings` | Bereinigt und validiert field settings. |
| 3743 | private | `sanitize_field_replacement_input` | Bereinigt und validiert field replacement input. |
| 3772 | private | `collect_removed_custom_group_conflicts` | Interne Funktion fuer removed custom group conflicts. |
| 3812 | private | `collect_removed_field_conflicts` | Interne Funktion fuer removed field conflicts. |
| 3862 | private | `apply_field_option_replacements` | Wendet field option replacements an. |
| 3956 | private | `get_member_field_values_by_group` | Liest oder ermittelt member field values by group. |
| 3988 | private | `set_member_field_values_by_group` | Interne Funktion fuer member field values by group. |
| 4027 | private | `persist_member_choice_meta` | Schreibt member choice meta dauerhaft in den Speicher. |
| 4052 | private | `persist_member_custom_fields_meta` | Schreibt member custom fields meta dauerhaft in den Speicher. |
| 4064 | private | `persist_member_contact_fields_meta` | Schreibt member contact fields meta dauerhaft in den Speicher. |
| 4076 | private | `get_pending_field_replacements` | Liest oder ermittelt pending field replacements. |
| 4082 | private | `set_pending_field_replacements` | Interne Funktion fuer pending field replacements. |
| 4086 | private | `clear_pending_field_replacements` | Interne Funktion fuer pending field replacements. |
| 4090 | private | `get_pending_field_replacements_key` | Liest oder ermittelt pending field replacements key. |
| 4094 | private | `get_member_post_ids` | Liest oder ermittelt member post ids. |
| 4119 | private | `sanitize_setting_text_list` | Bereinigt und validiert setting text list. |
| 4140 | private | `sanitize_setting_option_entries` | Bereinigt und validiert setting option entries. |
| 4177 | private | `sanitize_custom_field_groups` | Bereinigt und validiert custom field groups. |
| 4219 | private | `build_custom_field_group_key` | Erzeugt custom field group key. |
| 4241 | private | `sanitize_setting_post_types` | Bereinigt und validiert setting post types. |
| 4261 | private | `get_member_setting_options` | Liest oder ermittelt member setting options. |
| 4280 | private | `sanitize_member_single_select_value` | Bereinigt und validiert member single select value. |
| 4296 | private | `sanitize_member_multi_select_values` | Bereinigt und validiert member multi select values. |
| 4325 | private | `sanitize_member_education_values` | Bereinigt und validiert member education values. |
| 4329 | private | `normalize_member_education_meta` | Normalisiert member education meta. |
| 4333 | private | `sanitize_member_custom_fields_value` | Bereinigt und validiert member custom fields value. |
| 4374 | private | `sanitize_member_contact_fields_value` | Bereinigt und validiert member contact fields value. |
| 4431 | private | `sanitize_contact_handle` | Bereinigt und validiert contact handle. |
| 4464 | private | `sanitize_whatsapp_contact_value` | Bereinigt und validiert whatsapp contact value. |
| 4491 | private | `sanitize_custom_contact_link_value` | Bereinigt und validiert custom contact link value. |
| 4510 | private | `build_member_log_changes` | Erzeugt member log changes. |
| 4636 | private | `build_settings_log_changes` | Erzeugt settings log changes. |
| 4701 | private | `format_field_option_entries_for_log` | Formatiert field option entries for log. |
| 4725 | private | `format_custom_field_groups_for_log` | Formatiert custom field groups for log. |
| 4750 | private | `format_contact_field_settings_for_log` | Formatiert contact field settings for log. |
| 4770 | private | `format_contact_field_value_for_log` | Formatiert contact field value for log. |
| 4806 | private | `stringify_log_value` | Interne Funktion fuer log value. |
| 4828 | private | `get_category_name_for_log` | Liest oder ermittelt category name for log. |
| 4840 | private | `get_post_type_label_for_log` | Liest oder ermittelt post type label for log. |
| 4854 | private | `get_attachment_name_for_log` | Liest oder ermittelt attachment name for log. |
| 4871 | private | `get_template_choices` | Liest oder ermittelt template choices. |
| 4912 | private | `get_template_file` | Liest oder ermittelt template file. |
| 4919 | private | `sanitize_template_slug` | Bereinigt und validiert template slug. |
| 4937 | private | `get_template_directory` | Liest oder ermittelt template directory. |
| 4949 | private | `read_template_headers` | Interne Funktion fuer template headers. |
| 4974 | private | `sanitize_member_meta` | Bereinigt und validiert member meta. |
| 4998 | private | `sanitize_member_description` | Bereinigt und validiert member description. |
| 5002 | private | `sanitize_member_admin_note` | Bereinigt und validiert member admin note. |
| 5006 | private | `parse_member_name_from_title` | Interne Funktion fuer member name from title. |
| 5040 | private | `normalize_entry_year` | Normalisiert entry year. |
| 5066 | private | `resolve_shortcode_member_ids` | Loest shortcode member ids auf. |
| 5114 | private | `normalize_shortcode_category_ids` | Normalisiert shortcode category ids. |
| 5161 | private | `has_shortcode_category_filter` | Prueft, ob shortcode category filter vorhanden ist. |
| 5179 | private | `normalize_shortcode_count` | Normalisiert shortcode count. |
| 5189 | private | `validate_shortcode_member_id` | Interne Funktion fuer shortcode member id. |
| 5205 | private | `find_member_id_by_slug` | Interne Funktion fuer member id by slug. |
| 5219 | private | `find_member_id_by_name` | Interne Funktion fuer member id by name. |
| 5262 | private | `normalize_member_lookup_value` | Normalisiert member lookup value. |
| 5285 | private | `is_truthy_shortcode_attr` | Prueft, ob truthy shortcode attr zutrifft. |
| 5295 | private | `format_shortcode_count_text` | Formatiert shortcode count text. |
| 5304 | private | `sanitize_member_category` | Bereinigt und validiert member category. |
| 5320 | private | `sanitize_allowed_category_ids` | Bereinigt und validiert allowed category ids. |
| 5333 | private | `handle_gallery_uploads` | Verarbeitet gallery uploads. |
| 5403 | public | `filter_upload_dir` | Filtert oder passt upload dir an. |
| 5415 | private | `apply_watermark` | Wendet watermark an. |
| 5467 | private | `create_image_resource` | Erstellt image resource. |
| 5487 | private | `save_image_resource` | Speichert image resource. |
| 5507 | private | `prepare_watermark_text` | Bereitet watermark text vor. |
| 5525 | private | `get_member_meta` | Liest oder ermittelt member meta. |
| 5554 | public | `get_member_gallery` | Liest oder ermittelt member gallery. |
| 5570 | private | `is_cache_purge_enabled_in_settings` | Prueft, ob cache purge enabled in settings zutrifft. |
| 5574 | private | `normalize_cache_purge_ids` | Normalisiert cache purge ids. |
| 5578 | private | `normalize_cache_purge_urls` | Normalisiert cache purge urls. |
| 5596 | private | `get_shortcode_page_ids` | Liest oder ermittelt shortcode page ids. |
| 5644 | private | `get_shortcode_page_urls` | Liest oder ermittelt shortcode page urls. |
| 5657 | private | `get_member_cache_urls` | Liest oder ermittelt member cache urls. |
| 5685 | private | `purge_external_cache_plugins` | Leert oder bereinigt external cache plugins. |
| 5739 | private | `get_member_display_name` | Liest oder ermittelt member display name. |
| 5750 | private | `get_member_initials` | Liest oder ermittelt member initials. |
| 5768 | private | `get_member_visual_markup` | Liest oder ermittelt member visual markup. |
| 5792 | private | `get_member_contact_buttons_markup` | Liest oder ermittelt member contact buttons markup. |
| 5807 | private | `get_member_admin_notice_markup` | Liest oder ermittelt member admin notice markup. |
| 5827 | private | `get_member_contact_buttons` | Liest oder ermittelt member contact buttons. |
| 5851 | private | `build_member_contact_button` | Erzeugt member contact button. |
| 5879 | private | `build_contact_button_url` | Erzeugt contact button url. |
| 5922 | private | `get_single_detail_rows` | Liest oder ermittelt single detail rows. |
| 6001 | private | `current_user_can_manage_member_admin_note` | Interne Funktion fuer user can manage member admin note. |
| 6005 | private | `get_detail_value_markup` | Liest oder ermittelt detail value markup. |
| 6097 | private | `format_entry_date` | Formatiert entry date. |
| 6101 | private | `get_action_link_markup` | Liest oder ermittelt action link markup. |
| 6113 | private | `get_adjacent_members` | Liest oder ermittelt adjacent members. |
| 6130 | private | `get_related_member_ids` | Liest oder ermittelt related member ids. |
| 6159 | private | `get_ordered_member_ids` | Liest oder ermittelt ordered member ids. |
| 6163 | private | `get_active_profile_ids` | Liest oder ermittelt active profile ids. |
| 6211 | private | `get_countable_member_ids` | Liest oder ermittelt countable member ids. |
| 6224 | private | `should_load_frontend_assets` | Interne Funktion fuer load frontend assets. |
| 6256 | private | `get_default_settings` | Liest oder ermittelt default settings. |
| 6294 | private | `maybe_create_default_settings_option` | Fuehrt create default settings option nur bei Bedarf aus. |
| 6300 | private | `ensure_member_upload_storage` | Stellt sicher, dass member upload storage vorhanden ist. |
| 6311 | private | `prepare_storage_directory` | Bereitet storage directory vor. |
| 6339 | private | `get_member_upload_htaccess_content` | Liest oder ermittelt member upload htaccess content. |
| 6343 | public | `maybe_set_default_archive_template` | Fuehrt set default archive template nur bei Bedarf aus. |
| 6357 | private | `get_settings` | Liest oder ermittelt settings. |
| 6384 | public | `normalize_restored_settings` | Normalisiert restored settings. |
| 6410 | private | `set_member_save_error_notice` | Interne Funktion fuer member save error notice. |
| 6423 | private | `get_member_save_error_notice` | Liest oder ermittelt member save error notice. |
| 6441 | private | `sanitize_setting_color` | Bereinigt und validiert setting color. |
| 6456 | private | `get_member_title_class` | Liest oder ermittelt member title class. |
| 6462 | private | `get_member_title_style` | Liest oder ermittelt member title style. |
| 6476 | private | `sanitize_attachment_image_id` | Bereinigt und validiert attachment image id. |
| 6484 | private | `get_default_profile_image_id` | Liest oder ermittelt default profile image id. |
| 6492 | private | `get_default_profile_image_markup` | Liest oder ermittelt default profile image markup. |
| 6510 | private | `is_member_using_managed_default_image` | Prueft, ob member using managed default image zutrifft. |
| 6514 | private | `is_member_default_image_opted_out` | Prueft, ob member default image opted out zutrifft. |
| 6518 | private | `sync_default_profile_image_assignments` | Synchronisiert default profile image assignments. |
| 6533 | public | `sync_restored_member_default_thumbnail` | Synchronisiert restored member default thumbnail. |
| 6542 | private | `update_member_default_thumbnail_state` | Aktualisiert member default thumbnail state. |
| 6624 | private | `get_list_shortcode_tags` | Liest oder ermittelt list shortcode tags. |
| 6634 | private | `get_profile_shortcode_tags` | Liest oder ermittelt profile shortcode tags. |
| 6641 | private | `get_count_shortcode_tags` | Liest oder ermittelt count shortcode tags. |
| 6648 | private | `get_cards_shortcode_tags` | Liest oder ermittelt cards shortcode tags. |
| 6655 | private | `get_all_frontend_shortcode_tags` | Liest oder ermittelt all frontend shortcode tags. |
| 6680 | private | `get_allowed_category_ids` | Liest oder ermittelt allowed category ids. |
| 6687 | private | `get_allowed_category_ids_or_null` | Liest oder ermittelt allowed category ids or null. |
| 6693 | private | `get_category_choices` | Liest oder ermittelt category choices. |
| 6768 | private | `get_member_category_id` | Liest oder ermittelt member category id. |
| 6774 | private | `get_member_category_ids` | Liest oder ermittelt member category ids. |
| 6780 | private | `get_assigned_post_category_ids` | Liest oder ermittelt assigned post category ids. |
| 6784 | private | `get_preferred_member_category_id` | Liest oder ermittelt preferred member category id. |
| 6788 | private | `get_preferred_category_id_from_ids` | Liest oder ermittelt preferred category id from ids. |
| 6813 | private | `get_category_depth` | Liest oder ermittelt category depth. |
| 6817 | private | `build_member_category_assignment_ids` | Erzeugt member category assignment ids. |
| 6834 | private | `get_main_feuer_mannschaft_category_id` | Liest oder ermittelt main feuer mannschaft category id. |
| 6840 | private | `is_relevant_member_category` | Prueft, ob relevant member category zutrifft. |
| 6866 | private | `get_member_back_url` | Liest oder ermittelt member back url. |
| 6892 | private | `is_feuer_mannschaft_post` | Prueft, ob feuer mannschaft post zutrifft. |
| 6900 | private | `is_member_post` | Prueft, ob member post zutrifft. |
| 6904 | private | `get_invite_url` | Liest oder ermittelt invite url. |
| 6908 | private | `is_excluded_from_member_count` | Prueft, ob excluded from member count zutrifft. |
| 6912 | private | `sanitize_invite_url` | Bereinigt und validiert invite url. |
| 6927 | private | `is_member_request` | Prueft, ob member request zutrifft. |
| 6939 | private | `is_member_category_archive` | Prueft, ob member category archive zutrifft. |
| 6949 | private | `is_member_list_request` | Prueft, ob member list request zutrifft. |
| 6961 | private | `is_member_edit_context` | Prueft, ob member edit context zutrifft. |
| 6983 | private | `is_settings_screen` | Prueft, ob settings screen zutrifft. |
| 6987 | private | `is_feuer_mannschaft_admin_context` | Prueft, ob feuer mannschaft admin context zutrifft. |
| 6991 | private | `get_member_list_url` | Liest oder ermittelt member list url. |
| 6995 | private | `get_add_new_url` | Liest oder ermittelt add new url. |
| 6999 | private | `get_member_list_menu_slug` | Liest oder ermittelt member list menu slug. |
| 7003 | private | `get_add_new_menu_slug` | Liest oder ermittelt add new menu slug. |
| 7007 | private | `can_remove_member` | Interne Funktion fuer remove member. |
| 7017 | private | `can_remove_member_by_user` | Interne Funktion fuer remove member by user. |
| 7027 | public | `filter_admin_body_class` | Filtert oder passt admin body class an. |
| 7043 | public | `filter_frontend_body_class` | Filtert oder passt frontend body class an. |
| 7061 | private | `is_shortcode_profile_context` | Prueft, ob shortcode profile context zutrifft. |
| 7080 | private | `is_profile_shortcode_page` | Prueft, ob profile shortcode page zutrifft. |
| 7088 | public | `filter_parent_file` | Filtert oder passt parent file an. |
| 7096 | public | `filter_submenu_file` | Filtert oder passt submenu file an. |
| 7114 | public | `filter_revision_author_display_name` | Filtert oder passt revision author display name an. |
| 7124 | public | `filter_revision_author_name` | Filtert oder passt revision author name an. |
| 7132 | public | `filter_revision_author_data_for_js` | Filtert oder passt revision author data for js an. |
| 7161 | public | `filter_delete_caps` | Filtert oder passt delete caps an. |
| 7175 | private | `is_revision_author_context` | Prueft, ob revision author context zutrifft. |
| 7195 | private | `get_revision_context_post_id` | Liest oder ermittelt revision context post id. |
| 7215 | private | `get_preferred_revision_author_name` | Liest oder ermittelt preferred revision author name. |
| 7247 | public | `maybe_migrate_legacy_members` | Fuehrt migrate legacy members nur bei Bedarf aus. |
| 7322 | public | `maybe_sync_member_category_hierarchy` | Fuehrt sync member category hierarchy nur bei Bedarf aus. |
| 7360 | private | `get_migrated_member_post_id` | Liest oder ermittelt migrated member post id. |
| 7375 | private | `mb_char` | Interne Funktion fuer char. |
| 7383 | private | `clamp_int` | Interne Funktion fuer int. |

### `includes/class-feuer-mannschaft-toolkit.php`

Projektdatei des Plugins.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 19 | public static | `instance` | Liefert die Singleton-Instanz der Klasse. |
| 26 | private | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 77 | private | `has` | Prueft, ob has vorhanden ist. |
| 81 | private | `members` | Interne Funktion fuer members. |
| 91 | private | `profile` | Interne Funktion fuer profile. |
| 96 | public | `directory_assets` | Interne Funktion fuer assets. |
| 101 | public | `directory_shortcode` | Interne Funktion fuer shortcode. |
| 141 | private | `term_select` | Interne Funktion fuer select. |
| 149 | private | `text_value` | Interne Funktion fuer value. |
| 155 | public | `qr_shortcode` | Interne Funktion fuer shortcode. |
| 159 | public | `member_actions_shortcode` | Interne Funktion fuer actions shortcode. |
| 167 | public | `download_vcard` | Interne Funktion fuer vcard. |
| 182 | private | `vcard_escape` | Interne Funktion fuer escape. |
| 184 | public | `print_profile` | Interne Funktion fuer profile. |
| 190 | public | `install_roles` | Interne Funktion fuer roles. |
| 200 | public | `visibility_meta_box` | Interne Funktion fuer meta box. |
| 201 | public | `render_visibility` | Rendert visibility. |
| 205 | public | `save_visibility` | Speichert visibility. |
| 209 | public | `filter_field_visibility` | Filtert oder passt field visibility an. |
| 214 | public | `csv_menu` | Interne Funktion fuer menu. |
| 215 | public | `csv_page` | Interne Funktion fuer page. |
| 219 | public | `csv_export` | Interne Funktion fuer export. |
| 222 | private | `terms` | Interne Funktion fuer terms. |
| 223 | public | `csv_import` | Interne Funktion fuer import. |
| 231 | public | `csv_rollback` | Interne Funktion fuer rollback. |
| 235 | public | `quality_menu` | Interne Funktion fuer menu. |
| 236 | public | `quality_page` | Interne Funktion fuer page. |
| 240 | public | `image_meta_box` | Interne Funktion fuer meta box. |
| 241 | public | `render_image_focus` | Rendert image focus. |
| 242 | public | `save_image_focus` | Speichert image focus. |
| 243 | public | `image_focus_attributes` | Interne Funktion fuer focus attributes. |
| 244 | public | `regenerate_images` | Interne Funktion fuer images. |
| 246 | public | `seo_meta_box` | Interne Funktion fuer meta box. |
| 247 | public | `render_seo` | Rendert seo. |
| 248 | public | `save_seo` | Speichert seo. |
| 249 | private | `seo_managed_externally` | Interne Funktion fuer managed externally. |
| 250 | public | `prepare_seo_head` | Bereitet seo head vor. |
| 251 | public | `seo_head` | Interne Funktion fuer head. |
| 252 | public | `robots` | Interne Funktion fuer robots. |
| 253 | public | `breadcrumbs_shortcode` | Interne Funktion fuer shortcode. |
| 255 | public | `schedule_automation` | Interne Funktion fuer automation. |
| 256 | public | `daily_audit` | Interne Funktion fuer audit. |
| 261 | public | `weekly_report` | Interne Funktion fuer report. |
| 262 | private | `automation_settings` | Interne Funktion fuer settings. |
| 263 | private | `notify` | Interne Funktion fuer notify. |
| 264 | public | `automation_menu` | Interne Funktion fuer menu. |
| 265 | public | `automation_page` | Interne Funktion fuer page. |
| 274 | public | `save_automation` | Speichert automation. |

### `includes/class-feuer-mannschaft-updater.php`

Prueft remote auf neue Versionen und bindet Custom-Updates in WordPress ein.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 16 | public static | `instance` | Liefert die Singleton-Instanz der Klasse. |
| 24 | private | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 36 | public | `filter_update_transient` | Filtert oder passt update transient an. |
| 64 | public | `filter_plugin_information` | Filtert oder passt plugin information an. |
| 100 | public | `disable_automatic_update` | Deaktiviert automatic update. |
| 108 | public | `verify_package_download` | Interne Funktion fuer package download. |
| 155 | public | `purge_update_cache` | Leert oder bereinigt update cache. |
| 164 | public | `get_update_status` | Liest oder ermittelt update status. |
| 190 | public | `render_update_notice` | Rendert update notice. |
| 214 | private | `get_remote_metadata` | Liest oder ermittelt remote metadata. |
| 280 | private | `normalize_metadata` | Normalisiert metadata. |
| 321 | private | `build_update_payload` | Erzeugt update payload. |
| 335 | private | `is_own_upgrade_context` | Prueft, ob own upgrade context zutrifft. |
| 343 | private | `is_own_update_item` | Prueft, ob own update item zutrifft. |
| 363 | private | `normalize_sha256` | Normalisiert sha256. |
| 369 | private | `get_metadata_urls` | Liest oder ermittelt metadata urls. |
| 379 | private | `get_package_url` | Liest oder ermittelt package url. |
| 387 | private | `is_allowed_package_url` | Prueft, ob allowed package url zutrifft. |

### `includes/traits/trait-feuer-mannschaft-complete-admin.php`

Projektdatei des Plugins.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 6 | public | `register_mention_visibility_box` | Registriert mention visibility box. |
| 15 | public | `render_mention_visibility_box` | Rendert mention visibility box. |
| 26 | public | `save_hidden_mentions` | Speichert hidden mentions. |
| 34 | public | `replace_admin_pages` | Ersetzt admin pages. |
| 44 | public | `render_import_page` | Rendert import page. |
| 55 | private | `render_import_mapping` | Rendert import mapping. |
| 60 | public | `render_quality_page` | Rendert quality page. |
| 67 | public | `render_automation_page` | Rendert automation page. |
| 73 | private | `render_admin_result` | Rendert admin result. |

### `includes/traits/trait-feuer-mannschaft-complete-import.php`

Projektdatei des Plugins.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 6 | private | `import_targets` | Importiert targets. |
| 10 | private | `auto_map_column` | Interne Funktion fuer map column. |
| 16 | public | `import_preview` | Importiert preview. |
| 32 | public | `import_execute` | Importiert execute. |
| 53 | public | `import_rollback` | Importiert rollback. |
| 59 | private | `snapshot_member` | Interne Funktion fuer member. |
| 65 | private | `sideload_member_image` | Interne Funktion fuer member image. |
| 67 | private | `resolve_import_member_id` | Loest import member id auf. |
| 69 | public | `export_archive` | Exportiert archive. |
| 77 | private | `detect_delimiter` | Interne Funktion fuer delimiter. |
| 78 | private | `normalize_import_status` | Normalisiert import status. |
| 79 | private | `content_ids_mentioning_member` | Interne Funktion fuer ids mentioning member. |
| 80 | private | `verify_complete_request` | Interne Funktion fuer complete request. |
| 81 | private | `complete_admin_url` | Interne Funktion fuer admin url. |
| 82 | private | `set_import_result` | Interne Funktion fuer import result. |

### `includes/traits/trait-feuer-mannschaft-complete-operations.php`

Projektdatei des Plugins.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 8 | public | `visual_attachment_id` | Interne Funktion fuer attachment id. |
| 16 | public | `image_attributes` | Interne Funktion fuer attributes. |
| 22 | public | `modern_image_markup` | Interne Funktion fuer image markup. |
| 26 | public | `limit_profile_image_upload` | Interne Funktion fuer profile image upload. |
| 30 | public | `delete_modern_image_variants` | Entfernt modern image variants. |
| 32 | private | `remove_modern_variant_files` | Interne Funktion fuer modern variant files. |
| 34 | public | `process_media` | Interne Funktion fuer media. |
| 40 | public | `download_vcard` | Interne Funktion fuer vcard. |
| 41 | private | `complete_vcard_escape` | Interne Funktion fuer vcard escape. |
| 42 | public | `print_profile` | Interne Funktion fuer profile. |
| 44 | public | `sitemap_query` | Interne Funktion fuer query. |
| 45 | private | `seo_managed_externally` | Interne Funktion fuer managed externally. |
| 46 | public | `prepare_seo` | Bereitet seo vor. |
| 47 | public | `robots` | Interne Funktion fuer robots. |
| 48 | public | `seo_head` | Interne Funktion fuer head. |
| 50 | public | `run_audit_request` | Interne Funktion fuer audit request. |
| 51 | public | `run_integrity_audit` | Interne Funktion fuer integrity audit. |
| 52 | private | `build_integrity_audit` | Erzeugt integrity audit. |
| 57 | private | `automation_settings` | Interne Funktion fuer settings. |
| 58 | public | `save_automation` | Speichert automation. |
| 59 | public | `run_weekly_maintenance` | Interne Funktion fuer weekly maintenance. |
| 60 | public | `notify_bulk_or_restore` | Interne Funktion fuer bulk or restore. |
| 61 | private | `send_complete_mail` | Interne Funktion fuer complete mail. |

### `index.php`

Projektdatei des Plugins.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 6 | global | `fm_index_read` | Interne Funktion fuer index read. |
| 11 | global | `fm_index_escape` | Interne Funktion fuer index escape. |
| 12 | global | `fm_index_url` | Interne Funktion fuer index url. |
| 13 | global | `fm_index_header` | Interne Funktion fuer index header. |
| 16 | global | `fm_index_json` | Interne Funktion fuer index json. |
| 17 | global | `fm_index_templates` | Interne Funktion fuer index templates. |
| 29 | global | `fm_index_history` | Interne Funktion fuer index history. |

### `templates/admin/action-link.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/dashboard-summary.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/member-categories.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/member-details.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/member-gallery.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/member-preview.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/member-submitbox.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/mention-fallback.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/mention-shortcodes.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/message.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/no-photo.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/notice.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/service-history.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/archives.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/fields.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/general.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/health.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/logs.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/media.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/mentions.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/updates.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings-page.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/archive-feuer-mannschaft-member.php`

WordPress-Archiv-Wrapper fuer Mannschaftslisten.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/admin-note.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/archived-members.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/contact-buttons.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/detail-tags.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/detail-value.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/member-link.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/member-placeholder.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/member-posts.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/member-standard-card.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/member-team-card.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/mentions-section.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/org-chart.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/service-timeline.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/page-feuer-mannschaft-profile.php`

Spezielle Seitenvorlage fuer Profilseiten oder Shortcode-Ausgaben.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/profil/compact.php`

Frontend-Template fuer die Einzelansicht eines Mitglieds.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/profil/dossier.php`

Frontend-Template fuer die Einzelansicht eines Mitglieds.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/profil/magazin.php`

Frontend-Template fuer die Einzelansicht eines Mitglieds.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/profil/standard.php`

Frontend-Template fuer die Einzelansicht eines Mitglieds.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/single-feuer-mannschaft-member.php`

WordPress-Single-Wrapper fuer Einzelprofile.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/cards.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/cards-modal.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/directory.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/grayscale-cards.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/list.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/member-card.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/member-row.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/spotlight.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/standard.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/team-grayscale.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/uebersicht/team-grid.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `uninstall.php`

Entfernt Plugin-Daten und bereinigt Inhalte waehrend der Deinstallation.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 36 | global | `feuer_mannschaft_delete_directory` | Interne Funktion fuer mannschaft delete directory. |
| 64 | global | `feuer_mannschaft_delete_option` | Interne Funktion fuer mannschaft delete option. |
| 72 | global | `feuer_mannschaft_delete_matching_options` | Interne Funktion fuer mannschaft delete matching options. |
| 102 | global | `feuer_mannschaft_get_member_post_ids` | Interne Funktion fuer mannschaft get member post ids. |
| 116 | global | `feuer_mannschaft_get_legacy_post_ids` | Interne Funktion fuer mannschaft get legacy post ids. |
| 130 | global | `feuer_mannschaft_get_gallery_attachment_ids` | Interne Funktion fuer mannschaft get gallery attachment ids. |
| 145 | global | `feuer_mannschaft_get_attached_attachment_ids` | Interne Funktion fuer mannschaft get attached attachment ids. |
| 165 | global | `feuer_mannschaft_remove_owned_mentions` | Interne Funktion fuer mannschaft remove owned mentions. |
| 211 | global | `feuer_mannschaft_delete_modern_image_variants` | Interne Funktion fuer mannschaft delete modern image variants. |

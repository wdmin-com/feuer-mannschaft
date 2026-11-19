# Feuer-Mannschaft Dokumentation

Automatisch erzeugt am 2026-04-17 15:34:31.

## Zweck

Diese Datei dokumentiert die aktuelle Projektstruktur, beschreibt die wichtigsten Dateien und listet alle im Quellcode gefundenen PHP-Funktionen mit Dateipfad, Zeile und Kurzbeschreibung auf.

## Dateikarte

| Pfad | Beschreibung |
| --- | --- |
| `.gitattributes` | Git-Attribute fuer Zeilenenden und Export-Verhalten bei GitHub-Archiven. |
| `.gitignore` | Git-Regeln fuer lokale und generierte Dateien, die nicht versioniert werden sollen. |
| `ANLEITUNG.md` | Anwenderdokumentation fuer Installation, Bedienung und Einsatz im WordPress-Backend. |
| `assets/css/admin.css` | Styles fuer die Plugin-Administration, Einstellungen und Metaboxen. |
| `assets/css/feuer-mannschaft.css` | Styles fuer Archiv-, Karten- und Profilansichten im Frontend. |
| `assets/js/admin.js` | Admin-JavaScript fuer wiederholbare Felder, Uploads und Einstellungsoberflaechen. |
| `assets/js/feuer-mannschaft.js` | Frontend-JavaScript fuer Slider, Modalansichten und Interaktionen. |
| `DEPLOYMENT.md` | Anleitung fuer Release-Builds, Update-Bereitstellung und Serverablage. |
| `DOKUMENTATION.md` | Automatisch erzeugte Dokumentation mit Dateikarte und Funktionsindex. |
| `feuer-mannschaft.php` | Plugin-Bootstrap mit Konstanten, Aktivierungs-/Deaktivierungslogik und Laden der Kernklassen. |
| `feuer-mannschaft/README.md` | Zusatz-README in einem Unterordner, aktuell ohne inhaltliche Dokumentation. |
| `HISTORIE.md` | Chronologische Aenderungshistorie aller Plugin-Versionen. |
| `includes/class-feuer-mannschaft-backup-manager.php` | Sichert und stellt Plugin-Daten, Medien und Einstellungen aus Archiven wieder her. |
| `includes/class-feuer-mannschaft-logger.php` | Kapselt Log-Tabelle, Schema-Updates und Protokollschreibvorgaenge. |
| `includes/class-feuer-mannschaft-mentions.php` | Verwaltet Erwahnungen, Verknuepfungen und Frontend-Ausgabe dazu. |
| `includes/class-feuer-mannschaft-plugin.php` | Zentrale Plugin-Klasse fuer Backend, Frontend, Settings, Templates und Mitgliederdaten. |
| `includes/class-feuer-mannschaft-updater.php` | Prueft remote auf neue Versionen und bindet Custom-Updates in WordPress ein. |
| `README.md` | GitHub-Startdokument mit Projektueberblick, Entwicklungshinweisen und Release-Ablauf. |
| `templates/admin/action-link.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/member-categories.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/member-details.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/member-gallery.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/member-submitbox.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/mention-fallback.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/mention-shortcodes.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/message.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/no-photo.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/notice.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/admin/settings/archives.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/fields.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/general.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/logs.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings/mentions.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings-page.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/archive-feuer-mannschaft-member.php` | WordPress-Archiv-Wrapper fuer Mannschaftslisten. |
| `templates/frontend/detail-tags.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/detail-value.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/member-link.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/member-placeholder.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/member-standard-card.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/member-team-card.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/mentions-section.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/page-feuer-mannschaft-profile.php` | Spezielle Seitenvorlage fuer Profilseiten oder Shortcode-Ausgaben. |
| `templates/profil/compact.php` | Frontend-Template fuer die Einzelansicht eines Mitglieds. |
| `templates/profil/dossier.php` | Frontend-Template fuer die Einzelansicht eines Mitglieds. |
| `templates/profil/magazin.php` | Frontend-Template fuer die Einzelansicht eines Mitglieds. |
| `templates/profil/standard.php` | Frontend-Template fuer die Einzelansicht eines Mitglieds. |
| `templates/single-feuer-mannschaft-member.php` | WordPress-Single-Wrapper fuer Einzelprofile. |
| `templates/uebersicht/cards.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/cards-modal.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/directory.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/list.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/member-card.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/member-row.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/spotlight.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/standard.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `templates/uebersicht/team-grid.php` | Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft. |
| `tools/build-plugin-docs.ps1` | Erzeugt die Funktions- und Dateidokumentation automatisch aus dem Projektstand. |
| `tools/build-release-package.ps1` | Erstellt ein auslieferbares ZIP-Paket fuer Server oder GitHub-Releases. |
| `tools/create-version-snapshot.ps1` | Erzeugt einen Versions-Snapshot unter Version/<version>/ fuer den aktuellen Stand. |
| `uninstall.php` | Entfernt Plugin-Daten und bereinigt Inhalte waehrend der Deinstallation. |
| `update.json` | Remote-Manifest fuer die benutzerdefinierte WordPress-Updatepruefung. |

## PHP-Funktionsindex

Die folgenden Tabellen werden direkt aus den PHP-Dateien erzeugt. Dadurch bleiben Dateipfade und Zeilennummern fuer Wartung, Reviews und GitHub-Navigation nachvollziehbar.

### `feuer-mannschaft.php`

Plugin-Bootstrap mit Konstanten, Aktivierungs-/Deaktivierungslogik und Laden der Kernklassen.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 25 | global | `feuer_mannschaft_is_supported_php` | Interne Funktion fuer mannschaft is supported php. |
| 29 | global | `feuer_mannschaft_render_php_notice` | Interne Funktion fuer mannschaft render php notice. |
| 51 | global | `feuer_mannschaft_plugin` | Interne Funktion fuer mannschaft plugin. |
| 55 | global | `feuer_mannschaft_updater` | Interne Funktion fuer mannschaft updater. |
| 59 | global | `feuer_mannschaft_activate` | Interne Funktion fuer mannschaft activate. |
| 72 | global | `feuer_mannschaft_deactivate` | Interne Funktion fuer mannschaft deactivate. |

### `includes/class-feuer-mannschaft-backup-manager.php`

Sichert und stellt Plugin-Daten, Medien und Einstellungen aus Archiven wieder her.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 14 | public | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 18 | public | `register_hooks` | Registriert hooks. |
| 27 | public | `ensure_storage` | Stellt sicher, dass storage vorhanden ist. |
| 53 | public | `get_archives` | Liest oder ermittelt archives. |
| 83 | public | `handle_create_request` | Verarbeitet create request. |
| 105 | public | `handle_delete_request` | Verarbeitet delete request. |
| 138 | public | `handle_download_request` | Verarbeitet download request. |
| 172 | public | `handle_upload_request` | Verarbeitet upload request. |
| 249 | public | `handle_restore_request` | Verarbeitet restore request. |
| 293 | public | `create_archive` | Erstellt archive. |
| 343 | public | `restore_archive` | Stellt archive wieder her. |
| 437 | private | `build_manifest` | Erzeugt manifest. |
| 451 | private | `export_members` | Exportiert members. |
| 497 | private | `export_member_categories` | Exportiert member categories. |
| 521 | private | `export_gallery` | Exportiert gallery. |
| 557 | private | `export_mentions` | Exportiert mentions. |
| 602 | private | `export_logs` | Exportiert logs. |
| 625 | private | `get_member_meta_export` | Liest oder ermittelt member meta export. |
| 638 | private | `restore_member_record` | Stellt member record wieder her. |
| 685 | private | `restore_member_gallery` | Stellt member gallery wieder her. |
| 797 | private | `restore_mention_record` | Stellt mention record wieder her. |
| 847 | private | `find_existing_member_id` | Interne Funktion fuer existing member id. |
| 900 | private | `resolve_member_category_ids` | Loest member category ids auf. |
| 938 | private | `reset_member_gallery` | Interne Funktion fuer member gallery. |
| 953 | private | `sync_member_default_thumbnail` | Synchronisiert member default thumbnail. |
| 965 | private | `verify_manage_options_request` | Interne Funktion fuer manage options request. |
| 973 | private | `redirect_with_notice` | Interne Funktion fuer with notice. |
| 983 | private | `get_backup_dir` | Liest oder ermittelt backup dir. |
| 993 | private | `get_archive_path` | Liest oder ermittelt archive path. |
| 1010 | private | `create_temp_directory` | Erstellt temp directory. |
| 1026 | private | `delete_directory` | Entfernt directory. |
| 1055 | private | `detect_mime_type` | Interne Funktion fuer mime type. |
| 1065 | private | `validate_archive_file` | Interne Funktion fuer archive file. |
| 1118 | private | `validate_manifest_contents` | Interne Funktion fuer manifest contents. |

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
| 200 | public | `get_user_choices` | Liest oder ermittelt user choices. |
| 224 | public | `get_action_label` | Liest oder ermittelt action label. |
| 243 | public | `format_details_for_display` | Formatiert details for display. |
| 281 | public | `get_table_name` | Liest oder ermittelt table name. |
| 285 | private | `table_exists` | Interne Funktion fuer exists. |
| 293 | private | `quote_table_name` | Interne Funktion fuer table name. |
| 297 | private | `normalize_details` | Normalisiert details. |
| 313 | private | `decode_details` | Interne Funktion fuer details. |

### `includes/class-feuer-mannschaft-mentions.php`

Verwaltet Erwahnungen, Verknuepfungen und Frontend-Ausgabe dazu.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 12 | public | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 16 | public | `register_hooks` | Registriert hooks. |
| 30 | public | `register_acf_field_group` | Registriert acf field group. |
| 77 | public | `filter_acf_relationship_query` | Filtert oder passt acf relationship query an. |
| 94 | public | `filter_acf_prepare_field` | Filtert oder passt acf prepare field an. |
| 117 | public | `register_fallback_meta_boxes` | Registriert fallback meta boxes. |
| 137 | public | `register_shortcode_meta_boxes` | Registriert shortcode meta boxes. |
| 157 | public | `render_fallback_meta_box` | Rendert fallback meta box. |
| 180 | public | `render_shortcode_meta_box` | Rendert shortcode meta box. |
| 200 | public | `save_fallback_mentions` | Speichert fallback mentions. |
| 247 | public | `render_mentions_block` | Rendert mentions block. |
| 275 | public | `shortcode_member_link` | Rendert den Shortcode fuer member link. |
| 317 | public | `is_editor_context` | Prueft, ob editor context zutrifft. |
| 338 | public | `get_editor_assets_data` | Liest oder ermittelt editor assets data. |
| 372 | public | `get_supported_post_types` | Liest oder ermittelt supported post types. |
| 402 | public | `is_enabled` | Prueft, ob enabled zutrifft. |
| 408 | public | `get_mentions_for_member` | Liest oder ermittelt mentions for member. |
| 468 | private | `get_member_choices` | Liest oder ermittelt member choices. |
| 486 | private | `build_shortcode_items` | Erzeugt shortcode items. |
| 511 | private | `get_member_link_shortcode_tags` | Liest oder ermittelt member link shortcode tags. |
| 518 | private | `format_member_ids_for_log` | Formatiert member ids for log. |
| 534 | private | `sanitize_selected_ids` | Bereinigt und validiert selected ids. |
| 560 | private | `normalize_selected_ids` | Normalisiert selected ids. |
| 576 | private | `get_current_post_id` | Liest oder ermittelt current post id. |
| 588 | private | `is_member_post` | Prueft, ob member post zutrifft. |
| 596 | private | `render_frontend_template` | Rendert frontend template. |

### `includes/class-feuer-mannschaft-plugin.php`

Zentrale Plugin-Klasse fuer Backend, Frontend, Settings, Templates und Mitgliederdaten.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 36 | public static | `instance` | Liefert die Singleton-Instanz der Klasse. |
| 44 | public static | `activate` | Fuehrt Aktivierungsroutinen des Plugins aus. |
| 53 | public static | `deactivate` | Fuehrt Deaktivierungsroutinen des Plugins aus. |
| 57 | public | `ensure_required_storage` | Stellt sicher, dass required storage vorhanden ist. |
| 62 | private | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 127 | public | `register_admin_menu` | Registriert admin menu. |
| 172 | public | `render_menu_placeholder` | Rendert menu placeholder. |
| 175 | public | `redirect_to_member_list` | Interne Funktion fuer to member list. |
| 180 | public | `register_post_status` | Registriert post status. |
| 201 | public | `disable_block_editor` | Deaktiviert block editor. |
| 209 | public | `disable_block_editor_for_post` | Deaktiviert block editor for post. |
| 217 | public | `filter_title_placeholder` | Filtert oder passt title placeholder an. |
| 225 | public | `display_post_states` | Zeigt post states an. |
| 239 | public | `filter_row_actions` | Filtert oder passt row actions an. |
| 274 | public | `filter_bulk_actions` | Filtert oder passt bulk actions an. |
| 287 | public | `handle_bulk_actions` | Verarbeitet bulk actions. |
| 316 | public | `register_meta_boxes` | Registriert meta boxes. |
| 349 | public | `add_post_form_enctype` | Fuegt post form enctype hinzu. |
| 355 | public | `render_details_meta_box` | Rendert details meta box. |
| 447 | public | `render_categories_meta_box` | Rendert categories meta box. |
| 460 | public | `render_gallery_meta_box` | Rendert gallery meta box. |
| 481 | public | `render_submitbox_actions` | Rendert submitbox actions. |
| 514 | public | `handle_archive_request` | Verarbeitet archive request. |
| 518 | public | `handle_unarchive_request` | Verarbeitet unarchive request. |
| 522 | public | `render_admin_notice` | Rendert admin notice. |
| 554 | public | `save_member` | Speichert member. |
| 785 | public | `register_settings` | Registriert settings. |
| 793 | public | `sanitize_settings` | Bereinigt und validiert settings. |
| 862 | public | `render_settings_page` | Rendert settings page. |
| 914 | public | `enqueue_admin_assets` | Laedt Assets oder Ressourcen fuer admin assets. |
| 959 | public | `enqueue_frontend_assets` | Laedt Assets oder Ressourcen fuer frontend assets. |
| 1001 | public | `tune_member_queries` | Optimiert member queries. |
| 1032 | public | `template_include` | Interne Funktion fuer include. |
| 1048 | public | `maybe_block_archived_member_access` | Fuehrt block archived member access nur bei Bedarf aus. |
| 1073 | public | `maybe_redirect_default_single_template_url` | Fuehrt redirect default single template url nur bei Bedarf aus. |
| 1092 | public | `shortcode_list` | Rendert den Shortcode fuer list. |
| 1116 | public | `shortcode_profile` | Rendert den Shortcode fuer profile. |
| 1155 | public | `shortcode_count` | Rendert den Shortcode fuer count. |
| 1177 | public | `shortcode_cards` | Rendert den Shortcode fuer cards. |
| 1219 | public | `render_archive_page` | Rendert archive page. |
| 1232 | public | `render_single_page` | Rendert single page. |
| 1236 | public | `filter_admin_columns` | Filtert oder passt admin columns an. |
| 1252 | public | `render_admin_column` | Rendert admin column. |
| 1284 | private | `get_status_action_url` | Liest oder ermittelt status action url. |
| 1300 | private | `handle_status_request` | Verarbeitet status request. |
| 1329 | public | `handle_delete_request` | Verarbeitet delete request. |
| 1373 | private | `update_member_status` | Aktualisiert member status. |
| 1417 | public | `get_archive_markup` | Liest oder ermittelt archive markup. |
| 1446 | private | `get_archive_description` | Liest oder ermittelt archive description. |
| 1459 | public | `get_single_markup` | Liest oder ermittelt single markup. |
| 1498 | private | `get_archive_card_markup` | Liest oder ermittelt archive card markup. |
| 1524 | private | `get_archive_row_markup` | Liest oder ermittelt archive row markup. |
| 1546 | private | `get_archive_grid_markup` | Liest oder ermittelt archive grid markup. |
| 1554 | private | `get_member_standard_card_markup` | Liest oder ermittelt member standard card markup. |
| 1580 | private | `get_member_team_card_markup` | Liest oder ermittelt member team card markup. |
| 1597 | private | `get_members_query` | Liest oder ermittelt members query. |
| 1630 | private | `get_members_query_by_ids` | Liest oder ermittelt members query by ids. |
| 1661 | private | `get_filter_categories` | Liest oder ermittelt filter categories. |
| 1673 | private | `get_current_url_without_filter` | Liest oder ermittelt current url without filter. |
| 1680 | private | `map_legacy_display_to_template` | Ordnet legacy display to template zu. |
| 1691 | private | `get_requested_single_template` | Liest oder ermittelt requested single template. |
| 1699 | private | `resolve_single_template_slug` | Loest single template slug auf. |
| 1710 | private | `get_member_permalink` | Liest oder ermittelt member permalink. |
| 1742 | private | `get_selected_members_markup` | Liest oder ermittelt selected members markup. |
| 1773 | private | `render_template_file` | Rendert template file. |
| 1789 | private | `render_admin_template` | Rendert admin template. |
| 1806 | private | `render_frontend_template` | Rendert frontend template. |
| 1823 | public | `render_mentions_block` | Rendert mentions block. |
| 1827 | private | `get_admin_template_file` | Liest oder ermittelt admin template file. |
| 1834 | private | `get_frontend_template_file` | Liest oder ermittelt frontend template file. |
| 1841 | private | `get_settings_tabs` | Liest oder ermittelt settings tabs. |
| 1850 | private | `get_settings_tab` | Liest oder ermittelt settings tab. |
| 1857 | private | `get_settings_notice` | Liest oder ermittelt settings notice. |
| 1879 | private | `get_public_post_type_choices` | Liest oder ermittelt public post type choices. |
| 1904 | public | `maybe_reset_member_field_values` | Fuehrt reset member field values nur bei Bedarf aus. |
| 1934 | public | `maybe_sync_default_profile_images` | Fuehrt sync default profile images nur bei Bedarf aus. |
| 1948 | public | `maybe_sync_member_comments` | Fuehrt sync member comments nur bei Bedarf aus. |
| 1995 | public | `maybe_seed_default_field_options` | Fuehrt seed default field options nur bei Bedarf aus. |
| 2045 | private | `get_legacy_default_field_option_values` | Liest oder ermittelt legacy default field option values. |
| 2074 | private | `get_legacy_field_option_groups` | Liest oder ermittelt legacy field option groups. |
| 2099 | private | `legacy_sanitize_field_settings` | Interne Funktion fuer sanitize field settings. |
| 2161 | private | `legacy_sanitize_field_replacement_input` | Interne Funktion fuer sanitize field replacement input. |
| 2190 | private | `legacy_collect_removed_field_conflicts` | Interne Funktion fuer collect removed field conflicts. |
| 2237 | private | `legacy_apply_field_option_replacements` | Interne Funktion fuer apply field option replacements. |
| 2328 | private | `legacy_get_member_field_values_by_group` | Interne Funktion fuer get member field values by group. |
| 2348 | private | `legacy_set_member_field_values_by_group` | Interne Funktion fuer set member field values by group. |
| 2370 | private | `get_default_field_option_values` | Liest oder ermittelt default field option values. |
| 2402 | private | `get_fixed_field_option_groups` | Liest oder ermittelt fixed field option groups. |
| 2437 | private | `get_field_option_groups` | Liest oder ermittelt field option groups. |
| 2468 | private | `get_requested_field_settings` | Liest oder ermittelt requested field settings. |
| 2490 | private | `get_field_group_option_labels` | Liest oder ermittelt field group option labels. |
| 2505 | private | `sanitize_field_settings` | Bereinigt und validiert field settings. |
| 2594 | private | `sanitize_field_replacement_input` | Bereinigt und validiert field replacement input. |
| 2623 | private | `collect_removed_custom_group_conflicts` | Interne Funktion fuer removed custom group conflicts. |
| 2663 | private | `collect_removed_field_conflicts` | Interne Funktion fuer removed field conflicts. |
| 2713 | private | `apply_field_option_replacements` | Wendet field option replacements an. |
| 2807 | private | `get_member_field_values_by_group` | Liest oder ermittelt member field values by group. |
| 2839 | private | `set_member_field_values_by_group` | Interne Funktion fuer member field values by group. |
| 2878 | private | `persist_member_choice_meta` | Schreibt member choice meta dauerhaft in den Speicher. |
| 2903 | private | `persist_member_custom_fields_meta` | Schreibt member custom fields meta dauerhaft in den Speicher. |
| 2915 | private | `get_pending_field_replacements` | Liest oder ermittelt pending field replacements. |
| 2921 | private | `set_pending_field_replacements` | Interne Funktion fuer pending field replacements. |
| 2925 | private | `clear_pending_field_replacements` | Interne Funktion fuer pending field replacements. |
| 2929 | private | `get_pending_field_replacements_key` | Liest oder ermittelt pending field replacements key. |
| 2933 | private | `get_member_post_ids` | Liest oder ermittelt member post ids. |
| 2958 | private | `sanitize_setting_text_list` | Bereinigt und validiert setting text list. |
| 2979 | private | `sanitize_setting_option_entries` | Bereinigt und validiert setting option entries. |
| 3016 | private | `sanitize_custom_field_groups` | Bereinigt und validiert custom field groups. |
| 3058 | private | `build_custom_field_group_key` | Erzeugt custom field group key. |
| 3080 | private | `sanitize_setting_post_types` | Bereinigt und validiert setting post types. |
| 3100 | private | `get_member_setting_options` | Liest oder ermittelt member setting options. |
| 3119 | private | `sanitize_member_single_select_value` | Bereinigt und validiert member single select value. |
| 3135 | private | `sanitize_member_multi_select_values` | Bereinigt und validiert member multi select values. |
| 3164 | private | `sanitize_member_education_values` | Bereinigt und validiert member education values. |
| 3168 | private | `normalize_member_education_meta` | Normalisiert member education meta. |
| 3172 | private | `sanitize_member_custom_fields_value` | Bereinigt und validiert member custom fields value. |
| 3213 | private | `build_member_log_changes` | Erzeugt member log changes. |
| 3317 | private | `build_settings_log_changes` | Erzeugt settings log changes. |
| 3374 | private | `format_field_option_entries_for_log` | Formatiert field option entries for log. |
| 3398 | private | `format_custom_field_groups_for_log` | Formatiert custom field groups for log. |
| 3423 | private | `stringify_log_value` | Interne Funktion fuer log value. |
| 3445 | private | `get_category_name_for_log` | Liest oder ermittelt category name for log. |
| 3457 | private | `get_post_type_label_for_log` | Liest oder ermittelt post type label for log. |
| 3471 | private | `get_attachment_name_for_log` | Liest oder ermittelt attachment name for log. |
| 3488 | private | `get_template_choices` | Liest oder ermittelt template choices. |
| 3529 | private | `get_template_file` | Liest oder ermittelt template file. |
| 3536 | private | `sanitize_template_slug` | Bereinigt und validiert template slug. |
| 3554 | private | `get_template_directory` | Liest oder ermittelt template directory. |
| 3566 | private | `read_template_headers` | Interne Funktion fuer template headers. |
| 3591 | private | `sanitize_member_meta` | Bereinigt und validiert member meta. |
| 3610 | private | `sanitize_member_description` | Bereinigt und validiert member description. |
| 3614 | private | `parse_member_name_from_title` | Interne Funktion fuer member name from title. |
| 3648 | private | `normalize_entry_year` | Normalisiert entry year. |
| 3674 | private | `resolve_shortcode_member_ids` | Loest shortcode member ids auf. |
| 3722 | private | `normalize_shortcode_category_ids` | Normalisiert shortcode category ids. |
| 3769 | private | `has_shortcode_category_filter` | Prueft, ob shortcode category filter vorhanden ist. |
| 3787 | private | `normalize_shortcode_count` | Normalisiert shortcode count. |
| 3797 | private | `validate_shortcode_member_id` | Interne Funktion fuer shortcode member id. |
| 3813 | private | `find_member_id_by_slug` | Interne Funktion fuer member id by slug. |
| 3827 | private | `find_member_id_by_name` | Interne Funktion fuer member id by name. |
| 3870 | private | `normalize_member_lookup_value` | Normalisiert member lookup value. |
| 3893 | private | `is_truthy_shortcode_attr` | Prueft, ob truthy shortcode attr zutrifft. |
| 3903 | private | `format_shortcode_count_text` | Formatiert shortcode count text. |
| 3912 | private | `sanitize_member_category` | Bereinigt und validiert member category. |
| 3928 | private | `sanitize_allowed_category_ids` | Bereinigt und validiert allowed category ids. |
| 3941 | private | `handle_gallery_uploads` | Verarbeitet gallery uploads. |
| 4011 | public | `filter_upload_dir` | Filtert oder passt upload dir an. |
| 4023 | private | `apply_watermark` | Wendet watermark an. |
| 4075 | private | `create_image_resource` | Erstellt image resource. |
| 4095 | private | `save_image_resource` | Speichert image resource. |
| 4115 | private | `prepare_watermark_text` | Bereitet watermark text vor. |
| 4133 | private | `get_member_meta` | Liest oder ermittelt member meta. |
| 4154 | public | `get_member_gallery` | Liest oder ermittelt member gallery. |
| 4170 | private | `get_member_display_name` | Liest oder ermittelt member display name. |
| 4181 | private | `get_member_initials` | Liest oder ermittelt member initials. |
| 4199 | private | `get_member_visual_markup` | Liest oder ermittelt member visual markup. |
| 4220 | private | `get_single_detail_rows` | Liest oder ermittelt single detail rows. |
| 4270 | private | `get_detail_value_markup` | Liest oder ermittelt detail value markup. |
| 4362 | private | `format_entry_date` | Formatiert entry date. |
| 4366 | private | `get_action_link_markup` | Liest oder ermittelt action link markup. |
| 4378 | private | `get_adjacent_members` | Liest oder ermittelt adjacent members. |
| 4395 | private | `get_related_member_ids` | Liest oder ermittelt related member ids. |
| 4424 | private | `get_ordered_member_ids` | Liest oder ermittelt ordered member ids. |
| 4428 | private | `get_active_profile_ids` | Liest oder ermittelt active profile ids. |
| 4476 | private | `get_countable_member_ids` | Liest oder ermittelt countable member ids. |
| 4489 | private | `should_load_frontend_assets` | Interne Funktion fuer load frontend assets. |
| 4517 | private | `get_default_settings` | Liest oder ermittelt default settings. |
| 4541 | private | `maybe_create_default_settings_option` | Fuehrt create default settings option nur bei Bedarf aus. |
| 4547 | private | `ensure_member_upload_storage` | Stellt sicher, dass member upload storage vorhanden ist. |
| 4558 | private | `prepare_storage_directory` | Bereitet storage directory vor. |
| 4586 | private | `get_settings` | Liest oder ermittelt settings. |
| 4608 | private | `sanitize_attachment_image_id` | Bereinigt und validiert attachment image id. |
| 4616 | private | `get_default_profile_image_id` | Liest oder ermittelt default profile image id. |
| 4624 | private | `get_default_profile_image_markup` | Liest oder ermittelt default profile image markup. |
| 4642 | private | `is_member_using_managed_default_image` | Prueft, ob member using managed default image zutrifft. |
| 4646 | private | `is_member_default_image_opted_out` | Prueft, ob member default image opted out zutrifft. |
| 4650 | private | `sync_default_profile_image_assignments` | Synchronisiert default profile image assignments. |
| 4665 | public | `sync_restored_member_default_thumbnail` | Synchronisiert restored member default thumbnail. |
| 4674 | private | `update_member_default_thumbnail_state` | Aktualisiert member default thumbnail state. |
| 4756 | private | `get_list_shortcode_tags` | Liest oder ermittelt list shortcode tags. |
| 4766 | private | `get_profile_shortcode_tags` | Liest oder ermittelt profile shortcode tags. |
| 4773 | private | `get_count_shortcode_tags` | Liest oder ermittelt count shortcode tags. |
| 4780 | private | `get_cards_shortcode_tags` | Liest oder ermittelt cards shortcode tags. |
| 4787 | private | `get_all_frontend_shortcode_tags` | Liest oder ermittelt all frontend shortcode tags. |
| 4800 | private | `get_allowed_category_ids` | Liest oder ermittelt allowed category ids. |
| 4807 | private | `get_allowed_category_ids_or_null` | Liest oder ermittelt allowed category ids or null. |
| 4813 | private | `get_category_choices` | Liest oder ermittelt category choices. |
| 4888 | private | `get_member_category_id` | Liest oder ermittelt member category id. |
| 4894 | private | `get_member_category_ids` | Liest oder ermittelt member category ids. |
| 4900 | private | `get_assigned_post_category_ids` | Liest oder ermittelt assigned post category ids. |
| 4904 | private | `get_preferred_member_category_id` | Liest oder ermittelt preferred member category id. |
| 4908 | private | `get_preferred_category_id_from_ids` | Liest oder ermittelt preferred category id from ids. |
| 4933 | private | `get_category_depth` | Liest oder ermittelt category depth. |
| 4937 | private | `build_member_category_assignment_ids` | Erzeugt member category assignment ids. |
| 4954 | private | `get_main_feuer_mannschaft_category_id` | Liest oder ermittelt main feuer mannschaft category id. |
| 4960 | private | `is_relevant_member_category` | Prueft, ob relevant member category zutrifft. |
| 4986 | private | `get_member_back_url` | Liest oder ermittelt member back url. |
| 5007 | private | `is_feuer_mannschaft_post` | Prueft, ob feuer mannschaft post zutrifft. |
| 5015 | private | `is_member_post` | Prueft, ob member post zutrifft. |
| 5019 | private | `get_invite_url` | Liest oder ermittelt invite url. |
| 5023 | private | `is_excluded_from_member_count` | Prueft, ob excluded from member count zutrifft. |
| 5027 | private | `sanitize_invite_url` | Bereinigt und validiert invite url. |
| 5042 | private | `is_member_request` | Prueft, ob member request zutrifft. |
| 5054 | private | `is_member_category_archive` | Prueft, ob member category archive zutrifft. |
| 5064 | private | `is_member_list_request` | Prueft, ob member list request zutrifft. |
| 5076 | private | `is_member_edit_context` | Prueft, ob member edit context zutrifft. |
| 5098 | private | `is_settings_screen` | Prueft, ob settings screen zutrifft. |
| 5102 | private | `is_feuer_mannschaft_admin_context` | Prueft, ob feuer mannschaft admin context zutrifft. |
| 5106 | private | `get_member_list_url` | Liest oder ermittelt member list url. |
| 5110 | private | `get_add_new_url` | Liest oder ermittelt add new url. |
| 5114 | private | `get_member_list_menu_slug` | Liest oder ermittelt member list menu slug. |
| 5118 | private | `get_add_new_menu_slug` | Liest oder ermittelt add new menu slug. |
| 5122 | private | `can_remove_member` | Interne Funktion fuer remove member. |
| 5132 | private | `can_remove_member_by_user` | Interne Funktion fuer remove member by user. |
| 5142 | public | `filter_admin_body_class` | Filtert oder passt admin body class an. |
| 5158 | public | `filter_frontend_body_class` | Filtert oder passt frontend body class an. |
| 5170 | private | `is_shortcode_profile_context` | Prueft, ob shortcode profile context zutrifft. |
| 5189 | private | `is_profile_shortcode_page` | Prueft, ob profile shortcode page zutrifft. |
| 5197 | public | `filter_parent_file` | Filtert oder passt parent file an. |
| 5205 | public | `filter_submenu_file` | Filtert oder passt submenu file an. |
| 5223 | public | `filter_revision_author_display_name` | Filtert oder passt revision author display name an. |
| 5233 | public | `filter_revision_author_name` | Filtert oder passt revision author name an. |
| 5241 | public | `filter_revision_author_data_for_js` | Filtert oder passt revision author data for js an. |
| 5270 | public | `filter_delete_caps` | Filtert oder passt delete caps an. |
| 5284 | private | `is_revision_author_context` | Prueft, ob revision author context zutrifft. |
| 5304 | private | `get_revision_context_post_id` | Liest oder ermittelt revision context post id. |
| 5324 | private | `get_preferred_revision_author_name` | Liest oder ermittelt preferred revision author name. |
| 5356 | public | `maybe_migrate_legacy_members` | Fuehrt migrate legacy members nur bei Bedarf aus. |
| 5426 | public | `maybe_sync_member_category_hierarchy` | Fuehrt sync member category hierarchy nur bei Bedarf aus. |
| 5464 | private | `get_migrated_member_post_id` | Liest oder ermittelt migrated member post id. |
| 5479 | private | `mb_char` | Interne Funktion fuer char. |
| 5487 | private | `clamp_int` | Interne Funktion fuer int. |

### `includes/class-feuer-mannschaft-updater.php`

Prueft remote auf neue Versionen und bindet Custom-Updates in WordPress ein.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 14 | public static | `instance` | Liefert die Singleton-Instanz der Klasse. |
| 22 | private | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 31 | public | `filter_update_transient` | Filtert oder passt update transient an. |
| 58 | public | `filter_plugin_information` | Filtert oder passt plugin information an. |
| 94 | public | `purge_update_cache` | Leert oder bereinigt update cache. |
| 102 | public | `render_update_notice` | Rendert update notice. |
| 126 | private | `get_remote_metadata` | Liest oder ermittelt remote metadata. |
| 171 | private | `normalize_metadata` | Normalisiert metadata. |
| 204 | private | `build_update_payload` | Erzeugt update payload. |
| 218 | private | `get_package_url` | Liest oder ermittelt package url. |

### `templates/admin/action-link.php`

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

### `templates/admin/settings/archives.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/fields.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/general.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/logs.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings/mentions.php`

Teilvorlage fuer einzelne Bereiche der Einstellungsseite.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/admin/settings-page.php`

Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/archive-feuer-mannschaft-member.php`

WordPress-Archiv-Wrapper fuer Mannschaftslisten.

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

### `templates/frontend/member-standard-card.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/member-team-card.php`

Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `templates/frontend/mentions-section.php`

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

### `templates/uebersicht/team-grid.php`

Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.

Keine benannten PHP-Funktionen in dieser Datei gefunden.

### `uninstall.php`

Entfernt Plugin-Daten und bereinigt Inhalte waehrend der Deinstallation.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 31 | global | `feuer_mannschaft_delete_directory` | Interne Funktion fuer mannschaft delete directory. |
| 59 | global | `feuer_mannschaft_delete_option` | Interne Funktion fuer mannschaft delete option. |
| 67 | global | `feuer_mannschaft_delete_matching_options` | Interne Funktion fuer mannschaft delete matching options. |
| 97 | global | `feuer_mannschaft_get_member_post_ids` | Interne Funktion fuer mannschaft get member post ids. |
| 111 | global | `feuer_mannschaft_get_legacy_post_ids` | Interne Funktion fuer mannschaft get legacy post ids. |
| 124 | global | `feuer_mannschaft_get_gallery_attachment_ids` | Interne Funktion fuer mannschaft get gallery attachment ids. |
| 139 | global | `feuer_mannschaft_get_attached_attachment_ids` | Interne Funktion fuer mannschaft get attached attachment ids. |

# Feuer-Mannschaft Dokumentation

Automatisch erzeugt am 2026-06-20 22:21:14.

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
| `assets/css/feuer-mannschaft.css` | Styles fuer Archiv-, Karten- und Profilansichten im Frontend. |
| `assets/js/admin.js` | Admin-JavaScript fuer wiederholbare Felder, Uploads und Einstellungsoberflaechen. |
| `assets/js/feuer-mannschaft.js` | Frontend-JavaScript fuer Slider, Modalansichten und Interaktionen. |
| `DEPLOYMENT.md` | Anleitung fuer Release-Builds, Update-Bereitstellung und Serverablage. |
| `DOKUMENTATION.md` | Automatisch erzeugte Dokumentation mit Dateikarte und Funktionsindex. |
| `feuer-mannschaft.php` | Plugin-Bootstrap mit Konstanten, Aktivierungs-/Deaktivierungslogik und Laden der Kernklassen. |
| `feuer-mannschaft.zip` | Projektdatei des Plugins. |
| `HISTORIE.md` | Chronologische Aenderungshistorie aller Plugin-Versionen. |
| `includes/class-feuer-mannschaft-backup-manager.php` | Sichert und stellt Plugin-Daten, Medien und Einstellungen aus Archiven wieder her. |
| `includes/class-feuer-mannschaft-logger.php` | Kapselt Log-Tabelle, Schema-Updates und Protokollschreibvorgaenge. |
| `includes/class-feuer-mannschaft-mentions.php` | Verwaltet Erwahnungen, Verknuepfungen und Frontend-Ausgabe dazu. |
| `includes/class-feuer-mannschaft-plugin.php` | Zentrale Plugin-Klasse fuer Backend, Frontend, Settings, Templates und Mitgliederdaten. |
| `includes/class-feuer-mannschaft-updater.php` | Prueft remote auf neue Versionen und bindet Custom-Updates in WordPress ein. |
| `index.php` | Projektdatei des Plugins. |
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
| `templates/admin/settings/updates.php` | Teilvorlage fuer einzelne Bereiche der Einstellungsseite. |
| `templates/admin/settings-page.php` | Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen. |
| `templates/archive-feuer-mannschaft-member.php` | WordPress-Archiv-Wrapper fuer Mannschaftslisten. |
| `templates/frontend/admin-note.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
| `templates/frontend/contact-buttons.php` | Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter. |
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
| `tools/create-version-snapshot.ps1` | Erzeugt einen Versions-Snapshot unter Version/<version>/ fuer den aktuellen Stand. |
| `uninstall.php` | Entfernt Plugin-Daten und bereinigt Inhalte waehrend der Deinstallation. |
| `update.json` | Remote-Manifest fuer die benutzerdefinierte WordPress-Updatepruefung. |

## PHP-Funktionsindex

Die folgenden Tabellen werden direkt aus den PHP-Dateien erzeugt. Dadurch bleiben Dateipfade und Zeilennummern fuer Wartung, Reviews und GitHub-Navigation nachvollziehbar.

### `feuer-mannschaft.php`

Plugin-Bootstrap mit Konstanten, Aktivierungs-/Deaktivierungslogik und Laden der Kernklassen.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 24 | global | `feuer_mannschaft_is_supported_php` | Interne Funktion fuer mannschaft is supported php. |
| 28 | global | `feuer_mannschaft_render_php_notice` | Interne Funktion fuer mannschaft render php notice. |
| 50 | global | `feuer_mannschaft_plugin` | Interne Funktion fuer mannschaft plugin. |
| 54 | global | `feuer_mannschaft_updater` | Interne Funktion fuer mannschaft updater. |
| 58 | global | `feuer_mannschaft_activate` | Interne Funktion fuer mannschaft activate. |
| 71 | global | `feuer_mannschaft_deactivate` | Interne Funktion fuer mannschaft deactivate. |
| 75 | global | `feuer_mannschaft_cleanup_removed_usage_registry` | Interne Funktion fuer mannschaft cleanup removed usage registry. |

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
| 448 | private | `build_manifest` | Erzeugt manifest. |
| 462 | private | `export_members` | Exportiert members. |
| 508 | private | `export_member_categories` | Exportiert member categories. |
| 532 | private | `export_gallery` | Exportiert gallery. |
| 568 | private | `export_mentions` | Exportiert mentions. |
| 613 | private | `export_logs` | Exportiert logs. |
| 636 | private | `get_member_meta_export` | Liest oder ermittelt member meta export. |
| 649 | private | `restore_member_record` | Stellt member record wieder her. |
| 700 | private | `restore_member_gallery` | Stellt member gallery wieder her. |
| 812 | private | `restore_mention_record` | Stellt mention record wieder her. |
| 862 | private | `find_existing_member_id` | Interne Funktion fuer existing member id. |
| 915 | private | `resolve_member_category_ids` | Loest member category ids auf. |
| 953 | private | `reset_member_gallery` | Interne Funktion fuer member gallery. |
| 968 | private | `sync_member_default_thumbnail` | Synchronisiert member default thumbnail. |
| 980 | private | `verify_manage_options_request` | Interne Funktion fuer manage options request. |
| 988 | private | `redirect_with_notice` | Interne Funktion fuer with notice. |
| 998 | private | `get_backup_dir` | Liest oder ermittelt backup dir. |
| 1008 | private | `get_archive_path` | Liest oder ermittelt archive path. |
| 1025 | private | `create_temp_directory` | Erstellt temp directory. |
| 1041 | private | `delete_directory` | Entfernt directory. |
| 1070 | private | `detect_mime_type` | Interne Funktion fuer mime type. |
| 1080 | private | `validate_archive_file` | Interne Funktion fuer archive file. |
| 1133 | private | `validate_manifest_contents` | Interne Funktion fuer manifest contents. |

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
| 38 | public static | `instance` | Liefert die Singleton-Instanz der Klasse. |
| 46 | public static | `activate` | Fuehrt Aktivierungsroutinen des Plugins aus. |
| 55 | public static | `deactivate` | Fuehrt Deaktivierungsroutinen des Plugins aus. |
| 59 | public | `ensure_required_storage` | Stellt sicher, dass required storage vorhanden ist. |
| 64 | private | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 130 | public | `register_admin_menu` | Registriert admin menu. |
| 175 | public | `render_menu_placeholder` | Rendert menu placeholder. |
| 178 | public | `redirect_to_member_list` | Interne Funktion fuer to member list. |
| 183 | public | `register_post_status` | Registriert post status. |
| 204 | public | `disable_block_editor` | Deaktiviert block editor. |
| 212 | public | `disable_block_editor_for_post` | Deaktiviert block editor for post. |
| 220 | public | `filter_title_placeholder` | Filtert oder passt title placeholder an. |
| 228 | public | `display_post_states` | Zeigt post states an. |
| 242 | public | `filter_row_actions` | Filtert oder passt row actions an. |
| 277 | public | `filter_bulk_actions` | Filtert oder passt bulk actions an. |
| 290 | public | `handle_bulk_actions` | Verarbeitet bulk actions. |
| 319 | public | `register_meta_boxes` | Registriert meta boxes. |
| 352 | public | `add_post_form_enctype` | Fuegt post form enctype hinzu. |
| 358 | public | `render_details_meta_box` | Rendert details meta box. |
| 454 | public | `render_categories_meta_box` | Rendert categories meta box. |
| 467 | public | `render_gallery_meta_box` | Rendert gallery meta box. |
| 488 | public | `render_submitbox_actions` | Rendert submitbox actions. |
| 521 | public | `handle_archive_request` | Verarbeitet archive request. |
| 525 | public | `handle_unarchive_request` | Verarbeitet unarchive request. |
| 529 | public | `render_admin_notice` | Rendert admin notice. |
| 572 | public | `save_member` | Speichert member. |
| 845 | public | `register_settings` | Registriert settings. |
| 853 | public | `sanitize_settings` | Bereinigt und validiert settings. |
| 931 | public | `are_cache_purges_enabled` | Interne Funktion fuer cache purges enabled. |
| 935 | public | `purge_plugin_cache` | Leert oder bereinigt plugin cache. |
| 1000 | public | `are_automatic_updates_enabled` | Interne Funktion fuer automatic updates enabled. |
| 1006 | public | `render_settings_page` | Rendert settings page. |
| 1064 | public | `enqueue_admin_assets` | Laedt Assets oder Ressourcen fuer admin assets. |
| 1109 | public | `enqueue_frontend_assets` | Laedt Assets oder Ressourcen fuer frontend assets. |
| 1155 | public | `tune_member_queries` | Optimiert member queries. |
| 1186 | public | `template_include` | Interne Funktion fuer include. |
| 1202 | public | `maybe_block_archived_member_access` | Fuehrt block archived member access nur bei Bedarf aus. |
| 1227 | public | `maybe_redirect_default_single_template_url` | Fuehrt redirect default single template url nur bei Bedarf aus. |
| 1246 | public | `shortcode_list` | Rendert den Shortcode fuer list. |
| 1270 | public | `shortcode_profile` | Rendert den Shortcode fuer profile. |
| 1309 | public | `shortcode_count` | Rendert den Shortcode fuer count. |
| 1331 | public | `shortcode_cards` | Rendert den Shortcode fuer cards. |
| 1373 | public | `render_archive_page` | Rendert archive page. |
| 1386 | public | `render_single_page` | Rendert single page. |
| 1390 | public | `filter_admin_columns` | Filtert oder passt admin columns an. |
| 1414 | public | `render_admin_column` | Rendert admin column. |
| 1446 | public | `handle_update_check_request` | Verarbeitet update check request. |
| 1488 | private | `get_status_action_url` | Liest oder ermittelt status action url. |
| 1504 | private | `handle_status_request` | Verarbeitet status request. |
| 1533 | public | `handle_delete_request` | Verarbeitet delete request. |
| 1581 | private | `update_member_status` | Aktualisiert member status. |
| 1627 | public | `get_archive_markup` | Liest oder ermittelt archive markup. |
| 1656 | private | `get_archive_description` | Liest oder ermittelt archive description. |
| 1669 | public | `get_single_markup` | Liest oder ermittelt single markup. |
| 1714 | private | `get_archive_card_markup` | Liest oder ermittelt archive card markup. |
| 1742 | private | `get_archive_row_markup` | Liest oder ermittelt archive row markup. |
| 1766 | private | `get_archive_grid_markup` | Liest oder ermittelt archive grid markup. |
| 1774 | private | `get_member_standard_card_markup` | Liest oder ermittelt member standard card markup. |
| 1802 | private | `get_member_team_card_markup` | Liest oder ermittelt member team card markup. |
| 1821 | private | `get_members_query` | Liest oder ermittelt members query. |
| 1854 | private | `get_members_query_by_ids` | Liest oder ermittelt members query by ids. |
| 1885 | private | `get_filter_categories` | Liest oder ermittelt filter categories. |
| 1897 | private | `get_current_url_without_filter` | Liest oder ermittelt current url without filter. |
| 1904 | private | `map_legacy_display_to_template` | Ordnet legacy display to template zu. |
| 1915 | private | `get_requested_single_template` | Liest oder ermittelt requested single template. |
| 1923 | private | `resolve_single_template_slug` | Loest single template slug auf. |
| 1934 | private | `get_member_permalink` | Liest oder ermittelt member permalink. |
| 1966 | private | `get_selected_members_markup` | Liest oder ermittelt selected members markup. |
| 1997 | private | `render_template_file` | Rendert template file. |
| 2013 | private | `render_admin_template` | Rendert admin template. |
| 2030 | private | `render_frontend_template` | Rendert frontend template. |
| 2047 | public | `render_mentions_block` | Rendert mentions block. |
| 2051 | private | `get_admin_template_file` | Liest oder ermittelt admin template file. |
| 2058 | private | `get_frontend_template_file` | Liest oder ermittelt frontend template file. |
| 2065 | private | `get_settings_tabs` | Liest oder ermittelt settings tabs. |
| 2075 | private | `get_settings_tab` | Liest oder ermittelt settings tab. |
| 2082 | private | `get_settings_notice` | Liest oder ermittelt settings notice. |
| 2107 | private | `get_public_post_type_choices` | Liest oder ermittelt public post type choices. |
| 2132 | public | `maybe_reset_member_field_values` | Fuehrt reset member field values nur bei Bedarf aus. |
| 2162 | public | `maybe_sync_default_profile_images` | Fuehrt sync default profile images nur bei Bedarf aus. |
| 2176 | public | `maybe_sync_member_comments` | Fuehrt sync member comments nur bei Bedarf aus. |
| 2223 | public | `maybe_seed_default_field_options` | Fuehrt seed default field options nur bei Bedarf aus. |
| 2273 | private | `get_legacy_default_field_option_values` | Liest oder ermittelt legacy default field option values. |
| 2302 | private | `get_legacy_field_option_groups` | Liest oder ermittelt legacy field option groups. |
| 2327 | private | `legacy_sanitize_field_settings` | Interne Funktion fuer sanitize field settings. |
| 2389 | private | `legacy_sanitize_field_replacement_input` | Interne Funktion fuer sanitize field replacement input. |
| 2418 | private | `legacy_collect_removed_field_conflicts` | Interne Funktion fuer collect removed field conflicts. |
| 2465 | private | `legacy_apply_field_option_replacements` | Interne Funktion fuer apply field option replacements. |
| 2556 | private | `legacy_get_member_field_values_by_group` | Interne Funktion fuer get member field values by group. |
| 2576 | private | `legacy_set_member_field_values_by_group` | Interne Funktion fuer set member field values by group. |
| 2598 | private | `get_default_field_option_values` | Liest oder ermittelt default field option values. |
| 2630 | private | `get_fixed_field_option_groups` | Liest oder ermittelt fixed field option groups. |
| 2665 | private | `get_contact_field_definitions` | Liest oder ermittelt contact field definitions. |
| 2707 | private | `get_contact_field_settings` | Liest oder ermittelt contact field settings. |
| 2729 | private | `sanitize_contact_field_settings` | Bereinigt und validiert contact field settings. |
| 2737 | private | `get_enabled_contact_field_definitions` | Liest oder ermittelt enabled contact field definitions. |
| 2757 | private | `get_field_option_groups` | Liest oder ermittelt field option groups. |
| 2788 | private | `get_requested_field_settings` | Liest oder ermittelt requested field settings. |
| 2815 | private | `get_field_group_option_labels` | Liest oder ermittelt field group option labels. |
| 2830 | private | `sanitize_field_settings` | Bereinigt und validiert field settings. |
| 2919 | private | `sanitize_field_replacement_input` | Bereinigt und validiert field replacement input. |
| 2948 | private | `collect_removed_custom_group_conflicts` | Interne Funktion fuer removed custom group conflicts. |
| 2988 | private | `collect_removed_field_conflicts` | Interne Funktion fuer removed field conflicts. |
| 3038 | private | `apply_field_option_replacements` | Wendet field option replacements an. |
| 3132 | private | `get_member_field_values_by_group` | Liest oder ermittelt member field values by group. |
| 3164 | private | `set_member_field_values_by_group` | Interne Funktion fuer member field values by group. |
| 3203 | private | `persist_member_choice_meta` | Schreibt member choice meta dauerhaft in den Speicher. |
| 3228 | private | `persist_member_custom_fields_meta` | Schreibt member custom fields meta dauerhaft in den Speicher. |
| 3240 | private | `persist_member_contact_fields_meta` | Schreibt member contact fields meta dauerhaft in den Speicher. |
| 3252 | private | `get_pending_field_replacements` | Liest oder ermittelt pending field replacements. |
| 3258 | private | `set_pending_field_replacements` | Interne Funktion fuer pending field replacements. |
| 3262 | private | `clear_pending_field_replacements` | Interne Funktion fuer pending field replacements. |
| 3266 | private | `get_pending_field_replacements_key` | Liest oder ermittelt pending field replacements key. |
| 3270 | private | `get_member_post_ids` | Liest oder ermittelt member post ids. |
| 3295 | private | `sanitize_setting_text_list` | Bereinigt und validiert setting text list. |
| 3316 | private | `sanitize_setting_option_entries` | Bereinigt und validiert setting option entries. |
| 3353 | private | `sanitize_custom_field_groups` | Bereinigt und validiert custom field groups. |
| 3395 | private | `build_custom_field_group_key` | Erzeugt custom field group key. |
| 3417 | private | `sanitize_setting_post_types` | Bereinigt und validiert setting post types. |
| 3437 | private | `get_member_setting_options` | Liest oder ermittelt member setting options. |
| 3456 | private | `sanitize_member_single_select_value` | Bereinigt und validiert member single select value. |
| 3472 | private | `sanitize_member_multi_select_values` | Bereinigt und validiert member multi select values. |
| 3501 | private | `sanitize_member_education_values` | Bereinigt und validiert member education values. |
| 3505 | private | `normalize_member_education_meta` | Normalisiert member education meta. |
| 3509 | private | `sanitize_member_custom_fields_value` | Bereinigt und validiert member custom fields value. |
| 3550 | private | `sanitize_member_contact_fields_value` | Bereinigt und validiert member contact fields value. |
| 3607 | private | `sanitize_contact_handle` | Bereinigt und validiert contact handle. |
| 3640 | private | `sanitize_whatsapp_contact_value` | Bereinigt und validiert whatsapp contact value. |
| 3667 | private | `sanitize_custom_contact_link_value` | Bereinigt und validiert custom contact link value. |
| 3686 | private | `build_member_log_changes` | Erzeugt member log changes. |
| 3812 | private | `build_settings_log_changes` | Erzeugt settings log changes. |
| 3877 | private | `format_field_option_entries_for_log` | Formatiert field option entries for log. |
| 3901 | private | `format_custom_field_groups_for_log` | Formatiert custom field groups for log. |
| 3926 | private | `format_contact_field_settings_for_log` | Formatiert contact field settings for log. |
| 3946 | private | `format_contact_field_value_for_log` | Formatiert contact field value for log. |
| 3982 | private | `stringify_log_value` | Interne Funktion fuer log value. |
| 4004 | private | `get_category_name_for_log` | Liest oder ermittelt category name for log. |
| 4016 | private | `get_post_type_label_for_log` | Liest oder ermittelt post type label for log. |
| 4030 | private | `get_attachment_name_for_log` | Liest oder ermittelt attachment name for log. |
| 4047 | private | `get_template_choices` | Liest oder ermittelt template choices. |
| 4088 | private | `get_template_file` | Liest oder ermittelt template file. |
| 4095 | private | `sanitize_template_slug` | Bereinigt und validiert template slug. |
| 4113 | private | `get_template_directory` | Liest oder ermittelt template directory. |
| 4125 | private | `read_template_headers` | Interne Funktion fuer template headers. |
| 4150 | private | `sanitize_member_meta` | Bereinigt und validiert member meta. |
| 4172 | private | `sanitize_member_description` | Bereinigt und validiert member description. |
| 4176 | private | `sanitize_member_admin_note` | Bereinigt und validiert member admin note. |
| 4180 | private | `parse_member_name_from_title` | Interne Funktion fuer member name from title. |
| 4214 | private | `normalize_entry_year` | Normalisiert entry year. |
| 4240 | private | `resolve_shortcode_member_ids` | Loest shortcode member ids auf. |
| 4288 | private | `normalize_shortcode_category_ids` | Normalisiert shortcode category ids. |
| 4335 | private | `has_shortcode_category_filter` | Prueft, ob shortcode category filter vorhanden ist. |
| 4353 | private | `normalize_shortcode_count` | Normalisiert shortcode count. |
| 4363 | private | `validate_shortcode_member_id` | Interne Funktion fuer shortcode member id. |
| 4379 | private | `find_member_id_by_slug` | Interne Funktion fuer member id by slug. |
| 4393 | private | `find_member_id_by_name` | Interne Funktion fuer member id by name. |
| 4436 | private | `normalize_member_lookup_value` | Normalisiert member lookup value. |
| 4459 | private | `is_truthy_shortcode_attr` | Prueft, ob truthy shortcode attr zutrifft. |
| 4469 | private | `format_shortcode_count_text` | Formatiert shortcode count text. |
| 4478 | private | `sanitize_member_category` | Bereinigt und validiert member category. |
| 4494 | private | `sanitize_allowed_category_ids` | Bereinigt und validiert allowed category ids. |
| 4507 | private | `handle_gallery_uploads` | Verarbeitet gallery uploads. |
| 4577 | public | `filter_upload_dir` | Filtert oder passt upload dir an. |
| 4589 | private | `apply_watermark` | Wendet watermark an. |
| 4641 | private | `create_image_resource` | Erstellt image resource. |
| 4661 | private | `save_image_resource` | Speichert image resource. |
| 4681 | private | `prepare_watermark_text` | Bereitet watermark text vor. |
| 4699 | private | `get_member_meta` | Liest oder ermittelt member meta. |
| 4723 | public | `get_member_gallery` | Liest oder ermittelt member gallery. |
| 4739 | private | `is_cache_purge_enabled_in_settings` | Prueft, ob cache purge enabled in settings zutrifft. |
| 4743 | private | `normalize_cache_purge_ids` | Normalisiert cache purge ids. |
| 4747 | private | `normalize_cache_purge_urls` | Normalisiert cache purge urls. |
| 4765 | private | `get_shortcode_page_ids` | Liest oder ermittelt shortcode page ids. |
| 4813 | private | `get_shortcode_page_urls` | Liest oder ermittelt shortcode page urls. |
| 4826 | private | `get_member_cache_urls` | Liest oder ermittelt member cache urls. |
| 4854 | private | `purge_external_cache_plugins` | Leert oder bereinigt external cache plugins. |
| 4908 | private | `get_member_display_name` | Liest oder ermittelt member display name. |
| 4919 | private | `get_member_initials` | Liest oder ermittelt member initials. |
| 4937 | private | `get_member_visual_markup` | Liest oder ermittelt member visual markup. |
| 4958 | private | `get_member_contact_buttons_markup` | Liest oder ermittelt member contact buttons markup. |
| 4973 | private | `get_member_admin_notice_markup` | Liest oder ermittelt member admin notice markup. |
| 4993 | private | `get_member_contact_buttons` | Liest oder ermittelt member contact buttons. |
| 5014 | private | `build_member_contact_button` | Erzeugt member contact button. |
| 5042 | private | `build_contact_button_url` | Erzeugt contact button url. |
| 5085 | private | `get_single_detail_rows` | Liest oder ermittelt single detail rows. |
| 5135 | private | `current_user_can_manage_member_admin_note` | Interne Funktion fuer user can manage member admin note. |
| 5139 | private | `get_detail_value_markup` | Liest oder ermittelt detail value markup. |
| 5231 | private | `format_entry_date` | Formatiert entry date. |
| 5235 | private | `get_action_link_markup` | Liest oder ermittelt action link markup. |
| 5247 | private | `get_adjacent_members` | Liest oder ermittelt adjacent members. |
| 5264 | private | `get_related_member_ids` | Liest oder ermittelt related member ids. |
| 5293 | private | `get_ordered_member_ids` | Liest oder ermittelt ordered member ids. |
| 5297 | private | `get_active_profile_ids` | Liest oder ermittelt active profile ids. |
| 5345 | private | `get_countable_member_ids` | Liest oder ermittelt countable member ids. |
| 5358 | private | `should_load_frontend_assets` | Interne Funktion fuer load frontend assets. |
| 5386 | private | `get_default_settings` | Liest oder ermittelt default settings. |
| 5416 | private | `maybe_create_default_settings_option` | Fuehrt create default settings option nur bei Bedarf aus. |
| 5422 | private | `ensure_member_upload_storage` | Stellt sicher, dass member upload storage vorhanden ist. |
| 5433 | private | `prepare_storage_directory` | Bereitet storage directory vor. |
| 5461 | private | `get_settings` | Liest oder ermittelt settings. |
| 5488 | public | `normalize_restored_settings` | Normalisiert restored settings. |
| 5514 | private | `set_member_save_error_notice` | Interne Funktion fuer member save error notice. |
| 5527 | private | `get_member_save_error_notice` | Liest oder ermittelt member save error notice. |
| 5545 | private | `sanitize_setting_color` | Bereinigt und validiert setting color. |
| 5560 | private | `get_member_title_class` | Liest oder ermittelt member title class. |
| 5566 | private | `get_member_title_style` | Liest oder ermittelt member title style. |
| 5580 | private | `sanitize_attachment_image_id` | Bereinigt und validiert attachment image id. |
| 5588 | private | `get_default_profile_image_id` | Liest oder ermittelt default profile image id. |
| 5596 | private | `get_default_profile_image_markup` | Liest oder ermittelt default profile image markup. |
| 5614 | private | `is_member_using_managed_default_image` | Prueft, ob member using managed default image zutrifft. |
| 5618 | private | `is_member_default_image_opted_out` | Prueft, ob member default image opted out zutrifft. |
| 5622 | private | `sync_default_profile_image_assignments` | Synchronisiert default profile image assignments. |
| 5637 | public | `sync_restored_member_default_thumbnail` | Synchronisiert restored member default thumbnail. |
| 5646 | private | `update_member_default_thumbnail_state` | Aktualisiert member default thumbnail state. |
| 5728 | private | `get_list_shortcode_tags` | Liest oder ermittelt list shortcode tags. |
| 5738 | private | `get_profile_shortcode_tags` | Liest oder ermittelt profile shortcode tags. |
| 5745 | private | `get_count_shortcode_tags` | Liest oder ermittelt count shortcode tags. |
| 5752 | private | `get_cards_shortcode_tags` | Liest oder ermittelt cards shortcode tags. |
| 5759 | private | `get_all_frontend_shortcode_tags` | Liest oder ermittelt all frontend shortcode tags. |
| 5776 | private | `get_allowed_category_ids` | Liest oder ermittelt allowed category ids. |
| 5783 | private | `get_allowed_category_ids_or_null` | Liest oder ermittelt allowed category ids or null. |
| 5789 | private | `get_category_choices` | Liest oder ermittelt category choices. |
| 5864 | private | `get_member_category_id` | Liest oder ermittelt member category id. |
| 5870 | private | `get_member_category_ids` | Liest oder ermittelt member category ids. |
| 5876 | private | `get_assigned_post_category_ids` | Liest oder ermittelt assigned post category ids. |
| 5880 | private | `get_preferred_member_category_id` | Liest oder ermittelt preferred member category id. |
| 5884 | private | `get_preferred_category_id_from_ids` | Liest oder ermittelt preferred category id from ids. |
| 5909 | private | `get_category_depth` | Liest oder ermittelt category depth. |
| 5913 | private | `build_member_category_assignment_ids` | Erzeugt member category assignment ids. |
| 5930 | private | `get_main_feuer_mannschaft_category_id` | Liest oder ermittelt main feuer mannschaft category id. |
| 5936 | private | `is_relevant_member_category` | Prueft, ob relevant member category zutrifft. |
| 5962 | private | `get_member_back_url` | Liest oder ermittelt member back url. |
| 5983 | private | `is_feuer_mannschaft_post` | Prueft, ob feuer mannschaft post zutrifft. |
| 5991 | private | `is_member_post` | Prueft, ob member post zutrifft. |
| 5995 | private | `get_invite_url` | Liest oder ermittelt invite url. |
| 5999 | private | `is_excluded_from_member_count` | Prueft, ob excluded from member count zutrifft. |
| 6003 | private | `sanitize_invite_url` | Bereinigt und validiert invite url. |
| 6018 | private | `is_member_request` | Prueft, ob member request zutrifft. |
| 6030 | private | `is_member_category_archive` | Prueft, ob member category archive zutrifft. |
| 6040 | private | `is_member_list_request` | Prueft, ob member list request zutrifft. |
| 6052 | private | `is_member_edit_context` | Prueft, ob member edit context zutrifft. |
| 6074 | private | `is_settings_screen` | Prueft, ob settings screen zutrifft. |
| 6078 | private | `is_feuer_mannschaft_admin_context` | Prueft, ob feuer mannschaft admin context zutrifft. |
| 6082 | private | `get_member_list_url` | Liest oder ermittelt member list url. |
| 6086 | private | `get_add_new_url` | Liest oder ermittelt add new url. |
| 6090 | private | `get_member_list_menu_slug` | Liest oder ermittelt member list menu slug. |
| 6094 | private | `get_add_new_menu_slug` | Liest oder ermittelt add new menu slug. |
| 6098 | private | `can_remove_member` | Interne Funktion fuer remove member. |
| 6108 | private | `can_remove_member_by_user` | Interne Funktion fuer remove member by user. |
| 6118 | public | `filter_admin_body_class` | Filtert oder passt admin body class an. |
| 6134 | public | `filter_frontend_body_class` | Filtert oder passt frontend body class an. |
| 6146 | private | `is_shortcode_profile_context` | Prueft, ob shortcode profile context zutrifft. |
| 6165 | private | `is_profile_shortcode_page` | Prueft, ob profile shortcode page zutrifft. |
| 6173 | public | `filter_parent_file` | Filtert oder passt parent file an. |
| 6181 | public | `filter_submenu_file` | Filtert oder passt submenu file an. |
| 6199 | public | `filter_revision_author_display_name` | Filtert oder passt revision author display name an. |
| 6209 | public | `filter_revision_author_name` | Filtert oder passt revision author name an. |
| 6217 | public | `filter_revision_author_data_for_js` | Filtert oder passt revision author data for js an. |
| 6246 | public | `filter_delete_caps` | Filtert oder passt delete caps an. |
| 6260 | private | `is_revision_author_context` | Prueft, ob revision author context zutrifft. |
| 6280 | private | `get_revision_context_post_id` | Liest oder ermittelt revision context post id. |
| 6300 | private | `get_preferred_revision_author_name` | Liest oder ermittelt preferred revision author name. |
| 6332 | public | `maybe_migrate_legacy_members` | Fuehrt migrate legacy members nur bei Bedarf aus. |
| 6402 | public | `maybe_sync_member_category_hierarchy` | Fuehrt sync member category hierarchy nur bei Bedarf aus. |
| 6440 | private | `get_migrated_member_post_id` | Liest oder ermittelt migrated member post id. |
| 6455 | private | `mb_char` | Interne Funktion fuer char. |
| 6463 | private | `clamp_int` | Interne Funktion fuer int. |

### `includes/class-feuer-mannschaft-updater.php`

Prueft remote auf neue Versionen und bindet Custom-Updates in WordPress ein.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 14 | public static | `instance` | Liefert die Singleton-Instanz der Klasse. |
| 22 | private | `__construct` | Initialisiert Abhaengigkeiten und registriert Hooks. |
| 33 | public | `filter_update_transient` | Filtert oder passt update transient an. |
| 61 | public | `filter_plugin_information` | Filtert oder passt plugin information an. |
| 97 | public | `disable_automatic_update` | Deaktiviert automatic update. |
| 105 | public | `purge_update_cache` | Leert oder bereinigt update cache. |
| 114 | public | `get_update_status` | Liest oder ermittelt update status. |
| 137 | public | `render_update_notice` | Rendert update notice. |
| 161 | private | `get_remote_metadata` | Liest oder ermittelt remote metadata. |
| 206 | private | `normalize_metadata` | Normalisiert metadata. |
| 239 | private | `build_update_payload` | Erzeugt update payload. |
| 253 | private | `is_own_update_item` | Prueft, ob own update item zutrifft. |
| 273 | private | `get_package_url` | Liest oder ermittelt package url. |

### `index.php`

Projektdatei des Plugins.

| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |
| --- | --- | --- | --- |
| 6 | global | `fm_index_read_text` | Interne Funktion fuer index read text. |
| 17 | global | `fm_index_escape` | Interne Funktion fuer index escape. |
| 22 | global | `fm_index_plugin_field` | Interne Funktion fuer index plugin field. |
| 33 | global | `fm_index_load_update_metadata` | Interne Funktion fuer index load update metadata. |
| 45 | global | `fm_index_collect_templates` | Interne Funktion fuer index collect templates. |
| 85 | global | `fm_index_parse_history` | Interne Funktion fuer index parse history. |
| 130 | global | `fm_index_char_count` | Interne Funktion fuer index char count. |
| 135 | global | `fm_index_collect_code_metrics` | Interne Funktion fuer index collect code metrics. |
| 188 | global | `fm_index_format_number` | Interne Funktion fuer index format number. |

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
| 31 | global | `feuer_mannschaft_delete_directory` | Interne Funktion fuer mannschaft delete directory. |
| 59 | global | `feuer_mannschaft_delete_option` | Interne Funktion fuer mannschaft delete option. |
| 67 | global | `feuer_mannschaft_delete_matching_options` | Interne Funktion fuer mannschaft delete matching options. |
| 97 | global | `feuer_mannschaft_get_member_post_ids` | Interne Funktion fuer mannschaft get member post ids. |
| 111 | global | `feuer_mannschaft_get_legacy_post_ids` | Interne Funktion fuer mannschaft get legacy post ids. |
| 124 | global | `feuer_mannschaft_get_gallery_attachment_ids` | Interne Funktion fuer mannschaft get gallery attachment ids. |
| 139 | global | `feuer_mannschaft_get_attached_attachment_ids` | Interne Funktion fuer mannschaft get attached attachment ids. |

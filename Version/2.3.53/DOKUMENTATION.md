# Feuer-Mannschaft Dokumentation

Automatisch erzeugt am 2026-04-30 17:37:16.

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
| 442 | private | `build_manifest` | Erzeugt manifest. |
| 456 | private | `export_members` | Exportiert members. |
| 502 | private | `export_member_categories` | Exportiert member categories. |
| 526 | private | `export_gallery` | Exportiert gallery. |
| 562 | private | `export_mentions` | Exportiert mentions. |
| 607 | private | `export_logs` | Exportiert logs. |
| 630 | private | `get_member_meta_export` | Liest oder ermittelt member meta export. |
| 643 | private | `restore_member_record` | Stellt member record wieder her. |
| 690 | private | `restore_member_gallery` | Stellt member gallery wieder her. |
| 802 | private | `restore_mention_record` | Stellt mention record wieder her. |
| 852 | private | `find_existing_member_id` | Interne Funktion fuer existing member id. |
| 905 | private | `resolve_member_category_ids` | Loest member category ids auf. |
| 943 | private | `reset_member_gallery` | Interne Funktion fuer member gallery. |
| 958 | private | `sync_member_default_thumbnail` | Synchronisiert member default thumbnail. |
| 970 | private | `verify_manage_options_request` | Interne Funktion fuer manage options request. |
| 978 | private | `redirect_with_notice` | Interne Funktion fuer with notice. |
| 988 | private | `get_backup_dir` | Liest oder ermittelt backup dir. |
| 998 | private | `get_archive_path` | Liest oder ermittelt archive path. |
| 1015 | private | `create_temp_directory` | Erstellt temp directory. |
| 1031 | private | `delete_directory` | Entfernt directory. |
| 1060 | private | `detect_mime_type` | Interne Funktion fuer mime type. |
| 1070 | private | `validate_archive_file` | Interne Funktion fuer archive file. |
| 1123 | private | `validate_manifest_contents` | Interne Funktion fuer manifest contents. |

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
| 453 | public | `render_categories_meta_box` | Rendert categories meta box. |
| 466 | public | `render_gallery_meta_box` | Rendert gallery meta box. |
| 487 | public | `render_submitbox_actions` | Rendert submitbox actions. |
| 520 | public | `handle_archive_request` | Verarbeitet archive request. |
| 524 | public | `handle_unarchive_request` | Verarbeitet unarchive request. |
| 528 | public | `render_admin_notice` | Rendert admin notice. |
| 560 | public | `save_member` | Speichert member. |
| 809 | public | `register_settings` | Registriert settings. |
| 817 | public | `sanitize_settings` | Bereinigt und validiert settings. |
| 895 | public | `are_cache_purges_enabled` | Interne Funktion fuer cache purges enabled. |
| 899 | public | `purge_plugin_cache` | Leert oder bereinigt plugin cache. |
| 964 | public | `are_automatic_updates_enabled` | Interne Funktion fuer automatic updates enabled. |
| 970 | public | `render_settings_page` | Rendert settings page. |
| 1028 | public | `enqueue_admin_assets` | Laedt Assets oder Ressourcen fuer admin assets. |
| 1073 | public | `enqueue_frontend_assets` | Laedt Assets oder Ressourcen fuer frontend assets. |
| 1119 | public | `tune_member_queries` | Optimiert member queries. |
| 1150 | public | `template_include` | Interne Funktion fuer include. |
| 1166 | public | `maybe_block_archived_member_access` | Fuehrt block archived member access nur bei Bedarf aus. |
| 1191 | public | `maybe_redirect_default_single_template_url` | Fuehrt redirect default single template url nur bei Bedarf aus. |
| 1210 | public | `shortcode_list` | Rendert den Shortcode fuer list. |
| 1234 | public | `shortcode_profile` | Rendert den Shortcode fuer profile. |
| 1273 | public | `shortcode_count` | Rendert den Shortcode fuer count. |
| 1295 | public | `shortcode_cards` | Rendert den Shortcode fuer cards. |
| 1337 | public | `render_archive_page` | Rendert archive page. |
| 1350 | public | `render_single_page` | Rendert single page. |
| 1354 | public | `filter_admin_columns` | Filtert oder passt admin columns an. |
| 1378 | public | `render_admin_column` | Rendert admin column. |
| 1410 | public | `handle_update_check_request` | Verarbeitet update check request. |
| 1452 | private | `get_status_action_url` | Liest oder ermittelt status action url. |
| 1468 | private | `handle_status_request` | Verarbeitet status request. |
| 1497 | public | `handle_delete_request` | Verarbeitet delete request. |
| 1545 | private | `update_member_status` | Aktualisiert member status. |
| 1591 | public | `get_archive_markup` | Liest oder ermittelt archive markup. |
| 1620 | private | `get_archive_description` | Liest oder ermittelt archive description. |
| 1633 | public | `get_single_markup` | Liest oder ermittelt single markup. |
| 1676 | private | `get_archive_card_markup` | Liest oder ermittelt archive card markup. |
| 1704 | private | `get_archive_row_markup` | Liest oder ermittelt archive row markup. |
| 1728 | private | `get_archive_grid_markup` | Liest oder ermittelt archive grid markup. |
| 1736 | private | `get_member_standard_card_markup` | Liest oder ermittelt member standard card markup. |
| 1764 | private | `get_member_team_card_markup` | Liest oder ermittelt member team card markup. |
| 1783 | private | `get_members_query` | Liest oder ermittelt members query. |
| 1816 | private | `get_members_query_by_ids` | Liest oder ermittelt members query by ids. |
| 1847 | private | `get_filter_categories` | Liest oder ermittelt filter categories. |
| 1859 | private | `get_current_url_without_filter` | Liest oder ermittelt current url without filter. |
| 1866 | private | `map_legacy_display_to_template` | Ordnet legacy display to template zu. |
| 1877 | private | `get_requested_single_template` | Liest oder ermittelt requested single template. |
| 1885 | private | `resolve_single_template_slug` | Loest single template slug auf. |
| 1896 | private | `get_member_permalink` | Liest oder ermittelt member permalink. |
| 1928 | private | `get_selected_members_markup` | Liest oder ermittelt selected members markup. |
| 1959 | private | `render_template_file` | Rendert template file. |
| 1975 | private | `render_admin_template` | Rendert admin template. |
| 1992 | private | `render_frontend_template` | Rendert frontend template. |
| 2009 | public | `render_mentions_block` | Rendert mentions block. |
| 2013 | private | `get_admin_template_file` | Liest oder ermittelt admin template file. |
| 2020 | private | `get_frontend_template_file` | Liest oder ermittelt frontend template file. |
| 2027 | private | `get_settings_tabs` | Liest oder ermittelt settings tabs. |
| 2037 | private | `get_settings_tab` | Liest oder ermittelt settings tab. |
| 2044 | private | `get_settings_notice` | Liest oder ermittelt settings notice. |
| 2069 | private | `get_public_post_type_choices` | Liest oder ermittelt public post type choices. |
| 2094 | public | `maybe_reset_member_field_values` | Fuehrt reset member field values nur bei Bedarf aus. |
| 2124 | public | `maybe_sync_default_profile_images` | Fuehrt sync default profile images nur bei Bedarf aus. |
| 2138 | public | `maybe_sync_member_comments` | Fuehrt sync member comments nur bei Bedarf aus. |
| 2185 | public | `maybe_seed_default_field_options` | Fuehrt seed default field options nur bei Bedarf aus. |
| 2235 | private | `get_legacy_default_field_option_values` | Liest oder ermittelt legacy default field option values. |
| 2264 | private | `get_legacy_field_option_groups` | Liest oder ermittelt legacy field option groups. |
| 2289 | private | `legacy_sanitize_field_settings` | Interne Funktion fuer sanitize field settings. |
| 2351 | private | `legacy_sanitize_field_replacement_input` | Interne Funktion fuer sanitize field replacement input. |
| 2380 | private | `legacy_collect_removed_field_conflicts` | Interne Funktion fuer collect removed field conflicts. |
| 2427 | private | `legacy_apply_field_option_replacements` | Interne Funktion fuer apply field option replacements. |
| 2518 | private | `legacy_get_member_field_values_by_group` | Interne Funktion fuer get member field values by group. |
| 2538 | private | `legacy_set_member_field_values_by_group` | Interne Funktion fuer set member field values by group. |
| 2560 | private | `get_default_field_option_values` | Liest oder ermittelt default field option values. |
| 2592 | private | `get_fixed_field_option_groups` | Liest oder ermittelt fixed field option groups. |
| 2627 | private | `get_contact_field_definitions` | Liest oder ermittelt contact field definitions. |
| 2669 | private | `get_contact_field_settings` | Liest oder ermittelt contact field settings. |
| 2691 | private | `sanitize_contact_field_settings` | Bereinigt und validiert contact field settings. |
| 2699 | private | `get_enabled_contact_field_definitions` | Liest oder ermittelt enabled contact field definitions. |
| 2715 | private | `get_field_option_groups` | Liest oder ermittelt field option groups. |
| 2746 | private | `get_requested_field_settings` | Liest oder ermittelt requested field settings. |
| 2773 | private | `get_field_group_option_labels` | Liest oder ermittelt field group option labels. |
| 2788 | private | `sanitize_field_settings` | Bereinigt und validiert field settings. |
| 2877 | private | `sanitize_field_replacement_input` | Bereinigt und validiert field replacement input. |
| 2906 | private | `collect_removed_custom_group_conflicts` | Interne Funktion fuer removed custom group conflicts. |
| 2946 | private | `collect_removed_field_conflicts` | Interne Funktion fuer removed field conflicts. |
| 2996 | private | `apply_field_option_replacements` | Wendet field option replacements an. |
| 3090 | private | `get_member_field_values_by_group` | Liest oder ermittelt member field values by group. |
| 3122 | private | `set_member_field_values_by_group` | Interne Funktion fuer member field values by group. |
| 3161 | private | `persist_member_choice_meta` | Schreibt member choice meta dauerhaft in den Speicher. |
| 3186 | private | `persist_member_custom_fields_meta` | Schreibt member custom fields meta dauerhaft in den Speicher. |
| 3198 | private | `persist_member_contact_fields_meta` | Schreibt member contact fields meta dauerhaft in den Speicher. |
| 3210 | private | `get_pending_field_replacements` | Liest oder ermittelt pending field replacements. |
| 3216 | private | `set_pending_field_replacements` | Interne Funktion fuer pending field replacements. |
| 3220 | private | `clear_pending_field_replacements` | Interne Funktion fuer pending field replacements. |
| 3224 | private | `get_pending_field_replacements_key` | Liest oder ermittelt pending field replacements key. |
| 3228 | private | `get_member_post_ids` | Liest oder ermittelt member post ids. |
| 3253 | private | `sanitize_setting_text_list` | Bereinigt und validiert setting text list. |
| 3274 | private | `sanitize_setting_option_entries` | Bereinigt und validiert setting option entries. |
| 3311 | private | `sanitize_custom_field_groups` | Bereinigt und validiert custom field groups. |
| 3353 | private | `build_custom_field_group_key` | Erzeugt custom field group key. |
| 3375 | private | `sanitize_setting_post_types` | Bereinigt und validiert setting post types. |
| 3395 | private | `get_member_setting_options` | Liest oder ermittelt member setting options. |
| 3414 | private | `sanitize_member_single_select_value` | Bereinigt und validiert member single select value. |
| 3430 | private | `sanitize_member_multi_select_values` | Bereinigt und validiert member multi select values. |
| 3459 | private | `sanitize_member_education_values` | Bereinigt und validiert member education values. |
| 3463 | private | `normalize_member_education_meta` | Normalisiert member education meta. |
| 3467 | private | `sanitize_member_custom_fields_value` | Bereinigt und validiert member custom fields value. |
| 3508 | private | `sanitize_member_contact_fields_value` | Bereinigt und validiert member contact fields value. |
| 3565 | private | `sanitize_contact_handle` | Bereinigt und validiert contact handle. |
| 3598 | private | `sanitize_whatsapp_contact_value` | Bereinigt und validiert whatsapp contact value. |
| 3625 | private | `sanitize_custom_contact_link_value` | Bereinigt und validiert custom contact link value. |
| 3644 | private | `build_member_log_changes` | Erzeugt member log changes. |
| 3769 | private | `build_settings_log_changes` | Erzeugt settings log changes. |
| 3834 | private | `format_field_option_entries_for_log` | Formatiert field option entries for log. |
| 3858 | private | `format_custom_field_groups_for_log` | Formatiert custom field groups for log. |
| 3883 | private | `format_contact_field_settings_for_log` | Formatiert contact field settings for log. |
| 3903 | private | `format_contact_field_value_for_log` | Formatiert contact field value for log. |
| 3939 | private | `stringify_log_value` | Interne Funktion fuer log value. |
| 3961 | private | `get_category_name_for_log` | Liest oder ermittelt category name for log. |
| 3973 | private | `get_post_type_label_for_log` | Liest oder ermittelt post type label for log. |
| 3987 | private | `get_attachment_name_for_log` | Liest oder ermittelt attachment name for log. |
| 4004 | private | `get_template_choices` | Liest oder ermittelt template choices. |
| 4045 | private | `get_template_file` | Liest oder ermittelt template file. |
| 4052 | private | `sanitize_template_slug` | Bereinigt und validiert template slug. |
| 4070 | private | `get_template_directory` | Liest oder ermittelt template directory. |
| 4082 | private | `read_template_headers` | Interne Funktion fuer template headers. |
| 4107 | private | `sanitize_member_meta` | Bereinigt und validiert member meta. |
| 4128 | private | `sanitize_member_description` | Bereinigt und validiert member description. |
| 4132 | private | `parse_member_name_from_title` | Interne Funktion fuer member name from title. |
| 4166 | private | `normalize_entry_year` | Normalisiert entry year. |
| 4192 | private | `resolve_shortcode_member_ids` | Loest shortcode member ids auf. |
| 4240 | private | `normalize_shortcode_category_ids` | Normalisiert shortcode category ids. |
| 4287 | private | `has_shortcode_category_filter` | Prueft, ob shortcode category filter vorhanden ist. |
| 4305 | private | `normalize_shortcode_count` | Normalisiert shortcode count. |
| 4315 | private | `validate_shortcode_member_id` | Interne Funktion fuer shortcode member id. |
| 4331 | private | `find_member_id_by_slug` | Interne Funktion fuer member id by slug. |
| 4345 | private | `find_member_id_by_name` | Interne Funktion fuer member id by name. |
| 4388 | private | `normalize_member_lookup_value` | Normalisiert member lookup value. |
| 4411 | private | `is_truthy_shortcode_attr` | Prueft, ob truthy shortcode attr zutrifft. |
| 4421 | private | `format_shortcode_count_text` | Formatiert shortcode count text. |
| 4430 | private | `sanitize_member_category` | Bereinigt und validiert member category. |
| 4446 | private | `sanitize_allowed_category_ids` | Bereinigt und validiert allowed category ids. |
| 4459 | private | `handle_gallery_uploads` | Verarbeitet gallery uploads. |
| 4529 | public | `filter_upload_dir` | Filtert oder passt upload dir an. |
| 4541 | private | `apply_watermark` | Wendet watermark an. |
| 4593 | private | `create_image_resource` | Erstellt image resource. |
| 4613 | private | `save_image_resource` | Speichert image resource. |
| 4633 | private | `prepare_watermark_text` | Bereitet watermark text vor. |
| 4651 | private | `get_member_meta` | Liest oder ermittelt member meta. |
| 4674 | public | `get_member_gallery` | Liest oder ermittelt member gallery. |
| 4690 | private | `is_cache_purge_enabled_in_settings` | Prueft, ob cache purge enabled in settings zutrifft. |
| 4694 | private | `normalize_cache_purge_ids` | Normalisiert cache purge ids. |
| 4698 | private | `normalize_cache_purge_urls` | Normalisiert cache purge urls. |
| 4716 | private | `get_shortcode_page_ids` | Liest oder ermittelt shortcode page ids. |
| 4764 | private | `get_shortcode_page_urls` | Liest oder ermittelt shortcode page urls. |
| 4777 | private | `get_member_cache_urls` | Liest oder ermittelt member cache urls. |
| 4805 | private | `purge_external_cache_plugins` | Leert oder bereinigt external cache plugins. |
| 4859 | private | `get_member_display_name` | Liest oder ermittelt member display name. |
| 4870 | private | `get_member_initials` | Liest oder ermittelt member initials. |
| 4888 | private | `get_member_visual_markup` | Liest oder ermittelt member visual markup. |
| 4909 | private | `get_member_contact_buttons_markup` | Liest oder ermittelt member contact buttons markup. |
| 4924 | private | `get_member_contact_buttons` | Liest oder ermittelt member contact buttons. |
| 4945 | private | `build_member_contact_button` | Erzeugt member contact button. |
| 4973 | private | `build_contact_button_url` | Erzeugt contact button url. |
| 5016 | private | `get_single_detail_rows` | Liest oder ermittelt single detail rows. |
| 5066 | private | `get_detail_value_markup` | Liest oder ermittelt detail value markup. |
| 5158 | private | `format_entry_date` | Formatiert entry date. |
| 5162 | private | `get_action_link_markup` | Liest oder ermittelt action link markup. |
| 5174 | private | `get_adjacent_members` | Liest oder ermittelt adjacent members. |
| 5191 | private | `get_related_member_ids` | Liest oder ermittelt related member ids. |
| 5220 | private | `get_ordered_member_ids` | Liest oder ermittelt ordered member ids. |
| 5224 | private | `get_active_profile_ids` | Liest oder ermittelt active profile ids. |
| 5272 | private | `get_countable_member_ids` | Liest oder ermittelt countable member ids. |
| 5285 | private | `should_load_frontend_assets` | Interne Funktion fuer load frontend assets. |
| 5313 | private | `get_default_settings` | Liest oder ermittelt default settings. |
| 5343 | private | `maybe_create_default_settings_option` | Fuehrt create default settings option nur bei Bedarf aus. |
| 5349 | private | `ensure_member_upload_storage` | Stellt sicher, dass member upload storage vorhanden ist. |
| 5360 | private | `prepare_storage_directory` | Bereitet storage directory vor. |
| 5388 | private | `get_settings` | Liest oder ermittelt settings. |
| 5415 | private | `sanitize_setting_color` | Bereinigt und validiert setting color. |
| 5430 | private | `get_member_title_class` | Liest oder ermittelt member title class. |
| 5436 | private | `get_member_title_style` | Liest oder ermittelt member title style. |
| 5450 | private | `sanitize_attachment_image_id` | Bereinigt und validiert attachment image id. |
| 5458 | private | `get_default_profile_image_id` | Liest oder ermittelt default profile image id. |
| 5466 | private | `get_default_profile_image_markup` | Liest oder ermittelt default profile image markup. |
| 5484 | private | `is_member_using_managed_default_image` | Prueft, ob member using managed default image zutrifft. |
| 5488 | private | `is_member_default_image_opted_out` | Prueft, ob member default image opted out zutrifft. |
| 5492 | private | `sync_default_profile_image_assignments` | Synchronisiert default profile image assignments. |
| 5507 | public | `sync_restored_member_default_thumbnail` | Synchronisiert restored member default thumbnail. |
| 5516 | private | `update_member_default_thumbnail_state` | Aktualisiert member default thumbnail state. |
| 5598 | private | `get_list_shortcode_tags` | Liest oder ermittelt list shortcode tags. |
| 5608 | private | `get_profile_shortcode_tags` | Liest oder ermittelt profile shortcode tags. |
| 5615 | private | `get_count_shortcode_tags` | Liest oder ermittelt count shortcode tags. |
| 5622 | private | `get_cards_shortcode_tags` | Liest oder ermittelt cards shortcode tags. |
| 5629 | private | `get_all_frontend_shortcode_tags` | Liest oder ermittelt all frontend shortcode tags. |
| 5646 | private | `get_allowed_category_ids` | Liest oder ermittelt allowed category ids. |
| 5653 | private | `get_allowed_category_ids_or_null` | Liest oder ermittelt allowed category ids or null. |
| 5659 | private | `get_category_choices` | Liest oder ermittelt category choices. |
| 5734 | private | `get_member_category_id` | Liest oder ermittelt member category id. |
| 5740 | private | `get_member_category_ids` | Liest oder ermittelt member category ids. |
| 5746 | private | `get_assigned_post_category_ids` | Liest oder ermittelt assigned post category ids. |
| 5750 | private | `get_preferred_member_category_id` | Liest oder ermittelt preferred member category id. |
| 5754 | private | `get_preferred_category_id_from_ids` | Liest oder ermittelt preferred category id from ids. |
| 5779 | private | `get_category_depth` | Liest oder ermittelt category depth. |
| 5783 | private | `build_member_category_assignment_ids` | Erzeugt member category assignment ids. |
| 5800 | private | `get_main_feuer_mannschaft_category_id` | Liest oder ermittelt main feuer mannschaft category id. |
| 5806 | private | `is_relevant_member_category` | Prueft, ob relevant member category zutrifft. |
| 5832 | private | `get_member_back_url` | Liest oder ermittelt member back url. |
| 5853 | private | `is_feuer_mannschaft_post` | Prueft, ob feuer mannschaft post zutrifft. |
| 5861 | private | `is_member_post` | Prueft, ob member post zutrifft. |
| 5865 | private | `get_invite_url` | Liest oder ermittelt invite url. |
| 5869 | private | `is_excluded_from_member_count` | Prueft, ob excluded from member count zutrifft. |
| 5873 | private | `sanitize_invite_url` | Bereinigt und validiert invite url. |
| 5888 | private | `is_member_request` | Prueft, ob member request zutrifft. |
| 5900 | private | `is_member_category_archive` | Prueft, ob member category archive zutrifft. |
| 5910 | private | `is_member_list_request` | Prueft, ob member list request zutrifft. |
| 5922 | private | `is_member_edit_context` | Prueft, ob member edit context zutrifft. |
| 5944 | private | `is_settings_screen` | Prueft, ob settings screen zutrifft. |
| 5948 | private | `is_feuer_mannschaft_admin_context` | Prueft, ob feuer mannschaft admin context zutrifft. |
| 5952 | private | `get_member_list_url` | Liest oder ermittelt member list url. |
| 5956 | private | `get_add_new_url` | Liest oder ermittelt add new url. |
| 5960 | private | `get_member_list_menu_slug` | Liest oder ermittelt member list menu slug. |
| 5964 | private | `get_add_new_menu_slug` | Liest oder ermittelt add new menu slug. |
| 5968 | private | `can_remove_member` | Interne Funktion fuer remove member. |
| 5978 | private | `can_remove_member_by_user` | Interne Funktion fuer remove member by user. |
| 5988 | public | `filter_admin_body_class` | Filtert oder passt admin body class an. |
| 6004 | public | `filter_frontend_body_class` | Filtert oder passt frontend body class an. |
| 6016 | private | `is_shortcode_profile_context` | Prueft, ob shortcode profile context zutrifft. |
| 6035 | private | `is_profile_shortcode_page` | Prueft, ob profile shortcode page zutrifft. |
| 6043 | public | `filter_parent_file` | Filtert oder passt parent file an. |
| 6051 | public | `filter_submenu_file` | Filtert oder passt submenu file an. |
| 6069 | public | `filter_revision_author_display_name` | Filtert oder passt revision author display name an. |
| 6079 | public | `filter_revision_author_name` | Filtert oder passt revision author name an. |
| 6087 | public | `filter_revision_author_data_for_js` | Filtert oder passt revision author data for js an. |
| 6116 | public | `filter_delete_caps` | Filtert oder passt delete caps an. |
| 6130 | private | `is_revision_author_context` | Prueft, ob revision author context zutrifft. |
| 6150 | private | `get_revision_context_post_id` | Liest oder ermittelt revision context post id. |
| 6170 | private | `get_preferred_revision_author_name` | Liest oder ermittelt preferred revision author name. |
| 6202 | public | `maybe_migrate_legacy_members` | Fuehrt migrate legacy members nur bei Bedarf aus. |
| 6272 | public | `maybe_sync_member_category_hierarchy` | Fuehrt sync member category hierarchy nur bei Bedarf aus. |
| 6310 | private | `get_migrated_member_post_id` | Liest oder ermittelt migrated member post id. |
| 6325 | private | `mb_char` | Interne Funktion fuer char. |
| 6333 | private | `clamp_int` | Interne Funktion fuer int. |

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

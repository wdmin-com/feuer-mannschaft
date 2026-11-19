<?php

if (! defined('ABSPATH')) {
    exit;
}
class Feuer_Mannschaft_Plugin {
    const OPTION_KEY       = 'feuer_mannschaft_settings';
    const POST_TYPE        = 'feuer_mannschaft';
    const LEGACY_POST_TYPE = 'feuer_mannschaft_member';
    const LEGACY_POST_TYPE_TRUNCATED = 'feuer_mannschaft_mem';
    const LEGACY_POST_TYPE_REPAIR_OPTION = 'feuer_mannschaft_legacy_cpt_repaired_2_4_1';
    const ARCHIVED_STATUS  = 'feuer_archived';
    const ARCHIVED_STATUS_MIGRATION_OPTION = 'feuer_mannschaft_archived_status_migrated';
    const PROFILE_POST_MIGRATION_OPTION = 'feuer_mannschaft_profiles_to_cpt_migrated';
    const ARCHIVE_TEMPLATE_DEFAULT_MIGRATION_OPTION = 'feuer_mannschaft_archive_template_default_migrated_2_4_8';
    const OLD_PERMALINK_META = '_feuer_mannschaft_old_permalink';
    const PROFILE_META     = '_feuer_mannschaft_profile';
    const GALLERY_META     = '_feuer_mannschaft_gallery';
    const SERVICE_HISTORY_META = '_feuer_mannschaft_service_history';
    const ARCHIVE_REASON_META = '_feuer_mannschaft_archive_reason';
    const SERVICE_END_DATE_META = '_feuer_mannschaft_service_end_date';
    const META_PREFIX      = '_feuer_mannschaft_';
    const MEMBER_UPLOAD_SUBDIR = 'feuer-mannschaft';
    const BACKUP_UPLOAD_SUBDIR = 'feuer-mannschaft-backups';
    const BACKUP_TEMP_PREFIX = 'feuer-mannschaft-backups-tmp-';
    const MIGRATION_OPTION = 'feuer_mannschaft_legacy_cpt_migrated';
    const CATEGORY_SYNC_OPTION = 'feuer_mannschaft_category_hierarchy_synced';
    const FIELD_RESET_OPTION = 'feuer_mannschaft_member_field_values_reset_2_1_1';
    const FIELD_PENDING_REPLACEMENTS_TRANSIENT = 'feuer_mannschaft_field_pending_replacements_';
    const DEFAULT_IMAGE_MANAGED_META = '_feuer_mannschaft_default_image_managed';
    const DEFAULT_IMAGE_OPT_OUT_META = '_feuer_mannschaft_default_image_opt_out';
    const DEFAULT_IMAGE_SYNC_OPTION = 'feuer_mannschaft_default_images_synced_2_1_15';
    const COMMENTS_SYNC_OPTION = 'feuer_mannschaft_comments_closed_2_1_16';
    const ENTRY_TYPE_META   = '_feuer_mannschaft_entry_type';
    const INVITE_URL_META   = '_feuer_mannschaft_invite_url';
    const EXCLUDE_FROM_COUNT_META = '_feuer_mannschaft_exclude_from_count';

    private static $instance = null;
    private $syncing_title          = false;
    private $template_choices_cache = array();
    private $shortcode_page_ids_cache = null;
    private $cache_purge_running    = false;
    private $logger                 = null;
    private $mentions               = null;
    private $backup_manager         = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function activate() {
        $plugin = self::instance();
        $plugin->logger->maybe_install();
        $plugin->maybe_create_default_settings_option();
        $plugin->ensure_required_storage();
        $plugin->migrate_archived_status();
        $plugin->register_post_status();
        $plugin->register_member_taxonomies();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('feuer_mannschaft_prune_logs');
        wp_clear_scheduled_hook('feuer_mannschaft_daily_audit');
        wp_clear_scheduled_hook('feuer_mannschaft_weekly_report');
        flush_rewrite_rules();
    }

    public function ensure_required_storage() {
        $this->ensure_member_upload_storage();
        $this->backup_manager->ensure_storage(true);
    }

    private function __construct() {
        $this->logger         = new Feuer_Mannschaft_Logger();
        $this->mentions       = new Feuer_Mannschaft_Mentions($this->logger);
        $this->backup_manager = new Feuer_Mannschaft_Backup_Manager($this->logger);

        $this->mentions->register_hooks();
        $this->backup_manager->register_hooks();

        add_action('init', array($this, 'migrate_archived_status'), 4);
        add_action('init', array($this, 'register_post_status'), 5);
        add_action('init', array($this, 'register_member_taxonomies'), 6);
        add_action('init', array($this, 'maybe_set_default_archive_template'), 7);
        add_action('init', array($this, 'migrate_profiles_to_post_type'), 20);
        add_action('admin_menu', array($this, 'register_admin_menu'), 20);
        add_action('admin_init', array($this->logger, 'maybe_install'));
        add_action('admin_init', array($this, 'maybe_migrate_legacy_members'), 20);
        add_action('admin_init', array($this, 'maybe_sync_member_category_hierarchy'), 25);
        add_action('admin_init', array($this, 'maybe_reset_member_field_values'), 30);
        add_action('admin_init', array($this, 'maybe_sync_default_profile_images'), 35);
        add_action('admin_init', array($this, 'maybe_sync_member_comments'), 40);
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_action('post_edit_form_tag', array($this, 'add_post_form_enctype'));
        add_action('save_post_' . self::POST_TYPE, array($this, 'save_member'), 10, 3);
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_action_feuer_mannschaft_archive', array($this, 'handle_archive_request'));
        add_action('admin_action_feuer_mannschaft_unarchive', array($this, 'handle_unarchive_request'));
        add_action('admin_action_feuer_mannschaft_delete', array($this, 'handle_delete_request'));
        add_action('admin_action_feuer_mannschaft_delete_permanently', array($this, 'handle_permanent_delete_request'));
        add_action('admin_post_feuer_mannschaft_check_updates', array($this, 'handle_update_check_request'));
        add_action('admin_post_feuer_mannschaft_cleanup_orphan_media', array($this, 'handle_cleanup_orphan_media_request'));
        add_action('admin_post_feuer_mannschaft_save_log_retention', array($this, 'handle_save_log_retention'));
        add_action('admin_post_feuer_mannschaft_export_logs', array($this, 'handle_export_logs_request'));
        add_action('init', array($this, 'schedule_log_pruning'), 30);
        add_action('feuer_mannschaft_prune_logs', array($this, 'prune_logs'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('post_submitbox_misc_actions', array($this, 'render_submitbox_actions'));
        add_action('admin_notices', array($this, 'render_admin_notice'));
        add_action('pre_get_posts', array($this, 'tune_member_queries'));
        add_action('template_redirect', array($this, 'maybe_redirect_default_single_template_url'), 5);
        add_action('template_redirect', array($this, 'maybe_redirect_legacy_profile_url'), 1);
        add_action('template_redirect', array($this, 'maybe_block_archived_member_access'));
        add_filter('template_include', array($this, 'template_include'));
        add_filter('get_the_author_display_name', array($this, 'filter_revision_author_display_name'), 10, 3);
        add_filter('the_author', array($this, 'filter_revision_author_name'), 10, 2);
        add_filter('wp_prepare_revision_for_js', array($this, 'filter_revision_author_data_for_js'), 10, 3);
        add_filter('body_class', array($this, 'filter_frontend_body_class'));
        add_filter('admin_body_class', array($this, 'filter_admin_body_class'));
        add_filter('parent_file', array($this, 'filter_parent_file'));
        add_filter('submenu_file', array($this, 'filter_submenu_file'));
        add_filter('map_meta_cap', array($this, 'filter_delete_caps'), 10, 4);
        add_filter('use_block_editor_for_post_type', array($this, 'disable_block_editor'), 10, 2);
        add_filter('use_block_editor_for_post', array($this, 'disable_block_editor_for_post'), 10, 2);
        add_filter('enter_title_here', array($this, 'filter_title_placeholder'), 10, 2);
        add_filter('display_post_states', array($this, 'display_post_states'), 10, 2);
        add_filter('post_row_actions', array($this, 'filter_row_actions'), 10, 2);
        add_filter('bulk_actions-edit-' . self::POST_TYPE, array($this, 'filter_bulk_actions'));
        add_filter('handle_bulk_actions-edit-' . self::POST_TYPE, array($this, 'handle_bulk_actions'), 10, 3);
        add_filter('manage_edit-' . self::POST_TYPE . '_columns', array($this, 'filter_admin_columns'));
        add_action('manage_posts_custom_column', array($this, 'render_admin_column'), 10, 2);
        foreach ($this->get_list_shortcode_tags() as $shortcode_tag) {
            add_shortcode($shortcode_tag, array($this, 'shortcode_list'));
        }

        foreach ($this->get_profile_shortcode_tags() as $shortcode_tag) {
            add_shortcode($shortcode_tag, array($this, 'shortcode_profile'));
        }

        add_shortcode('feuer_org_chart', array($this, 'shortcode_org_chart'));
        add_shortcode('feuer_mannschaft_org_chart', array($this, 'shortcode_org_chart'));
        add_shortcode('feuer_service_history', array($this, 'shortcode_service_history'));
        add_shortcode('feuer_mannschaft_dienstverlauf', array($this, 'shortcode_service_history'));
        add_shortcode('feuer_archived_members', array($this, 'shortcode_archived_members'));
        add_shortcode('feuer_mannschaft_ehemalige', array($this, 'shortcode_archived_members'));
        add_shortcode('feuer_member', array($this, 'shortcode_profile'));
        add_shortcode('feuer_members', array($this, 'shortcode_members'));

        foreach ($this->get_count_shortcode_tags() as $shortcode_tag) {
            add_shortcode($shortcode_tag, array($this, 'shortcode_count'));
        }

        foreach ($this->get_cards_shortcode_tags() as $shortcode_tag) {
            add_shortcode($shortcode_tag, array($this, 'shortcode_cards'));
        }
    }

    public function register_admin_menu() {
        $list_slug    = $this->get_member_list_menu_slug();
        $add_new_slug = $this->get_add_new_menu_slug();

        $top_level_hook = add_menu_page(
            'Mannschaft',
            'Mannschaft',
            'edit_posts',
            'feuer-mannschaft',
            array($this, 'render_menu_placeholder'),
            'dashicons-groups',
            24
        );

        if ($top_level_hook) {
            add_action('load-' . $top_level_hook, array($this, 'redirect_to_member_list'));
        }

        add_submenu_page(
            'feuer-mannschaft',
            'Alle Mannschaft-Profile',
            'Alle Profile',
            'edit_posts',
            $list_slug
        );

        add_submenu_page(
            'feuer-mannschaft',
            'Neues Mannschaft-Profil',
            'Neu hinzufügen',
            'edit_posts',
            $add_new_slug
        );

        add_submenu_page(
            'feuer-mannschaft',
            'Mannschaft-Einstellungen',
            'Einstellungen',
            'manage_options',
            'feuer-mannschaft-settings',
            array($this, 'render_settings_page')
        );

        remove_submenu_page('feuer-mannschaft', 'feuer-mannschaft');
    }
    public function render_menu_placeholder() {
    }

    public function redirect_to_member_list() {
        wp_safe_redirect($this->get_member_list_url());
        exit;
    }

    public function register_post_status() {
        register_post_status(
            self::ARCHIVED_STATUS,
            array(
                'label'                     => 'Archiviert',
                'public'                    => false,
                'internal'                  => false,
                'protected'                 => true,
                'private'                   => false,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'Archiviert <span class="count">(%s)</span>',
                    'Archiviert <span class="count">(%s)</span>',
                    'feuer-mannschaft'
                ),
            )
        );
        $this->register_member_post_type();
    }

    public function migrate_archived_status() {
        if ('2.4.1' === get_option(self::ARCHIVED_STATUS_MIGRATION_OPTION)) {
            return;
        }

        global $wpdb;
        $legacy_statuses = array('feuer_mannschaft_arc', 'feuer_mannschaft_archived');
        $post_ids = $wpdb->get_col(
            "SELECT ID FROM {$wpdb->posts} WHERE post_status IN ('feuer_mannschaft_arc','feuer_mannschaft_archived')"
        );

        foreach ($legacy_statuses as $legacy_status) {
            $wpdb->update(
                $wpdb->posts,
                array('post_status' => self::ARCHIVED_STATUS),
                array('post_status' => $legacy_status),
                array('%s'),
                array('%s')
            );
        }

        foreach (array_map('absint', (array) $post_ids) as $post_id) {
            clean_post_cache($post_id);
        }

        update_option(self::ARCHIVED_STATUS_MIGRATION_OPTION, '2.4.1', false);
    }

    public function register_member_taxonomies() {
        $taxonomies = array(
            'feuer_department' => array(
                'singular'     => 'Abteilung',
                'plural'       => 'Abteilungen',
                'hierarchical' => true,
                'slug'         => 'mannschaft-abteilung',
            ),
            'feuer_group' => array(
                'singular'     => 'Gruppe',
                'plural'       => 'Gruppen',
                'hierarchical' => true,
                'slug'         => 'mannschaft-gruppe',
            ),
            'feuer_specialty' => array(
                'singular'     => 'Spezialisierung',
                'plural'       => 'Spezialisierungen',
                'hierarchical' => false,
                'slug'         => 'mannschaft-spezialisierung',
            ),
        );

        foreach ($taxonomies as $taxonomy => $config) {
            register_taxonomy($taxonomy, array(self::POST_TYPE), array(
                'labels' => array(
                    'name'          => $config['plural'],
                    'singular_name' => $config['singular'],
                    'search_items'  => $config['plural'] . ' durchsuchen',
                    'all_items'     => 'Alle ' . $config['plural'],
                    'edit_item'     => $config['singular'] . ' bearbeiten',
                    'add_new_item'  => $config['singular'] . ' hinzufügen',
                ),
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => false,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'hierarchical'      => $config['hierarchical'],
                'rewrite'           => array('slug' => $config['slug'], 'with_front' => false),
            ));
        }
    }

    public function disable_block_editor($use_block_editor, $post_type) {
        if (self::POST_TYPE === $post_type && $this->is_member_request()) {
            return false;
        }

        return $use_block_editor;
    }

    public function disable_block_editor_for_post($use_block_editor, $post) {
        if ($post instanceof WP_Post && $this->is_feuer_mannschaft_post($post)) {
            return false;
        }

        return $use_block_editor;
    }

    public function filter_title_placeholder($placeholder, $post) {
        if ($post instanceof WP_Post && self::POST_TYPE === $post->post_type && ($this->is_feuer_mannschaft_post($post) || $this->is_member_request())) {
            return 'Der interne Titel wird automatisch aus Vorname und Nachname gebildet';
        }

        return $placeholder;
    }

    public function display_post_states($states, $post) {
        if (! $post instanceof WP_Post || ! $this->is_feuer_mannschaft_post($post)) {
            return $states;
        }

        $states['feuer_mannschaft'] = 'Mannschaft';

        if ('feuer_archived' === $post->post_status) {
            $states['feuer_archived'] = 'Archiviert';
        }

        return $states;
    }

    public function filter_row_actions($actions, $post) {
        if (! $post instanceof WP_Post || ! $this->is_feuer_mannschaft_post($post)) {
            return $actions;
        }

        unset($actions['delete'], $actions['trash']);

        if (! current_user_can('edit_post', $post->ID)) {
            return $actions;
        }

        if ('feuer_archived' === $post->post_status) {
            $actions['feuer_mannschaft_unarchive'] = $this->get_action_link_markup(
                $this->get_status_action_url('unarchive', $post->ID),
                'Aktivieren'
            );
        } elseif ('trash' !== $post->post_status) {
            $actions['feuer_mannschaft_archive'] = $this->get_action_link_markup(
                $this->get_status_action_url('archive', $post->ID),
                'Archivieren'
            );
        }

        if ($this->can_remove_member()) {
            $is_trashed = 'trash' === $post->post_status;
            $actions['feuer_mannschaft_delete'] = $this->get_action_link_markup(
                $this->get_status_action_url($is_trashed ? 'delete_permanently' : 'delete', $post->ID),
                $is_trashed ? __('Endgültig löschen', 'feuer-mannschaft') : __('In Papierkorb', 'feuer-mannschaft'),
                'submitdelete',
                $is_trashed
                    ? __('Dieses Profil unwiderruflich löschen? Diese Aktion kann nicht rückgängig gemacht werden.', 'feuer-mannschaft')
                    : __('Profil in den Papierkorb verschieben? Es kann innerhalb der WordPress-Aufbewahrungsfrist wiederhergestellt werden.', 'feuer-mannschaft')
            );
        }

        return $actions;
    }

    public function filter_bulk_actions($actions) {
        if (! $this->is_member_list_request()) {
            return $actions;
        }

        unset($actions['delete']);

        $actions['feuer_mannschaft_archive']   = 'Archivieren';
        $actions['feuer_mannschaft_unarchive'] = 'Aktivieren';

        return $actions;
    }

    public function handle_bulk_actions($redirect_to, $doaction, $post_ids) {
        if (! in_array($doaction, array('feuer_mannschaft_archive', 'feuer_mannschaft_unarchive'), true)) {
            return $redirect_to;
        }

        $target_status = 'feuer_mannschaft_archive' === $doaction ? 'feuer_archived' : 'publish';
        $count         = 0;

        foreach ((array) $post_ids as $post_id) {
            $post_id = absint($post_id);
            if (! $post_id || ! $this->is_feuer_mannschaft_post($post_id) || ! current_user_can('edit_post', $post_id)) {
                continue;
            }

            if ($this->update_member_status($post_id, $target_status)) {
                $count++;
            }
        }

        return add_query_arg(
            array(
                'feuer_mannschaft_profile' => 1,
                'feuer_mannschaft_notice' => 'feuer_mannschaft_archive' === $doaction ? 'archived' : 'unarchived',
                'feuer_mannschaft_count'  => $count,
            ),
            $redirect_to
        );
    }

    public function register_meta_boxes() {
        if (! $this->is_member_edit_context()) {
            return;
        }

        remove_meta_box('categorydiv', self::POST_TYPE, 'side');
        remove_meta_box('tagsdiv-post_tag', self::POST_TYPE, 'side');
        remove_meta_box('postexcerpt', self::POST_TYPE, 'normal');
        remove_meta_box('postdivrich', self::POST_TYPE, 'normal');
        remove_meta_box('postimagediv', self::POST_TYPE, 'side');
        remove_meta_box('commentstatusdiv', self::POST_TYPE, 'normal');
        remove_meta_box('commentsdiv', self::POST_TYPE, 'normal');
        remove_meta_box('trackbacksdiv', self::POST_TYPE, 'normal');

        add_meta_box(
            'feuer_mannschaft_details',
            'Mannschaft-Profil',
            array($this, 'render_details_meta_box'),
            self::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'feuer_mannschaft_gallery',
            'Fotos',
            array($this, 'render_gallery_meta_box'),
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'feuer_mannschaft_service_history',
            'Dienstverlauf',
            array($this, 'render_service_history_meta_box'),
            self::POST_TYPE,
            'normal',
            'default'
        );
    }

    public function render_service_history_meta_box($post) {
        $events = $this->get_service_history($post->ID);
        wp_nonce_field('feuer_mannschaft_save_service_history', 'feuer_mannschaft_service_history_nonce');
        echo $this->render_admin_template('service-history', array(
            'events'      => $events,
            'event_types' => $this->get_service_history_types(),
        )); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function add_post_form_enctype() {
        if ($this->is_member_edit_context()) {
            echo ' enctype="multipart/form-data"';
        }
    }

    public function render_details_meta_box($post) {
        $meta                    = $this->get_member_meta($post->ID);
        $custom_link             = $this->get_invite_url($post->ID);
        $exclude_from_count      = $this->is_excluded_from_member_count($post->ID);
        $choices                 = $this->get_category_choices($this->get_allowed_category_ids_or_null());
        $category_id             = $this->get_member_category_id($post->ID);
        $entry_year              = $this->normalize_entry_year($meta['entry_date']);
        $current_year            = (int) wp_date('Y') + 1;
        $settings_url            = admin_url('admin.php?page=feuer-mannschaft-settings');
        $field_groups            = $this->get_field_option_groups();
        $enabled_contact_fields  = $this->get_enabled_contact_field_definitions();
        $choice_groups           = array();

        if (! $category_id && ! empty($choices)) {
            $category_id = (int) $choices[0]['term']->term_id;
        }

        foreach ($field_groups as $group_id => $group) {
            $selected_values = $this->get_member_field_values_by_group($meta, $group);
            $known_values    = $this->get_field_group_option_labels($group);
            $legacy_values   = array_values(array_diff($selected_values, $known_values));
            $display_options = isset($group['options']) && is_array($group['options']) ? $group['options'] : array();
            $seen_labels     = array();

            foreach ($legacy_values as $legacy_value) {
                array_unshift(
                    $display_options,
                    array(
                        'label'  => $legacy_value,
                        'url'    => '',
                        'legacy' => true,
                    )
                );
            }

            $display_options = array_values(
                array_filter(
                    array_map(
                        function ($option) use (&$seen_labels) {
                            if (! is_array($option)) {
                                return null;
                            }

                            $label = isset($option['label']) ? trim((string) $option['label']) : '';
                            if ('' === $label || isset($seen_labels[$label])) {
                                return null;
                            }

                            $seen_labels[$label] = true;
                            $option['label']     = $label;
                            $option['url']       = isset($option['url']) ? $this->sanitize_invite_url($option['url']) : '';
                            $option['legacy']    = ! empty($option['legacy']);

                            return $option;
                        },
                        $display_options
                    )
                )
            );

            $choice_groups[$group_id] = array_merge(
                $group,
                array(
                    'selected_values' => $selected_values,
                    'display_options' => $display_options,
                    'field_name'      => 'custom' === $group['source']
                        ? 'feuer_mannschaft_meta[custom_fields][' . $group['custom_key'] . '][]'
                        : 'feuer_mannschaft_meta[' . $group['meta_key'] . '][]',
                    'switch_group'    => ! empty($group['multiple'])
                        ? ''
                        : 'feuer-mannschaft-' . ('custom' === $group['source'] ? $group['custom_key'] : $group['meta_key']),
                )
            );
        }

        $preview_category = $category_id ? get_category($category_id) : null;
        $profile_preview  = array(
            'name'              => trim($meta['first_name'] . ' ' . $meta['last_name']),
            'rank'              => $meta['rank'],
            'function'          => $meta['function'],
            'category'          => $preview_category instanceof WP_Term ? $preview_category->name : '',
            'has_gallery'       => ! empty($this->get_member_gallery($post->ID)),
            'has_default_image' => (bool) $this->get_default_profile_image_id(),
            'is_archived'       => 'feuer_archived' === get_post_status($post),
            'is_excluded'       => $exclude_from_count,
            'completion'        => array(
                'name'     => '' !== trim($meta['first_name'] . ' ' . $meta['last_name']),
                'category' => $category_id > 0,
                'photo'    => ! empty($this->get_member_gallery($post->ID)) || (bool) $this->get_default_profile_image_id(),
            ),
        );

        wp_nonce_field('feuer_mannschaft_save_member', 'feuer_mannschaft_member_nonce');

        echo $this->render_admin_template(
            'member-details',
            array(
                'meta'                    => $meta,
                'profile_slug'            => (string) $post->post_name,
                'show_admin_note'         => $this->current_user_can_manage_member_admin_note(),
                'custom_link'             => $custom_link,
                'exclude_from_count'      => $exclude_from_count,
                'choices'                 => $choices,
                'category_id'             => $category_id,
                'entry_year'              => $entry_year,
                'current_year'            => $current_year,
                'settings_url'            => $settings_url,
                'choice_groups'           => $choice_groups,
                'enabled_contact_fields'  => $enabled_contact_fields,
                'contact_field_values'    => isset($meta['contact_fields']) ? $meta['contact_fields'] : array(),
                'profile_preview'         => $profile_preview,
                'archive_reason'          => get_post_meta($post->ID, self::ARCHIVE_REASON_META, true),
                'service_end_date'        => get_post_meta($post->ID, self::SERVICE_END_DATE_META, true),
            )
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function render_categories_meta_box($post) {
        $selected = wp_get_post_categories($post->ID, array('fields' => 'ids'));
        $choices  = $this->get_category_choices($this->get_allowed_category_ids_or_null());

        echo $this->render_admin_template(
            'member-categories',
            array(
                'selected' => $selected,
                'choices'  => $choices,
            )
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function render_gallery_meta_box($post) {
        $gallery                = $this->get_member_gallery($post->ID);
        $settings               = $this->get_settings();
        $name                   = $this->get_member_display_name($post->ID);
        $default_image_id       = $this->get_default_profile_image_id();
        $default_image_active   = $this->is_member_using_managed_default_image($post->ID);
        $default_image_opted_out = $this->is_member_default_image_opted_out($post->ID);

        echo $this->render_admin_template(
            'member-gallery',
            array(
                'gallery'                => $gallery,
                'settings'               => $settings,
                'name'                   => $name,
                'default_image_id'       => $default_image_id,
                'default_image_active'   => $default_image_active,
                'default_image_opted_out'=> $default_image_opted_out,
            )
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function render_submitbox_actions() {
        global $post;

        if (! $post instanceof WP_Post || ! $this->is_feuer_mannschaft_post($post)) {
            return;
        }

        if (empty($post->ID) || 'auto-draft' === $post->post_status) {
            return;
        }

        if (! current_user_can('edit_post', $post->ID)) {
            return;
        }

        $is_archived = 'feuer_archived' === $post->post_status;
        $label       = $is_archived ? 'Status: archiviert' : 'Status: aktiv';
        $button_text = $is_archived ? 'Eintrag aktivieren' : 'Eintrag archivieren';
        $button_url  = $this->get_status_action_url($is_archived ? 'unarchive' : 'archive', $post->ID);
        $delete_url  = $this->can_remove_member() ? $this->get_status_action_url('delete', $post->ID) : '';

        echo $this->render_admin_template(
            'member-submitbox',
            array(
                'label'       => $label,
                'button_text' => $button_text,
                'button_url'  => $button_url,
                'delete_url'  => $delete_url,
                'can_remove'  => (bool) $delete_url,
            )
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function handle_archive_request() {
        $this->handle_status_request('archive');
    }

    public function handle_unarchive_request() {
        $this->handle_status_request('unarchive');
    }

    public function render_admin_notice() {
        $screen = get_current_screen();

        if (! $screen || ! $this->is_feuer_mannschaft_admin_context()) {
            return;
        }

        $save_error_notice = $this->get_member_save_error_notice();
        if (! empty($save_error_notice)) {
            echo $this->render_admin_template(
                'notice',
                array(
                    'type'    => 'error',
                    'message' => $save_error_notice,
                )
            ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if (! isset($_GET['feuer_mannschaft_notice'])) {
            return;
        }

        $notice = sanitize_key(wp_unslash($_GET['feuer_mannschaft_notice']));
        $count  = isset($_GET['feuer_mannschaft_count']) ? absint(wp_unslash($_GET['feuer_mannschaft_count'])) : 1;

        if ('archived' === $notice) {
            $message = 1 === $count ? 'Mitglied wurde archiviert.' : sprintf('%d Mitglieder wurden archiviert.', $count);
        } elseif ('unarchived' === $notice) {
            $message = 1 === $count ? 'Mitglied ist wieder aktiv.' : sprintf('%d Mitglieder wurden aktiviert.', $count);
        } elseif ('deleted' === $notice) {
            $message = 1 === $count ? 'Profil wurde endgültig entfernt.' : sprintf('%d Profile wurden endgültig entfernt.', $count);
        } elseif ('trashed' === $notice) {
            $message = 1 === $count ? 'Profil wurde in den Papierkorb verschoben.' : sprintf('%d Profile wurden in den Papierkorb verschoben.', $count);
        } else {
            return;
        }

        echo $this->render_admin_template(
            'notice',
            array(
                'type'    => 'success',
                'message' => $message,
            )
        );
    }
    public function save_member($post_id, $post, $update) {
        unset($update);

        if ($this->syncing_title) {
            return;
        }

        $has_member_nonce = isset($_POST['feuer_mannschaft_member_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feuer_mannschaft_member_nonce'])), 'feuer_mannschaft_save_member');
        $is_inline_edit   = isset($_POST['_inline_edit']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_inline_edit'])), 'inlineeditnonce');

        if (! $has_member_nonce && ! $is_inline_edit) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        if (! $post instanceof WP_Post || self::POST_TYPE !== $post->post_type) {
            return;
        }

        $was_managed_post = $this->is_feuer_mannschaft_post($post_id);

        if (! $was_managed_post && ! $this->is_member_request()) {
            return;
        }

        $before_meta       = $this->get_member_meta($post_id);
        $before_category   = $this->get_member_category_id($post_id);
        $before_gallery    = $this->get_member_gallery($post_id);
        $before_post_title = $post->post_title;
        $before_invite_url = $this->get_invite_url($post_id);
        $before_exclude_from_count = $this->is_excluded_from_member_count($post_id);
        $before_service_history = $this->get_service_history($post_id);

        $invite_url = $this->sanitize_invite_url(
            isset($_POST['feuer_mannschaft_custom_link'])
                ? wp_unslash($_POST['feuer_mannschaft_custom_link'])
                : $before_invite_url
        );
        $exclude_from_count = ! empty($_POST['feuer_mannschaft_exclude_from_count']);
        $archive_reason = isset($_POST['feuer_mannschaft_archive_reason'])
            ? sanitize_textarea_field(wp_unslash($_POST['feuer_mannschaft_archive_reason']))
            : (string) get_post_meta($post_id, self::ARCHIVE_REASON_META, true);
        $service_end_date = isset($_POST['feuer_mannschaft_service_end_date'])
            ? sanitize_text_field(wp_unslash($_POST['feuer_mannschaft_service_end_date']))
            : (string) get_post_meta($post_id, self::SERVICE_END_DATE_META, true);
        if ($service_end_date && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $service_end_date)) {
            $service_end_date = '';
        }

        $service_history = $before_service_history;
        if (
            isset($_POST['feuer_mannschaft_service_history_nonce'])
            && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feuer_mannschaft_service_history_nonce'])), 'feuer_mannschaft_save_service_history')
        ) {
            $service_history = $this->sanitize_service_history(
                isset($_POST['feuer_mannschaft_service_history'])
                    ? wp_unslash($_POST['feuer_mannschaft_service_history'])
                    : array()
            );
        }

        $raw_meta = isset($_POST['feuer_mannschaft_meta']) ? (array) wp_unslash($_POST['feuer_mannschaft_meta']) : array();
        $meta_input = array(
            'first_name'    => isset($raw_meta['first_name']) ? $raw_meta['first_name'] : $before_meta['first_name'],
            'last_name'     => isset($raw_meta['last_name']) ? $raw_meta['last_name'] : $before_meta['last_name'],
            'job_title'     => isset($raw_meta['job_title']) ? $raw_meta['job_title'] : $before_meta['job_title'],
            'entry_date'    => isset($raw_meta['entry_date']) ? $raw_meta['entry_date'] : $before_meta['entry_date'],
            'admin_note'    => $this->current_user_can_manage_member_admin_note()
                ? (isset($raw_meta['admin_note']) ? $raw_meta['admin_note'] : $before_meta['admin_note'])
                : $before_meta['admin_note'],
            'rank'          => $is_inline_edit ? $before_meta['rank'] : (isset($raw_meta['rank']) ? $raw_meta['rank'] : array()),
            'function'      => $is_inline_edit ? $before_meta['function_values'] : (isset($raw_meta['function']) ? $raw_meta['function'] : array()),
            'education'     => $is_inline_edit ? $before_meta['education_values'] : (isset($raw_meta['education']) ? $raw_meta['education'] : array()),
            'lehrgang'     => $is_inline_edit ? $before_meta['lehrgang_values'] : (isset($raw_meta['lehrgang']) ? $raw_meta['lehrgang'] : $before_meta['lehrgang_values']),
            'contact_fields'=> $is_inline_edit
                ? $before_meta['contact_fields']
                : (
                    isset($raw_meta['contact_fields']) && is_array($raw_meta['contact_fields'])
                        ? array_merge($before_meta['contact_fields'], $raw_meta['contact_fields'])
                        : $before_meta['contact_fields']
                ),
            'custom_fields' => $is_inline_edit ? $before_meta['custom_fields'] : (isset($raw_meta['custom_fields']) ? $raw_meta['custom_fields'] : array()),
            'sort_order'    => isset($raw_meta['sort_order']) ? $raw_meta['sort_order'] : $before_meta['sort_order'],
        );
        $meta     = $this->sanitize_member_meta(
            $meta_input
        );
        $meta['description'] = $this->sanitize_member_description(
            isset($_POST['content'])
                ? wp_unslash($_POST['content'])
                : (isset($raw_meta['description']) ? wp_unslash($raw_meta['description']) : $post->post_content)
        );

        $submitted_title       = isset($_POST['post_title']) ? sanitize_text_field(wp_unslash($_POST['post_title'])) : '';
        $name_fields_submitted = isset($raw_meta['first_name']) || isset($raw_meta['last_name']);
        $before_member_name    = trim($before_meta['first_name'] . ' ' . $before_meta['last_name']);
        $after_member_name     = trim($meta['first_name'] . ' ' . $meta['last_name']);

        if (
            '' !== $submitted_title
            && (
                ! $name_fields_submitted
                || '' === $after_member_name
                || ($submitted_title !== $before_member_name && $after_member_name === $before_member_name)
            )
        ) {
            $parsed_name        = $this->parse_member_name_from_title($submitted_title);
            $meta['first_name'] = $parsed_name['first_name'];
            $meta['last_name']  = $parsed_name['last_name'];
        }

        $has_category_input = isset($_POST['feuer_mannschaft_profile_category']);
        $selected_category  = $has_category_input
            ? $this->sanitize_member_category(wp_unslash($_POST['feuer_mannschaft_profile_category']))
            : $before_category;
        $allowed_categories = $this->get_allowed_category_ids();

        $member_name = trim($meta['first_name'] . ' ' . $meta['last_name']);
        if (! $member_name) {
            $member_name = '' !== $submitted_title ? $submitted_title : $post->post_title;
        }

        $display_title = $member_name;
        if ('' === $display_title) {
            $display_title = get_the_title($post_id);
        }

        $gallery              = $before_gallery;
        $remove_default_image = ! empty($_POST['feuer_mannschaft_remove_default_image']);
        $restore_default_image = ! empty($_POST['feuer_mannschaft_restore_default_image']);

        $description_excerpt = wp_trim_words(wp_strip_all_tags($meta['description']), 55, '...');
        $current_slug       = (string) $post->post_name;
        $slug_was_submitted = isset($_POST['feuer_mannschaft_slug']);
        $requested_slug     = $slug_was_submitted
            ? sanitize_title(wp_unslash($_POST['feuer_mannschaft_slug']))
            : $current_slug;

        // A displayed PHP warning was accidentally saved as the slug on older releases.
        // Recover a deterministic URL from the member name and let the legacy URL
        // handling below retain the corrupted path as a redirect.
        if (preg_match('/(?:warning|notice|deprecated|fatal-error|parse-error|undefined-variable|attempt-to-read-property|member-details-php|var-www)/i', $requested_slug)) {
            $requested_slug = sanitize_title($display_title);
        }

        if (! $requested_slug) {
            $requested_slug = sanitize_title($display_title);
        } elseif ($display_title !== $post->post_title && $requested_slug === $current_slug && $current_slug === sanitize_title($before_member_name)) {
            // Keep the legacy automatic name-based URL until the owner edits it.
            $requested_slug = sanitize_title($display_title);
        }

        $requested_slug = wp_unique_post_slug(
            $requested_slug,
            $post_id,
            $post->post_status,
            self::POST_TYPE,
            0
        );
        $post_update         = array(
            'ID'             => $post_id,
            'post_name'      => $requested_slug,
            'post_excerpt'   => $description_excerpt,
            'post_content'   => $meta['description'],
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        );

        if ($display_title && $display_title !== $post->post_title) {
            $post_update['post_title'] = $display_title;
        }

        if ($requested_slug !== $current_slug) {
            $previous_permalink = get_permalink($post_id);
            $previous_path      = $previous_permalink ? wp_parse_url($previous_permalink, PHP_URL_PATH) : '';

            if (is_string($previous_path) && '' !== $previous_path) {
                $previous_path = untrailingslashit($previous_path);
                $known_paths   = array_map('strval', get_post_meta($post_id, self::OLD_PERMALINK_META, false));

                if (! in_array($previous_path, $known_paths, true)) {
                    add_post_meta($post_id, self::OLD_PERMALINK_META, $previous_path, false);
                }
            }
        }

        $this->syncing_title = true;

        try {
            $post_update_result = wp_update_post($post_update, true);
        } finally {
            $this->syncing_title = false;
        }

        if (is_wp_error($post_update_result)) {
            $error_message  = $post_update_result->get_error_message();
            $notice_message = $error_message
                ? sprintf('Profil konnte nicht gespeichert werden: %s', $error_message)
                : 'Profil konnte nicht gespeichert werden.';

            $this->set_member_save_error_notice($notice_message);

            if ($this->logger instanceof Feuer_Mannschaft_Logger) {
                $this->logger->log(
                    'member_save_failed',
                    'member',
                    $post_id,
                    $display_title,
                    array(
                        array(
                            'message' => $notice_message,
                        ),
                    )
                );
            }

            return;
        }

        update_post_meta($post_id, self::PROFILE_META, 1);
        delete_post_meta($post_id, self::ENTRY_TYPE_META);

        if ('' !== $invite_url) {
            update_post_meta($post_id, self::INVITE_URL_META, $invite_url);
        } else {
            delete_post_meta($post_id, self::INVITE_URL_META);
        }

        if ($exclude_from_count) {
            update_post_meta($post_id, self::EXCLUDE_FROM_COUNT_META, 1);
        } else {
            delete_post_meta($post_id, self::EXCLUDE_FROM_COUNT_META);
        }

        foreach ($meta as $key => $value) {
            if (in_array($key, array('rank', 'function', 'education'), true)) {
                $this->persist_member_choice_meta($post_id, $key, $value);
                continue;
            }

            if ('custom_fields' === $key) {
                $this->persist_member_custom_fields_meta($post_id, $value);
                continue;
            }

            if ('contact_fields' === $key) {
                $this->persist_member_contact_fields_meta($post_id, $value);
                continue;
            }

            update_post_meta($post_id, self::META_PREFIX . $key, $value);
        }

        if ($selected_category > 0) {
            wp_set_post_categories($post_id, $this->build_member_category_assignment_ids($selected_category), false);
        } elseif (! $was_managed_post && ! empty($allowed_categories)) {
            wp_set_post_categories($post_id, $this->build_member_category_assignment_ids((int) $allowed_categories[0]), false);
        }

        $remove_ids = isset($_POST['feuer_mannschaft_remove_gallery']) ? array_map('absint', (array) wp_unslash($_POST['feuer_mannschaft_remove_gallery'])) : array();
        if (! empty($remove_ids)) {
            $remove_ids = array_values(array_intersect($gallery, $remove_ids));
            $gallery    = array_values(array_diff($gallery, $remove_ids));
        }

        $uploaded_ids = $this->handle_gallery_uploads($post_id, $display_title);
        if (! empty($uploaded_ids)) {
            $gallery = array_merge($gallery, $uploaded_ids);
        }

        $gallery = array_values(array_unique(array_filter(array_map('absint', $gallery))));

        if (! empty($gallery)) {
            update_post_meta($post_id, self::GALLERY_META, $gallery);
            set_post_thumbnail($post_id, $gallery[0]);
            delete_post_meta($post_id, self::DEFAULT_IMAGE_MANAGED_META);
            delete_post_meta($post_id, self::DEFAULT_IMAGE_OPT_OUT_META);
        } else {
            delete_post_meta($post_id, self::GALLERY_META);
            $this->update_member_default_thumbnail_state(
                $post_id,
                array(
                    'remove_default'  => $remove_default_image,
                    'restore_default' => $restore_default_image,
                )
            );
        }

        $changes = $this->build_member_log_changes(
            $before_meta,
            $this->get_member_meta($post_id),
            array(
                'before_category' => $before_category,
                'after_category'  => $this->get_member_category_id($post_id),
                'before_gallery'  => $before_gallery,
                'after_gallery'   => $gallery,
                'before_title'    => $before_post_title,
                'after_title'     => $display_title ? $display_title : get_the_title($post_id),
                'before_invite_url' => $before_invite_url,
                'after_invite_url'  => $this->get_invite_url($post_id),
                'before_exclude_from_count' => $before_exclude_from_count,
                'after_exclude_from_count'  => $this->is_excluded_from_member_count($post_id),
            )
        );

        if (! empty($changes)) {
            $this->logger->log(
                $was_managed_post ? 'member_updated' : 'member_created',
                'member',
                $post_id,
                $display_title ? $display_title : get_the_title($post_id),
                $changes
            );
        }

        if (empty($service_history)) {
            delete_post_meta($post_id, self::SERVICE_HISTORY_META);
        } else {
            update_post_meta($post_id, self::SERVICE_HISTORY_META, $service_history);
        }

        $archive_reason ? update_post_meta($post_id, self::ARCHIVE_REASON_META, $archive_reason) : delete_post_meta($post_id, self::ARCHIVE_REASON_META);
        $service_end_date ? update_post_meta($post_id, self::SERVICE_END_DATE_META, $service_end_date) : delete_post_meta($post_id, self::SERVICE_END_DATE_META);

        $this->purge_plugin_cache(
            'member_saved',
            array($post_id),
            array_merge($before_gallery, $gallery, $uploaded_ids, $remove_ids)
        );

    }

    public function migrate_profiles_to_post_type() {
        if (get_option(self::PROFILE_POST_MIGRATION_OPTION)) {
            return;
        }

        global $wpdb;

        $profile_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT p.ID FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID WHERE p.post_type = %s AND pm.meta_key = %s AND pm.meta_value = %s",
                'post',
                self::PROFILE_META,
                '1'
            )
        );
        $failed = false;

        foreach (array_unique(array_map('absint', (array) $profile_ids)) as $profile_id) {
            $old_permalink = get_permalink($profile_id);
            $updated_id    = wp_update_post(array('ID' => $profile_id, 'post_type' => self::POST_TYPE), true);

            if (is_wp_error($updated_id) || ! $updated_id) {
                $failed = true;
                continue;
            }

            if ($old_permalink) {
                update_post_meta($profile_id, self::OLD_PERMALINK_META, untrailingslashit((string) wp_parse_url($old_permalink, PHP_URL_PATH)));
            }
        }

        if (! $failed) {
            update_option(self::PROFILE_POST_MIGRATION_OPTION, time(), false);
            flush_rewrite_rules(false);
        }
    }

    public function register_settings() {
        register_setting(
            'feuer_mannschaft_settings_group',
            self::OPTION_KEY,
            array($this, 'sanitize_settings')
        );
    }

    public function sanitize_settings($input) {
        $defaults = $this->get_default_settings();
        $input    = is_array($input) ? $input : array();
        $previous = $this->get_settings();
        $active_tab = isset($_POST['feuer_mannschaft_settings_tab']) ? sanitize_key(wp_unslash($_POST['feuer_mannschaft_settings_tab'])) : 'general';

        $archive_display = isset($input['archive_display']) ? sanitize_key($input['archive_display']) : $defaults['archive_display'];
        if (! in_array($archive_display, array('cards', 'modal', 'list'), true)) {
            $archive_display = $defaults['archive_display'];
        }

        $archive_template = $this->sanitize_template_slug(
            'archive',
            isset($input['archive_template']) ? wp_unslash($input['archive_template']) : '',
            $this->map_legacy_display_to_template($archive_display)
        );

        $single_template = $this->sanitize_template_slug(
            'single',
            isset($input['single_template']) ? wp_unslash($input['single_template']) : '',
            $defaults['single_template']
        );

        $sanitized = $previous;

        if ('general' === $active_tab) {
            $sanitized['watermark_text']      = isset($input['watermark_text']) ? sanitize_text_field(wp_unslash($input['watermark_text'])) : '';
            $sanitized['allowed_categories']  = $this->sanitize_allowed_category_ids(isset($input['allowed_categories']) ? (array) $input['allowed_categories'] : array());
            $sanitized['archive_display']     = isset($previous['archive_display']) ? $previous['archive_display'] : $archive_display;
            $sanitized['archive_template']    = isset($input['archive_template']) ? $archive_template : $previous['archive_template'];
            $sanitized['single_template']     = isset($input['single_template']) ? $single_template : $previous['single_template'];
            $sanitized['default_profile_image_id'] = $this->sanitize_attachment_image_id(isset($input['default_profile_image_id']) ? wp_unslash($input['default_profile_image_id']) : 0);
            $sanitized['card_image_width']    = $this->clamp_int(isset($input['card_image_width']) ? $input['card_image_width'] : $defaults['card_image_width'], 180, 1400);
            $sanitized['card_image_height']   = $this->clamp_int(isset($input['card_image_height']) ? $input['card_image_height'] : $defaults['card_image_height'], 180, 1600);
            $sanitized['card_grayscale_strength'] = $this->clamp_int(isset($input['card_grayscale_strength']) ? $input['card_grayscale_strength'] : $defaults['card_grayscale_strength'], 0, 100);
            $sanitized['card_preset'] = in_array(isset($input['card_preset']) ? sanitize_key($input['card_preset']) : '', array('classic', 'minimal', 'dark', 'official', 'portrait-light', 'compact-horizontal', 'editorial'), true) ? sanitize_key($input['card_preset']) : 'classic';
            $sanitized['card_overlay_opacity'] = $this->clamp_int(isset($input['card_overlay_opacity']) ? $input['card_overlay_opacity'] : 65, 0, 100);
            $sanitized['card_radius'] = $this->clamp_int(isset($input['card_radius']) ? $input['card_radius'] : 12, 0, 40);
            $sanitized['card_name_size'] = $this->clamp_int(isset($input['card_name_size']) ? $input['card_name_size'] : 24, 16, 42);
            $sanitized['card_text_position'] = in_array(isset($input['card_text_position']) ? sanitize_key($input['card_text_position']) : '', array('top', 'center', 'bottom'), true) ? sanitize_key($input['card_text_position']) : 'bottom';
            $sanitized['card_show_rank'] = empty($input['card_show_rank']) ? 0 : 1;
            $sanitized['card_show_function'] = empty($input['card_show_function']) ? 0 : 1;
            $sanitized['excluded_title_color'] = $this->sanitize_setting_color(isset($input['excluded_title_color']) ? wp_unslash($input['excluded_title_color']) : $defaults['excluded_title_color'], $defaults['excluded_title_color']);
            $sanitized['enable_cache_purge'] = empty($input['enable_cache_purge']) ? 0 : 1;
            $sanitized['single_image_height'] = $this->clamp_int(isset($input['single_image_height']) ? $input['single_image_height'] : $defaults['single_image_height'], 240, 2000);
            $sanitized['slider_autoplay']     = $this->clamp_int(isset($input['slider_autoplay']) ? $input['slider_autoplay'] : $defaults['slider_autoplay'], 0, 30000);
            $sanitized['enable_gallery']      = empty($input['enable_gallery']) ? 0 : 1;
        } elseif ('mentions' === $active_tab) {
            $sanitized['enable_mentions']    = empty($input['enable_mentions']) ? 0 : 1;
            $sanitized['mention_post_types'] = $this->sanitize_setting_post_types(isset($input['mention_post_types']) ? (array) wp_unslash($input['mention_post_types']) : $defaults['mention_post_types']);
        } elseif ('fields' === $active_tab) {
            $sanitized = $this->sanitize_field_settings($input, $previous, $sanitized, $defaults);
        } elseif ('updates' === $active_tab) {
            $sanitized['enable_automatic_updates'] = empty($input['enable_automatic_updates']) ? 0 : 1;
        }

        if (
            'general' === $active_tab
            && (int) $this->sanitize_attachment_image_id(isset($previous['default_profile_image_id']) ? $previous['default_profile_image_id'] : 0)
                !== (int) $this->sanitize_attachment_image_id(isset($sanitized['default_profile_image_id']) ? $sanitized['default_profile_image_id'] : 0)
        ) {
            $this->sync_default_profile_image_assignments(
                (int) $this->sanitize_attachment_image_id(isset($previous['default_profile_image_id']) ? $previous['default_profile_image_id'] : 0),
                (int) $this->sanitize_attachment_image_id(isset($sanitized['default_profile_image_id']) ? $sanitized['default_profile_image_id'] : 0)
            );
        }

        $changes = $this->build_settings_log_changes($previous, $sanitized);
        if (! empty($changes)) {
            $this->logger->log(
                'settings_updated',
                'settings',
                0,
                'Mannschaft-Einstellungen',
                $changes
            );

            if (in_array($active_tab, array('general', 'fields'), true) && $this->is_cache_purge_enabled_in_settings($sanitized)) {
                $this->purge_plugin_cache('settings_updated', array(), array(), array(), true);
            }
        }

        return $sanitized;
    }

    public function are_cache_purges_enabled() {
        return $this->is_cache_purge_enabled_in_settings($this->get_settings());
    }

    public function purge_plugin_cache($context = 'manual', $post_ids = array(), $attachment_ids = array(), $extra_urls = array(), $force = false) {
        if ($this->cache_purge_running) {
            return false;
        }

        if (! $force && ! $this->are_cache_purges_enabled()) {
            return false;
        }

        $this->cache_purge_running = true;

        try {
            $post_ids       = $this->normalize_cache_purge_ids($post_ids);
            $attachment_ids = $this->normalize_cache_purge_ids($attachment_ids);

            foreach ($post_ids as $post_id) {
                if (function_exists('clean_post_cache')) {
                    clean_post_cache($post_id);
                }

                if (function_exists('clean_object_term_cache')) {
                    clean_object_term_cache($post_id, self::POST_TYPE);
                }

                $attachment_ids = array_merge($attachment_ids, $this->get_member_gallery($post_id));
            }

            $attachment_ids = $this->normalize_cache_purge_ids($attachment_ids);

            foreach ($attachment_ids as $attachment_id) {
                if (function_exists('clean_post_cache')) {
                    clean_post_cache($attachment_id);
                }

                if (function_exists('clean_attachment_cache')) {
                    clean_attachment_cache($attachment_id);
                }
            }

            $shortcode_page_ids = $this->get_shortcode_page_ids();
            foreach ($shortcode_page_ids as $page_id) {
                if (function_exists('clean_post_cache')) {
                    clean_post_cache($page_id);
                }
            }

            $urls = $this->normalize_cache_purge_urls(
                array_merge(
                    (array) $extra_urls,
                    $this->get_member_cache_urls($post_ids),
                    $this->get_shortcode_page_urls($shortcode_page_ids),
                    array(home_url('/'))
                )
            );

            $this->purge_external_cache_plugins($post_ids, $attachment_ids, $urls, $context);

            do_action('feuer_mannschaft_cache_purged', $context, $post_ids, $attachment_ids, $urls);

            return true;
        } finally {
            $this->cache_purge_running = false;
        }
    }

    public function are_automatic_updates_enabled() {
        $settings = $this->get_settings();

        return ! empty($settings['enable_automatic_updates']);
    }

    public function render_settings_page() {
        if (! current_user_can('manage_options')) {
            return;
        }

        $settings          = $this->get_settings();
        $choices           = $this->get_category_choices();
        $allowed           = $this->get_allowed_category_ids();
        $archive_templates = $this->get_template_choices('archive');
        $single_templates  = $this->get_template_choices('single');
        $current_tab       = $this->get_settings_tab();
        $tabs              = $this->get_settings_tabs();
        $notice            = $this->get_settings_notice();
        $update_status     = feuer_mannschaft_updater()->get_update_status(false);
        $public_post_types = $this->get_public_post_type_choices();
        $acf_available     = function_exists('acf_add_local_field_group');
        $archives          = $this->backup_manager->get_archives();
        $preview_archive   = 'archives' === $current_tab && isset($_GET['preview_archive'])
            ? $this->backup_manager->preview_archive(sanitize_file_name(wp_unslash($_GET['preview_archive'])))
            : array();
        $selected_log_user = isset($_GET['log_user']) ? absint(wp_unslash($_GET['log_user'])) : 0;
        $selected_log_action = isset($_GET['log_action']) ? sanitize_key(wp_unslash($_GET['log_action'])) : '';
        $log_date_from = isset($_GET['log_date_from']) ? sanitize_text_field(wp_unslash($_GET['log_date_from'])) : '';
        $log_date_to = isset($_GET['log_date_to']) ? sanitize_text_field(wp_unslash($_GET['log_date_to'])) : '';
        $log_page           = isset($_GET['log_page']) ? max(1, absint(wp_unslash($_GET['log_page']))) : 1;
        $log_page_size      = 50;
        $log_total           = 'logs' === $current_tab
            ? $this->logger->count_logs(array('user_id' => $selected_log_user, 'action' => $selected_log_action, 'date_from' => $log_date_from, 'date_to' => $log_date_to))
            : 0;
        if ('logs' === $current_tab) {
            $log_page = min($log_page, max(1, (int) ceil($log_total / $log_page_size)));
        }
        $logs                = 'logs' === $current_tab
            ? $this->logger->get_logs(
                array(
                    'user_id' => $selected_log_user,
                    'action'  => $selected_log_action,
                    'date_from' => $log_date_from,
                    'date_to' => $log_date_to,
                    'limit'   => $log_page_size,
                    'offset'  => ($log_page - 1) * $log_page_size,
                )
            )
            : array();
        $log_users         = $this->logger->get_user_choices();
        $log_actions       = $this->logger->get_action_choices();
        $logger            = $this->logger;
        $pending_field_replacements = $this->get_pending_field_replacements();
        $contact_field_definitions = $this->get_contact_field_definitions();
        $contact_field_settings    = $this->get_contact_field_settings($settings);
        $health_checks = 'health' === $current_tab ? $this->get_health_checks() : array();
        $orphan_media  = 'media' === $current_tab ? $this->get_orphan_member_media() : array();

        if ('fields' === $current_tab && ! empty($pending_field_replacements['requested_settings']) && is_array($pending_field_replacements['requested_settings'])) {
            $settings = wp_parse_args($pending_field_replacements['requested_settings'], $settings);
        }

        $field_groups = $this->get_field_option_groups($settings);

        echo $this->render_admin_template(
            'settings-page',
            array(
                'settings'          => $settings,
                'choices'           => $choices,
                'allowed'           => $allowed,
                'archive_templates' => $archive_templates,
                'single_templates'  => $single_templates,
                'current_tab'       => $current_tab,
                'tabs'              => $tabs,
                'notice'            => $notice,
                'update_status'     => $update_status,
                'public_post_types' => $public_post_types,
                'acf_available'     => $acf_available,
                'archives'          => $archives,
                'preview_archive'   => $preview_archive,
                'selected_log_user' => $selected_log_user,
                'logs'              => $logs,
                'log_page'          => $log_page,
                'log_page_size'     => $log_page_size,
                'log_total'         => $log_total,
                'log_users'         => $log_users,
                'log_actions'       => $log_actions,
                'selected_log_action' => $selected_log_action,
                'log_date_from'     => $log_date_from,
                'log_date_to'       => $log_date_to,
                'log_retention_days' => isset($settings['log_retention_days']) ? absint($settings['log_retention_days']) : 0,
                'logger'            => $logger,
                'field_groups'      => $field_groups,
                'contact_field_definitions' => $contact_field_definitions,
                'contact_field_settings'    => $contact_field_settings,
                'pending_field_replacements' => $pending_field_replacements,
                'health_checks' => $health_checks,
                'orphan_media'  => $orphan_media,
            )
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private function get_health_checks() {
        global $wpdb;

        $upload_dir = wp_upload_dir();
        $backup_dir = ! empty($upload_dir['basedir'])
            ? trailingslashit($upload_dir['basedir']) . self::BACKUP_UPLOAD_SUBDIR
            : '';
        $table_name = $wpdb->prefix . Feuer_Mannschaft_Logger::TABLE_SUFFIX;
        $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name))) === $table_name;
        $old_profiles = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID WHERE p.post_type = %s AND pm.meta_key = %s AND pm.meta_value = %s",
                'post',
                self::PROFILE_META,
                '1'
            )
        );

        return array(
            array('label' => 'PHP', 'ok' => version_compare(PHP_VERSION, '8.1', '>='), 'message' => PHP_VERSION),
            array('label' => 'WordPress', 'ok' => version_compare(get_bloginfo('version'), '6.0', '>='), 'message' => get_bloginfo('version')),
            array('label' => 'Uploads', 'ok' => ! empty($upload_dir['basedir']) && is_writable($upload_dir['basedir']), 'message' => ! empty($upload_dir['basedir']) ? $upload_dir['basedir'] : 'Pfad nicht verfügbar'),
            array('label' => 'Backup-Speicher', 'ok' => '' !== $backup_dir && is_dir($backup_dir) && is_writable($backup_dir), 'message' => '' !== $backup_dir ? $backup_dir : 'Pfad nicht verfügbar'),
            array('label' => 'Aktivitätsprotokoll', 'ok' => $table_exists, 'message' => $table_exists ? 'Datenbanktabelle vorhanden' : 'Datenbanktabelle fehlt'),
            array('label' => 'Profilmigration', 'ok' => 0 === $old_profiles, 'message' => 0 === $old_profiles ? 'Alle markierten Profile verwenden den eigenen Inhaltstyp' : sprintf('%d Profile warten noch auf Migration', $old_profiles)),
        );

    }

    private function get_orphan_member_media() {
        $member_ids = get_posts(
            array(
                'post_type'      => self::POST_TYPE,
                'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'trash', 'feuer_archived'),
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => array(array('key' => self::PROFILE_META, 'value' => '1')),
            )
        );
        $used_ids      = array();

        foreach ($member_ids as $member_id) {
            $used_ids = array_merge($used_ids, $this->get_member_gallery($member_id));
            $thumbnail_id = get_post_thumbnail_id($member_id);
            if ($thumbnail_id) {
                $used_ids[] = (int) $thumbnail_id;
            }
        }

        $used_ids = array_fill_keys(array_map('absint', $used_ids), true);
        $attachments = get_posts(
            array(
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => 500,
                'orderby'        => 'ID',
                'order'          => 'DESC',
                'meta_query'     => array(array('key' => '_wp_attached_file', 'value' => self::MEMBER_UPLOAD_SUBDIR . '/', 'compare' => 'LIKE')),
            )
        );
        $orphans = array();

        foreach ($attachments as $attachment) {
            if (isset($used_ids[$attachment->ID])) {
                continue;
            }

            if ($this->is_attachment_referenced_elsewhere($attachment)) {
                continue;
            }

            $orphans[] = $attachment;
        }

        return $orphans;
    }

    private function is_attachment_referenced_elsewhere($attachment) {
        global $wpdb;

        $thumbnail_reference = get_posts(
            array(
                'post_type'      => 'any',
                'post_status'    => 'any',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_query'     => array(array('key' => '_thumbnail_id', 'value' => (int) $attachment->ID)),
            )
        );

        if (! empty($thumbnail_reference) || ($attachment->post_parent && self::POST_TYPE !== get_post_type($attachment->post_parent))) {
            return true;
        }

        $url = wp_get_attachment_url($attachment->ID);
        if (! $url) {
            return false;
        }

        $image_class = '%wp-image-' . absint($attachment->ID) . '%';
        $url_pattern = '%' . $wpdb->esc_like($url) . '%';
        $reference = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE ID <> %d AND post_type NOT IN ('attachment', 'revision') AND post_status NOT IN ('trash', 'auto-draft') AND (post_content LIKE %s OR post_content LIKE %s) LIMIT 1",
                (int) $attachment->ID,
                $image_class,
                $url_pattern
            )
        );

        return (bool) $reference;
    }

    public function handle_cleanup_orphan_media_request() {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('Sie haben keine Berechtigung für diese Aktion.', 'feuer-mannschaft'));
        }

        $attachment_id = isset($_GET['attachment']) ? absint(wp_unslash($_GET['attachment'])) : 0;
        check_admin_referer('feuer_mannschaft_cleanup_orphan_media_' . $attachment_id);
        $attachment = get_post($attachment_id);

        $orphan_ids = array_map('absint', wp_list_pluck($this->get_orphan_member_media(), 'ID'));
        if (! $attachment instanceof WP_Post || 'attachment' !== $attachment->post_type || ! in_array($attachment_id, $orphan_ids, true)) {
            wp_die(esc_html__('Das Medium wird noch verwendet oder wurde nicht gefunden.', 'feuer-mannschaft'));
        }

        wp_delete_attachment($attachment_id, true);
        wp_safe_redirect(admin_url('admin.php?page=feuer-mannschaft-settings&tab=media'));
        exit;
    }

    public function schedule_log_pruning() {
        if (! wp_next_scheduled('feuer_mannschaft_prune_logs')) {
            wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'feuer_mannschaft_prune_logs');
        }
    }

    public function prune_logs() {
        $settings = $this->get_settings();
        $this->logger->prune_logs(isset($settings['log_retention_days']) ? absint($settings['log_retention_days']) : 0);
    }

    public function handle_save_log_retention() {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('Sie haben keine Berechtigung für diese Aktion.', 'feuer-mannschaft'));
        }
        check_admin_referer('feuer_mannschaft_save_log_retention');

        $allowed = array(0, 90, 180, 365, 730);
        $days = isset($_POST['log_retention_days']) ? absint(wp_unslash($_POST['log_retention_days'])) : 365;
        if (! in_array($days, $allowed, true)) {
            $days = 365;
        }

        $settings = get_option(self::OPTION_KEY, array());
        $settings = is_array($settings) ? $settings : array();
        $settings['log_retention_days'] = $days;
        update_option(self::OPTION_KEY, $settings, false);

        wp_safe_redirect(admin_url('admin.php?page=feuer-mannschaft-settings&tab=logs&feuer_mannschaft_settings_notice=log_retention_saved'));
        exit;
    }

    public function handle_export_logs_request() {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('Sie haben keine Berechtigung für diese Aktion.', 'feuer-mannschaft'));
        }
        check_admin_referer('feuer_mannschaft_export_logs');

        $filters = array(
            'user_id'  => isset($_GET['log_user']) ? absint(wp_unslash($_GET['log_user'])) : 0,
            'action'   => isset($_GET['log_action']) ? sanitize_key(wp_unslash($_GET['log_action'])) : '',
            'date_from' => isset($_GET['log_date_from']) ? sanitize_text_field(wp_unslash($_GET['log_date_from'])) : '',
            'date_to'  => isset($_GET['log_date_to']) ? sanitize_text_field(wp_unslash($_GET['log_date_to'])) : '',
            'limit'    => 10000,
        );
        $logs = $this->logger->get_logs($filters);

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="feuer-mannschaft-log.csv"');
        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, array('Zeitpunkt (UTC)', 'Benutzer', 'Aktion', 'Objekttyp', 'Objekt-ID', 'Objekt', 'Details'), ';', '"', '\\');

        foreach ($logs as $log) {
            $user = $log->user_id ? get_user_by('id', (int) $log->user_id) : null;
            fputcsv(
                $output,
                array($log->created_at, $user ? $user->display_name : 'System', $this->logger->get_action_label($log->action), $log->entity_type, (int) $log->entity_id, $log->entity_label, $this->logger->format_details_for_display($log->details_array)),
                ';',
                '"',
                '\\'
            );
        }

        fclose($output);
        exit;
    }

    public function enqueue_admin_assets() {
        $is_feuer_mannschaft_context = $this->is_feuer_mannschaft_admin_context();
        $is_mentions_context   = $this->mentions instanceof Feuer_Mannschaft_Mentions && $this->mentions->is_editor_context();

        if (! $is_feuer_mannschaft_context && ! $is_mentions_context) {
            return;
        }

        if ($this->is_settings_screen()) {
            wp_enqueue_media();
        }

        wp_enqueue_style(
            'feuer-mannschaft-admin',
            FEUER_MANNSCHAFT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            FEUER_MANNSCHAFT_VERSION
        );

        wp_enqueue_script(
            'feuer-mannschaft-admin',
            FEUER_MANNSCHAFT_PLUGIN_URL . 'assets/js/admin.js',
            array(),
            FEUER_MANNSCHAFT_VERSION,
            true
        );

        wp_localize_script(
            'feuer-mannschaft-admin',
            'feuerMannschaftAdminData',
            array(
                'addNewUrl'           => $this->get_add_new_url(),
                'mentions'            => $is_mentions_context ? $this->mentions->get_editor_assets_data() : array(),
                'defaultProfileImage' => array(
                    'chooseLabel' => 'Standardfoto auswählen',
                    'replaceLabel' => 'Standardfoto ersetzen',
                    'removeLabel' => 'Standardfoto entfernen',
                    'emptyNotice' => 'Noch kein Standardfoto ausgewaehlt.',
                    'frameTitle' => 'Standardfoto auswählen',
                    'frameButton' => 'Dieses Foto verwenden',
                ),
            )
        );

    }

    private function register_member_post_type() {
        register_post_type(self::POST_TYPE, array(
            'labels' => array(
                'name' => 'Mannschaft',
                'singular_name' => 'Teilnehmerprofil',
                'add_new' => 'Profil hinzufügen',
                'add_new_item' => 'Neues Teilnehmerprofil',
                'edit_item' => 'Teilnehmerprofil bearbeiten',
                'view_item' => 'Profil ansehen',
                'search_items' => 'Profile suchen',
                'not_found' => 'Keine Profile gefunden.',
                'not_found_in_trash' => 'Keine Profile im Papierkorb.',
                'all_items' => 'Alle Profile',
            ),
            'public' => true,
            'has_archive' => 'mannschaft',
            'rewrite' => array('slug' => 'mannschaft', 'with_front' => false),
            'query_var' => true,
            'show_in_rest' => false,
            'show_in_menu' => false,
            'show_in_nav_menus' => true,
            'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions'),
            'taxonomies' => array('category'),
            'capability_type' => array('feuer_member', 'feuer_members'),
            'map_meta_cap' => true,
            'delete_with_user' => false,
        ));
    }

    public function enqueue_frontend_assets() {
        if (! $this->should_load_frontend_assets()) {
            return;
        }

        $settings    = $this->get_settings();
        $card_width  = max(180, (int) $settings['card_image_width']);
        $card_height = max(180, (int) $settings['card_image_height']);
        $card_grayscale = $this->clamp_int(isset($settings['card_grayscale_strength']) ? $settings['card_grayscale_strength'] : 100, 0, 100);
        $excluded_title_color = $this->sanitize_setting_color(isset($settings['excluded_title_color']) ? $settings['excluded_title_color'] : '#b22222', '#b22222');
        $card_overlay = $this->clamp_int(isset($settings['card_overlay_opacity']) ? $settings['card_overlay_opacity'] : 65, 0, 100) / 100;
        $card_radius = $this->clamp_int(isset($settings['card_radius']) ? $settings['card_radius'] : 12, 0, 40);
        $card_name_size = $this->clamp_int(isset($settings['card_name_size']) ? $settings['card_name_size'] : 24, 16, 42);

        wp_enqueue_style(
            'feuer-mannschaft-frontend',
            FEUER_MANNSCHAFT_PLUGIN_URL . 'assets/css/feuer-mannschaft.css',
            array(),
            FEUER_MANNSCHAFT_VERSION
        );

        wp_add_inline_style(
            'feuer-mannschaft-frontend',
            sprintf(
                '.feuer-mannschaft-archive-page{--feuer-mannschaft-card-aspect-ratio:%1$d / %2$d;--feuer-mannschaft-card-grayscale:%3$d%%;--feuer-card-overlay:%5$s;--feuer-card-radius:%6$dpx;--feuer-card-name-size:%7$dpx;}.feuer-mannschaft-single-page{--feuer-mannschaft-single-height:%8$dpx;--feuer-mannschaft-excluded-title-color:%4$s;}',
                $card_width,
                $card_height,
                $card_grayscale,
                $excluded_title_color,
                $card_overlay,
                $card_radius,
                $card_name_size,
                $this->clamp_int(isset($settings['single_image_height']) ? $settings['single_image_height'] : 620, 240, 2000)
            )
        );

        wp_enqueue_script(
            'feuer-mannschaft-frontend',
            FEUER_MANNSCHAFT_PLUGIN_URL . 'assets/js/feuer-mannschaft.js',
            array(),
            FEUER_MANNSCHAFT_VERSION,
            true
        );

        wp_localize_script(
            'feuer-mannschaft-frontend',
            'feuerMannschaftData',
            array(
                'archiveStorageKey' => 'feuerMannschaftArchiveUrl',
            )
        );
    }

    public function tune_member_queries($query) {
        if (! $query instanceof WP_Query) {
            return;
        }

        if (! is_admin() || ! $query->is_main_query() || ! $this->is_member_list_request()) {
            return;
        }

        $meta_query = $query->get('meta_query');
        if (! is_array($meta_query)) {
            $meta_query = array();
        }

        $meta_query[] = array(
            'key'   => self::PROFILE_META,
            'value' => '1',
        );

        $query->set('meta_query', $meta_query);
        $query->set('meta_key', self::META_PREFIX . 'sort_order');
        $query->set(
            'orderby',
            array(
                'meta_value_num' => 'ASC',
                'title'          => 'ASC',
            )
        );
        $query->set('order', 'ASC');
    }

    public function template_include($template) {
        if (is_singular(self::POST_TYPE) && $this->is_member_post(get_queried_object_id())) {
            return FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/single-feuer-mannschaft-member.php';
        }

        if (is_post_type_archive(self::POST_TYPE)) {
            return FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/archive-feuer-mannschaft-member.php';
        }

        if ($this->is_profile_shortcode_page()) {
            return FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/page-feuer-mannschaft-profile.php';
        }

        if ($this->is_member_category_archive()) {
            return FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/archive-feuer-mannschaft-member.php';
        }

        return $template;
    }

    public function maybe_block_archived_member_access() {
        if (! is_singular(self::POST_TYPE)) {
            return;
        }

        $post_id = get_queried_object_id();
        if (! $post_id || ! $this->is_member_post($post_id) || 'feuer_archived' !== get_post_status($post_id)) {
            return;
        }

        global $wp_query;

        if ($wp_query instanceof WP_Query) {
            $wp_query->set_404();
        }

        status_header(404);
        nocache_headers();
        $template = get_404_template();
        if ($template) {
            include $template;
        }
        exit;
    }

    public function maybe_redirect_default_single_template_url() {
        if (! is_singular(self::POST_TYPE) || ! $this->is_member_post(get_queried_object_id())) {
            return;
        }

        if (! isset($_GET['feuer_mannschaft_single_template'])) {
            return;
        }

        $requested_template = $this->get_requested_single_template();
        $settings           = $this->get_settings();
        $default_template   = $this->sanitize_template_slug('single', $settings['single_template'], 'standard');

        if ('' === $requested_template || $requested_template === $default_template || 'standard' === $requested_template) {
            wp_safe_redirect(remove_query_arg('feuer_mannschaft_single_template', get_permalink(get_queried_object_id())), 301);
            exit;
        }
    }

    public function shortcode_list($atts) {
        $atts = shortcode_atts(
            array(
                'category'        => 0,
                'mode'            => '',
                'template'        => '',
                'single_template' => '',
            ),
            $atts,
            'feuer_mannschaft_list'
        );

        $template = sanitize_key($atts['template']);
        if ('' === $template && ! empty($atts['mode'])) {
            $template = $this->map_legacy_display_to_template(sanitize_key($atts['mode']));
        }

        return $this->get_archive_markup(
            (int) $atts['category'],
            $template,
            sanitize_key($atts['single_template'])
        );
    }

    public function shortcode_profile($atts) {
        $atts = shortcode_atts(
            array(
                'id'       => 0,
                'slug'     => '',
                'name'     => '',
                'random'   => 0,
                'category' => '',
                'template' => '',
            ),
            $atts,
            'feuer_mannschaft_profile'
        );

        $post_ids = $this->resolve_shortcode_member_ids(
            array(
                'id'       => $atts['id'],
                'slug'     => $atts['slug'],
                'name'     => $atts['name'],
                'random'   => $atts['random'],
                'count'    => 1,
                'category' => $atts['category'],
            )
        );

        $post_id            = empty($post_ids) ? 0 : (int) $post_ids[0];
        $has_explicit_match = ! empty($atts['id']) || ! empty($atts['slug']) || ! empty($atts['name']) || $this->is_truthy_shortcode_attr($atts['random']);

        if (! $post_id && ! $has_explicit_match && is_singular(self::POST_TYPE) && $this->is_member_post(get_queried_object_id())) {
            $post_id = get_queried_object_id();
        }

        if (! $post_id) {
            return '';
        }

        return $this->get_single_markup($post_id, sanitize_key($atts['template']));
    }

    public function shortcode_members($atts) {
        $atts = shortcode_atts(array(
            'category' => '', 'group' => '', 'department' => '', 'specialty' => '',
            'limit' => -1, 'mentioned_in' => '', 'template' => '',
        ), $atts, 'feuer_members');

        $query_args = array(
            'post_type' => self::POST_TYPE, 'post_status' => 'publish',
            'posts_per_page' => -1 === (int) $atts['limit'] ? -1 : min(100, max(1, absint($atts['limit']))),
            'fields' => 'ids', 'orderby' => array('menu_order' => 'ASC', 'title' => 'ASC'),
            'meta_query' => array(array('key' => self::PROFILE_META, 'value' => '1')),
        );
        $tax_query = array();
        foreach (array('category' => 'category', 'group' => 'feuer_group', 'department' => 'feuer_department', 'specialty' => 'feuer_specialty') as $attribute => $taxonomy) {
            if ('' !== trim((string) $atts[$attribute])) {
                $tax_query[] = array('taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => array_map('sanitize_title', explode(',', $atts[$attribute])));
            }
        }
        if ($tax_query) { $query_args['tax_query'] = array_merge(array('relation' => 'AND'), $tax_query); }
        if ('current' === sanitize_key($atts['mentioned_in']) && get_the_ID()) {
            $ids = get_post_meta(get_the_ID(), Feuer_Mannschaft_Mentions::META_KEY, true);
            $query_args['post__in'] = array_filter(array_map('absint', (array) $ids));
            if (empty($query_args['post__in'])) { return ''; }
        }
        $ids = get_posts($query_args);
        return empty($ids) ? '' : $this->get_selected_members_markup($ids, sanitize_key($atts['template']));
    }

    public function shortcode_org_chart($atts) {
        $atts = shortcode_atts(array('show_empty' => '0'), $atts, 'feuer_org_chart');
        $departments = get_terms(array(
            'taxonomy'   => 'feuer_department',
            'hide_empty' => ! $this->is_truthy_shortcode_attr($atts['show_empty']),
            'orderby'    => 'name',
        ));

        if (is_wp_error($departments) || empty($departments)) {
            return '';
        }

        $sections = array();
        foreach ($departments as $department) {
            $member_ids = get_posts(array(
                'post_type'      => self::POST_TYPE,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'orderby'        => array('menu_order' => 'ASC', 'title' => 'ASC'),
                'tax_query'      => array(array(
                    'taxonomy' => 'feuer_department',
                    'field'    => 'term_id',
                    'terms'    => array($department->term_id),
                )),
                'meta_query'     => array(array('key' => self::PROFILE_META, 'value' => '1')),
            ));

            if (empty($member_ids) && ! $this->is_truthy_shortcode_attr($atts['show_empty'])) {
                continue;
            }

            $sections[] = array('term' => $department, 'member_ids' => $member_ids);
        }

        return $this->render_frontend_template('org-chart', array('sections' => $sections));
    }

    public function shortcode_service_history($atts) {
        $atts = shortcode_atts(array('id' => 0), $atts, 'feuer_service_history');
        $post_id = absint($atts['id']);
        if (! $post_id && is_singular(self::POST_TYPE)) {
            $post_id = get_queried_object_id();
        }

        return $this->render_service_timeline($post_id);
    }

    public function shortcode_archived_members($atts) {
        $atts = shortcode_atts(array('limit' => -1), $atts, 'feuer_archived_members');
        $limit = -1 === (int) $atts['limit'] ? -1 : min(100, max(1, absint($atts['limit'])));
        $member_ids = get_posts(array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'feuer_archived',
            'posts_per_page' => $limit,
            'fields'         => 'ids',
            'orderby'        => 'title',
            'order'          => 'ASC',
            'meta_query'     => array(array('key' => self::PROFILE_META, 'value' => '1')),
        ));

        if (empty($member_ids)) {
            return '';
        }

        return $this->render_frontend_template('archived-members', array(
            'member_ids' => $member_ids,
        ));
    }

    public function render_service_timeline($post_id) {
        if (! apply_filters('feuer_mannschaft_field_visible', true, 'service_history', absint($post_id))) {
            return '';
        }
        $events = $this->get_service_history($post_id);
        if (empty($events)) {
            return '';
        }

        return $this->render_frontend_template('service-timeline', array(
            'events'      => $events,
            'event_types' => $this->get_service_history_types(),
            'post_id'     => absint($post_id),
        ));
    }

    private function get_service_history($post_id) {
        $events = get_post_meta(absint($post_id), self::SERVICE_HISTORY_META, true);
        return $this->sanitize_service_history(is_array($events) ? $events : array());
    }

    private function get_service_history_types() {
        return array(
            'entry'         => 'Eintritt',
            'rank'          => 'Dienstgrad',
            'function'      => 'Funktion',
            'department'    => 'Abteilung',
            'qualification' => 'Qualifikation',
            'award'         => 'Auszeichnung',
            'jubilee'       => 'Jubiläum',
            'exit'          => 'Austritt',
            'other'         => 'Sonstiges',
        );
    }

    private function sanitize_service_history($events) {
        if (! is_array($events)) {
            return array();
        }

        $types = $this->get_service_history_types();
        $clean = array();
        foreach ($events as $event) {
            if (! is_array($event)) {
                continue;
            }

            $date = isset($event['date']) ? sanitize_text_field((string) $event['date']) : '';
            $title = isset($event['title']) ? sanitize_text_field((string) $event['title']) : '';
            $description = isset($event['description']) ? sanitize_textarea_field((string) $event['description']) : '';
            $type = isset($event['type']) ? sanitize_key((string) $event['type']) : 'other';

            if (! isset($types[$type])) {
                $type = 'other';
            }
            if ($date && ! preg_match('/^\d{4}(?:-\d{2}(?:-\d{2})?)?$/', $date)) {
                $date = '';
            }
            if ('' === $date && '' === $title && '' === $description) {
                continue;
            }

            $clean[] = compact('date', 'type', 'title', 'description');
        }

        usort($clean, function ($left, $right) {
            return strcmp($right['date'], $left['date']);
        });

        return array_slice($clean, 0, 100);
    }

    public function shortcode_count($atts) {
        $atts = shortcode_atts(
            array(
                'category' => '',
                'text'     => '',
            ),
            $atts,
            'feuer_mannschaft_count'
        );

        $category_ids = $this->normalize_shortcode_category_ids($atts['category']);
        $count        = $this->has_shortcode_category_filter($atts['category']) && empty($category_ids)
            ? 0
            : count($this->get_countable_member_ids($category_ids));

        if ('' !== trim((string) $atts['text'])) {
            return esc_html($this->format_shortcode_count_text($atts['text'], $count));
        }

        return (string) $count;
    }

    public function shortcode_cards($atts) {
        $atts = shortcode_atts(
            array(
                'id'              => 0,
                'slug'            => '',
                'name'            => '',
                'random'          => 0,
                'count'           => 1,
                'category'        => '',
                'template'        => '',
                'single_template' => '',
                'title'           => '',
                'description'     => '',
            ),
            $atts,
            'feuer_mannschaft_cards'
        );

        $post_ids = $this->resolve_shortcode_member_ids(
            array(
                'id'       => $atts['id'],
                'slug'     => $atts['slug'],
                'name'     => $atts['name'],
                'random'   => $atts['random'],
                'count'    => $atts['count'],
                'category' => $atts['category'],
            )
        );

        if (empty($post_ids)) {
            return '';
        }

        return $this->get_selected_members_markup(
            $post_ids,
            sanitize_key($atts['template']),
            sanitize_key($atts['single_template']),
            sanitize_text_field($atts['title']),
            sanitize_text_field($atts['description'])
        );
    }

    public function render_archive_page() {
        $category_id = 0;

        if ($this->is_member_category_archive()) {
            $term = get_queried_object();
            if ($term instanceof WP_Term) {
                $category_id = (int) $term->term_id;
            }
        }

        echo $this->get_archive_markup($category_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function render_single_page() {
        echo $this->get_single_markup(get_queried_object_id(), $this->get_requested_single_template()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function filter_admin_columns($columns) {
        if (! $this->is_member_list_request()) {
            return $columns;
        }

        if (function_exists('wp_is_mobile') && wp_is_mobile()) {
            return array(
                'cb'         => isset($columns['cb']) ? $columns['cb'] : '',
                'title'      => 'Mitglied',
                'categories' => isset($columns['categories']) ? $columns['categories'] : 'Kategorie',
            );
        }

        return array(
            'cb'         => isset($columns['cb']) ? $columns['cb'] : '',
            'thumb'      => 'Foto',
            'title'      => 'Mitglied',
            'category'   => 'Kategorie',
            'rank'       => 'Dienstgrad',
            'sort_order' => 'Position',
            'date'       => isset($columns['date']) ? $columns['date'] : 'Datum',
        );
    }

    public function render_admin_column($column, $post_id) {
        if (! $this->is_feuer_mannschaft_post($post_id)) {
            return;
        }

        $meta = $this->get_member_meta($post_id);

        if ('thumb' === $column) {
            $gallery = $this->get_member_gallery($post_id);
            if (! empty($gallery)) {
                echo wp_get_attachment_image($gallery[0], array(72, 72)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } elseif ($this->get_default_profile_image_id()) {
                echo $this->get_default_profile_image_markup(array(72, 72), 'feuer-mannschaft-admin-default-thumb'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                echo $this->render_admin_template('no-photo'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }

        if ('rank' === $column) {
            echo esc_html($meta['rank']);
        }

        if ('category' === $column) {
            $term = $this->get_member_category_id($post_id) ? get_category($this->get_member_category_id($post_id)) : null;
            echo $term instanceof WP_Term ? esc_html($term->name) : '&mdash;'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if ('sort_order' === $column) {
            echo esc_html((string) $meta['sort_order']);
        }
    }

    public function maybe_redirect_legacy_profile_url() {
        if (! is_404()) {
            return;
        }

        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
        if ('' === $request_uri) {
            return;
        }

        $matches = get_posts(
            array(
                'post_type'      => self::POST_TYPE,
                'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'feuer_archived'),
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_query'     => array(
                    array(
                        'key'   => self::OLD_PERMALINK_META,
                        'value' => untrailingslashit((string) wp_parse_url($request_uri, PHP_URL_PATH)),
                    ),
                ),
            )
        );

        if (! empty($matches)) {
            wp_safe_redirect(get_permalink((int) $matches[0]), 301);
            exit;
        }
    }

    public function handle_update_check_request() {
        if (! current_user_can('update_plugins')) {
            wp_die('Sie haben keine Berechtigung fuer diese Aktion.');
        }

        check_admin_referer('feuer_mannschaft_check_updates');

        $status = feuer_mannschaft_updater()->get_update_status(true);
        $notice = 'update_check_failed';
        $message = 'Die Update-Pruefung konnte nicht abgeschlossen werden.';

        if (! empty($status['metadata']) && ! empty($status['remote_version'])) {
            if (! empty($status['has_update'])) {
                $notice = 'update_check_available';
                $message = sprintf(
                    'Version %1$s ist verfuegbar. Installiert ist aktuell %2$s.',
                    $status['remote_version'],
                    $status['installed_version']
                );
            } else {
                $notice = 'update_check_current';
                $message = sprintf(
                    'Mannschaft ist aktuell. Installierte Version: %s.',
                    $status['installed_version']
                );
            }
        } elseif (! empty($status['metadata_error'])) {
            $message = sprintf(
                'Die Update-Pruefung konnte nicht abgeschlossen werden: %s',
                $status['metadata_error']
            );
        }

        wp_safe_redirect(
            add_query_arg(
                array(
                    'page' => 'feuer-mannschaft-settings',
                    'tab' => 'updates',
                    'feuer_mannschaft_settings_notice' => $notice,
                    'feuer_mannschaft_settings_message' => rawurlencode($message),
                ),
                admin_url('admin.php')
            )
        );
        exit;
    }

    private function get_status_action_url($action, $post_id) {
        $allowed_actions = array('archive', 'unarchive', 'delete', 'delete_permanently');
        $action = in_array($action, $allowed_actions, true) ? $action : 'archive';

        return wp_nonce_url(
            admin_url(
                sprintf(
                    'admin.php?action=feuer_mannschaft_%1$s&post=%2$d',
                    $action,
                    absint($post_id)
                )
            ),
            'feuer_mannschaft_' . $action . '_' . absint($post_id)
        );
    }

    private function handle_status_request($action) {
        $action  = 'unarchive' === $action ? 'unarchive' : 'archive';
        $post_id = isset($_GET['post']) ? absint(wp_unslash($_GET['post'])) : 0;

        if (! $post_id || ! $this->is_feuer_mannschaft_post($post_id)) {
            wp_die('Ungültiges Mannschaft-Profil.');
        }

        if (! current_user_can('edit_post', $post_id)) {
            wp_die('Sie haben keine Berechtigung für diese Aktion.');
        }

        check_admin_referer('feuer_mannschaft_' . $action . '_' . $post_id);

        $target_status = 'archive' === $action ? 'feuer_archived' : 'publish';
        $this->update_member_status($post_id, $target_status);

        wp_safe_redirect(
            add_query_arg(
                array(
                    'feuer_mannschaft_profile' => 1,
                    'feuer_mannschaft_notice'  => 'archive' === $action ? 'archived' : 'unarchived',
                    'feuer_mannschaft_count'   => 1,
                ),
                $this->get_member_list_url()
            )
        );
        exit;
    }
    public function handle_delete_request() {
        $post_id = isset($_GET['post']) ? absint(wp_unslash($_GET['post'])) : 0;

        if (! $post_id || ! $this->is_feuer_mannschaft_post($post_id)) {
            wp_die('Ungültiges Mannschaft-Profil.');
        }

        if (! $this->can_remove_member()) {
            wp_die('Nur die Hauptadministration darf Mannschaft-Profile entfernen.');
        }

        check_admin_referer('feuer_mannschaft_delete_' . $post_id);

        $member_name = $this->get_member_display_name($post_id);
        $gallery_ids = $this->get_member_gallery($post_id);
        $cache_urls  = $this->get_member_cache_urls(array($post_id));
        $trashed = wp_trash_post($post_id);

        if (! $trashed) {
            wp_die('Das Profil konnte nicht in den Papierkorb verschoben werden.');
        }

        $this->logger->log(
            'member_trashed',
            'member',
            $post_id,
            $member_name,
            array(
                array(
                    'message' => 'Profil wurde in den Papierkorb verschoben.',
                ),
            )
        );

        $this->purge_plugin_cache('member_trashed', array($post_id), $gallery_ids, $cache_urls);

        wp_safe_redirect(
            add_query_arg(
                array(
                    'feuer_mannschaft_profile' => 1,
                    'feuer_mannschaft_notice'  => 'trashed',
                    'feuer_mannschaft_count'   => 1,
                ),
                $this->get_member_list_url()
            )
        );
        exit;
    }

    public function handle_permanent_delete_request() {
        $post_id = isset($_GET['post']) ? absint(wp_unslash($_GET['post'])) : 0;

        if (! $post_id || ! $this->is_feuer_mannschaft_post($post_id) || 'trash' !== get_post_status($post_id)) {
            wp_die('Nur Profile im Papierkorb können endgültig gelöscht werden.');
        }

        if (! $this->can_remove_member()) {
            wp_die('Nur die Hauptadministration darf Mannschaft-Profile endgültig löschen.');
        }

        check_admin_referer('feuer_mannschaft_delete_permanently_' . $post_id);

        $member_name = $this->get_member_display_name($post_id);
        $gallery_ids = $this->get_member_gallery($post_id);
        $cache_urls  = $this->get_member_cache_urls(array($post_id));
        $deleted     = wp_delete_post($post_id, true);

        if (! $deleted) {
            wp_die('Das Profil konnte nicht endgültig gelöscht werden.');
        }

        $this->logger->log('member_deleted', 'member', $post_id, $member_name, array(array('message' => 'Profil endgültig aus dem Papierkorb gelöscht.')));
        $this->purge_plugin_cache('member_deleted', array(), $gallery_ids, $cache_urls);

        wp_safe_redirect(add_query_arg(array('feuer_mannschaft_profile' => 1, 'feuer_mannschaft_notice' => 'deleted', 'feuer_mannschaft_count' => 1), $this->get_member_list_url()));
        exit;
    }
    private function update_member_status($post_id, $status) {
        $post = get_post($post_id);

        if (! $post instanceof WP_Post || ! $this->is_feuer_mannschaft_post($post)) {
            return false;
        }

        if (! in_array($status, array('publish', 'feuer_archived'), true)) {
            return false;
        }

        if ($post->post_status === $status) {
            return true;
        }

        $result = wp_update_post(
            array(
                'ID'          => $post_id,
                'post_status' => $status,
            ),
            true
        );

        if (is_wp_error($result)) {
            return false;
        }

        $this->logger->log(
            'publish' === $status ? 'member_unarchived' : 'member_archived',
            'member',
            $post_id,
            $this->get_member_display_name($post_id),
            array(
                array(
                    'field'  => 'Status',
                    'before' => 'publish' === $status ? 'Archiviert' : 'Aktiv',
                    'after'  => 'publish' === $status ? 'Aktiv' : 'Archiviert',
                ),
            )
        );

        $this->purge_plugin_cache('member_status_changed', array($post_id), $this->get_member_gallery($post_id));

        return true;
    }

    public function get_archive_markup($preset_category = 0, $preset_template = '', $preset_single_template = '') {
        $settings         = $this->get_settings();
        $archive_template = $this->sanitize_template_slug('archive', $preset_template, $settings['archive_template']);
        $single_template  = $this->sanitize_template_slug('single', $preset_single_template, $settings['single_template']);
        $query            = $this->get_members_query($preset_category);
        $term             = $preset_category > 0 ? get_category($preset_category) : null;
        $title            = $term instanceof WP_Term ? $term->name : 'Mannschaft';
        $description      = $this->get_archive_description($term);

        $output = $this->render_template_file(
            'archive',
            $archive_template,
            array(
                'settings'         => $settings,
                'card_display_classes' => $this->get_archive_card_display_classes($settings),
                'query'            => $query,
                'term'             => $term,
                'category_id'      => (int) $preset_category,
                'title'            => $title,
                'description'      => $description,
                'archive_template' => $archive_template,
                'single_template'  => $single_template,
            )
        );

        wp_reset_postdata();

        return $output;
    }

    private function get_archive_card_display_classes($settings) {
        $settings = is_array($settings) ? $settings : array();
        $preset = isset($settings['card_preset']) ? sanitize_html_class($settings['card_preset']) : 'classic';
        $position = isset($settings['card_text_position']) ? sanitize_html_class($settings['card_text_position']) : 'bottom';
        $classes = array('feuer-card-preset-' . $preset, 'feuer-card-text-' . $position);

        if (empty($settings['card_show_rank'])) {
            $classes[] = 'feuer-card-hide-rank';
        }
        if (empty($settings['card_show_function'])) {
            $classes[] = 'feuer-card-hide-function';
        }

        return implode(' ', $classes);
    }

    private function get_archive_description($term) {
        if ($term instanceof WP_Term) {
            $description = trim((string) wp_strip_all_tags((string) term_description($term, 'category')));
            if ($description !== '') {
                return $description;
            }

            return 'Hier stellen wir unser Team vor';
        }

        return 'Hier stellen wir unser Team vor';
    }

    public function get_single_markup($post_id, $preset_template = '') {
        $post = get_post($post_id);
        if (! $post instanceof WP_Post || ! $this->is_member_post($post)) {
            return '';
        }
        $post = clone $post;
        if (! apply_filters('feuer_mannschaft_field_visible', true, 'content', $post_id)) {
            $post->post_content = '';
        }

        $settings        = $this->get_settings();
        $single_template = $this->resolve_single_template_slug($preset_template);
        $meta            = $this->get_member_meta($post_id);
        $gallery         = $this->get_member_gallery($post_id);
        if (! apply_filters('feuer_mannschaft_field_visible', true, 'gallery', $post_id)) {
            $gallery = array();
        }
        $nav             = $this->get_adjacent_members($post_id);
        $back_url        = $this->get_member_back_url($post_id);
        $name            = $this->get_member_display_name($post_id);
        $has_slider      = (int) $settings['enable_gallery'] === 1 && count($gallery) > 1;
        $media_style     = esc_attr(sprintf('--feuer-mannschaft-single-height: %dpx;', (int) $settings['single_image_height']));
        $related_ids     = $this->get_related_member_ids($post_id, 4);
        $detail_rows     = $this->get_single_detail_rows($post_id);
        $contact_buttons_markup = $this->get_member_contact_buttons_markup($post_id, $settings);
        $admin_notice_markup    = $this->get_member_admin_notice_markup($post_id);

        return $this->render_template_file(
            'single',
            $single_template,
            array(
                'settings'        => $settings,
                'single_template' => $single_template,
                'post'            => $post,
                'post_id'         => $post_id,
                'meta'            => $meta,
                'gallery'         => $gallery,
                'nav'             => $nav,
                'back_url'        => $back_url,
                'name'            => $name,
                'has_slider'      => $has_slider,
                'media_style'     => $media_style,
                'related_ids'     => $related_ids,
                'detail_rows'     => $detail_rows,
                'contact_buttons_markup' => $contact_buttons_markup,
                'admin_notice_markup'    => $admin_notice_markup,
                'title_class'     => $this->get_member_title_class($post_id),
                'title_style'     => $this->get_member_title_style($post_id),
            )
        );
    }

    private function get_archive_card_markup($post_id, $display_mode, $single_template = '') {
        $meta        = $this->get_member_meta($post_id);
        $name        = $this->get_member_display_name($post_id);
        $description = $meta['description'] ? wp_trim_words($meta['description'], 24, '...') : '';
        $modal_id    = 'feuerMannschaftModal-' . $post_id;
        $detail_rows = $this->get_single_detail_rows($post_id, 4);
        $function    = trim((string) $meta['function']);
        $profile_url = $this->get_member_permalink($post_id, $single_template);

        return $this->render_template_file(
            'uebersicht',
            'member-card',
            array(
                'post_id'      => $post_id,
                'meta'         => $meta,
                'name'         => $name,
                'description'  => $description,
                'modal_id'     => $modal_id,
                'detail_rows'  => $detail_rows,
                'function'     => $function,
                'profile_url'  => $profile_url,
                'display_mode' => $display_mode,
                'title_class'  => $this->get_member_title_class($post_id),
                'title_style'  => $this->get_member_title_style($post_id),
            )
        );
    }

    private function get_archive_row_markup($post_id, $single_template = '') {
        $meta         = $this->get_member_meta($post_id);
        $name         = $this->get_member_display_name($post_id);
        $detail_rows  = $this->get_single_detail_rows($post_id, 3);
        $description  = $meta['description'] ? wp_trim_words($meta['description'], 18, '...') : '';
        $profile_url  = $this->get_member_permalink($post_id, $single_template);
        $role_caption = $meta['function'] ? $meta['function'] : $meta['rank'];

        return $this->render_template_file(
            'uebersicht',
            'member-row',
            array(
                'post_id'      => $post_id,
                'name'         => $name,
                'detail_rows'  => $detail_rows,
                'description'  => $description,
                'profile_url'  => $profile_url,
                'role_caption' => $role_caption,
                'title_class'  => $this->get_member_title_class($post_id),
                'title_style'  => $this->get_member_title_style($post_id),
            )
        );
    }

    private function get_archive_grid_markup($post_id, $layout, $single_template = '') {
        if ('team-grid' === $layout) {
            return $this->get_member_team_card_markup($post_id, $single_template);
        }

        return $this->get_member_standard_card_markup($post_id, $single_template);
    }

    private function get_member_standard_card_markup($post_id, $single_template = '') {
        $meta         = $this->get_member_meta($post_id);
        $name         = $this->get_member_display_name($post_id);
        $profile_url  = $this->get_member_permalink($post_id, $single_template);
        $role_caption = $meta['function'];
        $rank_caption = $meta['rank'];

        return $this->render_frontend_template(
            'member-standard-card',
            array(
                'member_id'    => $post_id,
                'name'         => $name,
                'profile_url'  => $profile_url,
                'role_caption' => $role_caption,
                'rank_caption' => $rank_caption,
                'title_class'  => $this->get_member_title_class($post_id),
                'title_style'  => $this->get_member_title_style($post_id),
            )
        );
    }

    private function get_member_team_card_markup($post_id, $single_template = '') {
        $meta         = $this->get_member_meta($post_id);
        $name         = $this->get_member_display_name($post_id);
        $role_caption = $meta['function'];
        $rank_caption = $meta['rank'];
        $profile_url  = $this->get_member_permalink($post_id, $single_template);

        return $this->render_frontend_template(
            'member-team-card',
            array(
                'member_id'    => $post_id,
                'name'         => $name,
                'role_caption' => $role_caption,
                'rank_caption' => $rank_caption,
                'profile_url'  => $profile_url,
                'title_class'  => $this->get_member_title_class($post_id),
                'title_style'  => $this->get_member_title_style($post_id),
            )
        );
    }

    private function get_members_query($category_id = 0) {
        $args = array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'   => self::PROFILE_META,
                    'value' => '1',
                ),
            ),
            'meta_key'       => self::META_PREFIX . 'sort_order',
            'orderby'        => array(
                'meta_value_num' => 'ASC',
                'title'          => 'ASC',
            ),
            'order'          => 'ASC',
        );

        if ($category_id > 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy'         => 'category',
                    'field'            => 'term_id',
                    'terms'            => array((int) $category_id),
                    'include_children' => true,
                ),
            );
        }

        return new WP_Query($args);
    }

    private function get_members_query_by_ids($post_ids) {
        $post_ids = array_values(array_filter(array_map('absint', (array) $post_ids)));

        if (empty($post_ids)) {
            return new WP_Query(
                array(
                    'post_type'      => self::POST_TYPE,
                    'post_status'    => 'publish',
                    'posts_per_page' => 0,
                )
            );
        }

        return new WP_Query(
            array(
                'post_type'           => self::POST_TYPE,
                'post_status'         => 'publish',
                'posts_per_page'      => count($post_ids),
                'post__in'            => $post_ids,
                'orderby'             => 'post__in',
                'ignore_sticky_posts' => true,
                'meta_query'          => array(
                    array(
                        'key'   => self::PROFILE_META,
                        'value' => '1',
                    ),
                ),
            )
        );
    }

    private function get_filter_categories() {
        $allowed_ids = $this->get_allowed_category_ids_or_null();
        $choices     = $this->get_category_choices($allowed_ids);
        $terms       = array();

        foreach ($choices as $choice) {
            $terms[] = $choice['term'];
        }

        return $terms;
    }

    private function get_current_url_without_filter() {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
        $base_url    = home_url($request_uri);

        return remove_query_arg('feuer_mannschaft_category', $base_url);
    }

    private function map_legacy_display_to_template($display_mode) {
        switch (sanitize_key($display_mode)) {
            case 'modal':
                return 'cards-modal';
            case 'list':
                return 'list';
            default:
                return 'cards';
        }
    }

    private function get_requested_single_template() {
        if (! isset($_GET['feuer_mannschaft_single_template'])) {
            return '';
        }

        return sanitize_key(wp_unslash($_GET['feuer_mannschaft_single_template']));
    }

    private function resolve_single_template_slug($preset_template = '') {
        $settings = $this->get_settings();
        $preset   = sanitize_key($preset_template);

        if ('' === $preset) {
            $preset = $this->get_requested_single_template();
        }

        return $this->sanitize_template_slug('single', $preset, $settings['single_template']);
    }

    private function get_member_permalink($post_id, $single_template = '') {
        $custom_link = $this->get_invite_url($post_id);
        if ('' !== trim($custom_link)) {
            return $custom_link;
        }

        $url = get_permalink($post_id);

        if (! $url) {
            return '';
        }

        $template_slug = sanitize_key($single_template);
        if ('' === $template_slug) {
            return $url;
        }

        $template_slug = $this->sanitize_template_slug('single', $template_slug, '');
        if ('' === $template_slug) {
            return $url;
        }

        $settings         = $this->get_settings();
        $default_template = $this->sanitize_template_slug('single', $settings['single_template'], 'standard');

        if ($template_slug === $default_template || 'standard' === $template_slug) {
            return remove_query_arg('feuer_mannschaft_single_template', $url);
        }

        return add_query_arg('feuer_mannschaft_single_template', $template_slug, $url);
    }

    private function get_selected_members_markup($post_ids, $preset_template = '', $preset_single_template = '', $title = '', $description = '') {
        $post_ids = array_values(array_filter(array_map('absint', (array) $post_ids)));
        if (empty($post_ids)) {
            return '';
        }

        $settings         = $this->get_settings();
        $archive_template = $this->sanitize_template_slug('archive', $preset_template, $settings['archive_template']);
        $single_template  = $this->sanitize_template_slug('single', $preset_single_template, $settings['single_template']);
        $query            = $this->get_members_query_by_ids($post_ids);

        $output = $this->render_template_file(
            'archive',
            $archive_template,
            array(
                'settings'         => $settings,
                'card_display_classes' => $this->get_archive_card_display_classes($settings),
                'query'            => $query,
                'term'             => null,
                'category_id'      => 0,
                'title'            => $title,
                'description'      => $description,
                'archive_template' => $archive_template,
                'single_template'  => $single_template,
            )
        );

        wp_reset_postdata();

        return $output;
    }

    private function render_template_file($type, $slug, $context = array()) {
        $file = $this->get_template_file($type, $slug);
        if (! $file) {
            return '';
        }

        if (! empty($context)) {
            extract($context, EXTR_SKIP);
        }

        ob_start();
        include $file;

        return ob_get_clean();
    }

    private function render_admin_template($slug, $context = array()) {
        $file = $this->get_admin_template_file($slug);

        if (! $file) {
            return '';
        }

        if (! empty($context)) {
            extract($context, EXTR_SKIP);
        }

        ob_start();
        include $file;

        return ob_get_clean();
    }

    private function render_frontend_template($slug, $context = array()) {
        $file = $this->get_frontend_template_file($slug);

        if (! $file) {
            return '';
        }

        if (! empty($context)) {
            extract($context, EXTR_SKIP);
        }

        ob_start();
        include $file;

        return ob_get_clean();
    }

    public function render_mentions_block($post_id) {
        return $this->mentions->render_mentions_block($post_id);
    }

    private function get_admin_template_file($slug) {
        $slug = trim((string) $slug, '/');
        $file = FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/admin/' . $slug . '.php';

        return file_exists($file) ? $file : '';
    }

    private function get_frontend_template_file($slug) {
        $slug = trim((string) $slug, '/');
        $file = FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/frontend/' . $slug . '.php';

        return file_exists($file) ? $file : '';
    }

    private function get_settings_tabs() {
        return array(
            'general'  => 'Allgemein',
            'mentions' => 'Erwähnungen',
            'fields'   => 'Listenfelder',
            'archives' => 'Archive',
            'updates'  => 'Updates',
            'logs'     => 'Logs',
            'health'   => 'Diagnose',
            'media'    => 'Mediathek',
        );
    }
    private function get_settings_tab() {
        $tab  = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'general';
        $tabs = $this->get_settings_tabs();

        return isset($tabs[$tab]) ? $tab : 'general';
    }

    private function get_settings_notice() {
        if (! isset($_GET['feuer_mannschaft_settings_notice'])) {
            return array();
        }

        $notice  = sanitize_key(wp_unslash($_GET['feuer_mannschaft_settings_notice']));
        $message = isset($_GET['feuer_mannschaft_settings_message']) ? sanitize_text_field(rawurldecode(wp_unslash($_GET['feuer_mannschaft_settings_message']))) : '';
        $map     = array(
            'backup_created'          => array('type' => 'success', 'message' => 'Archiv wurde erstellt.'),
            'backup_deleted'          => array('type' => 'success', 'message' => 'Archiv wurde gelöscht.'),
            'backup_uploaded'         => array('type' => 'success', 'message' => 'Archiv wurde hochgeladen.'),
            'backup_restored'         => array('type' => 'success', 'message' => 'Archiv wurde wiederhergestellt.'),
            'backup_restored_partial' => array('type' => 'warning', 'message' => $message ? $message : 'Das Archiv wurde nur teilweise wiederhergestellt. Bitte prüfen Sie das Protokoll.'),
            'backup_missing'          => array('type' => 'error', 'message' => 'Das gewählte Archiv wurde nicht gefunden.'),
            'backup_upload_missing'   => array('type' => 'error', 'message' => 'Bitte zuerst eine ZIP-Datei auswählen.'),
            'backup_upload_invalid'   => array('type' => 'error', 'message' => 'Es werden nur gültige ZIP-Archive unterstützt.'),
            'backup_upload_too_large' => array('type' => 'error', 'message' => 'Das Archiv ist zu groß.'),
            'backup_upload_failed'    => array('type' => 'error', 'message' => 'Das Archiv konnte nicht hochgeladen werden.'),
            'backup_failed'           => array('type' => 'error', 'message' => $message ? $message : 'Die Archiv-Aktion konnte nicht abgeschlossen werden.'),
            'log_retention_saved'     => array('type' => 'success', 'message' => 'Protokoll-Aufbewahrung wurde gespeichert.'),
            'update_check_available'  => array('type' => 'success', 'message' => $message ? $message : 'Eine neue Plugin-Version ist verfuegbar.'),
            'update_check_current'    => array('type' => 'success', 'message' => $message ? $message : 'Das Plugin ist aktuell.'),
            'update_check_failed'     => array('type' => 'error', 'message' => $message ? $message : 'Die Update-Pruefung konnte nicht abgeschlossen werden.'),
        );

        return isset($map[$notice]) ? $map[$notice] : array();
    }
    private function get_public_post_type_choices() {
        $objects = get_post_types(
            array(
                'public' => true,
            ),
            'objects'
        );

        $choices = array();

        foreach ($objects as $post_type => $object) {
            if ('attachment' === $post_type) {
                continue;
            }

            $choices[$post_type] = ! empty($object->labels->singular_name)
                ? $object->labels->singular_name
                : $post_type;
        }

        asort($choices, SORT_NATURAL | SORT_FLAG_CASE);

        return $choices;
    }

    public function maybe_reset_member_field_values() {
        if (! is_admin() || ! current_user_can('manage_options') || get_option(self::FIELD_RESET_OPTION)) {
            return;
        }

        $member_ids = $this->get_member_post_ids(array('publish', 'draft', 'pending', 'future', 'private'));

        foreach ($member_ids as $member_id) {
            delete_post_meta($member_id, self::META_PREFIX . 'rank');
            delete_post_meta($member_id, self::META_PREFIX . 'function');
            delete_post_meta($member_id, self::META_PREFIX . 'education');
        }

        update_option(self::FIELD_RESET_OPTION, time(), false);

        if (! empty($member_ids)) {
            $this->logger->log(
                'field_values_reset',
                'settings',
                0,
                'Listenfelder',
                array(
                    array(
                        'message' => sprintf('Feldwerte in %d aktiven Profilen wurden einmalig geleert.', count($member_ids)),
                    ),
                )
            );
        }
    }

    public function maybe_sync_default_profile_images() {
        if (! is_admin() || ! current_user_can('manage_options') || get_option(self::DEFAULT_IMAGE_SYNC_OPTION)) {
            return;
        }

        $default_image_id = $this->get_default_profile_image_id();

        if ($default_image_id > 0) {
            $this->sync_default_profile_image_assignments(0, $default_image_id);
        }

        update_option(self::DEFAULT_IMAGE_SYNC_OPTION, time(), false);
    }

    public function maybe_sync_member_comments() {
        if (! is_admin() || ! current_user_can('manage_options') || get_option(self::COMMENTS_SYNC_OPTION)) {
            return;
        }

        $member_ids = $this->get_member_post_ids();
        $changed    = 0;

        foreach ($member_ids as $member_id) {
            $post = get_post($member_id);

            if (! $post instanceof WP_Post) {
                continue;
            }

            if ('closed' === $post->comment_status && 'closed' === $post->ping_status) {
                continue;
            }

            wp_update_post(
                array(
                    'ID'             => $member_id,
                    'comment_status' => 'closed',
                    'ping_status'    => 'closed',
                )
            );

            $changed++;
        }

        update_option(self::COMMENTS_SYNC_OPTION, time(), false);

        if ($changed > 0 && $this->logger instanceof Feuer_Mannschaft_Logger) {
            $this->logger->log(
                'member_comments_closed',
                'settings',
                0,
                'Mannschaft-Kommentare',
                array(
                    array(
                        'message' => sprintf('Kommentare und Pings wurden in %d Profilen einmalig deaktiviert.', $changed),
                    ),
                )
            );
        }
    }

    public function maybe_seed_default_field_options() {
        $this->maybe_create_default_settings_option();

        $saved        = get_option(self::OPTION_KEY, array());
        $saved        = is_array($saved) ? $saved : array();
        $defaults     = $this->get_default_field_option_values();
        $field_groups = $this->get_fixed_field_option_groups();
        $changed      = array();

        foreach ($defaults as $setting_key => $values) {
            $current = isset($saved[$setting_key])
                ? $this->sanitize_setting_option_entries($saved[$setting_key])
                : array();

            if (! empty($current)) {
                continue;
            }

            $saved[$setting_key] = $values;
            $changed[] = isset($field_groups[$setting_key]['label']) ? $field_groups[$setting_key]['label'] : $setting_key;
        }

        if (! isset($saved['custom_field_groups']) || ! is_array($saved['custom_field_groups'])) {
            $saved['custom_field_groups'] = array();
        }

        if (! empty($changed)) {
            update_option(self::OPTION_KEY, wp_parse_args($saved, $this->get_default_settings()), false);

            if ($this->logger instanceof Feuer_Mannschaft_Logger) {
                $this->logger->log(
                    'settings_updated',
                    'settings',
                    0,
                    'Mannschaft-Einstellungen',
                    array(
                        array(
                            'message' => sprintf(
                                'Standardwerte für %s wurden automatisch ergänzt.',
                                implode(', ', $changed)
                            ),
                        ),
                    )
                );
            }
        }

        update_option(self::FIELD_DEFAULTS_OPTION, time(), false);
    }

    private function get_legacy_default_field_option_values() {
        return array(
            'rank_options' => array(
                'Feuerwehrmannanwärter',
                array('label' => 'Feuerwehrmann', 'url' => ''),
                'Oberfeuerwehrmann',
                'Hauptfeuerwehrmann',
                'Brandmeister',
                'Oberbrandmeister',
                'Hauptbrandmeister',
                'Erster Hauptbrandmeister',
                'Brandinspektor',
            ),
            'function_options' => array(
                'Wehrführer',
                'Wehrführer-Vertreter',
                'Jugendfeuerwehrwart',
                'Jugendfeuerwehrwartvertreter',
                'Bekleidungswart',
            ),
            'education_options' => array(
                'Zugführer',
                'Gruppenführer',
                'Maschinist',
                'Truppführer',
                'Atemschutzgeräteträger',
            ),
        );
    }
    private function get_legacy_field_option_groups() {
        return array(
            'rank_options' => array(
                'label'       => 'Dienstgrad',
                'description' => 'Jede Zeile ist ein eigener Dienstgrad. Im Profil kann immer nur ein Dienstgrad aktiv sein.',
                'meta_key'    => 'rank',
                'multiple'    => false,
                'add_label'   => 'Dienstgrad hinzufügen',
            ),
            'function_options' => array(
                'label'       => 'Funktion',
                'description' => 'Jede Zeile ist eine eigene Funktion. Im Profil können mehrere Funktionen per Switch aktiviert werden.',
                'meta_key'    => 'function',
                'multiple'    => true,
                'add_label'   => 'Funktion hinzufügen',
            ),
            'education_options' => array(
                'label'       => 'Ausbildung',
                'description' => 'Jede Zeile ist eine eigene Ausbildung. Im Profil können mehrere Ausbildungen per Switch aktiviert werden.',
                'meta_key'    => 'education',
                'multiple'    => true,
                'add_label'   => 'Ausbildung hinzufügen',
            ),
        );
    }
    private function legacy_sanitize_field_settings($input, $previous, $sanitized, $defaults) {
        $field_groups      = $this->get_field_option_groups();
        $requested         = array();
        $raw_replacements  = isset($input['pending_replacements']) ? wp_unslash($input['pending_replacements']) : array();

        foreach ($field_groups as $setting_key => $group) {
            unset($group);
            $requested[$setting_key] = $this->sanitize_setting_text_list(
                isset($input[$setting_key]) ? wp_unslash($input[$setting_key]) : $defaults[$setting_key]
            );
        }

        $conflicts     = $this->collect_removed_field_conflicts($previous, $requested);
        $replacements  = $this->sanitize_field_replacement_input($raw_replacements);

        if (! empty($conflicts)) {
            $missing_replacements = array();

            foreach ($conflicts as $setting_key => $items) {
                foreach ($items as $removed_value => $details) {
                    $replacement = isset($replacements[$setting_key][$removed_value]) ? $replacements[$setting_key][$removed_value] : '';
                    $is_valid    = '' !== $replacement
                        && $replacement !== $removed_value
                        && in_array($replacement, $requested[$setting_key], true);

                    if (! $is_valid) {
                        $missing_replacements[$setting_key][$removed_value] = $details;
                    }
                }
            }

            if (! empty($missing_replacements)) {
                $this->set_pending_field_replacements(
                    array(
                        'requested'    => $requested,
                        'conflicts'    => $conflicts,
                        'replacements' => $replacements,
                    )
                );

                add_settings_error(
                    self::OPTION_KEY,
                    'feuer_mannschaft_field_replacements_required',
                    'Bitte wählen Sie für entfernte und bereits verwendete Einträge eine Ersetzung aus.',
                    'error'
                );

                return $sanitized;
            }

            $this->apply_field_option_replacements($conflicts, $replacements);
        }

        $this->clear_pending_field_replacements();

        foreach ($requested as $setting_key => $values) {
            $sanitized[$setting_key] = $values;
        }

        return $sanitized;
    }

    private function legacy_sanitize_field_replacement_input($replacements) {
        $field_groups = $this->get_field_option_groups();
        $sanitized    = array();

        if (! is_array($replacements)) {
            return $sanitized;
        }

        foreach ($field_groups as $setting_key => $group) {
            unset($group);
            if (empty($replacements[$setting_key]) || ! is_array($replacements[$setting_key])) {
                continue;
            }

            foreach ($replacements[$setting_key] as $old_value => $new_value) {
                $old_value = sanitize_text_field((string) $old_value);
                $new_value = sanitize_text_field((string) $new_value);

                if ('' === $old_value) {
                    continue;
                }

                $sanitized[$setting_key][$old_value] = $new_value;
            }
        }

        return $sanitized;
    }

    private function legacy_collect_removed_field_conflicts($previous, $requested) {
        $field_groups = $this->get_field_option_groups();
        $member_ids   = $this->get_member_post_ids();
        $conflicts    = array();

        if (empty($member_ids)) {
            return $conflicts;
        }

        foreach ($field_groups as $setting_key => $group) {
            $previous_values = isset($previous[$setting_key]) ? (array) $previous[$setting_key] : array();
            $requested_values = isset($requested[$setting_key]) ? (array) $requested[$setting_key] : array();
            $removed_values   = array_values(array_diff($previous_values, $requested_values));

            if (empty($removed_values)) {
                continue;
            }

            foreach ($member_ids as $member_id) {
                $meta         = $this->get_member_meta($member_id);
                $member_name  = $this->get_member_display_name($member_id);
                $member_values = $this->get_member_field_values_by_group($meta, $setting_key);

                foreach ($removed_values as $removed_value) {
                    if (! in_array($removed_value, $member_values, true)) {
                        continue;
                    }

                    if (! isset($conflicts[$setting_key][$removed_value])) {
                        $conflicts[$setting_key][$removed_value] = array(
                            'count'   => 0,
                            'members' => array(),
                        );
                    }

                    $conflicts[$setting_key][$removed_value]['count']++;
                    $conflicts[$setting_key][$removed_value]['members'][] = array(
                        'id'   => $member_id,
                        'name' => $member_name,
                    );
                }
            }
        }

        return $conflicts;
    }

    private function legacy_apply_field_option_replacements($conflicts, $replacements) {
        $field_groups = $this->get_field_option_groups();
        $member_ids   = $this->get_member_post_ids();

        if (empty($member_ids) || empty($conflicts)) {
            return;
        }

        foreach ($member_ids as $member_id) {
            $before_meta = $this->get_member_meta($member_id);
            $after_meta  = $before_meta;
            $has_changes = false;

            foreach ($field_groups as $setting_key => $group) {
                if (empty($conflicts[$setting_key]) || empty($replacements[$setting_key])) {
                    continue;
                }

                $current_values = $this->get_member_field_values_by_group($after_meta, $setting_key);

                if (empty($current_values)) {
                    continue;
                }

                if (! empty($group['multiple'])) {
                    $updated_values = $current_values;

                    foreach ($conflicts[$setting_key] as $removed_value => $details) {
                        unset($details);
                        if (! in_array($removed_value, $updated_values, true)) {
                            continue;
                        }

                        $replacement = isset($replacements[$setting_key][$removed_value]) ? $replacements[$setting_key][$removed_value] : '';
                        if ('' === $replacement) {
                            continue;
                        }

                        $updated_values = array_map(
                            function ($item) use ($removed_value, $replacement) {
                                return $removed_value === $item ? $replacement : $item;
                            },
                            $updated_values
                        );
                    }

                    $updated_values = array_values(array_unique(array_filter($updated_values)));

                    if ($updated_values !== $current_values) {
                        $after_meta = $this->set_member_field_values_by_group($after_meta, $setting_key, $updated_values);
                        $has_changes = true;
                    }
                } else {
                    $current_value = isset($current_values[0]) ? $current_values[0] : '';

                    if ('' === $current_value || ! isset($replacements[$setting_key][$current_value])) {
                        continue;
                    }

                    $replacement = $replacements[$setting_key][$current_value];
                    if ('' === $replacement || $replacement === $current_value) {
                        continue;
                    }

                    $after_meta   = $this->set_member_field_values_by_group($after_meta, $setting_key, array($replacement));
                    $has_changes  = true;
                }
            }

            if (! $has_changes) {
                continue;
            }

            $this->persist_member_choice_meta($member_id, 'rank', isset($after_meta['rank']) ? $after_meta['rank'] : '');
            $this->persist_member_choice_meta($member_id, 'function', isset($after_meta['function_values']) ? $after_meta['function_values'] : array());
            $this->persist_member_choice_meta($member_id, 'education', isset($after_meta['education_values']) ? $after_meta['education_values'] : array());

            $changes = $this->build_member_log_changes($before_meta, $this->get_member_meta($member_id));

            if (! empty($changes)) {
                $this->logger->log(
                    'member_updated',
                    'member',
                    $member_id,
                    $this->get_member_display_name($member_id),
                    $changes
                );
            }
        }
    }

    private function legacy_get_member_field_values_by_group($meta, $setting_key) {
        switch ($setting_key) {
            case 'rank_options':
                $value = isset($meta['rank']) ? trim((string) $meta['rank']) : '';
                return '' === $value ? array() : array($value);

            case 'function_options':
                return isset($meta['function_values']) && is_array($meta['function_values'])
                    ? array_values(array_filter($meta['function_values']))
                    : array();

            case 'education_options':
                return isset($meta['education_values']) && is_array($meta['education_values'])
                    ? array_values(array_filter($meta['education_values']))
                    : array();
        }

        return array();
    }

    private function legacy_set_member_field_values_by_group($meta, $setting_key, $values) {
        $values = array_values(array_unique(array_filter(array_map('sanitize_text_field', (array) $values))));

        switch ($setting_key) {
            case 'rank_options':
                $meta['rank'] = isset($values[0]) ? $values[0] : '';
                break;

            case 'function_options':
                $meta['function_values'] = $values;
                $meta['function'] = implode(', ', $values);
                break;

            case 'education_options':
                $meta['education_values'] = $values;
                $meta['education'] = implode(', ', $values);
                break;
        }

        return $meta;
    }

    private function get_default_field_option_values() {
        return array(
            'rank_options' => array(
                array('label' => 'Feuerwehrmannanwärter', 'url' => ''),
                array('label' => 'Feuerwehrfrauanwärterin', 'url' => ''),
                array('label' => 'Feuerwehrmann', 'url' => ''),
                array('label' => 'Feuerwehrfrau', 'url' => ''),
                array('label' => 'Oberfeuerwehrmann', 'url' => ''),
                array('label' => 'Oberfeuerwehrfrau', 'url' => ''),
                array('label' => 'Brandmeister', 'url' => ''),
                array('label' => 'Oberbrandmeister', 'url' => ''),
                array('label' => 'Hauptbrandmeister', 'url' => ''),
                array('label' => 'Erster Hauptbrandmeister', 'url' => ''),
                array('label' => 'Brandinspektor', 'url' => ''),
            ),
            'function_options' => array(
                array('label' => 'Wehrführer', 'url' => ''),
                array('label' => 'Wehrführer-Vertreter', 'url' => ''),
                array('label' => 'Jugendfeuerwehrwart', 'url' => ''),
                array('label' => 'Jugendfeuerwehrwartvertreter', 'url' => ''),
                array('label' => 'Minifeuerwehrwart', 'url' => ''),
            ),
            'education_options' => array(
                array('label' => 'Zugführer', 'url' => ''),
                array('label' => 'Gruppenführer', 'url' => ''),
                array('label' => 'Maschinist', 'url' => ''),
                array('label' => 'Truppführer', 'url' => ''),
                array('label' => 'Atemschutzgeräteträger', 'url' => ''),
            ),
        );
    }

    private function get_fixed_field_option_groups() {
        return array(
            'rank_options' => array(
                'id'          => 'rank_options',
                'source'      => 'fixed',
                'setting_key' => 'rank_options',
                'label'       => 'Dienstgrad',
                'description' => 'Jede Zeile ist ein eigener Dienstgrad. Im Profil kann immer nur ein Dienstgrad aktiv sein.',
                'meta_key'    => 'rank',
                'multiple'    => false,
                'add_label'   => 'Dienstgrad hinzufügen',
            ),
            'function_options' => array(
                'id'          => 'function_options',
                'source'      => 'fixed',
                'setting_key' => 'function_options',
                'label'       => 'Funktion',
                'description' => 'Jede Zeile ist eine eigene Funktion. Im Profil können mehrere Funktionen per Switch aktiviert werden.',
                'meta_key'    => 'function',
                'multiple'    => true,
                'add_label'   => 'Funktion hinzufügen',
            ),
            'education_options' => array(
                'id'          => 'education_options',
                'source'      => 'fixed',
                'setting_key' => 'education_options',
                'label'       => 'Ausbildung',
                'description' => 'Jede Zeile ist eine eigene Ausbildung. Im Profil können mehrere Ausbildungen per Switch aktiviert werden.',
                'meta_key'    => 'education',
                'multiple'    => true,
                'add_label'   => 'Ausbildung hinzufügen',
            ),
        );
    }

    private function get_contact_field_definitions() {
        return array(
            'email' => array(
                'label'        => 'Email',
                'button_label' => 'Email',
                'description'  => 'Gueltige Email-Adresse des Mitglieds.',
                'placeholder'  => 'name@example.de',
            ),
            'instagram' => array(
                'label'        => 'Instagram',
                'button_label' => 'Instagram',
                'description'  => 'Nur den Login ohne URL eintragen.',
                'placeholder'  => 'feuerwehr.mustermann',
            ),
            'facebook' => array(
                'label'        => 'Facebook',
                'button_label' => 'Facebook',
                'description'  => 'Nur den Login ohne URL eintragen.',
                'placeholder'  => 'feuerwehr.mustermann',
            ),
            'whatsapp' => array(
                'label'        => 'WhatsApp',
                'button_label' => 'WhatsApp',
                'description'  => 'Telefonnummer oder Gruppenlink.',
                'placeholder'  => '+491701234567 oder https://chat.whatsapp.com/...',
            ),
            'telegram' => array(
                'label'        => 'Telegram',
                'button_label' => 'Telegram',
                'description'  => 'Login, Kanal oder Bot ohne URL.',
                'placeholder'  => 'feuerwehr_bot',
            ),
            'link' => array(
                'label'           => 'Link',
                'button_label'    => 'Link',
                'description'     => 'Eigener Button mit Name und URL.',
                'placeholder'     => 'Mitglied werden',
                'url_placeholder' => 'https://example.de/',
            ),
        );
    }

    private function get_contact_field_settings($settings = null) {
        $definitions = $this->get_contact_field_definitions();
        $defaults    = array_fill_keys(array_keys($definitions), 0);

        if (is_array($settings) && isset($settings['contact_fields'])) {
            $settings = $settings['contact_fields'];
        }

        if (! is_array($settings)) {
            $settings = array();
        }

        $sanitized = wp_parse_args($settings, $defaults);

        foreach ($defaults as $field_key => $default_value) {
            unset($default_value);
            $sanitized[$field_key] = empty($sanitized[$field_key]) ? 0 : 1;
        }

        return $sanitized;
    }

    private function sanitize_contact_field_settings($value) {
        return $this->get_contact_field_settings(
            array(
                'contact_fields' => is_array($value) ? $value : array(),
            )
        );
    }

    private function get_enabled_contact_field_definitions($settings = null) {
        if (! is_array($settings)) {
            $settings = $this->get_settings();
        }

        $definitions = $this->get_contact_field_definitions();
        $enabled     = $this->get_contact_field_settings($settings);
        $result      = array();

        foreach ($definitions as $field_key => $definition) {
            if (empty($enabled[$field_key])) {
                continue;
            }

            $result[$field_key] = $definition;
        }

        return $result;
    }

    private function get_field_option_groups($settings = null) {
        $settings = is_array($settings) ? $settings : $this->get_settings();
        $groups   = array();

        foreach ($this->get_fixed_field_option_groups() as $group_id => $group) {
            $group['options']  = $this->sanitize_setting_option_entries(isset($settings[$group_id]) ? $settings[$group_id] : array());
            $groups[$group_id] = $group;
        }

        $custom_groups = $this->sanitize_custom_field_groups(isset($settings['custom_field_groups']) ? $settings['custom_field_groups'] : array());

        foreach ($custom_groups as $custom_group) {
            $group_id = 'custom_field_group:' . $custom_group['key'];

            $groups[$group_id] = array(
                'id'          => $group_id,
                'source'      => 'custom',
                'setting_key' => 'custom_field_groups',
                'custom_key'  => $custom_group['key'],
                'label'       => $custom_group['label'],
                'description' => $custom_group['description'],
                'meta_key'    => 'custom_fields',
                'multiple'    => ! empty($custom_group['multiple']),
                'add_label'   => 'Eintrag hinzufügen',
                'options'     => $custom_group['options'],
            );
        }

        return $groups;
    }

    private function get_requested_field_settings($input, $previous, $defaults) {
        $requested = array(
            'custom_field_groups' => $this->sanitize_custom_field_groups(
                isset($input['custom_field_groups'])
                    ? wp_unslash($input['custom_field_groups'])
                    : (isset($previous['custom_field_groups']) ? $previous['custom_field_groups'] : array())
            ),
            'contact_fields' => $this->sanitize_contact_field_settings(
                isset($input['contact_fields'])
                    ? wp_unslash($input['contact_fields'])
                    : array()
            ),
        );

        foreach ($this->get_fixed_field_option_groups() as $setting_key => $group) {
            unset($group);

            $requested[$setting_key] = $this->sanitize_setting_option_entries(
                isset($input[$setting_key])
                    ? wp_unslash($input[$setting_key])
                    : (isset($defaults[$setting_key]) ? $defaults[$setting_key] : array())
            );
        }

        return $requested;
    }

    private function get_field_group_option_labels($group) {
        $options = isset($group['options']) && is_array($group['options']) ? $group['options'] : array();

        return array_values(
            array_filter(
                array_map(
                    function ($entry) {
                        return isset($entry['label']) ? trim((string) $entry['label']) : '';
                    },
                    $options
                )
            )
        );
    }

    private function sanitize_field_settings($input, $previous, $sanitized, $defaults) {
        $requested_settings = $this->get_requested_field_settings($input, $previous, $defaults);
        $previous_groups    = $this->get_field_option_groups($previous);
        $requested_groups   = $this->get_field_option_groups(wp_parse_args($requested_settings, $previous));
        $raw_replacements   = isset($input['pending_replacements']) ? wp_unslash($input['pending_replacements']) : array();
        $group_conflicts    = $this->collect_removed_custom_group_conflicts($previous_groups, $requested_groups);
        $conflicts          = $this->collect_removed_field_conflicts($previous_groups, $requested_groups);
        $replacements       = $this->sanitize_field_replacement_input($raw_replacements, $previous_groups);

        if (! empty($group_conflicts)) {
            $labels = array();

            foreach ($group_conflicts as $group_conflict) {
                if (! empty($group_conflict['label'])) {
                    $labels[] = $group_conflict['label'];
                }
            }

            $this->set_pending_field_replacements(
                array(
                    'requested_settings' => $requested_settings,
                    'group_conflicts'    => $group_conflicts,
                    'conflicts'          => $conflicts,
                    'replacements'       => $replacements,
                )
            );

            add_settings_error(
                self::OPTION_KEY,
                'feuer_mannschaft_field_groups_in_use',
                sprintf(
                    'Die Zusatzfelder %s werden noch in Profilen verwendet und koennen deshalb nicht entfernt werden.',
                    implode(', ', $labels)
                ),
                'error'
            );

            return $sanitized;
        }

        if (! empty($conflicts)) {
            $missing_replacements = array();

            foreach ($conflicts as $group_id => $items) {
                foreach ($items as $removed_value => $details) {
                    $replacement = isset($replacements[$group_id][$removed_value]) ? $replacements[$group_id][$removed_value] : '';
                    $is_valid    = '' !== $replacement
                        && $replacement !== $removed_value
                        && isset($requested_groups[$group_id])
                        && in_array($replacement, $this->get_field_group_option_labels($requested_groups[$group_id]), true);

                    if (! $is_valid) {
                        $missing_replacements[$group_id][$removed_value] = $details;
                    }
                }
            }

            if (! empty($missing_replacements)) {
                $this->set_pending_field_replacements(
                    array(
                        'requested_settings' => $requested_settings,
                        'group_conflicts'    => $group_conflicts,
                        'conflicts'          => $conflicts,
                        'replacements'       => $replacements,
                    )
                );

                add_settings_error(
                    self::OPTION_KEY,
                    'feuer_mannschaft_field_replacements_required',
                    'Bitte waehlen Sie fuer entfernte und bereits verwendete Eintraege eine Ersetzung aus.',
                    'error'
                );

                return $sanitized;
            }

            $this->apply_field_option_replacements($conflicts, $replacements, $previous_groups);
        }

        $this->clear_pending_field_replacements();

        foreach ($requested_settings as $setting_key => $values) {
            $sanitized[$setting_key] = $values;
        }

        return $sanitized;
    }

    private function sanitize_field_replacement_input($replacements, $field_groups) {
        $sanitized = array();

        if (! is_array($replacements)) {
            return $sanitized;
        }

        foreach ($field_groups as $group_id => $group) {
            unset($group);

            if (empty($replacements[$group_id]) || ! is_array($replacements[$group_id])) {
                continue;
            }

            foreach ($replacements[$group_id] as $old_value => $new_value) {
                $old_value = sanitize_text_field((string) $old_value);
                $new_value = sanitize_text_field((string) $new_value);

                if ('' === $old_value) {
                    continue;
                }

                $sanitized[$group_id][$old_value] = $new_value;
            }
        }

        return $sanitized;
    }

    private function collect_removed_custom_group_conflicts($previous_groups, $requested_groups) {
        $member_ids = $this->get_member_post_ids();
        $conflicts  = array();

        if (empty($member_ids)) {
            return $conflicts;
        }

        foreach ($previous_groups as $group_id => $group) {
            if ('custom' !== (isset($group['source']) ? $group['source'] : '') || isset($requested_groups[$group_id])) {
                continue;
            }

            foreach ($member_ids as $member_id) {
                $meta          = $this->get_member_meta($member_id);
                $member_values = $this->get_member_field_values_by_group($meta, $group);

                if (empty($member_values)) {
                    continue;
                }

                if (! isset($conflicts[$group_id])) {
                    $conflicts[$group_id] = array(
                        'label'   => isset($group['label']) ? $group['label'] : $group_id,
                        'count'   => 0,
                        'members' => array(),
                    );
                }

                $conflicts[$group_id]['count']++;
                $conflicts[$group_id]['members'][] = array(
                    'id'   => $member_id,
                    'name' => $this->get_member_display_name($member_id),
                );
            }
        }

        return $conflicts;
    }

    private function collect_removed_field_conflicts($previous_groups, $requested_groups) {
        $member_ids = $this->get_member_post_ids();
        $conflicts  = array();

        if (empty($member_ids)) {
            return $conflicts;
        }

        foreach ($previous_groups as $group_id => $group) {
            if (! isset($requested_groups[$group_id])) {
                continue;
            }

            $previous_values = $this->get_field_group_option_labels($group);
            $requested_values = $this->get_field_group_option_labels($requested_groups[$group_id]);
            $removed_values   = array_values(array_diff($previous_values, $requested_values));

            if (empty($removed_values)) {
                continue;
            }

            foreach ($member_ids as $member_id) {
                $meta          = $this->get_member_meta($member_id);
                $member_name   = $this->get_member_display_name($member_id);
                $member_values = $this->get_member_field_values_by_group($meta, $group);

                foreach ($removed_values as $removed_value) {
                    if (! in_array($removed_value, $member_values, true)) {
                        continue;
                    }

                    if (! isset($conflicts[$group_id][$removed_value])) {
                        $conflicts[$group_id][$removed_value] = array(
                            'count'   => 0,
                            'members' => array(),
                        );
                    }

                    $conflicts[$group_id][$removed_value]['count']++;
                    $conflicts[$group_id][$removed_value]['members'][] = array(
                        'id'   => $member_id,
                        'name' => $member_name,
                    );
                }
            }
        }

        return $conflicts;
    }

    private function apply_field_option_replacements($conflicts, $replacements, $field_groups) {
        $member_ids = $this->get_member_post_ids();

        if (empty($member_ids) || empty($conflicts)) {
            return;
        }

        foreach ($member_ids as $member_id) {
            $before_meta = $this->get_member_meta($member_id);
            $after_meta  = $before_meta;
            $has_changes = false;

            foreach ($field_groups as $group_id => $group) {
                if (empty($conflicts[$group_id]) || empty($replacements[$group_id])) {
                    continue;
                }

                $current_values = $this->get_member_field_values_by_group($after_meta, $group);

                if (empty($current_values)) {
                    continue;
                }

                if (! empty($group['multiple'])) {
                    $updated_values = $current_values;

                    foreach ($conflicts[$group_id] as $removed_value => $details) {
                        unset($details);

                        if (! in_array($removed_value, $updated_values, true)) {
                            continue;
                        }

                        $replacement = isset($replacements[$group_id][$removed_value]) ? $replacements[$group_id][$removed_value] : '';

                        if ('' === $replacement) {
                            continue;
                        }

                        $updated_values = array_map(
                            function ($item) use ($removed_value, $replacement) {
                                return $removed_value === $item ? $replacement : $item;
                            },
                            $updated_values
                        );
                    }

                    $updated_values = array_values(array_unique(array_filter($updated_values)));

                    if ($updated_values !== $current_values) {
                        $after_meta  = $this->set_member_field_values_by_group($after_meta, $group, $updated_values);
                        $has_changes = true;
                    }
                } else {
                    $current_value = isset($current_values[0]) ? $current_values[0] : '';

                    if ('' === $current_value || ! isset($replacements[$group_id][$current_value])) {
                        continue;
                    }

                    $replacement = $replacements[$group_id][$current_value];

                    if ('' === $replacement || $replacement === $current_value) {
                        continue;
                    }

                    $after_meta  = $this->set_member_field_values_by_group($after_meta, $group, array($replacement));
                    $has_changes = true;
                }
            }

            if (! $has_changes) {
                continue;
            }

            $this->persist_member_choice_meta($member_id, 'rank', isset($after_meta['rank']) ? $after_meta['rank'] : '');
            $this->persist_member_choice_meta($member_id, 'function', isset($after_meta['function_values']) ? $after_meta['function_values'] : array());
            $this->persist_member_choice_meta($member_id, 'education', isset($after_meta['education_values']) ? $after_meta['education_values'] : array());
            $this->persist_member_custom_fields_meta($member_id, isset($after_meta['custom_fields']) ? $after_meta['custom_fields'] : array());

            $changes = $this->build_member_log_changes($before_meta, $this->get_member_meta($member_id));

            if (! empty($changes)) {
                $this->logger->log(
                    'member_updated',
                    'member',
                    $member_id,
                    $this->get_member_display_name($member_id),
                    $changes
                );
            }
        }
    }

    private function get_member_field_values_by_group($meta, $group) {
        $group    = is_array($group) ? $group : array();
        $group_id = isset($group['id']) ? $group['id'] : '';

        switch ($group_id) {
            case 'rank_options':
                $value = isset($meta['rank']) ? trim((string) $meta['rank']) : '';
                return '' === $value ? array() : array($value);

            case 'function_options':
                return isset($meta['function_values']) && is_array($meta['function_values'])
                    ? array_values(array_filter($meta['function_values']))
                    : array();

            case 'education_options':
                return isset($meta['education_values']) && is_array($meta['education_values'])
                    ? array_values(array_filter($meta['education_values']))
                    : array();
        }

        if ('custom' === (isset($group['source']) ? $group['source'] : '')) {
            $custom_key    = isset($group['custom_key']) ? $group['custom_key'] : '';
            $custom_fields = isset($meta['custom_fields']) && is_array($meta['custom_fields']) ? $meta['custom_fields'] : array();

            return isset($custom_fields[$custom_key]) && is_array($custom_fields[$custom_key])
                ? array_values(array_filter($custom_fields[$custom_key]))
                : array();
        }

        return array();
    }

    private function set_member_field_values_by_group($meta, $group, $values) {
        $values   = array_values(array_unique(array_filter(array_map('sanitize_text_field', (array) $values))));
        $group    = is_array($group) ? $group : array();
        $group_id = isset($group['id']) ? $group['id'] : '';

        switch ($group_id) {
            case 'rank_options':
                $meta['rank'] = isset($values[0]) ? $values[0] : '';
                break;

            case 'function_options':
                $meta['function_values'] = $values;
                $meta['function']        = implode(', ', $values);
                break;

            case 'education_options':
                $meta['education_values'] = $values;
                $meta['education']        = implode(', ', $values);
                break;
        }

        if ('custom' === (isset($group['source']) ? $group['source'] : '')) {
            $custom_key    = isset($group['custom_key']) ? $group['custom_key'] : '';
            $custom_fields = isset($meta['custom_fields']) && is_array($meta['custom_fields']) ? $meta['custom_fields'] : array();

            if ('' !== $custom_key) {
                if (empty($values)) {
                    unset($custom_fields[$custom_key]);
                } else {
                    $custom_fields[$custom_key] = $values;
                }
            }

            $meta['custom_fields'] = $custom_fields;
        }

        return $meta;
    }

    private function persist_member_choice_meta($post_id, $meta_key, $value) {
        $storage_key = self::META_PREFIX . $meta_key;

        if (is_array($value)) {
            $value = array_values(array_unique(array_filter(array_map('sanitize_text_field', $value))));

            if (empty($value)) {
                delete_post_meta($post_id, $storage_key);
                return;
            }

            update_post_meta($post_id, $storage_key, $value);
            return;
        }

        $value = sanitize_text_field((string) $value);

        if ('' === $value) {
            delete_post_meta($post_id, $storage_key);
            return;
        }

        update_post_meta($post_id, $storage_key, $value);
    }

    private function persist_member_custom_fields_meta($post_id, $value) {
        $storage_key = self::META_PREFIX . 'custom_fields';
        $value       = $this->sanitize_member_custom_fields_value($value);

        if (empty($value)) {
            delete_post_meta($post_id, $storage_key);
            return;
        }

        update_post_meta($post_id, $storage_key, $value);
    }

    private function persist_member_contact_fields_meta($post_id, $value) {
        $storage_key = self::META_PREFIX . 'contact_fields';
        $value       = $this->sanitize_member_contact_fields_value($value);

        if (empty($value)) {
            delete_post_meta($post_id, $storage_key);
            return;
        }

        update_post_meta($post_id, $storage_key, $value);
    }

    private function get_pending_field_replacements() {
        $pending = get_transient($this->get_pending_field_replacements_key());

        return is_array($pending) ? $pending : array();
    }

    private function set_pending_field_replacements($data) {
        set_transient($this->get_pending_field_replacements_key(), $data, HOUR_IN_SECONDS);
    }

    private function clear_pending_field_replacements() {
        delete_transient($this->get_pending_field_replacements_key());
    }

    private function get_pending_field_replacements_key() {
        return self::FIELD_PENDING_REPLACEMENTS_TRANSIENT . get_current_user_id();
    }

    private function get_member_post_ids($post_status = null) {
        $post_status = is_array($post_status) && ! empty($post_status)
            ? array_values(array_unique(array_map('sanitize_key', $post_status)))
            : array('publish', 'private', 'draft', 'pending', 'future', 'feuer_archived');

        $post_ids = get_posts(
            array(
                'post_type'      => self::POST_TYPE,
                'post_status'    => $post_status,
                'posts_per_page' => -1,
                'orderby'        => 'ID',
                'order'          => 'ASC',
                'fields'         => 'ids',
                'meta_query'     => array(
                    array(
                        'key'   => self::PROFILE_META,
                        'value' => '1',
                    ),
                ),
            )
        );

        return array_values(array_map('absint', (array) $post_ids));
    }

    private function sanitize_setting_text_list($values) {
        if (is_string($values)) {
            $values = preg_split('/\r\n|\r|\n/', $values);
        }

        if (! is_array($values)) {
            return array();
        }

        $values = array_map(
            function ($value) {
                return sanitize_text_field((string) $value);
            },
            $values
        );

        $values = array_values(array_unique(array_filter(array_map('trim', $values))));

        return $values;
    }

    private function sanitize_setting_option_entries($values) {
        if (is_string($values)) {
            $values = preg_split('/\r\n|\r|\n/', $values);
        }

        if (! is_array($values)) {
            return array();
        }

        $sanitized = array();
        $seen      = array();

        foreach ($values as $entry) {
            if (is_array($entry)) {
                $label = isset($entry['label']) ? sanitize_text_field((string) $entry['label']) : '';
                $url   = isset($entry['url']) ? $this->sanitize_invite_url($entry['url']) : '';
            } else {
                $label = sanitize_text_field((string) $entry);
                $url   = '';
            }

            $label = trim($label);

            if ('' === $label || isset($seen[$label])) {
                continue;
            }

            $seen[$label] = true;
            $sanitized[]  = array(
                'label' => $label,
                'url'   => $url,
            );
        }

        return $sanitized;
    }

    private function sanitize_custom_field_groups($groups) {
        if (! is_array($groups)) {
            return array();
        }

        $sanitized = array();
        $used_keys = array();

        foreach ($groups as $group) {
            if (! is_array($group)) {
                continue;
            }

            $label       = sanitize_text_field(isset($group['label']) ? (string) $group['label'] : '');
            $description = sanitize_textarea_field(isset($group['description']) ? (string) $group['description'] : '');
            $options     = $this->sanitize_setting_option_entries(isset($group['options']) ? $group['options'] : array());
            $multiple    = ! empty($group['multiple']);
            $raw_key     = isset($group['key']) ? sanitize_key((string) $group['key']) : '';

            if ('' === $label && empty($options)) {
                continue;
            }

            if ('' === $label) {
                $label = 'Zusatzfeld';
            }

            $key = $this->build_custom_field_group_key($raw_key, $label, $used_keys);
            $used_keys[] = $key;

            $sanitized[] = array(
                'key'         => $key,
                'label'       => $label,
                'description' => $description,
                'multiple'    => $multiple ? 1 : 0,
                'options'     => $options,
            );
        }

        return $sanitized;
    }

    private function build_custom_field_group_key($raw_key, $label, $used_keys) {
        $base_key = sanitize_key($raw_key);

        if ('' === $base_key) {
            $base_key = sanitize_key($label);
        }

        if ('' === $base_key) {
            $base_key = 'zusatzfeld';
        }

        $key     = $base_key;
        $counter = 2;

        while (in_array($key, $used_keys, true)) {
            $key = $base_key . '_' . $counter;
            $counter++;
        }

        return $key;
    }

    private function sanitize_setting_post_types($post_types) {
        $available = array_keys($this->get_public_post_type_choices());
        $post_types = array_values(array_unique(array_map('sanitize_key', (array) $post_types)));

        $post_types = array_values(
            array_filter(
                $post_types,
                function ($post_type) use ($available) {
                    return in_array($post_type, $available, true);
                }
            )
        );

        if (empty($post_types)) {
            $post_types[] = 'post';
        }

        return $post_types;
    }

    private function get_member_setting_options($setting_key) {
        $settings = $this->get_settings();

        $entries = isset($settings[$setting_key]) && is_array($settings[$setting_key])
            ? $this->sanitize_setting_option_entries($settings[$setting_key])
            : array();

        return array_values(
            array_filter(
                array_map(
                    function ($entry) {
                        return isset($entry['label']) ? trim((string) $entry['label']) : '';
                    },
                    $entries
                )
            )
        );
    }

    private function sanitize_member_single_select_value($value, $setting_key) {
        $options = $this->get_member_setting_options($setting_key);
        $value   = $this->sanitize_member_multi_select_values($value);
        $value   = isset($value[0]) ? $value[0] : '';

        if ('' === $value) {
            return '';
        }

        if (! empty($options) && ! in_array($value, $options, true)) {
            return $value;
        }

        return sanitize_text_field((string) $value);
    }

    private function sanitize_member_multi_select_values($values, $split_string_values = false) {
        if (is_string($values)) {
            $decoded = maybe_unserialize($values);

            if (is_array($decoded)) {
                $values = $decoded;
            } elseif ('' === trim($values)) {
                $values = array();
            } elseif ($split_string_values) {
                $values = preg_split('/\r\n|\r|\n|,/', $values);
            } else {
                $values = array($values);
            }
        }

        if (! is_array($values)) {
            $values = array();
        }

        $values = array_map(
            function ($value) {
                return sanitize_text_field((string) $value);
            },
            $values
        );

        return array_values(array_unique(array_filter(array_map('trim', $values))));
    }

    private function sanitize_member_education_values($values) {
        return $this->sanitize_member_multi_select_values($values, true);
    }

    private function normalize_member_education_meta($value) {
        return $this->sanitize_member_multi_select_values($value, true);
    }

    private function sanitize_member_custom_fields_value($value) {
        if (is_string($value)) {
            $decoded = maybe_unserialize($value);
            $value   = is_array($decoded) ? $decoded : array();
        }

        if (! is_array($value)) {
            return array();
        }

        $field_groups     = $this->get_field_option_groups();
        $custom_group_map = array();

        foreach ($field_groups as $group) {
            if ('custom' !== (isset($group['source']) ? $group['source'] : '')) {
                continue;
            }

            $custom_group_map[$group['custom_key']] = $group;
        }

        $sanitized = array();

        foreach ($value as $group_key => $group_values) {
            $group_key      = sanitize_key((string) $group_key);
            $selected_values = $this->sanitize_member_multi_select_values($group_values);

            if (isset($custom_group_map[$group_key]) && empty($custom_group_map[$group_key]['multiple'])) {
                $selected_values = isset($selected_values[0]) ? array($selected_values[0]) : array();
            }

            if (empty($selected_values)) {
                continue;
            }

            $sanitized[$group_key] = $selected_values;
        }

        return $sanitized;
    }

    private function sanitize_member_contact_fields_value($value) {
        if (is_string($value)) {
            $decoded = maybe_unserialize($value);
            $value   = is_array($decoded) ? $decoded : array();
        }

        if (! is_array($value)) {
            return array();
        }

        $sanitized = array();

        foreach ($this->get_contact_field_definitions() as $field_key => $definition) {
            unset($definition);

            if (! array_key_exists($field_key, $value)) {
                continue;
            }

            $raw_value = $value[$field_key];

            switch ($field_key) {
                case 'email':
                    $normalized = sanitize_email((string) $raw_value);
                    if ($normalized && is_email($normalized)) {
                        $sanitized[$field_key] = $normalized;
                    }
                    break;

                case 'instagram':
                case 'facebook':
                case 'telegram':
                    $normalized = $this->sanitize_contact_handle($raw_value);
                    if ('' !== $normalized) {
                        $sanitized[$field_key] = $normalized;
                    }
                    break;

                case 'whatsapp':
                    $normalized = $this->sanitize_whatsapp_contact_value($raw_value);
                    if ('' !== $normalized) {
                        $sanitized[$field_key] = $normalized;
                    }
                    break;

                case 'link':
                    $normalized = $this->sanitize_custom_contact_link_value($raw_value);
                    if (! empty($normalized['label']) && ! empty($normalized['url'])) {
                        $sanitized[$field_key] = $normalized;
                    }
                    break;
            }
        }

        return $sanitized;
    }

    private function sanitize_contact_handle($value) {
        $value = trim(wp_strip_all_tags((string) $value));

        if ('' === $value) {
            return '';
        }

        if (preg_match('#^(?:https?:)?//#i', $value)) {
            $path  = wp_parse_url($value, PHP_URL_PATH);
            $value = is_string($path) ? $path : '';
        }

        $value = trim((string) $value);

        if (false !== strpos($value, '/')) {
            $segments = array_values(array_filter(explode('/', trim($value, '/'))));
            $value    = empty($segments) ? '' : (string) end($segments);
        }

        if (false !== strpos($value, '?')) {
            $value = (string) strtok($value, '?');
        }

        if (false !== strpos($value, '#')) {
            $value = (string) strtok($value, '#');
        }

        $value = ltrim(trim($value), '@');
        $value = preg_replace('/[^A-Za-z0-9._-]/', '', (string) $value);

        return trim((string) $value);
    }

    private function sanitize_whatsapp_contact_value($value) {
        $value = trim(wp_strip_all_tags((string) $value));

        if ('' === $value) {
            return '';
        }

        if (preg_match('#^(?:wa\.me|chat\.whatsapp\.com)/#i', $value)) {
            $value = 'https://' . ltrim($value, '/');
        }

        if (
            preg_match('#^(?:https?:)?//wa\.me/#i', $value)
            || preg_match('#^(?:https?:)?//chat\.whatsapp\.com/#i', $value)
        ) {
            if (0 === strpos($value, '//')) {
                $value = 'https:' . $value;
            }

            return $this->sanitize_invite_url($value);
        }

        $normalized = preg_replace('/[^\d+]/', '', $value);

        return trim((string) $normalized);
    }

    private function sanitize_custom_contact_link_value($value) {
        if (is_string($value)) {
            $decoded = maybe_unserialize($value);
            $value   = is_array($decoded) ? $decoded : array();
        }

        if (! is_array($value)) {
            return array(
                'label' => '',
                'url'   => '',
            );
        }

        return array(
            'label' => isset($value['label']) ? sanitize_text_field((string) $value['label']) : '',
            'url'   => isset($value['url']) ? $this->sanitize_invite_url($value['url']) : '',
        );
    }

    private function build_member_log_changes($before_meta, $after_meta, $context = array()) {
        $changes = array();
        $labels  = array(
            'first_name' => 'Vorname',
            'last_name'  => 'Nachname',
            'entry_date' => 'Eintrittsjahr',
            'admin_note' => 'Administrator-Hinweis',
            'rank'       => 'Dienstgrad',
            'function'   => 'Funktion',
            'education'  => 'Ausbildung',
            'sort_order' => 'Position',
            'description'=> 'Beschreibung',
        );

        foreach ($labels as $key => $label) {
            $before = isset($before_meta[$key]) ? $before_meta[$key] : '';
            $after  = isset($after_meta[$key]) ? $after_meta[$key] : '';

            if (in_array($key, array('description', 'admin_note'), true)) {
                $before = wp_trim_words((string) $before, 12, '...');
                $after  = wp_trim_words((string) $after, 12, '...');
            }

            if ((string) $before !== (string) $after) {
                $changes[] = array(
                    'field'  => $label,
                    'before' => $this->stringify_log_value($before),
                    'after'  => $this->stringify_log_value($after),
                );
            }
        }

        foreach ($this->get_contact_field_definitions() as $field_key => $definition) {
            $before_value = $this->format_contact_field_value_for_log(
                $field_key,
                isset($before_meta['contact_fields'][$field_key]) ? $before_meta['contact_fields'][$field_key] : ''
            );
            $after_value = $this->format_contact_field_value_for_log(
                $field_key,
                isset($after_meta['contact_fields'][$field_key]) ? $after_meta['contact_fields'][$field_key] : ''
            );

            if ($before_value === $after_value) {
                continue;
            }

            $changes[] = array(
                'field'  => isset($definition['label']) ? $definition['label'] : $field_key,
                'before' => $before_value,
                'after'  => $after_value,
            );
        }

        $before_title = isset($context['before_title']) ? $context['before_title'] : '';
        $after_title  = isset($context['after_title']) ? $context['after_title'] : '';
        if ((string) $before_title !== (string) $after_title) {
            $changes[] = array(
                'field'  => 'Titel',
                'before' => $this->stringify_log_value($before_title),
                'after'  => $this->stringify_log_value($after_title),
            );
        }

        $before_invite_url = $this->stringify_log_value(isset($context['before_invite_url']) ? $context['before_invite_url'] : '');
        $after_invite_url  = $this->stringify_log_value(isset($context['after_invite_url']) ? $context['after_invite_url'] : '');
        if ($before_invite_url !== $after_invite_url) {
            $changes[] = array(
                'field'  => 'Eigener Link',
                'before' => $before_invite_url,
                'after'  => $after_invite_url,
            );
        }

        $before_exclude_from_count = ! empty($context['before_exclude_from_count']) ? 'ausgeschlossen' : 'mitgezählt';
        $after_exclude_from_count  = ! empty($context['after_exclude_from_count']) ? 'ausgeschlossen' : 'mitgezählt';
        if ($before_exclude_from_count !== $after_exclude_from_count) {
            $changes[] = array(
                'field'  => 'Teilnehmerzähler',
                'before' => $before_exclude_from_count,
                'after'  => $after_exclude_from_count,
            );
        }

        $before_category = $this->get_category_name_for_log(isset($context['before_category']) ? $context['before_category'] : 0);
        $after_category  = $this->get_category_name_for_log(isset($context['after_category']) ? $context['after_category'] : 0);
        if ($before_category !== $after_category) {
            $changes[] = array(
                'field'  => 'Kategorie',
                'before' => $before_category,
                'after'  => $after_category,
            );
        }

        $before_gallery = isset($context['before_gallery']) ? count((array) $context['before_gallery']) : 0;
        $after_gallery  = isset($context['after_gallery']) ? count((array) $context['after_gallery']) : 0;
        if ($before_gallery !== $after_gallery) {
            $changes[] = array(
                'field'  => 'Fotos',
                'before' => (string) $before_gallery,
                'after'  => (string) $after_gallery,
            );
        }

        foreach ($this->get_field_option_groups() as $group) {
            if ('custom' !== (isset($group['source']) ? $group['source'] : '')) {
                continue;
            }

            $custom_key    = isset($group['custom_key']) ? $group['custom_key'] : '';
            $before_values = isset($before_meta['custom_fields'][$custom_key]) ? (array) $before_meta['custom_fields'][$custom_key] : array();
            $after_values  = isset($after_meta['custom_fields'][$custom_key]) ? (array) $after_meta['custom_fields'][$custom_key] : array();

            if ($this->stringify_log_value($before_values) === $this->stringify_log_value($after_values)) {
                continue;
            }

            $changes[] = array(
                'field'  => isset($group['label']) ? $group['label'] : $custom_key,
                'before' => $this->stringify_log_value($before_values),
                'after'  => $this->stringify_log_value($after_values),
            );
        }

        return $changes;
    }

    private function build_settings_log_changes($before, $after) {
        $changes = array();
        $labels  = array(
            'watermark_text'          => 'Watermark',
            'allowed_categories'      => 'Freigegebene Kategorien',
            'archive_template'        => 'Listenvorlage',
            'single_template'         => 'Profilvorlage',
            'default_profile_image_id'=> 'Standard-Profilfoto',
            'card_image_width'        => 'Kartenbild Breite',
            'card_grayscale_strength' => 'Graustufen fuer Karten',
            'excluded_title_color'    => 'Farbe fuer ausgeschlossene Profilnamen',
            'enable_cache_purge'      => 'Cache nach Profilaenderungen automatisch leeren',
            'enable_automatic_updates' => 'Automatische Plugin-Updates',
            'card_image_height'       => 'Kartenbild Höhe',
            'single_image_height'     => 'Profilbild Höhe',
            'slider_autoplay'         => 'Slider Autoplay',
            'enable_gallery'          => 'Galerie',
            'enable_mentions'         => 'Erwähnungen',
            'mention_post_types'      => 'Beitragstypen für Erwähnungen',
            'rank_options'            => 'Dienstgrad-Liste',
            'function_options'        => 'Funktion-Liste',
            'education_options'       => 'Ausbildung-Liste',
            'contact_fields'          => 'Kontaktfelder',
            'custom_field_groups'     => 'Zusatzfelder',
        );

        foreach ($labels as $key => $label) {
            $before_value = isset($before[$key]) ? $before[$key] : '';
            $after_value  = isset($after[$key]) ? $after[$key] : '';

            if ('allowed_categories' === $key) {
                $before_value = array_map(array($this, 'get_category_name_for_log'), (array) $before_value);
                $after_value  = array_map(array($this, 'get_category_name_for_log'), (array) $after_value);
            } elseif ('mention_post_types' === $key) {
                $before_value = array_map(array($this, 'get_post_type_label_for_log'), (array) $before_value);
                $after_value  = array_map(array($this, 'get_post_type_label_for_log'), (array) $after_value);
            } elseif ('default_profile_image_id' === $key) {
                $before_value = $this->get_attachment_name_for_log($before_value);
                $after_value  = $this->get_attachment_name_for_log($after_value);
            } elseif (in_array($key, array('enable_gallery', 'enable_mentions', 'enable_cache_purge', 'enable_automatic_updates'), true)) {
                $before_value = ! empty($before_value) ? 'aktiv' : 'deaktiviert';
                $after_value  = ! empty($after_value) ? 'aktiv' : 'deaktiviert';
            } elseif (in_array($key, array('rank_options', 'function_options', 'education_options'), true)) {
                $before_value = $this->format_field_option_entries_for_log($before_value);
                $after_value  = $this->format_field_option_entries_for_log($after_value);
            } elseif ('contact_fields' === $key) {
                $before_value = $this->format_contact_field_settings_for_log($before_value);
                $after_value  = $this->format_contact_field_settings_for_log($after_value);
            } elseif ('custom_field_groups' === $key) {
                $before_value = $this->format_custom_field_groups_for_log($before_value);
                $after_value  = $this->format_custom_field_groups_for_log($after_value);
            }

            if ($this->stringify_log_value($before_value) !== $this->stringify_log_value($after_value)) {
                $changes[] = array(
                    'field'  => $label,
                    'before' => $this->stringify_log_value($before_value),
                    'after'  => $this->stringify_log_value($after_value),
                );
            }
        }

        return $changes;
    }

    private function format_field_option_entries_for_log($entries) {
        return array_values(
            array_filter(
                array_map(
                    function ($entry) {
                        if (is_array($entry)) {
                            $label = isset($entry['label']) ? trim((string) $entry['label']) : '';
                            $url   = isset($entry['url']) ? trim((string) $entry['url']) : '';

                            if ('' === $label) {
                                return '';
                            }

                            return '' !== $url ? $label . ' -> ' . $url : $label;
                        }

                        return trim((string) $entry);
                    },
                    (array) $entries
                )
            )
        );
    }

    private function format_custom_field_groups_for_log($groups) {
        $groups = $this->sanitize_custom_field_groups($groups);
        $lines  = array();

        foreach ($groups as $group) {
            $labels = $this->format_field_option_entries_for_log(isset($group['options']) ? $group['options'] : array());
            $mode   = ! empty($group['multiple']) ? 'Mehrfach' : 'Einzelauswahl';
            $line   = trim((string) $group['label']);

            if ('' === $line) {
                continue;
            }

            $line .= ' [' . $mode . ']';

            if (! empty($labels)) {
                $line .= ': ' . implode(', ', $labels);
            }

            $lines[] = $line;
        }

        return $lines;
    }

    private function format_contact_field_settings_for_log($settings) {
        $definitions = $this->get_contact_field_definitions();
        $settings    = $this->get_contact_field_settings(
            array(
                'contact_fields' => is_array($settings) ? $settings : array(),
            )
        );
        $labels = array();

        foreach ($settings as $field_key => $enabled) {
            if (empty($enabled) || empty($definitions[$field_key]['label'])) {
                continue;
            }

            $labels[] = $definitions[$field_key]['label'];
        }

        return $labels;
    }

    private function format_contact_field_value_for_log($field_key, $value) {
        $field_key = sanitize_key((string) $field_key);

        if ('link' === $field_key) {
            $value = $this->sanitize_custom_contact_link_value($value);

            if (empty($value['label']) && empty($value['url'])) {
                return '';
            }

            if (empty($value['url'])) {
                return (string) $value['label'];
            }

            return trim((string) $value['label']) . ' -> ' . trim((string) $value['url']);
        }

        $contact_values = $this->sanitize_member_contact_fields_value(
            array(
                $field_key => $value,
            )
        );

        if (empty($contact_values[$field_key])) {
            return '';
        }

        $value = $contact_values[$field_key];

        if (in_array($field_key, array('instagram', 'facebook', 'telegram'), true)) {
            return '@' . ltrim((string) $value, '@');
        }

        return trim((string) $value);
    }

    private function stringify_log_value($value) {
        if (is_array($value)) {
            $value = array_values(
                array_filter(
                    array_map(
                        function ($item) {
                            return trim((string) $item);
                        },
                        $value
                    ),
                    function ($item) {
                        return '' !== $item;
                    }
                )
            );

            return empty($value) ? '' : implode(', ', $value);
        }

        return trim((string) $value);
    }

    private function get_category_name_for_log($category_id) {
        $category_id = absint($category_id);

        if (! $category_id) {
            return '';
        }

        $term = get_category($category_id);

        return $term instanceof WP_Term ? $term->name : '';
    }

    private function get_post_type_label_for_log($post_type) {
        $post_type = sanitize_key((string) $post_type);

        if ('' === $post_type) {
            return '';
        }

        $object = get_post_type_object($post_type);

        return $object && ! empty($object->labels->singular_name)
            ? $object->labels->singular_name
            : $post_type;
    }

    private function get_attachment_name_for_log($attachment_id) {
        $attachment_id = absint($attachment_id);

        if (! $attachment_id) {
            return '';
        }

        $title = trim((string) get_the_title($attachment_id));
        if ('' !== $title) {
            return $title;
        }

        $path = get_attached_file($attachment_id);

        return $path ? wp_basename($path) : (string) $attachment_id;
    }

    private function get_template_choices($type) {
        $type = sanitize_key($type);

        if (isset($this->template_choices_cache[$type])) {
            return $this->template_choices_cache[$type];
        }

        $directory = $this->get_template_directory($type);
        if (! is_dir($directory)) {
            $this->template_choices_cache[$type] = array();

            return $this->template_choices_cache[$type];
        }

        $files = glob(trailingslashit($directory) . '*.php');
        if (empty($files)) {
            $this->template_choices_cache[$type] = array();

            return $this->template_choices_cache[$type];
        }

        natcasesort($files);

        $choices = array();
        foreach ($files as $file) {
            $slug    = sanitize_key(wp_basename($file, '.php'));
            $headers = $this->read_template_headers($file);

            $choices[$slug] = array(
                'slug'        => $slug,
                'label'       => '' !== $headers['name'] ? $headers['name'] : ucwords(str_replace(array('-', '_'), ' ', $slug)),
                'description' => $headers['description'],
                'file'        => $file,
            );
        }

        $this->template_choices_cache[$type] = $choices;

        return $this->template_choices_cache[$type];
    }

    private function get_template_file($type, $slug) {
        $choices = $this->get_template_choices($type);
        $slug    = $this->sanitize_template_slug($type, $slug);

        return isset($choices[$slug]['file']) ? $choices[$slug]['file'] : '';
    }

    private function sanitize_template_slug($type, $slug, $fallback = '') {
        $choices  = $this->get_template_choices($type);
        $slug     = sanitize_key($slug);
        $fallback = sanitize_key($fallback);

        if ('' !== $slug && isset($choices[$slug])) {
            return $slug;
        }

        if ('' !== $fallback && isset($choices[$fallback])) {
            return $fallback;
        }

        $available = array_keys($choices);

        return empty($available) ? '' : (string) reset($available);
    }

    private function get_template_directory($type) {
        $type = sanitize_key($type);

        if ('archive' === $type) {
            $type = 'uebersicht';
        } elseif ('single' === $type) {
            $type = 'profil';
        }

        return FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/' . $type;
    }

    private function read_template_headers($file) {
        $defaults = array(
            'name'        => '',
            'description' => '',
        );

        $contents = file_exists($file) ? (string) file_get_contents($file, false, null, 0, 8192) : '';
        if ('' === $contents) {
            return $defaults;
        }

        $map = array(
            'name'        => '/^[ \t\/*#@]*Template Name:\s*(.+)$/mi',
            'description' => '/^[ \t\/*#@]*(?:Description|Beschreibung):\s*(.+)$/mi',
        );

        foreach ($map as $key => $pattern) {
            if (preg_match($pattern, $contents, $matches)) {
                $defaults[$key] = trim(wp_strip_all_tags($matches[1]));
            }
        }

        return $defaults;
    }

    private function sanitize_member_meta($raw_meta) {
        $function_values  = $this->sanitize_member_multi_select_values(isset($raw_meta['function']) ? $raw_meta['function'] : array());
        $education_values = $this->sanitize_member_education_values(isset($raw_meta['education']) ? $raw_meta['education'] : array());
        $lehrgang_values = $this->sanitize_member_education_values(isset($raw_meta['lehrgang']) ? $raw_meta['lehrgang'] : array());
        $contact_fields   = $this->sanitize_member_contact_fields_value(isset($raw_meta['contact_fields']) ? $raw_meta['contact_fields'] : array());
        $custom_fields    = $this->sanitize_member_custom_fields_value(isset($raw_meta['custom_fields']) ? $raw_meta['custom_fields'] : array());

        return array(
            'first_name'  => isset($raw_meta['first_name']) ? sanitize_text_field($raw_meta['first_name']) : '',
            'last_name'   => isset($raw_meta['last_name']) ? sanitize_text_field($raw_meta['last_name']) : '',
            'job_title'   => isset($raw_meta['job_title']) ? sanitize_text_field($raw_meta['job_title']) : '',
            'entry_date'  => isset($raw_meta['entry_date']) ? $this->normalize_entry_year($raw_meta['entry_date']) : '',
            'admin_note'  => isset($raw_meta['admin_note']) ? $this->sanitize_member_admin_note($raw_meta['admin_note']) : '',
            'rank'        => $this->sanitize_member_single_select_value(isset($raw_meta['rank']) ? $raw_meta['rank'] : '', 'rank_options'),
            'function'    => $function_values,
            'education'   => $education_values,
            'lehrgang'    => $lehrgang_values,
            'contact_fields' => $contact_fields,
            'custom_fields' => $custom_fields,
            'sort_order'  => isset($raw_meta['sort_order']) ? absint($raw_meta['sort_order']) : 0,
            'description' => '',
        );
    }

    private function sanitize_member_description($description) {
        return wp_kses_post((string) $description);
    }

    private function sanitize_member_admin_note($value) {
        return sanitize_textarea_field((string) $value);
    }

    private function parse_member_name_from_title($title) {
        $title = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags((string) $title)));

        if ('' === $title) {
            return array(
                'first_name' => '',
                'last_name'  => '',
            );
        }

        $parts = preg_split('/\s+/', $title);
        if (! is_array($parts) || empty($parts)) {
            return array(
                'first_name' => $title,
                'last_name'  => '',
            );
        }

        if (1 === count($parts)) {
            return array(
                'first_name' => sanitize_text_field($parts[0]),
                'last_name'  => '',
            );
        }

        $last_name  = array_pop($parts);
        $first_name = implode(' ', $parts);

        return array(
            'first_name' => sanitize_text_field($first_name),
            'last_name'  => sanitize_text_field($last_name),
        );
    }

    private function normalize_entry_year($value) {
        $value = trim(wp_strip_all_tags((string) $value));

        if ('' === $value) {
            return '';
        }

        if (preg_match('/\b(18|19|20|21)\d{2}\b/', $value, $matches)) {
            return $matches[0];
        }

        if (is_numeric($value)) {
            $year = absint($value);
            if ($year >= 1800 && $year <= ((int) wp_date('Y') + 10)) {
                return (string) $year;
            }
        }

        $timestamp = strtotime($value);
        if ($timestamp) {
            return wp_date('Y', $timestamp);
        }

        return '';
    }

    private function resolve_shortcode_member_ids($args = array()) {
        $defaults = array(
            'id'       => 0,
            'slug'     => '',
            'name'     => '',
            'random'   => 0,
            'count'    => 1,
            'category' => '',
        );

        $args               = wp_parse_args($args, $defaults);
        $category_requested = $this->has_shortcode_category_filter($args['category']);
        $category_ids       = $this->normalize_shortcode_category_ids($args['category']);
        $count              = $this->normalize_shortcode_count($args['count']);
        $member_id          = 0;

        if ($category_requested && empty($category_ids)) {
            return array();
        }

        if (! empty($args['id'])) {
            $member_id = $this->validate_shortcode_member_id(absint($args['id']), $category_ids);
        }

        if (! $member_id && ! empty($args['slug'])) {
            $member_id = $this->find_member_id_by_slug($args['slug'], $category_ids);
        }

        if (! $member_id && ! empty($args['name'])) {
            $member_id = $this->find_member_id_by_name($args['name'], $category_ids);
        }

        if ($member_id) {
            return array($member_id);
        }

        $post_ids = $this->get_ordered_member_ids($category_ids);
        if (empty($post_ids)) {
            return array();
        }

        if ($this->is_truthy_shortcode_attr($args['random'])) {
            shuffle($post_ids);
        }

        return array_slice($post_ids, 0, $count);
    }

    private function normalize_shortcode_category_ids($value) {
        if (is_array($value)) {
            $tokens = $value;
        } elseif (is_string($value)) {
            $tokens = preg_split('/\s*,\s*/', trim($value));
        } elseif (is_numeric($value)) {
            $tokens = array((string) $value);
        } else {
            $tokens = array();
        }

        $resolved = array();
        foreach ((array) $tokens as $token) {
            $token = is_scalar($token) ? trim((string) $token) : '';
            if ('' === $token) {
                continue;
            }

            if (ctype_digit($token)) {
                $category_id = absint($token);
                if ($category_id && term_exists($category_id, 'category')) {
                    $resolved[] = $category_id;
                }

                continue;
            }

            $term = get_category_by_slug(sanitize_title($token));
            if (! $term instanceof WP_Term) {
                $term = get_term_by('name', $token, 'category');
            }

            if ($term instanceof WP_Term) {
                $resolved[] = (int) $term->term_id;
            }
        }

        $resolved = array_values(array_unique(array_filter(array_map('absint', $resolved))));
        $allowed  = $this->get_allowed_category_ids();

        if (! empty($allowed)) {
            $resolved = array_values(array_intersect($resolved, $allowed));
        }

        return $resolved;
    }

    private function has_shortcode_category_filter($value) {
        if (is_array($value)) {
            foreach ($value as $item) {
                if ('' !== trim((string) $item)) {
                    return true;
                }
            }

            return false;
        }

        if (is_numeric($value)) {
            return absint($value) > 0;
        }

        return '' !== trim((string) $value);
    }

    private function normalize_shortcode_count($value) {
        $count = absint($value);

        if ($count < 1) {
            $count = 1;
        }

        return min($count, 5000);
    }

    private function validate_shortcode_member_id($post_id, $category_ids = array()) {
        $post = get_post($post_id);
        if (! $post instanceof WP_Post || ! $this->is_member_post($post) || 'publish' !== $post->post_status) {
            return 0;
        }

        if (! empty($category_ids)) {
            $member_categories = $this->get_member_category_ids($post_id);
            if (empty(array_intersect($member_categories, $category_ids))) {
                return 0;
            }
        }

        return (int) $post_id;
    }

    private function find_member_id_by_slug($slug, $category_ids = array()) {
        $slug = sanitize_title($slug);
        if ('' === $slug) {
            return 0;
        }

        $post = get_page_by_path($slug, OBJECT, self::POST_TYPE);
        if (! $post instanceof WP_Post) {
            return 0;
        }

        return $this->validate_shortcode_member_id($post->ID, $category_ids);
    }

    private function find_member_id_by_name($name, $category_ids = array()) {
        $needle = $this->normalize_member_lookup_value($name);
        if ('' === $needle) {
            return 0;
        }

        $post_ids = $this->get_ordered_member_ids($category_ids);
        if (empty($post_ids)) {
            return 0;
        }

        $partial_match = 0;

        foreach ($post_ids as $post_id) {
            $post        = get_post($post_id);
            $meta        = $this->get_member_meta($post_id);
            $candidates  = array(
                $this->get_member_display_name($post_id),
                $post instanceof WP_Post ? $post->post_title : '',
                $meta['first_name'],
                $meta['last_name'],
                trim($meta['first_name'] . ' ' . $meta['last_name']),
            );

            foreach ($candidates as $candidate) {
                $normalized_candidate = $this->normalize_member_lookup_value($candidate);
                if ('' === $normalized_candidate) {
                    continue;
                }

                if ($needle === $normalized_candidate) {
                    return (int) $post_id;
                }

                if (! $partial_match && false !== strpos($normalized_candidate, $needle)) {
                    $partial_match = (int) $post_id;
                }
            }
        }

        return $partial_match;
    }

    private function normalize_member_lookup_value($value) {
        $value = trim(wp_strip_all_tags((string) $value));
        if ('' === $value) {
            return '';
        }

        if (function_exists('mb_strtolower')) {
            $value = mb_strtolower($value, 'UTF-8');
        } else {
            $value = strtolower($value);
        }

        $value = remove_accents($value);
        $value = str_replace(
            array('ae', 'oe', 'ue', 'ss'),
            array('a', 'o', 'u', 's'),
            $value
        );
        $value = preg_replace('/\s+/', ' ', $value);

        return trim((string) $value);
    }

    private function is_truthy_shortcode_attr($value) {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, array('1', 'true', 'yes', 'ja', 'on', 'zufall'), true);
    }

    private function format_shortcode_count_text($text, $count) {
        $text = sanitize_text_field($text);
        if (false !== strpos($text, '%d')) {
            return sprintf($text, (int) $count);
        }

        return $text . ' ' . (int) $count;
    }

    private function sanitize_member_category($selected_category) {
        $selected_category = absint($selected_category);

        if (! $selected_category || ! term_exists($selected_category, 'category')) {
            return 0;
        }

        $allowed_categories = $this->get_allowed_category_ids();

        if (! empty($allowed_categories) && ! in_array($selected_category, $allowed_categories, true)) {
            return 0;
        }

        return $selected_category;
    }

    private function sanitize_allowed_category_ids($selected_categories) {
        $selected_categories = array_values(array_unique(array_filter(array_map('absint', $selected_categories))));

        return array_values(
            array_filter(
                $selected_categories,
                function ($category_id) {
                    return (bool) term_exists($category_id, 'category');
                }
            )
        );
    }

    private function handle_gallery_uploads($post_id, $member_name) {
        if (empty($_FILES['feuer_mannschaft_gallery']) || empty($_FILES['feuer_mannschaft_gallery']['name']) || ! is_array($_FILES['feuer_mannschaft_gallery']['name'])) {
            return array();
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $uploaded_ids = array();
        $files        = $_FILES['feuer_mannschaft_gallery'];
        $file_count   = count($files['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if (UPLOAD_ERR_NO_FILE === (int) $files['error'][$i]) {
                continue;
            }

            $file = array(
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            );

            add_filter('upload_dir', array($this, 'filter_upload_dir'));
            $uploaded = wp_handle_upload(
                $file,
                array(
                    'test_form' => false,
                    'mimes'     => array(
                        'jpg|jpeg|jpe' => 'image/jpeg',
                        'png'          => 'image/png',
                        'gif'          => 'image/gif',
                        'webp'         => 'image/webp',
                    ),
                )
            );
            remove_filter('upload_dir', array($this, 'filter_upload_dir'));

            if (! empty($uploaded['error']) || empty($uploaded['file'])) {
                continue;
            }

            $this->apply_watermark($uploaded['file'], $member_name);

            $attachment_id = wp_insert_attachment(
                array(
                    'guid'           => $uploaded['url'],
                    'post_mime_type' => $uploaded['type'],
                    'post_title'     => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
                    'post_status'    => 'inherit',
                ),
                $uploaded['file'],
                $post_id
            );

            if (is_wp_error($attachment_id)) {
                continue;
            }

            $metadata = wp_generate_attachment_metadata($attachment_id, $uploaded['file']);
            wp_update_attachment_metadata($attachment_id, $metadata);
            $uploaded_ids[] = $attachment_id;
        }

        return $uploaded_ids;
    }

    public function filter_upload_dir($uploads) {
        $subdir = '/' . self::MEMBER_UPLOAD_SUBDIR;

        $uploads['subdir'] = $subdir;
        $uploads['path']   = $uploads['basedir'] . $subdir;
        $uploads['url']    = $uploads['baseurl'] . $subdir;

        $this->prepare_storage_directory($uploads['path'], $this->get_member_upload_htaccess_content());

        return $uploads;
    }

    private function apply_watermark($file_path, $member_name) {
        if (! file_exists($file_path)) {
            return false;
        }

        $settings       = $this->get_settings();
        $watermark_text = trim($member_name . ' ' . $settings['watermark_text']);

        if ('' === trim($watermark_text)) {
            return false;
        }

        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $image     = $this->create_image_resource($file_path, $extension);

        if (! $image) {
            return false;
        }

        $width  = imagesx($image);
        $height = imagesy($image);

        if (! $width || ! $height) {
            imagedestroy($image);
            return false;
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $font = 5;
        $text = $this->prepare_watermark_text($watermark_text, $width, $font);

        $padding     = 14;
        $text_width  = imagefontwidth($font) * strlen($text);
        $text_height = imagefontheight($font);
        $bar_height  = $text_height + ($padding * 2);
        $x           = max(10, (int) floor(($width - $text_width) / 2));
        $y           = $height - $bar_height + $padding;

        $overlay_color = imagecolorallocatealpha($image, 0, 0, 0, 55);
        $text_color    = imagecolorallocatealpha($image, 255, 255, 255, 0);

        imagefilledrectangle($image, 0, $height - $bar_height, $width, $height, $overlay_color);
        imagestring($image, $font, $x, $y, $text, $text_color);

        $saved = $this->save_image_resource($image, $file_path, $extension);
        imagedestroy($image);

        return $saved;
    }

    private function create_image_resource($file_path, $extension) {
        if ('jpg' === $extension || 'jpeg' === $extension || 'jpe' === $extension) {
            return function_exists('imagecreatefromjpeg') ? imagecreatefromjpeg($file_path) : false;
        }

        if ('png' === $extension) {
            return function_exists('imagecreatefrompng') ? imagecreatefrompng($file_path) : false;
        }

        if ('gif' === $extension) {
            return function_exists('imagecreatefromgif') ? imagecreatefromgif($file_path) : false;
        }

        if ('webp' === $extension) {
            return function_exists('imagecreatefromwebp') ? imagecreatefromwebp($file_path) : false;
        }

        return false;
    }

    private function save_image_resource($image, $file_path, $extension) {
        if ('jpg' === $extension || 'jpeg' === $extension || 'jpe' === $extension) {
            return imagejpeg($image, $file_path, 90);
        }

        if ('png' === $extension) {
            return imagepng($image, $file_path, 6);
        }

        if ('gif' === $extension) {
            return imagegif($image, $file_path);
        }

        if ('webp' === $extension && function_exists('imagewebp')) {
            return imagewebp($image, $file_path, 90);
        }

        return false;
    }

    private function prepare_watermark_text($text, $width, $font) {
        $safe_text = $text;

        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
            if (false !== $converted) {
                $safe_text = $converted;
            }
        }

        $max_chars = (int) floor(max(20, ($width - 24)) / imagefontwidth($font));
        if (strlen($safe_text) > $max_chars) {
            $safe_text = substr($safe_text, 0, max(0, $max_chars - 3)) . '...';
        }

        return $safe_text;
    }

    private function get_member_meta($post_id) {
        $function_values  = $this->sanitize_member_multi_select_values(get_post_meta($post_id, self::META_PREFIX . 'function', true));
        $education_values = $this->normalize_member_education_meta(get_post_meta($post_id, self::META_PREFIX . 'education', true));
        $lehrgang_values  = $this->sanitize_member_education_values(get_post_meta($post_id, self::META_PREFIX . 'lehrgang', true));
        $contact_fields   = $this->sanitize_member_contact_fields_value(get_post_meta($post_id, self::META_PREFIX . 'contact_fields', true));
        $custom_fields    = $this->sanitize_member_custom_fields_value(get_post_meta($post_id, self::META_PREFIX . 'custom_fields', true));

        $meta = array(
            'first_name'       => (string) get_post_meta($post_id, self::META_PREFIX . 'first_name', true),
            'last_name'        => (string) get_post_meta($post_id, self::META_PREFIX . 'last_name', true),
            'job_title'        => (string) get_post_meta($post_id, self::META_PREFIX . 'job_title', true),
            'entry_date'       => $this->normalize_entry_year(get_post_meta($post_id, self::META_PREFIX . 'entry_date', true)),
            'admin_note'       => $this->sanitize_member_admin_note(get_post_meta($post_id, self::META_PREFIX . 'admin_note', true)),
            'rank'             => (string) get_post_meta($post_id, self::META_PREFIX . 'rank', true),
            'function'         => implode(', ', $function_values),
            'function_values'  => $function_values,
            'education'        => implode(', ', $education_values),
            'education_values' => $education_values,
            'lehrgang'         => implode("\n", $lehrgang_values),
            'lehrgang_values'  => $lehrgang_values,
            'contact_fields'   => $contact_fields,
            'custom_fields'    => $custom_fields,
            'sort_order'       => (int) get_post_meta($post_id, self::META_PREFIX . 'sort_order', true),
            'description'      => (string) get_post_meta($post_id, self::META_PREFIX . 'description', true),
        );

        return apply_filters('feuer_mannschaft_member_meta', $meta, absint($post_id));
    }

    public function get_member_gallery($post_id) {
        $gallery = get_post_meta($post_id, self::GALLERY_META, true);
        if (! is_array($gallery)) {
            return array();
        }

        return array_values(
            array_filter(
                array_map(
                    'absint',
                    $gallery
                )
            )
        );
    }

    private function is_cache_purge_enabled_in_settings($settings) {
        return ! empty($settings['enable_cache_purge']);
    }

    private function normalize_cache_purge_ids($ids) {
        return array_values(array_unique(array_filter(array_map('absint', (array) $ids))));
    }

    private function normalize_cache_purge_urls($urls) {
        $normalized = array();

        foreach ((array) $urls as $url) {
            $url = is_string($url) ? trim($url) : '';
            if ('' === $url) {
                continue;
            }

            $url = esc_url_raw($url);
            if ('' !== $url) {
                $normalized[] = $url;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function get_shortcode_page_ids() {
        if (is_array($this->shortcode_page_ids_cache)) {
            return $this->shortcode_page_ids_cache;
        }

        global $wpdb;

        $post_types = get_post_types(array('public' => true), 'names');
        $post_types = array_values(array_diff((array) $post_types, array('attachment')));

        if (empty($post_types)) {
            $this->shortcode_page_ids_cache = array();

            return $this->shortcode_page_ids_cache;
        }

        $placeholders = implode(', ', array_fill(0, count($post_types), '%s'));
        $like         = '%' . $wpdb->esc_like('feuer_mannschaft') . '%';
        $query        = $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_status IN ('publish', 'private') AND post_type IN ({$placeholders}) AND post_content LIKE %s",
            array_merge($post_types, array($like))
        );

        $candidate_ids = array_values(array_map('absint', (array) $wpdb->get_col($query)));
        $shortcodes    = $this->get_all_frontend_shortcode_tags();
        $page_ids      = array();

        foreach ($candidate_ids as $page_id) {
            $content = (string) get_post_field('post_content', $page_id);

            foreach ($shortcodes as $shortcode) {
                if (function_exists('has_shortcode') && has_shortcode($content, $shortcode)) {
                    $page_ids[] = $page_id;
                    break;
                }

                if (false !== strpos($content, '[' . $shortcode)) {
                    $page_ids[] = $page_id;
                    break;
                }
            }
        }

        $this->shortcode_page_ids_cache = array_values(array_unique(array_filter($page_ids)));

        return $this->shortcode_page_ids_cache;
    }

    private function get_shortcode_page_urls($page_ids) {
        $urls = array();

        foreach ($this->normalize_cache_purge_ids($page_ids) as $page_id) {
            $url = get_permalink($page_id);
            if (is_string($url) && '' !== $url && ! is_wp_error($url)) {
                $urls[] = $url;
            }
        }

        return $this->normalize_cache_purge_urls($urls);
    }

    private function get_member_cache_urls($post_ids = array()) {
        $urls         = array();
        $category_ids = array();
        $post_ids     = $this->normalize_cache_purge_ids($post_ids);

        foreach ($post_ids as $post_id) {
            $url = get_permalink($post_id);
            if (is_string($url) && '' !== $url && ! is_wp_error($url)) {
                $urls[] = $url;
            }

            $category_ids = array_merge($category_ids, $this->get_member_category_ids($post_id));
        }

        if (empty($category_ids)) {
            $category_ids = $this->get_allowed_category_ids();
        }

        foreach ($this->normalize_cache_purge_ids($category_ids) as $category_id) {
            $url = get_category_link($category_id);
            if (is_string($url) && '' !== $url && ! is_wp_error($url)) {
                $urls[] = $url;
            }
        }

        return $this->normalize_cache_purge_urls($urls);
    }

    private function purge_external_cache_plugins($post_ids, $attachment_ids, $urls, $context) {
        $post_ids       = $this->normalize_cache_purge_ids($post_ids);
        $attachment_ids = $this->normalize_cache_purge_ids($attachment_ids);
        $urls           = $this->normalize_cache_purge_urls($urls);

        foreach ($post_ids as $post_id) {
            do_action('litespeed_purge_post', $post_id);

            if (function_exists('rocket_clean_post')) {
                rocket_clean_post($post_id);
            }

            if (function_exists('w3tc_flush_post')) {
                w3tc_flush_post($post_id);
            }

            if (function_exists('wp_cache_post_change')) {
                wp_cache_post_change($post_id);
            }
        }

        foreach ($urls as $url) {
            do_action('litespeed_purge_url', $url);

            if (function_exists('w3tc_flush_url')) {
                w3tc_flush_url($url);
            }
        }

        if (! empty($urls) && function_exists('rocket_clean_files')) {
            rocket_clean_files($urls);
        }

        if (function_exists('wp_cache_clear_cache') && (empty($post_ids) || 'settings_updated' === $context || 'backup_restored' === $context)) {
            wp_cache_clear_cache();
        }

        if (function_exists('w3tc_flush_all') && ! function_exists('w3tc_flush_post') && ! function_exists('w3tc_flush_url')) {
            w3tc_flush_all();
        }

        if (function_exists('sg_cachepress_purge_cache')) {
            sg_cachepress_purge_cache();
        }

        if (function_exists('sg_cachepress_purge_everything') && in_array($context, array('settings_updated', 'backup_restored'), true)) {
            sg_cachepress_purge_everything();
        }

        do_action('siteground_optimizer_flush_cache');
        do_action('siteground_optimizer_purge_cache');
        do_action('feuer_mannschaft_cache_purge_external', $context, $post_ids, $attachment_ids, $urls);
    }

    private function get_member_display_name($post_id) {
        $meta = $this->get_member_meta($post_id);
        $name = trim($meta['first_name'] . ' ' . $meta['last_name']);

        if (! $name) {
            $name = get_the_title($post_id);
        }

        return $name;
    }

    private function get_member_initials($post_id) {
        $meta     = $this->get_member_meta($post_id);
        $first    = $meta['first_name'] ? $this->mb_char($meta['first_name']) : '';
        $last     = $meta['last_name'] ? $this->mb_char($meta['last_name']) : '';
        $initials = strtoupper(trim($first . $last));

        if (! $initials) {
            $title_words = preg_split('/\s+/', trim((string) get_the_title($post_id)));
            if (is_array($title_words) && ! empty($title_words)) {
                $title_first = isset($title_words[0]) ? $this->mb_char($title_words[0]) : '';
                $title_last  = isset($title_words[1]) ? $this->mb_char($title_words[1]) : '';
                $initials    = strtoupper(trim($title_first . $title_last));
            }
        }

        return $initials ? $initials : 'M';
    }

    private function get_member_visual_markup($post_id, $size = 'large', $class = '') {
        $gallery = $this->get_member_gallery($post_id);

        $attachment_id = ! empty($gallery) ? absint($gallery[0]) : 0;
        $attachment_id = absint(apply_filters('feuer_mannschaft_visual_attachment_id', $attachment_id, $post_id, $size, $class));

        if ($attachment_id) {
            return wp_get_attachment_image($attachment_id, $size, false, array('class' => trim('img-fluid ' . $class), 'loading' => 'lazy'));
        }

        $default_image = $this->get_default_profile_image_markup($size, $class);
        if ('' !== $default_image) {
            return $default_image;
        }

        return $this->render_frontend_template(
            'member-placeholder',
            array(
                'class_name' => trim($class),
                'label'      => $this->get_member_initials($post_id),
            )
        );
    }

    private function get_member_contact_buttons_markup($post_id, $settings = null) {
        $buttons = $this->get_member_contact_buttons($post_id, $settings);

        if (empty($buttons)) {
            return '';
        }

        return $this->render_frontend_template(
            'contact-buttons',
            array(
                'buttons' => $buttons,
            )
        );
    }

    private function get_member_admin_notice_markup($post_id) {
        if (! $this->current_user_can_manage_member_admin_note()) {
            return '';
        }

        $meta = $this->get_member_meta($post_id);
        $note = isset($meta['admin_note']) ? trim((string) $meta['admin_note']) : '';

        if ('' === $note) {
            return '';
        }

        return $this->render_frontend_template(
            'admin-note',
            array(
                'note' => $note,
            )
        );
    }

    private function get_member_contact_buttons($post_id, $settings = null) {
        $meta         = $this->get_member_meta($post_id);
        $definitions  = $this->get_enabled_contact_field_definitions($settings);
        $contact_data = isset($meta['contact_fields']) && is_array($meta['contact_fields']) ? $meta['contact_fields'] : array();
        $buttons      = array();

        foreach ($definitions as $field_key => $definition) {
            if (! apply_filters('feuer_mannschaft_field_visible', true, $field_key, absint($post_id))) {
                continue;
            }
            if (! isset($contact_data[$field_key])) {
                continue;
            }

            $button = $this->build_member_contact_button($field_key, $contact_data[$field_key], $definition);

            if (! empty($button)) {
                $buttons[] = $button;
            }
        }

        return $buttons;
    }

    private function build_member_contact_button($field_key, $value, $definition) {
        $field_key = sanitize_key((string) $field_key);
        $url       = $this->build_contact_button_url($field_key, $value);

        if ('' === $url) {
            return array();
        }

        $label = isset($definition['button_label']) ? (string) $definition['button_label'] : (isset($definition['label']) ? (string) $definition['label'] : ucfirst($field_key));

        if ('link' === $field_key) {
            $value = $this->sanitize_custom_contact_link_value($value);
            $label = ! empty($value['label']) ? (string) $value['label'] : $label;
        }

        $scheme      = strtolower((string) wp_parse_url($url, PHP_URL_SCHEME));
        $external    = in_array($scheme, array('http', 'https'), true);
        $button_type = sanitize_html_class($field_key, 'contact');

        return array(
            'label'  => sanitize_text_field($label),
            'url'    => $url,
            'target' => $external ? '_blank' : '',
            'rel'    => $external ? 'noopener noreferrer' : '',
            'type'   => $button_type,
        );
    }

    private function build_contact_button_url($field_key, $value) {
        $field_key = sanitize_key((string) $field_key);

        switch ($field_key) {
            case 'email':
                $email = sanitize_email((string) $value);
                return $email && is_email($email) ? 'mailto:' . rawurlencode($email) : '';

            case 'instagram':
                $handle = $this->sanitize_contact_handle($value);
                return '' !== $handle ? 'https://www.instagram.com/' . rawurlencode($handle) . '/' : '';

            case 'facebook':
                $handle = $this->sanitize_contact_handle($value);
                return '' !== $handle ? 'https://www.facebook.com/' . rawurlencode($handle) . '/' : '';

            case 'telegram':
                $handle = $this->sanitize_contact_handle($value);
                return '' !== $handle ? 'https://t.me/' . rawurlencode($handle) : '';

            case 'whatsapp':
                $value = $this->sanitize_whatsapp_contact_value($value);

                if ('' === $value) {
                    return '';
                }

                if (preg_match('#^(?:https?:)?//#i', $value)) {
                    return $this->sanitize_invite_url($value);
                }

                $digits = preg_replace('/\D/', '', $value);

                return '' !== $digits ? 'https://wa.me/' . $digits : '';

            case 'link':
                $link = $this->sanitize_custom_contact_link_value($value);
                return ! empty($link['url']) ? (string) $link['url'] : '';
        }

        return '';
    }

    private function get_single_detail_rows($post_id, $limit = 0) {
        $meta         = $this->get_member_meta($post_id);
        $field_groups = $this->get_field_option_groups();
        $rows         = array();

        $entry_year = $this->format_entry_date($meta['entry_date']);
        if ('' !== trim((string) $entry_year) && apply_filters('feuer_mannschaft_field_visible', true, 'entry_date', absint($post_id))) {
            $rows[] = array(
                'field'  => 'entry_date',
                'label'  => 'Eintrittsjahr',
                'value'  => $entry_year,
                'values' => array(),
                'items'  => array(),
            );
        }

        foreach ($field_groups as $group_key => $group) {
            $field_key = sanitize_key(isset($group['id']) ? $group['id'] : $group_key);
            if (! apply_filters('feuer_mannschaft_field_visible', true, $field_key, absint($post_id))) {
                continue;
            }
            $selected_values = $this->get_member_field_values_by_group($meta, $group);

            if (empty($selected_values)) {
                continue;
            }

            $option_map = array();
            foreach ($this->sanitize_setting_option_entries(isset($group['options']) ? $group['options'] : array()) as $option) {
                $option_map[$option['label']] = $option;
            }

            $items = array();
            foreach ($selected_values as $selected_value) {
                $items[] = array(
                    'label' => $selected_value,
                    'url'   => isset($option_map[$selected_value]['url']) ? $this->sanitize_invite_url($option_map[$selected_value]['url']) : '',
                );
            }

            $rows[] = array(
                'field'  => $field_key,
                'label'  => isset($group['label']) ? $group['label'] : '',
                'value'  => implode(', ', array_map('trim', $selected_values)),
                'values' => $selected_values,
                'items'  => $items,
            );
        }

        $lehrgang_values = $this->sanitize_member_education_values($meta['lehrgang_values']);
        if (! empty($lehrgang_values) && apply_filters('feuer_mannschaft_field_visible', true, 'lehrgang', absint($post_id))) {
            $course_values = array_map(function ($value) {
                return 'Lehrgang: ' . $value;
            }, $lehrgang_values);
            $education_row_index = null;
            foreach ($rows as $index => $row) {
                if ('education_options' === $row['field']) {
                    $education_row_index = $index;
                    break;
                }
            }
            if (null === $education_row_index) {
                $rows[] = array('field' => 'education', 'label' => 'Ausbildung', 'value' => '', 'values' => array(), 'items' => array());
                $education_row_index = count($rows) - 1;
            }
            $rows[$education_row_index]['values'] = array_merge($rows[$education_row_index]['values'], $course_values);
            $rows[$education_row_index]['items'] = array_merge($rows[$education_row_index]['items'], array_map(function ($value) {
                return array('label' => $value, 'url' => '');
            }, $course_values));
            $rows[$education_row_index]['value'] = implode(', ', $rows[$education_row_index]['values']);
        }

        if ($limit > 0) {
            $rows = array_slice($rows, 0, $limit);
        }

        return $rows;
    }

    private function current_user_can_manage_member_admin_note() {
        return current_user_can('manage_options');
    }

    private function get_detail_value_markup($detail, $single_value_class = '') {
        $items = isset($detail['items']) && is_array($detail['items'])
            ? array_values(
                array_filter(
                    array_map(
                        function ($item) {
                            if (! is_array($item)) {
                                return null;
                            }

                            $label = isset($item['label']) ? sanitize_text_field((string) $item['label']) : '';
                            if ('' === $label) {
                                return null;
                            }

                            return array(
                                'label' => $label,
                                'url'   => isset($item['url']) ? $this->sanitize_invite_url($item['url']) : '',
                            );
                        },
                        $detail['items']
                    )
                )
            )
            : array();

        $values = isset($detail['values']) && is_array($detail['values'])
            ? array_values(array_filter(array_map('sanitize_text_field', $detail['values'])))
            : array();

        if (! empty($items) && count($items) > 1) {
            return $this->render_frontend_template(
                'detail-tags',
                array(
                    'items' => $items,
                )
            );
        }

        $single_value_class = trim((string) $single_value_class);

        if (! empty($items)) {
            return $this->render_frontend_template(
                'detail-value',
                array(
                    'class_name' => $single_value_class,
                    'value'      => $items[0]['label'],
                    'url'        => $items[0]['url'],
                )
            );
        }

        if (! empty($values)) {
            $fallback_items = array_map(
                function ($value) {
                    return array(
                        'label' => $value,
                        'url'   => '',
                    );
                },
                $values
            );

            if (count($fallback_items) > 1) {
                return $this->render_frontend_template(
                    'detail-tags',
                    array(
                        'items' => $fallback_items,
                    )
                );
            }

            return $this->render_frontend_template(
                'detail-value',
                array(
                    'class_name' => $single_value_class,
                    'value'      => $values[0],
                    'url'        => '',
                )
            );
        }

        return $this->render_frontend_template(
            'detail-value',
            array(
                'class_name' => $single_value_class,
                'value'      => isset($detail['value']) ? (string) $detail['value'] : '',
                'url'        => '',
            )
        );
    }

    private function format_entry_date($value) {
        return $this->normalize_entry_year($value);
    }

    private function get_action_link_markup($url, $label, $class_name = '', $confirm_message = '') {
        return $this->render_admin_template(
            'action-link',
            array(
                'url'             => (string) $url,
                'label'           => (string) $label,
                'class_name'      => (string) $class_name,
                'confirm_message' => (string) $confirm_message,
            )
        );
    }

    private function get_adjacent_members($post_id) {
        $post_ids = $this->get_ordered_member_ids($this->get_member_category_ids($post_id));

        $index = array_search($post_id, $post_ids, true);
        if (false === $index) {
            return array(
                'prev' => 0,
                'next' => 0,
            );
        }

        return array(
            'prev' => isset($post_ids[$index - 1]) ? (int) $post_ids[$index - 1] : 0,
            'next' => isset($post_ids[$index + 1]) ? (int) $post_ids[$index + 1] : 0,
        );
    }

    private function get_related_member_ids($post_id, $limit = 4) {
        $limit    = max(0, (int) $limit);
        $post_ids = array_values(array_map('absint', (array) $this->get_ordered_member_ids($this->get_member_category_ids($post_id))));
        $count    = count($post_ids);

        if ($limit < 1 || $count < 2) {
            return array();
        }

        $current_index = array_search((int) $post_id, $post_ids, true);
        if (false === $current_index) {
            $post_ids = array_values(array_diff($post_ids, array((int) $post_id)));

            return array_slice($post_ids, 0, $limit);
        }

        $related_ids = array();

        for ($offset = 1; $offset < $count && count($related_ids) < $limit; $offset++) {
            $candidate = (int) $post_ids[($current_index + $offset) % $count];

            if ($candidate !== (int) $post_id) {
                $related_ids[] = $candidate;
            }
        }

        return $related_ids;
    }

    private function get_ordered_member_ids($category_ids = array()) {
        return $this->get_active_profile_ids($category_ids, true);
    }

    private function get_active_profile_ids($category_ids = array(), $exclude_custom_links = false) {
        $args = array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'   => self::PROFILE_META,
                    'value' => '1',
                ),
            ),
            'meta_key'       => self::META_PREFIX . 'sort_order',
            'orderby'        => array(
                'meta_value_num' => 'ASC',
                'title'          => 'ASC',
            ),
            'order'          => 'ASC',
        );

        if (! empty($category_ids)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy'         => 'category',
                    'field'            => 'term_id',
                    'terms'            => array_values(array_map('absint', $category_ids)),
                    'include_children' => true,
                ),
            );
        }

        if ($exclude_custom_links) {
            $args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key'     => self::INVITE_URL_META,
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'   => self::INVITE_URL_META,
                    'value' => '',
                ),
            );
        }

        return get_posts($args);
    }

    private function get_countable_member_ids($category_ids = array()) {
        $post_ids = $this->get_active_profile_ids($category_ids);

        return array_values(
            array_filter(
                array_map('absint', (array) $post_ids),
                function ($post_id) {
                    return ! $this->is_excluded_from_member_count($post_id);
                }
            )
        );
    }

    private function should_load_frontend_assets() {
        if (is_admin()) {
            return false;
        }

        if (is_post_type_archive(self::POST_TYPE)) {
            return true;
        }

        if (is_singular(self::POST_TYPE) && $this->is_member_post(get_queried_object_id())) {
            return true;
        }

        if ($this->is_member_category_archive()) {
            return true;
        }

        $post = get_post();

        if (! $post instanceof WP_Post) {
            return false;
        }

        foreach ($this->get_all_frontend_shortcode_tags() as $shortcode) {
            if (has_shortcode($post->post_content, $shortcode)) {
                return true;
            }
        }

        return false;
    }

    private function get_default_settings() {
        $default_field_options = $this->get_default_field_option_values();
        $default_contact_fields = array_fill_keys(array_keys($this->get_contact_field_definitions()), 0);

        return array(
            'watermark_text'      => '',
            'allowed_categories'  => array(),
            'archive_display'     => 'cards',
            'archive_template'    => 'team-grid',
            'single_template'     => 'standard',
            'default_profile_image_id' => 0,
            'card_image_width'    => 360,
            'card_image_height'   => 460,
            'card_grayscale_strength' => 100,
            'card_preset' => 'classic',
            'card_overlay_opacity' => 65,
            'card_radius' => 12,
            'card_name_size' => 24,
            'card_text_position' => 'bottom',
            'card_show_rank' => 1,
            'card_show_function' => 1,
            'excluded_title_color' => '#b22222',
            'enable_cache_purge'  => 1,
            'enable_automatic_updates' => 0,
            'single_image_height' => 620,
            'slider_autoplay'     => 5000,
            'enable_gallery'      => 1,
            'enable_mentions'     => 1,
            'mention_post_types'  => array('post'),
            'rank_options'        => $default_field_options['rank_options'],
            'function_options'    => $default_field_options['function_options'],
            'education_options'   => $default_field_options['education_options'],
            'contact_fields'      => $default_contact_fields,
            'custom_field_groups' => array(),
            'log_retention_days'  => 0,
        );
    }

    private function maybe_create_default_settings_option() {
        if (false === get_option(self::OPTION_KEY, false)) {
            add_option(self::OPTION_KEY, $this->get_default_settings(), '', false);
        }
    }

    private function ensure_member_upload_storage() {
        $upload_dir = wp_upload_dir();

        if (empty($upload_dir['basedir'])) {
            return;
        }

        $directory = trailingslashit($upload_dir['basedir']) . self::MEMBER_UPLOAD_SUBDIR;
        $this->prepare_storage_directory($directory, $this->get_member_upload_htaccess_content());
    }

    private function prepare_storage_directory($directory, $htaccess_content = '') {
        $directory = (string) $directory;

        if ('' === $directory) {
            return;
        }

        if (! file_exists($directory)) {
            wp_mkdir_p($directory);
        }

        if (! is_dir($directory)) {
            return;
        }

        $index_file = trailingslashit($directory) . 'index.php';
        if (! file_exists($index_file)) {
            file_put_contents($index_file, "<?php\n// Silence is golden.\n");
        }

        if ('' !== $htaccess_content) {
            $htaccess_file = trailingslashit($directory) . '.htaccess';
            if (! file_exists($htaccess_file) || (string) file_get_contents($htaccess_file) !== $htaccess_content) {
                file_put_contents($htaccess_file, $htaccess_content);
            }
        }
    }

    private function get_member_upload_htaccess_content() {
        return "Options -Indexes\n<FilesMatch \"\\.(?:php|php[0-9]?|phtml|phar|cgi|pl|asp|aspx|jsp)$\">\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n</FilesMatch>\n";
    }

    public function maybe_set_default_archive_template() {
        if (get_option(self::ARCHIVE_TEMPLATE_DEFAULT_MIGRATION_OPTION)) {
            return;
        }

        $settings = get_option(self::OPTION_KEY, array());
        $settings = is_array($settings) ? $settings : array();
        $settings['archive_template'] = 'team-grid';

        update_option(self::OPTION_KEY, $settings, false);
        update_option(self::ARCHIVE_TEMPLATE_DEFAULT_MIGRATION_OPTION, 1, false);
        $this->purge_plugin_cache('archive_template_default_migrated', array(), array(), array(), true);
    }

    private function get_settings() {
        $saved = get_option(self::OPTION_KEY, array());
        $saved = is_array($saved) ? $saved : array();

        if (empty($saved['archive_template']) && ! empty($saved['archive_display'])) {
            $saved['archive_template'] = $this->map_legacy_display_to_template($saved['archive_display']);
        }

        $settings = wp_parse_args($saved, $this->get_default_settings());

        $settings['archive_template']         = $this->sanitize_template_slug('archive', $settings['archive_template'], 'team-grid');
        $settings['single_template']          = $this->sanitize_template_slug('single', $settings['single_template'], 'standard');
        $settings['default_profile_image_id'] = $this->sanitize_attachment_image_id(isset($settings['default_profile_image_id']) ? $settings['default_profile_image_id'] : 0);
        $settings['card_grayscale_strength']  = $this->clamp_int(isset($settings['card_grayscale_strength']) ? $settings['card_grayscale_strength'] : 100, 0, 100);
        $settings['excluded_title_color']     = $this->sanitize_setting_color(isset($settings['excluded_title_color']) ? $settings['excluded_title_color'] : '#b22222', '#b22222');
        $settings['enable_cache_purge']       = empty($settings['enable_cache_purge']) ? 0 : 1;
        $settings['enable_automatic_updates'] = empty($settings['enable_automatic_updates']) ? 0 : 1;
        $settings['mention_post_types']       = $this->sanitize_setting_post_types(isset($settings['mention_post_types']) ? (array) $settings['mention_post_types'] : array('post'));
        $settings['rank_options']             = $this->sanitize_setting_option_entries(isset($settings['rank_options']) ? $settings['rank_options'] : array());
        $settings['function_options']         = $this->sanitize_setting_option_entries(isset($settings['function_options']) ? $settings['function_options'] : array());
        $settings['education_options']        = $this->sanitize_setting_option_entries(isset($settings['education_options']) ? $settings['education_options'] : array());
        $settings['contact_fields']           = $this->sanitize_contact_field_settings(isset($settings['contact_fields']) ? $settings['contact_fields'] : array());
        $settings['custom_field_groups']      = $this->sanitize_custom_field_groups(isset($settings['custom_field_groups']) ? $settings['custom_field_groups'] : array());

        return $settings;
    }

    public function normalize_restored_settings($settings) {
        $settings = is_array($settings) ? $settings : array();

        if (empty($settings['archive_template']) && ! empty($settings['archive_display'])) {
            $settings['archive_template'] = $this->map_legacy_display_to_template($settings['archive_display']);
        }

        $settings = wp_parse_args($settings, $this->get_default_settings());

        $settings['archive_template']         = $this->sanitize_template_slug('archive', $settings['archive_template'], 'team-grid');
        $settings['single_template']          = $this->sanitize_template_slug('single', $settings['single_template'], 'standard');
        $settings['default_profile_image_id'] = $this->sanitize_attachment_image_id(isset($settings['default_profile_image_id']) ? $settings['default_profile_image_id'] : 0);
        $settings['card_grayscale_strength']  = $this->clamp_int(isset($settings['card_grayscale_strength']) ? $settings['card_grayscale_strength'] : 100, 0, 100);
        $settings['excluded_title_color']     = $this->sanitize_setting_color(isset($settings['excluded_title_color']) ? $settings['excluded_title_color'] : '#b22222', '#b22222');
        $settings['enable_cache_purge']       = empty($settings['enable_cache_purge']) ? 0 : 1;
        $settings['enable_automatic_updates'] = empty($settings['enable_automatic_updates']) ? 0 : 1;
        $settings['mention_post_types']       = $this->sanitize_setting_post_types(isset($settings['mention_post_types']) ? (array) $settings['mention_post_types'] : array('post'));
        $settings['rank_options']             = $this->sanitize_setting_option_entries(isset($settings['rank_options']) ? $settings['rank_options'] : array());
        $settings['function_options']         = $this->sanitize_setting_option_entries(isset($settings['function_options']) ? $settings['function_options'] : array());
        $settings['education_options']        = $this->sanitize_setting_option_entries(isset($settings['education_options']) ? $settings['education_options'] : array());
        $settings['contact_fields']           = $this->sanitize_contact_field_settings(isset($settings['contact_fields']) ? $settings['contact_fields'] : array());
        $settings['custom_field_groups']      = $this->sanitize_custom_field_groups(isset($settings['custom_field_groups']) ? $settings['custom_field_groups'] : array());

        return $settings;
    }

    private function set_member_save_error_notice($message) {
        $user_id = get_current_user_id();
        if ($user_id <= 0) {
            return;
        }

        set_transient(
            'feuer_mannschaft_member_save_error_' . $user_id,
            sanitize_text_field((string) $message),
            MINUTE_IN_SECONDS * 5
        );
    }

    private function get_member_save_error_notice() {
        $user_id = get_current_user_id();
        if ($user_id <= 0) {
            return '';
        }

        $key     = 'feuer_mannschaft_member_save_error_' . $user_id;
        $message = get_transient($key);

        if (! is_string($message) || '' === $message) {
            return '';
        }

        delete_transient($key);

        return $message;
    }

    private function sanitize_setting_color($color, $fallback = '') {
        $color = trim((string) $color);

        if (function_exists('sanitize_hex_color')) {
            $sanitized = sanitize_hex_color($color);
            if ($sanitized) {
                return $sanitized;
            }
        } elseif (preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $color)) {
            return strtolower($color);
        }

        return (string) $fallback;
    }

    private function get_member_title_class($post_id) {
        return $this->is_excluded_from_member_count($post_id)
            ? 'feuer-mannschaft-title-excluded'
            : '';
    }

    private function get_member_title_style($post_id) {
        if (! $this->is_excluded_from_member_count($post_id)) {
            return '';
        }

        $settings = $this->get_settings();
        $color    = $this->sanitize_setting_color(
            isset($settings['excluded_title_color']) ? $settings['excluded_title_color'] : '#b22222',
            '#b22222'
        );

        return '' !== $color ? sprintf('color: %s;', $color) : '';
    }

    private function sanitize_attachment_image_id($attachment_id) {
        $attachment_id = absint($attachment_id);

        return $attachment_id > 0 && wp_attachment_is_image($attachment_id)
            ? $attachment_id
            : 0;
    }

    private function get_default_profile_image_id() {
        $settings = $this->get_settings();

        return isset($settings['default_profile_image_id'])
            ? absint($settings['default_profile_image_id'])
            : 0;
    }

    private function get_default_profile_image_markup($size = 'large', $class = '') {
        $image_id = $this->get_default_profile_image_id();

        if (! $image_id) {
            return '';
        }

        return wp_get_attachment_image(
            $image_id,
            $size,
            false,
            array(
                'class'   => trim('img-fluid ' . $class),
                'loading' => 'lazy',
            )
        );
    }

    private function is_member_using_managed_default_image($post_id) {
        return '1' === (string) get_post_meta($post_id, self::DEFAULT_IMAGE_MANAGED_META, true);
    }

    private function is_member_default_image_opted_out($post_id) {
        return '1' === (string) get_post_meta($post_id, self::DEFAULT_IMAGE_OPT_OUT_META, true);
    }

    private function sync_default_profile_image_assignments($old_default_image_id, $new_default_image_id) {
        $member_ids = $this->get_member_post_ids();

        foreach ($member_ids as $member_id) {
            $this->update_member_default_thumbnail_state(
                $member_id,
                array(
                    'default_image_id'     => absint($new_default_image_id),
                    'previous_image_id'    => absint($old_default_image_id),
                    'respect_existing_opt' => true,
                )
            );
        }
    }

    public function sync_restored_member_default_thumbnail($post_id) {
        $this->update_member_default_thumbnail_state(
            $post_id,
            array(
                'respect_existing_opt' => true,
            )
        );
    }

    private function update_member_default_thumbnail_state($post_id, $args = array()) {
        $defaults = array(
            'default_image_id'     => $this->get_default_profile_image_id(),
            'previous_image_id'    => 0,
            'remove_default'       => false,
            'restore_default'      => false,
            'respect_existing_opt' => true,
        );
        $args = wp_parse_args($args, $defaults);

        $post_id           = absint($post_id);
        $default_image_id  = absint($args['default_image_id']);
        $previous_image_id = absint($args['previous_image_id']);
        $remove_default    = ! empty($args['remove_default']);
        $restore_default   = ! empty($args['restore_default']);
        $respect_opt_out   = ! empty($args['respect_existing_opt']);

        if (! $post_id || ! $this->is_member_post($post_id)) {
            return;
        }

        if (! empty($this->get_member_gallery($post_id))) {
            delete_post_meta($post_id, self::DEFAULT_IMAGE_MANAGED_META);
            delete_post_meta($post_id, self::DEFAULT_IMAGE_OPT_OUT_META);

            return;
        }

        $managed_thumbnail = $this->is_member_using_managed_default_image($post_id);
        $opted_out         = $this->is_member_default_image_opted_out($post_id);
        $current_thumbnail = (int) get_post_thumbnail_id($post_id);
        $managed_ids       = array_filter(array_unique(array_map('absint', array($default_image_id, $previous_image_id))));
        $has_custom_thumb  = $current_thumbnail > 0 && ! $managed_thumbnail && ! in_array($current_thumbnail, $managed_ids, true);

        if ($restore_default) {
            $opted_out = false;
            delete_post_meta($post_id, self::DEFAULT_IMAGE_OPT_OUT_META);
        }

        if ($remove_default) {
            update_post_meta($post_id, self::DEFAULT_IMAGE_OPT_OUT_META, 1);

            if ($managed_thumbnail || in_array($current_thumbnail, $managed_ids, true)) {
                delete_post_thumbnail($post_id);
            }

            delete_post_meta($post_id, self::DEFAULT_IMAGE_MANAGED_META);

            return;
        }

        if ($respect_opt_out && $opted_out) {
            if ($managed_thumbnail) {
                delete_post_thumbnail($post_id);
            }

            delete_post_meta($post_id, self::DEFAULT_IMAGE_MANAGED_META);

            return;
        }

        if ($has_custom_thumb) {
            delete_post_meta($post_id, self::DEFAULT_IMAGE_MANAGED_META);

            return;
        }

        if ($default_image_id > 0) {
            set_post_thumbnail($post_id, $default_image_id);
            update_post_meta($post_id, self::DEFAULT_IMAGE_MANAGED_META, 1);
            delete_post_meta($post_id, self::DEFAULT_IMAGE_OPT_OUT_META);

            return;
        }

        if ($managed_thumbnail || in_array($current_thumbnail, $managed_ids, true)) {
            delete_post_thumbnail($post_id);
        }

        delete_post_meta($post_id, self::DEFAULT_IMAGE_MANAGED_META);
    }

    private function get_list_shortcode_tags() {
        return array(
            'feuer_mannschaft',
            'feuer_mannschaft_list',
            'feuer_mannschaft_liste',
            'feuer_mannschaft_uebersicht',
            'feuer_mannschaft_gesamtliste',
        );
    }

    private function get_profile_shortcode_tags() {
        return array(
            'feuer_mannschaft_profile',
            'feuer_mannschaft_profil',
        );
    }

    private function get_count_shortcode_tags() {
        return array(
            'feuer_mannschaft_count',
            'feuer_mannschaft_anzahl',
        );
    }

    private function get_cards_shortcode_tags() {
        return array(
            'feuer_mannschaft_cards',
            'feuer_mannschaft_karten',
        );
    }

    private function get_all_frontend_shortcode_tags() {
        return array_values(
            array_unique(
                array_merge(
                    $this->get_list_shortcode_tags(),
                    $this->get_profile_shortcode_tags(),
                    $this->get_count_shortcode_tags(),
                    $this->get_cards_shortcode_tags(),
                    array(
                        'feuer_mannschaft_person',
                        'feuer_mannschaft_person_link',
                        'feuer_member',
                        'feuer_members',
                        'feuer_org_chart',
                        'feuer_mannschaft_org_chart',
                        'feuer_service_history',
                        'feuer_mannschaft_dienstverlauf',
                        'feuer_archived_members',
                        'feuer_mannschaft_ehemalige',
                    )
                )
            )
        );
    }

    private function get_allowed_category_ids() {
        $settings = $this->get_settings();
        $allowed  = isset($settings['allowed_categories']) ? (array) $settings['allowed_categories'] : array();

        return array_values(array_unique(array_map('absint', $allowed)));
    }

    private function get_allowed_category_ids_or_null() {
        $allowed_ids = $this->get_allowed_category_ids();

        return empty($allowed_ids) ? null : $allowed_ids;
    }

    private function get_category_choices($allowed_ids = null) {
        $terms = get_categories(
            array(
                'taxonomy'   => 'category',
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            )
        );

        if (empty($terms) || is_wp_error($terms)) {
            return array();
        }

        $children = array();
        foreach ($terms as $term) {
            $children[(int) $term->parent][] = $term;
        }

        foreach ($children as &$siblings) {
            usort(
                $siblings,
                function ($left, $right) {
                    return strnatcasecmp($left->name, $right->name);
                }
            );
        }
        unset($siblings);

        $choices = array();
        $visited = array();

        $walk = function ($parent_id, $depth) use (&$walk, &$choices, &$visited, $children, $allowed_ids) {
            if (empty($children[$parent_id])) {
                return;
            }

            foreach ($children[$parent_id] as $term) {
                if (isset($visited[$term->term_id])) {
                    continue;
                }

                $visited[$term->term_id] = true;

                if (null === $allowed_ids || in_array((int) $term->term_id, $allowed_ids, true)) {
                    $choices[] = array(
                        'term'  => $term,
                        'depth' => $depth,
                    );
                }

                $walk((int) $term->term_id, $depth + 1);
            }
        };

        $walk(0, 0);

        foreach ($terms as $term) {
            if (isset($visited[$term->term_id])) {
                continue;
            }

            if (null === $allowed_ids || in_array((int) $term->term_id, $allowed_ids, true)) {
                $choices[] = array(
                    'term'  => $term,
                    'depth' => 0,
                );
            }

            $walk((int) $term->term_id, 1);
        }

        return $choices;
    }

    private function get_member_category_id($post_id) {
        $category_ids = $this->get_member_category_ids($post_id);

        return empty($category_ids) ? 0 : (int) $category_ids[0];
    }

    private function get_member_category_ids($post_id) {
        $preferred_category = $this->get_preferred_member_category_id($post_id);

        return $preferred_category > 0 ? array($preferred_category) : array();
    }

    private function get_assigned_post_category_ids($post_id) {
        return array_values(array_map('absint', wp_get_post_categories($post_id, array('fields' => 'ids'))));
    }

    private function get_preferred_member_category_id($post_id) {
        return $this->get_preferred_category_id_from_ids($this->get_assigned_post_category_ids($post_id));
    }

    private function get_preferred_category_id_from_ids($category_ids) {
        $category_ids = array_values(array_filter(array_map('absint', (array) $category_ids)));
        $allowed_ids  = $this->get_allowed_category_ids();

        if (! empty($allowed_ids)) {
            $allowed_category_ids = array_values(array_intersect($category_ids, $allowed_ids));
            if (! empty($allowed_category_ids)) {
                $category_ids = $allowed_category_ids;
            }
        }

        if (empty($category_ids)) {
            return 0;
        }

        usort(
            $category_ids,
            function ($left, $right) {
                return $this->get_category_depth($right) <=> $this->get_category_depth($left);
            }
        );

        return (int) $category_ids[0];
    }

    private function get_category_depth($category_id) {
        return count(get_ancestors((int) $category_id, 'category', 'taxonomy'));
    }

    private function build_member_category_assignment_ids($selected_category, $preserve_ids = array()) {
        $selected_category = absint($selected_category);
        $preserve_ids      = array_values(array_filter(array_map('absint', (array) $preserve_ids)));

        if (! $selected_category || ! term_exists($selected_category, 'category')) {
            return $preserve_ids;
        }

        $assignment_ids = array_merge(
            $preserve_ids,
            array_values(array_map('absint', get_ancestors($selected_category, 'category', 'taxonomy'))),
            array($selected_category)
        );

        return array_values(array_unique(array_filter($assignment_ids)));
    }

    private function get_main_feuer_mannschaft_category_id() {
        $term = get_category_by_slug('feuer-mannschaft');

        return $term instanceof WP_Term ? (int) $term->term_id : 0;
    }

    private function is_relevant_member_category($category_id) {
        $category_id = absint($category_id);
        if (! $category_id || ! term_exists($category_id, 'category')) {
            return false;
        }

        $allowed_ids = $this->get_allowed_category_ids();
        $main_id     = $this->get_main_feuer_mannschaft_category_id();

        if ($main_id && ($category_id === $main_id || cat_is_ancestor_of($main_id, $category_id))) {
            return true;
        }

        if (in_array($category_id, $allowed_ids, true)) {
            return true;
        }

        foreach ($allowed_ids as $allowed_id) {
            if (cat_is_ancestor_of($category_id, $allowed_id) || cat_is_ancestor_of($allowed_id, $category_id)) {
                return true;
            }
        }

        return false;
    }

    private function get_member_back_url($post_id) {
        $archive_url = get_post_type_archive_link(self::POST_TYPE);
        if ($archive_url) {
            return $archive_url;
        }

        $category_id = $this->get_member_category_id($post_id);

        if ($category_id > 0) {
            $category_link = get_category_link($category_id);
            if (! is_wp_error($category_link)) {
                return $category_link;
            }
        }

        $posts_page_id = (int) get_option('page_for_posts');
        if ($posts_page_id > 0) {
            $posts_page_url = get_permalink($posts_page_id);
            if ($posts_page_url) {
                return $posts_page_url;
            }
        }

        return home_url('/');
    }

    private function is_feuer_mannschaft_post($post) {
        $post = get_post($post);

        return $post instanceof WP_Post
            && self::POST_TYPE === $post->post_type
            && '1' === (string) get_post_meta($post->ID, self::PROFILE_META, true);
    }

    private function is_member_post($post) {
        return $this->is_feuer_mannschaft_post($post);
    }

    private function get_invite_url($post_id) {
        return (string) get_post_meta($post_id, self::INVITE_URL_META, true);
    }

    private function is_excluded_from_member_count($post_id) {
        return '1' === (string) get_post_meta($post_id, self::EXCLUDE_FROM_COUNT_META, true);
    }

    private function sanitize_invite_url($value) {
        $value = trim((string) $value);

        if ('' === $value) {
            return '';
        }

        $sanitized = esc_url_raw($value, array('http', 'https', 'mailto', 'tel'));
        if ('' !== $sanitized) {
            return $sanitized;
        }

        return esc_url_raw(home_url('/' . ltrim($value, '/')), array('http', 'https'));
    }

    private function is_member_request() {
        $request_value = 0;

        if (isset($_GET['feuer_mannschaft_profile'])) {
            $request_value = absint(wp_unslash($_GET['feuer_mannschaft_profile']));
        } elseif (isset($_POST['feuer_mannschaft_profile'])) {
            $request_value = absint(wp_unslash($_POST['feuer_mannschaft_profile']));
        }

        return 1 === $request_value;
    }

    private function is_member_category_archive() {
        if (! is_category()) {
            return false;
        }

        $term = get_queried_object();

        return $term instanceof WP_Term && $this->is_relevant_member_category((int) $term->term_id);
    }

    private function is_member_list_request() {
        global $pagenow;

        if (! is_admin() || 'edit.php' !== $pagenow) {
            return false;
        }

        $post_type = isset($_GET['post_type']) ? sanitize_key(wp_unslash($_GET['post_type'])) : self::POST_TYPE;

        return self::POST_TYPE === $post_type && $this->is_member_request();
    }

    private function is_member_edit_context() {
        global $post, $pagenow;

        if (! is_admin() || ! in_array($pagenow, array('post.php', 'post-new.php'), true)) {
            return false;
        }

        if ('post-new.php' === $pagenow) {
            $post_type = isset($_GET['post_type']) ? sanitize_key(wp_unslash($_GET['post_type'])) : self::POST_TYPE;

            return self::POST_TYPE === $post_type && $this->is_member_request();
        }

        if ($post instanceof WP_Post && $this->is_feuer_mannschaft_post($post)) {
            return true;
        }

        $post_id = isset($_GET['post']) ? absint(wp_unslash($_GET['post'])) : 0;

        return $post_id > 0 && $this->is_feuer_mannschaft_post($post_id);
    }

    private function is_settings_screen() {
        return is_admin() && isset($_GET['page']) && 'feuer-mannschaft-settings' === sanitize_key(wp_unslash($_GET['page']));
    }

    private function is_feuer_mannschaft_admin_context() {
        return is_admin() && ($this->is_member_list_request() || $this->is_member_edit_context() || $this->is_settings_screen());
    }

    private function get_member_list_url() {
        return admin_url($this->get_member_list_menu_slug());
    }

    private function get_add_new_url() {
        return admin_url($this->get_add_new_menu_slug());
    }

    private function get_member_list_menu_slug() {
        return 'edit.php?post_type=' . self::POST_TYPE . '&feuer_mannschaft_profile=1';
    }

    private function get_add_new_menu_slug() {
        return 'post-new.php?post_type=' . self::POST_TYPE . '&feuer_mannschaft_profile=1';
    }

    private function can_remove_member() {
        if (is_multisite()) {
            return is_super_admin();
        }

        return current_user_can('manage_options')
            && current_user_can('activate_plugins')
            && current_user_can('delete_users');
    }

    private function can_remove_member_by_user($user_id) {
        if (is_multisite()) {
            return is_super_admin($user_id);
        }

        return user_can($user_id, 'manage_options')
            && user_can($user_id, 'activate_plugins')
            && user_can($user_id, 'delete_users');
    }

    public function filter_admin_body_class($classes) {
        if ($this->is_member_list_request()) {
            $classes .= ' feuer-mannschaft-profile-list';
        }

        if ($this->is_member_edit_context()) {
            $classes .= ' feuer-mannschaft-profile-screen';
        }

        if ($this->is_settings_screen()) {
            $classes .= ' feuer-mannschaft-settings-screen';
        }

        return trim($classes);
    }

    public function filter_frontend_body_class($classes) {
        if (is_admin()) {
            return $classes;
        }

        if ((is_singular(self::POST_TYPE) && $this->is_member_post(get_queried_object_id())) || $this->is_shortcode_profile_context()) {
            $classes[] = 'feuer-mannschaft-single-layout';
        }

        $settings = $this->get_settings();
        $classes[] = 'feuer-card-preset-' . sanitize_html_class(isset($settings['card_preset']) ? $settings['card_preset'] : 'classic');
        $classes[] = 'feuer-card-text-' . sanitize_html_class(isset($settings['card_text_position']) ? $settings['card_text_position'] : 'bottom');
        if (empty($settings['card_show_rank'])) { $classes[] = 'feuer-card-hide-rank'; }
        if (empty($settings['card_show_function'])) { $classes[] = 'feuer-card-hide-function'; }

        return $classes;
    }

    private function is_shortcode_profile_context() {
        if (! is_singular()) {
            return false;
        }

        $post = get_post();
        if (! $post instanceof WP_Post) {
            return false;
        }

        foreach ($this->get_profile_shortcode_tags() as $shortcode) {
            if (has_shortcode($post->post_content, $shortcode)) {
                return true;
            }
        }

        return false;
    }

    private function is_profile_shortcode_page() {
        if (! is_page()) {
            return false;
        }

        return $this->is_shortcode_profile_context();
    }

    public function filter_parent_file($parent_file) {
        if ($this->is_feuer_mannschaft_admin_context()) {
            return 'feuer-mannschaft';
        }

        return $parent_file;
    }

    public function filter_submenu_file($submenu_file) {
        global $pagenow;

        if ($this->is_settings_screen()) {
            return 'feuer-mannschaft-settings';
        }

        if ($this->is_member_list_request()) {
            return $this->get_member_list_menu_slug();
        }

        if ($this->is_member_edit_context()) {
            return 'post-new.php' === $pagenow ? $this->get_add_new_menu_slug() : $this->get_member_list_menu_slug();
        }

        return $submenu_file;
    }

    public function filter_revision_author_display_name($display_name, $user_id = 0, $original_user_id = 0) {
        if (! $this->is_revision_author_context()) {
            return $display_name;
        }

        $target_user_id = absint($user_id ? $user_id : $original_user_id);

        return $this->get_preferred_revision_author_name($target_user_id, $display_name);
    }

    public function filter_revision_author_name($display_name, $user_id = 0) {
        if (! $this->is_revision_author_context()) {
            return $display_name;
        }

        return $this->get_preferred_revision_author_name(absint($user_id), $display_name);
    }

    public function filter_revision_author_data_for_js($revision_data, $revision = null, $post = null) {
        unset($post);

        if (! is_array($revision_data) || ! $this->is_revision_author_context()) {
            return $revision_data;
        }

        $user_id = 0;

        if ($revision instanceof WP_Post) {
            $user_id = (int) $revision->post_author;
        } elseif (isset($revision_data['author']['id'])) {
            $user_id = absint($revision_data['author']['id']);
        }

        if ($user_id > 0) {
            if (! isset($revision_data['author']) || ! is_array($revision_data['author'])) {
                $revision_data['author'] = array();
            }

            $revision_data['author']['name'] = $this->get_preferred_revision_author_name(
                $user_id,
                isset($revision_data['author']['name']) ? (string) $revision_data['author']['name'] : ''
            );
        }

        return $revision_data;
    }

    public function filter_delete_caps($caps, $cap, $user_id, $args) {
        if ('delete_post' !== $cap || empty($args[0])) {
            return $caps;
        }

        $post_id = absint($args[0]);

        if (! $post_id || ! $this->is_feuer_mannschaft_post($post_id)) {
            return $caps;
        }

        return $this->can_remove_member_by_user($user_id) ? $caps : array('do_not_allow');
    }

    private function is_revision_author_context() {
        if (! is_admin() && ! wp_doing_ajax()) {
            return false;
        }

        global $pagenow;

        if (! in_array($pagenow, array('post.php', 'revision.php', 'admin-ajax.php'), true)) {
            return false;
        }

        if ('post.php' === $pagenow && $this->is_member_request()) {
            return true;
        }

        $post_id = $this->get_revision_context_post_id();

        return $post_id > 0 && $this->is_feuer_mannschaft_post($post_id);
    }

    private function get_revision_context_post_id() {
        foreach (array('post', 'post_id', 'post_ID', 'revision', 'revision_id', 'from', 'to') as $key) {
            if (empty($_REQUEST[$key])) {
                continue;
            }

            $post_id = absint(wp_unslash($_REQUEST[$key]));

            if (! $post_id) {
                continue;
            }

            $parent_id = wp_is_post_revision($post_id);

            return $parent_id ? (int) $parent_id : (int) $post_id;
        }

        return 0;
    }

    private function get_preferred_revision_author_name($user_id, $fallback = '') {
        $user_id = absint($user_id);

        if (! $user_id) {
            return (string) $fallback;
        }

        $user = get_userdata($user_id);

        if (! $user instanceof WP_User) {
            return (string) $fallback;
        }

        $first_name = trim((string) $user->first_name);
        $last_name  = trim((string) $user->last_name);
        $full_name  = trim($first_name . ' ' . $last_name);

        if ('' !== $full_name) {
            return $full_name;
        }

        if (! empty($user->display_name)) {
            return (string) $user->display_name;
        }

        if (! empty($user->user_login)) {
            return (string) $user->user_login;
        }

        return (string) $fallback;
    }

    public function maybe_migrate_legacy_members() {
        if (! is_admin() || ! current_user_can('manage_options') || get_option(self::LEGACY_POST_TYPE_REPAIR_OPTION)) {
            return;
        }

        global $wpdb;
        $legacy_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type IN (%s, %s) ORDER BY ID ASC",
                self::LEGACY_POST_TYPE,
                self::LEGACY_POST_TYPE_TRUNCATED
            )
        );
        $legacy_posts = array_filter(array_map('get_post', array_map('absint', (array) $legacy_ids)));
        $migration_failed = false;

        foreach ($legacy_posts as $legacy_post) {
            if (! $legacy_post instanceof WP_Post || $this->get_migrated_member_post_id($legacy_post->ID)) {
                continue;
            }

            $new_post_id = wp_insert_post(
                array(
                    'post_type'      => self::POST_TYPE,
                    'post_status'    => 'feuer_archived' === $legacy_post->post_status ? 'feuer_archived' : $legacy_post->post_status,
                    'post_author'    => $legacy_post->post_author,
                    'post_date'      => $legacy_post->post_date,
                    'post_date_gmt'  => $legacy_post->post_date_gmt,
                    'post_title'     => $legacy_post->post_title,
                    'post_name'      => $legacy_post->post_name,
                    'post_excerpt'   => $legacy_post->post_excerpt,
                    'post_content'   => $legacy_post->post_content,
                    'comment_status' => $legacy_post->comment_status,
                    'ping_status'    => $legacy_post->ping_status,
                ),
                true
            );

            if (is_wp_error($new_post_id) || ! $new_post_id) {
                $migration_failed = true;
                continue;
            }

            update_post_meta($new_post_id, self::PROFILE_META, 1);
            update_post_meta($new_post_id, self::META_PREFIX . 'legacy_source_id', $legacy_post->ID);

            foreach (array('first_name', 'last_name', 'job_title', 'entry_date', 'rank', 'function', 'education', 'lehrgang', 'sort_order', 'description') as $meta_key) {
                $value = get_post_meta($legacy_post->ID, self::META_PREFIX . $meta_key, true);
                if ('' !== $value && null !== $value) {
                    update_post_meta($new_post_id, self::META_PREFIX . $meta_key, $value);
                }
            }

            $gallery = get_post_meta($legacy_post->ID, self::GALLERY_META, true);
            if (is_array($gallery) && ! empty($gallery)) {
                update_post_meta($new_post_id, self::GALLERY_META, array_values(array_map('absint', $gallery)));
            }

            $category_ids = wp_get_post_categories($legacy_post->ID, array('fields' => 'ids'));
            if (! empty($category_ids)) {
                wp_set_post_categories($new_post_id, array_values(array_map('absint', $category_ids)), false);
            }

            $thumbnail_id = get_post_thumbnail_id($legacy_post->ID);
            if ($thumbnail_id) {
                set_post_thumbnail($new_post_id, $thumbnail_id);
            }
        }

        if (! $migration_failed) {
            update_option(self::MIGRATION_OPTION, time(), false);
            update_option(self::LEGACY_POST_TYPE_REPAIR_OPTION, time(), false);
        }
    }

    public function maybe_sync_member_category_hierarchy() {
        if (! is_admin() || ! current_user_can('manage_options') || get_option(self::CATEGORY_SYNC_OPTION)) {
            return;
        }

        $post_ids = get_posts(
            array(
                'post_type'      => self::POST_TYPE,
                'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'feuer_archived'),
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => array(
                    array(
                        'key'   => self::PROFILE_META,
                        'value' => '1',
                    ),
                ),
            )
        );

        foreach ($post_ids as $post_id) {
            $post_id            = absint($post_id);
            $current_categories = $this->get_assigned_post_category_ids($post_id);
            $selected_category  = $this->get_preferred_category_id_from_ids($current_categories);

            if (! $post_id || ! $selected_category) {
                continue;
            }

            $assignment_ids = $this->build_member_category_assignment_ids($selected_category, $current_categories);
            if ($assignment_ids !== $current_categories) {
                wp_set_post_categories($post_id, $assignment_ids, false);
            }
        }

        update_option(self::CATEGORY_SYNC_OPTION, time(), false);
    }

    private function get_migrated_member_post_id($legacy_post_id) {
        $posts = get_posts(
            array(
                'post_type'      => self::POST_TYPE,
                'post_status'    => array('publish', 'private', 'draft', 'pending', 'future', 'feuer_archived'),
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_key'       => self::META_PREFIX . 'legacy_source_id',
                'meta_value'     => (int) $legacy_post_id,
            )
        );

        return empty($posts) ? 0 : (int) $posts[0];
    }

    private function mb_char($value) {
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, 1);
        }

        return substr($value, 0, 1);
    }

    private function clamp_int($value, $min, $max) {
        $value = absint($value);

        if ($value < $min) {
            return $min;
        }

        if ($value > $max) {
            return $max;
        }

        return $value;
    }
}

<?php

if (! defined('ABSPATH')) {
    exit;
}

class Feuer_Mannschaft_Mentions {
    const META_KEY = 'mentioned_persons';

    private $logger;

    public function __construct($logger = null) {
        $this->logger = $logger;
    }

    public function register_hooks() {
        add_action('init', array($this, 'register_blocks'));
        add_action('acf/init', array($this, 'register_acf_field_group'));
        add_filter('acf/fields/relationship/query/name=' . self::META_KEY, array($this, 'filter_acf_relationship_query'), 10, 3);
        add_filter('acf/prepare_field/name=' . self::META_KEY, array($this, 'filter_acf_prepare_field'));

        add_action('add_meta_boxes', array($this, 'register_fallback_meta_boxes'), 30);
        add_action('add_meta_boxes', array($this, 'register_shortcode_meta_boxes'), 35);
        add_action('save_post', array($this, 'save_fallback_mentions'), 20, 2);

        foreach ($this->get_member_link_shortcode_tags() as $shortcode_tag) {
            add_shortcode($shortcode_tag, array($this, 'shortcode_member_link'));
        }

        add_shortcode('feuer_mannschaft_member_posts', array($this, 'shortcode_member_posts'));
        add_shortcode('feuer_member_posts', array($this, 'shortcode_member_posts'));
        add_shortcode('feuer_mannschaft_mentioned_members', array($this, 'shortcode_mentioned_members'));
    }

    public function register_blocks() {
        if (! function_exists('register_block_type')) {
            return;
        }

        wp_register_script(
            'feuer-mannschaft-blocks',
            FEUER_MANNSCHAFT_PLUGIN_URL . 'assets/js/blocks.js',
            array('wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n'),
            FEUER_MANNSCHAFT_VERSION,
            true
        );

        $member_options = array(array('label' => __('Teilnehmer auswählen', 'feuer-mannschaft'), 'value' => 0));
        foreach ($this->get_member_choices() as $member) {
            $member_options[] = array(
                'label' => get_the_title($member),
                'value' => (int) $member->ID,
            );
        }

        wp_localize_script(
            'feuer-mannschaft-blocks',
            'feuerMannschaftBlocks',
            array('members' => $member_options)
        );

        register_block_type('feuer-mannschaft/member', array(
            'api_version'     => 3,
            'editor_script'   => 'feuer-mannschaft-blocks',
            'render_callback' => array($this, 'render_member_block'),
            'attributes'      => array(
                'memberId' => array('type' => 'integer', 'default' => 0),
                'view'     => array('type' => 'string', 'default' => 'card'),
            ),
        ));

        register_block_type('feuer-mannschaft/mentioned-members', array(
            'api_version'     => 3,
            'editor_script'   => 'feuer-mannschaft-blocks',
            'uses_context'    => array('postId'),
            'render_callback' => array($this, 'render_mentioned_members_block'),
            'attributes'      => array(
                'view' => array('type' => 'string', 'default' => 'links'),
            ),
        ));

        register_block_type('feuer-mannschaft/member-posts', array(
            'api_version'     => 3,
            'editor_script'   => 'feuer-mannschaft-blocks',
            'uses_context'    => array('postId'),
            'render_callback' => array($this, 'render_member_posts_block'),
            'attributes'      => array(
                'memberId' => array('type' => 'integer', 'default' => 0),
                'limit'    => array('type' => 'integer', 'default' => 6),
            ),
        ));

        register_block_type('feuer-mannschaft/members', array(
            'api_version' => 3, 'editor_script' => 'feuer-mannschaft-blocks',
            'render_callback' => array($this, 'render_members_block'),
            'attributes' => array(
                'group' => array('type'=>'string','default'=>''),
                'department' => array('type'=>'string','default'=>''),
                'limit' => array('type'=>'integer','default'=>8),
            ),
        ));
        register_block_type('feuer-mannschaft/org-chart', array(
            'api_version'=>3,'editor_script'=>'feuer-mannschaft-blocks','render_callback'=>array($this,'render_org_chart_block'),
        ));
        register_block_type('feuer-mannschaft/service-history', array(
            'api_version'=>3,'editor_script'=>'feuer-mannschaft-blocks','uses_context'=>array('postId'),
            'render_callback'=>array($this,'render_service_history_block'),
            'attributes'=>array('memberId'=>array('type'=>'integer','default'=>0)),
        ));
        register_block_type('feuer-mannschaft/archived-members', array(
            'api_version'=>3,'editor_script'=>'feuer-mannschaft-blocks','render_callback'=>array($this,'render_archived_members_block'),
            'attributes'=>array('limit'=>array('type'=>'integer','default'=>24)),
        ));
    }

    public function register_acf_field_group() {
        if (! function_exists('acf_add_local_field_group') || ! $this->is_enabled()) {
            return;
        }

        $locations = array();

        foreach ($this->get_supported_post_types() as $post_type) {
            $locations[] = array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => $post_type,
                ),
            );
        }

        if (empty($locations)) {
            return;
        }

        acf_add_local_field_group(
            array(
                'key'      => 'group_feuer_mannschaft_mentions',
                'title'    => __('Erwähnte Personen', 'feuer-mannschaft'),
                'fields'   => array(
                    array(
                        'key'           => 'field_feuer_mannschaft_mentioned_persons',
                        'label'         => __('Erwähnte Personen', 'feuer-mannschaft'),
                        'name'          => self::META_KEY,
                        'type'          => 'relationship',
                        'post_type'     => array(Feuer_Mannschaft_Plugin::POST_TYPE),
                        'filters'       => array('search'),
                        'elements'      => array(),
                        'return_format' => 'id',
                        'min'           => 0,
                        'max'           => 0,
                        'instructions'  => __('Wählen Sie erwähnte Personen. Darunter werden passende Kurzcodes mit Profil-Link angezeigt.', 'feuer-mannschaft'),
                    ),
                ),
                'location' => $locations,
                'position' => 'normal',
                'style'    => 'default',
                'active'   => true,
            )
        );
    }
    public function filter_acf_relationship_query($args, $field, $post_id) {
        unset($field, $post_id);

        $args['post_type']   = array(Feuer_Mannschaft_Plugin::POST_TYPE);
        $args['post_status'] = array('publish');
        $args['meta_query']  = array(
            array(
                'key'   => Feuer_Mannschaft_Plugin::PROFILE_META,
                'value' => '1',
            ),
        );
        $args['orderby']     = 'title';
        $args['order']       = 'ASC';

        return $args;
    }

    public function filter_acf_prepare_field($field) {
        if (! $this->is_enabled()) {
            return false;
        }

        $post_id = $this->get_current_post_id();
        $post_type = $post_id ? get_post_type($post_id) : (isset($_GET['post_type']) ? sanitize_key(wp_unslash($_GET['post_type'])) : 'post');

        if ($post_id && $this->is_member_post($post_id)) {
            return false;
        }

        if (isset($_GET['feuer_mannschaft_profile']) && 1 === absint(wp_unslash($_GET['feuer_mannschaft_profile']))) {
            return false;
        }

        if (! in_array($post_type, $this->get_supported_post_types(), true)) {
            return false;
        }

        return $field;
    }

    public function register_fallback_meta_boxes() {
        if (function_exists('acf_add_local_field_group') || ! $this->is_enabled()) {
            return;
        }

        if (isset($_GET['feuer_mannschaft_profile']) && 1 === absint(wp_unslash($_GET['feuer_mannschaft_profile']))) {
            return;
        }

        foreach ($this->get_supported_post_types() as $post_type) {
            add_meta_box(
                'feuer_mannschaft_mentions',
                __('Erwähnte Personen', 'feuer-mannschaft'),
                array($this, 'render_fallback_meta_box'),
                $post_type,
                'side',
                'default'
            );
        }
    }
    public function register_shortcode_meta_boxes() {
        if (! $this->is_enabled()) {
            return;
        }

        if (isset($_GET['feuer_mannschaft_profile']) && 1 === absint(wp_unslash($_GET['feuer_mannschaft_profile']))) {
            return;
        }

        foreach ($this->get_supported_post_types() as $post_type) {
            add_meta_box(
                'feuer_mannschaft_mention_shortcodes',
                __('Erwähnte Personen: Kurzcodes', 'feuer-mannschaft'),
                array($this, 'render_shortcode_meta_box'),
                $post_type,
                'normal',
                'default'
            );
        }
    }
    public function render_fallback_meta_box($post) {
        if (! $post instanceof WP_Post || $this->is_member_post($post->ID)) {
            $message    = __('Dieser Bereich wird bei Teilnehmerprofilen nicht verwendet.', 'feuer-mannschaft');
            $class_name = '';
            include FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/admin/message.php';
            return;
        }

        $selected_ids = $this->normalize_selected_ids(get_post_meta($post->ID, self::META_KEY, true));
        $members      = $this->get_member_choices();

        wp_nonce_field('feuer_mannschaft_mentions_save', 'feuer_mannschaft_mentions_nonce');

        if (empty($members)) {
            $message    = __('Es sind keine veröffentlichten Teilnehmer verfügbar.', 'feuer-mannschaft');
            $class_name = '';
            include FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/admin/message.php';
            return;
        }

        $meta_key = self::META_KEY;
        include FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/admin/mention-fallback.php';
    }
    public function render_shortcode_meta_box($post) {
        if (! $post instanceof WP_Post || $this->is_member_post($post->ID)) {
            $message    = __('Dieser Bereich wird bei Teilnehmerprofilen nicht verwendet.', 'feuer-mannschaft');
            $class_name = '';
            include FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/admin/message.php';
            return;
        }

        $selected_ids = $this->normalize_selected_ids(get_post_meta($post->ID, self::META_KEY, true));
        $items        = $this->build_shortcode_items($selected_ids);
        $template     = FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/admin/mention-shortcodes.php';

        if (! file_exists($template)) {
            return;
        }

        $empty_message = __('Wählen Sie zuerst eine oder mehrere erwähnte Personen aus. Danach erscheinen hier Kurzcode und Vorschau mit Profil-Link.', 'feuer-mannschaft');

        include $template;
    }
    public function save_fallback_mentions($post_id, $post) {
        if (function_exists('acf_add_local_field_group') || ! $this->is_enabled()) {
            return;
        }

        if (! $post instanceof WP_Post || wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        if (! in_array($post->post_type, $this->get_supported_post_types(), true) || $this->is_member_post($post_id)) {
            return;
        }

        if (! isset($_POST['feuer_mannschaft_mentions_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feuer_mannschaft_mentions_nonce'])), 'feuer_mannschaft_mentions_save')) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        $before = $this->normalize_selected_ids(get_post_meta($post_id, self::META_KEY, true));
        $after  = $this->sanitize_selected_ids(isset($_POST[self::META_KEY]) ? wp_unslash($_POST[self::META_KEY]) : array());

        if (empty($after)) {
            delete_post_meta($post_id, self::META_KEY);
        } else {
            update_post_meta($post_id, self::META_KEY, array_map('strval', $after));
        }

        if ($before !== $after && $this->logger instanceof Feuer_Mannschaft_Logger) {
            $this->logger->log(
                'mentions_updated',
                'content',
                $post_id,
                get_the_title($post_id),
                array(
                    array(
                        'field'  => __('Erwähnungen', 'feuer-mannschaft'),
                        'before' => $this->format_member_ids_for_log($before),
                        'after'  => $this->format_member_ids_for_log($after),
                    ),
                )
            );
        }
    }

    public function render_mentions_block($member_id) {
        $member_id = absint($member_id);

        if (! $member_id || ! $this->is_enabled()) {
            return '';
        }

        $mentions = $this->get_mentions_for_member($member_id);

        if (empty($mentions['items'])) {
            return '';
        }

        $template = FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/frontend/mentions-section.php';

        if (! file_exists($template)) {
            return '';
        }

        $items = $mentions['items'];
        $count = $mentions['count'];

        ob_start();
        include $template;

        return ob_get_clean();
    }

    public function shortcode_member_link($atts) {
        $atts = shortcode_atts(
            array(
                'id'   => 0,
                'text' => '',
                'link' => '1',
            ),
            $atts,
            'feuer_mannschaft_person'
        );

        $member_id = absint($atts['id']);
        if (! $member_id || ! $this->is_member_post($member_id)) {
            return '';
        }

        $label = sanitize_text_field((string) $atts['text']);
        if ('' === $label) {
            $label = get_the_title($member_id);
        }

        if ('' === $label) {
            return '';
        }

        $is_link_enabled = ! in_array(strtolower((string) $atts['link']), array('0', 'false', 'no', 'nein'), true);
        $url             = get_permalink($member_id);
        $post_status     = get_post_status($member_id);

        if (! $is_link_enabled || ! $url || 'publish' !== $post_status) {
            return esc_html($label);
        }

        return $this->render_frontend_template(
            'member-link',
            array(
                'url'   => $url,
                'label' => $label,
            )
        );
    }

    public function render_member_block($attributes) {
        $member_id = isset($attributes['memberId']) ? absint($attributes['memberId']) : 0;
        $view      = isset($attributes['view']) ? sanitize_key($attributes['view']) : 'card';

        if (! $member_id || ! $this->is_member_post($member_id)) {
            return '';
        }

        if ('link' === $view) {
            return $this->shortcode_member_link(array('id' => $member_id));
        }

        return Feuer_Mannschaft_Plugin::instance()->shortcode_profile(array('id' => $member_id, 'template' => 'compact'));
    }

    public function render_mentioned_members_block($attributes, $content = '', $block = null) {
        unset($content);
        $post_id = $this->resolve_block_post_id($block);
        $view    = isset($attributes['view']) ? sanitize_key($attributes['view']) : 'links';

        return $this->render_mentioned_members($post_id, $view);
    }

    public function render_member_posts_block($attributes, $content = '', $block = null) {
        unset($content);
        $member_id = isset($attributes['memberId']) ? absint($attributes['memberId']) : 0;
        if (! $member_id) {
            $member_id = $this->resolve_block_post_id($block);
        }

        $limit = isset($attributes['limit']) ? min(24, max(1, absint($attributes['limit']))) : 6;

        return $this->render_member_posts($member_id, $limit);
    }

    public function render_members_block($attributes) {
        return Feuer_Mannschaft_Plugin::instance()->shortcode_members($attributes);
    }
    public function render_org_chart_block() {
        return Feuer_Mannschaft_Plugin::instance()->shortcode_org_chart(array());
    }
    public function render_service_history_block($attributes, $content = '', $block = null) {
        unset($content);
        $id = ! empty($attributes['memberId']) ? absint($attributes['memberId']) : $this->resolve_block_post_id($block);
        return Feuer_Mannschaft_Plugin::instance()->shortcode_service_history(array('id'=>$id));
    }
    public function render_archived_members_block($attributes) {
        return Feuer_Mannschaft_Plugin::instance()->shortcode_archived_members($attributes);
    }

    public function shortcode_member_posts($atts) {
        $atts = shortcode_atts(array('id' => 0, 'limit' => 6), $atts, 'feuer_member_posts');
        $member_id = absint($atts['id']);
        if (! $member_id && is_singular(Feuer_Mannschaft_Plugin::POST_TYPE)) {
            $member_id = get_queried_object_id();
        }

        return $this->render_member_posts($member_id, min(24, max(1, absint($atts['limit']))));
    }

    public function shortcode_mentioned_members($atts) {
        $atts = shortcode_atts(array('post_id' => 0, 'view' => 'links'), $atts, 'feuer_mannschaft_mentioned_members');
        $post_id = absint($atts['post_id']);
        if (! $post_id) {
            $post_id = get_the_ID();
        }

        return $this->render_mentioned_members($post_id, sanitize_key($atts['view']));
    }

    private function render_mentioned_members($post_id, $view) {
        $post_id = absint($post_id);
        if (! $post_id || ! $this->is_enabled()) {
            return '';
        }

        $member_ids = $this->normalize_selected_ids(get_post_meta($post_id, self::META_KEY, true));
        if (empty($member_ids)) {
            return '';
        }

        $items = array();
        foreach ($member_ids as $member_id) {
            if (! $this->is_member_post($member_id) || 'publish' !== get_post_status($member_id)) {
                continue;
            }

            $items[] = 'cards' === $view
                ? Feuer_Mannschaft_Plugin::instance()->shortcode_profile(array('id' => $member_id, 'template' => 'compact'))
                : $this->shortcode_member_link(array('id' => $member_id));
        }

        if (empty($items)) {
            return '';
        }

        $class_name = 'cards' === $view ? 'feuer-mannschaft-mentioned-members is-cards' : 'feuer-mannschaft-mentioned-members is-links';
        return sprintf('<div class="%1$s">%2$s</div>', esc_attr($class_name), implode('', array_map(function ($item) {
            return '<div class="feuer-mannschaft-mentioned-member">' . $item . '</div>';
        }, $items)));
    }

    private function render_member_posts($member_id, $limit) {
        $member_id = absint($member_id);
        if (! $member_id || ! $this->is_member_post($member_id)) {
            return '';
        }

        $mentions = $this->get_mentions_for_member($member_id, array('limit' => $limit));
        if (empty($mentions['items'])) {
            return '';
        }

        return $this->render_frontend_template('member-posts', array(
            'items'     => $mentions['items'],
            'member_id' => $member_id,
        ));
    }

    private function resolve_block_post_id($block) {
        if (is_object($block) && isset($block->context['postId'])) {
            return absint($block->context['postId']);
        }

        return get_the_ID();
    }

    public function is_editor_context() {
        if (! is_admin() || ! $this->is_enabled()) {
            return false;
        }

        global $pagenow;

        if (! in_array($pagenow, array('post.php', 'post-new.php'), true)) {
            return false;
        }

        $post_id   = $this->get_current_post_id();
        $post_type = $post_id ? get_post_type($post_id) : (isset($_GET['post_type']) ? sanitize_key(wp_unslash($_GET['post_type'])) : 'post');

        if (! in_array($post_type, $this->get_supported_post_types(), true)) {
            return false;
        }

        return ! $this->is_member_post($post_id);
    }

    public function get_editor_assets_data() {
        $members = array();

        foreach ($this->get_member_choices() as $member) {
            $member_id = (int) $member->ID;
            $url       = get_permalink($member_id);

            if (! $url) {
                continue;
            }

            $members[(string) $member_id] = array(
                'id'        => $member_id,
                'name'      => get_the_title($member),
                'url'       => $url,
                'shortcode' => sprintf('[feuer_mannschaft_person id="%d"]', $member_id),
            );
        }

        return array(
            'members' => $members,
            'texts'   => array(
                'empty'        => __('Wählen Sie zuerst eine oder mehrere erwähnte Personen aus. Danach erscheinen hier Kurzcode und Vorschau mit Profil-Link.', 'feuer-mannschaft'),
                'shortcode'    => __('Kurzcode', 'feuer-mannschaft'),
                'preview'      => __('Vorschau', 'feuer-mannschaft'),
                'openProfile'  => __('Profil öffnen', 'feuer-mannschaft'),
                'copy'         => __('Kopieren', 'feuer-mannschaft'),
                'copied'       => __('Kopiert', 'feuer-mannschaft'),
                'insert'       => __('Einfügen', 'feuer-mannschaft'),
                'inserted'     => __('Eingefügt', 'feuer-mannschaft'),
                'insertFailed' => __('Bitte den Kurzcode manuell kopieren.', 'feuer-mannschaft'),
            ),
        );
    }
    public function get_supported_post_types() {
        $settings   = get_option(Feuer_Mannschaft_Plugin::OPTION_KEY, array());
        $post_types = isset($settings['mention_post_types']) ? (array) $settings['mention_post_types'] : array('post');
        $allowed    = get_post_types(
            array(
                'public' => true,
            ),
            'names'
        );

        $allowed = array_values(array_diff((array) $allowed, array('attachment')));

        $post_types = array_values(
            array_unique(
                array_filter(
                    array_map('sanitize_key', $post_types),
                    function ($post_type) use ($allowed) {
                        return in_array($post_type, $allowed, true);
                    }
                )
            )
        );

        if (empty($post_types)) {
            $post_types[] = 'post';
        }

        return $post_types;
    }

    public function is_enabled() {
        $settings = get_option(Feuer_Mannschaft_Plugin::OPTION_KEY, array());

        return ! isset($settings['enable_mentions']) || ! empty($settings['enable_mentions']);
    }

    public function get_mentions_for_member($member_id, $args = array()) {
        $member_id = absint($member_id);

        if (! $member_id) {
            return array(
                'count' => 0,
                'items' => array(),
            );
        }

        $args = wp_parse_args($args, array('limit' => -1));
        $limit = -1 === (int) $args['limit'] ? -1 : min(100, max(1, absint($args['limit'])));

        $query = new WP_Query(
            array(
                'post_type'      => $this->get_supported_post_types(),
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'meta_query'     => array(
                    'relation' => 'OR',
                    array(
                        'key'     => self::META_KEY,
                        'value'   => '"' . $member_id . '"',
                        'compare' => 'LIKE',
                    ),
                    array(
                        'key'     => self::META_KEY,
                        'value'   => 'i:' . $member_id . ';',
                        'compare' => 'LIKE',
                    ),
                ),
            )
        );

        $items = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $post_type_object = get_post_type_object(get_post_type());

                $items[] = array(
                    'title'           => get_the_title(),
                    'url'             => get_permalink(),
                    'date'            => get_the_date(get_option('date_format')),
                    'post_type_label' => $post_type_object && ! empty($post_type_object->labels->singular_name)
                        ? $post_type_object->labels->singular_name
                        : get_post_type(),
                );
            }
        }

        wp_reset_postdata();

        return array(
            'count' => count($items),
            'items' => $items,
        );
    }

    private function get_member_choices() {
        return get_posts(
            array(
                'post_type'      => Feuer_Mannschaft_Plugin::POST_TYPE,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'meta_query'     => array(
                    array(
                        'key'   => Feuer_Mannschaft_Plugin::PROFILE_META,
                        'value' => '1',
                    ),
                ),
            )
        );
    }

    private function build_shortcode_items($selected_ids) {
        $items = array();

        foreach ((array) $selected_ids as $member_id) {
            $member_id = absint($member_id);
            if (! $member_id || ! $this->is_member_post($member_id)) {
                continue;
            }

            $url = get_permalink($member_id);
            if (! $url) {
                continue;
            }

            $items[] = array(
                'id'        => $member_id,
                'name'      => get_the_title($member_id),
                'url'       => $url,
                'shortcode' => sprintf('[feuer_mannschaft_person id="%d"]', $member_id),
            );
        }

        return $items;
    }

    private function get_member_link_shortcode_tags() {
        return array(
            'feuer_mannschaft_person',
            'feuer_mannschaft_person_link',
        );
    }

    private function format_member_ids_for_log($member_ids) {
        $labels = array();

        foreach ((array) $member_ids as $member_id) {
            $member_id = absint($member_id);
            if (! $member_id) {
                continue;
            }

            $name = get_the_title($member_id);
            $labels[] = $name ? $name : (string) $member_id;
        }

        return implode(', ', array_values(array_unique(array_filter($labels))));
    }

    private function sanitize_selected_ids($selected_ids) {
        $selected_ids = array_values(array_unique(array_map('absint', (array) $selected_ids)));

        if (empty($selected_ids)) {
            return array();
        }

        $valid_ids = get_posts(
            array(
                'post_type'      => Feuer_Mannschaft_Plugin::POST_TYPE,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'post__in'       => $selected_ids,
                'meta_query'     => array(
                    array(
                        'key'   => Feuer_Mannschaft_Plugin::PROFILE_META,
                        'value' => '1',
                    ),
                ),
            )
        );

        return array_values(array_map('absint', (array) $valid_ids));
    }

    private function normalize_selected_ids($raw_value) {
        if (is_array($raw_value)) {
            return array_values(array_map('absint', $raw_value));
        }

        if (is_string($raw_value) && '' !== trim($raw_value)) {
            $decoded = maybe_unserialize($raw_value);

            if (is_array($decoded)) {
                return array_values(array_map('absint', $decoded));
            }
        }

        return array();
    }

    private function get_current_post_id() {
        if (isset($_GET['post'])) {
            return absint(wp_unslash($_GET['post']));
        }

        if (isset($_POST['post_ID'])) {
            return absint(wp_unslash($_POST['post_ID']));
        }

        return 0;
    }

    private function is_member_post($post_id) {
        return (
            '1' === (string) get_post_meta($post_id, Feuer_Mannschaft_Plugin::PROFILE_META, true)
            && '' === trim((string) get_post_meta($post_id, Feuer_Mannschaft_Plugin::INVITE_URL_META, true))
        )
            || (isset($_GET['feuer_mannschaft_profile']) && 1 === absint(wp_unslash($_GET['feuer_mannschaft_profile'])));
    }

    private function render_frontend_template($slug, $context = array()) {
        $slug = trim((string) $slug, '/');
        $file = FEUER_MANNSCHAFT_PLUGIN_DIR . 'templates/frontend/' . $slug . '.php';

        if (! file_exists($file)) {
            return '';
        }

        if (! empty($context)) {
            extract($context, EXTR_SKIP);
        }

        ob_start();
        include $file;

        return ob_get_clean();
    }
}

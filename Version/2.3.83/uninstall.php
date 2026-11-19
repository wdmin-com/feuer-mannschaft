<?php
/**
 * Uninstall routine for Feuer-Mannschaft.
 */

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

const FEUER_MANNSCHAFT_OPTION_KEY = 'feuer_mannschaft_settings';
const FEUER_MANNSCHAFT_PROFILE_META = '_feuer_mannschaft_profile';
const FEUER_MANNSCHAFT_GALLERY_META = '_feuer_mannschaft_gallery';
const FEUER_MANNSCHAFT_META_PREFIX = '_feuer_mannschaft_';
const FEUER_MANNSCHAFT_MENTION_META = 'mentioned_persons';
const FEUER_MANNSCHAFT_MIGRATION_OPTION = 'feuer_mannschaft_legacy_posts_migrated';
const FEUER_MANNSCHAFT_CPT_MIGRATION_OPTION = 'feuer_mannschaft_profiles_to_cpt_migrated';
const FEUER_MANNSCHAFT_LEGACY_CPT_MIGRATION_OPTION = 'feuer_mannschaft_legacy_cpt_migrated';
const FEUER_MANNSCHAFT_CATEGORY_SYNC_OPTION = 'feuer_mannschaft_category_hierarchy_synced';
const FEUER_MANNSCHAFT_FIELD_RESET_OPTION = 'feuer_mannschaft_member_field_values_reset_2_1_1';
const FEUER_MANNSCHAFT_FIELD_DEFAULTS_OPTION = 'feuer_mannschaft_default_field_options_seeded_2_1_6';
const FEUER_MANNSCHAFT_LOG_SCHEMA_OPTION = 'feuer_mannschaft_log_schema_version';
const FEUER_MANNSCHAFT_DEFAULT_IMAGE_SYNC_OPTION = 'feuer_mannschaft_default_images_synced_2_1_15';
const FEUER_MANNSCHAFT_COMMENTS_SYNC_OPTION = 'feuer_mannschaft_comments_closed_2_1_16';
const FEUER_MANNSCHAFT_FIELD_REPLACEMENT_TRANSIENT = 'feuer_mannschaft_field_pending_replacements_';
const FEUER_MANNSCHAFT_BACKUP_SUBDIR = 'feuer-mannschaft-backups';
const FEUER_MANNSCHAFT_BACKUP_TEMP_PREFIX = 'feuer-mannschaft-backups-tmp-';
const FEUER_MANNSCHAFT_MEMBER_SUBDIR = 'feuer-mannschaft';
const FEUER_MANNSCHAFT_LEGACY_POST_TYPE = 'feuer_mannschaft_member';
const FEUER_MANNSCHAFT_LOG_TABLE = 'feuer_mannschaft_logs';

function feuer_mannschaft_delete_directory($directory) {
    $directory = (string) $directory;

    if ('' === $directory || ! file_exists($directory)) {
        return;
    }

    if (is_file($directory) || is_link($directory)) {
        @unlink($directory);
        return;
    }

    $items = scandir($directory);
    if (! is_array($items)) {
        return;
    }

    foreach ($items as $item) {
        if ('.' === $item || '..' === $item) {
            continue;
        }

        feuer_mannschaft_delete_directory(trailingslashit($directory) . $item);
    }

    @rmdir($directory);
}

function feuer_mannschaft_delete_option($option_name) {
    delete_option($option_name);

    if (is_multisite()) {
        delete_site_option($option_name);
    }
}

function feuer_mannschaft_delete_matching_options($prefix) {
    global $wpdb;

    foreach (array(
        '_transient_' . $prefix,
        '_transient_timeout_' . $prefix,
    ) as $pattern) {
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like($pattern) . '%'
            )
        );
    }

    if (is_multisite() && ! empty($wpdb->sitemeta)) {
        foreach (array(
            '_site_transient_' . $prefix,
            '_site_transient_timeout_' . $prefix,
        ) as $pattern) {
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s",
                    $wpdb->esc_like($pattern) . '%'
                )
            );
        }
    }
}

function feuer_mannschaft_get_member_post_ids() {
    global $wpdb;

    $ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
            FEUER_MANNSCHAFT_PROFILE_META,
            '1'
        )
    );

    return array_values(array_unique(array_map('absint', (array) $ids)));
}

function feuer_mannschaft_get_legacy_post_ids() {
    global $wpdb;

    $ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
            FEUER_MANNSCHAFT_LEGACY_POST_TYPE
        )
    );

    return array_values(array_unique(array_map('absint', (array) $ids)));
}

function feuer_mannschaft_get_gallery_attachment_ids($post_ids) {
    $attachment_ids = array();

    foreach ((array) $post_ids as $post_id) {
        $gallery = get_post_meta((int) $post_id, FEUER_MANNSCHAFT_GALLERY_META, true);
        if (! is_array($gallery)) {
            continue;
        }

        $attachment_ids = array_merge($attachment_ids, array_map('absint', $gallery));
    }

    return array_values(array_unique(array_filter($attachment_ids)));
}

function feuer_mannschaft_get_attached_attachment_ids($post_ids) {
    global $wpdb;

    $post_ids = array_values(array_filter(array_map('absint', (array) $post_ids)));

    if (empty($post_ids)) {
        return array();
    }

    $placeholders = implode(', ', array_fill(0, count($post_ids), '%d'));
    $sql = $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_parent IN ({$placeholders})",
        $post_ids
    );

    $ids = $wpdb->get_col($sql);

    return array_values(array_unique(array_map('absint', (array) $ids)));
}

function feuer_mannschaft_remove_owned_mentions($member_ids) {
    global $wpdb;

    $member_ids = array_fill_keys(array_map('strval', array_filter(array_map('absint', (array) $member_ids))), true);
    if (empty($member_ids)) {
        return;
    }

    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s",
            FEUER_MANNSCHAFT_MENTION_META
        )
    );

    foreach ((array) $rows as $row) {
        $value = is_serialized($row->meta_value)
            ? @unserialize($row->meta_value, array('allowed_classes' => false))
            : $row->meta_value;

        if (! is_array($value)) {
            if (is_scalar($value) && isset($member_ids[(string) absint($value)])) {
                delete_metadata_by_mid('post', (int) $row->meta_id);
            }
            continue;
        }

        $remaining = array_values(
            array_filter(
                $value,
                function ($mentioned_id) use ($member_ids) {
                    return ! is_scalar($mentioned_id) || ! isset($member_ids[(string) absint($mentioned_id)]);
                }
            )
        );

        if ($remaining !== $value) {
            if (empty($remaining)) {
                delete_metadata_by_mid('post', (int) $row->meta_id);
            } else {
                update_metadata_by_mid('post', (int) $row->meta_id, $remaining);
            }
        }
    }
}

$member_ids = feuer_mannschaft_get_member_post_ids();
$legacy_ids = feuer_mannschaft_get_legacy_post_ids();
feuer_mannschaft_remove_owned_mentions(array_merge($member_ids, $legacy_ids));
$attachment_ids = array_values(
    array_unique(
        array_merge(
            feuer_mannschaft_get_gallery_attachment_ids($member_ids),
            feuer_mannschaft_get_gallery_attachment_ids($legacy_ids),
            feuer_mannschaft_get_attached_attachment_ids(array_merge($member_ids, $legacy_ids))
        )
    )
);

foreach ($attachment_ids as $attachment_id) {
    if ($attachment_id > 0) {
        wp_delete_attachment($attachment_id, true);
    }
}

foreach ($member_ids as $member_id) {
    if ($member_id > 0) {
        wp_delete_post($member_id, true);
    }
}

foreach ($legacy_ids as $legacy_id) {
    if ($legacy_id > 0) {
        wp_delete_post($legacy_id, true);
    }
}

$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
        $wpdb->esc_like(FEUER_MANNSCHAFT_META_PREFIX) . '%'
    )
);

feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_OPTION_KEY);
feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_MIGRATION_OPTION);
feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_CPT_MIGRATION_OPTION);
feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_LEGACY_CPT_MIGRATION_OPTION);
feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_CATEGORY_SYNC_OPTION);
feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_FIELD_RESET_OPTION);
feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_FIELD_DEFAULTS_OPTION);
feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_LOG_SCHEMA_OPTION);
feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_DEFAULT_IMAGE_SYNC_OPTION);
feuer_mannschaft_delete_option(FEUER_MANNSCHAFT_COMMENTS_SYNC_OPTION);
feuer_mannschaft_delete_option('feuer_mannschaft_automation_settings');
feuer_mannschaft_delete_option('feuer_mannschaft_last_audit');
feuer_mannschaft_delete_option('feuer_mannschaft_roles_version');
feuer_mannschaft_delete_matching_options(FEUER_MANNSCHAFT_FIELD_REPLACEMENT_TRANSIENT);
feuer_mannschaft_delete_matching_options('_transient_feuer_mannschaft_csv_rollback_');
feuer_mannschaft_delete_matching_options('_transient_timeout_feuer_mannschaft_csv_rollback_');

wp_clear_scheduled_hook('feuer_mannschaft_daily_audit');
wp_clear_scheduled_hook('feuer_mannschaft_weekly_report');
$administrator = get_role('administrator');
if ($administrator) {
    foreach (array('manage_feuer_members','edit_feuer_member','read_feuer_member','delete_feuer_member','edit_feuer_members','edit_others_feuer_members','publish_feuer_members','read_private_feuer_members','delete_feuer_members','delete_private_feuer_members','delete_published_feuer_members','delete_others_feuer_members','edit_private_feuer_members','edit_published_feuer_members','edit_feuer_mentions','view_feuer_internal','delete_feuer_members_permanently') as $capability) {
        $administrator->remove_cap($capability);
    }
}
remove_role('feuer_mannschaft_editor');

$wpdb->query('DROP TABLE IF EXISTS `' . str_replace('`', '``', $wpdb->prefix . FEUER_MANNSCHAFT_LOG_TABLE) . '`');

$upload_dir = wp_upload_dir();

if (! empty($upload_dir['basedir'])) {
    $base_directory = trailingslashit($upload_dir['basedir']);

    feuer_mannschaft_delete_directory($base_directory . FEUER_MANNSCHAFT_MEMBER_SUBDIR);
    feuer_mannschaft_delete_directory($base_directory . FEUER_MANNSCHAFT_BACKUP_SUBDIR);

    $temporary_directories = glob($base_directory . FEUER_MANNSCHAFT_BACKUP_TEMP_PREFIX . '*');
    if (is_array($temporary_directories)) {
        foreach ($temporary_directories as $temporary_directory) {
            feuer_mannschaft_delete_directory($temporary_directory);
        }
    }
}

<?php

if (! defined('ABSPATH')) {
    exit;
}

$profile_preview = isset($profile_preview) && is_array($profile_preview) ? $profile_preview : array();
$completion      = isset($profile_preview['completion']) && is_array($profile_preview['completion']) ? $profile_preview['completion'] : array();
$name            = ! empty($profile_preview['name']) ? (string) $profile_preview['name'] : 'Neues Mannschaft-Profil';
$rank            = ! empty($profile_preview['rank']) ? (string) $profile_preview['rank'] : 'Dienstgrad offen';
$function        = ! empty($profile_preview['function']) ? (string) $profile_preview['function'] : 'Funktion offen';
$category        = ! empty($profile_preview['category']) ? (string) $profile_preview['category'] : 'Kategorie offen';
$has_photo       = ! empty($completion['photo']);
$is_archived     = ! empty($profile_preview['is_archived']);
$is_excluded     = ! empty($profile_preview['is_excluded']);
$complete_count  = 0;
$avatar_letter   = function_exists('mb_substr') ? mb_substr($name, 0, 1) : substr($name, 0, 1);

foreach (array('name', 'category', 'photo') as $completion_key) {
    if (! empty($completion[$completion_key])) {
        $complete_count++;
    }
}
?>
<aside class="feuer-mannschaft-profile-preview" data-profile-preview>
    <div class="feuer-mannschaft-profile-preview-main">
        <div class="feuer-mannschaft-profile-preview-avatar <?php echo $has_photo ? 'has-photo' : 'is-empty'; ?>" data-profile-preview-avatar>
            <span><?php echo esc_html($avatar_letter); ?></span>
        </div>

        <div>
            <span class="feuer-mannschaft-admin-kicker">Live Vorschau</span>
            <h2 data-preview-name><?php echo esc_html($name); ?></h2>
            <p>
                <span data-preview-rank><?php echo esc_html($rank); ?></span>
                <span aria-hidden="true"> / </span>
                <span data-preview-function><?php echo esc_html($function); ?></span>
            </p>
        </div>
    </div>

    <div class="feuer-mannschaft-profile-preview-meta">
        <span class="feuer-mannschaft-badge" data-preview-category><?php echo esc_html($category); ?></span>
        <span class="feuer-mannschaft-badge <?php echo $has_photo ? 'is-ok' : 'is-warning'; ?>" data-preview-photo><?php echo $has_photo ? 'Foto bereit' : 'Foto offen'; ?></span>
        <?php if ($is_archived) : ?>
            <span class="feuer-mannschaft-badge is-muted">Archiviert</span>
        <?php endif; ?>
        <?php if ($is_excluded) : ?>
            <span class="feuer-mannschaft-badge is-warning" data-preview-excluded>Ohne Zaehler</span>
        <?php else : ?>
            <span class="feuer-mannschaft-badge is-ok" data-preview-excluded>Zaehlt mit</span>
        <?php endif; ?>
    </div>

    <div class="feuer-mannschaft-profile-completion" aria-label="Profil Vollstaendigkeit">
        <span style="width: <?php echo esc_attr((string) round(($complete_count / 3) * 100)); ?>%;" data-preview-progress></span>
    </div>
</aside>

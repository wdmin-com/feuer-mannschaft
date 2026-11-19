<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="feuer-mannschaft-archived-members">
    <h2>Ehemalige Mitglieder</h2>
    <div class="feuer-mannschaft-archived-grid">
        <?php foreach ($member_ids as $member_id) : ?>
            <?php
            $end_date = get_post_meta($member_id, Feuer_Mannschaft_Plugin::SERVICE_END_DATE_META, true);
            $reason = get_post_meta($member_id, Feuer_Mannschaft_Plugin::ARCHIVE_REASON_META, true);
            $thumbnail = get_the_post_thumbnail($member_id, 'medium', array('loading' => 'lazy'));
            ?>
            <article class="feuer-mannschaft-archived-card">
                <?php if ($thumbnail) : ?><div class="feuer-mannschaft-archived-image"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
                <div class="feuer-mannschaft-archived-content">
                    <h3><?php echo esc_html(get_the_title($member_id)); ?></h3>
                    <?php if ($end_date) : ?><p><strong>Dienstende:</strong> <time datetime="<?php echo esc_attr($end_date); ?>"><?php echo esc_html($end_date); ?></time></p><?php endif; ?>
                    <?php if ($reason) : ?><p><?php echo esc_html($reason); ?></p><?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

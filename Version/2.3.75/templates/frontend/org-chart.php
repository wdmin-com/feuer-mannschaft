<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="feuer-mannschaft-org-chart">
    <?php foreach ($sections as $section) : ?>
        <section class="feuer-mannschaft-org-chart-section">
            <h2><?php echo esc_html($section['term']->name); ?></h2>
            <?php if (! empty($section['term']->description)) : ?>
                <p><?php echo esc_html($section['term']->description); ?></p>
            <?php endif; ?>
            <div class="feuer-mannschaft-org-chart-members">
                <?php foreach ($section['member_ids'] as $member_id) : ?>
                    <article class="feuer-mannschaft-org-chart-member">
                        <a href="<?php echo esc_url(get_permalink($member_id)); ?>"><?php echo esc_html(get_the_title($member_id)); ?></a>
                        <?php $groups = get_the_terms($member_id, 'feuer_group'); ?>
                        <?php if (is_array($groups) && ! empty($groups)) : ?>
                            <span><?php echo esc_html(implode(', ', wp_list_pluck($groups, 'name'))); ?></span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

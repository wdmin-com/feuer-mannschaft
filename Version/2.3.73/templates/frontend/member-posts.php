<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="feuer-mannschaft-member-posts" aria-labelledby="feuer-mannschaft-member-posts-<?php echo esc_attr($member_id); ?>">
    <h2 id="feuer-mannschaft-member-posts-<?php echo esc_attr($member_id); ?>"><?php esc_html_e('Beiträge mit Beteiligung', 'feuer-mannschaft'); ?></h2>
    <ul class="feuer-mannschaft-member-posts-list">
        <?php foreach ($items as $item) : ?>
            <li>
                <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                <span><?php echo esc_html($item['date']); ?> · <?php echo esc_html($item['post_type_label']); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="feuer-mannschaft-service-timeline" aria-labelledby="feuer-service-history-<?php echo esc_attr($post_id); ?>">
    <h2 id="feuer-service-history-<?php echo esc_attr($post_id); ?>">Dienstverlauf</h2>
    <ol>
        <?php foreach ($events as $event) : ?>
            <li class="is-<?php echo esc_attr($event['type']); ?>">
                <div class="feuer-mannschaft-service-timeline-marker" aria-hidden="true"></div>
                <div>
                    <p class="feuer-mannschaft-service-timeline-meta">
                        <?php if ($event['date']) : ?><time datetime="<?php echo esc_attr($event['date']); ?>"><?php echo esc_html($event['date']); ?></time><?php endif; ?>
                        <span><?php echo esc_html($event_types[$event['type']]); ?></span>
                    </p>
                    <?php if ($event['title']) : ?><h3><?php echo esc_html($event['title']); ?></h3><?php endif; ?>
                    <?php if ($event['description']) : ?><p><?php echo nl2br(esc_html($event['description'])); ?></p><?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</section>

<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap feuer-mannschaft-settings">
    <?php
    $partial_context = get_defined_vars();
    unset($partial_context['this']);
    ?>
    <h1>Mannschaft-Einstellungen</h1>
    <?php settings_errors(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>
    <?php echo $this->render_admin_template('dashboard-summary', $partial_context); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

    <?php if (! empty($notice)) : ?>
        <div class="notice notice-<?php echo esc_attr($notice['type']); ?> is-dismissible">
            <p><?php echo esc_html($notice['message']); ?></p>
        </div>
    <?php endif; ?>

    <nav class="nav-tab-wrapper feuer-mannschaft-admin-tabs">
        <?php foreach ($tabs as $tab_key => $tab_label) : ?>
            <a class="nav-tab <?php echo $tab_key === $current_tab ? 'nav-tab-active' : ''; ?>" data-admin-tab="<?php echo esc_attr($tab_key); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'feuer-mannschaft-settings', 'tab' => $tab_key), admin_url('admin.php'))); ?>">
                <?php echo esc_html($tab_label); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="feuer-mannschaft-settings-panel" data-settings-panel>
    <?php if (in_array($current_tab, array('general', 'mentions', 'fields'), true)) : ?>
        <form method="post" action="options.php">
            <?php settings_fields('feuer_mannschaft_settings_group'); ?>
            <input type="hidden" name="feuer_mannschaft_settings_tab" value="<?php echo esc_attr($current_tab); ?>">
            <?php echo $this->render_admin_template('settings/' . $current_tab, $partial_context); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php submit_button(); ?>
        </form>
    <?php else : ?>
        <?php echo $this->render_admin_template('settings/' . $current_tab, $partial_context); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    <?php endif; ?>
    </div>
</div>

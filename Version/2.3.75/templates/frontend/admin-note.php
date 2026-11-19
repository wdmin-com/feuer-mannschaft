<?php

if (! defined('ABSPATH')) {
    exit;
}

$note = isset($note) ? trim((string) $note) : '';

if ('' === $note) {
    return;
}
?>
<section class="feuer-mannschaft-admin-note" aria-label="Administrator-Hinweis">
    <details class="feuer-mannschaft-admin-note-toggle">
        <summary class="feuer-mannschaft-admin-note-summary">
            <span class="feuer-mannschaft-admin-note-summary-label">Administrator-Hinweis</span>
            <span class="feuer-mannschaft-admin-note-summary-hint">Nur fuer Administratoren sichtbar</span>
        </summary>
        <div class="feuer-mannschaft-admin-note-panel">
            <div class="feuer-mannschaft-admin-note-label">Nur fuer Administratoren</div>
            <div class="feuer-mannschaft-admin-note-content"><?php echo nl2br(esc_html($note)); ?></div>
        </div>
    </details>
</section>

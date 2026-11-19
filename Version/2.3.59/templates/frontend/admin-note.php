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
    <div class="feuer-mannschaft-admin-note-label">Nur fuer Administratoren</div>
    <div class="feuer-mannschaft-admin-note-content"><?php echo nl2br(esc_html($note)); ?></div>
</section>

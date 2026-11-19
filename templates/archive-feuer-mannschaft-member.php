<?php

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="main-content" class="feuer-mannschaft-main-content site-main">
<?php
feuer_mannschaft_plugin()->render_archive_page();
?>
</main>
<?php
get_footer();

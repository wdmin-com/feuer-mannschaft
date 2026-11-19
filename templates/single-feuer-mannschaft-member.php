<?php

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="main-content" class="feuer-mannschaft-profile-main site-main">
<?php
feuer_mannschaft_plugin()->render_single_page();
?>
</main>
<?php
get_footer();

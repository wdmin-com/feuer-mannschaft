<?php

if (! defined('ABSPATH')) {
    exit;
}

get_header();
feuer_mannschaft_plugin()->render_archive_page();
get_footer();

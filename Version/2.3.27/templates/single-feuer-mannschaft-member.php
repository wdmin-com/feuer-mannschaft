<?php

if (! defined('ABSPATH')) {
    exit;
}

get_header();
feuer_mannschaft_plugin()->render_single_page();
get_footer();

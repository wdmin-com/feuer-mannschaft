<?php

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>
<style>
#secondary,
.sidebar,
.widget-area,
.sidebar-area,
.site-sidebar,
.content-sidebar,
.primary-sidebar,
aside[id*="sidebar"],
aside[class*="sidebar"] {
    display: none !important;
}

#primary,
.content-area,
.site-main,
.main-content,
.primary-content,
main[role="main"] {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
    float: none !important;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var singlePage = document.querySelector('.feuer-mannschaft-single-page');

    if (!singlePage) {
        return;
    }

    var selector = 'aside, #secondary, .sidebar, .widget-area, .sidebar-area, .site-sidebar, .content-sidebar, .primary-sidebar, [id*="sidebar"], [class*="sidebar"]';
    var current = singlePage;
    var steps = 0;

    while (current && current.parentElement && steps < 8) {
        Array.prototype.forEach.call(current.parentElement.children, function (sibling) {
            if (sibling === current || !sibling.matches(selector)) {
                return;
            }

            sibling.style.display = 'none';
            sibling.setAttribute('hidden', 'hidden');
        });

        current = current.parentElement;
        steps += 1;
    }
});
</script>
<?php
feuer_mannschaft_plugin()->render_single_page();
get_footer();

<?php
/*
Plugin Name: HTML Minifier copy
Description: Minify HTML output for improved performance.
Version: 1.0
Author: Mehreen
*/

// Minify HTML function
function minify_html($html) {
    // Remove HTML comments
    $html = preg_replace('/<!--(.|\s)*?-->/', '', $html);
    // Remove extra whitespace
    $html = preg_replace('/\s+/', ' ', $html);
    return $html;
}

// Hook into WordPress to minify HTML
function minify_html_output() {
    ob_start('minify_html');
}
add_action('template_redirect', 'minify_html_output');


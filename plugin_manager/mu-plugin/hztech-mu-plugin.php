<?php
// Function to disable plugins based on page conditions
function disable_plugin_based_on_pages($plugins) {
    // Get the options from the database
    $options = get_option('hztech_cache_options');

    // Check if the options are already unserialized, if not, unserialize them
    if (!is_array($options)) {
        // Add debug statement
        error_log('Options are not serialized correctly');
        return $plugins;
    }

    // Get the current page URL
    $current_page_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $remove_plugins = array(); // Initialize array to store plugins to be removed

    foreach ($options as $plugin => $data) {
        // Check if the plugin is disabled and if the current page matches any of the disabled pages
        if (isset($data['disabled']) && $data['disabled'] === 'on' && isset($data['pages'])) {
            foreach ($data['pages'] as $page) {
                // Split the page data into ID and URL
                list($page_id, $page_url) = explode(',', $page);

                // Check if the current page URL matches the disabled page URL
                if ($current_page_url == $page_url) {
                    // Add the plugin to the array of plugins to be removed
                    $remove_plugins[] = $plugin;
                }
            }
        }
        if (isset($data['disabled']) && $data['disabled'] === 'on' && isset($data['posts'])) {
            foreach ($data['posts'] as $post) {
                // Split the post data into ID and URL
                list($post_id, $post_url) = explode(',', $post);
                // Check if the current post URL matches the disabled post URL
                if ($current_page_url == $post_url) {
                    // Add the plugin to the array of plugins to be removed
                    $remove_plugins[] = $plugin;
                }
            }
        }

    }

    // Remove the plugins from the list of active plugins
    $plugins = array_diff($plugins, $remove_plugins);

    return $plugins;
}

// Hook into the 'option_active_plugins' filter to dynamically modify the list of active plugins
add_filter('option_active_plugins', 'disable_plugin_based_on_pages');

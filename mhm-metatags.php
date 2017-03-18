<?php
/*
Plugin Name: Meta tags
Plugin URI: https://github.com/markhowellsmead/wordpress.plugin.mhm-metatags
Description: Adds custom meta tags – OG and Twitter – to the head of the page, based on the page request.
Author: Mark Howells-Mead
Version: 1.0.0
Author URI: https://markweb.ch/
Text Domain: mhm-metatags
Domain Path: /languages
 */

if (version_compare($wp_version, '4.6', '<') || version_compare(PHP_VERSION, '5.3', '<')) {
    function mhm_metatags_compatability_warning()
    {
        echo '<div class="error"><p>' . sprintf(
            __('“%1$s” requires PHP %2$s (or newer) and WordPress %3$s (or newer) to function properly. Your site is using PHP %4$s and WordPress %5$s. Please upgrade. The plugin has been automatically deactivated.', 'TEXT-DOMAIN'),
            'Meta tags',
            '5.3',
            '4.6',
            PHP_VERSION,
            $GLOBALS['wp_version']
        ) . '</p></div>';
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
    add_action('admin_notices', 'mhm_metatags_compatability_warning');

    function mhm_metatags_deactivate_self()
    {
        deactivate_plugins(plugin_basename(__FILE__));
    }
    add_action('admin_init', 'mhm_metatags_deactivate_self');

    return;
} else {
    include 'Classes/Plugin.php';
}

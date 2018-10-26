<?php

/*
 *  Plugin Name:  Custom Taxonomy Order
 *  Plugin URI:   https://github.com/msicknick/msicknick-custom-taxonomy-order/
 *  Description:  Enanbles drag and drop on taxonomy lists for a custom order
 *  Version:      1.0.0
 *  Author:       Magda Sicknick
 *  Author URI:   https://www.msicknick.com/
 *  License:      GPL2
 *  License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 *  Text Domain:  custom-taxonomy-order
 */

/**
 * Exit if accessed directly
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * DEFINE PATHS
 */
define('CUSTOM_TAX_ORDER_PATH', plugin_dir_path(__FILE__));
define('CUSTOM_TAX_ORDER_INCLUDES_PATH', CUSTOM_TAX_ORDER_PATH . 'includes/');

/**
 * DEFINE URLS
 */
define('CUSTOM_TAX_ORDER_URL', plugin_dir_url(__FILE__));
define('CUSTOM_TAX_ORDER_JS_URL', CUSTOM_TAX_ORDER_URL . 'assets/js/');
define('CUSTOM_TAX_ORDER_CSS_URL', CUSTOM_TAX_ORDER_URL . 'assets/css/');
define('CUSTOM_TAX_ORDER_IMAGES_URL', CUSTOM_TAX_ORDER_URL . 'assets/images/');
define('CUSTOM_TAX_ORDER_GITHUB_URL', 'https://github.com/msicknick/');

/**
 * FUNCTIONS
 */
require_once(CUSTOM_TAX_ORDER_INCLUDES_PATH . 'helper-functions.php');

/**
 * FRONT END
 */
require_once(CUSTOM_TAX_ORDER_INCLUDES_PATH . 'custom-taxonomy-order.php');

register_activation_hook(__FILE__, array('Custom_Taxonomy_Order', 'activate'));
if (is_admin()) {
    add_action('plugins_loaded', array('Custom_Taxonomy_Order', 'init'));
}

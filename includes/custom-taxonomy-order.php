<?php

class Custom_Taxonomy_Order {

    const VERSION = '1.0.0';

    private $settings;
    protected $plugin_slug;
    protected $plugin_basename;
    protected $plugin_path;
    protected $plugin_url;
    protected static $instance = null;

    public function __construct() {

        $this->plugin_slug = "custom-taxonomy-order";
        $this->plugin_path = CUSTOM_TAX_ORDER_PATH;
        $this->plugin_url = CUSTOM_TAX_ORDER_URL;
        $this->plugin_basename = plugin_basename($this->plugin_path . '/' . $this->plugin_slug . '.php');
        
        $this->settings = get_option('cto_order_options', array());

        // Load front end
        add_action('init', array($this, 'front_end_init'));

        // Load plugin menu
        add_action('admin_menu', array($this, 'add_plugin_menu'));
        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_head', array($this, 'admin_init'));

        // handle the AJAX request
        add_action('wp_ajax_update_taxonomy_order', array($this, 'update_taxonomy_order'));

        // Settings link
        add_filter("plugin_action_links_" . $this->plugin_basename, array($this, 'add_settings_link'));
    }

    /**
     * Get instance of class
     * 
     * @since 1.0.0
     */
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Initialize class
     *
     * @since 1.0.0
     */
    public static function init() {

        self::get_instance();
    }

    /**
     * Activate function
     *
     * @since 1.0.0
     */
    public function activate() {
        register_uninstall_hook(__FILE__, array($this, 'uninstall'));
    }

    /**
     * Uninstall funcion
     *
     * @since 1.0.0
     */
    public function uninstall() {
        unregister_setting('cto_options', 'cto_order_options');

        // Remove 'position' meta tags from all taxonomies
        $registered_taxonomies = get_taxonomies();
        foreach ($registered_taxonomies as $taxonomy) {
            $terms = get_terms($taxonomy, array('hide_empty' => false));
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    delete_term_meta($term->term_id, 'position');
                }
            }
        }
    }

    /**
     * Load CSS and JS files
     *
     * @since 1.0.0
     */
    public function enqueue_scripts_and_styles() {

        // enqueue jquery ui drag and drop
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-sortable');

        wp_enqueue_script($this->plugin_slug . '-admin-script', CUSTOM_TAX_ORDER_JS_URL . 'admin.js', array(), self::VERSION);
        wp_localize_script($this->plugin_slug . '-admin-script', 'ms_cto_data', array(
            'ajax_url' => esc_url(admin_url('admin-ajax.php')),
            'preloader_url' => esc_url(admin_url('images/wpspin_light.gif')),
        ));

        wp_enqueue_style($this->plugin_slug . '-admin-styles', CUSTOM_TAX_ORDER_CSS_URL . 'admin.css', array(), self::VERSION);
    }

    /**
     * Add link in Plugins screen
     *
     * @since 1.0.0
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . esc_url(get_admin_url(null, 'tools.php?page=custom-taxonomy-order')) . '">' . "Settings" . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add footer to plugin settings page
     *
     * @since 1.0.0
     */
    public function return_footer_function() {
        // silence
    }

    /**
     * Add plugin in Tools menu
     *
     * @since 1.0.0
     */
    public function add_plugin_menu() {
        add_management_page(
                __('Taxonomy Order', $this->plugin_slug), __('Taxonomy Order', $this->plugin_slug), 'manage_options', $this->plugin_slug, array($this, 'load_admin_page'), $this->plugin_slug
        );
    }

    /**
     * Initialize ordering on the taxonomy screen
     *
     * @since 1.0.0
     */
    public function admin_init() {
        if (is_admin()) {
            $curr_screen = get_current_screen();
            if ($curr_screen->base == 'edit-tags') {
                ms_cto_add_position_value($curr_screen);

                if (ms_cto_position_meta_exists($curr_screen->taxonomy)) {
                    $this->enqueue_scripts_and_styles();

                    add_filter('terms_clauses', array($this, 'reorder_taxonomies'), 10, 3);
                }
            }
        }
    }

    /**
     * Initialize ordering on the site
     *
     * @since 1.0.0
     */
    public function front_end_init() {
        add_filter('terms_clauses', array($this, 'reorder_taxonomies'), 10, 3);
    }

    /**
     * Re-order taxonomies
     *
     * @since 1.0.0
     */
    public function reorder_taxonomies($sql, $taxonomies, $args) {
        foreach ($taxonomies as $taxonomy) {
            if (ms_cto_position_meta_exists($taxonomy)) {
                global $wpdb;

                $join_statement = " LEFT JOIN $wpdb->termmeta AS position_meta
                                        ON t.term_id = position_meta.term_id 
                                        AND position_meta.meta_key = 'position'";

                if (!strpos($sql['join'], $join_statement)) {
                    $sql['join'] .= $join_statement;
                }
                $sql['orderby'] = "ORDER BY CAST( position_meta.meta_value AS UNSIGNED )";
            }
        }

        return $sql;
    }

    /**
     * Update the taxonomy order meta tags from the ajax call
     *
     * @since 1.0.0
     */
    function update_taxonomy_order() {
        $array_data = $_POST['data_array'];
        foreach ($array_data as $taxonomy_data) {
            update_term_meta($taxonomy_data[0], 'position', (int) ( $taxonomy_data[1] + 1 ));
        }
        wp_die();
        exit;
    }

    /**
     * Load settings page
     *
     * @since 1.0.0
     */
    public function load_admin_page() {
        ?>
        <div class='wrap'>    
            <h1>
                <img style="width: 30px;height: 30px;vertical-align: middle;" src="<?php echo CUSTOM_TAX_ORDER_IMAGES_URL . "icon.png"; ?>">
                <div style="display:inline-block;vertical-align:text-bottom;">Custom Taxonomy Order</div>
                <p class='description'> Enanbles drag and drop on taxonomy lists for a custom order.</p>
            </h1>
            <h1></h1>          
            <form method="post" action="options.php">
                
        <?php
        settings_fields('cto_options');
        do_settings_sections('custom-taxonomy-order');
        submit_button();
        ?>
                
            </form>
        </div>
        <?php
        add_action('admin_footer_text', array($this, 'return_footer_function'));
    }

    /**
     * Register settings
     *
     * @since 1.0.0
     */
    public function settings_init() {

        register_setting('cto_options', 'cto_order_options');

        add_settings_section(
                'cto_setting_section',
                '',
                array($this, 'cto_settings'), 
                'custom-taxonomy-order'
        );

        add_settings_field(
                'enabled_taxonomies',
                __('Enabled Taxonomies', $this->plugin_slug),
                array($this, 'enabled_taxaonomies_settings'),
                'custom-taxonomy-order',
                'cto_setting_section'          
        );
    }

    /**
     * Print settings page header
     *
     * @since 1.0.0
     */
    public function cto_settings() {
        _e('Select taxonomies to enable drag and drop functionality.', $this->plugin_slug);
    }

    /**
     * Get list of registered taxonomies, exclude core Wordpress taxonomies
     *
     * @since 1.0.0
     */
    public static function get_registered_taxonomies() {
        $registered_taxonomies = get_taxonomies();

        $excludes = array(
            // Core wordpress taxonomies
            'nav_menu',
            'link_category',
            'post_format'
        );
        
        foreach ($excludes as $exclude) {
            if ($key = array_search($exclude, $registered_taxonomies)) {
                unset($registered_taxonomies[$key]);
            }
        }

        return $registered_taxonomies;
    }

    /**
     * Print settings options for enabled taxonomies
     *
     * @since 1.0.0
     */
    public function enabled_taxaonomies_settings() {
                $taxonomies = self::get_registered_taxonomies();
                if (is_array($taxonomies)) {
                    foreach ($taxonomies as $taxonomy) {
                        $checked = (isset($this->settings['enabled_taxonomies']) && in_array($taxonomy, $this->settings['enabled_taxonomies'])) ? 'checked' : '';
                        ?>

                        <input type="checkbox" id="chkTaxonomy<?php echo $taxonomy; ?>" name="cto_order_options[enabled_taxonomies][]" value="<?php echo $taxonomy; ?>" <?php echo $checked; ?>/>
                        <label for="chkTaxonomy<?php echo $taxonomy; ?>"><?php echo get_taxonomy($taxonomy)->labels->name . ' <span style="font-size: 0.7rem;font-weight:700;">(' . get_post_type_object(get_taxonomy($taxonomy)->object_type[0])->labels->name . ')</span>'; ?></label>
                        <br/>

                <?php
            }
        } else {
            _e('No taxonomies found', $this->plugin_slug);
        }
    }

}

if (is_admin()) {
    Custom_Taxonomy_Order::init();
}
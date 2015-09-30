<?php

/*
 * Plugin Name: Marketing Optimizer for Wordpress Plugin
 * URI: http://www.marketingoptimizer.com/?apcid=8381
 * Version: 20151010
 * Description: Create Landing Pages for Wordpress
 * Author: Marketing Optimizer, customercare@marketingoptimizer.com
 * Author URI: http://www.marketingoptimizer.com/?apcid=8381
 */
/*
 * ===============================================================================
 * Plugin Class
 * ===============================================================================
 */
class mo_plugin
{

    /*
     * =============================================================================== Declare Class Variables ===============================================================================
     */
    CONST MO_LP_TEXT_DOMAIN = 'mo_landing_pages';

    CONST MO_DIRECTORY = 'marketing-optimizer';

    public static $plugin_version = '20150930';

    public static $plugin_name = 'marketing-optimizer';

    public $plugin_prefix;

    public $menu_title;

    public $options_list;

    public $plugin_meta_links;

    public static $plugin_url;

    public $plugin_dir;

    public $plugin_basename;

    public $plugin_settings;

    public $plugin_settings_group;

    public $domain;

    public $plugin_options;

    /**
     * Class Constructor
     *
     * @since 1.0
     */
    function __construct()
    {
        /**
         * ***********************************
         * User Configurable Variables
         * ***********************************
         */
        require_once ('includes/class.mo_autoloader.php');
        require_once ('includes/class.mo_ab_testing.php');
        require_once ('includes/class.mo_landing_pages.php');
        require_once ('includes/class.mo_variation.php');
        require_once ('includes/class.mo_settings.php');
        include ('includes/class.mo_page_post_type.php');
        include ('includes/class.mo_lp_post_type.php');
        if (class_exists("GFFormsModel")) {
            include ('includes/class.mo_gravity_forms.php');
        }
        include ('includes/class.mo_lp_metaboxes.php');
        include ('includes/class.mo_page_metaboxes.php');
        include ('includes/class.mo_pages.php');
        
        include ('includes/class.mo_ct_metaboxes.php');
        include ('includes/class.mo_ct_post_type.php');
        include ('includes/class.mo_ct_variation.php');
        include ('includes/class.mo_callto_action.php');
        include ('includes/class.mo.ct.widget.php');
        
        include ('includes/class.mo_sp_post_type.php');
        include ('includes/class.mo_squeeze_pages.php');
        include ('includes/class.mo_sp_metaboxes.php');
        include ('includes/class.mo_sp_variation.php');
        
        include ('includes/mo_lp_ab_testing.php');
        include ('includes/mo_lp_templates.php');
        
        // add admin specific scripts
        add_action('admin_enqueue_scripts', array(
            $this,
            'mo_lp_admin_scripts'
        ));
        
        // Plugin Prefix
        $this->plugin_prefix = 'molp';
        // Main Menu Item Title : Optional
        $this->menu_title = 'Maketing Optimizer for Wordpress';
        
        /**
         * ***********************************
         * Plugin System Variables
         * ***********************************
         */
        
        if (isset($_SERVER['HTTPS'])) {
            define('WP_MO_PLUGIN_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/wp-content/plugins');
        } else {
            define('WP_MO_PLUGIN_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/wp-content/plugins');
        }
        // The Plugin URL Path
        self::$plugin_url = WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
        // The Plugin DIR Path
        $this->plugin_dir = WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
        // The Plugin Name. Dirived from the plugin folder name.
        self::$plugin_name = basename(dirname(__FILE__));
        // The Plugin Basename
        $this->plugin_basename = plugin_basename(__FILE__);
        // Variable for the Plugin Settings
        $this->plugin_settings = mo_plugin::$plugin_name . '_settings';
        // Variable for the Plugin Settings Group
        $this->plugin_settings_group = mo_plugin::$plugin_name . '_settings_group';
        // Variable for the Plugin Prefix
        $this->domain = $this->plugin_prefix;
        // Variable for the Plugin Options Array
        $this->plugin_options = get_option($this->plugin_settings);
        
        /**
         * ***********************************
         * Plugin System Function Init
         * ***********************************
         */
        $this->on_activation();
        // Initiate functions on Deactivation : Optional
        $this->on_deactivation();
        // internationalization
        
        $this->get_login_cookie_api();
        $this->set_account_id();
        
        add_action('after_setup_theme', array(
            $this,
            'internationalize'
        ));
        // Add Top Level Menu Item : Optional
        add_action('admin_menu', array(
            $this,
            'top_level_menu_item'
        ));
        // Remove Top Level Menu Item in Submenu
        add_action('admin_menu', array(
            $this,
            'remove_duplicate_submenu_item'
        ));
        // Add Settings Submenu Item to the "Settings" Top Level Menu Item : Optional
        add_action('admin_menu', array(
            $this,
            'settings_sub_menu_item'
        ));
        // Add Links to the Plugin Page in the Description : Optional
        add_filter('plugin_row_meta', array(
            $this,
            'plugin_meta_links'
        ), 10, 2);
        // Add Settings Link to the Plugin Page Under Plugin's Name : Optional
        add_filter('plugin_action_links_' . $this->plugin_basename, array(
            $this,
            'add_settings_link'
        ));
        // Register External CSS and JavaScript files for : Optional
        add_action('admin_init', array(
            $this,
            'register_admin_scripts'
        ));
        // Enqueue External JavaScript file frontend-scripts.js to the Frontend : Optional
        add_action('wp_enqueue_scripts', array(
            $this,
            'enqueue_frontend_scripts'
        ));
        // Enqueue External CSS file frontend-style.css to the Frontend : Optional
        add_action('wp_print_styles', array(
            $this,
            'enqueue_frontend_styles'
        ));
    
    /**
     * ***********************************
     * Plugin Custom Function Init
     * ***********************************
     */
    /**
     * This is the action to initiate the code for your plugin
     * If you have multiple functions you can copy
     * the following action and and change 'custom_method_one'
     * to your function's unique name
     */
    }
    // End Class Constructor
    
    /*
     * ============================== Plugin System Functions ============================================
     */
    /**
     * On Activation
     *
     * This function creates the options for the plugin
     * as defined from the $options_list variable.
     *
     * @uses empty()
     * @uses register_activation_hook()
     * @uses array()
     *      
     * @since 1.0
     */
    public function on_activation()
    {
        register_activation_hook($this->plugin_basename, array(
            $this,
            'load_plugin_options'
        ));
        if (get_option('mo_lp_plugin_activated', 0)) {
            update_option('mo_lp_plugin_activated', 1);
        }
    }
    // End On Activation
    
    /**
     * On Deactivation
     *
     * This function removes all options and tables
     * apon deactivation of the plugin
     *
     * @uses empty()
     * @uses register_deactivation_hook()
     * @uses array()
     *      
     * @since 1.0
     */
    public function on_deactivation()
    {
        register_deactivation_hook($this->plugin_basename, array(
            $this,
            'delete_plugin_options'
        ));
    }
    // End On Deactivation
    
    // ---------------- login to marketing optimizer site using api ---------------
    public function get_login_cookie_api()
    {
        
        $mo_settings_obj = new mo_settings();
        $mo_account_user_id = $mo_settings_obj->get_mo_username();
        $mo_account_password = $mo_settings_obj->get_mo_password();
        
        if (! isset($_COOKIE["mo_api_live"]) && $mo_account_user_id && $mo_account_password) {
            $mo_api = new mo_api_auth($mo_account_user_id, $mo_account_password);
            $mo_api->set_cookie_name('mo_api_live')
                ->set_is_new_session(true)
                ->execute();
            $decodec_result = json_decode($mo_api->get_response(), true);
            setcookie("mo_api_live", $decodec_result['data']['session_id'], time() + 600, "/");
            if (! $mo_settings_obj->get_mo_account_id()) {
                $this->set_account_id($decodec_result['data']['account_id']);
            }
        }
    }

    public function set_account_id($account_id = false)
    {
        if ($account_id) {
            $mo_settings_obj = new mo_settings();
            $mo_settings_obj->set_mo_account_id($account_id);
            $mo_settings_obj->save();
        }
    }
    
    /**
     * Load Plugin Options
     *
     * Loads the plugin options to the
     * WordPress options table if there are
     * options set in the $options_list array.
     *
     * @uses update_option()
     *      
     * @since 1.0
     */
    public function load_plugin_options()
    {
        update_option($this->plugin_settings, $this->options_list);
        $this->mo_migrate_to_new_plugin();
    }
    // End Load Plugin Options
    
    /**
     * Remove Plugin Options
     *
     * @uses delete_option()
     *      
     * @since 1.0
     */
    public function delete_plugin_options()
    {
        delete_option($this->plugin_settings);
    }
    // End Remove Plugin Options
    
    /**
     * internationalize
     *
     * Make youe plugin translatable
     *
     * @uses load_theme_textdomain()
     *      
     * @since 1.0
     */
    public function internationalize()
    {
        load_theme_textdomain($this->domain, $this->plugin_dir . '/languages');
    }

    /**
     * Menu Item Title
     *
     * If the $menu_title variable is set
     * this function will override the
     * generic menu title
     *
     * @uses ucwords()
     * @uses str_replace()
     * @uses isset()
     * @return $generic
     * @return $this->menu_title
     *
     * @since 1.0
     */
    public function menu_title()
    {
        $generic = ucwords(str_replace("-", " ", self::$plugin_name));
        if (! isset($this->menu_title)) {
            return $generic;
        } else {
            return $this->menu_title;
        }
    }
    // End Menu Item Title
    
    /**
     * Top Level Menu Item
     *
     * @uses add_menu_page()
     * @uses array()
     * @uses add_action()
     *      
     * @since 1.0
     */
    public function top_level_menu_item()
    {
        $add_top_level_menu_page = add_menu_page(
            // Page Title
            $this->menu_title(), 
            // Menu Title
            'Marketing Optimizer', 
            // User Capability
            'manage_options', 
            // Menu Slug
            self::$plugin_name . '-settings', 
            // Page Link Function
            array(
                $this,
                'load_plugin_settings_page'
            ), 
            // Icon URL
            self::$plugin_url . 'images/moicon.png', 100);
        add_action('admin_print_styles-' . $add_top_level_menu_page, array(
            $this,
            'enqueue_admin_scripts'
        ));
    }
    // End Top Level Menu Item
    
    /**
     * Remove Duplicate Submenu Menu Item
     *
     * By default WordPress creates a submenu link of your
     * top-level menu item. The following will remove that
     * from your submenu list.
     *
     * @uses add_submenu_page()
     * @uses array()
     * @uses add_action()
     *      
     * @since 1.0
     */
    public function remove_duplicate_submenu_item()
    {
        $remove_duplicate_submenu_item = add_submenu_page(self::$plugin_name . '-settings', '', '', 'manage_options', self::$plugin_name . '-settings');
        
        add_action('admin_print_styles-' . $remove_duplicate_submenu_item, array(
            $this,
            'enqueue_admin_scripts'
        ));
    }
    // End Remove Duplicate Submenu Menu Item
    
    /**
     * Submenu Item
     *
     * This is a template for submenu items.
     * You should give unique names to the
     * function name, Page Title, Menu Title, and Menu Slug.
     * Make sure you add a unique add_action() for
     * each submenu item in the Class Constructor
     *
     * @uses add_submenu_page()
     * @uses array()
     * @uses add_action()
     * @uses load_plugin_settings_pages_class()
     * @uses load_plugin_settings_scripts_class()
     *      
     * @since 1.0
     */
    public function sub_menu_item()
    {
        $add_new_submenu_page = add_submenu_page(
            // Parent Slug
            self::$plugin_name . '-settings', 
            // Page Title
            'Templates', 
            // Menu Title
            'Templates', 
            // User Capability
            'manage_options', 
            // Menu Slug
            'mo-lp-templates', 
            // Page Link Function
            array(
                $this,
                'load_plugin_settings_subpage'
            ));
        add_action('admin_print_styles-' . $add_new_submenu_page, array(
            $this,
            'enqueue_admin_scripts'
        ));
    }
    // End Submenu Item
    
    /**
     * Settings Submenu Item
     *
     * This is a function adds a submenu
     * link to the Settings top level menu
     * item
     *
     * @uses add_submenu_page()
     * @uses array()
     * @uses add_action()
     *      
     * @since 1.0
     */
    public function settings_sub_menu_item()
    {
        $add_settings_submenu = add_submenu_page(
            // Parent Slug
            'options-general.php', 
            // Page Title
            $this->menu_title(), 
            // Menu Title
            $this->menu_title(), 
            // User Capability
            'manage_options', 
            // Menu Slug
            'settings-sub-menu', 
            // Page Link Function
            array(
                $this,
                'load_plugin_settings_page'
            ));
        add_action('admin_print_styles-' . $add_settings_submenu, array(
            $this,
            'enqueue_admin_scripts'
        ));
    }
    // End Settings Submenu Item
    
    /**
     * Add Meta Links
     *
     * This function will create links from
     * the $$plugin_meta_links array above.
     * The links will appear in the description
     * area on the plugin page.
     *
     * @param string $links            
     * @param string $file            
     * @since 1.0
     */
    public function plugin_meta_links($links, $file)
    {
        if ($file == $this->plugin_basename) {
            if (isset($this->plugin_meta_links) && is_array($this->plugin_meta_links)) {
                foreach ($this->plugin_meta_links as $key => $value) {
                    $links[] = '<a href="' . $value . '">' . __($key, $this->domain) . '</a>';
                }
            }
        }
        return $links;
    }
    // End Add Meta Links
    
    /**
     * Add Settings Link
     *
     * This function creates a "Settings"
     * link Under the Plugin's name
     * on the plugin page.
     *
     * @param string $links            
     * @uses array()
     * @return array_merge
     *
     * @since 1.0
     */
    public function add_settings_link($links)
    {
        return array_merge(array(
            'settings' => '<a href="options-general.php?page=' . self::$plugin_name . '-settings">' . __("Settings", $this->domain) . '</a>'
        ), $links);
    }
    // Settings Submenu Item
    
    /**
     * Main Settings Admin Page
     *
     * This function loads the main Admin setting
     * page for your plugin.
     *
     * @since 1.0
     */
    public function load_plugin_settings_page()
    {
        include ($this->plugin_dir . 'admin/main-settings-page.php');
    }
    // End Main Settings Admin Page
    
    /**
     * Sub Settings Admin Page
     *
     * Create a unique funtion for each submenu item
     * with a unique finction name.
     *
     * @since 1.0
     */
    public function load_plugin_settings_subpage()
    {
        include ($this->plugin_dir . 'admin/mo-templates-settings.php');
    }
    // End Sub Settings Admin Page
    
    /**
     * Register Admin Styles
     *
     * Register the external CSS
     * file admin-settings-styles.js
     *
     * @uses wp_register_style()
     *      
     * @since 1.0
     */
    public function register_admin_scripts()
    {
        // Register plugin CSS file
        wp_register_style(self::$plugin_name . '_admin-settings-css', self::$plugin_url . 'css/admin-settings-styles.css', false, $this->get_version());
        wp_register_style(self::$plugin_name . '_font_awesome_css', self::$plugin_url . 'admin/css/font-awesome.min.css', false, $this->get_version());
        // Register plugin JavaScript file
        wp_register_script(self::$plugin_name . '_admin-settings-js', self::$plugin_url . 'js/admin-settings-scripts.js', false, $this->get_version(), true);
    }
    // End Register Settings Page Scripts
    
    /**
     * Enqueue Settings Page Scripts
     *
     * Enqueues the settings pages JavaScript
     * and CSS files when called from the menu
     * item creation classes.
     *
     * @uses wp_enqueue_style()
     * @uses wp_enqueue_script()
     *      
     * @since 1.0
     */
    public function enqueue_admin_scripts()
    {
        // Enqueue plugin CSS file
        // wp_enqueue_style($this->plugin_name . '_admin-settings-css');
        // Enqueue plugin JavaScript file
        // wp_enqueue_script($this->plugin_name . '_admin-settings-js' );
    }
    // End Register Frontend Scripts
    
    /**
     * Enqueue Frontend Scripts
     *
     * Enqueues the Frontend JavaScript files
     *
     * @uses wp_register_script()
     * @uses wp_enqueue_script()
     *      
     * @since 1.0
     */
    public function enqueue_frontend_scripts()
    {
        wp_enqueue_script('jquery-ui-dialog');
    }
    // End Enqueue Frontend Scripts
    
    /**
     * Enqueue Frontend Styles
     *
     * Enqueues the Frontend CSS files
     *
     * @uses wp_register_style()
     * @uses wp_enqueue_style()
     *      
     * @since 1.0
     */
    public function enqueue_frontend_styles()
    {
        wp_enqueue_style('jquery_ui-css', plugins_url('admin/css/jquery_ui.css', __FILE__));
    }
    // End Enqueue Frontend Styles
    public static function get_version()
    {
        return self::$plugin_version;
    }

    public function mo_lp_admin_scripts($hook)
    {
        if (is_admin()) {
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('jquery-toggles', self::$plugin_url . 'admin/js/toggles.min.js');
            wp_enqueue_script('jquery-clone-fix', self::$plugin_url . 'admin/js/jquery.fix.clone.js', 'jquery');
            wp_enqueue_style('mo_lp_admin_toggles_css', self::$plugin_url . 'admin/css/toggles.css');
            wp_enqueue_style('mo_lp_admin_toggles_modern_css', self::$plugin_url . 'admin/css/toggles-modern.css');
            wp_enqueue_style('mo_admin_css', self::$plugin_url . 'admin/css/mo_admin.css');
            wp_enqueue_style('jquery_ui-css', plugins_url('admin/css/jquery_ui.css', __FILE__));
            wp_enqueue_script('jquery-ui-slider');
            wp_enqueue_style(self::$plugin_name . '_font_awesome_css');
            wp_enqueue_script('jquery-fancy', self::$plugin_url . 'admin/js/jquery.fancybox-1.3.4.js');
            wp_enqueue_style('mo_admin_css_fancy', self::$plugin_url . 'admin/css/jquery.fancybox-1.3.4.css');
        }
        if ($hook == 'post-new.php' && (isset($_GET['post_type']) && ($_GET['post_type'] === 'mo_landing_page' || $_GET['post_type'] === 'mo_sp' || $_GET['post_type'] === 'mo_ct'))) {
            // Create New Landing Jquery UI
            wp_enqueue_style('mo_lp_admin_post_new_css', self::$plugin_url . 'admin/css/mo_admin_post_new.css');
            wp_enqueue_script('mo_lp_mixitup_js', self::$plugin_url . 'admin/js/jquery.mixitup.min.js');
            wp_enqueue_script('mo_lp_admin_post_new_js', self::$plugin_url . 'admin/js/mo_lp_admin_post_new.js');
        }
        if ($hook == 'post.php') {
            wp_enqueue_style('mo_lp_admin_post_css', self::$plugin_url . 'admin/css/mo_admin_post.css');
            wp_enqueue_script('mo_lp_mixitup_js', self::$plugin_url . 'admin/js/jquery.mixitup.min.js');
            wp_enqueue_script('mo_lp_admin_post_new_js', self::$plugin_url . 'admin/js/mo_lp_admin_post_new.js');
        }
        add_action('admin_footer', array(
            $this,
            'mo_lp_get_slider_js'
        ));
    }

    public function mo_lp_get_slider_js()
    {
        $mo_settings_obj = new mo_settings();
        $mo_lp_slider_start_value = $mo_settings_obj->get_mo_lp_variation_percentage() ? $mo_settings_obj->get_mo_lp_variation_percentage() : 90;
        $mo_sp_slider_start_value = $mo_settings_obj->get_mo_sp_variation_percentage() ? $mo_settings_obj->get_mo_sp_variation_percentage() : 90;
        echo '<script>
                    jQuery(function() {
			    jQuery( "#mo_lp_slider-range-max" ).slider({
                                range: "max",
                                min: 10,
                                max: 90,
                                value: ' . $mo_lp_slider_start_value . ',
                                    step:10,
                                slide: function( event, ui ) {
                                          var label = "Exploitation: "+ui.value+"%/Exploration: "+(100-ui.value)+"%"
                                  jQuery( "#mo_lp_amount" ).val(label  );
                                  jQuery( "#mo_lp_variation_percentage" ).val(ui.value  );
                                }
			    });
                            var labelval =  "Exploitation: "+jQuery( "#mo_lp_slider-range-max" ).slider( "value" )+"%/Exploration: "+(100-jQuery( "#mo_lp_slider-range-max" ).slider( "value" ))+"%";
			    jQuery( "#mo_lp_amount" ).val( labelval );
			      		
			    jQuery( "#mo_sp_slider-range-max" ).slider({
                                range: "max",
                                min: 10,
                                max: 90,
                                value: ' . $mo_sp_slider_start_value . ',
                                    step:10,
                                slide: function( event, ui ) {
                                          var label = "Exploitation: "+ui.value+"%/Exploration: "+(100-ui.value)+"%"
                                          jQuery( "#amount" ).val(label  );
                                  jQuery( "#mo_sp_variation_percentage" ).val(ui.value  );
                                }
			    });
                            var labelval =  "Exploitation: "+jQuery( "#mo_sp_slider-range-max" ).slider( "value" )+"%/Exploration: "+(100-jQuery( "#mo_sp_slider-range-max" ).slider( "value" ))+"%";
			    jQuery( "#amount" ).val( labelval );
				
                    });
	</script>';
    }

    public function mo_migrate_to_new_plugin()
    {
        global $wpdb;
        if (! get_option('mo_migration', 0)) {
            $old_var_page_ids_arr = $wpdb->get_results('SELECT * FROM wp_posts WHERE post_type = "variation-page"');
            foreach ($old_var_page_ids_arr as $post_obj) {
                $parent_page = get_post_meta($post_obj->ID, 'mo_variation_parent', true);
                if ($parent_page) {
                    $this->mo_update_parent_page($parent_page);
                    $mo_page_obj = mo_pages::instance($parent_page);
                    $mo_page_var_ids_arr = $mo_page_obj->get_variation_ids_arr();
                    if (empty($mo_page_var_ids_arr)) {
                        $mo_page_var_ids_arr[0] = 0;
                    }
                    $next_var_id = end($mo_page_var_ids_arr) + 1;
                    $mo_page_var_ids_arr[$next_var_id] = $next_var_id;
                    $mo_page_obj->set_variation_ids_arr($mo_page_var_ids_arr);
                    $mo_page_obj->set_variations_arr($mo_page_var_ids_arr);
                    $mo_page_obj->set_variation_property($next_var_id, 'title', $post_obj->post_title);
                    $mo_page_obj->set_variation_property($next_var_id, 'description', get_post_meta($post_obj->ID, 'mo_variation_name', true));
                    $mo_page_obj->set_variation_property($next_var_id, 'content', $post_obj->post_content);
                    $mo_page_obj->set_variation_property($next_var_id, 'variation_id', get_post_meta($post_obj->ID, 'mo_variation_id', true));
                    $status = get_post_meta($post_obj->ID, 'mo_variation_active', true) == 'true' ? 1 : 0;
                    $mo_page_obj->set_variation_property($next_var_id, 'status', $status);
                    $mo_page_obj->set_variation_property($next_var_id, 'template', get_post_meta($post_obj->ID, '_post_template', true));
                    $mo_page_obj->set_variation_property($next_var_id, 'impressions', get_post_meta($post_obj->ID, 'mo_page_views_count', true));
                    $mo_page_obj->set_variation_property($next_var_id, 'visitors', get_post_meta($post_obj->ID, 'mo_unique_page_views_count', true));
                    $mo_page_obj->set_variation_property($next_var_id, 'conversions', get_post_meta($post_obj->ID, 'mo_conversion_count', true));
                    $mo_page_obj->save();
                    wp_delete_post($post_obj->ID, true);
                }
            }
            update_option('mo_migration', 1);
        }
    }

    public function mo_update_parent_page($post_id)
    {
        $mo_page_obj = mo_pages::instance($post_id);
        $mo_page_var_ids_arr = $mo_page_obj->get_variation_ids_arr();
        if (empty($mo_page_var_ids_arr)) {
            $mo_page_var_ids_arr[0] = 0;
        }
        $mo_page_obj->set_variation_ids_arr($mo_page_var_ids_arr);
        $mo_page_obj->set_variations_arr($mo_page_var_ids_arr);
        $mo_page_obj->save();
        $mo_page_obj->set_variation_property(0, 'variation_id', get_post_meta($post_id, 'mo_variation_id', true));
        $status = get_post_meta($post_id, 'mo_variation_active', true) == 'true' ? 1 : 0;
        $mo_page_obj->set_variation_property(0, 'status', $status);
        $mo_page_obj->set_variation_property(0, 'template', get_post_meta($post_id, '_post_template', true));
        $mo_page_obj->set_variation_property(0, 'impressions', get_post_meta($post_id, 'mo_page_views_count', true));
        $mo_page_obj->set_variation_property(0, 'visitors', get_post_meta($post_id, 'mo_unique_page_views_count', true));
        $mo_page_obj->set_variation_property(0, 'conversions', get_post_meta($post_id, 'mo_conversion_count', true));
        $mo_page_obj->save();
    }
}
// Initiate the Plugin Class
$mo_plugin_obj = new mo_plugin();
?>
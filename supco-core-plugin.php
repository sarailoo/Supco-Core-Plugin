<?php
/**
 * Plugin Name: Supco Core Plugin
 * Description: Supco Core Plugin For Managing Products
 * Plugin URI:  https://sarailoo.ir
 * Version:     1.0
 * Author:      Reza Sarailoo
 * Author URI:  https://sarailoo.ir
 * License:     MIT
 * Text Domain: supco-core
 * Domain Path: /languages
 */
add_action('plugins_loaded', array(PSR4_WordPress_Plugin::get_instance(), 'plugin_setup'));
register_activation_hook( __FILE__ , array('PSR4_WordPress_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__ , array('PSR4_WordPress_Plugin', 'deactivate' ) );
register_uninstall_hook(__FILE__,  array('PSR4_WordPress_Plugin', 'uninstall' ) );
class PSR4_WordPress_Plugin
{
    /**
     * Plugin instance.
     *
     * @see get_instance()
     * @type object
     */
    protected static $instance = NULL;
    /**
     * URL to this plugin's directory.
     *
     * @type string
     */
    public $plugin_url = '';
    /**
     * Path to this plugin's directory.
     *
     * @type string
     */
    public $plugin_path = '';

    /**
     * Access this plugin’s working instance
     *
     * @wp-hook plugins_loaded
     * @since   2012.09.13
     * @return  object of this class
     */
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }

    /**
     * Used for regular plugin work.
     *
     * @wp-hook plugins_loaded
     * @return  void
     */
    public function plugin_setup()
    {
        $this->plugin_url = plugins_url('/', __FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->load_language('supco-core');
        spl_autoload_register(array($this, 'autoload'));
        Actions\Post::hook_into_wordpress();
    }
     
    /**
     * Constructor. Intentionally left empty and public.
     *
     * @see plugin_setup()
     */
    public function __construct() {}

    public static function activate() {
        flush_rewrite_rules();
        require_once plugin_dir_path(__FILE__).'includes/Actions/Post.php';
    }
    public static function deactivate() {
        flush_rewrite_rules();
    }
    public static function uninstall() {
        require_once plugin_dir_path(__FILE__).'includes/Actions/Post.php';
	}
    /**
     * Loads translation file.
     *
     * Accessible to other classes to load different language files (admin and
     * front-end for example).
     *
     * @wp-hook init
     * @param   string $domain
     * @return  void
     */
    public function load_language($domain)
    {
        load_plugin_textdomain($domain, FALSE, $this->plugin_path . '/languages');
    }

    /**
     * @param $class
     *
     * autoload function runs when the class doesn't exist in that file and autoloud function includes it
     * and it supports namespaces and because namespaces uses backslashes(/) and paths in includes uses slashes(/)
     * then we should replace string backslash with slash to correctly include our class when the class created by namespaces like new Post\MyClass. 
     */
    public function autoload($class)
    {
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        if (!class_exists($class)) {
            $class_full_path = $this->plugin_path . 'includes/' . $class . '.php';

            if (file_exists($class_full_path)) {
                require $class_full_path;
            }
        }
    }
}
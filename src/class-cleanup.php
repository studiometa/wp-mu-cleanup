<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.studiometa.fr
 * @since             1.0.0
 * @package           Wp_Mu_Cleanup
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress MU Cleanup
 * Plugin URI:        https://github.com/studiometa/wp-mu-cleanup
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Studio Meta
 * Author URI:        https://www.studiometa.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-mu-cleanup
 * Domain Path:       /languages
 */

namespace Studiometa\WP;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Mu_Cleanup
 * @subpackage Wp_Mu_Cleanup/public
 * @author     Studio Meta <agence@studiometa.fr>
 */
class Cleanup {

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version = '1.0.0';

	/**
	 * __construct
	 */
	public function __construct() {
		// Clean up <head>.
		add_action( 'init', array( $this, 'cleanup_head' ) );

		// Remove WP version from RSS.
		add_filter( 'the_generator', array( $this, 'remove_version' ) );

		// Remove WordPress version from js & css enqueued files.
		add_filter( 'style_loader_src', array( $this, 'remove_version_css_js' ), 9999 );
		add_filter( 'script_loader_src', array( $this, 'remove_version_css_js' ), 9999 );

		// Make login error message the same.
		add_filter( 'login_errors', array( $this, 'simple_wordpress_errors' ) );

		// Remove emojis related files.
		add_action( 'init', array( $this, 'disable_emojis' ) );

		// Remove useless widgets from the dashboard.
		add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ) );

		// Remove comments from the admin bar.
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_comments_from_admin_bar' ) );

		// Remove comments from the admin menu.
		add_action( 'admin_menu', array( $this, 'remove_comments_from_admin_menu' ) );
	}

	/**
	 * Remove a lot of useless stuff added by default in WordPress
	 */
	public function cleanup_head() {
		// EditURI link.
		remove_action( 'wp_head', 'rsd_link' );
		// Category feed links.
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		// Post and comment feed links.
		remove_action( 'wp_head', 'feed_links', 2 );
		// Windows Live Writer.
		remove_action( 'wp_head', 'wlwmanifest_link' );
		// Index link.
		remove_action( 'wp_head', 'index_rel_link' );
		// Previous link.
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
		// Start link.
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
		// Canonical.
		remove_action( 'wp_head', 'rel_canonical', 10, 0 );
		// Shortlink.
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
		// Links for adjacent posts.
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
		// WP version.
		remove_action( 'wp_head', 'wp_generator' );
	}

	/**
	 * Remove WordPress version number in meta tags
	 *
	 * @return String
	 */
	public function remove_version() {
		return '';
	}

	/**
	 * Suppress version number in enqued css & js files
	 *
	 * @param  String $src The source path of the asset.
	 * @return String
	 */
	public function remove_version_css_js( $src ) {
		if ( strpos( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}

	/**
	 * Make all login faillure message the same
	 *
	 * @return String Basic login error message
	 */
	public function simple_wordpress_errors() {
		return __( 'Login credentials are incorrect', 'studio-meta-cleaner' );
	}

	/**
	 * Remove all occurence of Emoji's in WordPress
	 */
	public function disable_emojis() {
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	}

	/**
	 * Remove useless widgets from the dashboard
	 */
	public function remove_dashboard_widgets() {
		// Remove WordPress activities.
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
		// Remove WordPress events.
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		// Remove WordPress welcome board.
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}

	/**
	 * Remove comments from admin Bar
	 */
	public function remove_comments_from_admin_bar() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu( 'comments' );
	}

	/**
	 * Remove comments from admin menu
	 */
	public function remove_comments_from_admin_menu() {
		// Remove comments admin menu item.
		remove_menu_page( 'edit-comments.php' );
	}
}

new \Studiometa\WP\Cleanup();

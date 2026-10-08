<?php
/**
 * Plugin Name:       Scroll To Wide Effects
 * Description:       Custom Elementor widgets. Includes the "Scroll Expand Image" widget (a sticky image that grows to full width while scrolling).
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:      Saeed Amini
 * Author URI:  https://github.com/saeedamini144
 * License:           GPL-2.0-or-later
 * Text Domain:       scroll-to-wide-effects
 * Domain Path:       /languages
 * Elementor tested up to: 3.25
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEI_WIDGETS_VERSION', '1.0.0' );
define( 'SEI_WIDGETS_FILE', __FILE__ );
define( 'SEI_WIDGETS_PATH', plugin_dir_path( __FILE__ ) );
define( 'SEI_WIDGETS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main plugin class.
 */
final class Sei_Widgets_Plugin {

	const MIN_ELEMENTOR_VERSION = '3.20.0';
	const MIN_PHP_VERSION       = '7.4';

	/**
	 * @var Sei_Widgets_Plugin|null
	 */
	private static $instance = null;

	/**
	 * @return Sei_Widgets_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', [ $this, 'load_textdomain' ] );
		add_action( 'plugins_loaded', [ $this, 'boot' ], 20 );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'scroll-to-wide-effects', false, dirname( plugin_basename( SEI_WIDGETS_FILE ) ) . '/languages' );
	}

	/**
	 * Run compatibility checks, then hook into Elementor.
	 */
	public function boot() {
		if ( version_compare( PHP_VERSION, self::MIN_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_min_php' ] );
			return;
		}

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_missing_elementor' ] );
			return;
		}

		if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, self::MIN_ELEMENTOR_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_min_elementor' ] );
			return;
		}

		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	/**
	 * Register (not enqueue) assets. They load only when a widget lists them
	 * in get_style_depends() / get_script_depends().
	 */
	public function register_styles() {
		wp_register_style(
			'sei-scroll-expand-image',
			SEI_WIDGETS_URL . 'assets/css/scroll-expand-image.css',
			[],
			SEI_WIDGETS_VERSION
		);
	}

	public function register_scripts() {
		wp_register_script(
			'sei-scroll-expand-image',
			SEI_WIDGETS_URL . 'assets/js/scroll-expand-image.js',
			[],
			SEI_WIDGETS_VERSION,
			[
				'in_footer' => true,
				'strategy'  => 'defer',
			]
		);
	}

	/**
	 * @param \Elementor\Elements_Manager $elements_manager
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'scroll-to-wide-effects',
			[
				'title' => esc_html__( 'Scroll To Wide Effects', 'scroll-to-wide-effects' ),
				'icon'  => 'eicon-font',
			]
		);
	}

	/**
	 * Add every widget of this plugin here.
	 * To add a new design: create widgets/class-{name}.php, require it and register it below.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public function register_widgets( $widgets_manager ) {
		require_once SEI_WIDGETS_PATH . 'widgets/class-scroll-expand-image.php';

		$widgets_manager->register( new Sei_Scroll_Expand_Image_Widget() );
	}

	/* ---------------------------------------------------------------------
	 * Admin notices
	 * ------------------------------------------------------------------ */

	public function notice_missing_elementor() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			esc_html__( '"Scroll To Wide Effects" requires Elementor to be installed and activated.', 'scroll-to-wide-effects' )
		);
	}

	public function notice_min_elementor() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %s: minimum Elementor version. */
					__( '"Scroll To Wide Effects" requires Elementor version %s or greater.', 'scroll-to-wide-effects' ),
					self::MIN_ELEMENTOR_VERSION
				)
			)
		);
	}

	public function notice_min_php() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %s: minimum PHP version. */
					__( '"Scroll To Wide Effects" requires PHP version %s or greater.', 'scroll-to-wide-effects' ),
					self::MIN_PHP_VERSION
				)
			)
		);
	}
}

Sei_Widgets_Plugin::instance();

<?php
/**
 * Settings Page.
 *
 * @package CodeSoup\InstapageCache
 */

declare( strict_types=1 );

namespace CodeSoup\InstapageCache\Admin;

use function CodeSoup\InstapageCache\plugin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SettingsPage class.
 */
class SettingsPage {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	private const OPTION_NAME = 'codesoup_ilc_caching_settings';

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	private const MENU_SLUG = 'instapage-cache';

	/**
	 * Settings page constructor.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 */
	private function register_hooks(): void {
		$hooker = plugin()->get( 'hooker' );

		$hooker->add_actions(
			array(
				array( 'admin_menu', $this, 'register_menu_page', 20 ),
				array( 'admin_init', $this, 'register_settings' ),
				array( 'admin_post_instapage_cache_sync', $this, 'handle_sync' ),
			)
		);
	}

	/**
	 * Register the settings menu page.
	 */
	public function register_menu_page(): void {
		add_submenu_page(
			'instapage_dashboard',
			__( 'Settings', 'instapage-cache' ),
			__( 'Settings', 'instapage-cache' ),
			'manage_instapage_cache',
			self::MENU_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Register settings and fields.
	 */
	public function register_settings(): void {
		// Register setting
		register_setting(
			self::MENU_SLUG,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'show_in_rest'      => false,
			)
		);

		// Register section
		add_settings_section(
			'caching_section',
			__( 'Settings', 'instapage-cache' ),
			null,
			self::MENU_SLUG
		);

		// Enable caching field
		add_settings_field(
			'enable_caching',
			__( 'Enable Caching', 'instapage-cache' ),
			array( $this, 'render_checkbox_field' ),
			self::MENU_SLUG,
			'caching_section',
			array(
				'name'  => 'enable_caching',
				'label' => __( 'Enable caching for Instapage pages', 'instapage-cache' ),
			)
		);

		// Auto clear interval field
		add_settings_field(
			'auto_clear_interval',
			__( 'Auto Clear Cache Every', 'instapage-cache' ),
			array( $this, 'render_number_field' ),
			self::MENU_SLUG,
			'caching_section',
			array(
				'name'        => 'auto_clear_interval',
				'label'       => __( 'minutes', 'instapage-cache' ),
				'placeholder' => '60',
			)
		);
	}

	/**
	 * Render checkbox field.
	 *
	 * @param array $args Field arguments.
	 */
	public function render_checkbox_field( array $args ): void {
		$value = $this->get_option( $args['name'] );
		?>
		<input 
			type="checkbox" 
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['name'] ); ?>]" 
			value="1" 
			<?php checked( $value, 1 ); ?>
		/>
		<label><?php echo esc_html( $args['label'] ); ?></label>
		<?php
	}

	/**
	 * Render number field.
	 *
	 * @param array $args Field arguments.
	 */
	public function render_number_field( array $args ): void {
		$value = $this->get_option( $args['name'] );
		?>
		<input 
			type="number" 
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['name'] ); ?>]" 
			value="<?php echo esc_attr( $value ); ?>" 
			placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
			min="1"
		/>
		<label><?php echo esc_html( $args['label'] ); ?></label>
		<?php
	}

	/**
	 * Render the settings page.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_instapage_cache' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'instapage-cache' ) );
		}

		require plugin()->config['PLUGIN_BASE_PATH'] . 'templates/admin/caching-settings.php';
	}

	/**
	 * Sanitize settings.
	 *
	 * @param mixed $input The input to sanitize.
	 * @return array
	 */
	public function sanitize_settings( $input ): array {
		if ( ! is_array( $input ) ) {
			return array();
		}

		$sanitized = array();

		// Sanitize enable_caching
		if ( isset( $input['enable_caching'] ) ) {
			$sanitized['enable_caching'] = (int) $input['enable_caching'];
		}

		// Sanitize auto_clear_interval
		if ( isset( $input['auto_clear_interval'] ) ) {
			$value = (int) $input['auto_clear_interval'];
			$sanitized['auto_clear_interval'] = $value > 0 ? $value : 60;
		}

		return $sanitized;
	}

	/**
	 * Get a single option value.
	 *
	 * @param string $key The option key.
	 * @return mixed
	 */
	public function get_option( string $key ) {
		$options = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		return $options[ $key ] ?? null;
	}

	/**
	 * Update a single option value.
	 *
	 * @param string $key The option key.
	 * @param mixed  $value The option value.
	 * @return bool
	 */
	public function update_option( string $key, $value ): bool {
		$options = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		$options[ $key ] = $value;

		return update_option( self::OPTION_NAME, $options );
	}

	/**
	 * Get all options.
	 *
	 * @return array
	 */
	public function get_all_options(): array {
		$options = get_option( self::OPTION_NAME, array() );

		return is_array( $options ) ? $options : array();
	}

	/**
	 * Handle sync action.
	 */
	public function handle_sync(): void {
		if ( ! current_user_can( 'manage_instapage_cache' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'instapage-cache' ) );
		}

		check_admin_referer( 'instapage_cache_sync' );

		$sync_service = new \CodeSoup\InstapageCache\Services\SyncService();
		$result       = $sync_service->full_sync();

		$redirect_url = add_query_arg(
			array(
				'page'    => self::MENU_SLUG,
				'synced'  => $result['success'] ? '1' : '0',
				'message' => urlencode( $result['message'] ),
			),
			admin_url( 'admin.php' )
		);

		wp_redirect( $redirect_url );
		exit;
	}
}


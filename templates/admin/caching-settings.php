<?php
/**
 * Caching Settings Template.
 *
 * @package CodeSoup\InstapageCache
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php
	// Display sync message
	if ( isset( $_GET['synced'] ) ) {
		$synced = intval( $_GET['synced'] );
		$message = isset( $_GET['message'] ) ? sanitize_text_field( wp_unslash( $_GET['message'] ) ) : '';
		$class = $synced ? 'notice-success' : 'notice-error';
		?>
		<div class="notice <?php echo esc_attr( $class ); ?> is-dismissible">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}
	?>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'instapage-cache' );
		do_settings_sections( 'instapage-cache' );
		submit_button();
		?>
	</form>

	<hr />

	<h2><?php esc_html_e( 'Sync Cache', 'instapage-cache' ); ?></h2>
	<p><?php esc_html_e( 'Click the button below to sync cache data from Instapage.', 'instapage-cache' ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="instapage_cache_sync" />
		<?php wp_nonce_field( 'instapage_cache_sync' ); ?>
		<?php submit_button( __( 'Sync Now', 'instapage-cache' ), 'primary', 'submit', false ); ?>
	</form>
</div>


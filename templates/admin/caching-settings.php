<?php
/**
 * Caching Settings Template.
 *
 * @package CodeSoup\InstapageCache
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Get active tab from URL
$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php
	// Display sync message using wp_admin_notice
	if ( isset( $_GET['synced'] ) ) {
		$synced = intval( $_GET['synced'] );
		$message = isset( $_GET['message'] ) ? sanitize_text_field( wp_unslash( $_GET['message'] ) ) : '';
		$type = $synced ? 'success' : 'error';

		wp_admin_notice(
			$message,
			array(
				'type'        => $type,
				'dismissible' => true,
			)
		);
	}
	?>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo esc_url( add_query_arg( 'tab', 'general' ) ); ?>" class="nav-tab <?php echo 'general' === $active_tab ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'General', 'instapage-cache' ); ?>
		</a>
		<a href="<?php echo esc_url( add_query_arg( 'tab', 'sync' ) ); ?>" class="nav-tab <?php echo 'sync' === $active_tab ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Sync', 'instapage-cache' ); ?>
		</a>
	</h2>

	<div class="tab-content">
		<?php if ( 'general' === $active_tab ) : ?>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'instapage-cache' );
				do_settings_sections( 'instapage-cache' );
				submit_button();
				?>
			</form>
		<?php elseif ( 'sync' === $active_tab ) : ?>
			<h3><?php esc_html_e( 'Sync Cache', 'instapage-cache' ); ?></h3>
			<p><?php esc_html_e( 'Click the button below to sync cache data from Instapage.', 'instapage-cache' ); ?></p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="instapage_cache_sync" />
				<?php wp_nonce_field( 'instapage_cache_sync' ); ?>
				<?php submit_button( __( 'Sync Now', 'instapage-cache' ), 'primary', 'submit', false ); ?>
			</form>
		<?php endif; ?>
	</div>
</div>

<style>
	.tab-content {
		margin-top: 20px;
	}
</style>


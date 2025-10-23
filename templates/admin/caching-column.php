<?php
/**
 * Caching Column Template
 *
 * @package CodeSoup\InstapageCache
 * @var string $status_text The cache status text (Active/Inactive)
 * @var string $button_class The button CSS class
 * @var int $post_id The post ID
 * @var string $new_status The new status value for toggle
 * @var string $nonce The nonce for security
 * @var string $button_text The button text (Enable/Disable)
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="cache-status">
	<span><?php echo esc_html( $status_text ); ?></span>
	<span>
		<button 
			class="button <?php echo esc_attr( $button_class ); ?> toggle-cache-btn" 
			data-post-id="<?php echo intval( $post_id ); ?>" 
			data-enable="<?php echo esc_attr( $new_status ); ?>" 
			data-nonce="<?php echo esc_attr( $nonce ); ?>">
			<?php echo esc_html( $button_text ); ?>
		</button>
	</span>
</div>


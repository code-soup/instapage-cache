<?php
/**
 * Taxonomy Meta.
 *
 * @package CodeSoup\InstapageCache
 */

declare( strict_types=1 );

namespace CodeSoup\InstapageCache\Admin;

use function CodeSoup\InstapageCache\plugin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * TaxonomyMeta class.
 */
class TaxonomyMeta {

	/**
	 * Meta key.
	 *
	 * @var string
	 */
	private const META_KEY = 'scripts_custom_footer';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$hooker = plugin()->get( 'hooker' );
		$hooker->add_actions(
			array(
				array( PostType::TAXONOMY . '_add_form_fields', $this, 'add_meta_field' ),
				array( PostType::TAXONOMY . '_edit_form_fields', $this, 'edit_meta_field', 10, 2 ),
				array( 'edited_' . PostType::TAXONOMY, $this, 'save_meta_field', 10, 2 ),
				array( 'create_' . PostType::TAXONOMY, $this, 'save_meta_field', 10, 2 ),
				array( 'admin_head',  $this, 'hide_edit_term_fields' ),
			)
		);

		$hooker->add_filters(
			array(
				array( 'manage_edit-' . PostType::TAXONOMY . '_columns', $this, 'remove_description_column' ),
			)
		);
	}

	/**
	 * Add meta field to add term form.
	 */
	public function add_meta_field(): void {
		// Don't show on add form, only on edit
		return;
	}

	/**
	 * Edit meta field on edit term form.
	 *
	 * @param \WP_Term $term The term object.
	 */
	public function edit_meta_field( \WP_Term $term ): void {
		$value = get_term_meta( $term->term_id, self::META_KEY, true );
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="<?php echo esc_attr( self::META_KEY ); ?>">
					<?php esc_html_e( 'Custom Footer Scripts', 'instapage-cache' ); ?>
				</label>
			</th>
			<td>
				<textarea
					id="<?php echo esc_attr( self::META_KEY ); ?>"
					name="<?php echo esc_attr( self::META_KEY ); ?>"
					rows="5"
					placeholder="<?php esc_attr_e( 'Enter custom scripts for footer', 'instapage-cache' ); ?>"
				><?php echo esc_textarea( $value ); ?></textarea>
				<p class="description">
					<?php esc_html_e( 'Custom scripts to be added to the footer of pages in this category.', 'instapage-cache' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Hide fields from edit term form.
	 */
	public function hide_edit_term_fields(): void {
		global $pagenow;

		// Check if we're on the taxonomy edit page
		if ( ! isset( $_GET['taxonomy'] ) ) {
			return;
		}

		$taxonomy = sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) );

		if ( PostType::TAXONOMY !== $taxonomy ) {
			return;
		}

		?>
		<style>
			.edit + span.inline,
			.form-field.term-slug-wrap,
			.form-field.term-parent-wrap,
			.form-field.term-description-wrap {
				display: none !important;
			}

			#parent,
			.form-field.term-parent-wrap,
			#description,
			.form-field.term-description-wrap,
			#slug,
			.form-field.term-slug-wrap {
				display: none !important;
			}
		</style>
		<?php
	}

	/**
	 * Save meta field.
	 *
	 * @param int $term_id Term ID.
	 */
	public function save_meta_field( int $term_id ): void {
		if ( ! isset( $_POST[ self::META_KEY ] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_instapage_cache' ) ) {
			return;
		}

		$value = sanitize_textarea_field( wp_unslash( $_POST[ self::META_KEY ] ) );

		update_term_meta( $term_id, self::META_KEY, $value );
	}

	/**
	 * Get meta value for term.
	 *
	 * @param int $term_id Term ID.
	 * @return string
	 */
	public static function get_meta( int $term_id ): string {
		$value = get_term_meta( $term_id, self::META_KEY, true );

		return is_string( $value ) ? $value : '';
	}

	/**
	 * Remove description column from taxonomy list.
	 *
	 * @param array $columns Taxonomy columns.
	 * @return array Filtered columns.
	 */
	public function remove_description_column( array $columns ): array {

		unset( $columns['slug'] );
		unset( $columns['posts'] );
		unset( $columns['description'] );

		return $columns;
	}
}


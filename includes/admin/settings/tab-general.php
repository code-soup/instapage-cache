<?php

// Exit if accessed directly
defined( 'WPINC' ) || die; ?>

<table class="form-table" role="presentation">
	<?php do_settings_fields( 'codesoup_ilc_settigns_page', 'general' ); ?>
</table>

<?php submit_button(); ?>
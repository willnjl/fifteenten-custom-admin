<?php

function fifteenten_custom_admin_page_contents()
{
	if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) :
		update_option( 'image_attachment_id', absint( $_POST['image_attachment_id'] ) );
	endif;
?>
    <div class="fifteenten-admin-body">
        <div class="wrap">
           
            <form method='post' action="options.php">
                <!-- settings_fields( string $option_group )  -->
                <?php settings_fields( 'fifteenten_custom_admin_options' ); ?>
                <!-- do_settings_sections( string $page ) -->
                <?php do_settings_sections('fifteenten-theme-settings'); ?>
            
                <?php  submit_button(); ?>
            </form>
        </div>
    </div>
    <?
}

?>
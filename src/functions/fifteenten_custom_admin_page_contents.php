<?php

function fifteenten_custom_admin_page_contents()
{
if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) :
        update_option( 'media_selector_attachment_id', absint( $_POST['image_attachment_id'] ) );
    endif;?>
    <div class="fifteenten-admin-body">
        <section>
            <h1>
                Fifteen Ten Custom Settings
            </h1>
        </section>
        <form method='post' action="options.php">
            <?php settings_fields( 'fifteenten_custom_admin_options' ); ?>
            <section >
                <div class='image-preview-wrapper'>
                    <img id='image-preview' src='<?php echo wp_get_attachment_url( get_option( 'media_selector_attachment_id' ) ); ?>' width='200'>
                </div>
                <div>
                    <h2>
                        Admin Area Logo
                    </h2>
                    <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
                    <input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'media_selector_attachment_id' ); ?>'>
                    <input type="submit" name="submit_image_selector" value="Save" class="button-primary">
                </div>
            </section>
            <section>
                <div class="">        
                    <input
                    type="checkbox"
                    id="fifteenten_custom_disable_comments"
                    name="fifteenten_custom_disable_comments"
                    value="true"
                    <?= checked('true', get_option('fifteenten_custom_disable_comments', 'false')); ?>
                    />
                    
                    <label for="fifteenten_custom_disable_comments">
                        disable all comments    
                    </label>
                    
                </div>
        
            </section>
            <section>
                <div class="">        
                    <input
                    type="checkbox"
                    id="fifteenten_custom_acf_options"
                    name="fifteenten_custom_acf_options"
                    value="true"
                    <?= checked('true', get_option('fifteenten_custom_acf_options', 'true')); ?>
                    />
                    
                    <label for="fifteenten_custom_acf_options">
                        Enable ACF Options Page  
                    </label>
                    
                </div>
        
            </section>
            
                <?php  submit_button(); ?>
        </form>
    </div>
    <?
}

?>
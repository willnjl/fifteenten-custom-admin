<?php

function fifteenten_custom_admin_page_contents()
{
	if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) :
		update_option( 'image_attachment_id', absint( $_POST['image_attachment_id'] ) );
	endif;
?>
    <div class="fifteenten-admin-body">
        <div class="wrap">
            <section>
                <h1>
                    Fifteen Ten Custom Settings
                </h1>
            </section>
            <form method='post' action="options.php">
                <?php settings_fields( 'fifteenten_custom_admin_options' ); ?>
                <?php settings_fields( 'fifteenten_comments_disabler_options' ); ?>
                <?php settings_fields( 'fifteenten_cc_options' ); ?>
                <?php settings_fields( 'fifteenten_custom_acf_options' ); ?>
                <section >
                    <div class='image-preview-wrapper'>
                        <img id='image-preview' src='<?php echo wp_get_attachment_url( get_option( 'media_selector_attachment_id', 1 ) ); ?>' width='200'>
                    </div>
                    <div>
                        <h2>
                            Admin Area Logo
                        </h2>
                        <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Select Image' ); ?>" />
                        <input type='hidden' name='media_selector_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'media_selector_attachment_id' ); ?>'>
                        <input type="submit" name="submit_image_selector" value="Save" class="button-primary">
                    </div>
                </section>
                <section>
                    <div class="">
                        <h2>
                            Comments
                        </h2>
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
                        <h2>
                            Advanced Custom Fields
                        </h2>
                        <input
                        type="checkbox"
                        id="fifteenten_show_acf_page"
                        name="fifteenten_show_acf_page"
                        value="true"
                        <?= checked('true', get_option('fifteenten_show_acf_page', true)); ?>
                        />
                        <label for="fifteenten_show_acf_page">
                            Enable ACF Options Page
                        </label>
                    </div>
                </section>
                <section>
                    <div class="">
                        <h2>
                            Google Analytics
                        </h2>
            
                        <ul>
                            <li>
                                <input
                                type="checkbox"
                                id="fifteenten_enable_analytics"
                                name="fifteenten_enable_analytics"
                                value="true"
                                <?= checked('true', get_option('fifteenten_enable_analytics', true)); ?>
                                />
                                <?= get_option('fifteenten_enable_analytics', true)?>
    
                                <label for="fifteenten_enable_analytics">
                                    Enable 1510 Analytics
                                </label>
                            </li>
                            <li>
                                <input
                                type="text"
                                id="fifteenten_analytics_id"
                                name="fifteenten_analytics_id"
                                placeholder="GTM-XXXXXXX"
                                value="<?= esc_attr(get_option('fifteenten_analytics_id', '')); ?>"
                                />
                                <label for="fifteenten_analytics_id">
                                    Analytics ID <em>eg. GTM-XXXXXXX</em>
                                </label>
                            </li>
                            <li>
                                <input type="text" name="fifteenten_analytics_domain" id="fifteenten_analytics_domain" value="<?= esc_attr(get_option('fifteenten_analytics_domain', 'fifteenten_analytics_domain')); ?>">
                                <label for="fifteenten_analytics_domain">
                                    * Domain: e.g <em> example.co.uk</em>
                                </label>
                            </li>
                            <li>
                                <input type="number" name="fifteenten_analytics_duration" id="fifteenten_analytics_duration" value="<?= esc_attr(intval(get_option('fifteenten_analytics_duration', 90))); ?>">
                                <label for="fifteenten_analytics_duration">
                                    Days consent lasts for
                                </label>
                            </li>
                        </ul>
            
                    </div>
            
                </section>
            
                    <?php  submit_button(); ?>
            </form>
        </div>
    </div>
    <?
}

?>
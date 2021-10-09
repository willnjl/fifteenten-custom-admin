<?php

function fifteenten_customizer_page_contents()
{
    ?>
  
    <h1>

        Fifteen Ten Admin Customizer

    </h1>


    <form action="options.php" method="post">
          <?php  settings_fields( 'fifteenten_customizer_option' ); ?>
            <h3>This is my option</h3>
            <p>Some text here.</p>
            <table>
            <tr valign="top">
            <th scope="row"><label for="fifteenten_customizer_admin_logo">Label</label></th>
            <td><input type="text" id="fifteenten_customizer_admin_logo" name="fifteenten_customizer_admin_logo" value="<?php echo get_option('fifteenten_customizer_admin_logo'); ?>" /></td>
            </tr>
            </table>
          <?php  submit_button(); ?>
    </form>

    <?php
}
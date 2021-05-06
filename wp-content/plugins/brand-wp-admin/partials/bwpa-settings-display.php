<?php

/**
 * Provide an admin area view for the plugin settings.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Brand_WP_Admin
 * @subpackage Brand_WP_Admin/partials
 */
?>

<div class="wrap">
 
  <h2><?php _e( 'Branding Options', 'et-ext' ); ?></h2>

  <form id="form-brandwpadmin-options" action="options.php" method="post" enctype="multipart/form-data">

    <?php
      settings_fields('brandwpadmin_plugin_options');
      do_settings_sections('brandwpadmin');
    ?>

    <p class="submit">
      <input name="brandwpadmin_plugin_options[submit]" id="submit_options_form" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'brandwpadmin'); ?>" />
    </p>

  </form>

</div>

<?php

/**
 * Create the settings page for the plugin.
 */

class Brand_WP_Admin_Settings {
	/**
	 * Path to the main plugin directory.
	 * @var string
	 */
	private $plugin_dir_path;

	/**
	 * A plugin options object, with defaults and values loaded from the DB.
	 * @var Brand_WP_Admin_Options
	 */
	private $options;

	function __construct( $plugin_dir_path ) {
		$this->plugin_dir_path = $plugin_dir_path;

		$this->options = new Brand_WP_Admin_Options();
		$this->options->load();
	}

	/**
	 * Register a theme page for our options.
	 */
	public function add_theme_page() {
		// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
		add_submenu_page( 'options-general.php', 'Branding Options', 'Branding Options', 'edit_theme_options', 'brandwpadmin-settings', array( $this, 'render_options_page' ) );
	}

	/**
	 * Display the options page.
	 */
	public function render_options_page() {
		include_once $this->plugin_dir_path . 'partials/bwpa-settings-display.php';
	}

	/**
	 * Register the settings and sections required.
	 */
	public function register_settings() {
		register_setting( 'brandwpadmin_plugin_options', 'brandwpadmin_plugin_options', array( $this, 'validate_settings' ) );
			
	    // Add a form section for the Logo
	    add_settings_section('brandwpadmin_settings_logo_section', __( 'Logo for Login Page', 'brandwpadmin' ), array( $this, 'render_logo_section_header_text' ), 'brandwpadmin');
	 
	    // Add Logo uploader
	    add_settings_field('brandwpadmin_setting_logo',  __( 'Logo', 'brandwpadmin' ), array( $this, 'render_logo_setting' ), 'brandwpadmin', 'brandwpadmin_settings_logo_section');
	    
	    // Add Logo width field
	    add_settings_field('brandwpadmin_setting_logo_width',  __( 'Logo Max Width (px)', 'brandwpadmin' ), array( $this, 'render_logo_width_setting' ), 'brandwpadmin', 'brandwpadmin_settings_logo_section');

	    // Add logo preview
	    add_settings_field('brandwpadmin_setting_logo_preview',  __( 'Current Logo', 'brandwpadmin' ), array( $this, 'render_logo_preview_setting' ), 'brandwpadmin', 'brandwpadmin_settings_logo_section');


	    // Add a form section for the favicon
	    add_settings_section('brandwpadmin_settings_favicon_section', __( 'Favicon for WordPress admin tabs', 'brandwpadmin' ), array( $this, 'render_favicon_section_header_text' ), 'brandwpadmin');

	    // Add Favicon uploader
	    add_settings_field('brandwpadmin_setting_favicon',  __( 'Favicon', 'brandwpadmin' ), array( $this, 'render_favicon_setting' ), 'brandwpadmin', 'brandwpadmin_settings_favicon_section');
	    
	    // Add favicon preview
	    add_settings_field('brandwpadmin_setting_favicon_preview',  __( 'Current Favicon', 'brandwpadmin' ), array( $this, 'render_favicon_preview_setting' ), 'brandwpadmin', 'brandwpadmin_settings_favicon_section');
	}

	/**
	 * Render the description area for the logo section.
	 */
	public function render_logo_section_header_text() {
		
	}

	/**
	 * Render the file upload for the logo.
	 */
	public function render_logo_setting() {
		if ( 0 === $this->options->logo_id ) {
			$logo_id = '';
		} else {
			$logo_id = $this->options->logo_id;
		}

		?>

		<input type="hidden" id="brandwpadmin_logo_id" name="brandwpadmin_plugin_options[logo_id]" value="<?php echo esc_attr( $logo_id ); ?>" />
	    <input id="brandwpadmin_upload_logo_button" type="button" class="button js-brandwpadmin-open-upload-frame" value="<?php _e( 'Choose Image', 'brandwpadmin' ); ?>" />

		<?php
	}

	/**
	 * Render the input for the logo width.
	 */
	public function render_logo_width_setting() {
		if ( !$this->options->logo_width ) {
			$defaults = $this->options->get_defaults();
			$logo_width = $defaults['logo_width'];
		} else {
			$logo_width = $this->options->logo_width;
		}

		?>

		<input type="number" min="1" max="320" step="1" id="brandwpadmin_logo_width" name="brandwpadmin_plugin_options[logo_width]" value="<?php echo esc_attr( $logo_width ); ?>" />
	    <p class="description"><?php _e('The maximum width the login page can support is 320px.', 'brandwpadmin' ); ?></p>

		<?php
	}

	/**
	 * Render the logo preview.
	 */
	public function render_logo_preview_setting() {
		if ( !$this->options->logo_id ) {
			$logo_object = null;
		} else {
			$logo_object = wp_get_attachment_image_src( $this->options->logo_id, 'full' );
		}

		if ( !$this->options->logo_width ) {
			$defaults = $this->options->get_defaults();
			$logo_width = $defaults['logo_width'];
		} else {
			$logo_width = $this->options->logo_width;
		}

		?>

		<img class="" id="brandwpadmin_upload_logo_preview" src="<?php echo $logo_object[0]; ?>" alt="Current Logo" width="<?php echo $logo_object[1]; ?>" height="<?php echo $logo_object[2]; ?>" style="<?php echo ( !$logo_object ? 'display:none;' : '' ); ?> width:<?php echo esc_attr( $logo_width ); ?>px">
		<p class="description" <?php echo ( $logo_object ? 'style="display:none;"' : '' ); ?>>-</p>

		<?php
	}

	/**
	 * Render the description area for the favicon section.
	 */
	public function render_favicon_section_header_text() {
		?><p>
			We recommend that you use a recolored version of the main website favicon (e.g. your logo in red) to make it easy to differentiate between front-end and admin tabs.
		</p>
		<p>
			If a favicon is not added here, a default favicon will be used.
		</p><?php
	}

	/**
	 * Render the file upload for the favicon.
	 */
	public function render_favicon_setting() {
		if ( 0 === $this->options->favicon_id ) {
			$favicon_id = '';
		} else {
			$favicon_id = $this->options->favicon_id;
		}

		?>

		<input type="hidden" id="brandwpadmin_favicon_id" name="brandwpadmin_plugin_options[favicon_id]" value="<?php echo esc_attr( $favicon_id ); ?>" />
	    <input id="brandwpadmin_upload_favicon_button" type="button" class="button js-brandwpadmin-open-upload-frame" value="<?php _e( 'Choose Image', 'brandwpadmin' ); ?>" data-mime-type="image/x-icon" />
	    <p class="description">Upload a .ico file, not a .jpg or .png</p>

		<?php
	}

	/**
	 * Render the favicon preview.
	 */
	public function render_favicon_preview_setting() {
		if ( !$this->options->favicon_id ) {
			$favicon_src = bwpa_get_default_favicon_uri();
		} else {
			$favicon_object = wp_get_attachment_image_src( $this->options->favicon_id, 'full' );

			if ( $favicon_object ) {
				$favicon_src = $favicon_object[0];
			} else {
				$favicon_src = bwpa_get_default_favicon_uri();
			}
		}

		?>

		<img class="" id="brandwpadmin_upload_favicon_preview" src="<?php echo $favicon_src; ?>" alt="Current Favicon" width="16" height="16">
		<p class="description">Preview is only possible for browsers that support the .ico extension for images.</p>

		<?php
	}

	/**
	 * Validate the settings before they are stored in the database.
	 * Reject any that do not match and display an error.
	 * @param  array $input The submitted fields.
	 * @return array        The filtered data containing only passing values.
	 */
	public function validate_settings( $input ) {
		$valid_input = $this->options->load();

		// Load the current values into valid input by default
		// If a value isn't set, load it's default
		$valid_input = array_merge( $this->options->get_defaults(), $valid_input );

		$was_submitted = !empty($input['submit']) ? true : false;

		// Check for a submitted form
		if ( $was_submitted ) {

			/**
			 * Validate the logo upload field.
			 */
			$_logoid = $input['logo_id'];

			if ( $_logoid ) {
				
				// Attempt to get image object
				$_logo = wp_get_attachment_image_src( $_logoid, 'full' );

				// Validate: Numeric ID and image object exists
				if ( is_numeric( $_logoid ) && $_logo ) {
					
					$valid_input['logo_id'] = $input['logo_id'];

				} else {
					
					add_settings_error( 'brandwpadmin_plugin_options', 'logo-not-exists', "Could not find an image with ID '$_logoid'", 'error' );

				}

			}


			/**
			 * Validate the logo width field.
			 */
			$width = $input['logo_width'];

			if ( $width ) {
				
				if ( !is_numeric( $width ) ) {
					
					add_settings_error( 'brandwpadmin_plugin_options', 'width-not-numeric', "The logo width must be numeric", 'error' );
				
				} elseif ( $width < 1 || $width > 320 ) {
					
					add_settings_error( 'brandwpadmin_plugin_options', 'width-out-bounds', "The logo width must be between 1px and 320px", 'error' );
				
				} else {
					
					$valid_input['logo_width'] = $input['logo_width'];
				
				}

			}


			/**
			 * Validate the favicon upload field.
			 */
			$_faviconid = $input['favicon_id'];

			if ( $_faviconid ) {
				
				// Attempt to get image object
				$_favicon = wp_get_attachment_image_src( $_faviconid, 'full' );

				// Validate: Numeric ID and image object exists
				if ( is_numeric( $_faviconid ) && $_favicon ) {
					
					$valid_input['favicon_id'] = $input['favicon_id'];

				} else {
					
					add_settings_error( 'brandwpadmin_plugin_options', 'favicon-not-exists', "Could not find an image with ID '$_faviconid'", 'error' );

				}

			}


		}

		return $valid_input;
	}
}

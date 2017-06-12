<?php

/**
 * Plugin Name:       BOND WP Cookie Consent
 * Plugin URI:        xxx
 * Version:           0.1.0
 * Author:            BOND Developers <dev@bond.fi>
 * Author URI:        https://bond-agency.com
 */

if( ! defined( 'WPINC') ) {
  die;
}

if( ! class_exists( 'BOND_Cookie_Consent' ) ) :

class BOND_Cookie_Consent {

  /**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected static $version;

  /**
	 * Static instance of the plugin.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @var      string    $instance    Static instance of the plugin.
	 */
  public static $instance;

  /**
	 * Minimum required WordPress version.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @var      array    $min_wp_version    Version number.
	 */
	public static $min_wp_version;

  /**
	 * Minimum required PHP version.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @var      array    $min_php_version    Version number.
	 */
	public static $min_php_version;

  /**
	 * Class dependencies of the plugin.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @var      array    $class_dependencies    Array of class names.
	 */
	public static $class_dependencies;

  /**
	 * PHP extensions required by the plugin.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @var      array    $required_php_extensions    Array of PHP extensions names.
	 */
	public static $required_php_extensions;

  public static function init() {
    if( is_null( self::$instance ) ) {
      self::$instance = new BOND_Cookie_Consent();
    }
    return self::$instance;
  }

  /**
   * Plugin constructor.
   *
   * @since    0.1.0
   */
  function __construct() {

    self::$version = '0.1.0';
    self::$min_wp_version = '4.7';
    self::$min_php_version = '7.0';
    self::$class_dependencies = array();
    self::$required_php_extensions = array('ftp');

    // Define constants.
    define( 'BOND_CC_SLUG', 'bond-wp-cookie-consent' );
    define( 'BOND_CC_TEXTDOMAIN', 'bond-wp-cookie-consent' );
    define( 'BOND_CC_OPTIONS', 'bond-wp-cc-options' );

    // Attach hooks.
    register_activation_hook( __FILE__ , array( $this, 'activate' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts') );
    add_action( 'admin_menu', array( $this, 'create_admin_page') );
    add_action( 'admin_init', array( $this, 'init_settings' ) );
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'settings_link' ) );
  }

  /**
   * Init scripts.
   *
   * @since    0.1.0
   */
  function add_scripts() {
    wp_register_script(
      BOND_CC_SLUG,
      plugin_dir_url( __FILE__ ) . 'bond-wp-cookie-consent.js',
      array(),
      self::$version,
      true
    );

    $data = self::get_options();

    wp_localize_script( BOND_CC_SLUG, 'bond_cc_data', $data );
    wp_enqueue_script( BOND_CC_SLUG );
  }

  /**
   * Init admin scripts.
   *
   * @since    0.1.0
   */
  function add_admin_scripts() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( BOND_CC_SLUG, plugins_url( 'iris-init.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
  }

  /**
   * Get options data.
   *
   * @since    0.1.0
   * @param    string   $language   Get options for specific language.
   * @return   array    $data       The array of the plugin settings.
   */
  function get_options( $language = null ) {
    $options = get_option( BOND_CC_OPTIONS );

    if( ! $language ) {
      $language = self::get_language();
    }

    $data = array(
      'info_text' => empty($options[$language]['info_text']) ? null : $options[$language]['info_text'],
      'dismiss_text' => empty($options[$language]['dismiss_text']) ? null : $options[$language]['dismiss_text'],
      'link_text' => empty($options[$language]['link_text']) ? null : $options[$language]['link_text'],
      'link_href' => empty($options[$language]['link_href']) ? null : $options[$language]['link_href'],
      'position' => empty($options['position']) ? 'bottom' : $options['position'],
      'bg_color' => empty($options['bg_color']) ? '#ccc' : $options['bg_color'],
      'info_text_color' => empty($options['info_text_color']) ? '#ccc' : $options['info_text_color'],
      'link_text_color' => empty($options['link_text_color']) ? '#ccc' : $options['link_text_color'],
      'button_bg_color' => empty($options['button_bg_color']) ? '#ccc' : $options['button_bg_color'],
      'button_text_color' => empty($options['button_text_color']) ? '#ccc' : $options['button_text_color'],
      'language' => $language
    );

    switch ($data['language']) {
      case 'fi':
        if ( empty($data['info_text']) ) $data['info_text'] = "Evästeet auttavat meitä palvelujemme toimituksessa. Käyttämällä palvelujamme hyväksyt evästeiden käytön.";
        if ( empty($data['dismiss_text']) ) $data['dismiss_text'] = "Selvä";
        if ( empty($data['link_text']) ) $data['link_text'] = "Lisätietoja";
        break;
      default:
        if ( empty($data['info_text']) ) $data['info_text'] = "Cookies help us deliver our services. By using our services, you agree to our use of cookies.";
        if ( empty($data['dismiss_text']) ) $data['dismiss_text'] = "Got it";
        if ( empty($data['link_text']) ) $data['link_text'] = "Learn more";
        break;
    }

    return $data;
  }

  /**
   * Add settings link on plugin page.
   *
   * @since    0.1.0
   * @param    array    $links     List of links to display on the plugins page.
   * @return   array    $links     Same list with our settings link appended to the end.
   */
  function settings_link( $links ) {
    $links[] = '<a href="options-general.php?page=' . BOND_CC_SLUG . '">Settings</a>';
    return $links;
  }

  /**
   * Options page.
   *
   * @since    0.1.0
   */
  function create_admin_page() {
    add_options_page(
      'Cookie Consent Settings',
      'Cookie Consent',
      'manage_options',
      BOND_CC_SLUG,
      array( $this, 'options_page_content' )
    );
  }

  /**
   * Display contents of the options page.
   *
   * @since    0.1.0
   */
  function options_page_content() {

?>
<div class="wrap">
  <h2>Cookie Consent - Settings</h2>
  <form action="options.php" method="post">
    <?php settings_fields( BOND_CC_OPTIONS ); ?>
    <?php do_settings_sections( BOND_CC_SLUG ); ?>
    <input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
  </form>
</div>
<?php

  }

  /**
   * Add settings for the plugin.
   *
   * @since    0.1.0
   */
  function init_settings() {
    // Register settings.
    register_setting( BOND_CC_OPTIONS, BOND_CC_OPTIONS );

    // Add section for general settings.
    add_settings_section(
      BOND_CC_SLUG . '_general',
      esc_html__( 'General settings', BOND_CC_TEXTDOMAIN ),
      '',
      BOND_CC_SLUG
    );

    // Add field for banner position.
    add_settings_field(
      BOND_CC_OPTIONS . '_position',
      esc_html__( 'Position', BOND_CC_TEXTDOMAIN ),
      array( $this, 'create_radio_field' ),
      BOND_CC_SLUG,
      BOND_CC_SLUG . '_general',
      array(
        'name' => 'position',
        'description' => esc_html__( 'Choose the position for the banner', BOND_CC_TEXTDOMAIN ),
        'fields' => array( 'top', 'bottom' )
      )
    );

    // Color picker for background color.
    add_settings_field(
      BOND_CC_OPTIONS . '_bg_color',
      esc_html__( 'Background color', BOND_CC_TEXTDOMAIN ),
      array( $this, 'create_color_picker_field' ),
      BOND_CC_SLUG,
      BOND_CC_SLUG . '_general',
      array(
        'name' => 'bg_color',
        'description' => esc_html__( 'Choose background color for the banner', BOND_CC_TEXTDOMAIN )
      )
    );

    // Color picker for the info text color.
    add_settings_field(
      BOND_CC_OPTIONS . '_info_text_color',
      esc_html__( 'Text color', BOND_CC_TEXTDOMAIN ),
      array( $this, 'create_color_picker_field' ),
      BOND_CC_SLUG,
      BOND_CC_SLUG . '_general',
      array(
        'name' => 'info_text_color',
        'description' => esc_html__( 'Choose the info text color', BOND_CC_TEXTDOMAIN )
      )
    );

    // Color picker for link text color.
    add_settings_field(
      BOND_CC_OPTIONS . '_link_text_color',
      esc_html__( 'Link color', BOND_CC_TEXTDOMAIN ),
      array( $this, 'create_color_picker_field' ),
      BOND_CC_SLUG,
      BOND_CC_SLUG . '_general',
      array(
        'name' => 'link_text_color',
        'description' => esc_html__( 'Choose text color for the link', BOND_CC_TEXTDOMAIN )
      )
    );

    // Color picker for button background.
    add_settings_field(
      BOND_CC_OPTIONS . '_button_bg_color',
      esc_html__( 'Button background', BOND_CC_TEXTDOMAIN ),
      array( $this, 'create_color_picker_field' ),
      BOND_CC_SLUG,
      BOND_CC_SLUG . '_general',
      array(
        'name' => 'button_bg_color',
        'description' => esc_html__( 'Choose the background color for the button', BOND_CC_TEXTDOMAIN )
      )
    );

    // Color picker for button text.
    add_settings_field(
      BOND_CC_OPTIONS . '_button_text_color',
      esc_html__( 'Button text', BOND_CC_TEXTDOMAIN ),
      array( $this, 'create_color_picker_field' ),
      BOND_CC_SLUG,
      BOND_CC_SLUG . '_general',
      array(
        'name' => 'button_text_color',
        'description' => esc_html__( 'Choose the text color for the button', BOND_CC_TEXTDOMAIN )
      )
    );

    // Create language specific settings.
    $languages = self::get_languages();
    foreach( $languages as $language ) {

      $section_key = BOND_CC_SLUG . '_' . $language;

      // Add settings section for the language.
      add_settings_section(
        $section_key,
        esc_html__( 'Settings: ' . $language, BOND_CC_TEXTDOMAIN ),
        '',
        BOND_CC_SLUG
      );

      // Field for info text
      add_settings_field(
        BOND_CC_OPTIONS . '_info_text',
        esc_html__( 'Info text', BOND_CC_TEXTDOMAIN ),
        array( $this, 'create_textarea_field' ),
        BOND_CC_SLUG,
        $section_key,
        array(
          'name'  => 'info_text',
          'description' => '',
          'language'  => $language
        )
      );

      // Field for dismiss button text
      add_settings_field(
        BOND_CC_OPTIONS . '_dismiss_text',
        esc_html__( 'Dismiss text', BOND_CC_TEXTDOMAIN ),
        array( $this, 'create_input_field' ),
        BOND_CC_SLUG,
        $section_key,
        array(
          'name' => 'dismiss_text',
          'description' => '',
          'language' => $language
        )
      );

      // Field for link text
      add_settings_field(
        BOND_CC_OPTIONS . '_link_text',
        esc_html__( 'Cookie policy link text', BOND_CC_TEXTDOMAIN ),
        array( $this, 'create_input_field' ),
        BOND_CC_SLUG,
        $section_key,
        array(
          'name' => 'link_text',
          'description' => '',
          'language' => $language
        )
      );

      // Field for link href
      add_settings_field(
        BOND_CC_OPTIONS . '_link_href',
        esc_html__( 'Cookie policy page url', BOND_CC_TEXTDOMAIN ),
        array( $this, 'create_input_field' ),
        BOND_CC_SLUG,
        $section_key,
        array(
          'name' => 'link_href',
          'description' => '',
          'language' => $language
        )
      );
    }
  }

  /**
   * Color picker field.
   *
   * @since    0.1.0
   * @param    array    $args     arguments for creating the field.
   */
  function create_color_picker_field( $args ) {
    $options = self::get_options();
    $name = $args['name'];
    $value = $options[$name];
    echo "<input type='text' class='bond-cc-color-picker' id='" . BOND_CC_OPTIONS . "_$name' name='" . BOND_CC_OPTIONS ."[$name]' value='$value' />";
    if( ! empty( $args['description'] ) ) {
      echo "<p class='description'>" . $args['description'] . "</p>";
    }
  }

  /**
   * Radio field.
   *
   * @since    0.1.0
   * @param    array    $args     arguments for creating the field.
   */
  function create_radio_field( $args ) {
    $options = self::get_options();
    $fields = $args['fields'];
    $name = $args['name'];
    if( ! empty( $fields ) ) {
      echo "<fieldset>";
      foreach( $fields as $field ) {
        echo "<input type='radio' id='" . BOND_CC_OPTIONS . "_$field' name='" . BOND_CC_OPTIONS ."[$name]' value='$field'" . ($field == $options[$name] ? 'checked' : '').">";
        echo "<label for='tuo_rad_" . $field . "'>" . $field . "</label><br />";
      }
      if( ! empty( $args['description'] ) ) {
        echo "<p class='description'>" . $args['description'] . "</p>";
      }
      echo "</fieldset>";
    }
  }

  /**
   * Textarea field.
   *
   * @since    0.1.0
   * @param    array    $args     arguments for creating the field.
   */
  function create_textarea_field( $args ) {
    $language = $args['language'];
    $options = self::get_options( $language );
    $name = $args['name'];
    $value = esc_attr($options[$args['name']]);
    echo "<textarea id='" . BOND_CC_OPTIONS . "_$name' name='" . BOND_CC_OPTIONS ."[$language][$name]' cols='40' rows='5'>";
    echo $value;
    echo "</textarea>";
    if( ! empty( $args['description'] ) ) {
      echo "<p class='description'>" . $args['description'] . "</p>";
    }
  }

  /**
   * Text input field.
   *
   * @since    0.1.0
   * @param    array    $args     arguments for creating the field.
   */
  function create_input_field( $args ) {
    $language = $args['language'];
    $options = self::get_options( $language );
    $name = $args['name'];
    $value = esc_attr($options[$args['name']]);
    echo "<input type='text' id='" . BOND_CC_OPTIONS . "_$name' name='" . BOND_CC_OPTIONS ."[$language][$name]' value='$value' />";
    if( ! empty( $args['description'] ) ) {
      echo "<p class='description'>" . $args['description'] . "</p>";
    }
  }

  /**
   * Get the current language.
   *
   * @since    0.1.0
   * @return   string   $language     Returns the current language.
   */
  function get_language() {
    $language = null;
		if( function_exists( 'pll_current_language' ) ) {
		  //get language from polylang plugin https://wordpress.org/plugins/polylang/
			$language = pll_current_language();
    } elseif( defined( 'ICL_LANGUAGE_CODE' ) ) {
		  //get language from wpml plugin https://wpml.org
			$language = ICL_LANGUAGE_CODE;
    } else {
		  //return wp get_locale() - first 2 chars (en, fi, ...)
			$language = substr(get_locale(),0,2);
    }
		return $language;
  }

  /**
   * Get all available languages.
   *
   * @since    0.1.0
   * @return   array    $languages    List of language codes available on the site.
   */
  function get_languages() {
    $languages = array();
    global $polylang;
    if( function_exists( 'PPL' ) ) {
      // for polylang versions > 1.8
			$pl_languages = PLL()->model->get_languages_list();
      foreach ( $pl_languages as $pl_language ) {
				$languages[] = $pl_language->slug;
			}
    } else if ( function_exists( 'icl_get_languages' ) ) {
      $wpml_languages = icl_get_languages('skip_missing=0');
      foreach( $wpml_languages as $wpml_language ) {
        $languages[] = $wpml_language['language_code'];
      }
    } else {
      $languages[] = self::get_language();
    }
    return $languages;
  }

  /**
	 * Checks if plugin dependencies & requirements are met.
	 *
	 * @since    0.1.0
   * @return   bool     Whether the plugins requirements are fulfilled or not?
	 */
  function are_requirements_met() {
    // Check for WordPress version
    if ( version_compare( get_bloginfo('version'), self::$min_wp_version, '<' ) ) {
      return false;
    }
    // Check the PHP version
    if ( version_compare( PHP_VERSION, self::$min_php_version, '<' ) ) {
      return false;
    }
    // Check PHP loaded extensions
    foreach ( self::$required_php_extensions as $ext ) {
      if ( ! extension_loaded( $ext ) ) {
        return false;
      }
    }
    // Check for required classes
    foreach ( self::$class_dependencies as $class_name ) {
      if ( ! class_exists( $class_name ) ) {
        return false;
      }
    }
    return true;
  }

  /**
	 * Checks if plugin dependencies & requirements are met before allows activation.
   * After dependencies & requirements are ok it attaches the plugin hooks that
   * include the actual plugin functionality.
	 *
	 * @since    0.1.0
	 */
	function activate() {
    if (!$this->are_requirements_met()) {
      deactivate_plugins( plugin_basename( __FILE__ ) );
      wp_die( "<p>Some of the plugin dependencies aren't met and the plugin can't be enabled. This plugin requires the followind dependencies:</p><ul><li>Minimum WP version: ".self::$min_wp_version."</li><li>Minimum PHP version: ".self::$min_php_version."</li><li>Classes / plugins: ".implode (", ", self::$class_dependencies)."</li><li>PHP extensions: ".implode (", ", self::$required_php_extensions)."</li></ul>" );
    } else {
      // Normal plugin functionality hooks here.
    }
	}
}

endif;

BOND_Cookie_Consent::init();
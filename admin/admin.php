<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://qedev.com
 * @since      1.0.0
 *
 * @package    TKChecker
 * @subpackage TKChecker/includes
 */

/**
 * The admin dashboard-specific functionality of the plugin.
 *
 *
 * @package    TKChecker
 * @subpackage TKChecker/admin
 * @author     Mark Hurst Deutsch <admin@qedev.com>
 */
class TKCheckerAdmin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name    The ID of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/* =================================== LOADERS =================================== */

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version ) {

		$this->name = $name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->name, 'TK_SETTINGS', get_option('tk_settings'));
		wp_localize_script($this->name, 'TK_I18N', array(
			'confirmPublishPrompt' => __("There are still facts to check in your post. Are you sure you want to publish?", $this->name)
		));

	}

	/* ================================= META BOXES ================================= */

	/**
	 * Render the metabox.
	 *
	 * @since    1.0.0
	 * @var      array    $post    		Post we're creating the MB for.
	 */
	public function render_meta_box($post) {
		wp_nonce_field('tk_mb', 'tk_mb_nonce');
	}

	/**
	 * Called upon loading the meta boxes when creating the post editor.
	 *
	 * @since    1.0.0
	 * @var      string    $post_type    Post type.
	 */
	public function add_meta_boxes($post_type) {
		$settings = get_option('tk_settings');
		$wildcard = $settings['wildcard'];
		add_meta_box(
			'tk_mb',
			__("TK Checker - '{$wildcard}' Summary", $this->name),
			array($this, 'render_meta_box'),
			'post'
		);
	}

	/**
	 * Function called on post save.
	 *
	 * @since    1.0.0
	 * @var      int       $post_id       ID of the post.
	 */
	public function save_meta_boxes($post_id) {
		// Verify the nonce to ensure we're getting called from the right spot.
		if (!isset($_POST['tk_mb_nonce'])) {
			return $post_id;
		}
		$nonce = $_POST['tk_mb_nonce'];
		if (!wp_verify_nonce($nonce, 'tk_mb')) {
			return $post_id;
		}


		return $post_id;
	}

	/* ================================== SETTINGS ================================== */

	/**
	 * Create a link to the settings page on the plugins page.
	 *
	 * @since    1.0.0
	 * @var      array    $links 		Links to display for the plugin.
	 */
	public function settings_link($links) {
		$link = "<a href='" . get_admin_url(null, "admin.php?page={$this->name}/admin/views/settings.php'>Settings</a>");
		array_unshift($links, $link);
		return $links;
	}

	/**
	 * Create the menu option to view the settings page.
	 *
	 * @since    1.0.0
	 */
	public function add_menus() {
		add_options_page(
			__('TK Checker Settings', $this->name),
			__('TK Checker Settings', $this->name),
			'manage_options',
			"{$this->name}/admin/views/settings.php",
			'',
			'dashicons-admin-plugins'
		);
	}

	/**
	 * Add a setting to a specified section. Automatically prepends identifiers.
	 *
	 * @since    1.0.0
	 * @access 	 private
	 * @var      string    $id               The ID of the setting.
	 * @var      string    $label            The label of the setting.
	 * @var      string    $section          The section to add it to.
	 * @var      string    $desc (opt)       The description to print out.
	 * @var      string    $options (opt)    The options available to a select dropdown.
	 */
	private function add_setting($id, $label, $type, $section, $desc, $options) {
		add_settings_field(
			'tk_settings_' . $id,
			$label,
			array($this, 'render_settings_' . $type),
			"{$this->name}/admin/views/settings.php",
			'tk_settings_' . $section,
			array(
				'id' => $id,
				'desc' => $desc,
				'options' => $options
			)
		);
	}

	/**
	 * Add a setting to a specified section. Automatically prepends identifiers.
	 *
	 * @since    1.0.0
	 * @access 	 private
	 * @var      string    $id         The ID of the section.
	 * @var      string    $label      The label of the section.
	 * @var      string    $desc       Description to show in the section header.
	 * @var      array     $settings   An array of settings to create.
	 */
	private function add_section($id, $label, $desc, $settings) {
		add_settings_section(
			'tk_settings_' . $id,
			$label,
			array($this, 'render_settings_section_header'),
			"{$this->name}/admin/views/settings.php",
			array('desc' => $desc)
		);

		foreach ($settings as $setting) {
			$this->add_setting(
				$setting['id'],
				$setting['label'],
				$setting['type'],
				$id,
				isset($setting['desc']) ? $setting['desc'] : null,
				isset($setting['options']) ? $setting['options'] : null
			);
		}
	}

	/**
	 * Add a setting to a specified section. Automatically prepends identifiers.
	 *
	 * @since    1.0.0
	 * @var      string    $id         The ID of the section.
	 * @var      string    $label      The label of the section.
	 * @var 	 array     $settings   An array of settings to create.
	 */
	public function render_settings_section_header($args) {
		$desc = isset($args['desc']) ? $args['desc'] : '';
		echo $desc;
	}

	/**
	 * Create a settings textbox and sets its state
	 *
	 * @since    1.0.0
	 * @var      array    $args 		Args passed to the callback
	 */
 	public function render_settings_text($args) {
		$id = $args['id'];
		$desc = isset($args['desc']) ? $args['desc'] : '';
		$options = get_option('tk_settings');
		$val = $options[$id];
		echo "<input name='tk_settings[{$id}]' type='text' class='tk-settings-text' value='{$val}'/>";
		echo "<p>{$desc}</p>";
	}

	/**
	 * Validates the settings that have been entered
	 *
	 * @since    1.0.0
	 * @var      array    $input 		Args posted with the settings form.
	 */
	public function validate_settings($input) {
		$valid = array();
		$type = 'updated';
		$msg = __('Settings successfully saved.', $this->name);

		// General settings
		if (isset($input['wildcard'])) {
			$valid['wildcard'] = $input['wildcard'];
		}
		if (isset($input['excerpt_length'])) {
			$valid['excerpt_length'] = intval($input['excerpt_length']);
		}
		if (isset($input['no_tks_text'])) {
			$valid['no_tks_text'] = $input['no_tks_text'];
		}
		add_settings_error('tk_settings', 'tk_settings', $msg, $type);
		return $valid;
	}

	/**
	 * Register the settings available for configuration
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting(
			'tk_settings',
			'tk_settings',
			array($this, 'validate_settings')
		);
		// General Settings
		$this->add_section(
			'general',
			__('General Settings', $this->name),
			'',
			array(
				array(
					'id' => 'wildcard',
					'label' => __('Wildcard', $this->name),
					'desc' => __('The text string that you use to mark locations in your posts where info is still to come.', $this->name),
					'type' => 'text'
				), array(
					'id' => 'excerpt_length',
					'label' => __('Excerpt Length', $this->name),
					'desc' => __('The number of characters to show before and after any matched wildcard.', $this->name),
					'type' => 'text'
				), array(
					'id' => 'no_tks_text',
					'label' => __('"No Wildcards Found" Text', $this->name),
					'desc' => __('Customise the text shown when no wildcards are found. Give yourself a hand!', $this->name),
					'type' => 'text'
				)
			)
		);
	}
}

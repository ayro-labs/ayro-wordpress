<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueue the admin-specific stylesheet and JavaScript.
 */
class AyroAdmin {

  const CHATBOX_HEADER_TITLE_DEFAULT = 'Como podemos ajudá-lo?';
  const CHATBOX_INPUT_PLACEHOLDER_DEFAULT = 'Digite uma mensagem...';

  /**
   * The ID of this plugin.
   */
  private $pluginName;

  /**
   * The version of this plugin.
   */
  private $pluginVersion;

  /**
   * Admin defined settings for the plugin.
   */
  private $settings;

  /**
   * Initializes the class and set its properties.
   */
  public function __construct($pluginName, $pluginVersion) {
    $this->pluginName = $pluginName;
    $this->pluginVersion = $pluginVersion;
    $this->settings = get_option('ayro_settings');
  }

  /**
   * Registers the stylesheets for the admin area.
   */
  public function enqueueStyles() {

  }

  /**
   * Registers the JavaScript for the admin area.
   */
  public function enqueueScripts() {

  }

  /**
   * Creates the settings menu and page for the admin area.
   */
  public function createSettingsPage() {
    add_options_page(
      'Ayro Settings',
      'Ayro',
      'manage_options',
      'ayro',
      array($this, 'createSettingsPageCallback')
    );
  }

  /**
   * Renders the settings page
   */
  public function createSettingsPageCallback() {
    ?>
      <div class="wrap">
        <h2>
          <?php echo esc_html(get_admin_page_title()); ?>
        </h2>
        <p></p>
        <form method="post" action="options.php">
          <?php
            settings_fields('ayro_settings');
            do_settings_sections('ayro_settings');
            submit_button();
          ?>
        </form>
      </div>
    <?php
  }

  public function initSettingsPage() {
    register_setting(
      'ayro_settings',
      'ayro_settings',
      array($this, 'sanitizeSettings')
    );

    add_settings_section(
      'ayro_section_general_settings',
      'General Settings',
      null,
      'ayro_settings'
    );

    add_settings_section(
      'ayro_section_chatbox_translation',
      'Chatbox Translation',
      null,
      'ayro_settings'
    );

    add_settings_field(
      'app_token',
      'App Token',
      array($this, 'addAppTokenFieldCallback'),
      'ayro_settings',
      'ayro_section_general_settings'
    );

    add_settings_field(
      'chatbox_header_title',
      'Header title',
      array($this, 'addHeaderTitleFieldCallback'),
      'ayro_settings',
      'ayro_section_chatbox_translation'
    );

    add_settings_field(
      'chatbox_input_placeholder',
      'Input placeholder',
      array($this, 'addInputPlaceholderFieldCallback'),
      'ayro_settings',
      'ayro_section_chatbox_translation'
    );
  }

  public function addAppTokenFieldCallback() {
    printf(
      '<input class="regular-text" type="text" name="ayro_settings[app_token]" id="app_token" value="%s">',
      isset($this->settings['app_token']) ? esc_attr($this->settings['app_token']) : ''
    );
  }

  public function addHeaderTitleFieldCallback() {
    printf(
      '<input class="regular-text" type="text" name="ayro_settings[chatbox_header_title]" id="chatbox_header_title" value="%s">',
      isset($this->settings['chatbox_header_title']) ? esc_attr($this->settings['chatbox_header_title']) : AyroAdmin::CHATBOX_HEADER_TITLE_DEFAULT
    );
  }

  public function addInputPlaceholderFieldCallback() {
    printf(
      '<input class="regular-text" type="text" name="ayro_settings[chatbox_input_placeholder]" id="chatbox_input_placeholder" value="%s">',
      isset($this->settings['chatbox_input_placeholder']) ? esc_attr($this->settings['chatbox_input_placeholder']) : AyroAdmin::CHATBOX_INPUT_PLACEHOLDER_DEFAULT
    );
  }

  public function sanitizeSettings($input) {
    $values = array();
    if (isset($input['app_token'])) {
      $values['app_token'] = sanitize_text_field($input['app_token']);
    }
    if (isset($input['chatbox_header_title'])) {
      $values['chatbox_header_title'] = sanitize_text_field($input['chatbox_header_title']);
    }
    if (isset($input['chatbox_input_placeholder'])) {
      $values['chatbox_input_placeholder'] = sanitize_text_field($input['chatbox_input_placeholder']);
    }
    return $values;
  }
}

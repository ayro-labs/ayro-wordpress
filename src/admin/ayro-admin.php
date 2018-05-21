<?php

/**
 * The admin-specific functionality of the plugin.
 */
class AyroAdmin {

  const DEFAULT_CHATBOX_HEADER_TITLE = 'Como podemos ajudá-lo?';
  const DEFAULT_CHATBOX_INPUT_PLACEHOLDER = 'Digite uma mensagem...';
  const DEFAULT_CHATBOX_CONNECT_CHANNELS_MESSAGE = 'Conecte seus aplicativos e seja notificado dentro deles quando for respondido.';

  const DEFAULT_CONNECT_EMAIL_DESCRIPTION = 'Conecte seu email e seja notificado quando receber uma resposta.';
  const DEFAULT_CONNECT_EMAIL_INPUT_PLACEHOLDER = 'Digite seu email...';
  const DEFAULT_CONNECT_EMAIL_SEND_BUTTON = 'Enviar';
  const DEFAULT_CONNECT_EMAIL_SUCCESS_MESSAGE = 'Email conectado com sucesso!';

  /**
   * Admin defined settings for the plugin.
   */
  private $settings;

  /**
   * Initializes the class and set its properties.
   */
  public function __construct() {
    $this->settings = get_option('ayro_settings');
    if (!isset($this->settings['sounds'])) {
      $this->settings['sounds'] = '1';
    }
    if (!isset($this->settings['chatbox_header_title'])) {
      $this->settings['chatbox_header_title'] = AyroAdmin::DEFAULT_CHATBOX_HEADER_TITLE;
    }
    if (!isset($this->settings['chatbox_input_placeholder'])) {
      $this->settings['chatbox_input_placeholder'] = AyroAdmin::DEFAULT_CHATBOX_INPUT_PLACEHOLDER;
    }
    if (!isset($this->settings['chatbox_connect_channels_message'])) {
      $this->settings['chatbox_connect_channels_message'] = AyroAdmin::DEFAULT_CHATBOX_CONNECT_CHANNELS_MESSAGE;
    }
    if (!isset($this->settings['connect_email_description'])) {
      $this->settings['connect_email_description'] = AyroAdmin::DEFAULT_CONNECT_EMAIL_DESCRIPTION;
    }
    if (!isset($this->settings['connect_email_input_placeholder'])) {
      $this->settings['connect_email_input_placeholder'] = AyroAdmin::DEFAULT_CONNECT_EMAIL_INPUT_PLACEHOLDER;
    }
    if (!isset($this->settings['connect_email_send_button'])) {
      $this->settings['connect_email_send_button'] = AyroAdmin::DEFAULT_CONNECT_EMAIL_SEND_BUTTON;
    }
    if (!isset($this->settings['connect_email_success_message'])) {
      $this->settings['connect_email_success_message'] = AyroAdmin::DEFAULT_CONNECT_EMAIL_SUCCESS_MESSAGE;
    }
    update_option('ayro_settings', $this->settings);
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
      'ayro_section_chatbox',
      'Chatbox',
      null,
      'ayro_settings'
    );

    add_settings_section(
      'ayro_section_connect_email',
      'Connect Email',
      null,
      'ayro_settings'
    );

    add_settings_field(
      'app_token',
      'App Token',
      array($this, 'addGeneralSettingsAppTokenCallback'),
      'ayro_settings',
      'ayro_section_general_settings'
    );

    add_settings_field(
      'sounds',
      'Notification sound',
      array($this, 'addGeneralSettingsNotificationSoundCallback'),
      'ayro_settings',
      'ayro_section_general_settings'
    );

    add_settings_field(
      'chatbox_header_title',
      'Header title',
      array($this, 'addChatboxHeaderTitleCallback'),
      'ayro_settings',
      'ayro_section_chatbox'
    );

    add_settings_field(
      'chatbox_input_placeholder',
      'Input placeholder',
      array($this, 'addChatboxInputPlaceholderCallback'),
      'ayro_settings',
      'ayro_section_chatbox'
    );

    add_settings_field(
      'chatbox_connect_channels_message',
      'Connect channels message',
      array($this, 'addChatboxConnectChannelsMessageCallback'),
      'ayro_settings',
      'ayro_section_chatbox'
    );

    add_settings_field(
      'connect_email_description',
      'Description',
      array($this, 'addConnectEmailDescriptionCallback'),
      'ayro_settings',
      'ayro_section_connect_email'
    );

    add_settings_field(
      'connect_email_input_placeholder',
      'Input placeholder',
      array($this, 'addConnectEmailInputPlaceholderCallback'),
      'ayro_settings',
      'ayro_section_connect_email'
    );

    add_settings_field(
      'connect_email_send_button',
      'Send button',
      array($this, 'addConnectEmailSendButtonCallback'),
      'ayro_settings',
      'ayro_section_connect_email'
    );

    add_settings_field(
      'connect_email_success_message',
      'Success message',
      array($this, 'addConnectEmailSuccessMessageCallback'),
      'ayro_settings',
      'ayro_section_connect_email'
    );
  }

  public function addGeneralSettingsAppTokenCallback() {
    printf(
      '<input id="app_token" class="regular-text" type="text" name="ayro_settings[app_token]" value="%s">',
      isset($this->settings['app_token']) ? esc_attr($this->settings['app_token']) : ''
    );
  }

  public function addGeneralSettingsNotificationSoundCallback() {
    $checked = '';
    if (isset($this->settings['sounds'])) {
      $checked = 'checked="checked"';
    }
    echo '<input '.$checked.' id="sounds" type="checkbox" name="ayro_settings[sounds]" value="1"/>';
  }

  public function addChatboxHeaderTitleCallback() {
    printf(
      '<input id="chatbox_header_title" class="regular-text" type="text" name="ayro_settings[chatbox_header_title]" value="%s">',
      esc_attr($this->settings['chatbox_header_title'])
    );
  }

  public function addChatboxInputPlaceholderCallback() {
    printf(
      '<input id="chatbox_input_placeholder" class="regular-text" type="text" name="ayro_settings[chatbox_input_placeholder]" value="%s">',
      esc_attr($this->settings['chatbox_input_placeholder'])
    );
  }

  public function addChatboxConnectChannelsMessageCallback() {
    printf(
      '<input id="chatbox_connect_channels_message" class="large-text" type="text" name="ayro_settings[chatbox_connect_channels_message]" value="%s">',
      esc_attr($this->settings['chatbox_connect_channels_message'])
    );
  }

  public function addConnectEmailDescriptionCallback() {
    printf(
      '<input id="connect_email_description" class="large-text" type="text" name="ayro_settings[connect_email_description]" value="%s">',
      esc_attr($this->settings['connect_email_description'])
    );
  }

  public function addConnectEmailInputPlaceholderCallback() {
    printf(
      '<input id="connect_email_input_placeholder" class="regular-text" type="text" name="ayro_settings[connect_email_input_placeholder]" value="%s">',
      esc_attr($this->settings['connect_email_input_placeholder'])
    );
  }

  public function addConnectEmailSendButtonCallback() {
    printf(
      '<input id="connect_email_send_button" class="regular-text" type="text" name="ayro_settings[connect_email_send_button]" value="%s">',
      esc_attr($this->settings['connect_email_send_button'])
    );
  }

  public function addConnectEmailSuccessMessageCallback() {
    printf(
      '<input id="connect_email_success_message" class="regular-text" type="text" name="ayro_settings[connect_email_success_message]" value="%s">',
      esc_attr($this->settings['connect_email_success_message'])
    );
  }

  public function sanitizeSettings($input) {
    $values = array();
    if (isset($input['app_token'])) {
      $values['app_token'] = sanitize_text_field($input['app_token']);
    }
    if (isset($input['sounds'])) {
      $values['sounds'] = sanitize_text_field($input['sounds']);
    }
    if (isset($input['chatbox_header_title'])) {
      $values['chatbox_header_title'] = sanitize_text_field($input['chatbox_header_title']);
    }
    if (isset($input['chatbox_input_placeholder'])) {
      $values['chatbox_input_placeholder'] = sanitize_text_field($input['chatbox_input_placeholder']);
    }
    if (isset($input['chatbox_connect_channels_message'])) {
      $values['chatbox_connect_channels_message'] = sanitize_text_field($input['chatbox_connect_channels_message']);
    }
    if (isset($input['connect_email_description'])) {
      $values['connect_email_description'] = sanitize_text_field($input['connect_email_description']);
    }
    if (isset($input['connect_email_input_placeholder'])) {
      $values['connect_email_input_placeholder'] = sanitize_text_field($input['connect_email_input_placeholder']);
    }
    if (isset($input['connect_email_send_button'])) {
      $values['connect_email_send_button'] = sanitize_text_field($input['connect_email_send_button']);
    }
    if (isset($input['connect_email_success_message'])) {
      $values['connect_email_success_message'] = sanitize_text_field($input['connect_email_success_message']);
    }
    return $values;
  }
}

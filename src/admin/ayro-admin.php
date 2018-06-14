<?php

/**
 * The admin-specific functionality of the plugin.
 */
class AyroAdmin {

  const CHATBOX_HEADER_TITLE = 'Como podemos ajudá-lo?';
  const CHATBOX_INPUT_PLACEHOLDER = 'Digite uma mensagem...';
  const CHATBOX_ERRORS_FILE_SIZE_LIMIT_EXCEEDED = 'Arquivo deve possuir no máximo 5 MB';

  const ASK_FOR_EMAIL = 'Deixe seu email:';
  const EMAIL_PROVIDED = 'Agora também podemos trocar mensagens por e-mail.';
  const EMAIL_INPUT_PLACEHOLDER = 'Email';
  const SEND_EMAIL_BUTTON = 'Enviar';
  const EDIT_EMAIL_BUTTON = 'Editar';

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
      $this->settings['chatbox_header_title'] = AyroAdmin::CHATBOX_HEADER_TITLE;
    }
    if (!isset($this->settings['chatbox_input_placeholder'])) {
      $this->settings['chatbox_input_placeholder'] = AyroAdmin::CHATBOX_INPUT_PLACEHOLDER;
    }
    if (!isset($this->settings['chatbox_errors_file_size_limit_exceeded'])) {
      $this->settings['chatbox_errors_file_size_limit_exceeded'] = AyroAdmin::CHATBOX_ERRORS_FILE_SIZE_LIMIT_EXCEEDED;
    }
    if (!isset($this->settings['connect_channels_message_ask_for_email'])) {
      $this->settings['connect_channels_message_ask_for_email'] = AyroAdmin::ASK_FOR_EMAIL;
    }
    if (!isset($this->settings['connect_channels_message_email_provided'])) {
      $this->settings['connect_channels_message_email_provided'] = AyroAdmin::EMAIL_PROVIDED;
    }
    if (!isset($this->settings['connect_channels_message_email_input_placeholder'])) {
      $this->settings['connect_channels_message_email_input_placeholder'] = AyroAdmin::EMAIL_INPUT_PLACEHOLDER;
    }
    if (!isset($this->settings['connect_channels_message_send_email_button'])) {
      $this->settings['connect_channels_message_send_email_button'] = AyroAdmin::SEND_EMAIL_BUTTON;
    }
    if (!isset($this->settings['connect_channels_message_edit_email_button'])) {
      $this->settings['connect_channels_message_edit_email_button'] = AyroAdmin::EDIT_EMAIL_BUTTON;
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
      'ayro_section_chatbox_errors',
      'Chatbox errors',
      null,
      'ayro_settings'
    );

    add_settings_section(
      'ayro_section_connect_channels_message',
      'Connect channels message',
      null,
      'ayro_settings'
    );

    add_settings_field(
      'app_token',
      'App Token',
      array($this, 'printAppTokenInputCallback'),
      'ayro_settings',
      'ayro_section_general_settings'
    );

    add_settings_field(
      'sounds',
      'Notification sound',
      array($this, 'printNotificationSoundInputCallback'),
      'ayro_settings',
      'ayro_section_general_settings'
    );

    add_settings_field(
      'chatbox_header_title',
      'Header title',
      array($this, 'printChatboxHeaderTitleInputCallback'),
      'ayro_settings',
      'ayro_section_chatbox'
    );

    add_settings_field(
      'chatbox_input_placeholder',
      'Input placeholder',
      array($this, 'printChatboxInputPlaceholderInputCallback'),
      'ayro_settings',
      'ayro_section_chatbox'
    );

    add_settings_field(
      'chatbox_errors_file_size_limit_exceeded',
      'File size limit exceeded',
      array($this, 'printChatboxErrorsFileSizeLimitExceededInputCallback'),
      'ayro_settings',
      'ayro_section_chatbox_errors'
    );

    add_settings_field(
      'connect_channels_message_ask_for_email',
      'Ask for email',
      array($this, 'printAskForEmailInputCallback'),
      'ayro_settings',
      'ayro_section_connect_channels_message'
    );

    add_settings_field(
      'connect_channels_message_email_provided',
      'Email provided',
      array($this, 'printEmailProvidedInputCallback'),
      'ayro_settings',
      'ayro_section_connect_channels_message'
    );

    add_settings_field(
      'connect_channels_message_email_input_placeholder',
      'Email input placeholder',
      array($this, 'printEmailInputPlaceholderInputCallback'),
      'ayro_settings',
      'ayro_section_connect_channels_message'
    );

    add_settings_field(
      'connect_channels_message_send_email_button',
      'Send email button',
      array($this, 'printSendEmailButtonInputCallback'),
      'ayro_settings',
      'ayro_section_connect_channels_message'
    );

    add_settings_field(
      'connect_channels_message_edit_email_button',
      'Edit email button',
      array($this, 'printEditEmailButtonInputCallback'),
      'ayro_settings',
      'ayro_section_connect_channels_message'
    );
  }

  public function printAppTokenInputCallback() {
    printf(
      '<input id="app_token" class="regular-text" type="text" name="ayro_settings[app_token]" value="%s">',
      isset($this->settings['app_token']) ? esc_attr($this->settings['app_token']) : ''
    );
  }

  public function printNotificationSoundInputCallback() {
    $checked = '';
    if (isset($this->settings['sounds'])) {
      $checked = 'checked="checked"';
    }
    echo '<input '.$checked.' id="sounds" type="checkbox" name="ayro_settings[sounds]" value="1"/>';
  }

  public function printChatboxHeaderTitleInputCallback() {
    printf(
      '<input id="chatbox_header_title" class="regular-text" type="text" name="ayro_settings[chatbox_header_title]" value="%s">',
      esc_attr($this->settings['chatbox_header_title'])
    );
  }

  public function printChatboxInputPlaceholderInputCallback() {
    printf(
      '<input id="chatbox_input_placeholder" class="regular-text" type="text" name="ayro_settings[chatbox_input_placeholder]" value="%s">',
      esc_attr($this->settings['chatbox_input_placeholder'])
    );
  }

  public function printChatboxErrorsFileSizeLimitExceededInputCallback() {
    printf(
      '<input id="chatbox_errors_file_size_limit_exceeded" class="regular-text" type="text" name="ayro_settings[chatbox_errors_file_size_limit_exceeded]" value="%s">',
      esc_attr($this->settings['chatbox_errors_file_size_limit_exceeded'])
    );
  }

  public function printAskForEmailInputCallback() {
    printf(
      '<input id="connect_channels_message_ask_for_email" class="large-text" type="text" name="ayro_settings[connect_channels_message_ask_for_email]" value="%s">',
      esc_attr($this->settings['connect_channels_message_ask_for_email'])
    );
  }

  public function printEmailProvidedInputCallback() {
    printf(
      '<input id="connect_channels_message_email_provided" class="large-text" type="text" name="ayro_settings[connect_channels_message_email_provided]" value="%s">',
      esc_attr($this->settings['connect_channels_message_email_provided'])
    );
  }

  public function printEmailInputPlaceholderInputCallback() {
    printf(
      '<input id="connect_channels_message_email_input_placeholder" class="regular-text" type="text" name="ayro_settings[connect_channels_message_email_input_placeholder]" value="%s">',
      esc_attr($this->settings['connect_channels_message_email_input_placeholder'])
    );
  }

  public function printSendEmailButtonInputCallback() {
    printf(
      '<input id="connect_channels_message_send_email_button" class="regular-text" type="text" name="ayro_settings[connect_channels_message_send_email_button]" value="%s">',
      esc_attr($this->settings['connect_channels_message_send_email_button'])
    );
  }

  public function printEditEmailButtonInputCallback() {
    printf(
      '<input id="connect_channels_message_edit_email_button" class="regular-text" type="text" name="ayro_settings[connect_channels_message_edit_email_button]" value="%s">',
      esc_attr($this->settings['connect_channels_message_edit_email_button'])
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
    if (isset($input['chatbox_errors_file_size_limit_exceeded'])) {
      $values['chatbox_errors_file_size_limit_exceeded'] = sanitize_text_field($input['chatbox_errors_file_size_limit_exceeded']);
    }
    if (isset($input['connect_channels_message_ask_for_email'])) {
      $values['connect_channels_message_ask_for_email'] = sanitize_text_field($input['connect_channels_message_ask_for_email']);
    }
    if (isset($input['connect_channels_message_email_provided'])) {
      $values['connect_channels_message_email_provided'] = sanitize_text_field($input['connect_channels_message_email_provided']);
    }
    if (isset($input['connect_channels_message_email_input_placeholder'])) {
      $values['connect_channels_message_email_input_placeholder'] = sanitize_text_field($input['connect_channels_message_email_input_placeholder']);
    }
    if (isset($input['connect_channels_message_send_email_button'])) {
      $values['connect_channels_message_send_email_button'] = sanitize_text_field($input['connect_channels_message_send_email_button']);
    }
    if (isset($input['connect_channels_message_edit_email_button'])) {
      $values['connect_channels_message_edit_email_button'] = sanitize_text_field($input['connect_channels_message_edit_email_button']);
    }
    return $values;
  }
}

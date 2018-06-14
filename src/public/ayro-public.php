<?php

/**
 * The public-specific functionality of the plugin.
 */
class AyroPublic {

  /**
   * The version of the javascript library.
   */
  private $libraryVersion;

  /**
   * Initialize the class and set its properties.
   */
  public function __construct($libraryVersion) {
    $this->libraryVersion = $libraryVersion;
  }

  /**
   * Register the stylesheets for the public area.
   */
  public function enqueueStyles() {

  }

  /**
   * Register the JavaScript for the public area.
   */
  public function enqueueScripts() {
    $libraryUrl = null;
    if (AYRO_ENV === 'development') {
      $libraryUrl = 'http://localhost:9000/dist/ayro.js';
    } else {
      $libraryUrl = 'https://cdn.ayro.io/sdks/ayro-' . $this->libraryVersion . '.min.js';
    }
    // Ayro widget script
    wp_register_script('ayro-script', $libraryUrl);
    wp_enqueue_script('ayro-script');

    // Ayro init script
    wp_register_script('ayro-script-init', plugins_url('js/ayro-init.js', __FILE__, array(), false, true));
    $settings = get_option('ayro_settings');
    $config = array(
      'appToken' => $settings['app_token'],
      'sounds' => $settings['sounds'],
      'chatboxHeaderTitle' => $settings['chatbox_header_title'],
      'chatboxInputPlaceholder' => $settings['chatbox_input_placeholder'],
      'chatboxErrorsFileSizeLimitExceeded' => $settings['chatbox_errors_file_size_limit_exceeded'],
      'askForEmail' => $settings['connect_channels_message_ask_for_email'],
      'emailProvided' => $settings['connect_channels_message_email_provided'],
      'emailInputPlaceholder' => $settings['connect_channels_message_email_input_placeholder'],
      'sendEmailButton' => $settings['connect_channels_message_send_email_button'],
      'editEmailButton' => $settings['connect_channels_message_edit_email_button'],
    );

    wp_localize_script('ayro-script-init', 'config', $config);
    wp_enqueue_script('ayro-script-init');
  }
}

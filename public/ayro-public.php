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
    // Ayro widget script
    wp_register_script('ayro-script', 'https://cdn.ayro.io/sdks/ayro-'.$this->libraryVersion.'.min.js');
    wp_enqueue_script('ayro-script');

    // Ayro init script
    wp_register_script('ayro-script-init', plugins_url('js/ayro-init.js', __FILE__, array(), false, true));
    $settings = get_option('ayro_settings');
    $config = array(
      'appToken' => $settings['app_token'],
      'sounds' => $settings['sounds'],
      'chatboxHeaderTitle' => $settings['chatbox_header_title'],
      'chatboxInputPlaceholder' => $settings['chatbox_input_placeholder'],
      'chatboxConnectChannelsMessage' => $settings['chatbox_connect_channels_message'],
    );
    wp_localize_script('ayro-script-init', 'config', $config);
    wp_enqueue_script('ayro-script-init');
  }
}

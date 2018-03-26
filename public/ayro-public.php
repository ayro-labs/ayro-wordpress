<?php

/**
 * The public-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueue the public-specific stylesheet and JavaScript.
 */
class AyroPublic {

  /**
   * The ID of this plugin.
   */
  private $pluginName;

  /**
   * The version of this plugin.
   */
  private $pluginVersion;

  /**
   * Initialize the class and set its properties.
   */
  public function __construct($pluginName, $pluginVersion) {
    $this->pluginName = $pluginName;
    $this->pluginVersion = $pluginVersion;
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
    wp_register_script('ayro-script', plugins_url('js/ayro-wordpress.min.js', __FILE__, array(), false, true));
    wp_enqueue_script('ayro-script');

    // Ayro init script
    wp_register_script('ayro-script-init', plugins_url('js/ayro-init.js', __FILE__, array(), false, true));
    $settings = get_option('ayro_settings');
    $config = array(
      'appToken' => $settings['app_token'],
      'chatboxHeaderTitle' => $settings['chatbox_header_title'],
      'chatboxInputPlaceholder' => $settings['chatbox_input_placeholder'],
    );
    wp_localize_script('ayro-script-init', 'config', $config);
    wp_enqueue_script('ayro-script-init');
  }
}

<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class AyroI18n {

  /**
   * Load the plugin text domain for translation.
   */
  public function loadTextDomain() {
    load_plugin_textdomain(
      AYRO_PLUGIN_NAME,
      false,
      dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
    );
  }
}

<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class AyroPlugin {

  /**
   * The loader that's responsible for maintaining and registering all hooks that power the plugin.
   */
  private $loader;

  /**
   * The unique identifier of this plugin.
   */
  private $name;

  /**
   * The current version of this plugin.
   */
  private $version;

  /**
   * The current version of the javascript library.
   */
  private $libraryVersion;

  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the admin area and
   * the public-facing side of the site.
   */
  public function __construct() {
    $this->name = AYRO_PLUGIN_NAME;
    $this->version = AYRO_PLUGIN_VERSION;
    $this->libraryVersion = AYRO_LIBRARY_VERSION;
    $this->loadDependencies();
    $this->setLocale();
    $this->defineAdminHooks();
    $this->definePublicHooks();
  }

  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   */
  public function getLoader() {
    return $this->loader;
  }

  /**
   * The name of the plugin used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Retrieve the version number of the plugin.
   */
  public function getVersion() {
    return $this->version;
  }

  /**
   * Retrieve the version number of the plugin.
   */
  public function getLibraryVersion() {
    return $this->libraryVersion;
  }

  /**
   * Load the required dependencies for this plugin.
   *
   * Include the following files that make up the plugin:
   *
   * - AyroLoader. Orchestrates the hooks of the plugin.
   * - AyroI18n. Defines internationalization functionality.
   * - AyroAdmin. Defines all hooks for the admin area.
   * - AyroPublic. Defines all hooks for the public side of the site.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   */
  private function loadDependencies() {
    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/ayro-loader.php';

    /**
     * The class responsible for defining internationalization functionality
     * of the plugin.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/ayro-i18n.php';

    /**
     * The class responsible for defining all actions that occur in the admin area.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'admin/ayro-admin.php';

    /**
     * The class responsible for defining all actions that occur in the public-facing
     * side of the site.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'public/ayro-public.php';

    $this->loader = new AyroLoader();
  }

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the AyroI18n class in order to set the domain and to register the hook
   * with WordPress.
   */
  private function setLocale() {
    $ayroI18n = new AyroI18n();
    $this->loader->addAction('plugins_loaded', $ayroI18n, 'loadTextDomain');
  }

  /**
   * Register all of the hooks related to the admin area functionality
   * of the plugin.
   */
  private function defineAdminHooks() {
    $ayroAdmin = new AyroAdmin();
    $this->loader->addAction('admin_enqueue_scripts', $ayroAdmin, 'enqueueStyles');
    $this->loader->addAction('admin_enqueue_scripts', $ayroAdmin, 'enqueueScripts');
    $this->loader->addAction('admin_menu', $ayroAdmin, 'createSettingsPage');
    $this->loader->addAction('admin_init', $ayroAdmin, 'initSettingsPage');
  }

  /**
   * Register all of the hooks related to the public-facing functionality
   * of the plugin.
   */
  private function definePublicHooks() {
    $ayroPublic = new AyroPublic($this->libraryVersion);
    $this->loader->addAction('wp_enqueue_scripts', $ayroPublic, 'enqueueStyles');
    $this->loader->addAction('wp_enqueue_scripts', $ayroPublic, 'enqueueScripts');
  }

  /**
   * Run the loader to execute all of the hooks with WordPress.
   */
  public function run() {
    $this->loader->run();
  }
}

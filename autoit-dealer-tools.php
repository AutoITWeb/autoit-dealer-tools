<?php
/*
Plugin Name: AutoIT Dealer Tools
Plugin URI:  http://www.autoit.dk/hjemmesider
Description: Tools providing connection to AutoDesktop, and other Biltorvet services.
Version:     3.3.5
Requires PHP: 7.4
Tested up to: 6.7.2
Author:      Auto IT A/S
Author URI:  https://www.autoit.dk
License:     Proprietary
License URI: https://www.biltorvet.as/media/1385/betingelser-biltorvetas.pdf
Text Domain: biltorvet-dealer-tools
Domain Path: /languages
GitHub Plugin URI: AutoITWeb/autoit-dealer-tools
GitHub Plugin URI: https://github.com/AutoITWeb/autoit-dealer-tools

Auto IT Dealer Tools is free software: you can redistribute it, but NOT modify it.
Changes to the code or attempts on rebranding is NOT allowed.
*/

require_once plugin_dir_path( __FILE__ ) . 'enums/ActivityType.php';
require_once plugin_dir_path( __FILE__ ) . 'enums/WidgetType.php';
require_once plugin_dir_path( __FILE__ ) . 'enums/WidgetAutodesktopLeadsActivityTypesEnum.php';
require_once plugin_dir_path( __FILE__ ) . 'objects/BDTFilterObject.php';
require_once plugin_dir_path( __FILE__ ) . 'objects/LeadInputObject.php';
require_once plugin_dir_path( __FILE__ ) . 'objects/NewLeadInputObject.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/PluginSettings.php';
require_once plugin_dir_path( __FILE__ ) . 'ajax/Ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'api/CustomApiRoutes.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/BiltorvetAPI.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/TextUtils.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/Biltorvet.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/BiltorvetShortcodes.php';
require_once plugin_dir_path( __FILE__ ) . 'v2/src/Utility/PrivatePluginUpdater.php';

register_activation_hook( __FILE__, array('Biltorvet', 'bdt_plugin_activated'));

// Init
new Biltorvet();

// Include version 2
require_once(plugin_dir_path( __FILE__ ) . 'v2/biltorvet.php');

$updater = new \Biltorvet\Utility\PrivatePluginUpdater(__FILE__, 'AutoITWeb', 'autoit-dealer-tools');

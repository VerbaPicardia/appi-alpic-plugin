<?php
/**
 * Plugin Name: Alpic
 */

require_once 'src/AlpicPlugin.php';

$plugin = new AlpicPlugin();
register_activation_hook( __FILE__, array($plugin, 'activation') );
register_deactivation_hook( __FILE__, array($plugin, 'deactivation') );
$plugin->init();
?>

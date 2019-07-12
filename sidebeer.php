<?php
/**
 * Plugin Name: Sidebeer
 * Plugin URI: https://github.com/quimo/sidebeer
 * Description: Plugin WordPress per la creazione di sidebar. Lo shortcode [sidebeer] aggiunge una sidebar alla pagina in cui è chiamato. La sidebar è mostrata se il suo nome corrisponde, al nome di un file di un template del tema, allo slug di un custom post o allo slug della pagina corrente.
 * Version: 1.0.0
 * Author: Simone Alati
 * Author URI: https://www.simonealati.it
 * Text Domain: sidebeer
 */

if (!defined('WPINC')) die;

include_once __DIR__ . '/inc/wpb.class.php';
include_once __DIR__ . '/inc/wpb-settings.class.php';
include_once __DIR__ . '/inc/sidebeer.class.php';

WpbFactory::getInstance(__FILE__, new Sidebeer());
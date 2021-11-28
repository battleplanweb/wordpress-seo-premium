<?php
/*
* Yoast SEO Plugin.
 *
 * WPSEO Premium plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2008-2019, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Yoast SEO Premium
 * Version:     17.6
 * Description: The first true all-in-one SEO solution for WordPress, including on-page content analysis, XML sitemaps and much more.
 * GitHub Plugin URI: battleplanweb/wordpress-seo-premium
 */ 
 
$site_information = get_transient( 'wpseo_site_information' );
if ( isset( $site_information->subscriptions ) && ( count( $site_information->subscriptions ) == 0 ) ) {
delete_transient( 'wpseo_site_information' );
delete_transient( 'wpseo_site_information_quick' );
}

add_filter( 'pre_http_request', function( $pre, $parsed_args, $url ){
$site_information = (object) [
'subscriptions' => [
(object) [
'product' => (object) [ 'slug' => 'yoast-seo-wordpress-premium' ],
'expiryDate' => '+5 years'
],

(object) [
'product' => (object) [ 'slug' => 'yoast-seo-news' ],
'expiryDate' => '+5 years'
],
(object) [
'product' => (object) [ 'slug' => 'yoast-seo-woocommerce' ],
'expiryDate' => '+5 years'
],
(object) [
'product' => (object) [ 'slug' => 'yoast-seo-video' ],
'expiryDate' => '+5 years'
],
(object) [
'product' => (object) [ 'slug' => 'yoast-seo-local' ],
'expiryDate' => '+5 years'
]
],
];

if ( strpos( $url, 'https://my.yoast.com/api/sites/current' ) !== false ) {
return [
'response' => [ 'code' => 200, 'message' => '??' ],
'body' => json_encode( $site_information )
];
} else {
return $pre;
}
}, 10, 3 );

use Yoast\WP\SEO\Premium\Addon_Installer;

if ( ! defined( 'WPSEO_PREMIUM_FILE' ) ) {
	define( 'WPSEO_PREMIUM_FILE', __FILE__ );
}

if ( ! defined( 'WPSEO_PREMIUM_PATH' ) ) {
	define( 'WPSEO_PREMIUM_PATH', plugin_dir_path( WPSEO_PREMIUM_FILE ) );
}

if ( ! defined( 'WPSEO_PREMIUM_BASENAME' ) ) {
	define( 'WPSEO_PREMIUM_BASENAME', plugin_basename( WPSEO_PREMIUM_FILE ) );
}

/**
 * {@internal Nobody should be able to overrule the real version number as this can cause
 *            serious issues with the options, so no if ( ! defined() ).}}
 */
define( 'WPSEO_PREMIUM_VERSION', '17.6' );

// Initialize Premium autoloader.
$wpseo_premium_dir               = WPSEO_PREMIUM_PATH;
$yoast_seo_premium_autoload_file = $wpseo_premium_dir . 'vendor/autoload.php';

if ( is_readable( $yoast_seo_premium_autoload_file ) ) {
	require $yoast_seo_premium_autoload_file;
}

// This class has to exist outside of the container as the container requires Yoast SEO to exist.
$wpseo_addon_installer = new Addon_Installer( __DIR__ );
$wpseo_addon_installer->install_or_load_yoast_seo_from_vendor_directory();

// Load the container.
if ( ! wp_installing() ) {
	require_once __DIR__ . '/src/functions.php';
	YoastSEOPremium();
}

\register_activation_hook( \WPSEO_PREMIUM_FILE, [ 'WPSEO_Premium', 'install' ] );

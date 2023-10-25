<?php
/*
Plugin Name: Upload
Plugin URI: https://example.com/my-plugin
Description: Plugin
Version: 1.0
Author: Name
Author URI: https://example.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

require_once __DIR__ . '/yandex.php';

use Aws\S3\S3Client;

function my_function($upload)
{
    $s3 = new S3Client([
        'version' => 'latest',
        'endpoint' => 'https://storage.yandexcloud.net',
        'region' => 'ru-central1',
        'credentials' => [
            'key' => '',
            'secret' => '',
        ],
    ]);
    $yc = new Yandex_Cloud($s3);
    $result = $yc->sendToStorage();
    return $upload;
}

add_action('wp_handle_upload', 'my_function');

function yandexcloud_cdn_plugin_image_url( $url ) {

    // Check if the URL is an image URL
    if ( preg_match( '/\.(jpg|jpeg|png|gif)(\?.*)?$/', $url ) ) {
  
      // Replace the domain name
      $new_url = str_replace( home_url(), 'https://storage.yandexcloud.net/<bucketName>', $url );
  
      // Return the new URL
      return $new_url;
    }
  
    // Return the original URL if it's not an image URL
    return $url;
  }
  
  add_filter( 'wp_get_attachment_url', 'yandexcloud_cdn_plugin_image_url' );

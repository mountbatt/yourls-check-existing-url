<?php
/*
Plugin Name: YOURLS Check URL Exists API
Plugin URI: https://yourls.org/
Description: Fügt eine API-Methode hinzu, um zu prüfen, ob eine Long-URL bereits eine Short-URL hat, ohne eine neue zu erstellen.
Version: 1.0
Author: BÜRO BATTENBERG
Author URI: https://deine-webseite.de/
*/

if( !defined( 'YOURLS_ABSPATH' ) ) die();

/**
 * Registriert die API-Methode "check_url_exists".
 */
yourls_add_filter( 'api_action_check_url_exists', 'check_url_exists' );

function check_url_exists() {
    // Die Long-URL aus der API-Anfrage abrufen
    //$url = yourls_sanitize_url( yourls_get_request( 'url' ) );
    $url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
    //echo "xxx".$url;
    // Datenbankverbindung abrufen
    global $ydb;

    // Prüfen, ob die URL bereits existiert
    $table = YOURLS_DB_TABLE_URL;
    $query = $ydb->fetchOne("SELECT * FROM `$table` WHERE `url` = :url", [ 'url' => $url ]);
    //print_r($query);

    if( $query ) {
        // Short-URL zurückgeben, wenn sie existiert
        $shorturl = yourls_get_yourls_site() . '/' . $query['keyword'];
        return [
            'status'    => 'success',
            'shorturl'  => $shorturl,
            'longurl'   => $url,
            'message'   => 'Short URL exists',
            'clicks'    => $query['clicks'],
            'keyword'   => $query['keyword'],
        ];
    } else {
        // Falls die URL nicht existiert, eine entsprechende Antwort geben
        return [
            'status'    => 'fail',
            'longurl'   => $url,
            'message'   => 'No short URL found for this long URL'
        ];
    }
}

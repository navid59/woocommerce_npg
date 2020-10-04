<?php
/**
 * @package NetopiaAgreemnetPlugin
 */

/*
Plugin Name: NETOPIA Payments agreements checklist
Plugin URI: https://www.netopia-payments.com
Description: a plugin to check the necessary options. it's helping you to connect your website to NETOPIA PAYMENTS faster.
Author: Netopia
Version: 0.0.1
License: GPLv2 or later
Text Domain : netopia-agreements-checklist
*/
defined( 'ABSPATH' ) or die('Access denied');
DEFINE ('NTP_AGGREMENT_PLUGIN_DIR', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) . '/' );
include_once( 'wc-netopiapayments-agreement.php' );
$ntpAgreement = new NetopiapaymentsAgreement();

@wp_enqueue_script( 'netopia_payments_agreement', plugin_dir_url( __FILE__ ) . 'js/netopia_payments_agreement.js',array('jquery'),'1.0' ,true);
wp_localize_script( 'netopia_payments_agreement', 'checkAddress_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));

@wp_enqueue_script( 'netopia_payments_agreement_toastrjs', plugin_dir_url( __FILE__ ) . 'js/toastr.min.js',array(),'2.0' ,true);
@wp_enqueue_style( 'netopia_payments_agreement_toastrcss', plugin_dir_url( __FILE__ ) . 'css/toastr.min.css',array(),'2.0' ,false);

add_action( 'wp_ajax_check_url_validation','check_url_validation');
add_action( 'wp_ajax_nopriv_check_url_validation','check_url_validation');

add_action( 'wp_ajax_ssl_validation','ssl_validation');
add_action( 'wp_ajax_nopriv_ssl_validation','ssl_validation');

function check_url_validation() {
    $temp = false;
    if($_POST['address'] && is_string($_POST['address']) && preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $_POST['address'])){
        $ch = curl_init($_POST['address']);
        if($ch !== false) {
            curl_setopt($ch, CURLOPT_HEADER         ,true);
            curl_setopt($ch, CURLOPT_NOBODY         ,true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
            curl_setopt($ch, CURLOPT_MAXREDIRS      ,10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
            curl_setopt($ch, CURLOPT_TIMEOUT        ,6);
            curl_exec($ch);
            if(!curl_errno($ch)) {
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if($code == 200) {$temp = true;}
            }
        }
    }
    $response['exists'] = $temp;
    $response = json_encode($response);
    echo $response;
    wp_die();
}

function ssl_validation() {
    $temp = false;
    $serverName =   'http://netopia-system.com';
//    $serverName =   $_SERVER['HTTP_HOST'];
    $stream = stream_context_create (array("ssl" => array("capture_peer_cert" => true)));
    $read   = @fopen($serverName, "rb", false, $stream);
    $cont   = @stream_context_get_params($read);
    $var    = @($cont["options"]["ssl"]["peer_certificate"]);
    $result = (!is_null($var)) ? true : false;
    $response = json_encode($result);
    echo $response;
    wp_die();
}

$ntpAgreement->getNetopiaOptions();
/**
 * For Logo in Footer , ...
 * <img src="https://netopia-system.com/12345/">
 */

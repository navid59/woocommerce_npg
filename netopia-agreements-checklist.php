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

@wp_enqueue_script( 'netopia_payments_agreement', plugin_dir_url( __FILE__ ) . 'js/netopia_payments_agreement.js',array('jquery'),'2.0' ,true);
wp_localize_script( 'netopia_payments_agreement', 'checkAddress_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));

@wp_enqueue_script( 'netopia_payments_agreement_toastrjs', plugin_dir_url( __FILE__ ) . 'js/toastr.min.js',array(),'2.0' ,true);
@wp_enqueue_style( 'netopia_payments_agreement_toastrcss', plugin_dir_url( __FILE__ ) . 'css/toastr.min.css',array(),'2.0' ,false);

add_action( 'wp_ajax_check_url_validation','check_url_validation');
add_action( 'wp_ajax_nopriv_check_url_validation','check_url_validation');

add_action( 'wp_ajax_ssl_validation','ssl_validation');
add_action( 'wp_ajax_nopriv_ssl_validation','ssl_validation');

add_action( 'wp_ajax_golive_validation','golive_validation');
add_action( 'wp_ajax_nopriv_golive_validation','golive_validation');

add_action( 'wp_ajax_send_agreement','send_agreement');
add_action( 'wp_ajax_nopriv_send_agreement','send_agreement');

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

/**
 * to check if web site has valid SSl Certificate
 * @param $toSend is define to display or retuen ssl status
 */
function ssl_validation($toSend = false) {
    $temp = false;
    $serverName =   'http://netopia-system.com';
//    $serverName =   $_SERVER['HTTP_HOST'];
    $stream = stream_context_create (array("ssl" => array("capture_peer_cert" => true)));
    $read   = @fopen($serverName, "rb", false, $stream);
    $cont   = @stream_context_get_params($read);
    $var    = @($cont["options"]["ssl"]["peer_certificate"]);
    $result = (!is_null($var)) ? true : false;
    $response = json_encode($result);
    if(!$toSend)
        echo $response;
    else
        return $response;
    wp_die();
}

/**
 * Send agreement Json
 */
function send_agreement() {
    
    $ntpInstance = new NetopiapaymentsAgreement();
    $sacKey = get_option($ntpInstance->getSlug().'_seller_account');
    $declarations = get_option( $ntpInstance->getSlug().'_'.'declaration' ); // get all declaration (declarations are checkbox)
    $forbiddens = get_option( $ntpInstance->getSlug().'_'.'forbidden' ); // get all forbiddens (forbiddens are checkbox)
    
    $ntpDeclare = array (
        'completeDescription' => (bool) in_array("declaration_description", $declarations) ? true : false,
        'priceCurrency' =>  (bool) in_array("declaration_price_currency", $declarations) ? true : false,
        'contactInfo' =>  (bool) in_array("declaration_contact_info", $declarations) ? true : false,
        'forbiddenBusiness' =>  (bool) in_array("declaration_forbidden_business", $forbiddens) ? true : false
      );

    
    // echo "<pre>";
    // print_r($ntpDeclare);
    // echo "</pre><hr>";

    $ntpUrl = array(
        'termsAndConditions' => get_option( $ntpInstance->getSlug().'_'.'terms_conditions' ),
        'privacyPolicy' => get_option( $ntpInstance->getSlug().'_'.'privacy_policy' ),
        'deliveryPolicy' => get_option( $ntpInstance->getSlug().'_'.'delivery_policy' ),
        'returnAndCancelPolicy' => get_option( $ntpInstance->getSlug().'_'.'return_cancel' ),
        'gdprPolicy' => get_option( $ntpInstance->getSlug().'_'.'gdpr' )
        );

    
    $ntpImg = array(
        'visaLogoLink' => get_option( $ntpInstance->getSlug().'_'.'visa_logo' ),
        'masterLogoLink' => get_option( $ntpInstance->getSlug().'_'.'master_logo' ),
        'netopiaLogoLink' => get_option( $ntpInstance->getSlug().'_'.'netopia_logo' )
    );


    $jsonData = makeActivateJson($sacKey, $ntpDeclare, $ntpUrl, $ntpImg);

    // echo "---- Jason Without Encrypt ----".PHP_EOL;
    // print_r($this->jsonData);

    die(print_r($jsonData));
    
    
    

}

function makeActivateJson($sacKey, $declareatins, $urls, $images) {
    $jsonData = array(
      "sac_key" => $sacKey,
      "agreements" => array(
            "declare" => $declareatins,
            "urls"    => $urls,
            "images"  => $images,
            "ssl"     => ssl_validation(true)
          ),
      "lastUpdate" => date("c", strtotime(date("Y-m-d H:i:s"))), // To have Date & Time format on RFC3339
      "platform" => 'Woocomerce'
    );
    
    $post_data = json_encode($jsonData, JSON_FORCE_OBJECT);
    return $post_data;
  }

/**
 * Create a local agreement.xml 
 */
function golive_validation() {
    
    $agreements = array(
        'declaration',
        'forbidden',
        'terms_conditions',
        'privacy_policy',
        'delivery_policy',
        'return_cancel',
        'gdpr',
        'visa_logo',
        'master_logo',
        'netopia_logo',
        'ssl'
    );

    $ntpInstance = new NetopiapaymentsAgreement();

    $domtree = new \DOMDocument('1.0', 'UTF-8');
    $domtree->formatOutput = true;
    $xmlRoot = $domtree->createElement("xml");
    $xmlRoot = $domtree->appendChild($xmlRoot);

    $sac_key = $domtree->createElement("sac_key", get_option($ntpInstance->getSlug().'_seller_account'));
    $sac_key = $xmlRoot->appendChild($sac_key);
    $agr = $domtree->createElement("agrremnts");
    $agr = $xmlRoot->appendChild($agr);

    foreach ($agreements as $agreement) {
        switch ($agreement) {
            case "declaration":
            case "forbidden":
                $declarations = get_option( $ntpInstance->getSlug().'_'.$agreement );
                foreach ($declarations as $declarItem) {
                    $agr->appendChild($domtree->createElement($declarItem,1));
                }
                break;
            default:
                $agr->appendChild($domtree->createElement($agreement,get_option( $ntpInstance->getSlug().'_'.$agreement ) ? get_option( $ntpInstance->getSlug().'_'.$agreement ) : 'null'));
                break;
        }
    }

    $last_update = $domtree->createElement("last_update", date("Y/m/d H:i:s"));
    $last_update = $xmlRoot->appendChild($last_update);

    $last_update = $domtree->createElement("platform", 'wordpress_'.$ntpInstance->getVersion());
    $last_update = $xmlRoot->appendChild($last_update);

    $result = $domtree->save($ntpInstance->getPluginPath().'agreements.xml') ? true : false;
    if($result)
        echo "Agrrement saved localy!";
    else
        echo "Agrrement could not save localy!!";    
    echo $result;
    wp_die();
}

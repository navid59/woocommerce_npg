<?php
class NetopiapaymentsAgreement
{
    protected $page_title = 'NETOPIA Agreements Check List';
    protected $menu_title = 'NETOPIA';
    protected $slug = 'netopia_agreement';
    private $options;
    public function __construct()
    {
    add_action( 'admin_menu', array( $this, 'create_plugin_settings' ) );
    add_action( 'admin_init', array( $this, 'setup_agreement_section' ) );
    add_action( 'admin_init', array( $this, 'setup_agreement_fields' ) );


    }

    public function create_plugin_settings() {
        // Add the menu item and page
        $capability = 'manage_options';
        $callback = array( $this, 'dashbord' );
        $icon = 'dashicons-awards';
        $position = 100;

        add_menu_page( $this->page_title, $this->menu_title, $capability, $this->slug, $callback, $icon, $position );
    }

    public function dashbord() {
        ?>
        <div class="wrap">
            <h2><?=$this->page_title ?></h2>
            <form method="post" action="options.php">

                <?php
                settings_fields( 'netopia_agreement' );
                do_settings_sections( 'netopia_agreement' );
                submit_button();

                ?>
            </form>
        </div>
        <?php
    }

    public function setup_agreement_section() {
        add_settings_section( 'general', 'General section ', array( $this, 'section_callback' ), 'netopia_agreement' );
        add_settings_section( 'conditions', 'Mandatory conditions', array( $this, 'section_callback' ), 'netopia_agreement' );
        add_settings_section( 'forbidden', 'Forbidden domains', array( $this, 'section_callback' ), 'netopia_agreement' );
        add_settings_section( 'urls', 'Links', array( $this, 'section_callback' ), 'netopia_agreement' );
        add_settings_section( 'img', 'Mandatory images', array( $this, 'section_callback' ), 'netopia_agreement' );
        add_settings_section( 'ssl', 'SSL certification', array( $this, 'section_callback' ), 'netopia_agreement' );
    }

    public function setup_agreement_fields() {
//        add_settings_field( 'seller_account', 'Seller Account', array( $this, 'field_callback' ), 'netopia_agreement', 'our_first_section' );
//        register_setting( 'netopia_agreement', 'seller_account' );
        $fields = array(
            array(
                'uid' => $this->slug.'_seller_account',
                'label' => __('Signature Key'),
                'section' => 'general',
                'type' => 'text',
                'options' => false,
                'placeholder' => __('XXXX-XXXX-XXXX-XXXX-XXXX'),
                'helper' => __(''),
                'supplemental' => __(''),
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_declaration',
                'label' => '',
                'section' => 'conditions',
                'type' => 'checkbox',
                'options' => array(
                    'declaration_description' => __('Declare that there is a clear and complete description of the goods and services that we will sell'),
                    'declaration_price_currency' => __('Declare that the prices and currency are clear displayed for the goods / services'),
                    'declaration_contact_info' => __('Declare that, the contact details of the company (SC, CUI, address, telephone, fax / e-mail,...) are on the website clearly'),
                ),
                'helper' => '',
                'supplemental' => '',
                'default' => array()
            ),
            array(
                'uid' => $this->slug.'_forbidden',
                'label' => '',
                'section' => 'forbidden',
                'type' => 'special_checkbox',
                'options' => array(
                    'declaration_forbidden_business' => __('Declare that we are not do trade on the list above also in none of the forbidden business / services'),
                ),
                'helper' => '',
                'supplemental' => '',
                'default' => array(),
                'items' => array(
                    'domeniul farmaceutic: produse sintetice si naturiste, inclusiv suplimente alimentare',
                    'tutun, inclusiv narghilea, tigari electronice si consumabile',
                    'gambling sub toate formele (jocuri de noroc, pariuri sportive si nu numai, quizz-uri in care se percepe taxa de participare si se castiga bani, alte jocuri cu castiguri in bani - bingo, forex, site-uri de licitatii, etc)',
                    'materiale pentru adulti: inchiriere/vanzare casete video de profil, (video)chat, dating, escorte',
                    'alcool (exceptie vin si bere)',
                    'articole si produse de vanatoare (arme albe si arme de foc, cu munitie clasica si comprimata)',
                    'videostreaming (cu exceptia cazului in care exista acte doveditoare: sunteti persoana care realizeaza aceste video-uri sau detineti drepturi de autor asupra lor',
                    'astrologie, tarot, etc.'),
            ),
            array(
                'uid' => $this->slug.'_terms_conditions',
                'label' => 'Terms and conditions',
                'section' => 'urls',
                'type' => 'link',
                'options' => false,
                'placeholder' => 'Terms and conditions URL',
                'helper' => '',
                'supplemental' => 'ex. somewhere/terms_and_conditions',
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_privacy_policy',
                'label' => 'Privacy policy',
                'section' => 'urls',
                'type' => 'link',
                'options' => false,
                'placeholder' => 'Privacy policy URL',
                'helper' => '',
                'supplemental' => 'ex. somewhere/privacy_policy',
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_delivery_policy',
                'label' => 'Delivery policy',
                'section' => 'urls',
                'type' => 'link',
                'options' => false,
                'placeholder' => 'Delivery URL',
                'helper' => '',
                'supplemental' => 'ex. somewhere/delivery',
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_return_cancel',
                'label' => 'Return / Cancellation policy',
                'section' => 'urls',
                'type' => 'link',
                'options' => false,
                'placeholder' => 'Return / Cancel URL',
                'helper' => '',
                'supplemental' => 'ex. somewhere/return_cancel',
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_gdpr',
                'label' => 'General Data Protection Regulation',
                'section' => 'urls',
                'type' => 'link',
                'options' => false,
                'placeholder' => 'GDPR URL',
                'helper' => '',
                'supplemental' => 'ex. somewhere/gdpr',
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_visa_logo',
                'label' => 'Logo of Visa Card',
                'section' => 'img',
                'type' => 'link',
                'options' => false,
                'placeholder' => 'Visa Card image URL',
                'helper' => '',
                'supplemental' => 'ex. img/visa_img.jpg',
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_master_logo',
                'label' => 'Logo of Master Card',
                'section' => 'img',
                'type' => 'link',
                'options' => false,
                'placeholder' => 'Master Card image URL',
                'helper' => '',
                'supplemental' => 'ex. img/master_img.jpg',
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_netopia_logo',
                'label' => 'Logo of NETOPIA Payments',
                'section' => 'img',
                'type' => 'link',
                'options' => false,
                'placeholder' => 'NETOPIA Payments logo URL',
                'helper' => '',
                'supplemental' => 'ex. img/netopia_payments.svg',
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_ssl',
                'label' => 'Using SSL (https)',
                'section' => 'ssl',
                'type' => 'select_ssl',
                'options' => array(
                    'yes' => 'Yes, we use HTTPS protocol as base for our web site',
                    'no' => 'No, we use HTTP protocol !!!',
                    'maybe' => 'Not sure if SSL Certificate is still valid!!!'
                ),
                'placeholder' => 'Text goes here',
                'helper' => '',
                'supplemental' => '',
                'default' => 'maybe'
            )
        );
        foreach( $fields as $field ){
            add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'netopia_agreement', $field['section'], $field );
            register_setting( 'netopia_agreement', $field['uid'] );
        }
    }

    public function section_callback( $arguments )
    {
        switch( $arguments['id'] ){
            case 'general':
                echo '';
                break;
            case 'conditions':
                echo '';
                break;
            case 'forbidden':
                echo '';
                break;
            case 'ssl':
                echo '';
                break;
            case 'img':
                echo '';
                break;
        }
    }

    public function field_callback( $arguments ) {
        $value = get_option( $arguments['uid'] ); // Get the current value, if there is one
        if( ! $value ) { // If no value exists
            $value = $arguments['default']; // Set to our default
        }

        // Check which type of field we want
        switch( $arguments['type'] ){
            case 'text': // If it is a text field
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                break;
            case 'textarea': // If it is a textarea
                printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                break;
            case 'select': // If it is a select dropdown
            case 'select_ssl':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    foreach( $arguments['options'] as $key => $label ){
                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value, $key, false ), $label );
                    }
                    printf( '<select name="%1$s" id="%1$s">%2$s</select>', $arguments['uid'], $options_markup );
                    if( $arguments['type'] == 'select_ssl'){
                        printf('<button type="button" id="%2$s_verify" class="button button-primary">%s</button>', 'Verify SSL Certificate', $arguments['uid']);
                    }
                }
                break;
            case 'radio':
            case 'checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
                        @$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator, null, null );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
            case 'special_checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
                        @$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], 'checkbox', $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator, null, null );
                    }
                    if( ! empty($arguments['items']) ){
                        foreach ($arguments['items'] as $item){
                            printf( '<li>%s</li>', $item );
                        }
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
            case 'link': // If it is a text field
                printf( '<span><b>%1$s%2$s%3$s</b></span>', 'https://',$_SERVER['HTTP_HOST'], '/' );
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                printf('<button type="button" id="%2$s_verify" class="button button-primary">%1$s</button>', 'check', $arguments['uid']);
                break;
        }

        // If there is help text
        if( $helper = $arguments['helper'] ){
            printf( '<span class="helper"> %s</span>', $helper ); // Show it
        }

        // If there is supplemental text
        if( $supplimental = $arguments['supplemental'] ){
            printf( '<p class="description">%s</p>', $supplimental ); // Show it
        }
    }

    public function sslCertificate() {
        $serverName = "https://www.netopia-system.com";
//        $serverName =   $_SERVER['HTTP_HOST'];

        $stream = stream_context_create (array("ssl" => array("capture_peer_cert" => true)));
        $read   = @fopen($serverName, "rb", false, $stream);
        $cont   = @stream_context_get_params($read);
        $var    = @($cont["options"]["ssl"]["peer_certificate"]);
        $result = (!is_null($var)) ? true : false;
        return $result;
    }

    public function getNetopiaOptions() {
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

        $domtree = new DOMDocument('1.0', 'UTF-8');
        $domtree->formatOutput = true;

        $xmlRoot = $domtree->createElement("xml");
        $xmlRoot = $domtree->appendChild($xmlRoot);

        $sac_key = $domtree->createElement("sac_key", get_option($this->slug.'_seller_account'));
        $sac_key = $xmlRoot->appendChild($sac_key);

        $agr = $domtree->createElement("agrremnts");
        $agr = $xmlRoot->appendChild($agr);

        foreach ($agreements as $agreement) {
            switch ($agreement) {
                case "declaration":
                case "forbidden":
                    $declarations = get_option( $this->slug.'_'.$agreement );
                    foreach ($declarations as $declarItem) {
                        $agr->appendChild($domtree->createElement($declarItem,1));
                    }
                    break;
                default:
                    $agr->appendChild($domtree->createElement($agreement,get_option( $this->slug.'_'.$agreement )));
                    break;
            }
        }

        $last_update = $domtree->createElement("last_update", date("Y/m/d H:i:s"));
        $last_update = $xmlRoot->appendChild($last_update);

        $domtree->save('agreements.xml');
        ///
    }
}
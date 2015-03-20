<?php

/**
 */
class Onetree_Ocauru_Model_Standard extends Mage_Payment_Model_Method_Abstract {

    const INSTALLMENT_ATTRIBUTE_CODE = 'oca_installment';
    /**
     * unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'ocauru';
    /**
     * Here are examples of flags that will determine functionality availability
     * of this module to be used by frontend and backend.
     *
     * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
     *
     * It is possible to have a custom dynamic logic by overloading
     * public function can* for each flag respectively
     */
    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway = true;
    /**
     * Can authorize online?
     */
    protected $_canAuthorize = true;
    /**
     * Can capture funds online?
     */
    protected $_canCapture = true;
    /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial = false;
    /**
     * Can refund online?
     */
    protected $_canRefund = false;
    /**
     * Can void transactions online?
     */
    protected $_canVoid = true;
    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal = true;
    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout = true;
    /**
     * Is in test environment?
     */
    protected $_test;
    /**
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping = true;

    protected $_actionUrl = 'https://comprasweb.oca.com.uy/index.aspx';
    protected $_testActionUrl = 'https://190.64.15.112/index.aspx';

    protected $_requestUrl = 'https://compraswebcomercio.oca.com.uy/inicio.aspx';
    protected $_testRequestUrl = 'https://190.64.15.113/inicio.aspx';

    protected $_responseUrl = 'https://compraswebcomercio.oca.com.uy/presentacion.aspx';
    protected $_testResponseUrl = 'https://190.64.15.113/presentacion.aspx';
    // protected $_requestUrl = 'http://localhost/notela_test/ws.php';

    /**
     * Here you will need to implement authorize, capture and void public methods
     *
     * @see examples of transaction specific public methods such as
     * authorize, capture and void in Mage_Paygate_Model_Authorizenet
     */
    protected $description;
    protected $isActive = 0;
    protected $configRead = false;
    protected $orderStatus;
    protected $dest;

    protected $_config = null;

    public function __construct() {
        parent::__construct();
        $this->readConfig();
    }

    public function getAttCode(){

        return self::INSTALLMENT_ATTRIBUTE_CODE;
    }

    public function readConfig() {
        if ($this->configRead)
            return;
        $this->description  = $this->getConfigData('title');
        $this->isActive     = $this->getConfigData('is_active');
        $this->orderStatus  = $this->getOrderStatus();
        $this->configRead   = true;
        $this->_test 		= $this->getConfigData('test_environment');
        return;
    }

    public function getDestination() {
        return $this->dest;
    }


    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getActionUrl() {
        if ($this->_test) {
            return $this->_testActionUrl;
        } else {
            return $this->_actionUrl;
        }
    }

    public function getRequestUrl() {
        if ($this->_test) {
            return $this->_testRequestUrl;
        } else {
            return $this->_requestUrl;
        }
    }

    public function getResponseUrl() {
        if ($this->_test) {
            return $this->_testResponseUrl;
        } else {
            return $this->_responseUrl;
        }
    }

    /**

    Validates the payment by request params.
    Return array(
    'orderNr' => 'orderNr'
    'status' => 'new order status'
    'payment' => success,cancelled,failed
    'params' => everything else in array
    )


     */
    public function validatePayment($orderId) {
        $this->readConfig();
        $result = array('orderNr' => $orderId, 'payment' => 'failed', 'auto' => false);

        return $result;
    }

    /**
    Return array('destination' => 'destination url',
    'params' => array of params
    )
    so that it would be possible to construct the payment form
     */
    public function startPayment($totalSum, $orderNr, $currency) {
        return true;
    }


    public function assignData($data) {
        return $this;
    }

    /**
     * Authorize
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount) {
        parent::authorize($payment, $amount);
        return $this;
    }

    /**
    Url, where the user will be redirected in order to start the payment.
     */
    public function getOrderPlaceRedirectUrl() {
        $redirectUrl = Mage::getUrl('ocauru/standard/start/', array('_secure' => true));
        return $redirectUrl;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount) {

        return $this;
    }

    /**
     * Void payment
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function void(Varien_Object $payment) {
        return $this;
    }

    public function getOrderStatus() {
        return $this->getConfigData('order_status');
    }

    public function getPaymentMethod() {
        $this->paymentMethod = Mage::getSingleton('core/session')->getPaymentMethod();
        return $this->paymentMethod;
    }

    /**
     * @desc Instanciates a Visa config object and sets it as an attribute $this->_cofig
     *
     * @return Onetree_Ocauru_Model_Config
     */
    public function getConfig() {
        if($this->_config) {
            return $this->_config;
        } else {
            return $this->_config = Mage::getModel('ocauru/config');
        }
    }

    /**
     * Creates Array with the required fields to send to OCA and returns it.
     *
     * @return Array with values for OCA.
     */
    public function getCheckoutFormFields() {

        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $address = $order->getBillingAddress();
        $installments	= $order->getData(self::INSTALLMENT_ATTRIBUTE_CODE);

        $array_send["Nrocom"] = $this->getConfigData('nrocom');
        $array_send["Nroterm"] = $this->getConfigData('nroterm');
        $array_send["Moneda"] = $this->getConfigData('currency');
        $array_send['Importe']=number_format($order->getGrandTotal(),0)."00"; // El valor a oca deber ir con dos ceros al final y sin comas ni puntos. 
        $array_send['Importe'] = str_replace(",", "", $array_send['Importe']); // Se le quitan las comas para cuando el numero es mayor a mil.
        $array_send['Plan'] = ($installments)? str_pad($installments, 3, "0", STR_PAD_LEFT) : '001';
        $array_send['Tcompra'] = 0;
        $array_send['Urlresponse'] = $this->getResponseUrl();
        $array_send['Info'] = $orderIncrementId; // Este es el increment ID de la orden de Magento.
        $array_send['Tconn'] = 0;

        /*[START] get default value if is a guest order*/
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $storeId = Mage::app()->getStore()->getStoreId();
        $isconsfinal = Mage::getStoreConfig('billofsale/billofsalenumber_group/customer_type',$storeId);

        if($customer) {
            $group_id = $customer->getGroupId();
            $customerGroup = Mage::getModel('customer/group')->load($group_id);
            $isconsfinal = $customerGroup->getCustomerGroupIsEndUser();
        }
        /*[END] is final customer*/
        /*[START] patch financial law*/
        if($isconsfinal){
            $array_send['ConsFinal'] = $isconsfinal;
            //importe grabado importe total menos IVA
            $importe_grabado = $order->getBaseSubtotal() + $order->getBaseShippingAmount();
            $array_send['Importegrbd'] = str_replace(",", "", number_format($importe_grabado,0)."00");
            $array_send['Nrofactura'] = Mage::helper('billofsalenumber')->checkBillFormat($order->getBillofsaleNumber());
            /*[END] finacial law*/
        }

        $options = array(
            CURLOPT_URL                             => $this->getRequestUrl(),
            CURLOPT_HEADER                  => true,
            CURLOPT_POST                    => true,
            CURLOPT_POSTFIELDS              => $array_send,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            /*[START] Manually change this information foreach environment servers*/
            CURLOPT_SSLCERT                 => '/var/www/cert/mitrenda.com.crt',
            CURLOPT_SSLKEY                  => '/var/www/cert/mitrenda.com.key',
            /*[END]*/
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13 ( .NET CLR 3.5.30729)',
            CURLOPT_VERBOSE        => true,
        );

        Mage::log('[START] array to send',null,'oca_inicio_send.log');
        Mage::log($array_send,null,'oca_inicio_send.log');
        Mage::log('[END] array to send',null,'oca_inicio_send.log');

        $arr = $this->makeCurlConnection($options, $order);

        if(!array_key_exists('Idtrn',$arr)){
            $order->addStatusHistoryComment('No se registro valor Idtrn en orden');
            $order->save();
            Mage::log('[START] array from OCA',null,'oca_inicio_send.log');
            Mage::log('No se registro valor Idtrn en orden en el Inicio',null,'oca_inicio_send.log');
            Mage::log('[END] array from OCA',null,'oca_inicio_send.log');

            Mage::throwException('No se registro valor Idtrn en orden en el Inicio');

        }


        Mage::log('[START] array from OCA',null,'oca_inicio_send.log');
        Mage::log($arr,null,'oca_inicio_send.log');
        Mage::log('[END] array from OCA',null,'oca_inicio_send.log');

        $final_arr = array();

        $final_arr['Idtrn'] = trim($arr['Idtrn']);
        $final_arr['Info'] = $orderIncrementId;
        $final_arr['Urlresponse'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK,true).'oca/standard/response';

        return $final_arr;

    }

    /**
     * @desc Makes the connection with Oca through CURL to get the final data for the form.
     *
     * @param options array with options for CURL
     * @param order obj magento order loaded with the order ID
     * @param response bool is the Curl connection being used to get a response?
     * @return arr final XML returned by OCA converted to array
     */
    public function makeCurlConnection($options, $order, $step2 = false) {

        try {
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_POSTFIELDS, $options[CURLOPT_POSTFIELDS]);
            curl_setopt_array($ch, $options);


            $response = curl_exec($ch);
            if($response === false){
                $errText = curl_error($ch) . ' (error '.curl_errno($ch).')';
                Mage::log($errText);
                return array();
            }

            curl_close($ch);

            if ($step2) {
                $exp = '/<\?xml.*<\/presentacion>/';
            } else {
                $exp = '/<\?xml.*<\/Inicio>/';
            }

            preg_match($exp, $response, $matches ,PREG_OFFSET_CAPTURE, 0);
            $response = $matches[0][0];

            $xml_string = simplexml_load_string($response);
            $arr = json_decode( json_encode($xml_string) , 1);
            return $arr;
        }catch (Exception $e){
            Mage::logException($e);
            throw $e;
        }
    }


    /**
     * @desc Convierte el currency code de Magento al codigo equivalente ISO 4217
     * Complete a su necesidad
     *
     * @param string code
     *
     * @return string
     */
    public function getIsoCurrency($code) {
        switch($code) {
            case "UYU":
                return 858;

            default:
                return 858;
        }
    }

    public function processResponse() {

    }

}

?>
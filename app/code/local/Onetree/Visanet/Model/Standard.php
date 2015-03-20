<?php

/**
 */
class Onetree_Visanet_Model_Standard extends Mage_Payment_Model_Method_Abstract {

    /**
     * unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'visanet';
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
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping = true;

    protected $_actionUrl = '';

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
    /**
     * Is in test environment?
     */
    protected $_test;
    protected $_config = null;

    public function __construct() {
        parent::__construct();
        $this->readConfig();
    }

    public function readConfig() {
        if ($this->configRead)
            return;
        $this->description  = $this->getConfigData('title');
        $this->isActive     = $this->getConfigData('is_active');
        $this->orderStatus  = $this->getOrderStatus();
        $this->_test 		= $this->getConfigData('test_environment');
        $this->configRead   = true;
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
            return $this->getConfigData('testActionUrl');
        } else {
            return $this->getConfigData('actionUrl');
        }
    }

    /*
        public function getActionUrl() {
            return $this->_actionUrl;
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
        $redirectUrl = Mage::getUrl('visanet/standard/start/', array('_secure' => true));
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
     * 	If the system is set to TESTING then will use the testing config file.
     *
     * @return Onetree_Visanet_Model_Config
     */
    public function getConfig() {
        if($this->_config) {
            return $this->_config;
        } else {
            if ($this->_test) {
                $this->_config = Mage::getModel('visanet/configtesting');
            } else {
                $this->_config = Mage::getModel('visanet/config');
            }
        }
        return $this->_config;
    }

    /**
     * Creates Array with the required fields to send to Visa and returns it.
     *
     * @return Array with values for Visa.
     */
    public function getCheckoutFormFields() {

        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        /*
         * Descomentar debajo para ver los datos de la orden como los trae Magento.
         */
//        zend_debug::dump($order);

        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $baddress = $order->getBillingAddress();


        $vpos = Mage::getModel('visanet/vpos');

        $array_send["commerceId"] = $this->getConfig()->getIdCommerce();
        $array_send["acquirerId"] = $this->getConfig()->getIdAcquirer();

        $array_send['purchaseAmount']=number_format($order->getBaseGrandTotal(),0)."00"; // El valor a visa deber ir con dos ceros al final y sin comas ni puntos.
        $array_send['purchaseAmount'] = str_replace(",", "", $array_send['purchaseAmount']); // Se le quitan las comas para cuando el numero es mayor a mil.
        $array_send['purchaseCurrencyCode']=$this->getIsoCurrency($order->getOrderCurrencyCode());
        $array_send['purchaseOperationNumber']=$orderIncrementId;
        $array_send['billingAddress']=implode($baddress->getStreet());
        $array_send['billingCity']=$baddress->getCity();
        $array_send['billingState']=$baddress->getCity();
        $array_send['billingCountry']=$baddress->getCountryId();
        $array_send['billingZIP']=$baddress->getPostcode();
        $array_send['billingPhone']=$baddress->getTelephone();
        $array_send['billingEMail']=$baddress->getEmail();
        $array_send['billingFirstName']=$baddress->getFirstname();
        $array_send['billingLastName']=$baddress->getLastname();


        //Indicador de devolucioÌn de impuestos, solo seraÌ aceptado los valores â€œ0â€, â€œ4â€ y â€œ6â€ (3)
        //Valor 0 = No aplica Ley 19210 ni Ley 18999 
        //Valor 6 = Aplica Ley 19210
        //Valor 4 = Reservado
        /*[START] get default value if is a guest order*/
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $storeId = Mage::app()->getStore()->getStoreId();
        $isconsfinal = Mage::getStoreConfig('billofsale/billofsalenumber_group/customer_type',$storeId);

        if($customer) {
            $group_id = $customer->getGroupId();
            $customerGroup = Mage::getModel('customer/group')->load($group_id);
            $isconsfinal = $customerGroup->getCustomerGroupIsEndUser();
        }

        $customerType = 0;
        $customInvoiceNumber = '00000000';
        $mount = 0;

        if($isconsfinal){
            $customerType = 6;
            $customInvoiceNumber = Mage::helper('billofsalenumber')->checkBillFormat($order->getBillofsaleNumber());
            //$importeGrabado = $order->getBaseSubtotal() + $order->getBaseShippingAmount();
            $importeGrabado = number_format($order->getBaseGrandTotal(),0)/1.22;
            $mount = number_format($importeGrabado,2,"","");
//            $mount = str_replace(",", "", $mount);

        }else{
            $customerType = 4;
            $customInvoiceNumber = Mage::helper('billofsalenumber')->checkBillFormat($order->getBillofsaleNumber());
            $mount = 0;
        }


        /*[END] is final customer*/
        $array_send['reserved10'] = $customerType;
        $array_send['reserved11'] = $customInvoiceNumber; //NuÌmero de Factura, valor fijo de 7 caracteres
        $array_send['reserved12'] = $mount; //Monto Gravado, valor maÌximo de 12 caracteres(Si el monto es 100.30 doÌlares entonces la cantidadaenviares10030)

        $array_send['language']='SP'; //En espaÃ±ol

        //Mage::log($array_send, null, 'visanet.log');

        //Setear un arreglo de cadenas con los parÃ¡metros que serÃ¡n devueltos
        //por el componente

        $array_get["IDCOMMERCE"] = $this->getConfig()->getIdCommerce();
        $array_get["IDACQUIRER"] = $this->getConfig()->getIdAcquirer();

        //[OLD version]
        //$array_get["IDCOMMERCE"] = $this->getConfigData('IDCOMMERCE');
        //$array_get["IDACQUIRER"] = $this->getConfigData('IDACQUIRER');
        /*[END]*/

        $array_get['XMLREQ']="";
        $array_get['DIGITALSIGN']="";
        $array_get['SESSIONKEY']="";


        /*Get information from the "Configuration" file*/
        $VI = $this->getConfig()->getVi();
        $llaveVPOSCryptoPub = $this->getConfig()->getVposCryptoPub();
        $llaveComercioFirmaPriv = $this->getConfig()->getComSignPri();

        // Mage::log(var_export($array_send,true),null,'visa-info.log');
        Mage::log('[START] Infor to SEND',null,'visanet_send_data.log');
        Mage::log($array_send,null,'visanet_send_data.log');
        Mage::log('[END] Infor to SEND',null,'visanet_send_data.log');
        Mage::getModel('visanet/vpos')->VPOSSend($array_send,$array_get,$llaveVPOSCryptoPub,$llaveComercioFirmaPriv,$VI);
        Mage::log('[START] response from vPos',null,'visanet_response_data.log');
        Mage::log($array_get,null,'visanet_response_data.log');
        Mage::log('[END] response from vPos',null,'visanet_response_data.log');
        return $array_get;

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

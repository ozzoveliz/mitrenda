<?php
/**
 */
class Onetree_FirstData_Abstract extends Mage_Payment_Model_Method_Abstract {
	/**
	 * unique internal payment method identifier
	 *
	 * @var string [a-z0-9_]
	 */
	protected $_code = 'undefined';
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
	/**
	 * Here you will need to implement authorize, capture and void public methods
	 *
	 * @see examples of transaction specific public methods such as
	 * authorize, capture and void in Mage_Paygate_Model_Authorizenet
	 */
	protected $description;
	protected $orderStatus;
	protected $vk_version = '001';
	protected $testmode;
	protected $backurlOk = '';
	protected $backurlCancel = '';
	protected $backurlError = '';
	protected $merchant = '';
	protected $currency = '';
	protected $installments = '';
	protected $destination = '';
	protected $isActive = 0;
	protected $configRead = false;
	protected $dest;
	
	public function __construct() {
		parent::__construct ();
		$this->readConfig ();
	}
	
	public function readConfig() {
		if ($this->configRead)
			return;
		$this->orderStatus 		= $this->getConfigData ( 'order_payd' );
		$this->description 		= $this->getConfigData ( 'title' );
		$this->testmode 		= $this->getConfigData ( 'testmode' );
		$this->isActive 		= $this->getConfigData ( 'is_active' );
		$this->backurlOk 		= $this->getConfigData ( 'backurl_ok' );
		$this->backurlError 		= $this->getConfigData ( 'backurl_error' );
		$this->backurlCancel		= $this->getConfigData ( 'backurl_cancel' );
		$this->merchant			= $this->getConfigData ( 'merchant' );
		$this->currency			= $this->getConfigData ( 'currency' );
		$this->destination		= $this->getConfigData ( 'destination' );
		$this->installments		= 0;
// 		Mage::log('backurl: ');
// 		Mage::log(print_r($this->backurlOk , true));
// 		Mage::log('testmode: ');
// 		Mage::log(print_r($this->testmode , true));
		if ($this->testmode) {
//			$this->currency	    = '032';
			//$this->destination 		= 'http://argentina.local.rebatenetworks.net/firstdata/standard/ok';
//			$this->min 			= 'D051160019';
//			$this->secret_key 	= 'O2L670Q9NA2sWZ1N9IQTLDUOOJ20ER2KIGR4RF7UFA557H40O8Z1EBY9O08R0HWPO';
		}
		$this->configRead = true;
		return;
	}
	
	public function getTestmode() {
// 	    Mage::log('get testmode');
	    return $this->testmode;
	}
	
	public function getMerchant () {
	    return $this->merchant;
	}

	public function getInstallments () {
		return $this->installments;
	}
	
	public function getDestination () {
	    return $this->destination;
	}
	
	public function getBackurlOk () {
		return $this->backurlOk;
	}
	
	public function getBackurlCancel () {
		return $this->backurlCancel;
	}
	
	public function getBackurlError () {
		return $this->backurlError;
	}
	
	public function validatePayment($orderId) {
		$this->readConfig ();
		$result = array ('orderNr' => $orderId, 'payment' => 'failed', 'auto' => false );
		
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
	
	/**
	debug function, html formatted
	 */
	function d($var, $echo = true) {
		$r = '<pre>';
		$r .= htmlspecialchars ( print_r ( $var, true ) );
		$r .= '</pre>';
		if ($echo) {
			echo $r;
		}
		return $r;
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
		parent::authorize ( $payment, $amount );
		return $this;
	}
	
	/**
    Url, where the user will be redirected in order to start the payment.
	 */
	public function getOrderPlaceRedirectUrl() {
		$redirectUrl = Mage::getUrl ( 'firstdata/standard/start/', array ('_secure' => true ) );
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
	 * @param   Varien_Object $invoic_epayment
	 * @return  Mage_Payment_Model_Abstract
	 */
	public function void(Varien_Object $payment) {
		return $this;
	}
	
	public function hmac($algo,$data,$passwd){
	    /* md5 and sha1 only */
	    $algo=strtolower($algo);
	    $p=array('md5'=>'H32','sha1'=>'H40');
	    if(strlen($passwd)>64) $passwd=pack($p[$algo],$algo($passwd));
	    if(strlen($passwd)<64) $passwd=str_pad($passwd,64,chr(0));

	    $ipad=substr($passwd,0,64) ^ str_repeat(chr(0x36),64);
	    $opad=substr($passwd,0,64) ^ str_repeat(chr(0x5C),64);

	    return($algo($opad.pack($p[$algo],$algo($ipad.$data))));
	}
}

?>
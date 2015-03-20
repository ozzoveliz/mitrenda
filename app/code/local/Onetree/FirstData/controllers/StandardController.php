<?php

/**
 * Banklink Standard Checkout Controller
 *
 * @category   Mage
 * @package    Onetree_FirstData
 * @author     Jens Freudenau <jens@freude-now.de>
 */
class Onetree_FirstData_StandardController extends Mage_Core_Controller_Front_Action {

    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder() {
	if ($this->_order == null) {

	}
	return $this->_order;
    }

    protected function _expireAjax() {
	if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
	    $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
	    exit;
	}
    }

    /**
     * Get the payment instance according to order
     */
    public function getPayment($code = 'firstdata') {
	return Mage::getSingleton($code . '/payment');
    }

    /**

      On index and error redirect user to the front page.

     */
    public function indexAction() {
	$this->_redirect('index');
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    private function _getCheckoutSession() {
	return Mage::getSingleton('checkout/session');
    }

    /**
      Starts the payment, redirects user to the bank.
     */
    public function startAction() {

        $session	= Mage::getSingleton('checkout/session');
        $payment	= $this->getPayment();
        $order		= Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
        $orderData	= $order->getData();
        $secret		= $payment->getSecret();
        $min		= $payment->getMin();

        /*[START] check amount depending of the business of the client*/
        $amount     = number_format(round($orderData['grand_total'],0),2,",","");
        //$amount     = str_replace(",", "", $amount);
        // $amount		= round($orderData['grand_total'],0);
        /*[END]*/

        $backurlOk	= $payment->getBackurlOk();
        $backurlCancel	= $payment->getBackurlCancel();
        $backurlError	= $payment->getBackurlError();
        $merchant	= $payment->getMerchant();
        $destination	= $payment->getDestination();
        $invoice	= $orderData['increment_id'];
        $currency	= $orderData['order_currency_code'];
        $installments	= (isset($orderData[Onetree_FirstData_Model_Payment::INSTALLMENT_ATTRIBUTE_CODE]))? $orderData[Onetree_FirstData_Model_Payment::INSTALLMENT_ATTRIBUTE_CODE] : 1;
        $email		= $orderData['customer_email'];
    //858 = PesosUruguayos
    //032 = PesosArgentinos
    //840 = DólaresEstadounidenses
        switch ($currency) {
            case 'ARS':
            $currencyCode = '032';
            break;
            case 'UYU':
            $currencyCode = '858';
            break;

            default:
            $currencyCode = '840';
            break;
        }

        /*[START] get default value if is a guest order*/
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $storeId = Mage::app()->getStore()->getStoreId();
        $isconsfinal = Mage::getStoreConfig('billofsale/billofsalenumber_group/customer_type',$storeId);

        if($customer) {
            $group_id       = $customer->getGroupId();
            $customerGroup  = Mage::getModel('customer/group')->load($group_id);
            $isconsfinal    = $customerGroup->getCustomerGroupIsEndUser();
        }
        /*[END] is final customer*/
        /*[START] patch financial law*/
        if($isconsfinal){

            //importe grabado importe total menos IVA
            $importe_grabado    = $order->getBaseSubtotal() + $order->getBaseShippingAmount();
            $importe_grabado    = number_format($importe_grabado,2,"","");
            $nrofactura         = $order->getBillofsaleNumber();
            /*[END] finacial law*/
        }

        /*if($payment->getTestmode()){
            $merchant = 20001600;
            $destination = 'http://46.30.80.142:8080/mbsapp-ntlp-test/servlet/MerBuyServlet';
        }*/

        if($payment->getTestmode()){
            // new info. shared from FirstData
            $merchant = 30001300;
            //$destination = 'http://46.30.80.142:8080/mbsapp-ntlp-test/servlet/MerBuyServlet';
            // new destination test
            $destination = 'http://64.34.173.183:8080/mbsapp-ntlp-testmm/servlet/MerBuyServlet';


        }

        $post = '';
        $post .= '<form method="post" action="' . $destination . '"  name="formk" id="formk">';
        $post .='<input name=MERCHANT type=hidden value="' . $merchant . '">
                <input name=CURRENCY type=hidden value="' . $currencyCode . '">
                <input name=AMOUNTEXP10 type=hidden value="0">
                <input name=FAILUREURL type=hidden value="' . $backurlError . '">
                <input name=SUCCESSURL type=hidden value="' . $backurlOk . '">
                <input name=CANCELURL type=hidden value="' . $backurlCancel . '">
                <input name=ORDERNUMBER type=hidden size=9 value="' . substr($invoice, -9) . '">
                <input name=AMOUNT type=hidden size=12 value="' . $amount . '">
                <input name=INSTALLMENTS type=hidden  size=2 value="' . $installments . '">
                <input name=EMAILTH type=hidden size=20 value="' . $email . '">';

        /*[START] financial law*/
        /*MASTERCARD only for now*/
        $post .='<input name=BINCC type=hidden  size=1 value="000001">
                 <input name=IFAPLICA type=hidden  size=1 value="' . $isconsfinal . '">
                 <input name=IFFAC type=hidden  size=20 value="' . $nrofactura . '">
                 <input name=IFIMP type=hidden  size=12 value="' . $importe_grabado . '">';
        /*[END]*/
        $post .='</form>';
        $post .= '<script type="text/javascript">' . "\r\n";
        $post .= "<!--\r\n";
        $post .= "
                    function subForm() {
                        var frm = document.getElementById(\"formk\");
                    frm.submit();
                    }
                    window.onload = subForm;";
        $post .= "// -->\r\n";
        $post .= '</script>' . "\r\n";
        $post .= "</html>";
        //		$this->_helper->viewRenderer->setNoRender();

        Mage::log($post,null,'send_master.log');

            $this->getResponse()->setHeader('Content-Type', 'text/html');
        $this->getResponse()->appendBody($post);
        //		$this->_helper->layout->disableLayout();
        //    	$redirectUrl = $result['destination']."?".substr($r, 1);
        //    	header("Location: ".$redirectUrl);
        //    	exit();
    }

    /**
      Validates the payment and if payment is successful, then the user is notified.
      If payment is cancelled, then this method also cancels payment.
      On any error, user is redirected to the front page.

     */
    public function cancelAction() {
        $status = 'cancel';
        $orderId = $this->_getCheckoutSession()->getLastRealOrderId();
        $orderId = $orderId;
        $payment = $this->getPayment();
        $result = $payment->validatePayment($this->_getCheckoutSession()->getLastRealOrderId());
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $arrayData = Mage::app()->getRequest()->getParams();

        if (strtolower($status) == 'cancel') {
	    if ($order->getId()) {
		    $order->cancel()->save();
            if(is_array($arrayData) && count($arrayData) > 0){
                $message = "Error al pagar con FirstData. Orden: ".$arrayData['ORDERNUMBER'] . "\n" . "Code: [".$arrayData['ResponseCode']."] : ".$arrayData['ResponseText'];
            }else{
                $message = 'Hubo un problema con la orden';
            }
            $order->addStatusToHistory(true,$message);
            $order->save();
	    }
	}
	//redirect
//	$this->_redirect('checkout/cart');
	$this->_redirect('checkout/onepage/failure');
    }

    public function errorAction() {
        $status = 'error';
        $orderId = $this->_getCheckoutSession()->getLastRealOrderId();
        $payment = $this->getPayment();
        $result = $payment->validatePayment($this->_getCheckoutSession()->getLastRealOrderId());
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $arrayData = Mage::app()->getRequest()->getParams();
    //	$orderId = '4'.$orderId;
        if (strtolower($status) == 'error') {
            if ($order->getId()) {
                $order->cancel()->save();
                if(is_array($arrayData) && count($arrayData) > 0){
                    $message = "Error al pagar con FirstData. Orden: ".$arrayData['ORDERNUMBER'] . "\n" . "Code: [".$arrayData['ResponseCode']."] : ".$arrayData['ResponseText'];
                }else{
                    $message = 'Hubo un problema con la orden';
                }
                $order->addStatusToHistory(true,$message);
                $order->save();
            }
        }
        $this->_redirect('checkout/onepage/failure',array("_secure"=>true));
    }

    public function okAction() {

        $status = 'ok';
        $orderId = $this->_getCheckoutSession()->getLastRealOrderId();
        $payment = $this->getPayment();
        $result = $payment->validatePayment($this->_getCheckoutSession()->getLastRealOrderId());
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $arrayData = Mage::app()->getRequest()->getParams();

        try{
            if ($order->getId() && $order->getTotalPayd() == 0) {

                $invoice = $order->prepareInvoice();
                $invoice->register()->capture();
                //	    $invoice->sendEmail(true, $this->__('the bill was paid'));
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();
                $order->save();

                if(is_array($arrayData) && count($arrayData) > 0){
                    $message = "Orden success, codigo de autorización : ".$arrayData['APPROVALCODE'];
                    $order->addStatusToHistory(true,$message);
                    $order->save();
                }

                $this->_redirect('checkout/onepage/success', array('_secure' => true));
                return;
            } else {
                $order->addStatusToHistory($order->getStatus(), __('manual payment:') . ' ' . $orderId, false);
                $order->save();
            }
        }catch (Exception $e){
            /*something get wrong*/
        }
        $this->_redirect('checkout/onepage/failure',array("_secure"=>true));
    }

    public function endAction() {

	if (!$this->getRequest()->isPost()) {
	    Mage::log('end Action: post is empty');
	    $this->_redirect('');
	    return;
	}
	$post = $this->getRequest()->getPost();
	$debug = $post['debug'];
	$payment = $this->getPayment();
	$payment->readConfig();

	$encoded = $post['encoded'];
	$checksum = $post['checksum'];
	$hmac = $payment->hmac('sha1', $encoded, $payment->getSecret());

	if ($debug) {
	    Mage::log('Debug Mode');
	    Mage::log(print_r('checksum: ' . $checksum, true));
	    Mage::log(print_r($hmac, true));
	    Mage::log(print_r('post :' . $post, true));
	}

	if ($hmac == $checksum) {

	    $data = base64_decode($encoded);

	    if ($debug) {
		$data.= "\nINVOICE=" . $post['invoice'] . ":STATUS=PAID:PAY_TIME=20100929150130:STAN=123456:BCODE=123abc";
		Mage::log(print_r('data: ' . $data, true));
	    }

	    $lines_arr = explode("\n", $data);

	    if ($debug) {
		Mage::log(print_r('linesArr:' . $lines_arr, true));
	    }

	    foreach ($lines_arr as $key => $line) {

		if (preg_match("/^INVOICE=(\d+):STATUS=(PAID|DENIED|EXPIRED)(:PAY_TIME=(\d+):STAN=(\d+):BCODE=([0-9a-zA-Z]+))?$/", $line, $regs)) {
		    if ($debug) {
			Mage::log(print_r('regs: ' . $regs, true));
		    }
		    $orderId = $regs[1];
		    $status = strtolower($regs[2]);
		    $pay_date = $regs[4]; # XXX if PAID
		    $stan = $regs[5]; # XXX if PAID
		    $bcode = $regs[6]; # XXX if PAID
		}
		Mage::log('preg string failed in standard/endAction');
	    }

	    $order = Mage::getModel('sales/order');
	    $order->loadByIncrementId($orderId);

	    $newOrderStatus = Mage::getStoreConfig('payment/first_data/order_status', $order->getStoreId());
	    if (empty($newOrderStatus)) {
		$newOrderStatus = $order->getStatus();
	    }
	    switch ($status) {
		case 'paid' :
		    $order->addStatusToHistory('ok', __('The payment is authorized by FirstData. Order:') . ' ' . $orderId, true);
		    $order->getPayment()->setTransactionId($stan);
		    $invoice = $order->prepareInvoice();
		    #$invoice->register()->capture();
		    Mage::getModel('core/resource_transaction')
			    ->addObject($invoice)
			    ->addObject($invoice->getOrder())
			    ->save();
		    if ($order->getStatus() == Mage_Sales_Model_Order::STATE_COMPLETE) {
			$order->setState(
				Mage_Sales_Model_Order::STATE_COMPLETE, true, Mage::helper('FirstData')->__('Invoice #%s created; FirstData transaction id: %s', $invoice->getIncrementId(), $stan), $notified = true
			);
		    } else {
			$order->setState(
				Mage_Sales_Model_Order::STATE_PROCESSING, $newOrderStatus, Mage::helper('FirstData')->__('Invoice #%s created; FirstData transaction id: %s', $invoice->getIncrementId(), $stan), $notified = true
			);
		    }
		    $order->save();
		    break;

		case 'denied':
		    $order->addStatusToHistory('err', __('Error sended by FirstData. Order:') . ' ' . $orderId, true);
		    $order->save();
		    break;

		case 'expired':
		    $order->addStatusToHistory('expired', __('The payment is canceld. Order:') . ' ' . $orderId, true);
		    $order->cancel()->save();
		    break;
	    }
	} else {
	    Mage::log("ERR: Not valid CHECKSUM\n"); # XXX The description of error is REQUIRED
	}
    }

    public function oldendAction() {
	$id = $this->getRequest()->getParam('id');
	$payment = $this->getPayment();
	$res = $_REQUEST;
	$result = $payment->validatePayment($_REQUEST);

	if ($result['payment'] == 'cancelled') {
	    $order = Mage::getModel('sales/order')->loadByIncrementId($id);
	    if ($order->getId()) {
		$order->cancel()->save();
	    }
	    //redirect
	    $this->_redirect('checkout/cart');
	} else if ($result['payment'] == 'success') {
	    //mark the order payd
	    $order = Mage::getModel('sales/order')->loadByIncrementId($result['orderNr']);
	    if ($order->getId()) {

		if ($result['auto'] && $order->getTotalPayd() == 0) {
		    $order->addStatusToHistory($result['status'], 'Tellimuse eest tasutud: ' . $result['orderNr'], true);
		    $order->save();
		    $invoice = $order->prepareInvoice();
		    $invoice->register()->capture();
		    $invoice->sendEmail(true, $this->__('Arve on makstud'));
		    Mage::getModel('core/resource_transaction')
			    ->addObject($invoice)
			    ->addObject($invoice->getOrder())
			    ->save();
		    $order->save();
		    $order->sendOrderUpdateEmail(true, 'Tellimuse eest tasutud: ' . $result['orderNr']);
		    $order->sendNewOrderEmail();
		} else {
		    $order->addStatusToHistory($order->getStatus(), 'manual banklink payment: ' . $result['orderNr'], false);
		    $order->save();
		}
	    }
	    $this->_redirect('checkout/onepag	e/success', array('_secure' => true));

	    //redirect
	} else {
	    //finally redirect to index
	    $this->_redirect('');
	}
    }

}

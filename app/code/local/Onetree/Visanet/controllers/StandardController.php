<?php

/**
 * Banklink Standard Checkout Controller
 *
 * @category   Mage
 * @package    Onetree_Visanet
 * @author     Felipe Carrera <carreraf@adinet.com.uy>
 */
class Onetree_Visanet_StandardController extends Mage_Core_Controller_Front_Action {

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
    public function getPayment($code = 'visanet') {
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
        $session = Mage::getSingleton('checkout/session');
        $this->getResponse()->setBody($this->getLayout()->createBlock('visanet/standard_redirect')->toHtml());
    }

    public function responseAction() {
        // exit('hola');
        $session = Mage::getSingleton('checkout/session');

        $orderId = $session->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        try{
            if ($this->getLayout()->createBlock('visanet/standard_response')->evalResponse($order)) {
                $this->_redirect('checkout/onepage/success',array("_secure"=>true));
                return $this;
            } else {

                //$orderId = $this->_getCheckoutSession()->getLastRealOrderId();
                //$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

                if ($order->getId()) {
                    $order->cancel()->save();
                }
            }
        }catch (Exception $e){
            Mage::logException($e);
            $order->addStatusHistoryComment($e->getMessage());
            if ($order->getId()) {
                $order->cancel()->save();
            }
        }

        $this->_redirect('checkout/onepage/failure',array("_secure"=>true));
    }

    public function helloAction() {
        echo "Hello";
    }

}

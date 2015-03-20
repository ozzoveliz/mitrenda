<?php

/**
 * Banklink Standard Checkout Controller
 *
 * @category   Mage
 * @package    Onetree_Ocauru
 * @author     Felipe Carrera <carreraf@adinet.com.uy>
 */
class Onetree_Ocauru_StandardController extends Mage_Core_Controller_Front_Action {

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
            Mage::log('llego aca');

            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit;
        }
    }

    /**
     * Get the payment instance according to order
     */
    public function getPayment($code = 'ocauru') {
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
        try
        {
            $this->getResponse()->setBody($this->getLayout()->createBlock('ocauru/redirect')->toHtml());
        }catch (Exception $e){
            $orderId = $session->getLastRealOrderId();
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            Mage::logException($e);
            if ($order->getId()) {
                $order->addStatusHistoryComment($e->getMessage());
                $order->cancel()->save();
            }
            $this->_redirect('checkout/onepage/failure',array("_secure"=>true));
        }

    }

    public function responseAction() {
//        exit('hola');
        $session = Mage::getSingleton('checkout/session');

        $orderId = $session->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        try{
            if ($this->getLayout()->createBlock('ocauru/response')->evalResponse($order)) {
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
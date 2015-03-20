<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 6/17/14
 * Time: 6:23 PM
 */ 
class Onetree_Checkout_Block_Checkout_Onepage_Success extends Mage_Checkout_Block_Onepage_Success
{
    /**
     * Get last order ID from session, fetch it and check whether it can be viewed, printed etc
     */
    protected function _prepareLastOrder()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()) {
                $isVisible = !in_array($order->getState(),
                    Mage::getSingleton('sales/order_config')->getInvisibleOnFrontStates());
                $this->addData(array(
                    'is_order_visible' => $isVisible,
                    'view_order_id' => $this->getUrl('sales/order/view/', array('order_id' => $orderId)),
                    'print_url' => $this->getUrl('sales/order/print', array('order_id'=> $orderId)),
                    'can_print_order' => $isVisible,
                    'can_view_order'  => Mage::getSingleton('customer/session')->isLoggedIn() && $isVisible,
                    'order_id'  => $order->getIncrementId(),
                    'order'		=> $order
                ));
            }
        }
    }
}
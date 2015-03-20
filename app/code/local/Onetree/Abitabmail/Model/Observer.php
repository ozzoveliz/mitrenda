<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 7/8/14
 * Time: 3:31 AM
 */
class Onetree_Abitabmail_Model_Observer
{
    public function changeOrderStatus($observer){
        /*After the payment is confirmed, manually the order need to change to processing */
        $order = $observer->getOrder();
        $orderPayment = $observer->getOrder()->getPayment();

        if(($orderPayment->getMethod() == Onetree_Abitabmail_Model_Method_Mail::ABITABMAIL) &&
            ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT)){

            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING,true);
            $order->save();

        }
        return $this;
    }

}
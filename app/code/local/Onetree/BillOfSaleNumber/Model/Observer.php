<?php

class Onetree_BillOfSaleNumber_Model_Observer {

    public function invoice_increment_bill_number(Varien_Event_Observer $observer) {

        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        
        if(is_null($invoice->getBillofsaleNumber()) && $order->getPayment()->hasMethodInstance() &&
            ($order->getPayment()->getMethodInstance()->getCode() == 'abitabmail'
                || $order->getPayment()->getMethodInstance()->getCode() == 'checkmo')) 
        {

            $billofsale = $this->addBillNumber();
            $invoice->setBillofsaleNumber($billofsale)->save();
            $order->setBillofsaleNumber($billofsale)->save();
        }

        return $this;
    }

    /**
    * Si el método de pago es alguna de las tarjetas
    * incremento Bill of Sales y lo agrego a la orden
    * para luego usar cuando se envía a la procesadora los parametros
    */
    public function order_increment_bill_number(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $payment = $order->getPayment();

        if(is_null($order->getBillofsaleNumber()) && $payment->hasMethodInstance() 
            && Onetree_BillOfSaleNumber_Model_Billofsalenumber::canAddNumber($payment)) {

            $billofsale = $this->addBillNumber();
            $order->setBillofsaleNumber($billofsale); 
        }

        return $this;
    }

    private function addBillNumber() {
        $billofsale = Mage::getResourceModel('billofsalenumber/billofsalenumber_collection');
        /*[START] could be much better how to get first item of a collection and save data*/
        $billofsale = $billofsale->getFirstItem();
        $number = $billofsale->getBillNumber();
        $billNumber = $number;

        if(is_numeric($number)) {
            $number =  Mage::helper('billofsalenumber')->checkBillFormat($number+1);
            $billofsale->setData('bill_number',$number);
            $billofsale->save();
        }
        /*[END]*/
        return $billNumber;
    }
}
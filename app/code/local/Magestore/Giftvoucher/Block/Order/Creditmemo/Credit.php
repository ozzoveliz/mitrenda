<?php

class Magestore_Giftvoucher_Block_Order_Creditmemo_Credit extends Mage_Core_Block_Template {

    public function initTotals() {
        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getCreditmemo();
        if ($order->getUseGiftCreditAmount() && $order->getUseGiftCreditAmount() > 0) {
            $orderTotalsBlock->addTotal(new Varien_Object(array(
                'code' => 'giftcardcredit',
                'label' => $this->__('Gift Card credit'),
                'value' => -$order->getUseGiftCreditAmount(),
                    )), 'subtotal');
        }
    }

}

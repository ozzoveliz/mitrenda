<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.5.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Rma_Model_Observer
{
    public static function removeSessionData()
    {
        Mage::getSingleton('customer/session')->getAWRMAFormData(true);
        Mage::getSingleton('customer/session')->getAWRMACommentFormData(true);
    }

    /**
     * Replace view order page template in customer account for adding link
     * Request RMA
     *
     * @return null
     */
    public static function setOrderInfoTemplate()
    {
        if (!Mage::getSingleton('core/layout')->getBlock('sales.order.info')) {
            return;
        }
        if (Mage::helper('awrma')->checkExtensionVersion('Mage_Core', '0.8.25')) {
            $_template = 'aw_rma/sales/order/info.phtml';
        } else {
            $_template = 'aw_rma/sales/order/info13x.phtml';
        }
        Mage::getSingleton('core/layout')->getBlock('sales.order.info')->setTemplate($_template);
    }
}

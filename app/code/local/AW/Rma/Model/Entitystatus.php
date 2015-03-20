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


class AW_Rma_Model_Entitystatus extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('awrma/entitystatus');
    }

    /**
     * Load status by name
     * @param string $name
     * @return AW_Rma_Model_Entitystatus
     */
    public function loadByName($name)
    {
        $this->load($name, 'name');
        return $this;
    }

    protected function _beforeSave()
    {
        if (is_array($this->getStore())) {
            $this->setStore(implode(',', $this->getStore()));
        }
    }

    protected function _afterLoad()
    {
        if (is_string($this->getStore())) {
            $this->setStore(explode(',', $this->getStore()));
        }
        $this->storeTemplateFromRma(Mage::app()->getStore()->getId());
    }

    public function getName()
    {
        $_helper = Mage::helper('awrma');
        return $_helper->__($this->getData('name'));
    }

    public function storeTemplateFromRma($storeId)
    {
        $template = Mage::getModel('awrma/status_template')
                ->loadByStatusAndStore($this->getId(), $storeId)
        ;
        if ($template) {
            if ($template->getData('name')) {
                $this->setData('name', $template->getData('name'));
            }
            $this->setData('to_customer', $template->getData('to_customer'));
            $this->setData('to_admin', $template->getData('to_admin'));
            $this->setData('to_chatbox', $template->getData('to_chatbox'));
        }
        return $this;
    }
}
<?php
/**
 * Yireo TrashCan for Magento
 *
 * @package     Yireo_TrashCan
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 * @link        http://www.yireo.com/
 */

class LUKA_GoogleAdWords_Model_Observer
{
    public function addTrackCode($observer){

        if(Mage::app()->getStore()->getId() == Mage_Core_Model_App::ADMIN_STORE_ID) return $this;

        $block          = $observer->getBlock();
        $layout         = $block->getLayout();
        $nameInLayout   = $block->getNameInLayout();

        if(!is_null($layout) && ($layout->getArea() == Mage_Core_Model_App_Area::AREA_FRONTEND && $nameInLayout == 'before_body_end')){

            $customAction = Mage::getSingleton('customer/session')->getCustomAction();
            if( !is_null($customAction)){

                $blockHtml = $observer->getTransport()->getHtml();
                $track = $layout->createBlock('luka_googleaw/conversion');
                $track->setAttribute('custom_action',$customAction);
                $html = $track->setTemplate('luka/google/adwords/custom_conversion.phtml')->toHtml();

                $observer->getTransport()->setHtml($blockHtml.$html);

                Mage::getSingleton('customer/session')->unsetData('custom_action');
            }
        }elseif(!is_null($layout) && ($layout->getArea() == Mage_Core_Model_App_Area::AREA_FRONTEND && $nameInLayout == 'head')){
            $customAction = Mage::getSingleton('customer/session')->getCustomAction();
            $blockHtml = $observer->getTransport()->getHtml();
            /*[START] FB-conversion code*/
            if($customAction == 'newsletter_subscriber_save_after'){
                $fbNewsletterNewsubscribe = $layout->createBlock('core/template')->setTemplate('luka/facebook/newsletter/new-register.phtml')->toHtml();
                $html = $blockHtml.$fbNewsletterNewsubscribe;
                $observer->getTransport()->setHtml($html);
            }
            /*[END] FB-conversion code*/
        }

        return $this;
    }

    public function addStatusNewsletterSubscribe($observer){
        $subscribe  = $observer->getSubscriber();
        $status     = $subscribe->getStatus();
        $obj        = $observer->getEvent()->getData();
        $name = $obj['name'];

        if($status == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED){
            Mage::getSingleton('customer/session')->setData('custom_action',$name);
        }
        return $this;
    }

    public function addCustomerRegistration($observer){
        $obj        = $observer->getEvent()->getData();
        $name = $obj['name'];
        Mage::getSingleton('customer/session')->setData('custom_action',$name);
    }


    /*
     * Method that is thrown with the event "model_delete_before"
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Yireo_TrashCan_Model_Observer
     */
    public function modelDeleteBefore($observer)
    {
        // Fetch the event-object
        $currentObject = $observer->getEvent()->getObject();
        $currentResourceClass = str_replace('/','_', $currentObject->getResourceName());

        // Check if this object is supported
        $supportedModels = Mage::helper('trashcan')->getSupportedModels();
        if(array_key_exists($currentResourceClass, $supportedModels) == false) {
            return $this;
        }

        // Check the configuration whether trash-can is enabled
        $config = Mage::helper('trashcan')->setting('enable_'.$currentResourceClass);
        if($config != 1) {
            return $this;
        }
        
        // Create a new object
        $trashObject = Mage::getModel('trashcan/object');
        if($trashObject->loadFromObject($currentObject)) {
            $trashObject->save();
        } else {
			Mage::getSingleton('adminhtml/session')->addError('Unable to create trashcan-object');
        }

        // Return nothing
        return null;
    }
}

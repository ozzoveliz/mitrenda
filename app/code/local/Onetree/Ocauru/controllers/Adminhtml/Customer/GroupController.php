<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') .DS. 'Customer/GroupController.php');

class Onetree_Ocauru_Adminhtml_Customer_GroupController extends Mage_Adminhtml_Customer_GroupController
{
    /**
     * Create or save customer group.
     */
    public function saveAction()
    {
        $customerGroup = Mage::getModel('customer/group');
        $id = $this->getRequest()->getParam('id');
        if (!is_null($id)) {
            $customerGroup->load((int)$id);
        }

        $taxClass = (int)$this->getRequest()->getParam('tax_class');

        if ($taxClass) {
            try {
                $customerGroupCode = (string)$this->getRequest()->getParam('code');

                if (!empty($customerGroupCode)) {
                    $customerGroup->setCode($customerGroupCode);
                }

                $isEndUser = (int)$this->getRequest()->getParam('is_end_user');
                $customerGroup->setCustomerGroupIsEndUser($isEndUser);

                $customerGroup->setTaxClassId($taxClass)->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customer')->__('The customer group has been saved.'));
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group'));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCustomerGroupData($customerGroup->getData());
                $this->getResponse()->setRedirect($this->getUrl('*/customer_group/edit', array('id' => $id)));
                return;
            }
        } else {
            $this->_forward('new');
        }
    }
}

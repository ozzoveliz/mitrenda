<?php

class Onetree_Ocauru_Block_Adminhtml_Customer_Group_Edit_Form extends Mage_Adminhtml_Block_Customer_Group_Edit_Form
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField('customer_group_is_end_user', 'select',
            array(
                'name'  => 'is_end_user',
                'label' => Mage::helper('customer')->__('Is End User'),
                'title' => Mage::helper('customer')->__('Is End User'),
                'class' => 'required-entry',
                'required' => true,
                'values' => array( 0 => 'No', 1 => 'Yes')
            )
        );

        $customerGroup = Mage::registry('current_group');
        if( Mage::getSingleton('adminhtml/session')->getCustomerGroupData() ) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getCustomerGroupData());
            Mage::getSingleton('adminhtml/session')->setCustomerGroupData(null);
        } else {
            $form->addValues($customerGroup->getData());
        }
    }
}
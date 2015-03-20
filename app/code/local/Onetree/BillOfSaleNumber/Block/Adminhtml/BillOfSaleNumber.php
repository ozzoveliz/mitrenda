<?php  

class Onetree_BillOfSaleNumber_Block_Adminhtml_BillOfSaleNumber extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
{
    $this->_controller = 'adminhtml_billofsalenumber';
    $this->_headerText = Mage::helper('billofsalenumber')->__('Manage Bill Of Sale Number');
    $this->_addButtonLabel = Mage::helper('billofsalenumber')->__('Add');
    parent::__construct();
}

}
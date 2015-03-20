<?php  

class Onetree_BillOfSaleNumber_Block_Adminhtml_Billofsalenumber_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_billofsalenumber';
        $this->_blockGroup = 'billofsalenumber';

        parent::__construct();
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        // For now always show first Item from the Collection
        if (Mage::registry('billofsale')->getId()) {
            return Mage::helper('billofsalenumber')->__("Edit Number");
        }
        else {
            return Mage::helper('billofsalenumber')->__('New Block');
        }
    }

}
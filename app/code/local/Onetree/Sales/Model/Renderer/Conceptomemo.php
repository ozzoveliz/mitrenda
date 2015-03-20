<?php

class Onetree_Sales_Model_Renderer_Conceptomemo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return 'N'.$row->getData('billofsale_number') .' '. $row->getBillingName();
    }
}

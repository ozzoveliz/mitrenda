<?php

class Onetree_Sales_Model_Renderer_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getId());
        $value = number_format($value, 2, '.', '');
        return $value;
    }
}

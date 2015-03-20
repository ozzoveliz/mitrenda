<?php

class Onetree_Sales_Model_Renderer_Ivamemo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $importe = $row->getData('grand_total');
        $iva = ($importe * 18.03) / 100;
        $value = number_format($iva, 2, '.', '');
        return ($value) ? "-".$value : '-0.00';
    }
}

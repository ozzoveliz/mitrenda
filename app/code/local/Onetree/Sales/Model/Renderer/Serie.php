<?php

class Onetree_Sales_Model_Renderer_Serie extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if (!Mage::registry('serie')) {
            Mage::register('serie',1);
            return Mage::registry('serie');
        }

        if (Mage::registry('serie') == 1) {
            Mage::unregister('serie');
            Mage::register('serie',2);
            return Mage::registry('serie');
        } else {
            Mage::unregister('serie');
            Mage::register('serie',1);
            return Mage::registry('serie');
        }
    }
}

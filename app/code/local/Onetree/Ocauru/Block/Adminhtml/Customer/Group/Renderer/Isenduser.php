<?php

class Onetree_Ocauru_Block_Adminhtml_Customer_Group_Renderer_Isenduser extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) {
        $value =  $row->getData($this->getColumn()->getIndex());
        return ($value)? 'Yes':'No';
    }
}
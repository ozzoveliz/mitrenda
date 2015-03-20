<?php
    class Onetree_Sales_Model_Renderer_Preciocolumngrid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    {
        public function render(Varien_Object $row)
        {
           $val = $row->getData($this->getColumn()->getIndex());
           if($row->getMethod()=='checkmo'){
               $val = round($val, 0); 
           }else{
               $val = 0; 
           }
            return $val;
        }
    }

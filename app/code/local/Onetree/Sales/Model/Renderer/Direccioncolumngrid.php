<?php
    class Onetree_Sales_Model_Renderer_Direccioncolumngrid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    {
        public function render(Varien_Object $row)
        {
          $sustituye = array("\r\n", "\n\r", "\n", "\r");
          $value = $row->getData($this->getColumn()->getIndex());
          $content = str_replace($sustituye, " ", $value);
          return $content;
        }
    }

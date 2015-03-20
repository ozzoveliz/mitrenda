<?php

class Onetree_Sales_Model_Renderer_Paymentcolumngrid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $method = $row->getMethod();
        switch($method) {
            case 'checkmo': return '1120001';
                break;
            case 'visanet': return '1120002';
                break;
            case 'abitabmail': return '1120003';
                break;
            case 'ocauru': return '1120004';
                break;
            case 'firstdata': return '1120005';
                break;
            default: return '0000000';
        }
    }
}

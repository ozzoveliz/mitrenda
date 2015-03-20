<?php

class Onetree_Sales_Model_Renderer_Value extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $defaultValue = '';

    public function __construct(array $args = array())
    {
        $this->defaultValue = $args;
        parent::__construct($args);
    }


    public function render(Varien_Object $row)
    {
        return isset($this->defaultValue[0]) ? $this->defaultValue[0] : '';
    }
}

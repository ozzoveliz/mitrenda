<?php

class Onetree_Ocauru_Model_System_Config_Source_View {

    public function toOptionArray() {
        return array(
            array('value' => 0, 'label' => Mage::helper('adminhtml')->__('No installments allowed')),
            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('1')),
            array('value' => 2, 'label' => Mage::helper('adminhtml')->__('2')),
            array('value' => 3, 'label' => Mage::helper('adminhtml')->__('3')),
            array('value' => 4, 'label' => Mage::helper('adminhtml')->__('4')),
            array('value' => 5, 'label' => Mage::helper('adminhtml')->__('5')),
            array('value' => 6, 'label' => Mage::helper('adminhtml')->__('6')),
            array('value' => 7, 'label' => Mage::helper('adminhtml')->__('7')),
            array('value' => 8, 'label' => Mage::helper('adminhtml')->__('8')),
            array('value' => 9, 'label' => Mage::helper('adminhtml')->__('9')),
            array('value' => 10, 'label' => Mage::helper('adminhtml')->__('10')),
        );
    }

    public function toArray() {
        return array(
            0 => Mage::helper('adminhtml')->__('No installments allowed'),
            1 => Mage::helper('adminhtml')->__('1'),
            2 => Mage::helper('adminhtml')->__('2'),
            3 => Mage::helper('adminhtml')->__('3'),
            4 => Mage::helper('adminhtml')->__('4'),
            5 => Mage::helper('adminhtml')->__('5'),
            6 => Mage::helper('adminhtml')->__('6'),
            7 => Mage::helper('adminhtml')->__('7'),
            8 => Mage::helper('adminhtml')->__('8'),
            9 => Mage::helper('adminhtml')->__('9'),
            10 => Mage::helper('adminhtml')->__('10'),
        );
    }
}
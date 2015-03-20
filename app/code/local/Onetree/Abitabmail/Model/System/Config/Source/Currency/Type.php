<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 7/14/14
 * Time: 10:48 PM
 */
class Onetree_Abitabmail_Model_System_Config_Source_Currency_Type {

    public function toOptionArray() {
        return array(
            array(
                'value' => '1',
                'label' => 'Pesos'
            ),
//            array(
//                'value' => '2',
//                'label' => 'Dólares'
//            ),
        );
    }
}
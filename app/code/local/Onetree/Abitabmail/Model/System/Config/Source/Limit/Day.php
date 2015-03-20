<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 7/14/14
 * Time: 10:48 PM
 */
class Onetree_Abitabmail_Model_System_Config_Source_Limit_Day {

    public function toOptionArray() {
        return array(
            array(
                'value' => '1',
                'label' => '1 day'
            ),
            array(
                'value' => '2',
                'label' => '2 days'
            ),
            array(
                'value' => '3',
                'label' => '3 days'
            ),
            array(
                'value' => '4',
                'label' => '4 days'
            ),
            array(
                'value' => '5',
                'label' => '5 days'
            ),
            array(
                'value' => '6',
                'label' => '6 days'
            ),
            array(
                'value' => '7',
                'label' => '7 days'
            )
        );
    }
}
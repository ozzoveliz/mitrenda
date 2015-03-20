<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 8/4/14
 * Time: 10:28 PM
 */ 
class Onetree_Directory_Model_Currency extends Mage_Directory_Model_Currency
{
    /**
     * Format price to currency format
     *
     * @param float $price
     * @param array $options
     * @param bool $includeContainer
     * @param bool $addBrackets
     * @return string
     */
    public function format($price, $options = array(), $includeContainer = true, $addBrackets = false)
    {
        return $this->formatPrecision($price, 0, $options, $includeContainer, $addBrackets);
    }
}
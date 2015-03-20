<?php
/**
 */
class Onetree_FirstData_Model_Payment extends Onetree_FirstData_Abstract{
	
	const INSTALLMENT_ATTRIBUTE_CODE = 'firstdata_installment';
	
	/**
	 * unique internal payment method identifier
	 *
	 * @var string [a-z0-9_]
	 */
	protected $_code = 'firstdata';
		
	public function getAttCode(){
	
		return self::INSTALLMENT_ATTRIBUTE_CODE;
	}
}

?>
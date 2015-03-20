<?php
/**
 * Created by PhpStorm.
 * User: JuanCarlos
 * Date: 1/28/2015
 * Time: 4:41 PM
 */
require_once 'Magestore/Sociallogin/controllers/PopupController.php';


class Onetree_Sociallogin_PopupController extends Magestore_Sociallogin_PopupController
{
    public function createAccAction() {
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            $result = array('success'=>false, 'Can Not Login!');
        }
        else{
            $firstName =  $this->getRequest()->getPost('firstname', false);
            $lastName =  $this->getRequest()->getPost('lastname', false);
            $pass =  $this->getRequest()->getPost('pass', false);
            $passConfirm =  $this->getRequest()->getPost('passConfirm', false);
            $email = $this->getRequest()->getPost('email', false);
            $customer = Mage::getModel('customer/customer')
                ->setFirstname($firstName)
                ->setLastname($lastName)
                ->setEmail($email)
                ->setPassword($pass)
                ->setConfirmation($passConfirm);

            if ($this->getRequest()->getPost('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            try{
                $customer->save();
                Mage::dispatchEvent('customer_register_success',
                    array('customer' => $customer)
                );
                $result = array('success'=>true);
                $session->setCustomerAsLoggedIn($customer);
                //$url = $this->_welcomeCustomer($customer);
                // $this->_redirectSuccess($url);
            }catch(Exception $e){
                $result = array('success'=>false, 'error'=>$e->getMessage());
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}
<?php
class Onetree_BillOfSaleNumber_Adminhtml_BillofsalenumberController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('billofsalenumber');
        return $this;
    }

    public function indexAction()
    {
       $this->_forward('edit');
    }

    public function editAction(){
        $this->_title($this->__('CMS'))->_title($this->__('Static Blocks'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('block_id');
        $model = Mage::getModel('billofsalenumber/billofsalenumber')->load(1);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Block'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('billofsale', $model);

        // 5. Build edit form

        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('billofsalenumber/adminhtml_billofsalenumber_edit'));
        $this->renderLayout();
    }
    public function saveAction(){

       if($this->getRequest()->getPost()){
            try {

                $id         = $this->getRequest()->getParam('id');
                $number     = $this->getRequest()->getParam('bill_number');
                $billNumber = Mage::getModel('billofsalenumber/billofsalenumber')->load($id);
                $bill       = Mage::helper('billofsalenumber')->checkBillFormat($number);
                $billNumber->setData('bill_number',$bill);

                $errors = $billNumber->validate();
                if(empty($errors)){
                    $billNumber->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('billofsalenumber')->__('Bill of Sale Number saved.'));
                }else{
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            Mage::getSingleton('adminhtml/session')->addError($errorMessage);
                        }
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPartnerData($this->getRequest()->getPost());
                $redirectBack = true;
            }

            if ($redirectBack) {
                $this->_redirect('*/*/edit', array(
                    'id'    => $this->getRequest()->getParam('id'),
                    '_current'=>true
                ));
                return;
            }

            $this->_redirect('*/*/');
            return;
        }

    }
}
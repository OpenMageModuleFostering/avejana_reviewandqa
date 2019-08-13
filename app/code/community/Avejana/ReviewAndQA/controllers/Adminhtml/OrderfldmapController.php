<?php

class Avejana_ReviewAndQA_Adminhtml_OrderfldmapController extends Mage_Adminhtml_Controller_Action{
	
	protected function _initAction() {
		
		
		$this->loadLayout()
			->_setActiveMenu('reviewandqa/reviewandqa')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Customer Manager'), Mage::helper('adminhtml')->__('Customer Manager'));
		$this->getLayout()->getBlock('head')->setTitle('Orders Fields Mapping'); 
		return $this;
	}

	protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/reviewandqa/mycustomtab/fields_mapppings');
    }
 
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}
	
	public function editAction()
    {	
    	
        $fld_id = $this->getRequest()->getParam('entity_id');
        //echo $fld_id;exit;
        $state = Mage::getModel('reviewandqa/orderfldmap')->load($fld_id);

        if ($state->getId() || $fld_id == 0) {
            $this->_initAction();
            Mage::register('reviewandqa_data', $state);
            $this->_addBreadcrumb(Mage::helper('reviewandqa')->__('Orders Fields Mapping'), Mage::helper('reviewandqa')->__('Orders Fields Mapping'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('reviewandqa/adminhtml_orderfldmap_edit'))
                ->_addLeft($this->getLayout()->createBlock('reviewandqa/adminhtml_orderfldmap_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reviewandqa')->__('Fields does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $request = $this->getRequest();

        if ($this->getRequest()->getPost()) {
           
			$id = $request->getParam('entity_id');

			$mage_field_val = $request->getParam('mage_field');
			$mage_field_val_arr = explode(" : ", $mage_field_val);
			$mage_field_of = $mage_field_val_arr[0];
			$mage_field_label = $mage_field_val_arr[1];
			$mage_field = $mage_field_val_arr[2];

			$sf_field = $request->getParam('sf_field');
			$default_val = $request->getParam('default_value');
           
            if (!$mage_field OR !$sf_field) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please fill the required fields'));
                $this->_redirect('*/*/');
                return;
            }
            
            if($id != ''){
				$hubArr = Mage::getModel('reviewandqa/orderfldmap')->getCollection()
							//->addFieldToFilter('sf_field_of',$sf_field_of)
               				->addFieldToFilter('sf_field', $sf_field)
               				->addFieldToFilter('entity_id',  array('nin' => array($id)))->getAllIds();
			}else{
				$hubArr = Mage::getModel('reviewandqa/orderfldmap')->getCollection()
							//->addFieldToFilter('sf_field_of',$sf_field_of)
               				->addFieldToFilter('sf_field', $sf_field)->getAllIds();
			}
           
		    if (count($hubArr) > 0) {
              
			 	Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('This Salesforce Field is alredy mapped'));
                $this->_redirect('*/*/edit', array('entity_id' => $id));
                return;
            }

            try {
                $state = Mage::getModel('reviewandqa/orderfldmap');
                $state->setEntityId($id)
				      ->setMageFieldOf($mage_field_of)
				      ->setMageField($mage_field)
				      ->setMageFieldLabel($mage_field_label)
                      ->setSfFieldOf($sf_field_of)
                      ->setSfField($sf_field)
                      ->setSfFieldLabel($sf_field_label)
                      ->setDefaultValue($default_val)
                      ->save();
               
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Field was successfully mapped'));
                Mage::getSingleton('adminhtml/session')->getMagerpsyncData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setStateData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('entity_id' => $this->getRequest()->getParam('entity_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    
    public function massDeleteAction()
    {
        $fldIds = $this->getRequest()->getParam('reviewandqa');
        if (!is_array($fldIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Field(s).'));
        } else {
            try {
                $state = Mage::getModel('reviewandqa/orderfldmap');
                foreach ($fldIds as $fldId) {
                    $state->load($fldId)
                        ->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($fldIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');

    }
}

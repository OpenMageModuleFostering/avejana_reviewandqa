<?php
class Avejana_ReviewAndQA_Adminhtml_QuestionsController extends Mage_Adminhtml_Controller_Action{
	const XML_PATH_EMAIL_PRODUCT_QUESTION_IDENTITY  = 'default/reviewandqa/emails/email_identity';
	const XML_PATH_EMAIL_PRODUCT_QUESTION_TEMPLATE  = 'product_qa_answer';
		
	const CONFIG_SEND_USER_EMAIL = 'reviewandqa/general/send_email';
    
    
	public function indexAction(){                
		$this->loadLayout();

		$this->_setActiveMenu('catalog/reviewandqa');
		$this->_addBreadcrumb(Mage::helper('reviewandqa')->__('Manage Product Questions'), Mage::helper('reviewandqa')->__('Manage Product Questions'));
		$this->_addContent($this->getLayout()->createBlock('reviewandqa/adminhtml_questions_list'));

		$this->renderLayout();
	}
    
	// mass action
	public function massDeleteAction(){
		$questionsIds = $this->getRequest()->getParam('questions');
		if(!is_array($questionsIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one question'));
		} else{
			try{
				$questions = Mage::getModel('reviewandqa/questions');
				foreach($questionsIds as $questionsId){
					$questions->load($questionsId)
					->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($questionsIds)
					)
				);
			} catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('*/*/index');
	}


	public function newAction(){
		$this->_forward('edit');
	}
    
	public function editAction(){       
		$this->loadLayout();
        
		$recordId  = (int) $this->getRequest()->getParam('id');
		$recordModel   = Mage::getModel('reviewandqa/questions');
		$record = $recordModel->loadExtra($recordId);
        
		if(!$record->getId()){
			$message = $this->__('Invalid record id');
			Mage::getSingleton('adminhtml/session')->addError($message);
			$this->_redirect('*/*/');
			return;
		}        
        
		Mage::register('questions', $record);
		Mage::register('current_questions', $record);

		$this->_setActiveMenu('catalog/reviewandqa');
		$this->_addBreadcrumb(Mage::helper('reviewandqa')->__('Manage Product Question and Answers'), Mage::helper('reviewandqa')->__('Manage Product Question and Answers'));

		$this->_addContent($this->getLayout()->createBlock('reviewandqa/adminhtml_questions_edit'));
		$this->_addLeft($this->getLayout()->createBlock('reviewandqa/adminhtml_questions_edit_tabs'));
		$this->renderLayout();
	}

    

	public function saveAction(){

		if($this->getRequest()->getPost()){
			try{
				$recordModel = Mage::getModel('reviewandqa/questions')->load($this->getRequest()->getParam('id'));
				$answeredOnDate = Mage::getModel('reviewandqa/questions')->getResource()->formatDate(time());
              
				$record_data = $this->getRequest()->getPost('record');
                
				$recordModel->setAnswer($record_data['answer']);   
				$recordModel->setStatus($record_data['status']);
				$recordModel->setAnsweredOn($answeredOnDate);         
				$recordModel->save();
             
             
				// --------------------- SEND EMAIL TO POSTER
                
				$sendEmailToPoster = Mage::getStoreConfig(self::CONFIG_SEND_USER_EMAIL);
                
				if($sendEmailToPoster){                
					$emailData = array();
					$emailData['to_email'] = $record_data['email'];
					$emailData['to_name'] = $record_data['name'];
					$emailData['email'] = array(
						'product_name' => $record_data['product_name'],
						'store_id' => $recordModel->getStoreId(),
						'store_name' => $record_data['store_name'],
						'question' => $record_data['question'],
						'answer' => $record_data['answer'],
						'customer_name' => $record_data['name'],
						'date_posted' => Mage::helper('core')->formatDate($recordModel->getCreatedOn(), 'long'), 
					); 
					$result = $this->sendEmail($emailData);
		            
					if(!$result){
						Mage::throwException($this->__('Cannot send email'));
					}
				}                               
         
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reviewandqa')->__('The record has been successfully saved'));
				Mage::getSingleton('adminhtml/session')->setRecordData(false);

				$this->_redirect('*/*/');
				return;
			} catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setRecordData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}

	public function deleteAction(){
		if( $this->getRequest()->getParam('id') > 0 ){
			try{
				$recordModel = Mage::getModel('reviewandqa/questions');
                
				$recordModel->setId($this->getRequest()->getParam('id'))
				->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reviewandqa')->__('The record has been successfully deleted'));
				$this->_redirect('*/*/');
			} catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
   
    
	private function sendEmail($data){	
		
		$storeID = $data['email']['store_id'];
		
		$translate = Mage::getSingleton('core/translate');
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);

		$result = Mage::getModel('core/email_template')
		->setDesignConfig(array('area' => 'frontend', 'store' => $storeID));
        
		$result->sendTransactional(
			self::XML_PATH_EMAIL_PRODUCT_QUESTION_TEMPLATE,
			Mage::getConfig()->getNode(self::XML_PATH_EMAIL_PRODUCT_QUESTION_IDENTITY),
			$data['to_email'],
			$data['to_name'],
			$data['email'],
			$storeID
		);
		//$result->getProcessedTemplate($data);       

		$translate->setTranslateInline(true);
        
		return $result;
	}
    
}    

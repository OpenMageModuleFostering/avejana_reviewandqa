<?php
class Avejana_ReviewAndQA_Block_Adminhtml_Questions_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        
        $fieldset_question = $form->addFieldset('questions_form_question', array(
            'legend'=>Mage::helper('reviewandqa')->__('Question (readonly)')
        ));
        
        $fieldset_question->addField('name', 'text', array(
            'name'      => 'record[name]',
            'label'     => Mage::helper('reviewandqa')->__('Name'),
            'readonly' => true,
        ));

        $fieldset_question->addField('email', 'text', array(
            'name'      => 'record[email]',
            'label'     => Mage::helper('reviewandqa')->__('Email'),
            'readonly' => true,

        ));
        
         $fieldset_question->addField('product_name', 'text', array(
            'name'      => 'record[product_name]',
            'label'     => Mage::helper('reviewandqa')->__('Product'),
            'readonly' => true,

        ));
        
         $fieldset_question->addField('store_name', 'text', array(
            'name'      => 'record[store_name]',
            'label'     => Mage::helper('reviewandqa')->__('Store'),
            'readonly' => true,

        ));
        
        $fieldset_question->addField('created_on', 'date', array(
            'name'      => 'record[created_on]',
            'label'     => Mage::helper('reviewandqa')->__('Posted On'),
            'readonly' => true,
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
        ));
        
         $fieldset_question->addField('question', 'textarea', array(
            'name'      => 'record[question]',
            'label'     => Mage::helper('reviewandqa')->__('Question'),
            'readonly' => true,
        ));
        
        
       
        
       
        
        $fieldset_question->addField('status', 'select', array(
                'label'     => Mage::helper('reviewandqa')->__('Status'),
                'name'      => 'record[status]',
                'value'		=> 'public',   
                'values'    => array(
                	array('value'=>'public','label'=>'public'),
                	array('value'=>'hidden','label'=>'hidden')
                ),
                'readonly' => true,
            ));
        
        $fieldset_answer = $form->addFieldset('questions_form_answer', array(
            'legend'=>Mage::helper('reviewandqa')->__('Answer')
        ));    

        $fieldset_answer->addField('answer', 'textarea', array(
            'name'      => 'record[answer]',
            'label'     => Mage::helper('reviewandqa')->__('Answer'),
            'class'     => 'required-entry',
            'required'  => true,
        ));

		if(Mage::getSingleton('adminhtml/session')->getRecordData()){	
		    $record = Mage::getSingleton('adminhtml/session')->getRecordData();		
        	$form->setValues($record['record']);
        	Mage::getSingleton('adminhtml/session')->setRecordData(false);
        } elseif(Mage::registry('questions')) {
            $form->setValues(Mage::registry('questions')->getData());
        }
        return parent::_prepareForm();
    }

    

    protected function _toHtml()
    {
        return parent::_toHtml();
    }


}

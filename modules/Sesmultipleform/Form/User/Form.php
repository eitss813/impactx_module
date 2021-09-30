<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Form.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Form_User_Form extends Engine_Form
{
	protected $_defaultProfileId;
	protected $_formId;
	protected $_widgetId;
	protected $_formSettings;
	public function getWidgetId()
	{
		return $this->_widgetId;
	}
	public function setWidgetId($widgetId)
	{
		$this->_widgetId = $widgetId;
		return $this;
	}
	public function getFormId()
	{
		return $this->_formId;
	}
	public function setFormId($form_id)
	{
		$this->_formId = $form_id;
		return $this;
	}
	public function getFormSettings()
	{
		return $this->_formSettings;
	}
	public function setFormSettings($form_settings)
	{
		$this->_formSettings = $form_settings;
		return $this;
	}
	public function getDefaultProfileId() {
		return $this->_defaultProfileId;
	}
	public function setDefaultProfileId($default_profile_id) {
		$this->_defaultProfileId = $default_profile_id;
		return $this;
	}
	public function init()
	{
		$formObj = Engine_Api::_()->getItem('sesmultipleform_form',$this->getFormId());
		$formSetting = $this->getFormSettings();
		$widgetId = $this->getWidgetId();			
		$emailId = '';
		$viewer_name = '';
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity()) {
			$viewer_name = ucfirst($viewer->displayname);
			$emailId = $viewer->email;
		}	
		$description = $this->getTranslator()->translate(nl2br($formObj->heading_description));
    $this->loadDefaultDecorators();
	  $this->getDecorator('Description')->setOption('escape', false);
		 $this
					->setTitle($formObj->heading_text)
					->setDescription($description)
					->setAttrib('enctype','multipart/form-data')
					->setAttrib('class', 'global_form sesmultipleform_create_'.$this->getWidgetId());
		
			$this->addElement('Text', 'first_name_'.$widgetId, array(
				'label' => $formObj->label_name,
				'allowEmpty' => false,
				'required' => true,
					 'filters' => array(
						new Engine_Filter_Censor(),
						new Engine_Filter_HtmlSpecialChars(),
				),
				'value'=>$viewer_name,
		));
		$this->addElement('Text', 'email_'.$widgetId, array(
			'label' => $formObj->label_email,
			'required' => true,
			'allowEmpty' => false,
			'validators' => array(
				'EmailAddress'
			),
				 'filters' => array(
						new Engine_Filter_Censor(),
						new Engine_Filter_HtmlSpecialChars(),
				),
				'value'=>$emailId,
		));
		
		 $categories = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->getCategory(array('column_name' => '*','id'=>$this->getFormId()));
			$data[''] = 'Choose a Category';
			foreach ($categories as $category) {
				$data[$category['category_id']] = $category['title'];
				$categoryId = $category['category_id'];
			}
			 if (count($data) > 2) {
			 if($formObj->category_required){
					$requiredcat = true;	
					$allowEmptycat = false;
				}else{
					$requiredcat = false;	
					$allowEmptycat = true;	
				}
				//Add Element: Category
				$this->addElement('Select', 'category_id_'.$widgetId, array(
						'label' => $formObj->label_category,
						'allowEmpty' => $allowEmptycat,
						'required' => $requiredcat,
						'multiOptions' => $data,
						'onchange' => "showSubCategory_".$this->getWidgetId()."(this.value);showFields_".$this->getWidgetId()."(this.value,1);",
				));

				//Add Element: Sub Category
				$this->addElement('Select', 'subcat_id_'.$widgetId, array(
						'label' => $formObj->label_subcategory,
						'allowEmpty' => true,
						'required' => false,
						'registerInArrayValidator' => false,
						'onchange' => "showSubSubCategory_".$this->getWidgetId()."(this.value);",
				));
				//Add Element: Sub Sub Category
				$this->addElement('Select', 'subsubcat_id_'.$widgetId, array(
						'label' => $formObj->label_subsubcategory,
						'allowEmpty' => true,
						'registerInArrayValidator' => false,
						'required' => false,
						'onchange' => 'showCustom_'.$this->getWidgetId().'(this.value);',
				));
			}
			elseif(count($data) == 2)
			{
				//Add Element: Category
				$this->addElement('Select', 'category_id_'.$widgetId, array(
						'label' => $formObj->label_category,
						'allowEmpty' => true,
						'required' => false,
						'multiOptions' => $data,
						'value' => $categoryId,
				));
			}
			$defaultProfileId = "0_0_" . $this->getDefaultProfileId();
			$customFields = new Sesmultipleform_Form_Custom_Fields(array(
					'item' => 'sesmultipleform_entry',
					'widget'=>$widgetId,
					'decorators' => array(
							'FormElements'
			)));
			$customFields->removeElement('submit');
			if ($customFields->getElement($defaultProfileId)) {
				$customFields->getElement($defaultProfileId)
								->clearValidators()
								->setRequired(false)
								->setAllowEmpty(true);
			}
			$this->addSubForms(array(
					'fields' => $customFields
			));
		
		
		if($formObj->description)
		{
			if($formObj->description_required){
					$requireddes = true;	
					$allowEmptydes = false;
			}else{
					$requireddes = false;	
					$allowEmptydes = true;	
			}	
			$this->addElement('textarea', 'description_'.$widgetId, array(
					'label' => $formObj->label_description,
					'required' => $requireddes,
					'allowEmpty' => $allowEmptydes,
					'filters' => array(
					new Engine_Filter_Censor(),
					new Engine_Filter_StringLength(array('max' => '400')),
					new Engine_Filter_Html(array('AllowedTags'=> array('a'))),
					),
			 ));
		}
		if ($formSetting->display_file_upload && $viewer->getIdentity() != 0) {
			if($formSetting->display_file_upload_required){
					$fileUploadReq = true;	
					$fileUploadEmpty = false;
				}else{
					$fileUploadReq = false;	
					$fileUploadEmpty = true;	
				}	
				 $ex_type = '';
			$uploadtype = $formSetting->file_upload;
			if ($uploadtype == 1)
				$ex_type = 'bmp, jpg, png, psd, jpeg';
			elseif ($uploadtype == 2)
				$ex_type = 'pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, bmp, jpg, png, psd, jpeg';
			elseif ($uploadtype == 0)
				$ex_type = 'pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx';
			$this->addElement('File', 'file_'.$widgetId, array(
					'label' => $formObj->label_file_upload,
					'description' => 'Allowed Extention ( '.$ex_type.' )',
					'required' => $fileUploadReq,
					'allowEmpty' => $fileUploadEmpty,	
			));
			$this->{'file_'.$widgetId}->addValidator('Extension', false, $ex_type);
		}		
		
		//Show captcha
		$showcaptcha = $formSetting->show_captcha;
		if ($showcaptcha && !Engine_Api::_()->user()->getViewer()->getIdentity()) {
			$this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions(array(
				//'tabindex' => $tabIndex++,
			)));
		}
		if ($formSetting->send_copy) {
			// SEND COPY TO ME
			$this->addElement('Checkbox', 'sesmultipleform_copymain_'.$widgetId, array(
					'label' => "Send a copy of this on my email address.",
			));
		}
		$this->addElement('Hidden', 'captchValue', array(
			'required' => false,
			'allowEmpty' => true,
			'order' => 999,
		));
		if ($formSetting->enable_terms) {
			// Element: terms
			$description = Zend_Registry::get('Zend_Translate')->_('I have read and agree to the <a target="_blank" href="%s/help/terms">terms of service</a>.');
			$description = sprintf($description, Zend_Controller_Front::getInstance()->getBaseUrl());

			$this->addElement('Checkbox', 'terms_'.$widgetId, array(
					'label' => 'Terms of Service',
					'description' => $description,
					'required' => true,
					'validators' => array(
							'notEmpty',
							array('GreaterThan', false, array(0)),
					),
			));
			$this->{'terms_'.$widgetId}->getValidator('GreaterThan')->setMessage('You must agree to the terms of service to continue.', 'notGreaterThan');
			$this->{'terms_'.$widgetId}->clearDecorators()
							->addDecorator('ViewHelper')
							->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'label', 'class' => 'null', 'escape' => false, 'for' => 'terms_'.$widgetId))
							->addDecorator('DivDivDivWrapper');
		}
					// Submit
		$this->addElement('Button', 'submit', array(
			'label' => $formObj->label_submit,
			'ignore' => true,
			'type' => 'submit'
		));
	}
}
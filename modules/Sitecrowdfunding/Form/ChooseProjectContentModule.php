<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ChooseProjectContentModule.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_ChooseProjectContentModule extends Engine_Form {
	protected $_item; 

    public function getItem() {
        return $this->_item;
    } 

    public function setItem($item) {
        $this->_item = $item;
        return $this;
    }

    public function init() {

        $itemTitle = $this->getItem()->title; 
        $itemModule = ucfirst($this->getItem()->getMediaType());
        $module = explode('_', $this->getItem()->getType())[0];
        $moduleName = explode('_', $this->getItem()->getType())[0].$this->getItem()->getIdentity();
        if($module == 'sitereview') {
            $moduleName = $module."_".$this->getItem()->listingtype_id.$this->getItem()->getIdentity();
        } 
        
        $this->setTitle('Choose a Project') 
            ->setAttrib('id', 'choose-project')
            ->setAttrib('name', 'choose_project')
            ->setAttrib('enctype', 'multipart/form-data');
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);
        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array())); 
        $coreSettings = Engine_Api::_()->getApi('settings', 'core'); 
     
        $projectTitle = ''; 
        $ownerSelectedProject = $coreSettings->getSetting($moduleName.".choosed.project", 0); 
        if($ownerSelectedProject) {
            $projectTitle = Engine_Api::_()->getItem('sitecrowdfunding_project', $ownerSelectedProject)->title;
        } 
        $this->addElement('Text', 'project_ids', array( 
            'label' => 'Select Project', 
            'description' => "Below are the list of your projects. You can only associate one of your project with the $itemTitle. Once, a project gets associated then ‘Donate Now’ button will appear on the profile page of your $itemTitle $itemModule.<br>[Note: At a time, only one project can be linked with one $itemModule but you can link one project with multiple from the dashboard of respective item. ]",
            'autocomplete' => 'off', 
            'placeholder' => 'Start typing project name...',
            'value' => $projectTitle
        ));
        Engine_Form::addDefaultDecorators($this->project_ids);
        $this->project_ids->getDecorator('Description')->setOption('escape', false);
 
        $this->addElement('Hidden', $moduleName."_choosed_project", array(
            'label' => '',
            'order' => 1,
            'filters' => array(
                'HtmlEntities'
            ),
            'value' => $coreSettings->getSetting($moduleName.".choosed.project", 0)
        )); 
  
        $this->addElement('Text', $moduleName."_back_project_label", array(
            'label' => '‘Donate Now’ Button Text',
            'description' => 'You can change the text of ‘Donate Now’ button like: Donations, Fund Now etc. as per your requirement. [Note: If you don’t enter any text then ‘Donate Now’ will be used by default.]', 
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '128')),
            ),
            'value' => $coreSettings->getSetting($moduleName.".back.project.label", 'Donate Now')
        )); 

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        )); 
    }
}
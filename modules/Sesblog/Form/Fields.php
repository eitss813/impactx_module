<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Fields.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Fields extends Engine_Form
{
  protected $_defaultProfileId;

  public function getDefaultProfileId() {
    return $this->_defaultProfileId;
  }

  public function setDefaultProfileId($default_profile_id) {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }

  public function init() {
 		if (Engine_Api::_()->core()->hasSubject('sesblog_blog'))
    	$sesblog = Engine_Api::_()->core()->getSubject();
		
		
    $this->setTitle('Custom Fields Entry')
      ->setDescription('Edit custom fields below, then click "Post Form" to publish the entry on your blog.')->setAttrib('name', 'sesblogs_fields');
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

		
      // General form w/o profile type
      $aliasedFields = $sesblog->fields()->getFieldsObjectsByAlias();
      $topLevelId = $topLevelId = 0;
      $topLevelValue = $topLevelValue = null;

      if (isset($aliasedFields['profile_type'])) {
				$aliasedFieldValue = $aliasedFields['profile_type']->getValue($sesblog);
				$topLevelId = $aliasedFields['profile_type']->field_id;
				$topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
				if (!$topLevelId || !$topLevelValue) {	
					$topLevelId = null;
					$topLevelValue = null;
				}
				$topLevelId = $topLevelId;
				$topLevelValue = $topLevelValue;
      }
      // Get category map form data
      $defaultProfileId = "0_0_" . $this->getDefaultProfileId();
      $customFields = new Sesblog_Form_Custom_Dashboardfields(array(
				'item' => Engine_Api::_()->core()->getSubject(),
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
		 
    
  // Element: execute
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }
}

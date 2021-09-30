<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Join.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitepagemember_Form_Join extends Engine_Form	{

  public function init() {
      // By Questwalk
  $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
                
    $formmappingTable = Engine_Api::_()->getDbtable('formmappings', 'impactx');
  
                // End
    $this->setTitle('Join Page')
       ->setAction("dynamic-form/entry/join-org-role-form/1/user_id/" . $viewer_id ."/is_role/1")
       ->setMethod('GET')     
         ->setDescription('Would you like to join this page?');


    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id',null);

		if (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'pagemember.title', 1)) {
		
			//$roles = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRolesAssoc($page_id);
// 			if (!empty($roles)) {
// 				asort($roles, SORT_LOCALE_STRING);
// 				$roleOptions = array('0' => '');
// 				foreach( $roles as $k => $v ) {
// 					$roleOptions[$k] = $v;
// 				}
// 				
// 				$this->addElement('Select', 'role_id', array(
// 					'label' => 'ROLE',
// 					'multiOptions' => $roleOptions,
// 				));
// 			}
			
			$roles = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRolesAssoc($page_id);
			$rolesAssocWithForm = $formmappingTable->getAllMappingFormRole();
                        
                        if (!empty($roles)) {
				$roleKey = array();
                                $role_name[0] = ''; 
				foreach ($roles as $k => $role) {  
                                    
                                    // If role not associated with any form or form has not field escape it
                                    if(!in_array($k,$rolesAssocWithForm) || $formmappingTable->formHasNofiled($k))
                                        continue;
                                   
                                            
					$role_name[$k] = $role;
					$roleKey[] = $k;
				}
				reset($role_name);

//				$this->addElement('Multiselect', 'role_id', array(
//					'label' => 'ROLE',
//					'multiOptions' => $role_name,
//					'value' => $roleKey,
//				));
                                
                                   $this->addElement('Select', 'role_id', array(
                                        'label' => 'ROLE',
                                        'allowEmpty' => false,
                                        'required' => true,
                                        'multiOptions' => $role_name,
//                                        'value' =>$roleKey
                                    ));
      }
		}

    if (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'pagemember.date', 1)) {
      $curYear = date('Y');
      $year = array('Year');

      for ($i = 0; $i <= 110; $i++) {
        $year[$curYear] = $curYear;
        $curYear--;
      }
      
      $this->addElement('Dummy', 'date', array(
         'label' => 'MEMBER_DATE',
      ));
      
      $this->addElement('Select', 'year', array(
				//'label' => 'MEMBER_DATE',
				'allowEmpty' => false,
				'required' => true,
				'multiOptions' => $year,
				'value' => date('Y')
      ));

      $months = array('Month');
      for ($x = 1; $x <= 12; $x++) {
        $months[] = date('F', mktime(0, 0, 0, $x));
      }

      $this->addElement('Dummy', 'addmonth', array(
          'description' => "<a href='javascript:void(0);' onclick ='showMonth(0);' onblur='setTimeMonth();' >" . Zend_Registry::get('Zend_Translate')->_('+Add Month') . "</a>",
      ));
      $this->getElement('addmonth')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));
      $this->addElement('Select', 'month', array(
				//'label' => 'Month',
				'allowEmpty' => true,
				'required' => false,
				'attribs' => array('style' => 'display:none;'),
				'multiOptions' => $months,
				'onblur' => 'showAddmonth(2);',
				'onclick' => "showMonth(1);",
				'onchange' => "showAddday(2);"
      ));

      $this->addElement('Dummy', 'addday', array(
          'description' => "<a href='javascript:void(0);' onclick ='showDay(0);' onblur='setTime();' id='addday' style='display:none;' >" . Zend_Registry::get('Zend_Translate')->_('+Add Day') . "</a>",
      ));
      $this->getElement('addday')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

      $day = array('Day');
      for ($x = 1; $x <= 31; $x++) {
        $day[] = $x;
      }

      $this->addElement('Select', 'day', array(
				'allowEmpty' => true,
				'required' => false,
				'attribs' => array('style' => 'display:none;'),
				'multiOptions' => $day,
				'onblur' => 'showAddday(2);',
				'onclick' => "showDay(1);",
				'onchange' => "showAddday(2);"
      ));
    }

    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons');


    // $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->setMethod('POST');
  }
}

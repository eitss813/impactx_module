<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitepagemember_Form_Edit extends Engine_Form {

  public function init() {
  
    $this->setTitle('Edit Membership')
      ->setAttrib('id', 'group_form_title');

    $member_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('member_id',null);
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id',null);

    $table = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $tablename = $table->info('name');
    $select = $table->select()
						->from($table->info('name'), array('title', 'date'))
						->where($tablename . '.member_id = ?', $member_id);
		$result = $table->fetchRow($select)->toArray();

		$resultDate = explode('-',$result['date']);
		
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
			if (!empty($roles)) {
				$roleKey = array();
				foreach ($roles as $k => $role) {  
					$role_name[$k] = $role;
					$roleKey[] = $k;
				}
				reset($role_name);

				$this->addElement('Multiselect', 'role_id', array(
					'label' => 'ROLE',
					'multiOptions' => $role_name,
					'value' => $roleKey,
				));
      }
		}
    
		$YY = $resultDate['0']; 
		$MM = isset($resultDate['1']) ? $resultDate['1'] : '';
		$DD = isset($resultDate['2']) ? $resultDate['2'] : '';
		
    $display = 'display:block;';
    $modisplay = 'display:none;';
		if (empty($YY)) { $YY = ''; $display = 'display:none;'; }
		if (empty($MM)) { $MM = array('Month'); $modisplay = 'display:block;'; }
		if (empty($DD)) { $DD = array('Day'); }
		
		
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
          'value' => $YY
      ));

      $months = array('Month');
      for ($x = 1; $x <= 12; $x++) {
        $months[] = date('F', mktime(0, 0, 0, $x));
      }


      $this->addElement('Dummy', 'addmonth', array(
          'description' => "<a href='javascript:void(0);' onclick ='showMonth(0);'  id='addmonth' style='$modisplay' >" . Zend_Registry::get('Zend_Translate')->_('+Add Month') . "</a>",
      ));
      $this->getElement('addmonth')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));
      $this->addElement('Select', 'month', array(
          //'label' => 'Month',
          'allowEmpty' => true,
          'required' => false,
          'attribs' => array('style' => $display),
          'multiOptions' => $months,
          'onblur' => 'showAddmonth(2);',
          'onclick' => "showMonth(1);",
          'onchange' => "showAddday(2);",
          'value' => $MM
      ));

      $this->addElement('Dummy', 'addday', array(
          'description' => "<a href='javascript:void(0);' onclick ='showDay(0);'  id='addday' style='display:none;' >" . Zend_Registry::get('Zend_Translate')->_('+Add Day') . "</a>",
      ));
      $this->getElement('addday')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

      $day = array('Day');
      //$num = cal_days_in_month(CAL_GREGORIAN,$MM,$YY);
      for ($x = 1; $x <= 31; $x++) {
        $day[] = $x;
      }

      $this->addElement('Select', 'day', array(
          'allowEmpty' => true,
          'required' => false,
          'attribs' => array('style' => $display),
          'multiOptions' => $day,
          'onblur' => 'showAddday(2);',
          'onclick' => "showDay(1);",
          'onchange' => "showAddday(2);",
          'value' => $DD
      ));
    }
    
    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Save Changes',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'onclick' => 'parent.Smoothbox.close();',
      'prependText' => ' or ',
      'link' => true,
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
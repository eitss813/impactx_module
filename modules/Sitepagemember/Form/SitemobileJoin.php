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
 
class Sitepagemember_Form_SitemobileJoin extends Engine_Form	{

  public function init() {
  
    $this->setTitle('Join Page')
         ->setDescription('Would you like to join this page?');

    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id',null);

		if (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'pagemember.title', 1)) {
		
			$roles = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRolesAssoc($page_id);
			if (!empty($roles)) {
				asort($roles, SORT_LOCALE_STRING);
				$roleOptions = array('0' => '');
				foreach( $roles as $k => $v ) {
					$roleOptions[$k] = $v;
				}
				
				$this->addElement('Select', 'role_id', array(
					'label' => 'ROLE',
					'multiOptions' => $roleOptions,
					'value' => 0
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
				'value' => '2013'
      ));

      $months = array('Month');
      for ($x = 1; $x <= 12; $x++) {
        $months[] = date('F', mktime(0, 0, 0, $x));
      }

      $this->addElement('Select', 'month', array(
				//'label' => 'Month',
				'allowEmpty' => true,
				'required' => false,
				'multiOptions' => $months,
      ));

      $day = array('Day');
      for ($x = 1; $x <= 31; $x++) {
        $day[] = $x;
      }

      $this->addElement('Select', 'day', array(
				'allowEmpty' => true,
				'required' => false,
				'multiOptions' => $day,
      ));
   
   $this->addDisplayGroup(array('year', 'month', 'day'), 'select', array(
        'decorators' => array(
            'FormElements',
            array('HtmlTag', array('data-role' => 'controlgroup', 'data-type' => 'horizontal', 'data-corners' => 'false', 'data-mini' => 'true' )),
        ),
    )); 

    }
       
    $this->addElement('Button', 'submit', array(
      'label' => 'Join Page',
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


    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->setMethod('POST');
  }
}
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetForm.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Form_WidgetForm extends Engine_Form {

  public function init() {
  
    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    
    $table = Engine_Api::_()->getDbTable('pages', 'sitestaticpage');
    $select  = $table->select()->where('page_url =?', '');
    $static_pages = $table->fetchAll($select);
    
    if (count($static_pages) != 0) {
      $staticpages_prepared[0] = "";
      foreach ($static_pages as $page) {
          $staticpages_prepared[$page->page_id] = $page->title;
      }
    }

    $this->addElement('Text', 'title', array(
        'label' => 'Title',
    ));

    $this->addElement('Select', 'static_pages', array(
        'label' => 'Choose Content',
        'required' => true,
        'allowEmpty' => false,
        'multiOptions' => $staticpages_prepared,
        'value' => 0,
    ));
    
    $this->addElement('dummy', 'start_end_msg', array(
        'description' => '<div class="tip"><span>While editing this widget, you may have to again choose its Start Time and End Time as the dates could have automatically reset to the date on which you are editing this widget.</span></div>',
            )
    );
    $this->start_end_msg->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    //START TIME
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Time");
    $start->setAllowEmpty(false);
    $start->setValue(date('Y-m-d H:i:s'));
    $this->addElement($start);

    //END TIME
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time [Leave this empty if you want this widget to be permanently visible.]");
    $this->addElement($end);

    $this->addElement('Dummy', 'pricingHeading', array(
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => 'application/modules/Sitestaticpage/views/scripts/_calender.tpl'
                  ))),
     ));
	
    $this->addElement('Text', 'width', array(
        'label' => 'Block Content Width (in px). [Leave blank, if you want the width to be automatically set.]',
    ));

    $this->addElement('Text', 'height', array(
        'label' => 'Block Content Height (in px). [Leave blank, if you want the height to be automatically set.]',
    ));
  }

}
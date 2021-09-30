<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Locationedit.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
class Sesblog_Form_Locationedit extends Engine_Form {

  public function init() {
    $action = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    if ($action['action'] == 'edit-location')
      $this->setTitle('Edit Location')->setDescription('Choose the location.');
    else
      $this->setTitle('Add Location')->setDescription('Choose the location.');

    if ($action['action'] == 'edit-location') {
      $this->addElement('Text', 'ses_edit_location', array(
          'label' => 'Location',
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_HtmlSpecialChars(),
          ),
      ));
      $this->addElement('Text', 'ses_lat', array(
          'label' => 'Latitude',
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_HtmlSpecialChars(),
          )
              )
      );
      $this->addElement('Text', 'ses_lng', array(
          'label' => 'Longitude',
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_HtmlSpecialChars(),
          )
              )
      );
      $this->addElement('Text', 'ses_zip', array(
          'label' => 'Zipcode',
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_HtmlSpecialChars(),
          )
              )
      );
      $this->addElement('Text', 'ses_city', array(
          'label' => 'City',
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_HtmlSpecialChars(),
          )
              )
      );
      $this->addElement('Text', 'ses_state', array(
          'label' => 'State',
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_HtmlSpecialChars(),
          )
              )
      );
      $this->addElement('Text', 'ses_country', array(
          'label' => 'Country',
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_HtmlSpecialChars(),
          )
              )
      );

      $this->addDisplayGroup(array(
          'ses_edit_location',
          'ses_lat',
          'ses_lng',
          'ses_zip',
          'ses_city',
          'ses_state',
          'shortURL',
          'ses_country',
              ), 'input_fields', array(
          'decorators' => array(
              'FormElements',
              'DivDivDivWrapper'
          ),
      ));
      if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1)) {
        $this->addElement('Dummy', 'map-canvas-div', array(
            'content' => '<div id="locationSesEdit" style="height:260px;"></div>'
        ));
      }
    } else {
      $this->addElement('Text', 'ses_location', array(
          'label' => 'Location',
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_HtmlSpecialChars(),
          ),
      ));
      $this->addElement('Hidden', 'ses_lat', array(
          'order' => 9995,
      ));
      $this->addElement('Hidden', 'ses_lng', array(
          'order' => 9996,
      ));
      $this->addElement('Hidden', 'ses_zip', array(
          'order' => 9997,
      ));
      $this->addElement('Hidden', 'ses_city', array(
          'order' => 9998,
      ));
      $this->addElement('Hidden', 'ses_state', array(
          'order' => 9999,
      ));
      $this->addElement('Hidden', 'ses_country', array(
          'order' => 10000,
      ));
    }
    $sunmitText = 'Save Location';
    if ($action['action'] == 'add-location') {
      $sunmitText = 'Add Location';
    }
    $this->addElement('Button', 'submit', array(
        'label' => $sunmitText,
        'type' => 'submit',
        'ignore' => true,
    ));
    if ($action['action'] != 'edit-location') {
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'onclick' => 'parent.Smoothbox.close();',
          'decorators' => array(
              'ViewHelper',
          ),
      ));
      $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }
  }

}

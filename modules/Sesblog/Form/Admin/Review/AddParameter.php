<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: AddParameter.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
class Sesblog_Form_Admin_Review_AddParameter extends Engine_Form {

  public function init() {

    $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', 0);
    
   	$reviewParameters = Engine_Api::_()->getDbtable('parameters', 'sesblog')->getParameterResult(array('category_id'=>$category_id));
   	
    $this->setMethod('post');
    
    if(count($reviewParameters)) {

			foreach($reviewParameters as $val){
				$this->addElement('Text', 'sesblog_review_'.$val['parameter_id'], array(
          'label' => '',
					'class'=>'sesblog_added_parameter',
          'allowEmpty' => true,
					'value'=>$val['title'],
          'required' => false,
          'maxlength' => "255",
      	));	
			}
		}
		
	  $this->addElement('Dummy', 'addmore', array('content'=> '<div><input type="text" name="parameters[]" value="" class="reviewparameter"><a href="javascript:;" class="removeAddedElem fa fa-trash">Remove</a></div><a href="javascript:;" id="addmoreelem" class="fa fa-plus">Add more parameters</a>'));
		
    $this->addElement('Hidden', 'deletedIds',array('order'=>999));
    $this->addElement('Button', 'submit', array(
        'label' => 'Add',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

     $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}

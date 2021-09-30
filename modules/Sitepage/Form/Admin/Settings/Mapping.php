<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Mapping.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Sitepage_Form_Admin_Settings_Mapping extends Engine_Form
{
  public function init()
  {
		$this
		->setTitle('Delete Category')
		->setDescription('Please select atleast one category to map already created pages with it.(If any)');

    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

		$category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories(null);
    if (count($categories) != 0) {
      $categories_prepared[0] = "";
      foreach ($categories as $category) {
				if($category_id != $category->category_id) {
					$categories_prepared[$category->category_id] = $category->category_name;
				}
      }

			$this->addElement('Select', 'new_category_id', array(
					'label' => 'Category',
					'multiOptions' => $categories_prepared
			));
		}

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
			'onclick' => 'javascript:parent.Smoothbox.close()',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
	}

}
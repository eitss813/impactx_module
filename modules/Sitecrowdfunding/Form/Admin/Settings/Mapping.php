<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mapping.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Settings_Mapping extends Engine_Form {

    public function init() {

        $this->setTitle('Delete Category')
                ->setDescription('If you want to map Projects belongs to this category, with other category then select the new category.');

        $this->addElement('dummy', 'message', array(
            'description' => '<div class="tip"><span>Note: If you do not map projects belonging to this category with any other, then projects associated with this category will also be deleted from your site. This data is not recoverable.</span></div>',
        ));
        $this->message->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

        $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
        $categories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
        if (count($categories) != 0) {
            $categories_prepared[0] = "";
            foreach ($categories as $category) {
                if ($category_id != $category->category_id) {
                    $categories_prepared[$category->category_id] = $category->category_name;
                }
            }

            $this->addElement('Select', 'new_category_id', array(
                'label' => 'Category',
                'multiOptions' => $categories_prepared
            ));
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Delete',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'onclick' => 'javascript:closeSmoothbox()',
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
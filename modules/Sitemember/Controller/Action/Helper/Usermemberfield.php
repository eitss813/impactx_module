<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Usermemberfield.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Controller_Action_Helper_Usermemberfield extends Zend_Controller_Action_Helper_Abstract {

  function postDispatch() {

    //GET NAME OF MODULE, CONTROLLER AND ACTION
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $view = $this->getActionController()->view;

    //ADD USER PRIVACY FIELDS AT FIELD CREATION AND EDITION
    if (($module == 'user') && ($action == 'field-create' || $action == 'heading-edit' || $action == 'field-edit') && ($controller == 'admin-fields')) {

      $new_element = $view->form;
      if (!$this->getRequest()->isPost() || (isset($view->form) && (!$view->form->isValid($this->getRequest()->getPost())))) {
        $new_element->addElement('Select', 'member', array(
            'label' => 'Show on all widgets of Advanced Members Plugin except the search widget. [Note: For search widget, please use above "Show on Browse Members Page?" setting.]',
            'multiOptions' => array(
                1 => 'Show in such Widgets',
                0 => 'Hide in such Widgets'
            )
        ));
        if ($front->getRequest()->getParam('field_id')) {
          $field = Engine_Api::_()->fields()->getField($front->getRequest()->getParam('field_id'), 'user');
          $new_element->member->setValue($field->member);
        }
        $new_element->buttons->setOrder(999);
      } else {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->update('engine4_user_fields_meta', array('member' => $_POST['member']), array('field_id = ?' => $view->field['field_id']));
      }
    }
  }

}
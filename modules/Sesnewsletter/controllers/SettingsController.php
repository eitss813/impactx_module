<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: SettingsController.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_SettingsController extends Core_Controller_Action_User
{
  protected $_user;

  public function init()
  {
    // Can specifiy custom id
    $id = $this->_getParam('id', null);
    $subject = null;
    if( null === $id )
    {
      $subject = Engine_Api::_()->user()->getViewer();
      Engine_Api::_()->core()->setSubject($subject);
    }
    else
    {
      $subject = Engine_Api::_()->getItem('user', $id);
      Engine_Api::_()->core()->setSubject($subject);
    }

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setAuthParams(
      $subject,
      null,
      'edit'
    );

    // Set up navigation
    // $this->view->navigation = $navigation = Engine_Api::_()
    //   ->getApi('menus', 'core')
    //   ->getNavigation('user_settings', ( $id ? array('params' => array('id'=>$id)) : array()));

    $contextSwitch = $this->_helper->contextSwitch;
    $contextSwitch
      //->addActionContext('reject', 'json')
      ->initContext();

    // Render
    $this->_helper->content
        // ->setNoRender()
        ->setEnabled();
  }

  public function newsletterSettingsAction() {

    $subject = Engine_Api::_()->core()->getSubject();

    $this->view->form = $form = new Sesnewsletter_Form_NewsletterSettings(array(
      'item' => $subject->getIdentity()
    ));

    // Check request
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    //Process form
    $subscribers = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter');
    $db = $subscribers->getAdapter();

    $getResults = $subscribers->getResult(array('fetchAll' => '1', 'resource_type' => 'user', 'resource_id' => $subject->getIdentity(), 'email' => $subject->email));

    // Save
    $db->beginTransaction();

    try {

        foreach ($getResults as $getResult) {
            $subscribers->delete(array('subscriber_id = ?' => $getResult['subscriber_id']));
        }

        foreach($values['newsletter_types'] as $type) {
            $subscribers->insert(array(
                'resource_id' => $subject->getIdentity(),
                'resource_type' => 'user',
                'email' => $subject->email,
                'enabled' => '1',
                'level_id' => $subject->level_id,
                'displayname' => $subject->getTitle(),
                'type_id' => $type,

            ));
        }
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
  }
}

<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSubscriptionController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_AdminSubscriptionController extends Core_Controller_Action_Admin {
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_settings_subscription');

    $this->view->form = $form = new Sitecoretheme_Form_Admin_Subscribermail();
    // let the level_ids be specified in GET string

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'sitecoretheme');
    $emails = $subscriptionTable->getEmailList();

    // temporarily enable queueing if requested
    $temporary_queueing = Engine_Api::_()->getApi('settings', 'core')->core_mail_queueing;
    if( isset($values['queueing']) && $values['queueing'] ) {
      Engine_Api::_()->getApi('settings', 'core')->core_mail_queueing = 1;
    }

    $mailApi = Engine_Api::_()->getApi('mail', 'core');

    $mail = $mailApi->create();
    $mail
      ->setFrom($values['from_address'], $values['from_name'])
      ->setSubject($values['subject'])
      ->setBodyHtml(nl2br($values['body']))
    ;

    if( !empty($values['body_text']) ) {
      $mail->setBodyText($values['body_text']);
    } else {
      $mail->setBodyText(strip_tags($values['body']));
    }

    foreach( $emails as $email ) {
      $mail->addTo($email);
    }

    $mailApi->send($mail);

    $mailComplete = $mailApi->create();
    $mailComplete
      ->addTo(Engine_Api::_()->user()->getViewer()->email)
      ->setFrom($values['from_address'], $values['from_name'])
      ->setSubject('Mailing Complete: ' . $values['subject'])
      ->setBodyHtml('Your email blast to your members has completed.  Please note that, while the emails have been
        sent to the recipients\' mail server, there may be a delay in them actually receiving the email due to
        spam filtering systems, incoming mail throttling features, and other systems beyond SocialEngine\'s control.')
    ;
    $mailApi->send($mailComplete);

    // emails have been queued (or sent); re-set queueing value to original if changed
    if( isset($values['queueing']) && $values['queueing'] ) {
      Engine_Api::_()->getApi('settings', 'core')->core_mail_queueing = $temporary_queueing;
    }

    $this->view->form = null;
    $this->view->status = true;
  }

  public function subscriberListAction() {
      $page = $this->_getParam('page', 1);
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_settings_subscription');

      $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'sitecoretheme');
      $selectSubscribers = $subscriptionTable->select();

      // Make paginator
      $this->view->paginator = $paginator = Zend_Paginator::factory($selectSubscribers);
      $this->view->paginator = $paginator->setCurrentPageNumber( $page );
    }

}
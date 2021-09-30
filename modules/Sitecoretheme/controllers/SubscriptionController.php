<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SubscriptionController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_SubscriptionController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $email = $request->getParam('email');

    if( empty($email) ) {
      $this->view->resp = false;
      $this->view->msg = 'Please enter valid email address.';
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'sitecoretheme');
    if( $subscriptionTable->isSubscribed($email) ) {
      $this->view->resp = false;
      $this->view->msg = 'You have already subscribed.';
      return;
    }

    $subscription = $subscriptionTable->createRow();
    $subscription->email = $email;
    if( !empty($viewer) ) {
      $subscription->user_id = $viewer->getIdentity();
    }
    $subscription->save();

    $this->view->resp = true;
    $this->view->msg = 'Thank you for subscribing.';
    return;

  }

}
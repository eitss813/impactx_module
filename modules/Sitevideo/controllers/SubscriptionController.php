<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SubscriptionController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_SubscriptionController extends Seaocore_Controller_Action_Standard {

    public function init() {
        //SET THE SUBJECT
        if (0 !== ($channel_id = (int) $this->_getParam('channel_id')) &&
                null !== ($channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($channel);
        }
    }

    /*
     * This action used to subscribe the channel
     */

    public function subscribeChannelAction() {

        //Checking for "Subscription" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $sitevideoSubscription = Zend_Registry::isRegistered('sitevideoSubscription') ? Zend_Registry::get('sitevideoSubscription') : null;
        if (empty($sitevideoSubscription) || !Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        }

        $message = Zend_Registry::get('Zend_Translate')->_("Subscribed successfully.");
        $subscriptionCount = 0;
        //FIND THE CHANNEL MODEL
        $channel = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $data = array();
        if( !$channel->canView($viewer) ) {
          $data[] = array(
              'message' => Zend_Registry::get('Zend_Translate')->_("Subscribed Fail."),
          );
          return $this->_helper->json($data);
        }
        $viewer_id = $viewer->getIdentity();
        $table = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $subscriptionModel = $table->fetchRow($table->select()
                            ->from($table->info('name'), array('subscription_id'))
                            ->where('channel_id = ?', $channel->channel_id)
                            ->where('owner_type = ?', 'user')
                            ->where('owner_id = ?', $viewer_id)
            );
            if (!$subscriptionModel) {
                $form = new Sitevideo_Form_NotificationSettings();
                $values = $form->getValues();
                $subscriptionModel = $table->createRow();
                $subscriptionModel->channel_id = $channel->channel_id;
                $subscriptionModel->owner_type = 'user';
                $subscriptionModel->owner_id = $viewer_id;
                $channel->subscribe_count = new Zend_Db_Expr('subscribe_count + 1');
                $notificationArr['email'] = empty($values['email']) ? 0 : 1;
                $notificationArr['notification'] = empty($values['notification']) ? 0 : 1;

                if (empty($notificationArr['email']))
                    $values['action_email'] = null;

                if (empty($notificationArr['notification']))
                    $values['action_notification'] = null;
                $notificationArr['action_notification'] = empty($values['action_notification']) ? array() : $values['action_notification'];
                $notificationArr['action_email'] = empty($values['action_email']) ? array() : $values['action_email'];
                $notification = Zend_Json_Encoder::encode($notificationArr);
                $subscriptionModel->notification = $notification;
                $subscriptionModel->save();
                $channel->save();
                $ownerObj = $channel->getOwner();
                $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notificationTable->addNotification($ownerObj, $viewer, $channel, "sitevideo_channel_subscribe", array("label" => "channel", 'user_name' => $viewer->getTitle(), 'channel_title' => $channel->getTitle()));
                $subscriptionCount = $channel->subscribe_count;
            } else
                $message = Zend_Registry::get('Zend_Translate')->_("This channel is already subscribed.");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $data = array();
        $data[] = array(
            'message' => $message,
            'subscription_count' => $subscriptionCount
        );
        return $this->_helper->json($data);
    }

    /*
     * This action used to unsubscribe the channel
     */

    public function unsubscribeChannelAction() {

        //Checking for "Subscription" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $sitevideoSubscription = Zend_Registry::isRegistered('sitevideoSubscription') ? Zend_Registry::get('sitevideoSubscription') : null;
        if (empty($sitevideoSubscription) || !Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        }
        $message = Zend_Registry::get('Zend_Translate')->_("Unsubscribed successfully.");
        $subscriptionCount = 0;
        //FIND THE CHANNEL MODEL
        $channel = Engine_Api::_()->core()->getSubject();
        $viewer_id = Engine_Api::_()->user()->getViewer()->user_id;
        $table = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $subscriptionModel = $table->fetchRow($table->select()
                            ->from($table->info('name'), array('subscription_id'))
                            ->where('channel_id = ?', $channel->channel_id)
                            ->where('owner_type = ?', 'user')
                            ->where('owner_id = ?', $viewer_id)
            );
            if ($subscriptionModel) {
                $subscriptionModel->delete();
                $channel->subscribe_count = new Zend_Db_Expr('subscribe_count - 1');
                $channel->save();
                $subscriptionCount = $channel->subscribe_count;
            } else
                $message = Zend_Registry::get('Zend_Translate')->_("Channel is already unsubscribed.");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $data = array();
        $data[] = array(
            'message' => $message,
            'subscription_count' => $subscriptionCount
        );
        return $this->_helper->json($data);
    }

    /*
     * This action used to unsubscribe the channel
     */

    public function unsubscribeAction() {

        //Checking for "Subscription" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        }
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form = $form = new Sitevideo_Form_Unsubscribe();
        //CHECKING REQUEST FOR POST
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $message = Zend_Registry::get('Zend_Translate')->_("This channel is unsubscribed successfully.");
        //FIND THE CHANNEL MODEL
        $channel = Engine_Api::_()->core()->getSubject();
        $viewer_id = Engine_Api::_()->user()->getViewer()->user_id;
        $table = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $subscriptionModel = $table->fetchRow($table->select()
                            ->from($table->info('name'), array('subscription_id'))
                            ->where('channel_id = ?', $channel->channel_id)
                            ->where('owner_type = ?', 'user')
                            ->where('owner_id = ?', $viewer_id)
            );
            if ($subscriptionModel) {
                $subscriptionModel->delete();
                $channel->subscribe_count = new Zend_Db_Expr('subscribe_count - 1');
                $channel->save();
            } else
                $message = Zend_Registry::get('Zend_Translate')->_("This channel is already unsubscribed.");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->view->status = true;
        $this->view->message = $message;
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitevideo_subscription_general', true),
                    'messages' => Array($this->view->message)
        ));
    }

    public function notificationSettingsAction() {

        //Checking for "Subscription" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $channel_id = $this->_getParam('channel_id');
        //FIND THE CHANNEL MODEL
        $channelModel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid action.');
        $subscriptionModel = $channelModel->getSubscriptionModel();
        if ($subscriptionModel) {
            //INITIATE FORM OBJECT
            $this->view->form = $form = new Sitevideo_Form_NotificationSettings();
            $data = Zend_Json_Decoder::decode($subscriptionModel->notification);
            $form->populate($data);
            //CHECKING REQUEST FOR POST
            if (!$this->getRequest()->isPost()) {
                return;
            }
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }
            $values = $form->getValues();
            $notificationArr['email'] = empty($values['email']) ? 0 : 1;
            $notificationArr['notification'] = empty($values['notification']) ? 0 : 1;
            $notificationArr['action_notification'] = empty($notificationArr['notification']) ? array() : (empty($values['action_notification']) ? array() : $values['action_notification']);
            $notificationArr['action_email'] = empty($notificationArr['email']) ? array() : (empty($values['action_email']) ? array() : $values['action_email']);
            $notification = Zend_Json_Encoder::encode($notificationArr);
            $subscriptionModel->notification = $notification;
            $subscriptionModel->save();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Notifications has been updated.');
        }
        $this->view->status = true;
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitevideo_channel_general', true),
                    'messages' => Array($this->view->message)
        ));
    }

    public function manageAction() {
        //Checking for "Subscription" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

}

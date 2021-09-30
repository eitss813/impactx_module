<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Newsletter.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Plugin_Task_Newsletter extends Core_Plugin_Task_Abstract {

    public function execute() {

        $testemail = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.enabletestmode', 0);
        if(empty($testemail)) {
            //Refrence from Core/Bootstrap.php
            $content = Engine_Content::getInstance();
            $content->getView()->baseUrl();
            $storage = $content->getStorage();
            $view = Zend_Registry::get('Zend_View');
            $emailtemwidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.emailtemwidth', '500');
            $campaigns = Engine_Api::_()->getDbTable('campaigns', 'sesnewsletter')->getResult();
            $dbInsert = Engine_Db_Table::getDefaultAdapter();
            $send_email_count = 0;
            foreach($campaigns as $campaign) {
                Zend_Controller_Front::getInstance()->getResponse()->setBody($campaign->body);
                $content->setStorage(Engine_Api::_()->getDbtable('templates', 'sesnewsletter'));
                $header = $content->render('header');
                $footer = $content->render('footer');
                $contentBody = $content->render($campaign->template_id);
                $content->setStorage($storage);
                $emails = Engine_Api::_()->getDbTable('newsletteremails', 'sesnewsletter')->getResult(array('campaign_id' => $campaign->campaign_id));
                foreach($emails as $email) {
                    $unsubscribelink = $view->absoluteUrl(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesnewsletter', 'controller' => 'index', 'action' => 'unsubscribe','email' => base64_encode($email->email)), 'default', true));
                    $unsubscribeMessage = '<div style="margin:auto;font-size:10px;color:#666666;text-align:center;max-width:'.$emailtemwidth.'px;">This message was sent to '.$email->email.'. If you don\'t want to receive these emails from in the future, please <a href='.$unsubscribelink.' target="_blank">Unsubscribe</a></div>';
                    $newsletter_message = '<div style="margin:auto; border:1px solid #ddd;max-width:'.$emailtemwidth.'px;">'.$header. $contentBody.$footer.'</div>';
                    $dom = new DomDocument();
                    $dom->loadHTML($newsletter_message);
                    $newsletter_message = $dom->saveHTML();
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($email->email, 'sesnewsletter_newslettermailsend', array('subject' => $campaign->title,'message' => $newsletter_message,'unsubscribe' => $unsubscribeMessage, 'email' => $email->email));
                    //Delete afetr sent mail
                    $campaign->send_email_count++;
                    $campaign->save();
                    $dbInsert->query('DELETE FROM `engine4_sesnewsletter_newsletteremails` WHERE `engine4_sesnewsletter_newsletteremails`.`newsletteremail_id` = "'.$email->newsletteremail_id.'";');
                }
                if($campaign->send_email_count == $campaign->email_count) {
                    $campaign->status = '2';
                    $campaign->save();
                }
            }
        }
    }
}

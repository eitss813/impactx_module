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

class Sesnewsletter_Plugin_Task_Newsletteremail extends Core_Plugin_Task_Abstract {

    public function execute() {

        $testemail = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.enabletestmode', 0);
        if(empty($testemail)) {
            //Refrence from Core/Bootstrap.php
            $content = Engine_Content::getInstance();
            $content->getView()->baseUrl();
            $storage = $content->getStorage();
            $emailtemwidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.emailtemwidth', '500');
            $results = Engine_Api::_()->getDbTable('emails', 'sesnewsletter')->getResult();
            $dbInsert = Engine_Db_Table::getDefaultAdapter();
            $mailApi = Engine_Api::_()->getApi('mail', 'core');
            foreach($results as $result) {
                Zend_Controller_Front::getInstance()->getResponse()->setBody($result->body);
                $content->setStorage(Engine_Api::_()->getDbtable('templates', 'sesnewsletter'));
                $header = $content->render('header');
                $footer = $content->render('footer');
                $contentBody = $content->render($result->template_id);
                $content->setStorage($storage);

                $newsletter_message = '<div style="margin:auto; border:1px solid #ddd;max-width:'.$emailtemwidth.'px;">'.$header. $contentBody.$footer.'</div>';
                $dom = new DomDocument();
                $dom->loadHTML($newsletter_message);
                $newsletter_message = $dom->saveHTML();

                //Send email
                $mail = $mailApi->create();
                $mail
                    ->setFrom($result->from_address, $result->from_name)
                    ->setSubject($result->subject)
                    ->setBodyHtml($newsletter_message);
                $mail->addTo($result->email);
                $mailApi->send($mail);

                //Delete afetr sent mail
                $dbInsert->query('DELETE FROM `engine4_sesnewsletter_emails` WHERE `engine4_sesnewsletter_emails`.`email_id` = "'.$result->email_id.'";');
            }
        }
    }
}

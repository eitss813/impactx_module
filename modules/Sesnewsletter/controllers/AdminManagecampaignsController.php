<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManagecampaignsController.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_AdminManagecampaignsController extends Core_Controller_Action_Admin {

    public function indexAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_managecampaigns');

        $this->view->formFilter = $formFilter = new Sesnewsletter_Form_Admin_FilterNewsletter();
        $page = $this->_getParam('page', 1);

        $table = Engine_Api::_()->getDbtable('campaigns', 'sesnewsletter');
        $select = $table->select();

        // Process form
        $values = array();
        if ($formFilter->isValid($this->_getAllParams()))
            $values = $formFilter->getValues();

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array('campaign_id' => 'DESC'), $values);
        $this->view->assign($values);

        if (!empty($values['title']))
            $select->where('title LIKE ?', '%' . $values['title'] . '%');

        if (isset($values['status']) && $values['status'] != -1 && $values['status'] == 3) {
            $select->where('status = ?', 1)->where('publish_type =?', 2);
        } else if (isset($values['status']) && $values['status'] != -1)
            $select->where('status = ?', $values['status'])->where('publish_type =?', 1);
        //else if(@$values['status'] != -1)
           // $select->where('status =?', 0);

        $select->order('campaign_id DESC');
        $valuesCopy = array_filter($values);
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(100);
        $this->view->formValues = $valuesCopy;

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                $action = Engine_Api::_()->getItem('sesnewsletter_campaign', $value)->delete();
                $db->query("DELETE FROM engine4_sesnewsletter_campaigns WHERE campaign_id = " . $value);
                }
            }
        }
    }

    public function createAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_managecampaigns');

        $this->view->form = $form = new Sesnewsletter_Form_Admin_CreateCampaign();

        $id = $this->_getParam('id');
        if(!empty($id)) {
            $item = Engine_Api::_()->getItem('sesnewsletter_campaign', $id);
            $form->populate($item->toArray());
        }

        $newsletteremailsTable = Engine_Api::_()->getDbtable('newsletteremails', 'sesnewsletter');
        $dbInsert = Engine_Db_Table::getDefaultAdapter();

        if ($this->getRequest()->isPost()) {
            if (!$form->isValid($this->getRequest()->getPost()))
                return;
            $db = Engine_Api::_()->getDbtable('campaigns', 'sesnewsletter')->getAdapter();
            $db->beginTransaction();
            try {
                $table = Engine_Api::_()->getDbtable('campaigns', 'sesnewsletter');
                $values = $form->getValues();
                $newsletter_types = $values['newsletter_types'];
                if(isset($values['newsletter_types']))
                    $values['newsletter_types'] = implode(',', @$values['newsletter_types']);

                if (@$values['profile_types'])
                    $values['profile_types'] = json_encode($values['profile_types']);
                else
                    $values['profile_types'] = json_encode($optionValues);

                $networkValues = array();
                foreach (Engine_Api::_()->getDbtable('networks', 'network')->fetchAll() as $network) {
                    $networkValues[] = $network->network_id;
                }
                if (@$values['networks'])
                    $values['networks'] = json_encode($values['networks']);
                else
                    $values['networks'] = json_encode($networkValues);
                $values['member_levels'] = json_encode($values['member_levels']);

                $row = $table->createRow();
                $row->setFromArray($values);
                $row->status = 0;
                $row->save();

        //         if(count($newsletter_types) > 0) {
        //             foreach($newsletter_types as $newslettertype) {
        //                 $getTypeEmails = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->getTypeSubscribersEmails(array('type_id' => $newslettertype, 'fetchAll' => 1));
        //                 foreach($getTypeEmails as $getTypeEmail) {
        //                     $dbInsert->query('INSERT INTO engine4_sesnewsletter_newsletteremails(email, campaign_id) VALUES ("'.$getTypeEmail.'", "'.$row->campaign_id.'")');
        //                 }
        //             }
        //         }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_redirect('admin/sesnewsletter/managecampaigns');
        }
    }

    public function resendAction() {

        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');

        $this->view->form = $form = new Sesnewsletter_Form_Admin_Resend();
        $form->setTitle('Resend This Newsletter?');
        $form->setDescription('Are you sure that you want to resend this newsletter? It will not be undone after being published.');
        $form->submit->setLabel('Resend');

        $this->view->item_id = $id = $this->_getParam('id');

        // Check post
        if ($this->getRequest()->isPost()) {
            $campaign = Engine_Api::_()->getItem('sesnewsletter_campaign', $id);

            $newsletter_types = $campaign->newsletter_types;
            $newsletter_types = explode(',', $newsletter_types);

            $email_count = 0;
            if(count($newsletter_types) > 0) {
                foreach($newsletter_types as $newslettertype) {
                    $getTypeEmails = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->getTypeSubscribersEmails(array('type_id' => $newslettertype, 'fetchAll' => 1));
                    $dbInsert = Engine_Db_Table::getDefaultAdapter();
                    foreach($getTypeEmails as $getTypeEmail) {
                        $dbInsert->query('INSERT INTO engine4_sesnewsletter_newsletteremails(email, campaign_id) VALUES ("'.$getTypeEmail.'", "'.$id.'")');
                        $campaign->email_count++;
                        $campaign->save();
                    }
                }
            }

            $campaign->status = 1;
            $campaign->save();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Newsletter Re-sended Successfully.')
            ));
        }
        // Output
        $this->renderScript('admin-managecampaigns/resend.tpl');
    }

    public function publishAction() {

        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');

        $this->view->form = $form = new Sesnewsletter_Form_Admin_Publish();
        $form->setTitle('Publish This Newsletter?');
        $form->setDescription('From here choose to publish or Schedule this newsletter? It will not be undone after being Published or Scheduled.');
        $form->submit->setLabel('Publish Now');

        $this->view->item_id = $id = $this->_getParam('id');

        // Check post
        if ($this->getRequest()->isPost()) {
            $values = $form->getValues();

            if($_POST['publish_type'] == 2) {
                $date = explode('/',$_POST['starttime']['date']); //dd-mm-yyyy
                $date = $date['2'].'-'.$date['1'].'-'.$date['0']; //yyyy-mm-dd
            } else {
                $date = date('Y-m-d');
            }

            $campaign = Engine_Api::_()->getItem('sesnewsletter_campaign', $id);
            $newsletter_types = $campaign->newsletter_types;
            $newsletter_types = explode(',', $newsletter_types);

            if(count($newsletter_types) > 0) {
                foreach($newsletter_types as $newslettertype) {
                    $getTypeEmails = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->getTypeSubscribersEmails(array('type_id' => $newslettertype, 'fetchAll' => 1, 'campaign' => $campaign));
                    $dbInsert = Engine_Db_Table::getDefaultAdapter();
                    foreach($getTypeEmails as $getTypeEmail) {
                        $dbInsert->query('INSERT INTO engine4_sesnewsletter_newsletteremails(email, campaign_id) VALUES ("'.$getTypeEmail.'", "'.$id.'")');
                        $campaign->email_count++;
                        $campaign->save();
                    }
                }
            }

            $campaign->publish_type = $_POST['publish_type'];
            $campaign->publish_date = @$date;
            $campaign->status = 1;
            $campaign->save();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Newsletter has been published successfully.')
            ));
        }
        // Output
        $this->renderScript('admin-managecampaigns/publish.tpl');
    }


    public function editAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_managecampaigns');
        $this->view->form = $form = new Sesnewsletter_Form_Admin_EditCampaign();
        $id = $this->_getParam('id');
        $this->view->item = $item = Engine_Api::_()->getItem('sesnewsletter_campaign', $id);
        $form->populate($item->toArray());

        //Check post
        if (!$this->getRequest()->isPost())
        return;

        //Check
        if (!$form->isValid($this->getRequest()->getPost()))
            return;

        $values = $form->getValues();
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $item->setFromArray($values);
            $item->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->_helper->redirector->gotoRoute(array('module' => 'sesnewsletter', 'action' => 'index', 'controller' => 'managecampaigns'), 'admin_default', true);
    }

    public function stopAction() {

        $id = $this->_getParam('id');
        $db = Engine_Db_Table::getDefaultAdapter();
        if (!empty($id)) {
            $item = Engine_Api::_()->getItem('sesnewsletter_campaign', $id);
            $item->stop = !$item->stop;
            $item->save();
            if($item->stop == 1) {
                $db->query('UPDATE `engine4_sesnewsletter_newsletteremails` SET `stop` = "1" WHERE `engine4_sesnewsletter_newsletteremails`.`campaign_id` = "'.$id.'";');
            } else {
                $db->query('UPDATE `engine4_sesnewsletter_newsletteremails` SET `stop` = "0" WHERE `engine4_sesnewsletter_newsletteremails`.`campaign_id` = "'.$id.'";');
            }
        }
        $this->_redirect('admin/sesnewsletter/managecampaigns');
    }

    public function enabledAction() {

        $id = $this->_getParam('id');
        if (!empty($id)) {
            $item = Engine_Api::_()->getItem('sesnewsletter_subscriber', $id);
            $item->enabled = !$item->enabled;
            $item->save();
        }
        $this->_redirect('admin/sesnewsletter/managecampaigns');
    }

    public function deleteAction() {

        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');

        $this->view->form = $form = new Sesnewsletter_Form_Admin_DeleteCampaign();
        $form->setTitle('Delete This Newsletter?');
        $form->setDescription('Are you sure that you want to delete this Newsletter? It will not be recoverable after being deleted.');
        $form->submit->setLabel('Delete');

        $this->view->item_id = $id = $this->_getParam('id');

        // Check post
        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getItem('sesnewsletter_campaign', $id)->delete();
            $db = Engine_Db_Table::getDefaultAdapter();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Newsletter Deleted Successfully.')
            ));
        }
        // Output
        $this->renderScript('admin-managecampaigns/delete.tpl');
    }

    public function previewAction() {

        $campaigns = Engine_Api::_()->getItem('sesnewsletter_campaign', $this->_getParam('id'));
        $content = Engine_Content::getInstance();
        $content->getView()->baseUrl();
        $storage = $content->getStorage();
        $emailtemwidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.emailtemwidth', '500');
        Zend_Controller_Front::getInstance()->getResponse()->setBody($campaigns->body);
        $content->setStorage(Engine_Api::_()->getDbtable('templates', 'sesnewsletter'));
        $header = $content->render('header');
        $footer = $content->render('footer');
        $contentBody = $content->render($campaigns->template_id);
        $content->setStorage($storage);
        $newsletter_message = '<div style="margin:auto; border:1px solid #ddd;max-width:'.$emailtemwidth.'px;">'.$header. $contentBody.$footer.'</div>';
        $dom = new DomDocument();
        $dom->loadHTML($newsletter_message);
        $newsletter_message = $dom->saveHTML();
        echo $newsletter_message;die;
    }

    public function testemailAction() {

        $campaigns = Engine_Api::_()->getItem('sesnewsletter_campaign', $this->_getParam('id'));
        $testEmai = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.testemail', '');
        $emailtemwidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.emailtemwidth', '500');
        $unsubscribelink = $this->view->absoluteUrl(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesnewsletter', 'controller' => 'index', 'action' => 'unsubscribe', 'email' => base64_encode($testEmai)), 'default', true));
        $unsubscribeMessage = '<div style="margin:auto;font-size:11px;max-width:'.$emailtemwidth.'px;color:#666666;text-align:center;">This message was sent to '.$testEmai.'. If you do not want to receive these emails from in the future, please <a href='.$unsubscribelink.' target="_blank">Unsubscribe</a></div>';
        $content = Engine_Content::getInstance();
        $content->getView()->baseUrl();
        $storage = $content->getStorage();
        Zend_Controller_Front::getInstance()->getResponse()->setBody($campaigns->body);
        $content->setStorage(Engine_Api::_()->getDbTable('templates', 'sesnewsletter'));
        $header = $content->render('header');
        $footer = $content->render('footer');
        $contentBody = $content->render($campaigns->template_id);
        $content->setStorage($storage);
        $newsletter_message = '<div style="max-width:'.$emailtemwidth.'px; margin:auto; border:1px solid #ddd;">'.$header. $contentBody.$footer.'</div>';
        $dom = new DomDocument();
        $dom->loadHTML($newsletter_message);
        $newsletter_message = $dom->saveHTML();
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($testEmai, 'sesnewsletter_newslettermailsend', array('subject' => $campaigns->title,'message' => $newsletter_message,'unsubscribe' => $unsubscribeMessage, 'email' => $testEmai));
        $this->_redirect('admin/sesnewsletter/managecampaigns');
    }
}

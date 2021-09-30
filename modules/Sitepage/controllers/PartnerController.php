<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_PartnerController extends Core_Controller_Action_Standard
{

    // list partners
    public function managePartnerAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        // get my partners
        $this->view->myPartners= $myPartners = Engine_Api::_()->getDbtable('partners', 'sitepage')->getPartnerPages($page_id);

        // get i joined pages as partners
        $this->view->joinedAsPartner= $joinedAsPartner = Engine_Api::_()->getDbtable('partners', 'sitepage')->getJoinedAsPartnerPages($page_id);

    }

    // add partners
    public function addPartnerAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //SELECTED TAB
        $this->view->TabActive = "sitecrowdfunding_dashboard_editoutput";

        //PREPARE FORM
        $this->view->form = $form = new Sitepage_Form_AddPartners();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        $partner_page_ids = explode(",", $values['toValues']);

        if (empty($values['page_ids']) && empty($values['toValues'])) {
            $form->addError('Please complete this field - It is requried.');
            return;
        }

        if (empty($values['toValues'])) {
            $form->addError('This is an invalid user name. Please select a valid user name from the autosuggest.');
            return;
        }

        $partnersTable = Engine_Api::_()->getDbtable('partners', 'sitepage');

        if (!empty($partner_page_ids)) {

            foreach ($partner_page_ids as $partner_page_id) {

                $row = $partnersTable->createRow();
                $row->page_id = $page_id;
                $row->partner_page_id = $partner_page_id;
                $row->created_at = 1;
                $row->save();
            }
        }
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Added Successfully!'))
        ));

//        return $this->_forward('success', 'utility', 'core', array(
//            'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected pages have been successfully added to this pages.')),
//            'layout' => 'default-simple',
//            'parentRefresh' => true,
//        ));

    }

    // delete partners
    public function deletePartnerAction()
    {

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PAGE ID
        $this->view->partner_id = $partner_id= $this->_getParam('partner_id');
        $this->view->action_type = $action_type= $this->_getParam('action_type');

        $this->view->form = $form = new Sitepage_Form_RemovePartner();

        if ($action_type === 'ACCEPT'){
            $form->setTitle('Accept');
            $form->setDescription('Are you sure you want to accept this sister organization ?');
            $toastMsg = 'Accepted successfully';
            $form->submit->setLabel('Accept');
        }elseif ($action_type === 'REJECT'){
            $form->setTitle('Reject');
            $form->setDescription('Are you sure you want to reject this sister organization ?');
            $toastMsg = 'Rejected successfully';
            $form->submit->setLabel('Reject');
        }else{
           $toastMsg = 'Removed successfully';
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $item  = Engine_Api::_()->getItem('sitepage_partner', $partner_id);

        if ($action_type === 'DELETE'){
            $item->delete();
        }
        if ($action_type === 'ACCEPT'){
            $item->rejected  = 0;
            $item->accepted   = 1;
            $item->save();
        }
        if ($action_type === 'REJECT'){
            $item->rejected  = 1;
            $item->accepted   = 0;
            $item->save();
        }
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_($toastMsg))
        ));
//        return $this->_forward('success', 'utility', 'core', array(
//            'smoothboxClose' => true,
//            'parentRefresh' => true,
//            'messages' => array(Zend_Registry::get('Zend_Translate')->_($toastMsg))
//        ));

    }

    public function getPartnersAction()
    {
        //GET pageId
        $page_id = $this->_getParam('page_id', null);

        $data = array();

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pagesTableName = $pagesTable->info('name');

        $partnerTable = Engine_Api::_()->getDbtable('partners', 'sitepage');
        $partnerTableName = $partnerTable->info('name');

        // fetch the name who are not as partners
        $select1 = $partnerTable->select()
            ->from($partnerTableName, 'partner_page_id')
            ->where('page_id = ?', $page_id);

        $select2 = $partnerTable->select()
            ->from($partnerTableName, 'page_id')
            ->where('partner_page_id = ?', $page_id);

        $select = $partnerTable->select()->union(array($select1, $select2));

        $partner_page_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        // dont fetch their name itself
        $partner_page_ids[] = $page_id;

        $autoRequest = '';

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $autoRequest = $this->_getParam('page_ids', null);
        } else {
            $autoRequest = $this->_getParam('text', null);
        }

        if($partner_page_ids > 0){
            $pagesSelect = $pagesTable->select()
                ->from($pagesTableName, array('page_id', 'title'))
                ->where('title  LIKE ? ', '%' . $autoRequest . '%')
                ->where('page_id NOT IN (?)', (array) $partner_page_ids)
                ->where('approved = ?', '1');
        } else {
            $pagesSelect = $pagesTable->select()
                ->from($pagesTableName, array('page_id', 'title'))
                ->where('title  LIKE ? ', '%' . $autoRequest . '%')
                ->where('page_id NOT IN (?)', (array) $partner_page_ids)
                ->where('approved = ?', '1');
        }

        $pages = $pagesTable->fetchAll($pagesSelect);

        foreach ($pages as $page) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page->page_id);
            $photo = $this->view->itemPhoto($sitepage, 'thumb.icon', '', array('nolazy' => true));
            $data[] = array(
                'id' => $page->page_id,
                'label' => $page->title,
                'photo' => $photo,
            );
        }

        return $this->_helper->json($data);
    }

}

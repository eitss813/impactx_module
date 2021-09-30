<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_StatusController extends Seaocore_Controller_Action_Standard {

    public function submitAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->sitepage_id = $sitepage_id = $this->_getParam('sitepage_id');
        //GET PROJECT ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepage_id);

        //IF THERE IS NO PROJECT.
        if (empty($sitepage)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        Engine_Api::_()->core()->setSubject($sitepage);

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $table = Engine_Api::_()->getItemTable('sitepage_page');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $pageModel = $sitepage;
            $inputs = array(
                'state'  => 'submitted',
            );
            $pageModel->setFromArray($inputs);
            $pageModel->save();
            $db->commit();

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Submitted for approval.'))
            ));

        }catch (Exception $e){
            $db->rollBack();
            throw $e;
        }
    }

    public function viewNotesAction(){

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->sitepage_id = $sitepage_id = $this->_getParam('sitepage_id');

        //GET PROJECT ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepage_id);

        Engine_Api::_()->core()->setSubject($sitepage);

        $this->view->adminnotes = $adminnotes = Engine_Api::_()->getDbTable('adminnotes','sitepage')->getAllAdminNotesByProjectId($sitepage_id);
    }

}
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */

/**
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 */
class Cbpageanalytics_IndexController extends Core_Controller_Action_Standard {

    public function indexAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_getParam('request', 0);
        if (!$request) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $analyticsTable = Engine_Api::_()->getDbtable('pages', 'cbpageanalytics');

        $pageData = array(
            'page_original_id' => $this->_getParam('page_original_id', null),
            'title' => $this->_getParam('page_title', null),
            'subject_type' => $this->_getParam('page_subject_type', null),
            'subject_id' => $this->_getParam('page_subject_id', null),
            'subject_name' => $this->_getParam('page_subject_name', null),
            'page_url' => $this->_getParam('page_url', null),
            'referrer_page' => $this->_getParam('referrer_page', null),
            'user_id' => $this->_getParam('user_id', null),
            'page_name' => $this->_getParam('page_name', null),
            'module' => $this->_getParam('page_module', null),
            'controller' => $this->_getParam('page_controller', null),
            'action' => $this->_getParam('page_action', null),
            'request' => $this->_getParam('request', null)
        );

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $stat = $analyticsTable->createRow();
            $stat->setFromArray($pageData);
            $stat->save();

            $db->commit();
        } catch (Exception $ex) {
            $db->rollBack();
            throw $ex;
        }
    }

}

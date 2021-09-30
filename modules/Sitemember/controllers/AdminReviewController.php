<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminReviewController.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_AdminReviewController extends Core_Controller_Action_Admin {

    public function indexAction() {
        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_review');

        $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_reviewmain', array(), 'sitemember_admin_reviewmain_general');

        // Make form
        $this->view->form = $form = new Sitemember_Form_Admin_Review_Global();

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        include APPLICATION_PATH . '/application/modules/Sitemember/controllers/license/license2.php';

        $form->addNotice('Your changes have been saved successfully.');
    }

    public function levelAction() {
        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_review');

        $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_reviewmain', array(), 'sitemember_admin_reviewmain_level');

        //GET LEVEL ID
        if (null != ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;

        //MAKE FORM
        $this->view->form = $form = new Sitemember_Form_Admin_Review_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));

        include APPLICATION_PATH . '/application/modules/Sitemember/controllers/license/license2.php';
        $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
        if ($public_level_id) {
            $form->level_id->removeMultiOption($public_level_id);
        }

        $form->level_id->setValue($id);

        //POPULATE DATA
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

        $form->populate($permissionsTable->getAllowed('user', $id, array_keys($form->getValues())));

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //CHECK VALIDITY
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $values = $form->getValues();

        $otherSettings = array();

        foreach ($values as $key => $value) {
            $otherSettings[$key] = $value;
        }

        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();
        try {

            //SET PERMISSION
            $permissionsTable->setAllowed('user', $id, $otherSettings);

            //COMMIT
            $db->commit();

            $form->addNotice('Your changes have been saved successfully.');
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR MANAGING REVIEWS
    public function manageAction() {

        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_review');

        $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_reviewmain', array(), 'sitemember_admin_reviewmain_manage');

        //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
        $this->view->formFilter = $formFilter = new Sitemember_Form_Admin_Manage_Filter();

        $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

        $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore')->info('name');
        $tableReviewRating = Engine_Api::_()->getDbtable('ratings', 'sitemember');
        $tableReviewRatingName = $tableReviewRating->info('name');

        $table = Engine_Api::_()->getDbtable('reviews', 'sitemember');
        $rName = $table->info('name');
        $select = $table->select()
                ->setIntegrityCheck(false)
                ->from($rName)
                ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", array('username', 'email', 'displayname as user_title'))
                ->joinLeft($tableUserInfo, "$rName.resource_id = $tableUserInfo.user_id", array('rating_users'))
                ->joinLeft($tableReviewRatingName, "$rName.review_id = $tableReviewRatingName.review_id", array('rating As review_rating', 'ratingparam_id'))
                ->where($tableReviewRatingName . '.ratingparam_id = ?', 0);

        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        //REVIEW SEARCH WORK
        if (isset($_POST['search'])) {
            if (!empty($_POST['review_title'])) {
                $this->view->review_title = $_POST['review_title'];
                $select->where($rName . '.title  LIKE ?', '%' . $_POST['review_title'] . '%');
            }


            if (!empty($_POST['review_type'])) {
                $this->view->review_type = $_POST['review_type'];
                $select->where($rName . '.type  LIKE ?', '%' . $_POST['review_type'] . '%');
            }
            if (isset($_POST['review_status'])) {
                $this->view->review_status = $review_status = $_POST['review_status'];
                if ($review_status == 3) {
                    $select->where($rName . '.status =?', 0);
                    $this->view->review_status = 3;
                } else if ($review_status == 1) {
                    $select->where($rName . '.status =?', 1);
                } else if ($review_status == 2) {
                    $select->where($rName . '.status =?', 2);
                }
            }
        } else {
            $this->view->review_title = '';
            $this->view->user_title = '';
            $this->view->name = '';
            $this->view->email = '';
            $this->view->review_status = '';
            $this->view->review_type = '';
        }

        $values = array_merge(array(
            'order' => 'review_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);
        $select->order((!empty($values['order']) ? $values['order'] : 'review_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
        include APPLICATION_PATH . '/application/modules/Sitemember/controllers/license/license2.php';
    }

    //ACTION FOR DELETING A REVIEW
    public function deleteAction() {

        $review_id = $this->_getParam('review_id');
        $this->view->review = $review = Engine_Api::_()->getItem('sitemember_review', (int) $review_id);

        if ($this->getRequest()->isPost()) {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $review->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Review has been deleted successfully.'))
            ));
        } else {
            $this->renderScript('admin-review/delete.tpl');
        }
    }

    //ACTION FOR MULTI DELETE REVIEWS
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    //DELETE DOCUMENTS FROM DATABASE AND SCRIBD
                    Engine_Api::_()->getItem('sitemember_review', (int) $value)->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('controller' => 'review', 'action' => 'manage'));
    }

    //ACTION FOR MAKE REVIEW FEATURED
    public function featuredAction() {

        //GET REVIEW ITEM
        $review = Engine_Api::_()->getItem('sitemember_review', $this->_getParam('review_id'));
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            if ($review->featured == 0) {
                $review->featured = 1;
            } else {
                $review->featured = 0;
            }
            $review->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/sitemember/review/manage');
    }

}
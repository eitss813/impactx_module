<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminRatingparametersController.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_AdminRatingparametersController extends Fields_Controller_AdminAbstract {

    protected $_fieldType = 'user';
    protected $_requireProfileType = true;
    protected $_moduleName = 'user';

    //ACTION FOR MANAGING RATING PARAMETERS
    public function manageAction() {

        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_review');

        $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_reviewmain', array(), 'sitemember_admin_reviewmain_ratingparams');

        include APPLICATION_PATH . '/application/modules/Sitemember/controllers/license/license2.php';
    }

    //ACTION FOR CREATE NEW REVIEW PARAMETER
    public function createAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GENERATE FORM
        $form = $this->view->form = new Sitemember_Form_Admin_Ratingparameter_Create();
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        $this->view->options = array();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                //CHECK PARAMETERS
                $options = (array) $this->_getParam('optionsArray');
                $options = array_filter(array_map('trim', $options));
                $options = array_slice($options, 0, 100);
                $this->view->options = $options;
                if (empty($options) || !is_array($options) || count($options) < 1) {
                    return $form->addError('You must add at least one parameter.');
                }

                $tableReviewParams = Engine_Api::_()->getDbtable('ratingparams', 'sitemember');
                foreach ($options as $option) {
                    $row = $tableReviewParams->createRow();
                    $row->profiletype_id = $this->_getParam('profiletype_id');
                    $row->ratingparam_name = $option;
                    $row->resource_type = 'user';
                    $row->save();
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }

        $this->renderScript('admin-ratingparameters/create.tpl');
    }

    //ACTION FOR EDITING THE REVIEW PARAMETER NAME
    public function editAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        if (!($profiletype_id = $this->_getParam('profiletype_id'))) {
            die('No identifier specified');
        }

        //FETCH PARAMETERS ACCORDING TO THIS CATEGORY
        $profiletypeIdsArray = array();
        $profiletypeIdsArray[] = $profiletype_id;
        $ratingParams = Engine_Api::_()->getDbtable('ratingparams', 'sitemember')->memberParams($profiletypeIdsArray, 'user');

        $this->view->options = array();
        $this->view->totalOptions = 1;

        //GENERATE A FORM
        $form = $this->view->form = new Sitemember_Form_Admin_Ratingparameter_Edit();
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
        $form->setField($ratingParams);

        //CHECK PARAMETERS
        $options = (array) $this->_getParam('optionsArray');
        $options = array_filter(array_map('trim', $options));
        $options = array_slice($options, 0, 100);
        $this->view->options = $options;
        $this->view->totalOptions = Count($options);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                foreach ($values as $key => $value) {
                    if ($key != 'options' && $key != 'dummy_text') {
                        $ratingparam_id = explode('ratingparam_name_', $key);

                        if (!empty($ratingparam_id)) {
                            $membercat = Engine_Api::_()->getItem('sitemember_ratingparam', $ratingparam_id[1]);

                            //EDIT CATEGORY NAMES
                            if (!empty($membercat)) {
                                $membercat->ratingparam_name = $value;
                                $membercat->save();
                            }
                        }
                    }
                }

                //INSERT THE REVIEW CATEGORY IN TO THE DATABASE
                foreach ($options as $option) {
                    $row = Engine_Api::_()->getDbtable('ratingparams', 'sitemember')->createRow();
                    $row->profiletype_id = $this->_getParam('profiletype_id');
                    $row->ratingparam_name = $option;
                    $row->resource_type = 'user';
                    $row->save();
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Parameters has been edited successfully.')
            ));
        }

        $this->renderScript('admin-ratingparameters/edit.tpl');
    }

    //ACTION FOR DELETING THE REVIEW PARAMETERS
    public function deleteAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        if (!($profiletype_id = $this->_getParam('profiletype_id'))) {
            die('No identifier specified');
        }

        //GENERATE FORM
        $form = $this->view->form = new Sitemember_Form_Admin_Ratingparameter_Delete();
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            foreach ($values as $key => $value) {
                if ($value == 1) {
                    $ratingparam_id = explode('ratingparam_name_', $key);
                    $memberprofiletype = Engine_Api::_()->getItem('sitemember_ratingparam', $ratingparam_id[1]);

                    //DELETE ENTRIES FROM RATING TABLE CORROSPONDING TO REVIEW CATEGORY ID
                    Engine_Api::_()->getDbtable('ratings', 'sitemember')->delete(array('ratingparam_id = ?' => $ratingparam_id[1], 'resource_type =? ' => 'user'));

                    $db = Engine_Db_Table::getDefaultAdapter();
                    $db->beginTransaction();

                    try {
                        //DELETE THE REVIEW PARAMETERS
                        $memberprofiletype->delete();
                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                }
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Parameters has been deleted successfully.')
            ));
        }
        $this->renderScript('admin-ratingparameters/delete.tpl');
    }

}
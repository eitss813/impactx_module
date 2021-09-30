<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_ProjectCreateTwoController extends Seaocore_Controller_Action_Standard
{

    protected $_hasPackageEnable;

    public function init()
    {
        //SET THE SUBJECT
        if (0 !== ($project_id = (int)$this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
            Engine_Api::_()->sitecrowdfunding()->setPaymentFlag($project_id);
        }
        $this->_hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
    }

    //ACTION FOR EDITING THE PROJECT
    public function aboutAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepTwo(array(
            'project_id' => $project_id
        ));

        // Populate records
        $populatedArray = $project->toArray();
        $form->populate($populatedArray);
        if ($project->funding_start_date !== null && $project->funding_end_date !== null) {
            $form->populate(array(
                'starttime' => date('Y-m-d', strtotime($project->funding_start_date)),
                'endtime' => date('Y-m-d', strtotime($project->funding_end_date)),
            ));
        }

        // Save
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            // get form values
            $values = $form->getValues();
            if (empty($values))
                return;

            // valid start time
            if (!empty($values['starttime']) && empty($values['endtime'])) {
                $error = $this->view->translate('Please enter End Date - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            // valid end time
            if (!empty($values['endtime']) && empty($values['starttime'])) {
                $error = $this->view->translate('Please enter Start Date - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            if (!empty($values['starttime']) && !empty($values['endtime']) && $values['starttime'] > $values['endtime']) {
                $error = $this->view->translate('Please enter End Date greater than Start Date - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            if (!empty($values['starttime']) && !empty($values['endtime'])) {
                $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));
                $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));
                $days = Engine_Api::_()->sitecrowdfunding()->findDays($startDate, $endDate);
                $startDate2 = date('Y-m-d', strtotime($startDate));
                $endDate2 = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m', strtotime($endDate)), date('d', strtotime($endDate)), date('Y', strtotime($endDate))));
            } else {
                $startDate2 = null;
                $endDate2 = null;
            }

            $projectTable = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
            $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
            $db = $projectTable->getAdapter();
            $db->beginTransaction();

            try {

                // Update project
                $projectModel = $project;
                $inputs = array(
                    'title' => $values['title'],
                    'description' => $values['description'],
                    'funding_start_date' => $startDate2,
                    'funding_end_date' => $endDate2
                );
                $projectModel->setFromArray($inputs);
                $projectModel->save();

                // update location
                if (isset($values['locationParams']) && $values['locationParams']) {
                    if (is_string($values['locationParams'])) {
                        $locationParams = Zend_Json_Decoder::decode($values['locationParams']);
                        $locationTable->update($locationParams, array('project_id = ?' => $project_id));
                    }
                }

                $db->commit();

            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

    }
}

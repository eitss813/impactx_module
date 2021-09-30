<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


class Sitepage_MetricsController extends Core_Controller_Action_Standard
{

    public function indexAction()
    {
//        $this->view->metric_id = $metric_id = $this->_getParam('metric_id');
//
//        $this->view->metric_details = $metric_details = Engine_Api::_()->getItem('sitepage_metric', $metric_id);
//
//        if (!Engine_Api::_()->core()->hasSubject('sitepage_metric')) {
//            Engine_Api::_()->core()->setSubject($metric_details);
//        }
//
//        $request = Zend_Controller_Front::getInstance()->getRequest();
//        $params = $request->getParams();
//
//        // get field_id
//        $field_ids = array();
//        foreach (Engine_Api::_()->fields()->getFieldsMeta('yndynamicform_entry') as $field) {
//            if ($field->type == 'metrics') {
//                $fieldMeta = Engine_Api::_()->fields()->getField($field->field_id, 'yndynamicform_entry');
//                if ($fieldMeta->config['selected_metric_id'] == $metric_id) {
//                    $field_ids[] = $field->field_id;
//                }
//            }
//        }
//
//        if ($field_ids) {
//            $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
//            $entryTableName = $entryTable->info('name');
//
//            $valuesTableName = 'engine4_yndynamicform_entry_fields_values';
//
//            $projectSelect = $entryTable->select()
//                ->setIntegrityCheck(false)
//                ->from($entryTableName, array("$entryTableName.form_id", "$entryTableName.project_id", "$entryTableName.entry_id", "$valuesTableName.value"))
//                ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id", array("$valuesTableName.value", "$valuesTableName.field_id"))
//                ->where("$valuesTableName.field_id in (?)", $field_ids)
//                ->where("$entryTableName.publish=1")
//                ->where("$entryTableName.project_id IS NOT NULL")
//                ->where("$entryTableName.user_id IS NULL");
//
//            $userSelect = $entryTable->select()
//                ->setIntegrityCheck(false)
//                ->from($entryTableName, array("$entryTableName.form_id", "$entryTableName.user_id", "$entryTableName.entry_id", "$valuesTableName.value"))
//                ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id", array("$valuesTableName.value", "$valuesTableName.field_id"))
//                ->where("$valuesTableName.field_id in (?)", $field_ids)
//                ->where("$entryTableName.publish=1")
//                ->where("$entryTableName.project_id IS NULL")
//                ->where("$entryTableName.user_id IS NOT NULL");
//
//            $project_paginator = $entryTable->fetchAll($projectSelect);
//            $user_paginator = $entryTable->fetchAll($userSelect);
//
//
//            // get total aggregate value
//            $project_aggregate_value = $entryTable->select()
//                ->setIntegrityCheck(false)
//                ->from($entryTableName, array("SUM($valuesTableName.value) as project_aggregate"))
//                ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id")
//                ->where("$valuesTableName.field_id in (?)", $field_ids)
//                ->where("$entryTableName.publish=1")
//                ->where("$entryTableName.project_id IS NOT NULL")
//                ->where("$entryTableName.user_id IS NULL")
//                ->query()
//                ->fetchColumn();
//
//            $user_aggregate_value = $entryTable->select()
//                ->setIntegrityCheck(false)
//                ->from($entryTableName, array("SUM($valuesTableName.value) as user_aggregate"))
//                ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id")
//                ->where("$valuesTableName.field_id in (?)", $field_ids)
//                ->where("$entryTableName.publish=1")
//                ->where("$entryTableName.project_id IS NULL")
//                ->where("$entryTableName.user_id IS NOT NULL")
//                ->query()
//                ->fetchColumn();
//
//            $this->view->project_aggregate_value = $project_aggregate_value;
//            $this->view->user_aggregate_value = $user_aggregate_value;
//            $this->view->totalAggregateValue = (int)$project_aggregate_value + (int)$user_aggregate_value;
//
//            /*
//            $project_paginator = Zend_Paginator::factory($projectSelect);
//            $user_paginator = Zend_Paginator::factory($userSelect);
//
//            if (!empty($params['projects_page_no'])) {
//                $project_paginator->setCurrentPageNumber($params['projects_page_no']);
//                $this->view->projects_page_no = $projects_page_no = $params['projects_page_no'];
//            }elseif (!empty($params['users_page_no'])) {
//                $user_paginator->setCurrentPageNumber($params['users_page_no']);
//                $this->view->users_page_no = $user_page_no = $params['users_page_no'];
//            } else {
//                $project_paginator->setCurrentPageNumber(1);
//                $user_paginator->setCurrentPageNumber(1);
//                $this->view->projects_page_no = $projects_page_no = 1;
//                $this->view->users_page_no = $user_page_no = 1;
//            }
//
//            $project_paginator->setItemCountPerPage(1);
//            $user_paginator->setItemCountPerPage(1);
//            */
//
//            $this->view->project_entries = $project_paginator;
//            $this->view->user_entries = $user_paginator;
//
//        }
        
        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if( empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0')) ) {
          $this->_helper->content->render();
        } else {
          $this->_helper->content
          ->setNoRender()
          ->setEnabled();
        }

    }

    public function editAction()
    {
        //USER VALIDIATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->metric_id = $metric_id = $this->_getParam('metric_id', null);
        $this->view->metricDetails = $metricDetails = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Editmetrics(array('metric_id' => $metric_id));
        $form->populate($metricDetails->toArray());

        if ($this->getRequest()->getPost()) {

            if ($form->isValid($this->getRequest()->getPost())) {

                //get form values
                $value = $form->getValues();

                $file_id = null;
                if (!empty($value['logo'])) {
                    $file_id = $metricDetails->setLogo($form->logo);
                }

                $metric_inputs = array(
                    'metric_name' => $value['metric_name'],
                    'metric_description' => $value['metric_description'],
                    'metric_unit' => $value['metric_unit'],
                    'logo' => $file_id,
                    'updated_date' => new Zend_Db_Expr('NOW()')
                );
                $metricDetails->setFromArray($metric_inputs);
                $metricDetails->save();

                $redirect = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                    'action' => 'index',
                    'metric_id' => $metric_id
                ), 'sitepage_metrics', true);

                return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => $redirect,
                    'parentRefresh' => 10,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Metric updated successfully.'))
                ));

            }

        }
    }

    public function saveCroppedImageAction()
    {
        if (empty($_POST) || !isset($_POST['metric_id'])) {
            return false;
        }

        $values = $_POST;

        if (empty($values)) {
            return;
        }

        $metric_id = $values['metric_id'];
        $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        $coordinatesInput = $values['coordinates'];
        $photo_id = $values['photo_id'];

        $storage = Engine_Api::_()->storage();
        $iMain = $storage->get($photo_id, 'thumb.main');
        $iCover = $storage->get($photo_id, 'thumb.cover');

        if (empty($iCover)) {
            $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            $params = array(
                'parent_type' => 'sitepage_metric',
                'parent_id' => $metric_id,
                'user_id' => $metric->user_id,
                'name' => $iMain->name,
            );

            $iCover = $filesTable->createFile($iMain->storage_path, $params);
            $iMain->bridge($iCover, 'thumb.cover');
        }

        $pName = $iMain->getStorageService()->temporary($iMain);
        $iName = dirname($pName) . '/nis_' . basename($pName);
        list($x, $y, $w, $h) = explode(':', $coordinatesInput);
        $image = Engine_Image::factory();
        $image->open($pName)
            ->resample($x + .1, $y + .1, $w - .1, $h - .1, $w, $h)
            ->write($iName)
            ->destroy();
        $iCover->store($iName);
        @unlink($iName);

        // remove cover params
        $metric->cover_params = null;
        $metric->save();

        return true;
    }

    public function getCoverPhotoAction()
    {

        //START MANAGE-ADMIN CHECK
        $this->view->metric_id = $metric_id = $this->_getParam('metric_id', null);

        $this->view->metric = $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $this->view->coverTop = 0;
        $this->view->coverLeft = 0;

        $coverParams = $metric['cover_params'];

        if (isset($coverParams)) {
            $coverParams = json_decode($coverParams);
            $this->view->coverTop = $coverParams->top;
        }

    }

    public function uploadCoverPhotoAction()
    {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LAYOUT
        $this->_helper->layout->setLayout('default-simple');
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        $metric_id = $this->_getParam('metric_id');

        $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        //GET FORM
        $this->view->form = $form = new Sitepage_Form_MetricCover();

        //CHECK FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //UPLOAD PHOTO
        if ($form->Filedata->getValue() !== null) {

            //PROCESS
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $file_id = null;
                $file_id = $metric->setLogo($form->Filedata, true);
                $metric->logo = $file_id;
                $metric->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
                $metric->save();
                $db->commit();

                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
                ));

            } catch (Exception $e) {
                $db->rollBack();
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
                return;
            }
        }
    }

    public function removeCoverPhotoAction()
    {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $metric_id = $this->_getParam('metric_id', null);

        $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        if ($this->getRequest()->isPost()) {
            $metric->logo = null;
            $metric->cover_params = array('top' => '0', 'left' => 0);
            $metric->save();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    public function repositionCoverPhotoAction()
    {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->metric_id = $metric_id = $this->_getParam('metric_id', null);

        $this->view->metric = $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    }

}

?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminReportController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AdminReportController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_statistics_report');

        //GET NAVIGATION FOR SUB TABS
    $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main_statistics_report', array(), 'sitecrowdfunding_admin_main_reports');
    $this->view->reportform = $reportform = new Sitecrowdfunding_Form_Admin_Report;

		// POPULATE FORM
    if (isset($_GET['generate_report']) ) {
      $reportform->populate($_GET);

			// Get Form Values
			$values = $reportform->getValues();
      $report_form_error = false;
      if( ($values['select_project'] == 'specific_project') && empty($values['project_id'])) {
        $reportform->addError('Must fill Project name');
        $report_form_error = true;
      }
      
      if( !empty($report_form_error) )
        return;
      
			$start_cal_date = $values['start_cal'];
			$end_cal_date = $values['end_cal'];
			$start_tm = strtotime($start_cal_date);
			$end_tm = strtotime($end_cal_date);
			$url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);

			if(empty($values['format_report'])) {
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitecrowdfunding', 'controller' => 'report', 'action' => 'export-webpage', 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'admin_default', true) . '?' . $url_values[1];
			}
			else {
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitecrowdfunding', 'controller' => 'report', 'action' => 'export-excel', 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm,), 'admin_default', true) . '?' . $url_values[1];
			}
			// Session Object
			$session = new Zend_Session_Namespace('empty_adminredirect');
			if(isset($session->empty_session) && !empty($session->empty_session)) {
				unset($session->empty_session);
       } else {
				header("Location: $url");
			}
    }
    $this->view->empty = $this->_getParam('empty', 0);
  }
  
  // in case of admin's report format is excel file, the form action is redirected to this action
  public function exportExcelAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->post = $post = 0; 
    if (!empty($_GET)) {
      $this->_helper->layout->setLayout('default-simple');
      $this->view->post = $post = 1;
      $values = $_GET; 
      $this->view->values = $values;
      $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getReports($values);

      $rawdata_array = $rawdata->toarray();
      if($values['select_project'] == 'specific_project')
      {
        $project_id = $values['project_id'];
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project',$project_id);
        $this->view->backedAmount = $project->getFundedAmount();
        $this->view->totalCommission = $project->getTotalCommission();
      }
      $this->view->reportType = $values['report_type'];

      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitecrowdfunding', 'controller' => 'report', 'action' => 'index', 'empty' => '1'), 'admin_default', true) . '?' . $url_values[1];

      if (empty($rawdata_array)) {
				// Session Object
				$session = new Zend_Session_Namespace('empty_adminredirect');
				$session->empty_session = 1;
        header("Location: $url");
      }
    }
  }

  // in case of admin's report format is webpage, the form action is redirected to this action
  public function exportWebpageAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_statistics_report');

        //GET NAVIGATION FOR SUB TABS
    $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main_statistics_report', array(), 'sitecrowdfunding_admin_main_reports');

    $this->view->post = $post = 0;
		 
    if (!empty($_GET)) {
      $this->view->post = $post = 1;
      $values = $_GET; 
      $this->view->values = $values; 
     
      $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getReports($values);
      $rawdata_array = $rawdata->toarray();
      if($values['select_project'] == 'specific_project')
      {
        $project_id = $values['project_id'];
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project',$project_id);
        $this->view->backedAmount = $project->getFundedAmount();
        $this->view->totalCommission = $project->getTotalCommission();
      }

      $this->view->reportType = $values['report_type'];
 
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitecrowdfunding', 'controller' => 'report', 'action' => 'index', 'empty' => '1'), 'admin_default', true) . '?' . $url_values[1];
      if (empty($rawdata_array)) {
				// Session Object
				$session = new Zend_Session_Namespace('empty_adminredirect');
				$session->empty_session = 1;
        header("Location: $url");
      }
    }
  }
  
  // To display projects in the auto suggest at report form
  public function suggestProjectsAction() {
    $text = $this->_getParam('search'); 
    $limit = $this->_getParam('limit', 40);
    $pageTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
    $select = $pageTable->select()
            ->where('title LIKE ?', '%' . $text . '%'); 
    $select->order('title ASC')->limit($limit);
    $pageObjects = $pageTable->fetchAll($select);

    $data = array();
    $mode = $this->_getParam('struct');
    if ($mode == 'text') {
      foreach ($pageObjects as $pages) {
        $data[] = $pages->title;
      }
    } else {
      foreach ($pageObjects as $pages) {
        $data[] = array(
                'id' => $pages->project_id,
                'label' => $pages->title,
                'photo' => $this->view->itemPhoto($pages, 'thumb.icon'),
        );
      }
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }
}
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InsightController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_InsightsController extends Core_Controller_Action_Standard {

  protected $_navigation;
  protected $_viewer;
  protected $_viewer_id;
  protected $_periods = array(
      Zend_Date::DAY, //dd
      Zend_Date::WEEK, //ww
      Zend_Date::MONTH, //MM
      Zend_Date::YEAR, //y
  );
  protected $_allPeriods = array(
      Zend_Date::SECOND,
      Zend_Date::MINUTE,
      Zend_Date::HOUR,
      Zend_Date::DAY,
      Zend_Date::WEEK,
      Zend_Date::MONTH,
      Zend_Date::YEAR,
  );
  protected $_periodMap = array(
      Zend_Date::DAY => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
      ),
      Zend_Date::WEEK => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::WEEKDAY_8601 => 1,
      ),
      Zend_Date::MONTH => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
      ),
      Zend_Date::YEAR => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
          Zend_Date::MONTH => 1,
      ),
  );

  public function init() {
    $this->_viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $this->_viewer_id = $this->_viewer->getIdentity();
  }

  public function indexAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->page_id = $page_id = $this->_getParam('page_id');
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'insight');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $chunk = Zend_Date::DAY;
    $period = Zend_Date::WEEK;
    $start = time();

    // Make start fit to period?
    $startObject = new Zend_Date($start);

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }
    $startObject->add(1, $chunk);

    $this->view->package_id = $package_id = $this->_getParam('package_id');
    $this->view->sitepages_view_menu = 8;

    if (!Engine_Api::_()->core()->hasSubject('sitepage')) {
      Engine_Api::_()->core()->setSubject($sitepage);
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');
    $this->view->filterForm = $filterForm = new Sitepage_Form_Insights_Filter();
    $values = array();
    $values['page_id'] = $page_id;
    $values['viewer_id'] = $this->_viewer_id;
    $values['month_activeusers'] = 1;

    // Get data
    $statObject = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage')->getInsights($values);
    $rawData = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage')->fetchAll($statObject);
    $total_likes = Engine_Api::_()->sitepage()->getPageLikes($values);

    // check if comments should be displayed or not
    $this->view->show_comments = $show_comments = Engine_Api::_()->sitepage()->displayCommentInsights();
    if (!empty($show_comments)) {
      $this->view->total_comments = $total_comments = Engine_Api::_()->sitepage()->getPageComments($values);
    }

    $new_responder_array = array();
    $merged_array = array();
    $total_users = 0;

    // count total monthly active users
    foreach ($rawData as $rawDatum) {
      $new = 0;
      $rawDatumDate = strtotime($rawDatum->response_date);
      $array = array();

      if (!empty($rawDatum->viewer_id)) {
        $array[] = $rawDatum->viewer_id;
        $new_responder_array[] = $rawDatum->viewer_id;
      }
      $merged_array = array_unique(array_merge($array, $merged_array));
      if (!empty($merged_array)) {
        $new = count($merged_array);
      }
      if (!empty($rawDatum->summation_view)) {
        $total_users = $new;
      }
    }
    $this->view->total_users = $total_users;
    $this->view->total_likes = $total_likes;
    $this->view->total_views = $sitepage->view_count;

    $creation_date = Engine_Api::_()->getItem('sitepage_page', $page_id)->creation_date;
    $this->view->prev_link = 1;
    $this->view->startObject = $startObject = strtotime($startObject);
    $this->view->creation_date = $creation_date = strtotime($creation_date);
    if ($creation_date > $startObject) {
      $this->view->prev_link = 0;
    }
  }

  public function pageStatisticsAction() {
    $page_id = $this->_getParam('page_id');
    if (!empty($page_id)) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $viewer_id = $viewer->getIdentity();
      //GET SUBJECT AND PAGE ID
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      $owner = $sitepage->getOwner();
      $values = array('page_id' => $sitepage->page_id);
      $sub_status_table = Engine_Api::_()->getDbTable('pagestatistics', 'sitepage');
      $statObject = $sub_status_table->pageReportInsights($values);
      $raw_views = $sub_status_table->fetchRow($statObject);
      $raw_views_count = $raw_views['views'];
      if (!$owner->isSelf($viewer)) {
        $sitepage->view_count++;
        $sitepage->save();
        $sub_status_table->pageViewCount($page_id);
      } else if ($sitepage->view_count == 1 && empty($raw_views_count)) {
        $sub_status_table->pageViewCount($page_id);
      }
    }
    exit(0);
  }

  public function chartDataAction() {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    // Get params
    $type = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);
    $page_id = $this->_getParam('page_id');

    // Validate chunk/period
    if (!$chunk || !in_array($chunk, $this->_periods)) {
      $chunk = Zend_Date::DAY;
    }
    if (!$period || !in_array($period, $this->_periods)) {
      $period = Zend_Date::MONTH;
    }

    if (array_search($chunk, $this->_periods) >= array_search($period, $this->_periods)) {
      die('whoops.');
      return;
    }

    // Validate start
    if ($start && !is_numeric($start)) {
      $start = strtotime($start);
    }
    if (!$start) {
      $start = time();
    }

    Zend_Date::setOptions(array(
        'extend_month' => true,
    ));

    // Make start fit to period?
    $startObject = new Zend_Date($start);
    $startObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }

    // Do offset
    if ($offset != 0) {
      $startObject->add($offset, $period);
    }

    // Get end time
    $endObject = new Zend_Date($startObject->getTimestamp());
    $endObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $endObject->add($periodCount, $period);

    $end_tmstmp_obj = new Zend_Date(time());
    $end_tmstmp_obj->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $end_tmstmp = $end_tmstmp_obj->getTimestamp();
    if ($endObject->getTimestamp() < $end_tmstmp) {
      $end_tmstmp = $endObject->getTimestamp();
    }
    $end_tmstmp_object = new Zend_Date($end_tmstmp);
    $end_tmstmp_object->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    $param['page_id'] = $page_id;
    $param['startObject'] = $startObject;
    $param['endObject'] = $endObject;

    $statsTable = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage');
    $statsSelect = $statsTable->getInsights($param);

    $rawData = $statsTable->fetchAll($statsSelect);

    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;

    $data_views = array();
    $data_likes = array();
    $data_users = array();
    $dataLabels = array();
    $cumulative_views = 0;
    $cumulative_likes = 0;
    $cumulative_users = 0;
    $previous_views = 0;
    $previous_likes = 0;
    $previous_users = 0;

    $data_comments = array();
    $cumulative_comments = 0;
    $previous_comments = 0;
    $old_responder_array = array();

    // check if comments should be displayed or not
    $show_comments = Engine_Api::_()->sitepage()->displayCommentInsights();

    do {
      $nextObject->add(1, $chunk);

      $currentObjectTimestamp = $currentObject->getTimestamp();
      $nextObjectTimestamp = $nextObject->getTimestamp();

      $data_views[$currentObjectTimestamp] = $cumulative_views;
      $data_likes[$currentObjectTimestamp] = $cumulative_likes;
      $data_users[$currentObjectTimestamp] = $cumulative_users;
      if (!empty($show_comments)) {
        $data_comments[$currentObjectTimestamp] = $cumulative_comments;
      }

      // Get everything that matches
      $currentPeriodCount_views = 0;
      $currentPeriodCount_likes = 0;
      $currentPeriodCount_users = 0;
      if (!empty($show_comments)) {
        $currentPeriodCount_comments = 0;
      }
      $new_responder_array = array();
      $merged_array = array();
      $new_users = 0;

      foreach ($rawData as $rawDatum) {
        $new = 0;
        $rawDatumDate = strtotime($rawDatum->response_date);
        if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {
          $array = array();

          if (!empty($rawDatum->viewer_id)) {
            $array[] = $rawDatum->viewer_id;
            $new_responder_array[] = $rawDatum->viewer_id;
          }
          $merged_array = array_unique(array_merge($array, $merged_array));

          if (!empty($merged_array)) {
            $new = count($merged_array);
          }
          $currentPeriodCount_views += $rawDatum->summation_view;
          if (!empty($rawDatum->summation_view)) {
            $currentPeriodCount_users = $new;
          }
        }
      }
      $old_responder_array = array_unique(array_merge($new_responder_array, $old_responder_array));
      if (!empty($old_responder_array)) {
        $new_users = count($old_responder_array);
      }

      $vals['page_id'] = $page_id;
      $vals['startTime'] = $currentObjectTimestamp;
      $vals['endTime'] = $nextObjectTimestamp;
      $currentPeriodCount_likes = Engine_Api::_()->sitepage()->getPageLikes($vals);
      if (!empty($show_comments)) {
        $currentPeriodCount_comments = Engine_Api::_()->sitepage()->getPageComments($vals);
      }

      // Now do stuff with it
      switch ($mode) {
        default:
        case 'normal':
          $data_views[$currentObjectTimestamp] = $currentPeriodCount_views;
          $data_likes[$currentObjectTimestamp] = $currentPeriodCount_likes;
          $data_users[$currentObjectTimestamp] = $currentPeriodCount_users;
          if (!empty($show_comments)) {
            $data_comments[$currentObjectTimestamp] = $currentPeriodCount_comments;
          }
          break;
        case 'cumulative':
          $cumulative_views += $currentPeriodCount_views;
          $cumulative_likes += $currentPeriodCount_likes;
          $data_views[$currentObjectTimestamp] = $cumulative_views;
          $data_likes[$currentObjectTimestamp] = $cumulative_likes;
          $data_users[$currentObjectTimestamp] = $new_users;

          if (!empty($show_comments)) {
            $cumulative_comments += $currentPeriodCount_comments;
            $data_comments[$currentObjectTimestamp] = $cumulative_comments;
          }
          break;
        case 'delta':
          $data_views[$currentObjectTimestamp] = $currentPeriodCount_views - $previous_views;
          $data_likes[$currentObjectTimestamp] = $currentPeriodCount_likes - $previous_likes;
          $data_users[$currentObjectTimestamp] = $currentPeriodCount_users - $previous_users;
          $previous_views = $currentPeriodCount_views;
          $previous_likes = $currentPeriodCount_likes;
          $previous_users = $currentPeriodCount_users;
          if (!empty($show_comments)) {
            $data_comments[$currentObjectTimestamp] = $currentPeriodCount_comments - $previous_comments;
            $previous_comments = $currentPeriodCount_comments;
          }
          break;
      }

      $currentObject->add(1, $chunk);
    } while ($nextObject->getTimestamp() < $end_tmstmp);

    $data_views_count = count($data_views);
    $data_likes_count = count($data_likes);
    $data_users_count = count($data_users);
    if (!empty($show_comments)) {
      $data_comments_count = count($data_comments);
    }
    else
      $data_comments_count = 0;
    $data = array();

    switch ($type) {

      case 'all':
        $merged_data_array = array_merge($data_views, $data_likes, $data_users, $data_comments);
        $data_count_max = max($data_views_count, $data_likes_count, $data_users_count, $data_comments_count);
        $data = $data_views;
        break;

      case 'view':
        $data = $merged_data_array = $data_views;
        $data_count_max = $data_views_count;
        break;

      case 'like':
        $data = $merged_data_array = $data_likes;
        $data_count_max = $data_likes_count;
        break;

      case 'active_users':
        $data = $merged_data_array = $data_users;
        $data_count_max = $data_users_count;
        break;

      case 'comment':
        $data = $merged_data_array = $data_comments;
        $data_count_max = $data_comments_count;
        break;
    }

    // Reprocess label
    $labelStrings = array();
    $labelDate = new Zend_Date();
    foreach ($data as $key => $value) {
      if ($key <= $end_tmstmp) {
        $labelDate->set($key);
        $labelStrings[] = $this->view->locale()->toDate($labelDate, array('size' => 'short')); //date('D M d Y', $key);
      } else {
        $labelDate->set($end_tmstmp);
        $labelStrings[] = date('n/j/y', $end_tmstmp);
      }
    }

    // Reprocess label
    $translate = Zend_Registry::get('Zend_Translate');
    $titleStr = $translate->_('_SITEPAGE_INSIGHTS_' .strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_')));
    $title = $titleStr . ': '. $this->view->locale()->toDateTime($startObject) . ' to ' . $this->view->locale()->toDateTime($endObject);

    // Make data
    switch ($type) {

      case 'all':
          $labelStrings = '';
          $labelData1 = array();
          $labelData1['Date'] = 'Views';
          $labelDate = new Zend_Date();
          foreach ($data_views as $key => $value) {
            $labelDate->set($key);
            $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
            $labelData1["$labelStrings"] = $value;
          }
          $labelStrings = '';
          $labelData2 = array();
          $labelData2['Date'] = 'Likes';
          $labelDate = new Zend_Date();
          foreach ($data_likes as $key => $value) {
            $labelDate->set($key);
            $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
            $labelData2["$labelStrings"] = $value;
          }
            $labelStrings = '';
            $labelData4 = array();
            $labelData4['Date'] = 'Active Users';
            $labelDate = new Zend_Date();
            foreach ($data_users as $key => $value) {
              $labelDate->set($key);
              $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
              $labelData4["$labelStrings"] = $value;
            }
          if (!empty($show_comments)) {
            $labelStrings = '';
            $labelData3 = array();
            $labelData3['Date'] = 'Comments';
            $labelDate = new Zend_Date();
            foreach ($data_comments as $key => $value) {
              $labelDate->set($key);
              $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
              $labelData3["$labelStrings"] = $value;
            }
          }
          $label[]=array('Date','Views','Likes','Comments','Active Users');
          foreach ($data_likes as $key => $value) {
            $labelDate->set($key);
            $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
            $label[]=array($labelStrings,(int)$labelData1[$labelStrings],(int)$labelData2[$labelStrings],(int)$labelData3[$labelStrings],(int)$labelData4[$labelStrings]);
          }
          $this->view->case="all";
          $this->view->data=$label;
           break;

           case 'view':
            $labelStrings = '';
            $labelData = array();
            $labelData['Date'] = $titleStr;
            $labelDate = new Zend_Date();
            foreach ($data_views as $key => $value) {
              $labelDate->set($key);
              $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
              $labelData["$labelStrings"] = $value;
            }
            $this->view->data=$labelData;
            break;

           case 'like':
            $labelStrings = '';
            $labelData = array();
            $labelData['Date'] = $titleStr;
            $labelDate = new Zend_Date();
            foreach ($data_likes as $key => $value) {
              $labelDate->set($key);
              $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
              $labelData["$labelStrings"] = $value;
            }
            $this->view->data=$labelData;
            break;

          case 'comment':
            $labelStrings = '';
            $labelData = array();
            $labelData['Date'] = $titleStr;
            $labelDate = new Zend_Date();
            foreach ($data_comments as $key => $value) {
              $labelDate->set($key);
              $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
              $labelData["$labelStrings"] = $value;
            }
           $this->view->data=$labelData;
           break;

          case 'active_users':
            $labelStrings = '';
            $labelData = array();
            $labelData['Date'] = $titleStr;
            $labelDate = new Zend_Date();
            foreach ($data_users as $key => $value) {
              $labelDate->set($key);
              $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
              $labelData["$labelStrings"] = $value;
            }
           $this->view->data=$labelData ;
           break;
       }

   $this->view->title=$title;
  }

  public function exportReportAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_quick');
    $this->view->showMarkerInDate = $this->showMarkerInDate();
    $this->view->sitepages_view_menu = 23;
    $this->view->page_id = $page_id = $this->_getParam('page_id');
    if (empty($page_id) && !empty($_GET['page_id'])) {
      $page_id = $_GET['page_id'];
    }
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'insight');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    if (!Engine_Api::_()->core()->hasSubject('sitepage')) {
      Engine_Api::_()->core()->setSubject($sitepage);
    }

    // Get viewer's Pages
    $pagesTable = Engine_Api::_()->getDbtable('pages', 'Sitepage');
    $pagesName = $pagesTable->info('name');

    // to calculate the oldest page's creation year
    $select = $pagesTable->select();
    $select
            ->from($pagesName, array('page_id', 'MIN(creation_date) as min_year'))
            ->where('owner_id = ?', $this->_viewer_id)
            ->group('page_id')
            ->limit(1);

    $this->view->no_pages = 0;
    $min_year = $pagesTable->fetchRow($select);
    $date = explode(' ', $min_year['min_year']);
    $yr = explode('-', $date[0]);
    $current_yr = date('Y', time());
    $year_array = array();
    $year_array[$current_yr] = $current_yr;
    while ($current_yr != $yr[0]) {
      $current_yr--;
      $year_array[$current_yr] = $current_yr;
    }

    $this->view->reportform = $reportform = new Sitepage_Form_Insights_Report();
    $reportform->year_start->setMultiOptions($year_array);
    $reportform->year_end->setMultiOptions($year_array);
    $reportform->page_id->setValue($page_id);
    $this->view->prefield = 0;

    // populate form
    if (!empty($_GET['time_summary'])) {
      $this->view->prefield = 1;
      $reportform->populate($_GET);

      // Get Form Values
      $values = $reportform->getValues();
      $start_cal_date = $values['start_cal'];
      $end_cal_date = $values['end_cal'];
      $start_tm = strtotime($start_cal_date);
      $end_tm = strtotime($end_cal_date);
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);

      if (empty($values['format_report'])) {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('page_id' => $page_id, 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'sitepage_webpagereport', true) . '?' . $url_values[1];
      } else {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitepage', 'controller' => 'insights', 'action' => 'export-excel', 'page_id' => $page_id, 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'default', true) . '?' . $url_values[1];
      }
      // Session Object
      $session = new Zend_Session_Namespace('empty_redirect');
      if (isset($session->empty_session) && !empty($session->empty_session)) {
        unset($session->empty_session);
      } else {
        header("Location: $url");
      }
    }

    $this->view->empty = $this->_getParam('empty', 0);
  }

  public function exportExcelAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->sitepages_view_menu = 23;
    $this->view->post = $post = 0;
    $start_daily_time = $this->_getParam('start_daily_time', time());
    $end_daily_time = $this->_getParam('end_daily_time', time());

    if (!empty($_GET)) {
      $this->_helper->layout->setLayout('default-simple');
      $this->view->post = $post = 1;
      $values = $_GET;
      $this->view->page_id = $page_id = $values['page_id'];
      $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
      if (empty($isManageAdmin)) {
        return $this->_forward('requireauth', 'error', 'core');
      }

      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'insight');
      if (empty($isManageAdmin)) {
        return $this->_forward('requireauth', 'error', 'core');
      }
      //END MANAGE-ADMIN CHECK

      if (!Engine_Api::_()->core()->hasSubject('sitepage')) {
        Engine_Api::_()->core()->setSubject($sitepage);
      }

      $values = array_merge(array(
          'start_daily_time' => $start_daily_time,
          'end_daily_time' => $end_daily_time,
          'user_report' => '5',
          'viewer_id' => $this->_viewer_id,
              ), $values);
      $this->view->values = $values;

      $statTable = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage');
      $statsName = $statTable->info('name');

      // check if comments should be displayed or not
      $this->view->show_comments = $show_comments = Engine_Api::_()->sitepage()->displayCommentInsights();
      $rawdata = array();

      // FETCH VIEWS
      $statObject2 = $statTable->pageReportInsights($values);
      $rawviews = $statTable->fetchAll($statObject2);

      $rawviews_array = $rawviews->toarray();

      if (!empty($rawviews_array)) {
        foreach ($rawviews_array as $views) {
          $response_date = explode(' ', $views['response_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value]['views'] = $views['views'];
              $rawdata[$date_value]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($views['response_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['views'] = $views['views'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH ACTIVE USERS
      $values = array_merge(array(
          'active_user' => '10',
              ), $values);

      $statObject = $statTable->pageReportInsights($values);
      $raw_activeusers = $statTable->fetchAll($statObject);
      $raw_activeusers_array = $raw_activeusers->toarray();


      if (!empty($raw_activeusers_array)) {
        foreach ($raw_activeusers_array as $activeusers) {
          $response_date = explode(' ', $activeusers['response_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value]['active_users'] = $activeusers['viewers'];
              $rawdata[$date_value]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($activeusers['response_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['active_users'] = $activeusers['viewers'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH LIKES
      $this->view->total_likes = $total_likes = Engine_Api::_()->sitepage()->getReportLikes($values);
      foreach ($total_likes as $likes) {
        if (!empty($likes['creation_date'])) {
          $response_date = explode(' ', $likes['creation_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value]['likes'] = $likes['page_likes'];
              $rawdata[$date_value]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($likes['creation_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['likes'] = $likes['page_likes'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH COMMENTS
      if (!empty($show_comments)) {
        $this->view->total_comments = $total_comments = Engine_Api::_()->sitepage()->getReportComments($values);
        foreach ($total_comments as $comments) {
          if (!empty($comments['creation_date'])) {
            $response_date = explode(' ', $comments['creation_date']);
            $date_array = explode('-', $response_date[0]);

            switch ($values['time_summary']) {
              case 'Monthly':
                $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
                $rawdata[$date_value]['comments'] = $comments['page_comments'];
                $rawdata[$date_value]['date_value'] = $date_value;
                break;

              case 'Daily':
                $response_time = strtotime($comments['creation_date']);
                $labelDate = new Zend_Date();
                $labelDate->set($response_time);
                $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
                $rawdata[$response_date[0]]['comments'] = $comments['page_comments'];
                $rawdata[$response_date[0]]['date_value'] = $date_value;
                break;
            }
          }
        }
      }
      $this->view->rawdata = $rawdata;

      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('page_id' => $values['page_id'], 'empty' => 1), 'sitepage_reports', true) . '?' . $url_values[1];

      // in case no data, redirect to the report page with an empty parameter
      if (empty($rawdata)) {

        // Session Object
        $session = new Zend_Session_Namespace('empty_redirect');
        $session->empty_session = 1;
        header("Location: $url");
      }
    }
  }

  public function exportWebpageAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_quick');

    $this->view->sitepages_view_menu = 23;
    $this->view->post = $post = 0;
    $start_daily_time = $this->_getParam('start_daily_time', time());
    $end_daily_time = $this->_getParam('end_daily_time', time());

    if (!empty($_GET)) {
      $this->view->post = $post = 1;
      $values = $_GET;
      $this->view->page_id = $page_id = $values['page_id'];
      $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
      if (empty($isManageAdmin)) {
        return $this->_forward('requireauth', 'error', 'core');
      }

      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'insight');
      if (empty($isManageAdmin)) {
        return $this->_forward('requireauth', 'error', 'core');
      }
      //END MANAGE-ADMIN CHECK

      if (!Engine_Api::_()->core()->hasSubject('sitepage')) {
        Engine_Api::_()->core()->setSubject($sitepage);
      }

      $values = array_merge(array(
          'start_daily_time' => $start_daily_time,
          'end_daily_time' => $end_daily_time,
          'user_report' => '5',
          'viewer_id' => $this->_viewer_id,
          'total_stats' => '10',
              ), $values);
      $this->view->values = $values;

      $statTable = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage');
      $statsName = $statTable->info('name');

      $totalObject = $statTable->pageReportInsights($values);
      $rawTotalviews = $statTable->fetchRow($totalObject);
      if (!empty($rawTotalviews)) {
        $this->view->total_views = $rawTotalviews = $rawTotalviews['views'];
      } else {
        $this->view->total_views = $rawTotalviews = 0;
      }
      $totallikes = Engine_Api::_()->sitepage()->getReportLikes($values);
      if (!empty($totallikes)) {
        $this->view->totallikes = $totallikes = $totallikes[0]['page_likes'];
      } else {
        $this->view->totallikes = $totallikes = 0;
      }

      // check if comments should be displayed or not
      $this->view->show_comments = $show_comments = Engine_Api::_()->sitepage()->displayCommentInsights();
      if (!empty($show_comments)) {
        $totalcomments = Engine_Api::_()->sitepage()->getReportComments($values);
        if (!empty($totalcomments)) {
          $this->view->totalcomments = $totalcomments = $totalcomments[0]['page_comments'];
        } else {
          $this->view->totalcomments = $totalcomments = 0;
        }
      }

      $totalObject2 = $statTable->pageReportInsights($values);
      $rawTotalusers = $statTable->fetchRow($totalObject2);
      if (!empty($rawTotalusers)) {
        $rawTotalusers = $rawTotalusers['viewers'];
      }
      else
        $rawTotalusers = 0;

      // check non-logged-in user views and decrement active users by 1 if exist
      $select_non_loggedins = $statTable->select();
      $select_non_loggedins->from($statsName, array('pagestatistic_id', 'viewer_id'))->where('page_id = ?', $page_id)->where('viewer_id = ?', 0)->limit(1);
      $is_non_loggedin = $statTable->fetchRow($select_non_loggedins);

      if (!empty($is_non_loggedin)) {
        $rawTotalusers = $rawTotalusers - 1;
      }
      $this->view->totalusers = $rawTotalusers;

      unset($values['total_stats']);
      $rawdata = array();

      // FETCH VIEWS
      $statObject2 = $statTable->pageReportInsights($values);
      $rawviews = $statTable->fetchAll($statObject2);
      $rawviews_array = $rawviews->toarray();

      if (!empty($rawviews_array)) {
        foreach ($rawviews_array as $views) {
          $response_date = explode(' ', $views['response_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value]['views'] = $views['views'];
              $rawdata[$date_value]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($views['response_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['views'] = $views['views'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH ACTIVE USERS
      $values = array_merge(array(
          'active_user' => '10',
              ), $values);

      $statObject = $statTable->pageReportInsights($values);
      $raw_activeusers = $statTable->fetchAll($statObject);
      $raw_activeusers_array = $raw_activeusers->toarray();

      if (!empty($raw_activeusers_array)) {
        foreach ($raw_activeusers_array as $activeusers) {
          $response_date = explode(' ', $activeusers['response_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value]['active_users'] = $activeusers['viewers'];
              $rawdata[$date_value]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($activeusers['response_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['active_users'] = $activeusers['viewers'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH LIKES
      $this->view->total_likes = $total_likes = Engine_Api::_()->sitepage()->getReportLikes($values);
      foreach ($total_likes as $likes) {
        if (!empty($likes['creation_date'])) {
          $response_date = explode(' ', $likes['creation_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value]['likes'] = $likes['page_likes'];
              $rawdata[$date_value]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($likes['creation_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['likes'] = $likes['page_likes'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH COMMENTS
      if (!empty($show_comments)) {
        $this->view->total_comments = $total_comments = Engine_Api::_()->sitepage()->getReportComments($values);
        foreach ($total_comments as $comments) {
          if (!empty($comments['creation_date'])) {
            $response_date = explode(' ', $comments['creation_date']);
            $date_array = explode('-', $response_date[0]);

            switch ($values['time_summary']) {
              case 'Monthly':
                $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
                $rawdata[$date_value]['comments'] = $comments['page_comments'];
                $rawdata[$date_value]['date_value'] = $date_value;
                break;

              case 'Daily':
                $response_time = strtotime($comments['creation_date']);
                $labelDate = new Zend_Date();
                $labelDate->set($response_time);
                $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
                $rawdata[$response_date[0]]['comments'] = $comments['page_comments'];
                $rawdata[$response_date[0]]['date_value'] = $date_value;
                break;
            }
          }
        }
      }
      $this->view->rawdata = $rawdata;

      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('page_id' => $values['page_id'], 'empty' => 1), 'sitepage_reports', true) . '?' . $url_values[1];

      // in case no data, redirect to the report page with an empty parameter
      if (empty($rawdata)) {

        // Session Object
        $session = new Zend_Session_Namespace('empty_redirect');
        $session->empty_session = 1;
        header("Location: $url");
      }
    }
  }

  public function showMarkerInDate() {
    $localeObject = Zend_Registry::get('Locale');
    $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
    $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
    $dateLocaleString = strtolower($dateLocaleString);
    $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
    $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('y', 'm', 'd'), $dateLocaleString);
    $dateFormat = $dateLocaleString;
    return $dateFormat == "mdy" ? 1 : 0;
  }

}

?>
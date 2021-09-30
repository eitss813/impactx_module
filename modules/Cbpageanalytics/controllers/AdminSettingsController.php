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
class Cbpageanalytics_AdminSettingsController extends Core_Controller_Action_Admin {

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

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('cbpageanalytics_admin_main', array(), 'cbpageanalytics_admin_main_settings');

        $this->view->form = $form = new Cbpageanalytics_Form_Admin_Global();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();

            foreach ($values as $key => $value) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
            }

            $form->addNotice('Your changes have been saved.');
        }
    }

    public function pageAnalyticsAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('cbpageanalytics_admin_main', array(), 'cbpageanalytics_admin_main_analytics');

        $values = array();
        $this->view->formFilter = $formFilter = new Cbpageanalytics_Form_Admin_Filter();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        // Get Users
        $usersTable = Engine_Api::_()->getItemTable('user');
        $dataUsers = $dataUsers = $usersTable->fetchAll($usersTable->select()->where('enabled = ?', 1)->where('verified = ?', 1)->where('approved = ?', 1));

        $users = array('0' => 'All Users');
        foreach ($dataUsers as $user) {
            $users[$user->getIdentity()] = $user->getTitle();
        }

        $this->view->users = $users;

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'cbpageanalytics');
        $pages = $pagesTable->fetchAll($pagesTable->select()->where('status = ?', 1));
        $this->view->pageAvailable = $pages->toArray();

        $page = $this->_getParam('page', 1);
        $pagesTableName = $pagesTable->info('name');

        if (!empty($_GET['title'])) {
            $title = $_GET['title'];
        } else {
            $title = '';
        }

        if (!empty($_GET['group_by'])) {
            $group_by = $_GET['group_by'];
        } else {
            $group_by = '';
        }

        if (!empty($_GET['user'])) {
            $user = $_GET['user'];
        } else {
            $user = '';
        }

        if (!empty($group_by)) {
            $select = $pagesTable->select()->from($pagesTableName, array('*', 'total_views' => 'sum(view_count)'))->where('status = ?', 1);
        } else {
            $select = $pagesTable->select()->where('status = ?', 1);
        }

        $this->view->group_by = $values['group_by'] = $group_by;
        $this->view->title = $values['title'] = $title;
        $this->view->user = $values['user'] = $user;

        if (!empty($title)) {
            $select->where($pagesTableName . '.title LIKE ?', '%' . trim($title) . '%');
        }

        if (!empty($user) && $user != 'All Users') {
            $userData = $usersTable->fetchRow($usersTable->select()->where('displayname = ?', $user));
            $select->where($pagesTableName . '.user_id = ?', $userData->getIdentity());
        }

        $values = array_merge(array(
            'order' => 'page_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->formValues = array_filter($values);
        $this->view->assign($values);

        if (!empty($group_by) && $group_by != 'none') {
            if ($group_by == 'creation_date') {
                $select->group(new Zend_Db_Expr("CAST(" . $pagesTableName . " .creation_date AS DATE)"));
            } else {
                $select->group($pagesTableName . ".$group_by");
                $select->group(new Zend_Db_Expr("CAST(" . $pagesTableName . " .creation_date AS DATE)"));
            }
        }

        $select->order((!empty($values['order']) ? $values['order'] : 'page_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        //MAKE PAGINATOR
        $this->view->paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(50);
        $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
    }

    public function graphAnalyticsAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('cbpageanalytics_admin_main', array(), 'cbpageanalytics_admin_main_graphs');

        // Get pages
        $table = Engine_Api::_()->getDbtable('pages', 'core');
        $dataPages = $table->fetchAll($table->select()->where('page_id NOT IN (?)', array('1', '2')));

        $pages = array('1' => 'All Pages');
        foreach ($dataPages as $page) {
            $pages[$page->page_id] = $page->displayname;
        }

        // Get Users
        $usersTable = Engine_Api::_()->getItemTable('user');
        $dataUsers = $usersTable->fetchAll($usersTable->select()->where('enabled = ?', 1)->where('verified = ?', 1)->where('approved = ?', 1));

        $users = array('0' => 'All Users');
        foreach ($dataUsers as $user) {
            $users[$user->getIdentity()] = $user->getTitle();
        }

        $this->view->users = $users;

        $this->view->formFilter = $formFilter = new Cbpageanalytics_Form_Admin_Statistics_Filter();
        $formFilter->page->setMultiOptions($pages);
    }

    public function chartDataAction() {
        // Disable layout and viewrenderer
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // Get params
        $pageID = $this->_getParam('page', '1');
        $user = $this->_getParam('user', NULL);
        $start = $this->_getParam('start');
        $offset = $this->_getParam('offset', 0);
        $chunk = $this->_getParam('chunk');
        $period = $this->_getParam('period');
        $periodCount = $this->_getParam('periodCount', 1);

        $usersTable = Engine_Api::_()->getItemTable('user');
        
        //$end = $this->_getParam('end');
        // Validate chunk/period
        if (!$chunk || !in_array($chunk, $this->_periods)) {
            $chunk = Zend_Date::DAY;
        }

        if (!$period || !in_array($period, $this->_periods)) {
            $period = Zend_Date::MONTH;
        }

        if (array_search($chunk, $this->_periods) >= array_search($period, $this->_periods)) {
            //die('whoops');
            //return;
        }

        // Validate start
        if ($start && !is_numeric($start)) {
            $start = strtotime($start);
        }
        if (!$start) {
            $start = time();
        }

        // Fixes issues with month view
        Zend_Date::setOptions(array(
            'extend_month' => true,
        ));

        // Get timezone
        $timezone = Engine_Api::_()->getApi('settings', 'core')
                ->getSetting('core_locale_timezone', 'GMT');

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer && $viewer->getIdentity() && !empty($viewer->timezone)) {
            $timezone = $viewer->timezone;
        }

        // Make start fit to period?
        $startObject = new Zend_Date($start);
        $startObject->setTimezone($timezone);

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
        $endObject->setTimezone($timezone);
        $endObject->add($periodCount, $period);
        $endObject->sub(1, Zend_Date::SECOND); // Subtract one second
        // Get data
        $table = Engine_Api::_()->getDbtable('pages', 'cbpageanalytics');
        $tableName = $table->info('name');
        $statsSelect = $table->select()->from($tableName, array('*', 'total_views' => 'sum(view_count)'));

        if ($pageID != '1' && $pageID != null) {
            $statsSelect->where($tableName . '.page_original_id = ?', $pageID);
        }

        if ($user != NULL && $user != 'All Users') {
            $userData = $usersTable->fetchRow($usersTable->select()->where('displayname = ?', $user));
            $statsSelect->where($tableName . '.user_id = ?', $userData->getIdentity());
        }

        $statsSelect->where($tableName . '.creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()));
        $statsSelect->where($tableName . '.creation_date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()));
        $statsSelect->where($tableName . '.status = ?', 1);
        $statsSelect->group($tableName . '.title');
        $statsSelect->group(new Zend_Db_Expr("CAST(" . $tableName . " .creation_date AS DATE)"));
        $statsSelect->order($tableName . '.creation_date ASC');

        $rawData = $table->fetchAll($statsSelect);

        // Reprocess label
        if ($pageID != '1' && $pageID != null) {
            $page = Engine_Api::_()->getItem('core_page', $pageID);

            $titleStr = $page->displayname . ' views';
        } else {
            $titleStr = 'All views';
        }

        // Now create data structure
        $currentObject = clone $startObject;
        $nextObject = clone $startObject;
        $data = array();
        $cumulative = 0;

        do {
            $nextObject->add(1, $chunk);

            $currentObjectTimestamp = $currentObject->getTimestamp();
            $nextObjectTimestamp = $nextObject->getTimestamp();

            $data[$currentObjectTimestamp][''] = $cumulative;

            foreach ($rawData as $rawDatum) {
                $rawDatumDate = strtotime($rawDatum->creation_date);
                if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {
                    $data[$currentObjectTimestamp][$rawDatum->getTitle()] = $rawDatum->total_views;
                }
            }

            $currentObject->add(1, $chunk);
        } while ($currentObject->getTimestamp() < $endObject->getTimestamp());

        $title = $titleStr . ': ' . $this->view->locale()->toDateTime($startObject) . ' to ' . $this->view->locale()->toDateTime($endObject);

        $labelStrings = '';
        $labelData = array();
        $labelDate = new Zend_Date();
        foreach ($data as $key => $value) {
            $labelDate->set($key);
            foreach ($value as $k => $v) {
                $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
                $labelData[$labelStrings][$k] = (int) $v;
            }
        }

        $this->view->title = $title;
        $this->view->data = $labelData;
    }

    public function viewDetailAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');

        $id = $this->_getParam('id');
        $pagesTable = Engine_Api::_()->getDbtable('pages', 'cbpageanalytics');

        $this->view->page = $page = $pagesTable->fetchRow($pagesTable->select()->where('page_id = ?', $id));
    }

    public function deleteAction() {
        //delete a code
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->page_id = $id;

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'cbpageanalytics');

        // Check post
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $page = $pagesTable->fetchRow($pagesTable->select()->where('page_id = ?', $id));

                $page->status = 0;
                $page->save();

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
    }

}

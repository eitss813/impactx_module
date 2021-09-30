<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecredit
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_AdminStatisticsController extends Core_Controller_Action_Admin {

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
                ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_stats');

        $this->view->searchAjax = $this->_getParam('searchAjax', false);
        $creditTable = Engine_Api::_()->getDbtable('statistics', 'siteotpverifier');
        $creditTableName = $creditTable->info('name');

        $select = $creditTable->select();
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
        $this->view->is_ajax = $this->_getParam('is_ajax', 0);
        $this->view->formFilterGraph = $formFilterGraph = new Siteotpverifier_Form_Admin_Statistics_FilterGraph();
        // get period and chunk object here.
        $getFormElements = $formFilterGraph->getElements();
        $firstClass = true;
        foreach ($getFormElements as $formKey => $formElement) {
            $label = $formFilterGraph->$formKey->getLabel();
            $formFilterGraph->$formKey->setDecorators(array('ViewHelper', array(array('label' => 'HtmlTag'), array('class' => $formKey, 'tag' => 'label', 'placement' => 'prepend', 'for' => $formKey)), array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => $firstClass ? 'custom-divs-first' : 'custom-divs'))));
            $firstClass = false;
            $formFilterGraph->$formKey->setAttrib('class', 'label-field');
            if ($formFilterGraph->$formKey->type == 'submit') {
                continue;
            }
            $labels[$formKey] = $label;
        }
        $this->view->getFormLabels = json_encode($labels);
        $this->view->periodOption = json_encode($formFilterGraph->period->options);
        $this->view->chunkOption = json_encode($formFilterGraph->chunk->options);
        $this->view->periodOptionKey = json_encode(array_keys($formFilterGraph->period->options));
        $this->view->chunkOptionKey = json_encode(array_keys($formFilterGraph->chunk->options));
        $date_select = $select->from($creditTable, array('MIN(creation_date) as earliest_creation_date'));
        $earliest_creation_date = $select->query()
                ->fetchColumn();
        $this->view->prev_link = 1;
        $this->view->startObject = $startObject = strtotime($startObject);
        $this->view->earliest_ad_date = $earliest_creation_date = strtotime($earliest_creation_date);
        if ($earliest_creation_date > $startObject) {
            $this->view->prev_link = 0;
        }
    }

    public function chartDataAction() {
        // fetch and assign data for statistics
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

        // Fixes issues with month view
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

        // Get data
        $statsTable = Engine_Api::_()->getDbtable('statistics', 'siteotpverifier');
        $statsName = $statsTable->info('name');

        $statsSelect = $statsTable->select();
        // check for selected data
        $statsSelect
                ->from($statsName, array('count(*) as message_sent', 'creation_date as timestamp'))
                ->where($statsName . '.creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
                ->where($statsName . '.creation_date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()));
        switch ($mode) {
            case "all" : break;
            case "signup" : $statsSelect->where($statsName . '.type = "signup" ');
                break;
            case "login" : $statsSelect->where($statsName . '.type = "login" ');
                break;
            case "forget" : $statsSelect->where($statsName . '.type = "forget" ');
                break;
            case "edit" : $statsSelect->where($statsName . '.type = "edit" ');
                break;
            case "add" : $statsSelect->where($statsName . '.type = "add" ');
                break;
            case "add" : $statsSelect->where($statsName . '.type = "admin_sent" ');
                break;
        }
        $statsSelect->where($statsName . '.service = ?',$type);
        $statsSelect->group("DATE_FORMAT(" . $statsName . " .creation_date, '%Y-%m-%d')")
                ->order($statsName . '.creation_date ASC')
                ->distinct(true);
        $rawData = $statsTable->fetchAll($statsSelect);
        $translate = Zend_Registry::get('Zend_Translate');
        $titleStr = $translate->_('Messages Statistics');
        $title = $titleStr . ': '. $this->view->locale()->toDateTime($startObject) . ' to ' . $this->view->locale()->toDateTime($endObject);

        // Now create data structure
        $currentObject = clone $startObject;
        $nextObject = clone $startObject;

        $data_message_sent = array();

        $cumulative_sent = 0;

        $previous_sent = 0;

        $oldtimestamp = $currentObject->getTimestamp();
        do {

            $nextObject->add(1, $chunk);
            $currentObjectTimestamp = $this->view->locale()->toDate($currentObject->getTimestamp(), array('size' => 'short'));
            $data_message_sent[$currentObjectTimestamp] = $cumulative_sent;

            // Get everything that matches
            $currentPeriodCount_sent = 0;

            foreach ($rawData as $key => $rawDatum) {
                $timestamp = explode(" ", $rawDatum->timestamp);
                $rawDatumDate = strtotime($timestamp[0] . '00:00:00');
                if ($rawDatumDate <= $currentObjectTimestamp && $rawDatumDate > $oldtimestamp) {
                    $currentPeriodCount_sent = abs($rawDatum->message_sent);
                    $oldtimestamp = $rawDatumDate;
                }
            }


            $data_message_sent[$currentObjectTimestamp] = $currentPeriodCount_sent;

            $currentObject->add(1, $chunk);
        } while ($currentObject->getTimestamp() < $end_tmstmp);

        $data_message_sent_count = count($data_message_sent);


        $data = array();

        $merged_data_array = $data_message_sent;
        $data_count_max = $data_message_sent_count;

        $labelStrings = '';
        $labelData = array();
        $labelData['Date'] = Zend_Registry::get('Zend_Translate')->_('Messages sent');
        $labelDate = new Zend_Date();
        foreach ($data_message_sent as $key => $value) {
          $labelDate->set($key);
          $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
          $labelData["$labelStrings"] = $value;
        }
        $this->view->data = $labelData ;
        $this->view->title = $title;
    }

}

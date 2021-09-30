<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InsightNotification.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Plugin_Task_InsightNotification extends Core_Plugin_Task_Abstract {

  public function execute() {

    // create an object for view
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $sitepageInsightmail = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.insightemail', 1);
		if(empty($sitepageInsightmail))
		{
      return;
		}
    $date = time();
    $days_string = "";
    $insight_email_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.insightmail.time', 1);

    // fetch that time stamp when the reminder mail was last sent
    $taskstable = Engine_Api::_()->getDbtable('tasks', 'core');
    $rtasksName = $taskstable->info('name');
    $taskstable_result = $taskstable->select()
            ->from($rtasksName, array('success_last'))
            ->where('title = ?', 'Sitepage Insight Mail')
            ->where('plugin = ?', 'Sitepage_Plugin_Task_InsightNotification')
            ->limit(1);

    $value = $taskstable->fetchRow($taskstable_result);
    $old_success_last = $value['success_last'];

    // find the difference between current time and the time when the mail was last sent successfully
    $diff = (int) (($date - $old_success_last) / 86400);
		$statTable = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage');

    switch ($insight_email_time) {
      // 1st Case when mail reminder for this week's insights has to be sent on sunday
      case 1:
        $current_day = date('w', $date);
        $days_missed = $current_day;
        $vals = array(
            'time' => '7',
            'days_missed' => $days_missed,
        );

        // if current day is sunday, send mail in any case
        if ($current_day == 0) {
          $days_string.= $view->translate(" week");
          $vals['days_string'] = $days_string;
					$statTable->insightsMailSend($vals);
        }
        // if due to inactive state of site mail was not sent on sunday send it on tuesday or so on till saturday
        else {
          // check if the time difference is greater than the time that has passed from this week's start
          if ($diff > $current_day) {
            $days_string.= $view->translate(" week");
            $vals['days_string'] = $days_string;
						$statTable->insightsMailSend($vals);
          }
        }
        break;

      // 2nd Case when mail reminder for this month's insights has to be sent on 1st day of the month
      case 2:
        $current_date = date('j', $date);
        $days_in_month = date('t', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')));

        // days passsed in this month
        $days_missed = $current_date - 1;
        $vals = array(
            'time' => $days_in_month,
            'days_missed' => $days_missed,
        );

        // if current day is 1st date of the month, send mail in any case
        if ($current_date == 01) {
          $days_string.= $view->translate(" month");
          $vals['days_string'] = $days_string;
					$statTable->insightsMailSend($vals);
        }

        // if due to inactive state of site mail was not sent on 1st send it on 2nd or so on till 2nd last date of the month
				// check if the time difference is greater than the time that has passed from this month's start
        else if ($diff > $current_date) {
					$days_string.= $view->translate(" month");
					$vals['days_string'] = $days_string;
					$statTable->insightsMailSend($vals);
				}
        break;
    }
  }

}

?>
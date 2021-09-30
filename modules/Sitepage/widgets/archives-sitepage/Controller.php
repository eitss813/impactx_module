<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_ArchivesSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET ARCHIVE SITEPAGE OF SITEPAGE USER
    $archiveSitepage =  Engine_Api::_()->getDbTable('pages', 'sitepage')->getArchiveSitepage(null);

		//CALL TO handleArchiveSitepage ACTION OF SAME CONTROLLER
    $this->view->archive_sitepage = $this->_handleArchiveSitepage($archiveSitepage);

    if (!(count($this->view->archive_sitepage) > 0)) {
      return $this->setNoRender();
    } 

    if (isset($_GET['start_date'])) {
      $this->view->start_date = $_GET['start_date'];
		}
  }

  //ACTION FOR handleArchiveSitepage
  protected function _handleArchiveSitepage($results) {

    $localeObject = Zend_Registry::get('Locale');
    $sitepage_dates = array();
    foreach ($results as $result)
      $sitepage_dates[] = strtotime($result->creation_date);

    //GET ARCHIVE SITEPAGE
    $time = time();
    $archive_sitepage = array();

    foreach ($sitepage_dates as $sitepage_date) {
      $ltime = localtime($sitepage_date, TRUE);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;

      //LESS THAN A YEAR AGO - MONTHS
      if ($sitepage_date + 31536000 > $time) {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"] + 1, 1, $ltime["tm_year"]);
        $label = date('F Y', $sitepage_date);
        $type = 'month';
      }

      //MORE THAN A YEAR AGO - YEARS
      else {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"] + 1);
        $type = 'year';

        $dateObject = new Zend_Date($sitepage_date);
        $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
        if (!$format) {
          $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
        }
        $label = $dateObject->toString($format, $localeObject);
      }

      if (!isset($archive_sitepage[$date_start])) {
        $archive_sitepage[$date_start] = array(
            'type' => $type,
            'label' => $label,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'count' => 1
        );
      } else {
        $archive_sitepage[$date_start]['count']++;
      }
    }
    return $archive_sitepage;
  }
}

?>

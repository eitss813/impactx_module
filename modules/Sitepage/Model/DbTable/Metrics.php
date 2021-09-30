<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Pages.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Metrics extends Engine_Db_Table
{

    protected $_rowClass = 'Sitepage_Model_Metric';

    // all metrics
    public function getMetricsData()
    {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName);

        return  $metricsTable->fetchAll($metricsSelect);

    }

    // metrics by organisation-id
    public function getMetricsDataByOrganisationId($page_id)
    {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where('page_id = ?', $page_id)
            ->where('visibility = ?', 1);

        return  $metricsTable->fetchAll($metricsSelect);

    }

    public function getMetricsDataByOrganisationIdPaginator($page_id,$metric_page_no) {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where('page_id = ?', $page_id)
            ->where('visibility = ?', 1);

        $paginator = Zend_Paginator::factory($metricsSelect);
        $paginator->setCurrentPageNumber($metric_page_no);
        $paginator->setItemCountPerPage(4);

        return $paginator;

    }

    public function getMetricsDataByIdPaginator($metric_id,$metric_page_no) {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where('metric_id in (?)', $metric_id);

        $paginator = Zend_Paginator::factory($metricsSelect);
        $paginator->setCurrentPageNumber($metric_page_no);
        $paginator->setItemCountPerPage(4);

        return $paginator;

    }

    public function getProjectVisibilityMetricsDataByIdPaginator($metric_id,$metric_page_no) {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where('metric_id in (?)', $metric_id)
            ->where('project_side_visibility = ?', 1);

        $paginator = Zend_Paginator::factory($metricsSelect);
        $paginator->setCurrentPageNumber($metric_page_no);
        $paginator->setItemCountPerPage(4);

        return $paginator;

    }

    public function getMetricsDataById($metric_id) {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where('metric_id in (?)', $metric_id);

        return  $metricsTable->fetchAll($metricsSelect);

    }

    // metrics by organisation-id
    public function getAllMetricsDataByOrganisationId($page_id)
    {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where('page_id = ?', $page_id);

        return  $metricsTable->fetchAll($metricsSelect);

    }

    // metrics by organisation-id
    public function getMetricsDataByOrganisationIdAndText($page_id,$text)
    {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        if($text){
            $metricsSelect = $metricsTable->select()
                ->setIntegrityCheck(false)
                ->from($metricsTableName)
                ->where('page_id = ?', $page_id)
                ->where("metric_name LIKE ? OR metric_description LIKE ? OR metric_unit LIKE ?", '%' . $text . '%');
        }else{
            $metricsSelect = $metricsTable->select()
                ->setIntegrityCheck(false)
                ->from($metricsTableName)
                ->where('page_id = ?', $page_id);
        }

        return  $metricsTable->fetchAll($metricsSelect);

    }

    public function getMetricById($metric_id)
    {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where('metric_id = ?', $metric_id);


        return  $metricsTable->fetchRow($metricsSelect);

    }

    public function getMetricByIds($metric_id)
    {

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where('metric_id = ?', $metric_id);

        return  $metricsTable->fetchAll($metricsSelect);

    }


    public function getMetricSearcgByName($keyword)
    {

        /*
        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where("(`metric_name` LIKE ? )", "%$keyword%");

        return $metricsTable->fetchAll($metricsSelect);
        */

        $metricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricsTableName = $metricsTable->info('name');

        // metric select
        $metricsSelect = $metricsTable->select()
            ->setIntegrityCheck(false)
            ->from($metricsTableName)
            ->where("(`metric_name` LIKE ? )", "%$keyword%");

        return   $metricsTable->fetchAll($metricsSelect);

    }
}
?>
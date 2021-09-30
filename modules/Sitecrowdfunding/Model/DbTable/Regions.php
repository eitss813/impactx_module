<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Regions.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Regions extends Engine_Db_Table {

    protected $_name = 'sitecrowdfunding_regions';
    protected $_rowClass = 'Sitecrowdfunding_Model_Region';

    /**
     * Return regions for a country
     *
     * @param array $params
     * @return object
     */
    public function getRegionsPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getRegionsSelect($params));
        if (!empty($params['page']))
            $paginator->setCurrentPageNumber($params['page']);

        if (!empty($params['limit']))
            $paginator->setItemCountPerPage($params['limit']);

        return $paginator;
    }

    public function getRegionsSelect($params) {

        $table = Engine_Api::_()->getDbtable('regions', 'Sitecrowdfunding');
        $tableName = $table->info('name');

        $select = $table->select();

        if (!empty($params['country'])) {
            $select->where('country LIKE ?', '%' . $params['country'] . '%');
        } else {
            $select->from($tableName, array('country', 'country_status', 'COUNT(region) as regions', 'region_id'))
                    ->group('country');
        }

        if (empty($params['orderby']) || $params['orderby'] == 'country') {
            $select->order('country ASC');
        } else if ($params['orderby'] == 'region') {
            $select->order('region ASC');
        } else {
            $select->order('creation_date DESC');
        }

        return $select;
    }

    /**
     * Return regions by name
     *
     * @param array $params
     * @return object
     */
    public function getRegionsByName($params) {

        $tableName = $this->info('name');
        $select = $this->select();

        if (!empty($params['country'])) {
            $select->from($tableName, array('region_id', 'region'))
                    ->where('country LIKE ?', '%' . $params['country'] . '%')
                    ->where('country_status = ?', 1)
                    ->where('status = ?', 1)
                    ->order('region ASC');
        } else if (!isset($params['region'])) {
            $select->from($tableName, 'country')
                    ->distinct(true)
                    ->where('country_status = ?', 1)
                    ->where('status = ?', 1)
                    ->order('country ASC');
        }

        if (!empty($params['region'])) {
            $select->from($tableName, 'region_id')
                    ->where('country LIKE ?', $params['country_name'])
                    ->where('region LIKE ?', $params['region']);
        }

        return $select->query()->fetchAll();
    }

    /**
     * Return is any country enable
     *
     * @return object
     */
    function isAnyCountryEnable() {
        return $this->select()
                        ->where('country_status = ?', 1)
                        ->where('status = ?', 1)
                        ->limit(1)
                        ->query()
                        ->fetchAll();
    }

    /**
     * Return disabled countries
     *
     * @return array
     */
    function getDisabledCountries() {
        $tableName = $this->info('name');

        $select = $this->select()
                ->from($tableName, 'country')
                ->distinct(true)
                ->where('country_status = ?', 0)
                ->query()
                ->fetchAll();

        $disabledCountriesArray = array();
        foreach ($select as $key => $value) {
            $disabledCountriesArray[] = $value['country'];
        }

        return $disabledCountriesArray;
    }

    /**
     * Return is region already exist
     *
     * @param array $params
     * @param $flag
     * @return object
     */
    public function isRegionAlreadyExist($params, $flag = false) {

        if (!empty($flag)) {
            $params['region'] = '\'' . $params['region'] . '\'';
        }
        $regionStr = '';
        $regionStr = $params['region'];
        $select = $this->select()
                        ->from($this->info('name'), 'region')
                        ->where('country LIKE ?', $params['country'])
                        ->where("region IN ($regionStr)")
                        ->query()->fetchAll();

        $existedRegionsArray = array();
        if (!empty($select)) {
            foreach ($select as $key => $regioon) {
                if (empty($regioon['region']))
                    return 1;

                $existedRegionsArray[] = $regioon['region'];
            }
            return @implode(', ', $existedRegionsArray);
        } else
            return 0;
    }

    /**
     * 
     */
    public function isCountryExist($countryCode, $returnId = false) {
        $isCountryExist = $this->select()
                        ->from($this->info('name'), 'region_id')
                        ->where('country LIKE ?', $countryCode)
                        ->limit(1)
                        ->query()->fetchColumn();

        if (empty($isCountryExist))
            return false;
        if ($returnId)
            return $isCountryExist;
        return true;
    }

    /**
     * Return country status
     *
     * @param $countryCode
     * @return country status
     */
    public function getCountryStatus($countryCode) {
        $isCountryExist = $this->select()
                        ->from($this->info('name'), 'region_id')
                        ->where('country LIKE ?', $countryCode)
                        ->limit(1)
                        ->query()->fetchColumn();

        if (empty($isCountryExist))
            return 1;
        else {
            return $this->select()
                            ->from($this->info('name'), 'country_status')
                            ->where('country LIKE ?', $countryCode)
                            ->limit(1)
                            ->query()->fetchColumn();
        }
    }

    public function getEmptyRegionCount($country) {
        $fetchColumn = $this->select()
                        ->from($this->info('name'), 'COUNT(region_id) as numberOfRegion')
                        ->where('country LIKE ?', '%' . $country . '%')
                        ->where('region = ""')
                        ->query()->fetchColumn();

        return $fetchColumn;
    }

    public function getAllRegionsCountryArray() {
        $fetchRow = $this->select()
                        ->from($this->info('name'))
                        ->query()->fetchAll();

        return $fetchRow;
    }

}

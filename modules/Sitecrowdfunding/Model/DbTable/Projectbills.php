<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Projectbills.php 2017-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Projectbills extends Engine_Db_Table {

    protected $_name = 'sitecrowdfunding_projectbills';
    protected $_rowClass = 'Sitecrowdfunding_Model_Projectbill';
    protected $_serializedColumns = array('config');
    protected $_cryptedColumns = array('config');
    static private $_cryptKey;

    /**
     * Return project bill object
     *
     * @param array $params
     * @return object
     */
    public function getProjectBillPaginator($params = array()) {
 
        $paginator = Zend_Paginator::factory($this->getProjectBillSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getProjectBillSelect($params) {
        $select = $this->select()->where('project_id =?', $params['project_id']);
        $from_date = date('Y-m-d',strtotime(trim($params['from'])));
        $to_date = date('Y-m-d',strtotime(trim($params['to'])));
        if (isset($params['search'])) {
            if (!empty($params['from']))
                $select->where("creation_date >=?", $from_date);

            if (!empty($params['to']))
                $select->where("creation_date <=?", $to_date);

            $api = Engine_Api::_()->sitecrowdfunding();

            if (!empty($params['bill_min_amount']) && is_numeric($params['bill_min_amount']))
                $select->where("amount >=?", $api->getPriceWithCurrency(trim($params['bill_min_amount']), 1, 1));

            if (!empty($params['bill_max_amount']) && is_numeric($params['bill_max_amount']))
                $select->where("amount <=?", $api->getPriceWithCurrency(trim($params['bill_max_amount']), 1, 1));

            if (!empty($params['payment']) && $params['payment'] == 1) {
                $select->where("status = 'active'");
            }

            if (!empty($params['payment']) && $params['payment'] == 2) {
                $select->where("status != 'active'");
            }
        }
        $select->order('projectbill_id DESC');
        return $select;
    }

    /**
     * Return total paid bill amount
     *
     * @param int $project_id
     * @return float
     */
    public function totalPaidBillAmount($project_id) {
        $projectBillTableName = $this->info('name');

        $select = $this->select()
                        ->from($projectBillTableName, array("SUM(amount)"))
                        ->where("project_id =?", $project_id)
                        ->where("status = 'active'")
                        ->query()->fetchColumn();
        return empty($select) ? 0 : $select;
    }

    /**
     * Return total failed bill payment amount
     *
     * @param int $project_id
     * @return float
     */
    public function paymentFailedBillAmount($project_id) {
        $select = $this->select()
                        ->from($this->info('name'), array("SUM(amount)"))
                        ->where("project_id =?", $project_id)
                        ->where("status != 'active'")
                        ->where("status != 'not_paid'")
                        ->query()->fetchColumn();
        return $select;
    }

    public function getPaidCommissionDetail() {
        $select = $this->select()
                ->from($this->info('name'), array("SUM(amount) as paid_commission", "project_id"))
                ->where("status = 'active'")
                ->group("project_id");

        return $select->query()->fetchAll();
    }

    public function getEnabledGatewayCount() {
        return $this->select()
                        ->from($this, new Zend_Db_Expr('COUNT(*)'))
                        ->where('enabled = ?', 1)
                        ->query()
                        ->fetchColumn()
        ;
    }

    public function getEnabledGateways() {
        return $this->fetchAll($this->select()->where('enabled = ?', true));
    }

    // Inline encryption/decryption
    public function insert(array $data) {
        // Serialize
        $data = $this->_serializeColumns($data);

        // Encrypt each column
        foreach ($this->_cryptedColumns as $col) {
            if (!empty($data[$col])) {
                $data[$col] = self::_encrypt($data[$col]);
            }
        }

        return parent::insert($data);
    }

    public function update(array $data, $where) {
        // Serialize
        $data = $this->_serializeColumns($data);

        // Encrypt each column
        foreach ($this->_cryptedColumns as $col) {
            if (!empty($data[$col])) {
                $data[$col] = self::_encrypt($data[$col]);
            }
        }

        return parent::update($data, $where);
    }

    protected function _fetch(Zend_Db_Table_Select $select) {
        $rows = parent::_fetch($select);

        foreach ($rows as $index => $data) {
            // Decrypt each column
            foreach ($this->_cryptedColumns as $col) {
                if (!empty($rows[$index][$col])) {
                    $rows[$index][$col] = self::_decrypt($rows[$index][$col]);
                }
            }
            // Unserialize
            $rows[$index] = $this->_unserializeColumns($rows[$index]);
        }

        return $rows;
    }

    // Crypt Utility

    static private function _encrypt($data) {
        if (!extension_loaded('mcrypt')) {
            return $data;
        }

        $key = self::_getCryptKey();

        $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
        $checkVersion = Engine_Api::_()->seaocore()->checkVersion($coreVersion, '4.9.4');
        if (!empty($checkVersion) && version_compare(phpversion(), '7.1', '>=')) {
            return $data;
        }

        $cryptData = mcrypt_encrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);

        return $cryptData;
    }

    static private function _decrypt($data) {
        if (!extension_loaded('mcrypt')) {
            return $data;
        }

        $key = self::_getCryptKey();

        $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
        $checkVersion = Engine_Api::_()->seaocore()->checkVersion($coreVersion, '4.9.4');
        if (!empty($checkVersion) && version_compare(phpversion(), '7.1', '>=') && is_string($data) && substr($data, -1) != '=') {
            return $data;
        }
        
        $cryptData = mcrypt_decrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);
        $cryptData = rtrim($cryptData, "\0");

        return $cryptData;
    }

    static private function _getCryptKey() {
        if (null === self::$_cryptKey) {
            $key = Engine_Api::_()->getApi('settings', 'core')->core_secret
                    . '^'
                    . Engine_Api::_()->getApi('settings', 'core')->payment_secret;
            self::$_cryptKey = substr(md5($key, true), 0, 8);
        }

        return self::$_cryptKey;
    }

}

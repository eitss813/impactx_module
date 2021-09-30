<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Backers.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Backers extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Backer';

    public function getInternalBackers($params) {
        $backerTableName = $this->info('name');
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
        $select = $this->select()->from($backerTableName, array('sum(amount) as funding_amount','user_id as resource_id'))
            ->setIntegrityCheck(false)
            ->joinLeft($tableUserName, "$tableUserName.user_id = $backerTableName.user_id", 'username as title')
            ->group("$backerTableName.backer_id")
            ->order("$backerTableName.backer_id DESC");
        if (!empty($params['project_id'])) {
            $select->where('project_id = ?', $params['project_id']);
        }

        $select->where('payment_status = "active" OR payment_status = "authorised"
                                OR payment_status = "failed" OR payment_status = "refunded"
                                OR payment_status = "pending"');

        $select->group("$backerTableName.user_id");

        return $this->fetchAll($select);
    }

    public function getAllBackers($params) {
        $backerTableName = $this->info('name');
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
        $select = $this->select()->from($backerTableName, '*')
                ->setIntegrityCheck(false)
                ->joinLeft($tableUserName, "$tableUserName.user_id = $backerTableName.user_id", 'username')
                ->group("$backerTableName.backer_id")
                ->order("$backerTableName.backer_id DESC");
        if (!empty($params['project_id'])) {
            $select->where('project_id = ?', $params['project_id']);
        }
        if (!empty($params['gateway_id'])) {
            $select->where('gateway_id = ?', $params['gateway_id']);
        }
        //projects Backed by a user 
        if (!empty($params['user_id'])) {
            $select->where($backerTableName . '.user_id = ?', $params['user_id']);
        }
        if (!empty($params['username']) && empty($user_id))
            $select->where('(' . $tableUserName . '.username  LIKE ? ||' . $tableUserName . '.displayname LIKE ?)', '%' . $params['username'] . '%');

        if (!empty($params['searchByRewards'])) {
            switch ($params['searchByRewards']) {
                case 1:
                    $select->where('reward_id IS NOT NULL');
                    break;
                case 2:
                    $select->where('reward_id IS NOT NULL');
                    $select->where('reward_status = ?', 0);
                    break;
                case 3:
                    $select->where('reward_id IS NULL');
                    break;
                default:
                    break;
            }
        }
        if (!empty($params['payment_status'])) {
            $select->where('payment_status = ?', $params["payment_status"]);
        } else {
            //EMAIL WILL BE SENT ONLY TO ACTIVE AND AUTHORISED PAYMENT STATUS BACKERS
            if (!empty($params['email_backers'])) {
                $select->where('payment_status = "active" OR payment_status = "authorised"');
            } else if (!empty($params['export_backers'])) {
                $select->where('payment_status = "active"');
            } else {
                //SHOW BACKERS WITH BELOW PAYMENT STATUS (AUTHORISED IN CASE OF PREAPPROVAL) 
                $select->where('payment_status = "active" OR payment_status = "authorised"
                                OR payment_status = "failed" OR payment_status = "refunded"
                                OR payment_status = "pending"');
            }
        }



        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }
        if (!empty($params['paginator'])) {
            return $select;
        }
        return $this->fetchAll($select);
    }

    /**
     * Return list of backers
     *
     * @param $param = page id/ buyer id of the backer
     * @param $flag
     * @return object
     */
    public function getBackersPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getAllBackers($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    //All projects Backed by a user
    public function getBackedProjects($params = array()) {
        $backerTableName = $this->info('name');
        $select = $this->select()->from($backerTableName, 'project_id');

        if (isset($params['user_id'])) {
            $select = $select->where('user_id = ?', $params['user_id']);
        }
        $select = $select->where('payment_status = "active" OR payment_status = "authorised"')
                ->group('project_id');
        return $this->fetchAll($select);
    }

    //RETURN BACKED PROJECT COUNT
    public function getBackedProjectsCount($params) {
        $backerTableName = $this->info('name');
        $tableProjecName = Engine_Api::_()->getItemTable('sitecrowdfunding_project')->info('name');
        $select = $this->select()->from($backerTableName)
                ->setIntegrityCheck(false)
                ->joinLeft($tableProjecName, "$tableProjecName.project_id = $backerTableName.project_id");
        if (isset($params['user_id'])) {
            $select = $select->where('user_id = ?', $params['user_id']);
        }
        $select = $select->where('payment_status = "active" OR payment_status = "authorised"')
                ->where("$tableProjecName.state <> ?", 'draft')
                ->where("$tableProjecName.approved = ?", 1)
                ->group($backerTableName . '.project_id');
        return count($this->fetchAll($select));
    }

    public function getPaymentStates($forTransactions = false) {
        if ($forTransactions) {
            $multiOptions = array('active' => 'Okay', 'pending' => 'Pending', 'failed' => 'Failed');
        } else {
            //STATES TO BE SHOWN IN BACKERS REPORT TAB AND ADMIN TAB MANAGE BACKERS
            $multiOptions = array('active' => 'Okay', 'authorised' => 'Preapproved', 'pending' => 'Pending', 'failed' => 'Failed');
        }
        return $multiOptions;
    }

    public function getTotalAmount($project_id) {
        $select = $this->select()
                ->from($this->info('name'), array(new Zend_Db_Expr("SUM(amount) as backed_amount "), new Zend_Db_Expr("SUM(commission_value) as commission_value "), new Zend_Db_Expr("COUNT(backer_id) as backer_count ")))
                ->where('project_id =? AND payment_request_id = 0 AND direct_payment = 0 AND payment_status LIKE \'active\' ', $project_id)
                ->where('gateway_type=\'normal\'');
        return $this->fetchRow($select);
    }

    public function notPaidBillAmount($project_id) {
        $select = $this->select()
                ->from($this->info('name'), array("sum(commission_value) as commission"))
                ->where('project_id =?', $project_id)
                ->where('direct_payment = 1')
                ->where('non_payment_admin_reason = 1')
                ->where("payment_status != 'not_paid'")
                ->where('order_status = 3');

        return $select->query()->fetchColumn();
    }

    /**
     * Return bill amount for a project
     *
     * @param $project_id
     * @return object
     */
    public function getProjectBillAmount($project_id) {
        $select = $this->select()
                ->from($this->info('name'), array('SUM(commission_value) as commission'))
                ->where("project_id =? AND projectbill_id = 0 AND direct_payment = 1 AND non_payment_admin_reason != 1 AND order_status != 3 AND payment_status != 'not_paid'", $project_id)
                ->where('gateway_type=\'normal\'');

        return $select->query()->fetchColumn();
    }

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
        $backerTableName = $this->info('name');

        $select = $this->select()
                ->from($backerTableName, array("sum(amount) as grand_total", "sum(commission_value) as commission", "count(backer_id) as backer_count", "MONTHNAME(creation_date) as month", "MONTH(creation_date) as month_no", "YEAR(creation_date) as year"))
                ->where('project_id =?', $params['project_id'])
                ->where('direct_payment = 1')
                ->where('non_payment_admin_reason != 1')
                ->where('order_status != 3');

        $select->group("YEAR($backerTableName.creation_date)", "MONTH($backerTableName.creation_date)");
        return $select;
    }

    public function getProjectMonthlyBillPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getProjectMonthlyBillSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getProjectMonthlyBillSelect($params) {
        $backerTableName = $this->info('name');

        $select = $this->select()
                ->from($backerTableName, array("backer_id", "commission_value", "amount as grand_total", "creation_date", "payment_status"))
                ->where('project_id =?', $params['project_id'])
                ->where('direct_payment = 1');

        if (isset($params['month']) && !empty($params['month'])) {
            $select->where('MONTH(creation_date) = ?', $params['month']);
        }
        if (isset($params['year']) && !empty($params['year'])) {
            $select->where('YEAR(creation_date) = ?', $params['year']);
        }

        $select->order('backer_id DESC');
        return $select;
    }

    public function getCommissionValue($projectId) {

        $backerTableName = $this->info('name');
        $select = $this->select()
                ->from($backerTableName, array(new Zend_Db_Expr("SUM(commission_value) AS total_commission"),new Zend_Db_Expr("SUM(amount) AS total_amount")))
                ->where('project_id =?', $projectId)
                ->where('payment_status = ?', 'active')
                ->where('payout_status <> ? or payout_status is null', 'success')
                ->where('refund_status <> ? or refund_status is null', 'success')
                ->where('gateway_type = ?', 'escrow');
        return $this->fetchRow($select);
    }

}

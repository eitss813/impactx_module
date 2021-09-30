<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Transactions.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Transactions extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Transaction';

    public function getBenefitStatus(User_Model_User $user = null) {
        return true;
    }

    public function getBackerTransactionsPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getBackerTransactionsSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        $paginator->setItemCountPerPage(10);
        return $paginator;
    }

    public function getBackerTransactionsSelect($params = array()) {
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
        $transactionsName = $this->info('name');

        $backerTableName = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->info('name');
        $gatewayTableName = Engine_Api::_()->getDbtable('gateways', 'payment')->info('name');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $projectTable->info('name');

        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
        $locationTableName = $locationTable->info('name');

        $transactionSelect = $this->select()
                ->from($transactionsName, array('transaction_id', 'timestamp', 'state', 'amount', 'source_id'))
                ->setIntegrityCheck(false)
                ->join($backerTableName, "$backerTableName.backer_id = $transactionsName.source_id", array('backer_id', 'user_id', 'commission_value', 'payout_status', 'refund_status', 'gateway_type','project_id'))
                ->joinLeft($tableUserName, "$tableUserName.user_id = $backerTableName.user_id", array('username','displayname'))
                ->joinLeft($projectTableName, "$projectTableName.project_id = $backerTableName.project_id", 'title')
                ->joinLeft($gatewayTableName, "$transactionsName.gateway_id = $gatewayTableName.gateway_id", array('title','title as gateway_title'));

        //FETCH ONLY SITECROWDFUNDING_PROJECT TYPE ROWS
        $transactionSelect->where($transactionsName . '.source_type = ?', 'sitecrowdfunding_backer');

        // filter by single project_id
        if (isset($params['project_id']) && !empty($params['project_id'])) {
            $transactionSelect->where($backerTableName . '.project_id = ?', $params['project_id']);
        }

        // filter by single project_ids
        if (isset($params['project_ids']) && !empty($params['project_ids'])) {
            $transactionSelect->where($backerTableName . '.project_id IN (?)', $params['project_ids']);
        }

        // filter by project name , if project_id is not passed
        if (!isset($params['project_id']) && empty($params['project_id']) && !empty($params['project_name']) ) {
            $transactionSelect->where($projectTableName . '.title LIKE ?', '%' .trim($params['project_name']) . '%');
        }

        // filter by backer name
        if (!empty($params['backer_name'])) {
            $transactionSelect->where($tableUserName . '.username  LIKE ?', '%' . trim($params['backer_name']) . '%');
        }

        // filter by user_id
        if (!empty($params['user_id'])) {
            $transactionSelect->where($tableUserName . '.user_id  = ?', (Int)$params['user_id']);
        }

        // filter by user_name if user_id is not passed
        if (!empty($params['user_name']) && empty($params['user_id'])) {
            $transactionSelect->where($tableUserName . '.displayname  LIKE ?', '%' . trim($params['user_name']) . '%');
        }

        // filter by date from and to to time
        if (isset($params['from']) && !empty($params['from'])) {
            $transactionSelect->where("CAST($transactionsName.timestamp AS DATE) >=?", trim($params['from']));
        }
        if (isset($params['to']) && !empty($params['to'])) {
            $transactionSelect->where("CAST($transactionsName.timestamp AS DATE) <=?", trim($params['to']));
        }

        // filter by transaction min/max amt
        if (!empty($params['transaction_min_amount'])) {
            $transaction_min_amount_value = (float) str_replace( ',', '', $params['transaction_min_amount']);
            $transactionSelect->where("$transactionsName.amount >= " . $transaction_min_amount_value);
        }
        if (!empty($params['transaction_max_amount'])) {
            $transaction_max_amount_value = (float) str_replace( ',', '', $params['transaction_max_amount']);
            $transactionSelect->where("$transactionsName.amount <= " . $transaction_max_amount_value);
        }

        // filter by commission min/max amt
        if (!empty($params['commission_min_amount'])) {
            $commission_min_amount_value = (float) str_replace( ',', '', $params['commission_min_amount']);
            $transactionSelect->where("$backerTableName.commission_value >= " . $commission_min_amount_value);
        }
        if (!empty($params['commission_max_amount'])) {
            $commission_max_amount_value = (float) str_replace( ',', '', $params['commission_max_amount']);
            $transactionSelect->where("$backerTableName.commission_value <= " . $commission_max_amount_value);
        }

        // filter by payment status
        if (!empty($params['payment_status'])){
            $transactionSelect->where($backerTableName . '.payment_status = ?', $params['payment_status']);
        }

        // Filter based on location or Latitude/Longitude present
        if (
            (isset($params['location']) && !empty($params['location']))
            ||
            (!empty($params['Latitude']) && !empty($params['Longitude']))
        )
        {
            $location = $params['location'];
            $latitude = (float) $params['Latitude'];
            $longitude = (float) $params['Longitude'];
            $locationmiles = $params['locationmiles'];

            // if both locationMiles and lat/long given during filter
            if (!empty($locationmiles) && !empty($latitude) && !empty($longitude)) {
                $radius = $locationmiles;

                $flag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
                if (!empty($flag)) {
                    $radius = $radius * (0.621371192);
                }
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $transactionSelect->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $transactionSelect->where($sqlstring);
            }else {
                $transactionSelect->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", null);
                $transactionSelect->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $location . "%");
            }

        }

        // get only location based projects
        if(isset($params['location_only_projects'])){
            if($params['location_only_projects'] === true ){
                $transactionSelect->where($locationTableName . '.project_id IN (?)', $params['project_ids']);
            }
        }

        $transactionSelect->group("$backerTableName.backer_id");

        // sorting
        if (!empty($params['sort_field'])) {

            $sort_direction = $params['sort_direction'];

            switch ($params['sort_field']) {
                case "transaction_id" :
                    $transactionSelect->order("$transactionsName.transaction_id $sort_direction");
                    break;
                case "project_name" :
                    $transactionSelect->order("$projectTableName.title $sort_direction");
                    break;
                case "user_name" :
                    $transactionSelect->order("$tableUserName.displayname $sort_direction");
                    break;
                case "transaction_amount" :
                    $transactionSelect->order("$transactionsName.amount $sort_direction");
                    break;
                case "commission_amount" :
                    $transactionSelect->order("$backerTableName.commission_value $sort_direction");
                    break;
                case "gateway" :
                    $transactionSelect->order("$gatewayTableName.title $sort_direction");
                    break;
                case "payment_status" :
                    $transactionSelect->order("$backerTableName.payout_status $sort_direction");
                    break;
                case "date" :
                    $transactionSelect->order("$transactionsName.timestamp $sort_direction");
                    break;
                default:
                    $transactionSelect->order("$transactionsName.timestamp $sort_direction");
            }

        } else {
            $transactionSelect->order("$transactionsName.timestamp DESC");
        }

        return $transactionSelect;
    }

    public function getSumOfAmountFromBackerTransactions($params = array()) {
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
        $transactionsName = $this->info('name');

        $backerTableName = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->info('name');
        $gatewayTableName = Engine_Api::_()->getDbtable('gateways', 'payment')->info('name');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $projectTable->info('name');

        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
        $locationTableName = $locationTable->info('name');

        $transactionSelect = $this->select()
            ->from($transactionsName, array("SUM($transactionsName.amount) as total_amount"))
            ->setIntegrityCheck(false)
            ->join($backerTableName, "$backerTableName.backer_id = $transactionsName.source_id", array('project_id'))
            ->joinLeft($tableUserName, "$tableUserName.user_id = $backerTableName.user_id", array())
            ->joinLeft($projectTableName, "$projectTableName.project_id = $backerTableName.project_id", array('project_id'))
            ->joinLeft($gatewayTableName, "$transactionsName.gateway_id = $gatewayTableName.gateway_id", array());

        //FETCH ONLY SITECROWDFUNDING_PROJECT TYPE ROWS
        $transactionSelect->where($transactionsName . '.source_type = ?', 'sitecrowdfunding_backer');

        // filter by single project_id
        if (isset($params['project_id']) && !empty($params['project_id'])) {
            $transactionSelect->where($backerTableName . '.project_id = ?', $params['project_id']);
        }

        // filter by single project_ids
        if (isset($params['project_ids']) && !empty($params['project_ids'])) {
            $transactionSelect->where($backerTableName . '.project_id IN (?)', $params['project_ids']);
        }

        // filter by project name , if project_id is not passed
        if (!isset($params['project_id']) && empty($params['project_id']) && !empty($params['project_name']) ) {
            $transactionSelect->where($projectTableName . '.title LIKE ?', '%' .trim($params['project_name']) . '%');
        }

        // filter by backer name
        if (!empty($params['backer_name'])) {
            $transactionSelect->where($tableUserName . '.username  LIKE ?', '%' . trim($params['backer_name']) . '%');
        }

        // filter by user_id
        if (!empty($params['user_id'])) {
            $transactionSelect->where($tableUserName . '.user_id  = ?', (Int)$params['user_id']);
        }

        // filter by user_name if user_id is not passed
        if (!empty($params['user_name']) && empty($params['user_id'])) {
            $transactionSelect->where($tableUserName . '.displayname  LIKE ?', '%' . trim($params['user_name']) . '%');
        }

        // filter by date from and to to time
        if (isset($params['from']) && !empty($params['from'])) {
            $transactionSelect->where("CAST($transactionsName.timestamp AS DATE) >=?", trim($params['from']));
        }
        if (isset($params['to']) && !empty($params['to'])) {
            $transactionSelect->where("CAST($transactionsName.timestamp AS DATE) <=?", trim($params['to']));
        }

        // filter by transaction min/max amt
        if (!empty($params['transaction_min_amount'])) {
            $transaction_min_amount_value = (float) str_replace( ',', '', $params['transaction_min_amount']);
            $transactionSelect->where("$transactionsName.amount >= " . $transaction_min_amount_value);
        }
        if (!empty($params['transaction_max_amount'])) {
            $transaction_max_amount_value = (float) str_replace( ',', '', $params['transaction_max_amount']);
            $transactionSelect->where("$transactionsName.amount <= " . $transaction_max_amount_value);
        }

        // filter by commission min/max amt
        if (!empty($params['commission_min_amount'])) {
            $commission_min_amount_value = (float) str_replace( ',', '', $params['commission_min_amount']);
            $transactionSelect->where("$backerTableName.commission_value >= " . $commission_min_amount_value);
        }
        if (!empty($params['commission_max_amount'])) {
            $commission_max_amount_value = (float) str_replace( ',', '', $params['commission_max_amount']);
            $transactionSelect->where("$backerTableName.commission_value <= " . $commission_max_amount_value);
        }

        // filter by payment status
        if (!empty($params['payment_status'])){
            $transactionSelect->where($backerTableName . '.payment_status = ?', $params['payment_status']);
        }

        // Filter based on location or Latitude/Longitude present
        if (
            (isset($params['location']) && !empty($params['location']))
            ||
            (!empty($params['Latitude']) && !empty($params['Longitude']))
        )
        {
            $location = $params['location'];
            $latitude = (float) $params['Latitude'];
            $longitude = (float) $params['Longitude'];
            $locationmiles = $params['locationmiles'];

            // if both locationMiles and lat/long given during filter
            if (!empty($locationmiles) && !empty($latitude) && !empty($longitude)) {
                $radius = $locationmiles;

                $flag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
                if (!empty($flag)) {
                    $radius = $radius * (0.621371192);
                }
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $transactionSelect->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $transactionSelect->where($sqlstring);
            }else {
                $transactionSelect->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", null);
                $transactionSelect->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $location . "%");
            }

        }

        // get only location based projects
        if(isset($params['location_only_projects'])){
            if($params['location_only_projects'] === true ){
                $transactionSelect->where($locationTableName . '.project_id IN (?)', $params['project_ids']);
            }
        }

        return $transactionSelect->query()->fetchColumn();
    }


    // get projects with transactions
    public function getProjectTransactionsPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getProjectBackerTransactionsSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        $paginator->setItemCountPerPage(10);
        return $paginator;
    }

    public function getProjectBackerTransactionsSelect($params = array()) {
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        //GET PROJECT TABLE
        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $projectTable->info('name');

        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backerTableName = $backerTable->info('name');

        $externalBackersTable = Engine_Api::_()->getDbTable('externalfundings', 'sitecrowdfunding');
        $externalBackersTableName = $externalBackersTable->info('name');

        // amount
        $backerSelect = $backerTable->select()
            ->from($backerTableName,array('SUM(amount)'))
            ->where("$backerTableName.project_id = $projectTableName.project_id")
            ->where("$backerTableName.payment_status = 'active' OR $backerTableName.payment_status = 'authorised'");

        $externalBackersSelect = $externalBackersTable->select()
            ->from($externalBackersTable,array('SUM(funding_amount)'))
            ->where("$externalBackersTableName.project_id = $projectTableName.project_id")
            ->where("$externalBackersTableName.resource_type IN (?) ", array("member", "organization"));

        // backers members count
        $backerMembersSelect = $backerTable->select()
            ->from($backerTableName,array('Count(distinct user_id)'))
            ->where("$backerTableName.project_id = $projectTableName.project_id")
            ->where("$backerTableName.payment_status = 'active' OR $backerTableName.payment_status = 'authorised'");

        // external members count
        $externalMembersBackersSelect = $externalBackersTable->select()
            ->from($externalBackersTable,array('Count(distinct resource_id,resource_type)'))
            ->where("$externalBackersTableName.project_id = $projectTableName.project_id")
            ->where("$externalBackersTableName.resource_id IS NOT NULL")
            ->where("$externalBackersTableName.resource_type IN (?) ", array("member"));

        // external org count
        $externalOrgMembersBackersSelect = $externalBackersTable->select()
            ->from($externalBackersTable,array('Count(distinct resource_id,resource_type)'))
            ->where("$externalBackersTableName.project_id = $projectTableName.project_id")
            ->where("$externalBackersTableName.resource_id IS NOT NULL")
            ->where("$externalBackersTableName.resource_type IN (?) ", array("organization"));

        // external org count with no org id
        $externalOrgNameMembersBackersSelect = $externalBackersTable->select()
            ->from($externalBackersTable,array('Count(distinct resource_name,resource_type)'))
            ->where("$externalBackersTableName.project_id = $projectTableName.project_id")
            ->where("$externalBackersTableName.resource_id IS NULL")
            ->where("$externalBackersTableName.resource_type IN (?) ", array("organization"));

        $projectSelect = $projectTable->select()
            ->setIntegrityCheck(false)
            ->from($projectTableName)
            ->joinLeft($tableUserName, "$projectTableName.owner_id = $tableUserName.user_id", array('user_id','username','displayname'))
            ->group("$projectTableName.project_id");

        $projectSelect->columns(array(
            "total_funding_amount" => new Zend_Db_Expr(
                '('
                .new Zend_Db_Expr( 'COALESCE('.new Zend_Db_Expr('('.$backerSelect.')').',0)') .
                '+'
                .new Zend_Db_Expr( 'COALESCE('.new Zend_Db_Expr('('.$externalBackersSelect.')').',0)') .
                '+'
                .new Zend_Db_Expr( 'COALESCE('.new Zend_Db_Expr('('.$projectTableName.'.invested_amount)').',0)') .
                ')'
            ),
            "total_funders_count" => new Zend_Db_Expr(
                '('
                .new Zend_Db_Expr( 'COALESCE('.new Zend_Db_Expr('('.$backerMembersSelect.')').',0)') .
                '+'
                .new Zend_Db_Expr( 'COALESCE('.new Zend_Db_Expr('('.$externalMembersBackersSelect.')').',0)') .
                '+'
                .new Zend_Db_Expr( 'COALESCE('.new Zend_Db_Expr('('.$externalOrgMembersBackersSelect.')').',0)') .
                '+'
                .new Zend_Db_Expr( 'COALESCE('.new Zend_Db_Expr('('.$externalOrgNameMembersBackersSelect.')').',0)') .
                ')'
            )
        ));

        // filter by multiple project ids
        if (isset($params['project_ids']) && !empty($params['project_ids'])) {
            $projectSelect->where($projectTableName . '.project_id IN (?)', $params['project_ids']);
        }

        // filter by single project id
        if (!empty($params['project_id'])) {
            $project_id = $params['project_id'];
            if (!empty($project_id)) {
                $projectSelect->where($projectTableName . '.project_id = ? ', $project_id);
            }
        }

        // filter by single user_id
        if (!empty($params['user_id'])) {
            $projectSelect->where($tableUserName . '.user_id  = ?', (Int)$params['user_id']);
        }

        // filter by user_name if user_id is not passed
        if (!empty($params['user_name']) && empty($params['user_id'])) {
            $projectSelect->where($tableUserName . '.displayname  LIKE ?', '%' . trim($params['user_name']) . '%');
        }

        // filter by project name , if project_id is not passed
        if ( empty($params['project_id']) && !empty($params['project_name']) ) {
            $projectSelect->where($projectTableName . '.title LIKE ?', '%' .trim($params['project_name']) . '%');
        }

        // project state
        if (!empty($params['state'])) {
            $state = "";
            switch ($params['state']) {
                case "1" :
                    $state = 'draft';
                    break;
                case "2" :
                    $state = "published";
                    break;
                case "3" :
                    $state = "successful";
                    break;
                case "4" :
                    $state = "failed";
                    break;
                case "5" :
                    $state = "submitted";
                    break;
                case "6" :
                    $state = "rejected";
                    break;
            }
            if (!empty($state)) {
                $projectSelect->where($projectTableName . '.state = ? ', $state);
            }
        }

        // funding state
        if (!empty($params['funding_state'])) {
            $funding_state = "";
            switch ($params['funding_state']) {
                case "1" :
                    $funding_state = 'draft';
                    break;
                case "2" :
                    $funding_state = "published";
                    break;
                case "3" :
                    $funding_state = "successful";
                    break;
                case "4" :
                    $funding_state = "failed";
                    break;
                case "5" :
                    $funding_state = "submitted";
                    break;
                case "6" :
                    $funding_state = "rejected";
                    break;
            }
            if (!empty($funding_state)) {
                $projectSelect
                    ->where($projectTableName. '.is_fund_raisable = ?',1)
                    ->where($projectTableName . '.funding_state = ? ', $funding_state);
            }
        }

        // goal min and max value
        if (!empty($params['goal_amount_min']) && empty($params['goal_amount_max'])) {
            $goal_amt_min_value = str_replace( ',', '', $params['goal_amount_min']);
            $projectSelect
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->where($projectTableName . '.goal_amount >= ? ', $goal_amt_min_value);
        }
        if (empty($params['goal_amount_min']) && !empty($params['goal_amount_max'])) {
            $goal_amt_max_value = str_replace( ',', '', $params['goal_amount_max']);
            $projectSelect
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->where($projectTableName . '.goal_amount <= ? ', $goal_amt_max_value);
        }
        if (!empty($params['goal_amount_min']) && !empty($params['goal_amount_max'])) {
            $goal_amt_min_value = str_replace( ',', '', $params['goal_amount_min']);
            $goal_amt_max_value = str_replace( ',', '', $params['goal_amount_max']);
            $projectSelect
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->where($projectTableName . '.goal_amount >= ? ', $goal_amt_min_value)
                ->where($projectTableName . '.goal_amount <= ? ', $goal_amt_max_value);

        }

        // funding min and max value
        if (!empty($params['funding_amount_min']) && empty($params['funding_amount_max'])) {
            $funding_amount_min_value = str_replace( ',', '', $params['funding_amount_min']);
            $projectSelect
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->having("( total_funding_amount ) >= ?",floatval($funding_amount_min_value));
        }
        if (empty($params['funding_amount_min']) && !empty($params['funding_amount_max'])) {
            $funding_amount_max_value = str_replace( ',', '', $params['funding_amount_max']);
            $projectSelect
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->having("( total_funding_amount ) <= ?",floatval($funding_amount_max_value));
        }
        if (!empty($params['funding_amount_min']) && !empty($params['funding_amount_max'])) {
            $funding_amount_min_value = str_replace( ',', '', $params['funding_amount_min']);
            $funding_amount_max_value = str_replace( ',', '', $params['funding_amount_max']);
            $projectSelect
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->having("( total_funding_amount ) >= ?", $funding_amount_min_value)
                ->having("( total_funding_amount ) <= ?", $funding_amount_max_value);
        }

        // total_funders
        if (!empty($params['total_funders_min']) && empty($params['total_funders_max'])) {
            $total_funders_min_value = str_replace( ',', '', $params['total_funders_min']);
            $projectSelect
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->having('total_funders_count >= ? ', $total_funders_min_value);
        }
        if (empty($params['total_funders_min']) && !empty($params['total_funders_max'])) {
            $total_funders_max_value = str_replace( ',', '', $params['total_funders_max']);
            $projectSelect
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->having('total_funders_count <= ? ', $total_funders_max_value);
        }
        if (!empty($params['total_funders_min']) && !empty($params['total_funders_max'])) {
            $total_funders_min_value = str_replace( ',', '', $params['total_funders_min']);
            $total_funders_max_value = str_replace( ',', '', $params['total_funders_max']);
            $projectSelect
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->having('total_funders_count >= ? ', $total_funders_min_value)
                ->having('total_funders_count <= ? ', $total_funders_max_value);
        }

        // sorting
        if (!empty($params['sort_field'])) {

            $sort_direction = $params['sort_direction'];

            switch ($params['sort_field']) {
                case "project_id" :
                    $projectSelect->order("$projectTableName.project_id $sort_direction");
                    break;
                case "project_name" :
                    $projectSelect->order("$projectTableName.title $sort_direction");
                    break;
                case "owner" :
                    $projectSelect->order("$tableUserName.displayname $sort_direction");
                    break;
                case "project_status" :
                    $projectSelect->order("$projectTableName.state $sort_direction");
                    break;
                case "funding_status" :
                    $projectSelect->order("$projectTableName.funding_state $sort_direction");
                    break;
                case "goal_amount" :
                    $projectSelect->order("$projectTableName.goal_amount $sort_direction");
                    break;
                case "total_funding_amount" :
                    $projectSelect->order("total_funding_amount $sort_direction");
                    break;
                case "total_funders" :
                    $projectSelect->order("total_funders_count $sort_direction");
                    break;
                default:
                    $projectSelect->order("$projectTableName.project_id DESC");
            }

        } else {
            $projectSelect->order("$projectTableName.project_id DESC");
        }

        return $projectSelect;
    }

    /**
     * Return transactions state
     *
     * @param $sender_type
     * @param #project_id
     * @return string
     */
    public function getTransactionState($sender_type = false, $project_id = null) {
        $paymentRequestTableName = Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding')->info('name');
        $transactionTableName = $this->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($transactionTableName, array('state'))
                ->distinct(true)
                ->join($paymentRequestTableName, "($paymentRequestTableName.request_id = $transactionTableName.source_id)", '')
                ->where("$transactionTableName.source_type = ?", 'sitecrowdfunding_paymentrequest');

        empty($sender_type) ? $select->where("sender_type = 0") : $select->where("sender_type = 1");

        if (!empty($project_id))
            $select->where("$paymentRequestTableName.project_id =?", $project_id);

        return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
    }
}

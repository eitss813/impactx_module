<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Projects.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Projects extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Project';

    public function getColumnValue($project_id, $column_name) {

        return $this->select()
            ->from($this->info('name'), array("$column_name"))
            ->where('project_id = ?', $project_id)
            ->limit(1)
            ->query()
            ->fetchColumn();
    }

    public function getPendingPayoutProjects() {
        $projectTableName = $this->info('name');
        $otherInfoTable = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding');
        $otherInfoTableName = $otherInfoTable->info('name');
        $select = $this->select()->from($projectTableName, '*')
                ->join($otherInfoTableName, "$projectTableName.project_id = $otherInfoTableName.project_id", array())
                ->where('state in (?)', array('successful', 'failed'))
                ->where('payout_status = ?', '')
                ->where('gateway_type = ?', 'escrow');
        return $this->fetchAll($select);
    }

    public function updateProjectState() {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        date_default_timezone_set($timezone);
        $currentDate = date('Y-m-d H:i:s');
        $projectTableName = $this->info('name');
        $select = $this->select()->from($projectTableName, '*')
                ->where('funding_end_date<?', $currentDate)
                ->where('funding_state=?', 'published');
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $projects = $this->fetchAll($select);
        foreach ($projects as $project) {
            $notifyUser = $project->getOwner();
            // Add parameters for email notification
            $params = array();
            $params['project_name'] = $project->title;
            $params['member_name'] = $notifyUser->getTitle();
            $host = $_SERVER['HTTP_HOST'];
            $params['project_link'] = $view->htmlLink($host . $project->getHref(), $project->title);
            $params['goal_amount'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($project->goal_amount);
            $params['collected_amount'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($project->getFundedAmount());
            $settings = Engine_Api::_()->getApi('settings', 'core');
            $paymentSetting = $settings->getSetting('sitecrowdfunding.payment.setting', 'automatic');
            $paymentType = $settings->getSetting('sitecrowdfunding.automatic.payment.method', 'payout');
            if ($project->isProjectSucceeded()) {
                $type = "sitecrowdfunding_project_completion";
                $project->funding_state = 'successful';
                $notifyApi->addNotification($notifyUser, $notifyUser, $project, $type, $params);
                //Email notification to backers
                Engine_Api::_()->sitecrowdfunding()->sendEmailToBackers($project, 'SITECROWDFUNDING_BACKER_PROJECT_SUCCESSFUL_EMAIL');
                //Generate Feed 
                $owner = $project->getOwner();
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $project, 'sitecrowdfunding_project_completion');
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                }

                //START JOB FOR PAYOUT IN CASE OF AUTOMATIC PAYMENT 
                if ($paymentSetting == 'automatic') {
                    Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitecrowdfunding_project_payments', array('project_id' => $project->project_id, 'payment_type' => 'payout'));
                }
            } else {
                $type = "sitecrowdfunding_project_failure";
                $project->funding_state = 'failed';
               // $notifyApi->addNotification($notifyUser, $notifyUser, $project, $type, $params);
               // Engine_Api::_()->sitecrowdfunding()->sendEmailToBackers($project, 'SITECROWDFUNDING_BACKER_PROJECT_FAILURE_EMAIL');
                //Generate Feed 
                $owner = $project->getOwner();
              //  $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $project, 'sitecrowdfunding_project_failure');
                if ($action != null) {
                  //  Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                }

                //START JOB FOR PAYOUT/REFUND IN CASE OF AUTOMATIC PAYMENT
                if ($paymentSetting == 'automatic') {
                    if ($paymentType == 'payout') {
                        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitecrowdfunding_project_payments', array('project_id' => $project->project_id, 'payment_type' => 'payout'));
                    } elseif ($paymentType == 'refund') {
                        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitecrowdfunding_project_payments', array('project_id' => $project->project_id, 'payment_type' => 'refund'));
                    }
                }
            }
            $project->save();
        }
    }

    public function getProjectSelect(array $params, $customParams = array()) {

        $projectTableName = $this->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');

        $locationTableName = $locationTable->info('name');
        $select = $this->select()->from($projectTableName, '*');
        if (isset($customParams)) {
            //GET SEARCH TABLE
            $searchTable = Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'search')->info('name');
            //PROCESS OPTIONS
            $tmp = array();
            foreach ($customParams as $k => $v) {
                if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                    continue;
                } else if (false !== strpos($k, '_field_')) {
                    list($null, $field) = explode('_field_', $k);
                    $tmp['field_' . $field] = $v;
                } else if (false !== strpos($k, '_alias_')) {
                    list($null, $alias) = explode('_alias_', $k);
                    $tmp[$alias] = $v;
                } else {
                    $tmp[$k] = $v;
                }
            }
            $customParams = $tmp;

            $select = $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($searchTable, "$searchTable.item_id = $projectTableName.project_id", null);

            $searchParts = Engine_Api::_()->fields()->getSearchQuery('sitecrowdfunding_project', $customParams);
            foreach ($searchParts as $k => $v) {
                $select->where("`{$searchTable}`.{$k}", $v);
            }
        }

        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $interval = '';
        if (isset($params['interval']) && !empty($params['interval'])) {
            $interval = $params['interval'];
            $current_time = date("Y-m-d H:i:s");
            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".start_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".start_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }
        }

        if (isset($params['project_city']) && !empty($params['project_city']) && strstr(',', $params['project_city'])) {
            $project_city = explode(',', $params['project_city']);
            $params['project_city'] = $project_city[0];
        }

        if (isset($params['project_street']) && !empty($params['project_street']) || isset($params['project_city']) && !empty($params['project_city']) || isset($params['project_state']) && !empty($params['project_state']) || isset($params['project_country']) && !empty($params['project_country'])) {
            $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id   ", null);
        }

        if (isset($params['project_street']) && !empty($params['project_street'])) {
            $select->where($locationTableName . '.address   LIKE ? ', '%' . $params['project_street'] . '%');
        } if (isset($params['project_city']) && !empty($params['project_city'])) {
            $select->where($locationTableName . '.city = ?', $params['project_city']);
        } if (isset($params['project_state']) && !empty($params['project_state'])) {
            $select->where($locationTableName . '.state = ?', $params['project_state']);
        } if (isset($params['project_country']) && !empty($params['project_country'])) {
            $select->where($locationTableName . '.country = ?', $params['project_country']);
        }
        if (!isset($params['location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
            $params['location'] = $params['locationSearch'];

            if (isset($params['locationmilesSearch'])) {
                $params['locationmiles'] = $params['locationmilesSearch'];
            }
        }
        $addGroupBy = 1;
        if (empty($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

            if (isset($params['orderby']) && $params['orderby'] == "distance") {
                $select->order("distance");
            }
            $select->group("$projectTableName.project_id");
            $addGroupBy = 0;
        }
        if ((isset($params['location']) && !empty($params['location'])) || (!empty($params['Latitude']) && !empty($params['Longitude']))) {
            $longitude = 0;
            $latitude = 0;
            $detactLatLng = false;
            if (isset($params['location']) && $params['location']) {
                $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                $detactLatLng = isset($cookieLocation['location']) && $cookieLocation['location'] != $params['location'];
            }
            if ((isset($params['locationmiles']) && (!empty($params['locationmiles']))) || $detactLatLng) {

                if ($params['location']) {
                    $selectLocQuery = $locationTable->select()->where('location = ?', $params['location']);
                    $locationValue = $locationTable->fetchRow($selectLocQuery);
                }

                //check for zip code in location search.
                if ((empty($params['Latitude']) && empty($params['Longitude'])) || $detactLatLng) {
                    if (empty($locationValue)) {

                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Sitecrowdfunding'));
                        if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                            $latitude = $locationResults['latitude'];
                            $longitude = $locationResults['longitude'];
                        }
                    } else {
                        $latitude = (float) $locationValue->latitude;
                        $longitude = (float) $locationValue->longitude;
                    }
                } else {
                    $latitude = (float) $params['Latitude'];
                    $longitude = (float) $params['Longitude'];
                }
                if ($latitude && $latitude && isset($params['location']) && $params['location']) {
                    $seaocore_myLocationDetails['latitude'] = $latitude;
                    $seaocore_myLocationDetails['longitude'] = $longitude;
                    $seaocore_myLocationDetails['location'] = $params['location'];
                    $seaocore_myLocationDetails['locationmiles'] = $params['locationmiles'];

                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                }
            }
            if (isset($params['locationmiles']) && (!empty($params['locationmiles'])) && $latitude && $longitude) {
                $radius = $params['locationmiles'];

                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }

                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);

                if (isset($params['orderby']) && $params['orderby'] == "distance") {
                    $select->order("distance");
                }
            } else {
                $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", null);
                $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $params['location'] . "%");
            }
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if (isset($params['view_view']) && $params['view_view'] == '1') {
            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }

        if (!empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($projectTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }
        if (isset($params['synchronized'])) {
            $select->where($projectTableName . '.synchronized = ?', $params['synchronized']);
        }
        if (!empty($params['category_id'])) {
            $select->where($projectTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($projectTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }
        if (!empty($params['subsubcategory_id'])) {
            $select->where($projectTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }
        //GET TAGMAP TABLE NAME
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
        $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
        $isTagIdSearch = false;
        if (isset($params['tag_id']) && !empty($params['tag_id'])) {
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'))
                    ->where($tagMapTableName . '.resource_type = ?', 'sitecrowdfunding_project')
                    ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
            $isTagIdSearch = true;
        }
        if (isset($params['search']) && !empty($params['search'])) {

            if ($isTagIdSearch == false) {
                $select
                        ->setIntegrityCheck(false)
                        ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'));
            }
            $select->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id", array());
            $select->where("lower($projectTableName.title) LIKE ? OR lower($projectTableName.description) LIKE ? OR lower($projectTableName.desire_desc) LIKE ? OR lower($tagName.text) LIKE ? ", '%' . strtolower($params['search']) . '%');
            $select->group("$projectTableName.project_id");
        }
        if (isset($params['projectType']) && !empty($params['projectType']) && $params['projectType'] != 'user' && $params['projectType'] != 'All') {
            if (strpos($params['projectType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['projectType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $projectTableName . '.parent_id', array(""));
                $select->where($projectTableName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else
                $select->where("$projectTableName.parent_type = ?", $params['projectType']);
        } else if (isset($params['projectType']) && $params['projectType'] == 'user' && $params['projectType'] != 'All') {
            $select->where("$projectTableName.parent_type is NULL");
        }
        if (isset($params['parent_type']) && !empty($params['parent_type'])) {
            $select->where("$projectTableName.parent_type = ?", $params['parent_type']);
        }
        if (isset($params['parent_id']) && !empty($params['parent_id'])) {
            $select->where("$projectTableName.parent_id = ?", $params['parent_id']);
        }
        if (!empty($params['owner_id']))
            $select->where("$projectTableName.owner_id = ?", $params['owner_id']);

        if (isset($params['project_ids']) && !empty($params['project_ids'])) {
            $select->where($projectTableName . '.project_id IN(?)', $params['project_ids']);
        }
        if (isset($params['view_view'])) {
            $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        }
        if (isset($params['showProject']) && !empty($params['showProject'])) {
            switch ($params['showProject']) {
                case 'featured' :
                    $select->where("$projectTableName.featured = ?", 1);
                    break;
                case 'sponsored' :
                    $select->where($projectTableName . '.sponsored = ?', '1');
                    break;
                case 'featuredSponsored' :
                    $select->where("$projectTableName.sponsored = 1 OR $projectTableName.featured = 1");
                    break;
            }
        }
        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($projectTableName . '.featured = ?', 1);
        }
        if (isset($params['selectProjects']) && !empty($params['selectProjects'])) {
            switch ($params['selectProjects']) {
                case 'successful' :
                    $select->where("$projectTableName.state = ?", 'successful');
                    break;
                case 'ongoing' :
                    $select->where("$projectTableName.state = ?", 'published');
                    break;
                case 'failed' :
                    $select->where("$projectTableName.state = ?", 'failed');
                    break;
                case 'all' :
                    break;
            }
        }
        //IN MY PROJECTS WHEN THE TAB IS NOT ALL PROJECT THAN SHOW APPROVED AND NOT DRAFTED PROJECT ONLY
        if (!isset($params['allProjects']) && empty($params['allProjects'])) {
            //DO NOT SHOW THE PROJECTS BEFORE START DATE
            $currentDate = date('Y-m-d H:i:s');
            // naaziya: 15th july 2020: show only published projects only in pages
            // $select->where("$projectTableName.state <> ?", 'draft')
            $select->where("$projectTableName.state = ?", 'published')
                    ->where("$projectTableName.approved = ?", 1)
                    // todo: naaziya: As projects is not configured with gateway, so it is not listing in ui, so we have hide it
                    //->where("$projectTableName.is_gateway_configured = ?", 1)
                    ->where("start_date <= '$currentDate'");
        }

        if (isset($params['selectProjects']) && $params['selectProjects'] == 'ongoing') {
            if (isset($params['daysFilter']) && !empty($params['daysFilter'])) {
                $days = abs(intval($params['daysFilter']));
                $currentDate = date('Y-m-d H:i:s');
                $newDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime("+$days days"))));
                $select->where("funding_end_date BETWEEN '$currentDate' AND '$newDate'");
            }
            if (isset($params['backedPercentFilter']) && !empty($params['backedPercentFilter'])) {
                $backedPercent = floatval($params['backedPercentFilter']);
                $ratio = $backedPercent / 100;
                $tempResult = $this->fetchAll($select);
                $project_ids = array(null);
                foreach ($tempResult as $tempProject) {
                    if ($tempProject->getFundedAmount() / $tempProject->goal_amount > $ratio)
                        $project_ids[] = $tempProject->getIdentity();
                }
                $select->where($projectTableName . '.project_id IN ( ? ) ', $project_ids);
            }
        }

        if (!isset($params['orderby']) && !isset($params['customOrder']))
            $select->order(new Zend_Db_Expr("-project_order DESC"));

        if (isset($params['orderby']) && !empty($params['orderby']) && !isset($params['customOrder'])) {
            switch ($params['orderby']) {
                case "modifiedDate":
                case 'modified_date':
                    $select->order($projectTableName . '.modified_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case "startDate":
                case 'start_date':
                    $select->order($projectTableName . '.start_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case "startDateAsc":
                    $select->order($projectTableName . '.start_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case "backerCount":
                case 'backer_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
                        $backerTableName = $backerTable->info('name');
                        $select = $select->joinLeft($backerTableName, $backerTableName . '.project_id = ' . $projectTableName . '.project_id', array("COUNT($projectTableName.project_id) as total_count"))
                                ->where("$backerTableName.payment_status = 'active' OR $backerTableName.payment_status = 'authorised'")
                                ->order("total_count DESC");
                        $select->where($backerTableName . "$sqlTimeStr");
                    } else {
                        $select->order($projectTableName . '.backer_count DESC');
                        $select->where("$projectTableName.backer_count > ?", 0);
                    }
                    break;
                case "favouriteCount":
                    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'seaocore');
                    $favouriteTableName = $favouriteTable->info('name');
                    $favouriteSelect = $favouriteTable->select()->from($favouriteTable, 'resource_id')
                        ->where("$favouriteTableName.resource_type = 'sitecrowdfunding_project'");
                    $select = $select->where($projectTableName . '.project_id IN ?', $favouriteSelect);
                    break;
                case "backerCountAsc":
                    $select->order($projectTableName . '.backer_count ASC');
                    $select->where("$projectTableName.backer_count > ?", 0);
                    break;
                case "commentCount":
                    $select->order($projectTableName . '.comment_count DESC');
                    $select->where("$projectTableName.comment_count > ?", 0);
                    break;
                case "likeCount":
                case 'like_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                        $popularityTableName = $popularityTable->info('name');
                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $projectTableName . '.project_id', array("COUNT($projectTableName.project_id) as total_count"))
                                ->where($popularityTableName . '.resource_type = ?', 'sitecrowdfunding_project')
                                ->order("total_count DESC");

                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($projectTableName . '.like_count DESC');
                        $select->where("$projectTableName.like_count > ?", 0);
                    }
                    break;
                case 'comment_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                        $popularityTableName = $popularityTable->info('name');
                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $projectTableName . '.project_id', array("COUNT($projectTableName.project_id) as total_count"))
                                ->where($popularityTableName . '.resource_type = ?', 'sitecrowdfunding_project')
                                ->order("total_count DESC");
                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($projectTableName . '.comment_count DESC');
                        $select->where("$projectTableName.comment_count > ?", 0);
                    }
                    break;
                case 'title':
                    $select->order($projectTableName . '.title ASC');
                    break;
                case 'titleReverse':
                    $select->order($projectTableName . '.title DESC');
                    break;
                case 'featured':
                    $select->order($projectTableName . '.featured DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'sponsored':
                    $select->order($projectTableName . '.sponsored DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'sponsoredFeatured':
                    $select->order($projectTableName . '.sponsored DESC');
                    $select->order($projectTableName . '.featured DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'featuredSponsored':
                    $select->order($projectTableName . '.featured DESC');
                    $select->order($projectTableName . '.sponsored DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'random':
                    //$select->order('Rand()');
                    $select->order(new Zend_Db_Expr("-project_order DESC"));
                    //$select->order($projectTableName . '.project_order ASC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case 'mostFunded':

                    $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
                    $backerTableName = $backerTable->info('name');

                    $externalBackersTable = Engine_Api::_()->getDbTable('externalfundings', 'sitecrowdfunding');
                    $externalBackersTableName = $externalBackersTable->info('name');

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

                    $select->columns(array(
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

                    $select
                        ->where($projectTableName. '.is_fund_raisable = ?',1)
                        ->order("total_funders_count DESC");
                    break;
                default:
                    $select->order($projectTableName . '.modified_date DESC');
                    break;
            }
        }
        if($params['orderby'] =='random')
            $select->order(new Zend_Db_Expr("-project_order DESC"));

        if (isset($params['customOrder'])) {
            $ord = implode($params['customOrder'], ',');
            $select->order("FIELD({$ord})");
        }
        if (isset($params['selectLimit']) && !empty($params['selectLimit']) && isset($params['start_index']) && $params['start_index'] >= 0) {
            $select->limit($params['selectLimit'], $params['start_index']);
        } else if (isset($params['selectLimit']) && !empty($params['selectLimit'])) {
            $select->limit($params['selectLimit']);
        }

        //filtering with project id
        if(isset($params['selected_project_ids'])){
            if(!empty($params['selected_project_ids'])){
                $select = $select
                    ->where('project_id IN (?)', $params['selected_project_ids']);
            }else{
                //if params were set and empty data means we should not return any data selecting -1 data
                $select = $select
                    ->where('project_id IN (?)', array(-1));
            }

        }

        // get projects by page_id and no initiative_id and no gallery name
        if(isset($params['page_id']) && !isset($params['initiative']) && !isset($params['initiative_galleries'] ) ){
            if(!empty($params['page_id'])){
                $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($params['page_id']);
                $select = $select->where('project_id IN (?)', $projectsIds);
            }
        }

        // get projects by page_id and no initiative_id and no gallery name
        if(isset($params['page_ids'])){
            if(!empty($params['page_ids'])){
                $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageIds($params['page_ids']);
                $select = $select->where('project_id IN (?)', $projectsIds);
            }
        }

        // get projects by page_id and initiative_id and no gallery name
        if(isset($params['page_id']) && isset($params['initiative'])){

            if( !empty($params['page_id']) && !empty($params['initiative']) ) {

                $initiative = Engine_Api::_()->getItem('sitepage_initiative', $params['initiative']);

                $initiative_id = $initiative['initiative_id'];

                $sections = array();

                // if project-galleries is not passed, then get initiative project galleries
                if (!isset($params['initiative_galleries']) || empty($params['initiative_galleries'])) {
                    $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
                    $initiativeSections = array_filter(array_map("trim", $initiativeSections));

                    if (count($initiativeSections) > 0) {
                        $sections = $initiativeSections;
                    }
                    // show both tag based projects and initiative based projects too
                    $showOnlySectionBasedProjects = false;
                    $showNonSectionBasedProjects = false;
                }
                // if project-galleries is passed, then get that only
                else {
                    // if project-galleries=='OTHER' is passed, then non-section based projects
                    if ($params['initiative_galleries'] == 'OTHER') {

                        $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
                        $initiativeSections = array_filter(array_map("trim", $initiativeSections));

                        if (count($initiativeSections) > 0) {
                            $sections = $initiativeSections;
                        }

                        $showOnlySectionBasedProjects = false;
                        $showNonSectionBasedProjects = true;
                    }
                    // if project-galleries is passed, then get that only
                    else {
                        $sections[] = $params['initiative_galleries'];
                        $showOnlySectionBasedProjects = true;
                        $showNonSectionBasedProjects = false;
                    }
                }

                $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageId($params['page_id']);

                // if tag stored in initiatives, then get based on it
                // if tag is not stored, then get by initiative id

                $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                $tagMapTableName = $tagMapTable->info('name');

                $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
                $tagTableName = $tagTable->info('name');

                // get projects which has no section set to it
                if ($showNonSectionBasedProjects == true) {
                    $select
                        ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                        ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array('text'))
                        ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                        ->where("($tagTableName.text is null OR $tagTableName.text NOT IN (?)) AND $projectTableName.initiative_id = $initiative_id", $sections);
                } else {
                    // get projects which has this section name as tag into it
                    if ($showOnlySectionBasedProjects === true) {
                        $select
                            ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                            ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array('text'))
                            ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                            ->where("$tagTableName.text IN (?)", $sections);
                    } else {
                        // to support older projects, where tag is there and initiative_id isnull
                        // to support new projects, where tag may/maynot there and initiative_id present
                        if (count($sections) > 0) {
                            $select
                                ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                                ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array('text'))
                                ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                                ->where("$tagTableName.text IN (?) OR $projectTableName.initiative_id = $initiative_id", $sections);
                        } else {
                            $select
                                ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                                ->where("$projectTableName.initiative_id = ?", $initiative_id);
                        }
                    }
                }
                $select->group("$projectTableName.project_id");
            }

        }

        return $select;
    }

    public function getProjectIds(array $params) {

        $projectTableName = $this->info('name');

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($projectTableName, 'project_id');

        // get projects by page_id and no initiative_id and no gallery name
        if(isset($params['page_id']) && !isset($params['initiative']) && !isset($params['initiative_galleries'] ) ){
            if(!empty($params['page_id'])){
                $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($params['page_id']);
                $select = $select->where('project_id IN (?)', $projectsIds);
            }
        }

        // get projects by page_id and initiative_id and no gallery name
        if(isset($params['page_id']) && isset($params['initiative'])){

            if( !empty($params['page_id']) && !empty($params['initiative']) ) {

                $initiative = Engine_Api::_()->getItem('sitepage_initiative', $params['initiative']);

                $initiative_id = $initiative['initiative_id'];

                $sections = array();

                // if project-galleries is not passed, then get initiative project galleries
                if (!isset($params['initiative_galleries']) || empty($params['initiative_galleries'])) {
                    $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
                    $initiativeSections = array_filter(array_map("trim", $initiativeSections));

                    if (count($initiativeSections) > 0) {
                        $sections = $initiativeSections;
                    }
                    // show both tag based projects and initiative based projects too
                    $showOnlySectionBasedProjects = false;
                    $showNonSectionBasedProjects = false;
                }
                // if project-galleries is passed, then get that only
                else {
                    // if project-galleries=='OTHER' is passed, then non-section based projects
                    if ($params['initiative_galleries'] == 'OTHER') {

                        $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
                        $initiativeSections = array_filter(array_map("trim", $initiativeSections));

                        if (count($initiativeSections) > 0) {
                            $sections = $initiativeSections;
                        }

                        $showOnlySectionBasedProjects = false;
                        $showNonSectionBasedProjects = true;
                    }
                    // if project-galleries is passed, then get that only
                    else {
                        $sections[] = $params['initiative_galleries'];
                        $showOnlySectionBasedProjects = true;
                        $showNonSectionBasedProjects = false;
                    }
                }

                $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageId($params['page_id']);

                // if tag stored in initiatives, then get based on it
                // if tag is not stored, then get by initiative id

                $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                $tagMapTableName = $tagMapTable->info('name');

                $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
                $tagTableName = $tagTable->info('name');

                // get projects which has no section set to it
                if ($showNonSectionBasedProjects == true) {
                    $select
                        ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                        ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array())
                        ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                        ->where("($tagTableName.text is null OR $tagTableName.text NOT IN (?)) AND $projectTableName.initiative_id = $initiative_id", $sections);
                } else {
                    // get projects which has this section name as tag into it
                    if ($showOnlySectionBasedProjects === true) {
                        $select
                            ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                            ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array())
                            ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                            ->where("$tagTableName.text IN (?)", $sections);
                    } else {
                        // to support older projects, where tag is there and initiative_id isnull
                        // to support new projects, where tag may/maynot there and initiative_id present
                        if (count($sections) > 0) {
                            $select
                                ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                                ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array())
                                ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                                ->where("$tagTableName.text IN (?) OR $projectTableName.initiative_id = $initiative_id", $sections);
                        } else {
                            $select
                                ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                                ->where("$projectTableName.initiative_id = ?", $initiative_id);
                        }
                    }
                }
                $select->where("$projectTableName.state = ?", 'published');
                $select->group("$projectTableName.project_id");
            }

        }

        $result =  $select->query()->fetchAll();

        return $result;

    }

    public function getUserProjects($user_id) {

        $rName = $this->info('name');

        $currentDate = date('Y-m-d H:i:s');

        $select = $this->select()->from($rName, array('project_id'))
            ->where("owner_id", $user_id)
            ->where("state = ?", 'published')
            ->where("approved = ?", 1)
            ->where("start_date <= '$currentDate'")
            ->order("creation_date DESC")
            ->limit(3);

        return $this->fetchALL($select);
    }

    public function getUserProjectAndAdminProjects($user_id) {

        $streamTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $db = $streamTable->getAdapter();
        $union = new Zend_Db_Select($db);

        // user projects
        $rName = $this->info('name');
        $currentDate = date('Y-m-d H:i:s');
        $select1 = $this->select()->from($rName, array('project_id'))
            ->where("owner_id = ?", $user_id)
            ->where("state = ?", 'published')
            ->where("approved = ?", 1)
            ->where("start_date <= '$currentDate'")
            ->order("creation_date DESC");

        // joined as admin projects
        $listTable = Engine_Api::_()->getDbTable('lists', 'sitecrowdfunding');
        $listTableName = $listTable->info('name');
        $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
        $listItemTableName = $listItemTable->info('name');
        $select2 = $listTable->select()
            ->setIntegrityCheck(false)
            ->from($listTableName, array('owner_id as project_id'))
            ->joinInner($listItemTableName, "$listTableName . list_id = $listItemTableName . list_id", null)
            ->where("$listItemTableName.child_id = ?", $user_id)
            ->where("$listTableName.title = ?", "SITECROWDFUNDING_LEADERS");

        $select = $union->union(array('('.$select1->__toString().')','('.$select2->__toString().')'));
        $select = $union->order('project_id DESC')->limit(3);
        $project_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return $project_ids;

    }

    public function getUserProjectAndAdminProjectsCount($user_id) {

        $streamTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $db = $streamTable->getAdapter();
        $union = new Zend_Db_Select($db);

        // user projects
        $rName = $this->info('name');
        $currentDate = date('Y-m-d H:i:s');
        $select1 = $this->select()->from($rName, array('project_id'))
            ->where("owner_id = ?", $user_id)
            ->where("state = ?", 'published')
            ->where("approved = ?", 1)
            ->where("start_date <= '$currentDate'")
            ->order("creation_date DESC");

        // joined as admin projects
        $listTable = Engine_Api::_()->getDbTable('lists', 'sitecrowdfunding');
        $listTableName = $listTable->info('name');
        $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
        $listItemTableName = $listItemTable->info('name');
        $select2 = $listTable->select()
            ->setIntegrityCheck(false)
            ->from($listTableName, array('owner_id as project_id'))
            ->joinInner($listItemTableName, "$listTableName . list_id = $listItemTableName . list_id", null)
            ->where("$listItemTableName.child_id = ?", $user_id)
            ->where("$listTableName.title = ?", "SITECROWDFUNDING_LEADERS");

        $select = $union->union(array('('.$select1->__toString().')','('.$select2->__toString().')'));
        $project_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return count($project_ids);

    }

    public function getTotalUserProjects($user_id) {

        $rName = $this->info('name');

        $currentDate = date('Y-m-d H:i:s');

        return $this->select()->from($rName, new Zend_Db_Expr('COUNT(*)'))
            ->where("owner_id", $user_id)
            ->where("state = ?", 'published')
            ->where("approved = ?", 1)
            ->where("start_date <= '$currentDate'")
            ->order("creation_date DESC")
            ->query()
            ->fetchColumn();

    }

    public function getMyProjectSelect(array $params, $customParams = array()) {

        $projectTableName = $this->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');

        $locationTableName = $locationTable->info('name');
        $select = $this->select()->from($projectTableName, '*');
        if (isset($customParams)) {
            //GET SEARCH TABLE
            $searchTable = Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'search')->info('name');
            //PROCESS OPTIONS
            $tmp = array();
            foreach ($customParams as $k => $v) {
                if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                    continue;
                } else if (false !== strpos($k, '_field_')) {
                    list($null, $field) = explode('_field_', $k);
                    $tmp['field_' . $field] = $v;
                } else if (false !== strpos($k, '_alias_')) {
                    list($null, $alias) = explode('_alias_', $k);
                    $tmp[$alias] = $v;
                } else {
                    $tmp[$k] = $v;
                }
            }
            $customParams = $tmp;

            $select = $select
                ->setIntegrityCheck(false)
                ->joinLeft($searchTable, "$searchTable.item_id = $projectTableName.project_id", null);

            $searchParts = Engine_Api::_()->fields()->getSearchQuery('sitecrowdfunding_project', $customParams);
            foreach ($searchParts as $k => $v) {
                $select->where("`{$searchTable}`.{$k}", $v);
            }
        }

        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $interval = '';
        if (isset($params['interval']) && !empty($params['interval'])) {
            $interval = $params['interval'];
            $current_time = date("Y-m-d H:i:s");
            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".start_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".start_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }
        }

        if (isset($params['project_city']) && !empty($params['project_city']) && strstr(',', $params['project_city'])) {
            $project_city = explode(',', $params['project_city']);
            $params['project_city'] = $project_city[0];
        }

        if (isset($params['project_street']) && !empty($params['project_street']) || isset($params['project_city']) && !empty($params['project_city']) || isset($params['project_state']) && !empty($params['project_state']) || isset($params['project_country']) && !empty($params['project_country'])) {
            $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id   ", null);
        }

        if (isset($params['project_street']) && !empty($params['project_street'])) {
            $select->where($locationTableName . '.address   LIKE ? ', '%' . $params['project_street'] . '%');
        } if (isset($params['project_city']) && !empty($params['project_city'])) {
            $select->where($locationTableName . '.city = ?', $params['project_city']);
        } if (isset($params['project_state']) && !empty($params['project_state'])) {
            $select->where($locationTableName . '.state = ?', $params['project_state']);
        } if (isset($params['project_country']) && !empty($params['project_country'])) {
            $select->where($locationTableName . '.country = ?', $params['project_country']);
        }
        if (!isset($params['location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
            $params['location'] = $params['locationSearch'];

            if (isset($params['locationmilesSearch'])) {
                $params['locationmiles'] = $params['locationmilesSearch'];
            }
        }
        $addGroupBy = 1;
        if (empty($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

            if (isset($params['orderby']) && $params['orderby'] == "distance") {
                $select->order("distance");
            }
            $select->group("$projectTableName.project_id");
            $addGroupBy = 0;
        }
        if ((isset($params['location']) && !empty($params['location'])) || (!empty($params['Latitude']) && !empty($params['Longitude']))) {
            $longitude = 0;
            $latitude = 0;
            $detactLatLng = false;
            if (isset($params['location']) && $params['location']) {
                $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                $detactLatLng = isset($cookieLocation['location']) && $cookieLocation['location'] != $params['location'];
            }
            if ((isset($params['locationmiles']) && (!empty($params['locationmiles']))) || $detactLatLng) {

                if ($params['location']) {
                    $selectLocQuery = $locationTable->select()->where('location = ?', $params['location']);
                    $locationValue = $locationTable->fetchRow($selectLocQuery);
                }

                //check for zip code in location search.
                if ((empty($params['Latitude']) && empty($params['Longitude'])) || $detactLatLng) {
                    if (empty($locationValue)) {

                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Sitecrowdfunding'));
                        if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                            $latitude = $locationResults['latitude'];
                            $longitude = $locationResults['longitude'];
                        }
                    } else {
                        $latitude = (float) $locationValue->latitude;
                        $longitude = (float) $locationValue->longitude;
                    }
                } else {
                    $latitude = (float) $params['Latitude'];
                    $longitude = (float) $params['Longitude'];
                }
                if ($latitude && $latitude && isset($params['location']) && $params['location']) {
                    $seaocore_myLocationDetails['latitude'] = $latitude;
                    $seaocore_myLocationDetails['longitude'] = $longitude;
                    $seaocore_myLocationDetails['location'] = $params['location'];
                    $seaocore_myLocationDetails['locationmiles'] = $params['locationmiles'];

                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                }
            }
            if (isset($params['locationmiles']) && (!empty($params['locationmiles'])) && $latitude && $longitude) {
                $radius = $params['locationmiles'];

                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }

                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);

                if (isset($params['orderby']) && $params['orderby'] == "distance") {
                    $select->order("distance");
                }
            } else {
                $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", null);
                $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $params['location'] . "%");
            }
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if (isset($params['view_view']) && $params['view_view'] == '1') {
            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }

        if (!empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($projectTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }
        if (isset($params['synchronized'])) {
            $select->where($projectTableName . '.synchronized = ?', $params['synchronized']);
        }
        if (!empty($params['category_id'])) {
            $select->where($projectTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($projectTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }
        if (!empty($params['subsubcategory_id'])) {
            $select->where($projectTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }
        //GET TAGMAP TABLE NAME
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
        $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
        $isTagIdSearch = false;
        if (isset($params['tag_id']) && !empty($params['tag_id'])) {
            $select
                ->setIntegrityCheck(false)
                ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'))
                ->where($tagMapTableName . '.resource_type = ?', 'sitecrowdfunding_project')
                ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
            $isTagIdSearch = true;
        }
        if (isset($params['search']) && !empty($params['search'])) {

            if ($isTagIdSearch == false) {
                $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'));
            }
            $select->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id", array());
            $select->where("lower($projectTableName.title) LIKE ? OR lower($projectTableName.description) LIKE ? OR lower($projectTableName.desire_desc) LIKE ? OR lower($tagName.text) LIKE ? ", '%' . strtolower($params['search']) . '%');
            $select->group("$projectTableName.project_id");
        }
        if (isset($params['projectType']) && !empty($params['projectType']) && $params['projectType'] != 'user' && $params['projectType'] != 'All') {
            if (strpos($params['projectType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['projectType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $projectTableName . '.parent_id', array(""));
                $select->where($projectTableName . ".parent_type =?", 'sitereview_listing')
                    ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else
                $select->where("$projectTableName.parent_type = ?", $params['projectType']);
        } else if (isset($params['projectType']) && $params['projectType'] == 'user' && $params['projectType'] != 'All') {
            $select->where("$projectTableName.parent_type is NULL");
        }
        if (isset($params['parent_type']) && !empty($params['parent_type'])) {
            $select->where("$projectTableName.parent_type = ?", $params['parent_type']);
        }
        if (isset($params['parent_id']) && !empty($params['parent_id'])) {
            $select->where("$projectTableName.parent_id = ?", $params['parent_id']);
        }
        if (!empty($params['owner_id']))
            $select->where("$projectTableName.owner_id = ?", $params['owner_id']);

        if (isset($params['project_ids']) && !empty($params['project_ids'])) {
            $select->where($projectTableName . '.project_id IN(?)', $params['project_ids']);
        }
        if (isset($params['view_view'])) {
            $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        }
        if (isset($params['showProject']) && !empty($params['showProject'])) {
            switch ($params['showProject']) {
                case 'featured' :
                    $select->where("$projectTableName.featured = ?", 1);
                    break;
                case 'sponsored' :
                    $select->where($projectTableName . '.sponsored = ?', '1');
                    break;
                case 'featuredSponsored' :
                    $select->where("$projectTableName.sponsored = 1 OR $projectTableName.featured = 1");
                    break;
            }
        }
        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($projectTableName . '.featured = ?', 1);
        }
        if (isset($params['selectProjects']) && !empty($params['selectProjects'])) {
            switch ($params['selectProjects']) {
                case 'successful' :
                    $select->where("$projectTableName.state = ?", 'successful');
                    break;
                case 'ongoing' :
                    $select->where("$projectTableName.state = ?", 'published');
                    break;
                case 'failed' :
                    $select->where("$projectTableName.state = ?", 'failed');
                    break;
                case 'all' :
                    break;
            }
        }
        //IN MY PROJECTS WHEN THE TAB IS NOT ALL PROJECT THAN SHOW APPROVED AND NOT DRAFTED PROJECT ONLY
        if (!isset($params['allProjects']) && empty($params['allProjects'])) {
            //DO NOT SHOW THE PROJECTS BEFORE START DATE
            $currentDate = date('Y-m-d H:i:s');
            // naaziya: 15th july 2020: show only published projects only in pages
            // $select->where("$projectTableName.state <> ?", 'draft')
            $select->where("start_date <= '$currentDate'");
        }

        if (isset($params['selectProjects']) && $params['selectProjects'] == 'ongoing') {
            if (isset($params['daysFilter']) && !empty($params['daysFilter'])) {
                $days = abs(intval($params['daysFilter']));
                $currentDate = date('Y-m-d H:i:s');
                $newDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime("+$days days"))));
                $select->where("funding_end_date BETWEEN '$currentDate' AND '$newDate'");
            }
            if (isset($params['backedPercentFilter']) && !empty($params['backedPercentFilter'])) {
                $backedPercent = floatval($params['backedPercentFilter']);
                $ratio = $backedPercent / 100;
                $tempResult = $this->fetchAll($select);
                $project_ids = array(null);
                foreach ($tempResult as $tempProject) {
                    if ($tempProject->getFundedAmount() / $tempProject->goal_amount > $ratio)
                        $project_ids[] = $tempProject->getIdentity();
                }
                $select->where($projectTableName . '.project_id IN ( ? ) ', $project_ids);
            }
        }

        if (!isset($params['orderby']) && !isset($params['customOrder']))
            $select->order(new Zend_Db_Expr("-project_order DESC"));

        if (isset($params['orderby']) && !empty($params['orderby']) && !isset($params['customOrder'])) {
            switch ($params['orderby']) {
                case "modifiedDate":
                case 'modified_date':
                    $select->order($projectTableName . '.modified_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case "startDate":
                case 'start_date':
                    $select->order($projectTableName . '.start_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case "startDateAsc":
                    $select->order($projectTableName . '.start_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case "backerCount":
                case 'backer_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
                        $backerTableName = $backerTable->info('name');
                        $select = $select->joinLeft($backerTableName, $backerTableName . '.project_id = ' . $projectTableName . '.project_id', array("COUNT($projectTableName.project_id) as total_count"))
                            ->where("$backerTableName.payment_status = 'active' OR $backerTableName.payment_status = 'authorised'")
                            ->order("total_count DESC");
                        $select->where($backerTableName . "$sqlTimeStr");
                    } else {
                        $select->order($projectTableName . '.backer_count DESC');
                        $select->where("$projectTableName.backer_count > ?", 0);
                    }
                    break;
                case "favouriteCount":
                    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'seaocore');
                    $favouriteTableName = $favouriteTable->info('name');
                    $favouriteSelect = $favouriteTable->select()->from($favouriteTable, 'resource_id')
                        ->where("$favouriteTableName.resource_type = 'sitecrowdfunding_project'");
                    $select = $select->where($projectTableName . '.project_id IN ?', $favouriteSelect);
                    break;
                case "backerCountAsc":
                    $select->order($projectTableName . '.backer_count ASC');
                    $select->where("$projectTableName.backer_count > ?", 0);
                    break;
                case "commentCount":
                    $select->order($projectTableName . '.comment_count DESC');
                    $select->where("$projectTableName.comment_count > ?", 0);
                    break;
                case "likeCount":
                case 'like_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                        $popularityTableName = $popularityTable->info('name');
                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $projectTableName . '.project_id', array("COUNT($projectTableName.project_id) as total_count"))
                            ->where($popularityTableName . '.resource_type = ?', 'sitecrowdfunding_project')
                            ->order("total_count DESC");

                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($projectTableName . '.like_count DESC');
                        $select->where("$projectTableName.like_count > ?", 0);
                    }
                    break;
                case 'comment_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                        $popularityTableName = $popularityTable->info('name');
                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $projectTableName . '.project_id', array("COUNT($projectTableName.project_id) as total_count"))
                            ->where($popularityTableName . '.resource_type = ?', 'sitecrowdfunding_project')
                            ->order("total_count DESC");
                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($projectTableName . '.comment_count DESC');
                        $select->where("$projectTableName.comment_count > ?", 0);
                    }
                    break;
                case 'title':
                    $select->order($projectTableName . '.title ASC');
                    break;
                case 'titleReverse':
                    $select->order($projectTableName . '.title DESC');
                    break;
                case 'featured':
                    $select->order($projectTableName . '.featured DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'sponsored':
                    $select->order($projectTableName . '.sponsored DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'sponsoredFeatured':
                    $select->order($projectTableName . '.sponsored DESC');
                    $select->order($projectTableName . '.featured DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'featuredSponsored':
                    $select->order($projectTableName . '.featured DESC');
                    $select->order($projectTableName . '.sponsored DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'random':
                    //$select->order('Rand()');
                    $select->order(new Zend_Db_Expr("-project_order DESC"));
                    //$select->order($projectTableName . '.project_order ASC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case 'mostFunded':

                    $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
                    $backerTableName = $backerTable->info('name');

                    $externalBackersTable = Engine_Api::_()->getDbTable('externalfundings', 'sitecrowdfunding');
                    $externalBackersTableName = $externalBackersTable->info('name');

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

                    $select->columns(array(
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

                    $select
                        ->where($projectTableName. '.is_fund_raisable = ?',1)
                        ->order("total_funders_count DESC");
                    break;
                default:
                    $select->order($projectTableName . '.modified_date DESC');
                    break;
            }
        }
        if($params['orderby'] =='random')
            $select->order(new Zend_Db_Expr("-project_order DESC"));

        if (isset($params['customOrder'])) {
            $ord = implode($params['customOrder'], ',');
            $select->order("FIELD({$ord})");
        }
        if (isset($params['selectLimit']) && !empty($params['selectLimit']) && isset($params['start_index']) && $params['start_index'] >= 0) {
            $select->limit($params['selectLimit'], $params['start_index']);
        } else if (isset($params['selectLimit']) && !empty($params['selectLimit'])) {
            $select->limit($params['selectLimit']);
        }

        //filtering with project id
        if(isset($params['selected_project_ids'])){
            if(!empty($params['selected_project_ids'])){
                $select = $select
                    ->where('project_id IN (?)', $params['selected_project_ids']);
            }else{
                //if params were set and empty data means we should not return any data selecting -1 data
                $select = $select
                    ->where('project_id IN (?)', array(-1));
            }

        }

        // get projects by page_id and no initiative_id and no gallery name
        if(isset($params['page_id']) && !isset($params['initiative']) && !isset($params['initiative_galleries'] ) ){
            if(!empty($params['page_id'])){
                $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($params['page_id']);
                $select = $select->where('project_id IN (?)', $projectsIds);
            }
        }

        // get projects by page_id and initiative_id and no gallery name
        if(isset($params['page_id']) && isset($params['initiative'])){

            if( !empty($params['page_id']) && !empty($params['initiative']) ) {

                $initiative = Engine_Api::_()->getItem('sitepage_initiative', $params['initiative']);

                $initiative_id = $initiative['initiative_id'];

                $sections = array();

                // if project-galleries is not passed, then get initiative project galleries
                if (!isset($params['initiative_galleries']) || empty($params['initiative_galleries'])) {
                    $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
                    $initiativeSections = array_filter(array_map("trim", $initiativeSections));

                    if (count($initiativeSections) > 0) {
                        $sections = $initiativeSections;
                    }
                    // show both tag based projects and initiative based projects too
                    $showOnlySectionBasedProjects = false;
                    $showNonSectionBasedProjects = false;
                }
                // if project-galleries is passed, then get that only
                else {
                    // if project-galleries=='OTHER' is passed, then non-section based projects
                    if ($params['initiative_galleries'] == 'OTHER') {

                        $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
                        $initiativeSections = array_filter(array_map("trim", $initiativeSections));

                        if (count($initiativeSections) > 0) {
                            $sections = $initiativeSections;
                        }

                        $showOnlySectionBasedProjects = false;
                        $showNonSectionBasedProjects = true;
                    }
                    // if project-galleries is passed, then get that only
                    else {
                        $sections[] = $params['initiative_galleries'];
                        $showOnlySectionBasedProjects = true;
                        $showNonSectionBasedProjects = false;
                    }
                }

                $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageId($params['page_id']);

                // if tag stored in initiatives, then get based on it
                // if tag is not stored, then get by initiative id

                $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                $tagMapTableName = $tagMapTable->info('name');

                $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
                $tagTableName = $tagTable->info('name');

                // get projects which has no section set to it
                if ($showNonSectionBasedProjects == true) {
                    $select
                        ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                        ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array('text'))
                        ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                        ->where("($tagTableName.text is null OR $tagTableName.text NOT IN (?)) AND $projectTableName.initiative_id = $initiative_id", $sections);
                } else {
                    // get projects which has this section name as tag into it
                    if ($showOnlySectionBasedProjects === true) {
                        $select
                            ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                            ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array('text'))
                            ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                            ->where("$tagTableName.text IN (?)", $sections);
                    } else {
                        // to support older projects, where tag is there and initiative_id isnull
                        // to support new projects, where tag may/maynot there and initiative_id present
                        if (count($sections) > 0) {
                            $select
                                ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                                ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array('text'))
                                ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                                ->where("$tagTableName.text IN (?) OR $projectTableName.initiative_id = $initiative_id", $sections);
                        } else {
                            $select
                                ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                                ->where("$projectTableName.initiative_id = ?", $initiative_id);
                        }
                    }
                }
                $select->group("$projectTableName.project_id");
            }

        }

        return $select;
    }
    public function getInitiativeProjectsByLocationSelect($page_id,$intiative_id) {

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
        $locationTableName = $locationTable->info('name');

           // get projects by page_id and initiative_id and no gallery name
                if($page_id && $intiative_id){
                    if($page_id && $intiative_id){
                        $initiative = Engine_Api::_()->getItem('sitepage_initiative', $intiative_id);
                      //  $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageId($page_id);

                        $projects  = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsByPageIdAndInitiativesId($page_id,$intiative_id,null);
                        $projectsIds  = [];
                        foreach ($projects as $project) {
                              array_push($projectsIds,$project['project_id']);
                        }

                    }

                }

        // get only location based projects
        $select = $this->select()->distinct(true)->setIntegrityCheck(false)->from($locationTableName,'*');
        $select->where($locationTableName . '.project_id IN (?)',$projectsIds)->group('location');


        return $this->fetchAll($select);

    }


        // used in initiative landing page
    public function getInitiativeProjectsSelect(array $params) {

        $projectTableName = $this->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
        $locationTableName = $locationTable->info('name');

        $select = $this->select()->from($projectTableName, '*');

        if (isset($params['project_ids']) && !empty($params['project_ids'])) {
            $select->where($projectTableName . '.project_id IN(?)', $params['project_ids']);
        }

        $currentDate = date('Y-m-d H:i:s');
        $select->where("$projectTableName.state = ?", 'published')
            ->where("$projectTableName.approved = ?", 1)
            ->where("start_date <= '$currentDate'");
        $select->order(new Zend_Db_Expr("-project_order DESC"));

        // get projects by page_id and no initiative_id and no gallery name
        if(isset($params['page_id']) && !isset($params['initiative']) && !isset($params['initiative_galleries'] ) ){
            if(!empty($params['page_id'])){
                $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($params['page_id']);
                $select = $select->where('project_id IN (?)', $projectsIds);
            }
        }

        // get projects by page_id and initiative_id and no gallery name
        if(isset($params['page_id']) && isset($params['initiative'])){

            if( !empty($params['page_id']) && !empty($params['initiative']) ) {

                $initiative = Engine_Api::_()->getItem('sitepage_initiative', $params['initiative']);

                $initiative_id = $initiative['initiative_id'];

                $sections = array();

                // if project-galleries is not passed, then get initiative project galleries
                if (!isset($params['initiative_galleries']) || empty($params['initiative_galleries'])) {
                    $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
                    $initiativeSections = array_filter(array_map("trim", $initiativeSections));

                    if (count($initiativeSections) > 0) {
                        $sections = $initiativeSections;
                    }
                    // show both tag based projects and initiative based projects too
                    $showOnlySectionBasedProjects = false;
                    $showNonSectionBasedProjects = false;
                }
                // if project-galleries is passed, then get that only
                else {
                    // if project-galleries=='OTHER' is passed, then non-section based projects
                    if ($params['initiative_galleries'] == 'OTHER') {

                        $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
                        $initiativeSections = array_filter(array_map("trim", $initiativeSections));

                        if (count($initiativeSections) > 0) {
                            $sections = $initiativeSections;
                        }

                        $showOnlySectionBasedProjects = false;
                        $showNonSectionBasedProjects = true;
                    }
                    // if project-galleries is passed, then get that only
                    else {
                        $sections[] = $params['initiative_galleries'];
                        $showOnlySectionBasedProjects = true;
                        $showNonSectionBasedProjects = false;
                    }
                }

                $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageId($params['page_id']);

                // if tag stored in initiatives, then get based on it
                // if tag is not stored, then get by initiative id

                $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                $tagMapTableName = $tagMapTable->info('name');

                $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
                $tagTableName = $tagTable->info('name');

                // get projects which has no section set to it
                if ($showNonSectionBasedProjects == true) {
                    $select
                        ->setIntegrityCheck(false)
                        ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                        ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array('text'))
                        ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                        ->where("($tagTableName.text is null OR $tagTableName.text NOT IN (?)) AND $projectTableName.initiative_id = $initiative_id", $sections);
                } else {
                    // get projects which has this section name as tag into it
                    if ($showOnlySectionBasedProjects === true) {
                        $select
                            ->setIntegrityCheck(false)
                            ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                            ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array('text'))
                            ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                            ->where("$tagTableName.text IN (?)", $sections);
                    } else {
                        // to support older projects, where tag is there and initiative_id isnull
                        // to support new projects, where tag may/maynot there and initiative_id present
                        if (count($sections) > 0) {
                            $select
                                ->setIntegrityCheck(false)
                                ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                                ->joinLeft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array('text'))
                                ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                                ->where("$tagTableName.text IN (?) OR $projectTableName.initiative_id = $initiative_id", $sections);
                        } else {
                            $select
                                ->where($projectTableName . '.project_id IN (?)', $projectsIds)
                                ->where("$projectTableName.initiative_id = ?", $initiative_id);
                        }
                    }
                }
                $select->group("$projectTableName.project_id");
            }

        }

        // get only location based projects
        if(isset($params['location_only_projects'])){
            if($params['location_only_projects'] === true ){
                $select
                    ->setIntegrityCheck(false)
                    ->joinInner($locationTableName, "$locationTableName.project_id = $projectTableName.project_id", array());
            }
        }

        // get only search projects
        if (isset($params['search']) && !empty($params['search'])) {

            $isTagIdSearch = false;

            //GET TAGMAP TABLE NAME
            $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
            $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');

            if ($isTagIdSearch == false) {
                $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'));
            }
            $select->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id", array());
            $select->where("lower($projectTableName.title) LIKE ? OR lower($projectTableName.description) LIKE ? OR lower($projectTableName.desire_desc) LIKE ? OR lower($tagName.text) LIKE ? ", '%' . strtolower($params['search']) . '%');
            $select->group("$projectTableName.project_id");
        }


        $latitude = (float) $params['Latitude'];
        $longitude = (float) $params['Longitude'];
        $radius = $params['locationmiles'];

        if (isset($radius) && (!empty($radius)) && $latitude && $longitude) {

            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }

            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";
            $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

        }

        if(isset($params['location']) && !empty($params['location'])){
            $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", null);
            $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $params['location'] . "%");
        }

        return $select;
    }

    // Retrive project which has set location only
    public function getProjectLocationSelect(array $params, $customParams = array()) {

        $projectTableName = $this->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');

        $locationTableName = $locationTable->info('name');
        $select = $this->select()->from($projectTableName, '*');

        if (isset($customParams)) {
            //GET SEARCH TABLE
            $searchTable = Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'search')->info('name');
            //PROCESS OPTIONS
            $tmp = array();
            foreach ($customParams as $k => $v) {
                if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                    continue;
                } else if (false !== strpos($k, '_field_')) {
                    list($null, $field) = explode('_field_', $k);
                    $tmp['field_' . $field] = $v;
                } else if (false !== strpos($k, '_alias_')) {
                    list($null, $alias) = explode('_alias_', $k);
                    $tmp[$alias] = $v;
                } else {
                    $tmp[$k] = $v;
                }
            }
            $customParams = $tmp;

            $select = $select
                ->setIntegrityCheck(false)
                ->joinLeft($searchTable, "$searchTable.item_id = $projectTableName.project_id", null);

            $searchParts = Engine_Api::_()->fields()->getSearchQuery('sitecrowdfunding_project', $customParams);
            foreach ($searchParts as $k => $v) {
                $select->where("`{$searchTable}`.{$k}", $v);
            }
        }

        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $interval = '';
        if (isset($params['interval']) && !empty($params['interval'])) {
            $interval = $params['interval'];
            $current_time = date("Y-m-d H:i:s");
            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".start_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".start_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }
        }

        if (isset($params['project_city']) && !empty($params['project_city']) && strstr(',', $params['project_city'])) {
            $project_city = explode(',', $params['project_city']);
            $params['project_city'] = $project_city[0];
        }

        if (isset($params['project_street']) && !empty($params['project_street']) || isset($params['project_city']) && !empty($params['project_city']) || isset($params['project_state']) && !empty($params['project_state']) || isset($params['project_country']) && !empty($params['project_country'])) {
            $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id   ", null);
        }

        if (isset($params['project_street']) && !empty($params['project_street'])) {
            $select->where($locationTableName . '.address   LIKE ? ', '%' . $params['project_street'] . '%');
        } if (isset($params['project_city']) && !empty($params['project_city'])) {
            $select->where($locationTableName . '.city = ?', $params['project_city']);
        } if (isset($params['project_state']) && !empty($params['project_state'])) {
            $select->where($locationTableName . '.state = ?', $params['project_state']);
        } if (isset($params['project_country']) && !empty($params['project_country'])) {
            $select->where($locationTableName . '.country = ?', $params['project_country']);
        }
        if (!isset($params['location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
            $params['location'] = $params['locationSearch'];

            if (isset($params['locationmilesSearch'])) {
                $params['locationmiles'] = $params['locationmilesSearch'];
            }
        }
        $addGroupBy = 1;
        if (empty($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

            if (isset($params['orderby']) && $params['orderby'] == "distance") {
                $select->order("distance");
            }
            $select->group("$projectTableName.project_id");
            $addGroupBy = 0;
        }
        if ((isset($params['location']) && !empty($params['location'])) || (!empty($params['Latitude']) && !empty($params['Longitude']))) {
            $longitude = 0;
            $latitude = 0;
            $detactLatLng = false;
            if (isset($params['location']) && $params['location']) {
                $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                $detactLatLng = isset($cookieLocation['location']) && $cookieLocation['location'] != $params['location'];
            }
            if ((isset($params['locationmiles']) && (!empty($params['locationmiles']))) || $detactLatLng) {

                if ($params['location']) {
                    $selectLocQuery = $locationTable->select()->where('location = ?', $params['location']);
                    $locationValue = $locationTable->fetchRow($selectLocQuery);
                }

                //check for zip code in location search.
                if ((empty($params['Latitude']) && empty($params['Longitude'])) || $detactLatLng) {
                    if (empty($locationValue)) {

                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Sitecrowdfunding'));
                        if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                            $latitude = $locationResults['latitude'];
                            $longitude = $locationResults['longitude'];
                        }
                    } else {
                        $latitude = (float) $locationValue->latitude;
                        $longitude = (float) $locationValue->longitude;
                    }
                } else {
                    $latitude = (float) $params['Latitude'];
                    $longitude = (float) $params['Longitude'];
                }
                if ($latitude && $latitude && isset($params['location']) && $params['location']) {
                    $seaocore_myLocationDetails['latitude'] = $latitude;
                    $seaocore_myLocationDetails['longitude'] = $longitude;
                    $seaocore_myLocationDetails['location'] = $params['location'];
                    $seaocore_myLocationDetails['locationmiles'] = $params['locationmiles'];

                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                }
            }
            if (isset($params['locationmiles']) && (!empty($params['locationmiles'])) && $latitude && $longitude) {
                $radius = $params['locationmiles'];

                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }

                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);

                if (isset($params['orderby']) && $params['orderby'] == "distance") {
                    $select->order("distance");
                }
            } else {
                $select->join($locationTableName, "$projectTableName.project_id = $locationTableName.project_id", null);
                $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $params['location'] . "%");
            }
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if (isset($params['view_view']) && $params['view_view'] == '1') {
            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }

        if (!empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($projectTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }
        if (isset($params['synchronized'])) {
            $select->where($projectTableName . '.synchronized = ?', $params['synchronized']);
        }
        if (!empty($params['category_id'])) {
            $select->where($projectTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($projectTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }
        if (!empty($params['subsubcategory_id'])) {
            $select->where($projectTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }
        //GET TAGMAP TABLE NAME
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
        $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
        $isTagIdSearch = false;
        if (isset($params['tag_id']) && !empty($params['tag_id'])) {
            $select
                ->setIntegrityCheck(false)
                ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'))
                ->where($tagMapTableName . '.resource_type = ?', 'sitecrowdfunding_project')
                ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
            $isTagIdSearch = true;
        }
        if (isset($params['search']) && !empty($params['search'])) {

            if ($isTagIdSearch == false) {
                $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $projectTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'));
            }
            $select->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id", array());
            $select->where("lower($projectTableName.title) LIKE ? OR lower($projectTableName.description) LIKE ? OR lower($tagName.text) LIKE ? ", '%' . strtolower($params['search']) . '%');
            $select->group("$projectTableName.project_id");
        }
        if (isset($params['projectType']) && !empty($params['projectType']) && $params['projectType'] != 'user' && $params['projectType'] != 'All') {
            if (strpos($params['projectType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['projectType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $projectTableName . '.parent_id', array(""));
                $select->where($projectTableName . ".parent_type =?", 'sitereview_listing')
                    ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else
                $select->where("$projectTableName.parent_type = ?", $params['projectType']);
        } else if (isset($params['projectType']) && $params['projectType'] == 'user' && $params['projectType'] != 'All') {
            $select->where("$projectTableName.parent_type is NULL");
        }
        if (isset($params['parent_type']) && !empty($params['parent_type'])) {
            $select->where("$projectTableName.parent_type = ?", $params['parent_type']);
        }
        if (isset($params['parent_id']) && !empty($params['parent_id'])) {
            $select->where("$projectTableName.parent_id = ?", $params['parent_id']);
        }
        if (!empty($params['owner_id']))
            $select->where("$projectTableName.owner_id = ?", $params['owner_id']);

        if (isset($params['project_ids']) && !empty($params['project_ids'])) {
            $select->where($projectTableName . '.project_id IN(?)', $params['project_ids']);
        }
        if (isset($params['view_view'])) {
            $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        }
        if (isset($params['showProject']) && !empty($params['showProject'])) {
            switch ($params['showProject']) {
                case 'featured' :
                    $select->where("$projectTableName.featured = ?", 1);
                    break;
                case 'sponsored' :
                    $select->where($projectTableName . '.sponsored = ?', '1');
                    break;
                case 'featuredSponsored' :
                    $select->where("$projectTableName.sponsored = 1 OR $projectTableName.featured = 1");
                    break;
            }
        }
        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($projectTableName . '.featured = ?', 1);
        }
        if (isset($params['selectProjects']) && !empty($params['selectProjects'])) {
            switch ($params['selectProjects']) {
                case 'successful' :
                    $select->where("$projectTableName.state = ?", 'successful');
                    break;
                case 'ongoing' :
                    $select->where("$projectTableName.state = ?", 'published');
                    break;
                case 'failed' :
                    $select->where("$projectTableName.state = ?", 'failed');
                    break;
                case 'all' :
                    break;
            }
        }
        //IN MY PROJECTS WHEN THE TAB IS NOT ALL PROJECT THAN SHOW APPROVED AND NOT DRAFTED PROJECT ONLY
        if (!isset($params['allProjects']) && empty($params['allProjects'])) {
            //DO NOT SHOW THE PROJECTS BEFORE START DATE
            $currentDate = date('Y-m-d H:i:s');
            // naaziya: 15th july 2020: show only published projects only in pages
            // $select->where("$projectTableName.state <> ?", 'draft')
            $select->where("$projectTableName.state = ?", 'published')
                ->where("$projectTableName.approved = ?", 1)
                // todo: naaziya: As projects is not configured with gateway, so it is not listing in ui, so we have hide it
                //->where("$projectTableName.is_gateway_configured = ?", 1)
                ->where("start_date <= '$currentDate'");
        }

        if (isset($params['selectProjects']) && $params['selectProjects'] == 'ongoing') {
            if (isset($params['daysFilter']) && !empty($params['daysFilter'])) {
                $days = abs(intval($params['daysFilter']));
                $currentDate = date('Y-m-d H:i:s');
                $newDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime("+$days days"))));
                $select->where("funding_end_date BETWEEN '$currentDate' AND '$newDate'");
            }
            if (isset($params['backedPercentFilter']) && !empty($params['backedPercentFilter'])) {
                $backedPercent = floatval($params['backedPercentFilter']);
                $ratio = $backedPercent / 100;
                $tempResult = $this->fetchAll($select);
                $project_ids = array(null);
                foreach ($tempResult as $tempProject) {
                    if ($tempProject->getFundedAmount() / $tempProject->goal_amount > $ratio)
                        $project_ids[] = $tempProject->getIdentity();
                }
                $select->where($projectTableName . '.project_id IN ( ? ) ', $project_ids);
            }
        }

        if (!isset($params['orderby']) && !isset($params['customOrder']))
            $select->order("$projectTableName.start_date DESC");

        if (isset($params['orderby']) && !empty($params['orderby']) && !isset($params['customOrder'])) {
            switch ($params['orderby']) {
                case "modifiedDate":
                case 'modified_date':
                    $select->order($projectTableName . '.modified_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case "startDate":
                case 'start_date':
                    $select->order($projectTableName . '.start_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case "startDateAsc":
                    $select->order($projectTableName . '.start_date ASC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                    }
                    break;
                case "backerCount":
                case 'backer_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
                        $backerTableName = $backerTable->info('name');
                        $select = $select->joinLeft($backerTableName, $backerTableName . '.project_id = ' . $projectTableName . '.project_id', array("COUNT($projectTableName.project_id) as total_count"))
                            ->where("$backerTableName.payment_status = 'active' OR $backerTableName.payment_status = 'authorised'")
                            ->order("total_count DESC");
                        $select->where($backerTableName . "$sqlTimeStr");
                    } else {
                        $select->order($projectTableName . '.backer_count DESC');
                        $select->where("$projectTableName.backer_count > ?", 0);
                    }
                    break;
                case "favouriteCount":
                    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'seaocore');
                    $favouriteTableName = $favouriteTable->info('name');
                    $favouriteSelect = $favouriteTable->select()->from($favouriteTable, 'resource_id')
                        ->where("$favouriteTableName.resource_type = 'sitecrowdfunding_project'");
                    $select = $select->where($projectTableName . '.project_id IN ?', $favouriteSelect);
                    break;
                case "backerCountAsc":
                    $select->order($projectTableName . '.backer_count ASC');
                    $select->where("$projectTableName.backer_count > ?", 0);
                    break;
                case "commentCount":
                    $select->order($projectTableName . '.comment_count DESC');
                    $select->where("$projectTableName.comment_count > ?", 0);
                    break;
                case "likeCount":
                case 'like_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                        $popularityTableName = $popularityTable->info('name');
                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $projectTableName . '.project_id', array("COUNT($projectTableName.project_id) as total_count"))
                            ->where($popularityTableName . '.resource_type = ?', 'sitecrowdfunding_project')
                            ->order("total_count DESC");

                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($projectTableName . '.like_count DESC');
                        $select->where("$projectTableName.like_count > ?", 0);
                    }
                    break;
                case 'comment_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                        $popularityTableName = $popularityTable->info('name');
                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $projectTableName . '.project_id', array("COUNT($projectTableName.project_id) as total_count"))
                            ->where($popularityTableName . '.resource_type = ?', 'sitecrowdfunding_project')
                            ->order("total_count DESC");
                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($projectTableName . '.comment_count DESC');
                        $select->where("$projectTableName.comment_count > ?", 0);
                    }
                    break;
                case 'title':
                    $select->order($projectTableName . '.title ASC');
                    break;
                case 'titleReverse':
                    $select->order($projectTableName . '.title DESC');
                    break;
                case 'featured':
                    $select->order($projectTableName . '.featured DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'sponsored':
                    $select->order($projectTableName . '.sponsored DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'sponsoredFeatured':
                    $select->order($projectTableName . '.sponsored DESC');
                    $select->order($projectTableName . '.featured DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'featuredSponsored':
                    $select->order($projectTableName . '.featured DESC');
                    $select->order($projectTableName . '.sponsored DESC');
                    $select->order($projectTableName . '.start_date ASC');
                    break;
                case 'random':
                   // $select->order('Rand()');
                    $select->order(new Zend_Db_Expr("-project_order DESC"));
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($projectTableName . "$sqlTimeStr");
                        $select->order(new Zend_Db_Expr("-project_order DESC"));
                    }
                    break;
                case 'mostFunded':
                    $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
                    $backerTableName = $backerTable->info('name');
                    $select = $select->joinLeft($backerTableName, $backerTableName . '.project_id = ' . $projectTableName . '.project_id', array("SUM($backerTableName.amount) as total_amount"))
                        ->where("$backerTableName.payment_status = 'active' OR $backerTableName.payment_status = 'authorised'")
                        ->order("total_amount DESC");
                    break;
                default:
                    $select->order($projectTableName . '.modified_date DESC');
                    break;
            }
        }
        if($params['orderby'] =='random')
            $select->order(new Zend_Db_Expr("-project_order DESC"));

        if (isset($params['customOrder'])) {
            $ord = implode($params['customOrder'], ',');
            $select->order("FIELD({$ord})");
        }
        if (isset($params['selectLimit']) && !empty($params['selectLimit']) && isset($params['start_index']) && $params['start_index'] >= 0) {
            $select->limit($params['selectLimit'], $params['start_index']);
        } else if (isset($params['selectLimit']) && !empty($params['selectLimit'])) {
            $select->limit($params['selectLimit']);
        }

        //filtering with project id
        if(isset($params['selected_project_ids'])){
            if(!empty($params['selected_project_ids'])){
                $select = $select
                    ->where('project_id IN (?)', $params['selected_project_ids']);
            }else{
                //if params were set and empty data means we should not return any data selecting -1 data
                $select = $select
                    ->where('project_id IN (?)', array(-1));
            }
        }

        return $this->fetchAll($select);
    }

    public function getMappedSitecrowdfunding($category_id) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($category_id)) {
            return null;
        }

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), 'project_id')
                ->where("category_id = $category_id OR subcategory_id = $category_id OR subsubcategory_id = $category_id");

        //GET DATA
        $categoryData = $this->fetchAll($select);

        if (!empty($categoryData)) {
            return $categoryData->toArray();
        }

        return null;
    }

    public function getCategoryList($category_id, $categoryType) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($category_id)) {
            return null;
        }

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), 'project_id')
                ->where("$categoryType = ?", $category_id);

        //GET DATA
        return $this->fetchAll($select);
    }

    /**
     * Get project count based on category
     *
     * @param int $id
     * @param string $column_name
     * @param int $authorization
     * @return project count
     */
    public function getProjectsCount($id, $column_name, $foruser = null) {
        $projectTableName = $this->info('name');
        //DO NOT INCLUDE THE PROJECTS BEFORE START DATE
        $currentDate = date('Y-m-d H:i:s');
        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'));

        if (!empty($column_name) && !empty($id)) {
            $select->where("$column_name = ?", $id);
            $select->where($projectTableName . '.approved = ?', 1)
                    ->where($projectTableName . '.state = ?', 'published')
                    ->where($projectTableName.'.start_date <= ?',$currentDate);
        }

        $totalProjects = $select->query()->fetchColumn();

        //RETURN PROJECTS COUNT
        return $totalProjects;
    }


    public function getPageProjectsCountByCategoryId($category_id, $page_id) {

        //DO NOT INCLUDE THE PROJECTS BEFORE START DATE
        $currentDate = date('Y-m-d H:i:s');

        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getPageProjects($page_id);

        $totalProjects = 0;

        if( count($projectsIds) > 0 ){
            //MAKE QUERY
            $select = $this->select()->from($this->info('name'), array('COUNT(*) AS count'));

            $select
                ->where("project_id IN (?)", $projectsIds)
                ->where("category_id = ?", $category_id)
                ->where("state = ?", 'published')
                ->where("approved = ?", 1)
                ->where("start_date <= '$currentDate'")
                ->order(new Zend_Db_Expr("-project_order DESC"));

            $totalProjects = $select->query()->fetchColumn();
        }

        //RETURN PROJECTS COUNT
        return $totalProjects;
    }

    public function getProjectPaginator($params, $customParams = array()) {
        return Zend_Paginator::factory($this->getProjectSelect($params, $customParams));
    }
    public function getMyProjectPaginator($params, $customParams = array()) {
        return Zend_Paginator::factory($this->getMyProjectSelect($params, $customParams));
    }

    public function getNetworkBaseSql($select, $params = array()) {
        $select = $this->addPrivacyProjectsSQl($select, $this->info('name'));
        if (empty($select))
            return;

        $sitecrowdfunding_tableName = $this->info('name');

        //START NETWORK WORK
        $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.network', 0);

        if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
            $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);

            if (!Engine_Api::_()->sitecrowdfunding()->projectBaseNetworkEnable()) {
                $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

                if (!empty($viewerNetwork)) {
                    if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
                        $select->setIntegrityCheck(false)
                                ->from($sitecrowdfunding_tableName);
                    }
                    $networkMembershipName = $networkMembershipTable->info('name');
                    $select
                            ->join($networkMembershipName, "`{$sitecrowdfunding_tableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                            ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                            ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
                    if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
                        $select->group($sitecrowdfunding_tableName . ".project_id");
                    }
                    if (isset($params['extension_group']) && !empty($params['extension_group'])) {
                        $select->group($params['extension_group']);
                    }
                }
            } else {
                $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);
                $str = array();
                $columnName = "`{$sitecrowdfunding_tableName}`.networks_privacy";
                foreach ($viewerNetwork as $networkvalue) {
                    $network_id = $networkvalue->resource_id;
                    $str[] = "'" . $network_id . "'";
                    $str[] = "'" . $network_id . ",%'";
                    $str[] = "'%," . $network_id . ",%'";
                    $str[] = "'%," . $network_id . "'";
                }
                if (!empty($str)) {
                    $likeNetworkVale = (string) ( join(" or $columnName  LIKE ", $str) );
                    $select->where($columnName . ' LIKE ' . $likeNetworkVale . ' or ' . $columnName . " IS NULL");
                } else {
                    $select->where($columnName . " IS NULL");
                }
            }
            //END NETWORK WORK
        }
        return $select;
    }

    public function addPrivacyProjectsSQl($select, $tableName = null) {

        $column = $tableName ? "$tableName.project_id" : "project_id";

        return $select->where("$column IN(?)", $this->getOnlyViewableProjectsId());
    }

    public function getOnlyViewableProjectsId() {

        $viewer = Engine_Api::_()->user()->getViewer();
        set_time_limit(0);
        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
        $project_select = $table->select()
                ->where('search = ?', true)
                ->order('project_id DESC');

        $i = 0;
        $project_ids = array();
        foreach ($project_select->getTable()->fetchAll($project_select) as $project) {
            if ($project->isOwner($viewer) || Engine_Api::_()->authorization()->isAllowed($project, $viewer, 'view')) {
                $project_ids[$i++] = $project->project_id;
            }
        }
        if (empty($project_ids))
            $project_ids = array(0);

        return $project_ids;
    }

    public function getDayItems($title, $limit = 10) {
        $currentDate = date('Y-m-d H:i:s');
        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('project_id', 'owner_id', 'title', 'photo_id'))
                ->where('lower(title)  LIKE ? ', '%' . strtolower($title) . '%')
                ->where('search = ?', '1')
                ->where("state <> ?", 'draft')
                ->where("approved = ?", 1)
                ->where("start_date <= '$currentDate'")
                ->where("is_gateway_configured = ?", 1)
                ->order('title ASC')
                ->limit($limit);
        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    public function getReports($values = array()) {

        $projectTableName = $this->info('name');

        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backersTableName = $backersTable->info('name');

        $usersTableName = Engine_Api::_()->getItemTable('user')->info('name');

        if (!empty($values['select_project']))
            $select_project = $values['select_project']; 

        if ($values['report_type'] == 'summarised') {
            $startTimestamp = $values['start_cal']['date'];
            $endTimestamp = $values['end_cal']['date'];
        } else {
            $startTimestamp = date('Y-m-d H:i:s', mktime(00, 00, 00, $values['from_month']['month'], $values['from_month']['day'], $values['from_month']['year']));

            $endTimestamp = date('Y-m-d H:i:s', mktime(23, 59, 59, $values['to_month']['month'], $values['to_month']['day'], $values['to_month']['year']));
        }
        
        $select = $backersTable->select()->setIntegrityCheck(false);
        if ($select_project == 'specific_project' && !empty($values['project_id'])) {
            $project_id = $values['project_id'];
            $select->from($backersTableName)
                    ->where("$backersTableName.project_id = $project_id")
                    ->where("$backersTableName.payment_status = 'active'")
                    ->where("$backersTableName.payout_status = 'success'");
            if (!empty($startTimestamp) && !empty($endTimestamp)) {

                $startTime = date("Y-m-d H:i:s", strtotime($startTimestamp));
                $endTime = date("Y-m-d H:i:s", strtotime($endTimestamp));
                $select->where("$backersTableName.creation_date BETWEEN '$startTime' AND '$endTime'");
            }
            $select->order("$backersTableName.creation_date");
            // echo $select; die;
        } else {
            $select->from($projectTableName)
                    ->join($backersTableName, "$backersTableName.project_id = $projectTableName.project_id", array("$backersTableName.creation_date", 'sum(commission_value) as total_commission', 'sum(amount) as total_backed_amount', "MONTHNAME($backersTableName.creation_date) as month", "MONTH($backersTableName.creation_date) as month_no", "YEAR($backersTableName.creation_date) as year"))
                    ->where("$backersTableName.payment_status = 'active'")
                    ->group("$projectTableName.project_id");
            //IF REPORT IS MONTH WISE THAN GROUP THE DATA MONTHWISE
            if($values['report_type'] != 'summarised'){
                $select->group("MONTH($backersTableName.creation_date)");
            }

            if (isset($select_project) && $select_project == 'ongoing') {

                $select->where("$projectTableName.status = 'published'")
                        ->where("$projectTableName.approved = 1");
            }
            if (isset($select_project) && $select_project == 'failed') {

                $select->where("$projectTableName.status = 'failed'")
                        ->where("$backersTableName.payout_status = 'success'");
            }
            if (isset($select_project) && $select_project == 'successful') {

                $select->where("$projectTableName.state = 'successful'")
                        ->where("$backersTableName.payout_status = 'success'");
            }

            if (!empty($startTimestamp) && !empty($endTimestamp)) {

                $startTime = date("Y-m-d H:i:s", strtotime($startTimestamp));
                $endTime = date("Y-m-d H:i:s", strtotime($endTimestamp));

                $select->where("$backersTableName.creation_date BETWEEN '$startTime' AND '$endTime'");
            }
        } 
        return $this->fetchAll($select);
    }

    public function createDefaultProjects() {

        $db = Engine_Db_Table::getDefaultAdapter();
        $viewer = Engine_Api::_()->user()->getViewer();
        $ownerId = $viewer->getIdentity();
        $ownerType = $viewer->getType();
        $category_table = Engine_Api::_()->getItemTable('sitecrowdfunding_category');
        $category = $category_table->fetchRow(array('category_name=?' => 'Others'));
        $categoryId = 0;
        if ($category)
            $categoryId = $category->category_id;

        $projects_table = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
        $max_project_id = $projects_table->select()
                ->from($projects_table->info('name'), 'max(project_id) as max_project_id')
                ->query()
                ->fetchColumn();
        $ct = date('Y-m-d');
        $db->query("
                INSERT IGNORE INTO `engine4_sitecrowdfunding_projects`(`title`, `description`, `search`, `owner_type`, `owner_id`, `creation_date`, `modified_date`, `category_id`,`featured`,`sponsored`,`approved`,`goal_amount`,`duration_format`,`status`,`state`,`approved_date`,`package_id`,`is_gateway_configured`,`location`,`start_date`)
                VALUES
                ('Galaxy Art Studio','Galaxy Art Studios is a not-for-profit group looking to open affordable studios for disabled and low income artists. We will love and appreciate your support.',1,'$ownerType',$ownerId,'$ct','$ct',$categoryId,1,1,1,500,0,'active','published','$ct',1,1,'Airport Way, Luton, United Kingdom','$ct'),
                ('The Jim Henson Exhibition','A dynamic exhibition exploring the work of the creative genius behind the Muppets, and his unique contributions to popular culture.With a generous gift of hundreds of puppets and artifacts from the Henson family, Museum of the Moving Image in New York City plans to open a brand new, permanent exhibition dedicated to the work of legendary puppeteer and director Jim Henson. Over the course of a singular career, Henson created stories and characters for television and film that have delighted and inspired generations of fans around the world — from the glamorous Miss Piggy and the gentle Grover, to the mystical creatures of The Dark Crystal. The Jim Henson Exhibition will share the full story of Henson’s unique talent, and the deeply collaborative creative process behind his work.',1,'$ownerType',$ownerId,'$ct','$ct',$categoryId,1,1,1,500,0,'active','published','$ct',1,1,'New York, NY, United States','$ct'),
                ('The Aspiring Potter','I make handcrafted functional pottery including mugs, tumblers, bowls, vases, and miniatures, and need a kiln and electric wheel. I love making functional ware, especially mugs and tumblers. There is no substitute for the feeling of awe when drinking out of a mug that was literally made from the ground up. No two of my handmade pottery pieces are exactly alike, and that’s what makes them beautiful.',1,'$ownerType',$ownerId,'$ct','$ct',$categoryId,1,1,1,500,0,'active','published','$ct',1,1,'Magong City, Taiwan Province, Taiwan','$ct'),
                ('Classic Painting Collection','Classic Painting Collection is a stunning collection of a variety of amazing paintings that combines top quality photography and fine art. The series was created to showcase paintings of various talented artists and is produced using the finest materials in limited edition numbers. The Artist’s signature confirms that the finest materials have been used in every one of a kind, hand-produced Masterwork on Canvas edition.',1,'$ownerType',$ownerId,'$ct','$ct',$categoryId,1,1,1,500,0,'active','published','$ct',1,1,'Georgia, United States','$ct');
               ");

        $select = $this->select()->from($this->info('name'), '*');
        if ($max_project_id) {
            $select->where('project_id > (?)', $max_project_id);
        }
        $select->limit(4);
        $projects = $this->fetchAll($select);
        $duration = 50;
        $overviews = $this->getDefaultOverview();
        foreach ($projects as $k => $project) {
            $duration++;
            $db->query("INSERT IGNORE INTO `engine4_sitecrowdfunding_otherinfo` (`project_id`,`overview`) VALUES ($project->project_id,'$overviews[$k]');");
            //Save the project pic
            $imgpath = APPLICATION_PATH . "/application/modules/Sitecrowdfunding/externals/images/project_images/$k.jpg";
            @chmod($imgpath, 0777);
            $project->setPhoto($imgpath);
            $project->expiration_date = date('Y-m-d H:i:s', strtotime("+{$duration} days", strtotime(date('Y-m-d'))) - 1);
            $project->parent_type = 'user';
            $project->parent_id = $ownerId;
            $project->save();

            // CREATE AUTH STUFF HERE
            $values = array();
            //PRIVACY WORK
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $leaderList = $project->getLeaderList();
            $values['auth_view'] = 'everyone';
            $values['auth_comment'] = 'everyone';
            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            foreach ($roles as $i => $role) {
                if ($role === 'leader') {
                    $role = $leaderList;
                }
                $auth->setAllowed($project, $role, "view", ($i <= $viewMax));
                $auth->setAllowed($project, $role, "comment", ($i <= $commentMax));
            }
            $roles = array('leader', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            $values['auth_topic'] = "registered";
            $values['auth_post'] = "registered";
            $postMax = '';
            $topicMax = array_search($values['auth_topic'], $roles);
            $postMax = array_search($values['auth_post'], $roles);
            foreach ($roles as $i => $role) {
                if ($role === 'leader') {
                    $role = $leaderList;
                }
                $auth->setAllowed($project, $role, "topic", ($i <= $topicMax));
                if ($postMax)
                    $auth->setAllowed($project, $role, "post", ($i <= $postMax));
            }
            // Create some auth stuff for all leaders
            $auth->setAllowed($project, $leaderList, 'topic.edit', 1);
            $auth->setAllowed($project, $leaderList, 'edit', 1);
            $auth->setAllowed($project, $leaderList, 'delete', 1);
        }
    }


    public function getAllProjectLocationSelect(){

        $projectTableName = $this->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
        $locationTableName = $locationTable->info('name');

        // Get Current date
        $currentDate = date('Y-m-d H:i:s');

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($locationTableName)
            ->join($projectTableName, "$projectTableName.project_id = $locationTableName.project_id")
            ->where($projectTableName . '.state <> ?', 'draft')
            ->where($projectTableName . '.approved = ?', 1)
            ->where($projectTableName . '.start_date <= ?', $currentDate);

        return $this->fetchAll($select);

    }

    public function getDefaultOverview() {
        return array(
'<p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Galaxy Art</span><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #fbfbfa; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"> Studios is a not-for-profit group looking to open affordable studios for disabled and low income artists.</span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Hello! We are A4 Studios; a not-for-profit group set up by 4 creatives from different backgrounds. </span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We are making affordable studio space for creatives in the North West area of the U.K, with a main goal to provide spaces for artists with disabilities and those on low incomes. </span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We want to become the Arts disability hub of the North-West with the best contemporary gallery in the area, offering opportunities for emerging and early career artists. </span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">But we need your help! Affordable studio space with full disabled access is very hard to find, so with this in mind we have sourced a suitable building with fantastic transport links. We have drawn up a floor plan and developed a sustainable 5 year business plan.</span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Now we need your help to make this happen. this Kickstarter is for phase 1 of a 4 phase project that is being funded through private investment, grant assistance and donations, and you guys can play a vital role in making this project happen </span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Our building is of local historical interest and is in desperate need of renovation. Your donations will help us secure the building and start the necessary renovation works to restore the building and redevelop the internal space to create the disabled access studios and create the space needed for public use. </span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Our plans include larger than average studios, disability toilets and to ensure all entrances, corridors and studios are wheelchair accessible.</span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Your donations will put us well on our way creating 27 studios (21 of which are wheelchair accessible), 2 fully accessible galleries spaces and 1 large creative space which can be used for performances, large scale installations and events. </span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">This project will allow us to set up a rolling programme of exhibitions, a series of monthly workshops and to host open studio events throughout the year. </span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Our workshops would be catered for everyone in the community but we will also have art therapy workshops aimed at socially isolated individuals and those with disabilities to improve on health and well-being. We would also host platforms for emerging local talents to perform their craft to increase confidence and personal development. </span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">This type of facility is sorely needed; with studio closures happening across Manchester, an increase of students studying art, on top of this there are a very limited number of facilities that provide for the disabled. We will be one of the only venues in the North West that provide for the growing need of accessible space but we cannot do it without you! </span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We are hugely thankful for any help you can give to help us achieve our goal and you would be helping us bring a much needed service to both artists and the wider community.</span></p><h1 dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Rewards</span></h1><strong id="docs-internal-guid-e29f25ca-e6e8-53dc-b0d2-44a1bd3af334" style="font-weight: normal;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Every donation no matter how large or small will get a heartfelt "Thank You" from the team on our dedicated friends of A4 Studios webpage, as well as one of the rewards listed as a way of spreading the word for A4 Studios Ltd.</span></strong>',

            '<p dir="ltr"><span style="font-size: 10pt;">A dynamic exhibition exploring the work of the creative genius behind the Muppets, and his unique contributions to popular culture.</span></p>
<span style="font-size: 10pt;"><strong><strong><br></strong></strong></span>
<p dir="ltr"><span style="font-size: 10pt;">With a generous gift of hundreds of puppets and artifacts from the Henson family, Museum of the Moving Image in New York City plans to open a brand new, permanent exhibition dedicated to the work of legendary puppeteer and director Jim Henson. Over the course of a singular career, Henson created stories and characters for television and film that have delighted and inspired generations of fans around the world &mdash; from the glamorous Miss Piggy and the gentle Grover, to the mystical creatures of The Dark Crystal. The Jim Henson Exhibition will share the full story of Henson&rsquo;s unique talent, and the deeply collaborative creative process behind his work.</span></p>
<span style="font-size: 10pt;"><strong><strong><br></strong></strong></span>
<h1 dir="ltr"><span style="font-size: 10pt;">What will the exhibition include?</span></h1>
<p dir="ltr"><span style="font-size: 10pt;">The exhibition will feature more than 40 original puppets &mdash; including Kermit the Frog, Miss Piggy, Elmo, Cookie Monster, the Fraggles, and a Skeksis from The Dark Crystal &mdash; as well as puppet prototypes, original character sketches, rare behind-the-scenes footage and photographs, and interactive puppetry design and performance experiences.</span></p>
<p dir="ltr"><span style="font-size: 10pt;">Sections of the exhibition will be devoted to:</span></p>
<ul>
<li dir="ltr">
<p dir="ltr"><span style="font-size: 10pt;">Henson&rsquo;s early work for television and film in the 1950s and 60s, in which his unbridled imagination, wit, and capacity for creative innovation were clearly established</span></p>
</li>
<li dir="ltr">
<p dir="ltr"><span style="font-size: 10pt;">Sesame Street, where his remarkably expressive puppets have delighted children for generations</span></p>
</li>
<li dir="ltr">
<p dir="ltr"><span style="font-size: 10pt;">The Muppet Show and Muppet feature films, which have had an indelible impact on popular culture</span></p>
</li>
<li dir="ltr">
<p dir="ltr"><span style="font-size: 10pt;">and the imaginary worlds of Fraggle Rock, The Dark Crystal, and Labyrinth</span></p>
</li>
</ul>
<h1 dir="ltr"><span style="font-size: 10pt;">Preserving the puppets for future generations</span></h1>
<p dir="ltr"><span style="font-size: 10pt;">The Jim Henson Collection at Museum of the Moving Image includes approximately 175 historic puppets, such as Kermit the Frog, Miss Piggy, Big Bird, Ernie, Bert, Elmo, the Fraggles, Jen and Kira from The Dark Crystal, and many, many more. The restoration of this important collection is an ongoing collaboration between the Museum and Jim Henson&rsquo;s Creature Shop, with input from fine art conservators. The Museum is honored to be working with the designers and builders at the legendary Creature Shop to restore and preserve the puppets for future generations.</span></p>
<p dir="ltr"><span style="font-size: 10pt;">The lasting, universal appeal of Henson&rsquo;s work is unprecedented. People of all ages continue to be delighted and inspired by Jim Henson&rsquo;s stories and characters. We&rsquo;re excited to use Kickstarter to connect with Henson fans around the world, and bring this global community together to support a permanent exhibition honoring his creative legacy.</span></p>
<h1 dir="ltr"><span style="font-size: 10pt;">What will your contributions support?</span></h1>
<p dir="ltr"><span style="font-size: 10pt;">The City of New York awarded the Museum a generous grant that has enabled us to build the gallery in which this important and inspiring exhibition will be housed. Construction of the gallery space is complete and plans for the exhibition have been finalized.</span></p>
<p dir="ltr"><span style="font-size: 10pt;">But we need your support to bring the rest of this incredible exhibition to life, in time for the 2017 school year. Your pledges will help us complete the important work of restoring the puppets in the Museum&rsquo;s Henson Collection and fabricating and installing the display cases for the puppets and other artifacts.</span></p>
<span style="font-size: 10pt;"><strong id="docs-internal-guid-e29f25ca-b448-41fe-a748-cbad173a402d">Your contributions will ensure that hundreds of thousands of visitors from around the world will be inspired by the creative process behind Jim Henson&rsquo;s work, and be able to get up close and personal with such beloved puppet characters as Kermit the Frog, Miss Piggy, Elmo, Big Bird, the Fraggles, and many more.</strong></span>', 

'<p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 10pt;"><span style="font-family: Arial; color: #353535; background-color: #ffffff; font-weight: bold; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Let me introduce myself:</span><span style="font-family: Arial; color: #353535; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"> I am an aspiring potter living in southwest taiwan working to fuel my future and continue my education in hand-built ceramics. In the past year I have been working hard to gain the supplies needed to launch my own home studio. This is where you come in. I need your funding help for a critical piece of equipment: the kiln. The photos below show a sampling of my recent work. The greenware (unfired clay) items are awaiting time in a kiln that I have occasional access to, hence the need for this Kickstarter project.</span></span></p>
<p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 10pt; font-family: Arial; color: #353535; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">I love making functional ware, especially mugs and tumblers. There is no substitute for the feeling of awe when drinking out of a mug that was literally made from the ground up. No two of my handmade pottery pieces are exactly alike, and that&rsquo;s what makes them beautiful.</span></p>
<span style="font-size: 10pt;"><strong id="docs-internal-guid-e29f25ca-e6fa-2093-06bc-f9df1002e528" style="font-weight: normal;"><span style="font-family: Arial; color: #353535; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">By backing this project, you will help me raise funds of </span><span style="font-family: Arial; color: #353535; background-color: #ffffff; font-weight: bold; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">$1,850</span><span style="font-family: Arial; color: #353535; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"> for a kiln in my own home studio. Even if no funds are raised, I will continue to work toward living out my dream as a potter.<br><br><strong id="docs-internal-guid-f8baec8e-e6fb-b547-1834-60b09c6b48ce" style="font-weight: normal;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"><img style="border: none; transform: rotate(0.00rad); -webkit-transform: rotate(0.00rad);" src="https://lh5.googleusercontent.com/1s7CtSk4gBhRNksj5rDE04mprvn2XqGO7qW9xQMCGe9WJA3SyVaAQX3a6dS-Fdg9cA1PLlgBMtMZGRDji1eGEelo5bG5iItb0QT-eTrpvQjp_uZXffpQqr4TdcuZeBECu4-4qDh4" alt="767cdd480d84f0ae9f35d1b4661ae94e.jpg" width="624" height="832"></span></strong><br><br><strong id="docs-internal-guid-f8baec8e-e6fb-f545-3d0e-354132ecd9d6" style="font-weight: normal;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"><img style="border: none; transform: rotate(0.00rad); -webkit-transform: rotate(0.00rad);" src="https://lh5.googleusercontent.com/Z3OAwDokXMEGQSpwGYYBhGR6zxQ4pR3AmC0FTsvLXBtjBNJE9HWtA1ZppwT_Jvt3uZhy3L3lOjvuZ_tW9dp4QXELdHpl5XEHqONJWjv_nVUVUaIoHpNZc0nsvi0bOTYTx90uRiPU" alt="abaea5_237ca9f654b042f1b6a9a970c5461876-mv2_d_2160_2880_s_2.jpg" width="624" height="832"></span></strong><br></span></strong></span>',
'<p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 10pt;"><span style="font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Classic Painting Collection</span><span style="font-family: Arial; color: #353535; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"> is a stunning collection of a variety of amazing paintings that combines top quality photography and fine art. The series was created to showcase paintings of various talented artists and is produced using the finest materials in limited edition numbers.</span></span></p><p dir="ltr" style="line-height: 1.2; margin-top: 0pt; margin-bottom: 15pt;"><span style="font-size: 10pt; font-family: Arial; color: #353535; background-color: #ffffff; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">The Artist&rsquo;s signature confirms that the finest materials have been used in every one of a kind, hand-produced Masterwork on Canvas edition. A Masterwork consists of hand- painted enhancements, using oil paint to increase brightness and intensify color. Once dry, the artist applies multiple coats to the canvas to create texture, depth and brush strokes. The result of this technique intensifies light in the print and creates a sense of movement throughout the masterworks on canvas pieces.<br><br><strong id="docs-internal-guid-f8baec8e-ddaa-2ef5-c509-83a20b964e2a" style="font-weight: normal;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"><img style="border: none; transform: rotate(0.00rad); -webkit-transform: rotate(0.00rad);" src="https://lh5.googleusercontent.com/iCZhtPOogt5t9AKUBh3pMn5qrK34TXmrL5HHuto8cD0qFAUD0I6nPdVpOPlPAlQVq7WJYU70IJvTNKhtpOVhH_ExULQZ-TiTWnKDEZlSmDwlDhvjqnudnbAXNAJsj3S8LeWu7Lud" alt="16a6443782617770afc2fe574da23cc7.jpg" width="624" height="500"></span></strong><br><br><br><strong id="docs-internal-guid-f8baec8e-ddaa-6e61-7b0e-cff28e4ce379" style="font-weight: normal;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;"><img style="border: none; transform: rotate(0.00rad); -webkit-transform: rotate(0.00rad);" src="https://lh6.googleusercontent.com/vpoEnLs6QxGUp8Sm2nTCT1ChggPFjMVGo1ZWoy-Ha6rPCaYRPKmjg-LyOMvwWSaiOMJ6ZSFsw3VZpa8h8p66MVQeKnSpu1Qxq7HlxUctoRVJJ0QN7AOHlknp9Ei79xqW5H08ksWr" alt="97e81062ba932f6b5bb3fc5324660eda.jpg" width="602" height="743"></span></strong><br><br><br></span></p>'
        );
    }

}


<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Api_Core extends Core_Api_Abstract {

    public function deleteSuggestion($viewer_id, $entity, $entity_id, $entity_type, $notifications_type) {
        $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
        if (!empty($is_moduleEnabled)) {
            $suggestion_table = Engine_Api::_()->getItemTable('suggestion');
            $suggestion_table_name = $suggestion_table->info('name');
            $suggestion_select = $suggestion_table->select()
                    ->from($suggestion_table_name, array('suggestion_id'))
                    ->where('owner_id = ?', $viewer_id)
                    ->where('entity = ?', $entity)
                    ->where('entity_id = ?', $entity_id);
            $suggestion_array = $suggestion_select->query()->fetchAll();
            if (!empty($suggestion_array)) {
                foreach ($suggestion_array as $sugg_id) {
                    Engine_Api::_()->getItem('suggestion', $sugg_id['suggestion_id'])->delete();
                    Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_id = ?' => $entity_id, 'object_type = ?' => $entity_type, 'type = ?' => $notifications_type));
                }
            }
        }
    }

    public function findDays($date1, $date2 = null) {

        $date1 = date('Y-m-d', strtotime($date1));
        if (empty($date2)) {
            $date2 = date('Y-m-d');
        } else {
            $date2 = date('Y-m-d', strtotime($date2));
        }
        if (strtotime($date1) < strtotime($date2)) {
            return -1;
        }
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $difference = $datetime1->diff($datetime2);
        if ($difference)
            return ($difference->days) + 1;
        return 0;
    }

    public function findDays2($date1, $date2 = null) {

        $date1 = date('Y-m-d', strtotime($date1));
        if (empty($date2)) {
            $date2 = date('Y-m-d');
        } else {
            $date2 = date('Y-m-d', strtotime($date2));
        }
        if (strtotime($date1) < strtotime($date2)) {
            return -1;
        }
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $difference = $datetime1->diff($datetime2);
        if ($difference)
            return ($difference->days);
        return 0;
    }

    /**
     * Video base network enable
     *
     * @return bool
     */
    public function projectBaseNetworkEnable() {

        return (bool) ( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.networks.type', 0) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.default.show', 0)));
    }

    /**
     * Get Currency Symbol
     *
     * @return string
     */
    public function getCurrencySymbol() {

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencySymbol = Zend_Locale_Data::getContent($localeObject, 'currencysymbol', $currencyCode);
        return $currencySymbol;
    }

    public function getPriceWithCurrencyAdmin($price) {
        if (empty($price)) {
            return $price;
        }

        $defaultParams = array();
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
            $defaultParams['locale'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
        }

        $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $defaultParams['precision'] = 2;
        $price = (float) $price;
        $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, $defaultParams);
        return $priceStr;
    }

    //Function to return Currency Conversion rate
    public function getPriceWithCurrency($price, $priceOnly = 0, $search = 0) {

        if (empty($price)) {
            return $price;
        }
        if (Engine_Api::_()->hasModuleBootstrap('sitemulticurrency') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemulticurrency') && Engine_Api::_()->getDbtable('modules', 'sitemulticurrency')->isModuleEnable('sitecrowdfunding')) {

            $priceStr = Engine_Api::_()->sitemulticurrency()->convertCurrencyRate($price, $priceOnly, $search);
        } else {
            $defaultParams = array();
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if (empty($viewer_id)) {
                $defaultParams['locale'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
            }
            $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $defaultParams['precision'] = 2;
            $price = (float) $price;
            if (!empty($priceOnly))
                return $price;
            $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, $defaultParams);
        }

        return $priceStr;
    }

    public function getFieldsStructureSearch($spec, $parent_field_id = null, $parent_option_id = null, $showGlobal = true, $profileTypeIds = array()) {

        $fieldsApi = Engine_Api::_()->getApi('core', 'fields');

        $type = $fieldsApi->getFieldType($spec);

        $structure = array();

        foreach ($fieldsApi->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map) {
// Skip maps that don't match parent_option_id (if provided)
            if (null !== $parent_option_id && $map->option_id != $parent_option_id) {
                continue;
            }

//FETCHING THE FIELDS WHICH BELONGS TO SOME SPECIFIC LISTNIG TYPE
            if ($parent_field_id == 1 && !empty($profileTypeIds) && !in_array($map->option_id, $profileTypeIds)) {
                continue;
            }

// Get child field
            $field = $fieldsApi->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
            if (empty($field)) {
                continue;
            }

// Add to structure
            if ($field->search) {
                $structure[$map->getKey()] = $map;
            }

// Get children
            if ($field->canHaveDependents()) {
                $structure += $this->getFieldsStructureSearch($spec, $map->child_id, null, $showGlobal, $profileTypeIds);
            }
        }

        return $structure;
    }

    public function sendMailCustom($type, $projectId) {

        if (empty($type) || empty($projectId)) {
            return;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $projectId);
        $mail_template = null;
        $package = $project->getPackage();
        if (!empty($project)) {

            $owner = Engine_Api::_()->user()->getUser($project->owner_id);
            switch ($type) {
//                case "APPROVAL_PENDING":
//                    $mail_template = 'sitecrowdfunding_approval_pending';
//                    break;
//                case "ACTIVE":
//                    $mail_template = 'sitecrowdfunding_active';
//                    break;
//                case "PENDING":
//                    $mail_template = 'sitecrowdfunding_pending';
//                    break;
                case "APPROVED":
                    $mail_template = 'SITECROWDFUNDING_APPROVED';
                    break;
                case "DISAPPROVED":
                    $mail_template = 'SITECROWDFUNDING_DISAPPROVED';
                    break;
                case "FUNDING_APPROVED":
                    $mail_template = 'SITECROWDFUNDING_FUNDING_APPROVED';
                    break;
                case "FUNDING_DISAPPROVED":
                    $mail_template = 'SITECROWDFUNDING_FUNDING_DISAPPROVED';
                    break;
//                case "CANCELLED":
//                    $mail_template = 'sitecrowdfunding_cancelled';
//                    break;
//                case "DISAPPROVED":
//                    $mail_template = 'sitecrowdfunding_disapproved';
//                    break;
//                case "RECURRENCE":
//                    $mail_template = 'sitecrowdfunding_recurrence';
//                    break;
//                case "EXPIRED":
//                    $mail_template = ($package && $package->isFree()) ? 'sitecrowdfunding_expired' : 'sitecrowdfunding_renew';
//                    break;
//                case "OVERDUE":
//                    $mail_template = 'sitecrowdfunding_overdue';
//                    break;
//                case "DECLINED":
//                    $mail_template = 'sitecrowdfunding_declined';
//                    break;
            }

            $httpVar = _ENGINE_SSL ? 'https://' : 'http://';
            $project_baseurl = $httpVar . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('project_id' => $project->project_id, 'slug' => $project->getSlug()), "sitecrowdfunding_entry_view", true);

            //MAKING PROJECT TITLE LINK
            $project_title_link = '<a href="' . $project_baseurl . '"  >' . $project->title . ' </a>';


            /***
             *
             * send notification and email to all project admins and project owner
             *
             ***/
            $list = $project->getLeaderList();
            $list_id = $list['list_id'];

            $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
            $listItemTableName = $listItemTable->info('name');

            $userTable = Engine_Api::_()->getDbtable('users', 'user');
            $userTableName = $userTable->info('name');

            $selectLeaders = $listItemTable->select()
                ->from($listItemTableName, array('child_id'))
                ->where("list_id = ?", $list_id)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
            $selectLeaders[] = $project->owner_id;

            $selectUsers = $userTable->select()
                ->from($userTableName)
                ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                ->order('displayname ASC');

            $adminMembers = $userTable->fetchAll($selectUsers);

            foreach($adminMembers as $adminMember){
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, $mail_template, array(
                    'member_name' => $adminMember->getTitle(),
                    'project_name' => ucfirst($project->getTitle()),
                    'project_description' => ucfirst($project->getDescription()),
                    'project_link' => $project_title_link,
                    'queue' => false,
                ));
            }
        }
    }

    /**
     * Send emails for particular project
     * @params $type : which mail send
     * $params $projectId : Id of project
     * */
    public function sendMail($type, $projectId) {
// Need to work here
        return;
        if (empty($type) || empty($projectId)) {
            return;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $projectId);
        $mail_template = null;
        $package = $project->getPackage();
        if (!empty($project)) {

            $owner = Engine_Api::_()->user()->getUser($project->owner_id);
            switch ($type) {
                case "APPROVAL_PENDING":
                    $mail_template = 'sitecrowdfunding_approval_pending';
                    break;
                case "ACTIVE":
                    $mail_template = 'sitecrowdfunding_active';
                    break;
                case "PENDING":
                    $mail_template = 'sitecrowdfunding_pending';
                    break;
                case "APPROVED":
                    $mail_template = 'sitecrowdfunding_approved';
                    break;
                case "CANCELLED":
                    $mail_template = 'sitecrowdfunding_cancelled';
                    break;
                case "DISAPPROVED":
                    $mail_template = 'sitecrowdfunding_disapproved';
                    break;
                case "RECURRENCE":
                    $mail_template = 'sitecrowdfunding_recurrence';
                    break;
                case "EXPIRED":
                    $mail_template = ($package && $package->isFree()) ? 'sitecrowdfunding_expired' : 'sitecrowdfunding_renew';
                    break;
                case "OVERDUE":
                    $mail_template = 'sitecrowdfunding_overdue';
                    break;
                case "DECLINED":
                    $mail_template = 'sitecrowdfunding_declined';
                    break;
            }

            $httpVar = _ENGINE_SSL ? 'https://' : 'http://';
            $project_baseurl = $httpVar . $_SERVER['HTTP_HOST'] .
                    Zend_Controller_Front::getInstance()->getRouter()->assemble(array('project_id' => $project->project_id, 'slug' => $project->getSlug()), "sitecrowdfunding_entry_view", true);

//MAKING PROJECT TITLE LINK
            $project_title_link = '<a href="' . $project_baseurl . '"  >' . $project->title . ' </a>';

            /***
             *
             * send notification and email to all project admins and project owner
             *
             ***/
            $list = $project->getLeaderList();
            $list_id = $list['list_id'];

            $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
            $listItemTableName = $listItemTable->info('name');

            $userTable = Engine_Api::_()->getDbtable('users', 'user');
            $userTableName = $userTable->info('name');

            $selectLeaders = $listItemTable->select()
                ->from($listItemTableName, array('child_id'))
                ->where("list_id = ?", $list_id)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
            $selectLeaders[] = $project->owner_id;

            $selectUsers = $userTable->select()
                ->from($userTableName)
                ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                ->order('displayname ASC');

            $adminMembers = $userTable->fetchAll($selectUsers);

            foreach($adminMembers as $adminMember){
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, $mail_template, array(
                    'member_name' => $adminMember->getTitle(),
                    'project_name' => $project->title,
                    'project_link' => $project_title_link,
                    'queue' => false,
                ));
            }
        }
    }

    public function sendEmailToBackers($project, $emailType = null) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $params = array();
        $params['email_backers'] = true;
        $params['project_id'] = $project->project_id;
        $backers = $backersTable->getAllBackers($params);
        $result = array();
        foreach ($backers as $backer)
            $result[] = $backer->user_id;
        $result = array_unique($result);

        //GET PAGE TITLE AND PROJECT TITLE.
        $project_name = $project->title;
        $host = $_SERVER['HTTP_HOST'];
        $project_link = $view->htmlLink($host . $project->getHref(), $project->title);
        $goal_amount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($project->goal_amount);

        foreach ($result as $backerId) {
            $user = Engine_Api::_()->user()->getUser($backerId);
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, "$emailType", array(
                'project_name' => $project_name,
                'member_name' => $user->getTitle(),
                'goal_amount' => $goal_amount,
                'project_link' => $project_link
            ));
        }
    }

    public function getActivtyFeedType($project, $type, $user = null) {
        return $type;
    }

    /*
     * Checking for user allowed to upload the video 
     * 
     */

    public function allowVideo($project, $viewer) {
        $allowed_upload_videoEnable = $this->enableVideoPlugin();
        if (empty($allowed_upload_videoEnable))
            return false;

        $allowed_upload_video = true;
        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            if (Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "video")) {
                $videoCount = Engine_Api::_()->getDbTable('packages', 'sitecrowdfunding')->getPackageOption($project->package_id, 'video_count');
                $videoTable = Engine_Api::_()->getItemTable('video');
                $counter = $videoTable->select()
                        ->from($videoTable->info('name'), 'count(*)')
                        ->where('parent_type = ?', $project->getType())
                        ->where('parent_id = ?', $project->project_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                if (empty($counter)) {
                    $counter = 0;
                }
                if (empty($videoCount))
                    $allowed_upload_video = true;
                elseif ($videoCount > $counter)
                    $allowed_upload_video = true;
                else
                    $allowed_upload_video = false;
            } else
                $allowed_upload_video = false;
        }
        if (empty($allowed_upload_video))
            return false;

        return true;
    }

    /**
     * Allow contect for particular package
     * @params $type : which check
     * $params $package_id : Id of project
     * $params $params : array some extra
     * */
    public function allowPackageContent($package_id, $type = null) {

        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable())
            return;

        $flage = false;
        $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $package_id);

        if (!empty($package) && isset($package->$type) && !empty($package->$type)) {
            $flage = true;
        }

        return $flage;
    }

    /**
     * Page base network enable
     *
     * @return bool
     */
    public function listBaseNetworkEnable() {

        $settings = Engine_Api::_()->getApi('settings', 'core');

        return (bool) ( $settings->getSetting('sitecrowdfunding.networks.type', 0) && ($settings->getSetting('sitecrowdfunding.network', 0) || $settings->getSetting('sitecrowdfunding.default.show', 0)));
    }

//CHECK VIDEO PLUGIN ENABLE / DISABLE
    public function enableVideoPlugin() {

        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        if ($sitevideoEnabled) {
            return true;
        } else {
            return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');
        }
    }

    /**
     * Check package is enable or not for site
     * @return bool
     */
    public function hasPackageEnable() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $hasPackageEnable = $settings->getSetting('sitecrowdfunding.package.setting', 0);
        return $hasPackageEnable;
    }

    public function categoriesPageCreate($categoryIds = array()) {

//GET DATABASE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        foreach ($categoryIds as $categoryId) {

            $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $categoryId);

            if ($category->cat_dependency || $category->subcat_dependency || empty($category)) {
                continue;
            }

            $categoryName = $category->getTitle(true);

            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitecrowdfunding_index_categories-home_category_" . $categoryId)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {

                $containerCount = 0;
                $widgetCount = 0;

//CREATE PAGE
                $db->insert('engine4_core_pages', array(
                    'name' => "sitecrowdfunding_index_categories-home_category_" . $categoryId,
                    'displayname' => "Crowdfunding - Category - " . $categoryName,
                    'title' => "Crowdfunding - " . $categoryName . " Home",
                    'description' => 'This is the Crowdfunding - ' . $categoryName . ' home page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

//TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $top_container_id = $db->lastInsertId();

//MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $main_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => $containerCount++,
                ));
                $top_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $main_right_id = $db->lastInsertId();

//LEFT CONTAINER

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecrowdfunding.navigation',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"0","name":"sitecrowdfunding.navigation"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecrowdfunding.project-categorybanner',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","logo":"public\/admin\/website-background-grey1.jpg","height":"555","categoryHeight":"400","fullWidth":"1","showExplore":"1","titleTruncation":"36","taglineTruncation":"100","nomobile":"","name":"sitecrowdfunding.project-categorybanner"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecrowdfunding.project-categories-grid-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"subCategoriesCount":"5","showProjectCount":"0","columnWidth":"225","columnHeight":"200","nomobile":"0","name":"sitecrowdfunding.project-categories-grid-view"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecrowdfunding.project-carousel',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"category" : "' . $categoryId . '"}',
                    'params' => '{"title":"' . $categoryName . '","category_id":"' . $categoryId . '","subcategory_id":"0","hidden_project_category_id":"' . $categoryId . '","hidden_project_subcategory_id":"0","hidden_project_subsubcategory_id":"0","projectType":null,"projectOption":["title","owner","creationDate","backer","like","favourite","comment","endDate","featured","sponsored","facebook","twitter","linkedin","googleplus"],"showProject":"","popularType":"random","selectProjects":"all","daysFilter":"20","backedPercentFilter":"50","showPagination":"1","projectWidth":"285","projectHeight":"250","showLink":"1","rowLimit":"3","itemCount":"12","interval":"3500","titleTruncation":"20","truncationLocation":"35","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitecrowdfunding.project-carousel"}'
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecrowdfunding.project-categories-navigation',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"orderBy":"category_name","viewDisplayHR":"0","title":"Categories","nomobile":"0","name":"sitecrowdfunding.project-categories-navigation"}',
                ));
            } else {

                $PagesTable = Engine_Api::_()->getDbTable('pages', 'core');
                $PagesTable->update(array(
                    'displayname' => "Crowdfunding - Category - " . $categoryName,
                    'title' => "Crowdfunding - " . $categoryName . " Home",
                    'description' => 'This is the Crowdfunding - ' . $categoryName . ' home page.',
                        ), array(
                    'name =?' => "sitecrowdfunding_index_categories-home_category_" . $categoryId,
                ));
            }
        }
    }

    /**
     * Plugin which return the error, if Siteadmin not using correct version for the plugin.
     *
     */
    public function isModulesSupport($modName = null) {
        if (empty($modName)) {
            $modArray = array(
                'sitepage' => '4.8.8p1',
                'sitebusiness' => '4.8.8p1',
                'sitegroup' => '4.8.8p1',
                'communityad' => '4.7.1',
                'communityadsponsored' => '4.7.1',
                'suggestion' => '4.7.1',
                'advancedactivity' => '4.7.1',
                'sitevideoview' => '4.7.1',
                'facebookse' => '4.7.1',
                'facebooksefeed' => '4.7.1',
                'sitetagcheckin' => '4.7.1',
                'sitecontentcoverphoto' => '4.7.1',
                'sitelike' => '4.7.1',
                'sitemailtemplates' => '4.7.1',
                'sitereview' => '4.7.1p2',
                'sitereviewlistingtype' => '4.7.1p2'
            );
        } else {
            $modArray[$modName['modName']] = $modName['version'];
        }
        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
            if (!empty($isModEnabled)) {
                $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
                $isModSupport = $this->checkVersion($getModVersion->version, $value);
                if (empty($isModSupport)) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }
        return $finalModules;
    }

    function checkVersion($databaseVersion, $checkDependancyVersion) {
        if (strcasecmp($databaseVersion, $checkDependancyVersion) == 0)
            return -1;
        $databaseVersionArr = explode(".", $databaseVersion);
        $checkDependancyVersionArr = explode('.', $checkDependancyVersion);
        $fValueCount = $count = count($databaseVersionArr);
        $sValueCount = count($checkDependancyVersionArr);
        if ($fValueCount > $sValueCount)
            $count = $sValueCount;
        for ($i = 0; $i < $count; $i++) {
            $fValue = $databaseVersionArr[$i];
            $sValue = $checkDependancyVersionArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                $result = $this->compareValues($fValue, $sValue);
                if ($result == -1) {
                    if (($i + 1) == $count) {
                        return $this->compareValues($fValueCount, $sValueCount);
                    } else
                        continue;
                }
                return $result;
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);
                $result = $this->compareValues($fsArr[0], $sValue);
                return $result == -1 ? 1 : $result;
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);
                $result = $this->compareValues($fValue, $ssArr[0]);
                return $result == -1 ? 0 : $result;
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                $result = $this->compareValues($fsArr[0], $ssArr[0]);
                if ($result != -1)
                    return $result;
                $result = $this->compareValues($fsArr[1], $ssArr[1]);
                return $result;
            }
        }
    }

    public function compareValues($firstVal, $secondVal) {
        $num = $firstVal - $secondVal;
        return ($num > 0) ? 1 : ($num < 0 ? 0 : -1);
    }

    /**
     * Set Meta Titles
     *
     * @param array $params
     */
    public function setMetaTitles($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $titles = $siteinfo['title'];

        if (isset($params['subcategoryname']) && !empty($params['subcategoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['subcategoryname'];
        }

        if (isset($params['categoryname']) && !empty($params['categoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['categoryname'];
        }

        if (isset($params['default_title'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['default_title'];
        }
        if (isset($params['dashboard'])) {
            if (isset($params['project_type_title'])) {
                if (!empty($titles))
                    $titles .= ' - ';
                $titles .= $params['project_type_title'];
            }

            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['dashboard'];
        }
        $siteinfo['title'] = $titles;
        $view->layout()->siteinfo = $siteinfo;
    }

    /**
     * Set Meta Titles
     *
     * @param array $params
     */
    public function setMetaDescriptionsBrowse($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $descriptions = '';
        if (isset($params['description'])) {
            if (!empty($descriptions))
                $descriptions .= ' - ';
            $descriptions .= $params['description'];
        }

        $siteinfo['description'] = $descriptions;
        $view->layout()->siteinfo = $siteinfo;
    }

    /**
     * Set Meta Keywords
     *
     * @param array $params
     */
    public function setMetaKeywords($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $keywords = "";

        if (isset($params['subcategoryname_keywords']) && !empty($params['subcategoryname_keywords'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['subcategoryname_keywords'];
        }

        if (isset($params['categoryname_keywords']) && !empty($params['categoryname_keywords'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['categoryname_keywords'];
        }

        if (isset($params['location']) && !empty($params['location'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['location'];
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['tag'];
        }

        if (isset($params['search'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['search'];
        }

        if (isset($params['keywords'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['keywords'];
        }

        if (isset($params['project_type_title'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['project_type_title'];
        }

        $siteinfo['keywords'] = $keywords;
        $view->layout()->siteinfo = $siteinfo;
    }

//RETURNS DATE OR TIME DEPEND ON THE $DATETIME PARAMTER DATABASE TO CURRENT USER
    public function dbToUserDateTime($dateparams = array(), $dateTime = 'date') {

        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        if ($viewer->getIdentity()) {
            $timezone = $viewer->timezone;
        }
        if (isset($dateparams['starttime']))
            $dateparams['starttime'] = strtotime($dateparams['starttime']);

        $oldTz = date_default_timezone_get();
        date_default_timezone_set($timezone);
        if (isset($dateparams['starttime']))
            $dateparams['starttime'] = date("Y-m-d H:i:s", $dateparams['starttime']);
        date_default_timezone_set($oldTz);
        if ($dateTime == 'time') {
            isset($dateparams['starttime']) ? $dateparams['starttime'] = strtotime($dateparams['starttime']) : '';
        }
        return $dateparams;
    }

    /**
     * Check location is enable
     *
     * @param array $params
     * @return int $check
     */
    public function enableLocation() {

        return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);
    }

    /**
     * Check here that show payment link or not
     * $params $projectId : Id of project
     * @return bool $showLink
     * */
    public function canShowPaymentLink($project_id) {

        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable())
            return;

        $showLink = true;
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (!empty($project)) {
            $package = $project->getPackage();
            if ($package->isFree()) {
                return (bool) false;
            }

            if ($project->status != "initial" && $project->status != "overdue") {
                return (bool) false;
            }

            if (($package->isOneTime()) && !$package->hasDuration() && !empty($project->approved)) {
                return false;
            }
        } else {
            $showLink = false;
        }
        return (bool) $showLink;
    }

    /**
     * Check here that show renew link or not
     * $params $project_id : Id of project
     * @return bool $showLink
     * */
    public function canShowRenewLink($project_id) {
        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable())
            return;
        $showLink = false;
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (!empty($project)) {
            $package = $project->getPackage();

            if (!$package->isOneTime() || $package->isFree() || (!empty($package->level_id) && !in_array($project->getOwner()->level_id, explode(",", $package->level_id)))) {
                return (bool) false;
            }
            if ($package->renew) {
                if (!empty($project->funding_end_date) && $project->funding_status != "initial" && $project->funding_status != "overdue") {
                    $diff_days = round((strtotime($project->funding_end_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                    if ($diff_days <= $package->renew_before || $project->funding_end_date <= date('Y-m-d H:i:s')) {
                        return (bool) true;
                    }
                }
            }
        }
        return (bool) $showLink;
    }

    /**
     * Check here that show cancel link or not
     * $params $projectId : Id of project
     * @return bool $showLink
     * */
    public function canShowCancelLink($project_id) {

        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable())
            return;

        $showLink = false;
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (!empty($project)) {
            $package = $project->getPackage();

            if (!$package->isFree() && $project->status == "active" && !$package->isOneTime() && !empty($project->approved)) {
                return (bool) true;
            }
        }

        return (bool) $showLink;
    }

    /**
     * Convert Decoded String into Encoded
     * @param string $string : decodeed string
     * @return string
     */
    public function getDecodeToEncode($string = null) {
        $encodeString = '';
        $string = (string) $string;
        if (!empty($string)) {
            $startIndex = 11;
            $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");

            $time = time();
            $timeLn = Engine_String::strlen($time);
            $last2DigtTime = substr($time, $timeLn - 2, 2);
            $sI1 = (int) ($last2DigtTime / 10);
            $sI2 = $last2DigtTime % 10;
            $Index = $sI1 + $sI2;

            $codeString = $CodeArray[$Index];
            $startIndex+=$Index % 10;
            $lenght = Engine_String::strlen($string);
            for ($i = 0; $i < $lenght; $i++) {
                $code = md5(uniqid(rand(), true));
                $encodeString.= substr($code, 0, $startIndex);
                $encodeString.=$string{$i};
                $startIndex++;
            }
            $code = md5(uniqid(rand(), true));
            $appendEnd = substr($code, 5, $startIndex);
            $prepandStart = substr($code, 20, 10);
            $encodeString = $prepandStart . $codeString . $encodeString . $appendEnd;
        }

        return $encodeString;
    }

    /**
     * Convert Encoded String into Decoded
     * @param string $string : encoded string
     * @return string
     */
    public function getEncodeToDecode($string) {
        $decodeString = '';

        if (!empty($string)) {
            $startIndex = 11;
            $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");
            $string = substr($string, 10, (Engine_String::strlen($string) - 10));
            $codeString = substr($string, 0, 10);

            $Index = array_search($codeString, $CodeArray);
            $string = substr($string, 10, Engine_String::strlen($string) - 10);
            $startIndex+=$Index % 10;

            $string = substr($string, 0, (Engine_String::strlen($string) - $startIndex));

            $lenght = Engine_String::strlen($string);
            $j = 1;
            for ($i = $startIndex; $i < $lenght;
            ) {
                $j++;
                $decodeString.= $string{$i};
                $i = $i + $startIndex + $j;
            }
        }
        return $decodeString;
    }

    /**
     * Get A Commission
     * @param int $project_id
     * @return array
     */
    public function getCommission($project_id) {
        $projectObj = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $commission = array();
        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $packageObj = Engine_Api::_()->getItem('sitecrowdfunding_package', $projectObj->package_id);
            if (!empty($packageObj->commission_settings)) {
                $commissionSettings = @unserialize($packageObj->commission_settings);
                $commission[] = $commissionSettings['commission_handling'];
                if (empty($commissionSettings['commission_handling'])) {
                    $commission[] = $commissionSettings['commission_fee'];
                } else {
                    $commission[] = $commissionSettings['commission_rate'];
                }
            } else {
                $commission[] = 1;
                $commission[] = 1;
            }
        } else {
            $user = $projectObj->getOwner();
            $commissionHandlingType = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitecrowdfunding_project', "commission_handling");
            if ($commissionHandlingType != 0 && $commissionHandlingType != 1) {
                $commission[] = 1;
                $commission[] = 1;
            } else {
                $commission[] = $commissionHandlingType;
                if (empty($commissionHandlingType)) {
                    $commissionRateValue = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitecrowdfunding_project', "commission_fee");
                } else {
                    $commissionRateValue = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitecrowdfunding_project', "commission_rate");
                }

                // Naaziya: 28th jan 2020
                if(empty($commissionRateValue)){
                    $commission[] = 1;
                }

            }
        }

        // Naaziya: 7th July 2020: set commission=0%
        $commission = array();
        $commission[] = 0;
        $commission[] = 0;
        return $commission;
    }

    public function getProjectVideos($project) {
        $videoTable = Engine_Api::_()->getDbtable('videos', 'video');
        $videoTableName = $videoTable->info('name');

//MAKE QUERY
        $select = $videoTable->select()
                ->from($videoTableName)
                ->where('parent_type = ?', $project->getType())
                ->where('parent_id = ?', $project->project_id);

        $rows = $videoTable->fetchAll($select);
        return empty($rows) ? array() : $rows;
    }

    public function showSelectedProjectBrowseBy($content_id) {

//GET CORE CONTENT TABLE
        $coreContentTable = Engine_Api::_()->getDbTable('content', 'core');
        $coreContentTableName = $coreContentTable->info('name');

        $page_id = $coreContentTable->select()
                ->from($coreContentTableName, array('page_id'))
                ->where('content_id = ?', $content_id)
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
            return 0;
        }

//GET DATA
        $params = $coreContentTable->select()
                ->from($coreContentTableName, array('params'))
                ->where($coreContentTableName . '.page_id = ?', $page_id)
                ->where($coreContentTableName . '.name = ?', 'sitecrowdfunding.browse-projects-sitecrowdfinding')
                ->query()
                ->fetchColumn();

        $paramsArray = !empty($params) ? Zend_Json::decode($params) : array();

        if (isset($paramsArray['orderby']) && !empty($paramsArray['orderby'])) {
            return $paramsArray['orderby'];
        } else {
            return 0;
        }
    }

    /**
     * Get sitecrowdfunding tags created by users 
     * @param int $itemCount : number tags to show
     */
    public function getProjectTags($itemCount = 0, $totalCount = 0, $params = array()) {

        $tableSiteproject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $tableSiteprojectName = $tableSiteproject->info('name');
        //DO NOT INCLUDE THE PROJECTS BEFORE START DATE
        $currentDate = date('Y-m-d H:i:s');
        //MAKE QUERY
        $select = $tableSiteproject->select()
                ->setIntegrityCheck(false)
                ->from($tableSiteprojectName, array("project_id"))
                ->where($tableSiteprojectName . ".search = ?", 1)
                ->where("$tableSiteprojectName.state <> ?", 'draft')
                ->where("$tableSiteprojectName.approved = ?", 1)
                ->where("$tableSiteprojectName.is_gateway_configured = ?", 1)
                ->where("start_date <= '$currentDate'");

        $select->distinct(true);

        $projectIds = $select
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        if (empty($projectIds)) {
            return;
        }

        $tableTagMaps = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tableTagMapsName = $tableTagMaps->info('name');
        //GET TAG TABLE NAME
        $tableTags = 'engine4_core_tags';

        $select = $tableTagMaps->select()
                ->setIntegrityCheck(false)
                ->from($tableTagMapsName, array("COUNT($tableTagMapsName.resource_id) AS Frequency", 'resource_id'))
                ->joinInner($tableTags, "$tableTags.tag_id = $tableTagMapsName.tag_id", array('text', 'tag_id'));

        $select->where($tableTagMapsName . '.resource_type = ?', 'sitecrowdfunding_project');
        $select->where($tableTagMapsName . '.resource_id IN(?)', (array) $projectIds);

        $select->group("$tableTags.text");
        if (isset($params['orderingType']) && !empty($params['orderingType']))
            $select->order("$tableTags.text");
        else
            $select->order("Frequency DESC");

        //SHOW ALL TAGS IF THE WIDGET IS PLACED ON TAG CLOUD PAGE
        if (isset($params['showMoreTag']) && $params['action'] != 'tagscloud') {
            if (!empty($itemCount)) {
                $select = $select->limit($itemCount);
            }
        }

        if (!empty($totalCount)) {
            $total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            return Count($total_results);
        }

//RETURN RESULTS

        return $select->query()->fetchAll();
    }

    public function getProjectTagsByProjectId($itemCount = 0, $totalCount = 0, $params = array(),$project_id) {

        $tableSiteproject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $tableSiteprojectName = $tableSiteproject->info('name');
        //DO NOT INCLUDE THE PROJECTS BEFORE START DATE
        $currentDate = date('Y-m-d H:i:s');
        //MAKE QUERY
        $select = $tableSiteproject->select()
            ->setIntegrityCheck(false)
            ->from($tableSiteprojectName, array("project_id"))
            ->where($tableSiteprojectName . ".search = ?", 1)
            ->where($tableSiteprojectName . ".project_id = ?", $project_id)
            ->where("$tableSiteprojectName.state <> ?", 'draft')
            ->where("$tableSiteprojectName.approved = ?", 1)
            ->where("$tableSiteprojectName.is_gateway_configured = ?", 1)
            ->where("start_date <= '$currentDate'");

        $select->distinct(true);

        $projectIds = $select
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

        if (empty($projectIds)) {
            return;
        }

        $tableTagMaps = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tableTagMapsName = $tableTagMaps->info('name');
        //GET TAG TABLE NAME
        $tableTags = 'engine4_core_tags';

        $select = $tableTagMaps->select()
            ->setIntegrityCheck(false)
            ->from($tableTagMapsName, array("COUNT($tableTagMapsName.resource_id) AS Frequency", 'resource_id'))
            ->joinInner($tableTags, "$tableTags.tag_id = $tableTagMapsName.tag_id", array('text', 'tag_id'));

        $select->where($tableTagMapsName . '.resource_type = ?', 'sitecrowdfunding_project');
        $select->where($tableTagMapsName . '.resource_id IN(?)', (array) $projectIds);

        $select->group("$tableTags.text");
        if (isset($params['orderingType']) && !empty($params['orderingType']))
            $select->order("$tableTags.text");
        else
            $select->order("Frequency DESC");

        //SHOW ALL TAGS IF THE WIDGET IS PLACED ON TAG CLOUD PAGE
        if (isset($params['showMoreTag']) && $params['action'] != 'tagscloud') {
            if (!empty($itemCount)) {
                $select = $select->limit($itemCount);
            }
        }

        if (!empty($totalCount)) {
            $total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            return Count($total_results);
        }

//RETURN RESULTS

        return $select->query()->fetchAll();
    }

    public function canDeletePrivacy($parent_type = null, $parent_id = null, $item = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (strstr($parent_type, 'sitereview_listing')) {
            $parent_type = 'sitereview_listing';
        }
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        if ($parent_type == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page')) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
            if ($viewer_id != $sitepage->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business')) {
            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
            if ($viewer_id != $sitebusiness->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'sitegroup_group' && Engine_Api::_()->hasItemType('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            if ($viewer_id != $sitegroup->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'siteevent_event' && Engine_Api::_()->hasItemType('siteevent_event')) {
            $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'video', "delete");
            if ($can_delete) {
                return true;
            } else {
                return false;
            }
        } elseif (strpos($parent_type, "sitereview_listing") !== false) {
            $sitereview = Engine_Api::_()->getItem('sitereview_listing', $parent_id);

            $can_delete = $sitereview->authorization()->isAllowed($viewer, 'delete_listtype_' . $sitereview->listingtype_id);
            if ($can_delete) {
                return true;
            } else {
                return false;
            }
        } else {

            if (!$item->authorization()->isAllowed($viewer, 'delete'))
                return false;
        }

        return true;
    }

    public function deleteProject($project) {

        $db = Engine_Db_Table::getDefaultAdapter();
        // delete storage files
        if (Engine_Api::_()->getItem('storage_file', $project->photo_id) && $project->photo_id)
            Engine_Api::_()->getItem('storage_file', $project->photo_id)->remove();

        // delete project favourites  
        Engine_Api::_()->getDbtable('favourites', 'seaocore')->delete(array(
            'resource_id = ?' => $project->project_id,
            'resource_type = ?' => 'sitecrowdfunding_project'
        ));

        //Delete entry from sitecrowdfunding location table
        Engine_Api::_()->getDbTable('locations', 'sitecrowdfunding')->delete(array(
            'project_id = ?' => $project->project_id,
        ));

        $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $project->project_id);
        if ($item) {
            $item->delete();
        }
    }

    public function isCreatePrivacy($parent_type = null, $parent_id = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (strstr($parent_type, 'sitereview_listing')) {
            $parent_type = 'sitereview_listing';
        }
        if ($parent_type == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page')) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
            $issprcreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sprcreate');
            if (empty($issprcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business')) {
            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
            $issprcreate = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'sprcreate');
            if (empty($issprcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitegroup_group' && Engine_Api::_()->hasItemType('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            $issprcreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sprcreate');
            if (empty($issprcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitereview_listing' && Engine_Api::_()->hasItemType('sitereview_listing')) {
            $sitereview = Engine_Api::_()->getItem('sitereview_listing', $parent_id);
            $issprcreate = Engine_Api::_()->authorization()->isAllowed($sitereview, $viewer, "sprcreate_listtype_$sitereview->listingtype_id");
            if (empty($issprcreate)) {
                return false;
            }
        } else {
            if (!Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "create")) {
                return false;
            }
        }

        return true;
    }

    public function isEditPrivacy($parent_type = null, $parent_id = null, $item = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (strstr($parent_type, 'sitereview_listing')) {
            $parent_type = 'sitereview_listing';
        }
        if ($parent_type == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page')) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
            $issprcreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sprcreate');
            if (empty($issprcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business')) {
            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
            $issprcreate = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'sprcreate');
            if (empty($issprcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitegroup_group' && Engine_Api::_()->hasItemType('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            $issprcreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sprcreate');
            if (empty($issprcreate) && empty($isManageAdmin)) {
                return false;
            }
        } elseif (strpos($parent_type, "sitereview_listing") !== false) {
            $parentTypeItem = Engine_Api::_()->getItem('sitereview_listing', $parent_id);
            $canEdit = $parentTypeItem->authorization()->isAllowed($viewer, "edit_listtype_$parentTypeItem->listingtype_id");
            if (empty($canEdit))
                return false;
        } else if ($parent_type == 'siteevent_event' && Engine_Api::_()->hasItemType('siteevent_event')) {
            $parentTypeItem = Engine_Api::_()->getItem('siteevent_event', $parent_id);

            $canEdit = $parentTypeItem->authorization()->isAllowed($viewer, "edit");
            if (empty($canEdit))
                return false;
        } else {
            if ($viewer->getIdentity() != $item->getOwner()->getIdentity() && !$item->authorization()->isAllowed($viewer, 'edit'))
                return false;
        }
        return true;
    }

    public function getTotalCount($project_id, $modulename, $tablename) {

        $table = Engine_Api::_()->getDbtable($tablename, $modulename);
        $count = 0;
        $count = $table
                ->select()
                ->from($table->info('name'), array('count(*) as count'))
                ->where("parent_type = ?", 'sitecrowdfunding_project')
                ->where("parent_id =?", $project_id)
                ->query()
                ->fetchColumn();

        return $count;
    }

    /**
     * Check widget is exist or not
     *
     */
    public function existWidget($widget = '', $identity = 0) {

//GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

//GET PAGE TABLE
        $tablePage = Engine_Api::_()->getDbtable('pages', 'core');
        $tablePageName = $tablePage->info('name');

        if ($widget == 'sitecrowdfunding.profile-announcements-sitecrowdfunding') {
//GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "sitecrowdfunding_project_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'sitecrowdfunding.profile-announcements-sitecrowdfunding')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        }
    }

    public function categoriesPageCreateOnInstall() {
        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
        $categories = $tableCategory->getCategories(array('category_id'), null, 0, 0, 0);
        $categoryIds = array();
        foreach ($categories as $category) {
            $categoryIds[] = $category->category_id;
        }
        $this->categoriesPageCreate($categoryIds);
    }

    public function getGatwayName($gateway_id) {
        if (!empty($gateway_id)) {
            $gateway = Engine_Api::_()->getItem("payment_gateway", $gateway_id);
        }
        return $gateway->title ? $gateway->title : '';
    }

    public function getPaymentGateway($plugin) {

        $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $whereCondition = "plugin = '{$plugin}'";

        $gatewayId = $gatewayTable->select()
                ->from($gatewayTable, 'gateway_id')
                ->where($whereCondition)
                ->query()
                ->fetchColumn();

        return Engine_Api::_()->getItem('payment_gateway', $gatewayId);
    }

    public function getTransferThreshold($project_id) {
        $projectObj = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $getThresholdAmount = Engine_Api::_()->authorization()->getPermission($projectObj->getOwner()->level_id, 'sitecrowdfunding_project', "transfer_threshold");
        //  echo $getThresholdAmount;
        // die;
        if (!empty($getThresholdAmount)) {
            return $getThresholdAmount;
        }

        return 100;
    }

    public function getRemainingBillAmount($project_id) {

        $projectRemainingBillObj = Engine_Api::_()->getDbtable('remainingbills', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id));

        $tempRemainingBillAmount = !empty($projectRemainingBillObj) ? $projectRemainingBillObj->remaining_bill : 0;
        $paymentFailedBillAmount = Engine_Api::_()->getDbtable('Projectbills', 'sitecrowdfunding')->paymentFailedBillAmount($project_id);
        // IF SEELER HAS MAKE PAYMENT AND HIS AMOUNT IS NOT SUBMMITED, THEN ADD IN REMAINING AMOUNT
        if (!empty($paymentFailedBillAmount)) {
            $remainingBillAmount = $tempRemainingBillAmount + $paymentFailedBillAmount;
            Engine_Api::_()->getDbtable('remainingbills', 'sitecrowdfunding')->update(array('remaining_bill' => round($remainingBillAmount, 2)), array('project_id = ?' => $project_id));
            Engine_Api::_()->getDbtable('projectbills', 'sitecrowdfunding')->update(array("status" => "not_paid"), array('project_id =?' => $project_id, "status != 'active'", "status != 'not_paid'"));
        } else {
            $remainingBillAmount = $tempRemainingBillAmount;
        }

        // SUBTRACT NON-PAYMENT BACKERS AMOUNT FROM PROJECT BILL
        $notPaidBillAmount = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->notPaidBillAmount($project_id);
        if (!empty($notPaidBillAmount) && ($remainingBillAmount >= $notPaidBillAmount)) {
            $remainingBillAmount -= round($notPaidBillAmount, 2);
            Engine_Api::_()->getDbtable('remainingbills', 'sitecrowdfunding')->update(array('remaining_bill' => round($remainingBillAmount, 2)), array('project_id = ?' => $project_id));
            Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->update(array('payment_status' => 'not_paid'), array('project_id = ?' => $project_id, 'direct_payment = 1', 'non_payment_admin_reason = 1', 'order_status = 3', "payment_status != 'not_paid'"));
        }

        return $remainingBillAmount;
    }

    public function isAllowThresholdNotifications($params = array()) {

        $project_id = $params['project_id'];
        $settingsApi = Engine_Api::_()->getApi('settings', 'core');
        $thresholdnotificationamount = $settingsApi->getSetting('sitecrowdfunding.thresholdnotificationamount', 100);
        $notificationType = $settingsApi->getSetting('sitecrowdfunding.thresholdnotify', array('owner', 'admin'));

        if (!$settingsApi->getSetting('sitecrowdfunding.payment.to.siteadmin', '0') && $settingsApi->getSetting('sitecrowdfunding.thresholdnotification', 0) && !empty($thresholdnotificationamount) && !empty($notificationType)) {

            $remainingBillAmount = $this->getRemainingBillAmount($project_id);
            $newBillAmount = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getProjectBillAmount($project_id);
            $remainingBillAmount = round($remainingBillAmount, 2);
            $totalBillAmount = round(($remainingBillAmount + $newBillAmount), 2);

            if ($totalBillAmount >= $thresholdnotificationamount) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get An Order Commission
     * @param int $project_id
     * @return array
     */
    public function getOrderCommission($project_id) {
        $projectObj = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $commission = array();
        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $packageObj = Engine_Api::_()->getItem('sitecrowdfunding_package', $projectObj->package_id);
            if (!empty($packageObj->commission_settings)) {
                $commissionInfo = @unserialize($packageObj->commission_settings);
                $commission[] = $commissionInfo['commission_handling'];
                if (empty($commissionInfo['commission_handling'])) {
                    $commission[] = $commissionInfo['commission_fee'];
                } else {
                    $commission[] = $commissionInfo['commission_rate'];
                }
            } else {
                $commission[] = 1;
                $commission[] = 1;
            }
        } else {
            $user = $projectObj->getOwner();
            $commissionHandlingType = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitecrowdfunding_project', "commission_handling");
            if ($commissionHandlingType != 0 && $commissionHandlingType != 1) {
                $commission[] = 1;
                $commission[] = 1;
            } else {
                $commission[] = $commissionHandlingType;
                if (empty($commissionHandlingType)) {
                    $commission[] = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitecrowdfunding_project', "commission_fee");
                } else {
                    $commission[] = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitecrowdfunding_project', "commission_rate");
                }
            }
        }
        return $commission;
    }

    public function createDefaultSlideshow() {
        $table = Engine_Api::_()->getDbtable('slideshows', 'sitehomepagevideo');
        $slideshow = $table->fetchRow(array('title = ?' => 'Crowdfunding landing page slideshow'));
        if ($slideshow) {
            return $slideshow->slideshow_id;
        }
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $row = $table->createRow();
            $values = array('slideshow_type' => 0,
                'delay' => 2000,
                'title' => 'Crowdfunding landing page slideshow',
                'enabled' => 1
            );
            $params = array_merge(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
                    ), $values);
            $row->setFromArray($params);
            $row->save();
            for ($i = 0; $i < 4; $i++) {
                $imgpath = APPLICATION_PATH . "/application/modules/Sitecrowdfunding/externals/images/project_images/$i.jpg";
                @chmod($imgpath, 0777);
                $this->uploadSlidePhoto($row->slideshow_id, $imgpath);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
        return $row->slideshow_id;
    }

    public function uploadSlidePhoto($slideshow_id, $file) {
        $db = Engine_Api::_()->getDbtable('videos', 'sitehomepagevideo')->getAdapter();
        $db->beginTransaction();
        try {
            $video = $this->createVideo($file);
            // sets up title and owner_id now just incase members switch page as soon as upload is completed
            $video->title = $video->video_id;
            $video->enabled = 1;
            $video->slideshow_id = $slideshow_id;
            $video->status = 1;
            $video->type = 3;

            $video->save();
        } catch (Exception $e) {
            
        }
        return $video->video_id;
    }

    public function createVideo($file) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $video = Engine_Api::_()->getDbtable('videos', 'sitehomepagevideo')->createRow();
        $extension = ltrim(strrchr(basename($file), '.'), '.');
        $video->code = 'image';
        $video->save();
        $viewer = Engine_Api::_()->user()->getViewer();
        $base = rtrim(substr(basename($file), 0, strrpos(basename($file), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'name' => $file,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        //Fetching the width and height of thumbmail
        $mainHeight = 680;
        $mainWidth = 1400;
        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize($mainWidth, $mainHeight)
                ->write($mainPath)
                ->destroy();

        $iMain = $filesTable->createFile($mainPath, $params);
        $iMain->bridge($iMain, 'thumb.main');
        @unlink($mainPath);
        $video->photo_id = $video->file_id = $iMain->getIdentity();
        $video->save();
        return $video;
    }

    //SET THE PAYMENT GATEWAY GATEWAY COLUMN IN PROJECTS TABLE
    public function setPaymentFlag($project_id) {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $paymentMethod = $settings->getSetting('sitecrowdfunding.payment.method', 'normal');
        $normalPaymentType = $settings->getSetting('sitecrowdfunding.payment.to.siteadmin', '0');
        $projectEnabledgateway = Engine_Api::_()->getDbTable('projectGateways', 'sitecrowdfunding')->getEnabledGateways($project_id);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($paymentMethod == 'normal' && $normalPaymentType) {
            $project->is_gateway_configured = 1;
            $project->save();
        } else { 
            switch ($paymentMethod) {
                case 'normal' :
                    $siteAdminEnablePaymentGateway = $settings->getSetting('sitecrowdfunding.allowed.payment.gateway', array('paypal'));
                    break;
                case 'split' :
                    $siteAdminEnablePaymentGateway = $settings->getSetting('sitecrowdfunding.allowed.payment.split.gateway', array());
                    break;
                case 'escrow' :
                    $siteAdminEnablePaymentGateway = $settings->getSetting('sitecrowdfunding.allowed.payment.escrow.gateway', array());
                    break;
            }
            $isGatewayConfigured = 0;
            if (!empty($projectEnabledgateway)) {
                foreach ($projectEnabledgateway as $enbGatewayName) {
                    if (in_array(strtolower($enbGatewayName->title), $siteAdminEnablePaymentGateway)) {
                        $isGatewayConfigured = 1;
                        break;
                    }
                }
            }
            $project->is_gateway_configured = $isGatewayConfigured;
            $project->save();
        }
    }

    //return gateway options with their id and name
    public function getGatewayOptions() {
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $multiOptions = array('' => '');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $multiOptions[$gateway->gateway_id] = $gateway->title;
        }
        return $multiOptions;
    }

    //FUNCTIION TO CHECK THE SITE IN MOBILE MODE
    public function isSiteMobileMode() {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if( preg_match('/Mobile/i', $ua) || preg_match('/Opera Mini/i', $ua) || preg_match('/NokiaN/i', $ua) ) {
          return true;
        }
        return false;
    }

    //return the project_id set for the widget placed on the content profile page
    public function adminSelectedProject($pageName = false, $returnBackTitle = false) {  
        if(empty($pageName)) {
            return false;
        }
        $db = Zend_Db_Table_Abstract::getDefaultAdapter(); 
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', $pageName)->limit(1);
 
        $info = $select->query()->fetch();
        if (!empty($info)) {
            $page_id = $info['page_id'];

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_content', array("params"))
                    ->where('page_id = ?', $page_id)
                    ->where("name LIKE '%sitecrowdfunding.back-project%'")
                    ->limit(1);
            $info = $select->query()->fetch();
            $params = json_decode($info['params']); 
        } 
        if($returnBackTitle) {
           return ($params->backTitle);
        }
        return ($params->toValues) ? $params->toValues : false; 
    }

}

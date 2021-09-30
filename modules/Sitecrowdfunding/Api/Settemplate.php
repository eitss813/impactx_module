<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settemplate.php 2017-05-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Api_Settemplate extends Core_Api_Abstract {

    public function checkPageId($name = false) {

        if (!$name)
            return false;

        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $page_id = $select
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name = ?', $name)
                        ->query()->fetchColumn();

        return $page_id;
    }

    public function deletePageAndContent($page_id) {

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->query("DELETE FROM `engine4_core_pages` WHERE `engine4_core_pages`.`page_id` = $page_id");
        $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");
    }

    public function checkoutPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_backer_checkout');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_backer_checkout','Crowdfunding - Checkout out',NULL,'Backing Project','This is backing checkout page.','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_checkout' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_checkout' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_checkout' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_checkout' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_checkout' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_checkout' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_checkout' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-information',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"title\":\"\",\"titleCount\":true}',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_checkout' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.reward-information',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"\",\"titleCount\":true}',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_checkout' ;
");
        }
    }

    public function projectViewPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_project_view');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'advancedactivity')
                ->where('enabled = ?', 1);
        $is_advancedactivity_object = $select->query()->fetchObject();


        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_project_view','Crowdfunding - Project Profile',NULL,'Project Profile','This is the main view page of a project.','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.container-tabs',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"max\":\"8\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"core.container-tabs\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            if ($is_advancedactivity_object) {
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','advancedactivity.home-feeds',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'9','{\"0\":\"\",\"title\":\"What\'s New\",\"showFeeds\":1,\"advancedactivity_tabs\":[\"aaffeed\"]}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;");
            } else {
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','activity.feed',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'9','{\"title\":\"What\'s New\",\"showFeeds\":1,\"advancedactivity_tabs\":[\"welcome\",\"aaffeed\",\"twitter\"],\"showTabs\":\"0\",\"loadByAjax\":\"0\",\"showScrollTopButton\":\"1\",\"widthphotoattachment\":\"618\",\"width1\":\"608\",\"width2\":\"608\",\"height2\":\"350\",\"width3big\":\"608\",\"height3big\":\"300\",\"width3small\":\"299\",\"height3small\":\"200\",\"width4big\":\"608\",\"height4big\":\"250\",\"width4small\":\"195\",\"height4small\":\"100\",\"width5big\":\"299\",\"height5big\":\"200\",\"width5small\":\"195\",\"height5small\":\"130\",\"nomobile\":\"0\",\"name\":\"activity.feed\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;");
            }
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-overview',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'7','{\"title\":\"Overview\",\"titleCount\":true,\"loaded_by_ajax\":\"0\",\"showComments\":\"0\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.project-overview\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.specification-project',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'8','{\"title\":\"Information\",\"titleCount\":true,\"loaded_by_ajax\":\"0\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.specification-project\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.profile-announcements-sitecrowdfunding',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'13','{\"title\":\"Announcements\",\"titleCount\":true,\"showTitle\":\"1\",\"itemCount\":\"100\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.profile-announcements-sitecrowdfunding\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.contenttype-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'11','{\"title\":\"Videos\",\"itemCountPerPage\":\"10\",\"margin_video\":\"2\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"ratings\",\"favourite\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoHeight\":\"245\",\"videoWidth\":\"283\",\"columnHeight\":\"200\",\"show_content\":\"2\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.contenttype-videos\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-photos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'12','{\"title\":\"Photos\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"showPhotosInJustifiedView\":\"1\",\"rowHeight\":\"180\",\"maxRowHeight\":\"250\",\"margin\":\"5\",\"lastRow\":\"justify\",\"width\":\"80\",\"height\":\"80\",\"itemCount\":\"20\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.project-photos\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-backers',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'10','{\"title\":\"Project Backers\",\"titleCount\":true,\"height\":\"230\",\"width\":\"150\",\"loaded_by_ajax\":\"0\",\"itemCount\":\"100\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.project-backers\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-location',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'15','{\"0\":\"\",\"title\":\"Map\",\"titleCount\":true}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.back-project',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'18','{\"title\":\"\",\"titleCount\":true,\"backTitle\":\"Back This Project\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.back-project\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.quick-specification-project',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'19','{\"0\":\"\",\"title\":\"Quick Information\",\"titleCount\":true}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.rewards-listing',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'20','{\"showSlide\":\"0\",\"slideHeight\":\"380\",\"descriptionTruncation\":\"100\",\"title\":\"Rewards\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.rewards-listing\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.people-who-backed',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'21','{\"title\":\"Who backed\",\"titleCount\":true,\"options\":[\"name\",\"amount\",\"totalCount\"],\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.people-who-backed\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-discussion',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'14','{\"title\":\"Discussions\",\"titleCount\":true,\"loaded_by_ajax\":\"0\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.project-discussion\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.main-project-information',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"projectOption\":[\"title\",\"description\",\"owner\",\"location\",\"fundingRatio\",\"fundedAmount\",\"daysLeft\",\"backerCount\",\"backButton\",\"category\",\"dashboardButton\",\"shareOptions\",\"optionsButton\"],\"columnHeight\":\"450\",\"titleTruncation\":\"100\",\"descriptionTruncation\":\"340\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.main-project-information\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-status',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'17','{\"title\":\"\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_view' ;
");
        }
    }

    public function topicViewPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_topic_view');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_topic_view','Crowdfunding - Discussion Topic View Page',NULL,'View Project Discussion Topic','This is the view page for a project discussion.','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top','0','1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_topic_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main','0','2','',NULL from engine4_core_pages where name = 'sitecrowdfunding_topic_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_topic_view' ;
");

            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','',NULL from engine4_core_pages where name = 'sitecrowdfunding_topic_view' ;
");

            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'3','',NULL from engine4_core_pages where name = 'sitecrowdfunding_topic_view' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.discussion-content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'5','',NULL from engine4_core_pages where name = 'sitecrowdfunding_topic_view' ;
");
        }
    }

    public function rewardSelectionPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_backer_reward-selection');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_backer_reward-selection','Crowdfunding - Reward Selection',NULL,'Backing Project','This is backing checkout page.','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top','0','1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_reward-selection' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main','0','2','',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_reward-selection' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_reward-selection' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_reward-selection' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_reward-selection' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'3','',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_reward-selection' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'5','',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_reward-selection' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-information',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'7','',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_reward-selection' ;
");
        }
    }

    public function categoryHomePage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_index_categories');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_index_categories','Crowdfunding - Categories Home',NULL,'Crowdfunding - Categories Home','This is the home page of Project Categories .','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-categorybanner-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'4','{\"title\":\"\",\"logo\":\"public\\/admin\\/website-background-grey1.jpg\",\"height\":\"520\",\"categoryHeight\":\"470\",\"fullWidth\":\"1\",\"category_id\":[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"56\",\"59\",\"11\",\"13\",\"14\",\"16\"],\"showExplore\":\"1\",\"titleTruncation\":\"36\",\"taglineTruncation\":\"100\",\"nomobile\":\"\",\"name\":\"sitecrowdfunding.project-categorybanner-slideshow\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'7','{\"title\":\"Categories\",\"titleCount\":true,\"orderBy\":\"category_name\",\"showAllCategories\":\"1\",\"columnWidth\":\"230\",\"columnHeight\":\"200\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.project-categories-withicon-grid-view\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'9','{\"title\":\"Charity\",\"category_id\":\"3\",\"subcategory_id\":\"0\",\"hidden_project_category_id\":\"3\",\"hidden_project_subcategory_id\":\"0\",\"hidden_project_subsubcategory_id\":\"0\",\"projectType\":null,\"projectOption\":[\"title\",\"owner\",\"creationDate\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showProject\":\"featuredSponsored\",\"popularType\":\"random\",\"selectProjects\":\"all\",\"daysFilter\":\"20\",\"backedPercentFilter\":\"50\",\"showPagination\":\"1\",\"projectWidth\":\"371\",\"projectHeight\":\"280\",\"showLink\":\"0\",\"rowLimit\":\"3\",\"itemCount\":\"12\",\"interval\":\"3500\",\"titleTruncation\":\"20\",\"truncationLocation\":\"35\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"1000\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.project-carousel\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'8','{\"title\":\"Education\",\"category_id\":\"6\",\"subcategory_id\":\"0\",\"hidden_project_category_id\":\"6\",\"hidden_project_subcategory_id\":\"0\",\"hidden_project_subsubcategory_id\":\"0\",\"projectType\":\"0\",\"projectOption\":[\"title\",\"owner\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showProject\":\"featuredSponsored\",\"popularType\":\"random\",\"selectProjects\":\"all\",\"daysFilter\":\"20\",\"backedPercentFilter\":\"50\",\"showPagination\":\"1\",\"projectWidth\":\"371\",\"projectHeight\":\"280\",\"showLink\":\"1\",\"rowLimit\":\"3\",\"itemCount\":\"12\",\"interval\":\"3500\",\"titleTruncation\":\"20\",\"truncationLocation\":\"35\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"1000\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.project-carousel\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'10','{\"title\":\"Fashion\",\"category_id\":\"7\",\"subcategory_id\":\"0\",\"hidden_project_category_id\":\"7\",\"hidden_project_subcategory_id\":\"0\",\"hidden_project_subsubcategory_id\":\"0\",\"projectType\":\"0\",\"projectOption\":[\"title\",\"owner\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showProject\":\"\",\"popularType\":\"random\",\"selectProjects\":\"all\",\"daysFilter\":\"20\",\"backedPercentFilter\":\"50\",\"showPagination\":\"1\",\"projectWidth\":\"371\",\"projectHeight\":\"280\",\"showLink\":\"1\",\"rowLimit\":\"3\",\"itemCount\":\"12\",\"interval\":\"3500\",\"titleTruncation\":\"20\",\"truncationLocation\":\"35\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"1000\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.project-carousel\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_categories' ;
");
        }
    }

    public function browsePage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_project_browse');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_project_browse','Crowdfunding - Browse Projects',NULL,'Crowdfunding - Browse Projects','This is the Browse page For Projects .','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','left',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'4','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.browse-projects',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'10','{\"title\":\"\",\"titleCount\":true,\"projectOption\":[\"title\",\"owner\",\"creationDate\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"location\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"projectType\":\"0\",\"selectProjects\":\"all\",\"viewType\":[\"gridView\",\"listView\"],\"defaultViewType\":\"gridView\",\"gridViewWidth\":\"283\",\"gridViewHeight\":\"510\",\"orderby\":\"featuredSponsored\",\"show_content\":\"2\",\"gridItemCountPerPage\":\"12\",\"listItemCountPerPage\":\"9\",\"titleTruncationGridView\":\"25\",\"titleTruncationListView\":\"55\",\"descriptionTruncation\":\"175\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"1000\",\"truncationLocation\":\"30\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.browse-projects\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.search-project-sitecrowdfunding',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'6','{\"title\":\"Search Projects\",\"titleCount\":true,\"viewType\":\"horizontal\",\"showAllCategories\":\"1\",\"locationDetection\":\"0\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.search-project-sitecrowdfunding\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.tagcloud-sitecrowdfunding-project',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'8','{\"title\":\"Popular Tags\",\"titleCount\":true}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.project-categories-navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'7','{\"orderBy\":\"category_name\",\"title\":\"Categories\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.project-categories-navigation\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_browse' ;
");
        }
    }

    public function myProjectPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_project_manage');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_project_manage','Crowdfunding - My Projects Page',NULL,'My Projects Page','This page shows the Projects created, liked, favorited and backed by the viewer.','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_manage' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_manage' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_manage' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_manage' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_manage' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_manage' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.my-projects',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"selectProjects\":\"all\",\"topNavigationLink\":[\"createProject\"],\"projectNavigationLink\":[\"all\",\"backed\",\"liked\",\"favourite\"],\"viewType\":[\"gridView\",\"listView\"],\"defaultViewType\":\"gridView\",\"searchButton\":\"1\",\"gridWidth\":\"283\",\"gridHeight\":\"530\",\"projectOption\":[\"title\",\"owner\",\"creationDate\",\"backer\",\"favourite\",\"like\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"location\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"show_content\":\"3\",\"gridItemCount\":\"12\",\"listItemCount\":\"9\",\"titleTruncationGridView\":\"25\",\"titleTruncationListView\":\"55\",\"truncationLocation\":\"30\",\"descriptionTruncation\":\"175\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.my-projects\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_manage' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.list-popular-projects',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Backed Projects\",\"itemCountPerPage\":\"3\",\"projectType\":\"0\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_project_category_id\":\"\",\"hidden_project_subcategory_id\":\"\",\"hidden_project_subsubcategory_id\":\"\",\"selectProjects\":\"all\",\"showProject\":\"featuredSponsored\",\"projectWidth\":\"217\",\"projectHeight\":\"200\",\"popularType\":\"backed\",\"interval\":\"overall\",\"projectInfo\":[\"title\",\"owner\",\"creationDate\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"location\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.list-popular-projects\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_manage' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.list-popular-projects',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"Most Rated Projects\",\"itemCountPerPage\":\"4\",\"projectType\":\"0\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_project_category_id\":\"\",\"hidden_project_subcategory_id\":\"\",\"hidden_project_subsubcategory_id\":\"\",\"selectProjects\":\"all\",\"showProject\":\"featuredSponsored\",\"projectWidth\":\"217\",\"projectHeight\":\"200\",\"popularType\":\"rated\",\"interval\":\"overall\",\"projectInfo\":[\"title\",\"owner\",\"creationDate\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"location\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.list-popular-projects\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_manage' ;
");
        }
    }

    public function tagscloudPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_project_tagscloud');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) { 
             $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_project_tagscloud','Crowdfunding - Projects Tagcloud Page',NULL,'Crowdfunding - Projects Tagcloud Page','This is the Tagcloud page For Projects .','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top','0','1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_tagscloud' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main','0','2','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_tagscloud' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_tagscloud' ;
");

            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_tagscloud' ;
");

            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'3','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_tagscloud' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.tagcloud-sitecrowdfunding-project',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'5','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_tagscloud' ;
");
        }
    }

    public function packagePage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_package_index');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_package_index','Crowdfunding - Packages for Projects',NULL,'Packages for Projects','This is the Packages page for Sitecrowdfunding projects.','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_package_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_package_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_package_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_package_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_package_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','[]',NULL from engine4_core_pages where name = 'sitecrowdfunding_package_index' ;
");
        }
    }

    public function projectMapPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_project_map');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_project_map','Crowdfunding - Projects Locations Page',NULL,'Crowdfunding - Projects Locations Page','This is the projects Locations page For Projects .','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_map' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_map' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_map' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_map' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_map' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.browselocation-sitecrowdfunding',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"viewType\":[\"gridView\",\"listView\"],\"defaultViewType\":\"gridView\",\"gridViewWidth\":\"365\",\"gridViewHeight\":\"505\",\"projectOption\":[\"title\",\"owner\",\"creationDate\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"location\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncationGridView\":\"32\",\"titleTruncationListView\":\"55\",\"descriptionTruncation\":\"225\",\"truncationLocation\":\"35\",\"showAllCategories\":\"1\",\"locationDetection\":\"0\",\"itemCount\":\"9\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.browselocation-sitecrowdfunding\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_map' ;
");
        }
    }

    public function projectCreatePage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_project_create');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_project_create','Crowdfunding - Create Project ',NULL,'Create Project','This is create project page.','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top','0','1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_create' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main','0','2','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_create' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_create' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_create' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'3','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_create' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'2','',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_create' ;
");
        }
    }

    public function pinboardPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_project_pinboard');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_project_pinboard','Crowdfunding - Browse Project\'s Pinboard View Page ',NULL,'Projects Pinboard View','This is Crowdfunding\'s Pinboard view page.It displays a list of all the projects on your site in attractive Pinboard View','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_pinboard' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_pinboard' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_pinboard' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_pinboard' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_pinboard' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.pinboard-browse-projects',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"projectOption\":[\"title\",\"backer\",\"like\",\"comment\",\"endDate\",\"location\"],\"projectType\":null,\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_project_category_id\":\"\",\"hidden_project_subcategory_id\":\"\",\"hidden_project_subsubcategory_id\":\"\",\"userComment\":\"1\",\"autoload\":\"1\",\"defaultLoadingImage\":\"1\",\"itemWidth\":\"389\",\"show_buttons\":[\"comment\",\"like\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"withoutStretch\":\"1\",\"orderby\":\"featuredSponsored\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"1000\",\"truncationLocation\":\"35\",\"titleTruncation\":\"35\",\"descriptionTruncation\":\"210\",\"itemCountPerPage\":\"12\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.pinboard-browse-projects\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_project_pinboard' ;
");
        }
    }

    public function projectHomePage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_index_index');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_index_index','Crowdfunding - Projects Home page ',NULL,'Projects Home Page','This is the Home page for Sitecrowdfunding projects','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.navigation\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.featured-projects-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"projectType\":null,\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_project_category_id\":\"\",\"hidden_project_subcategory_id\":\"0\",\"hidden_project_subsubcategory_id\":\"0\",\"showNavigationButton\":\"1\",\"fullWidth\":\"1\",\"popularType\":\"random\",\"interval\":\"overall\",\"height\":\"485\",\"delay\":\"3500\",\"slidesLimit\":\"4\",\"titleTruncation\":\"50\",\"descriptionTruncation\":\"130\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.featured-projects-slideshow\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.ajax-based-projects-home',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"\",\"defaultViewType\":\"gridZZZview\",\"ajaxTabs\":[\"mostZZZrecent\",\"mostZZZliked\",\"mostZZZcommented\",\"mostZZZbacked\",\"random\"],\"projectType\":null,\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_project_category_id\":\"\",\"hidden_project_subcategory_id\":\"\",\"hidden_project_subsubcategory_id\":\"\",\"gridViewWidth\":\"283\",\"gridViewHeight\":\"510\",\"viewType\":[\"gridZZZview\",\"listZZZview\"],\"projectOption\":[\"title\",\"owner\",\"creationDate\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"location\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"selectProjects\":\"all\",\"daysFilter\":\"90\",\"backedPercentFilter\":\"1\",\"recentOrder\":\"1\",\"likedOrder\":\"3\",\"commentedOrder\":\"4\",\"backedOrder\":\"2\",\"randomOrder\":\"5\",\"showViewMore\":\"1\",\"gridItemCountPerPage\":\"9\",\"listItemCountPerPage\":\"6\",\"titleTruncationGridView\":\"25\",\"titleTruncationListView\":\"55\",\"descriptionTruncation\":\"175\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"1000\",\"truncationLocation\":\"30\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.ajax-based-projects-home\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.create-project-link',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"\",\"titleCount\":true,\"create_button\":1,\"name\":\"sitecrowdfunding.create-project-link\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.special-projects',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"title\":\"Special Projects\",\"project_ids\":\"\",\"toValues\":\"20,3,2,6,85,64,73,80,98\",\"starttime\":\"2017-04-01 01:00:00\",\"endtime\":\"2020-09-27 01:00:00\",\"columnWidth\":\"217\",\"columnHeight\":\"200\",\"projectOption\":[\"title\",\"owner\",\"creationDate\",\"backer\",\"favourite\",\"like\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"location\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"itemCount\":\"3\",\"titleTruncation\":\"16\",\"descriptionTruncation\":\"40\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.special-projects\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.list-popular-projects',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'11','{\"title\":\"Popular Projects\",\"itemCountPerPage\":\"3\",\"projectType\":\"0\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_project_category_id\":\"\",\"hidden_project_subcategory_id\":\"\",\"hidden_project_subsubcategory_id\":\"\",\"selectProjects\":\"all\",\"showProject\":\"featuredSponsored\",\"projectWidth\":\"217\",\"projectHeight\":\"200\",\"popularType\":\"backed\",\"interval\":\"overall\",\"projectInfo\":[\"title\",\"owner\",\"creationDate\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\",\"location\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.list-popular-projects\"}',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_index' ;
");
        }
    }

    public function backerSuccessPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_backer_success');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_backer_success','Crowdfunding - Backer Success Page',NULL,'','','','1','0','','[\"1\",\"2\",\"3\",\"4\",\"5\",\"7\",\"8\",\"9\"]','no-subject','0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_success' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_success' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[]',NULL from engine4_core_pages where name = 'sitecrowdfunding_backer_success' ;
");
        }
    }

    public function backerFaqPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_index_backers-faq');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_index_backers-faq','Crowdfunding - FAQs for Backers Page',NULL,'FAQs for Backers Page','This page having FAQs for Backers.','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top','0','1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_backers-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_backers-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main','0','2','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_backers-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_backers-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_backers-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_backers-faq' ;
");
        }
    }

    public function ownerFaqPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitecrowdfunding_index_project-owner-faq');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitecrowdfunding_index_project-owner-faq','Crowdfunding - FAQs for Project Owner Page',NULL,'FAQs for Project Owner Page','This page having FAQs for Project Owner.','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top','0','1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_project-owner-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_project-owner-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main','0','2','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_project-owner-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_project-owner-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_project-owner-faq' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'1','',NULL from engine4_core_pages where name = 'sitecrowdfunding_index_project-owner-faq' ;
");
        }
    }

    public function landingPage($reset = false,$slideShowId) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('core_index_index');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('core_index_index','Home Page',NULL,'Home Page','This is the home page.','','0','0','default',NULL,'no-viewer;no-subject','0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'core_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'core_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'core_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'core_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitehomepagevideo.videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"columnHeightWidth\":\"0\",\"videoScreenHeight\":\"1\",\"showStartViewing\":\"1\",\"columnHeight\":\"600\",\"columnWidth\":\"\",\"selectedSlides\":\"$slideShowId\",\"showNextLink\":\"1\",\"showLogo\":\"0\",\"logo\":\"\",\"sitehomepagevideoSignupLoginLink\":\"0\",\"sitehomepagevideoBrowseMenus\":\"0\",\"max\":\"7\",\"sitehomepagevideoFirstImprotantLink\":\"0\",\"sitehomepagevideoFirstTitle\":\"Important Title & Link\",\"sitehomepagevideoFirstUrl\":\"#\",\"sitehomepagevideoHtmlTitle\":\"RAISE FUNDS FOR ANYTHING\",\"sitehomepagevideoHtmlDescription\":\"Combining Social Power to help you achieve your Fundraising goals\",\"sitehomepagevideoTitleColor\":\"0\",\"sitehomepagevideoHowItWorks\":\"0\",\"sitehomepagevideoSignupLoginButton\":\"1\",\"sitehomepagevideoSearchBox\":\"4\",\"showSignupFields\":\"\",\"showTagLine\":\"1\",\"showLeftRightSignupButton\":\"0\",\"playVideoSound\":\"0\",\"nomobile\":\"\",\"title\":\"\",\"name\":\"sitehomepagevideo.videos\"}',NULL from engine4_core_pages where name = 'core_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.featured-fundraiser',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"Featured Fundraiser\",\"titleCount\":true,\"projectOption\":[\"title\",\"owner\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"featured\",\"sponsored\"],\"selectProjects\":\"all\",\"showProject\":\"featuredSponsored\",\"project_ids\":\"\",\"toValues\":\"\",\"projectHeight\":\"450\",\"projectWidth\":\"1250\",\"viewProjectButton\":\"1\",\"viewProjectTitle\":\"View Project\",\"orderby\":null,\"itemCount\":\"5\",\"titleTruncation\":\"30\",\"descriptionTruncation\":\"200\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.featured-fundraiser\"}',NULL from engine4_core_pages where name = 'core_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.best-projects',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"\",\"categoryAtTop\":\"1\",\"category_ids\":[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"9\",\"14\"],\"popularType\":null,\"columnWidth\":\"265\",\"columnHeight\":\"480\",\"projectOption\":[\"title\",\"owner\",\"view\",\"like\",\"comment\",\"endDate\",\"backer\",\"featured\",\"sponsored\",\"location\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"itemCount\":\"20\",\"itemCountPerPage\":\"4\",\"titleTruncation\":\"25\",\"descriptionTruncation\":\"125\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.best-projects\"}',NULL from engine4_core_pages where name = 'core_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.sponsored-categories-with-image',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"height\":\"290\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.sponsored-categories-with-image\"}',NULL from engine4_core_pages where name = 'core_index_index' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecrowdfunding.ajax-based-projects-home',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"Recommended For You\",\"defaultViewType\":\"gridZZZview\",\"ajaxTabs\":[\"random\"],\"projectType\":\"0\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_project_category_id\":\"\",\"hidden_project_subcategory_id\":\"\",\"hidden_project_subsubcategory_id\":\"\",\"gridViewWidth\":\"276\",\"gridViewHeight\":\"510\",\"viewType\":[\"gridZZZview\"],\"projectOption\":[\"title\",\"owner\",\"backer\",\"like\",\"favourite\",\"comment\",\"endDate\",\"location\",\"facebook\",\"twitter\",\"linkedin\"],\"selectProjects\":\"all\",\"daysFilter\":\"20\",\"backedPercentFilter\":\"40\",\"recentOrder\":\"1\",\"likedOrder\":\"2\",\"commentedOrder\":\"3\",\"backedOrder\":\"4\",\"randomOrder\":\"5\",\"showViewMore\":\"0\",\"gridItemCountPerPage\":\"4\",\"listItemCountPerPage\":\"12\",\"titleTruncationGridView\":\"24\",\"titleTruncationListView\":\"40\",\"descriptionTruncation\":\"160\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"1000\",\"truncationLocation\":\"30\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitecrowdfunding.ajax-based-projects-home\"}',NULL from engine4_core_pages where name = 'core_index_index' ;
");
        }
    }

    public function setProjectCategories($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $results = $db->select()
                ->from('engine4_sitecrowdfunding_categories', array('category_id', 'category_name'))
                ->where('cat_dependency =?', 0)
                ->query()
                ->fetchAll();
        $containerCount = 0;
        $widgetCount = 0;
        foreach ($results as $result) {
            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', 'sitecrowdfunding_index_categories-home_category_' . $result['category_id'])
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if ($page_id && $reset) {
                $this->deletePageAndContent($page_id);
                $page_id = false;
            }

            if (!$page_id) {
                $db->insert('engine4_core_pages', array('name' => 'sitecrowdfunding_index_categories-home_category_' . $result['category_id'],
                    'displayname' => 'Crowdfunding - Category - ' . $result['category_name'],
                    'title' => 'Crowdfunding ' . $result['category_name'] . ' Home',
                    'description' => 'This is the Crowdfunding ' . $result['category_name'] . ' home page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

//TOP CONTAINER
                $db->insert('engine4_core_content', array('type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $top_container_id = $db->lastInsertId();

//MAIN CONTAINER
                $db->insert('engine4_core_content', array
                    (
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $main_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
                $db->insert('engine4_core_content', array(
                    'type' =>
                    'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => $containerCount++,
                ));
                $top_middle_id = $db->lastInsertId();

// Top Middle
                $db->insert('engine4_core_content', array(
                    'page_id' =>
                    $page_id,
                    'type' => 'widget',
                    'name' => 'sitecrowdfunding.navigation',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"0","name":"sitecrowdfunding.navigation"}',
                ));
                $db->insert('engine4_core_content', array(
                    'page_id' =>
                    $page_id,
                    'type' => 'widget',
                    'name' => 'sitecrowdfunding.project-categorybanner',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","logo":"public\/admin\/website-background-grey1.jpg","height":"555","categoryHeight":"400","fullWidth":"1","showExplore":"1","titleTruncation":"36","taglineTruncation":"100","nomobile":"","name":"sitecrowdfunding.project-categorybanner"}',
                ));


//RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'left',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $left_container_id = $db->lastInsertId();

//MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $main_middle_id = $db->lastInsertId();
                $db->insert('engine4_core_content', array(
                    'page_id' =>
                    $page_id,
                    'type' => 'widget',
                    'name' => 'sitecrowdfunding.project-categories-navigation',
                    'parent_content_id' => $left_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"orderBy":"category_name","viewDisplayHR":"0","title":"Categories","nomobile":"0","name":"sitecrowdfunding.project-categories-navigation"}',
                ));
                $db->insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget',
                    'name' => 'sitecrowdfunding.project-categories-grid-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"subCategoriesCount":"5","showProjectCount":"0","columnWidth":"225","columnHeight":"200","nomobile":"0","name":"sitecrowdfunding.project-categories-grid-view"}',
                ));
                $db->insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget',
                    'name' => 'sitecrowdfunding.project-carousel',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"' . $result['category_name'] . '","category_id":"' . $result['category_id'] . '","subcategory_id":"0","hidden_project_category_id":"' . $result['category_id'] . '","hidden_project_subcategory_id":"0","hidden_project_subsubcategory_id":"0","projectType":null,"projectOption":["title","owner","creationDate","backer","like","favourite","comment","endDate","featured","sponsored","facebook","twitter","linkedin","googleplus"],"showProject":"","popularType":"random","selectProjects":"all","daysFilter":"20","backedPercentFilter":"50","showPagination":"1","projectWidth":"285","projectHeight":"250","showLink":"1","rowLimit":"3","itemCount":"12","interval":"3500","titleTruncation":"20","truncationLocation":"35","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitecrowdfunding.project-carousel"}',
                ));
            }
        }
    }

}

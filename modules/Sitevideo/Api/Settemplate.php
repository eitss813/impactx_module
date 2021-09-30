<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settemplate.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Api_Settemplate extends Core_Api_Abstract {

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

    public function getCurrentActivateTheme($default = false) {

        if ($default)
            return $this->setLayoutAccordingToTheme();

        if (isset($_POST['sitevideo_setlayyoutpages']) && $_POST['sitevideo_setlayyoutpages'] == 1) {
            return 1;
        } else if (isset($_POST['sitevideo_setlayyoutpages']) && $_POST['sitevideo_setlayyoutpages'] == 3) {
            return 3;
        } else if (isset($_POST['sitevideo_setlayyoutpages']) && $_POST['sitevideo_setlayyoutpages'] == 2) {
            return 2;
        }

        return false;
    }

    public function setLayoutAccordingToTheme() {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $theme = '';
        $themeArray = $view->layout()->themes;
        if (isset($themeArray[0])) {
            $theme = $view->layout()->themes[0];
        }
        $file = APPLICATION_PATH . '/application/themes/' . $theme . '/constants.css';
        $width = Engine_Api::_()->seaocore()->getValueOfCssVariables($file, 'theme_content_width');
        if ($width >= 1200) {
            $setLayout = 3;
        } else if (1100 > $width) {
            $setLayout = 1;
        } else if (1100 >= $width && 1200 > $width) {
            $setLayout = 2;
        }

        return $setLayout;
    }

    public function playlistPlayallPage($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_playlist_playall');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_playall','Advanced Videos – Playlist Play All Videos Page',NULL,'Playlist Play All Videos Page','This page will play all videos of a playlist','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.playlist-playall',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','{\"title\":\"\",\"playlistOptions\":\"0\",\"height\":\"540\",\"titleTruncation\":\"35\",\"nomobile\":\"0\",\"name\":\"sitevideo.playlist-playall\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
        }
    }

    public function playlistCreatePage($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_playlist_create');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_create','Advanced Videos - Create Playlists Page',NULL,'Playlists Create Page','This page is used to create the playlist.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1','',NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_create','Advanced Videos - Create Playlists Page',NULL,'Playlists Create Page','This page is used to create the playlist.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1','',NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_create','Advanced Videos - Create Playlists Page',NULL,'Playlists Create Page','This page is used to create the playlist.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1','',NULL from engine4_core_pages where name = 'sitevideo_playlist_create' ;
");
            }
        }
        //END PLAYLIST CREATION PAGE
    }

    public function playlistBrowsePage($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_playlist_browse');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (!$page_id) {
            // Insert page
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_browse','Advanced Videos - Browse Playlists Page',NULL,'Playlists Browse Page','This page lists playlist user\'s playlists.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.playlist-search',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"formElements\":[\"playlistelement\",\"videoelement\",\"membername\"],\"playlistWidth\":\"250\",\"videoWidth\":\"250\",\"memberNameWidth\":\"200\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.playlist-search\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browse-playlist',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"viewType\":[\"gridView\",\"listView\"],\"viewFormat\":\"listView\",\"playlistGridViewWidth\":\"283\",\"playlistGridViewHeight\":\"210\",\"playlistOption\":[\"owner\",\"videosCount\",\"like\"],\"itemCountPerPage\":\"10\",\"show_content\":\"2\",\"titleTruncation\":\"67\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-playlist\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"Most Liked Videos\",\"itemCountPerPage\":\"2\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"200\",\"videoHeight\":\"250\",\"popularType\":\"like\",\"interval\":\"month\",\"videoInfo\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"15\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"title\":\"Most Rated Videos\",\"itemCountPerPage\":\"2\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"200\",\"videoHeight\":\"265\",\"popularType\":\"rating\",\"interval\":\"overall\",\"videoInfo\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"rating\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"15\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_browse','Advanced Videos - Browse Playlists Page',NULL,'Playlists Browse Page','This page lists playlist user\'s playlists.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.playlist-search',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"formElements\":[\"playlistelement\",\"videoelement\",\"membername\"],\"playlistWidth\":\"200\",\"videoWidth\":\"200\",\"memberNameWidth\":\"200\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.playlist-search\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browse-playlist',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"viewType\":[\"gridView\",\"listView\"],\"viewFormat\":\"listView\",\"playlistGridViewWidth\":\"269\",\"playlistGridViewHeight\":\"210\",\"playlistOption\":[\"owner\",\"videosCount\",\"like\"],\"itemCountPerPage\":\"10\",\"show_content\":\"2\",\"titleTruncation\":\"57\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-playlist\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"Most Liked Videos\",\"itemCountPerPage\":\"2\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"200\",\"videoHeight\":\"274\",\"popularType\":\"like\",\"interval\":\"month\",\"videoInfo\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"15\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"title\":\"Most Rated Videos\",\"itemCountPerPage\":\"2\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"200\",\"videoHeight\":\"274\",\"popularType\":\"rating\",\"interval\":\"overall\",\"videoInfo\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"rating\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"15\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_browse','Advanced Videos - Browse Playlists Page',NULL,'Playlists Browse Page','This page lists playlist user\'s playlists.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.playlist-search',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"formElements\":[\"playlistelement\",\"videoelement\",\"membername\"],\"playlistWidth\":\"200\",\"videoWidth\":\"200\",\"memberNameWidth\":\"200\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.playlist-search\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browse-playlist',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'7','{\"viewType\":[\"gridView\",\"listView\"],\"viewFormat\":\"listView\",\"playlistGridViewWidth\":\"230\",\"playlistGridViewHeight\":\"210\",\"playlistOption\":[\"owner\",\"videosCount\",\"like\"],\"itemCountPerPage\":\"10\",\"show_content\":\"2\",\"titleTruncation\":\"55\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-playlist\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"Most Liked Videos\",\"itemCountPerPage\":\"2\",\"videoType\":null,\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"186\",\"videoHeight\":\"245\",\"popularType\":\"like\",\"interval\":\"month\",\"videoInfo\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"15\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"title\":\"Most Rated Videos\",\"itemCountPerPage\":\"2\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"186\",\"videoHeight\":\"274\",\"popularType\":\"rating\",\"interval\":\"overall\",\"videoInfo\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"rating\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"15\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_browse' ;
");
            }
        }
        //END PLAYLIST BROWSE PAGE
    }

    public function tagCloudVideo($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_tagscloud');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (empty($page_id)) {
            //CREATE PAGE
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_tagscloud','Advanced Videos - Video Tags',NULL,'Popular Video Tags','This is the video tags page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'4','',NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','seaocore.scroll-top',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'5','',NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-video',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'6','',NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_tagscloud','Advanced Videos - Video Tags',NULL,'Popular Video Tags','This is the video tags page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'4','',NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','seaocore.scroll-top',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'5','',NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-video',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'6','',NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_tagscloud','Advanced Videos - Video Tags',NULL,'Popular Video Tags','This is the video tags page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'4','',NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','seaocore.scroll-top',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'5','',NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-video',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'6','',NULL from engine4_core_pages where name = 'sitevideo_video_tagscloud' ;
");
            }
        }
    }

    public function tagCloudChannel($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_channel_tagscloud');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (empty($page_id)) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_tagscloud','Advanced Videos - Channel Tags',NULL,'Popular Channel Tags','This is the channel tags page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'0','',NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','seaocore.scroll-top',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1','',NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'2','',NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_tagscloud','Advanced Videos - Channel Tags',NULL,'Popular Channel Tags','This is the channel tags page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'0','',NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','seaocore.scroll-top',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1','',NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'2','',NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_tagscloud','Advanced Videos - Channel Tags',NULL,'Popular Channel Tags','This is the channel tags page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'0','',NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','seaocore.scroll-top',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1','',NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'2','',NULL from engine4_core_pages where name = 'sitevideo_channel_tagscloud' ;
");
            }
        }
    }

    public function topicView($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_topic_view');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (empty($page_id)) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_topic_view','Advanced Videos - Discussion Topic View Page',NULL,'View Channel Discussion Topic','This is the view page for a channel discussion.','','0','0','',NULL,'subject=sitevideo_topic','0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main','0','1','',NULL from engine4_core_pages where name = 'sitevideo_topic_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3','',NULL from engine4_core_pages where name = 'sitevideo_topic_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.discussion-content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'1','',NULL from engine4_core_pages where name = 'sitevideo_topic_view' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_topic_view','Advanced Videos - Discussion Topic View Page',NULL,'View Channel Discussion Topic','This is the view page for a channel discussion.','','0','0','',NULL,'subject=sitevideo_topic','0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main','0','1','',NULL from engine4_core_pages where name = 'sitevideo_topic_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3','',NULL from engine4_core_pages where name = 'sitevideo_topic_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.discussion-content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'1','',NULL from engine4_core_pages where name = 'sitevideo_topic_view' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_topic_view','Advanced Videos - Discussion Topic View Page',NULL,'View Channel Discussion Topic','This is the view page for a channel discussion.','','0','0','',NULL,'subject=sitevideo_topic','0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main','0','1','',NULL from engine4_core_pages where name = 'sitevideo_topic_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3','',NULL from engine4_core_pages where name = 'sitevideo_topic_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.discussion-content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'1','',NULL from engine4_core_pages where name = 'sitevideo_topic_view' ;
");
            }
        }
    }

    public function playlistViewPage($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_playlist_view');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (empty($page_id)) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_view','Advanced Videos - View Playlist',NULL,'View Playlist','This page is used to view a playlist','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.playlist-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"show_content\":\"2\",\"orderBy\":\"creation_date\",\"itemCountPerPage\":\"3\",\"nomobile\":\"0\",\"name\":\"sitevideo.playlist-view\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Viewed Videos\",\"itemCountPerPage\":\"5\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"220\",\"videoHeight\":\"250\",\"popularType\":\"view\",\"interval\":\"month\",\"videoInfo\":[\"title\",\"owner\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_view','Advanced Videos - View Playlist',NULL,'View Playlist','This page is used to view a playlist','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.playlist-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"show_content\":\"2\",\"orderBy\":\"creation_date\",\"itemCountPerPage\":\"3\",\"nomobile\":\"0\",\"name\":\"sitevideo.playlist-view\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Viewed Videos\",\"itemCountPerPage\":\"5\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"186\",\"videoHeight\":\"274\",\"popularType\":\"view\",\"interval\":\"month\",\"videoInfo\":[\"title\",\"owner\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_view','Advanced Videos - View Playlist',NULL,'View Playlist','This page is used to view a playlist','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.playlist-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"show_content\":\"2\",\"orderBy\":\"creation_date\",\"itemCountPerPage\":\"3\",\"nomobile\":\"0\",\"name\":\"sitevideo.playlist-view\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Viewed Videos\",\"itemCountPerPage\":\"5\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"186\",\"videoHeight\":\"274\",\"popularType\":\"view\",\"interval\":\"month\",\"videoInfo\":[\"title\",\"owner\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_view' ;
");
            }
        }
    }

    public function postNewVideo($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_create');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (empty($page_id)) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_create','Advanced Videos - Post New Video',NULL,'Post New Video','This page is used to post new video','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'0','{}',NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_create','Advanced Videos - Post New Video',NULL,'Post New Video','This page is used to post new video','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'0','{}',NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_create','Advanced Videos - Post New Video',NULL,'Post New Video','This page is used to post new video','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'0','{}',NULL from engine4_core_pages where name = 'sitevideo_video_create' ;
");
            }
        }
    }

    public function editVideo($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_edit');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (empty($page_id)) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_edit','Advanced Videos - Video Edit Page',NULL,'Video Edit Page','This page is used to edit the video','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'0','',NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'1','{}',NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_edit','Advanced Videos - Video Edit Page',NULL,'Video Edit Page','This page is used to edit the video','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'0','',NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'1','{}',NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_edit','Advanced Videos - Video Edit Page',NULL,'Video Edit Page','This page is used to edit the video','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'0','',NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=3 limit 1),'1','{}',NULL from engine4_core_pages where name = 'sitevideo_video_edit' ;
");
            }
        }
    }

    public function channelCreate($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_index_create');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_create','Advanced Videos - Create Channel',NULL,'Create Channel','This page is used to create the channel.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','[\"{}\"]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_create','Advanced Videos - Create Channel',NULL,'Create Channel','This page is used to create the channel.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','[\"{}\"]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_create','Advanced Videos - Create Channel',NULL,'Create Channel','This page is used to create the channel.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','[\"{}\"]',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_index_create' ;
");
            }
        }

// Create Channel PAGE END
    }

    public function channelEdit($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_channel_edit');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_edit','Advanced Videos - Edit Channel',NULL,'Edit Channel','This page is used to edit the channel.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','[\"{}\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_edit','Advanced Videos - Edit Channel',NULL,'Edit Channel','This page is used to edit the channel.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','[\"{}\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_edit','Advanced Videos - Edit Channel',NULL,'Edit Channel','This page is used to edit the channel.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','[\"{}\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[]',NULL from engine4_core_pages where name = 'sitevideo_channel_edit' ;
");
            }
        }
    }

    public function watchLaterManage($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_watchlater_manage');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_watchlater_manage','Advanced Videos - My Watchlaters Page',NULL,'My Watchlaters Page','This page lists watchlater a user\'s watchlaters.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'4',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-watchlaters-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=4 limit 1),'5','{}',NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_watchlater_manage','Advanced Videos - My Watchlaters Page',NULL,'My Watchlaters Page','This page lists watchlater a user\'s watchlaters.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'4',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-watchlaters-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=4 limit 1),'5','{}',NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_watchlater_manage','Advanced Videos - My Watchlaters Page',NULL,'My Watchlaters Page','This page lists watchlater a user\'s watchlaters.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'0',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=0 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'3',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=1 limit 1),'4',NULL,NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-watchlaters-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=4 limit 1),'5','{}',NULL from engine4_core_pages where name = 'sitevideo_watchlater_manage' ;
");
            }
        }
//WATCHLATER MANAGE PAGE END
    }

    public function subscriptionManage($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_subscription_manage');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_subscription_manage','Advanced Videos - My Subscriptions Page',NULL,'My Subscriptions Page','This page lists channel a user\'s subscribed channels.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-subscriptions-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'5','{\"title\":\"\",\"itemCountPerPage\":\"15\",\"orderBy\":\"creation_date\",\"show_content\":\"2\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-subscriptions-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_subscription_manage','Advanced Videos - My Subscriptions Page',NULL,'My Subscriptions Page','This page lists channel a user\'s subscribed channels.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-subscriptions-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'5','{\"title\":\"\",\"itemCountPerPage\":\"15\",\"orderBy\":\"creation_date\",\"show_content\":\"2\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-subscriptions-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_subscription_manage','Advanced Videos - My Subscriptions Page',NULL,'My Subscriptions Page','This page lists channel a user\'s subscribed channels.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-subscriptions-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'5','{\"title\":\"\",\"itemCountPerPage\":\"15\",\"orderBy\":\"creation_date\",\"show_content\":\"2\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-subscriptions-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_subscription_manage' ;
");
            }
        }
        //SUBSCRIPTION MANAGE PAGE END
    }

    public function browseVideo($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_browse');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_browse','Advanced Videos - Browse Videos',NULL,'Browse Videos','This is the video browse page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','left',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'4','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browse-videos-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"titleCount\":true,\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"defaultViewType\":\"gridView\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"rating\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"videoViewWidth\":\"352\",\"videoViewHeight\":\"330\",\"gridViewWidth\":\"283\",\"gridViewHeight\":\"265\",\"show_content\":\"1\",\"orderby\":\"featuredSponsored\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"0\",\"titleTruncation\":\"77\",\"titleTruncationGridNVideoView\":\"32\",\"descriptionTruncation\":\"50\",\"itemCountPerPage\":\"18\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categories-navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'8','{\"orderBy\":\"cat_order\",\"viewDisplayHR\":\"0\",\"title\":\"Categories\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-categories-navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.search-video-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'7','{\"title\":\"Search Videos\",\"titleCount\":true,\"viewType\":\"vertical\",\"showAllCategories\":\"1\",\"locationDetection\":\"0\",\"nomobile\":\"0\",\"name\":\"sitevideo.search-video-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-video',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'9','{\"title\":\"Popular Tags\",\"titleCount\":true,\"orderingType\":\"1\",\"itemCount\":\"25\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.tagcloud-sitevideo-video\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.post-new-video',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"upload_button\":\"1\",\"upload_button_title\":\"Post New Video\",\"nomobile\":\"0\",\"name\":\"sitevideo.post-new-video\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_browse','Advanced Videos - Browse Videos',NULL,'Browse Videos','This is the video browse page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','left',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'4','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browse-videos-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"titleCount\":true,\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"defaultViewType\":\"gridView\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"rating\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"videoViewWidth\":\"329\",\"videoViewHeight\":\"361\",\"gridViewWidth\":\"269\",\"gridViewHeight\":\"255\",\"show_content\":\"1\",\"orderby\":\"featuredSponsored\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"0\",\"titleTruncation\":\"63\",\"titleTruncationGridNVideoView\":\"25\",\"descriptionTruncation\":\"500\",\"itemCountPerPage\":\"18\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categories-navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'8','{\"orderBy\":\"cat_order\",\"viewDisplayHR\":\"0\",\"title\":\"Categories\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-categories-navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.search-video-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'7','{\"title\":\"Search Videos\",\"titleCount\":true,\"viewType\":\"vertical\",\"showAllCategories\":\"1\",\"locationDetection\":\"0\",\"nomobile\":\"0\",\"name\":\"sitevideo.search-video-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-video',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'9','{\"title\":\"Popular Tags\",\"titleCount\":true,\"orderingType\":\"1\",\"itemCount\":\"25\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.tagcloud-sitevideo-video\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.post-new-video',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"upload_button\":\"1\",\"upload_button_title\":\"Post New Video\",\"nomobile\":\"0\",\"name\":\"sitevideo.post-new-video\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_browse','Advanced Videos - Browse Videos',NULL,'Browse Videos','This is the video browse page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','left',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'4','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browse-videos-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"titleCount\":true,\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"defaultViewType\":\"gridView\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"rating\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoType\":null,\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"videoViewWidth\":\"270\",\"videoViewHeight\":\"371\",\"gridViewWidth\":\"231\",\"gridViewHeight\":\"242\",\"show_content\":\"1\",\"orderby\":\"featuredSponsored\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"0\",\"titleTruncation\":\"54\",\"titleTruncationGridNVideoView\":\"21\",\"descriptionTruncation\":\"400\",\"itemCountPerPage\":\"18\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitevideo.navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categories-navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'8','{\"orderBy\":\"cat_order\",\"viewDisplayHR\":\"0\",\"title\":\"Categories\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-categories-navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.search-video-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'7','{\"title\":\"Search Videos\",\"titleCount\":true,\"viewType\":\"vertical\",\"showAllCategories\":\"1\",\"locationDetection\":\"0\",\"nomobile\":\"0\",\"name\":\"sitevideo.search-video-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-video',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'9','{\"title\":\"Popular Tags\",\"titleCount\":true,\"orderingType\":\"1\",\"itemCount\":\"25\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.tagcloud-sitevideo-video\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.post-new-video',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"upload_button\":\"1\",\"upload_button_title\":\"Post New Video\",\"nomobile\":\"0\",\"name\":\"sitevideo.post-new-video\"}',NULL from engine4_core_pages where name = 'sitevideo_video_browse' ;
");
            }
        }
    }

    public function pinboardBrowseVideo($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_pinboard');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_pinboard','Advanced Videos - Browse Video\'s Pinboard View Page',NULL,'Browse Video\'s Pinboard View','This is the browse videos pinboard view page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.pinboard-browse-videos-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"like\",\"comment\",\"view\",\"duration\",\"rating\",\"location\"],\"videoType\":\"\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"userComment\":\"1\",\"autoload\":\"1\",\"defaultLoadingImage\":\"1\",\"itemWidth\":\"385\",\"show_buttons\":[\"comment\",\"like\",\"favourite\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\",\"pinit\"],\"withoutStretch\":\"0\",\"orderby\":\"featured\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"0\",\"truncationLocation\":\"35\",\"titleTruncation\":\"41\",\"descriptionTruncation\":\"0\",\"itemCountPerPage\":\"12\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_pinboard','Advanced Videos - Browse Video\'s Pinboard View Page',NULL,'Browse Video\'s Pinboard View','This is the browse videos pinboard view page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.pinboard-browse-videos-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"like\",\"comment\",\"view\",\"duration\",\"rating\",\"location\"],\"videoType\":\"\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"userComment\":\"1\",\"autoload\":\"1\",\"defaultLoadingImage\":\"1\",\"itemWidth\":\"366\",\"show_buttons\":[\"comment\",\"like\",\"favourite\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"withoutStretch\":\"0\",\"orderby\":\"featured\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"0\",\"truncationLocation\":\"35\",\"titleTruncation\":\"41\",\"descriptionTruncation\":\"0\",\"itemCountPerPage\":\"12\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_pinboard','Advanced Videos - Browse Video\'s Pinboard View Page',NULL,'Browse Video\'s Pinboard View','This is the browse videos pinboard view page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.pinboard-browse-videos-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"like\",\"comment\",\"view\",\"duration\",\"rating\",\"location\"],\"videoType\":null,\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"userComment\":\"1\",\"autoload\":\"1\",\"defaultLoadingImage\":\"1\",\"itemWidth\":\"316\",\"show_buttons\":[\"comment\",\"like\",\"favourite\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"withoutStretch\":\"0\",\"orderby\":\"featured\",\"detactLocation\":\"0\",\"defaultLocationDistance\":\"0\",\"truncationLocation\":\"25\",\"titleTruncation\":\"30\",\"descriptionTruncation\":\"0\",\"itemCountPerPage\":\"12\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_pinboard' ;
");
            }
        }
    }

    public function pinboardBrowseChannel($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_channel_pinboard');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_pinboard','Advanced Videos - Browse Channel\'s Pinboard View Page',NULL,'Browse Channel\'s Pinboard View','This is the browse channels pinboard view page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.pinboard-browse-channels-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"channelOption\":[\"title\",\"owner\",\"numberOfVideos\",\"like\",\"comment\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"userComment\":\"1\",\"autoload\":\"1\",\"defaultLoadingImage\":\"1\",\"itemWidth\":\"385\",\"withoutStretch\":\"0\",\"show_buttons\":[\"comment\",\"like\",\"favourite\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\",\"pinit\"],\"orderby\":\"featured\",\"titleTruncation\":\"37\",\"descriptionTruncation\":\"0\",\"itemCountPerPage\":\"12\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_pinboard','Advanced Videos - Browse Channel\'s Pinboard View Page',NULL,'Browse Channel\'s Pinboard View','This is the browse channels pinboard view page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.pinboard-browse-channels-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"channelOption\":[\"title\",\"owner\",\"numberOfVideos\",\"like\",\"comment\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"userComment\":\"1\",\"autoload\":\"1\",\"defaultLoadingImage\":\"1\",\"itemWidth\":\"366\",\"withoutStretch\":\"0\",\"show_buttons\":[\"comment\",\"like\",\"favourite\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\",\"pinit\"],\"orderby\":\"featured\",\"titleTruncation\":\"37\",\"descriptionTruncation\":\"0\",\"itemCountPerPage\":\"12\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_pinboard','Advanced Videos - Browse Channel\'s Pinboard View Page',NULL,'Browse Channel\'s Pinboard View','This is the browse channels pinboard view page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.pinboard-browse-channels-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"channelOption\":[\"title\",\"owner\",\"numberOfVideos\",\"like\",\"comment\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"userComment\":\"1\",\"autoload\":\"1\",\"defaultLoadingImage\":\"1\",\"itemWidth\":\"316\",\"withoutStretch\":\"0\",\"show_buttons\":[\"comment\",\"like\",\"favourite\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\",\"pinit\"],\"orderby\":\"featured\",\"titleTruncation\":\"30\",\"descriptionTruncation\":\"0\",\"itemCountPerPage\":\"12\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_pinboard' ;
");
            }
        }
    }

    public function getFullThemeValue() {
        //Start work for responsive theme/media query
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $theme = '';
        $themeArray = $view->layout()->themes;
        if (isset($themeArray[0])) {
            $theme = $view->layout()->themes[0];
        }
        $fullWidth = 0;
        if ($theme == 'spectacular' || $theme == 'captivate') {
            $fullWidth = 1;
        }
        return $fullWidth;
    }

    public function channelHome($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_index_index');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        $fullWidth = $this->getFullThemeValue();
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_index','Advanced Videos - Channels Home Page',NULL,'Channels Home Page','This is channel home page.','','0','0','',NULL,'no-subject','0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'11','{\"title\":\"Most Rated Channels\",\"itemCountPerPage\":\"3\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"168\",\"channelWidth\":\"218\",\"popularType\":\"rating\",\"interval\":\"overall\",\"channelInfo\":[\"favourite\",\"numberOfVideos\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'12','{\"title\":\"Most Liked Channels\",\"itemCountPerPage\":\"3\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"250\",\"channelWidth\":\"218\",\"popularType\":\"like\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.special-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"title\":\"Channel of the Day\",\"channel_ids\":\"\",\"toValues\":\"165\",\"starttime\":\"2016-02-01 01:00:00\",\"endtime\":\"2017-03-31 01:00:00\",\"columnWidth\":\"218\",\"columnHeight\":\"250\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"itemCount\":\"1\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.special-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.featured-channels-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'4','{\"title\":\"\",\"channelOption\":[\"title\",\"subscribe\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"showTagline1\":\"1\",\"showTagline2\":\"1\",\"showTaglineDesc\":\"1\",\"showNavigationButton\":\"1\",\"fullWidth\":\"$fullWidth\",\"popularType\":\"creation\",\"interval\":\"overall\",\"slideshow_height\":\"500\",\"delay\":\"4500\",\"slidesLimit\":\"10\",\"titleTruncation\":\"50\",\"taglineTruncation\":\"50\",\"descriptionTruncation\":\"100\",\"nomobile\":\"0\",\"name\":\"sitevideo.featured-channels-slideshow\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-recently-view-random-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'7','{\"title\":\"\",\"viewType\":[\"videoZZZview\",\"gridZZZview\",\"listZZZview\"],\"defaultViewType\":\"videoZZZview\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"rating\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"ajaxTabs\":[\"mostZZZrecent\",\"mostZZZliked\",\"mostZZZsubscribed\",\"mostZZZcommented\",\"mostZZZrated\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"videoViewWidth\":\"895\",\"videoViewHeight\":\"330\",\"gridViewWidth\":\"283\",\"gridViewHeight\":\"255\",\"recent_order\":\"5\",\"liked_order\":\"1\",\"subscribed_order\":\"2\",\"commented_order\":\"3\",\"rated_order\":\"4\",\"favourites_order\":\"6\",\"random_order\":\"7\",\"showViewMore\":\"1\",\"itemCountPerPage\":\"5\",\"gridItemCountPerPage\":\"18\",\"listItemCountPerPage\":\"6\",\"titleTruncation\":\"58\",\"titleTruncationGridNVideoView\":\"29\",\"descriptionTruncation\":\"400\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-recently-view-random-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.create-new-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"\",\"name\":\"sitevideo.create-new-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_index','Advanced Videos - Channels Home Page',NULL,'Channels Home Page','This is channel home page.','','0','0','',NULL,'no-subject','0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'11','{\"title\":\"Most Rated Channels\",\"itemCountPerPage\":\"3\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"260\",\"channelWidth\":\"216\",\"popularType\":\"rating\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'12','{\"title\":\"Most Liked Channels\",\"itemCountPerPage\":\"3\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"260\",\"channelWidth\":\"216\",\"popularType\":\"like\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.special-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"title\":\"Channel of the Day\",\"channel_ids\":\"\",\"toValues\":\"165\",\"starttime\":\"2016-02-01 01:00:00\",\"endtime\":\"2017-03-31 01:00:00\",\"columnWidth\":\"216\",\"columnHeight\":\"255\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"itemCount\":\"1\",\"titleTruncation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.special-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.featured-channels-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"channelOption\":[\"title\",\"subscribe\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"showTagline1\":\"1\",\"showTagline2\":\"1\",\"showTaglineDesc\":\"1\",\"showNavigationButton\":\"1\",\"fullWidth\":\"0\",\"popularType\":\"creation\",\"interval\":\"overall\",\"slideshow_height\":\"500\",\"delay\":\"4500\",\"slidesLimit\":\"10\",\"titleTruncation\":\"50\",\"taglineTruncation\":\"50\",\"descriptionTruncation\":\"100\",\"nomobile\":\"0\",\"name\":\"sitevideo.featured-channels-slideshow\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-recently-view-random-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"\",\"viewType\":[\"videoZZZview\",\"gridZZZview\",\"listZZZview\"],\"defaultViewType\":\"videoZZZview\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"rating\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"ajaxTabs\":[\"mostZZZrecent\",\"mostZZZliked\",\"mostZZZsubscribed\",\"mostZZZcommented\",\"mostZZZrated\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"videoViewWidth\":\"847\",\"videoViewHeight\":\"330\",\"gridViewWidth\":\"269\",\"gridViewHeight\":\"255\",\"recent_order\":\"5\",\"liked_order\":\"1\",\"subscribed_order\":\"2\",\"commented_order\":\"3\",\"rated_order\":\"4\",\"favourites_order\":\"6\",\"random_order\":\"7\",\"showViewMore\":\"1\",\"itemCountPerPage\":\"5\",\"gridItemCountPerPage\":\"18\",\"listItemCountPerPage\":\"6\",\"titleTruncation\":\"54\",\"titleTruncationGridNVideoView\":\"24\",\"descriptionTruncation\":\"400\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-recently-view-random-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.create-new-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"\",\"name\":\"sitevideo.create-new-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_index','Advanced Videos - Channels Home Page',NULL,'Channels Home Page','This is channel home page.','','0','0','',NULL,'no-subject','0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]','' from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'11','{\"title\":\"Most Rated Channels\",\"itemCountPerPage\":\"3\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"255\",\"channelWidth\":\"186\",\"popularType\":\"rating\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'12','{\"title\":\"Most Liked Channels\",\"itemCountPerPage\":\"3\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"260\",\"channelWidth\":\"186\",\"popularType\":\"like\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.special-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"title\":\"Channel of the Day\",\"channel_ids\":\"\",\"toValues\":\"165,5\",\"starttime\":\"2016-02-01 01:00:00\",\"endtime\":\"2017-03-31 01:00:00\",\"columnWidth\":\"186\",\"columnHeight\":\"258\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"itemCount\":\"1\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.special-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.featured-channels-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"channelOption\":[\"title\",\"subscribe\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"showTagline1\":\"1\",\"showTagline2\":\"1\",\"showTaglineDesc\":\"1\",\"showNavigationButton\":\"1\",\"fullWidth\":\"0\",\"popularType\":\"creation\",\"interval\":\"overall\",\"slideshow_height\":\"500\",\"delay\":\"4500\",\"slidesLimit\":\"10\",\"titleTruncation\":\"50\",\"taglineTruncation\":\"50\",\"descriptionTruncation\":\"100\",\"nomobile\":\"0\",\"name\":\"sitevideo.featured-channels-slideshow\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-recently-view-random-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"\",\"viewType\":[\"videoZZZview\",\"gridZZZview\",\"listZZZview\"],\"defaultViewType\":\"videoZZZview\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"rating\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"ajaxTabs\":[\"mostZZZrecent\",\"mostZZZliked\",\"mostZZZsubscribed\",\"mostZZZcommented\",\"mostZZZrated\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"videoViewWidth\":\"730\",\"videoViewHeight\":\"330\",\"gridViewWidth\":\"231\",\"gridViewHeight\":\"255\",\"recent_order\":\"5\",\"liked_order\":\"1\",\"subscribed_order\":\"2\",\"commented_order\":\"3\",\"rated_order\":\"4\",\"favourites_order\":\"6\",\"random_order\":\"7\",\"showViewMore\":\"1\",\"itemCountPerPage\":\"5\",\"gridItemCountPerPage\":\"18\",\"listItemCountPerPage\":\"6\",\"titleTruncation\":\"48\",\"titleTruncationGridNVideoView\":\"24\",\"descriptionTruncation\":\"400\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-recently-view-random-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.create-new-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"\",\"name\":\"sitevideo.create-new-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_index' ;
");
            }
        }
    }

    public function channelView($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_channel_view');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        $fullWidth = $this->getFullThemeValue();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'advancedactivity')
                ->where('enabled = ?', 1);
        $is_advancedactivity_object = $select->query()->fetchObject();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitecontentcoverphoto')
                ->where('enabled = ?', 1);
        $is_sitecontentcoverphoto_object = $select->query()->fetchObject();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitealbum')
                ->where('enabled = ?', 1);
        $is_sitealbum_object = $select->query()->fetchObject();

        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_view','Advanced Videos - Channel Profile',NULL,'Channel Profile',' This is the main view page of an channel.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.container-tabs',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"max\":\"8\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"core.container-tabs\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.overview-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'8','{\"title\":\"Overview\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.overview-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                if ($is_sitealbum_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-photos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'11','{\"title\":\"Photos\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"showPhotosInJustifiedView\":\"1\",\"rowHeight\":\"205\",\"maxRowHeight\":\"0\",\"margin\":\"5\",\"lastRow\":\"justify\",\"width\":\"240\",\"height\":\"200\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-photos\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
    ");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-photos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'11','{\"title\":\"Photos\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"showPhotosInJustifiedView\":\"0\",\"rowHeight\":\"205\",\"maxRowHeight\":\"0\",\"margin\":\"5\",\"lastRow\":\"justify\",\"width\":\"240\",\"height\":\"200\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-photos\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
    ");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'10','{\"title\":\"Videos\",\"itemCountPerPage\":\"10\",\"margin_video\":\"2\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"ratings\",\"favourite\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoHeight\":\"265\",\"videoWidth\":\"283\",\"show_content\":\"2\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-view\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.discussion-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'12','{\"title\":\"Discussions\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.discussion-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-favourite',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'20','{\"title\":\"People Who Favourite This\",\"titleCount\":true,\"height\":\"100\",\"itemCount\":\"5\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-favourite\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                if ($is_sitecontentcoverphoto_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecontentcoverphoto.content-cover-photo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"modulename\":\"sitevideo_channel\",\"showContent_0\":\"\",\"showContent_siteevent_event\":\"\",\"showContent_album\":\"\",\"showContent_sitevideo_channel\":[\"mainPhoto\",\"title\",\"totalVideos\",\"likeCount\",\"commentCount\",\"subscribe\",\"rating\",\"optionsButton\",\"shareOptions\",\"uploadVideos\"],\"profile_like_button\":\"1\",\"columnHeight\":\"500\",\"contentFullWidth\":\"$fullWidth\",\"sitecontentcoverphotoChangeTabPosition\":\"1\",\"contacts\":\"\",\"showMemberLevelBasedPhoto\":\"1\",\"emailme\":\"1\",\"editFontColor\":\"1\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitecontentcoverphoto.content-cover-photo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.profile-breadcrumb',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"name\":\"sitevideo.profile-breadcrumb\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;");
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.top-content-of-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'5','',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-subscribers',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'13','{\"title\":\"Subscribers\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"height\":\"160\",\"width\":\"160\",\"itemCount\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-subscribers\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-like',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'19','{\"title\":\"People Who Like This\",\"titleCount\":true,\"height\":\"100\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-like\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.your-stuff',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'21','{\"statistics\":[\"videocount\",\"likedvideocount\",\"ratedvideocount\",\"favvideocount\",\"watchlatercount\",\"playlistcount\",\"channelscreated\",\"channelsliked\",\"channelsubscribed\",\"channelsrated\",\"channelsfavourited\"],\"title\":\"Your Stuff\",\"nomobile\":\"0\",\"name\":\"sitevideo.your-stuff\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                if ($is_advancedactivity_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','advancedactivity.home-feeds',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'7','{\"title\":\"What\'s New\",\"showFeeds\":1,\"advancedactivity_tabs\":[\"welcome\",\"aaffeed\",\"twitter\"],\"showTabs\":\"0\",\"loadByAjax\":\"0\",\"showScrollTopButton\":\"1\",\"widthphotoattachment\":\"618\",\"width1\":\"608\",\"width2\":\"608\",\"height2\":\"350\",\"width3big\":\"608\",\"height3big\":\"300\",\"width3small\":\"299\",\"height3small\":\"200\",\"width4big\":\"608\",\"height4big\":\"250\",\"width4small\":\"195\",\"height4small\":\"100\",\"width5big\":\"299\",\"height5big\":\"200\",\"width5small\":\"195\",\"height5small\":\"130\",\"nomobile\":\"0\",\"name\":\"advancedactivity.home-feeds\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','activity.feed',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'7','{\"title\":\"What\'s New\",\"showFeeds\":1,\"advancedactivity_tabs\":[\"welcome\",\"aaffeed\",\"twitter\"],\"showTabs\":\"0\",\"loadByAjax\":\"0\",\"showScrollTopButton\":\"1\",\"widthphotoattachment\":\"618\",\"width1\":\"608\",\"width2\":\"608\",\"height2\":\"350\",\"width3big\":\"608\",\"height3big\":\"300\",\"width3small\":\"299\",\"height3small\":\"200\",\"width4big\":\"608\",\"height4big\":\"250\",\"width4small\":\"195\",\"height4small\":\"100\",\"width5big\":\"299\",\"height5big\":\"200\",\"width5small\":\"195\",\"height5small\":\"130\",\"nomobile\":\"0\",\"name\":\"activity.feed\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.quick-specification-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'18','{\"title\":\"Quick Information\",\"titleCount\":true,\"itemCount\":\"3\",\"nomobile\":\"0\",\"name\":\"sitevideo.quick-specification-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.information-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'16','{\"title\":\"Channel Information\",\"titleCount\":true,\"showContent\":[\"totalVideos\",\"creationDate\",\"likeCount\",\"commentCount\",\"socialShare\",\"categoryLink\"],\"nomobile\":\"0\",\"name\":\"sitevideo.information-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.create-new-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'15','{\"title\":\"\",\"name\":\"sitevideo.create-new-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.specification-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'9','{\"title\":\"Information\",\"titleCount\":true,\"loaded_by_ajax\":1}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.share-via-badge',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'17','{\"title\":\"\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_view','Advanced Videos - Channel Profile',NULL,'Channel Profile',' This is the main view page of an channel.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.container-tabs',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"max\":\"5\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"core.container-tabs\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.overview-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'8','{\"title\":\"Overview\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.overview-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                if ($is_sitealbum_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-photos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'11','{\"title\":\"Photos\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"showPhotosInJustifiedView\":\"1\",\"rowHeight\":\"205\",\"maxRowHeight\":\"0\",\"margin\":\"5\",\"lastRow\":\"justify\",\"width\":\"240\",\"height\":\"200\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-photos\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
    ");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-photos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'11','{\"title\":\"Photos\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"showPhotosInJustifiedView\":\"0\",\"rowHeight\":\"205\",\"maxRowHeight\":\"0\",\"margin\":\"5\",\"lastRow\":\"justify\",\"width\":\"240\",\"height\":\"200\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-photos\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
    ");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'10','{\"title\":\"Videos\",\"itemCountPerPage\":\"10\",\"margin_video\":\"2\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"ratings\",\"favourite\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoHeight\":\"265\",\"videoWidth\":\"283\",\"show_content\":\"2\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-view\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.discussion-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'12','{\"title\":\"Discussions\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.discussion-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-favourite',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'20','{\"title\":\"People Who Favourite This\",\"titleCount\":true,\"height\":\"100\",\"itemCount\":\"5\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-favourite\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                if ($is_sitecontentcoverphoto_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecontentcoverphoto.content-cover-photo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"modulename\":\"sitevideo_channel\",\"showContent_0\":\"\",\"showContent_siteevent_event\":\"\",\"showContent_album\":\"\",\"showContent_sitevideo_channel\":[\"mainPhoto\",\"title\",\"optionsButton\",\"shareOptions\",\"uploadVideos\"],\"profile_like_button\":\"1\",\"columnHeight\":\"500\",\"contentFullWidth\":\"$fullWidth\",\"sitecontentcoverphotoChangeTabPosition\":\"1\",\"contacts\":\"\",\"showMemberLevelBasedPhoto\":\"1\",\"emailme\":\"1\",\"editFontColor\":\"1\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitecontentcoverphoto.content-cover-photo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.profile-breadcrumb',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"name\":\"sitevideo.profile-breadcrumb\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.top-content-of-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'5','',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-subscribers',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'13','{\"title\":\"Subscribers\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"height\":\"160\",\"width\":\"160\",\"itemCount\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-subscribers\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-like',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'19','{\"title\":\"People Who Like This\",\"titleCount\":true,\"height\":\"100\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-like\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.your-stuff',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'21','{\"statistics\":[\"videocount\",\"likedvideocount\",\"ratedvideocount\",\"favvideocount\",\"watchlatercount\",\"playlistcount\",\"channelscreated\",\"channelsliked\",\"channelsubscribed\",\"channelsrated\",\"channelsfavourited\"],\"title\":\"Your Stuff\",\"nomobile\":\"0\",\"name\":\"sitevideo.your-stuff\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                if (!$is_advancedactivity_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','activity.feed',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'7','{\"title\":\"What\'s New\",\"showFeeds\":1,\"advancedactivity_tabs\":[\"welcome\",\"aaffeed\",\"twitter\"],\"showTabs\":\"0\",\"loadByAjax\":\"0\",\"showScrollTopButton\":\"1\",\"widthphotoattachment\":\"618\",\"width1\":\"608\",\"width2\":\"608\",\"height2\":\"350\",\"width3big\":\"608\",\"height3big\":\"300\",\"width3small\":\"299\",\"height3small\":\"200\",\"width4big\":\"608\",\"height4big\":\"250\",\"width4small\":\"195\",\"height4small\":\"100\",\"width5big\":\"299\",\"height5big\":\"200\",\"width5small\":\"195\",\"height5small\":\"130\",\"nomobile\":\"0\",\"name\":\"activity.feed\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','advancedactivity.home-feeds',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'7','{\"title\":\"What\'s New\",\"showFeeds\":1,\"advancedactivity_tabs\":[\"welcome\",\"aaffeed\",\"twitter\"],\"showTabs\":\"0\",\"loadByAjax\":\"0\",\"showScrollTopButton\":\"1\",\"widthphotoattachment\":\"618\",\"width1\":\"608\",\"width2\":\"608\",\"height2\":\"350\",\"width3big\":\"608\",\"height3big\":\"300\",\"width3small\":\"299\",\"height3small\":\"200\",\"width4big\":\"608\",\"height4big\":\"250\",\"width4small\":\"195\",\"height4small\":\"100\",\"width5big\":\"299\",\"height5big\":\"200\",\"width5small\":\"195\",\"height5small\":\"130\",\"nomobile\":\"0\",\"name\":\"advancedactivity.home-feeds\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.quick-specification-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'18','{\"title\":\"Quick Information\",\"titleCount\":true,\"itemCount\":\"3\",\"nomobile\":\"0\",\"name\":\"sitevideo.quick-specification-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.information-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'16','{\"title\":\"Channel Information\",\"titleCount\":true,\"showContent\":[\"totalVideos\",\"creationDate\",\"likeCount\",\"commentCount\",\"socialShare\",\"categoryLink\"],\"nomobile\":\"0\",\"name\":\"sitevideo.information-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.create-new-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'15','{\"title\":\"\",\"name\":\"sitevideo.create-new-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.specification-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'9','{\"title\":\"Information\",\"titleCount\":true,\"loaded_by_ajax\":1}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.share-via-badge',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'17','{\"title\":\"\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_view','Advanced Videos - Channel Profile',NULL,'Channel Profile',' This is the main view page of an channel.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.container-tabs',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"max\":\"5\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"core.container-tabs\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.overview-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'8','{\"title\":\"Overview\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.overview-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                if ($is_sitealbum_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-photos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'11','{\"title\":\"Photos\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"showPhotosInJustifiedView\":\"1\",\"rowHeight\":\"205\",\"maxRowHeight\":\"0\",\"margin\":\"5\",\"lastRow\":\"justify\",\"width\":\"240\",\"height\":\"200\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-photos\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-photos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'11','{\"title\":\"Photos\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"showPhotosInJustifiedView\":\"0\",\"rowHeight\":\"205\",\"maxRowHeight\":\"0\",\"margin\":\"5\",\"lastRow\":\"justify\",\"width\":\"240\",\"height\":\"200\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-photos\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'10','{\"title\":\"Videos\",\"itemCountPerPage\":\"10\",\"margin_video\":\"2\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"ratings\",\"favourite\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoHeight\":\"265\",\"videoWidth\":\"283\",\"show_content\":\"2\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-view\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.discussion-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'12','{\"title\":\"Discussions\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.discussion-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-favourite',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'20','{\"title\":\"People Who Favourite This\",\"titleCount\":true,\"height\":\"100\",\"itemCount\":\"5\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-favourite\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                if ($is_sitecontentcoverphoto_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitecontentcoverphoto.content-cover-photo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"modulename\":\"sitevideo_channel\",\"showContent_0\":\"\",\"showContent_siteevent_event\":\"\",\"showContent_album\":\"\",\"showContent_sitevideo_channel\":[\"mainPhoto\",\"title\",\"optionsButton\",\"shareOptions\",\"uploadVideos\"],\"profile_like_button\":\"1\",\"columnHeight\":\"500\",\"contentFullWidth\":\"$fullWidth\",\"sitecontentcoverphotoChangeTabPosition\":\"1\",\"contacts\":\"\",\"showMemberLevelBasedPhoto\":\"1\",\"emailme\":\"1\",\"editFontColor\":\"1\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"sitecontentcoverphoto.content-cover-photo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.profile-breadcrumb',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','{\"name\":\"sitevideo.profile-breadcrumb\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;");
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.top-content-of-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'5','',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-subscribers',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'13','{\"title\":\"Subscribers\",\"titleCount\":true,\"loaded_by_ajax\":\"1\",\"height\":\"160\",\"width\":\"160\",\"itemCount\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-subscribers\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-like',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'19','{\"title\":\"People Who Like This\",\"titleCount\":true,\"height\":\"100\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-like\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.your-stuff',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'21','{\"statistics\":[\"videocount\",\"likedvideocount\",\"ratedvideocount\",\"favvideocount\",\"watchlatercount\",\"playlistcount\",\"channelscreated\",\"channelsliked\",\"channelsubscribed\",\"channelsrated\",\"channelsfavourited\"],\"title\":\"Your Stuff\",\"nomobile\":\"0\",\"name\":\"sitevideo.your-stuff\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                if ($is_advancedactivity_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','advancedactivity.home-feeds',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'7','{\"title\":\"What\'s New\",\"showFeeds\":1,\"advancedactivity_tabs\":[\"welcome\",\"aaffeed\",\"twitter\"],\"showTabs\":\"0\",\"loadByAjax\":\"0\",\"showScrollTopButton\":\"1\",\"widthphotoattachment\":\"618\",\"width1\":\"608\",\"width2\":\"608\",\"height2\":\"350\",\"width3big\":\"608\",\"height3big\":\"300\",\"width3small\":\"299\",\"height3small\":\"200\",\"width4big\":\"608\",\"height4big\":\"250\",\"width4small\":\"195\",\"height4small\":\"100\",\"width5big\":\"299\",\"height5big\":\"200\",\"width5small\":\"195\",\"height5small\":\"130\",\"nomobile\":\"0\",\"name\":\"advancedactivity.home-feeds\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','activity.feed',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'7','{\"title\":\"What\'s New\",\"showFeeds\":1,\"advancedactivity_tabs\":[\"welcome\",\"aaffeed\",\"twitter\"],\"showTabs\":\"0\",\"loadByAjax\":\"0\",\"showScrollTopButton\":\"1\",\"widthphotoattachment\":\"618\",\"width1\":\"608\",\"width2\":\"608\",\"height2\":\"350\",\"width3big\":\"608\",\"height3big\":\"300\",\"width3small\":\"299\",\"height3small\":\"200\",\"width4big\":\"608\",\"height4big\":\"250\",\"width4small\":\"195\",\"height4small\":\"100\",\"width5big\":\"299\",\"height5big\":\"200\",\"width5small\":\"195\",\"height5small\":\"130\",\"nomobile\":\"0\",\"name\":\"activity.feed\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.quick-specification-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'18','{\"title\":\"Quick Information\",\"titleCount\":true,\"itemCount\":\"3\",\"nomobile\":\"0\",\"name\":\"sitevideo.quick-specification-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.information-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'16','{\"title\":\"Channel Information\",\"titleCount\":true,\"showContent\":[\"totalVideos\",\"creationDate\",\"likeCount\",\"commentCount\",\"socialShare\",\"categoryLink\"],\"nomobile\":\"0\",\"name\":\"sitevideo.information-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.create-new-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'15','{\"title\":\"\",\"name\":\"sitevideo.create-new-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.specification-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='widget' and `name`='core.container-tabs' and `order`=6 limit 1),'9','{\"title\":\"Information\",\"titleCount\":true,\"loaded_by_ajax\":1}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.share-via-badge',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'17','{\"title\":\"\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_view' ;
");
            }

            if ($is_sitecontentcoverphoto_object) {
                $db->query('INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` (`module`, `resource_type`, `resource_id`, `enabled`) VALUES
    ("sitevideo", "sitevideo_channel", "channel_id", 1)');
                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
				SELECT
					level_id as `level_id`,
					'sitecontentcoverphoto_sitevideo_channel' as `type`,
					'upload' as `name`,
					1 as `value`,
					NULL as `params`
				FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');");
            }
        }
    }

    public function videoView($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_view');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'nestedcomment')
                    ->where('enabled = ?', 1);
            $is_nestedcomment_object = $select->query()->fetchObject();
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_view','Advanced Videos - Video View Page',NULL,'Video View Page','This is the main view page of a video.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','{\"title\":\"\",\"viewOptions\":[\"title\",\"owner\",\"lightbox\",\"share\",\"suggest\",\"like\",\"dislike\",\"favourite\",\"comment\",\"view\",\"report\",\"hashtags\",\"ratings\",\"watchlater\",\"playlist\",\"subscribe\"],\"nomobile\":\"0\",\"name\":\"sitevideo.video-view\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                if ($is_nestedcomment_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','nestedcomment.comments',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"title\":\"\",\"taggingContent\":[\"friends\",\"siteevent\"],\"showComposerOptions\":[\"addLink\",\"addPhoto\",\"addSmilies\"],\"showAsNested\":\"1\",\"showAsLike\":\"0\",\"showDislikeUsers\":\"0\",\"showLikeWithoutIcon\":\"1\",\"showLikeWithoutIconInReplies\":\"1\",\"commentsorder\":\"1\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"nestedcomment.comments\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.comments',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"0\":\"{}\",\"title\":\"Comments\",\"name\":\"core.comments\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-like',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"People Who Like This\",\"titleCount\":true,\"height\":\"\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-like\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-favourite',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"People Who Favourited This\",\"titleCount\":true,\"height\":\"\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-favourite\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.your-stuff',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"statistics\":[\"videocount\",\"watchlatercount\",\"channelscreated\",\"channelsubscribed\"],\"title\":\"Your Stuff\",\"nomobile\":\"0\",\"name\":\"sitevideo.your-stuff\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_view','Advanced Videos - Video View Page',NULL,'Video View Page','This is the main view page of a video.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','{\"title\":\"\",\"viewOptions\":[\"title\",\"owner\",\"lightbox\",\"share\",\"suggest\",\"like\",\"dislike\",\"favourite\",\"comment\",\"view\",\"report\",\"hashtags\",\"ratings\",\"watchlater\",\"playlist\",\"subscribe\"],\"nomobile\":\"0\",\"name\":\"sitevideo.video-view\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                if ($is_nestedcomment_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','nestedcomment.comments',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"title\":\"\",\"taggingContent\":[\"friends\",\"siteevent\"],\"showComposerOptions\":[\"addLink\",\"addPhoto\",\"addSmilies\"],\"showAsNested\":\"1\",\"showAsLike\":\"0\",\"showDislikeUsers\":\"0\",\"showLikeWithoutIcon\":\"1\",\"showLikeWithoutIconInReplies\":\"1\",\"commentsorder\":\"1\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"nestedcomment.comments\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.comments',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"0\":\"{}\",\"title\":\"Comments\",\"name\":\"core.comments\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-like',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"People Who Like This\",\"titleCount\":true,\"height\":\"\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-like\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-favourite',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"People Who Favourited This\",\"titleCount\":true,\"height\":\"\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-favourite\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.your-stuff',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"statistics\":[\"videocount\",\"watchlatercount\",\"channelscreated\",\"channelsubscribed\"],\"title\":\"Your Stuff\",\"nomobile\":\"0\",\"name\":\"sitevideo.your-stuff\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_view','Advanced Videos - Video View Page',NULL,'Video View Page','This is the main view page of a video.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','{\"title\":\"\",\"viewOptions\":[\"title\",\"owner\",\"lightbox\",\"share\",\"suggest\",\"like\",\"dislike\",\"favourite\",\"comment\",\"view\",\"report\",\"hashtags\",\"ratings\",\"watchlater\",\"playlist\",\"subscribe\"],\"nomobile\":\"0\",\"name\":\"sitevideo.video-view\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                if ($is_nestedcomment_object) {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','nestedcomment.comments',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"title\":\"\",\"taggingContent\":[\"friends\",\"siteevent\"],\"showComposerOptions\":[\"addLink\",\"addPhoto\",\"addSmilies\"],\"showAsNested\":\"1\",\"showAsLike\":\"0\",\"showDislikeUsers\":\"0\",\"showLikeWithoutIcon\":\"1\",\"showLikeWithoutIconInReplies\":\"1\",\"commentsorder\":\"1\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"nestedcomment.comments\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                } else {
                    $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.comments',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'6','{\"0\":\"{}\",\"title\":\"Comments\",\"name\":\"core.comments\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;");
                }
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-like',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"People Who Like This\",\"titleCount\":true,\"height\":\"\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-like\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.people-who-favourite',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"People Who Favourited This\",\"titleCount\":true,\"height\":\"\",\"itemCount\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.people-who-favourite\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.your-stuff',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'10','{\"statistics\":[\"videocount\",\"watchlatercount\",\"channelscreated\",\"channelsubscribed\"],\"title\":\"Your Stuff\",\"nomobile\":\"0\",\"name\":\"sitevideo.your-stuff\"}',NULL from engine4_core_pages where name = 'sitevideo_video_view' ;
");
            }
        }
    }

    public function browseChannel($reset, $default) {

        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_index_browse');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_browse','Advanced Videos - Browse Channels Page',NULL,'Browse Channels Page','This page lists channel entries.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','left',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'4','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.search-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'6','{\"title\":\"Search Channels\",\"titleCount\":true,\"viewType\":\"vertical\",\"showAllCategories\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.search-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browse-channels-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'10','{\"title\":\"\",\"titleCount\":true,\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"defaultViewType\":\"videoView\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"rating\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"\",\"videoViewWidth\":\"434\",\"videoViewHeight\":\"330\",\"gridViewWidth\":\"283\",\"gridViewHeight\":\"250\",\"show_content\":\"2\",\"orderby\":\"featuredSponsored\",\"titleTruncation\":\"60\",\"titleTruncationGridNVideoView\":\"48\",\"descriptionTruncation\":\"50\",\"itemCountPerPage\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-channels-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.categories-navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'7','{\"orderBy\":\"cat_order\",\"viewDisplayHR\":\"0\",\"title\":\"Categories\",\"nomobile\":\"0\",\"name\":\"sitevideo.categories-navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'8','{\"title\":\"Popular Tags (%s)\",\"titleCount\":true,\"orderingType\":\"1\",\"itemCount\":\"20\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.tagcloud-sitevideo-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_browse','Advanced Videos - Browse Channels Page',NULL,'Browse Channels Page','This page lists channel entries.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','left',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'4','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.search-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'6','{\"title\":\"Search Channels\",\"titleCount\":true,\"viewType\":\"vertical\",\"showAllCategories\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.search-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browse-channels-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'10','{\"title\":\"\",\"titleCount\":true,\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"defaultViewType\":\"videoView\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"rating\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"\",\"videoViewWidth\":\"412\",\"videoViewHeight\":\"340\",\"gridViewWidth\":\"269\",\"gridViewHeight\":\"250\",\"show_content\":\"2\",\"orderby\":\"featuredSponsored\",\"titleTruncation\":\"55\",\"titleTruncationGridNVideoView\":\"24\",\"descriptionTruncation\":\"500\",\"itemCountPerPage\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-channels-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.categories-navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'7','{\"orderBy\":\"cat_order\",\"viewDisplayHR\":\"0\",\"title\":\"Categories\",\"nomobile\":\"0\",\"name\":\"sitevideo.categories-navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'8','{\"title\":\"Popular Tags (%s)\",\"titleCount\":true,\"orderingType\":\"1\",\"itemCount\":\"20\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.tagcloud-sitevideo-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_browse','Advanced Videos - Browse Channels Page',NULL,'Browse Channels Page','This page lists channel entries.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','left',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'4','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.search-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'6','{\"title\":\"Search Channels\",\"titleCount\":true,\"viewType\":\"vertical\",\"showAllCategories\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.search-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browse-channels-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'10','{\"title\":\"\",\"titleCount\":true,\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"defaultViewType\":\"videoView\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"rating\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"\",\"videoViewWidth\":\"355\",\"videoViewHeight\":\"340\",\"gridViewWidth\":\"231\",\"gridViewHeight\":\"240\",\"show_content\":\"2\",\"orderby\":\"featuredSponsored\",\"titleTruncation\":\"48\",\"titleTruncationGridNVideoView\":\"23\",\"descriptionTruncation\":\"400\",\"itemCountPerPage\":\"6\",\"nomobile\":\"0\",\"name\":\"sitevideo.browse-channels-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.categories-navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'7','{\"orderBy\":\"cat_order\",\"viewDisplayHR\":\"0\",\"title\":\"Categories\",\"nomobile\":\"0\",\"name\":\"sitevideo.categories-navigation\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.tagcloud-sitevideo-channel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='left' and `order`=4 limit 1),'8','{\"title\":\"Popular Tags (%s)\",\"titleCount\":true,\"orderingType\":\"1\",\"itemCount\":\"20\",\"loaded_by_ajax\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.tagcloud-sitevideo-channel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_browse' ;
");
            }
        }
        //END CHANNEL BROWSE PAGE
    }

    public function channelManage($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_channel_manage');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_manage','Advanced Videos - My Channels Page',NULL,'My Channels Page','This page lists channel a user\'s channels.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-channels-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"topNavigationLink\":[\"video\",\"channel\",\"createVideo\",\"createChannel\"],\"channelNavigationLink\":[\"channel\",\"liked\",\"favourite\",\"subscribed\",\"rated\"],\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"channelOption\":[\"title\",\"like\",\"comment\",\"favourite\",\"rating\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"defaultViewType\":\"videoView\",\"searchButton\":\"1\",\"videoViewWidth\":\"434\",\"videoViewHeight\":\"332\",\"gridViewWidth\":\"282\",\"gridViewHeight\":\"247\",\"show_content\":\"2\",\"itemCountPerPage\":\"12\",\"titleTruncation\":\"24\",\"descriptionTruncation\":\"400\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-channels-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Liked Channels\",\"itemCountPerPage\":\"4\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"250\",\"channelWidth\":\"215\",\"popularType\":\"like\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"Recent Channels\",\"itemCountPerPage\":\"4\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"250\",\"channelWidth\":\"215\",\"popularType\":\"creation\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_manage','Advanced Videos - My Channels Page',NULL,'My Channels Page','This page lists channel a user\'s channels.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-channels-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"topNavigationLink\":[\"video\",\"channel\",\"createVideo\",\"createChannel\"],\"channelNavigationLink\":[\"channel\",\"liked\",\"favourite\",\"subscribed\",\"rated\"],\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"channelOption\":[\"title\",\"like\",\"comment\",\"favourite\",\"rating\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"defaultViewType\":\"videoView\",\"searchButton\":\"1\",\"videoViewWidth\":\"840\",\"videoViewHeight\":\"332\",\"gridViewWidth\":\"269\",\"gridViewHeight\":\"240\",\"show_content\":\"2\",\"itemCountPerPage\":\"12\",\"titleTruncation\":\"73\",\"descriptionTruncation\":\"400\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-channels-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Liked Channels\",\"itemCountPerPage\":\"4\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"260\",\"channelWidth\":\"215\",\"popularType\":\"like\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"19\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"Recent Channels\",\"itemCountPerPage\":\"4\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"260\",\"channelWidth\":\"215\",\"popularType\":\"creation\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"19\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_manage','Advanced Videos - My Channels Page',NULL,'My Channels Page','This page lists channel a user\'s channels.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-channels-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"topNavigationLink\":[\"video\",\"channel\",\"createVideo\",\"createChannel\"],\"channelNavigationLink\":[\"channel\",\"liked\",\"favourite\",\"subscribed\",\"rated\"],\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"channelOption\":[\"title\",\"like\",\"comment\",\"favourite\",\"rating\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"defaultViewType\":\"videoView\",\"searchButton\":\"1\",\"videoViewWidth\":\"723\",\"videoViewHeight\":\"332\",\"gridViewWidth\":\"231\",\"gridViewHeight\":\"240\",\"show_content\":\"2\",\"itemCountPerPage\":\"12\",\"titleTruncation\":\"48\",\"descriptionTruncation\":\"400\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-channels-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Liked Channels\",\"itemCountPerPage\":\"4\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"255\",\"channelWidth\":\"186\",\"popularType\":\"like\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"18\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-channels',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"title\":\"Recent Channels\",\"itemCountPerPage\":\"4\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"\",\"hidden_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"channelHeight\":\"250\",\"channelWidth\":\"215\",\"popularType\":\"creation\",\"interval\":\"overall\",\"channelInfo\":[\"title\",\"owner\",\"like\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"titleTruncation\":\"18\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-channels\"}',NULL from engine4_core_pages where name = 'sitevideo_channel_manage' ;
");
            }
        }
    }

    public function videoManage($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_manage');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_manage','Advanced Videos - My Videos Page',NULL,'My Video Page','This page lists video a user\'s videos.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-videos-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"itemCountPerPage\":\"12\",\"topNavigationLink\":[\"video\",\"channel\",\"createVideo\",\"createChannel\"],\"videoNavigationLink\":[\"video\",\"rated\",\"watchlater\",\"playlist\",\"liked\",\"favourite\"],\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"videoOption\":[\"title\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"favourite\",\"rating\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"searchButton\":\"1\",\"defaultViewType\":\"videoView\",\"videoViewWidth\":\"352\",\"videoViewHeight\":\"335\",\"gridViewWidth\":\"283\",\"gridViewHeight\":\"240\",\"show_content\":\"2\",\"orderby\":\"creation_date\",\"titleTruncation\":\"73\",\"titleTruncationGridNVideoView\":\"24\",\"descriptionTruncation\":\"400\",\"ad_header\":null,\"videoShowLinkCount\":\"3\",\"playlistViewType\":[\"gridView\",\"listView\"],\"playlistDefaultViewType\":\"gridView\",\"playlistGridViewWidth\":\"282\",\"playlistGridViewHeight\":\"210\",\"playlistOrder\":\"creation_date\",\"playlistVideoOrder\":\"creation_date\",\"showPlayAllOption\":\"1\",\"watchlater_header\":null,\"watchlaterOrder\":\"creation_date\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Liked Videos\",\"itemCountPerPage\":\"4\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"220\",\"videoHeight\":\"242\",\"popularType\":\"like\",\"interval\":\"overall\",\"videoInfo\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoTitleTruncation\":\"21\",\"truncationLocation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.your-stuff',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"statistics\":[\"videocount\",\"likedvideocount\",\"favvideocount\",\"channelscreated\",\"channelsliked\",\"channelsfavourited\",\"ratedvideocount\",\"channelsrated\",\"watchlatercount\",\"playlistcount\",\"channelsubscribed\"],\"title\":\"Your Stuff\",\"nomobile\":\"0\",\"name\":\"sitevideo.your-stuff\"}',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_manage','Advanced Videos - My Videos Page',NULL,'My Video Page','This page lists video a user\'s videos.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-videos-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"itemCountPerPage\":\"12\",\"topNavigationLink\":[\"video\",\"channel\",\"createVideo\",\"createChannel\"],\"videoNavigationLink\":[\"video\",\"rated\",\"watchlater\",\"playlist\",\"liked\",\"favourite\"],\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"videoOption\":[\"title\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"favourite\",\"rating\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"searchButton\":\"1\",\"defaultViewType\":\"videoView\",\"videoViewWidth\":\"750\",\"videoViewHeight\":\"335\",\"gridViewWidth\":\"269\",\"gridViewHeight\":\"250\",\"show_content\":\"2\",\"orderby\":\"creation_date\",\"titleTruncation\":\"73\",\"titleTruncationGridNVideoView\":\"24\",\"descriptionTruncation\":\"400\",\"ad_header\":null,\"videoShowLinkCount\":\"3\",\"playlistViewType\":[\"gridView\",\"listView\"],\"playlistDefaultViewType\":\"gridView\",\"playlistGridViewWidth\":\"269\",\"playlistGridViewHeight\":\"210\",\"playlistOrder\":\"creation_date\",\"playlistVideoOrder\":\"creation_date\",\"showPlayAllOption\":\"1\",\"watchlater_header\":null,\"watchlaterOrder\":\"creation_date\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Liked Videos\",\"itemCountPerPage\":\"4\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"215\",\"videoHeight\":\"260\",\"popularType\":\"like\",\"interval\":\"overall\",\"videoInfo\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoTitleTruncation\":\"19\",\"truncationLocation\":\"20\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.your-stuff',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"statistics\":[\"videocount\",\"likedvideocount\",\"favvideocount\",\"channelscreated\",\"channelsliked\",\"channelsfavourited\",\"ratedvideocount\",\"channelsrated\",\"watchlatercount\",\"playlistcount\",\"channelsubscribed\"],\"title\":\"Your Stuff\",\"nomobile\":\"0\",\"name\":\"sitevideo.your-stuff\"}',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_manage','Advanced Videos - My Videos Page',NULL,'My Video Page','This page lists video a user\'s videos.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-videos-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"itemCountPerPage\":\"12\",\"topNavigationLink\":[\"video\",\"channel\",\"createVideo\",\"createChannel\"],\"videoNavigationLink\":[\"video\",\"rated\",\"watchlater\",\"playlist\",\"liked\",\"favourite\"],\"viewType\":[\"videoView\",\"gridView\",\"listView\"],\"videoOption\":[\"title\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"favourite\",\"rating\",\"watchlater\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"searchButton\":\"1\",\"defaultViewType\":\"videoView\",\"videoViewWidth\":\"643\",\"videoViewHeight\":\"335\",\"gridViewWidth\":\"231\",\"gridViewHeight\":\"250\",\"show_content\":\"2\",\"orderby\":\"creation_date\",\"titleTruncation\":\"55\",\"titleTruncationGridNVideoView\":\"23\",\"descriptionTruncation\":\"400\",\"ad_header\":null,\"videoShowLinkCount\":\"3\",\"playlistViewType\":[\"gridView\",\"listView\"],\"playlistDefaultViewType\":\"gridView\",\"playlistGridViewWidth\":\"231\",\"playlistGridViewHeight\":\"210\",\"playlistOrder\":\"creation_date\",\"playlistVideoOrder\":\"creation_date\",\"showPlayAllOption\":\"1\",\"watchlater_header\":null,\"watchlaterOrder\":\"creation_date\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-videos-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.list-popular-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'8','{\"title\":\"Most Liked Videos\",\"itemCountPerPage\":\"4\",\"videoType\":null,\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"titleLink\":\"\",\"titleLinkPosition\":\"bottom\",\"featured\":\"0\",\"videoWidth\":\"186\",\"videoHeight\":\"245\",\"popularType\":\"like\",\"interval\":\"overall\",\"videoInfo\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoTitleTruncation\":\"19\",\"truncationLocation\":\"15\",\"nomobile\":\"0\",\"name\":\"sitevideo.list-popular-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.your-stuff',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='right' and `order`=5 limit 1),'9','{\"statistics\":[\"videocount\",\"likedvideocount\",\"favvideocount\",\"channelscreated\",\"channelsliked\",\"channelsfavourited\",\"ratedvideocount\",\"channelsrated\",\"watchlatercount\",\"playlistcount\",\"channelsubscribed\"],\"title\":\"Your Stuff\",\"nomobile\":\"0\",\"name\":\"sitevideo.your-stuff\"}',NULL from engine4_core_pages where name = 'sitevideo_video_manage' ;
");
            }
        }
//END VIDEO MANAGE PAGE
    }

    public function channelCategories($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_index_categories');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        $fullWidth = $this->getFullThemeValue();
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_categories','Advanced Videos - Channel Categories Home',NULL,'Channel Categories Home','This is the categories home page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-categorybanner-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"logo\":\"\",\"height\":\"555\",\"categoryHeight\":\"400\",\"category_id\":[\"2\",\"3\",\"5\",\"6\",\"7\",\"9\",\"10\",\"11\",\"14\",\"56\"],\"showExplore\":\"1\",\"fullWidth\":\"$fullWidth\", \"titleTruncation\":\"100\",\"taglineTruncation\":\"200\",\"nomobile\":\"\",\"name\":\"sitevideo.channel-categorybanner-slideshow\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"Others\",\"category_id\":\"14\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"14\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"213\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"24\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"213\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"24\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
//");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"Categories\",\"titleCount\":true,\"orderBy\":\"cat_order\",\"showAllCategories\":\"1\",\"columnWidth\":\"218\",\"columnHeight\":\"155\",\"showIcon\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.categories-withicon-grid-view\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'10','{\"title\":\"\",\"category_id\":\"1\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"213\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"24\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"category_id\":\"9\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"213\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"24\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
//");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_categories','Advanced Videos - Channel Categories Home',NULL,'Channel Categories Home','This is the categories home page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-categorybanner-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"logo\":null,\"height\":\"555\",\"categoryHeight\":\"370\",\"fullWidth\":\"0\",\"category_id\":[\"2\",\"3\",\"5\",\"6\",\"7\",\"9\",\"10\",\"11\",\"14\"],\"showExplore\":\"1\",\"titleTruncation\":\"100\",\"taglineTruncation\":\"200\",\"nomobile\":\"\",\"name\":\"sitevideo.channel-categorybanner-slideshow\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"Others\",\"category_id\":\"14\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"14\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"257\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"257\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
//");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"Categories\",\"titleCount\":true,\"orderBy\":\"cat_order\",\"showAllCategories\":\"1\",\"columnWidth\":\"204\",\"columnHeight\":\"155\",\"showIcon\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.categories-withicon-grid-view\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'10','{\"title\":\"\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"257\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"257\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
//");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_index_categories','Advanced Videos - Channel Categories Home',NULL,'Channel Categories Home','This is the categories home page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-categorybanner-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"logo\":null,\"height\":\"555\",\"categoryHeight\":\"400\",\"fullWidth\":\"0\",\"category_id\":[\"2\",\"3\",\"5\",\"6\",\"7\",\"9\",\"10\",\"11\",\"14\"],\"showExplore\":\"1\",\"titleTruncation\":\"48\",\"taglineTruncation\":\"60\",\"nomobile\":\"\",\"name\":\"sitevideo.channel-categorybanner-slideshow\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");

                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"Others\",\"category_id\":\"14\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"14\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"217\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"24\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"217\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"24\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
//");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"Categories\",\"titleCount\":true,\"orderBy\":\"cat_order\",\"showAllCategories\":\"1\",\"columnWidth\":\"174\",\"columnHeight\":\"155\",\"showIcon\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.categories-withicon-grid-view\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
");


//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'10','{\"title\":\"\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"217\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"24\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.channel-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_category_id\":\"\",\"hidden_subcategory_id\":\"0\",\"hidden_subsubcategory_id\":\"0\",\"showChannel\":\"\",\"channelOption\":[\"title\",\"owner\",\"like\",\"comment\",\"favourite\",\"numberOfVideos\",\"subscribe\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"channelWidth\":\"217\",\"channelHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"24\",\"nomobile\":\"0\",\"name\":\"sitevideo.channel-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_index_categories' ;
//");
            }
        }
    }

    public function videoCategories($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_categories');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        $categoryId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Others')
                ->limit(1)
                ->query()
                ->fetchColumn();

        $fullWidth = $this->getFullThemeValue();
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_categories','Advanced Videos - Video Categories Home',NULL,'Video Categories Home','This is the video categories home page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"All Categories \",\"titleCount\":true,\"orderBy\":\"cat_order\",\"showAllCategories\":\"1\",\"columnWidth\":\"218\",\"columnHeight\":\"155\",\"showIcon\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-categories-withicon-grid-view\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"Others\",\"videoType\":\"\",\"category_id\":\"$categoryId\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"$categoryId\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"213\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categorybanner-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"logo\":\"\",\"height\":\"555\",\"categoryHeight\":\"400\",\"category_id\":[\"$categoryId\",\"7\",\"11\",\"5\",\"1\",\"13\",\"9\",\"6\",\"8\",\"4\"],\"showExplore\":\"1\",\"fullWidth\":\"$fullWidth\",\"titleTruncation\":\"100\",\"taglineTruncation\":\"200\",\"nomobile\":\"\",\"name\":\"sitevideo.video-categorybanner-slideshow\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"213\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'10','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"13\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"213\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"213\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
//");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_categories','Advanced Videos - Video Categories Home',NULL,'Video Categories Home','This is the video categories home page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"All Categories \",\"titleCount\":true,\"orderBy\":\"cat_order\",\"showAllCategories\":\"1\",\"columnWidth\":\"204\",\"columnHeight\":\"155\",\"showIcon\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-categories-withicon-grid-view\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"Others\",\"videoType\":\"\",\"category_id\":\"$categoryId\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"$categoryId\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"257\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categorybanner-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"logo\":null,\"height\":\"555\",\"categoryHeight\":\"370\",\"fullWidth\":\"0\",\"category_id\":[\"1\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"11\",\"13\",\"15\"],\"showExplore\":\"1\",\"titleTruncation\":\"100\",\"taglineTruncation\":\"200\",\"nomobile\":\"\",\"name\":\"sitevideo.video-categorybanner-slideshow\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"257\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'10','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"257\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"257\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
//");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_categories','Advanced Videos - Video Categories Home',NULL,'Video Categories Home','This is the video categories home page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'7','{\"title\":\"All Categories \",\"titleCount\":true,\"orderBy\":\"cat_order\",\"showAllCategories\":\"1\",\"columnWidth\":\"174\",\"columnHeight\":\"155\",\"showIcon\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-categories-withicon-grid-view\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");

                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"Others\",\"videoType\":\"\",\"category_id\":\"$categoryId\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"$categoryId\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"219\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");

                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categorybanner-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"logo\":null,\"height\":\"555\",\"categoryHeight\":\"370\",\"fullWidth\":\"0\",\"category_id\":[\"1\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"11\",\"13\",\"15\"],\"showExplore\":\"1\",\"titleTruncation\":\"100\",\"taglineTruncation\":\"200\",\"nomobile\":\"\",\"name\":\"sitevideo.video-categorybanner-slideshow\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"217\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'10','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"217\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"217\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}',NULL from engine4_core_pages where name = 'sitevideo_video_categories' ;
//");
            }
        }
//END VIDEO CATEGORIES HOME PAGE
    }

    public function videoHome($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_index');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        $fullWidth = $this->getFullThemeValue();

        $categoryId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Others')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_index','Advanced Videos - Video Home Page',NULL,'Video Home Page','This is the video home page.','','0','0','',NULL,'no-subject','0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"All Categories \",\"titleCount\":true,\"orderBy\":\"cat_order\",\"showAllCategories\":\"1\",\"columnWidth\":\"218\",\"columnHeight\":\"155\",\"showIcon\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-categories-withicon-grid-view\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"Others\",\"videoType\":\"\",\"category_id\":\"$categoryId\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"$categoryId\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"213\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.featured-videos-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"videoOption\":[\"title\",\"watchlater\"],\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"showTagline1\":\"1\",\"showTagline2\":\"1\",\"showTaglineDesc\":\"1\",\"showNavigationButton\":\"1\",\"fullWidth\":\"$fullWidth\",\"popularType\":\"random\",\"interval\":\"overall\",\"slideshow_height\":\"500\",\"delay\":\"4500\",\"slidesLimit\":\"10\",\"titleTruncation\":\"200\",\"taglineTruncation\":\"200\",\"descriptionTruncation\":\"200\",\"nomobile\":\"0\",\"name\":\"sitevideo.featured-videos-slideshow\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'10','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"213\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"213\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'12','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"14\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"213\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"10\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
//");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.best-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"Best Videos\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"showVideo\":\"featured\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showLink\":\"1\",\"videoHeight\":\"250\",\"videoWidth\":\"274\",\"popularType\":\"random\",\"titleTruncation\":\"30\",\"nomobile\":\"0\",\"name\":\"sitevideo.best-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_video_index' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_index','Advanced Videos - Video Home Page',NULL,'Video Home Page','This is the video home page.','','0','0','',NULL,'no-subject','0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"All Categories \",\"titleCount\":true,\"orderBy\":\"cat_order\",\"showAllCategories\":\"1\",\"columnWidth\":\"204\",\"columnHeight\":\"155\",\"showIcon\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-categories-withicon-grid-view\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"Others\",\"videoType\":\"\",\"category_id\":\"$categoryId\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"$categoryId\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"257\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.featured-videos-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"videoOption\":\"\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"showTagline1\":\"1\",\"showTagline2\":\"1\",\"showTaglineDesc\":\"1\",\"showNavigationButton\":\"1\",\"fullWidth\":\"0\",\"popularType\":\"random\",\"interval\":\"overall\",\"slideshow_height\":\"350\",\"delay\":\"3500\",\"slidesLimit\":\"10\",\"titleTruncation\":\"200\",\"taglineTruncation\":\"200\",\"descriptionTruncation\":\"200\",\"nomobile\":\"0\",\"name\":\"sitevideo.featured-videos-slideshow\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'10','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"257\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"257\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'12','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"257\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
//");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.best-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"Best Videos\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"showVideo\":\"featured\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoHeight\":\"240\",\"videoWidth\":\"257\",\"showLink\":\"1\",\"buttonTitle\":\"Best Videos\",\"popularType\":\"random\",\"titleTruncation\":\"27\",\"nomobile\":\"0\",\"name\":\"sitevideo.best-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_video_index' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_index','Advanced Videos - Video Home Page',NULL,'Video Home Page','This is the video home page.','','0','0','',NULL,'no-subject','0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-categories-withicon-grid-view',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'7','{\"title\":\"All Categories \",\"titleCount\":true,\"orderBy\":\"cat_order\",\"showAllCategories\":\"1\",\"columnWidth\":\"174\",\"columnHeight\":\"155\",\"showIcon\":\"1\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-categories-withicon-grid-view\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'9','{\"title\":\"Others\",\"videoType\":\"\",\"category_id\":\"$categoryId\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"$categoryId\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"217\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.featured-videos-slideshow',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'4','{\"title\":\"\",\"videoOption\":\"\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"showTagline1\":\"1\",\"showTagline2\":\"1\",\"showTaglineDesc\":\"1\",\"showNavigationButton\":\"1\",\"fullWidth\":\"0\",\"popularType\":\"random\",\"interval\":\"overall\",\"slideshow_height\":\"350\",\"delay\":\"3500\",\"slidesLimit\":\"10\",\"titleTruncation\":\"200\",\"taglineTruncation\":\"200\",\"descriptionTruncation\":\"200\",\"nomobile\":\"0\",\"name\":\"sitevideo.featured-videos-slideshow\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'10','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"15\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"217\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"3500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'11','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"217\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
//");
//                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.video-carousel',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'12','{\"title\":\"\",\"videoType\":\"All\",\"category_id\":\"\",\"subcategory_id\":\"0\",\"hidden_video_category_id\":\"15\",\"hidden_video_subcategory_id\":\"0\",\"hidden_video_subsubcategory_id\":\"0\",\"showVideo\":\"\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"showPagination\":\"1\",\"showLink\":\"1\",\"videoWidth\":\"217\",\"videoHeight\":\"150\",\"popularType\":\"random\",\"interval\":\"4500\",\"itemCount\":\"8\",\"itemCountPerPage\":\"27\",\"titleTruncation\":\"21\",\"nomobile\":\"0\",\"name\":\"sitevideo.video-carousel\"}','' from engine4_core_pages where name = 'sitevideo_video_index' ;
//");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.best-videos',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'8','{\"title\":\"Best Videos\",\"videoType\":\"All\",\"category_id\":\"0\",\"subcategory_id\":null,\"hidden_video_category_id\":\"\",\"hidden_video_subcategory_id\":\"\",\"hidden_video_subsubcategory_id\":\"\",\"showVideo\":\"featured\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"duration\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"videoHeight\":\"220\",\"videoWidth\":\"219\",\"showLink\":\"1\",\"buttonTitle\":\"Best Videos\",\"popularType\":\"random\",\"titleTruncation\":\"24\",\"nomobile\":\"0\",\"name\":\"sitevideo.best-videos\"}',NULL from engine4_core_pages where name = 'sitevideo_video_index' ;
");
            }
        }
    }

    public function setVideoCategories($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $results = $db->select()
                ->from('engine4_video_categories', array('category_id', 'category_name'))
                ->where('cat_dependency =?', 0)
                ->query()
                ->fetchAll();
        $containerCount = 0;
        $widgetCount = 0;
        foreach ($results as $result) {

            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', 'sitevideo_video_categories-home_category_' . $result['category_id'])
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if ($page_id && $reset) {
                $this->deletePageAndContent($page_id);
                $page_id = false;
            }

            if (!$page_id) {
                $db->insert('engine4_core_pages', array('name' => 'sitevideo_video_categories-home_category_' . $result['category_id'],
                    'displayname' => 'Advanced Videos - Video Category - ' . $result['category_name'],
                    'title' => 'Video ' . $result['category_name'] . ' Home',
                    'description' => 'This is the Video ' . $result['category_name'] . ' home page.',
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
                    'name' => 'sitevideo.navigation',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"0","name":"sitevideo.navigation"}',
                ));
                if ($this->getCurrentActivateTheme($default) == 3) {
                    $db->insert('engine4_core_content', array(
                        'page_id' =>
                        $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.video-categorybanner-sitevideo',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"","logo":"","height":"555","categoryHeight":"400","showExplore":"1","titleTruncation":"20","taglineTruncation":"200","nomobile":"","name":"sitevideo.video-categorybanner-sitevideo"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 2) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.video-categorybanner-sitevideo',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"","logo":"","height":"555","categoryHeight":"400","fullWidth":"0","showExplore":"1","titleTruncation":"100","taglineTruncation":"200","nomobile":"","name":"sitevideo.video-categorybanner-sitevideo"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 1) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.video-categorybanner-sitevideo',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"","logo":"","height":"555","categoryHeight":"400","fullWidth":"0","showExplore":"1","titleTruncation":"100","taglineTruncation":"200","nomobile":"","name":"sitevideo.video-categorybanner-sitevideo"}',
                    ));
                }

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
                    'name' => 'sitevideo.video-categories-navigation',
                    'parent_content_id' => $left_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"orderBy":"category_name","viewDisplayHR":"0","title":"Categories","nomobile":"0","name":"sitevideo.video-categories-navigation"}',
                ));
                $db->insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget',
                    'name' => 'sitevideo.video-categories-grid-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Sub-categories","titleCount":true,"orderBy":"category_name","showAllCategories":"0","showSubCategoriesCount":"5","showCount":"0","columnWidth":"275","columnHeight":"220","nomobile":"0","name":"sitevideo.video-categories-grid-view"}',
                ));
                if ($this->getCurrentActivateTheme($default) == 3) {
                    $db->insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget',
                        'name' => 'sitevideo.video-carousel',
                        'parent_content_id' => $main_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"' . $result['category_name'] . '","videoType":"","category_id":"' . $result['category_id'] . '","subcategory_id":"0","hidden_video_category_id":"1","hidden_video_subcategory_id":"' . $result['category_id'] . '","hidden_video_subsubcategory_id":"0","showVideo":"","videoOption":["title","owner","creationDate","view","like","duration","watchlater","favourite","facebook","twitter","linkedin","googleplus"],"showPagination":"1","showLink":"1","videoWidth":"160","videoHeight":"150","popularType":"creation_date","interval":"3500","itemCount":"10","itemCountPerPage":"27","titleTruncation":"13","nomobile":"0","name":"sitevideo.video-carousel"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 2) {
                    $db->insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget',
                        'name' => 'sitevideo.video-carousel',
                        'parent_content_id' => $main_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"' . $result['category_name'] . '","videoType":"","category_id":"' . $result['category_id'] . '","subcategory_id":"0","hidden_video_category_id":"1","hidden_video_subcategory_id":"' . $result['category_id'] . '","hidden_video_subsubcategory_id":"0","showVideo":"","videoOption":["title","owner","creationDate","view","like","duration","watchlater","favourite","facebook","twitter","linkedin","googleplus"],"showPagination":"1","showLink":"1","videoWidth":"160","videoHeight":"150","popularType":"creation_date","interval":"3500","itemCount":"10","itemCountPerPage":"27","titleTruncation":"13","nomobile":"0","name":"sitevideo.video-carousel"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 1) {
                    $db->insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget',
                        'name' => 'sitevideo.video-carousel',
                        'parent_content_id' => $main_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"' . $result['category_name'] . '","videoType":"","category_id":"' . $result['category_id'] . '","subcategory_id":"0","hidden_video_category_id":"1","hidden_video_subcategory_id":"' . $result['category_id'] . '","hidden_video_subsubcategory_id":"0","showVideo":"","videoOption":["title","owner","creationDate","view","like","duration","watchlater","favourite","facebook","twitter","linkedin","googleplus"],"showPagination":"1","showLink":"1","videoWidth":"167","videoHeight":"150","popularType":"creation_date","interval":"3500","itemCount":"10","itemCountPerPage":"27","titleTruncation":"16","nomobile":"0","name":"sitevideo.video-carousel"}',
                    ));
                }
            }
        }
    }

    public function setChannelCategories($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $containerCount = 0;
        $widgetCount = 0;
        $results = $db->select()
                ->from('engine4_sitevideo_channel_categories', array('category_id', 'category_name'))
                ->where('cat_dependency =?', 0)
                ->query()
                ->fetchAll();

        foreach ($results as $result) {

            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', 'sitevideo_index_categories-home_category_' . $result['category_id'])->limit(1)
                    ->query()
                    ->fetchColumn();
            if ($page_id && $reset) {
                $this->deletePageAndContent($page_id);
                $page_id = false;
            }
            if (!$page_id) {
                $db->insert('engine4_core_pages', array(
                    'name' => 'sitevideo_index_categories-home_category_' . $result['category_id'], 'displayname' => 'Advanced Videos - Channel Category - ' . $result['category_name'],
                    'title' => 'Channel ' . $result['category_name'] . ' Home',
                    'description' => 'This is the Channel ' . $result['category_name'] . ' home page.',
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
                $db->insert('engine4_core_content', array('type' => 'container',
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
                    'name' => 'sitevideo.navigation',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"0","name":"sitevideo.navigation"}',
                ));
                if ($this->getCurrentActivateTheme($default) == 3) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.channel-categorybanner-sitevideo',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"","logo":"","height":"555","categoryHeight":"400","showExplore":"1","titleTruncation":"20","taglineTruncation":"65","nomobile":"","name":"sitevideo.channel-categorybanner-sitevideo"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 2) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.channel-categorybanner-sitevideo',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"","logo":null,"height":"555","categoryHeight":"400","fullWidth":"0","showExplore":"1","titleTruncation":"20","taglineTruncation":"65","nomobile":"","name":"sitevideo.channel-categorybanner-sitevideo"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 1) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.channel-categorybanner-sitevideo',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"","logo":null,"height":"555","categoryHeight":"400","fullWidth":"0","showExplore":"1","titleTruncation":"20","taglineTruncation":"65","nomobile":"","name":"sitevideo.channel-categorybanner-sitevideo"}',
                    ));
                }

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
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.categories-navigation',
                    'parent_content_id' => $left_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"orderBy":"category_name","viewDisplayHR":"0","title":"Categories","nomobile":"0","name":"sitevideo.categories-navigation"}',
                ));
                $db->insert('engine4_core_content', array('page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.categories-grid-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Sub-categories","titleCount":true,"orderBy":"cat_order","showAllCategories":"0","showSubCategoriesCount":"5","showCount":"0","columnWidth":"275","columnHeight":"220","nomobile":"0","name":"sitevideo.categories-grid-view"}',
                ));
                if ($this->getCurrentActivateTheme($default) == 3) {
                    $db->insert('engine4_core_content', array('page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.channel-carousel',
                        'parent_content_id' => $main_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"' . $result['category_name'] . '","category_id":"' . $result['category_id'] . '","subcategory_id":"0","hidden_category_id":"' . $result['category_id'] . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","showChannel":"","channelOption":["title","owner","like","comment","favourite","numberOfVideos","subscribe","facebook","twitter","linkedin","googleplus"],"showPagination":"1","showLink":"1","channelHeight":"150","channelWidth":"160","popularType":"random","interval":"3500","itemCount":"10","itemCountPerPage":"27","titleTruncation":"23","nomobile":"0","name":"sitevideo.channel-carousel"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 2) {
                    $db->insert('engine4_core_content', array('page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.channel-carousel',
                        'parent_content_id' => $main_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"' . $result['category_name'] . '","category_id":"' . $result['category_id'] . '","subcategory_id":"0","hidden_category_id":"' . $result['category_id'] . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","showChannel":"","channelOption":["title","owner","like","comment","favourite","numberOfVideos","subscribe","facebook","twitter","linkedin","googleplus"],"showPagination":"1","showLink":"1","channelHeight":"150","channelWidth":"160","popularType":"random","interval":"3500","itemCount":"10","itemCountPerPage":"27","titleTruncation":"23","nomobile":"0","name":"sitevideo.channel-carousel"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 1) {
                    $db->insert('engine4_core_content', array('page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.channel-carousel',
                        'parent_content_id' => $main_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"' . $result['category_name'] . '","category_id":"' . $result['category_id'] . '","subcategory_id":"0","hidden_category_id":"' . $result['category_id'] . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","showChannel":"","channelOption":["title","owner","like","comment","favourite","numberOfVideos","subscribe","facebook","twitter","linkedin","googleplus"],"showPagination":"1","showLink":"1","channelHeight":"150","channelWidth":"167","popularType":"random","interval":"3500","itemCount":"10","itemCountPerPage":"27","titleTruncation":"23","nomobile":"0","name":"sitevideo.channel-carousel"}',
                    ));
                }
            }
        }
    }

    public function setVideoLocations($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_video_map');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_map','Advanced Videos - Browse Videos\' Locations',NULL,'Browse Videos\' Locations','This is the video browse locations page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browselocation-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"showAllCategories\":\"1\",\"locationDetection\":\"1\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"location\",\"duration\",\"rating\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"nomobile\":\"0\",\"name\":\"sitevideo.browselocation-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_map','Advanced Videos - Browse Videos\' Locations',NULL,'Browse Videos\' Locations','This is the video browse locations page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browselocation-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"showAllCategories\":\"1\",\"locationDetection\":\"1\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"location\",\"duration\",\"rating\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"nomobile\":\"0\",\"name\":\"sitevideo.browselocation-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_video_map','Advanced Videos - Browse Videos\' Locations',NULL,'Browse Videos\' Locations','This is the video browse locations page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=6 limit 1),'3','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.browselocation-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'6','{\"title\":\"\",\"titleCount\":true,\"showAllCategories\":\"1\",\"locationDetection\":\"1\",\"videoOption\":[\"title\",\"owner\",\"creationDate\",\"view\",\"like\",\"comment\",\"location\",\"duration\",\"rating\",\"watchlater\",\"favourite\",\"facebook\",\"twitter\",\"linkedin\",\"googleplus\"],\"nomobile\":\"0\",\"name\":\"sitevideo.browselocation-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_video_map' ;
");
            }
        }
    }

    public function setChannelEditVideos($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_channel_editvideos');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_editvideos','Advanced Videos - Manage Videos Page',NULL,'Manage Videos','This page is the manage videos page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_editvideos','Advanced Videos - Manage Videos Page',NULL,'Manage Videos','This page is the manage videos page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_channel_editvideos','Advanced Videos - Manage Videos Page',NULL,'Manage Videos','This page is the manage videos page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_channel_editvideos' ;
");
            }
        }
    }

    public function setBadgeCreate($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_badge_create');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_badge_create','Advanced Videos - Channel Share by Badge Page',NULL,'Channel Share by Badge','This page is the channel share by badge page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_badge_create','Advanced Videos - Channel Share by Badge Page',NULL,'Channel Share by Badge','This page is the channel share by badge page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_badge_create','Advanced Videos - Channel Share by Badge Page',NULL,'Channel Share by Badge','This page is the channel share by badge page.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'2',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.navigation',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=1 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','core.content',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=2 limit 1),'1',NULL,NULL from engine4_core_pages where name = 'sitevideo_badge_create' ;
");
            }
        }
    }

    public function setManagePlaylist($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitevideo_playlist_manage');
        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }
        if (!$page_id) {
            if ($this->getCurrentActivateTheme($default) == 3) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_manage','Advanced Videos - My Playlists Page',NULL,'My Playlists Page','This page lists playlist a user\'s playlists.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-playlists-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'5','{\"title\":\"\",\"itemCountPerPage\":\"10\",\"playlistOrder\":\"creation_date\",\"playlistVideoOrder\":\"creation_date\",\"playlistGridViewWidth\":\"400\",\"playlistGridViewHeight\":\"280\",\"show_content\":\"2\",\"videoShowLinkCount\":\"3\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-playlists-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 2) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_manage','Advanced Videos - My Playlists Page',NULL,'My Playlists Page','This page lists playlist a user\'s playlists.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-playlists-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'5','{\"title\":\"\",\"itemCountPerPage\":\"10\",\"playlistOrder\":\"creation_date\",\"playlistVideoOrder\":\"creation_date\",\"playlistGridViewWidth\":\"400\",\"playlistGridViewHeight\":\"280\",\"show_content\":\"2\",\"videoShowLinkCount\":\"3\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-playlists-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
            } else if ($this->getCurrentActivateTheme($default) == 1) {
                $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_manage','Advanced Videos - My Playlists Page',NULL,'My Playlists Page','This page lists playlist a user\'s playlists.','','0','0','',NULL,NULL,'0','0');
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'6','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
                $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.my-playlists-sitevideo',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'5','{\"title\":\"\",\"itemCountPerPage\":\"10\",\"playlistOrder\":\"creation_date\",\"playlistVideoOrder\":\"creation_date\",\"playlistGridViewWidth\":\"400\",\"playlistGridViewHeight\":\"280\",\"show_content\":\"2\",\"videoShowLinkCount\":\"3\",\"nomobile\":\"0\",\"name\":\"sitevideo.my-playlists-sitevideo\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_manage' ;
");
            }
        }
    }

    public function memberProfileChannelParameter($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('user_profile_index');

        if ($page_id && $reset) {
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'sitevideo.my-channels-sitevideo' AND `engine4_core_content`.`page_id` = $page_id");
            $select = new Zend_Db_Select($db);

            $content_id = $select
                    ->from('engine4_core_content', 'content_id')
                    ->where('name = ?', 'core.container-tabs')
                    ->where('type = ?', 'widget')
                    ->where('page_id = ?', $page_id)
                    ->query()
                    ->fetchColumn();

            if ($content_id) {
                if ($this->getCurrentActivateTheme($default) == 3) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.my-channels-sitevideo',
                        'parent_content_id' => $content_id,
                        'order' => 9,
                        'params' => '{"title":"Channels","titleCount":true,"topNavigationLink":"","channelNavigationLink":["channel"],"viewType":["gridView"],"channelOption":["title","like","comment","favourite","rating","numberOfVideos","subscribe","facebook","twitter","linkedin","googleplus"],"defaultViewType":"gridView","searchButton":"0","videoViewWidth":"150","videoViewHeight":"150","gridViewWidth":"282","gridViewHeight":"255","show_content":"2","itemCountPerPage":"12","titleTruncation":"100","descriptionTruncation":"200","nomobile":"0","name":"sitevideo.my-channels-sitevideo"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 2) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.my-channels-sitevideo',
                        'parent_content_id' => $content_id,
                        'order' => 9,
                        'params' => '{"title":"Channels","titleCount":true,"topNavigationLink":"","channelNavigationLink":["channel"],"viewType":["gridView"],"channelOption":["title"],"defaultViewType":"gridView","searchButton":"0","videoViewWidth":"150","videoViewHeight":"150","gridViewWidth":"242","gridViewHeight":"230","show_content":"2","itemCountPerPage":"12","titleTruncation":"100","descriptionTruncation":"200","nomobile":"0","name":"sitevideo.my-channels-sitevideo"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 1) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.my-channels-sitevideo',
                        'parent_content_id' => $content_id,
                        'order' => 9,
                        'params' => '{"title":"Channels","titleCount":true,"topNavigationLink":"","channelNavigationLink":["channel"],"viewType":["gridView"],"channelOption":["title"],"defaultViewType":"gridView","searchButton":"0","videoViewWidth":"150","videoViewHeight":"150","gridViewWidth":"231","gridViewHeight":"230","show_content":"2","itemCountPerPage":"12","titleTruncation":"100","descriptionTruncation":"200","nomobile":"0","name":"sitevideo.my-channels-sitevideo"}',
                    ));
                }
            }
        }
    }

    public function memberProfileVideoParameter($reset, $default) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('user_profile_index');

        if ($page_id && $reset) {
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'sitevideo.my-videos-sitevideo' AND `engine4_core_content`.`page_id` = $page_id");
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'sitevideoview.profile-videos' AND `engine4_core_content`.`page_id` = $page_id");
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'video.profile-videos' AND `engine4_core_content`.`page_id` = $page_id");
            $select = new Zend_Db_Select($db);

            $content_id = $select
                    ->from('engine4_core_content', 'content_id')
                    ->where('name = ?', 'core.container-tabs')
                    ->where('type = ?', 'widget')
                    ->where('page_id = ?', $page_id)
                    ->query()
                    ->fetchColumn();

            if ($content_id) {

                if ($this->getCurrentActivateTheme($default) == 3) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.my-videos-sitevideo',
                        'parent_content_id' => $content_id,
                        'order' => 8,
                        'params' => '{"title":"Videos","itemCountPerPage":"12","topNavigationLink":"","videoNavigationLink":["video"],"viewType":["gridView"],"videoOption":["title","creationDate","view","like","comment","duration","favourite","rating","watchlater","facebook","twitter","linkedin","googleplus"],"searchButton":"0","defaultViewType":"gridView","videoViewWidth":"150","videoViewHeight":"150","gridViewWidth":"282","gridViewHeight":"255","show_content":"2","orderby":"creation_date","titleTruncation":"100","titleTruncationGridNVideoView":"100","descriptionTruncation":"100","ad_header":null,"videoShowLinkCount":"3","playlistViewType":"","playlistDefaultViewType":"gridView","playlistGridViewWidth":"150","playlistGridViewHeight":"150","playlistOrder":"creation_date","playlistVideoOrder":"creation_date","showPlayAllOption":"1","watchlater_header":null,"watchlaterOrder":"creation_date","nomobile":"0","name":"sitevideo.my-videos-sitevideo"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 2) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.my-videos-sitevideo',
                        'parent_content_id' => $content_id,
                        'order' => 8,
                        'params' => '{"title":"Videos","itemCountPerPage":"12","topNavigationLink":"","videoNavigationLink":["video"],"viewType":["gridView"],"videoOption":["title","creationDate","view","like","comment","duration","favourite","rating","watchlater","facebook","twitter","linkedin","googleplus"],"searchButton":"0","defaultViewType":"gridView","videoViewWidth":"350","videoViewHeight":"200","gridViewWidth":"242","gridViewHeight":"255","show_content":"2","orderby":"creation_date","titleTruncation":"100","titleTruncationGridNVideoView":"19","descriptionTruncation":"100","ad_header":null,"videoShowLinkCount":"3","playlistViewType":"","playlistDefaultViewType":"gridView","playlistGridViewWidth":"150","playlistGridViewHeight":"150","playlistOrder":"creation_date","playlistVideoOrder":"creation_date","showPlayAllOption":"1","watchlater_header":null,"watchlaterOrder":"creation_date","nomobile":"0","name":"sitevideo.my-videos-sitevideo"}',
                    ));
                } else if ($this->getCurrentActivateTheme($default) == 1) {
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitevideo.my-videos-sitevideo',
                        'parent_content_id' => $content_id,
                        'order' => 8,
                        'params' => '{"title":"Videos","itemCountPerPage":"12","topNavigationLink":"","videoNavigationLink":["video"],"viewType":["gridView"],"videoOption":["title","creationDate","view","like","comment","duration","favourite","rating","watchlater","facebook","twitter","linkedin","googleplus"],"searchButton":"0","defaultViewType":"gridView","videoViewWidth":"350","videoViewHeight":"200","gridViewWidth":"231","gridViewHeight":"255","show_content":"2","orderby":"creation_date","titleTruncation":"100","titleTruncationGridNVideoView":"24","descriptionTruncation":"100","ad_header":null,"videoShowLinkCount":"3","playlistViewType":"","playlistDefaultViewType":"gridView","playlistGridViewWidth":"150","playlistGridViewHeight":"150","playlistOrder":"creation_date","playlistVideoOrder":"creation_date","showPlayAllOption":"1","watchlater_header":null,"watchlaterOrder":"creation_date","nomobile":"0","name":"sitevideo.my-videos-sitevideo"}',
                    ));
                }
            }
        }
    }

    public function videoQueries() {
        $db = Engine_Db_Table::getDefaultAdapter();

        /*
         * START => CHECKING FOR COLUMN EXISTENCE AND CREATE REQUIRED COLUMN
         */
        if (!$this->_columnExist('engine4_video_categories', 'cat_order')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN  `cat_order` smallint(3)  NOT NULL DEFAULT 0 ;");
        }
        if (!$this->_columnExist('engine4_video_categories', 'category_slug')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN  `category_slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL;");
        }
        if (!$this->_columnExist('engine4_video_categories', 'cat_dependency')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `cat_dependency` int(11) NOT NULL DEFAULT '0';");
        }
        if (!$this->_columnExist('engine4_video_categories', 'video_id')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `video_id` int(11) NOT NULL DEFAULT '0';");
        }
        if (!$this->_columnExist('engine4_video_categories', 'file_id')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `file_id` int(11) NOT NULL DEFAULT '0';");
        }
        if (!$this->_columnExist('engine4_video_categories', 'banner_id')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `banner_id` int(11) NOT NULL DEFAULT '0';");
        }
        if (!$this->_columnExist('engine4_video_categories', 'banner_title')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `banner_title` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL;");
        }
        if (!$this->_columnExist('engine4_video_categories', 'banner_url')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `banner_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL;");
        }
        if (!$this->_columnExist('engine4_video_categories', 'banner_url_window')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `banner_url_window` tinyint(1) NOT NULL DEFAULT '0';");
        }
        if (!$this->_columnExist('engine4_video_categories', 'sponsored')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `sponsored` tinyint(1) NOT NULL DEFAULT '0';");
        }
        if (!$this->_columnExist('engine4_video_categories', 'profile_type')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `profile_type` int(11) NOT NULL DEFAULT '0';");
        }
        if (!$this->_columnExist('engine4_video_categories', 'meta_title')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `meta_title` text COLLATE utf8_unicode_ci;");
        }

        if (!$this->_columnExist('engine4_video_categories', 'meta_description')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `meta_description` text COLLATE utf8_unicode_ci;");
        }
        if (!$this->_columnExist('engine4_video_categories', 'meta_keywords')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `meta_keywords` text COLLATE utf8_unicode_ci;");
        }
        if (!$this->_columnExist('engine4_video_categories', 'top_content')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `top_content` text COLLATE utf8_unicode_ci NOT NULL;");
        }
        if (!$this->_columnExist('engine4_video_categories', 'bottom_content')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `bottom_content` text COLLATE utf8_unicode_ci NOT NULL;");
        }
        if (!$this->_columnExist('engine4_video_categories', 'banner_description')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `banner_description` text COLLATE utf8_unicode_ci;");
        }

        if (!$this->_columnExist('engine4_video_categories', 'subcat_dependency')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `subcat_dependency` text COLLATE utf8_unicode_ci;");
        }

        if (!$this->_columnExist('engine4_video_categories', 'featured_tagline')) {
            $db->query("ALTER  TABLE  engine4_video_categories ADD COLUMN   `featured_tagline` text COLLATE utf8_unicode_ci ;");
        }
        if (!$this->_columnExist('engine4_video_videos', 'main_channel_id')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN main_channel_id INT(11);");
        }
        if (!$this->_columnExist('engine4_video_videos', 'subcategory_id')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN subcategory_id INT(11);");
        }
        if (!$this->_columnExist('engine4_video_videos', 'subsubcategory_id')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN subsubcategory_id INT(11);");
        }
        if (!$this->_columnExist('engine4_video_videos', 'profile_type')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN profile_type INT(11);");
        }
        if (!$this->_columnExist('engine4_video_videos', 'featured')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN featured tinyint(1) NOT NULL DEFAULT 0;");
        }
        if (!$this->_columnExist('engine4_video_videos', 'favourite_count')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN favourite_count int(11) NOT NULL DEFAULT 0;");
        }
        if (!$this->_columnExist('engine4_video_videos', 'sponsored')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN sponsored tinyint(1) NOT NULL DEFAULT 0;");
        }
        if (!$this->_columnExist('engine4_video_videos', 'seao_locationid')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN seao_locationid INT(11) NOT NULL;");
        }
        if (!$this->_columnExist('engine4_video_videos', 'location')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN location varchar(264) COLLATE utf8_unicode_ci NOT NULL;");
        }

        if (!$this->_columnExist('engine4_video_videos', 'networks_privacy')) {
            $db->query("ALTER TABLE engine4_video_videos ADD COLUMN networks_privacy MEDIUMTEXT NULL;");
        }

        if (!$this->_columnExist('engine4_video_videos', 'password')) {
            $db->query('ALTER TABLE `engine4_video_videos` ADD `password` char(32) COLLATE utf8_unicode_ci DEFAULT NULL;');
        }
        if (!$this->_columnExist('engine4_video_videos', 'like_count')) {
            $db->query('ALTER TABLE `engine4_video_videos` ADD `like_count` INT(11) DEFAULT 0;');
        }
        if (!$this->_columnExist('engine4_video_videos', 'synchronized')) {
            $db->query('ALTER TABLE `engine4_video_videos` ADD `synchronized` INT(11) DEFAULT 0;');
        }
    }

    public function videoCategoriesQueries() {
        $db = Engine_Db_Table::getDefaultAdapter();
        /*
         * START => INSERT VIDEO CATEGORIES
         */
        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Autos & Vehicles')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Autos & Vehicles',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "If You Don't Take a Chance, You Don't Stand a Chance",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 51,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'No matter how bad your day is your bike will always make you feel better.',
                'featured_tagline' => 'Find Your Own Road for the Love of Your Car',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "If You Don't Take a Chance, You Don't Stand a Chance",
                'banner_description' => 'No matter how bad your day is your bike will always make you feel better.',
                'featured_tagline' => 'Find Your Own Road for the Love of Your Car',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Comedy')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Comedy',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => 'Follow Your Heart But Take Your Brain With You',
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 55,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Laughing so hard, no noise coming out, so you sit there clapping like a retarded seal.',
                'featured_tagline' => 'Being Happy is the New Trend',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => 'Follow Your Heart But Take Your Brain With You',
                'banner_description' => 'Laughing so hard, no noise coming out, so you sit there clapping like a retarded seal.',
                'featured_tagline' => 'Being Happy is the New Trend',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Education')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Education',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => 'The Starting Point of all Achievement is Desire',
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 60,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Education is the key to unlock the golden door of freedom.',
                'featured_tagline' => 'Education - The Foundation of Oneself',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => 'The Starting Point of all Achievement is Desire',
                'banner_description' => 'Education is the key to unlock the golden door of freedom.',
                'featured_tagline' => 'Education - The Foundation of Oneself',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Entertainment')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Entertainment',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => 'Thoughts Create More Noise Than the Voice',
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 65,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Sometimes you have to burn a few bridges to keep the crazies from following you.',
                'featured_tagline' => 'Sometimes all You Need is a Little Beat',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => 'Thoughts Create More Noise Than the Voice',
                'banner_description' => 'Sometimes you have to burn a few bridges to keep the crazies from following you.',
                'featured_tagline' => 'Sometimes all You Need is a Little Beat',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Film & Animation')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Film & Animation',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => 'Fear is Stupid, So are Regrets',
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 70,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'The most courageous act is still to think for yourself aloud.',
                'featured_tagline' => 'Bring Your Imagination to Life',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => 'Fear is Stupid, So are Regrets',
                'banner_description' => 'The most courageous act is still to think for yourself aloud.',
                'featured_tagline' => 'Bring Your Imagination to Life',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Gaming')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Gaming',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "Gamers Don't Fear the Apocalypse",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 75,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => "We've seen it many times before.",
                'featured_tagline' => 'It’s Not Just a Game, it’s an Irresistible Force',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "Gamers Don't Fear the Apocalypse",
                'banner_description' => "We've seen it many times before.",
                'featured_tagline' => 'It’s Not Just a Game, it’s an Irresistible Force',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Howto & Style')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Howto & Style',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "Life isn't About Getting More",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 80,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => "It's About Becoming More.",
                'featured_tagline' => 'Have Style Wherever You Are',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "Life isn't About Getting More",
                'banner_description' => "It's About Becoming More.",
                'featured_tagline' => 'Have Style Wherever You Are',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Music')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Music',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "Where Word Fail, Music Speaks",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 85,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Music in the soul can be heard by the universe.',
                'featured_tagline' => 'Music Brings You Closer To Life',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "Where Word Fail, Music Speaks",
                'banner_description' => 'Music in the soul can be heard by the universe.',
                'featured_tagline' => 'Music Brings You Closer To Life',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'News & Politics')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'News & Politics',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "If it Doesn't Challenge You, It Won't Change You",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 90,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => "Don't wait for the storm to pass, Learn to dance in the rain.",
                'featured_tagline' => 'Always Taking the Lead',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "If it Doesn't Challenge You, It Won't Change You",
                'banner_description' => "Don't wait for the storm to pass, Learn to dance in the rain.",
                'featured_tagline' => 'Always Taking the Lead',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'People & Blogs')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'People & Blogs',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "Succes is Not For the Lazy",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 100,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Imagine with all your mind. Believe with all your heart. Achieve with all your might.',
                'featured_tagline' => 'Show Your Innerself !!',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "Succes is Not For the Lazy",
                'banner_description' => 'Imagine with all your mind. Believe with all your heart. Achieve with all your might.',
                'featured_tagline' => 'Show Your Innerself !!',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Pets & Animals')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Pets & Animals',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "An Animal's Eyes Have the Power to Speak a Great Language",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 105,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Adopt and adore, don’t buy from a pet-store.',
                'featured_tagline' => "Until one has loved an animal a part of one's soul remains lost",
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "An Animal's Eyes Have the Power to Speak a Great Language",
                'banner_description' => 'Adopt and adore, don’t buy from a pet-store.',
                'featured_tagline' => "Until one has loved an animal a part of one's soul remains lost",
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Science & Technology')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Science & Technology',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "The Starting Point of all Achievement is Desire",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 110,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Education is the key to unlock the golden door of freedom.',
                'featured_tagline' => 'A Step Towards Innovation',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "The Starting Point of all Achievement is Desire",
                'banner_description' => 'Education is the key to unlock the golden door of freedom.',
                'featured_tagline' => 'A Step Towards Innovation',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Sports')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Sports',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "Sports is the Life With the Volume Turned Up",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 115,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Good players inspire themselves, great players inspire others.',
                'featured_tagline' => 'A Sport a Day Helps You Work, Rest and Play',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "Sports is the Life With the Volume Turned Up",
                'banner_description' => 'Good players inspire themselves, great players inspire others.',
                'featured_tagline' => 'A Sport a Day Helps You Work, Rest and Play',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Travel & Events')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Travel & Events',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "Life is Short and the World is Wide",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 120,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Travel is more than the seeing of sights. It is a change that goes on, deep & permanent, in the ideas of living.',
                'featured_tagline' => 'Discover Your Own World',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "Life is Short and the World is Wide",
                'banner_description' => 'Travel is more than the seeing of sights. It is a change that goes on, deep & permanent, in the ideas of living.',
                'featured_tagline' => 'Discover Your Own World',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }

        $categorieId = $db->select()->from('engine4_video_categories', 'category_id')
                ->where('category_name = ?', 'Others')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$categorieId) {
            $db->insert('engine4_video_categories', array(
                'category_name' => 'Others',
                'category_slug' => '',
                'cat_dependency' => 0,
                'video_id' => 0,
                'file_id' => 0,
                'banner_id' => 0,
                'banner_title' => "Imperfection is Individuality",
                'banner_url' => NULL,
                'banner_url_window' => 0,
                'cat_order' => 125,
                'sponsored' => 0,
                'profile_type' => 0,
                'meta_title' => NULL,
                'meta_description' => NULL,
                'meta_keywords' => NULL,
                'top_content' => '',
                'bottom_content' => '',
                'banner_description' => 'Life is 10% what happens to us and 90 % how we react to it.',
                'featured_tagline' => 'Explore Yourself till the Edge',
            ));
        } else {
            $db->update('engine4_video_categories', array(
                'banner_title' => "Imperfection is Individuality",
                'banner_description' => 'Life is 10% what happens to us and 90 % how we react to it.',
                'featured_tagline' => 'Explore Yourself till the Edge',
                    ), array(
                'category_id =?' => $categorieId,
            ));
        }
    }

    function _columnExist($table, $column) {
        $db = Engine_Db_Table::getDefaultAdapter();

        $columnName = $db->query("
        SHOW COLUMNS FROM `$table`
           LIKE '$column'")->fetch();
        if (!empty($columnName))
            return true;
        return false;
    }

}

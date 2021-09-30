<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settemplate.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Api_Settemplate extends Core_Api_Abstract {

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

    public function setAlbumsHomePage($reset = false) {
        //ADVANCED ALBUMS - ALBUM HOME PAGE
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitealbum_index_index');

        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (empty($page_id)) {
            $widgetCount = 0;
            $db->insert('engine4_core_pages', array(
                'name' => 'sitealbum_index_index',
                'displayname' => 'Advanced Albums - Albums Home Page',
                'title' => 'Advanced Albums - Albums Home Page',
                'description' => 'This is albums home page.',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 6,
                'params' => '',
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 6,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // Top Middle
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.featured-photos',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true,"width":"","height":"500","contentFullWidth":"1","speed":"5000","popularType":"date_taken","featuredPhotositemCount":"10","featuredPhotosHtmlTitle":"The community for all your photos.","featuredPhotosHtmlDescription":"Upload, access, organize, edit, and share your photos.","featuredPhotosSearchBox":"1","featuredAlbums":"1","nomobile":"0","name":"sitealbum.featured-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.html-block-albums-photos',
                'parent_content_id' => $middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.list-photos-tabs-view',
                'parent_content_id' => $middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","margin_photo":"10","showViewMore":"1","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"0","showPhotosInJustifiedView":"1","rowHeight":"300","maxRowHeight":"500","margin":"5","lastRow":"justify","photoHeight":"260","photoWidth":"290","columnHeight":"260","show_content":"1","loaded_by_ajax":"1","photoInfo":["ownerName","albumTitle"],"ajaxTabs":["featuredphotos"],"recentphotos":"1","most_likedphotos":"2","most_viewedphotos":"3","most_commentedphotos":"4","featuredphotos":"1","randomphotos":"6","most_ratedphotos":"7","orderBy":"creation_date","showPhotosInLightbox":"1","limit":"10","truncationLocation":"50","photoTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-photos-tabs-view"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.categories-sponsored',
                'parent_content_id' => $middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Browse by Category","titleCount":true,"itemCount":"0","showIcon":"1","columnPerRow":"4","nomobile":"0","name":"sitealbum.categories-sponsored"}',
            ));

            $select = new Zend_Db_Select($db);
            $album_ids = $select
                            ->from('engine4_album_albums', 'album_id')
                            ->order('Rand()')
                            ->limit(3)
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

            foreach ($album_ids as $album_id) {
                $db->query("UPDATE `engine4_album_albums` SET `featured` = '1' WHERE `engine4_album_albums`.`album_id` = $album_id;");
            }

            $select = new Zend_Db_Select($db);
            $photo_ids = $select
                            ->from('engine4_album_photos', 'photo_id')
                            ->order('Rand()')
                            ->limit(10)
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

            foreach ($photo_ids as $photo_id) {
                $db->query("UPDATE `engine4_album_photos` SET `featured` = '1' WHERE `engine4_album_photos`.`photo_id` = $photo_id;");
            }

            //INSERT LAST PHOTO ID INTO ENGINE4_CORE_SETTINGS TABLE
            $select = new Zend_Db_Select($db);
            $photo_id = $select
                    ->from('engine4_album_photos')
                    ->order('photo_id Desc')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            $db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = 'sitealbum.last.photoid';");
            $db->insert('engine4_core_settings', array('name' => 'sitealbum.last.photoid', 'value' => $photo_id));
        }
    }

    public function setAlbumsBrowsePage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitealbum_index_browse');

        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (empty($page_id)) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'sitealbum_index_browse',
                'displayname' => 'Advanced Albums - Albums Browse Page',
                'title' => 'Advanced Albums Browse Page',
                'description' => 'This page lists album entries.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.searchbox-sitealbum',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"","locationDetection":"0","formElements":["textElement","categoryElement","locationElement","locationmilesSearch"],"categoriesLevel":["category","subcategory"],"showAllCategories":"1","textWidth":"440","locationWidth":"330","locationmilesWidth":"120","categoryWidth":"220","nomobile":"0","name":"sitealbum.searchbox-sitealbum"}'
            ));

            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.categories-banner-sitealbum',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":true}',
            ));
            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.browse-albums-sitealbum',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 4,
                'params' => '{"title":"","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","margin_photo":"5","photoHeight":"300","photoWidth":"377","albumInfo":["ownerName","viewCount","likeCount","commentCount","ratingStar","albumTitle","totalPhotos","facebook","twitter","linkedin","google"],"infoOnHover":"1","columnHeight":"310","customParams":"1","orderby":"viewCount","show_content":"3","truncationLocation":"50","albumTitleTruncation":"50","limit":"25","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.browse-albums-sitealbum"}',
            ));
        }
        //END ALBUM BROWSE PAGE
    }

    public function setAlbumsPhotoBrowsePage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitealbum_photo_browse');

        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (empty($page_id)) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'sitealbum_photo_browse',
                'displayname' => 'Advanced Albums - Photos Browse Page',
                'title' => 'Advanced Albums - Photos Browse Page',
                'description' => 'This page lists photos entries.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));


            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.search-sitephoto',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":true,"viewType":"horizontal","showAllCategories":"1","locationDetection":"0","whatWhereWithinmile":"0","advancedSearch":"0","nomobile":"0","name":"sitealbum.search-sitealbum"}',
            ));
            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.browse-photos-sitealbum',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 5,
                'params' => '{"title":"","itemCountPerPage":"24","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","showPhotosInJustifiedView":"1","rowHeight":"375","maxRowHeight":"0","margin":"10","lastRow":"nojustify","photoHeight":"375","photoWidth":"208","orderby":"featuredTakenBy","show_content":"3","photoInfo":["ownerName","creationDate","viewCount","likeCount","commentCount","ratingStar","photoTitle","albumTitle"],"truncationLocation":"35","photoTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.browse-albums-sitealbum"}',
            ));
        }
        //END PHOTO BROWSE PAGE
    }

    public function setAlbumsLocationsPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitealbum_index_map');

        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (empty($page_id)) {
            $containerCount = 0;
            $widgetCount = 0;

            $db->insert('engine4_core_pages', array(
                'name' => "sitealbum_index_map",
                'displayname' => "Advanced Albums - Browse Albums' Locations",
                'title' => "Browse Albums' Locations",
                'description' => 'This is the album browse locations page.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

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
                'name' => 'sitealbum.navigation',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.bylocation-album',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Search","titleCount":true,"photoHeight":"195","photoWidth":"200","albumInfo":["ownerName","creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos","facebook","twitter","linkedin","google"],"truncationLocation":"50","showAllCategories":"1","locationDetection":"0","nomobile":"0","name":"sitealbum.bylocation-album"}',
            ));
        }
        //END LOCATION OR MAP.
    }

    public function setAlbumsPinboardViewPage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();

        $containerCount = 0;
        $widgetCount = 0;
        $page_id = $this->checkPageId('sitealbum_index_pinboard');

        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (empty($page_id)) {
            //CREATE PAGE
            $db->insert('engine4_core_pages', array(
                'name' => "sitealbum_index_pinboard",
                'displayname' => 'Advanced Albums - Browse Albums’ Pinboard View',
                'title' => 'Advanced Album Pinboard View Page',
                'description' => 'This is the browse albums pinboard view page.',
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
                'name' => 'sitealbum.navigation',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'seaocore.scroll-top',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.pinboard-browse',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","show_buttons":["comment","like","facebook","twitter","pinit"],"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","albumInfo":["ownerName","creationDate","viewCount","likeCount","commentCount","ratingStar","categoryLink","albumTitle","totalPhotos"],"customParams":"5","userComment":"1","autoload":"1","defaultLoadingImage":"1","itemWidth":"385","withoutStretch":"0","orderby":"featured","itemCount":"12","truncationLocation":"50","albumTitleTruncation":"100","truncationDescription":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.pinboard-browse"}',
            ));
        }
    }

    public function setAlbumsCategoriesHomePage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitealbum_index_categories');

        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (empty($page_id)) {

            $containerCount = 0;
            $widgetCount = 0;

            $db->insert('engine4_core_pages', array(
                'name' => "sitealbum_index_categories",
                'displayname' => 'Advanced Albums - Categories Home',
                'title' => 'Categories Home',
                'description' => 'This is the categories home page.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

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
                'name' => 'sitealbum.navigation',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.searchbox-sitealbum',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":"","locationDetection":"0","formElements":["textElement","categoryElement","locationElement","locationmilesSearch"],"categoriesLevel":["category","subcategory"],"showAllCategories":"1","textWidth":"440","locationWidth":"330","locationmilesWidth":"120","categoryWidth":"220","nomobile":"0","name":"sitealbum.searchbox-sitealbum"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.categories-grid-view',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Categories","titleCount":true,"orderBy":"cat_order","showAllCategories":"1","showSubCategoriesCount":"5","showCount":"0","columnWidth":"280","columnHeight":"280","nomobile":"0","name":"sitealbum.categories-grid-view"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.list-albums-tabs-view',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Albums","margin_photo":"5","showViewMore":"1","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","photoHeight":"300","photoWidth":"280","columnHeight":"330","show_content":"1","loaded_by_ajax":"1","albumInfo":["ownerName","creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos","facebook","twitter","linkedin","google"],"infoOnHover":"1","ajaxTabs":["recentalbums","mostZZZlikedalbums","mostZZZviewedalbums","mostZZZcommentedalbums","featuredalbums","randomalbums","mostZZZratedalbums"],"recentalbums":"7","most_likedalbums":"2","most_viewedalbums":"5","most_commentedalbums":"3","featuredalbums":"1","randomalbums":"6","most_ratedalbums":"4","orderBy":"creation_date","titleLink":"<a href=\"\/albums\/browse\">Explore Albums \u00bb<\/a>","limit":"12","truncationLocation":"70","albumTitleTruncation":"70","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-albums-tabs-view"}',
            ));
        }
    }

    public function setAlbumsManagePage($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        //START ALBUM MANAGE PAGE
        $page_id = $this->checkPageId('sitealbum_index_manage');

        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (empty($page_id)) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'sitealbum_index_manage',
                'displayname' => 'Advanced Albums - My Albums Page',
                'title' => 'Advanced Albums - My Albums Page',
                'description' => 'This page lists album a user\'s albums.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.navigation',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.my-albums-sitealbum',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'params' => '{"title":"","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","margin_photo":"5","photoHeight":"300","photoWidth":"435","columnHeight":"310","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos","facebook","twitter","linkedin","google"],"album_view_type":"2","show_content":"1","limit":"12","truncationLocation":"50","albumTitleTruncation":"70","truncationDescription":"200","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.my-albums-sitealbum"}',
                'order' => 1,
            ));

            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.search-sitealbum',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","whatWhereWithinmile":"0","advancedSearch":"0","locationDetection":"0","nomobile":"0","name":"sitealbum.search-sitealbum"}',
            ));

            // Insert browse menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.upload-photo-sitealbum',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":true,"upload_button":"1","upload_button_title":"Add New Photos","nomobile":"0","name":"sitealbum.upload-photo-sitealbum"}'
            ));

            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.list-popular-albums',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Popular Albums","itemCountPerPage":"2","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"0","popularType":"comment","interval":"overall","photoHeight":"205","photoWidth":"203","albumInfo":"","titleLink":"<a href=\"\/albums\/browse\">Explore Albums \u00bb<\/a>","truncationLocation":"50","albumTitleTruncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-albums"}',
            ));
        }
    }

    public function setAlbumsViewPageWithCoverPhoto($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitealbum_album_view');

        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (!$page_id) {
            $containerCount = 0;
            $widgetCount = 0;

            $db->insert('engine4_core_pages', array(
                'name' => 'sitealbum_album_view',
                'displayname' => 'Advanced Albums - Album View Page',
                'title' => 'Advanced Albums - Album View Page',
                'description' => ' This is the main view page of an album.',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');
            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();


            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $right_container_id = $db->lastInsertId();

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
                'name' => 'core.container-tabs',
                'parent_content_id' => $main_middle_id,
                'order' => $containerCount++,
                'params' => '{"max":"5","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $tab_id = $db->lastInsertId('engine4_core_content');

            if (Engine_Api::_()->hasModuleBootstrap('spectacular')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecontentcoverphoto.content-cover-photo',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"modulename":"album","showContent_0":"","showContent_album":["mainPhoto","title","owner","description","totalPhotos","viewCount","likeCount","commentCount","rating","optionsButton","shareOptions","uploadPhotos"],"showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitereview_listing_12":"","showContent_sitestore_store":"","profile_like_button":"1","columnHeight":"400","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","contentFullWidth":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecontentcoverphoto.content-cover-photo',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"modulename":"album","showContent_0":"","showContent_album":["mainPhoto","title","owner","description","totalPhotos","viewCount","likeCount","commentCount","rating","optionsButton","shareOptions","uploadPhotos"],"showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitereview_listing_12":"","showContent_sitestore_store":"","profile_like_button":"1","columnHeight":"400","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","contentFullWidth":"0","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                ));
            }

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.album-view',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"titleCount":true,"itemCountPerPage":"12","margin_photo":"2","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"250","photoWidth":"280","columnHeight":"300","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip"],"show_content":"2","title":"Photos","nomobile":"0","name":"sitealbum.album-view"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.profile-photos',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"View all albums","titleCount":true,"itemCountPerPage":"8","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["albums"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"205","photoWidth":"212","photoColumnHeight":"250","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"240","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.profile-photos',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Photos of Owner","titleCount":true,"itemCountPerPage":"8","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["photosofyou"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"250","photoWidth":"280","photoColumnHeight":"250","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"240","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
            ));


            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.profile-photos',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Owner\'s Photos","titleCount":true,"itemCountPerPage":"8","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["yourphotos"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"200","photoWidth":"280","photoColumnHeight":"200","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"240","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.profile-photos',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Liked Photos","titleCount":true,"itemCountPerPage":"8","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["likesphotos"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"205","photoWidth":"205","photoColumnHeight":"250","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"250","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.user-ratings',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"User Ratings","titleCount":true}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.make-featured-link',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","nomobile":"0","name":"sitealbum.make-featured-link"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.specification-sitealbum',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Additional Information","titleCount":true,"name":"sitealbum.specification-sitealbum"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.friends-photos',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"itemCountPhoto":"2","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","featured":"0","photoHeight":"220","photoWidth":"220","photoInfo":["ownerName","ratingStar","albumTitle"],"truncationLocation":"50","photoTitleTruncation":"100","title":"Friends\' Photos","nomobile":"0","name":"sitealbum.friends-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.friends-photo-albums',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"itemCountAlbum":"2","itemCountPhoto":"2","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","featured":"1","albumInfo":["ownerName"],"truncationLocation":"50","albumTitleTruncation":"100","title":"Friends\' Albums Photos","nomobile":"0","name":"sitealbum.friends-photo-albums"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.information-sitealbum',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Information","titleCount":true,"showContent":["totalPhotos","creationDate","updateDate","viewCount","likeCount","commentCount","location","directionLink","socialShare","tags","categoryLink"],"nomobile":"0","name":"sitealbum.information-sitealbum"}',
            ));


            $select = new Zend_Db_Select($db);
            $sitetagcheckinEnabled = $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitetagcheckin')
                    ->where('enabled = ?', '1')
                    ->query()
                    ->fetchObject();
            if (!empty($sitetagcheckinEnabled)) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
                ));
            }

            $select = new Zend_Db_Select($db);
            $communityadEnabled = $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'communityad')
                    ->where('enabled = ?', '1')
                    ->query()
                    ->fetchObject();
            if (!empty($communityadEnabled)) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'communityad.ads',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"loaded_by_ajax":"0","title":"","name":"communityad.ads","show_type":"all","itemCount":"4","showOnAdboard":"1","packageIds":"","nomobile":"0"}',
                ));
            }

            $db->query("UPDATE `engine4_sitecontentcoverphoto_modules` SET `enabled` = '1' WHERE `engine4_sitecontentcoverphoto_modules`.`module` ='sitealbum' LIMIT 1 ;");
        }
    }

    public function setAlbumsViewPageWithoutCoverPhoto($reset = false) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('sitealbum_album_view');

        if ($page_id && $reset) {
            $this->deletePageAndContent($page_id);
            $page_id = false;
        }

        if (!$page_id) {
            $containerCount = 0;
            $widgetCount = 0;

            $db->insert('engine4_core_pages', array(
                'name' => 'sitealbum_album_view',
                'displayname' => 'Advanced Albums - Album View Page',
                'title' => 'Advanced Albums - Album View Page',
                'description' => ' This is the main view page of an album.',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $right_container_id = $db->lastInsertId();

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
                'name' => 'core.container-tabs',
                'parent_content_id' => $main_middle_id,
                'order' => $containerCount++,
                'params' => '{"max":"5","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $tab_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.top-content-of-album',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true,"showInformationOptions":["title","owner","description","location","updateddate","likeButton","categoryLink","tags","editmenus","facebooklikebutton","checkinbutton"],"nomobile":"0","name":"sitealbum.top-content-of-album"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.album-view',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"titleCount":true,"itemCountPerPage":"12","margin_photo":"2","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"250","photoWidth":"280","columnHeight":"300","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip"],"show_content":"2","title":"Photos","nomobile":"0","name":"sitealbum.album-view"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.profile-photos',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"View all albums","titleCount":true,"itemCountPerPage":"8","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["albums"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"205","photoWidth":"212","photoColumnHeight":"250","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"240","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.profile-photos',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Photos of Owner","titleCount":true,"itemCountPerPage":"8","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["photosofyou"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"250","photoWidth":"280","photoColumnHeight":"250","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"240","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.profile-photos',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Owner\'s Photos","titleCount":true,"itemCountPerPage":"8","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["yourphotos"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"200","photoWidth":"280","photoColumnHeight":"200","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"240","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.profile-photos',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Liked Photos","titleCount":true,"itemCountPerPage":"8","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["likesphotos"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"205","photoWidth":"205","photoColumnHeight":"250","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"250","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.user-ratings',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"User Ratings","titleCount":true}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.make-featured-link',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","nomobile":"0","name":"sitealbum.make-featured-link"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.specification-sitealbum',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Additional Information","titleCount":true,"name":"sitealbum.specification-sitealbum"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.friends-photos',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"itemCountPhoto":"2","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","featured":"0","photoHeight":"220","photoWidth":"220","photoInfo":["ownerName","ratingStar","albumTitle"],"truncationLocation":"50","photoTitleTruncation":"100","title":"Friends\' Photos","nomobile":"0","name":"sitealbum.friends-photos"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.friends-photo-albums',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"itemCountAlbum":"2","itemCountPhoto":"2","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","featured":"1","albumInfo":["ownerName"],"truncationLocation":"50","albumTitleTruncation":"100","title":"Friends\' Albums Photos","nomobile":"0","name":"sitealbum.friends-photo-albums"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitealbum.information-sitealbum',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Information","titleCount":true,"showContent":["totalPhotos","creationDate","updateDate","viewCount","likeCount","commentCount","location","directionLink","socialShare","tags","categoryLink"],"nomobile":"0","name":"sitealbum.information-sitealbum"}',
            ));

            $select = new Zend_Db_Select($db);
            $sitetagcheckinEnabled = $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitetagcheckin')
                    ->where('enabled = ?', '1')
                    ->query()
                    ->fetchObject();
            if (!empty($sitetagcheckinEnabled)) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
                ));
            }

            $select = new Zend_Db_Select($db);
            $communityadEnabled = $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'communityad')
                    ->where('enabled = ?', '1')
                    ->query()
                    ->fetchObject();
            if (!empty($communityadEnabled)) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'communityad.ads',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"loaded_by_ajax":"0","title":"","name":"communityad.ads","show_type":"all","itemCount":"4","showOnAdboard":"1","packageIds":"","nomobile":"0"}',
                ));
            }
        }
    }

    public function setMemberProfileAlbumWidgetParameter() {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $this->checkPageId('user_profile_index');

        if ($page_id) {
            $db->query('UPDATE `engine4_core_content` SET `params` = \'{"title":"Photos","titleCount":true,"itemCountPerPage":"12","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["yourphotos","photosofyou","albums","likesphotos"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"211","photoWidth":"229","photoColumnHeight":"211","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"255","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}\' WHERE `page_id` = ' . $page_id . ' AND name = "sitealbum.profile-photos";');
        }
    }

}

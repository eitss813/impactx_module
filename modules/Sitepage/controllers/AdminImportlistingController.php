<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminImportlistingController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminImportlistingController extends Core_Controller_Action_Admin {

  //ACTION FOR IMPORTING DATA FROM LISTING TO PAGE
  public function indexAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    //START CODE FOR CREATING THE ListingToPageImport.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/ListingToPageImport.log')) {
      $log = new Zend_Log();
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/ListingToPageImport.log'));
      } catch (Exception $e) {
        //CHECK DIRECTORY
        if (!@is_dir(APPLICATION_PATH . '/temporary/log') && @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
          $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/ListingToPageImport.log'));
        } else {
          //Silence ...
          if (APPLICATION_ENV !== 'production') {
            $log->log($e->__toString(), Zend_Log::CRIT);
          } else {
            //MAKE SURE LOGGING DOESN'T CAUSE EXCEPTIONS
            $log->addWriter(new Zend_Log_Writer_Null());
          }
        }
      }
    }

    //GIVE WRITE PERMISSION IF FILE EXIST
    if (file_exists(APPLICATION_PATH . '/temporary/log/ListingToPageImport.log')) {
      @chmod(APPLICATION_PATH . '/temporary/log/ListingToPageImport.log', 0777);
    }
    //END CODE FOR CREATING THE ListingToPageImport.log FILE
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_import');

    //START IMPORTING WORK IF LIST AND SITEPAGE IS INSTALLED AND ACTIVATE
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('list') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {

      //ADD NEW COLUMN IN LISTING TABLE
      $db = Engine_Db_Table::getDefaultAdapter();
      $type_array = $db->query("SHOW COLUMNS FROM engine4_list_listings LIKE 'is_import'")->fetch();
      if (empty($type_array)) {
        $run_query = $db->query("ALTER TABLE `engine4_list_listings` ADD `is_import` TINYINT( 2 ) NOT NULL DEFAULT '0' AFTER `subcategory_id` ");
      }

      //START IF IMPORTING IS BREAKED BY SOME REASON
      $listingTable = Engine_Api::_()->getDbTable('listings', 'list');
      $listingTableName = $listingTable->info('name');
      $selectListings = $listingTable->select()
              ->from($listingTableName, 'listing_id')
              ->where('is_import != ?', 1)
              ->order('listing_id ASC');
      $listingDatas = $listingTable->fetchAll($selectListings);

      $this->view->first_listing_id = $first_listing_id = 0;
      $this->view->last_listing_id = $last_listing_id = 0;

      if (!empty($listingDatas)) {

        $flag_first_listing_id = 1;

        foreach ($listingDatas as $listingData) {

          if ($flag_first_listing_id == 1) {
            $this->view->first_listing_id = $first_listing_id = $listingData->listing_id;
          }
          $flag_first_listing_id++;

          $this->view->last_listing_id = $last_listing_id = $listingData->listing_id;
        }

        if (isset($_GET['assigned_previous_id'])) {
          $this->view->assigned_previous_id = $assigned_previous_id = $_GET['assigned_previous_id'];
        } else {
          $this->view->assigned_previous_id = $assigned_previous_id = $first_listing_id;
        }
      }
      //END IF IMPORTING IS BREAKED BY SOME REASON
      //START IMPORTING IF REQUESTED
      if (isset($_GET['start_import']) && $_GET['start_import'] == 1) {

        //START FETCH CATEGORY WORK
        $pageCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitepage');
        $pageCategoryTableName = $pageCategoryTable->info('name');
        $selectPageCategory = $pageCategoryTable->select()
                ->from($pageCategoryTableName, 'category_name')
                ->where('cat_dependency = ?', 0);
        $pageCategoryDatas = $pageCategoryTable->fetchAll($selectPageCategory);
        if (!empty($pageCategoryDatas)) {
          $pageCategoryDatas = $pageCategoryDatas->toArray();
        }

        $pageCategoryInArrayData = array();
        foreach ($pageCategoryDatas as $pageCategoryData) {
          $pageCategoryInArrayData[] = $pageCategoryData['category_name'];
        }

        $listCategoryTable = Engine_Api::_()->getDbtable('categories', 'list');
        $listCategoryTableName = $listCategoryTable->info('name');
        $selectListCategory = $listCategoryTable->select()
                ->from($listCategoryTableName)
                ->where('cat_dependency = ?', 0);
        $listCategoryDatas = $listCategoryTable->fetchAll($selectListCategory);
        if (!empty($listCategoryDatas)) {
          $listCategoryDatas = $listCategoryDatas->toArray();
          foreach ($listCategoryDatas as $listCategoryData) {
            if (!in_array($listCategoryData['category_name'], $pageCategoryInArrayData)) {
              $newCategory = $pageCategoryTable->createRow();
              //$newCategory->user_id = $listCategoryData['user_id'];
              $newCategory->category_name = $listCategoryData['category_name'];
              $newCategory->cat_dependency = 0;
              $newCategory->cat_order = 9999;
              $newCategory->save();

              $selectListSubCategory = $listCategoryTable->select()
                      ->from($listCategoryTableName)
                      ->where('cat_dependency = ?', $listCategoryData['category_id']);
              $listSubCategoryDatas = $listCategoryTable->fetchAll($selectListSubCategory);
              foreach ($listSubCategoryDatas as $listSubCategoryData) {
                $newSubCategory = $pageCategoryTable->createRow();
                //$newSubCategory->user_id = $listCategoryData['user_id'];
                $newSubCategory->category_name = $listSubCategoryData->category_name;
                $newSubCategory->cat_dependency = $newCategory->category_id;
                $newSubCategory->cat_order = 9999;
                $newSubCategory->save();
              }
            } elseif (in_array($listCategoryData['category_name'], $pageCategoryInArrayData)) {

              $pageCategory = $pageCategoryTable->fetchRow(array('category_name = ?' => $listCategoryData['category_name'], 'cat_dependency = ?' => 0));
              if (!empty($pageCategory))
                $pageCategoryId = $pageCategory->category_id;

              $selectPageSubCategory = $pageCategoryTable->select()
                      ->from($pageCategoryTableName, array('category_name'))
                      ->where('cat_dependency = ?', $pageCategoryId);
              $pageSubCategoryDatas = $pageCategoryTable->fetchAll($selectPageSubCategory);
              if (!empty($pageSubCategoryDatas)) {
                $pageSubCategoryDatas = $pageSubCategoryDatas->toArray();
              }

              $pageSubCategoryInArrayData = array();
              foreach ($pageSubCategoryDatas as $pageSubCategoryData) {
                $pageSubCategoryInArrayData[] = $pageSubCategoryData['category_name'];
              }

              $listCategory = $listCategoryTable->fetchRow(array('category_name = ?' => $listCategoryData['category_name'], 'cat_dependency = ?' => 0));
              if (!empty($listCategory))
                $listCategoryId = $listCategory->category_id;

              $selectListSubCategory = $listCategoryTable->select()
                      ->from($listCategoryTableName)
                      ->where('cat_dependency = ?', $listCategoryId);
              $listSubCategoryDatas = $listCategoryTable->fetchAll($selectListSubCategory);
              if (!empty($listSubCategoryDatas)) {
                $listSubCategoryDatas = $listSubCategoryDatas->toArray();
              }

              foreach ($listSubCategoryDatas as $listSubCategoryData) {
                if (!in_array($listSubCategoryData['category_name'], $pageSubCategoryInArrayData)) {
                  $newSubCategory = $pageCategoryTable->createRow();
                  //$newSubCategory->user_id = $listSubCategoryData['user_id'];
                  $newSubCategory->category_name = $listSubCategoryData['category_name'];
                  $newSubCategory->cat_dependency = $pageCategoryId;
                  $newSubCategory->cat_order = 9999;
                  $newSubCategory->save();
                }
              }
            }
          }
        }
        //END FETCH CATEGORY WOR
        //START COMMAN DATA
        $package_id = Engine_Api::_()->getItemtable('sitepage_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
        $package = Engine_Api::_()->getItemTable('sitepage_package')->fetchRow(array('package_id = ?' => $package_id));

        $metaTable = Engine_Api::_()->fields()->getTable('list_listing', 'meta');
        $selectMetaData = $metaTable->select()->where('type = ?', 'currency');
        $metaData = $metaTable->fetchRow($selectMetaData);

        $table = Engine_Api::_()->getDbtable('pages', 'sitepage');

        $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
        $likeTableName = $likeTable->info('name');

        $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
        $commentTableName = $commentTable->info('name');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
          $topicTable = Engine_Api::_()->getDbtable('topics', 'list');
          $topicTableName = $topicTable->info('name');
          $pageTopicTable = Engine_Api::_()->getDbtable('topics', 'sitepage');
          $pagePostTable = Engine_Api::_()->getDbtable('posts', 'sitepage');

          $postTable = Engine_Api::_()->getDbtable('posts', 'list');
          $postTableName = $postTable->info('name');

          $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'list');
          $pageTopicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitepage');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
          $reviewTable = Engine_Api::_()->getDbtable('reviews', 'list');
          $reviewTableName = $reviewTable->info('name');
          $pageReviewTable = Engine_Api::_()->getDbtable('reviews', 'sitepagereview');
          $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'sitepagereview');
        }

        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');

        $listLocationTable = Engine_Api::_()->getDbtable('locations', 'list');

        $pageLocationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');

        $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
        $listPhotoTable = Engine_Api::_()->getDbtable('photos', 'list');
        $storageTable = Engine_Api::_()->getDbtable('files', 'storage');

        $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
        if ($sitepageFormEnabled) {
          $sitepageformtable = Engine_Api::_()->getDbtable('sitepageforms', 'sitepageform');
          $optionid = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
          $table_option = Engine_Api::_()->fields()->getTable('sitepageform', 'options');
        }

        $writeTable = Engine_Api::_()->getDbtable('writes', 'list');
        $pageWriteTable = Engine_Api::_()->getDbtable('writes', 'sitepage');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {

          $pageVideoTable = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');
          $pageVideoTableName = $pageVideoTable->info('name');

          $listVideoRating = Engine_Api::_()->getDbTable('ratings', 'video');
          $listVideoRatingName = $listVideoRating->info('name');

          $pageVideoRatingTable = Engine_Api::_()->getDbTable('ratings', 'sitepagevideo');

          $listVideoTable = Engine_Api::_()->getDbtable('clasfvideos', 'list');
          $listVideoTableName = $listVideoTable->info('name');
        }

        $pageAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageAdminTableName = $pageAdminTable->info('name');
        $pageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
        //END COMMON DATA

        $selectListings = $listingTable->select()
                ->where('listing_id >= ?', $assigned_previous_id)
                ->from($listingTableName, 'listing_id')
                ->where('is_import != ?', 1)
                ->order('listing_id ASC');
        $listingDatas = $listingTable->fetchAll($selectListings);

        $next_import_count = 0;
        foreach ($listingDatas as $listingData) {

          $listing_id = $listingData->listing_id;

          if (!empty($listing_id)) {
            $listing = Engine_Api::_()->getItem('list_listing', $listing_id);

            $sitepage = $table->createRow();
            $sitepage->title = $listing->title;
            $sitepage->body = $listing->body;
            $sitepage->overview = $listing->overview;
            $sitepage->owner_id = $listing->owner_id;

            //START FETCH LIST CATEGORY AND SUB-CATEGORY
            if (!empty($listing->category_id)) {
              $listCategory = $listCategoryTable->fetchRow(array('category_id = ?' => $listing->category_id, 'cat_dependency = ?' => 0));
              if (!empty($listCategory)) {
                $listCategoryName = $listCategory->category_name;

                if (!empty($listCategoryName)) {
                  $pageCategory = $pageCategoryTable->fetchRow(array('category_name = ?' => $listCategoryName, 'cat_dependency = ?' => 0));
                  if (!empty($pageCategory)) {
                    $pageCategoryId = $sitepage->category_id = $pageCategory->category_id;
                  }

                  $listSubCategory = $listCategoryTable->fetchRow(array('category_id = ?' => $listing->subcategory_id, 'cat_dependency = ?' => $listing->category_id));
                  if (!empty($listSubCategory)) {
                    $listSubCategoryName = $listSubCategory->category_name;

                    $pageSubCategory = $pageCategoryTable->fetchRow(array('category_name = ?' => $listSubCategoryName, 'cat_dependency = ?' => $pageCategoryId));
                    if (!empty($pageSubCategory)) {
                      $sitepage->subcategory_id = $pageSubCategory->category_id;
                    }
                  }
                }
              }
            }
            //END FETCH LIST CATEGORY AND SUB-CATEGORY
            //START FETCH DEFAULT PACKAGE ID
            if (!empty($package))
              $sitepage->package_id = $package_id;
            //END FETCH DEFAULT PACKAGE ID

            $sitepage->profile_type = 0;

            $sitepage->photo_id = 0;

            //START FETCH PRICE
            if (!empty($metaData)) {
              $field_id = $metaData->field_id;

              $valueTable = Engine_Api::_()->fields()->getTable('list_listing', 'values');
              $selectValueData = $valueTable->select()->where('item_id = ?', $listing_id)->where('field_id = ?', $field_id);
              $valueData = $valueTable->fetchRow($selectValueData);
              if (!empty($valueData)) {
                $sitepage->price = $valueData->value;
              }
            }
            //END FETCH PRICE
            //START GET DATA FROM LISTING
            $sitepage->creation_date = $listing->creation_date;
            $sitepage->modified_date = $listing->modified_date;
            $sitepage->approved = $listing->approved;
            $sitepage->featured = $listing->featured;
            $sitepage->sponsored = $listing->sponsored;

            $sitepage->view_count = 1;
            if ($listing->view_count > 0) {
              $sitepage->view_count = $listing->view_count;
            }

            $sitepage->comment_count = $listing->comment_count;
            $sitepage->like_count = $listing->like_count;
            $sitepage->search = $listing->search;
            $sitepage->closed = $listing->closed;
            $sitepage->draft = $listing->draft;
            $sitepage->offer = 0;

            if (!empty($listing->aprrove_date)) {
              $sitepage->pending = 0;
              $sitepage->aprrove_date = $listing->aprrove_date;
              $sitepage->expiration_date = '2250-01-01 00:00:00';
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
              $sitepage->rating = round($listing->rating, 0);
            }

            $sitepage->save();
            $listing->is_import = 1;
            $listing->save();
            $next_import_count++;
            //END GET DATA FROM LISTING
            //START CREATE NEW PAGE URL
            $page_url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($listing->title))), '-');
            $sitepage_table = Engine_Api::_()->getItemTable('sitepage_page');
            $page = $sitepage_table->fetchRow(array('page_url = ?' => $page_url));
            if (!empty($page)) {
              $sitepage->page_url = $page_url . $sitepage->page_id;
            } else {
              $sitepage->page_url = $page_url;
            }
            //END CREATE NEW PAGE URL

            $sitepage->save();

            //START PROFILE MAPS WORK
            Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->profileMapping($sitepage);

//             //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW PAGE.
//             $selectPageAdmin = $pageAdminTable->select()
//                             ->setIntegrityCheck(false)
//                             ->from($pageAdminTableName)
//                             ->where('name = ?', 'sitepage_index_view');
//             $pageAdminresult = $pageAdminTable->fetchRow($selectPageAdmin);
//             //NOW INSERTING THE ROW IN PAGE TABLE
//             //MAKE NEW ENTRY FOR USER LAYOUT
//             $pageObject = $pageTable->createRow();
//             $pageObject->displayname = ( null !== ($name = $sitepage->title) ? $name : 'Untitled' );
//             $pageObject->title = ( null !== ($name = $sitepage->title) ? $name : 'Untitled' );
//             $pageObject->description = $sitepage->body;
//             $pageObject->name = "sitepage_index_view";
//             $pageObject->url = $pageAdminresult->url;
//             $pageObject->custom = $pageAdminresult->custom;
//             $pageObject->fragment = $pageAdminresult->fragment;
//             $pageObject->keywords = $pageAdminresult->keywords;
//             $pageObject->layout = $pageAdminresult->layout;
//             $pageObject->view_count = $pageAdminresult->view_count;
//             $pageObject->user_id = $sitepage->owner_id;
//             $pageObject->page_id = $sitepage->page_id;
//             $contentPageId = $pageObject->save();
// 
//             //NOW FETCHING PAGE CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS PAGE.
//             //NOW INSERTING DEFAULT PAGE CONTENT SETTINGS IN OUR CONTENT TABLE
// 						$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
// 						if (!$layout) {
// 							Engine_Api::_()->getDbtable('content', 'sitepage')->setContentDefault($contentPageId);
// 						} else {
// 							Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultLayout($contentPageId);
// 						}
            //START FETCH TAG
            $listTags = $listing->tags()->getTagMaps();
            $tagString = '';

            foreach ($listTags as $tagmap) {

              if ($tagString != '')
                $tagString .= ', ';
              $tagString .= $tagmap->getTag()->getTitle();

              $owner = Engine_Api::_()->getItem('user', $listing->owner_id);
              $tags = preg_split('/[,]+/', $tagString);
              $tags = array_filter(array_map("trim", $tags));
              $sitepage->tags()->setTagMaps($owner, $tags);
            }
            //END FETCH TAG
            //START FETCH LIKES
            $selectLike = $likeTable->select()
                    ->from($likeTableName, 'like_id')
                    ->where('resource_type = ?', 'list_listing')
                    ->where('resource_id = ?', $listing_id);
            $selectLikeDatas = $likeTable->fetchAll($selectLike);
            foreach ($selectLikeDatas as $selectLikeData) {
              $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

              $newLikeEntry = $likeTable->createRow();
              $newLikeEntry->resource_type = 'sitepage_page';
              $newLikeEntry->resource_id = $sitepage->page_id;
              $newLikeEntry->poster_type = 'user';
              $newLikeEntry->poster_id = $like->poster_id;
              $newLikeEntry->creation_date = $like->creation_date;
              $newLikeEntry->save();
            }
            //END FETCH LIKES
            //START FETCH COMMENTS
            $selectLike = $commentTable->select()
                    ->from($commentTableName, 'comment_id')
                    ->where('resource_type = ?', 'list_listing')
                    ->where('resource_id = ?', $listing_id);
            $selectLikeDatas = $commentTable->fetchAll($selectLike);
            foreach ($selectLikeDatas as $selectLikeData) {
              $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

              $newLikeEntry = $commentTable->createRow();
              $newLikeEntry->resource_type = 'sitepage_page';
              $newLikeEntry->resource_id = $sitepage->page_id;
              $newLikeEntry->poster_type = 'user';
              $newLikeEntry->poster_id = $comment->poster_id;
              $newLikeEntry->body = $comment->body;
              $newLikeEntry->creation_date = $comment->creation_date;
              $newLikeEntry->like_count = $comment->like_count;
              $newLikeEntry->save();
            }
            //END FETCH COMMENTS
            //START FETCH PRIVACY
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            foreach ($roles as $role) {
              if ($auth->isAllowed($listing, $role, 'view')) {
                $values['auth_view'] = $role;
              }
            }

            foreach ($roles as $role) {
              if ($auth->isAllowed($listing, $role, 'comment')) {
                $values['auth_comment'] = $role;
              }
            }

            foreach ($roles as $role) {
              if ($auth->isAllowed($listing, $role, 'photo')) {
                $values['auth_spcreate'] = $role;
              }
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $photoMax = array_search($values['auth_spcreate'], $roles);

            foreach ($roles as $i => $role) {
              $auth->setAllowed($sitepage, $role, 'view', ($i <= $viewMax));
              $auth->setAllowed($sitepage, $role, 'comment', ($i <= $commentMax));
              $auth->setAllowed($sitepage, $role, 'spcreate', ($i <= $photoMax));
            }
            //END FETCH PRIVACY
            //START FETCH DISCUSSION DATA
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {

              foreach ($roles as $i => $role) {
                $auth->setAllowed($sitepage, $role, 'sdicreate', ($i <= $photoMax));
              }

              $topicSelect = $topicTable->select()
                      ->from($topicTableName)
                      ->where('listing_id = ?', $listing_id);
              $topicSelectDatas = $topicTable->fetchAll($topicSelect);
              if (!empty($topicSelectDatas)) {
                $topicSelectDatas = $topicSelectDatas->toArray();

                foreach ($topicSelectDatas as $topicSelectData) {
                  $pageTopic = $pageTopicTable->createRow();
                  $pageTopic->page_id = $sitepage->page_id;
                  $pageTopic->user_id = $topicSelectData['user_id'];
                  $pageTopic->title = $topicSelectData['title'];
                  $pageTopic->creation_date = $topicSelectData['creation_date'];
                  $pageTopic->modified_date = $topicSelectData['modified_date'];
                  $pageTopic->sticky = $topicSelectData['sticky'];
                  $pageTopic->closed = $topicSelectData['closed'];
                  $pageTopic->post_count = $topicSelectData['post_count'];
                  $pageTopic->view_count = $topicSelectData['view_count'];
                  $pageTopic->lastpost_id = $topicSelectData['lastpost_id'];
                  $pageTopic->lastposter_id = $topicSelectData['lastposter_id'];
                  $pageTopic->save();

                  //START FETCH TOPIC POST'S
                  $postSelect = $postTable->select()
                          ->from($postTableName)
                          ->where('topic_id = ?', $topicSelectData['topic_id'])
                          ->where('listing_id = ?', $listing_id);
                  $postSelectDatas = $postTable->fetchAll($postSelect);
                  if (!empty($postSelectDatas)) {
                    $postSelectDatas = $postSelectDatas->toArray();

                    foreach ($postSelectDatas as $postSelectData) {
                      $pagePost = $pagePostTable->createRow();
                      $pagePost->topic_id = $pageTopic->topic_id;
                      $pagePost->page_id = $sitepage->page_id;
                      $pagePost->user_id = $postSelectData['user_id'];
                      $pagePost->body = $postSelectData['body'];
                      $pagePost->creation_date = $postSelectData['creation_date'];
                      $pagePost->modified_date = $postSelectData['modified_date'];
                      $pagePost->save();
                    }
                  }
                  //END FETCH TOPIC POST'S
                  //START FETCH TOPIC WATCH
                  $topicWatchData = $topicWatchesTable->fetchRow(array('resource_id = ?' => $listing_id, 'topic_id = ?' => $topicSelectData['topic_id'], 'user_id = ?' => $topicSelectData['user_id']));
                  if (!empty($topicWatchData))
                    $watch = $topicWatchData->watch;

                  $pageTopicWatchesTable->insert(array(
                      'resource_id' => $pageTopic->page_id,
                      'topic_id' => $pageTopic->topic_id,
                      'user_id' => $topicSelectData['user_id'],
                      'watch' => $watch,
                      'page_id' => $sitepage->page_id,
                  ));
                  //END FETCH TOPIC WATCH
                }
              }
            }
            //END FETCH DISCUSSION DATA
            //START FETCH REVIEW DATA
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
              $reviewTableSelect = $reviewTable->select()
                      ->from($reviewTableName, array('MAX(review_id) as review_id'))
                      ->where('listing_id = ?', $listing_id)
                      ->where('owner_id != ?', $listing->owner_id)
                      ->group('owner_id')
                      ->order('review_id ASC');
              $reviewSelectDatas = $reviewTable->fetchAll($reviewTableSelect);
              if (!empty($reviewSelectDatas)) {
                $reviewSelectDatas = $reviewSelectDatas->toArray();
                foreach ($reviewSelectDatas as $reviewSelectData) {
                  $review = Engine_Api::_()->getItem('list_review', $reviewSelectData['review_id']);

                  $pageReview = $pageReviewTable->createRow();
                  $pageReview->page_id = $sitepage->page_id;
                  $pageReview->owner_id = $review->owner_id;
                  $pageReview->title = $review->title;
                  $pageReview->body = $review->body;
                  $pageReview->view_count = 1;
                  $pageReview->recommend = 1;
                  $pageReview->creation_date = $review->creation_date;
                  $pageReview->modified_date = $review->modified_date;
                  $pageReview->save();

                  $reviewRating = $reviewRatingTable->createRow();
                  $reviewRating->review_id = $pageReview->review_id;
                  $reviewRating->category_id = $sitepage->category_id;
                  $reviewRating->page_id = $pageReview->page_id;
                  $reviewRating->reviewcat_id = 0;
                  $reviewRating->rating = round($listing->rating, 0);
                  $reviewRating->save();
                }
              }
            }
            //END FETCH REVIEW DATA
            //START INSERT SOME DEFAULT DATA
            $row = $manageadminsTable->createRow();
            $row->user_id = $sitepage->owner_id;
            $row->page_id = $sitepage->page_id;
            $row->save();

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $privacyMax = array_search('everyone', $roles);
            foreach ($roles as $i => $role) {
              $auth->setAllowed($sitepage, $role, 'print', ($i <= $privacyMax));
              $auth->setAllowed($sitepage, $role, 'tfriend', ($i <= $privacyMax));
              $auth->setAllowed($sitepage, $role, 'overview', ($i <= $privacyMax));
              $auth->setAllowed($sitepage, $role, 'map', ($i <= $privacyMax));
              $auth->setAllowed($sitepage, $role, 'insight', ($i <= $privacyMax));
              $auth->setAllowed($sitepage, $role, 'layout', ($i <= $privacyMax));
              $auth->setAllowed($sitepage, $role, 'contact', ($i <= $privacyMax));
              $auth->setAllowed($sitepage, $role, 'form', ($i <= $privacyMax));
              $auth->setAllowed($sitepage, $role, 'offer', ($i <= $privacyMax));
              $auth->setAllowed($sitepage, $role, 'invite', ($i <= $privacyMax));
            }

            $locationData = $listLocationTable->fetchRow(array('listing_id = ?' => $listing_id));
            if (!empty($locationData)) {
              $sitepage->location = $locationData->location;
              $sitepage->save();

              $pageLocation = $pageLocationTable->createRow();
              $pageLocation->page_id = $sitepage->page_id;
              $pageLocation->location = $sitepage->location;
              $pageLocation->latitude = $locationData->latitude;
              $pageLocation->longitude = $locationData->longitude;
              $pageLocation->formatted_address = $locationData->formatted_address;
              $pageLocation->country = $locationData->country;
              $pageLocation->state = $locationData->state;
              $pageLocation->zipcode = $locationData->zipcode;
              $pageLocation->city = $locationData->city;
              $pageLocation->address = $locationData->address;
              $pageLocation->zoom = $locationData->zoom;
              $pageLocation->save();
            }
            //END INSERT SOME DEFAULT DATA
            //START FETCH PHOTO DATA
            $selectListPhoto = $listPhotoTable->select()
                    ->from($listPhotoTable->info('name'))
                    ->where('listing_id = ?', $listing_id);
            $listPhotoDatas = $listPhotoTable->fetchAll($selectListPhoto);

            $sitpage = Engine_Api::_()->getItem('sitepage_page', $sitepage->page_id);

            if (!empty($listPhotoDatas)) {

              $listPhotoDatas = $listPhotoDatas->toArray();

              if (empty($listing->photo_id)) {
                foreach ($listPhotoDatas as $listPhotoData) {
                  $listing->photo_id = $listPhotoData['photo_id'];
                  break;
                }
              }

              if (!empty($listing->photo_id)) {
                $listPhotoData = $listPhotoTable->fetchRow(array('file_id = ?' => $listing->photo_id));
                if (!empty($listPhotoData)) {
                  $storageData = $storageTable->fetchRow(array('file_id = ?' => $listPhotoData->file_id));

                  if (!empty($storageData)) {

                    $sitpage->setPhoto($storageData->storage_path);

                    $album_id = $albumTable->update(array('photo_id' => $sitpage->photo_id, 'owner_id' => $sitpage->owner_id), array('page_id = ?' => $sitpage->page_id));

                    $pageProfilePhoto = Engine_Api::_()->getDbTable('photos', 'sitepage')->fetchRow(array('file_id = ?' => $sitpage->photo_id));
                    if (!empty($pageProfilePhoto)) {
                      $pageProfilePhotoId = $pageProfilePhoto->photo_id;
                    } else {
                      $pageProfilePhotoId = $sitpage->photo_id;
                    }

                    //START FETCH LIKES
                    $selectLike = $likeTable->select()
                            ->from($likeTableName, 'like_id')
                            ->where('resource_type = ?', 'list_photo')
                            ->where('resource_id = ?', $listing->photo_id);
                    $selectLikeDatas = $likeTable->fetchAll($selectLike);
                    foreach ($selectLikeDatas as $selectLikeData) {
                      $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                      $newLikeEntry = $likeTable->createRow();
                      $newLikeEntry->resource_type = 'sitepage_photo';
                      $newLikeEntry->resource_id = $pageProfilePhotoId;
                      $newLikeEntry->poster_type = 'user';
                      $newLikeEntry->poster_id = $like->poster_id;
                      $newLikeEntry->creation_date = $like->creation_date;
                      $newLikeEntry->save();
                    }
                    //END FETCH LIKES
                    //START FETCH COMMENTS
                    $selectLike = $commentTable->select()
                            ->from($commentTableName, 'comment_id')
                            ->where('resource_type = ?', 'list_photo')
                            ->where('resource_id = ?', $listing->photo_id);
                    $selectLikeDatas = $commentTable->fetchAll($selectLike);
                    foreach ($selectLikeDatas as $selectLikeData) {
                      $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                      $newLikeEntry = $commentTable->createRow();
                      $newLikeEntry->resource_type = 'sitepage_photo';
                      $newLikeEntry->resource_id = $pageProfilePhotoId;
                      $newLikeEntry->poster_type = 'user';
                      $newLikeEntry->poster_id = $comment->poster_id;
                      $newLikeEntry->body = $comment->body;
                      $newLikeEntry->creation_date = $comment->creation_date;
                      $newLikeEntry->like_count = $comment->like_count;
                      $newLikeEntry->save();
                    }
                    //END FETCH COMMENTS
                    //START FETCH TAGGER DETAIL
                    $tagmapsTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                    $tagmapsTableName = $tagmapsTable->info('name');
                    $selectTagmaps = $tagmapsTable->select()
                            ->from($tagmapsTableName, 'tagmap_id')
                            ->where('resource_type = ?', 'list_photo')
                            ->where('resource_id = ?', $listing->photo_id);
                    $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                    foreach ($selectTagmapsDatas as $selectTagmapsData) {
                      $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                      $newTagmapEntry = $tagmapsTable->createRow();
                      $newTagmapEntry->resource_type = 'sitepage_photo';
                      $newTagmapEntry->resource_id = $pageProfilePhotoId;
                      $newTagmapEntry->tagger_type = 'user';
                      $newTagmapEntry->tagger_id = $tagMap->tagger_id;
                      $newTagmapEntry->tag_type = 'user';
                      $newTagmapEntry->tag_id = $tagMap->tag_id;
                      $newTagmapEntry->creation_date = $tagMap->creation_date;
                      $newTagmapEntry->extra = $tagMap->extra;
                      $newTagmapEntry->save();
                    }
                    //END FETCH TAGGER DETAIL
                  }
                }

                $fetchDefaultAlbum = $albumTable->fetchRow(array('page_id = ?' => $sitepage->page_id, 'default_value = ?' => 1));
                if (!empty($fetchDefaultAlbum)) {

                  $order = 999;
                  foreach ($listPhotoDatas as $listPhotoData) {

                    if ($listPhotoData['photo_id'] != $listing->photo_id) {
                      $params = array(
                          'collection_id' => $fetchDefaultAlbum->album_id,
                          'album_id' => $fetchDefaultAlbum->album_id,
                          'page_id' => $sitpage->page_id,
                          'user_id' => $listPhotoData['user_id'],
                          'order' => $order,
                      );

                      $storageData = $storageTable->fetchRow(array('file_id = ?' => $listPhotoData['file_id']));
                      if (!empty($storageData)) {
                        $file = array();
                        $file['tmp_name'] = $storageData->storage_path;
                        $path_array = explode('/', $file['tmp_name']);
                        $file['name'] = end($path_array);

                        $pagePhoto = Engine_Api::_()->getDbtable('photos', 'sitepage')->createPhoto($params, $file);
                        if (!empty($pagePhoto)) {

                          $order++;

                          //START FETCH LIKES
                          $selectLike = $likeTable->select()
                                  ->from($likeTableName, 'like_id')
                                  ->where('resource_type = ?', 'list_photo')
                                  ->where('resource_id = ?', $listPhotoData['photo_id']);
                          $selectLikeDatas = $likeTable->fetchAll($selectLike);
                          foreach ($selectLikeDatas as $selectLikeData) {
                            $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                            $newLikeEntry = $likeTable->createRow();
                            $newLikeEntry->resource_type = 'sitepage_photo';
                            $newLikeEntry->resource_id = $pagePhoto->photo_id;
                            $newLikeEntry->poster_type = 'user';
                            $newLikeEntry->poster_id = $like->poster_id;
                            $newLikeEntry->creation_date = $like->creation_date;
                            $newLikeEntry->save();
                          }
                          //END FETCH LIKES
                          //START FETCH COMMENTS
                          $selectLike = $commentTable->select()
                                  ->from($commentTableName, 'comment_id')
                                  ->where('resource_type = ?', 'list_photo')
                                  ->where('resource_id = ?', $listPhotoData['photo_id']);
                          $selectLikeDatas = $commentTable->fetchAll($selectLike);
                          foreach ($selectLikeDatas as $selectLikeData) {
                            $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                            $newLikeEntry = $commentTable->createRow();
                            $newLikeEntry->resource_type = 'sitepage_photo';
                            $newLikeEntry->resource_id = $pagePhoto->photo_id;
                            $newLikeEntry->poster_type = 'user';
                            $newLikeEntry->poster_id = $comment->poster_id;
                            $newLikeEntry->body = $comment->body;
                            $newLikeEntry->creation_date = $comment->creation_date;
                            $newLikeEntry->like_count = $comment->like_count;
                            $newLikeEntry->save();
                          }
                          //END FETCH COMMENTS
                          //START FETCH TAGGER DETAIL
                          $selectTagmaps = $tagmapsTable->select()
                                  ->from($tagmapsTableName, 'tagmap_id')
                                  ->where('resource_type = ?', 'list_photo')
                                  ->where('resource_id = ?', $listPhotoData['photo_id']);
                          $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                          foreach ($selectTagmapsDatas as $selectTagmapsData) {
                            $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                            $newTagmapEntry = $tagmapsTable->createRow();
                            $newTagmapEntry->resource_type = 'sitepage_photo';
                            $newTagmapEntry->resource_id = $pagePhoto->photo_id;
                            $newTagmapEntry->tagger_type = 'user';
                            $newTagmapEntry->tagger_id = $tagMap->tagger_id;
                            $newTagmapEntry->tag_type = 'user';
                            $newTagmapEntry->tag_id = $tagMap->tag_id;
                            $newTagmapEntry->creation_date = $tagMap->creation_date;
                            $newTagmapEntry->extra = $tagMap->extra;
                            $newTagmapEntry->save();
                          }
                          //END FETCH TAGGER DETAIL
                        }
                      }
                    }
                  }
                }
              }
            }
            //END FETCH PHOTO DATA
            //START FETCH SITEPAGE-FORM DATA
            $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
            if ($sitepageFormEnabled) {
              $sitepageform = $table_option->createRow();
              $sitepageform->label = $sitpage->title;
              $sitepageform->field_id = 1;
              $option_id = $sitepageform->save();
              $optionids = $optionid->createRow();
              $optionids->option_id = $option_id;
              $optionids->page_id = $sitpage->page_id;
              $optionids->save();
              $sitepageforms = $sitepageformtable->createRow();
              $sitepageforms->page_id = $sitpage->page_id;
              $sitepageforms->save();
            }
            //END FETCH SITEPAGE-FORM DATA
            //START FETCH engine4_list_writes DATA
            $writeData = $writeTable->fetchRow(array('listing_id = ?' => $listing_id));
            if (!empty($writeData)) {
              $write = $pageWriteTable->createRow();
              $write->page_id = $sitepage->page_id;
              $write->text = $writeData->text;
              $write->save();
            }
            //END FETCH engine4_list_writes DATA
            //START FETCH VIDEO DATA
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {

              $selectListVideos = $listVideoTable->select()
                      ->from($listVideoTableName, 'video_id')
                      ->where('listing_id = ?', $listing_id)
                      ->group('video_id');
              $listVideoDatas = $listVideoTable->fetchAll($selectListVideos);
              foreach ($listVideoDatas as $listVideoData) {
                $listVideo = Engine_Api::_()->getItem('video', $listVideoData->video_id);
                if (!empty($listVideo)) {
                  $db = $pageVideoTable->getAdapter();
                  $db->beginTransaction();

                  try {
                    $pageVideo = $pageVideoTable->createRow();
                    $pageVideo->page_id = $sitepage->page_id;
                    $pageVideo->title = $listVideo->title;
                    $pageVideo->description = $listVideo->description;
                    $pageVideo->search = $listVideo->search;
                    $pageVideo->owner_id = $listVideo->owner_id;
                    $pageVideo->creation_date = $listVideo->creation_date;
                    $pageVideo->modified_date = $listVideo->modified_date;

                    $pageVideo->view_count = 1;
                    if ($listVideo->view_count > 0) {
                      $pageVideo->view_count = $listVideo->view_count;
                    }

                    $pageVideo->comment_count = $listVideo->comment_count;
                    $pageVideo->type = $listVideo->type;
                    $pageVideo->code = $listVideo->code;
                    $pageVideo->rating = $listVideo->rating;
                    $pageVideo->status = $listVideo->status;
                    $pageVideo->featured = 0;
                    $pageVideo->file_id = 0;
                    $pageVideo->duration = $listVideo->duration;
                    $pageVideo->save();
                    $db->commit();
                  } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                  }

                  //START VIDEO THUMB WORK
                  if (!empty($pageVideo->code) && !empty($pageVideo->type) && !empty($listVideo->photo_id)) {
                    $storageData = $storageTable->fetchRow(array('file_id = ?' => $listVideo->photo_id));
                    if (!empty($storageData)) {
                      $thumbnail = $storageData->storage_path;

                      $ext = ltrim(strrchr($thumbnail, '.'), '.');
                      $thumbnail_parsed = @parse_url($thumbnail);

                      if (@GetImageSize($thumbnail)) {
                        $valid_thumb = true;
                      } else {
                        $valid_thumb = false;
                      }

                      if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
                        $src_fh = fopen($thumbnail, 'r');
                        $tmp_fh = fopen($tmp_file, 'w');
                        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
                        $image = Engine_Image::factory();
                        $image->open($tmp_file)
                                ->resize(120, 240)
                                ->write($thumb_file)
                                ->destroy();

                        try {
                          $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                              'parent_type' => 'sitepagevideo_video',
                              'parent_id' => $pageVideo->video_id
                                  ));

                          //REMOVE TEMP FILE
                          @unlink($thumb_file);
                          @unlink($tmp_file);
                        } catch (Exception $e) {
                          
                        }

                        $pageVideo->photo_id = $thumbFileRow->file_id;
                        $pageVideo->save();
                      }
                    }
                  }
                  //END VIDEO THUMB WORK
                  //START FETCH TAG
                  $videoTags = $listVideo->tags()->getTagMaps();
                  $tagString = '';

                  foreach ($videoTags as $tagmap) {

                    if ($tagString != '')
                      $tagString .= ', ';
                    $tagString .= $tagmap->getTag()->getTitle();

                    $owner = Engine_Api::_()->getItem('user', $listVideo->owner_id);
                    $tags = preg_split('/[,]+/', $tagString);
                    $tags = array_filter(array_map("trim", $tags));
                    $pageVideo->tags()->setTagMaps($owner, $tags);
                  }
                  //END FETCH TAG
                  //START FETCH LIKES
                  $selectLike = $likeTable->select()
                          ->from($likeTableName, 'like_id')
                          ->where('resource_type = ?', 'video')
                          ->where('resource_id = ?', $listVideoData->video_id);
                  $selectLikeDatas = $likeTable->fetchAll($selectLike);
                  foreach ($selectLikeDatas as $selectLikeData) {
                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                    $newLikeEntry = $likeTable->createRow();
                    $newLikeEntry->resource_type = 'sitepagevideo_video';
                    $newLikeEntry->resource_id = $pageVideo->video_id;
                    $newLikeEntry->poster_type = 'user';
                    $newLikeEntry->poster_id = $like->poster_id;
                    $newLikeEntry->creation_date = $like->creation_date;
                    $newLikeEntry->save();
                  }
                  //END FETCH LIKES
                  //START FETCH COMMENTS
                  $selectLike = $commentTable->select()
                          ->from($commentTableName, 'comment_id')
                          ->where('resource_type = ?', 'video')
                          ->where('resource_id = ?', $listVideoData->video_id);
                  $selectLikeDatas = $commentTable->fetchAll($selectLike);
                  foreach ($selectLikeDatas as $selectLikeData) {
                    $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                    $newLikeEntry = $commentTable->createRow();
                    $newLikeEntry->resource_type = 'sitepagevideo_video';
                    $newLikeEntry->resource_id = $pageVideo->video_id;
                    $newLikeEntry->poster_type = 'user';
                    $newLikeEntry->poster_id = $comment->poster_id;
                    $newLikeEntry->body = $comment->body;
                    $newLikeEntry->creation_date = $comment->creation_date;
                    $newLikeEntry->like_count = $comment->like_count;
                    $newLikeEntry->save();
                  }
                  //END FETCH COMMENTS
                  //START UPDATE TOTAL LIKES IN PAGE-VIDEO TABLE
                  $selectLikeCount = $likeTable->select()
                          ->from($likeTableName, array('COUNT(*) AS like_count'))
                          ->where('resource_type = ?', 'sitepagevideo_video')
                          ->where('resource_id = ?', $pageVideo->video_id);
                  $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                  if (!empty($selectLikeCounts)) {
                    $selectLikeCounts = $selectLikeCounts->toArray();
                    $pageVideo->like_count = $selectLikeCounts[0]['like_count'];
                    $pageVideo->save();
                  }
                  //END UPDATE TOTAL LIKES IN PAGE-VIDEO TABLE
                  //START FETCH RATTING DATA
                  $selectVideoRating = $listVideoRating->select()
                          ->from($listVideoRatingName)
                          ->where('video_id = ?', $listVideoData->video_id);

                  $listVideoRatingDatas = $listVideoRating->fetchAll($selectVideoRating);
                  if (!empty($listVideoRatingDatas)) {
                    $listVideoRatingDatas = $listVideoRatingDatas->toArray();
                  }

                  foreach ($listVideoRatingDatas as $listVideoRatingData) {

                    $pageVideoRatingTable->insert(array(
                        'video_id' => $pageVideo->video_id,
                        'user_id' => $listVideoRatingData['user_id'],
                        'page_id' => $pageVideo->page_id,
                        'rating' => $listVideoRatingData['rating']
                    ));
                  }
                  //END FETCH RATTING DATA
                }
              }
              //END FETCH VIDEO DATA
            }
          }

          $this->view->assigned_previous_id = $listing_id;

          //CREATE LOG ENTRY IN LOG FILE
          if (file_exists(APPLICATION_PATH . '/temporary/log/ListingToPageImport.log')) {
            $myFile = APPLICATION_PATH . '/temporary/log/ListingToPageImport.log';
            $error = Zend_Registry::get('Zend_Translate')->_("can't open file");
            $fh = fopen($myFile, 'a') or die($error);
            $current_time = date('D, d M Y H:i:s T');
            $page_id = $sitepage->page_id;
            $page_title = $sitepage->title;
            $stringData = $this->view->translate('Listing with ID ') . $listing_id . $this->view->translate(' is successfully imported into a Page with ID ') . $page_id . $this->view->translate(' at ') . $current_time . $this->view->translate(". Title of that Page is '") . $page_title . "'.\n\n";
            fwrite($fh, $stringData);
            fclose($fh);
          }

          if ($next_import_count >= 100) {
            $this->_redirect("admin/sitepage/importlisting/index?start_import=1");
          }
        }
      }
    }
  }

  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function importAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');

    //MAKE FORM
    $this->view->form = $form = new Sitepage_Form_Admin_Import_Import();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //MAKE SURE THAT FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
      $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
      if (!in_array($ext, array('csv', 'CSV'))) {
        $error = $this->view->translate("Invalid file extension. Only 'csv' extension is allowed.");
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //START READING DATA FROM CSV FILE
      $fname = $_FILES['filename']['tmp_name'];
      $fp = fopen($fname, "r");

      if (!$fp) {
        echo "$fname File opening error";
        exit;
      }

      $formData = array();
      $formData = $form->getValues();

      if ($formData['import_seperate'] == 1) {
        while ($buffer = fgets($fp, 9999)) {
          $explode_array[] = explode('|', $buffer);
        }
      } else {
        while ($buffer = fgets($fp, 9999)) {
          $explode_array[] = explode(',', $buffer);
        }
      }
      //END READING DATA FROM CSV FILE

      $import_count = 0;
      
      foreach ($explode_array as $explode_data) {

        //GET PAGE DETAILS FROM DATA ARRAY
        $db = Engine_Db_Table::getDefaultAdapter();
        $columns = $db->query("SHOW COLUMNS FROM engine4_sitepage_imports")->fetchAll();
        $values = array();
        $values['title'] = trim($explode_data[0]);
        $values['page_url'] = trim($explode_data[1]);
        $values['category'] = trim($explode_data[2]);
        $values['sub_category'] = trim($explode_data[3]);
        $values['subsub_category'] = trim($explode_data[4]);
        $values['body'] = trim($explode_data[5]);
        $values['overview'] = trim($explode_data[6]);
        $values['tags'] = trim($explode_data[7]);
        $values['location'] = trim($explode_data[8]);
        $values['price'] = trim($explode_data[9]);
        $values['email'] = trim($explode_data[10]);
        $values['website'] = trim($explode_data[11]);
        $values['phone'] = trim($explode_data[12]);
        $values['userclaim'] = trim($explode_data[13]);
        $values['img_name'] = trim($explode_data[14]);

        $i = 1;
        $custom_field = '';
        foreach ($columns as $column) {
          $custom_field .= $column['Field'] . ',';
        }
        $custom_field = explode('userclaim', $custom_field);
        $custom_field = ltrim($custom_field[1], ',');
        $custom_field = rtrim($custom_field, ',');
        $custom_field = explode(',', $custom_field);
        $required_field = 1;

        $count_value_checkbox = 0;
        $count_value_selectbox = 0;
        $count_value_etnicity = 0;
        $count_value_lookingfor = 0;
        $count_value_interestedIn = 0;
        if (isset($explode_data[15])) {
          foreach ($custom_field as $field) {
            if (empty($field))
              continue;

            $column_name = $field;
            $explode_column = explode('_', $column_name);
            $columnIsRequired='';
            $fieldType ='';
            if(isset($explode_column[2])) {
            $columnIsRequired = $db->select()->from('engine4_sitepage_page_fields_meta', 'required')->where('field_id = ?', $explode_column[2])->query()->fetchColumn();
            }
            if(isset($explode_column[1])) {
            $fieldType = $db->select()->from('engine4_sitepage_page_fields_meta', 'type')->where('field_id = ?', $explode_column[1])->query()->fetchColumn();
            }
            if ($fieldType == 'multi_checkbox' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_checkbox++;
              }
              if ($count_value_checkbox == 0) {
                $required_field = 0;
                break;
              }
            } elseif ($fieldType == 'multiselect' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_selectbox++;
              }
              if ($count_value_selectbox == 0) {
                $required_field = 0;
                break;
              }
            } elseif ($fieldType == 'looking_for' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_lookingfor++;
              }
              if ($count_value_lookingfor == 0) {
                $required_field = 0;
                break;
              }
            } elseif ($fieldType == 'ethnicity' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_etnicity++;
              }
              if ($count_value_etnicity == 0) {
                $required_field = 0;
                break;
              }
            } elseif ($fieldType == 'partner_gender' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_interestedIn++;
              }
              if ($count_value_interestedIn == 0) {
                $required_field = 0;
                break;
              }
            } elseif (!empty($columnIsRequired) && ($explode_data[14 + $i] == ' ' || empty($explode_data[14 + $i]))) {
              $required_field = 0;
              break;
            }
            $values[$column_name] = trim($explode_data[14 + $i]);
            $i++;
          }
        }

        //IF PAGE TITLE AND CATEGORY IS EMPTY THEN CONTINUE;
        if (empty($values['title'])|| empty($values['category'])) {
          continue;
        }

        $db = Engine_Api::_()->getDbtable('imports', 'sitepage')->getAdapter();
        $db->beginTransaction();

        try {
          $import = Engine_Api::_()->getDbtable('imports', 'sitepage')->createRow();
          $import->setFromArray($values);
          $import->save();

          //COMMIT
          $db->commit();

          if (empty($import_count)) {
            $first_import_id = $last_import_id = $import->import_id;

            //SAVE DATA IN `engine4_sitepage_importfiles` TABLE
            $db = Engine_Api::_()->getDbtable('importfiles', 'sitepage')->getAdapter();
            $db->beginTransaction();

            try {

              //FETCH PRIVACY
              if (empty($formData['auth_view'])) {
                $formData['auth_view'] = "everyone";
              }

              if (empty($formData['auth_comment'])) {
                $formData['auth_comment'] = "everyone";
              }

              //SAVE OTHER DATA IN engine4_sitepage_importfiles TABLE
              $importFile = Engine_Api::_()->getDbtable('importfiles', 'sitepage')->createRow();
              $importFile->filename = $_FILES['filename']['name'];
              $importFile->status = 'Pending';
              $importFile->first_import_id = $first_import_id;
              $importFile->last_import_id = $last_import_id;
              $importFile->current_import_id = $first_import_id;
              $importFile->first_page_id = 0;
              $importFile->last_page_id = 0;
              $importFile->view_privacy = $formData['auth_view'];
              $importFile->comment_privacy = $formData['auth_comment'];
              $importFile->save();

              //COMMIT
              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          } else {

            //UPDATE LAST IMPORT ID
            $last_import_id = $import->import_id;
            $importFile->last_import_id = $last_import_id;
            $importFile->save();
          }

          $import_count++;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }
      //CLOSE THE SMOOTHBOX
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRedirect' => $this->_helper->url->url(array('module' => 'sitepage', 'controller' => 'admin-importlisting', 'action' => 'manage')),
          'parentRedirectTime' => '15',
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('CSV file has been imported succesfully !'))
      ));
    }
  }

  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function dataImportAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');
    $current_import = $this->_getParam('current_import');

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //RETURN IF importfile_id IS EMPTY
    if (empty($importfile_id)) {
      return;
    }

    //GET IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('sitepage_importfile', $importfile_id);
    if (empty($importFile)) {
      return;
    }

    //CHECK IF IMPORT WORK IS ALREADY IN RUNNING STATUS FOR SOME FILE
    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitepage');
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
    if (!empty($importFileStatusData) && empty($current_import)) {
      return;
    }

    //UPDATE THE STATUS
    $importFile->status = 'Running';
    $importFile->save();

    $first_import_id = $importFile->first_import_id;
    $last_import_id = $importFile->last_import_id;

    $current_import_id = $importFile->current_import_id;
    $return_current_import_id = $this->_getParam('current_import_id');
    if (!empty($return_current_import_id)) {
      $current_import_id = $this->_getParam('current_import_id');
    }

    //MAKE QUERY
    $tableImport = Engine_Api::_()->getDbtable('imports', 'sitepage');

    $sqlStr = "import_id BETWEEN " . "'" . $current_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

    $select = $tableImport->select()
            ->from($tableImport->info('name'), array('import_id'))
            ->where($sqlStr);
    $importDatas = $select->query()->fetchAll();

    if (empty($importDatas)) {
      return;
    }

    //START CODE FOR CREATING THE ListingToPageImport.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/CSVToPageImport.log')) {
      $log = new Zend_Log();
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToPageImport.log'));
      } catch (Exception $e) {
        //CHECK DIRECTORY
        if (!@is_dir(APPLICATION_PATH . '/temporary/log') &&
                @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
          $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToPageImport.log'));
        } else {
          //Silence ...
          if (APPLICATION_ENV !== 'production') {
            $log->log($e->__toString(), Zend_Log::CRIT);
          } else {
            //MAKE SURE LOGGING DOESN'T CAUSE EXCEPTIONS
            $log->addWriter(new Zend_Log_Writer_Null());
          }
        }
      }
    }

    //GIVE WRITE PERMISSION TO LOG FILE IF EXIST
    if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToPageImport.log')) {
      @chmod(APPLICATION_PATH . '/temporary/log/CSVToPageImport.log', 0777);
    }
    //END CODE FOR CREATING THE CSVToPageImport.log FILE
    //START COLLECTING COMMON DATAS
    $package_id = Engine_Api::_()->getItemtable('sitepage_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
    $package = Engine_Api::_()->getItemTable('sitepage_package')->fetchRow(array('package_id = ?' => $package_id));
    $table = Engine_Api::_()->getItemTable('sitepage_page');
    $pageCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitepage');
    $pageAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageAdminTableName = $pageAdminTable->info('name');
    $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
    $pageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
    //END COLLECTING COMMON DATAS

    $import_count = 0;

    //START THE IMPORT WORK
    foreach ($importDatas as $importData) {

      //GET IMPORT FILE OBJECT
      $importFile = Engine_Api::_()->getItem('sitepage_importfile', $importfile_id);

      //BREAK IF STATUS IS STOP
      if ($importFile->status == 'Stopped') {
        break;
      }

      $import_id = $importData['import_id'];
      if (empty($import_id)) {
        continue;
      }

      $import = Engine_Api::_()->getItem('sitepage_import', $import_id);
      if (empty($import)) {
        continue;
      }

      //GET PAGE DETAILS FROM DATA ARRAY
      $values = array();
      $values['title'] = $import->title;
      $page_url = $import->page_url;
      $page_category = $import->category;
      $page_subcategory = $import->sub_category;
      $sitepage_subsubcategory = $import->subsub_category;
      $values['body'] = $import->body;
      $values['price'] = $import->price;
      $values['location'] = $import->location;
      $values['overview'] = $import->overview;
      $page_tags = $import->tags;
      $values['email'] = $import->email;
      $values['website'] = $import->website;
      $values['phone'] = $import->phone;
      $values['userclaim'] = $import->userclaim;
      $values['owner_type'] = $viewer->getType();
      $values['owner_id'] = $viewer->getIdentity();
      $values['package_id'] = $package_id;

      //IF PAGE TITLE AND CATEGORY IS EMPTY THEN CONTINUE;
      if (empty($values['title']) || empty($page_category)) {
        continue;
      }

      $db = $table->getAdapter();
      $db->beginTransaction();

      try {

        $sitepage = $table->createRow();
        $sitepage->setFromArray($values);

        $sitepage->pending = 0;
        $sitepage->approved = 1;
        $sitepage->aprrove_date = date('Y-m-d H:i:s');

        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
          $expirationDate = $package->getExpirationDate();
          if (!empty($expirationDate))
            $sitepage->expiration_date = date('Y-m-d H:i:s', $expirationDate);
          else
            $sitepage->expiration_date = '2250-01-01 00:00:00';
        }
        else {
          $sitepage->expiration_date = '2250-01-01 00:00:00';
        }

        $sitepage->view_count = 1;
        $sitepage->save();
        $page_id = $sitepage->page_id;

        $importFile->current_import_id = $import->import_id;
        $importFile->last_page_id = $page_id;
        $importFile->save();

        if (empty($importFile->first_page_id)) {
          $importFile->first_page_id = $page_id;
          $importFile->save();
        }
        $import_count++;

        //START CREATE NEW PAGE URL
        if (empty($page_url)) {
          $page_url = $values['title'];
        }

        $page_url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($page_url))), '-');

        $page = $table->fetchRow(array('page_url = ?' => $page_url));
        if (!empty($page)) {
          $sitepage->page_url = $page_url . $sitepage->page_id;
        } else {
          $sitepage->page_url = $page_url;
        }

        $sitepage->page_url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($sitepage->page_url))), '-');
        //END CREATE NEW PAGE URL
        //START CATEGORY WORK
        $pageCategory = $pageCategoryTable->fetchRow(array('category_name = ?' => $page_category, 'cat_dependency = ?' => 0));
        if (!empty($pageCategory)) {
          $sitepage->category_id = $pageCategory->category_id;

          $pageSubcategory = $pageCategoryTable->fetchRow(array('category_name = ?' => $page_subcategory, 'cat_dependency = ?' => $sitepage->category_id));

          if (!empty($pageSubcategory)) {
            $sitepage->subcategory_id = $pageSubcategory->category_id;
            $sitepageSubsubcategory = $pageCategoryTable->fetchRow(array('category_name = ?' => $sitepage_subsubcategory, 'cat_dependency = ?' => $pageSubcategory->category_id));
            if (!empty($sitepageSubsubcategory)) {
              $sitepage->subsubcategory_id = $sitepageSubsubcategory->category_id;
            }
          }

          //START PROFILE MAPS WORK
          Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->profileMapping($sitepage);
        }
        //END CATEGORY WORK

        $sitepage->save();

        //SAVE TAGS
        $tags = preg_split('/[#]+/', $page_tags);
        $tags = array_filter(array_map("trim", $tags));
        $sitepage->tags()->addTagMaps($viewer, $tags);

        //START PROFILE IMAGE IMPORTING WORK
        $import_image = 'sitepage_importfiles_' . $importfile_id;
        $archiveFilename = APPLICATION_PATH . "/public/$import_image" . DIRECTORY_SEPARATOR . $importFile->photo_filename;

        if (!empty($import->img_name) && (strstr($import->img_name, 'http') || strstr($import->img_name, 'https'))) {
          $sitepage->setImportPhoto($import->img_name, 1);
          $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
          $album_id = $albumTable->update(array('photo_id' => $sitepage->photo_id), array('page_id = ?' => $sitepage->page_id));
        }

        if (file_exists(APPLICATION_PATH . "/public/$import_image") && !empty($import->img_name)) {
          // Make temporary folder
          $archiveOutputPath = substr($archiveFilename, 0, strrpos($archiveFilename, '.'));

          if (file_exists(APPLICATION_PATH . "/public/$import_image" . DIRECTORY_SEPARATOR . $importFile->photo_filename)) {
            // Extract
            $zip = new ZipArchive;
            $res = $zip->open($archiveFilename);
            $zip->extractTo(APPLICATION_PATH . "/public/$import_image");
            $zip->close();
            @chmod($archiveOutputPath, 0777);
            @unlink($archiveFilename);
          }

          $archiveFilename1 = APPLICATION_PATH . "/public/$import_image";
          if (file_exists($archiveFilename1 . '/' . $import->img_name)) {
            $sitepage->setPhoto($archiveFilename1 . '/' . $import->img_name);
          }
        }
        //END PROFILE IMAGE IMPORTING WORK
        //START CUSTOM FIELDS IMPORT WORK 
        //GET PROFILE MAPPING ID
        $categoryIds = array();
        $categoryIds[] = $sitepage->category_id;
        $categoryIds[] = $sitepage->subcategory_id;
        $categoryIds[] = $sitepage->subsubcategory_id;
        $profile_type = Engine_Api::_()->getDbtable('categories', 'sitepage')->getProfileType($categoryIds, 0, 'profile_type');

        $pageFieldValueTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'values');
        $pageFieldOptionTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'options');
        $pageFieldMapsTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'maps');
        $pageFieldSearchTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'search');

        $db = Engine_Db_Table::getDefaultAdapter();
        $columns = $db->query("SHOW COLUMNS FROM engine4_sitepage_imports")->fetchAll();
        $custom_field = '';
        $countCustomFields = 0;
        foreach ($columns as $column) {
          $countCustomFields++;
          $custom_field .= $column['Field'] . ',';
        }
        $custom_field = explode('userclaim', $custom_field);
        $custom_field = ltrim($custom_field[1], ',');
        $custom_field = rtrim($custom_field, ',');
        $custom_fields = explode(',', $custom_field);
        if ($countCustomFields > 16) {
          foreach ($custom_fields as $cloumn_name) {
            $field = explode('_', $cloumn_name);
            $field_id = $field[2];
            $selectFieldsMapTable = $pageFieldMapsTable->select()
                    ->from($pageFieldMapsTable->info('name'), array('option_id'))
                    ->where('child_id = ?', $field_id);
            $fieldsMappingResult = $selectFieldsMapTable->query()->fetchAll();
            $option_ids = array();
            foreach ($fieldsMappingResult as $map) {
              $option_ids[] = $map['option_id'];
            }
            if (!in_array($profile_type, $option_ids)) {
              continue;
            }

            $profileTypeExist = $db->select()->from('engine4_sitepage_page_fields_values', 'value')->where('item_id = ?', $page_id)->where('value = ?', $profile_type)->where('field_id = ?', 1)->query()->fetchColumn();
            if (empty($profileTypeExist)) {
              $pageFieldValueTable->insert(array('item_id' => $page_id, 'field_id' => 1, 'index' => 0, 'value' => $profile_type));
            }

            $pageFieldMetaTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'meta');
            $selectMetaData = $pageFieldMetaTable->select()->from($pageFieldMetaTable->info('name'), array('type', 'alias', 'search', 'required'))->where('field_id = ?', $field_id);
            $metaData = $pageFieldMetaTable->fetchRow($selectMetaData);
            $fieldType = $metaData->type;
            $fieldId = $field_id;
            $fieldAlias = $metaData->alias;
            $fieldSearch = $metaData->search;
            $fieldRequired = $metaData->required;

            if ($fieldType == 'multi_checkbox' || $fieldType == 'multiselect') {
              if ($import->$cloumn_name == 'Yes' || $import->$cloumn_name == 'yes') {
                $option_value = $field[4];
                $option_exist = 1;
              } else {
                $option_exist = 0;
              }
            } elseif (($fieldType == 'gender' || $fieldType == 'radio' || $fieldType == 'select') && !empty($import->$cloumn_name)) {
              $option_exist = 1;
              $selectOptionTable = $pageFieldOptionTable->select()
                      ->from($pageFieldOptionTable->info('name'), 'option_id')
                      ->where('label = ?', $import->$cloumn_name)
                      ->where('field_id = ?', $field_id);
              $optionTableResult = $pageFieldOptionTable->fetchRow($selectOptionTable);
              $option_value = $optionTableResult->option_id;
            } else {
              if (!empty($import->$cloumn_name)) {
                $option_value = $import->$cloumn_name;
                $option_exist = 1;
              } else {
                $option_exist = 0;
              }
            }

            $selectValueTable = $pageFieldValueTable->select()
                    ->from($pageFieldValueTable->info('name'), 'index')
                    ->where('field_id = ?', $fieldId)
                    ->where('item_id = ?', $page_id)
                    ->order('index DESC');
            $index_value = $pageFieldValueTable->fetchRow($selectValueTable);

            if (!empty($option_exist) && !empty($option_value)) {
              if (count($index_value)) {
                $index_value = $index_value->index;
                $pageFieldValueTable->insert(array('item_id' => $page_id, 'field_id' => $fieldId, 'index' => $index_value + 1, 'value' => $option_value));
              } else {
                $pageFieldValueTable->insert(array('item_id' => $page_id, 'field_id' => $fieldId, 'index' => 0, 'value' => $option_value));
              }
            }
            if ($fieldSearch == 1 && !empty($fieldAlias)) {
              $field_label = $fieldAlias;
            } elseif ($fieldSearch == 1 && empty($fieldAlias)) {
              $field_label = 'field_' . $fieldId;
            }

            if (!empty($fieldSearch)) {
              $selectSearchTable = $pageFieldSearchTable->select()
                      ->from($pageFieldSearchTable->info('name'), $field_label)
                      ->where('item_id = ?', $page_id)
                      ->where('profile_type = ?', (string) ($profile_type));
              $fieldSearchValue = $pageFieldSearchTable->fetchRow($selectSearchTable);

              if (!empty($option_exist)) {
                if (count($fieldSearchValue)) {
                  if (!empty($fieldSearchValue->$field_label)) {
                    $field_value = $fieldSearchValue->$field_label . ',' . $option_value;
                  } else {
                    $field_value = $option_value;
                  }
                } else {
                  $field_value = $option_value;
                }

                if (empty($fieldSearchValue)) {
                  $db->insert('engine4_sitepage_page_fields_search', array('item_id' => $page_id, 'profile_type' => $profile_type, $field_label => $field_value));
                } else {
                  $db->update('engine4_sitepage_page_fields_search', array($field_label => $field_value), array('profile_type = ?' => $profile_type, 'item_id = ?' => $page_id));
                }
              }
            }
          }
        }
        $sitepage->profile_type = $profile_type;


        //PUT PAGE OWNER IN MANAGE ADMIN TABLE
        $row = $manageadminsTable->createRow();
        $row->user_id = $sitepage->owner_id;
        $row->page_id = $sitepage->page_id;
        $row->save();

        //DEFAULT ENTRIES FOR SITEAPGE-FORM
        $page_id = $sitepage->page_id;
        $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
        if ($sitepageFormEnabled) {

          $sitepageformtable = Engine_Api::_()->getDbtable('sitepageforms', 'sitepageform');
          $optionid = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
          $table_option = Engine_Api::_()->fields()->getTable('sitepageform', 'options');

          $sitepageform = $table_option->createRow();
          $sitepageform->label = $values['title'];
          $sitepageform->field_id = 1;
          $option_id = $sitepageform->save();
          $optionids = $optionid->createRow();
          $optionids->option_id = $option_id;
          $optionids->page_id = $page_id;
          $optionids->save();
          $sitepageforms = $sitepageformtable->createRow();
          $sitepageforms->page_id = $page_id;
          $sitepageforms->save();
        }

        //SET PHOTO
        $album_id = $albumTable->insert(array(
            'photo_id' => 0,
            'owner_id' => $sitepage->owner_id,
            'page_id' => $sitepage->page_id,
            'title' => $sitepage->title,
            'creation_date' => $sitepage->creation_date,
            'modified_date' => $sitepage->modified_date));

        $sitepage->setLocation();

        //SET PRIVACY
        $auth = Engine_Api::_()->authorization()->context;
        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if (!empty($sitepagememberEnabled)) {
          $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        $privacyMax = array_search('everyone', $roles);

        if (empty($importFile->view_privacy)) {
          $importFile->view_privacy = "everyone";
        }

        if (empty($importFile->comment_privacy)) {
          $importFile->comment_privacy = "everyone";
        }

        $viewMax = array_search($importFile->view_privacy, $roles);
        $commentMax = array_search($importFile->comment_privacy, $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitepage, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($sitepage, $role, 'comment', ($i <= $commentMax));
          $auth->setAllowed($sitepage, $role, 'print', ($i <= $privacyMax));
          $auth->setAllowed($sitepage, $role, 'tfriend', ($i <= $privacyMax));
          $auth->setAllowed($sitepage, $role, 'overview', ($i <= $privacyMax));
          $auth->setAllowed($sitepage, $role, 'map', ($i <= $privacyMax));
          $auth->setAllowed($sitepage, $role, 'insight', ($i <= $privacyMax));
          $auth->setAllowed($sitepage, $role, 'layout', ($i <= $privacyMax));
          $auth->setAllowed($sitepage, $role, 'contact', ($i <= $privacyMax));
          $auth->setAllowed($sitepage, $role, 'form', ($i <= $privacyMax));
          $auth->setAllowed($sitepage, $role, 'offer', ($i <= $privacyMax));
          $auth->setAllowed($sitepage, $role, 'invite', ($i <= $privacyMax));
        }

        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if (!empty($sitepagememberEnabled)) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        }

        $createMax = array_search("owner", $roles);

        //START SITEPAGEDICUSSION PLUGIN WORK
        $sitepagediscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
        if ($sitepagediscussionEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitepage, $role, 'sdicreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEDICUSSION PLUGIN WORK        
        //START SITEPAGEALBUM PLUGIN WORK
        $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
        if ($sitepagealbumEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitepage, $role, 'spcreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEALBUM PLUGIN WORK
        //START SITEPAGEDOCUMENT PLUGIN WORK
        $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
        if ($sitepageDocumentEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitepage, $role, 'sdcreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEDOCUMENT PLUGIN WORK
        //START SITEPAGEVIDEO PLUGIN WORK
        $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
        if ($sitepageVideoEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitepage, $role, 'svcreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEVIDEO PLUGIN WORK
        //START SITEPAGEPOLL PLUGIN WORK
        $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
        if ($sitepagePollEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitepage, $role, 'splcreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEPOLL PLUGIN WORK
        //START SITEPAGENOTE PLUGIN WORK
        $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
        if ($sitepageNoteEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitepage, $role, 'sncreate', ($i <= $createMax));
          }
        }
        //END SITEPAGENOTE PLUGIN WORK
        //START SITEPAGEEVENT PLUGIN WORK
        $sitepageEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
        if ($sitepageEventEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitepage, $role, 'secreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEEVENT PLUGIN WORK
        //Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

//       //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW PAGE.
//       $selectPageAdmin = $pageAdminTable->select()
//                       ->setIntegrityCheck(false)
//                       ->from($pageAdminTableName)
//                       ->where('name = ?', 'sitepage_index_view');
//       $pageAdminresult = $pageAdminTable->fetchRow($selectPageAdmin);
// 
//       //NOW INSERTING THE ROW IN PAGE TABLE
//       $pageObject = $pageTable->createRow();
//       $pageObject->displayname = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $pageObject->title = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $pageObject->description = $values['body'];
//       $pageObject->name = "sitepage_index_view";
//       $pageObject->url = $pageAdminresult->url;
//       $pageObject->custom = $pageAdminresult->custom;
//       $pageObject->fragment = $pageAdminresult->fragment;
//       $pageObject->keywords = $pageAdminresult->keywords;
//       $pageObject->layout = $pageAdminresult->layout;
//       $pageObject->view_count = $pageAdminresult->view_count;
//       $pageObject->user_id = $values['owner_id'];
//       $pageObject->page_id = $page_id;
//       $contentPageId = $pageObject->save();
// 
//       //NOW FETCHING PAGE CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS PAGE.
//       $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
//       if (!$layout) {
//         Engine_Api::_()->getDbtable('content', 'sitepage')->setContentDefault($contentPageId);
//       } else {
//         Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultLayout($contentPageId);
//       }
      //IF ALL PAGES HAS BEEN IMPORTED THAN CHANGE THE STATUS
      if ($importFile->current_import_id == $importFile->last_import_id) {
        $importFile->status = 'Completed';
      }
      $importFile->save();

      //CREATE LOG ENTRY IN LOG FILE
      if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToPageImport.log')) {

        $stringData = '';
        if ($import_count == 1) {
          $stringData .= "\n\n----------------------------------------------------------------------------------------------------------------\n";
          $stringData .= $this->view->translate("Import History of '") . $importFile->filename . $this->view->translate("' with file id: ") . $importFile->importfile_id . $this->view->translate(", created on ") . $importFile->creation_date . $this->view->translate(" is given below.");
          $stringData .= "\n----------------------------------------------------------------------------------------------------------------\n\n";
        }

        $myFile = APPLICATION_PATH . '/temporary/log/CSVToPageImport.log';
        $fh = fopen($myFile, 'a') or die("can't open file");
        $current_time = date('D, d M Y H:i:s T');
        $page_id = $sitepage->page_id;
        $page_title = $sitepage->title;
        $stringData .= $this->view->translate("Successfully created a new page at ") . $current_time . $this->view->translate(". ID and title of that Page are ") . $page_id . $this->view->translate(" and '") . $page_title . $this->view->translate("' respectively.") . "\n\n";
        fwrite($fh, $stringData);
        fclose($fh);
      }

      if ($import_count >= 100) {
        $current_import_id = $importFile->current_import_id + 1;
        $this->_redirect("admin/sitepage/importlisting/data-import?importfile_id=$importfile_id&current_import_id=$current_import_id&current_import=1");
      }
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Importing is done successfully !'))
    ));
  }

  //ACTION FOR MANAGING THE CSV FILES DATAS
  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_import');

    //FORM CREATION FOR SORTING
    $this->view->formFilter = $formFilter = new Sitepage_Form_Admin_Import_Filter();
    $page = $this->_getParam('page', 1);

    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitepage');
    $select = $tableImportFile->select();

    //IF IMPORT IS IN RUNNING STATUS FOR SOME FILE THAN DONT SHOW THE START BUTTON FOR ALL
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
    $this->view->runningSomeImport = 0;
    if (!empty($importFileStatusData)) {
      $this->view->runningSomeImport = 1;
    }

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
        'order' => 'importfile_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'importfile_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->total_slideshows = $paginator->getTotalItemCount();
    $this->view->paginator->setItemCountPerPage(100);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
  }

  //ACTION FOR STOP IMPORTING DATA
  public function stopAction() {

    //UPDATE THE STATUS TO STOP
    $importfile_id = $this->_getParam('importfile_id');
    $importFile = Engine_Api::_()->getItem('sitepage_importfile', $importfile_id);
    $importFile->status = 'Stopped';
    $importFile->save();

    //REDIRECTING TO MANAGE PAGE IF FORCE STOP
    $forceStop = $this->_getParam('forceStop');
    if (!empty($forceStop)) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }

  //ACTION FOR ROLLBACK IMPORTING DATA
  public function rollbackAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    //FETCH IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('sitepage_importfile', $importfile_id);

    //IF STATUS IS PENDING THAN RETURN
    if ($importFile->status == 'Pending') {
      return;
    }

    $returend_current_page_id = $this->_getParam('current_page_id');

    $redirect = 0;
    if (isset($_GET['redirect'])) {
      $redirect = $_GET['redirect'];
    }

    if (empty($redirect) && isset($_POST['redirect'])) {
      $redirect = $_POST['redirect'];
    }

    //START ROLLBACK IF CONFIRM BY USER OR RETURNED CURRENT PAGE ID IS NOT EMPTY
    if (!empty($redirect)) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $first_page_id = $importFile->first_page_id;
        $last_page_id = $importFile->last_page_id;

        if (!empty($first_page_id) && !empty($last_page_id)) {
          $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');

          $current_page_id = $first_page_id;

          if (!empty($returend_current_page_id)) {
            $current_page_id = $returend_current_page_id;
          }

          //MAKE QUERY
          $sqlStr = "page_id BETWEEN " . "'" . $current_page_id . "'" . " AND " . "'" . $last_page_id . "'" . "";

          $select = $tablePage->select()
                  ->from($tablePage->info('name'), array('page_id'))
                  ->where($sqlStr);
          $pageDatas = $select->query()->fetchAll();

          if (!empty($pageDatas)) {
            $rollback_count = 0;
            foreach ($pageDatas as $pageData) {
              $page_id = $pageData['page_id'];

              //DELETE PAGE
              Engine_Api::_()->sitepage()->onPageDelete($page_id);

              $db->commit();

              $rollback_count++;

              //REDIRECTING TO SAME ACTION AFTER EVERY 100 ROLLBACKS
              if ($rollback_count >= 100) {
                $current_page_id = $page_id + 1;
                $this->_redirect("admin/sitepage/importlisting/rollback?importfile_id=$importfile_id&current_page_id=$current_page_id&redirect=1");
              }
            }
          }
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //UPDATE THE DATA IN engine4_sitepage_importfiles TABLE
      $importFile->status = 'Pending';
      $importFile->first_page_id = 0;
      $importFile->last_page_id = 0;
      $importFile->current_import_id = $importFile->first_import_id;
      $importFile->save();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rollbacked successfully !'))
      ));
    }
    $this->renderScript('admin-importlisting/rollback.tpl');
  }

  //ACTION FOR DELETE IMPORT FILES AND IMPORT DATA
  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    //IF CONFIRM FOR DATA DELETION
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //IMPORT FILE OBJECT
        $importFile = Engine_Api::_()->getItem('sitepage_importfile', $importfile_id);

        if (!empty($importFile)) {

          $first_import_id = $importFile->first_import_id;
          $last_import_id = $importFile->last_import_id;

          //MAKE QUERY FOR FETCH THE DATA
          $tableImport = Engine_Api::_()->getDbtable('imports', 'sitepage');

          $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

          $select = $tableImport->select()
                  ->from($tableImport->info('name'), array('import_id'))
                  ->where($sqlStr);
          $importDatas = $select->query()->fetchAll();

          if (!empty($importDatas)) {
            foreach ($importDatas as $importData) {
              $import_id = $importData['import_id'];

              //DELETE IMPORT DATA BELONG TO IMPORT FILE
              $tableImport->delete(array('import_id = ?' => $import_id));
            }
          }

          //FINALLY DELETE IMPORT FILE DATA
          Engine_Api::_()->getDbtable('importfiles', 'sitepage')->delete(array('importfile_id = ?' => $importfile_id));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Import data has been deleted successfully !'))
      ));
    }
    $this->renderScript('admin-importlisting/delete.tpl');
  }

  //ACTION FOR DELETE SLIDESHOW AND THEIR BELONGINGS
  public function multiDeleteAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      //IF ADMIN CLICK ON DELETE SELECTED BUTTON
      if (!empty($values['delete'])) {
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            $importfile_id = (int) $value;
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
              //IMPORT FILE OBJECT
              $importFile = Engine_Api::_()->getItem('sitepage_importfile', $importfile_id);

              if (!empty($importFile)) {

                $first_import_id = $importFile->first_import_id;
                $last_import_id = $importFile->last_import_id;

                //MAKE QUERY FOR FETCH THE DATA
                $tableImport = Engine_Api::_()->getDbtable('imports', 'sitepage');

                $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

                $select = $tableImport->select()
                        ->from($tableImport->info('name'), array('import_id'))
                        ->where($sqlStr);
                $importDatas = $select->query()->fetchAll();

                if (!empty($importDatas)) {
                  foreach ($importDatas as $importData) {
                    $import_id = $importData['import_id'];

                    //DELETE IMPORT DATA BELONG TO IMPORT FILE
                    $tableImport->delete(array('import_id = ?' => $import_id));
                  }
                }

                //FINALLY DELETE IMPORT FILE DATA
                Engine_Api::_()->getDbtable('importfiles', 'sitepage')->delete(array('importfile_id = ?' => $importfile_id));
              }

              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }

  //ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE
  public function downloadSampleAction() {

    $path = $this->_getPath();
    $file_path = "$path/example_page_import.csv";

    @chmod($path, 0777);
    @chmod($file_path, 0777);

    $file_string = "";

    $file_string = "Title|Page_Url|Category|Sub_Category|3rd Level Category|Description|Overview|Tag_String|Location|Price|Email_Id|Web_Address|Phone_Number|Claim_a_Page|Photo Name.ext";

    @chmod($path, 0777);
    @chmod($file_path, 0777);
    $fp = fopen(APPLICATION_PATH . '/temporary/example_page_import.csv', 'w+');
    fwrite($fp, $file_string);
    fclose($fp);

    //KILL ZEND'S OB
    $isGZIPEnabled = false;
    if (ob_get_level()) {
      $isGZIPEnabled = true;
      @ob_end_clean();
    }

    $path = APPLICATION_PATH . "/temporary/example_page_import.csv";
    header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
    header("Content-Transfer-Encoding: Binary", true);
    //header("Content-Type: application/x-tar", true);
    // header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    if (empty($isGZIPEnabled)) {
      header("Content-Length: " . filesize($path), true);
    }

    readfile("$path");

    exit();
  }

  //ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE
  public function downloadAction() {

    $path_import = $this->_getPathImport();

    $db = Engine_Db_Table::getDefaultAdapter();
    $import_id = $db->select('importfile_id')
            ->from('engine4_sitepage_importfiles')
            ->query()
            ->fetchColumn();

    if (file_exists($path_import) && is_file($path_import) && !empty($import_id)) {

      //KILL ZEND'S OB
      $isGZIPEnabled = false;
      if (ob_get_level()) {
        $isGZIPEnabled = true;
        @ob_end_clean();
      }

      header("Content-Disposition: attachment; filename=" . urlencode(basename($path_import)), true);
      header("Content-Transfer-Encoding: Binary", true);
      header("Content-Type: application/x-tar", true);
      //header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      if (empty($isGZIPEnabled)) {
        header("Content-Length: " . filesize($path_import), true);
      }
      readfile("$path_import");
    } else {
      $path = $this->_getPath();
      $file_path = "$path/previous_page_import.csv";

      @chmod($path, 0777);
      @chmod($file_path, 0777);

      $file_string = "";
      $file_string = "Title|Page_Url|Category|Sub_Category|3rd Level Category|Description|Overview|Tag_String|Location|Price|Email_Id|Web_Address|Phone_Number|Claim_a_Page|Photo Name.ext";

      @chmod($path, 0777);
      @chmod($file_path, 0777);
      $fp = fopen(APPLICATION_PATH . '/temporary/previous_page_import.csv', 'w+');
      fwrite($fp, $file_string);
      fclose($fp);

      //KILL ZEND'S OB
      $isGZIPEnabled = false;
      if (ob_get_level()) {
        $isGZIPEnabled = true;
        @ob_end_clean();
      }

      $path = APPLICATION_PATH . "/temporary/previous_page_import.csv";
      header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
      header("Content-Transfer-Encoding: Binary", true);
      //header("Content-Type: application/x-tar", true);
      //header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      if (empty($isGZIPEnabled)) {
        header("Content-Length: " . filesize($path), true);
      }
      readfile("$path");
    }

    exit();
  }

  protected function _getPathImport($key = 'path') {

    $basePath = realpath(APPLICATION_PATH . "/public/sitepage_page");
    return $this->_checkPath($this->_getParam($key, ''), $basePath);
  }

  protected function _getPath($key = 'path') {
    $basePath = realpath(APPLICATION_PATH . "/temporary");
    return $this->_checkPath($this->_getParam($key, ''), $basePath);
  }

  protected function _checkPath($path, $basePath) {
    //SANATIZE
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    //Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);

    //CHECK IF THIS IS A PARENT OF THE BASE PATH
    if ($basePath != $path && strpos($basePath, $path) !== false) {
      return $this->_helper->redirector->gotoRoute(array());
    }
    return $path;
  }

  public function showFieldsAction() {

    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
    $fieldType = $this->_getParam('field');

    if ($fieldType == 'ethnicity') {
      $this->view->values = array(
          'asian' => 'Asian',
          'black' => 'Black / African descent',
          'hispanic' => 'Latino / Hispanic',
          'pacific' => 'Pacific Islander',
          'white' => 'White / Caucasian',
          'other' => 'Other'
      );
    } elseif ($fieldType == 'occupation') {
      $this->view->values = array('admn' => 'Administrative / Secretarial',
          'arch' => 'Architecture / Interior design',
          'crea' => 'Artistic / Creative / Performance',
          'educ' => 'Education / Teacher / Professor',
          'mngt' => 'Executive / Management',
          'fash' => 'Fashion / Model / Beauty',
          'fina' => 'Financial / Accounting / Real Estate',
          'labr' => 'Labor / Construction',
          'lawe' => 'Law enforcement / Security / Military',
          'legl' => 'Legal',
          'medi' => 'Medical / Dental / Veterinary / Fitness',
          'nonp' => 'Nonprofit / Volunteer / Activist',
          'poli' => 'Political / Govt / Civil Service / Military',
          'retl' => 'Retail / Food services',
          'retr' => 'Retired',
          'sale' => 'Sales / Marketing',
          'self' => 'Self-Employed / Entrepreneur',
          'stud' => 'Student',
          'tech' => 'Technical / Science / Computers / Engineering',
          'trav' => 'Travel / Hospitality / Transportation',
          'othr' => 'Other profession'
      );
    } elseif ($fieldType == 'education_level') {
      $this->view->values = array(
          'high_school' => 'High School',
          'some_college' => 'Some College',
          'associates' => 'Associates Degree',
          'bachelors' => 'Bachelors Degree',
          'graduate' => 'Graduate Degree',
          'phd' => 'PhD / Post Doctoral'
      );
    } elseif ($fieldType == 'relationship_status') {
      $this->view->values = array(
          'single' => 'Single',
          'relationship' => 'In a Relationship',
          'engaged' => 'Engaged',
          'married' => 'Married',
          'complicated' => 'Its Complicated',
          'open' => 'In an Open Relationship',
          'widow' => 'Widowed'
      );
    } elseif ($fieldType == 'looking_for') {
      $this->view->values = array(
          'friendship' => 'Friendship',
          'dating' => 'Dating',
          'relationship' => 'A Relationship',
          'networking' => 'Networking'
      );
    } elseif ($fieldType == 'weight') {
      $this->view->values = array(
          'slender' => 'Slender',
          'average' => 'Average',
          'athletic' => 'Athletic',
          'heavy' => 'Heavy',
          'stocky' => 'Stocky',
          'little_fat' => 'A few extra pounds'
      );
    } elseif ($fieldType == 'religion') {
      $this->view->values = array(
          'agnostic' => 'Agnostic',
          'atheist' => 'Atheist',
          'buddhist' => 'Buddhist',
          'taoist' => 'Taoist',
          'catholic' => 'Christian (Catholic)',
          'mormon' => 'Christian (LDS)',
          'protestant' => 'Christian (Protestant)',
          'hindu' => 'Hindu',
          'jewish' => 'Jewish',
          'muslim' => 'Muslim ',
          'spiritual' => 'Spiritual',
          'other' => 'Other'
      );
    } elseif ($fieldType == 'political_views') {
      $this->view->values = array(
          'mid' => 'Middle of the Road',
          'far_right' => 'Very Conservative',
          'right' => 'Conservative',
          'left' => 'Liberal',
          'far_left' => 'Very Liberal',
          'anarchy' => 'Non-conformist',
          'libertarian' => 'Libertarian',
          'green' => 'Green',
          'other' => 'Other'
      );
    } elseif ($fieldType == 'income') {
      $this->view->values = array(
          '0' => 'Less than $25,000',
          '25_35' => '$25,001 to $35,000',
          '35_50' => '$35,001 to $50,000',
          '50_75' => '$50,001 to $75,000',
          '75_100' => '$75,001 to $100,000',
          '100_150' => '$100,001 to $150,000',
          '1' => '$150,001'
      );
    } elseif ($fieldType == 'partner_gender') {
      $this->view->values = array(
          'men' => 'Men',
          'women' => 'Women'
      );
    } elseif ($fieldType == 'country') {
      $this->view->values = $territories;
    } elseif ($fieldType == 'zodiac') {
      $this->view->values = array(
          'apricorn' => 'Apricorn',
          'aquarius' => 'Aquarius',
          'pisces' => 'Pisces',
          'aries' => 'Aries',
          'taurus' => 'Taurus',
          'gemini' => 'Gemini',
          'cancer' => 'Cancer',
          'leo' => 'Leo',
          'virgo' => 'Virgo',
          'libra' => 'Libra',
          'scorpio' => 'Scorpio',
          'sagittarius' => 'Sagittarius'
      );
    } elseif ($fieldType == 'date') {
      $this->view->values = array(
          'YYYY-MM-DD' => '2013-8-15'
      );
    }
  }

  public function uploadPhotoAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_import');

    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    $this->view->importFile = Engine_Api::_()->getItem('sitepage_importfile', $importfile_id);

    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitepage');
    $select = $tableImportFile->select();

    //IF IMPORT IS IN RUNNING STATUS FOR SOME FILE THAN DONT SHOW THE START BUTTON FOR ALL
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
    $this->view->runningSomeImport = 0;
    if (!empty($importFileStatusData)) {
      $this->view->runningSomeImport = 1;
    }
  }

  public function uploadAction() {

    $importfile_id = $this->_getParam('importfile_id');
    $basePath = 'sitepage_importfiles_' . $importfile_id;

    if (is_dir(APPLICATION_PATH . "/public/$basePath") != 1) {
      @mkdir(APPLICATION_PATH . "/public/$basePath", 0777, true);
    }

    @chmod(APPLICATION_PATH . "/public/$basePath", 0777);
    $this->view->path = $path = APPLICATION_PATH . "/public/$basePath";

    // Check method
    if (!$this->getRequest()->isPost()) {
      return;
    }

    // Check ul bit
    if (null === $this->_getParam('ul')) {
      return;
    }

    // Prepare
    if (empty($_FILES['Filedata'])) {
      $this->view->error = 'File failed to upload. Check your server settings (such as php.ini max_upload_filesize).';
      return;
    }

    // Prevent evil files from being uploaded
    $disallowedExtensions = array('zip');
    if (!in_array(end(explode(".", $_FILES['Filedata']['name'])), $disallowedExtensions)) {
      $this->view->error = 'File type or extension forbidden.';
      return;
    }


    $info = $_FILES['Filedata'];
    $targetFile = $path . '/' . $info['name'];
    $vals = array();

    if (file_exists($targetFile)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("File already exists.");
      return;
    }

    // Try to move uploaded file
    if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
      return;
    }

    $pageImportfileTable = Engine_Api::_()->getDbtable('importfiles', 'sitepage');

    $selectImportTable = $pageImportfileTable->select()
            ->from($pageImportfileTable->info('name'), 'photo_filename')
            ->where('importfile_id = ?', $importfile_id);
    $fileName = $selectImportTable->query()->fetchColumn();

    if (!empty($fileName)) {
      $import_image_path = 'sitereview_importfiles_' . $importfile_id;
      @unlink(APPLICATION_PATH . "/public/" . $import_image_path . '/' . $fileName);
    }

    $pageImportfileTable->update(array('photo_filename' => $_FILES['Filedata']['name']), array('importfile_id = ?' => $importfile_id));

    $this->view->target_path = $info['tmp_name'];
    $this->view->status = 1;

    // Redirect
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext()) {
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
              ));
    }
  }

}

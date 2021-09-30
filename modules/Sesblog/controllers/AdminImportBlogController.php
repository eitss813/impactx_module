<?php

class Sesblog_AdminImportBlogController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_importblog');
    $setting = Engine_Api::_()->getApi('settings', 'core');

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('blog') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblog') && $setting->getSetting('sesblog.pluginactivated')) {

      $ssesblogTable = Engine_Api::_()->getDbTable('blogs', 'blog');
      $ssesblogTableName = $ssesblogTable->info('name');

      $coreLikeTable = Engine_Api::_()->getDbTable('likes', 'core');
      $coreLikeTableName = $coreLikeTable->info('name');

      $seSubscriptionsTable = Engine_Api::_()->getDbTable('subscriptions', 'blog');
      $seSubscriptionsTableName = $seSubscriptionsTable->info('name');

      $sesSubscriptionsTable = Engine_Api::_()->getDbTable('subscriptions', 'sesblog');
      $sesSubscriptionsTableName = $sesSubscriptionsTable->info('name');

      $coreCommentsTable = Engine_Api::_()->getDbTable('comments', 'core');
      $coreCommentsTableName = $coreCommentsTable->info('name');

      $sesblogTable = Engine_Api::_()->getDbTable('blogs', 'sesblog');
      $sesblogTableName = $sesblogTable->info('name');

      $blogRole = Engine_Api::_()->getDbTable('roles', 'sesblog');
      $blogRoleName = $blogRole->info('name');

      //Category Work
      $blogCategories = Engine_Api::_()->getDbTable('categories', 'blog');
      $blogCategoriesName = $blogCategories->info('name');
      $sesblogCategories = Engine_Api::_()->getDbTable('categories', 'sesblog');
      $sesblogCategoriesName = $sesblogCategories->info('name');

      $selectCategory = $blogCategories->select()
                                      ->from($blogCategoriesName);
      $seBlogCatResults = $blogCategories->fetchAll($selectCategory);
      foreach($seBlogCatResults as $seBlogCatResult) {
        $hasCategory = $sesblogCategories->hasCategory(array('category_name' => $seBlogCatResult->category_name));
        if($hasCategory) {
          $db->update('engine4_sesblog_categories', array('ssesblog_categoryid' => $seBlogCatResult->category_id), array("category_id = ?" => $hasCategory));
        } else {
          $sesblogCat = $sesblogCategories->createRow();
          $sesblogCat->category_name = $seBlogCatResult->category_name;
          $sesblogCat->title = $seBlogCatResult->category_name;
          $sesblogCat->user_id = $seBlogCatResult->user_id;
          $sesblogCat->slug = $seBlogCatResult->getSlug();
          $sesblogCat->ssesblog_categoryid = $seBlogCatResult->category_id;
          $sesblogCat->save();
          $sesblogCat->order = $sesblogCat->category_id;
          $sesblogCat->save();
        }
      }

        $storageTable = Engine_Api::_()->getDbtable('files', 'storage');

        $sesblogsSelect = $sesblogTable->select()
                                    ->from($sesblogTableName, array('ssesblog_id'))
                                    ->where('ssesblog_id <> ?', 0);
        $sesblogResults = $sesblogTable->fetchAll($sesblogsSelect);
        $importedBlogArray = array();
        foreach($sesblogResults as $sesblogResult) {
            $importedBlogArray[] = $sesblogResult->ssesblog_id;
        }

        $selectSeBlogs = $ssesblogTable->select()->from($ssesblogTableName);
        if(count($importedBlogArray) > 0) {
            $selectSeBlogs->where('blog_id NOT IN (?)', $importedBlogArray);
        }
        $selectSeBlogs->order('blog_id ASC');
      $this->view->seBlogResults = $seBlogResults = $ssesblogTable->fetchAll($selectSeBlogs);
      if ($seBlogResults && isset($_GET['is_ajax']) && $_GET['is_ajax']) {
        try {
          foreach ($seBlogResults as $seBlogResult) {

            $se_blogId = $seBlogResult->blog_id;
            if ($se_blogId) {

              $sesblog = $sesblogTable->createRow();
              $sesblog->title = $seBlogResult->title;
              $sesblog->custom_url = $seBlogResult->getSlug();
              $sesblog->body = $seBlogResult->body;
              $sesblog->owner_type = $seBlogResult->owner_type;
              $sesblog->category_id = $seBlogResult->category_id;
              $sesblog->owner_id = $seBlogResult->owner_id;
              $sesblog->search = $seBlogResult->search;
              $sesblog->view_count = $seBlogResult->view_count;
              $sesblog->comment_count = $seBlogResult->comment_count;
              $sesblog->creation_date = $seBlogResult->creation_date;
              $sesblog->modified_date = $seBlogResult->modified_date;
              $sesblog->publish_date = $seBlogResult->creation_date;
              $sesblog->seo_title = $seBlogResult->title;
              $sesblog->seo_keywords = $seBlogResult->title;
              $sesblog->save();

            if (!empty($seBlogResult->photo_id)) {
                $photoImport = Engine_Api::_()->getDbtable('files', 'storage')->fetchRow(array('file_id = ?' => $seBlogResult->photo_id
                ));
                if (!empty($photoImport)) {
                    $sesblog->photo_id = Engine_Api::_()->sesbasic()->setPhoto($photoImport, false,false,'sesblog','sesblog_blog','',$sesblog,true);
                    $sesblog->save();
                    //$sesblog->setPhoto($photoImport->storage_path);
                }
            }

              if($seBlogResult->category_id) {
                $hasCategoryId = $sesblogCategories->hasCategoryId(array('ssesblog_categoryid' => $seBlogResult->category_id));
                if($hasCategoryId) {
                  $sesblog->category_id = $hasCategoryId;
                  $sesblog->save();
                }
              }
              $sesblog->creation_date = $seBlogResult->creation_date;
              $sesblog->modified_date = $seBlogResult->modified_date;
              $sesblog->publish_date = $seBlogResult->creation_date;
              $sesblog->save();
              //sesblog blog id.
              $sesblogId = $sesblog->blog_id;

              //Role Created to owner
              $sesblogRole = $blogRole->createRow();
              $sesblogRole->user_id = $sesblog->owner_id;
              $sesblogRole->blog_id = $sesblogId;
              $sesblogRole->save();

              //Core Tag Table Work
              $tagTitle = '';
              $seBlogTags = $seBlogResult->tags()->getTagMaps();
              foreach ($seBlogTags as $tag) {
                $user = Engine_Api::_()->getItem('user', $seBlogResult->owner_id);
                if ($tagTitle != '')
                  $tagTitle .= ', ';
                $tagTitle .= $tag->getTag()->getTitle();
                $tags = array_filter(array_map("trim", preg_split('/[,]+/', $tagTitle)));
                $sesblog->tags()->setTagMaps($user, $tags);
              }

              //Subscribe Table
              $selectseSubscriptions = $seSubscriptionsTable->select()
                                      ->from($seSubscriptionsTableName);
              $seSubscriptionsResults = $seSubscriptionsTable->fetchAll($selectseSubscriptions);
              foreach ($seSubscriptionsResults as $seSubscriptionsResult) {
                $sesSubscriptionsBlog = $sesSubscriptionsTable->createRow();
                $sesSubscriptionsBlog->user_id = $seSubscriptionsResult->user_id;
                $sesSubscriptionsBlog->subscriber_user_id = $seSubscriptionsResult->subscriber_user_id;;
                $sesSubscriptionsBlog->save();
              }

              //Core like table data
              $selectPlaylistLike = $coreLikeTable->select()
                      ->from($coreLikeTableName)
                      ->where('resource_id = ?', $se_blogId)
                      ->where('resource_type = ?', 'blog');
              $ssesblogLikeResults = $coreLikeTable->fetchAll($selectPlaylistLike);
              foreach ($ssesblogLikeResults as $ssesblogLikeResult) {
                $like = Engine_Api::_()->getItem('core_like', $ssesblogLikeResult->like_id);
                $coreLikeBlog = $coreLikeTable->createRow();
                $coreLikeBlog->resource_type = 'sesblog_blog';
                $coreLikeBlog->resource_id = $sesblogId;
                $coreLikeBlog->poster_type = 'user';
                $coreLikeBlog->poster_id = $like->poster_id;
                //$coreLikeBlog->creation_date = $like->creation_date;
                $coreLikeBlog->save();
              }

              //Core comments table data
              $selectSeBlogComments = $coreCommentsTable->select()
                      ->from($coreCommentsTableName)
                      ->where('resource_id = ?', $se_blogId)
                      ->where('resource_type = ?', 'blog');
              $seBlogCommentsResults = $coreCommentsTable->fetchAll($selectSeBlogComments);
              foreach ($seBlogCommentsResults as $seBlogCommentsResult) {
                $comment = Engine_Api::_()->getItem('core_comment', $seBlogCommentsResult->comment_id);

                $coreCommentBlog = $coreCommentsTable->createRow();
                $coreCommentBlog->resource_type = 'sesblog_blog';
                $coreCommentBlog->resource_id = $sesblogId;
                $coreCommentBlog->poster_type = 'user';
                $coreCommentBlog->poster_id = $comment->poster_id;
                $coreCommentBlog->body = $comment->body;
                $coreCommentBlog->creation_date = $comment->creation_date;
                $coreCommentBlog->like_count = $comment->like_count;
                $coreCommentBlog->save();
              }


              //Privacy work
              $auth = Engine_Api::_()->authorization()->context;
              $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

              foreach ($roles as $role) {
                if ($auth->isAllowed($sesblog, $role, 'view')) {
                  $values['auth_view'] = $role;
                }
              }
              foreach ($roles as $role) {
                if ($auth->isAllowed($sesblog, $role, 'comment')) {
                  $values['auth_comment'] = $role;
                }
              }

              $viewMax = array_search($values['auth_view'], $roles);
              $commentMax = array_search($values['auth_comment'], $roles);
              foreach ($roles as $i => $role) {
                $auth->setAllowed($sesblog, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($sesblog, $role, 'comment', ($i <= $commentMax));
              }

              $sesblog->ssesblog_id = $seBlogResult->getIdentity();
              $sesblog->save();
              //$seBlogResult->blogimport = 1;
              //$seBlogResult->save();
            }
          }
        } catch (Exception $e) {
          //$db->rollBack();
          throw $e;
        }
      }
    }
  }

}

<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagediscussion
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagediscussion_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {

    $getErrorMsg = $this->getVersion(); 
    if (!empty($getErrorMsg)) {
      return $this->_error($getErrorMsg);
    }

    parent::onPreInstall();
  }


  private function getVersion() {
  
    $db = $this->getDb();

    $errorMsg = '';
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
      'sitepage' => '4.6.0',
    );
    
    $finalModules = array();
    foreach ($modArray as $key => $value) {
    		$select = new Zend_Db_Select($db);
		$select->from('engine4_core_modules')
					->where('name = ?', "$key")
					->where('enabled = ?', 1);
		$isModEnabled = $select->query()->fetchObject();
			if (!empty($isModEnabled)) {
				$select = new Zend_Db_Select($db);
				$select->from('engine4_core_modules',array('title', 'version'))
					->where('name = ?', "$key")
					->where('enabled = ?', 1);
				$getModVersion = $select->query()->fetchObject();

				$isModSupport = $this->checkVersion($getModVersion->version, $value);
				if (empty($isModSupport)) {
					$finalModules[] = $getModVersion->title;
				}
			}
    }

    foreach ($finalModules as $modArray) {
      $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Directory / Pages Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
    }

    return $errorMsg;
  }
    private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
  function onInstall() {

    //GET DB
    $db = $this->getDb();
    
    $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `body` =  \'{item:$subject} has created a page discussion {item:$object}.\' WHERE  `engine4_activity_notificationtypes`.`type` =  "sitepagediscussion_create";');    

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepagediscussion')
            ->where('version <= ?', '4.6.0p3')
            ->where('enabled = ?', 1);
    $check_sitepage = $select->query()->fetchObject();
    if (!empty($check_sitepage)) {
			//START WORK FOR THE ALREADY CREATED DISCUSSION AND ENTRY FOR CORROSPONDING TO ALL MEMBER IN TO THE WATCH TABLE.
			$select = new Zend_Db_Select($db);
			$select->from('engine4_core_modules')
						->where('name = ?', 'sitepagemember')
						->where('enabled = ?', 1);
			$check_sitepagemember = $select->query()->fetchObject();
			if (!empty($check_sitepagemember)) {
				$select = new Zend_Db_Select($db);
				$select->from('engine4_sitepage_topics');
				$topics_results =  $select->query()->fetchAll();
				if (!empty($topics_results)) {
					foreach($topics_results as $result) {

						$page_id = $result['page_id'];
						$topic_id = $result['topic_id'];

						$select = new Zend_Db_Select($db);
						$select = $select->from('engine4_sitepage_membership')
											->where('active = ?', 1)
											->where('resource_approved = ?', 1)
											->where('user_approved = ?', 1)
											->where('page_id = ?', $page_id);

						$member_results =  $select->query()->fetchAll();
						if(!empty($member_results)) {
							foreach($member_results as $member) {
								$user_id = $member['user_id'];
								$db->query("INSERT IGNORE INTO `engine4_sitepage_topicwatches` (`resource_id`, `topic_id`, `user_id`, `watch`, `page_id`) VALUES ('$page_id', '$topic_id', '$user_id', '1', '$page_id');");
							}
						}
					}
				}
			}
			//START WORK FOR THE ALREADY CREATED DISCUSSION AND ENTRY FOR CORROSPONDING TO ALL MEMBER IN TO THE WATCH TABLE.
    }

    $db->update('engine4_activity_actiontypes', array('body' => '{item:$object} posted a discussion topic {itemChild:$object:sitepage_topic:$child_id} in the page {item:$object}: {body:$body}'), array('type = ?' => 'sitepage_admin_topic_create'));
    $db->update('engine4_activity_actiontypes', array('body' => '{item:$object} replied to a discussion topic {itemChild:$object:sitepage_topic:$child_id} in the page {item:$object}: {body:$body}'), array('type = ?' => 'sitepage_admin_topic_reply'));
    $db->update('engine4_activity_actiontypes', array('body' => '{item:$subject} posted a discussion topic {itemChild:$object:sitepage_topic:$child_id} in the page {item:$object}: {body:$body}'), array('type = ?' => 'sitepage_topic_create'));
    $db->update('engine4_activity_actiontypes', array('body' => '{item:$subject} replied to a discussion topic {itemChild:$object:sitepage_topic:$child_id} in the page {item:$object}: {body:$body}'), array('type = ?' => 'sitepage_topic_reply'));
    
    //START DISCUSSION PRIVACY WORK
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepagediscussion')
            ->where('version <= ?', '4.6.0')
            ->where('enabled = ?', 1);
    $check_sitepage = $select->query()->fetchObject();
    if (!empty($check_sitepage)) {
      
      //TOPIC CREATE ACTIVITY WORK
      $select = new Zend_Db_Select($db);
      $select->from('engine4_activity_actions', array('action_id'))
             ->where("type = 'sitepage_topic_create' OR type = 'sitepage_admin_topic_create'");
      $actionIds = $select->query()->fetchAll();
      foreach($actionIds as $action) {
        $actionId = $action['action_id'];
        $topicId = $db->select()
                      ->from('engine4_activity_attachments', array('id'))
                      ->where('action_id = ?', $actionId)
                      ->where('type = ?', 'sitepage_topic')
                      ->limit(1)
                      ->query()
                      ->fetchColumn();
        if(!empty($topicId)) {
          $db->update('engine4_activity_actions', array('params' => '{"child_id":'.$topicId.'}'), array('action_id = ?' => $actionId));          
        }
      }
      
      //TOPIC REPLY ACTIVITY WORK
      $select = new Zend_Db_Select($db);
      $select->from('engine4_activity_actions', array('action_id'))
             ->where("type = 'sitepage_topic_reply' OR type = 'sitepage_admin_topic_reply'");
      $actionIds = $select->query()->fetchAll();
      foreach($actionIds as $action) {
        $actionId = $action['action_id'];
        $postId = $db->select()
                      ->from('engine4_activity_attachments', array('id'))
                      ->where('action_id = ?', $actionId)
                      ->where('type = ?', 'sitepage_post')
                      ->limit(1)
                      ->query()
                      ->fetchColumn();
        if(!empty($postId)) {
          
          $topicId = $db->select()
                        ->from('engine4_sitepage_posts', array('topic_id'))
                        ->where('post_id = ?', $postId)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();   
          if(!empty($topicId)) {
            $db->update('engine4_activity_actions', array('params' => '{"child_id":'.$topicId.'}'), array('action_id = ?' => $actionId));       
          }
        }
      }
    }    

    //START DISCUSSION PRIVACY WORK
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepagediscussion')
            ->where('version <= ?', '4.6.0')
            ->where('enabled = ?', 1);
    $check_sitepage = $select->query()->fetchObject();
    if (!empty($check_sitepage)) {

      //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
      ini_set('memory_limit', '1024M');
      set_time_limit(0);

      $select = new Zend_Db_Select($db);
      $select = $select->from('engine4_authorization_allow', array('resource_id', 'role'))
              ->where('resource_type = ?', 'sitepage_page')
              ->where('action = ?', 'comment');
      $commentPrivacyDatas = $select->query()->fetchAll();
      foreach ($commentPrivacyDatas as $commentPrivacyData) {

        $resource_id = $commentPrivacyData['resource_id'];
        $role = $commentPrivacyData['role'];

        if ($commentPrivacyData['role'] == 'everyone') {
          continue;
        } elseif ($commentPrivacyData['role'] == 'owner_member') {

          $db->query("INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`,`resource_id`,`action`,`role`,`value`) VALUES ('sitepage_page',$resource_id,'sdicreate','like_member',1);");
        }

        $db->query("INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`,`resource_id`,`action`,`role`,`value`) VALUES ('sitepage_page',$resource_id,'$role','like_member',1);");
      }

      $db->query('
        INSERT IGNORE INTO `engine4_authorization_permissions`
          SELECT
            level_id as `level_id`,
            "sitepage_page" as `type`,
            "auth_sdicreate" as `name`,
            5 as `value`,
            \'["registered","owner_network","owner_member_member","owner_member","owner", "member", "like_member"]\' as `params`
          FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");      
      ');

      $db->query('
        INSERT IGNORE INTO `engine4_authorization_permissions`
          SELECT
            level_id as `level_id`,
            "sitepage_page" as `type`,
            "sdicreate" as `name`,
            1 as `value`,
            NULL as `params`
          FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");  
      ');
    }
			
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_modules')
						->where('name = ?', 'communityad')
						->where('enabled 	 = ?', 1)
						->limit(1);
		;
		$infomation = $select->query()->fetch();
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_settings')
						->where('name = ?', 'sitepage.communityads')
						->where('value 	 = ?', 1)
						->limit(1);
		$rowinfo = $select->query()->fetch();

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_pages')
						->where('name = ?', 'sitepage_topic_view')
						->limit(1);
		;
		$info = $select->query()->fetch();

		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
					'name' => 'sitepage_topic_view',
					'displayname' => 'Page Discussion Topic View Page',
					'title' => 'View Page Discussion Topic',
					'description' => 'This is the view page for a page discussion.',
					'custom' => 1,
					'provides' => 'subject=sitepage_topic',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 1,
					'params' => '',
			));
			$container_id = $db->lastInsertId('engine4_core_content');

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 1,
					'params' => '',
			));
			$right_id = $db->lastInsertId('engine4_core_content');

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 3,
					'params' => '',
			));
			$middle_id = $db->lastInsertId('engine4_core_content');

			// middle column content
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepage.discussion-content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '',
			));

			if ($infomation && $rowinfo) {
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepage.page-ads',
						'parent_content_id' => $right_id,
						'order' => 5,
						'params' => '{"title":"","titleCount":""}',
				));
			}
		}

    //CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.is.active')
            ->limit(1);
    $sitepage_settings = $select->query()->fetchAll();
    if (!empty($sitepage_settings)) {
      $sitepage_is_active = $sitepage_settings[0]['value'];
    } else {
      $sitepage_is_active = 0;
    }

    //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('enabled = ?', 1);
    $check_sitepage = $select->query()->fetchObject();
    if (!empty($check_sitepage) && !empty($sitepage_is_active)) {
      $select = new Zend_Db_Select($db);
      $check_sitepagediscussion = $select
                      ->from('engine4_core_modules')
                      ->where('name = ?', 'sitepagediscussion')->query()->fetchObject();
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitepagediscussion')
              ->where('version <= ?', '4.2.1');
      $is_enabled = $select->query()->fetchObject();
      if (!empty($is_enabled)) {
        $select = new Zend_Db_Select($db);
        $select_page = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitepage_index_view')
                ->limit(1);
        $page = $select_page->query()->fetchAll();
        if (!empty($page)) {
          $page_id = $page[0]['page_id'];
          //PUT SITEPAGE DISCUSSION WIDGET IN ADMIN CONTENT TABLE
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_admincontent')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepage.discussion-sitepage')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_admincontent', 'admincontent_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['admincontent_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitepage_admincontent')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['admincontent_id'];
                $select = new Zend_Db_Select($db);
                $select_tab = $select
                        ->from('engine4_sitepage_admincontent')
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('page_id = ?', $page_id)
                        ->limit(1);
                $tab = $select_tab->query()->fetchAll();
                $tab_id = 0;
                if (!empty($tab)) {
                  $tab_id = $tab[0]['admincontent_id'];
                } else {
                  $tab_id = $middle_id;
                }
                $db->insert('engine4_sitepage_admincontent', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.discussion-sitepage',
                    'parent_content_id' => $tab_id,
                    'order' => 119,
                    'params' => '{"title":"Discussions","titleCount":"true"}',
                ));
              }
            }
          }
        }
      }

      //PUT SITEPAGE MOST DISCUSSION WIDGET IN SITEPAGE HOME PAGE
      if (empty($check_sitepagediscussion)) {
        $select = new Zend_Db_Select($db);
        $fetchPageId = $select
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name =?', 'sitepage_index_home')
                        ->limit(1)->query()->fetchAll();
        $select = new Zend_Db_Select($db);
        $selectWidgetId = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id =?', $fetchPageId[0]['page_id'])
                ->where('type = ?', 'container')
                ->where('name = ?', 'main')
                ->limit(1);
        $fetchWidgetContenerId = $selectWidgetId->query()->fetchAll();
        $select = new Zend_Db_Select($db);
        $selectWidgetId = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id =?', $fetchPageId[0]['page_id'])
                ->where('type = ?', 'container')
                ->where('name = ?', 'right')
                ->where('parent_content_id = ?', $fetchWidgetContenerId[0]['content_id'])
                ->limit(1);
        $rightid = $selectWidgetId->query()->fetchAll();
        if (!empty($rightid)) {
          $select = new Zend_Db_Select($db);
          $selectWidgetId = $select
                  ->from('engine4_core_content', 'content_id')
                  ->where('page_id =?', $fetchPageId[0]['page_id'])
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepage.mostdiscussion-sitepage')
                  ->where('parent_content_id = ?', $rightid[0]['content_id'])
                  ->limit(1);
          $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
          if (empty($fetchWidgetContentId)) {
            $db = $this->getDb();
            $db->insert('engine4_core_content', array(
                'page_id' => $fetchPageId[0]['page_id'],
                'type' => 'widget',
                'name' => 'sitepage.mostdiscussion-sitepage',
                'parent_content_id' => $rightid[0]['content_id'],
                'order' => 999,
                'params' => '{"title":"Most Discussed Pages","titleCount":"true"}',
            ));
          }
        }
        //PUT SITEPAGE DISCUSSION WIDGET IN ADMIN CONTENT TABLE
        $select = new Zend_Db_Select($db);
        $select_page = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitepage_index_view')
                ->limit(1);
        $page = $select_page->query()->fetchAll();
        if (!empty($page)) {
          $page_id = $page[0]['page_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_admincontent')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepage.discussion-sitepage')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_admincontent', 'admincontent_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['admincontent_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitepage_admincontent')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['admincontent_id'];
                $select = new Zend_Db_Select($db);
                $select_tab = $select
                        ->from('engine4_sitepage_admincontent')
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('page_id = ?', $page_id)
                        ->limit(1);
                $tab = $select_tab->query()->fetchAll();
                $tab_id = 0;
                if (!empty($tab)) {
                  $tab_id = $tab[0]['admincontent_id'];
                } else {
                  $tab_id = $middle_id;
                }

                $db->insert('engine4_sitepage_admincontent', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.discussion-sitepage',
                    'parent_content_id' => $tab_id,
                    'order' => 119,
                    'params' => '{"title":"Discussions","titleCount":"true"}',
                ));
              }
            }
          }

          //PUT SITEPAGE DISCUSSION WIDGET IN CORE CONTENT TABLE
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_core_content')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepage.discussion-sitepage')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_core_content', 'content_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];

              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_core_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['content_id'];

                $select = new Zend_Db_Select($db);
                $select_tab = $select
                        ->from('engine4_core_content')
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('page_id = ?', $page_id)
                        ->limit(1);
                $tab = $select_tab->query()->fetchAll();
                if (!empty($tab)) {
                  $tab_id = $tab[0]['content_id'];
                }

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.discussion-sitepage',
                    'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
                    'order' => 119,
                    'params' => '{"title":"Discussions","titleCount":"true"}',
                ));

                //PUT SITEPAGE DISCUSSION WIDGET IN USER CONTENT TABLE
                $select = new Zend_Db_Select($db);
                $select = $select
                        ->from('engine4_sitepage_contentpages', 'contentpage_id');

                $contentpage_ids = $select->query()->fetchAll();
                foreach ($contentpage_ids as $contentpage_id) {
                  if (!empty($contentpage_id)) {
                    $page_id = $contentpage_id['contentpage_id'];
                    $select = new Zend_Db_Select($db);
                    $select_content = $select
                            ->from('engine4_sitepage_content')
                            ->where('contentpage_id = ?', $page_id)
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'sitepage.discussion-sitepage')
                            ->limit(1);
                    $content = $select_content->query()->fetchAll();
                    if (empty($content)) {
                      $select = new Zend_Db_Select($db);
                      $select_container = $select
                              ->from('engine4_sitepage_content', 'content_id')
                              ->where('contentpage_id = ?', $page_id)
                              ->where('type = ?', 'container')
                              ->limit(1);
                      $container = $select_container->query()->fetchAll();
                      if (!empty($container)) {
                        $container_id = $container[0]['content_id'];
                        $select = new Zend_Db_Select($db);
                        $select_middle = $select
                                ->from('engine4_sitepage_content')
                                ->where('parent_content_id = ?', $container_id)
                                ->where('type = ?', 'container')
                                ->where('name = ?', 'middle')
                                ->limit(1);
                        $middle = $select_middle->query()->fetchAll();
                        if (!empty($middle)) {
                          $middle_id = $middle[0]['content_id'];
                          $select = new Zend_Db_Select($db);
                          $select_tab = $select
                                  ->from('engine4_sitepage_content')
                                  ->where('type = ?', 'widget')
                                  ->where('name = ?', 'core.container-tabs')
                                  ->where('contentpage_id = ?', $page_id)
                                  ->limit(1);
                          $tab = $select_tab->query()->fetchAll();
                          if (!empty($tab)) {
                            $tab_id = $tab[0]['content_id'];
                          }
                          $db->insert('engine4_sitepage_content', array(
                              'contentpage_id' => $page_id,
                              'type' => 'widget',
                              'name' => 'sitepage.discussion-sitepage',
                              'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
                              'order' => 119,
                              'params' => '{"title":"Discussions","titleCount":"true"}',
                          ));
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        $this->oninstallPackageEnableSubMOdules();
      }


      $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_topicwatches'")->fetch();
      if (!empty($table_exist)) {
        //ADD THE INDEX FROM THE "engine4_sitepage_topicwatches" TABLE
        $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_topicwatches` WHERE Key_name = 'page_id'")->fetch();

        if (empty($pageIdColumnIndex)) {
          $db->query("ALTER TABLE `engine4_sitepage_topicwatches` ADD INDEX ( `page_id` )");
        }
      }
      parent::onInstall();
    } elseif (!empty($check_sitepage) && empty($sitepage_is_active)) {
      $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      if (strstr($url_string, "manage/install")) {
        $calling_from = 'install';
      } else if (strstr($url_string, "manage/query")) {
        $calling_from = 'queary';
      }
      $explode_base_url = explode("/", $baseUrl);
      foreach ($explode_base_url as $url_key) {
        if ($url_key != 'install') {
          $core_final_url .= $url_key . '/';
        }
      }

      return $this->_error("<span style='color:red'>Note: You have installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Discussions Extension.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Directory / Pages Plugin.");
    } else {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> on your site yet. Please install it first before installing the <a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-discussions' target='_blank'>Directory / Pages - Discussions Extension</a>.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.feed.type');
    $info = $select->query()->fetch();
    $enable = 1;
    if (!empty($info))
      $enable = $info['value'];
    $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitepage_admin_topic_create", "sitepagediscussion", "{item:$object} posted a new discussion topic:", ' . $enable . ', 6, 2, 1, 1, 1, 1),("sitepage_admin_topic_reply", "sitepagediscussion", "{item:$object} replied to a discussion in the page:", ' . $enable . ', "6", "2", "1", "1", "1", "1")');

		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitemobile')
					->where('enabled = ?', 1);
		$is_sitemobile_object = $select->query()->fetchObject();
		if($is_sitemobile_object)  {
				include APPLICATION_PATH . "/application/modules/Sitepagediscussion/controllers/license/mobileLayoutCreation.php";
		}
  }

  function oninstallPackageEnableSubMOdules() {

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepagediscussion');
    $check_sitepagediscussion = $select->query()->fetchObject();
    if (empty($check_sitepagediscussion)) {
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_sitepage_packages')
              ->where('defaultpackage = ?', '1')
              ->limit(1);
      $sitepage_defaultPackage = $select->query()->fetchAll();
      if (!empty($sitepage_defaultPackage)) {
        $values = array();
        $values = unserialize($sitepage_defaultPackage[0]['modules']);
        $values[] = 'sitepagediscussion';
        $modules = serialize($values);
        $db->update('engine4_sitepage_packages', array(
            'modules' => $modules,
                ), array(
            'defaultpackage = ?' => "1"
        ));
      }
    }
  }

  public function onPostInstall() {

    $db = $this->getDb();
		$select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
    $is_sitemobile_object = $select->query()->fetchObject();
    if(!empty($is_sitemobile_object)) {
			$db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('sitepagediscussion','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitepagediscussion')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepagediscussion/integrated/0/redirect/install');
				} 
      }
    } 
    
    //Work for the word changes in the page plugin .csv file.
    $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
    $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
    if($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
      $view = new Zend_View();
      $baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
      $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
      if ($actionName == 'install') {
        $redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/install');
      } else {
        $redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/query');
      }
    }
    
  }

}

?>

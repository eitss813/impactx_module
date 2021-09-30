<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Api_SubCore extends Core_Api_Abstract {

  /**
   * Get feeds for page profile page
   *
   * @$user User_Model_User
   * @param array $params
   */
  public function getEveryonePageProfileFeeds(Core_Model_Item_Abstract $about, array $params = array()) {
    $ids = array();
    if (!(bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feed.everyone', 0))
      return $ids;
    //Proc args
    extract($params); //action_id, limit, min_id, max_id

    $actionDbTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $select = $actionDbTable->select();
    if ($about->getType() == 'sitepage_page') {
      $select->where("(subject_type ='sitepage_page'  and subject_id = ? ) OR ( (type <> 'sitepage_new' AND type <> 'like_sitepage_page') and object_type ='sitepage_page'  and object_id = ?) ", $about->getIdentity());
    } elseif ($about->getType() == 'sitepageevent_event') {
      $select->where("(subject_type ='sitepageevent_event'  and subject_id = ? ) OR ( (type <> 'like_sitepageevent_event') and object_type ='sitepageevent_event'  and object_id = ?) ", $about->getIdentity());
    }
    $select->order('action_id DESC')
            ->limit($limit);

    // Add action_id/max_id/min_id
    if (null !== $action_id) {
      $select->where('action_id = ?', $action_id);
    } else {
      if (null !== $min_id) {
        $select->where('action_id >= ?', $min_id);
      } else if (null !== $max_id) {
        $select->where('action_id <= ?', $max_id);
      }
    }
    $results = $actionDbTable->fetchAll($select);
    foreach ($results as $actionData)
      $ids[] = $actionData->action_id;
    return $ids;
  }

  /**
   * Delete Create Activity Feed Of Item Before Delete Item
   *
   * $item
   * @$actionsType array $actionsType
   */
  public function deleteCreateActivityOfExtensionsItem($item, $actionsType = array()) {

    $attachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $attachmentsTableName = $attachmentsTable->info('name');
    $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $actionsTableName = $actionsTable->info('name');
    $select = $attachmentsTable->select()
            ->setIntegrityCheck(false)
            ->from($attachmentsTableName, array($attachmentsTableName . '.action_id'))
            ->join($actionsTableName, "`{$attachmentsTableName}`.action_id = `{$actionsTableName}`.action_id  ", null)
            ->where($attachmentsTableName . '.id = ?', $item->getIdentity())
            ->where($attachmentsTableName . '.type = ?', $item->getType())
            ->where($actionsTableName . '.type in(?)', new Zend_Db_Expr("'" . join("', '", $actionsType) . "'"));

    $row = $attachmentsTable->fetchRow($select);
    if (!empty($row)) {
      $action = $actionsTable->fetchRow(array('action_id =?' => $row->action_id));
      if (!empty($action)) {
        $action->deleteItem();
        $action->delete();
      }
    }
  }

  /**
   * Page base network enable
   *
   * @return bool
   */
  public function pageBaseNetworkEnable() {
    return (bool) ( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.networks.type', 0) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.default.show', 0)));
  }

  /**
   * Content in File or not
   *
   * @return bool
   */
  public function isContentInFile($path, $string) {

    $isContentInFile = 0;
    if (is_file($path)) {
      @chmod($path, 0777);
      $fileData = file($path);
      foreach ($fileData as $key => $value) {
        $pos = strpos($value, $string);
        if ($pos !== false) {
          $isContentInFile = 1;
          break;
        }
      }
    }
    return $isContentInFile;
  }

  /**
   * Activity Feed Widget
   *
   * @return bool
   */
  public function isCoreActivtyFeedWidget($pageName, $widgetName, $params = array()) {
    $isCoreActivtyFeedWidget = false;

    $pagesTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pagesTableName = $pagesTable->info('name');
    $contentsTable = Engine_Api::_()->getDbtable('content', 'core');
    $contentsTableName = $contentsTable->info('name');

    $select = $contentsTable->select()
            ->setIntegrityCheck(false)
            ->from($contentsTableName, array($contentsTableName . '.name'))
            ->join($pagesTableName, "`{$pagesTableName}`.page_id = `{$contentsTableName}`.page_id  ", null)
            ->where($pagesTableName . '.name = ?', $pageName)
            ->where($contentsTableName . '.name = ?', $widgetName);
    $row = $contentsTable->fetchRow($select);
    if (!empty($row))
      $isCoreActivtyFeedWidget = true;
    return $isCoreActivtyFeedWidget;
  }

  /**
   * Activity Feed Widget
   *
   * @return bool
   */
  public function isPageCoreActivtyFeedWidget($widgetName, $params = array()) {
    $isCoreActivtyFeedWidget = false;
    $contentsTable = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
    $select = $contentsTable->select()
            ->where('name = ?', $widgetName);
    $row = $contentsTable->fetchRow($select);
    if (!empty($row))
      $isCoreActivtyFeedWidget = true;
    return $isCoreActivtyFeedWidget;
  }

  /**
   * GET TRUE OR FALSE FOR SAMPLE AD WIDGET
   *
   * @return bool
   */
  public function getSampleAdWidgetEnabled($sitepage) {

    //CHECK PAGE OWNER IS THERE OR NOT
    $isManageAdmin = Engine_Api::_()->sitepage()->isPageOwner($sitepage);
    if (!$isManageAdmin) {
      return false;
    }

    //CHECK WHETHER THE SITEPAGE MODULE IN THE COMMUNITYAD TABLE OR NOT
    $ismoduleads_enabled = Engine_Api::_()->getDbtable('modules', 'communityad')->ismoduleads_enabled("sitepage");
    if (!$ismoduleads_enabled) {
      return false;
    }

    //CHECK WHETHER THE AD BELONG TO THE SITEPAGE MODULE OR NOT
    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $select = $useradsTable->select();
    $select
            ->from($useradsTable->info('name'), array('userad_id'))
            ->where('resource_type = ?', "sitepage")
            ->where('resource_id = ?', $sitepage->page_id)
            ->limit(1);
    $ad_exist = $useradsTable->fetchRow($select);
    if (!empty($ad_exist)) {
      return false;
    }

    //CHECK THE CREATE LINK OR ADPREVIEW LINK YES OR NOT FROM THE ADMIN
    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adcreatelink', 1) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpreview', 1))) {
      return true;
    }
  }

  /**
   * GET PAGES ON WHICH HE HAS ACTIVTYF FEED
   * 
   * @$member User_Model_User
   * @return bool
   */
  public function getMemberFeedsForPageOfIds($member) {
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $pageids = $streamTable->select()
            ->from($streamTable->info('name'), "target_id")
            ->where('subject_id = ?', $member->getIdentity())
            ->where('subject_type = ?', $member->getType())
            ->where('target_type = ?', 'sitepage_page')
            ->group('target_id')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
    $ids = array();
    foreach ($pageids as $id) {
      $page = Engine_Api::_()->getItem('sitepage_page', $id);
      if (empty($page) || !$page->isViewableByNetwork())
        continue;
      $ids[] = $id;
    }
    return $ids;
  }

  /**
   * DELETE ACTIVITY FEED STREAM PRIVACY
   * 
   * @$member User_Model_User
   * @return bool
   */
  public function deleteFeedStream($action, $onlyCheckForNetwork = false) {
    if (empty($action))
      return;
    $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
    if (!$onlyCheckForNetwork && !empty($settingsCoreApi->sitepage_feed_type) && !empty($settingsCoreApi->sitepage_feed_onlyliked)) {
      $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
      $streamTable->delete(array(
          'action_id = ?' => $action->getIdentity(),
          'target_type NOT IN(?)' => array('sitepage_page', 'owner', 'parent')
      ));
    }

    $enableNetwork = $this->pageBaseNetworkEnable();
    $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.networkprofile.privacy', 0);
    if ($enableNetwork && $viewPricavyEnable && $action->object_type = 'sitepage_page') {
      $sitepage = $action->getObject();
      if ($sitepage->networks_privacy) {
        $pageNetworkIds = explode(",", $sitepage->networks_privacy);
        if (count($pageNetworkIds)) {
          $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
          $streamTable->delete(array(
              'action_id = ?' => $action->getIdentity(),
              'target_type IN (?)' => array('everyone', 'registered', 'network', 'members')
          ));
        }
        $target_type = 'network';
        foreach ($pageNetworkIds as $target_id) {
          $streamTable->insert(array(
              'action_id' => $action->action_id,
              'type' => $action->type,
              'target_type' => (string) $target_type,
              'target_id' => (int) $target_id,
              'subject_type' => $action->subject_type,
              'subject_id' => $action->subject_id,
              'object_type' => $action->object_type,
              'object_id' => $action->object_id,
          ));
        }
      }
    }
  }

  /**
   * DELETE ACTIVITY FEED STREAM PRIVACY WHICH ARE NOT NEED
   * 
   * @$member User_Model_User
   * @return bool
   */
  public function getPageFeedActionIds() {

    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $actionIds = $streamTable->select()
            ->from($streamTable->info('name'), "action_id")
            ->where('target_type = ?', 'sitepage_page')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

    if (!empty($actionIds)) {
      $streamTable->delete(array(
          'action_id  In(?)' => $actionIds,
          'target_type <> ?' => 'sitepage_page'
      ));
    }
  }

}

?>
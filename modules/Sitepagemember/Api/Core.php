<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Api_Core extends Core_Api_Abstract {

	public function joinLeave($resource, $params = null) {

		//GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$resource_id = $resource->getIdentity();
		
		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $resource_id);
		$owner = $resource->getOwner();
		
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
		
			if ($params == 'Join' && Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.automatically.join')) {
			
				if (empty($hasMembers)) {
				
					$membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
					$row = $membersTable->createRow();
					$row->resource_id = $resource_id;
					$row->page_id = $resource_id;
					$row->user_id = $viewer_id;
					
					if (empty($resource->member_approval)) {
						$row->active = 0;
						$row->resource_approved = 0;
						$row->user_approved = 0;
						
						//Get manage admin and send notifications to all manage admins.
						$manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($resource_id);
						
						foreach($manageadmins as $manageadmin) {
							$user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);
							Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $resource, 'sitepagemember_approve');
						}
					}
					else {
					
						//Set the request as handled for Notifaction.
						Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $resource, 'sitepage_join');

						//Add activity
						Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $resource, 'sitepage_join');
						
						//Member count increase when member join the page.
            Engine_Api::_()->sitepage()->updateMemberCount($resource);
						$resource->save();
					}
					
					//If member is already featured then automatically featured when member join the any page.
					if(!empty($hasMembers->featured)) {
						$row->featured = 1;
					}
					
					$row->save();
				}
			}
			elseif($params == 'Leave') {
			
				if (!empty($hasMembers)) {
				
					//DELETE THE RESULT FORM THE TABLE.
					Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('resource_id =?' => $resource_id, 'user_id = ?' => $viewer_id));

					//Delete activity feed of join page according to user id.					
					$action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?'  => 'sitepage_join', 'subject_id = ?' => $viewer_id, 'object_id = ?' => $resource_id));
					$action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
					$action->delete();

					//Remove the notification.
					$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($owner, $resource, 'sitepage_join');
					if($notification) {
						$notification->delete();
					}

					if (!empty($hasMembers->active)) {
					
						//Member count decrease in the page table when member leave the page.
						$resource->member_count--;
						$resource->save();
					}
				}
			}
		}
	}
	
	
	public function getMorePage($pageIds, $page_title) {
	
		$memberstable = Engine_Api::_()->getDbtable('membership', 'sitepage');
		$tableMemberName = $memberstable->info('name'); 

		$tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
		$tablePageName = $tablePage->info('name');

		$select = $tablePage->select()
											->setIntegrityCheck(false)
											->from($tablePageName, array('page_id','title','photo_id'))
											->joinleft($tableMemberName, $tablePageName . ".page_id = " . $tableMemberName . '.resource_id', null)
											->where($tablePageName . ".title LIKE ? OR " . $tablePageName . ".body LIKE ? ", '%' . $page_title . '%')
											->where($tablePageName . '.page_id NOT IN (?)', (array) $pageIds)
											->where($tablePageName . '.closed = ?', '0')
											->where($tablePageName . '.approved = ?', '1')
											->where($tablePageName . '.search = ?', '1')
											->where($tablePageName . '.declined = ?', '0')
											->where($tablePageName . '.draft = ?', '1')
											->group('page_id');
		$moePages = $tablePage->fetchAll($select);
	  return $moePages;
	}
	
	/**
   * Plugin which return the error, if Siteadmin not using correct version for the plugin.
   *
   */
  public function isModulesSupport($modName = null) {
    if( empty($modName) ) {
    $modArray = array(
      'sitepagealbum' => '4.5.0',
      'sitepagebadge' => '4.5.0',
      'sitepagedocument' => '4.5.0',
      'sitepageevent' => '4.5.0',
      'sitepagemusic' => '4.5.0',
      'sitepagenote' => '4.5.0',
      'sitepageoffer' => '4.5.0',
      'sitepagepoll' => '4.5.0',
      'sitepagereview' => '4.5.0',
      'sitepageurl' => '4.5.0',
      'sitepagevideo' => '4.5.0',
      'sitelike' => '4.5.0'
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
}
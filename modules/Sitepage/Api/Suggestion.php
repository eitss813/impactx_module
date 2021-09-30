<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Suggestion.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Api_Suggestion extends Core_Api_Abstract {

  public function deleteSuggestion($viewer_id, $entity, $entity_id, $notifications_type) {
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
          Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_id = ?' => $sugg_id['suggestion_id'], 'type = ?' => $notifications_type));
        }
      }
    }
  }

	public function isSupport() {
		$isSupport = null;
		$suggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion');
		/*
			return < 0 : when running version is lessthen 4.1.5
			return 0 : If running version is equal to 4.1.5
			return > 0 : when running version is greaterthen 4.1.5
		*/
		if( !empty($suggVersion) ) {
			$suggVersion = $suggVersion->version;
			if( !empty(Engine_Api::_()->seaocore()->checkVersion($suggVersion, '4.1.5')) ) {
				return 1;
			}
		}
		return $isSupport;
	}

	public function isModulesSupport() {
		$modArray = array(
			'suggestion' => '4.2.3',
			'sitelike' => '4.1.5',
			'communityad' => '4.1.5',
			'facebookse' => '4.1.5',
			'facebooksefeed' => '4.1.5'
		);
		$finalModules = array();
		foreach( $modArray as $key => $value ) {
			$isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
			if( !empty($isModEnabled) ) {
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

?>
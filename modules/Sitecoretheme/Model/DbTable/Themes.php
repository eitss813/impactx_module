<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Themes.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Model_DbTable_Themes extends Core_Model_DbTable_Themes
{
	public function getThemes($params = array()) {
	    $tableName = $this->info('name');
	    $select = $this->select(); 
	    if (isset($params['type'])) {
	        $select->where('type = ?', $params['type']);
	    }
	    if (isset($params['themeIdDesc']) && !empty($params['themeIdDesc'])) {
	        $select->order("theme_id DESC");
	    } 
	    return $this->fetchAll($select);
	}
}
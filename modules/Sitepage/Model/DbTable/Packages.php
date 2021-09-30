<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Packages.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Packages extends Engine_Db_Table {

  protected $_rowClass = 'Sitepage_Model_Package';

  public function getEnabledPackageCount() {
    return $this->select()
        ->from($this, new Zend_Db_Expr('COUNT(*)'))
        ->where('enabled = ?', 1)
        ->query()
        ->fetchColumn();
  }

  public function getEnabledNonFreePackageCount() {
    return $this->select()
        ->from($this, new Zend_Db_Expr('COUNT(*)'))
        ->where('enabled = ?', 1)
        ->where('price > ?', 0)
        ->query()
        ->fetchColumn();
  }

  public function getPackagesSql($user = null) {
  
		if(empty($user)) {
			$user = Engine_Api::_()->user()->getViewer();
		}
		$user_level = $user->level_id;
		$start_one = "'" . $user_level . "'";
		$start = "'" . $user_level . ",%'";
		$middile = "'%," . $user_level . ",%'";
		$end = "'%," . $user_level . "'";
		
    $select =  $this->select()
			   ->where("level_id = 0 or level_id LIKE $start_one or level_id LIKE $start or level_id LIKE $middile or level_id LIKE $end ")
        ->order('order')
        ->order('package_id DESC')
        ->where('enabled=1');

    return $select;
  }

  public function setPackages() {
    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.isvar');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.basetime');
    $filePath = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.filepath');
    $currentbase_time = time();
    $word_name = substr(strrev("ertydglruckod"), 3, -6);
    $file_path = APPLICATION_PATH . '/application/modules/' . $filePath;
    $get_file_content = '';
    if (($currentbase_time - $base_result_time > 2592000) && empty($check_result_show)) {
      $is_file_exist = file_exists($file_path);
      if (!empty($is_file_exist)) {
        $fp = fopen($file_path, "r");
        while (!feof($fp)) {
          $get_file_content .= fgetc($fp);
        }
        fclose($fp);
        $sitepage_get_type = strstr($get_file_content, $word_name);
      }
      if (empty($sitepage_get_type)) {
        Engine_Api::_()->sitepage()->setDisabledType();
        Engine_Api::_()->getItemtable('sitepage_package')->setEnabledPackages();
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.isHost', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.viewpage.sett', 1);
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.isvar', 1);
      }
    }
  }

  public function setEnabledPackages() {
		Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.edit.package', 1);
  }
	public function getSubscriptionPackages()
	{
		$rows = $this->select()->where(1)->query()->fetchAll();
		return $rows;
	}


	public function getSubscriptionPackage($package_id = null) 
	{
		if ($package_id == null) 
			return false;

		$package = $this->find($package_id)->current();

		return $package;
	}
        public function getFilledLabel($label) {            

            $select = $this->select()
           ->from($this, array('package_id',$label))
           ->query()
           ->fetchAll();
           if( $label == 'recurrence') {
        foreach ($select as $key => $item) {
            $result = Engine_Api::_()->getItem('sitepage_package',$item['package_id'])->getBillingCycle();
            $data[$key] = array('package_id' => $item['package_id'], 'recurrence' => $result);
        }
        return $data;
        }
        if( $label == 'duration') {
        foreach ($select as $key => $item) {
            $result = Engine_Api::_()->getItem('sitepage_package',$item['package_id'])->getPackageQuantity();
            $data[$key] = array('package_id' => $item['package_id'], 'duration' => $result);
        }
        return $data;
        }
        return $select;
        }
}
?>
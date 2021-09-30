<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Services.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Model_DbTable_Services extends Engine_Db_Table {
  protected $_rowClass = 'Sitecoretheme_Model_Service';

    public function getServices($params = array()) {
      $select = $this->select();

      if( isset($params['enabled']) ) {
          $select->where('enabled = ?', $params['enabled']);
      }

      $select->order("order ASC");
      return $this->fetchAll($select);
    }

}
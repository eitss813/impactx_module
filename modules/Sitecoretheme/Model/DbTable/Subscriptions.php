<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Subscriptions.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Model_DbTable_Subscriptions extends Engine_Db_Table {

    protected $_name = 'sitecoretheme_subscriptions';
    protected $_rowClass = "Sitecoretheme_Model_Subscription";

    public function isSubscribed($email) {
      $tableName = $this->info('name');
      $select = $this->select();
      $data = $select->from($tableName, 'email')
        ->where('email = ?', $email)
        ->query()
        ->fetchColumn();

      if( $data ) {
        return true;
      }

      return false;
    }

    public function getEmailList() {
      $tableName = $this->info('name');
      $select = $this->select()
        ->from($tableName, 'email');

      foreach( $select->query()->fetchAll(Zend_Db::FETCH_COLUMN, 0) as $email ) {
        $emails[] = $email;
      }
      return $emails;
    }

}
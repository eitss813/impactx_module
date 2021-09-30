<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Subscriptions.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Subscriptions extends Engine_Db_Table {

    protected $_name = 'sitevideo_subscriptions';
    protected $_rowClass = 'Sitevideo_Model_Subscription';
    protected $_categories = array();

    /*
     * THIS FUNCTION IS USED TO RETURN THE SUBSCRIPTION DETAILS IN PAGINATION 
     */

    public function getSubscriptionPaginator(array $params) {
        return Zend_Paginator::factory($this->getSubscriptionSelect($params));
    }

    /*
     * MAKE A QUERY FOR PLAYLIST TABLE ACCOURDING TO REQUESTED PARAMETER
     */

    public function getSubscriptionSelect(array $params) {


        $tableName = $this->info('name');
        $select = $this->select()->from($tableName, '*');
        $channelTable = Engine_Api::_()->getItemTable('sitevideo_channel');
        $channelTableName = $channelTable->info('name');
        if (isset($params['search']) && !empty($params['search'])) {
            $searchTable = Engine_Api::_()->getDbtable('channels', 'sitevideo')->info('name');
            $select->setIntegrityCheck(true)
                    ->joinLeft($searchTable, "$channelTableName.channel_id = $tableName.channel_id", null);
            $select->where('lower(title) = ?', strtolower($params['search']));
        }
        if (!empty($params['owner_id']))
            $select->where("$tableName.owner_id = ?", $params['owner_id']);

        if (isset($params['orderBy']) && $params['orderBy'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$tableName.creation_date");
        }
        if (isset($params['itemCountPerPage']) && !empty($params['itemCountPerPage'])) {
            $select->limit($params['itemCountPerPage']);
        }
        return $select;
    }

    public function getSubscribedUser($channel_id, $user_id = 0) {
        $select = $this->select()
                ->from($this->info('name'))
                ->where('channel_id = ?', $channel_id);
        if (!empty($user_id)) {
            $select->where('owner_id <> ?', $user_id);
        }
        return $this->fetchAll($select);
    }

    public function getIdsOfSubscriberChannels(User_Model_User $user) {
      $ids = array();
      $rows = $this->fetchAll($this->getSubscriptionSelect(array('owner_id' => $user->getIdentity())));
      foreach( $rows as $row )
      {
        $ids[] = $row->channel_id;
      }
      return $ids;
    }
}

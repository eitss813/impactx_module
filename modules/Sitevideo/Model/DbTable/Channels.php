<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Channels.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Channels extends Engine_Db_Table {

    protected $_name = 'sitevideo_channels';
    protected $_rowClass = 'Sitevideo_Model_Channel';
//    protected $_serializedColumns = array('networks_privacy');

    public function getSpecialChannel(User_Model_User $user, $type) {

        if (!in_array($type, array('wall', 'profile', 'message', 'blog', 'comment'))) {
            throw new Channel_Model_Exception('Unknown special channel type');
        }
        $select = $this->select()
                ->where('owner_type = ?', $user->getType())
                ->where('owner_id = ?', $user->getIdentity())
                ->where('type = ?', $type)
                ->order('channel_id ASC')
                ->limit(1);

        $channel = $this->fetchRow($select);

        // Create wall videos channel if it doesn't exist yet
        if (null === $channel) {
            $translate = Zend_Registry::get('Zend_Translate');

            $channel = $this->createRow();
            $channel->owner_type = 'user';
            $channel->owner_id = $user->getIdentity();
            $channel->title = $translate->_(ucfirst($type) . ' Videos');
            $channel->type = $type;

            if ($type == 'message') {
                $channel->search = 0;
            } else {
                $channel->search = 1;
            }

            $channel->save();

            // Authorizations
            if ($type != 'message') {
                $auth = Engine_Api::_()->authorization()->context;
                $auth->setAllowed($channel, 'everyone', 'view', true);
                $auth->setAllowed($channel, 'everyone', 'comment', true);
            }
        }

        return $channel;
    }

    public function getLikedChannelSelect(array $params) {

        $channelTableName = $this->info('name');
        $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
        $likesTableName = $likesTable->info('name');
        $select = $this->select()->from($channelTableName, '*');
        $select->joinLeft($likesTableName, $likesTableName . ".resource_id=" . $channelTableName . ".channel_id", null);
        $select->where('resource_type = ?', 'sitevideo_channel');
        if (!empty($params['owner_id']))
            $select->where('poster_id = ?', $params['owner_id']);
        if (isset($params['order']) && $params['order'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$likesTableName.creation_date DESC");
        }
        if (isset($params['search']) && !empty($params['search'])) {
            $select->where("lower($channelTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
        }
        return $select;
    }

    public function getLikedChannelPaginator(array $params) {
        return Zend_Paginator::factory($this->getLikedChannelSelect($params));
    }

    public function getSubscribedChannelSelect(array $params) {

        $channelTableName = $this->info('name');
        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $subscriptionsTableName = $subscriptionsTable->info('name');
        $select = $this->select()->setIntegrityCheck(false);
        if (isset($params['channel_id']) && !empty($params['channel_id'])) {
            $select->from($subscriptionsTableName, '*');
            $select->where("$subscriptionsTableName.channel_id = ?", $params['channel_id']);
        } else {
            $select->from($channelTableName, '*');
            $select->joinLeft($subscriptionsTableName, $subscriptionsTableName . ".channel_id=" . $channelTableName . ".channel_id", null);
        }

        if (!empty($params['owner_id']))
            $select->where("$subscriptionsTableName.owner_id = ?", $params['owner_id']);
        if (isset($params['order']) && $params['order'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$subscriptionsTableName.creation_date DESC");
        }
        if (isset($params['search']) && !empty($params['search'])) {
            $select->where("lower($channelTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
        }
        return $select;
    }

    public function getSubscribedChannelPaginator(array $params) {
        return Zend_Paginator::factory($this->getSubscribedChannelSelect($params));
    }

    public function getRatedChannelSelect(array $params) {
        $channelTableName = $this->info('name');
        $ratingsTable = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        $ratingsTableName = $ratingsTable->info('name');
        $select = $this->select()->from($channelTableName, '*');
        $select->joinLeft($ratingsTableName, $ratingsTableName . ".resource_id=" . $channelTableName . ".channel_id", null);
        $select->where('resource_type = ?', 'sitevideo_channel');
        if (!empty($params['owner_id']))
            $select->where('user_id = ?', $params['owner_id']);

        if (isset($params['order']) && $params['order'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$ratingsTableName.rating_id DESC");
        }
        if (isset($params['search']) && !empty($params['search'])) {
            $select->where("lower($channelTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
        }
        return $select;
    }

    public function getRatedChannelPaginator(array $params) {
        return Zend_Paginator::factory($this->getRatedChannelSelect($params));
    }

    /**
     * Get total channels of particular category / subcategoty 
     *
     * @param array $params
     * @return int $totalChannels;
     */
    public function getChannelsCount($params = array()) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'));

        if (isset($params['foruser']) && !empty($params['foruser'])) {
            $select->where('search = ?', 1);
        }

        if (!empty($params['columnName']) && !empty($params['category_id'])) {
            $column_name = $params['columnName'];
            $select->where("$column_name = ?", $params['category_id']);
        }

        $totalChannels = $select->query()->fetchColumn();

        //RETURN CHANNEL COUNT
        return $totalChannels;
    }

    /**
     * Get channels of current viewing user
     * @param obj $user
     * @param array $params
     * @return string $select;
     */
    public function getUserChannels($user, $params = array()) {

        $select = $this->select()->where("owner_type = ?", "user")->where("owner_id = ?", $user->user_id)->where('search = ?', 1)->order('channel_id DESC');

        if (!empty($params['category_id']) && is_numeric($params['category_id'])) {
            $select->where('category_id = ?', $params['category_id']);
        }

        if (!empty($params['subcategory_id']) && is_numeric($params['subcategory_id'])) {
            $select->where('subcategory_id = ?', $params['subcategory_id']);
        }

        if (!empty($params['subsubcategory_id']) && is_numeric($params['subsubcategory_id'])) {
            $select->where('subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (!isset($params['defaultChannelsShow']) && empty($params['defaultChannelsShow']) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.specialchannel', 0))
            $select->where('type IS NULL');

        $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));

        if (isset($params['fetchAll']) && !empty($params['fetchAll'])) {
            return $this->fetchAll($select);
        } else {
            return $select;
        }
    }

    public function getChannelPaginator($params = array(), $customParams = array()) {
        return Zend_Paginator::factory($this->getChannelSelect($params, $customParams));
    }

    public function getChannel($params) {
        $select = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelSelect($params);
        return $this->fetchAll($select);
    }

    /**
     * Get channel select query
     *
     * @param array $params
     * @param array $customParams
     * @return string $select;
     */
    public function getChannelSelect($params = array(), $customParams = null) {

        //GET CHANNEL TABLE
        $channelTableName = $this->info('name');
        $select = $this->select();
        $select->from($channelTableName, '*');
        if (!empty($params['owner_id']) && isset($params['owner_id'])) {
            $select->where($channelTableName . '.owner_id = ?', $params['owner_id']);
        }
        if (isset($customParams)) {
            //GET SEARCH TABLE
            $searchTable = Engine_Api::_()->fields()->getTable('sitevideo_channel', 'search')->info('name');
            //PROCESS OPTIONS
            $tmp = array();
            foreach ($customParams as $k => $v) {
                if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                    continue;
                } else if (false !== strpos($k, '_field_')) {
                    list($null, $field) = explode('_field_', $k);
                    $tmp['field_' . $field] = $v;
                } else if (false !== strpos($k, '_alias_')) {
                    list($null, $alias) = explode('_alias_', $k);
                    $tmp[$alias] = $v;
                } else {
                    $tmp[$k] = $v;
                }
            }
            $customParams = $tmp;

            $select = $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($searchTable, "$searchTable.item_id = $channelTableName.channel_id", null);

            $searchParts = Engine_Api::_()->fields()->getSearchQuery('sitevideo_channel', $customParams);
            foreach ($searchParts as $k => $v) {
                $select->where("`{$searchTable}`.{$k}", $v);
            }
        }
        if (!empty($params['category_id'])) {
            $select->where($channelTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($channelTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (!empty($params['subsubcategory_id'])) {
            $select->where($channelTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (!empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($channelTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }

        if (empty($params['users']) && isset($params['view_view']) && $params['view_view'] == '1') {
            $select->where($channelTableName . '.owner_id = ?', '0');
        }
        if (isset($params['filter']) && !empty($params['filter'])) {
            switch ($params['filter']) {
                case "subscribe_count":
                    $select->where($channelTableName . '.subscribe_count>0');
                    break;
                case "comment_count":
                    $select->where($channelTableName . '.comment_count>0');
                    break;
                case 'like_count':
                    $select->where($channelTableName . '.like_count>0');
                    break;
                case 'rating':
                    $select->where($channelTableName . '.rating >0');
                    break;
                case 'favourite_count' :
                    $select->where($channelTableName . '.favourite_count >0');
                    break;

                case 'featured' :
                    $select->where($channelTableName . '.featured = 1');
                    break;
            }
        }
        if (!isset($params['orderby']) || empty($params['orderby'])) {
            $select->order($channelTableName . '.creation_date DESC');
        }
        if (isset($params['orderby']) && !empty($params['orderby'])) {
            switch ($params['orderby']) {
                case "creation_date":
                    $select->order($channelTableName . '.creation_date DESC');
                    break;
                case "creationDateAsc":
                    $select->order($channelTableName . '.creation_date ASC');
                    break;
                case "modified_date":
                    $select->order($channelTableName . '.modified_date DESC');
                    break;
                case "view_count":
                    $select->order($channelTableName . '.view_count DESC');
                    break;
                case "comment_count":
                    $select->order($channelTableName . '.comment_count DESC');
                    break;
                case 'like_count':
                    $select->order($channelTableName . '.like_count  DESC');
                    break;
                case 'videos_count':
                    $select->order($channelTableName . '.videos_count  DESC');
                    break;
                case 'rating':
                    $select->order($channelTableName . '.rating  DESC');
                    break;
                case 'favourite_count':
                    $select->order($channelTableName . '.favourite_count  DESC');
                    break;
                case 'subscribe_count':
                    $select->order($channelTableName . '.subscribe_count  DESC');
                    break;
                case 'title':
                    $select->order($channelTableName . '.title ASC');
                    break;
                case 'title_reverse':
                    $select->order($channelTableName . '.title DESC');
                    break;
                case 'featured':
                    $select->order($channelTableName . '.featured DESC');
                    $select->order($channelTableName . '.creation_date ASC');
                    break;
                case 'sponsored':
                    $select->order($channelTableName . '.sponsored DESC');
                    $select->order($channelTableName . '.creation_date ASC');
                    break;
                case 'sponsoredFeatured':
                    $select->order($channelTableName . '.sponsored DESC');
                    $select->order($channelTableName . '.featured DESC');
                    $select->order($channelTableName . '.creation_date ASC');
                    break;
                case 'featuredSponsored':
                    $select->order($channelTableName . '.featured DESC');
                    $select->order($channelTableName . '.sponsored DESC');
                    $select->order($channelTableName . '.creation_date ASC');
                    break;
                case 'random' :
                    $select->order('RAND()');
                    break;
                default:
                    $select->order($channelTableName . '.modified_date DESC');
                    break;
            }
        }
        //GET TAGMAP TABLE NAME
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
        $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
        $isTagIdSearch = false;
        if (isset($params['tag_id']) && !empty($params['tag_id'])) {
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $channelTableName.channel_id", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'))
                    ->where($tagMapTableName . '.resource_type = ?', 'sitevideo_channel')
                    ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
            $isTagIdSearch = true;
        }
        if (isset($params['search']) && !empty($params['search'])) {
            if ($isTagIdSearch == false) {
                $select
                        ->setIntegrityCheck(false)
                        ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $channelTableName.channel_id and " . $tagMapTableName . ".resource_type = 'sitevideo_channel'", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'));
            }
            $select->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id", array());
            $select->where("lower($channelTableName.title) LIKE ? OR lower($channelTableName.description) LIKE ? OR lower($tagName.text) LIKE ? ", '%' . strtolower($params['search']) . '%');
            $select->group("$channelTableName.channel_id");
        }
        if (isset($params['selectLimit']) && !empty($params['selectLimit'])) {
            $select->limit($params['selectLimit']);
        }

        //START NETWORK WORK
        $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        //END NETWORK WORK
        return $select;
    }

    public function getFavouriteChannelSelect(array $params) {

        $channelTableName = $this->info('name');
        $favouritesTable = Engine_Api::_()->getDbtable('favourites', 'seaocore');
        $favouritesTableName = $favouritesTable->info('name');
        $select = $this->select()->from($channelTableName, '*');
        $select->joinLeft($favouritesTableName, $favouritesTableName . ".resource_id=" . $channelTableName . ".channel_id", null);
        $select->where('resource_type = ?', 'sitevideo_channel');
        if (!empty($params['owner_id']))
            $select->where('poster_id = ?', $params['owner_id']);
        if (isset($params['order']) && $params['order'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$favouritesTableName.creation_date DESC");
        }
        if (isset($params['search']) && !empty($params['search'])) {
            $select->where("lower($channelTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
        }
        return $select;
    }

    public function getFavouriteChannelPaginator(array $params) {
        return Zend_Paginator::factory($this->getFavouriteChannelSelect($params));
    }

    /**
     * Get paginator of channels
     *
     * @param array $params
     * @return Zend_Paginator;
     */
    Public function channelBySettings($params = array()) {

        $channelTableName = $this->info('name');
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($channelTableName)
                ->where($channelTableName . '.search = ?', true);
        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $interval = '';
        if (isset($params['interval']) && !empty($params['interval'])) {
            $interval = $params['interval'];
            $current_time = date("Y-m-d H:i:s");
            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }
        }
        if (isset($params['channel_ids']) && !empty($params['channel_ids'])) {
            $select->where($channelTableName . '.channel_id IN(?)', $params['channel_ids']);
        }
        if (!empty($params['category_id'])) {
            $select->where($channelTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($channelTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (!empty($params['subsubcategory_id'])) {
            $select->where($channelTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (!empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($channelTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }

        if (empty($params['users']) && isset($params['view_view']) && $params['view_view'] == '1') {
            $select->where($channelTableName . '.owner_id = ?', '0');
        }

        if (isset($params['orderby']) && !empty($params['orderby'])) {
            switch ($params['orderby']) {

                case 'creation_date':
                    $select->order($channelTableName . '.creation_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($channelTableName . "$sqlTimeStr");
                    }
                    break;
                case 'modified_date':
                    $select->order($channelTableName . '.modified_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($channelTableName . "$sqlTimeStr");
                    }
                    break;
                case 'view_count':
                    $select->order($channelTableName . '.view_count DESC');
                    break;
                case 'like_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                        $popularityTableName = $popularityTable->info('name');
                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $channelTableName . '.channel_id', array("COUNT($channelTableName.channel_id) as total_count"))
                                ->where($popularityTableName . '.resource_type = ?', 'sitevideo_channel')
                                ->order("total_count DESC");

                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($channelTableName . '.like_count DESC');
                    }
                    break;
                case 'comment_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                        $popularityTableName = $popularityTable->info('name');

                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $channelTableName . '.channel_id', array("COUNT($channelTableName.channel_id) as total_count"))
                                ->where($popularityTableName . '.resource_type = ?', 'sitevideo_channel')
                                ->order("total_count DESC");
                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($channelTableName . '.comment_count DESC');
                    }
                    break;
                case 'rating':
                    $select->order($channelTableName . '.rating DESC');
                    break;
                case 'subscribe_count' :
                    $select->order($channelTableName . '.subscribe_count DESC');
                    break;
                case 'favourite_count' :
                    $select->order($channelTableName . '.favourite_count DESC');
                    break;
                case 'random':
                    $select->order('Rand()');
                    break;
            }
        }
        if (isset($params['orderby']) && !empty($params['orderby']) && $params['orderby'] != 'creation_date' && $params['orderby'] != 'modified_date' && $params['orderby'] != 'random') {
            $select->order($channelTableName . ".channel_id DESC");
        }
        if (isset($params['showChannel']) && !empty($params['showChannel'])) {
            switch ($params['showChannel']) {
                case 'featured' :
                    $select->where("$channelTableName.featured = ?", 1);
                    break;
                case 'sponsored' :
                    $select->where($channelTableName . '.sponsored = ?', '1');
                    break;
                case 'featuredSponsored' :
                    $select->where("$channelTableName.sponsored = 1 OR $channelTableName.featured = 1");
                    break;
            }
        }

        //Start Network work
        $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
        //End Network work

        $select->group("$channelTableName.channel_id");

        if (isset($params['limit']) && !empty($params['limit'])) {
            $limit = $params['limit'];
        }
        if (isset($params['start_index']) && $params['start_index'] >= 0) {
            $select = $select->limit($limit, $params['start_index']);
            return $this->fetchAll($select);
        }
        return Zend_Paginator::factory($select);
    }

    /**
     * Get pages to add as item of the day
     * @param string $title : search text
     * @param int $limit : result limit
     */
    public function getDayItems($title, $limit = 10) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('channel_id', 'owner_id', 'title', 'channel_url', 'file_id'))
                ->where('lower(title)  LIKE ? ', '%' . strtolower($title) . '%')
                ->where('search = ?', '1')
                ->order('title ASC')
                ->limit($limit);

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    /**
     * Return channelids which have this category and this mapping
     *
     * @param int category_id
     * @return array $channelIds
     */
    public function getMappedSitevideo($category_id) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($category_id)) {
            return null;
        }

        //MAKE QUERY
        $channelIds = $this->select()
                ->from($this->info('name'), 'channel_id')
                ->where("category_id = $category_id OR subcategory_id = $category_id OR subsubcategory_id = $category_id")
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        return $channelIds;
    }

    /**
     * Return channels which have this category and this mapping
     *
     * @param int category_id
     * @return Zend_Db_Table_Select
     */
    public function getCategoryList($params = array()) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($params['category_id'])) {
            return null;
        }

        //MAKE QUERY
        $categoty_type = $params['categoty_type'];
        return $this->select()
                        ->from($this->info('name'), 'channel_id')
                        ->where("$categoty_type = ?", $params['category_id'])
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    /**
     * Get Popular location base on city and state
     *
     */
    public function getPopularLocation($params = null) {

        //GET SITEVIDEO TABLE NAME
        $sitevideoTableName = $this->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
        $locationTableName = $locationTable->info('name');

        //MAKE QUERY
        $seaolocationIds = $this->select()
                ->setIntegrityCheck(false)
                ->from($sitevideoTableName, array("seao_locationid"))
                ->where($sitevideoTableName . '.search = ?', '1')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        if (empty($seaolocationIds)) {
            return;
        }

        $lselect = $locationTable->select()
                ->setIntegrityCheck(false)
                ->from($locationTableName, array("city", "count(city) as count_location", "state", "count(state) as count_location_state"))
                ->where("$locationTableName.locationitem_id IN (?)", $seaolocationIds)
                ->group("city")
                ->group("state")
                ->order("count_location DESC");

        if (isset($params['limit']) && !empty($params['limit'])) {
            $lselect->limit($params['limit']);
        }

        //RETURN RESULTS
        return $locationTable->fetchAll($lselect);
    }

    public function addPrivacyChannelsSQl($select, $tableName = null) {

        $privacybase = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.privacybase', 0);

        if (empty($privacybase))
            return $select;

        $column = $tableName ? "$tableName.channel_id" : "channel_id";

        return $select->where("$column IN(?)", $this->getOnlyViewableChannelsId());
    }

    public function getOnlyViewableChannelsId() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $channels_ids = array();
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheName = 'channel_ids_user_id_' . $viewer->getIdentity();

        $data = APPLICATION_ENV == 'development' ? ( Zend_Registry::isRegistered($cacheName) ? Zend_Registry::get($cacheName) : null ) : $cache->load($cacheName);
        if ($data && is_array($data)) {
            $channels_ids = $data;
        } else {
            set_time_limit(0);
            $table = Engine_Api::_()->getItemTable('sitevideo_channel');
            $channel_select = $table->select()
                    ->where('search = ?', true)
                    ->order('channel_id DESC');

            // Create new array filtering out private channels
            $i = 0;
            foreach ($channel_select->getTable()->fetchAll($channel_select) as $channel) {
                if ($channel->isOwner($viewer) || Engine_Api::_()->authorization()->isAllowed($channel, $viewer, 'view')) {
                    $channels_ids[$i++] = $channel->channel_id;
                }
            }

            // Try to save to cache
            if (empty($channels_ids))
                $channels_ids = array(0);

            if (APPLICATION_ENV == 'development') {
                Zend_Registry::set($cacheName, $channels_ids);
            } else {
                $cache->save($channels_ids, $cacheName);
            }
        }

        return $channels_ids;
    }

    public function getNetworkBaseSql($select, $params = array()) {
        $select = $this->addPrivacyChannelsSQl($select, $this->info('name'));
        if (empty($select))
            return;

        $sitevideo_tableName = $this->info('name');

        //START NETWORK WORK
        $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.network', 0);

        if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {

            $viewer = Engine_Api::_()->user()->getViewer();
            $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
            if (!Zend_Registry::isRegistered('viewerNetworksIdsChannel')) {
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                Zend_Registry::set('viewerNetworksIdsChannel', $viewerNetworkIds);
            } else {
                $viewerNetworkIds = Zend_Registry::get('viewerNetworksIdsChannel');
            }
            if (!Engine_Api::_()->sitevideo()->channelBaseNetworkEnable()) {
                $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

                if (!empty($viewerNetwork)) {
                    if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
                        $select->setIntegrityCheck(false)
                                ->from($sitevideo_tableName);
                    }
                    $networkMembershipName = $networkMembershipTable->info('name');
                    $select
                            ->join($networkMembershipName, "`{$sitevideo_tableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                            ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                            ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
                    if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
                        $select->group($sitevideo_tableName . ".channel_id");
                    }
                    if (isset($params['extension_group']) && !empty($params['extension_group'])) {
                        $select->group($params['extension_group']);
                    }
                }
            } else {
                $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);
                $str = array();
                $columnName = "`{$sitevideo_tableName}`.networks_privacy";
                foreach ($viewerNetwork as $networkvalue) {
                    $network_id = $networkvalue->resource_id;
                    $str[] = "'" . $network_id . "'";
                    $str[] = "'" . $network_id . ",%'";
                    $str[] = "'%," . $network_id . ",%'";
                    $str[] = "'%," . $network_id . "'";
                }
                if (!empty($str)) {
                    $likeNetworkVale = (string) ( join(" or $columnName  LIKE ", $str) );
                    $select->where($columnName . ' LIKE ' . $likeNetworkVale . ' or ' . $columnName . " IS NULL");
                } else {
                    $select->where($columnName . " IS NULL");
                }
            }
            //END NETWORK WORK
        }

        return $select;
    }

    public function createDefaultChannels() {
        $db = Engine_Db_Table::getDefaultAdapter();
        $viewer = Engine_Api::_()->user()->getViewer();
        $ownerId = $viewer->getIdentity();
        $ownerType = $viewer->getType();
        $channel_category_table = Engine_Api::_()->getItemTable('sitevideo_channel_category');
        $category = $channel_category_table->fetchRow(array('category_name=?' => 'Others'));
        $categoryId = 0;
        if ($category)
            $categoryId = $category->category_id;

        $channels_table = Engine_Api::_()->getDbTable('channels', 'sitevideo');
        $db->query("
                    INSERT IGNORE INTO `engine4_sitevideo_channels`(`title`, `channel_url`, `description`, `owner_type`, `owner_id`, `category_id`,`creation_date`, `modified_date`, `videos_count`, `search`, `featured`,`sponsored`)
                    VALUES
                    ('Motors TV','Motors-TV','Motors TV is a British digital television channel dedicated to motorsport. Launched in 2000, it broadcasts an extensive range of national and international racing.','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1),
                    ('Comedy Central','comedy-central','Comedy Central is an American basic cable and satellite television channel that is owned by Viacom Music and Entertainment Group, a unit of the Viacom Media Networks division of Viacom. Aimed limitedly to mature audience, age 18 to up, the channel carries comedy programming, in the form of both original and syndicated series and stand-up comedy specials, as well as feature films.','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1),
                    ('Poker','poker','The World Series of Poker is the Super Bowl of poker. It consists of over 55 events.','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1),
                    ('NatGeoWild','nat-geo-wild','Nat Geo Wild is the network all about animals from National Geographic, where every story is an adventure and your imagination is allowed to run wild.','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1),
                    ('DIY Network','diy-network','DIY Network is a channel that focuses on do it yourself projects at home.','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1),
                    ('Health Flavors','health-flavors','Health Flavors is a channel whose primary focus is on health and nutrition.','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1),
                    ('HGTV','hgtv','HGTV broadcasts a variety of how-to shows with a focus on home improvement, gardening, craft and remodeling. ','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1),
                    ('Ovation','ovation','Ovation is a network whose stated mission is to connect the world to all forms of art and artistic expression.','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1),
                    ('Cameras and Techniques','cameras-and-techniques','While it can take years to master the camera techniques you need to take amazing images, whatever your skill level and whatever you choose to shoot, it often pays to keep things simple.','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1),
                    ('Nature','nature','\"Nature\" can refer to the phenomena of the physical world, and also to life in general. The study of nature is a large part of science. Although humans are part of nature, human activity is often understood as a separate category from other natural phenomena.','$ownerType',$ownerId,$categoryId,now(),now(),1,1,1,1)
                ");
        $select = $channels_table->select()
                ->from($channels_table->info('name'), 'channel_id');

        $select->order('channel_id ASC')
                ->limit(10);
        $channels = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        if (!empty($channels)) {
            $db->query("INSERT IGNORE INTO `engine4_sitevideo_otherinfo` (`channel_id`, `tagline1`, `tagline2`, `tagline_description`, `url`) VALUES
('$channels[0]','Launched    : 1 September 2000', 'Owned by: Motors TV S.A.', 'Motors TV is a British digital television channel dedicated to motorsport. Launched in 2000, it broadcasts an extensive range of national and international racing.', NULL),
( '$channels[1]','Launched    : April 1, 1991', 'Headquarters: 345 Hudson Street New York City', 'Comedy Central is an American basic cable and satellite television channel that is owned by Viacom Music and Entertainment Group, a unit of the Viacom Media Networks division of Viacom. Aimed limitedly to mature audience, age 18 to up, the channel carries comedy programming, in the form of both original and syndicated series and stand-up comedy specials, as well as feature films. ', NULL),
( '$channels[2]','The game was played in New Orleans, Louisiana in 1829', 'Spread of the game done by Mississippi riverboats', 'The World Series of Poker is the Super Bowl of poker. It consists of over 55 events.', NULL),
('$channels[3]','Launched: August 21, 2006 ', 'The world first bilingual wildlife service', 'DIY Network is a channel that focuses on do it yourself projects at home.', NULL),
('$channels[4]',' Launched: January 1, 1999', 'Available to approximately 60,942,000 pay television households', 'Health Flavors is a channel whose primary focus is on health and nutrition.', NULL),
('$channels[5]','Launched on August 2, 1999', 'A joint venture between Canadian media giant CW Media and Discovery Communications Inc', 'HGTV broadcasts a variety of how-to shows with a focus on home improvement, gardening, craft and remodeling. ', NULL),
('$channels[6]','Owned by Scripps Networks Interactive', 'Headquartered in Knoxville, Tennessee', 'It broadcasts a variety of how-to shows with a focus on home improvement, gardening, craft and remodeling.', NULL),
( '$channels[7]','Premiered on April 21, 1996', 'Re-launched on June 20, 2007', 'Ovation is a network whose stated mission is to connect the world to all forms of art and artistic expression.', NULL),
('$channels[8]','By Daniel Reed', 'Created on: 18 Feb 2016', 'While it can take years to master the camera techniques you need to take amazing images, whatever your skill level and whatever you choose to shoot, it often pays to keep things simple. ', NULL),
('$channels[9]','By Lisa Carter', 'Created on: Feb 2016', 'Nature can refer to the phenomena of the physical world, and also to life in general. The study of nature is a large part of science. Although humans are part of nature, human activity is often understood as a separate category from other natural phenomena.', NULL);");
        }
        $toPath = "/temporary/channel_main_images";
        $fromPath = "/application/modules/Sitevideo/externals/images/channel_images";

        $channels_table = Engine_Api::_()->getDbTable('channels', 'sitevideo');
        $select = $channels_table->select()
                ->from($channels_table->info('name'), 'channel_id')
                ->limit(10);
        $channels = $select->query()->fetchAll();
        foreach ($channels as $channel) {
            Engine_Api::_()->sitevideo()->autoLike($channel['channel_id'], 'sitevideo_channel');
        }
        // set default images and set authorization for view , comment ,topic
        $this->setDefaultImages($toPath, $fromPath);
    }

    public function setDefaultImages($toPath, $fromPath) {

        @mkdir(APPLICATION_PATH . $toPath, 0777);
        $dir = APPLICATION_PATH . $fromPath;
        $public_dir = APPLICATION_PATH . $toPath;
        $fieArr = array();
        if (is_dir($dir) && is_dir($public_dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (strstr($file, '.png') || strstr($file, '.jpg') || strstr($file, '.jpeg')) {
                    $fieArr[] = $file;
                    @copy(APPLICATION_PATH . "$fromPath/$file", APPLICATION_PATH . "$toPath/$file");
                }
            }
            @chmod(APPLICATION_PATH . $toPath, 0777);
        }
        //MAKE QUERY
        $select = $this->select()->from($this->info('name'), '*');
        $channels = $this->fetchAll($select);
        //UPLOAD DEFAULT ICONS
        foreach ($channels as $channel) {

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $values = array();
            $values['auth_view'] = 'everyone';
            $values['auth_comment'] = 'everyone';
            $values['auth_topic'] = 'everyone';
            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $topicMax = array_search($values['auth_topic'], $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($channel, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($channel, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($channel, $role, 'topic', ($i <= $topicMax));
            }

            $channelUrl = $channel->channel_url;
            $iconName = false;
            foreach ($fieArr as $f) {
                if (strstr($f, $channelUrl)) {
                    $iconName = $f;
                    break;
                }
            }
            if ($iconName == false) {
                continue;
            }
            @chmod(APPLICATION_PATH . $toPath, 0777);
            $file = array();

            $file['tmp_name'] = APPLICATION_PATH . "$toPath/$iconName";
            $channel->setPhoto($file['tmp_name']);
            $channel->save();
        }

        //REMOVE THE CREATED PUBLIC DIRECTORY
        if (is_dir(APPLICATION_PATH . $toPath)) {
            $files = scandir(APPLICATION_PATH . $toPath);
            foreach ($files as $file) {
                $is_exist = file_exists(APPLICATION_PATH . "$toPath/$file");
                if ($is_exist) {
                    @unlink(APPLICATION_PATH . "$toPath/$file");
                }
            }
            @rmdir(APPLICATION_PATH . $toPath);
        }
    }
}

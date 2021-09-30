<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Likes.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

class Core_Model_DbTable_Shorturls extends Engine_Db_Table
{

    protected $_rowClass = 'Core_Model_Shorturl';

    protected $_custom = false;

    public function __construct($config = array())
    {
        if (get_class($this) !== 'Core_Model_DbTable_Shorturls') {
            $this->_custom = true;
        }

        parent::__construct($config);
    }

    public function addShorturl(Core_Model_Item_Abstract $resource,$shorturl)
    {
        $row = $this->getShorturl($resource);
        if (null !== $row) {
            throw new Core_Model_Exception('Already Added');
        }

        $row = $this->createRow();

        if (isset($row->resource_type)) {
            $row->resource_type = $resource->getType();
        }
        $row->resource_id = $resource->getIdentity();
        $row->link = $shorturl;
        $row->save();

        return $row;
    }

    public function isShorturlPresent(Core_Model_Item_Abstract $resource)
    {
        return (null !== $this->getShorturl($resource));
    }

    public function getShorturl(Core_Model_Item_Abstract $resource)
    {

        $select = $this->select();
        $select
            ->where('resource_type = ?', $resource->getType())
            ->where('resource_id = ?', $resource->getIdentity());

        return $this->fetchRow($select);

    }

}
<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Blocks.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Model_DbTable_Blocks extends Engine_Db_Table
{

  protected $_serializedColumns = array('params');
  protected $_rowClass = "Sitecoretheme_Model_Block";

  
  public function getBlocksPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getBlocksSelect());
    if( !empty($params['page']) ) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) ) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  
  public function getBlocksSelect()
  {
    return $this->select();
  }

  public function getBlock($id)
  {
    $select = $this->getBlocksSelect()->where('block_id = ?', $id);
    return $this->fetchRow($select);
  }

}
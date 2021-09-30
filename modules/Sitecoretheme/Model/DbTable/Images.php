<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Images.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Model_DbTable_Images extends Engine_Db_Table {

    protected $_name = 'sitecoretheme_images';
    protected $_rowClass = "Sitecoretheme_Model_Image";

    public function getImages($params = array(), $columns = array()) {
        $tableName = $this->info('name');
        $select = $this->select();

        if (!empty($columns))
            $select->from($tableName, $columns);

        if (isset($params['enabled'])) {
            $select->where('enabled = ?', $params['enabled']);
        }

        if (isset($params['selectedImages'])) {
            $select->where('image_id' . ' in (?)', new Zend_Db_Expr(trim(implode(',', $params['selectedImages']), ',')));
        }

        $select->order("order ASC");

        return $this->fetchAll($select);
    }

    public function getTitleMatch($title) {
        $tableName = $this->info('name');
        $select = $this->select();
        $title = $select->from($tableName, 'title')->where('title = ?', $title)->query()->fetchColumn();

        if ($title) {
            return $title;
        }

        return false;
    }

}
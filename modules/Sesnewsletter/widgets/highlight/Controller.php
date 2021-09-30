<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Widget_HighlightController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

        $this->view->title = $this->_getParam('title', '');
        $this->getElement()->removeDecorator('Title');
        $this->view->module = $module = $this->_getParam('module', '');

        $this->view->bgcolor = $this->_getParam('bgcolor', '#f2f2f2');
        $this->view->headingfontsize = $this->_getParam('headingfontsize', '20');
        $this->view->headingbordercolor = $this->_getParam('headingbordercolor', '#ff0000');
        $this->view->headingtextcolor = $this->_getParam('headingtextcolor', '#FFF');
        $this->view->titlefontsize = $this->_getParam('titlefontsize', '13');
        $this->view->titlebgcolor = $this->_getParam('titlebgcolor', '#ff0000');
        $this->view->titletextcolor = $this->_getParam('titletextcolor', '#ff0000');

        $limit = $this->_getParam('limit', 6);
        $popularitycriteria = $this->_getParam('popularitycriteria', 'creation_date');

        if(!$this->view->module)
            $this->setNoRender();

        $table = Engine_Api::_()->getItemTable($module);
        $tableName = $table->info('name');
        $select = $table->select()->from($tableName)->limit($limit);

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $popularitycriteria_exist = $db->query("SHOW COLUMNS FROM ".$tableName." LIKE '".$popularitycriteria."'")->fetch();
        
        $creation_date = $db->query("SHOW COLUMNS FROM ".$tableName." LIKE 'creation_date'")->fetch();
        
        $starttime = $db->query("SHOW COLUMNS FROM ".$tableName." LIKE 'starttime'")->fetch();

        if (!empty($popularitycriteria_exist)) {
            $select->order("$popularitycriteria DESC");
        } else if(!empty($creation_date)) {
            $select->order('creation_date DESC');
        } else if(!empty($starttime)) {
            $select->order('starttime DESC');
        }

        $this->view->result = $result = $table->fetchAll($select);
        if(count($result) == 0)
            $this->setNoRender();
  }
}

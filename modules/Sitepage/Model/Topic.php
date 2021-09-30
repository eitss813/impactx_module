<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topic.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_Topic extends Core_Model_Item_Abstract {

  protected $_parent_type = 'sitepage_page';
  protected $_owner_type = 'user';
  protected $_children_types = array('sitepage_post');

  /**
   * Gets an absolute URL to the album to view this item
   *
   * @param array $params 
   * @return string
   */
  public function getHref($params = array()) {

    $tab_id='';
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.sitemobile-discussion-sitepage', $this->page_id, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.discussion-sitepage', $this->page_id, $layout);
		}

    $params = array_merge(array(
        'route' => 'sitepage_extended',
        'controller' => 'topic',
        'action' => 'view',
        'page_id' => $this->page_id,
        'topic_id' => $this->getIdentity(),
        'tab' => $tab_id,
            ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getDescription()
  {
    if( !isset($this->store()->firstPost) && $this->getIdentity() && $this->page_id) {
      $postTable = Engine_Api::_()->getDbtable('posts', 'sitepage');
      $postSelect = $postTable->select()
        ->where('topic_id = ?', $this->getIdentity())
        ->where('page_id = ?', $this->page_id)
        ->order('post_id ASC')
        ->limit(1);
      $this->store()->firstPost = $postTable->fetchRow($postSelect);
    }
    if( isset($this->store()->firstPost) ) {
      // strip HTML and BBcode
      $content = $this->store()->firstPost->body;
      $content = strip_tags($content);
      $content = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $content);
      return $content;
    }
    return '';
  }

  public function getBody() {
    if( !isset($this->store()->firstPost) ) {
      $postTable = Engine_Api::_()->getDbtable('posts', 'sitepage');
      $postSelect = $postTable->select()
        ->where('topic_id = ?', $this->getIdentity())
        ->where('page_id = ?', $this->page_id)
        ->order('post_id ASC')
        ->limit(1);
      $this->store()->firstPost = $postTable->fetchRow($postSelect);
    }
    if( isset($this->store()->firstPost) ) {
      // strip HTML and BBcode
      $length = 200;
      $content = $this->store()->firstPost->body;
      return Engine_String::strlen($content) > $length ? Engine_String::substr($content, 0, ($length - 3)) . '...' : $content;
    }
    return '';
  }

  /**
   * Gets sitepage item
   *
   * @return sitepage item
   * */
  public function getParentSitepage() {

    return Engine_Api::_()->getItem('sitepage_page', $this->page_id);
  }

  public function getResource() {
    if ($this->resource_type && $this->resource_id)
      return Engine_Api::_()->getItem($this->resource_type, $this->resource_id);
  }

  /**
   * Gets first post
   *
   * @return first post
   * */
  public function getFirstPost() {

    return Engine_Api::_()->getDbtable('posts', 'sitepage')->fetchRow(Engine_Api::_()->getDbtable('posts', 'sitepage')->select()
                            ->where('topic_id = ?', $this->getIdentity())
                            ->order('post_id ASC')
                            ->limit(1));
  }

  /**
   * Gets last post
   *
   * @return last post
   * */
  public function getLastPost() {

    return Engine_Api::_()->getItemTable('sitepage_post')->fetchRow(Engine_Api::_()->getItemTable('sitepage_post')->select()
                            ->where('topic_id = ?', $this->getIdentity())
                            ->order('post_id DESC')
                            ->limit(1));
  }

  /**
   * Gets last poster information
   *
   * @return last poster item
   * */
  public function getLastPoster() {

    return Engine_Api::_()->getItem('user', $this->lastposter_id);
  }

  /**
   * Inserts topic
   * */
  protected function _insert() {

    if ($this->_disableHooks)
      return;

    if (!$this->page_id) {
      $error_msg = Zend_Registry::get('Zend_Translate')->_('Cannot create topic without page_id');
      throw new Exception($error_msg);
    }

    parent::_insert();
  }

  /**
   * Delete posts
   * */
  protected function _delete() {

    if ($this->_disableHooks)
      return;

    $postTable = Engine_Api::_()->getItemTable('sitepage_post');
    $postSelect = $postTable->select()->where('topic_id = ?', $this->getIdentity());
    foreach ($postTable->fetchAll($postSelect) as $sitepagePost) {
      $sitepagePost->disableHooks()->delete();
    }

    // Delete create activity feed of note before delete note 
    Engine_Api::_()->getApi('subCore', 'sitepage')->deleteCreateActivityOfExtensionsItem($this, array('sitepage_topic_create', 'sitepage_admin_topic_create'));

    parent::_delete();
  }

}

?>
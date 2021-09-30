<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MakeAlbumCover.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_MoveToOtherAlbum extends Engine_Form {

  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    $this
            ->setTitle('Move photo to another album?')
            ->setDescription('')
    ;

    // Get albums
    $albumTable = Engine_Api::_()->getItemTable('album');
    $myAlbums = $albumTable->select()
            ->from($albumTable, array('album_id', 'title', 'type'))
            ->where('owner_type = ?', 'user')
            ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
            ->query()
            ->fetchAll();

    $albumOptions = array('' => '');
    foreach ($myAlbums as $myAlbum) {
      if ($this->_item->getIdentity() == $myAlbum['album_id'] || ($myAlbum['type'] != null))
        continue;
      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
    }
    if (count($albumOptions) == 1) {
      $albumOptions = array();
    }

    $this->addElement('Select', 'move', array(
        'label' => 'Select another album for this photo:',
        'MultiOptions' => $albumOptions,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Move Photo',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $this->getDisplayGroup('buttons');
  }

}
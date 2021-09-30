<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Album.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Album extends Engine_Form
{
  public function init()
  {
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $user = Engine_Api::_()->user()->getViewer();
    // Init form
    $this
      ->setTitle('Add New Photos')
      ->setDescription('Choose photos on your computer to add to this album.')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('name', 'albums_create')
      ->setAttrib('enctype','multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    // Init album
    $blogId = Zend_Controller_Front::getInstance()->getRequest()->getParam('blog_id', null);
		$albumId = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id', null);
		if($albumId){
			$blog =  Engine_Api::_()->getItem('sesblog_album', $albumId)->getParent();
			$blogId = Engine_Api::_()->getItem('sesblog_album', $albumId)->blog_id;
			if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.enable.package', 1)) {
				$package = $blog->getPackage();
				$photoLeft = $package->allowUploadPhoto($blog->orderspackage_id,true);
				$modulesEnable = json_decode($package->params,true);
				if(isset($modulesEnable) && array_key_exists('photo_count',$modulesEnable) && $modulesEnable['photo_count']){
					if(isset($photoLeft))
						$photo_count = $photoLeft;
					else
						$photo_count = $modulesEnable['photo_count'];
					$this->addElement('hidden', 'photo_count', array('value' => $photo_count,'order'=>8769));
				}
			}
		}
    $albumTable = Engine_Api::_()->getItemTable('sesblog_album');
    $myAlbums = $albumTable->select()
        ->from($albumTable, array('album_id', 'title'))
        ->where('blog_id = ?', $blogId)
        ->query()
        ->fetchAll();
    $albumOptions = array('0' => 'Create A New Album');
    foreach( $myAlbums as $myAlbum ) {
      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
    }
    $this->addElement('Select', 'album', array(
      'label' => 'Choose Album',
      'multiOptions' => $albumOptions,
      'onchange' => "updateTextFields()",
    ));
    // Init name
    $this->addElement('Text', 'title', array(
      'label' => 'Album Title',
      'maxlength' => '255',
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));

    // Init descriptions
    $this->addElement('Textarea', 'description', array(
      'label' => 'Album Description',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $translate = Zend_Registry::get('Zend_Translate');
    $this->addElement('Dummy', 'fancyuploadfileids', array('content'=>'<input id="fancyuploadfileids" name="file" type="hidden" value="" >'));

    $this->addElement('Dummy', 'tabs_form_albumcreate', array(
     'content' => '<div class="sesblog_create_form_tabs sesbasic_clearfix sesbm"><ul id="sesblog_create_form_tabs" class="sesbasic_clearfix"><li class="active sesbm"><i class="fa fa-arrows-alt sesbasic_text_light"></i><a href="javascript:;" class="drag_drop">'.$translate->translate('Drag & Drop').'</a></li><li class=" sesbm"><i class="fa fa-upload sesbasic_text_light"></i><a href="javascript:;" class="multi_upload">'.$translate->translate('Multi Upload').'</a></li><li class=" sesbm"><i class="fa fa-link sesbasic_text_light"></i><a href="javascript:;" class="from_url">'.$translate->translate('From URL').'</a></li></ul></div>',
    ));
    $this->addElement('Dummy', 'drag-drop', array(
      'content' => '<div id="dragandrophandler" class="sesblog_upload_dragdrop_content sesbasic_bxs">'.$translate->translate('Drag & Drop Photos Here').'</div>',
    ));
    $this->addElement('Dummy', 'from-url', array(
      'content' => '<div id="from-url" class="sesblog_upload_url_content sesbm"><input type="text" name="from_url" id="from_url_upload" value="" placeholder="'.$translate->translate('Enter Image URL to upload').'"><span id="loading_image"></span><span></span><button id="upload_from_url">'.$translate->translate('Upload').'</button></div>',
    ));

    $this->addElement('Dummy', 'file_multi', array('content'=>'<input type="file" accept="image/x-png,image/jpeg" onchange="readImageUrl(this)" multiple="multiple" id="file_multi" name="file_multi">'));
    $this->addElement('Dummy', 'uploadFileContainer', array('content'=>'<div id="show_photo_container" class="sesblog_upload_photos_container sesbasic_bxs sesbasic_custom_scroll clear"><div id="show_photo"></div></div>'));
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Photos',
      'type' => 'submit',
    ));

  }

  public function clearAlbum() {
    $this->getElement('album')->setValue(0);
  }

  public function saveValues() {

    $set_cover = false;
    $blogId = Zend_Controller_Front::getInstance()->getRequest()->getParam('blog_id', null);

      $album_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id',null);

    $values = $this->getValues();
    $params = array();
    if ((empty($values['blog_id'])) || (empty($values['user_id']))) {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      $params['blog_id'] = $blogId;
    }
    else {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      $params['blog_id'] = $blogId;
      throw new Zend_Exception("Non-user album owners not yet implemented");
    }
          if(!$blogId){
    	$album = Engine_Api::_()->getItem('sesblog_album', $album_id);
			$this->view->blog_id = $blog_id = $album->blog_id;
			$params['blog_id'] = $blog_id;
		}
    if( ($values['album'] == 0) ) {
      $params['title'] = $values['title'];
      if (empty($params['title'])) {
        $params['title'] = "Untitled Album";
      }
      $params['description'] = $values['description'];
      $params['search'] = 1;
      $album = Engine_Api::_()->getDbtable('albums', 'sesblog')->createRow();
      $album->setFromArray($params);
      $album->save();
      $set_cover = true;
    }
    else {
      if (!isset($album)) {
        $album = Engine_Api::_()->getItem('sesblog_album', $values['album']);
      }
    }

    // Do other stuff
    $count = 0;
    if(isset($_POST['file'])) {
      $explodeFile = explode(' ',rtrim($_POST['file'],' '));
      foreach( $explodeFile as $photo_id ) {
	if($photo_id == '')
	continue;
	$photo = Engine_Api::_()->getItem("sesblog_photo", $photo_id);
	if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;
	if(isset($_POST['cover']) && $_POST['cover'] == $photo_id ){
	  $album->photo_id = $photo_id;
	  $album->save();
	  unset($_POST['cover']);
	  $set_cover = false;
	}
	else if( $set_cover){
	  $album->photo_id = $photo_id;
	  $album->save();
	  $set_cover = false;
	}
	$photo->album_id = $album->album_id;
	//$photo->order    = $photo_id;
	$photo->save();
	$count++;
      }
    }
    $album->save();
    return $album;
  }
}

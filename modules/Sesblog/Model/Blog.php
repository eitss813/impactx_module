<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Blog.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Model_Blog extends Core_Model_Item_Abstract
{
  // Properties

  protected $_parent_type = 'user';
  protected $_owner_type = 'user';
  protected $_parent_is_owner = true;
  protected $_searchTriggers = array('title', 'body', 'search');
  protected $_type = 'sesblog_blog';
  protected $_statusChanged;
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    $slug = $this->getSlug();
    $params = array_merge(array(
      'route' => 'sesblog_entry_view',
      'reset' => true,
     // 'user_id' => $this->owner_id,
      'blog_id' => $this->custom_url,
      //'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
    public function setPhoto($photo,$direct = '') {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
			$name = basename($file);
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
			$name = basename($photo['name']);
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
			$name = basename($file);
    } else {
      throw new Sesblog_Model_Exception('invalid argument passed to setPhoto');
    }
    
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_id' => $this->getIdentity(),
        'parent_type' => 'sesblog_blog'
    );
    // Save
    $storage = Engine_Api::_()->storage();
    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(1000, 1000)
            ->write($path . '/m_' . $name)
            ->destroy();
    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(500, 500)
            ->write($path . '/p_' . $name)
            ->destroy();
    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(200, 200)
            ->write($path . '/in_' . $name)
            ->destroy();
    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);
    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;
    $image->resample($x, $y, $size, $size, 48, 48)
            ->write($path . '/is_' . $name)
            ->destroy();
    // Store
    $iMain = $storage->create($path . '/m_' . $name, $params);
    $iProfile = $storage->create($path . '/p_' . $name, $params);
    $iIconNormal = $storage->create($path . '/in_' . $name, $params);
    $iSquare = $storage->create($path . '/is_' . $name, $params);
    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');
    // Remove temp files
    @unlink($path . '/p_' . $name);
    @unlink($path . '/m_' . $name);
    @unlink($path . '/in_' . $name);
    @unlink($path . '/is_' . $name);
    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->file_id;
    $this->save();
		if($direct == ''){
			// Add to album
			$viewer = Engine_Api::_()->user()->getViewer();
			$photoTable = Engine_Api::_()->getItemTable('sesblog_photo');
			$blogAlbum = $this->getSingletonAlbum();
			$blogAlbum->title = Zend_Registry::get('Zend_Translate')->_('Untitled');
			$blogAlbum->owner_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			$blogAlbum->save();
			$photoItem = $photoTable->createRow();
			$photoItem->setFromArray(array(
					'blog_id' => $this->getIdentity(),
					'album_id' => $blogAlbum->getIdentity(),
					'user_id' => $viewer->getIdentity(),
					'file_id' => $iMain->getIdentity(),
					'collection_id' => $blogAlbum->getIdentity(),
					'user_id' => $viewer->getIdentity(),
			));
			$photoItem->save();
		}
    return $this;
  }
  
    public function getSingletonAlbum() {
    $table = Engine_Api::_()->getItemTable('sesblog_album');
    $select = $table->select()
            ->where('blog_id = ?', $this->getIdentity())
            ->order('album_id ASC')
            ->limit(1);

    $album = $table->fetchRow($select);

    if (null === $album) {
      $album = $table->createRow();
      $album->setFromArray(array(
          'blog_id' => $this->getIdentity()
      ));
      $album->save();
    }

    return $album;
  }
	
  public function getPhotoUrl($type = null) {
    $photo_id = $this->photo_id;
    if ($photo_id) {
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
			if($file)
      	return $file->map();
			else{
				$file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id,'thumb.profile');	
				if($file)
					return $file->map();
			}
    } 
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$defaultPhoto = Zend_Registry::get('StaticBaseUrl').$settings->getSetting('sesblog_blog_default_photo', 'application/modules/Sesblog/externals/images/nophoto_blog_thumb_profile.png');
		return $defaultPhoto;
  }

  public function getDescription($limit = '255') {
     $body = htmlspecialchars_decode($this->body);
     $stringArray  =  Engine_Api::_()->sesbasic()->get_string_between($this->body);
     foreach ($stringArray as $array){
         if($array){
             $body = str_replace("[".$array."]","",$body);
         }
     }
    // @todo decide how we want to handle multibyte string functions
    $ro = preg_replace('/\s+/', ' ',$body);
    $tmpBody = preg_replace('/ +/', ' ',html_entity_decode(strip_tags( $ro)));
    return nl2br( Engine_String::strlen($tmpBody) > $limit ? Engine_String::substr($tmpBody, 0, $limit) . '...' : $tmpBody );
  }
  
  public function fields() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getApi('core', 'fields'));
  }

  public function getKeywords($separator = ' ') {
  
    $keywords = array();
    foreach( $this->tags()->getTagMaps() as $tagmap ) {
      $tag = $tagmap->getTag();
      if($tag) {
        $keywords[] = $tag->getTitle();
      }
    }

    if( null === $separator ) {
      return $keywords;
    }
    return join($separator, $keywords);
  }
  

  // Interfaces

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }


  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
  
  public function cancel() {
    $package = $this->getPackage();
    if ($package->isFree()) {
      return true;
    }
    //update transaction_id to other blog of same package blog
    if ($this->transaction_id && $this->orderspackage_id) {
      $transaction = $this->getTransaction();
      $table = Engine_Api::_()->getDbTable('blogs', 'sesblog');
      $tableName = $table->info('name');
      //select blog in package with our transaction id.
      $select = $table->select()->from($tableName)->where('transaction_id =?', '')->where('orderspackage_id =?', $this->orderspackage_id);
      $blog = $table->fetchRow($select);
      if ($blog) {
        $blog->transaction_id = $this->transaction_id;
        $blog->save();
        //update order
        $order_id = $transaction->order_id;
        $order = Engine_Api::_()->getItem('payment_order', $order_id);
        if ($order) {
          $order->source_id = $blog->getIdentity();
          $order->save();
        }
        //update item count in order package
        $orderpackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $this->orderspackage_id);
        $orderpackage->item_count = $orderpackage->item_count + 1;
        $orderpackage->save();
        return true;
      } else {
//         //delete order package
//         $orderpackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $this->orderspackage_id);
//         if ($orderpackage)
//           $orderpackage->delete();
      }
    }else {
      if($this->orderspackage_id) {
      //update item count in order package
      $orderpackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $this->orderspackage_id);
      $orderpackage->item_count = $orderpackage->item_count + 1;
      $orderpackage->save();
      return true;
      }
    }
    // Try to cancel recurring payments in the gateway
    if (!empty($transaction->gateway_id) && !empty($transaction->gateway_profile_id) && empty($transaction->gateway_transaction_id)) {
      try {

        $gateway = Engine_Api::_()->getItem('sesblogpackage_gateway', $transaction->gateway_id);
        if ($gateway) {
          $gatewayPlugin = $gateway->getPlugin();
          if (method_exists($gatewayPlugin, 'cancelBlog')) {
            $gatewayPlugin->cancelBlog($transaction->gateway_profile_id);
          }
        }
      } catch (Exception $e) {
        // Silence?
      }
    }
    return $this;
  }

  public function getPackage() {
    return Engine_Api::_()->getItem('sesblogpackage_package', $this->package_id);
  }

  public function getTransaction() {
    return Engine_Api::_()->getItem('sesblogpackage_transaction', $this->transaction_id);
  }

  // Cntests
  public function clearStatusChanged() {
    $this->_statusChanged = null;
    return $this;
  }

  public function didStatusChange() {
    return (bool) $this->_statusChanged;
  }

  // Active
  public function setActive($flag = true, $deactivateOthers = null) {
    //$this->active = true;
    if ((true === $flag && null === $deactivateOthers) ||
            $deactivateOthers === true) {
      $this->is_approved = 1;
    }
    $this->save();
    return $this;
  }

  public function changeApprovedStatus($approved = 0) {
    $transaction = $this->getTransaction();
    $orderPackageId = $this->orderspackage_id;
    if ($transaction && $orderPackageId) {
      $this->is_approved = $approved;
      $this->save();
      //Engine_Api::_()->getDbtable('blogs', 'sesblog')->update(array('is_approved' => $approved), array('orderspackage_id' => $orderPackageId));
      if ($approved) {
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity(Engine_Api::_()->user()->getViewer(), $this, 'sesblog_create');
        if ($action) {
          $activityApi->attachActivity($action, $this);
        }
      }
    }
  }

  public function onPaymentSuccess() {
    $this->_statusChanged = false;
    $transaction = $this->getTransaction();
    if ($transaction) {
      if (in_array($transaction->state, array('initial', 'trial', 'pending', 'active'))) {
        // If the package is in initial or pending, set as active and
        // cancel any other active subscriptions
        if (in_array($transaction->state, array('initial', 'pending'))) {
          $this->setActive(true);
        }

        // Update expiration to expiration + recurrence or to now + recurrence?
        $package = $this->getPackage();
        $expiration = $package->getExpirationDate();
        //get custom feature of package
        $params = json_decode($package->params, true);
        $approved = 0;
        if (isset($params['blog_approve']) && $params['blog_approve'])
          $approved = 1;
        if (isset($params['blog_featured']) && $params['blog_featured'])
          $this->featured = 1;
        if (isset($params['blog_sponsored']) && $params['blog_sponsored'])
          $this->sponsored = 1;
        if (isset($params['blog_verified']) && $params['blog_verified'])
          $this->verified = 1;
        if (isset($params['blog_hot']) && $params['blog_hot'])
          $this->hot = 1;
        $this->save();

        if (!$approved) {
          Engine_Api::_()->sesblog()->sendMailNotification(array('blog' => $this));
        }

        //check isonetime condition and renew exiration date if left
        $daysLeft = 0;
        if ($package->isOneTime() && !empty($transaction->expiration_date) && $transaction->expiration_date != '3000-00-00 00:00:00') {
          $datediff = strtotime($transaction->expiration_date) - time();
          $daysLeft = floor($datediff / (60 * 60 * 24));
        }
        $orderPackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $this->orderspackage_id);

        if ($expiration) {
          $expiration_date = date('Y-m-d H:i:s', $expiration);
          //check days left or not
          if ($daysLeft >= 1) {
            //reniew condition
            $expiration_date = date('Y-m-d H:i:s', strtotime($transaction->expiration_date . '+ ' . $daysLeft . ' days'));
          }
          $transaction->expiration_date = $expiration_date;
          $orderPackage->expiration_date = $expiration_date;
          $orderPackage->save();
        } else {
          //make it a future blog(never expired)
          $transaction->expiration_date = '3000-00-00 00:00:00';
          $orderPackage->expiration_date = '3000-00-00 00:00:00';
          $orderPackage->save();
        }
        //update all items in the transaction
        $this->changeApprovedStatus($approved);
        // Change status
        if ($transaction->state != 'active') {
          $transaction->state = 'active';
          $this->_statusChanged = true;
        }
        $transaction->save();
      }
    }
    return $transaction;
  }

  public function onPaymentPending() {
    $this->_statusChanged = false;
    $transaction = $this->getTransaction();
    if ($transaction && ( in_array($transaction->state, array('initial', 'trial', 'pending', 'active')))) {
      //update all items in the transaction
      $this->changeApprovedStatus(0);
      // Change status
      if ($transaction->state != 'pending') {
        $transaction->state = 'pending';
        $this->_statusChanged = true;
        $transaction->save();
      }
    }
    return $this;
  }

  public function onPaymentFailure() {
    $this->_statusChanged = false;
    $transaction = $this->getTransaction();

    if ($transaction && in_array($transaction->state, array('initial', 'trial', 'pending', 'active', 'overdue'))) {
      //update all items in the transaction
      $this->changeApprovedStatus(0);
      // Change status
      if ($transaction->state != 'overdue') {
        $transaction->state = 'overdue';
        $this->_statusChanged = true;
        $transaction->save();
      }
    }

    return $this;
  }

  public function onCancel() {
    $this->_statusChanged = false;
    $transaction = $this->getTransaction();
    if ($transaction && ( in_array($transaction->state, array('initial', 'trial', 'pending', 'active', 'overdue', 'cancelled', 'okay')) )) {
      //update all items in the transaction
      $this->changeApprovedStatus(0);
      // Change status
      if ($transaction->state != 'cancelled') {
        $transaction->state = 'cancelled';
        $this->_statusChanged = true;
        $transaction->save();
      }
    }

    return $this;
  }

  public function onExpiration() {
    $this->_statusChanged = false;
    $transaction = $this->getTransaction();
    if ($transaction && ( in_array($this->state, array('initial', 'trial', 'pending', 'active', 'expired', 'overdue')) )) {
      //update all items in the transaction
      $this->changeApprovedStatus(0);
      // Change status
      if ($transaction->state != 'expired') {
        $transaction->state = 'expired';
        $this->_statusChanged = true;
        $transaction->save();
      }
    }

    return $this;
  }

  public function onRefund() {
    $this->_statusChanged = false;
    $transaction = $this->getTransaction();
    if ($transaction && in_array($transaction->state, array('initial', 'trial', 'pending', 'active', 'refunded'))) {
      //update all items in the transaction
      $this->changeApprovedStatus(0);
      // Change status
      if ($transaction->state != 'refunded') {
        $transaction->state = 'refunded';
        $this->_statusChanged = true;
        $transaction->save();
      }
    }
    return $this;
  }
}

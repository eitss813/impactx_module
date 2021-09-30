<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitealbum_Widget_FeaturedPhotosSlideshowController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$this->_getParam('detactLocation', 0)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
      }
    }

    $params = array();
     //GET SUBJECT TYPE
    $this->view->whichslideshow = $params['whichslideshow'] = $this->_getParam('whichslideshow', 'photosslideshow');
    
    //GET SLIDESHOW TYPE
    $this->view->type = $params['slideshow_type'] = $this->_getParam('slideshow_type', 'zndp');

    //GET SLIDESHOW HIGHT
    $this->view->height = $params['slideshow_height'] = $this->_getParam('slideshow_height', 350);
 
    //GET SLIDESHOW WIDTH
    $this->view->width = $params['slideshow_width'] = $this->_getParam('slideshow_width', 825);
    
    //GET SLIDESHOW DELAY
    $this->view->delay = $params['delay'] = $this->_getParam('delay', 3500);
    
     //GET SLIDESHOW DURATION
    $this->view->duration = $params['duration'] = $this->_getParam('duration', 750);

    //GET TARGET SETTINGS
    $this->view->target = 0;

    // GET THUMBNAILS SETTINGS FOR NOOB SLIDESHOW
    $this->view->thumb = $params['showButtonSlide'] = $this->_getParam('showButtonSlide', 1);

    // GET THUMBNAILS SETTINGS FOR ZOOMING AND PANNING SLIDESHOW
    $this->view->showThumbnailInZP = $params['showThumbnailInZP'] = $this->_getParam('showThumbnailInZP', 1);

    // GET CAPTION SETTINGS
    $this->view->caption = $params['showCaption'] = $this->_getParam('showCaption', 0);

    // GET MOUSE EVENT ENTER SETTINGS
    $this->view->mouseEnterEvent = $params['mouseEnterEvent'] = $this->_getParam('mouseEnterEvent', 0);

    // GET THUMB POSITION SETTINGS
    $this->view->thumbPosition = $params['thumbPosition'] = $this->_getParam('thumbPosition', 'bottom');
      
     // GET SLIDESHOW CONTROLLER SETTINGS
    $this->view->showController = $params['showController'] = $this->_getParam('showController', '0');
    
    // GET AUTO PLAY SLIDESHOW SETTINGS
    $this->view->autoPlay = $params['autoPlay'] = $this->_getParam('autoPlay', 1);

    // GET CAPTION TRUNCATION LIMIT
    $this->view->captionTruncation = $params['captionTruncation'] = $this->_getParam('captionTruncation', 200);
    $params['limit'] = $this->_getParam('slidesLimit', 10);
    $params['category_id'] = $this->_getParam('category_id');
    $params['subcategory_id'] = $this->_getParam('subcategory_id');
    $params['popularType'] = $this->_getParam('popularType', 'creation');
    $params['interval'] = $interval = $this->_getParam('interval', 'overall');
    $params['featured'] = $this->_getParam('featured', 1);

    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1);
    }
    if ($this->view->detactLocation) {
      $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    switch ($params['popularType']) {
      case 'view':
        $params['orderby'] = 'view_count';
        break;
      case 'comment':
        $params['orderby'] = 'comment_count';
        break;
      case 'like':
        $params['orderby'] = 'like_count';
        break;
      case 'rating':
        $params['orderby'] = 'rating';
        break;
      case 'creation':
        $params['orderby'] = 'creation_date';
        break;
      case 'modified':
        $params['orderby'] = 'modified_date';
        break;
      case 'random':
        $params['orderby'] = 'random';
        break;
    }

    $this->view->params = $params;

    if (!$this->view->is_ajax_load)
      return;
      
    if($this->view->whichslideshow == 'photosslideshow')
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('photos', 'sitealbum')->photoBySettings($params);
    else
      $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->albumBySettings($params);
    
    $paginator->setItemCountPerPage($params['limit']);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    
    $this->view->total_images = $paginator->getTotalItemCount() > $params['limit'] ? $params['limit'] : $paginator->getTotalItemCount(); 
    
    if($params['slideshow_type'] == 'zndp') { 
    if ($this->view->total_images > 0)
      $this->view->thumb_width = (int) ($this->view->width / $this->view->total_images); 
    }
    
    $featuredPhoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.featuredalbum', null);
    // Do not render if nothing to show
    if (($paginator->getTotalItemCount() <= 0) || empty($featuredPhoto)) {
      return $this->setNoRender();
    }
  }

}
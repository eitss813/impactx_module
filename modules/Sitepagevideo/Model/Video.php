<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Video.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Model_Video extends Core_Model_Item_Abstract {

  protected $_parent_type = 'user';
  protected $_owner_type = 'user';
  protected $_parent_is_owner = true;

	public function getMediaType() {
		return 'video';
	}
	
  /**
   * Return page object
   *
   * @return page object
   * */
  public function getParent($recurseType = null) {
    
    if($recurseType == null) $recurseType = 'sitepage_page';
    
    return Engine_Api::_()->getItem($recurseType, $this->page_id);
  }
  
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    $pageid = $this->page_id;
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    $tab_id = '';
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagevideo.sitemobile-profile-sitepagevideos', $pageid, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagevideo.profile-sitepagevideos', $pageid, $layout);
		}
    $params = array_merge(array(
                'route' => 'sitepagevideo_view',
                'reset' => true,
                'user_id' => $this->owner_id,
                'video_id' => $this->video_id,
                'slug' => $this->getSlug(),
                'tab' => $tab_id,
                    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
  }

  /**
   * Return a truncate video owner name
   *
   * @return truncate owner name
   * */
  public function truncateOwner($owner_name) {
    $tmpBody = strip_tags($owner_name);
    return ( Engine_String::strlen($tmpBody) > 10 ? Engine_String::substr($tmpBody, 0, 10) . '..' : $tmpBody );
  }

  /**
   * Make format for activity feed
   *
   * @return activity feed content
   */
  public function getRichContent($view = false, $params = array()) {

    $session = new Zend_Session_Namespace('mobile');
    $mobile = $session->mobile;

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
     $mobile = 1;
		}

    //VIDEO TYPE IS YOUTUBE
    if ($this->type == 1) {
      $videoEmbedded = $this->compileYouTube($this->video_id, $this->code, $view, $mobile);
    }

    //VIDEO TYPE IS VIMEO
    if ($this->type == 2) {
      $videoEmbedded = $this->compileVimeo($this->video_id, $this->code, $view, $mobile);
    }

    //VIDEO TYPE IS MY COMPUTER
    if ($this->type == 3) {
      $video_location = Engine_Api::_()->storage()->get($this->file_id, $this->getType())->getHref();
      $videoEmbedded = $this->compileFlowPlayer($video_location, $view);
    }

    //THIS RICH IS REQUESTED FROM THE ACTIVITY FEED
    if ($view == false) {

      //DURATION
      $video_duration = "";
      if ($this->duration) {
        if ($this->duration > 360)
          $duration = gmdate("H:i:s", $this->duration);
        else
          $duration = gmdate("i:s", $this->duration);
        if ($duration[0] == '0')
          $duration = substr($duration, 1);
        $video_duration = "<span class='sitepagevideo_length'>" . $duration . "</span>";
      }

      //THUMBNAIL
      $thumb = Zend_Registry::get('Zend_View')->itemPhoto($this, 'thumb.video.activity');

      if ($this->photo_id) {
        $thumb = Zend_Registry::get('Zend_View')->itemPhoto($this, 'thumb.video.activity');
      } else {
        $thumb = '<img alt="" src="'.Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Video/externals/images/video.png">';
      }

      $thumb = '<a id="video_thumb_'.$this->video_id.'" class="video_thumb" href="' . $this->getHref() . '">
              <div class="sitepagevideo_thumb_wrapper">' . $video_duration . $thumb . '</div>
              </a>';

      //TITLE AND DESCRIPTION
      $title = "<a href='" . $this->getHref($params) . "' class='feed_video_title' >$this->title</a>";
      $tmpBody = strip_tags($this->description);
      $description = "<div class='video_desc'>" . (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody) . "</div>";

      $videoEmbedded = $thumb . '<div id="video_object_' . $this->video_id . '" style="display:none;">' . $videoEmbedded . '</div><div class="video_info">' . $title . $description . '</div>';
    }
    return $videoEmbedded;
  }

  public function getEmbedCode(array $options = null) {
    $httpInformation = _ENGINE_SSL ? 'https://' : 'http://';
    $options = array_merge(array(
                'height' => '525',
                'width' => '525',
                    ), (array) $options);

    $view = Zend_Registry::get('Zend_View');
    $url = $httpInformation . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'module' => 'sitepagevideo',
                'controller' => 'video',
                'action' => 'external',
                'video_id' => $this->getIdentity(),
                    ), 'default', true) . '?format=frame';
    return '<iframe '
    . 'src="' . $view->escape($url) . '" '
    . 'width="' . sprintf("%d", $options['width']) . '" '
    . 'height="' . sprintf("%d", $options['width']) . '" '
    . 'style="overflow:hidden;"'
    . '>'
    . '</iframe>';
  }

  public function compileYouTube($video_id, $code, $view, $mobile = false) {
  
    $httpInformation = _ENGINE_SSL ? 'https://' : 'http://';
    if (isset($_SERVER['HTTP_USER_AGENT']) &&  (preg_match('/' . 'iPad|Nexus|GT-P1000|SGH-T849|SHW-M180S|Kindle|Silk' . '/i', $_SERVER['HTTP_USER_AGENT'])||preg_match('/' . 'iPhone' . '/i', $_SERVER['HTTP_USER_AGENT'])) || Engine_Api::_()->seaocore()->isMobile()) {

    $mobile=true;
    }
    //560 x 340
    //legacy youtube embed code
    if (!$mobile) {
      $embedded = '
      <object width="' . ($view ? "560" : "425") . '" height="' . ($view ? "340" : "344") . '">
      <param name="movie" value="'.$httpInformation.'www.youtube.com/v/' . $code . '&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1"/>
      <param name="allowFullScreen" value="true"/>
      <param name="allowScriptAccess" value="always"/>
      <embed src="'.$httpInformation.'www.youtube.com/v/' . $code . '&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1' . ($view ? "" : "&autoplay=1") . '" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="' . ($view ? "560" : "425") . '" height="' . ($view ? "340" : "344") . '" wmode="transparent"/>
      <param name="wmode" value="transparent" />
      </object>';
    } else {
      $autoplay = !$mobile && !$view;

      $embedded = '
        <iframe
        title="YouTube video player"
        id="videoFrame' . $video_id . '"
        class="youtube_iframe' . ($view ? "_big" : "_small") . '"' .
              /*
                width="'.($view?"560":"425").'"
                height="'.($view?"340":"344").'"
               */'
        src="https://www.youtube.com/embed/' . $code . '?wmode=opaque&modestbranding=1&showinfo=0' . ($autoplay ? "&autoplay=1" : "") . '"
        frameborder="0"
        allowfullscreen="">
        </iframe>';
    }


    return $embedded;
  }

  public function compileVimeo($video_id, $code, $view, $mobile = false) {
    //640 x 360
     if (isset($_SERVER['HTTP_USER_AGENT']) &&  (preg_match('/' . 'iPad|Nexus|GT-P1000|SGH-T849|SHW-M180S|Kindle|Silk' . '/i', $_SERVER['HTTP_USER_AGENT'])||preg_match('/' . 'iPhone' . '/i', $_SERVER['HTTP_USER_AGENT'])) || Engine_Api::_()->seaocore()->isMobile()) {

    $mobile=true;
    }
    $httpInformation = _ENGINE_SSL ? 'https://' : 'http://';
    if (!$mobile) {
      $embedded = '
      <object width="' . ($view ? "560" : "425") . '" height="' . ($view ? "340" : "344") . '">
      <param name="allowfullscreen" value="true"/>
      <param name="allowscriptaccess" value="always"/>
      <param name="movie" value=""'.$httpInformation.'vimeo.com/moogaloop.swf?clip_id=' . $code . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" />
      <embed src="'.$httpInformation.'vimeo.com/moogaloop.swf?clip_id=' . $code . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1' . ($view ? "" : "&amp;autoplay=1") . '" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="' . ($view ? "640" : "400") . '" height="' . ($view ? "360" : "230") . '" wmode="transparent"/>
      <param name="wmode" value="transparent" />
      </object>';
    } else {
      $autoplay = !$mobile && !$view;

      $embedded = '
        <iframe
        title="Vimeo video player"
        id="videoFrame' . $video_id . '"
        class="vimeo_iframe' . ($view ? "_big" : "_small") . '"' .
              /*
                width="'.($view?"640":"400").'"
                height="'.($view?"360":"230").'"
               */'
        src="'.$httpInformation.'player.vimeo.com/video/' . $code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "") . '"
        frameborder="0"
        allowfullscreen="">
        </iframe>';
    }

    return $embedded;
  }

  public function compileFlowPlayer($location, $view) {

		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;


       $flowplayerSwf = Engine_Api::_()->sitepagevideo()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version, '4.8.10') ? '/externals/flowplayer/flowplayer-3.1.5.swf' : '/externals/flowplayer/flowplayer-3.2.18.swf';
    $embedded = "
    <div id='videoFrame" . $this->video_id . "'></div>
    <script type='text/javascript'>
    en4.core.runonce.add(function(){\$('video_thumb_" . $this->video_id . "').removeEvents('click').addEvent('click', function(){flashembed('videoFrame$this->video_id',{src: '" . $view->layout()->staticBaseUrl . $flowplayerSwf."', width: " . ($view ? "480" : "420") . ", height: " . ($view ? "386" : "326") . ", wmode: 'opaque'},{config: {clip: {url: '$location',autoPlay: " . ($view ? "false" : "true") . ", duration: '$this->duration', autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});})});
    </script>";
    return $embedded;
  }


  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
  
  protected function _delete() {
    // Delete create activity feed of video before delete video 
    Engine_Api::_()->getApi('subCore', 'sitepage')->deleteCreateActivityOfExtensionsItem($this, array('sitepagevideo_new', 'sitepagevideo_admin_new'));
    parent::_delete();
  }
}
?>

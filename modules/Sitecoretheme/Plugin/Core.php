<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Plugin_Core extends Zend_Controller_Plugin_Abstract
{

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      Engine_Api::_()->getDbTable('teams', 'sitecoretheme')->delete(array('user_id = ?' => $payload->getIdentity()));
    }
  }

  public function onRenderLayoutDefault($event)
  {

    $view = $event->getPayload();
    $view->headTranslate(array("Forgot Password?", "Login with Twitter", "Login with Facebook", "Mark as Read", "Mark as Unread"));

    $circularImageTheme = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.circular.image', 0);
    if( $circularImageTheme ) {
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/themes/sitecoretheme/theme_circular.css');
    }

    $floating_header = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.floating.header', 1);
    $backgroundImage = '';

    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.theme.website.body.background.image', 0) ) {
      $backgroundImage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.theme.website.body.background.image', 0);
    }
    $themeTable = Engine_Api::_()->getDbtable('themes', 'core');
    $active = $themeTable->select()
      ->from($themeTable->info('name'), 'active')
      ->where('name = ?', 'sitecoretheme')
      ->where('active = ?', 1)
      ->query()
      ->fetchColumn()
    ;
    if( $active ) {
      $this->_loadFontsCss($view);
      $includeThemeBasedClass = <<<EOF
                    var floating_header = '$floating_header';
                    var backgroundImage = '$backgroundImage';
        en4.core.runonce.add(function(){
        window.addEvent('domready', function() {
                setTimeout(function () {
                    if (floating_header == 0 && document.getElementsByTagName("BODY")[0]) {
                       document.getElementsByTagName("BODY")[0].addClass('sitecoretheme_non_floating_header');
                    }
                    if(backgroundImage)    
                    document.getElementsByTagName("BODY")[0].setStyle('background-image', 'url("$backgroundImage")');
                }, 100);
                //if the vertical theme header widget is nor present in header than add a class in header, to add some css
                if(!$('global_header').getElements('.layout_sitecoretheme_header')[0]) {
                  if($('global_header').getElements('.layout_page_header')[0]) {
                    $('global_header').getElements('.layout_page_header')[0].addClass('sitecoretheme_core_widgets_header');
                  }
                }
          });      
        });
EOF;
      $view->headScript()->appendScript($includeThemeBasedClass);
      $layout = $view->layout();
      $themeTable = Engine_Api::_()->getDbtable('themes', 'sitecoretheme');
      $themeSelect = $themeTable->select()
        ->where('active = ?', 1)
        ->limit(1);
      $themeRow = $themeTable->fetchRow($themeSelect);
      $colorName = $themeRow->name;
      $cookieName = 'sitecoretheme_seao_theme_color';
      if( !empty($_COOKIE[$cookieName]) ) {
        $colorName = $_COOKIE[$cookieName];
      }
      $themeName = 'sitecoretheme/' . $colorName;
      $themes = array();
      foreach( $layout->themes as $key => $theme ) {
        if( $theme == 'sitecoretheme' ) {
          $theme = $themeName;
        }
        $themes[$key] = $theme;
      }
      $layout->themes = $themes;
      $themesInfo = $layout->themesInfo;
      $themesInfo[$themeName] = include APPLICATION_PATH_COR . DS
        . 'themes' . DS . 'sitecoretheme' . DS . $colorName . DS . 'manifest.php';
      $layout->themesInfo = $themesInfo;
      Zend_Registry::set('Themes', $themesInfo);
    }
  }

  public function onRenderLayoutDefaultSimple($event)
  {
    // Forward
    return $this->onRenderLayoutDefault($event, 'simple');
  }

  protected function _loadFontsCss($view)
  {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $fontType = $coreSettings->getSetting('sitecoretheme.fonts.selected.font', 0);
    if( $fontType == 0 ) {
      $family[] = urlencode($this->_getFontsFamilyName('body'));
      $family[] = urlencode($this->_getFontsFamilyName('heading'));
      $family[] = urlencode($this->_getFontsFamilyName('mainmenu'));
      $family[] = urlencode($this->_getFontsFamilyName('tab'));
      $query = join("|", array_unique($family));
      $view->headLink()->appendStylesheet('https://fonts.googleapis.com/css?family=' . $query);
    }
  }

  protected function _getFontsFamilyName($name)
  {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $fonts = $coreSettings->getSetting('sitecoretheme.fonts.' . $name . '.font.family.google', '"Roboto", sans-serif');
    if( empty($fonts) ) {
      return 'Roboto';
    }
    $explode = (explode(',', $fonts));
    $family = trim(str_replace(array("'", '"'), '', $explode[0]));
    return $family;
  }

  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
		if (!defined('SITECORETHEME_PLUGIN_NAME')) {
			$theme_name = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitecoretheme');
			define('SITECORETHEME_PLUGIN_NAME', $theme_name->title);
		}
	}

}
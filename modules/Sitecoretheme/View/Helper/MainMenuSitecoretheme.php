<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MainMenuSitecoretheme.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_View_Helper_MainMenuSitecoretheme extends Zend_View_Helper_Navigation_Menu
{  //Create home screen icon's links and add those links on web page.

  

  public function mainMenuSitecoretheme(Zend_Navigation_Container $container = null)
  {
    if( null !== $container ) {
      $this->setContainer($container);
    }
    return $this;
  }

  
  public function htmlify(Zend_Navigation_Page $page)
  {
    // get label and title for translating
    $label = $page->getLabel();
    $title = $page->getTitle();

    // translate label and title?
    if( $this->getUseTranslator() && $t = $this->getTranslator() ) {
      if( is_string($label) && !empty($label) ) {
        $label = $t->translate($label);
      }
      if( is_string($title) && !empty($title) ) {
        $title = $t->translate($title);
      }
    }
    $label = $this->view->escape($label);

    $icon = $page->get('icon') ?: 'fa-star';
    if( $this->_isUrl($icon) ) {
      $label = '<i class="_buttonlink" style="background-image: url(' . $icon . ')"></i><span>' . $label . '</span>';
    } else {
      $label = '<i class="fa ' . $icon . '"></i><span>' . $label . '</span>';
    }
    // get attribs for element
    $attribs = array(
      'id' => $page->getId(),
      'title' => $title,
    );

    if( false === $this->getAddPageClassToLi() ) {
      $attribs['class'] = $page->getClass();
    }

    // does page have a href?
    if( $href = $page->getHref() ) {
      $element = 'a';
      $attribs['href'] = $href;
      $attribs['target'] = $page->getTarget();
      $attribs['accesskey'] = $page->getAccessKey();
    } else {
      $element = 'span';
    }

    // Add custom HTML attributes
    $attribs = array_merge($attribs, $page->getCustomHtmlAttribs());

    return '<' . $element . $this->_htmlAttribs($attribs) . '>'
      . $label
      . '</' . $element . '>'
      . ($page->hasChildren() ? '<span class="collapse_icon"></span>' : '');
  }

  protected function _isUrl($string)
  {
    return Zend_Uri::check($string);
  }

}
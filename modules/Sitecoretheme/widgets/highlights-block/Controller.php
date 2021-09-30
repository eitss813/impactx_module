<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Widget_highlightsBlockController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $params = array('enabled' => 1);
    $this->view->description  = $this->_getParam('description', '');
    $this->view->highlights = Engine_Api::_()->getDbtable('highlights', 'sitecoretheme')->getHighlights($params);
    $this->view->highlightsSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.landing.highlights');

    if( !count($this->view->highlights) ) {
      return $this->setNoRender();
    }
  }

}
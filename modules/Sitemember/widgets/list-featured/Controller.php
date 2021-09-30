<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemember_Widget_ListFeaturedController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->description = $this->_getParam('description', '');
    $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', 'featured');
    if ($fea_spo == 'featured') {
        $values['featured'] = 1;
    } elseif ($fea_spo == 'sponsored') {
        $values['sponsored'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
        $values['sponsored'] = 1;
        $values['featured'] = 1;
    }
    $this->view->limit = $values['limit'] = $this->_getParam('itemCount', 5);
    
    $this->view->members = $paginator = Engine_Api::_()->sitemember()->getUsersSelect($values);
    $this->view->totalCount = $paginator->getTotalItemCount();

    if( $this->view->totalCount <= 0 ) {
        return $this->setNoRender();
    }
  }

}
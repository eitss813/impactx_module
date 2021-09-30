<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_PlaylistSearchController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $widgetSettings = array();
    $widgetSettings['formElements'] = $this->_getParam('formElements',array('playlistelement','videoelement','membername'));  
    $widgetSettings['playlistWidth'] = $this->_getParam('playlistWidth', 200);
    $widgetSettings['videoWidth'] = $this->_getParam('videoWidth', 200);
    $widgetSettings['memberNameWidth'] = $this->_getParam('memberNameWidth', 200);
    if(empty($widgetSettings))
        return $this->setNoRender();
    
    $this->view->form = $form = new Sitevideo_Form_Search_Searchplaylist(array('widgetSettings' => $widgetSettings));
    
    //GET FORM VALUES
    $requestParams = $request->getParams();
    $viewFormat = $requestParams['viewFormat'] = $request->getParam('viewFormat','gridView');
    //POPULATE SEARCH FORM
    $form->populate($requestParams);
    }

}

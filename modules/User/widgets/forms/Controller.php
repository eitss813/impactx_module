<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FormController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_Widget_FormsController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->core()->hasSubject('user')) {
            $this->view->user = $user = $viewer;
        } else {
            $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $type = $this->_getParam('tab_link', 'forms_assigned');

        $this->view->tab_link = $tab_link = $type ? $type : $this->_getParam('tab_link');

        if ($tab_link == 'forms_assigned') {
            $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->userForms($viewer_id);
        }

        if ($tab_link == 'forms_submitted') {
            $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->userFormsSubmitted($viewer_id);
        }

    }
}
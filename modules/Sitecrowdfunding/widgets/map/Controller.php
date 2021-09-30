<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_MapController extends Seaocore_Content_Widget_Abstract
{

    public function indexAction() {

        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getAllProjectLocationSelect();

        if(count($paginator) == 0){
            return $this->setNoRender();
        }

    }

}

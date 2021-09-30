<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_ManageSearchSitepageController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Managesearch(array(
            'type' => 'sitepage_page'
        ));
        $form->removeElement('show');
        $this->view->category_id = '';
        $this->view->subcategory_id = '';
        $this->view->subsubcategory_id = '';
        if(isset($_POST['category_id']))
        $this->view->category_id = $_POST['category_id'];
        if(isset($_POST['subcategory_id']))
        $this->view->subcategory_id = $_POST['subcategory_id'];
        if(isset($_POST['subsubcategory_id']))
        $this->view->subsubcategory_id = $_POST['subsubcategory_id'];
        if (!empty($_POST))
            $form->populate($_POST);
    }

}

?>
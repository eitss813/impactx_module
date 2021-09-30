<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: AdminSettingsController.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_settings');

    $this->view->form  = $form = new Sesblog_Form_Admin_Settings_Global();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) ) {

      $values = $form->getValues();
      
      include_once APPLICATION_PATH . "/application/modules/Sesblog/controllers/License.php";

      $db = Engine_Db_Table::getDefaultAdapter();

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.pluginactivated')) {

        //Design Layout
        if (isset($values['sesblog_chooselayout']))
          $values['sesblog_chooselayout'] = serialize($values['sesblog_chooselayout']);
        else
          $values['sesblog_chooselayout'] = serialize(array());

        //Start Landing page set
//         if (isset($_POST['sesblog_changelanding']) && $_POST['sesblog_changelanding'] == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.changelanding', 0) != $_POST['sesblog_changelanding']) {
//           $this->landingPageSetup();
// 				}
        //End Landing Page set

        $this->changeLanguage($values);
        
        foreach ($values as $key => $value){
          if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key, $value))
              Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
          if (!$value && strlen($value) == 0)
              continue;
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
        $this->_helper->redirector->gotoRoute(array());
      }
    }
  }
  
  public function createsettingsAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_blogsettings');
    
    $this->view->form = $form = new Sesblog_Form_Admin_Settings_CreateSettings();
    
    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      $values = $form->getValues(); 
      if(!empty($values['sesblog_photouploadoptions'])) 
        $values['sesblog_photouploadoptions'] = serialize($values['sesblog_photouploadoptions']);
      else  
        $values['sesblog_photouploadoptions'] = serialize(array());

      foreach ($values as $key => $value) {
        if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key, $value))
            Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
//         if (!$value && strlen($value) == 0)
//             continue;
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }
  
  public function supportAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_support');
  }

  public function statisticAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_statistic');
  }

  public function manageWidgetizePageAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_managepages');
  }

  public function landingpagesetupAction() {

    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle("Set This Page As Landing Page?");
    $form->setDescription('Are you sure want to set the Welcome Page of this plugin as the Landing page of your website? For old landing page you will have to manually make changes in the Landing page from Layout Editor. Backup page of your current landing page will get created with the name "SNS - Advanced Blog - Backup - Landing Page".');
    $form->submit->setLabel("confirm");
    
    if (!$this->getRequest()->isPost())
      return;
      
    if (!$form->isValid($this->getRequest()->getPost()))
      return;
      
    $page_id = (int) $this->_getParam('page_id');
    $pageName = $this->_getParam('page_name');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    try {
      $db = Engine_Db_Table::getDefaultAdapter();

      // Get page param
      $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
      $contentTable = Engine_Api::_()->getDbtable('content', 'core');
      
      // Make new page
      $pageObject = $pageTable->createRow();
      $pageObject->displayname = "SNS - Advanced Blog - Backup - Landing Page";
      $pageObject->provides = 'no-subject';
      $pageObject->save();
      $new_page_id = $pageObject->page_id;
      
      $old_page_content = $db->select()
          ->from('engine4_core_content')
          ->where('`page_id` = ?', 3)
          ->order(array('type', 'content_id'))
          ->query()
          ->fetchAll();
      
      $content_count = count($old_page_content);
      for($i = 0; $i < $content_count; $i++){
        $contentRow = $contentTable->createRow();
        $contentRow->page_id = $new_page_id;
        $contentRow->type = $old_page_content[$i]['type'];
        $contentRow->name = $old_page_content[$i]['name'];
        if( $old_page_content[$i]['parent_content_id'] != null ) {
          $contentRow->parent_content_id = $content_id_array[$old_page_content[$i]['parent_content_id']];            
        }
        else{
          $contentRow->parent_content_id = $old_page_content[$i]['parent_content_id'];
        }
        $contentRow->order = $old_page_content[$i]['order'];
        $contentRow->params = $old_page_content[$i]['params'];
        $contentRow->attribs = $old_page_content[$i]['attribs'];
        $contentRow->save();
        $content_id_array[$old_page_content[$i]['content_id']] = $contentRow->content_id;
      }

      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = 3");
      $page_id = 3;
      $widgetOrder = 1;

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesblog.featured-sponsored-verified-category-slideshow',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"category":"2","criteria":"0","order":"","info":"recently_created","isfullwidth":"1","autoplay":"1","speed":"2000","show_criteria":["like","comment","favourite","view","title","by","creationDate","readtime","rating","ratingStar","featuredLabel","sponsoredLabel","verifiedLabel","favouriteButton","likeButton","category","description","socialSharing"],"socialshare_enable_plusicon":"1","socialshare_icon_limit":"2","title_truncation":"45","height":"500","limit_data":"5","title":"","nomobile":"0","name":"sesblog.featured-sponsored-verified-category-slideshow"}',
      ));

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesbasic.simple-html-block',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"en_bodysimple":"<div style=\"font-size: 34px;margin-top:50px;text-align: center;margin-bottom:20px;\">Featured Posts -  Heads up bloggers!<\/div>","bodysimple":"<div style=\"font-size: 34px;margin-top:50px;text-align: center;margin-bottom:20px;\">Featured Posts -  Heads up bloggers!<\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesblog.tabbed-widget-blog',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"enableTabs":["list1"],"openViewType":"list1","tabOption":"advance","htmlTitle":"1","category_id":"","show_criteria":["verifiedLabel","favouriteButton","likeButton","socialSharing","like","favourite","comment","readtime","ratingStar","rating","view","title","category","by","ownerPhoto","readmore","creationDate","location","descriptionlist","descriptionsimplelist","descriptionadvlist","description4list","descriptionadvgrid","descriptionsupergrid","description4grid","descriptionpinboard"],"socialshare_enable_listview1plusicon":"1","socialshare_icon_listview1limit":"2","socialshare_enable_listview2plusicon":"1","socialshare_icon_listview2limit":"2","socialshare_enable_listview3plusicon":"1","socialshare_icon_listview3limit":"2","socialshare_enable_listview4plusicon":"1","socialshare_icon_listview4limit":"2","socialshare_enable_gridview1plusicon":"1","socialshare_icon_gridview1limit":"2","socialshare_enable_gridview2plusicon":"1","socialshare_icon_gridview2limit":"2","socialshare_enable_gridview3plusicon":"1","socialshare_icon_gridview3limit":"2","socialshare_enable_gridview4plusicon":"1","socialshare_icon_gridview4limit":"2","socialshare_enable_pinviewplusicon":"1","socialshare_icon_pinviewlimit":"2","socialshare_enable_mapviewplusicon":"1","socialshare_icon_mapviewlimit":"2","show_limited_data":"no","pagging":"pagging","title_truncation_grid":"45","title_truncation_list":"45","title_truncation_simplelist":"45","title_truncation_advlist":"45","title_truncation_advlist2":"45","title_truncation_advgrid":"45","title_truncation_advgrid2":"45","title_truncation_supergrid":"45","title_truncation_pinboard":"45","limit_data_pinboard":"10","limit_data_list1":"4","limit_data_grid1":"10","limit_data_grid2":"10","limit_data_list2":"4","limit_data_list3":"4","limit_data_grid2":"10","limit_data_grid3":"10","description_truncation_list":"35","description_truncation_advgrid2":"45","description_truncation_simplelist":"300","description_truncation_advlist":"","description_truncation_advlist2":"45","description_truncation_advgrid":"45","description_truncation_supergrid":"45","description_truncation_pinboard":"45","height_grid":"280","width_grid":"393","height_list":"280","width_list":"552","height_simplelist":"230","width_simplelist":"260","height_advgrid":"230","width_advgrid":"260","height_advgrid2":"230","width_advgrid2":"260","height_supergrid":"230","width_supergrid":"260","width_pinboard":"300","search_type":["verified"],"dummy1":null,"recentlySPcreated_order":"1","recentlySPcreated_label":"Recently Created","dummy2":null,"mostSPviewed_order":"2","mostSPviewed_label":"Most Viewed","dummy3":null,"mostSPliked_order":"3","mostSPliked_label":"Most Liked","dummy4":null,"mostSPcommented_order":"4","mostSPcommented_label":"Most Commented","dummy5":null,"mostSPrated_order":"5","mostSPrated_label":"Most Rated","dummy6":null,"mostSPfavourite_order":"6","mostSPfavourite_label":"Most Favourite","dummy7":null,"featured_order":"7","featured_label":"Featured","dummy8":null,"sponsored_order":"8","sponsored_label":"Sponsored","dummy9":null,"verified_order":"9","verified_label":"Verified","dummy10":null,"week_order":"10","week_label":"This Week","dummy11":null,"month_order":"11","month_label":"This Month","title":"","nomobile":"0","name":"sesblog.tabbed-widget-blog"}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesbasic.simple-html-block',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"bodysimple":"<div style=\"text-align: center;margin-top:50px; box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(8,32,84,.1);padding-bottom:50px;\">\r\n\t<a class=\"sesblog_landing_link sesblog_welcome_btn sesbasic_animation\" href=\"blogs\/browse\">Read all Posts\r\n<\/a><\/div>\r\n<div style=\"font-size: 34px;margin-bottom: 30px;  margin-top: 30px;text-align: center;\">Verified Blogs on our Community\r\n<\/span><\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
      ));
      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesblog.featured-sponsored-verified-random-blog',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"description":"","category":"0","criteria":"1","order":"","show_criteria":["like","comment","favourite","view","title","by","rating","ratingStar","sponsoredLabel","verifiedLabel","favouriteButton","likeButton","category","socialSharing","creationDate"],"socialshare_enable_plusicon":"1","socialshare_icon_limit":"2","title":"","nomobile":"0","name":"sesblog.featured-sponsored-verified-random-blog"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesbasic.simple-html-block',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"bodysimple":"<div style=\"text-align: center;margin-top:50px; box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(8,32,84,.1);padding-bottom: 50px;\">\r\n\t<a class=\"sesblog_landing_link sesblog_welcome_btn sesbasic_animation\" href=\"blogs\/home\">Explore All Blogs\r\n<\/a><\/div>\r\n<div style=\"font-size: 34px;margin-bottom: 30px;  margin-top: 30px;text-align: center;\">What do you want to read out?\r\n<\/span><\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesblog.blog-category',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"height":"175","width":"191","limit":"12","blog_required":"0","criteria":"admin_order","show_criteria":["title","countBlogs"],"title":"","nomobile":"0","name":"sesblog.blog-category"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesbasic.simple-html-block',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"en_bodysimple":"","bodysimple":"<div style=\"font-size: 34px;  margin-top: 30px;text-align: center;margin-bottom:50px;\">Read our Sponsored Blogs!<\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesblog.featured-sponsored-verified-category-carousel',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"category":"0","criteria":"2","order":"","info":"most_liked","isfullwidth":"0","autoplay":"1","speed":"2000","show_criteria":["title","favouriteButton","likeButton","category","socialSharing","readtime"],"socialshare_enable_plusicon":"1","socialshare_icon_limit":"2","title_truncation":"35","height":"500","limit_data":"10","title":"","nomobile":"0","name":"sesblog.featured-sponsored-verified-category-carousel"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesbasic.simple-html-block',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"bodysimple":"<div style=\"text-align: center;margin-top:50px; box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(8,32,84,.1);padding-bottom:50px;\">\r\n\t<a class=\"sesblog_landing_link sesblog_welcome_btn sesbasic_animation\" href=\"blogs\/categories\">Browse All Categories\r\n<\/a><\/div>\r\n<div style=\"font-size: 34px;margin-bottom: 30px;  margin-top: 30px;text-align: center;\">Meet our Top Bloggers!\r\n<\/span><\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesblog.top-bloggers',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"show_criteria":["count","ownername"],"height":"180","width":"193","showLimitData":"0","limit_data":"6","title":"","nomobile":"0","name":"sesblog.top-bloggers"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sesspectromedia.banner',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetOrder++,
        'params' => '{"is_full":"1","is_pattern":"1","banner_image":"public\/admin\/banner_final.jpg","banner_title":"Start by creating your Unique Blog","title_button_color":"FFFFFF","description":"Publish your personal or professional blogs at your desired date and time!","description_button_color":"FFFFFF","button1":"1","button1_text":"Get Started","button1_text_color":"0295FF","button1_color":"FFFFFF","button1_mouseover_color":"EEEEEE","button1_link":"blogs\/create","button2":"0","button2_text":"Button - 2","button2_text_color":"FFFFFF","button2_color":"0295FF","button2_mouseover_color":"067FDE","button2_link":"","button3":"0","button3_text":"Button - 3","button3_text_color":"FFFFFF","button3_color":"F25B3B","button3_mouseover_color":"EA350F","button3_link":"","height":"400","title":"","nomobile":"0","name":"sesspectromedia.banner"}',
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    if( $this->getRequest()->getParam('format') == 'smoothbox' ) {
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('This Page has been reset successfully.')),
        'smoothboxClose' => true,
      ));
    }
  

  }
  
  protected function changeLanguage($values) {
  
    //START TEXT CHNAGE WORK IN CSV FILE
    $oldSigularWord = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.text.singular', 'blog');
    $oldPluralWord = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.text.plural', 'blogs');
    $newSigularWord = @$values['sesblog_text_singular'] ? @$values['sesblog_text_singular'] : 'blog';
    $newPluralWord = @$values['sesblog_text_plural'] ? @$values['sesblog_text_plural'] : 'blogs';
    $newSigularWordUpper = ucfirst($newSigularWord);
    $newPluralWordUpper = ucfirst($newPluralWord);

    if($newSigularWord != $oldSigularWord && $newPluralWord != $oldPluralWord) {

      $tmp = Engine_Translate_Parser_Csv::parse(APPLICATION_PATH . '/application/languages/en/sesblog.csv', 'null', array('delimiter' => ';','enclosure' => '"'));
      if( !empty($tmp['null']) && is_array($tmp['null']) )
        $inputData = $tmp['null'];
      else
        $inputData = array();

      $OutputData = array();
      $chnagedData = array();
      foreach($inputData as $key => $input) {
        $chnagedData = str_replace(array($oldPluralWord, $oldSigularWord,ucfirst($oldPluralWord),ucfirst($oldSigularWord),strtoupper($oldPluralWord),strtoupper($oldSigularWord)), array($newPluralWord, $newSigularWord, ucfirst($newPluralWord), ucfirst($newSigularWord), strtoupper($newPluralWord), strtoupper($newSigularWord)), $input);
        $OutputData[$key] = $chnagedData;
      }

      $targetFile = APPLICATION_PATH . '/application/languages/en/sesblog.csv';
      if (file_exists($targetFile))
        @unlink($targetFile);

      touch($targetFile);
      chmod($targetFile, 0777);

      $writer = new Engine_Translate_Writer_Csv($targetFile);
      $writer->setTranslations($OutputData);
      $writer->write();
      //END CSV FILE WORK
    }
  }
  
  public function resetPageSettingsAction(){
    
    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle("Reset This Page?");
    $form->setDescription('Are you sure you want to reset this page? Once reset, it will not be undone.');
    $form->submit->setLabel("Reset Page");
    if (!$this->getRequest()->isPost())
      return;
      
    if (!$form->isValid($this->getRequest()->getPost()))
      return;
      
    $page_id = (int) $this->_getParam('page_id');
    $pageName = $this->_getParam('page_name');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    try {
      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");
      include_once APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    if( $this->getRequest()->getParam('format') == 'smoothbox' ) {
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('This Page has been reset successfully.')),
        'smoothboxClose' => true,
      ));
    }
  }
  
  public function extensionsAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_extension');
  }


  public function moduleenableAction() {
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $module = $this->_getParam('modulename');
    $enabled = $this->_getParam('enabled');
    if (!empty($module)) {
      if($module == 'sesblogpackage') {
        $db->query('UPDATE `engine4_core_menuitems` SET `enabled` = "'.$enabled.'" WHERE `engine4_core_menuitems`.`name` = "sesblog_admin_packagesetting";');
      }

      $db->query('UPDATE `engine4_core_modules` SET `enabled` = "'.$enabled.'" WHERE `engine4_core_modules`.`name` = "'.$module.'";');
    }
    $this->_redirect('admin/sesblog/settings/extensions');
  }
}

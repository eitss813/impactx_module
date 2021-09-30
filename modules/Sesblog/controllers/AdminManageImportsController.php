<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: AdminManageImportsController.php  2018-11-30 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_AdminManageImportsController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_manageimport');
  }

  public function importAction() {

    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesblog_Form_Admin_Import_Import();

    if ($this->getRequest()->isPost()) {
        try {
            $csvFile = explode(".", $_FILES['csvfile']['name']);

            if (($csvFile[1] != "csv")) {
                $itemError = Zend_Registry::get('Zend_Translate')->_("Choose only CSV file.");
                $form->addError($itemError);
                return;
            }

            $csv_file = $_FILES['csvfile']['tmp_name']; // specify CSV file path

            $csvfile = fopen($csv_file, 'r');
            $theData = fgets($csvfile);
            $thedata = explode('|',$theData);

            $blog_title = $body = $category_id = $subcat_id = $subsubcat_id = $style = $counter = 0;
            foreach($thedata as $data) {
                //Direct CSV
                if(trim(strtolower($data)) == '[Blog Title]'){
                    $blog_title = $counter;
                } else if(trim(strtolower($data)) == '[Description]'){
                    $body = $counter;
                } else if(trim(strtolower($data)) == '[Category Id]'){
                    $category_id = $counter;
                } else if(trim(strtolower($data)) == '[2nd Category Id]'){
                    $subcat_id = $counter;
                } else if(trim(strtolower($data)) == '[3rd Category Id]'){
                    $subsubcat_id = $counter;
                } else if(trim(strtolower($data)) == '[Blog Style]'){
                    $style = $counter;
                }
                $counter++;
            }

            $i = 0;
            $importedData = array();
            while (!feof($csvfile))
            {
                $csv_data[] = fgets($csvfile, 1024);
                $csv_array = explode("|", $csv_data[$i]);

                if(!count($csv_array))
                    continue;

                if(isset($csv_array[$blog_title]))
                    $importedData[$i]['title'] = @$csv_array[0];

                if(isset($csv_array[$body]))
                    $importedData[$i]['body'] = @$csv_array[1];

                if(isset($csv_array[$category_id]))
                    $importedData[$i]['category_id'] = @$csv_array[2];

                if(isset($csv_array[$subcat_id]))
                    $importedData[$i]['subcat_id'] = @$csv_array[3];

                if(isset($csv_array[$subsubcat_id]))
                    $importedData[$i]['subsubcat_id'] = @$csv_array[4];

                if(isset($csv_array[$style]))
                    $importedData[$i]['style'] = @$csv_array[5];
                $i++;
            }
            fclose($csvfile);

            $values = $form->getValues();

            foreach($importedData as $result) {

                if(isset($result['title']) && !empty($result['title'])) {
                    $values = array_merge($values, $result);
                    $this->saveBlog($values);
                }
            }
            //$db->commit();
        } catch (Exception $e) {
            //$db->rollBack();
            throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('You have successfully imported FAQ.')
        ));
    }
  }

  public function saveBlog($values) {
    
    $roleTable = Engine_Api::_()->getDbtable('roles', 'sesblog');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authApi = Engine_Api::_()->authorization()->getAdapter('levels');
    
    $viewer = $this->view->viewer();
    $values['owner_id'] = $viewer->getIdentity();
    $values['owner_id'] = 'user';
    $values['is_publish'] = 1;

    $blogTable = Engine_Api::_()->getDbTable('blogs', 'sesblog');
    $db = $blogTable->getAdapter();
    $db->beginTransaction();
    try {
    
      $blog = $blogTable->createRow();
      
      if(isset($values['body']))
        $values['readtime'] = Engine_Api::_()->sesblog()->estimatedReadingTime(addslashes($values['body']));

      if (empty($values['category_id']))
          $values['category_id'] = 0;
      if (empty($values['subsubcat_id']))
          $values['subsubcat_id'] = 0;
      if (empty($values['subcat_id']))
          $values['subcat_id'] = 0;
          
      $values['body'] = isset($values['body']) ? addslashes($values['body']) : null;
      
      $values['is_approved'] = $authApi->getAllowed('sesblog_blog', $viewer, 'blog_approve');
      $values['featured'] = $authApi->getAllowed('sesblog_blog', $viewer, 'autofeatured');
      $values['sponsored'] = $authApi->getAllowed('sesblog_blog', $viewer, 'autosponsored');
      $values['verified'] = $authApi->getAllowed('sesblog_blog', $viewer, 'autoverified');
      if (isset($blog->package_id) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage')) {
        $values['package_id'] = Engine_Api::_()->getDbTable('packages', 'sesblogpackage')->getDefaultPackage();
      }
      
      $blog->setFromArray($values);
      if (!isset($values['auth_view'])) {
        $values['auth_view'] = 'everyone';
      }
      $blog->save();
      
      //Roles
      $row = $roleTable->createRow();
      $row->blog_id = $blog->blog_id;
      $row->user_id = $viewer->getIdentity();
      $row->resource_approved = '1';
      $row->save();

      // Auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if (empty($values['auth_view']))
        $values['auth_view'] = 'everyone';
      if (empty($values['auth_comment']))
        $values['auth_comment'] = 'everyone';
      if (empty($values['auth_video']))
        $values['auth_video'] = 'everyone';
      if (empty($values['auth_music']))
        $values['auth_music'] = 'everyone';

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $videoMax = array_search(isset($values['auth_video']) ? $values['auth_video'] : '', $roles);
      $musicMax = array_search(isset($values['auth_music']) ? $values['auth_music'] : '', $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($blog, $role, 'video', ($i <= $videoMax));
        $auth->setAllowed($blog, $role, 'music', ($i <= $musicMax));
      }

      $value = Engine_Api::_()->getDbTable('blogs', 'sesblog')->checkCustomUrl($blog->getSlug());
      if(empty($value))
        $blog->custom_url = $blog->getSlug();
      else
        $blog->custom_url = $blog->getSlug().'_'.$blog->blog_id;
      $blog->save();

      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function downloadAction() {

    $filepath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesblog' . DIRECTORY_SEPARATOR . "settings" .DIRECTORY_SEPARATOR.'default_template.csv';

    //KILL ZEND'S OB
    while (ob_get_level() > 0) {
      ob_end_clean();
    }

    @chmod($filepath, 0777);
    $default_template = '[Blog Title]|[Description]|[Category Id]|[2nd Category Id]|[3rd Category Id]|[Blog Style]';
    $fp = fopen(APPLICATION_PATH . '/temporary/default_template.csv', 'w+');
    fwrite($fp, $default_template);
    fclose($fp);

    $filepath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'. DIRECTORY_SEPARATOR . 'default_template.csv';

    header("Content-Disposition: attachment; filename=" . urlencode(basename($filepath)), true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Transfer-Encoding: Binary", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    header("Content-Length: " . filesize($filepath), true);
    readfile("$filepath");
    exit();
    return;
  }
}

  <?php
class Sesblog_ImportController extends Core_Controller_Action_Standard {
  public function indexAction() {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('sesblog_blog', null, 'create')->isValid()) return;
    $this->view->form = $form = new Sesblog_Form_Import();
     // If not post or form not valid, return
    if( !$this->getRequest()->isPost() )
    return;
    if( !$form->isValid($this->getRequest()->getPost()) )
    return;
    if (empty($values['file_data'])) {
        $form->addError($this->view->translate("Blog XML File * Please complete this field - it is required."));
        return;
    }
    $file = $form->getElement ( 'file_data' );
    $path = $file->getDestination () . DIRECTORY_SEPARATOR . $file->getValue ();
    $xml = simplexml_load_file($path);
    if($_POST['import_type'] == 1) {
      $posts = array();
      if (empty($_FILES['file_data']['name'])) {
        $form->addError('Please select blogger xml file you want to import here.');
        return;
      }
      $xml =  $xml->entry;
      foreach( $xml as $row ) {
        $posts[] = array(
          "title"=>$row->title,
          "content"=>$row->content,
          "import_type" => 1,
        );
      }
      $this->importBlogs($posts);
    }elseif($_POST['import_type'] == 2) {
      $posts = array();
      if (empty($_FILES['file_data']['name'])) {
        $form->addError('Please select wordpress xml file you want to import here.');
        return;
      }
      foreach($xml->channel->item as $item) {
        $content = $item->children('http://purl.org/rss/1.0/modules/content/');
        $posts[] = array(
        "title"=>$item->title,
        "content"=>$content->encoded,
        "import_type" => 2,
        );
      }
      $this->importBlogs($posts);
    }elseif($_POST['import_type'] == 3) {
      if(empty($_POST['user_name'])) {
        $form->addError ('Username can not be empty.');
        return;
      }
      $i = 0;
      $posts = array();
      $finish = true;
      $counter = 1;
      $arrayCounter = 0;
      do {
        $fileUrl = "http://".$_POST['user_name'].".tumblr.com/api/read/json?start=".$i."&num=50";
        $content = @file_get_contents ( $fileUrl );
        $subContent  = preg_replace('#var tumblr_api_read = (.*);#','$1',$content);;
        $data = json_decode($subContent, true);
        foreach($data['posts'] as $item) {
          $posts[$arrayCounter] = array(
            "title"=>$item['regular-title'],
            "content"=>$item['regular-body'],
            "import_type" => 3,
          );
          $arrayCounter++;
        }
        $total = $data['posts-total'];
        $i = ($arrayCounter);
        if(ceil($total/50) <= $counter) {
          $finish = false;
        }
        ++$counter;
      }
      while($finish);
      $this->importBlogs($posts);
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'),'sesblog_general',true);
  }
  public function importBlogs($posts) {
    foreach($posts as $post) {
      $title = $post['title'];
      if(!$title)
        continue;
      // Process
      $table = Engine_Api::_()->getDbtable('blogs', 'sesblog');
      $db = $table->getAdapter();
      $db->beginTransaction();
      try {
        // Create sesblog
        $viewer = Engine_Api::_()->user()->getViewer();
        $values = array();
        $sesblog = $table->createRow();
        $sesblog->ip_address = $_SERVER['REMOTE_ADDR'];
        $sesblog->title = htmlspecialchars_decode($title);
        $sesblog->body = $post['content'];
        $sesblog->owner_type = $viewer->getType();
        $sesblog->owner_id = $viewer->getIdentity();
        $sesblog->category_id = 0;
        $sesblog->subcat_id = 0;
        $sesblog->subsubcat_id = 0;
        $sesblog->is_approved = 1;
        $sesblog->seo_title = $title;
        if(isset($sesblog->package_id) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') ){
          $package_id = Engine_Api::_()->getDbTable('packages','sesblogpackage')->getDefaultPackage();
          $sesblog->package_id = $package_id;
        }
        $sesblog->save();
        $blog_id = $sesblog->blog_id;
        $sesblog->custom_url = $blog_id;
        $roleTable = Engine_Api::_()->getDbtable('roles', 'sesblog');
        $row = $roleTable->createRow();
        $row->blog_id = $blog_id;
        $row->user_id = $viewer->getIdentity();
        $row->save();
        // Auth
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $values['auth_view'] = 'everyone';
        $values['auth_comment'] = 'everyone';
        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);
        $videoMax = array_search(isset($values['auth_video']) ? $values['auth_video']: '', $roles);
        $musicMax = array_search(isset($values['auth_music']) ? $values['auth_music']: '', $roles);
        foreach( $roles as $i => $role ) {
          $auth->setAllowed($sesblog, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($sesblog, $role, 'comment', ($i <= $commentMax));
          $auth->setAllowed($sesblog, $role, 'video', ($i <= $videoMax));
          $auth->setAllowed($sesblog, $role, 'music', ($i <= $musicMax));
        }
        $sesblog->save();
        // Add activity only if sesblog is published
  //       if( $values['draft'] == 0 && $values['is_approved'] == 1 && (!$sesblog->publish_date || strtotime($sesblog->publish_date) <= time())) {
  //         $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sesblog, 'sesblog_new');
  //         // make sure action exists before attaching the sesblog to the activity
  //         if( $action ) {
  //           Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sesblog);
  //         }
  //         //Send notifications for subscribers
  //       	Engine_Api::_()->getDbtable('subscriptions', 'sesblog')->sendNotifications($sesblog);
  //       	$sesblog->is_publish = 1;
  //       	$sesblog->save();
  // 			}
        // Commit
        $db->commit();
      }
      catch( Exception $e ) {
        //silence and continue;
       //$db->rollBack();
      //throw $e;
      }
    }
  }
}

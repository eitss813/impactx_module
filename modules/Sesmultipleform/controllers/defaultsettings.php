<?php

$db = Zend_Db_Table_Abstract::getDefaultAdapter();
$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("sesmultipleform_email_simple", "sesmultipleform", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_name],[sender_email],[sender_link],[sender_photo],[message],[subcategory_name],[category_name],[profile_field]"),
("sesmultipleform_admin_reply", "sesmultipleform", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subject],[body]"),
("sesmultipleform_admin_reply_nonlogged", "sesmultipleform", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subject],[body]"),
("sesmultipleform_admin_confirmation", "sesmultipleform", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subject],[body]");');

$tableUser = Engine_Api::_()->getItemtable('user');
$select = $tableUser->select()->where('level_id = 1 || level_id = 2');
$users = $tableUser->fetchAll($select);
if(count($users)) {
	$counter = 1;
	foreach($users as $user){
		if($counter == 1)
			$active = 1;
		else
			$active = 0;
		$db->query("INSERT IGNORE INTO `engine4_sesmultipleform_keycontacts`(`keycontact_id`, `user_id`, `designation`, `active`, `order`, `creation_date`, `modified_date`) VALUES ('".$counter."',".$user->user_id.",'Manager','".$active."','".$counter."','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."')");
		$counter++;
	}
}

if(isset($values['sesmultipleform_footer_widgets']) && count($values['sesmultipleform_footer_widgets'])){
	//place widget of contactus
	$selectFooterMainParentId = $db->query("SELECT content_id FROM engine4_core_content WHERE page_id = 2 AND parent_content_id IS NULL")->fetchAll();
	 if(isset($selectFooterMainParentId[0]['content_id'])){
		 $parent_id = $selectFooterMainParentId[0]['content_id'];
			if(in_array('contactus',$values['sesmultipleform_footer_widgets'])){
				 //Insert content
				$db->insert('engine4_core_content', array(
						'type' => 'widget',
						'name' => 'sesmultipleform.popup',
						'page_id' => 2,
						'parent_content_id' => $parent_id,
						'order' => 999,
						'params'=>'{"buttontext":"Contact Us","formtype":"1","position":"1","buttoncolor":"ED895C","textcolor":"FFFFFF","margin":"30","margintype":"per","hideform":"1","closepopup":"1","texthovercolor":"E74802","redirect":"","title":"","nomobile":"0","name":"sesmultipleform.popup"}'
				));
			}
			//place widget of feedback
			if(in_array('feedback',$values['sesmultipleform_footer_widgets'])){
				 //Insert content
				$db->insert('engine4_core_content', array(
						'type' => 'widget',
						'name' => 'sesmultipleform.popup',
						'page_id' => 2,
						'parent_content_id' => $parent_id,
						'order' => 999,
						'params'=>'{"buttontext":"Feedback","formtype":"2","position":"1","buttoncolor":"005E99","textcolor":"FFFFFF","margin":"55","margintype":"per","hideform":"1","closepopup":"1","texthovercolor":"003F66","redirect":"","title":"","nomobile":"0","name":"sesmultipleform.popup"}'
				));
			}
	 }
}

//Aboutus page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesmultipleform_index_aboutus')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {

  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesmultipleform_index_aboutus',
    'displayname' => 'SES - All in One Multiple Forms Plugin - About Us',
    'title' => 'About Us Page',
    'description' => 'This is about us page.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  
	//Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  //Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  //Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();
  //Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  //Insert main-right
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();
  //Insert menu
	
	$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesmultipleform' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "about-us.jpg";
  if (is_file($PathFile)) {
    if (!file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin')) {
      mkdir(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin', 0777, true);
    }
    copy($PathFile, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin/about-us.jpg');
	}
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmultipleform.banner',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 1,
			'params'=>'{"banner_image":"public\/admin\/about-us.jpg","banner_title":"","title_button_color":"","description":"","description_button_color":"FFFFFF","fullwidth":"0","button1":"0","button1_text":"Button - 1","button1_text_color":"0295FF","button1_color":"FFFFFF","button1_mouseover_color":"EEEEEE","button1_link":"","button2":"0","button2_text":"Button - 2","button2_text_color":"FFFFFF","button2_color":"0295FF","button2_mouseover_color":"067FDE","button2_link":"","button3":"0","button3_text":"Button - 3","button3_text_color":"FFFFFF","button3_color":"F25B3B","button3_mouseover_color":"EA350F","button3_link":"","height":"350","title":"About us","nomobile":"0","name":"sesmultipleform.banner"}',
  ));
  //Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 1,
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesmultipleform.list-key-contacts',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 3,
      'params' => '{"title":"Key Contacts","listtype":"random","height":"200","width":"200","nonloggined":"1","emailshow":"1","blockposition":"1","itemCountPerPage":"3","nomobile":"0","name":"sesmultipleform.list-key-contacts"}',
  ));				
}
		
//Change System Mode of Website "Production Mode" to "Development Mode"
Engine_Api::_()->sesbasic()->changeEnvironmentMode($values['system_mode']);

$db->query('ALTER TABLE `engine4_sesmultipleform_entry_fields_meta` ADD `icon` TEXT NULL DEFAULT NULL;');

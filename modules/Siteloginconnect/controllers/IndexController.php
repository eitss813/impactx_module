<?php

class Siteloginconnect_IndexController extends Core_Controller_Action_Standard
{ 
  public function init() {
        // Can specifiy custom id
      $id = $this->_getParam('id', null);
      $subject = null;
      if( null === $id ) {
        $subject = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->core()->setSubject($subject);
      } else {
        $subject = Engine_Api::_()->getItem('user', $id);
        Engine_Api::_()->core()->setSubject($subject);
      }
  }

  public function indexAction()
  {
    $this->view->already_integrated=$this->_getParam('already_integrated');
    $this->view->socialsite=$this->_getParam('social_site');
    // Check viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setAuthParams(
      $subject, null, 'edit'
    );

    // Render
    $this->_helper->content
      // ->setNoRender()
      ->setEnabled()
    ;
    $translate = Zend_Registry::get('Zend_Translate');
    $this->view->user = $user = $subject; 
    $socialsiteslink = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteloginconnect.global.syncaccount',array());
    
    $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $domainUrl = (_ENGINE_SSL ? 'https://' : 'http://')
                . $_SERVER['HTTP_HOST'];
    if (isset($baseParentUrl) && !empty($baseParentUrl)) {
        $domainUrl = $domainUrl . $baseParentUrl;
    }
    foreach ($socialsiteslink as $key => $socialsite) {
      $siteintegtration=$socialsite.'IntegrationEnabled';
      if($socialsite == 'facebook' || $socialsite == 'twitter') {                
            $siteEnabled=Engine_Api::_()->sitelogin()->$siteintegtration();
      } else {                
            $siteEnabled = Engine_Api::_()->getDbtable($socialsite, 'sitelogin')->$siteintegtration();
      } 
      
      if (!empty($siteEnabled)) { 
        $href[$socialsite] = $domainUrl . "/siteloginconnect/sync/".$socialsite;  
      }
    }

    $this->view->socialsitehref= $href;   

  }

  public function selectdataAction() {

      $viewer = Engine_Api::_()->user()->getViewer();
      $userdetails=$this->_getParam('userdetails', null);
      $socialsite=$this->_getParam('social_site', null);
      if(empty($userdetails) || empty($socialsite)) {
          return;
      }
      $socialuserdetails = urldecode($userdetails);
      $socialuserdetails=json_decode($socialuserdetails);
      $this->view->socialsite=$socialsite;
        // Member type
      $subject = Engine_Api::_()->core()->getSubject();
      $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);

      if( !empty($fieldsByAlias['profile_type']) ) {
        $optionId = $fieldsByAlias['profile_type']->getValue($subject);
        if( $optionId ) {
            $optionObj = Engine_Api::_()->fields()
              ->getFieldsOptions($subject)
              ->getRowMatching('option_id', $optionId->value);
            if( $optionObj ) {
                $this->view->memberType = $optionObj->label;
                $profile_type=$optionObj->option_id;
            }
        }
      }

      $maps = Engine_Api::_()->siteloginconnect()->getProfileFieldMaps();

      $mapTable = Engine_Api::_()->getDbTable('maps', 'siteloginconnect');

      $result=$mapTable->fetchAll($mapTable->select()
                          ->where("social_site = ?",$socialsite)
                          ->where("profile_type= ?",$profile_type))
                        ->toarray();
               
      $fieldarray=array(); 
      $social_site_field=array();
      foreach ($maps['fieldsMap'][$profile_type] as $key => $fields) {
        
          foreach ($result as $value) {
          
              if( ($fields['field_id']==$value['field_id'])  && ($fields['profile_type']==$value['profile_type']) ) {

                  $fieldarray[]=array('profile_type'=>$fields['profile_type'],
                                      'field_id'=>$fields['field_id'],
                                      'label' => $fields['label'],
                                      'option' => $value['social_site_field'],);
                  $social_site_field[]=$value['social_site_field'];
              }            
          }
      }


      if(empty($fieldarray)) {
        return;
      }
      $fieldarray = $this->get_unique_associate_array($fieldarray);

      $this->view->fieldarray =  $fieldarray;

      $social_site_fields_value =array();

      if($socialsite=='linkedin') {
        foreach ($social_site_field as $value) {
          if($value=='location_country_code') {
            $social_site_fields_value[$value] = $socialuserdetails->location->country->code;
            continue;
          }
          if($value=='location_country_name') {
            $social_site_fields_value[$value] = $socialuserdetails->location->name;
            continue;
          }
          $social_site_fields_value[$value] = $socialuserdetails->$value;
        }
        if (isset($socialuserdetails->pictureUrls) && !empty($socialuserdetails->pictureUrls)) {
            $originalImageUrls = get_object_vars($socialuserdetails->pictureUrls);
            if (!empty($originalImageUrls)) {
                $image = isset($originalImageUrls['values'][0]) ? $originalImageUrls['values'][0] : 0;
            }
        }
        if(!empty($image)){
          $this->view->photo_url=$image;
        }
      }

      if($socialsite=='facebook') {
          foreach ($social_site_field as $value) {
            if($value=='age_range') {
              $social_site_fields_value[$value] = $socialuserdetails->age_range->min;
              continue;
            }
            $social_site_fields_value[$value] = $socialuserdetails->$value;
          }
            // Fetch image from Facebook
            $image = "https://graph.facebook.com/" 
                        . $socialuserdetails->id 
                        . "/picture?type=large"
                        ;          
            $this->view->photo_url=$image;
      }    

      if($socialsite=='instagram') {
        foreach ($social_site_field as $value) {
          $social_site_fields_value[$value] = $socialuserdetails->data->$value;
        }
        //instagram Image
        if (isset($socialuserdetails->data->profile_picture) && !empty($socialuserdetails->data->profile_picture)) {
            $image = isset($socialuserdetails->data->profile_picture) ? $socialuserdetails->data->profile_picture : 0;
        }
        if(!empty($image)){
          $this->view->photo_url=$image;
        }
      }
      
      if($socialsite=='twitter') {
          foreach ($social_site_field as $value) {
            $social_site_fields_value[$value] = $socialuserdetails->$value;
          }
          //instagram Image
          if (isset($socialuserdetails->profile_image_url) && !empty($socialuserdetails->profile_image_url)) {
              $image = isset($socialuserdetails->profile_image_url) ? $socialuserdetails->profile_image_url : 0;
          }
          if(!empty($image)) {
            $this->view->photo_url=$image;
          }
      }


      $this->view->social_site_fields_value= $social_site_fields_value;
      foreach ($fieldarray as $key => $value) {
         $FieldValuePair[$value['field_id']]  = $social_site_fields_value[$value['option']]; 
      }



      if( $this->getRequest()->isPost() ) {
          $file_id;
          $post = $this->getRequest()->getPost();
          $db = Engine_Db_Table::getDefaultAdapter();
          $queries = [];
          foreach ($post as $key => $value) {
              if($key=='submit')
                continue; 
              if($key=='photo_url'&& !empty($image)) {
                Engine_Api::_()->sitelogin()->fetchImage($image,$viewer);
                continue;
              }
              $field_id = explode('_',$key)[1];
              $value = $FieldValuePair[$field_id];
              $delSql = " DELETE FROM `engine4_user_fields_values` 
                WHERE 
                `field_id` = {$field_id} AND 
                `item_id` = {$viewer->getIdentity()}
              ";

              $query = "SELECT `type` FROM `engine4_user_fields_meta` WHERE `field_id` = {$field_id}";
              $fieldMeta = $db->query($query)->fetch();
              if(in_array($fieldMeta["type"], array("gender","select"))) {
     
                $optionsSql = "SELECT `label` as `label`,`option_id` FROM `engine4_user_fields_options` WHERE `field_id` = {$field_id}";
                $options = $db->query($optionsSql)->fetchAll();

                foreach( $options as $option ) {
                  if(strtolower($option["label"]) == strtolower($value)) {
                    $value = $option["option_id"];
                  }
                }
                
              }
              $sql = "INSERT IGNORE INTO `engine4_user_fields_values` 
              (`value`,`field_id`,`item_id`) VALUES
              ('{$value}', {$field_id}, {$viewer->getIdentity()})
              ";

              $queries[] = $delSql; 
              $queries[] = $sql;
       
          }

          foreach ($queries as $sql) {
            try{
              $db->query($sql);
            } catch (Exception $e) {
              throw $e->getMessage() . ' -- '. $sql;
            }
          } 
          $user = Engine_Api::_()->user()->getViewer();
          $aliasValues = $this->getaliasvalues();
          $user ->setDisplayName($aliasValues);
          $user ->save(); 
          $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
          $url = $view->baseUrl() . '/profile/' . $user ->username ;
          return $this->_redirect($url, array('prependBase' => false));

      } 

  }

  function get_unique_associate_array($array) {
                $serialized_array = array_map("serialize", $array);
                foreach ($serialized_array as $key => $val) {
                     $result[$val] = true;
                }
                return array_map("unserialize", (array_keys($result)));
  }

  function getaliasvalues(){
    $user = Engine_Api::_()->user()->getViewer();
    $valueUserTable= Engine_Api::_()->fields()->getTable('user','values');
    $metaUserTable = Engine_Api::_()->fields()->getTable('user','meta');
    $db = Engine_Db_Table::getDefaultAdapter();
    $select= $db->select()
                 ->from( $valueUserTable->info("name"), array("value"))
                 ->join( $metaUserTable->info("name"), "`{$metaUserTable->info("name")}`.`field_id` = `{$valueUserTable->info("name")}`.`field_id`", array("type"))
                 ->where("`{$valueUserTable->info("name")}`.`item_id` = ?",$user->getIdentity());
    $result=$db->query($select)->fetchAll();  

    $aliasarray;
    foreach ($result as $key => $value) {
      $aliasarray[$value['type']]=$value['value'];
    }      
    return $aliasarray;
          
  }

}

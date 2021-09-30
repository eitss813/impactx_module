<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    IndexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_ProfiletabController extends Siteapi_Controller_Action_Standard {

    public function init() {
//SET THE SUBJECT

        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();


        if (0 !== ($project_id = (int) $this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
            Engine_Api::_()->sitecrowdfunding()->setPaymentFlag($project_id);
        }
    }

     /*
     * Project Infromation .
     * Return Json
     */
    
    public function informationAction() {
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError("no_record");
        }
        $bodyParams = array();
//GET PROJECT SUBJECT
        try {


            $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

            $bodyParams['Basic Information']['Started By'] = $project->getOwner()->getTitle();
            $bodyParams['Basic Information']['Published On'] = date('M d, Y', strtotime($project->funding_start_date));
            $bodyParams['Basic Information']['Funding Ends'] = date('M d, Y', strtotime($project->fuunding_end_date));
            $bodyParams['Basic Information']['Backers'] = $project->backer_count;
            $bodyParams['Basic Information']['Likes'] = $project->like_count;
            $bodyParams['Basic Information']['Description'] = $project->description;
            $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $project->category_id);
            if (!empty($category)) {
                $bodyParams['Basic Information']['Category'] = $category->category_name;
            }

            $fundedAmount = $project->getFundedAmount();
            $fundedRatio = $project->getFundedRatio();
            if((((_CLIENT_TYPE == 'android') && _ANDROID_VERSION >= '3.5') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '2.6.1'))){
                $fundedAmount = Engine_Api::_()->getApi('Siteapi_Core', 'sitemulticurrency')->getPriceString($fundedAmount, $priceOnly);
            }
            else {
                $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
            }
            $days = $project->getRemainingDays();
            $bodyParams['Basic Information']['Funded'] = $fundedRatio;
            $bodyParams['Basic Information']['Backed'] = $fundedAmount;
            $bodyParams['Basic Information']['Remainig Days'] = $days;
            $profile_info = array();
            if (isset($project->profile_type) && !empty($project->profile_type)) {
                $profile_info = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getInformation($project, 'sitecrowdfunding_project');
                if (count($profile_info) > 0)
                    $bodyParams['Profile Information'] = $profile_info;
            }

            if (isset($_REQUEST['field_order']) && !empty($_REQUEST['field_order'])) {
                foreach ($bodyParams as $key => $value) {
                    $bodyParams[$key] = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($value);
                }
                $bodyParams = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($bodyParams);
            }

            $this->respondWithSuccess($bodyParams);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * User Infromation .
     * Return Json
     */
    public function aboutYouAction() {
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError("no_record");
        }
        try{
            
        
        $project = Engine_Api::_()->core()->getSubject();
        $owner = $project->getOwner();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerIdentity = $viewer->getIdentity();
        if (empty($viewerIdentity)) {
            $this->respondWithError("no_record");
        }
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            $this->respondWithError("no_record");
        }
        $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
        $user_id = $project->owner_id;
        $select = $tableUserInfo->select()->where('user_id = ?', $user_id);
        $user_info = $tableUserInfo->fetchRow($select);
        if ($this->getRequest()->isGet()) {
            $form = new Sitecrowdfunding_Form_AboutYou();
            $AboutForm = Engine_Api::_()->getApi('Form', 'siteapi')->getForm($form);
            $response['form'] = $AboutForm;
            if (!empty($user_info)) {
               $userInfo = $this-> _removeNullValue($user_info->toArray());
                $response['formValues'] = $userInfo;
                $response['editForm'] = $userInfo;
            } else {
                $response['formValues']['email'] = $owner->email;
            }
            $this->respondWithSuccess($response);
        }

        $values = $_REQUEST;
        if (empty($user_info)) {
            $user_info = $tableUserInfo->createRow();
            $user_info->user_id = $user_id;
            $user_info->save();
        }
        $isValidate=array();
        $validationMessage = $this->socialMediaValidation($values);
           if(is_array($validationMessage) && count($validationMessage)>0) {
               $this->respondWithValidationError('validation_fail', $validationMessage);
           }    
        $user_info->setFromArray($values);
        $user_info->save();
        $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
           $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * project Overview .
     * Return Json
     */
    
    public function overviewAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError("no_record");
        }
        $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $bodyParams = array();
        try {
            $params = array();
            $params = $this->_getAllParams();
            $tab_info = $this->_getParam('tab_info',null);
            $params['resource_id'] = $project->project_id;
            $params['resource_type'] = $project->getType();
            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
            if ($this->getRequest()->isGet()) {
                $overview = $tableOtherinfo->getColumnValue($project->getIdentity(), 'overview');
                $response = array();
                if(empty($tab_info)){
                $form = array();
                $form[] = array(
                    "name" => "overview",
                    "type" => "Textarea",
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Overview"),
                );
                $response['form'] = $form;
                $response['formValues']['overview'] = $overview;
                $response['editForm']['overview'] = $overview;
                }
                else{
                   $response =$overview;
                }
                $this->respondWithSuccess($response);
            }

            // If method not Post or form not valid , Return
            if ($this->getRequest()->isPost()) {
                $row = $tableOtherinfo->getOtherinfo($project->project_id);
                $row->overview = $params['overview'];
                $row->save();
                $this->successResponseNoContent('no_content', true);
            }
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

     /*
     * project Backers details .
     * Return Json
     */
    public function backersAction() {
//DONT RENDER IF VEWER IS EMPTY
        $params = array();
        $response=array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            $this->respondWithError("unauthorized", "You are logout user");
        }
//DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->respondWithError("unauthorized");
        }

        try {


//GET LIST SUBJECT
            $subject = Engine_Api::_()->core()->getSubject("sitecrowdfunding_project");

            $resource_type = $subject->getType();
            $resource_id = $subject->getIdentity();

            $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
            
            $params['project_id'] = $resource_id;
            $paginator = $backersTable->getBackersPaginator($params);
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));
            $paginator->setItemCountPerPage($this->_getParam('limit', 20));
            $response['totalItemCount'] = $paginator->getTotalItemCount();
            foreach ($paginator as $backer) {
                $owner = Engine_Api::_()->getItem('user', $backer->user_id);
                $backerBrowse = $backer->toArray();
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($owner);
                if (!$backer->is_private_backing)
                    $backerBrowse['onwer_title'] = $owner->getTitle();
                else
                    $backerBrowse['onwer_title'] = $this->translate('Anonymous');
                unset($backerBrowse['ip_address']);
                unset($backerBrowse['gateway_profile_id']);
                unset($backerBrowse['gateway_type']);

                if (!empty($getContentImages) && !$backer->is_private_backing)
                    $backerBrowse = array_merge($backerBrowse, $getContentImages);
                if((((_CLIENT_TYPE == 'android') && _ANDROID_VERSION >= '3.5') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '2.6.1'))){
                    $backerBrowse['amount'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitemulticurrency')->getPriceString($backer->amount,1);
                }
                else {
                    $backerBrowse['amount'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount);
                }
                $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                $backerBrowse['default'] = $backerBrowse['amount'];
                $backerBrowse['displayname'] =  $backerBrowse['onwer_title'];
                $backerBrowse['menus'] = array(
                    "default" => $backerBrowse['amount']
                );
                $tempBody[] = $backerBrowse;
            }
            $response['response'] = $tempBody;
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

         /*
     * project Photos .
     * Return Json
     */
    public function photoAction() {
        $response=array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            $this->respondWithError("unauthorized", "You are logout user");
        }
//DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->respondWithError("unauthorized");
        }

        try {

//GET LIST SUBJECT
            $project = Engine_Api::_()->core()->getSubject("sitecrowdfunding_project");
            $params = array();
            $album = $project->getSingletonAlbum();
            $paginator = $album->getCollectiblesPaginator();
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));
            $paginator->setItemCountPerPage($this->_getParam('limit', 20));
            $response['totalItemCount'] = $paginator->getTotalItemCount();
            $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "photo");
            foreach ($paginator as $photo) {
                $tempphoto = array();
                $tempPhoto = $photo->toArray();
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
                if (!empty($getContentImages))
                    $tempPhoto = array_merge($tempPhoto, $getContentImages);

                $tempPhoto["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($photo);
                $tempPhoto["canLike"] = $tempPhoto["canComment"] = $photo->authorization()->isAllowed($viewer, 'comment');
                // Getting like count.
                $tempPhoto["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($photo);
                $temparray[] = $tempPhoto;
            }
            $response['response'] = $temparray;
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

     /*
     * Upload photo in Project .
     * Return Json
     */
    function uploadPhotoAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $project_id = $this->_getParam('project_id', 0);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            $this->respondWithError("no_record");
        }
        $can_edit = $project->authorization()->isAllowed($viewer, "edit");

        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $photoCount = Engine_Api::_()->getItem('sitecrowdfunding_package', $project->package_id)->photo_count;
            $paginator = $project->getSingletonAlbum()->getCollectiblesPaginator();
            if (Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "photo")) {
                $allowed_upload_photo = 1;
                if (empty($photoCount))
                    $allowed_upload_photo = 1;
                elseif ($photoCount <= $paginator->getTotalItemCount()) {
                    $allowed_upload_photo = 0;
                }
            } else {
                $allowed_upload_photo = 0;
            }
        } else {//AUTHORIZATION CHECK
            $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "photo");
        }

        if (empty($allowed_upload_photo)) {
            $this->respondWithError("unauthorized", " Maximum photo upload limit has been exceeded or you don't have permission to upload photo.");
        }

        //AUTHORIZATION CHECK
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        if (!isset($_FILES['photo']) || !is_uploaded_file($_FILES['photo']['tmp_name'])) {
            $this->respondWithError("unauthorized", "Invalid Upload");
        }

        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitecrowdfunding');
        $db = $tablePhoto->getAdapter();
        $db->beginTransaction();
        try {
            $photo = $project->setPhoto($_FILES['photo'],array("setProjectMainPhoto"=>1));

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }
    
   private function _removeNullValue($data=array()){
       $finalData= array();
       foreach($data as $key=>$value){
           if(!isset($data[$key]))
           $finalData[$key] = '';
           else{
               $finalData[$key] = $value;
           }
       }
       return $finalData;
   }

   public function socialMediaValidation($socailMedia){
       $validationMessage=array();
       foreach($socailMedia as $key=>$URL){
         if(empty($socailMedia[$key]) ||!in_array($key,array("vimeo_profile_url","youtube_profile_url","twitter_profile_url","facebook_profile_url","website_url"))){
            continue;
         }
     
            if (!filter_var($URL, FILTER_VALIDATE_URL)) {
               $validationMessage[$key] = $this->translate('Please enter a valid  url.');
            }
           
       }
       return $validationMessage;
   }
}

?>
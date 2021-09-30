<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SitecrowdfundingShareLinks.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_View_Helper_sitecrowdfundingCustomBtn extends Zend_View_Helper_Abstract {

    public function sitecrowdfundingCustomBtn($subject, $donateText = 'Donate',$params = array(), $showText = false) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $subject->getHref());
        $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref();
        $urlShare = $this->view->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'not_parent_refresh' => 1, 'format' => 'smoothbox'), 'default', true);
        $baseUrl = $view->baseUrl();
        $imgpath = $baseUrl . "/application/modules/Sitecrowdfunding/externals/images/webshare.png";
        $imgpath2 = $baseUrl . "/application/modules/Sitecrowdfunding/externals/images/custom-share-icon.png";
        $resource_id = $subject->getIdentity();
        $resource_type = $subject->getType();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

      //  $defaultBackTitle =  'Donate' ;

      //  $project_id = $subject->getIdentity();
     //   $backTitle = $defaultBackTitle;
      //  $project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $project_id = $subject->getIdentity();

        $project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //$defaultBackTitle =  $project->payment_action_label ;
        $backTitle = $donateText;

        ?>


        <div class="seao_share_links">
            <div class="social_share_wrap">
                <link href="<?php echo $baseUrl.'application/modules/Sitecrowdfunding/externals/scripts/simple-modal/assets/css/simplemodal.css' ?>" rel="stylesheet">
                <script src="<?php echo $baseUrl.'application/modules/Sitecrowdfunding/externals/scripts/simple-modal/simple-modal.js' ?>"></script>
                <script>
                    function shwMsg(msg){
                        //alert(msg)
                        var SM = new SimpleModal({
                            "btn_ok":"Close",
                            "width": 300,
                        });

                        SM.show({
                            "title": 'Donation' ,
                            "contents": msg
                        });
                    }
                </script>
                <style>
                    a:disabled,a[disabled]{
                        background-color:#888 !important;
                    }
                    .custom_common_btn{
                        border-radius: 0 !important;
                        width: 90px !important;
                        height: 30px !important;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-left: 10px !important;
                        margin-right: 10px !important;
                    }
                    .seao_icon_shareicon::before{
                        font-family: 'FontAwesome';
                        font-weight: normal;
                        font-style: normal;
                        font-size: 16px;
                        line-height: 16px;
                        margin-right: 7px;
                    }
                    .main_project_info_share_btns .main_project_info_socialshare_btns .seao_share_links .social_share_wrap > a{
                        padding-top: 8px !important;
                    }
                    .seao_icon_shareicon::before {content: "\\f1e0";}
                </style>

                <?php if (in_array('facebook', $params)) : ?>
                    <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $urlencode; ?>" class="seao_icon_facebook fb_icon" title="<?php echo $view->translate("Facebook"); ?>"></a>
                <?php endif; ?>
                <?php if (in_array('twitter', $params)) : ?>
                    <a href="https://twitter.com/share?text=<?php echo $subject->getTitle(); ?>" target="_blank" class="seao_icon_twitter tt_icon" title="Twitter"></a>
                <?php endif; ?>
                <?php if (in_array('linkedin', $params)) : ?>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $object_link; ?>" target="_blank" class="seao_icon_linkedin li_icon" title="Linkedin"></a>
                <?php endif; ?>
                <?php if (in_array('googleplus', $params)) : ?>
                    <a href="https://plus.google.com/share?url=<?php echo $urlencode; ?>&t=<?php echo $subject->getTitle(); ?>" target="_blank" class="seao_icon_google_plus gp_icon" title="Google+"></a>
                <?php endif; ?>
                <?php if (in_array('community', $params)) : ?>
                  <!--  <a style="padding-top: 7px !important;" href="<?php echo $urlShare; ?>" class = 'smoothbox' title="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $view->translate('_SITE_TITLE')) ?>">
                        <img class="icon" src="<?php echo $imgpath2; ?>" style="width:35px;"/>
                    </a> -->
                    <a href="/projects/get-link/subject/sitecrowdfunding_project_<?php echo $project_id ?>" class="smoothbox" title="ImpactX" style="user-select: auto;">
                        <img class="icon" src="/public/sitecrowdfunding_project/copy-link.png" style="width: 35px; user-select: auto;">
                    </a>

                <?php endif; ?>

                <?php
                if (in_array('like', $params) && $viewer_id) :
                    ?>
                    <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($resource_type, $resource_id); ?>


                    <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');"  id="<?php echo $resource_type; ?>_unlikes_<?php echo $resource_id; ?>" style ='display:<?php echo $hasLike ? "inline-block" : "none" ?>' class="seaocore_icon_dislike <?php echo $resource_type; ?>_unlikes_<?php echo $resource_id; ?>" title="<?php echo $view->translate("Unlike"); ?>">
                        <?php if ($showText) : ?>
                            <?php echo $view->translate("Unlike"); ?>
                        <?php endif; ?>
                    </a>


                    <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $resource_type; ?>_most_likes_<?php echo $resource_id; ?>" style ='display:<?php echo empty($hasLike) ? "inline-block" : "none" ?>' class="seaocore_icon_like <?php echo $resource_type; ?>_most_likes_<?php echo $resource_id; ?>" title="<?php echo $view->translate("Like"); ?>">
                        <?php if ($showText) : ?>
                            <?php echo $view->translate("Like"); ?>
                        <?php endif; ?>
                    </a>


                    <input type ="hidden" id = "<?php echo $resource_type; ?>_like_<?php echo $resource_id; ?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] : 0; ?>' />
                <?php endif; ?>

                <!-- todo: Naaziya alert if not logged -->
                <?php if (in_array('favourite', $params)) : ?>
                    <?php $hasFavourite = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite($resource_type, $resource_id); ?>

                    <?php $unfavourites = $resource_type . '_unfavourites_' . $resource_id ?>
                    <?php $favourites = $resource_type . '_most_favourites_' . $resource_id ?>
                    <?php $fav = $resource_type . '_favourite_' . $resource_id; ?>

                    <?php if($viewer_id): ?>

                        <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $unfavourites; ?>" style ='display:<?php echo $hasFavourite ? "flex" : "none" ?>'  class="custom_common_btn project_view_unfollow_btn common_btn <?php echo $unfavourites; ?>" title="<?php echo $view->translate("Unfollow"); ?>">
                            <?php echo $view->translate("Unfollow"); ?>
                        </a>

                        <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $favourites; ?>" style ='display:<?php echo empty($hasFavourite) ? "flex" : "none" ?>' class="custom_common_btn project_view_follow_btn common_btn <?php echo $favourites; ?>" title="<?php echo $view->translate("Follow"); ?>">
                            <?php echo $view->translate("Follow"); ?>
                        </a>

                        <input type ="hidden" id = "<?php echo $fav ?>" value = '<?php echo $hasFavourite ? $hasFavourite[0]['favourite_id'] : 0; ?>' />

                    <?php else: ?>

                        <a href = "javascript:void(0);" style="display:flex" class="custom_common_btn project_view_follow_btn common_btn seao_popup_user_auth_link" title="<?php echo $view->translate("Follow"); ?>">
                            <?php echo $view->translate("Follow"); ?>
                        </a>

                    <?php endif; ?>
                <?php endif; ?>

                <?php if (in_array('join', $params)) : ?>

                    <?php $isMemberJoined = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->isMemberJoined($resource_id); ?>

                    <?php $removeMember = $resource_type . '_removeMember_' . $resource_id ?>
                    <?php $addMember = $resource_type . '_addMember_' . $resource_id ?>
                    <?php $inputId = $resource_type . '_member_' . $resource_id; ?>

                    <?php if($viewer_id): ?>

                        <?php $joinUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitecrowdfunding', 'controller' => 'member', 'action' => 'join','project_id' => $resource_id), 'sitecrowdfunding_project_member', true); ?>
                        <?php $leaveUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitecrowdfunding', 'controller' => 'member', 'action' => 'leave','project_id' => $resource_id), 'sitecrowdfunding_project_member', true); ?>

                        <a href = "javascript:void(0);" onclick="showSmoothBox('<?php echo $leaveUrl; ?>')" id="<?php echo $removeMember; ?>" style ='display:<?php echo $isMemberJoined ? "flex" : "none" ?>'  class="custom_common_btn project_view_leave_btn common_btn <?php echo $removeMember; ?>" title="<?php echo $view->translate("Leave"); ?>">
                            <?php echo $view->translate("Leave"); ?>
                        </a>

                        <a href = "javascript:void(0);" onclick="showSmoothBox('<?php echo $joinUrl; ?>')" id="<?php echo $addMember; ?>" style ='display:<?php echo empty($isMemberJoined) ? "flex" : "none" ?>' class="custom_common_btn project_view_join_btn common_btn <?php echo $addMember; ?>" title="<?php echo $view->translate("Join"); ?>">
                            <?php echo $view->translate("Join"); ?>
                        </a>

                        <input type ="hidden" id = "<?php echo $inputId ?>" value = '<?php echo $isMemberJoined ? $isMemberJoined[0]['membership_id'] : 0; ?>' />

                    <?php else: ?>

                        <a href = "javascript:void(0);" style="display: flex" class="custom_common_btn project_view_join_btn common_btn seao_popup_user_auth_link" title="<?php echo $view->translate("Join"); ?>">
                            <?php echo $view->translate("Join"); ?>
                        </a>

                    <?php endif; ?>

                <?php endif; ?>

                <?php
                $projectStartDate = date('Y-m-d', strtotime($project->funding_start_date));
                $currentDate = date('Y-m-d');
                $flag = $project->isFundingApproved() ;
                if( in_array('back-btn',$params)):
                    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                    $baseUrl = $view->baseUrl();
                    $backUrl =  $baseUrl.'/projects/backer/donate-to-project/project_id/'.$project->project_id.'/donationType/1';

                    //Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'donate-to-project','project_id'=>$project->project_id, 'donationType' => true), "sitecrowdfunding_backer", true);
                    ?>
                    <?php //if($viewer_id): ?>
                    <a <?php echo  !$flag ? 'disabled' :null ?> style="display: flex" class="custom_common_btn project_view_join_btn common_btn" href="<?php echo !$flag ? 'javascript:void(0);' : $backUrl ?>">
                        <?php echo $view->translate($backTitle); ?>
                    </a>
                    <?php //else:?>
                    <!--<a <?php echo  !$flag ? 'disabled' :null ?> href = "javascript:void(0);" style="display: flex" class="custom_common_btn project_view_join_btn common_btn seao_popup_user_auth_link" title="<?php echo $view->translate($backTitle); ?>">
                            <?php echo $view->translate($backTitle); ?>
                        </a>-->
                    <?php //endif; ?>
                <?php elseif( in_array('back-btn',$params) && $project->isFundingApproved()  ): ?>
                    <a <?php echo !$flag ? 'disabled' :null ?> href = "javascript:void(0);" onclick="shwMsg('<?php
                    $startindays = Engine_Api::_()->sitecrowdfunding()->findDays($project->funding_start_date);
                    $text = $startindays == 1 ?  'Day' : 'Days';
                    echo "Donation will start in ".$startindays. " ".$text ; ?>')" style="display: flex" class="custom_common_btn project_view_join_btn common_btn" title="<?php echo $view->translate($backTitle); ?>">
                        <?php echo $view->translate($backTitle); ?>
                    </a>
                <?php else:?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }


}

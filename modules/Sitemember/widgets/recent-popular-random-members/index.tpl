<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/infotooltip.tpl'; ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/scripts/core.js'); ?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css')
?>

<?php
if ($this->is_ajax_load):
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $this->url(array('action' => 'userby-locations'), "sitemember_userbylocation", true);
    ?>

    <?php
    if ($this->viewType == 'gridview'):
        $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
    endif;
    ?>

        <?php if ($this->viewType == 'listview'): ?>
        <ul class="seaocore_sidebar_list <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
            <?php $i = 0;
            foreach ($this->members as $sitemember):
                ?>
                <?php if (!empty($this->detactLocation)): ?>
                    <?php if ($i == $this->limit): break;
                    endif; ?> 
                        <?php if ($this->viewer_id == $sitemember->user_id || $this->subject_id == $sitemember->user_id): continue; ?>
                        <?php endif; ?>
            <?php endif; ?>
                <li>
                            <?php echo $this->htmlLink($sitemember->getHref(array()), $this->itemPhoto($sitemember, 'thumb.icon', array('title' => $sitemember->getTitle()))) ?>
                    <div class='seaocore_sidebar_list_info'>
                        <div class='seaocore_sidebar_list_title'>
                            <?php if (!empty($this->statistics) && in_array('title', $this->statistics)) : ?>
                                <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->truncation), array('title' => $sitemember->getTitle())); ?>
                            <?php endif; ?>

                            <?php
                            //GET VERIFY COUNT AND VERIFY LIMIT
                            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                                $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                                $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                                $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                                ?>
                <?php if ($verify_count >= $verify_limit): ?>
                                    <span class="siteverify_tip_wrapper">
                                        <i class="sitemember_list_verify_label mleft5"></i>
                                        <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                                    </span>
                                    <?php
                                endif;
                            endif;
                            ?>

                            <?php
                            if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :

                                $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
                                ?>
                                <span class="fright seaocore_txt_light">
                                    <?php if (!empty($online_status)) : ?>
                                        <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
                                        <!--<?php echo $this->translate("Online"); ?>-->
                <?php endif; ?>
                                </span>
                        <?php endif; ?>
                        </div>

                        <?php if (!empty($this->statistics)) : ?>
                            <?php
                            $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');
                            echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading));
                            ?>
                        <?php endif; ?>
<?php if(!empty($this->links)):?>
                            <div class="clr sitemember_action_link_options sitemember_action_links">
                        <?php $items = Engine_Api::_()->getItem('user', $sitemember->user_id); ?>
                        <?php if (Engine_Api::_()->seaocore()->canSendUserMessage($items) && !empty($this->viewer_id) && !empty($this->links) && in_array('messege', $this->links)): ?>
                            <a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink sitemember_action_links_message"><?php echo $this->translate('Message'); ?></a>
                        <?php endif; ?>

                        <?php if (!empty($this->links) && in_array('addfriend', $this->links)): ?>
                            <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id)); ?>
                            <?php if (!empty($uaseFRIENFLINK)) : ?>
                    <?php echo $uaseFRIENFLINK; ?>
                    <?php endif; ?></div>
                            <?php endif; ?>
                <?php endif; ?>
                    </div>
                </li>
            <?php $i++; ?>
        <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <?php $isLarge = ($this->columnWidth > 170); ?>
        <ul class="seaocore_sidebar_list sitemember_grid_view_sidebar o_hidden <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
            <?php $i = 0; ?>
            <?php foreach ($this->members as $sitemember): ?>
                <?php if (!empty($this->detactLocation)): ?>
                    <?php if ($i == $this->limit): return;
                    endif; ?> 
                            <?php if ($this->viewer_id == $sitemember->user_id || $this->subject_id == $sitemember->user_id): continue; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                <li class="sitemember_grid_view" <?php if(empty($this->circularImage)):?> style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;" <?php else:?> style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;" <?php endif;?>>
                    <div class="sitemember_grid_thumb">
            <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $sitemember->featured): ?>
                            <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></i>
                            <?php endif; ?>
                            <?php $rel = 'user' . ' ' . $sitemember->user_id; ?>

                        <a href="<?php echo $sitemember->getHref() ?>" class ="sitemember_thumb sea_add_tooltip_link" rel="<?php echo $rel ?>" >
                            <?php
                            $url = $sitemember->getPhotoUrl($isLarge ? 'thumb.profile' : 'thumb.profile');
                            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                            endif;
                            ?>

                            <?php if(!$this->circularImage):?>
                                <span style="background-image: url(<?php echo $url; ?>); <?php if ($isLarge): ?> height:160px; <?php else: ?> height:<?php echo $this->columnHeight; ?>px; <?php endif; ?>  "></span>
                           <?php else:?>
                                <span style="background-image: url(<?php echo $url; ?>); height:<?php echo $this->circularImageHeight; ?>px;"></span>
                            <?php endif;?>
                        </a>

                            <?php if (!empty($this->titlePosition)) : ?>
                            <div class="sitemember_grid_title">
                                <?php if (!empty($this->statistics) && in_array('title', $this->statistics)) : ?>
                                    <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->truncation), array('title' => $sitemember->getTitle())); ?>
                                <?php endif; ?>

                                <?php
                                //GET VERIFY COUNT AND VERIFY LIMIT
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                                    $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                                    $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                                    $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                                    ?>
                                    <?php if ($verify_count >= $verify_limit): ?>
                                        <span class="siteverify_tip_wrapper">
                                            <i class="sitemember_list_verify_label mleft5"></i>
                                            <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                                        </span>
                                        <?php
                                    endif;
                                endif;
                                ?>
                                    <?php
                                    if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :
                                        $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
                                        ?>
                                    <span class="fright seaocore_txt_light">
                                    <?php if (!empty($online_status)) : ?>
                                            <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
                                            <!--<?php echo $this->translate("Online"); ?>-->
                                <?php endif; ?>
                                    </span>
                        <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($sitemember->sponsored)): ?>
                        <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>;'>
                        <?php echo $this->translate('Sponsored'); ?>
                        </div>
                        <?php endif; ?>

                            <?php if (!empty($this->statistics) && !empty($this->links)): ?>
                        <div class="sitemember_grid_info">
                                <?php if (empty($this->titlePosition)) : ?>
                                <div class="bold">
                                    <?php if (!empty($this->statistics) && in_array('title', $this->statistics)) : ?>
                                        <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->truncation), array('title' => $sitemember->getTitle())) ?>
                                    <?php endif; ?>
                                    <?php
                                    //GET VERIFY COUNT AND VERIFY LIMIT
                                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                                        $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                                        $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                                        $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                                        ?>
                                        <?php if ($verify_count >= $verify_limit): ?>
                                            <span class="siteverify_tip_wrapper">
                                                <i class="sitemember_list_verify_label mleft5"></i>
                                                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                                            </span>
                                            <?php
                                        endif;
                                    endif;
                                    ?>

                                        <?php
                                        if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :
                                            $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
                                            ?>
                                        <span class="fright seaocore_txt_light">
                                        <?php if (!empty($online_status)) : ?>
                                                <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
                                                <!--<?php echo $this->translate("Online"); ?>-->
                                    <?php endif; ?>
                                        </span>
                                <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($this->statistics)) : ?>
                                <?php
                                echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading));
                                ?>
                            <?php endif; ?>

                            <?php if(!empty($this->links)):?>
                            <div class="clr sitemember_action_link_options sitemember_action_links">
                                <?php $items = Engine_Api::_()->getItem('user', $sitemember->user_id); ?>
                                <?php if (Engine_Api::_()->seaocore()->canSendUserMessage($items) && !empty($this->viewer_id) && in_array('messege', $this->links)): ?>
                                    <a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink sitemember_action_links_message"><?php echo $this->translate('Message'); ?></a>
                                <?php endif; ?>

                                <?php if (!empty($this->links) && in_array('addfriend', $this->links)): ?>
                                    <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id)); ?>
                                <?php if (!empty($uaseFRIENFLINK)) : ?>
                                            <?php echo $uaseFRIENFLINK; ?>
                                        <?php endif; ?>
                                <?php endif; ?>
                            </div>
<?php endif; ?>
                        </div>
            <?php endif; ?>
                </li>
            <?php $i++;
        endforeach; ?>
        </ul>
    <?php endif; ?>
<?php else: ?>

    <div id="layout_sitemember_recent_popular_random_members<?php echo $this->identity; ?>">
    </div>

    <script type="text/javascript">
        var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_sitemember_recent_popular_random_members<?php echo $this->identity; ?>',
            requestParams: requestParams
        };
        en4.seaocore.locationBased.startReq(params);
    </script>

<?php endif; ?>

<?php if ($this->viewtitletype == 'horizontal'): ?>
    <style type="text/css">
        /*Horizontal view*/
        .layout_sitemember_recent_popular_random_members li.sitemember_grid_view{clear:none; border-radius:0;margin:3px !important;}
    </style>
<?php endif; ?>

<?php if($this->circularImage):?>
<script type="text/javascript">

var list=$$('.layout_sitemember_recent_popular_random_members').getElements('.sitemember_grid_view');
if(list) {
	
	list.each(function(el, i)
	{
		if(el) {
	 el.getElement('.sitemember_grid_info').each(function(els, i)
	{
		if(els) {
			var sitememberHtml = els.innerHTML;
			if(sitememberHtml.trim() != '') {
				  els.getParent().style.cssText = 'height:<?php echo $this->columnHeight;?>px !important;width:<?php echo $this->columnWidth;?>px';
			}
		}
	});	
	
	}
	});		
	
	
	
	
}
</script>
<?php endif;?>

<style type="text/css" >
.sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::before, .sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::after {
    background: <?php echo $this->settings->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>;
	}
</style>
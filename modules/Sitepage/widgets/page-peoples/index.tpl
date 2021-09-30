<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js'); ?>
<?php $defaultURL =  $this->layout()->staticBaseUrl. "application/modules/User/externals/images/nophoto_user_thumb_profile.png" ?>

<?php
    if($this->identity)
$this->params['identity'] = $this->identity;
else
$this->identity = $this->params['identity'];
?>
<div  id="scroll_link"></div>
<?php
    $currentLink = 'followed';
    if(isset($this->params['link']) && !empty($this->params['link']))
$currentLink = $this->params['link'];
$viewType = isset($this->peopleNavigationLink[2]) ? ($this->peopleNavigationLink[2]) : 2;
?>

<?php if (empty($this->is_ajax)) : ?>
<div class="sitepage_page_top_links b_medium">
    <div class="sitepage_page_top_filter_links txt_center sitepage_page_top_filter_links_<?php echo $this->identity; ?>" style="display:<?php echo (count($this->peopleNavigationLink) > 0) ? 'block' : 'none'; ?>" >

        <?php if (in_array('all', $this->peopleNavigationLink)) : ?>
            <a href="javascript:void(0);" id='all'  onclick="filter_rsvp('all')" ><?php echo $this->translate('All'); ?></a>
        <?php endif; ?>

        <?php if (in_array('creator', $this->peopleNavigationLink)) : ?>
            <a href="javascript:void(0);" id='creator'  onclick="filter_rsvp('creator')" ><?php echo $this->translate('Creator'); ?></a>
        <?php endif; ?>

        <?php if (in_array('followed', $this->peopleNavigationLink)) : ?>
            <a href="javascript:void(0);" id='followed'  onclick="filter_rsvp('followed')" ><?php echo $this->translate('Followers'); ?></a>
        <?php endif; ?>

        <?php if (in_array('joined', $this->peopleNavigationLink)) : ?>
            <a href="javascript:void(0);" id='joined'  onclick="filter_rsvp('joined')" ><?php echo $this->translate('Members'); ?></a>
        <?php endif; ?>

        <?php if (in_array('admin', $this->peopleNavigationLink)) : ?>
            <a href="javascript:void(0);" id='admin'  onclick="filter_rsvp('admin')" ><?php echo $this->translate('Admin'); ?></a>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>

<div id="add_people_btn" style="display: none" class="fright">
    <div>
        <?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id, $params = 'Invite'); ?>
        <?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
        <?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'invite-members', 'page_id' => $this->sitepage->page_id), $this->translate("Add People"), array('class' => 'buttonlink icon_sitepage_ad_member smoothbox')); ?>
        <?php elseif (!empty($hasMembers) && empty($this->sitepage->member_invite)): ?>
        <?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'invite', 'page_id' => $this->sitepage->page_id), $this->translate("Add People"), array('class' => 'buttonlink icon_sitepage_ad_member smoothbox')); ?>
        <?php endif; ?>
    </div>
</div>
<div id='sitepage_page_peoples' style="display:<?php echo (count($this->peopleNavigationLink) > 0) ? 'block' : 'none'; ?>">

    <?php if(count($this->paginator) > 0): ?>

        <?php if ( $currentLink == 'all' && in_array('all', $this->peopleNavigationLink)) : ?>
            <ul id="page-all" class="grid_wrapper">
                <?php foreach ($this->paginator as $user): ?>
                    <?php $user = Engine_Api::_()->getItem('user', $user['user_id']);?>
                    <li>
                        <?php echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile')); ?>
                        <div class='followers-name'>
                            <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ( $currentLink == 'followed' && in_array('followed', $this->peopleNavigationLink)) : ?>
            <ul id="page-followers" class="grid_wrapper">
                <?php foreach ($this->paginator as $user): ?>
                    <li>
                        <?php echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile')); ?>
                        <div class='followers-name'>
                            <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($currentLink == 'joined' && in_array('joined', $this->peopleNavigationLink)) : ?>
            <br/>
            <ul id="page-joined" class="grid_wrapper">
                <?php foreach ($this->paginator as $item): ?>
                    <li>
                        <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item->getOwner(), 'thumb.profile')); ?>
                        <div class='joined-name'>
                            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php foreach ($this->pendingInvites as $item): ?>
                    <li>
                        <a href="javascript:void(0);"><span class="bg_item_photo bg_thumb_profile bg_item_photo_user bg_item_nophoto " style=" background-image:url('<?php echo $defaultURL ?>');"></span></a>
                        <div class='joined-name'>
                            <a href="javascript:void(0)">  <?php echo $item['recipient_name']; ?></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($currentLink == 'admin' && in_array('admin', $this->peopleNavigationLink)) : ?>
            <ul id="page-admins" class="grid_wrapper">
                <?php foreach ($this->paginator as $item): ?>
                    <li>
                        <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item->getOwner(), 'thumb.profile')); ?>
                        <div class='admin-name'>
                            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($currentLink == 'creator' && in_array('creator', $this->peopleNavigationLink)) : ?>
            <ul id="page-creator" class="grid_wrapper">
                <li>
                    <?php echo $this->htmlLink($this->sitepage->getOwner()->getHref(), $this->itemBackgroundPhoto($this->sitepage->getOwner(), 'thumb.profile')) ?>
                    <div class='followers-name'>
                        <?php echo $this->htmlLink($this->sitepage->getOwner(), $this->sitepage->getOwner()->getTitle()); ?>
                    </div>
                </li>
            </ul>
        <?php endif; ?>

    <?php else: ?>

        <?php if($currentLink == 'all'): ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('No Users Found'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($currentLink == 'followed'): ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('Nobody has followed this page.'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($currentLink == 'joined'): ?>
            <br/>
            <div class="tip">
                <span>
                    <?php echo $this->translate('Nobody has joined this page.'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($currentLink == 'admin'): ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('No admin for this page.'); ?>
                </span>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<div id="hidden_ajax_data" style="display: none;"></div>

<script>
    viewType = '<?php echo $viewType; ?>';
    viewFormatG = '<?php echo $this->viewFormat ?>';
    addBoldClass = function (reqType)
    {
        $$('div.sitepage_page_top_filter_links_<?php echo $this->identity; ?> > a').each(function (el) {
            el.removeClass('active');
        });
        $$('.seaocore_tab_icon_<?php echo $this->identity ?>').each(function (el) {
            el.removeClass('active');
        });
        $(reqType).addClass('active');
    }

    // Hide/show  the add people btn based on type
    var currentLink = "<?php echo $currentLink; ?>";
    console.log('currentLink',currentLink);

    if(currentLink=='joined'){
        document.getElementById("add_people_btn").style.display = "inherit";
    }else{
        document.getElementById("add_people_btn").style.display = "none";
    }

    var $j = jQuery.noConflict();

    $j(".followers").click(function() {
        filter_rsvp('followed');
    });
    $j(".all").click(function() {
        filter_rsvp('all');
    });
    $j(".members").click(function() {
        filter_rsvp('joined');
    });
    filter_rsvp = function (req_type)
    {
        if (req_type == '0')
            return false;
        addBoldClass(req_type);
        viewType = req_type;
        switch (req_type)
        {
            case 'all':
                var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-peoples/link/all';
                break;
            case 'joined':
                var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-peoples/link/joined';
                break;
            case 'followed':
                var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-peoples/link/followed';
                break;
            case 'admin':
                var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-peoples/link/admin';
                break;
            case 'creator':
                var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-peoples/link/creator';
                break;
        }
        $('sitepage_page_peoples').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

        // Hide/show  the add people btn based on type
        if(req_type=='joined'){
            document.getElementById("add_people_btn").style.display = "inherit";
        }else{
            document.getElementById("add_people_btn").style.display = "none";
        }

        var params = {
            requestParams:<?php echo json_encode($this->params) ?>
        };
        params.requestParams.is_ajax = 0;
        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax: 0,
                pagination: 0,
                page: 0,
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_data').innerHTML = responseHTML;
                $('sitepage_page_peoples').innerHTML = $('hidden_ajax_data').getElement('#sitepage_page_peoples').innerHTML;
                $('hidden_ajax_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($('sitepage_page_peoples'));
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }
    var currentLink = "<?php echo $currentLink; ?>";
    var allLinks = $$('div.sitapage_page_top_filter_links_<?php echo $this->identity; ?> > a');
    allLinks.removeClass('active');
    $(currentLink).addClass('active');

</script>
<style>
    .bg_item_photo{
        background-size: cover !important;
    }
    #sitepage_page_peoples ul.grid_wrapper {
        overflow: inherit !important;
    }
    @media (min-width: 980px){
        #sitepage_page_peoples ul.grid_wrapper>li {
            width: 18%;
        }
    }
    .joined-name, .admin-name {
        font-weight: bold;
    }
    #sitepage_page_peoples ul.grid_wrapper>li>a>span {
        background-position: center !important;
        /*background-repeat: no-repeat;*/
        /*text-align: center;*/
    }
    ul.grid_wrapper>li>a, ul.grid_wrapper>li>div:first-child>a {
        text-align: center;
        height: 200px  !important;
        vertical-align: middle;
        display: block;
        box-sizing: border-box;
        overflow: hidden;
        width: 100%;
    }
</style>

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

<?php
    $currentLink = 'creator';
    if(isset($this->params['link']) && !empty($this->params['link']))
    $currentLink = $this->params['link'];
    $viewType = isset($this->projectNavigationLink[0]) ? ($this->projectNavigationLink[0]) : 0;
?>
<div id="scroll_link_projects"></div>
<?php if (empty($this->is_ajax)) : ?>
    <div class="sitecrowdfunding_myprojects_top_links b_medium">
        <div class="sitecrowdfunding_myprojects_top_filter_links txt_center sitecrowdfunding_myprojects_top_filter_links_<?php echo $this->identity; ?>" style="display:<?php echo (count($this->peopleNavigationLink) > 0) ? 'block' : 'none'; ?>" >

            <a href="javascript:void(0);" id='creator'  onclick="filter_rsvp('creator')" ><?php echo $this->translate('Creator'); ?></a>

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

<div id="add_people_btn" style="display: none">
    <?php if ( empty($this->project->member_invite) && empty($this->viewer_id)) : ?>
        <div class="fright">
            <a class="button user_auth_link" href = "javascript:void(0);" >
                <?php echo $this->translate("Add People"); ?>
            </a>
        </div>
    <br/>
    <?php endif; ?>
</div>

<div id='sitecrowdfunding_project_peoples' style="display:<?php echo (count($this->peopleNavigationLink) > 0) ? 'block' : 'none'; ?>">

    <?php if(count($this->paginator) > 0): ?>

        <?php if ( $currentLink == 'followed' && in_array('followed', $this->peopleNavigationLink)) : ?>
            <ul id="project-followers" class="grid_wrapper">
                <?php foreach ($this->paginator as $item): ?>
                <li>
                    <?php
                        $user_id = $item['poster_id'];
                        $user = Engine_Api::_()->getItem('user', $user_id);
                    echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile'));
                    ?>
                    <div class='followers-name'>
                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($currentLink == 'joined' && in_array('joined', $this->peopleNavigationLink)) : ?>

            <?php if (empty($this->project->member_invite) && !empty($this->viewer_id)) : ?>
            <div class="fright">
                <a class="button smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'project_id' => $this->project_id), 'sitecrowdfunding_project_member', true)); ?>">
                    <span><?php echo $this->translate("Add People"); ?></span>
                </a>
            </div>
            <br/>
            <?php endif; ?>

            <ul id="project-followers" class="grid_wrapper">
                <?php foreach ($this->paginator as $item): ?>
                <li>
                    <?php
                        $user_id = $item['user_id'];
                        $user = Engine_Api::_()->getItem('user', $user_id);
                    echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile'));
                    ?>
                    <div class='followers-name'>
                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?>
                    </div>
                </li>
                <?php endforeach; ?>
                <?php foreach ($this->pendingInvites as $item): ?>
                <li>
                    <a href="javascript:void(0);"><span class="bg_item_photo bg_thumb_profile bg_item_photo_user " style=" background-image:url('<?php echo $defaultURL ?>');"></span></a>
                    <div class='followers-name'>
                        <a href="javascript:void(0)">  <?php echo $item['recipient_name']; ?></a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($currentLink == 'admin' && in_array('admin', $this->peopleNavigationLink)) : ?>
            <ul id="project-followers" class="grid_wrapper">
                <?php foreach ($this->paginator as $item): ?>
                <li>
                    <?php
                        $user_id = $item->user_id;
                        $user = Engine_Api::_()->getItem('user', $user_id);
                    echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile'));
                    ?>
                    <div class='followers-name'>
                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($currentLink == 'creator') : ?>
            <ul id="project-followers" class="grid_wrapper">
                <li>
                    <?php
                        echo $this->htmlLink($this->project->getOwner()->getHref(), $this->itemBackgroundPhoto($this->project->getOwner(), 'thumb.profile'));
                    ?>
                    <div class='followers-name'>
                        <?php echo $this->htmlLink($this->project->getOwner()->getHref(), $this->project->getOwner()->getTitle()); ?>
                    </div>
                </li>
            </ul>
        <?php endif; ?>

    <?php else: ?>

        <?php if($currentLink == 'followed'): ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('Nobody has followed into this project.'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($currentLink == 'joined'): ?>

            <?php if (empty($this->project->member_invite) && !empty($this->viewer_id)) : ?>
                <div class="fright">
                    <a class="button smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'project_id' => $this->project_id), 'sitecrowdfunding_project_member', true)); ?>">
                        <span><?php echo $this->translate("Add People"); ?></span>
                    </a>
                </div>
                <br/>
            <?php endif; ?>

            <div class="tip">
                <span>
                    <?php echo $this->translate('Nobody has joined into this project.'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($currentLink == 'admin'): ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('No admin for this project.'); ?>
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
        $$('div.sitecrowdfunding_myprojects_top_filter_links_<?php echo $this->identity; ?> > a').each(function (el) {
            el.removeClass('active');
        });
        $$('.seaocore_tab_icon_<?php echo $this->identity ?>').each(function (el) {
            el.removeClass('active');
        });
        $(reqType).addClass('active');
    }

    // Hide/show  the add people btn based on type
    var currentLink = "<?php echo $currentLink; ?>";
    if(currentLink=='joined'){
        document.getElementById("add_people_btn").style.display = "inherit";
    }else{
        document.getElementById("add_people_btn").style.display = "none";
    }
    var $j = jQuery.noConflict();

    $j(".follow_scroll").click(function() {
        filter_rsvp('followed');
    });
    $j(".backer_scroll").click(function() {
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
            case 'joined':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/project-peoples/link/joined';
                break;
            case 'followed':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/project-peoples/link/followed';
                break;
            case 'admin':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/project-peoples/link/admin';
                break;
            case 'creator':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/project-peoples/link/creator';
                break;
        }
        $('sitecrowdfunding_project_peoples').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

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
                $('sitecrowdfunding_project_peoples').innerHTML = $('hidden_ajax_data').getElement('#sitecrowdfunding_project_peoples').innerHTML;
                $('hidden_ajax_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($('sitecrowdfunding_project_peoples'));
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }
    var currentLink = "<?php echo $currentLink; ?>";
    var allLinks = $$('div.sitecrowdfunding_myprojects_top_filter_links_<?php echo $this->identity; ?> > a');
    allLinks.removeClass('active');
    $(currentLink).addClass('active');
</script>
<style>
    .bg_item_photo{
        background-size: cover !important;
        background-position: center !important;
    }
</style>

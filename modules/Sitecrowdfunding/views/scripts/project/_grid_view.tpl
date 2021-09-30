<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _grid_view.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$identity = $this->idenity;
if ($this->id) :
    $identity = $this->id;
endif;

if ($this->paginatorGridView) {
    $this->paginator = $this->paginatorGridView;
}
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class='projects_manage sitecrowdfunding_projects_grid_view' id='projects_manage<?php echo "_" . $identity; ?>'>
        <?php foreach ($this->paginator as $item): ?>
            <li class="effect2">
                <div class="sitecrowdfunding_thumb_wrapper sitecrowdfunding_thumb_viewer" style="width:<?php echo $this->gridViewWidth; ?>px; height:<?php echo $this->gridViewHeight; ?>px;">              
                    <div class="sitecrowdfunding_grid_thumb"> 
                        <?php $fsContent = ""; ?>
                        <?php /* if ($item->featured && in_array('featured', $this->projectOption)): ?>
                            <?php $fsContent .= '<div class="sitecrowdfunding_featured"  style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.featuredcolor', '#f72828') . '">' . $this->translate("Featured") . '</div>'; ?>
                        <?php endif; ?>
                        <?php if ($item->sponsored && in_array('sponsored', $this->projectOption)): ?>
                            <?php $fsContent .= '<div class="sitecrowdfunding_sponsored"  style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.sponsoredcolor', '#FC0505') . '">' . $this->translate("Sponsored") . '</div>'; ?>
                        <?php endif; */ ?>
                        <?php
                        if ($item->photo_id) {
                            echo $this->htmlLink($item->getHref(), $fsContent . $this->itemBackgroundPhoto($item,'thumb.cover', null, null, array('tag' => 'i')), array('class' => 'sitecrowdfunding_thumb','target' => '_blank'));
                        } else {
                            $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                            echo $this->htmlLink($item->getHref(), $fsContent . "<i style='background-image:url(" . $url . ")'></i>", array('class' => 'sitecrowdfunding_thumb','target' => '_blank'));
                        }
                        ?>
                        <div class='sitecrowdfunding_hover_info' style="display: flex;align-items: center;justify-content: center;">
                            <div class="txt_center">
                            <button onclick="window.open('<?php echo $item->getHref() ?>','_blank')">
                                <?php echo $this->translate('View'); ?>
                            </button>
                            </div>
                        </div>
                    </div>
                    <div class="sitecrowdfunding_info_wrapper">
                        <div class="sitecrowdfunding_info">
                            <div class="sitecrowdfunding_bottom_info sitecrowdfunding_grid_bott_info">

                                <?php if (in_array('title', $this->projectOption)) : ?>
                                   <!-- <h3><?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), $this->titleTruncationGridView), array('title' => $item->getTitle())) ?></h3> -->
                                   <h3 class="project_titles">   <?php echo $this->htmlLink($item->getHref(), $item->getTitle(),array('target' => '_blank')) ?> </h3>
                                <?php endif; ?>

                                <!-- organisation name -->
                                <?php
                                    $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($item->project_id);
                                    if (empty($parentOrganization)) {
                                        $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($item->project_id);
                                    }
                                ?>
                                <?php if(!empty($parentOrganization['title']) ): ?>
                                    <a class="main_project_parent_title" href="<?php echo !empty($parentOrganization['link']) ? $parentOrganization['link'] : 'javascript:void(0);'  ?>" > <b>Organisation :</b> <?php echo $parentOrganization['title'] ?></a><br>
                                <?php else:?>
                                    <a class="main_project_parent_title" href="javascript:void(0);"> <b>Organisation :</b> -</a><br>
                                <?php endif; ?>

                                <!-- initiative name -->
                                <?php if(!empty($parentOrganization['page_id'])):?>
                                    <?php if(!empty($item->initiative_id)):?>
                                        <?php $initiative = Engine_Api::_()->getItem('sitepage_initiative', $item->initiative_id); ?>
                                        <div class="project_initiative_container">
                                            <div class="project_initiative_name">
                                                <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $parentOrganization['page_id'], 'initiative_id' => $item->initiative_id), "sitepage_initiatives");?>
                                                <a title="<?php echo $initiative['title'];?>" class="main_project_parent_title" href="<?php echo !empty($initiativesURL) ? $initiativesURL : 'javascript:void(0);'  ?>" >
                                                    <b>Initiative : &nbsp;</b> <?php echo $initiative['title'];?>
                                                </a>
                                                <br>
                                            </div>
                                        </div>
                                    <?php else : ?>

                                        <?php
                                        //prepare tags
                                        $projectTags = $item->tags()->getTagMaps();
                                        $tagString =  array();
                                        foreach ($projectTags as $tagmap) {
                                            $tagString[]= $tagmap->getTag()->getTitle();
                                        }
                                        ?>

                                        <?php if(count($tagString) > 0):?>
                                            <?php $initiatives = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectInitiatives($parentOrganization['page_id'],$tagString); ?>
                                            <?php if(count($initiatives) > 0):?>
                                                <div class="project_initiative_container">
                                                    <div class="project_initiative_name">
                                                        <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $parentOrganization['page_id'], 'initiative_id' => $initiatives[0]['initiative_id']), "sitepage_initiatives");?>
                                                        <a class="main_project_parent_title" href="<?php echo !empty($initiativesURL) ? $initiativesURL : 'javascript:void(0);'  ?>" >
                                                            <b>Initiative : </b><?php echo $initiatives[0]['title'];?>
                                                        </a>
                                                        <br>
                                                    </div>
                                                </div>
                                            <?php else:?>
                                                <div class="project_initiative_container">
                                                    <div class="project_initiative_name">
                                                        <a class="main_project_parent_title" href="javascript:void(0);" >
                                                            <b>Initiative : </b>-
                                                        </a>
                                                        <br>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php else:?>
                                            <div class="project_initiative_container">
                                                <div class="project_initiative_name">
                                                    <a class="main_project_parent_title" href="javascript:void(0);" >
                                                        <b>Initiative : </b>-
                                                    </a>
                                                    <br>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    <?php endif; ?>
                                <?php else:?>
                                    <div class="project_initiative_container">
                                        <div class="project_initiative_name">
                                            <a class="main_project_parent_title" href="javascript:void(0);" >
                                                <b>Initiative : </b>-
                                            </a>
                                            <br>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="sitecrowdfunding_grid_bottom_info">
                                    <?php $title = ""; ?>
                                  <!--  <?php if (in_array('owner', $this->projectOption)) : ?>
                                        <?php
                                        $title = $this->translate('by %s', $this->htmlLink($item->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($item->getOwner()->getTitle()), 17), array('title' => $item->getOwner()->getTitle())));
                                        echo $title;
                                        ?> 
                                    <?php endif; ?> -->
                                    <div class="sitecrowdfunding_desc" title="<?php echo $this->string()->truncate($this->string()->stripTags($item->desire_desc), 250) ?>">
                                        <?php echo $this->string()->truncate($this->string()->stripTags($item->desire_desc), $this->descriptionTruncation ? $this->descriptionTruncation : 350) ?>
                                        <?php // echo $this->string()->stripTags($item->desire_desc); ?>
                                    </div>
                                    <?php if (isset($this->currenctTab) && $this->currenctTab == 'backed'):
                                        //TODO fix funding
                                    ?>
                                    <?php //$backedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->getFundedAmount(true)); ?>
                                    <?php //echo $this->translate('Backed Amount: ' . $backedAmount); ?>
                                    <?php endif; ?>
                                    <?php if($item->category_id): ?>
                                        <div class="sitecrowdfunding_bottom_info_category" style="display: none">
                                            <?php $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $item->category_id); ?>
                                            <?php if ($category->file_id): ?>
                                                <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
                                                <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="">
                                            <?php elseif ($category->font_icon): ?>
                                                <i class="fa <?php echo $category->font_icon; ?>"></i>
                                            <?php else: ?>
                                                <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?>
                                                <img src="<?php echo $src ?>" style="width: 16px; height: 16px;" alt="">
                                            <?php endif; ?>
                                            <?php echo $this->htmlLink($category->getHref(), $category->getTitle(),array('target' => '_blank')) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                      $pro_location = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation(array('id' => $item->project_id));
                                    ?>
                                    <?php if ($pro_location->location) : ?>
                                        <div class="sitecrowdfunding_bottom_info_location" title="<?php echo $item->location ?>">
                                            <i class="seao_icon_location"></i>
                                            <?php echo $this->string()->truncate($this->string()->stripTags($pro_location->location), $this->truncationLocation); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                $canDelete = Engine_Api::_()->sitecrowdfunding()->canDeletePrivacy($item->parent_type, $item->parent_id, $item);
                                $canEdit = Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($item->parent_type, $item->parent_id, $item);
                                ?>
                                <?php if (isset($this->isEditButton) && $this->isEditButton && isset($this->isDeleteButton) && $this->isDeleteButton): ?>
                                <?php if (($canEdit || $canDelete)): ?>
                                <div class='sitecrowdfunding_options'>
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                    <ul class="sitecrowdfunding_options_dropdown">
                                        <?php if ($canEdit && isset($this->isEditButton) && $this->isEditButton): ?>
                                        <li>
                                            <?php if($item->steps_completed): ?>
                                            <?php
                                                        echo $this->htmlLink(array(
                                            'route' => 'sitecrowdfunding_specific',
                                            'action' => 'edit',
                                            'project_id' => $item->project_id), $this->translate('Edit Project'), array(
                                            'class' => 'icon_project_edit',
                                            'target' => '_blank'
                                            ));
                                            ?>
                                            <?php else: ?>
                                            <?php
                                                        echo $this->htmlLink(array(
                                            'route' => 'sitecrowdfunding_createspecific',
                                            'action' => 'step-one',
                                            'project_id' => $item->project_id), $this->translate('Edit Project'), array(
                                            'class' => 'icon_project_edit',
                                            'target' => '_blank'
                                            ));
                                            ?>
                                            <?php endif; ?>
                                        </li>
                                        <?php endif; ?>
                                        <li>
                                            <?php
                                                    if ($canDelete && isset($this->isDeleteButton) && $this->isDeleteButton) {

                                            if (empty($project->backer_count)):
                                            echo $this->htmlLink(array('route' => 'sitecrowdfunding_specific', 'action' => 'delete', 'project_id' => $item->project_id, 'format' => 'smoothbox'), $this->translate('Delete Project'), array(
                                            'class' => 'smoothbox icon_project_delete',
                                            'target' => '_blank'
                                            ));
                                            else :
                                            ?>
                                            <a href="javascript:void(0);" class="buttonlink icon_project_delete " onclick='deleteProjectPrompt()'><?php echo $this->translate('Delete Project'); ?></a>
                                            <?php endif; ?>

                                            <?php } ?>
                                        </li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                        // disabling widget for now
                        // TODO: fix funding
                        if($item->isFundingApproved()): ?>
                        <?php
                        $fundedAmount = $item->getFundedAmount();
                        $fundedRatio = $item->getFundedRatio();
                        $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                        ?>

                        <?php
                        // Add Progressvive bar
                        echo $this->fundingProgressiveBar($fundedRatio);
                        ?>
                        <div class="sitecrowdfunding_funding_pledged_days_wrapper" style="position: absolute;bottom: 0px;left:0px;width:100%">
                            <div class="sitecrowdfunding_funding_pledged_days">
                                <span>
                                    <?php echo $this->translate("$fundedRatio %"); ?><br />
                                    <?php echo $this->translate("Funded "); ?>
                                </span>
                                <span>
                                    <?php echo $this->translate("%s", $fundedAmount); ?><br />
                                    <?php echo $this->translate("Backed"); ?>
                                </span>
                                <?php if (in_array('endDate', $this->projectOption)) : ?>
                                <span><?php echo $item->getRemainingDays(); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </li> 
        <?php endforeach; ?>
    </ul>
    <?php if (empty($this->is_ajax) && $this->isViewMoreButton) : ?>
        <div class = "seaocore_view_more mtop10" id="seaocore_view_more">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => '', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div class="seaocore_view_more" id="loding_image" style="display: none;">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
        </div>
        <div id="hideResponse_div"> </div>
    <?php endif; ?>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate($this->message); ?>
            <?php if ($this->can_upload_project) : ?> 
                <?php echo $this->translate('Get started by %1$sposting%2$s a new project.', '<a target="_blank" href="' . $this->url(array('action' => 'create'), 'sitecrowdfunding_project_general', true) . '">', '</a>'); ?>
            <?php endif; ?>
        </span>
    </div>

<?php endif; ?>
<?php if (empty($this->is_ajax) && $this->isViewMoreButton) : ?>
    <script type="text/javascript">
        function viewMorePlaylist(viewFormat)
        {
            $('seaocore_view_more').style.display = 'none';
            $('loding_image').style.display = '';
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>
            };
            en4.core.request.send(new Request.HTML({
                method: 'get',
                'url': en4.core.baseUrl + '<?php echo $this->widgetPath; ?>',
                data: $merge(params.requestParams, {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    page: getNextPage(),
                    is_ajax: 1,
                    loaded_by_ajax: true,
                }),
                evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('hideResponse_div').innerHTML = responseHTML;
                    var projectcontainer = $('hideResponse_div').getElement('#projects_manage<?php echo "_" . $identity; ?>').innerHTML;
                    $('projects_manage<?php echo "_" . $identity; ?>').innerHTML = $('projects_manage<?php echo "_" . $identity; ?>').innerHTML + projectcontainer;
                    $('loding_image').style.display = 'none';
                    $('hideResponse_div').innerHTML = "";
                    fundingProgressiveBarAnimation();

                }
            }));
            return false;
        }
    </script>
<?php endif; ?>
<?php if ($this->isViewMoreButton) : ?>
    <?php if ($this->showContent == 3): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php elseif ($this->showContent == 2): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php else: ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                $('seaocore_view_more').style.display = 'none';
            });
        </script>
        <?php
        echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding"), array("orderby" => $this->orderby));
        ?>
    <?php endif; ?>
    <script type="text/javascript">
        var pageAction = function (page) {
            window.location.href = en4.core.baseUrl + 'sitecrowdfunding/project/manage/page/' + page;
        }

        function getNextPage() {
            return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
        function hideViewMoreLink(showContent) {
            if (showContent == 3) {
                $('seaocore_view_more').style.display = 'none';
                var totalCount = '<?php echo $this->paginator->count(); ?>';
                var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

                function doOnScrollLoadChannel()
                {
                    if (typeof ($('seaocore_view_more').offsetParent) != 'undefined') {
                        var elementPostionY = $('seaocore_view_more').offsetTop;
                    } else {
                        var elementPostionY = $('seaocore_view_more').y;
                    }
                    if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {

                        if ((totalCount != currentPageNumber) && (totalCount != 0))
                            viewMorePlaylist();
                    }
                }
                window.onscroll = doOnScrollLoadChannel;

            } else if (showContent == 2) {
                var view_more_content = $('seaocore_view_more');
                view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
                view_more_content.removeEvents('click');
                view_more_content.addEvent('click', function () {
                    viewMorePlaylist();
                });
            }
        }
    </script>
<?php endif; ?>
<script type="text/javascript">
    $$('.core_main_project').getParent().addClass('active');
</script>

<script type="text/javascript">
    function deleteProjectPrompt() {
        Smoothbox.open('<div class="tip"><span><?php echo $this->string()->escapeJavascript($this->translate("You cannot delete this Project as it has been backed by some users. Still, you want to delete this Project then please contact site admin to do so. [Note: It would be better if you can inform the backers about the deletion of the project.]")); ?></span></div>');
    }
</script>
<style>
    .sitecrowdfunding_desc {
        display: block;
        font-size: 13px !important;
        margin: 5px 0;
        display: -webkit-box;
        -webkit-line-clamp: 8;
        -webkit-box-orient: vertical;
        /* height: 200px; */
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sitecrowdfunding_projects_grid_view, .sitecrowdfunding_projects_list_view {
        display: flex !important;
        justify-content: center;
        flex-wrap: wrap;
    }
    .sitecrowdfunding_funding_bar {
        position: absolute ;
        bottom: 77px !important;
        left: 0px;
        width: 99% !important;
    }
    .project_titles{
        font-weight: bold;
    }

    @media (max-width: 767px) {

        .sitecrowdfunding_projects_grid_view li .sitecrowdfunding_thumb_wrapper {
            height: 510px !important;
        }
    }
</style>
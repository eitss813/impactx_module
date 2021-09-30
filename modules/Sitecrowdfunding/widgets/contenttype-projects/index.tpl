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

<?php 
if(isset($this->params['id']) && $this->params['id'])
    $identity = $this->params['id'];
else
    $identity = $this->params['id'] = $this->identity;
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$widgetmodulename = $this->moduleName . '/widget';
$moduleName = $this->moduleName;
$baseUrl = $this->layout()->staticBaseUrl;
?>

<div class="o_hidden">
    <?php $isUploadAllowed = $this->canCreate && Engine_Api::_()->user()->getViewer()->getIdentity(); ?>
    <?php if ($isUploadAllowed): ?>
        <?php $url = $this->url(array('action' => 'create', 'parent_type' => $this->parent_type, 'parent_id' => $this->parent_id), 'sitecrowdfunding_project_general', true); ?>
        <div class="seaocore_add">
            <a href='<?php echo $url ?>' class='seaocore_icon_add'><?php echo $this->translate('Add Project'); ?></a>
        </div>
    <?php endif; ?>
    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
        <ul class='projects_manage sitecrowdfunding_projects_grid_view' id='projects_manage<?php echo "_" . $identity; ?>'>
            <?php foreach ($this->paginator as $item): ?>
                <li class="effect2">
                    <div class="sitecrowdfunding_thumb_wrapper sitecrowdfunding_thumb_viewer" style="width:<?php echo $this->gridViewWidth; ?>px; height:<?php echo $this->gridViewHeight; ?>px;">              
                        <div class="sitecrowdfunding_grid_thumb"> 
                            <?php $fsContent = ""; ?>
                            <?php if ($item->featured && in_array('featured', $this->projectOption)): ?>
                                <?php $fsContent .= '<div class="sitecrowdfunding_featured"  style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.featuredcolor', '#f72828') . '">' . $this->translate("Featured") . '</div>'; ?>
                            <?php endif; ?>
                            <?php if ($item->sponsored && in_array('sponsored', $this->projectOption)): ?>
                                <?php $fsContent .= '<div class="sitecrowdfunding_sponsored"  style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.sponsoredcolor', '#FC0505') . '">' . $this->translate("Sponsored") . '</div>'; ?>
                            <?php endif; ?>
                            <?php
                            if ($item->photo_id) {
                                echo $this->htmlLink($item->getHref(), $fsContent . $this->itemBackgroundPhoto($item, null, null, array('tag' => 'i')), array('class' => 'sitecrowdfunding_thumb'));
                            } else {
                                $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                                echo $this->htmlLink($item->getHref(), $fsContent . "<i style='background-image:url(" . $url . ")'></i>", array('class' => 'sitecrowdfunding_thumb'));
                            }
                            ?>
                            <div class='sitecrowdfunding_hover_info'>
                                <div class="sitecrowdfunding_stats sitecrowdfunding_grid_stats"> 
                                    <?php echo $this->sitecrowdfundingShareLinks($item, $this->projectOption); ?>  
                                    <span class="sitecrowdfunding_likes_comment_wrapper">
                                        <?php if (in_array('like', $this->projectOption)) : ?>
                                            <?php $count = $this->locale()->toNumber($item->likes()->getLikeCount()); ?>
                                            <?php $countText = $this->translate(array('%s like', '%s likes', $item->like_count), $count); ?>
                                            <span class="seaocore_icon_like" title="<?php echo $countText; ?>"><?php echo $this->translate($count); ?></span>
                                        <?php endif; ?>

                                        <?php if (in_array('comment', $this->projectOption)) : ?>
                                            <?php $count = $item->comment_count; ?>
                                            <span class="seaocore_icon_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $count), $this->locale()->toNumber($count)); ?>"><?php echo $this->translate($count); ?></span>
                                        <?php endif; ?> 
                                    </span>                      
                                </div>
                                <?php if($item->isFundingApproved()): ?>
                                <div class="sitecrowdfunding_backers">
                                    <?php if (in_array('backer', $this->projectOption)) : ?>
                                        <?php $count = $item->backer_count; ?>
                                        <?php $countText = $this->translate(array('%s backer', '%s backers', $count), $this->locale()->toNumber($count)) ?>
                                        <span class="backers" title="<?php echo $countText ?>">
                                            <?php echo $countText; ?> 
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <div class="txt_center">
                                <button onclick="window.location = '<?php echo $item->getHref() ?>'">
                                    <?php echo $this->translate('View'); ?>
                                </button>
                                </div>
                            </div>
                        </div>
                        <div class="sitecrowdfunding_info_wrapper">
                            <div class="sitecrowdfunding_info">
                                <div class="sitecrowdfunding_bottom_info sitecrowdfunding_grid_bott_info">

                                    <?php if (in_array('title', $this->projectOption)) : ?>
                                        <h3><?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), $this->titleTruncation), array('title' => $item->getTitle())) ?></h3>
                                    <?php endif; ?>
                                    <div class="sitecrowdfunding_grid_bottom_info">
                                        <?php $title = ""; ?>
                                        <?php if (in_array('owner', $this->projectOption)) : ?>
                                            <?php
                                            $title = $this->translate('by %s', $this->htmlLink($item->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($item->getOwner()->getTitle()), 17), array('title' => $item->getOwner()->getTitle())));
                                            echo $title;
                                            ?> 
                                        <?php endif; ?>
                                        <div class="sitecrowdfunding_desc" title="<?php echo $this->string()->truncate($this->string()->stripTags($item->description), 250) ?>">
                                            <?php echo $this->string()->truncate($this->string()->stripTags($item->description), $this->descriptionTruncation) ?>
                                        </div>
                                        <div class="sitecrowdfunding_backing_info">
                                            <?php if (isset($this->currenctTab) && $this->currenctTab == 'backed'): ?>
                                                <?php
                                                $backedAmount = $item->getFundedAmount(true);
                                                $result = $item->getBackingDetailForLoginUser();
                                                ?>
                                                <?php echo $this->translate('Backed Amount: ' . $backedAmount); ?>
                                                <?php
                                                echo $this->htmlLink(array(
                                                    'route' => 'sitecrowdfunding_backer',
                                                    'controller' => 'backer',
                                                    'action' => 'view-backed-details',
                                                    'project_id' => $item->project_id), $this->translate('details'), array(
                                                    'class' => 'buttonlink smoothbox'
                                                ));
                                                ?>  
                                                <?php if (isset($result) && count($result) == 1): ?>
                                                    <a href="<?php echo $this->url(Array('module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'print-invoice', 'backer_id' => Engine_Api::_()->sitecrowdfunding()->getDecodeToEncode($result[0]->backer_id)), 'default') ?>" target="_blank"><?php echo $this->translate("print invoice") ?></a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="sitecrowdfunding_bottom_info_category">
                                            <?php $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $item->category_id); ?>


                                            <?php if ($category->file_id): ?>
                                                <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
                                                 <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="">
                                            <?php elseif($category->font_icon): ?>
                                              <i class="fa <?php echo $category->font_icon; ?>"></i>
                                            <?php else: ?>
                                                 <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
                                                 <img src="<?php echo $src ?>" style="width: 16px; height: 16px;" alt="">
                                            <?php endif; ?> 
                                            <?php echo $this->htmlLink($category->getHref(), $category->getTitle()) ?>
                                        </div>

                                        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1) && in_array('location', $this->projectOption) && $item->location) : ?>
                                            <div class="sitecrowdfunding_bottom_info_location" title="<?php echo $item->location ?>">
                                                <i class="seao_icon_location"></i>
                                                <?php echo $this->string()->truncate($this->string()->stripTags($item->location), $this->truncationLocation); ?> 
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
                                                        <?php
                                                        echo $this->htmlLink(array(
                                                            'route' => 'sitecrowdfunding_specific',
                                                            'action' => 'edit',
                                                            'project_id' => $item->project_id), $this->translate('Edit Project'), array(
                                                            'class' => 'icon_project_edit'
                                                        ));
                                                        ?> 
                                                    </li> 
                                                <?php endif; ?>
                                                <li>
                                                    <?php
                                                    if ($canDelete && isset($this->isDeleteButton) && $this->isDeleteButton) {

                                                        if (empty($project->backer_count)):
                                                            echo $this->htmlLink(array('route' => 'sitecrowdfunding_specific', 'action' => 'delete', 'project_id' => $item->project_id, 'format' => 'smoothbox'), $this->translate('Delete Project'), array(
                                                                'class' => 'smoothbox icon_project_delete'
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
                            <?php if($item->isFundingApproved()): ?>
                            <?php
                            $fundedAmount = $item->getFundedAmount();
                            $fundedRatio = $item->getFundedRatio();
                            $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                            ?>
                            <?php
                            // Add Progressvive bar
                            echo $this->fundingProgressiveBar($fundedRatio);
                            ?>
                            <div class="sitecrowdfunding_funding_pledged_days_wrapper">
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
        <?php if (empty($this->is_ajax)) : ?>
            <div class = "seaocore_view_more mtop10" id="sitecrowdfunding_content_view_more">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => '', 'class' => 'buttonlink icon_viewmore')); ?>
            </div>
            <div class="seaocore_view_more" id="sitecrowdfunding_loding_image" style="display: none;">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
            </div>
            <div id="contenttype-hidden-ajax-reponse" style="display: none;"> </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="tip">
            <span>
                <?php if ($isUploadAllowed) : ?>
                    <?php echo $this->translate('You have not started any project yet for this content. %1$sClick here%2$s to add your first project.', "<a href='$url'>", "</a>"); ?>
                <?php else : ?>
                    <?php echo $this->translate('There is no project in this content yet.'); ?>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>
    <?php if (empty($this->is_ajax)) : ?>
        <script type="text/javascript">
            function viewMoreProjects(viewFormat)
            {
                $('sitecrowdfunding_content_view_more').style.display = 'none';
                $('sitecrowdfunding_loding_image').style.display = '';
                var params = {
                    requestParams:<?php echo json_encode($this->params) ?>
                };
                en4.core.request.send(new Request.HTML({
                    method: 'get',
                    'url': en4.core.baseUrl + '<?php echo $this->widgetPath; ?>',
                    data: $merge(params.requestParams, {
                        format: 'html',
                        subject: en4.core.subject.guid,
                        page: getNextProjectPage(),
                        is_ajax: 1,
                        loaded_by_ajax: true,
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('contenttype-hidden-ajax-reponse').innerHTML = responseHTML;
                        var projectcontainer = $('contenttype-hidden-ajax-reponse').getElement('#projects_manage<?php echo "_$identity"; ?>').innerHTML;
                        $('projects_manage<?php echo "_$identity"; ?>').innerHTML += projectcontainer;
                        $('sitecrowdfunding_loding_image').style.display = 'none';
                        $('contenttype-hidden-ajax-reponse').innerHTML = "";
                        fundingProgressiveBarAnimation();
                    }
                }));
                return false;
            }
        </script>
    <?php endif; ?>

    <?php if ($this->showContent == 3): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                hideViewMoreProjectLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php elseif ($this->showContent == 2): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                hideViewMoreProjectLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php else: ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                $('sitecrowdfunding_content_view_more').style.display = 'none';
            });
        </script>
        <?php
        echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding"), array("orderby" => $this->orderby));
        ?>
    <?php endif; ?>
</div>

<script type="text/javascript">

    function getNextProjectPage() {
        return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }

    function hideViewMoreProjectLink(showContent) {
        if (showContent == 3) {
            $('sitecrowdfunding_content_view_more').style.display = 'none';
            var totalCount = '<?php echo $this->paginator->count(); ?>';
            var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

            function doOnScrollLoadChannel()
            {
                if (typeof ($('sitecrowdfunding_content_view_more').offsetParent) != 'undefined') {
                    var elementPostionY = $('sitecrowdfunding_content_view_more').offsetTop;
                } else {
                    var elementPostionY = $('sitecrowdfunding_content_view_more').y;
                }
                if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {

                    if ((totalCount != currentPageNumber) && (totalCount != 0))
                        viewMoreProjects();
                }
            }
            window.onscroll = doOnScrollLoadChannel;

        } else if (showContent == 2) {
            var view_more_content = $('sitecrowdfunding_content_view_more');
            view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
            view_more_content.removeEvents('click');
            view_more_content.addEvent('click', function () {
                viewMoreProjects();
            });
        }
    }
</script>
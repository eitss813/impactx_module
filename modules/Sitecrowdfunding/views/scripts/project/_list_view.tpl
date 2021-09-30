<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _list_view.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$identity = $this->idenity;
if ($this->id) :
    $identity = $this->id;
endif;
if ($this->paginatorListView) {
    $this->paginator = $this->paginatorListView;
}
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>

    <ul class='projects_manage sitecrowdfunding_projects_list_view' id='projects_manage<?php echo "_" . $identity; ?>'>
        <?php foreach ($this->paginator as $item): ?> 
            <li>
                <div class="sitecrowdfunding_thumb_wrapper sitecrowdfunding_thumb_viewer">
                    <div class="sitecrowdfunding_list_thumb"> 
                        <?php $fsContent = ""; ?>
                        <?php if ($item->featured && in_array('featured', $this->projectOption)): ?>
                            <?php $fsContent .= '<div class="sitecrowdfunding_featured" style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.featuredcolor', '#f72828') . '">' . $this->translate("Featured") . '</div>'; ?>
                        <?php endif; ?>
                        <?php if ($item->sponsored && in_array('sponsored', $this->projectOption)): ?>
                            <?php $fsContent .= '<div class="sitecrowdfunding_sponsored"  style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.sponsoredcolor', '#FC0505') . '">' . $this->translate("Sponsored") . '</div>'; ?>
                        <?php endif; ?>

                        <?php
                        if ($item->photo_id) {
                            echo $this->htmlLink($item->getHref(), "" . $fsContent . $this->itemBackgroundPhoto($item, null, null, array('tag' => 'i')), array('class' => 'sitecrowdfunding_thumb'));
                        } else {
                            $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                            echo $this->htmlLink($item->getHref(), "" . $fsContent . "<i style='background-image:url(" . $url . ")'></i>", array('class' => 'sitecrowdfunding_thumb'));
                        }
                        ?> 
                        <div class="sitecrowdfunding_list_hover_info"> 
                            <div class="sitecrowdfunding_stats sitecrowdfunding_grid_stats">
                                <?php echo $this->sitecrowdfundingShareLinks($item, $this->projectOption); ?>
                                <div class="txt_center sitecrowdfunding_likes_comment_wrapper">
                                    <?php if (in_array('like', $this->projectOption)) : ?>
                                        <?php $count = $this->locale()->toNumber($item->likes()->getLikeCount()); ?>
                                        <?php $countText = $this->translate(array('%s like', '%s likes', $item->like_count), $count); ?>
                                        <span class="seaocore_icon_like" title="<?php echo $countText; ?>">
                                            <?php echo $this->translate($count); ?> 
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('comment', $this->projectOption)) : ?>
                                        <?php $count = $item->comment_count; ?>
                                        <span class="seaocore_icon_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $count), $this->locale()->toNumber($count)); ?>">
                                            <?php echo $this->translate($count); ?> 
                                        </span>
                                    <?php endif; ?> 
                                </div>
                            </div>
                            <?php if($item->isFundingApproved()):?>
                            <div class="sitecrowdfunding_backers">
                                <?php if (in_array('backer', $this->projectOption)) : ?>
                                    <?php $count = $item->backer_count; ?>
                                    <?php $countText = $this->translate(array('%s backer', '%s backers', $count), $this->locale()->toNumber($count)) ?>
                                    <span class="backers" title="<?php echo $countText; ?>">
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
                    <div class="sitecrowdfunding_info sitecrowdfunding_list_project_info">
                        <div class="sitecrowdfunding_listing_style">
                            <article class="sitecrowdfunding_listing_style_box">
                                <div class="sitecrowdfunding_listing_style_details">
                                    <div>
                                        <div>
                                            <?php if (in_array('title', $this->projectOption)) : ?>
                                                <h3 class="sitecrowdfunding_listing_style_box_title">
                                                    <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), $this->titleTruncationListView), array('title' => $item->getTitle())) ?>
                                                    <div>
                                                        <small>
                                                          <?php if (in_array('owner', $this->projectOption)) : ?>
                                                              <?php $owner = $item->getOwner(); ?> 
                                                              <?php echo $this->translate('by %s', $this->htmlLink($owner->getHref(), $this->string()->truncate($this->string()->stripTags($owner->getTitle()), 17), array('class' => 'mright5'), array('title' => $owner->getTitle()))); ?>
                                                          <?php endif; ?>
                                                          <?php if (in_array('startDate', $this->projectOption)) : ?>
                                                              |    <?php echo $this->timestamp(strtotime($item->start_date), array('class' => 'pright10 mleft5')) ?> 
                                                          <?php endif; ?> 

                                                          </small>
                                                          <span class="sitecrowdfunding_likes_comment_wrapper" style="display: inline-block;">
                                                            <?php if (in_array('like', $this->projectOption)) : ?>
                                                                |<?php $count = $this->locale()->toNumber($item->likes()->getLikeCount()); ?>
                                                                <?php $countText = $this->translate(array('%s like', '%s likes', $item->like_count), $count); ?>
                                                                <span class="seaocore_icon_like mleft5" title="<?php echo $this->translate($countText); ?>"><?php echo $this->translate($count); ?></span>
                                                            <?php endif; ?>
                                                            <?php if (in_array('comment', $this->projectOption)) : ?>
                                                                <?php $count = $item->comment_count; ?>
                                                                <span class="seaocore_icon_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $count), $this->locale()->toNumber($count)); ?>"><?php echo $this->translate($count); ?></span>
                                                            <?php endif; ?> 
                                                        </span></div>
                                                </h3>                           
                                            <?php endif; ?>

                                            <!--<div class="amenities">
                                            <?php if (in_array('favourite', $this->projectOption) && Engine_Api::_()->user()->getViewer()->getIdentity()) : ?>
                                                <?php $resource_id = $item->getIdentity(); ?>
                                                <?php $resource_type = $item->getType(); ?>
                                                <?php $hasFavourite = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite($resource_type, $resource_id); ?>

                                                <?php $unfavourites = $resource_type . '_unfavourites_' . $resource_id ?>
                                                <?php $favourites = $resource_type . '_most_favourites_' . $resource_id ?>
                                                <?php $fav = $resource_type . '_favourite_' . $resource_id; ?>
                                                                <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $unfavourites; ?>" style ='display:<?php echo $hasFavourite ? "inline-block" : "none" ?>'  class="seaocore_icon_unfavourite <?php echo $unfavourites; ?>" title="<?php echo $this->translate("Unfavourite"); ?>">
                                                                </a>
                                                                <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $favourites; ?>" style ='display:<?php echo empty($hasFavourite) ? "inline-block" : "none" ?>' class="seaocore_icon_favourite <?php echo $favourites; ?>" title="<?php echo $this->translate("Favourite"); ?>">

                                                                </a>
                                                                <input type ="hidden" id = "<?php echo $fav ?>" value = '<?php echo $hasFavourite ? $hasFavourite[0]['favourite_id'] : 0; ?>' />
                                            <?php endif; ?>

                                            </div> -->
                                        </div>
                                        <?php if($item->isFundingApproved()): ?>
                                        <div>
                                            <div class="goal_amount_container">
                                                <?php
                                                $totalAmount = $item->goal_amount;
                                                $totalAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($totalAmount);
                                                ?>
                                                <span class="goal_amount">
                                                    <?php echo $this->translate("Goal Amount %s", $totalAmount); ?> 
                                                </span>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p>
                                          <span title="<?php echo $this->string()->truncate($this->string()->stripTags($this->translate($item->description)), 250); ?>">
                                            <?php echo $this->string()->truncate($this->string()->stripTags($this->translate($item->description)), $this->descriptionTruncation) ?>                                            </span>
                                            <?php if($item->category_id): ?>
                                            <span class="sitecrowdfunding_bottom_info_category">
                                              <?php $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $item->category_id); ?>
                                              <?php if ($category->file_id): ?>
                                                  <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
                                                  <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="">
                                              <?php elseif ($category->font_icon): ?>
                                                  <i class="fa <?php echo $category->font_icon; ?>"></i>
                                              <?php else: ?>
                                                  <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
                                                  <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="">
                                              <?php endif; ?> 
                                              <?php echo $this->htmlLink($category->getHref(), $category->getTitle()) ?>
                                            </span>
                                            <?php endif; ?>

                                          <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1) && in_array('location', $this->projectOption) && $item->location) : ?>
                                              <span class="sitecrowdfunding_bottom_info_location" title="<?php echo $item->location ?>">
                                                  <i class="seao_icon_location"></i>
                                                  <?php echo $this->string()->truncate($this->string()->stripTags($item->location), $this->truncationLocation); ?> 
                                              </span>
                                          <?php endif; ?>


                                            <span class="sitecrowdfunding_list_view_options"> 
                                                <?php if (isset($this->currenctTab) && $this->currenctTab == 'backed'): ?>
                                                    <?php
                                                    //TODO fix funding
                                                    //$backedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->getFundedAmount(true));
                                                    $result = $item->getBackingDetailForLoginUser();
                                                    ?>
                                                    <?php //echo $this->translate('Backed Amount: %s', $backedAmount); ?>

                                                    <?php
                                                    echo $this->htmlLink(array(
                                                        'route' => 'sitecrowdfunding_backer',
                                                        'controller' => 'backer',
                                                        'action' => 'view-backed-details',
                                                        'project_id' => $item->project_id), $this->translate('Details'), array(
                                                        'class' => 'buttonlink smoothbox'
                                                    ));
                                                    ?>  
                                                    <?php if (isset($result) && count($result) == 1): ?>
                                                      <a href="<?php echo $this->url(Array('module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'print-invoice', 'backer_id' => Engine_Api::_()->sitecrowdfunding()->getDecodeToEncode($result[0]->backer_id)), 'default') ?>" target="_blank"><?php echo $this->translate("| Print invoice") ?></a>
                                                    <?php else: ?> 
                                                      <?php
                                                      echo $this->htmlLink(array(
                                                          'route' => 'sitecrowdfunding_backer',
                                                          'controller' => 'backer',
                                                          'action' => 'view-backed-details',
                                                          'project_id' => $item->project_id), $this->translate('| Print invoice'), array(
                                                          'class' => 'smoothbox'
                                                      ));
                                                      ?>
 
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </span> 

                                            <span class="sitecrowdfunding_list_view_options"> 
                                                <?php
                                                $canDelete = Engine_Api::_()->sitecrowdfunding()->canDeletePrivacy($item->parent_type, $item->parent_id, $item);
                                                $canEdit = Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($item->parent_type, $item->parent_id, $item);
                                                ?>
                                                <?php if ($canEdit && isset($this->isEditButton) && $this->isEditButton): ?> 
                                                    <?php
                                                    echo $this->htmlLink(array(
                                                        'route' => 'sitecrowdfunding_specific',
                                                        'action' => 'edit',
                                                        'project_id' => $item->project_id), $this->translate('Edit Project'), array(
                                                        'class' => ''
                                                    ));
                                                    ?>  
                                                <?php endif; ?>
                                                <?php if (isset($this->isEditButton) && $this->isEditButton && isset($this->isDeleteButton) && $this->isDeleteButton): ?>
                                                    |<?php endif; ?>
                                                <?php
                                                if ($canDelete && isset($this->isDeleteButton) && $this->isDeleteButton) {

                                                    if (empty($project->backer_count)):
                                                        echo $this->htmlLink(array('route' => 'sitecrowdfunding_specific', 'action' => 'delete', 'project_id' => $item->project_id, 'format' => 'smoothbox'), $this->translate('Delete Project'), array(
                                                            'class' => 'smoothbox'
                                                        ));
                                                    else :
                                                        ?>

                                                        <a href="javascript:void(0);" class="buttonlink icon_project_delete " onclick='deleteProjectPrompt()'><?php echo $this->translate('Delete Project'); ?></a>
                                                    <?php endif; ?>

                                                <?php } ?>  

                                            </span>
                                        </p>
                                        <?php
                                        //TODO fix funding
                                         if($item->isFundingApproved() && false): ?>
                                        <div>
                                            <span class="site_crowdfunding_funded_ratio">
                                                <?php $fundedAmount = $item->getFundedAmount(); ?>
                                                <?php $fundedRatio = $item->getFundedRatio(); ?>
                                                <span class="bold"><?php echo $this->translate("$fundedRatio %"); ?></span>
                                                <?php echo $this->translate('Funded') ?>
                                            </span>
                                            <span class="sitecrowdfunding_pledged">
                                                <?php $pledged = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount); ?>
                                                <span class="bold">
                                                    <?php echo $this->translate($pledged); ?>
                                                </span>
                                                <span><?php echo $this->translate('Backed') ?></span>
                                            </span>     
                                            <span class="sitecrowdfunding_daysleft">
                                                <?php if (in_array('endDate', $this->projectOption)) : ?>
                                                    <?php echo $item->getRemainingDays(); ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        </div> 
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

                <?php echo $this->translate('Get started by %1$sposting%2$s a new project.', '<a href="' . $this->url(array('action' => 'create'), 'sitecrowdfunding_project_general', true) . '">', '</a>'); ?>

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
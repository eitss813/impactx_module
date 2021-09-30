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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css'); ?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/pinboard.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/mooMasonry.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php if ($this->countPage > 0): ?>
    <?php if ($this->autoload): ?>
        <div id="pinboard_<?php echo $this->identity ?>">
            <?php if (isset($this->params['defaultLoadingImage']) && $this->params['defaultLoadingImage']): ?>
                <div class="sitecrowdfunding_profile_loading_image"></div>
            <?php endif; ?>
        </div>
        <script type="text/javascript">
            var layoutColumn = 'middle';
            if ($("pinboard_<?php echo $this->identity ?>").getParent('.layout_left')) {
                layoutColumn = 'left';
            } else if ($("pinboard_<?php echo $this->identity ?>").getParent('.layout_right')) {
                layoutColumn = 'right';
            }
            PinBoardSeaoObject[layoutColumn].add({
                contentId: 'pinboard_<?php echo $this->identity ?>',
                widgetId: '<?php echo $this->identity ?>',
                totalCount: '<?php echo $this->totalCount ?>',
                requestParams:<?php echo json_encode($this->params) ?>,
                detactLocation: <?php echo $this->detactLocation; ?>,
                responseContainerClass: 'layout_sitecrowdfunding_pinboard_browse_projects'
            });

        </script>
    <?php else: ?>
        <?php if (!$this->autoload && !$this->is_ajax_load): ?> 
            <div id="pinboard_<?php echo $this->identity ?>"></div>
            <script type="text/javascript">
                en4.core.runonce.add(function () {
                    var pinBoardViewMore = new PinBoardSeaoViewMore({
                        contentId: 'pinboard_<?php echo $this->identity ?>',
                        widgetId: '<?php echo $this->identity ?>',
                        totalCount: '<?php echo $this->totalCount ?>',
                        viewMoreId: 'seaocore_view_more_<?php echo $this->identity ?>',
                        loadingId: 'seaocore_loading_<?php echo $this->identity ?>',
                        requestParams:<?php echo json_encode($this->params) ?>,
                        responseContainerClass: 'layout_sitecrowdfunding_pinboard_browse_projects'
                    });
                    PinBoardSeaoViewMoreObjects.push(pinBoardViewMore);
                });
            </script>
        <?php endif; ?>  
        <?php $countButton = count($this->show_buttons); ?>
        <?php foreach ($this->paginator as $project): ?>
            <?php
            $noOfButtons = $countButton;
            if ($this->show_buttons):
                $alllowComment = (in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $project->authorization()->isAllowed($this->viewer(), "comment");
                if (in_array('comment', $this->show_buttons) && !$alllowComment) :
                    $noOfButtons--;
                endif;
                if (in_array('like', $this->show_buttons) && !$alllowComment) :
                    $noOfButtons--;
                endif;
            endif;
            ?>

            <div class="seaocore_list_wrapper" style="width:<?php echo $this->params['itemWidth'] ?>px;">
                <div class="seaocore_board_list b_medium" style="width:<?php echo $this->params['itemWidth'] - 18 ?>px;"> 
                    <div>
                        <div class="seaocore_board_list_thumb">
                            <a href="<?php echo $project->getHref() ?>" class="seaocore_thumb sitecrowdfunding_thumb_viewer">
                                <span class='project_overlay'></span> 
                                <?php
                                $options = array('align' => 'center');
                                if (isset($this->params['withoutStretch']) && $this->params['withoutStretch']):
                                    $options['style'] = 'width:auto; max-width:' . ($this->params['itemWidth'] - 18) . 'px;';
                                endif;
                                ?>  
                                <?php echo $this->itemPhoto($project, $this->thumbnailType, '', $options); ?>  
                            </a>
                        </div>

                        <div class="seaocore_board_list_btm">       
                            <?php if (!empty($this->projectOption) && in_array('owner', $this->projectOption)): ?>                
                                <?php echo $this->itemPhoto($project->getOwner(), 'thumb.profile'); ?>                 
                                <div class="o_hidden seaocore_stats seaocore_txt_light">            
                                    <b><?php echo $this->htmlLink($project->getOwner()->getHref(), $this->translate($this->translate($project->getOwner()->getTitle())), array('class' => 'thumbs_author')) ?> </b>     
                                </div>
                            <?php endif; ?>             
                        </div>
                        <?php if (!empty($this->projectOption) && in_array('title', $this->projectOption)): ?>
                            <div class="seaocore_title" title="<?php echo $project->getTitle(); ?>">
                                <?php echo $this->htmlLink($project, Engine_Api::_()->seaocore()->seaocoreTruncateText($this->translate($project->getTitle()), $this->titleTruncation)) ?>
                            </div>
                        <?php endif; ?>

                        <div class="seaocore_board_list_cont">
                            <?php if ($this->descriptionTruncation): ?>
                                <div class="seaocore_description" title="<?php echo $project->getDescription() ?>">
                                    <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($this->translate($project->getDescription()), $this->descriptionTruncation) ?>
                                </div>  
                            <?php endif; ?>
                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1) && in_array('location', $this->projectOption) && $project->location): ?>
                                <div class="sitecrowdfunding_bottom_info_location" title="<?php echo $project->location; ?>">
                                    <i class="seao_icon_location"></i>
                                    <?php
                                    $location = Engine_Api::_()->seaocore()->seaocoreTruncateText($project->location, $this->truncationLocation);
                                    echo $this->translate(" %s", $location);
                                    ?>
                                </div>
                            <?php endif ?>
                            <?php if($project->isFundingApproved()): ?>
                            <div class="seao_listings_stats">
                                <span class="funded_percent">
                                    <?php
                                    $fundedAmount = $project->getFundedAmount();
                                    $fundedRatio = $project->getFundedRatio();
                                    $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                                    ?>
                                    <b><?php echo $this->translate("%s", $fundedRatio.'%'); ?></b>
                                    <br />
                                    <?php echo $this->translate("Funded"); ?>
                                </span>
                                <?php if (in_array('endDate', $this->projectOption)): ?>
                                    <span class="end_date">
                                        <?php $days = $project->getRemainingDays(true);  ?>
                                        <?php echo $project->getRemainingDays(); ?>
                                         <?php //echo $this->translate(array("<b>%s</b><br /> Day to go", '<b>%s</b><br /> Days to goooooooo',$days),$this->locale()->toNumber($days));
 ?>
                                    </span>
                                <?php endif ?>
                                <span class="pledged_amount">
                                    <b><?php echo $this->translate("%s", $fundedAmount); ?></b>
                                    <br />
                                    <?php echo $this->translate("Backed"); ?>

                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($this->userComment)) : ?>
                            <div class="seaocore_board_list_comments o_hidden">
                                <?php echo $this->action("list", "pin-board-comment", "seaocore", array("type" => $project->getType(), "id" => $project->project_id, 'widget_id' => $this->identity)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($this->show_buttons)): ?>
                            <div class="seaocore_board_list_action_links">
                                <div class="sitecrowdfunding_social_icons txt_center mbot5">
                                    <?php $this->sitecrowdfundingPinboardShareLinks($project, $this->show_buttons); ?>
                                    <?php if ((in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $alllowComment && !empty($this->userComment)): ?>
                                        <?php if (in_array('comment', $this->show_buttons)): ?>
                                            <a href='javascript:void(0);' onclick="en4.seaocorepinboard.comments.addComment('<?php echo $project->getGuid() . "_" . $this->identity ?>')" class="seaocore_board_icon comment_icon" title="<?php echo $this->translate('Comment'); ?>"></a> 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="sitecrowdfunding_likes_comment txt_center mbot5">
                                    <?php if (in_array('like', $this->projectOption)) : ?>
                                        <?php $count = $this->locale()->toNumber($project->likes()->getLikeCount()); ?>
                                        <?php $countText = $this->translate(array('%s like', '%s likes', $count), $count); ?>
                                        <span class="seaocore_icon_like" title="<?php echo $countText ?>"><?php echo $this->translate($count); ?></span>
                                    <?php endif; ?>
                                    <?php if (in_array('comment', $this->projectOption)) : ?>
                                        <?php $countText = $this->translate(array('%s comment', '%s comments', $project->comment_count), $this->locale()->toNumber($project->comment_count)); ?>
                                        <span class="seaocore_icon_comment" title="<?php echo $countText; ?>"><?php echo $this->translate($project->comment_count . ''); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if($project->isFundingApproved()): ?>
                                <div class="sitecrowdfunding_bottom_info_backers mbot5">
                                  <?php if (in_array('backer', $this->projectOption)) : ?>
                                        <?php $count = $project->backer_count; ?>
                                        <?php $countText = $this->translate(array('%s backer', '%s backers', $count), $this->locale()->toNumber($count)) ?>
                                        <span title="<?php echo $countText; ?>">
                                            <?php echo $this->translate($countText); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <div class="txt_center sitecrowdfunding_pinboard_btn">
                                <button onclick="window.location = '<?php echo $project->getHref() ?>'">
                                <?php echo $this->translate('View'); ?>
                                </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div> </div>
        <?php endforeach; ?>

        <?php if (!$this->autoload && !$this->is_ajax_load): ?>
            <div class="seaocore_view_more mtop10 dnone" id="seaocore_view_more_<?php echo $this->identity ?>">
                <a href="javascript:void(0);" id="" class="buttonlink icon_viewmore"><?php echo $this->translate('View More') ?></a>
            </div>
            <div class="seaocore_loading dnone" id="seaocore_loading_<?php echo $this->identity ?>" >
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" style="margin-right: 5px;">
                <?php echo $this->translate('Loading...') ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php else: ?>
    <?php if ($this->is_ajax_load): ?>
        <script type="text/javascript">
            var layoutColumn = 'middle_page_browse';
            PinBoardSeaoObject[layoutColumn].currentIndex++;
        </script>
    <?php endif; ?>
    <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
        <div class="tip">
            <span>
                <?php if (isset($this->formValues['tag_id']) || isset($this->formValues['category_id']) || isset($this->formValues['location']) || isset($this->formValues['search'])): ?> 
                    <?php echo $this->translate('Nobody has started a project with that criteria.'); ?>
                <?php else: ?>  
                    <?php echo $this->translate('No projects has been started .'); ?>
                <?php endif; ?>  
                <?php if ($this->canCreate): ?>
                    <?php echo $this->translate('Be the first to %1$sstart%2$s one!', '<a href="' . $this->url(array('action' => 'create'), "Sitecrowdfunding_project_general") . '">', '</a>'); ?>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>
<?php endif; ?>

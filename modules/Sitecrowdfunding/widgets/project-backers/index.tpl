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
<?php //echo $this->content()->renderWidget('sitecrowdfunding.project-funding-chart', array()); ?>

<h3>Project Funders</h3>
<div id="scroll_link_project"></div>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_comment.css'); ?>
<?php $id = $this->identity ?>
<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_sitecrowdfunding_project_backers')
        }
        en4.sitecrowdfunding.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>
<?php if ($this->showContent): ?>
    <script type="text/javascript">
        var sitecrowdfundingPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
        var paginateSitecrowdfundingPage_<?php echo "$id"; ?> = function (page) {
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>,
                responseContainer: $$('.layout_sitecrowdfunding_project_backers')
            }
            params.requestParams.content_id = <?php echo sprintf('%d', $this->identity) ?>;
            params.requestParams.page = page;
            en4.sitecrowdfunding.ajaxTab.sendReq(params);

        }
    </script>
    <div>
        <?php if (!empty($this->backersCount)): ?>
            <ul class="sitecrowdfunding_thumbs thumbs_nocaptions sitecrowdfunding_project_backers">
                <?php foreach ($this->paginator as $backer): ?> 
                    <li class="selected" id="thumbs-photo-<?php echo $backer->backer_id ?>"  >
                        <?php $owner = Engine_Api::_()->getItem('user', $backer->user_id); ?>
                        <?php if ($backer->is_private_backing): ?>
                            <?php $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_user_thumb_profile.jpg"; ?>
                            <i background-image: url('<?php echo $url; ?>')></i>
                        <?php else: ?>
                            <?php if ($owner->photo_id): ?>
                                <a href="<?php echo $owner->getHref(); ?>">
                                    <?php echo $this->itemBackgroundPhoto($owner, null, $owner->getTitle(), array('tag' => 'i')); ?>
                                </a>          
                            <?php else : ?>
                                <a href = '<?php echo $owner->getHref(); ?>'><i style="background-image: url('<?php echo $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/owner-defaul-image.jpg"; ?>')"></i></a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="p5 txt_center thumbs_background">
                            <?php if ($backer->is_private_backing): ?>
                                <b><?php echo $this->translate('Anonymous'); ?></b>
                            <?php else: ?>
                                <?php echo $this->htmlLink($owner->getHref(), $this->translate($owner->getTitle())); ?>
                            <?php endif; ?>
                            <?php $backedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount); ?>
                            <p><?php echo $this->translate("Backed: %s", $backedAmount); ?></p>
                        </div>

                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('There is no Internal Backer for this project.'); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($this->paginator->count() > 1): ?>
        <div >
            <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
                <div id="user_group_members_previous" class="paginator_previous">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateSitecrowdfundingPage_' . $id . '(sitecrowdfundingPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
                </div>
            <?php endif; ?>
            <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
                <div id="user_group_members_next" class="paginator_next">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginateSitecrowdfundingPage_' . $id . '(sitecrowdfundingPage + 1)', 'class' => 'buttonlink_right icon_next')); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php echo $this->content()->renderWidget('sitecrowdfunding.project-external-funding', array()); ?>

<style type="text/css">
    /*.layout_sitecrowdfunding_project_backers ul.sitecrowdfunding_project_backers.thumbs_nocaptions > li:nth-child(4n+4){*/
    /*    margin-right: 7% !important;*/
    /*}*/
    .sitecrowdfunding_thumbs {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }
    .layout_sitecrowdfunding_project_backers ul.sitecrowdfunding_project_backers.thumbs_nocaptions > li {
        width: 23%;
        margin: 1%;
        margin-right: 1% !important;
    }

    @media (max-width: 767px) {
        .layout_sitecrowdfunding_project_backers .sitecrowdfunding_thumbs .thumbs_background {
            margin-right: 5%;
            margin-left: 5%;
        }
    }
</style>
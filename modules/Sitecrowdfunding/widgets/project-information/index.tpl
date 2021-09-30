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

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_comment.css'); ?>
<div class="sitecrowdfunding_project_information_titlebox">
    <div class="sitecrowdfunding_project_information_title">
        <?php echo $this->htmlLink($this->project->getHref(), $this->string()->truncate($this->string()->stripTags($this->project->getTitle()), $this->titleTruncation), array('title' => $this->project->getTitle())) ?>
    </div>
    <div class="sitecrowdfunding_project_information_author">
        <span>by</span>
        <?php echo $this->htmlLink($this->owner->getHref(), $this->translate($this->owner->getTitle())); ?>
    </div>
</div>
<div class="sitecrowdfunding_project_information_desc">
    <div class="" title="<?php echo $this->project->getDescription(); ?>">
        <?php echo $this->translate($this->string()->truncate($this->string()->stripTags($this->project->getDescription()), $this->descriptionTruncation)); ?>        
    </div>
    <div class="mtop10 sitecrowdfunding_project_information_category">

        <?php if ($this->category->file_id) : ?>
            <?php $url = Engine_Api::_()->storage()->get($this->category->file_id)->getPhotoUrl(); ?>
            <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="<?php echo $this->category->getTitle(); ?>">
        <?php elseif($this->category->font_icon): ?>
          <i class="fa <?php echo $this->category->font_icon; ?>"></i>
        <?php else: ?>
             <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
            <img src="<?php echo $src ?>" style="width: 16px; height: 16px;" alt="<?php echo $this->category->getTitle(); ?>">
        <?php endif; ?> 
 
        <?php $categoryUrl = $this->url(array('category_id' => $this->category->category_id, 'categoryname' => $this->category->getCategorySlug()), "sitecrowdfunding_general_category");
        ?>
        <a href="<?php echo $categoryUrl; ?>"><?php echo $this->translate($this->category->getTitle()); ?></a>
    </div> 
        <div class="mtop10">
        <span><?php echo $this->translate("Published On : "); ?></span>
        <?php echo date('M d, Y', strtotime($this->project->start_date)); ?>
    </div>
    <div class="">
        <span><?php echo $this->translate("Funding Ends :"); ?></span>
        <?php echo date('M d, Y', strtotime($this->project->funding_end_date)); ?>
    </div>
    <?php if (isset($this->project->location) && !empty($this->project->location)): ?>
        <div class="mtop10 sitecrowdfunding_bottom_info_location"><i class="seao_icon_location"></i><?php echo $this->project->location; ?></div>
    <?php endif; ?>
    <div class="mtop10 fund_graph">
        <?php
        $fundedAmount = $this->project->getFundedAmount();
        $fundedRatio = $this->project->getFundedRatio();
        $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
        ?>
        <span class="mtop10"><strong><?php echo $this->translate("%s",$fundedRatio.'%'); ?></strong><br />Funded</span>
        <span class="mtop10"><strong><?php echo $this->translate("%s",$fundedAmount); ?></strong><br/>Backed</span>
        <?php $days = $this->project->getRemainingDays() ?>
        <span class="mtop10"> 
            <?php echo $days; ?>
        </span>
    </div>
</div>
<style>
    @media(max-width:767px){
        .mobilehide{
            display: none !important;
        }
    }
</style>
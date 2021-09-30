<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2017-03-22 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<?php
$i = 1;
$widthArr = array(41.66, 33.33, 25, 33.33, 25, 25, 16.66);
?>
<ul class="sitecrowdfunding_sponsored_cat_with_image">
    <?php foreach ($this->categories as $category) : ?>

        <?php if ($i == 1 || $i == 4) : ?>
            <li>
            <?php endif; ?> 
            <?php if ($category->file_id): ?>
                <?php $src = $this->storage->get($category->file_id, '')->getPhotoUrl(); ?>
                <?php $htmlImage = "<img src=" . $src .">"; ?>
            <?php elseif (!empty($category->font_icon)): ?>
                <?php $htmlImage = "<i class='fa ".$category->font_icon."' ></i>"; ?>
            <?php else: ?>
                <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
                <?php $htmlImage = "<img src=" . $src .">"; ?> 
            <?php endif; ?> 

            <?php $link = ""; ?>
            <?php
              $link = $this->htmlLink($category->getHref(), $htmlImage);
            ?>  
            <div onclick="window.location = '<?php echo $category->getHref(); ?>'" style="width:<?php echo $widthArr[$i - 1]; ?>%; height:<?php echo $this->height; ?>px; cursor:pointer;" class="seao_sponscat_thumb_wrap">
                
                <?php if (!empty($category->photo_id)): ?>
                    <?php $temStorage = $this->storage->get($category->photo_id, ''); ?>
                    <?php if (!empty($temStorage)): ?>
                         <?php echo $this->itemBackgroundPhoto($temStorage, null, $temStorage->getTitle(), array('class' => 'dblock seao_cat_gd_img', 'tag' => 'a', 'href' => $category->getHref())); ?>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo $category->getHref(); ?>" class="dblock seao_cat_gd_img" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_normal.png');"></a> 
                <?php endif; ?>
                <div class="seao_sponscat_info">
                  <div class="seao_sponscat_info_txt">
                    <?php echo $link; ?>
                    <span class='seao_sponscat_title'><?php echo $this->translate($category->category_name); ?></span> 
                   </div>
                </div>
            </div>
            <?php if ($i == 3) : ?>
            </li>
        <?php endif; ?>
        <?php $i++; ?>
    <?php endforeach; ?>

    <?php for ($start = $i; $start <= 6; $start++): ?>
        <?php if ($start == 1 || $start == 4) : ?>
            <li>
            <?php endif; ?>
              <div style="width:<?php echo $widthArr[$start - 1]; ?>%; height:<?php echo $this->height; ?>px;" class="seao_sponscat_thumb_wrap"></div>
              <?php if ($start == 3) : ?>
            </li>
        <?php endif; ?>
    <?php endfor; ?>
    <div style="width:<?php echo $widthArr[6]; ?>%; height:<?php echo $this->height;?>px;" class="seao_sponscat_thumb_wrap">
        <a href="<?php echo $this->url(array('action' => 'categories'), 'sitecrowdfunding_general', true); ?>" class="dblock seao_cat_gd_img" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/fundraiser.jpg');"></a>   
        <div class="seao_sponscat_info">
          <div class="seao_sponscat_info_txt">
            <a href="<?php echo $this->url(array('action' => 'categories'), 'sitecrowdfunding_general', true); ?>">
              <i class="fa fa-arrow-circle-o-right"></i>
              <span class="seao_sponscat_title"><?php echo $this->translate("View all Fundraisers"); ?></span>
            </a>
          </div>
        </div>
    </div>
</li>
</ul>


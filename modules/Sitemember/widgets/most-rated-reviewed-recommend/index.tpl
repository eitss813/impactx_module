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

<?php
if ($this->viewType == 'gridview'):
  $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
endif;
?>
<?php if ($this->viewType == 'listview'): ?>
  <ul class="seaocore_sidebar_list <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
    <?php foreach ($this->members as $sitemember): ?>
      <li>
        <?php echo $this->htmlLink($sitemember->getHref(array()), $this->itemPhoto($sitemember, 'thumb.icon')) ?>
        <div class='seaocore_sidebar_list_info'>
          <div class='seaocore_sidebar_list_title'>
            <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->truncation), array('title' => $sitemember->getTitle())); ?>
          </div>

          <?php if (!empty($this->statistics)) : ?>
            <?php
            $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');
            echo $this->memberInfo($sitemember, $this->statistics);
            ?>
          <?php endif; ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <?php $isLarge = ($this->columnWidth > 170); ?>
  <ul class="seaocore_sidebar_list sitemember_grid_view_sidebar o_hidden <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
    <?php foreach ($this->members as $sitemember): ?>
      <li class="sitemember_grid_view" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
        <div class="sitemember_grid_thumb">
          <a href="<?php echo $sitemember->getHref(array('profile_link' => 1)) ?>" class ="sitemember_thumb">
            <?php
            $url = $sitemember->getPhotoUrl($isLarge ? 'thumb.profile' : 'thumb.profile');
            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
            endif;
            ?>
            <span style="background-image: url(<?php echo $url; ?>); <?php if($this->circularImage):?>height:<?php echo $this->circularImageHeight; ?>px;<?php else:?> <?php if ($isLarge): ?> height:160px; <?php endif; ?> <?php endif;?>;?>"></span>
          </a>
            
          <?php if (!empty($this->titlePosition)) : ?>
            <div class="sitemember_grid_title">
              <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->truncation), array('title' => $sitemember->getTitle())); ?>
            </div>
          <?php endif; ?>
        </div>


        <div class="sitemember_grid_info">
          <?php if (empty($this->titlePosition)) : ?>
            <div class="bold">
              <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->truncation), array('title' => $sitemember->getTitle())) ?>

            </div>
          <?php endif; ?>

          <?php if (!empty($this->statistics)) : ?>
            <?php
            echo $this->memberInfo($sitemember, $this->statistics);
            ?>
          <?php endif; ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php if($this->circularImage):?>
<script type="text/javascript">

var list=$$('.layout_sitemember_most_rated_reviewed_recommend').getElements('.sitemember_grid_view');
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
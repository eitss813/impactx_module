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
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
?>

<?php if ($this->controller == 'index' && $this->action == 'home'): ?>
  <h3>
  <?php echo $this->translate('Hi %1$s!', $this->object->getTitle()); ?>

    <?php
    //GET VERIFY COUNT AND VERIFY LIMIT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($this->object->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
      $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($this->object->getIdentity());
      $verify_limit = Engine_Api::_()->authorization()->getPermission($this->object->level_id, 'siteverify', 'verify_limit');
      ?>
      <?php if ($verify_count >= $verify_limit):
        ?>
        <span class="siteverify_tip_wrapper">
            <i class="sitemember_list_verify_label mleft5"></i>
            <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
        </span>
      <?php
      endif;
    endif;
    ?>

  </h3>

<?php endif; ?>

<ul class="seaocore_sidebar_list <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
  <li class="prelative">
    <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $this->featured): ?>
      <i class="seaocore_list_featured_label"><?php echo $this->translate('Featured'); ?></i>
    <?php endif; ?>

    <?php $rel = 'user' . ' ' . $this->object->user_id; ?>
                        <?php if($this->circularImage):?>
                        <?php
                        $url = $this->object->getPhotoUrl('thumb.profile');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                        endif;
                        ?>
                        

                        
                        
                         <?php if ($this->controller == 'index' && $this->action == 'home'): ?>
      <a href="<?php echo $this->object->getHref() ?>" class ="sitemember_thumb"  >
                        <span style="background-image: url(<?php echo $url; ?>);"></span>
                        </a>
    <?php else: ?>
     <a href="<?php echo $this->object->getHref() ?>" class ="sitemember_thumb"  >
                        <span style="background-image: url(<?php echo $url; ?>);"></span>
                        </a>
    <?php endif; ?>
    
                    <?php else:?>

                     <?php if ($this->controller == 'index' && $this->action == 'home'): ?>
      <?php echo $this->htmlLink($this->object->getHref(), $this->itemPhoto($this->object, 'thumb.profile', '', array('align' => 'center'))) ?>
    <?php else: ?>
      <?php echo $this->htmlLink($this->object->getHref(), $this->itemPhoto($this->object, 'thumb.profile', '', array('align' => 'center'))) ?>
    <?php endif; ?>
                    <?php endif;?>
                    
   

    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($this->sponsored)): ?>
      <div class="seaocore_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>">
        <?php echo $this->translate('Sponsored'); ?>
      </div>
    <?php endif; ?>
  </li>

</ul>

<style type="text/css" >
.sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::before, .sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::after {
    background: <?php echo $this->settings->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>;
	}

</style>
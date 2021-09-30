<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if( !empty($this->bgImage) ) :?>
  <style>
    .demo_app_container {
      background-image: url('<?php echo  $this->bgImage ?>');
    }
  </style>
<?php endif; ?>

<div class="demo_app_container">
	<div class="demo_app_wrapper">
		<div class="demo_app_container_inner">
			<h3><?php echo $this->translate($this->title) ; ?></h3>
    <div class="widgets_title_border">
	   <span></span>
	   <i></i>
	   <span></span>
   </div>
  <div class="widgets_title_description">
    <?php echo $this->translate($this->description); ?>

  </div>
    <?php if(!empty($this->showButtons)): ?>
      <?php if( !empty($this->actionButtonUrl) ) :?>
        <div class="app_download_links">
          <a href="<?php echo $this->actionButtonUrl; ?>" class="app_links_item" target="<?php echo $this->target; ?>">
            <span class="name" style="margin:0;"><?php echo $this->translate($this->actionButtonText); ?></span>
          </a>
        </div>
        <?php endif; ?>
    <?php else: ?>  
			<div class="app_download_links">
        <?php if( !empty($this->appstoreUrl) ) :?>
          <a href="<?php echo $this->appstoreUrl; ?>" class="app_links_item" target="<?php echo $this->target; ?>">
            <i class="fa fa-apple"></i>
            <span class="heading"><?php echo $this->translate("Available on the") ?></span>
            <span class="name"><?php echo $this->translate("App Store") ?></span>
          </a>
        <?php endif; ?>
        <?php if( !empty($this->playstoreUrl) ) :?>
          <a href="<?php echo $this->playstoreUrl; ?>" class="app_links_item" target="<?php echo $this->target; ?>">
            <i class="fa fa-play"></i>
            <span class="heading"><?php echo $this->translate("Available on the") ?></span>
            <span class="name"><?php echo $this->translate("Play Store") ?></span>
          </a>
        <?php endif; ?>
			</div>
    <?php endif; ?>

		</div>
	</div>
</div>
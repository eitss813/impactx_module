<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: blog.tpl 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblogpackage/externals/styles/styles.css'); ?>
<?php 
$information = array('description' => 'Package Description', 'featured' => 'Featured', 'sponsored' => 'Sponsored', 'verified' => 'Verified', 'custom_fields' => 'Custom Fields');
$showinfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.package.info', array_keys($information)); ?>
<?php $currentCurrency =  Engine_Api::_()->sesblogpackage()->getCurrentCurrency(); ?>
<?php if(count($this->existingleftpackages)){ ?>
	<div class="sesblog_packages_main sesbasic_clearfix sesbasic_bxs">
  	<div class="sesblog_packages_main_header">
      <h2><?php echo $this->translate("Existing Package(s)")?></h2>
    </div>
    <div class="sesblog_packages_table_container">
      <ul class="sesblog_packages_list">
        <?php $existing = 1;?>
      <?php foreach($this->existingleftpackages as $packageleft)	{
            $package = Engine_Api::_()->getItem('sesblogpackage_package',$packageleft->package_id);
            $enableModules = json_decode($package->params,true);
       ?>
        <?php include APPLICATION_PATH .  '/application/modules/Sesblogpackage/views/scripts/_packagesHorizontal.tpl';?>      
      <?php } ?>
      </ul>
    </div>
  </div>
<?php } ?>
<?php if(count($this->package)){ ?>
	<div class="sesblog_packages_main sesbasic_clearfix sesbasic_bxs">
  	<div class="sesblog_packages_main_header">
      <h2><?php echo $this->translate("Choose A Package")?></h2>
      <p><?php echo $this->translate('Select a package that suits you most to start creating blogs on this website.');?></p>
    </div>
    <div class="sesblog_packages_table_container">
      <ul class="sesblog_packages_table">
        <?php $existing = 0;?>
      	<?php foreach($this->package as $package)	{
              $enableModules = json_decode($package->params,true);
       	?>
         <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.package.style', 1)):?>
           <?php include APPLICATION_PATH .  '/application/modules/Sesblogpackage/views/scripts/_packages.tpl';?> 
         <?php else:?>
           <?php include APPLICATION_PATH .  '/application/modules/Sesblogpackage/views/scripts/_packagesHorizontal.tpl';?>
         <?php endif;?>
      	<?php } ?>
      </ul>
		</div>
  </div>  
<?php } ?>
  
<script type="application/javascript">
var elem = sesJqueryObject('.package_catogery_blog');
for(i=0;i<elem.length;i++){
	var widthTotal = sesJqueryObject(elem[i]).children().length * 265;
	sesJqueryObject(elem[i]).css('width',widthTotal+'px');
}
</script>

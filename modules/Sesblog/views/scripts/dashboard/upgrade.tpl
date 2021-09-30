<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesblog
 * @package    Sesblog
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: upgrade.tpl 2016-07-23 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblogpackage/externals/styles/styles.css'); ?>
<?php if(!$this->is_ajax) {
  echo $this->partial('dashboard/left-bar.tpl', 'sesblog', array('blog' => $this->blog));	
?>
	<div class="sesbasic_dashboard_content sesbm sesbasic_bg sesbasic_clearfix">
<?php }  ?>
	<div class="sesbasic_dashboard_form">
		
<?php 
$information = array('description' => 'Package Description', 'featured' => 'Featured', 'sponsored' => 'Sponsored', 'verified' => 'Verified', 'custom_fields' => 'Custom Fields');
$showinfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.package.info', array_keys($information)); ?>
<?php $currentCurrency =  Engine_Api::_()->sesblogpackage()->getCurrentCurrency(); ?>
<div class="sesblog_packages_main sesbasic_clearfix sesbasic_bxs sesblog_packages_upgrade">
  <?php if($this->currentPackage){ 
    $package = Engine_Api::_()->getItem('sesblogpackage_package',$this->currentPackage->package_id);
    if($package){
  ?>
  	<div class="sesblog_packages_main_header">
    	<h2><?php echo $this->translate("Existing Package")?></h2>
    </div>
    <div class="sesblog_packages_table_container">
      <ul class="sesblog_packages_list">
         <?php $enableModules = json_decode($package->params,true);?>
         <li class="sesblog_packages_list_item <?php echo ($package->highlight) ? 'active' : '' ?>">
          <section>
           <div class="_top sesbasic_clearfix">
              <div class="_title"><h5><?php echo $this->translate($package->title); ?></h5></div>
              <div class="_price">
                <?php if(!$package->isFree() && $package->recurrence_type != 'forever'){ ?>
                  <span><?php echo Engine_Api::_()->sesblogpackage()->getCurrencyPrice($package->price,'','',true); ?></span>
                  <small>
                    <?php if($package->recurrence_type == 'day'):?>
                       <?php echo $this->translate('Daily');?>
                     <?php elseif($package->price && $package->recurrence_type != 'forever'):?>
                       <?php echo $this->translate(ucfirst($package->recurrence_type).'ly');?>
                     <?php elseif($package->recurrence_type == 'forever'): ?>
                       <?php echo sprintf($this->translate('One-time fee of %1$s'), Engine_Api::_()->sesblogpackage()->getCurrencyPrice($package->price,'','',true)); ?>
                     <?php else:?>
                       <?php echo $this->translate('Free');?>
                     <?php endif;?>
                  </small>
                <?php }elseif($package->recurrence_type == 'forever'){ ?>
                  <span><?php echo sprintf($this->translate('One-time fee of %1$s'), Engine_Api::_()->sesblogpackage()->getCurrencyPrice($package->price,'','',true)); ?></span>
                <?php }else{ ?>
                  <span><?php echo $this->translate("FREE"); ?></span>
                <?php } ?>
              </div>
            </div>
            <div class="_cont sesbasic_clearfix">
              <div class="package_capabilities _features">
                <div class="sesbasic_clearfix">
                	<div>
                    <span class="_label"><?php echo $this->translate('Billing Duration');?></span>
                    <span class="_value">
                      <?php if($package->duration_type == 'forever'):?>
                        <?php echo $this->translate('Forever');?>
                      <?php else:?>
                        <?php if($package->duration > 1):?>
                          <?php echo $package->duration . ' ' . ucfirst($package->duration_type).'s';?>
                        <?php else:?>
                          <?php echo $package->duration . ' ' . ucfirst($package->duration_type);?>
                        <?php endif;?>
                      <?php endif;?>
                    </span>
                	</div>    
                </div>	
                <div class="sesbasic_clearfix">
                	<div>
                    <span class="_label"><?php echo $this->translate('Blogs Count');?></span>
                    <span class="_value"><?php echo (!$package->item_count) ? $this->translate("Unlimited") : $package->item_count.' ( '.$this->currentPackage->item_count.' Left )' ?></span>
                  </div>
                </div>
                <div class="sesbasic_clearfix">
                	<div>
                    <span class="_label"><?php echo $this->translate('Auto Approved Blogs');?></span>
                    <span class="_value"><i class="_icon _<?php echo ($enableModules['blog_approve']) ? 'yes' : 'no';?>"></i></span>
                	</div>
                </div>
                <?php if(in_array('featured',$showinfo)){ ?>	
                  <div class="sesbasic_clearfix <?php echo ($enableModules['blog_featured']) ? 'yes' : 'no'; ?>">
                  	<div>
                      <span class="_label">Featured</span>
                      <span class="_value"><i class="_icon <?php echo ($enableModules['blog_featured']) ? '_yes' : '_no'; ?>"></i></span>
                  	</div>
                  </div>
                <?php } ?>
                <?php if(in_array('sponsored',$showinfo)){ ?>  
                  <div class="sesbasic_clearfix <?php echo ($enableModules['blog_sponsored']) ? 'yes' : 'no'; ?>">
                  	<div>
                      <span class="_label">Sponsored</span>
                      <span class="_value"><i class="_icon <?php echo ($enableModules['blog_sponsored']) ? '_yes' : '_no'; ?>"></i></span>
                  	</div>
                  </div>
                <?php } ?>
                <?php if(in_array('verified',$showinfo)){ ?>  
                  <div class="sesbasic_clearfix <?php echo ($enableModules['blog_verified']) ? 'yes' : 'no'; ?>">
                  	<div>
                      <span class="_label">Verified</span>
                      <span class="_value"><i class="_icon <?php echo ($enableModules['blog_verified']) ? '_yes' : '_no'; ?>"></i></span>
                  	</div>
                  </div>
                <?php } ?>
                <div class="sesbasic_clearfix">
                	<div>
                    <span class="_label"><?php echo $this->translate('Main Photo');?></span>
                    <span class="_value"><i class="_icon <?php echo ($enableModules['upload_mainphoto']) ? '_yes' : '_no'; ?>"></i></span>
                	</div>
                </div>
              </div>
              <?php if(in_array('description',$showinfo)){ ?> 
                <div class="_des _colm">
                  <p class="package_des"><?php echo $package->description; ?> </p>
                </div>  
              <?php } ?>
            </div>
          </section>
         </li>      
      </ul>
    </div>
  <?php 
    }
  } ?>
  
  <?php if(count($this->upgradepackage)){ ?>
  	<div class="sesblog_packages_main_header">
    	<h2><?php echo $this->translate("Upgrade Your Package")?></h2>
        <p><?php echo $this->translate('Choose a higher package to create blogs on this website and get benefited with advance features and functionalities.');?></p>
    </div>
    <div class="sesblog_packages_table_container">
      <ul class="sesblog_packages_list">
        <?php foreach($this->upgradepackage as $package){ ?>  
           <?php $enableModules = json_decode($package->params,true);?>
           <li class="sesblog_packages_list_item <?php echo ($package->highlight) ? 'active' : '' ?>">
            <section>
             <div class="_top sesbasic_clearfix">
                <div class="_title"><h5><?php echo $this->translate($package->title); ?></h5></div>
                <div class="_price">
                  <?php if(!$package->isFree()){ ?>
                    <span><?php echo Engine_Api::_()->sesblogpackage()->getCurrencyPrice($package->price,'','',true); ?></span>
                    <small>
                      <?php if($package->recurrence_type == 'day'):?>
                        <?php echo $this->translate('Daily');?>
                      <?php elseif($package->price && $package->recurrence_type != 'forever'):?>
                        <?php echo $this->translate(ucfirst($package->recurrence_type).'ly');?>
                      <?php elseif($package->recurrence_type == 'forever'): ?>
                        <?php echo sprintf($this->translate('One-time fee of %1$s'), Engine_Api::_()->sesblogpackage()->getCurrencyPrice($package->price,'','',true)); ?>
                      <?php else:?>
                        <?php echo $this->translate('Free');?>
                      <?php endif;?>
                    </small>
                  <?php }else{ ?>
                    <span><?php echo $this->translate("FREE"); ?></span>
                  <?php } ?>
                </div>
              </div>
              <div class="_cont sesbasic_clearfix">
                <div class="package_capabilities _colm">
                  <div class="sesbasic_clearfix">
                    <span class="_label"><?php echo $this->translate('Billing Duration');?></span>
                    <span class="_value">
                      <?php if($package->duration_type == 'forever'):?>
                        <?php echo $this->translate('Forever');?>
                      <?php else:?>
                        <?php if($package->duration > 1):?>
                          <?php echo $package->duration . ' ' . ucfirst($package->duration_type).'s';?>
                        <?php else:?>
                          <?php echo $package->duration . ' ' . ucfirst($package->duration_type);?>
                        <?php endif;?>
                      <?php endif;?>
                    </span>
                  </div>	
                  <div class="sesbasic_clearfix">
                    <span class="_label"><?php echo $this->translate('Blogs Count');?></span>
                    <span class="_value"><?php echo (!$package->item_count) ? $this->translate("Unlimited") : $package->item_count; ?></span>
                  </div>
                  <div class="sesbasic_clearfix">
                    <span class="_label"><?php echo $this->translate('Auto Approved Blogs');?></span>
                    <span class="_value"><i class="_icon _<?php echo ($enableModules['blog_approve']) ? 'yes' : 'no';?>"></i></span>
                  </div>
                </div>
                <div class="package_capabilities _colm">
                  <?php if(in_array('featured',$showinfo)){ ?>	
                    <div class="sesbasic_clearfix <?php echo ($enableModules['blog_featured']) ? 'yes' : 'no'; ?>">
                      <span class="_label">Featured</span>
                      <span class="_value"><i class="_icon <?php echo ($enableModules['blog_featured']) ? '_yes' : '_no'; ?>"></i></span>
                    </div>
                  <?php } ?>
                  <?php if(in_array('sponsored',$showinfo)){ ?>  
                    <div class="sesbasic_clearfix <?php echo ($enableModules['blog_sponsored']) ? 'yes' : 'no'; ?>">
                      <span class="_label">Sponsored</span>
                      <span class="_value"><i class="_icon <?php echo ($enableModules['blog_sponsored']) ? '_yes' : '_no'; ?>"></i></span>
                    </div>
                  <?php } ?>
                  <?php if(in_array('verified',$showinfo)){ ?>  
                    <div class="sesbasic_clearfix <?php echo ($enableModules['blog_verified']) ? 'yes' : 'no'; ?>">
                      <span class="_label">Verified</span>
                      <span class="_value"><i class="_icon <?php echo ($enableModules['blog_verified']) ? '_yes' : '_no'; ?>"></i></span>
                    </div>
                  <?php } ?>
                </div>
                <div class="package_capabilities _colm">
                  <div class="sesbasic_clearfix">
                    <span class="_label"><?php echo $this->translate('Main Photo');?></span>
                    <span class="_value"><i class="_icon <?php echo ($enableModules['upload_mainphoto']) ? '_yes' : '_no'; ?>"></i></span>
                  </div>
                </div>
                <?php if(in_array('description',$showinfo)){ ?> 
                  <div class="_des _colm">
                    <p class="package_des"><?php echo $this->translate($package->description); ?> </p>
                  </div>  
                <?php } ?>
              </div>
              <div class="_btns">
                <a href="<?php echo $this->url(array('blog_id' => $this->blog->blog_id,'action'=>'confirm-upgrade','package_id'=>$package->package_id), 'sesblogpackage_general', true); ?>" class="smoothbox sesblog_packages_create_btn sesbasic_animation"><?php echo $this->translate('Upgrade Package');?></a>
              </div>
            </section>
           </li> 
      	<?php } ?>     
      </ul>
    </div>
  <?php } else { ?>
    <div class="tip">
      <span><?php echo $this->translate('Currently there are no package to upgrade when no package created to upgrade.'); ?></span>
    </div>
  <?php } ?>
</div>
<script type="application/javascript">
var elem = sesJqueryObject('.package_catogery_blog');
for(i=0;i<elem.length;i++){
	var widthTotal = sesJqueryObject(elem[i]).children().length * 265;
	sesJqueryObject(elem[i]).css('width',widthTotal+'px');
}
</script>       
	</div>
<?php if(!$this->is_ajax) { ?>
	</div>
  </div>
<?php } ?>

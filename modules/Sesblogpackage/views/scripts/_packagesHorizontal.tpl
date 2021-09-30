<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _packagesHorizontal.tpl 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
?>
<?php if($package) { ?>
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
            <?php if($existing):?>
              <span class="_value"><?php echo (!$package->item_count) ? $this->translate("Unlimited") : $package->item_count.' ( '.$packageleft->item_count.' Left )' ?></span>
            <?php else:?>
              <span class="_value"><?php echo $package->item_count ; ?></span>
            <?php endif;?>
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
        	<p class="package_des"><?php echo $this->translate($package->description); ?> </p>
        </div>  
      <?php } ?>
    </div>
    <div class="_btns sesbasic_clearfix">
      <?php if($existing && !$package->isOneTime()):?>
        <div class="_left">
          <a href="<?php echo $this->url(array('action' => 'cancel','package_id' => $package->package_id),'sesblogpackage_general',true);?>" class="sesblog_packages_cancel_btn smoothbox"><?php echo $this->translate("Cancel");?></a>
          <span><b><?php echo $this->translate("Subscribed on: ");?></b><?php echo date('d F Y', strtotime($packageleft->creation_date));?></span>
          <?php //if(!$package->isFree()){ ?>
          <span>&nbsp;|&nbsp;&nbsp;</span><span><b><?php echo $this->translate("Expire On: ");?></b><?php echo ($packageleft->expiration_date == '3000-00-00 00:00:00') ? $this->translate("Never Expire")  : date('d F Y', strtotime($packageleft->expiration_date));?></span>
          <?php //} ?>
        </div>
      <?php endif;?>
      <div class="_right">
        <?php if($existing):?>
          <a class="sesblog_packages_create_btn sesbasic_animation" href="<?php echo $this->url(array('action' => 'create', 'existing_package_id' => $packageleft->getIdentity()),'sesblog_general',true); ?>"><?php echo $this->translate('Create Blog');?></a>
        <?php else:?>
          <a class="sesblog_packages_create_btn sesbasic_animation" href="<?php echo $this->url(array('action' => 'create', 'package_id' => $package->package_id),'sesblog_general',true); ?>"><?php echo $this->translate('Create Blog');?></a>
        <?php endif;?>
      </div>
    </div>
  </section>
 </li>   
<?php } ?>

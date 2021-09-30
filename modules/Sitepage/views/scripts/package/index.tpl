<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: packages.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$package_view = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.view',1);
$packageInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.information',array('price', 'billing_cycle', 'duration', 'featured', 'sponsored', 'tellafriend', 'print', 'overview', 'map', 'insights', 'contactdetails', 'sendanupdate', 'apps', 'description', 'twitterupdates', 'ads'));
$base_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'update-confirmation'));
?>
<?php if (!empty($this->couponmodules_enabled)) :?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecoupon/views/scripts/getcode.tpl'; ?>
<?php endif; ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>
<?php 
	$baseUrl = $this->layout()->staticBaseUrl;

	?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
</div>
</div>
<div class="layout_middle sitepage_create_wrapper clr">
<div class="generic_layout_container">
	<h3><?php echo $this->translate("Create New Page") ?></h3>
	<p><?php echo $this->translate("Create a page using these quick, easy steps and get going.");?></p>	
	<h4 class="sitepage_create_step fleft"><?php echo $this->translate('1. Choose a Page Package');?></h4>
	
	<?php //start coupon plugin work. ?>
	<?php if (!empty($this->modules_enabled) && in_array("sitepage_package", $this->modules_enabled)) : ?>
	<h4 class="sitepage_create_step fright"><a href="javascript:void(0);" class=" buttonlink item_icon_coupon" onclick="javascript:preview('<?php echo '500' ?>', '<?php echo '500' ?>', '<?php echo 'sitepage_package' ?>');"><?php echo $this->translate('Discount Coupons') ?></a></h4>
	<?php endif; ?>
	
	<div class='sitepage_package_page'>
		<?php if( count($this->paginator) ): ?>
			
        
                                 <?php if ($package_view == 0): ?>
            <ul class="sitepage_package_list">
				<li>
					  <span>  <?php echo $this->translate('Select a package that best matches your requirements. Packages differ in terms of features available to pages created under them. You can change your package anytime later.');?></span>
				</li>
		 		<?php   foreach ($this->paginator as $item): ?>
					<li>
			 			<div class="sitepage_package_list_title">
	            <div class="sitepage_create_link">
								<?php if (!empty($this->parent_id)): ?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'parent_id' => $this->parent_id), 'sitepage_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
								<?php elseif(!empty($this->business_id)) :?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'business_id' => $this->business_id), 'sitepage_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
							  <?php elseif(!empty($this->group_id)) :?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'group_id' => $this->group_id), 'sitepage_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
								<?php elseif(!empty($this->store_id)) :?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'store_id' => $this->store_id), 'sitepage_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
								<?php else: ?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id), 'sitepage_general', true);
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
								<?php endif; ?>
	           </div>
           	 <h3>             
                <a href='<?php echo $this->url(array("action"=>"detail" ,'id' => $item->package_id), 'sitepage_packages', true) ?>' onclick="owner(this);return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
              </h3>            	 
			 			</div>
                                            
             <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/package/_packageInfo.tpl'; ?>
	          
                                        
		    <?php endforeach; ?>
                                        <br />
				<div>
				  <?php echo $this->paginationControl($this->paginator); ?>
				</div>
                                        </ul>
                                        <?php elseif ($package_view == 1):?>
                                        <ul class="seaocore_package_list">
				<li>
					  <span>  <?php echo $this->translate('Select a package that best matches your requirements. Packages differ in terms of features available to pages created under them. You can change your package anytime later.');?></span>
				</li>
                                        <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/package/_verticalPackageInfo.tpl'; ?>
                                </ul>
           
				       
                                        <br />
				<div>
				  <?php echo $this->paginationControl($this->paginator); ?>
				</div>
                                        </ul>
                                        <?php else:?>
                                        <ul class="seaocore_package_list">
				<li>
					  <span>  <?php echo $this->translate('Select a package that best matches your requirements. Packages differ in terms of features available to pages created under them. You can change your package anytime later.');?></span>
				</li>
                                        <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/package/_customPackageInfo.tpl'; ?>
                                </ul>
                                        <?php endif;?>
				
		<?php else: ?>
		 <div class="tip">
		    <span>
		      <?php echo $this->translate("There are no packages yet.") ?>
		    </span>
		  </div>
		<?php endif; ?>
  </div>
</div>
</div>

	
<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>
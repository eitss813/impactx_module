<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 

	$this->headLink()
			->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/styles/style_print.css');
?>
<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/styles/style_print.css'?>" type="text/css" rel="stylesheet" media="print">
<?php
  $contactPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($this->sitepage, 'contact');
?>
<div class="seaocore_print_page">
	<div class="seaocore_print_title">	
    <span class="left">
      <?php echo $this->translate($this->sitepage->getTitle()) ?>
    </span>
		<span class="right">
			<?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'));?>
		</span>
	</div>
	<div class='seaocore_print_profile_fields'>
		<?php if ($this->sitepage->closed == 1): ?>
			<div class="tip"> 
				<span> <?php echo $this->translate('This page has been closed by the poster.'); ?> </span>
			</div>
			<br/>
		<?php else: ?>
			<div class="seaocore_print_photo">
				<?php echo $this->itemPhoto($this->sitepage, 'thumb.normal', '' , array('align'=>'left')); ?>
				<div id="printdiv" class="seaocore_print_button">
					<a href="javascript:void(0);" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/printer.png');" class="buttonlink" onclick="printData()" align="right"><?php echo $this->translate('Take Print') ?></a>
				</div>
			</div>
			<div class="seaocore_print_details">	      
				<h4>
					<?php echo $this->translate('Page Information') ?>
				</h4>
				<ul>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>
            <li>
              <span><?php echo $this->translate('Posted By:'); ?> </span>
              <span><?php echo $this->translate($this->sitepage->getParent()->getTitle()) ?></span>
            </li>
          <?php endif;?>
					<li>
						<span><?php echo $this->translate('Posted On:'); ?></span>
						<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitepage->creation_date))) ?></span>
					</li>
					<?php if(!empty($this->sitepage->comment_count)): ?>
					<li>
						<span><?php echo $this->translate('Comments:'); ?></span>
						<span><?php echo $this->translate( $this->sitepage->comment_count) ?></span>
					</li>
					<?php endif; ?>
					<?php if(!empty($this->sitepage->view_count)): ?>
					<li>
						<span><?php echo $this->translate('Views:'); ?></span>
						<span><?php echo $this->translate( $this->sitepage->view_count) ?></span>
					</li>
					<?php endif; ?>
					<?php if(!empty($this->sitepage->like_count)): ?>
					<li>
						<span><?php echo $this->translate('Likes:'); ?></span>
						<span><?php echo $this->translate( $this->sitepage->like_count) ?></span>
					</li>
					<?php endif; ?>
					<?php if ($this->category): ?>
					<li>
						<span><?php echo $this->translate('Category:'); ?></span> 
						<span><?php echo $this->translate($this->category->category_name) ?>
						<?php if ($this->subcategory): ?> &raquo;
						<?php echo $this->translate($this->subcategory->category_name) ?>
            <?php endif; ?>
            <?php if ($this->subsubcategory): ?> &raquo;
            <?php echo $this->translate($this->subsubcategory->category_name); ?>
						<?php endif; ?>
						</span>
					</li>
					<?php endif; ?>
					<?php if ($this->sitepageTags): $tagCount=0;?>
						<li>
							<span><?php echo $this->translate('Tag:'); ?></span>
								<span>
								<?php foreach ($this->sitepageTags as $tag): ?>
								<?php if (!empty($tag->getTag()->text)):?>
								<?php if(empty($tagCount)):?>
								<?php echo "#". $tag->getTag()->text?>
								<?php "#".$tagCount++; else: ?>
								<?php echo $tag->getTag()->text?>
								<?php endif; ?>
								<?php endif; ?>
								<?php endforeach; ?>
							</span>
						</li>
					<?php endif; ?>
					<li>
						<span><?php echo $this->translate('Description:'); ?></span>
						<span><?php echo $this->translate(''); ?> <?php echo $this->sitepage->body ?></span>
					</li>
           <?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1); ?>
           <?php if($this->sitepage->price && $enablePrice):?>
          <li>
            <span><?php echo $this->translate('Price:'); ?></span>
            <span><?php echo Engine_Api::_()->sitepage()->getPriceWithCurrency($this->sitepage->price);?></span>
          </li>
          <?php endif; ?>
           <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1); ?>
           <?php if($this->sitepage->location && $enableLocation):?>
          <li>
            <span><?php echo $this->translate('Location:'); ?></span>
            <span><?php echo $this->sitepage->location ?></span>
          </li>
          <?php endif; ?>
				</ul>
					<?php
						$user = Engine_Api::_()->user()->getUser($this->sitepage->owner_id);
						$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'contact_detail');
						$availableLabels = array('phone' => 'Phone','website' => 'Website','email' => 'Email');
						$options_create = array_intersect_key($availableLabels, array_flip($view_options));
					?>
          <?php if(!empty($contactPrivacy)): ?>
            <?php if(!empty($options_create) && (!empty($this->sitepage->email) || !empty($this->sitepage->website) || !empty($this->sitepage->phone))):?>
							<h4>
								<?php echo $this->translate('Contact Details:');  ?>
							</h4>
            <?php endif; ?>
						<ul>               
							<?php if($options_create['phone'] == 'Phone'):?>
								<?php if(!empty($this->sitepage->phone)):?>
								<li>
									<span><?php echo $this->translate('Phone:'); ?></span>
									<span><?php echo $this->translate(''); ?> <?php echo $this->sitepage->phone ?></span>
								</li>
								<?php endif; ?>
							<?php endif; ?>
							<?php if($options_create['email'] == 'Email'):?>
								<?php if(!empty($this->sitepage->email)):?>
								<li>
									<span><?php echo $this->translate('Email:'); ?></span>
									<span><?php echo $this->translate(''); ?>
									<a href='mailto:<?php echo $this->sitepage->email ?>'><?php echo $this->sitepage->email ?></a></span>
								</li>
								<?php endif; ?>
							<?php endif; ?>
							<?php if($options_create['website'] == 'Website'):?>
								<?php if(!empty($this->sitepage->website)):?>
								<li>
									<span><?php echo $this->translate('Website:'); ?></span>
									<?php if(strstr($this->sitepage->website, 'http://')):?>
									<span><a href='<?php echo $this->sitepage->website ?>'><?php echo $this->translate(''); ?> <?php echo $this->sitepage->website ?></a></span>
									<?php else:?>
									<span><a href='http://<?php echo $this->sitepage->website ?>'><?php echo $this->translate(''); ?> <?php echo $this->sitepage->website ?></a></span>
									<?php endif;?>
								</li>
								<?php endif; ?>
							<?php endif; ?>
						</ul>
          <?php endif; ?> 
                            
            <?php if($this->sitepage->profile_type): ?>
                            
                <?php $str = $this->PageProfileFieldValueLoop($this->sitepage, $this->fieldStructure); ?>
                            <?php
                             $params = array('custom_field_heading' => 1, 'custom_field_title' => 1, 'customFieldCount' => 1000);
                             ?>
                <?php if(!empty($str) ): ?>
                  <h4>
                    <?php echo $this->translate('Profile Information') ?>
                  </h4>
                  <?php echo $this->PageProfileFieldValueLoop($this->sitepage, $this->fieldStructure, $params) ?>	
                       
                <?php endif; ?>
            <?php endif; ?>
                            
			</div>	
		<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
function printData() {
				document.getElementById('printdiv').style.display = "none";

				window.print();
				setTimeout(function() {
					     document.getElementById('printdiv').style.display = "block";
				}, 500);
			}
</script>

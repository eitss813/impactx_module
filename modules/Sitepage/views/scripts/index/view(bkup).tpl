<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>
<?php if (empty($this->viewPrivacy) && !empty($this->sitepageMemberEnabled) && empty($this->hasMembers)) : ?>
<div class="layout_middle">
	<div class="layout_left">
		<?php //echo $this->sitemain; ?>
		<?php if (!empty($this->sitepage->sponsored)): ?>
		  <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
		  if (!empty($sponsored)) { ?>
		    <div class="sitepage_profile_sponsorfeatured" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/star-img.png', '') ?>
		      <?php echo $this->translate('SPONSORED'); ?>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/star-img.png', '') ?>
		    </div>
		  <?php } ?>
		<?php endif; ?>
		<div class='sitepage_photo <?php if ($this->can_edit) : ?>sitepage_photo_edit_wrapper<?php endif; ?>'>
		  <?php if (!empty($this->can_edit)) : ?>
		    <a href="<?php echo $this->url(array('action' => 'profile-picture', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true) ?>" class="sitepage_photo_edit">  	  
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/edit_pencil.png', '') ?>
		      <?php echo $this->translate('Change Picture'); ?>
		    </a>
		  <?php endif; ?>
		
		  <?php echo $this->itemPhoto($this->sitepage, 'thumb.profile', '', array('align' => 'left')); ?>
		</div>
		<?php if (!empty($this->sitepage->featured)): ?>
		  <?php $feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.image', 1);
		  if (!empty($feature)) { ?>
		    <div class="sitepage_profile_sponsorfeatured"  style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0cf523'); ?>;'>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/star-img.png', '') ?>
		      <?php echo $this->translate('FEATURED'); ?>
		    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/star-img.png', '') ?>
		    </div>
		  <?php } ?>
		<?php endif; ?>
		<?php if (!empty($this->viewer_id)): ?>
			<div class="generic_layout_container layout_sitepage_options_sitepage">
				<?php
		            $table = Engine_Api::_()->getDbTable('pages', 'sitepage');
		            $select=$table->select()
		                          ->where('page_id=?',$this->sitepage->page_id);
		            $data=$table->fetchRow($select);
		            if($data['join_enable'] == 1) : ?>
				<?php if (!empty($this->member_approval)) : ?>
					<br /><?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'join', 'page_id' => $this->sitepage->page_id), $this->translate('Join Page'), array(  ' class' => 'buttonlink smoothbox icon_sitepage_join')); ?>
					<?php else : ?>
						<?php if (empty($this->hasMembers)) : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'request', 'page_id' => $this->sitepage->page_id), $this->translate('Join Page'), array(  ' class' => 'buttonlink smoothbox icon_sitepage_join')); ?>
						<?php else : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'cancel', 'page_id' => $this->sitepage->page_id), $this->translate('Cancel Member Request for Page'), array(  ' class' => 'buttonlink smoothbox icon_sitepage_join')); ?>
						<?php endif ;?>
					<?php endif;?>
				<?php endif;  ?>
			</div>
		<?php endif;  ?>
	</div>
	
	<div class="layout_middle">
		<div id='profile_status'>
		  <h2>
		    <?php echo $this->sitepage->getTitle() ?>
		  </h2>
		</div>
		
		<div class="generic_layout_container layout_core_container_tabs">
			<div class='tabs_alt tabs_parent'>
				<ul class="main_tabs">
					<li id="info" class="active">
						<a href="javascript:void(0);" onclick="showtab('info')"><?php echo $this->translate('Info') ?></a>
					</li>
					<?php if ($this->sitepage->overview && !empty($this->can_edit_overview)) :?>
					<li id="overview" class="active">
						<a href="javascript:void(0);" onclick="showtab('overview')"><?php echo $this->translate('Overview') ?></a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
			<div id="infocontentshow">
				 <?php 	echo $this->content()->renderWidget("sitepage.info-sitepage", array()); ?>
			</div>
			<?php if ($this->sitepage->overview && !empty($this->can_edit_overview)) :?>
				<div id="overviewcontentshow" >
					<?php echo $this->content()->renderWidget("sitepage.overview-sitepage", array()); ?>
				</div>
			<?php endif; ?>
	  </div>
	</div>

</div>
<?php elseif (empty($this->viewPrivacy) && !empty($this->sitepageMemberEnabled) && !empty($this->hasMembers)) : ?>
<div class="layout_middle">
	<div class="layout_left">
		<?php //echo $this->sitemain; ?>
		<?php if (!empty($this->sitepage->sponsored)): ?>
		  <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
		  if (!empty($sponsored)) { ?>
		    <div class="sitepage_profile_sponsorfeatured" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/star-img.png', '') ?>
		      <?php echo $this->translate('SPONSORED'); ?>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/star-img.png', '') ?>
		    </div>
		  <?php } ?>
		<?php endif; ?>
		<div class='sitepage_photo <?php if ($this->can_edit) : ?>sitepage_photo_edit_wrapper<?php endif; ?>'>
		  <?php if (!empty($this->can_edit)) : ?>
		    <a href="<?php echo $this->url(array('action' => 'profile-picture', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true) ?>" class="sitepage_photo_edit">  	  
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/edit_pencil.png', '') ?>
		      <?php echo $this->translate('Change Picture'); ?>
		    </a>
		  <?php endif; ?>
		
		  <?php echo $this->itemPhoto($this->sitepage, 'thumb.profile', '', array('align' => 'left')); ?>
		</div>
		<?php if (!empty($this->sitepage->featured)): ?>
		  <?php $feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.image', 1);
		  if (!empty($feature)) { ?>
		    <div class="sitepage_profile_sponsorfeatured"  style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0cf523'); ?>;'>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/star-img.png', '') ?>
		      <?php echo $this->translate('FEATURED'); ?>
		    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/star-img.png', '') ?>
		    </div>
		  <?php } ?>
		<?php endif; ?>
		<?php if (!empty($this->viewer_id)): ?>
			<div class="generic_layout_container layout_sitepage_options_sitepage">
				<?php
		            $table = Engine_Api::_()->getDbTable('pages', 'sitepage');
		            $select=$table->select()
		                          ->where('page_id=?',$this->sitepage->page_id);
		            $data=$table->fetchRow($select);
		            if($data['join_enable'] == 1) : ?>
				<?php if (!empty($this->member_approval)) : ?>
					<br /><?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'join', 'page_id' => $this->sitepage->page_id), $this->translate('Join Page'), array(  ' class' => 'buttonlink smoothbox icon_sitepage_join')); ?>
					<?php else : ?>
						<?php if (empty($this->hasMembers)) : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'request', 'page_id' => $this->sitepage->page_id), $this->translate('Request Member for Page'), array(  ' class' => 'buttonlink smoothbox icon_sitepage_join')); ?>
						<?php else : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'cancel', 'page_id' => $this->sitepage->page_id), $this->translate('Cancel Member Request for Page'), array(  ' class' => 'buttonlink smoothbox icon_sitepage_join')); ?>
						<?php endif ;?>
					<?php endif;?>
				<?php endif;  ?>
			</div>
		<?php endif;  ?>
	</div>
	
	<div class="layout_middle">
		<div id='profile_status'>
		  <h2>
		    <?php echo $this->sitepage->getTitle() ?>
		  </h2>
		</div>
		
		<div class="generic_layout_container layout_core_container_tabs">
			<div class='tabs_alt tabs_parent'>
				<ul class="main_tabs">
					<li id="info" class="active">
						<a href="javascript:void(0);" onclick="showtab('info')"><?php echo $this->translate('Info') ?></a>
					</li>
					<?php if ($this->sitepage->overview && !empty($this->can_edit_overview)) :?>
					<li id="overview" class="active">
						<a href="javascript:void(0);" onclick="showtab('overview')"><?php echo $this->translate('Overview') ?></a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
			<div id="infocontentshow">
				 <?php echo $this->content()->renderWidget("sitepage.info-sitepage", array()); ?>
			</div>
			<?php if ($this->sitepage->overview && !empty($this->can_edit_overview)) :?>
				<div id="overviewcontentshow">
					<?php echo $this->content()->renderWidget("sitepage.overview-sitepage", array()); ?>
				</div>
			<?php endif; ?>
	  </div>
	</div>

</div>
<?php else : ?>
<?php echo $this->sitemain; ?>
<?php endif; ?>
<?php if ($this->sitepage->overview) : ?>
<script type="text/javascript">

	en4.core.runonce.add(function() {
		$('overviewcontentshow').style.display = 'none';
		$('overview').removeClass('active');
	});
	</script>
	<?php endif; ?>
	<script type="text/javascript">
	function showtab(showtabcontent) {
		if (showtabcontent == 'info') {
			$('info').addClass('active');
			$('overview').removeClass('active');
			$('infocontentshow').style.display = 'block';
			$('overviewcontentshow').style.display = 'none';
		} else {	
			$('overview').addClass('active');
			$('info').removeClass('active');
      var globalContentElement = en4.seaocore.getDomElements('content');
			$(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'block';
			$('overviewcontentshow').style.display = 'block';
			$('infocontentshow').style.display = 'none';
		}
	}
</script>
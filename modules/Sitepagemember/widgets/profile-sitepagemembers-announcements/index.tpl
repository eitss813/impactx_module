<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/scripts/core.js'); ?>
<?php if($this->loaded_by_ajax):?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitepagemember_profile_sitepagemembers_announcements'),
      requestUrl: en4.core.baseUrl+'<?php echo ($this->user_layout) ? 'sitepage/widget' :'widget';?>'
    }
    en4.sitepage.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>

<?php if($this->showContent): ?> 
<div id='id_<?php echo $this->content_id; ?>'>
	<?php if (count($this->announcements) > 0): ?>
		<ul class="sitepage_profile_list sitepage_profile_announcements sitepage_list_highlight" >
			<?php foreach ($this->announcements as $item): ?>
				<li>
					<b><?php echo $item->title; ?></b>
					<?php if (!empty($item->body)): ?>
						<div class="sitepage_profile_list_info_des show_content_body">
							<?php echo $item->body; ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('No announcements have been created yet.'); ?>
			</span>
		</div>
	<?php endif; ?>
</div>


<script type="text/javascript">

  $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) 
  {	
  	var globalContentElement = en4.seaocore.getDomElements('content');
  	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {    	
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }

    prev_tab_id = '<?php echo $this->content_id; ?>';	
    prev_tab_class = 'layout_sitepagemember_profile_sitepagemembers_announcements';
		if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitepagemember_profile_sitepagemembers_announcements')==null)){
      scrollToTopForPage($(globalContentElement).getElement(".layout_sitepagemember_profile_sitepagemembers_announcements"));
    }	        
  });
</script>
<?php endif; ?>
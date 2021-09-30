<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl .
'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?> 
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
<script type="text/javascript">

	var page_communityads;
	var contentinformtion;
	var page_showtitle;
  var globalContentElement = en4.seaocore.getDomElements('content');
	//prev_tab_id = '<?php //echo $this->content_id; ?>';	
  if(contentinformtion == 0) {
		if($(globalContentElement).getElement('.layout_activity_feed')) {
			$(globalContentElement).getElement('.layout_activity_feed').style.display = 'none';
		}		
		if($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
			$(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'none';
		}	
		if($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
			$(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'block';
		}	
		if($(globalContentElement).getElement('.layout_core_profile_links')) {
			$(globalContentElement).getElement('.layout_core_profile_links').style.display = 'none';
		}
		if($(globalContentElement).getElement('.layout_sitepage_overview_sitepage')) {
			$(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'none';
	  }
    if($(globalContentElement).getElement('.layout_sitepageintegration_profile_items')) {
			$(globalContentElement).getElement('.layout_sitepageintegration_profile_items').style.display = 'none';
	  }
  }
  
</script>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')):?>
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adinfowidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)) : ?>
    <?php $flag = 1; ?>
  <?php else:?>
    <?php $flag = 0; ?>
  <?php endif;?>
<?php endif;?>
<?php
$contactPrivacy=0;
$profileTypePrivacy=0;
$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($this->sitepage, 'contact');
	if(!empty($isManageAdmin)) {
		$contactPrivacy = 1;
	}

  // PROFILE TYPE PRIVACY
  $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($this->sitepage, 'profile');
		if(!empty($isManageAdmin)) {
			$profileTypePrivacy = 1;
		}
?>

<?php if($this->showtoptitle == 1):?>
	<div class="layout_simple_head" id="layout_info">
		<?php echo $this->translate("Basic Information") ?>
	</div>
<?php endif;?>
<div id='id_<?php echo $this->content_id; ?>'>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')):?>
<?php if(Engine_Api::_()->getApi('SubCore', 'sitepage')->getSampleAdWidgetEnabled($this->sitepage) || $flag ) : ?>
	<div class="layout_right" >
	<?php endif; ?>
		<?php if(!empty($this->isManageAdmin) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adcreatelink', 1)) : ?>
			<?php 
				echo $this->content()->renderWidget("communityad.getconnection-link");
		  ?>
    <span class="adpreview_seprator"></span>
		<?php endif; ?>
		<?php if(!empty($this->isManageAdmin) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpreview', 1)) : ?>
			<?php
				// Render Sample Page Ad widget
				echo $this->content()->renderWidget("communityad.pagead-preview"); 
			?>
    <span class="adpreview_seprator"></span>
		<?php endif; ?>		
		<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adinfowidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)) : ?>
			<div id="communityad_info" >
              <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adinfowidget', 3),"loaded_by_ajax"=>0,'widgetId'=>'page_info'))?>
			</div>
		<?php endif; ?>
		<?php if(Engine_Api::_()->getApi('SubCore', 'sitepage')->getSampleAdWidgetEnabled($this->sitepage) || $flag ) : ?>
	</div>
	<?php endif; ?>
	<div class="layout_middle">
<?php endif;?>
<div class='profile_fields'>
    <div class="generic_layout_container layout_sitepage_page_peoples" style="display: block !important;">
        <h3 style="display: block;"><?php echo $this->translate('Basic Information'); ?></h3>
    </div>
	<ul>
    <?php if($this->sitepage->show_name) :?>
      <?php if($postedBy):?>
        <li>
          <span><?php echo $this->translate('Posted By:'); ?> </span>
          <span><?php echo $this->htmlLink($this->sitepage->getParent(), $this->sitepage->getParent()->getTitle()) ?></span>
        </li>
      <?php endif;?>
    <?php endif;?>
    <li>
    	<span><?php echo $this->translate('Posted:'); ?></span>
      <span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitepage->creation_date))) ?></span>
    </li>    
    <li>
    	<span><?php echo $this->translate('Last Updated:'); ?></span>
			<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitepage->modified_date))) ?></span>
    </li>
    <?php if(!empty($this->sitepage->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
    	<li>
			<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1); ?>
			<?php if ($this->sitepage->member_title && $memberTitle) : ?>
					<span><?php echo $this->sitepage->member_title . ':' ?></span>
					<span><?php echo $this->sitepage->member_count ?></span>
			<?php else: ?>
			<span><?php echo $this->translate('Members:'); ?></span>
			<span><?php echo $this->sitepage->member_count ?></span>
			<?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if(!empty($this->sitepage->comment_count)): ?>
    	<li>
    		<span><?php echo $this->translate('Comments:'); ?></span>
				<span><?php echo $this->sitepage->comment_count ?></span>
      </li>
    <?php endif; ?>
    <?php if(!empty($this->sitepage->view_count)): ?>
      <li>
      	<span><?php echo $this->translate('Views:'); ?></span>
				<span><?php echo $this->sitepage->view_count ?></span>
      </li>
    <?php endif; ?>
    <?php if(!empty($this->sitepage->like_count)): ?>
    	<li>
    		<span><?php echo $this->translate('Likes:'); ?></span>
				<span><?php echo $this->sitepage->like_count ?></span>
      </li>
    <?php endif; ?>
    <?php if(!empty($this->sitepage->follow_count) && isset($this->sitepage->follow_count)): ?>
    	<li>
    		<span><?php echo $this->translate('Followers:'); ?></span>
				<span><?php echo $this->sitepage->follow_count ?></span>
      </li>
    <?php endif; ?>
    <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'sitepage','action' => 'index'), 'sitepage_general', true) ?>' style='display: none;'>
      <input type="hidden" id="tag" name="tag" value=""/>
      <input type="hidden" id="category" name="category" value=""/>
      <input type="hidden" id="subcategory" name="subcategory" value=""/>
      <input type="hidden" id="categoryname" name="categoryname" value=""/>
      <input type="hidden" id="subcategoryname" name="subcategoryname" value=""/>
      <input type="hidden" id="subsubcategory" name="subsubcategory" value=""/>
      <input type="hidden" id="subsubcategoryname" name="subsubcategoryname" value=""/>
      <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date)
        echo $this->start_date; ?>"/>
      <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date)
               echo $this->end_date; ?>"/>
    </form>
    <li class="mtop5">
	    <?php if($this->category_name != '' && $this->subcategory_name == '') :?>
		    <span><?php echo $this->translate('Category:'); ?></span> 
		    <span>
		    				
				<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name)), 'sitepage_general_category'), $this->translate($this->category_name)) ?>
				
		    </span>
	    <?php elseif($this->category_name != '' && $this->subcategory_name != ''): ?> 
		    <span><?php echo $this->translate('Category:'); ?></span>
		    <span><?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name)), 'sitepage_general_category'), $this->translate($this->category_name)) ?>
				<?php if(!empty($this->category_name)): echo '&raquo;'; endif; ?>
			  <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name)), 'sitepage_general_subcategory'), $this->translate($this->subcategory_name)) ?>			  
			  <?php if(!empty($this->subsubcategory_name)): echo '&raquo;';?>
        <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->sitepage->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subsubcategory_name)), 'sitepage_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
	   		<?php endif; ?>
        </span>
	    <?php endif; ?>
    </li>
    <li>
    	<?php if (count($this->sitepageTags) >0): $tagCount=0;?>
    		<span><?php echo $this->translate('Tags:'); ?></span>
        <span>
    		 <?php foreach ($this->sitepageTags as $tag): ?>
					<?php if (!empty($tag->getTag()->text)):?>
						<?php if(empty($tagCount)):?>
						<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>
							<?php $tagCount++; else: ?>
						<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>
						<?php endif; ?>
					<?php endif; ?>
        <?php endforeach; ?>
        </span>
			<?php endif; ?>
    </li>
  
    <?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1); ?>
     <?php if($this->sitepage->price && $enablePrice):?>
    <li>
    	<span><?php echo $this->translate('Price:'); ?></span>
      <span><?php echo Engine_Api::_()->sitepage()->getPriceWithCurrency($this->sitepage->price); ?></span>
    </li>
    <?php endif; ?>
     <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1); ?>
     <?php if($this->sitepage->location && $enableLocation):?>
    <li>
    	<span><?php echo $this->translate('Location:'); ?></span>
      <span><?php echo $this->sitepage->location ?>&nbsp; - 
      <b>
				<?php $location_id = Engine_Api::_()->getDbTable('locations', 'sitepage')->getLocationId($this->sitepage->page_id, $this->sitepage->location);
				echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->sitepage->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('class' => 'smoothbox')) ; ?>
      </b>
      </span>
    </li>
    <?php endif; ?>
     <li>
    	<span><?php echo $this->translate('Description:'); ?></span>
      <span>
          <?php echo $this->viewMore($this->translate($this->sitepage->body), 300, 5000) ?>
      </span>
    </li>
    <?php if(!empty($this->totalNoOfFollowers) && isset($this->totalNoOfFollowers)): ?>
    <li>
        <span><?php echo $this->translate('Total No Of Followers'); ?></span>
        <span><?php echo $this->totalNoOfFollowers; ?></span>
    </li>
    <?php endif; ?>
  </ul>
    <?php if($this->totalNoOfProjects): ?>
    <div class="generic_layout_container layout_sitepage_page_peoples" style="display: block !important;">
        <h3 style="display: block;"><?php echo $this->translate('Project and funding Details');  ?></h3>
    </div>
    <ul>
        <li>
            <span><?php echo $this->translate('Total no of projects:'); ?></span>
            <span><?php echo $this->translate(''); ?> <?php echo $this->totalNoOfProjects ?></span>
        </li>
        <li>
            <span><?php echo $this->translate('Total funding amount:'); ?></span>
            <span><?php echo $this->translate(''); ?> <?php echo $this->totalFundingAmount ?></span>
        </li>
    </ul>
    <?php endif; ?>
  <?php
		$user = Engine_Api::_()->user()->getUser($this->sitepage->owner_id);
		$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'contact_detail');
    $availableLabels = array('phone' => 'Phone','website' => 'Website','email' => 'Email');		
    $options_create = array_intersect_key($availableLabels, array_flip($view_options));
  ?>
 <?php if(!empty($contactPrivacy)): ?>
  <?php if(!empty($options_create) && (!empty($this->sitepage->email) || !empty($this->sitepage->website) || !empty($this->sitepage->phone))):?>
  <h4>
		<span><?php echo $this->translate('Contact Details');  ?></span>
	</h4>  	
    <ul>
    	<li style="display:none;"></li>
      <?php if(isset($options_create['phone']) && $options_create['phone'] == 'Phone'):?>
        <?php if(!empty($this->sitepage->phone)):?>
        <li>
          <span><?php echo $this->translate('Phone:'); ?></span>
          <span><?php echo $this->translate(''); ?> <?php echo $this->sitepage->phone ?></span>
        </li>
        <?php endif; ?>
      <?php endif; ?>

      <?php if(isset($options_create['email']) && $options_create['email'] == 'Email'):?>
        <?php if(!empty($this->sitepage->email)):?>
        <li>
          <span><?php echo $this->translate('Email:'); ?></span>
          <span><?php echo $this->translate(''); ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitepage_profilepage', 'module' => 'sitepage', 'controller' => 'profile', 'action' => 'email-me', "id" => $this->sitepage->page_id), 'default' , true)); ?>'); return false;"><?php echo $this->sitepage->getTitle(); ?></a>
          </span>
        </li>
        <?php endif; ?>
      <?php endif; ?>
      <?php if( isset($options_create['website']) && $options_create['website'] == 'Website'):?>
        <?php if(!empty($this->sitepage->website)):?>
        <li>
          <span><?php echo $this->translate('Website:'); ?></span>
          <?php if(strstr($this->sitepage->website, 'http://') || strstr($this->sitepage->website, 'https://')):?>
          <span><a href='<?php echo $this->sitepage->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitepage->website ?></a></span>
          <?php else:?>
          <span><a href='http://<?php echo $this->sitepage->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitepage->website ?></a></span>
          <?php endif;?>
        </li>
        <?php endif; ?>
      <?php endif; ?>
    </ul>
    <?php endif; ?>
  <?php endif; ?>
 	<?php if(!empty ($profileTypePrivacy)):
 	  $params = array('custom_field_heading' => 1, 'custom_field_title' => 1, 'customFieldCount' => 1000, 'widgetName' => 'infoProfile');
    $str =  $this->PageProfileFieldValueLoop($this->sitepage, $this->fieldStructure, $params)?>
		<?php if($str): ?>
			<h4 >
				<span><?php  echo $this->translate('Profile Information');  ?></span>
			</h4>
			<?php echo $this->PageProfileFieldValueLoop($this->sitepage, $this->fieldStructure, $params) ?>
		<?php endif; ?>
	<?php endif; ?>

	<br />
	<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.checkcomment.widgets', 1)):?>
		<div id="info_comment">
        <?php 
            include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
        ?>
		</div>
	<?php endif; ?>
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')):?>
	</div>
<?php endif; ?>
</div>

<script type="text/javascript">
  var lsetting = '<?php echo $this->layoutSetting;?>';
  if(lsetting == 1) {
  $$('li.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) 
  {	
     if($(globalContentElement).getElement('.layout_sitepageintegration_profile_items')) {
			  $(globalContentElement).getElement('.layout_sitepageintegration_profile_items').style.display = 'none';
	   }

     if(page_showtitle != 0 ) {
			if($('profile_status')) {
				$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage->getTitle())?></h2>";
			}	  
			if($('layout_info')) {
				$('layout_info').style.display = 'block';
				$('show_basicinfo').style.display = 'none';
			}	  	
    }
    
    hideWidgetsForModule('sitepageinfo');

    if($(globalContentElement).getElement('.layout_sitepage_contactdetails_sitepage')) {
      $(globalContentElement).getElement('.layout_sitepage_contactdetails_sitepage').style.display = 'block';
    }

  	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {    	
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }

    prev_tab_id = '<?php echo $this->content_id; ?>';	
    prev_tab_class = 'layout_sitepage_info_sitepage';
		setLeftLayoutForPage(); 
		if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitepage_info_sitepage')==null)){
      scrollToTopForPage($(globalContentElement).getElement(".layout_sitepage_info_sitepage"));
    }	        
  });
  } else {
    $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) 
  {	
     if($(globalContentElement).getElement('.layout_sitepageintegration_profile_items')) {
			  $(globalContentElement).getElement('.layout_sitepageintegration_profile_items').style.display = 'none';
	   }

     if(page_showtitle != 0 ) {
			if($('profile_status')) {
				$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage->getTitle())?></h2>";
			}	  
			if($('layout_info')) {
				$('layout_info').style.display = 'block';
				$('show_basicinfo').style.display = 'none';
			}	  	
    }
    
    hideWidgetsForModule('sitepageinfo');

    if($(globalContentElement).getElement('.layout_sitepage_contactdetails_sitepage')) {
      $(globalContentElement).getElement('.layout_sitepage_contactdetails_sitepage').style.display = 'block';
    }

  	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {    	
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }

    prev_tab_id = '<?php echo $this->content_id; ?>';	
    prev_tab_class = 'layout_sitepage_info_sitepage';
		setLeftLayoutForPage(); 
		if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitepage_info_sitepage')==null)){
      scrollToTopForPage($(globalContentElement).getElement(".layout_sitepage_info_sitepage"));
    }	        
  });
  }
  var tagAction =function(tag)
  {    
    $('tag').value = tag;
    $('filter_form').submit();
  }
	if($("info_comment"))
  var info_comment = $("info_comment").innerHTML;

function showSmoothBox(url)
{
  Smoothbox.open(url);
  parent.Smoothbox.close;
}
</script>
<style>
    .comments {
        display: none;
    }
</style>
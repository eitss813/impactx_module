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
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
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

		<?php echo $this->translate("Basic Information") ?>

<?php endif;?>
<!--<div id='id_<?php echo $this->content_id; ?>'>-->
      
<!--<div class='profile_fields'>-->
	<h4 id='show_basicinfo'>
		<span><?php echo $this->translate('Basic Information'); ?></span>
	</h4>
  
	<div class="sm_ui_item_profile_details">
	<table>
		<tbody>
    <?php if($this->sitepage->show_name) :?>
      <?php if($postedBy):?>
        <tr valign="top">
          <td class="label"><div><?php echo $this->translate('Posted By:'); ?> </div></td>
          <td><?php echo $this->htmlLink($this->sitepage->getParent(), $this->sitepage->getParent()->getTitle()) ?></td>
        </tr>
      <?php endif;?>
      <?php endif;?>
    	<tr valign="top">
					<td class="label"><div><?php echo $this->translate('Posted:'); ?></div></td>
      <td><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitepage->creation_date))) ?></td>
    </tr> 
    <tr valign="top">
					<td class="label"><div><?php echo $this->translate('Last Updated:'); ?></div></td>
			<td><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitepage->modified_date))) ?></td>
   </tr>
    <?php if(!empty($this->sitepage->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
    	<tr valign="top">
			<td class="label"><div> <?php echo ($this->sitepage->member_title && $this->sitepage->member_count >1 && Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1) ) ? $this->sitepage->member_title . ':' :$this->translate('Members:'); ?></div></td>
			<td><?php echo $this->sitepage->member_count ?></td>
    </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitepage->comment_count)): ?>
    	<tr valign="top">
    		<td class="label"><div><?php echo $this->translate('Comments:'); ?></div></td>
				<td><?php echo $this->sitepage->comment_count ?></td>
       </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitepage->view_count)): ?>
    	<tr valign="top">
      <td class="label"><div><?php echo $this->translate('Views:'); ?></div></td>
			<td><?php echo $this->sitepage->view_count ?></td>
      </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitepage->like_count)): ?>
    <tr valign="top">
    	<td class="label"><div><?php echo $this->translate('Likes:'); ?></div></td>
			<td><?php echo $this->sitepage->like_count ?></td>
     </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitepage->follow_count) && isset($this->sitepage->follow_count)): ?>
  	<tr valign="top">
    	<td class="label"><div><?php echo $this->translate('Followers:'); ?></div></td>
				<td><?php echo $this->translate( $this->sitepage->follow_count) ?></td>
    </tr>
    <?php endif; ?>
    <tr valign="top" class="mtop5">
	    <?php if($this->category_name != '' && $this->subcategory_name == '') :?>
		    <td class="label"><div><?php echo $this->translate('Category:'); ?></div></td>		 
		    <td>	
				<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name)), 'sitepage_general_category'), $this->translate($this->category_name)) ?>
				</td>
	    <?php elseif($this->category_name != '' && $this->subcategory_name != ''): ?> 
		    <td class="label"><div><?php echo $this->translate('Category:'); ?></div></td>	
		    <td>	<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name)), 'sitepage_general_category'), $this->translate($this->category_name)) ?>
				<?php if(!empty($this->category_name)): echo '&raquo;'; endif; ?>
			  <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name)), 'sitepage_general_subcategory'), $this->translate($this->subcategory_name)) ?>			  
			  <?php if(!empty($this->subsubcategory_name)): echo '&raquo;';?>
        <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->sitepage->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subsubcategory_name)), 'sitepage_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
	   		<?php endif; ?>
        </td>	
	    <?php endif; ?>
    </tr>	
   <tr valign="top">    	
    	<?php if (count($this->sitepageTags) >0): $tagCount=0;?>
    		<td class="label"><div><?php echo $this->translate('Tags:'); ?></div></td>	
        <td>
    		 <?php foreach ($this->sitepageTags as $tag): ?>
					<?php if (!empty($tag->getTag()->text)):?>
						<?php if(empty($tagCount)):?>
							<a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitepage_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
							<?php $tagCount++; else: ?>
							<a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitepage_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
              
              
						<?php endif; ?>
					<?php endif; ?>
        <?php endforeach; ?>
        </td>	
			<?php endif; ?>
  </tr>	
    <?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1); ?>
     <?php if($this->sitepage->price && $enablePrice):?>
    <tr valign="top">    
    	<td class="label"><div><?php echo $this->translate('Price:'); ?></div></td>	
      <td><?php echo Engine_Api::_()->sitepage()->getPriceWithCurrency($this->sitepage->price); ?></td>	
     </tr>	
    <?php endif; ?>
     <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1); ?>
     <?php if($this->sitepage->location && $enableLocation):?>
    <tr valign="top">    
    	<td class="label"><div><?php echo $this->translate('Location:'); ?></div></td>	
      <td><?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($this->sitepage->location), $this->sitepage->location, array('target' => 'blank')) ?>
      </td>
   </tr>	
    <?php endif; ?>
    <tr valign="top">    
    	<td class="label"><div><?php echo $this->translate('Description:'); ?></div></td>
      <td><?php echo $this->viewMore($this->sitepage->body,300,5000) ?></td>
    </tr>	
  </tbody>
  </table>
    </div>
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
   <div class="sm_ui_item_profile_details">
	<table>
		<tbody>
          <tr valign="top" style="display:none;"> </tr>
      <?php if(isset($options_create['phone']) && $options_create['phone'] == 'Phone'):?>
        <?php if(!empty($this->sitepage->phone)):?>
        <tr valign="top"> 
          <td class="label"><div><?php echo $this->translate('Phone:'); ?></div></td>
          <td><?php echo $this->translate(''); ?> <a href="tel:<?php echo $this->sitepage->phone?>"> <?php echo $this->sitepage->phone?> </a></td>
        </tr>
        <?php endif; ?>
      <?php endif; ?>

      <?php if(isset($options_create['email']) && $options_create['email'] == 'Email'):?>
        <?php if(!empty($this->sitepage->email)):?>
        <tr valign="top"> 
          <td class="label"><div><?php echo $this->translate('Email:'); ?></div></td>
          <td><?php echo $this->translate(''); ?>
          <a href='mailto:<?php echo $this->sitepage->email ?>'><?php echo $this->sitepage->email ?></a></td>
        </tr>
        <?php endif; ?>
      <?php endif; ?>
      <?php if( isset($options_create['website']) && $options_create['website'] == 'Website'):?>
        <?php if(!empty($this->sitepage->website)):?>
        <tr valign="top"> 
         <td class="label"><div><?php echo $this->translate('Website:'); ?></div></td>
          <?php if(strstr($this->sitepage->website, 'http://') || strstr($this->sitepage->website, 'https://')):?>
          <td><a href='<?php echo $this->sitepage->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitepage->website ?></a></td>
          <?php else:?>
          <td><a href='http://<?php echo $this->sitepage->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitepage->website ?></a></td>
          <?php endif;?>
        </tr>
        <?php endif; ?>
      <?php endif; ?>
    </tbody>
  </table>
     </div>
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
	<?php echo $this->content()->renderWidget("sitemobile.comments", array('type' => $this->sitepage->getType(), 'id' => $this->sitepage->getIdentity())); ?>
<style>
    .comments{
        display: none;
    }
</style>
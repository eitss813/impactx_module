<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featured.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate("Advanced Photo Albums Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs clr'>
  <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
  </div>
<?php endif; ?>
<?php  if( empty( $this->isAlbum ) ){ return; } ?>
<p><?php echo $this->translate("This page lists all the Featured Albums of your community. You can also add a new album as featured using the link below. Albums can also be made featured / un-featured by you from their main page. Featured Albums are displayed in the Featured Albums Slideshow, Recent / Random / Popular Albums, etc widgets."); ?></p>
<br />
<div class="tip"> <span> <?php echo $this->translate("You should only make those albums featured whose view privacy has been set as 'Everyone' or 'All Registered Members'; If you have featured an album which has a different privacy,  then those albums below will be specially highlighted."); ?> </span> </div>
<br />
<div>
  <a href="<?php echo $this->url(array('action' =>'add-featured')) ?>" class="smoothbox buttonlink seaocore_icon_add" title="<?php echo $this->translate('Make Featured Album');?>"><?php echo $this->translate('Add an Album as Featured');?></a>
</div>
<br />
<div>
<?php echo $this->paginator->getTotalItemCount(). $this->translate(' results found');?>
 </div>
<br />
<div>
	<?php if ($this->paginator->getTotalItemCount() > 0): ?>
		<table class='admin_table' width="100%">
	    <thead>
	      <tr>       
	       <th width="19%" align="left"><?php echo $this->translate("Album") ?></th>
         <th width="19%" align="left"><?php echo $this->translate("Album Name") ?></th>
	        <th width="19%" align="left"><?php echo $this->translate("Owner") ?></th>
	        <th width="19%" class="center"><?php echo $this->translate("No. of Photos") ?></th>
	        <th width="19%" align="left"><?php echo $this->translate("Creation Date") ?></th>
	        <th width="19%" align="left"><?php echo $this->translate("Options");?></th>
	      </tr>
	    </thead>
	    <tbody>
	      <?php $auth = Engine_Api::_()->authorization()->context; ?>
	      <?php foreach ($this->paginator as $album): ?>
	      <?php if( 1 === $auth->isAllowed($album, 'everyone', 'view') ||  1 === $auth->isAllowed($album, 'registered', 'view')  ) :?>
		      <tr>
	      <?php else:?>
		      <tr class="sitealbum_list_highlighted">
	      <?php endif; ?>
	        <td width="19%" align="left" class="sitealbum_table_img"> <?php echo $this->htmlLink($album->getHref(),$this->itemPhoto($album, 'thumb.normal'),array('title'=>$album->getTitle())); ?></td>
         <td width="19%" class="admin_table_bold"><?php echo $this->htmlLink($album->getHref(), $album->getTitle(),array('target'=>'_blank')); ?></td>
          <td width="19%" class="admin_table_bold">
	        	<?php
	            $owner = $album->getOwner();
	            echo $this->translate($this->htmlLink($owner->getHref(), $owner->getTitle()));
	          ?>
	        </td>
	        <td width="19%" class="center"><?php echo $album->photos_count?></td>
	        <td width="19%"><?php echo $this->translate(gmdate('M d,Y',strtotime($album->creation_date)))?></td>
	        <td width="19%">
	          <a href='<?php echo $this->url(array('action' => 'remove-featured', 'id' => $album->getIdentity())) ?>' class="smoothbox" title="<?php echo $this->translate("Remove as featured") ?>">
		         <?php echo $this->translate("Remove as featured") ?>
		        </a>
	        </td>
	      </tr>
	      <?php endforeach;?>
	    </tbody>
	  </table>
	<?php else: ?>
	  <div class="tip">
	    <span><?php echo $this->translate("No albums have been featured."); ?></span>
	  </div>
	<?php endif; ?>
	<br />
	<?php echo $this->paginationControl($this->paginator); ?>
</div>	

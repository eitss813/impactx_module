<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: user-full-bio.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js');
?>
<?php
    $user = $this->user;
	$bio = $this->ownerBio;
 ?>
<div class="global_form_popup" id="user-full-bio-smoothbox" style="width:1000px;">
	
	<div class="sitecrowdfunding_user_profile_information">
	    <div class="sitecrowdfunding_user_profile_photo">
	        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'), array('class' => 'thumbs_photo', 'title' => $this->translate($user->getTitle()))); ?>
	        <div class="sitecrowdfunding_user_profile_title">
	            <?php echo $this->htmlLink($user->getHref(), $this->translate($user->getTitle())); ?>
	        </div>
	        <div class="sitecrowdfunding_user_profile_sociallinks mtop10">
	            
	                <?php if(!empty($bio['facebook_profile_url'])): ?>
	                    <div class="seao_icon_facebook_square mtop10">
	                        <?php echo $this->htmlLink($bio['facebook_profile_url'], $bio['facebook_profile_url'], array('target' => '_blank')) ?>
	                    </div>
	                <?php endif; ?> 

	                <?php if(!empty($bio['twitter_profile_url'])): ?> 
	                    <div class="seao_icon_twitter_square mtop10">
	                        <?php echo $this->htmlLink($bio['twitter_profile_url'], $bio['twitter_profile_url'], array('target' => '_blank')) ?>
	                    </div>
	               	<?php endif; ?>

	                <?php if(!empty($bio['instagram_profile_url'])): ?>
	                    <div class="seao_icon_instagram_square mtop10">
	                        <?php echo $this->htmlLink($bio['instagram_profile_url'], $bio['instagram_profile_url'], array('target' => '_blank')) ?>
	                    </div>
	                <?php endif; ?>

	                <?php if(!empty($bio['vimeo_profile_url'])): ?> 
	                    <div class="seao_icon_vimeo_square mtop10">
	                        <?php echo $this->htmlLink($bio['vimeo_profile_url'], $bio['vimeo_profile_url'], array('target' => '_blank')) ?>
	                    </div>
	               	<?php endif; ?>

	                <?php if(!empty($bio['youtube_profile_url'])): ?>
	                    <div class="seao_icon_youtube_square mtop10">
	                        <?php echo $this->htmlLink($bio['youtube_profile_url'], $bio['youtube_profile_url'], array('target' => '_blank')) ?>
	                    </div>
	                <?php endif; ?>

	                <?php if(!empty($bio['website_url'])): ?>
	                    <div class="seao_icon_sharelink_square mtop10">
	                        <?php echo $this->htmlLink($bio['website_url'], $bio['website_url'], array('target' => '_blank')) ?>
	                    </div>
	                <?php endif; ?> 
	            
	        </div>
	    </div>
	    <div class="sitecrowdfunding_user_profile_info"> 
	        
	            <?php if(!empty($bio['biography'])): ?>
	                <div class="sitecrowdfunding_user_profile_bio mbot10">
	                    <h3><?php echo $this->translate('Biography:') ?></h3>
	                    <?php echo $bio['biography'] ?> 
	                </div>
	            <?php endif; ?> 

	            <?php if(!empty($bio['phone'])): ?>
	                <div class="sitecrowdfunding_user_profile_phone mbot10">
	                    <?php echo $this->translate('Phone:') ?>
	                    <?php echo $bio['phone'] ?>
	                </div>
				<?php endif; ?>
	             
	            <?php if(!empty($bio['review_count'])): ?>
	                <div class="sitecrowdfunding_user_profile_review mbot10">
	                    <?php echo $this->translate(array('%s Review Count:', '%s Review Counts:', $bio['review_count']), $bio['review_count']); ?>
	                </div>
	            <?php endif; ?> 
	        
	        <div class="sitecrowdfunding_user_profile_projects mtop10">
	            <h3 class="mtop10">Projects :</h3>
	            <ul>
	                <?php if ($this->totalCount < 1) : ?>
	                    <li>
	                        <?php $url = $this->url(array('controller' => 'project', 'action' => 'create'), 'sitecrowdfunding_project_general'); ?>
	                            No Projects Started Yet. 
	                    </li>
	                <?php else: ?>
	                    <?php foreach ($this->projects as $project) : ?>
	                        <li>
	                            <?php echo $this->htmlLink($project->getHref(), $this->string()->truncate($this->string()->stripTags($project->getTitle()), 30), array('title' => $project->getTitle())) ?>
	                        
	                    <?php if($project->parent_type != 'user'): ?>
	                        <?php $parentContent = Engine_Api::_()->getItem($project->parent_type, $project->parent_id);?>
	                            <br>
	                            <span> <?php echo $this->translate('Belongs to:'); ?></span>
	                            <span>
	                                <?php echo $this->htmlLink($parentContent->getHref(), $this->string()->truncate($this->string()->stripTags($parentContent->getTitle()), 30), array('title' => $parentContent->getTitle())) ?>
	                            </span>
	                    <?php endif; ?>
	                    </li>
	                    <?php endforeach; ?>
	                <?php endif; ?>
	            </ul>
	        </div>
	    </div>
	</div> 

	<?php $url = $this->user->getHref(array('tab'=> $this->contentwidget_id)); ?>
	<button onClick="window.parent.location='<?php echo $url; ?>'">
		<?php echo $this->translate("See More") ?>
	</button> 
	<a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a>
</div>
<script>
	$('user-full-bio-smoothbox').getElements('a').addEvent('click', function (event) {
		event.stop();
		// window.open(event.target.href); // TO OPEN IN NEXT TAB
		window.parent.location.href = event.target.href; // TO OPEN IN SAME TAB
	})
</script>
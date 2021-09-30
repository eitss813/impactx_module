<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: takeaction.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup sitepage_claim_action_popup">
	<div class="settings">
	  <form class="global_form" method="POST">
	    <div>
	      <?php
	      $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage_id)), 'sitepage_entry_view');
	      $sitepage_title = "<a href='$url' target='_blank'>$this->sitepage_title</a>";
	      ?>

	      <?php if ($this->claiminfo->status == 1): ?>
	        <h3><?php echo $this->translate("Details"); ?></h3>
	        <p><?php echo $this->translate("Below are the details of the claim request that was approved.") ?></p>
	      <?php elseif ($this->claiminfo->status == 2): ?>
	        <h3><?php echo $this->translate("Details"); ?></h3>
	        <p><?php echo $this->translate("Below are the details of the claim request that was declined.") ?></p>
	      <?php else: ?>	
	        <h3><?php echo $this->translate("Take an Action"); ?></h3>
	        <p><?php echo $this->translate("Please take an appropriate action on the claim for this page: ") ?><?php echo $sitepage_title; ?></p>
	        <p><?php echo $this->translate("Once you save this form, an email will be sent to this claimer stating the action taken by you.") ?></p><br />
	      <?php endif; ?>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Member Id:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claiminfo->user_id; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Claimer Name:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claiminfo->nickname; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Email:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claiminfo->email; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Claimed Date:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claiminfo->creation_date; ?>
	        </div>
	      </div>		
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Last Action Taken:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claiminfo->modified_date; ?>
	        </div>
	      </div>
	      <?php if (!empty($this->claiminfo->contactno)): ?>			
	        <div class="form-wrapper">
	          <div class="form-label">
	            <label><?php echo $this->translate("Contact Number:") ?></label>
	          </div>
	          <div class="form-element">
	            <?php echo $this->claiminfo->contactno; ?>
	          </div>
	        </div>
	      <?php endif; ?>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("About Claimer and Page:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claiminfo->about; ?>
	        </div>
	      </div>
	      <?php if (!empty($this->claiminfo->usercomments)): ?>
	        <div class="form-wrapper">
	          <div class="form-label">
	            <label><?php echo $this->translate("User Comments:") ?></label>
	          </div>
	          <div class="form-element">
	            <?php echo $this->claiminfo->usercomments; ?>
	          </div>
	        </div>
	      <?php endif; ?>		
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Status:") ?> </label>
	        </div>
	        <div class="form-element">
	          <?php if ($this->claiminfo->status == 1) : ?>
	            <?php echo $this->translate("Approved") ?>
	          <?php elseif ($this->claiminfo->status == 2) : ?>
	            <?php echo $this->translate("Declined") ?>
	          <?php else: ?>
	            <select name="status">
	              <option value="1" <?php if ($this->claiminfo->status == 1): ?><?php echo "selected"; ?><?php endif; ?>><?php echo $this->translate("Approved") ?></option>
	              <option value="2" <?php if ($this->claiminfo->status == 2): ?><?php echo "selected"; ?><?php endif; ?>><?php echo $this->translate("Declined") ?></option>
	              <option value="4" <?php if ($this->claiminfo->status == 4): ?><?php echo "selected"; ?><?php endif; ?>><?php echo $this->translate("Hold") ?></option>
	            </select>
	          <?php endif; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Admin's Comments:") ?> </label>
	        </div>
	        <div class="form-element">
	          <?php if ($this->claiminfo->status == 1 || $this->claiminfo->status == 2) : ?>
	            <?php if (!empty($this->claiminfo->comments)): ?>
	              <?php echo $this->claiminfo->comments; ?>
	            <?php else: ?>
	              <?php echo '---'; ?>
	            <?php endif; ?>
	          <?php elseif ($this->claiminfo->status == 3 || $this->claiminfo->status == 4): ?>		
	            <textarea name="comments"><?php echo $this->claiminfo->comments; ?></textarea>					
	          <?php endif; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label>&nbsp;</label>
	        </div>
	        <div class="form-element">
	          <?php if ($this->claiminfo->status == 1) : ?>
	            <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close") ?></button>
	          <?php elseif ($this->claiminfo->status == 2) : ?>
	            <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close") ?></button>
	          <?php else: ?>
	            <button type='submit'><?php echo $this->translate('Save'); ?></button>
	            <?php echo $this->translate(" or ") ?> 
	            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("cancel") ?></a>
	          <?php endif; ?>
	        </div>
	      </div>
	    </div>
	  </form>
	</div>
</div>
<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: create.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>
<?php include APPLICATION_PATH .  '/application/modules/Sesnewsletter/views/scripts/dismiss_message.tpl';?>
<div class="sesbasic_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managecampaigns', 'action' => 'index'), $this->translate("Back to Manage Newsletters") , array('class'=>'sesbasic_icon_back buttonlink')); ?>
</div>
<div class='clear'>
  <div class='settings sesbasic_admin_form'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script type="application/javascript">

  window.addEvent('load', function() {
    choosemember(0);
  });
  
  function choosemember (value) {
    if(value == 1) {
      $('member_levels-wrapper').style.display = 'none';
      $('networks-wrapper').style.display = 'none';
      $('profile_types-wrapper').style.display = 'none';
    } else if(value == 2) {
      $('member_levels-wrapper').style.display = 'none';
      $('networks-wrapper').style.display = 'none';
      $('profile_types-wrapper').style.display = 'none';
    } else if(value == 0) {
      $('member_levels-wrapper').style.display = 'none';
      $('networks-wrapper').style.display = 'none';
      $('profile_types-wrapper').style.display = 'none';
      $('external_emails-wrapper').style.display = 'none';
      $('member_name-wrapper').style.display = 'none';
    } else if(value == 4) {
      $('member_levels-wrapper').style.display = 'block';
      $('networks-wrapper').style.display = 'block';
      $('profile_types-wrapper').style.display = 'block';
      $('external_emails-wrapper').style.display = 'none';
      $('member_name-wrapper').style.display = 'none';
    }
  }
</script>

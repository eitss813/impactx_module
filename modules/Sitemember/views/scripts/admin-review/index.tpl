<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<h2><?php echo $this->translate("Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
	<div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">

	window.addEvent('domready', function(){
		prosconsInReviews('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.proscons', 1); ?>');
    reviewRatingInSitemember('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2); ?>');
	});

  function reviewRatingInSitemember(option) {
  
    if(option == 0 || option == 3) {
      $('sitemember_proncons-wrapper').style.display = 'none';
      $('sitemember_proscons-wrapper').style.display = 'none';
      $('sitemember_limit_proscons-wrapper').style.display = 'none';
      $('sitemember_recommend-wrapper').style.display = 'none';
      $('sitemember_summary-wrapper').style.display = 'none';
      $('sitemember_report-wrapper').style.display = 'none';
      $('sitemember_share-wrapper').style.display = 'none';
      $('sitemember_email-wrapper').style.display = 'none';
    } else {
        $('sitemember_proncons-wrapper').style.display = 'block';
        $('sitemember_proscons-wrapper').style.display = 'block';
        $('sitemember_limit_proscons-wrapper').style.display = 'block';
        $('sitemember_recommend-wrapper').style.display = 'block';
        $('sitemember_summary-wrapper').style.display = 'block';
        $('sitemember_report-wrapper').style.display = 'block';
        $('sitemember_share-wrapper').style.display = 'block';
        $('sitemember_email-wrapper').style.display = 'block';
    }
  }


	function prosconsInReviews(option) {

		if($('sitemember_proncons-wrapper')) {
			if(option == 1) {
			$('sitemember_proncons-wrapper').style.display = 'block';
			$('sitemember_limit_proscons-wrapper').style.display = 'block';
			} else {
				$('sitemember_proncons-wrapper').style.display = 'none';
				$('sitemember_limit_proscons-wrapper').style.display = 'none';
			}
		}

	}

</script>
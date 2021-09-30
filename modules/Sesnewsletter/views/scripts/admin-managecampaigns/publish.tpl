<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: publish.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>
<div class='clear'>
  <div class='settings global_form_popup'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script>
  en4.core.runonce.add(function(){
    starttime.calendars[0].start = new Date( $('starttime-date').value );
    starttime.navigate(starttime.calendars[0], 'm', 1);
    starttime.navigate(starttime.calendars[0], 'm', -1);
  });

  window.addEvent('domready',function() {
    showDate(1);
  });
  $('starttime-hour').hide();
  $('starttime-minute').hide();
  $('starttime-ampm').hide();
  function showDate(value) {
    if(value == 1) {
      if($('starttime-wrapper'))
        $('starttime-wrapper').style.display = 'none';
    } else {
        if($('starttime-wrapper'))
          $('starttime-wrapper').style.display = 'block';
    }
  }
</script>

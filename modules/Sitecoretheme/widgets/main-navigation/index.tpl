<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="sitecoretheme_main_navigation_container">
  <div id="sitecoretheme_main_navigation_content_<?php echo $this->identity; ?>" class="sitecoretheme_main_navigation_container_inner">

  </div>
</div>

<script type="text/javascript">
  $('sitecoretheme_main_navigation_content_<?php echo $this->identity; ?>').getParent('.generic_layout_container').hide();
  en4.core.runonce.add(function () {
    var headline = $('global_wrapper').getElementById('global_content').getElement('.headline');
    if ($('global_header') && headline.getElement('ul')) {
      $('sitecoretheme_main_navigation_content_<?php echo $this->identity; ?>').getParent('.generic_layout_container').show();
      if (headline.getParent('.generic_layout_container')) {
        headline.getParent('.generic_layout_container').removeClass('generic_layout_container').inject($('sitecoretheme_main_navigation_content_<?php echo $this->identity; ?>'));
      } else {
        headline.inject($('sitecoretheme_main_navigation_content_<?php echo $this->identity; ?>'));
      }
      $('global_header').addClass('sitecoretheme_main_navigation_header');
    }
  });
</script>
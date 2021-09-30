<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php if ($this->canMakeFeatured && $this->allowView): ?> 
	<div><a href="javascript:void(0);" class="seaocore_icon_featured seaocore_featured_icon_size" onclick='featured();' ><span id="featured_sitealbum" <?php if ($this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" > <?php echo $this->translate("Make Featured"); ?> </span> 
		<span id="un_featured_sitealbum" <?php if (!$this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a></div>
<?php endif; ?>

<script type="text/javascript">
  function featured()
  {
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sitealbum/album/featured',
      'data': {
        format: 'html',
        'subject': '<?php echo $this->subject()->getGuid() ?>'
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('featured_sitealbum').style.display == 'none') {
          $('featured_sitealbum').style.display = "";
          $('un_featured_sitealbum').style.display = "none";
        } else {
          $('un_featured_sitealbum').style.display = "";
          $('featured_sitealbum').style.display = "none";
        }
      }
    }));
    return false;
  }
</script>
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
<section class="sitecoretheme_text_banner_container_fluid">
  <div class="sitecoretheme_text_banner_container">
    <div class="sitecoretheme_text_banner_content_wrapper">
      <h2><?php echo $this->text; ?></h2>
      <?php if( !empty($this->url) ) : ?>
        <button onClick="sitecoretheme_buttonclick()"> <?php echo $this->translate($this->ctatext); ?></button>
      <?php endif; ?>
    </div>
  </div>
</section>

<script type="text/javascript">
  function sitecoretheme_buttonclick() {
    <?php if( !empty($this->newtab) ) :?>
      window.open('<?php echo $this->url;?>');
    <?php else: ?>
      window.location = '<?php echo $this->url;?>';
    <?php endif; ?>
  }

</script>
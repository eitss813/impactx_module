<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: copy-url.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo "URL of this page"; ?></h3>
    <div class="staticpage_get_code_box staticpage_get_code_field" id="copy_url">
      <?php $url_http = constant(_ENGINE_SSL) ? 'https://' : 'http://'; ?>
      <?php $base_url = $this->baseUrl(); ?>
      <?php if (!empty($this->short_url)): ?>
        <?php if (!empty($base_url)): ?>
          <?php echo $url_http ?><?php echo $_SERVER['HTTP_HOST']; ?><?php echo $this->baseUrl(); ?>/<?php echo $this->page_url; ?>
        <?php else: ?>
          <?php echo $url_http ?><?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $this->page_url; ?>
        <?php endif; ?>
      <?php else: ?>
        <?php if (!empty($base_url)): ?>
          <?php echo $url_http ?><?php echo $_SERVER['HTTP_HOST']; ?><?php echo $this->baseUrl(); ?>/<?php echo $this->default_url; ?>/<?php echo $this->page_url; ?>
        <?php else : ?>
          <?php echo $url_http ?><?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $this->default_url; ?>/<?php echo $this->page_url; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <button type='button' onclick='javascript:SelectUrl();'><?php echo "Select To Copy"; ?></button>
    <?php echo " or "; ?> 
    <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close();'>
      <?php echo "cancel"; ?></a>
  </div>
</form>

<script>

      function SelectUrl() {
        var selection = window.getSelection();
        var range = document.createRange();
        range.selectNodeContents(document.getElementById('copy_url'));
        selection.removeAllRanges();
        selection.addRange(range);
      }
</script>

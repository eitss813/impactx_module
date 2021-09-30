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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/core.js'); ?>

<?php if ($this->is_ajax_load): ?>

  <ul class="seaocore_sidebar_list" id="browse_sitealbum_tagsCloud">
    <li>
      <div>
        <?php foreach ($this->tag_array as $key => $frequency): ?>
          <?php $string = $this->string()->escapeJavascript($key); ?>
          <?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
          <a href='<?php echo $this->url(array('action' => 'browse'), "sitealbum_general"); ?>?tag=<?php echo urlencode($key) ?>&tag_id=<?php echo $this->tag_id_array[$key] ?>' style="font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a> 
        <?php endforeach; ?>
      </div>		
    </li>

    <?php if (empty($this->notShowExploreTags)) : ?>
      <li>
        <?php echo $this->htmlLink(array('route' => "sitealbum_general", 'action' => 'tagscloud'), $this->translate('Explore Tags &raquo;'), array('class' => 'more_link')) ?>
      </li>
    <?php endif; ?>
  </ul>

<?php else: ?>

  <div id="layout_sitealbum_tagcloud_sitealbum_<?php echo $this->identity; ?>"></div>
  <script>
    en4.core.runonce.add(function() {
      en4.sitealbum.ajaxTab.sendReq({
        requestParams: $merge(<?php echo json_encode($this->allParams); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
        responseContainer: [$('layout_sitealbum_tagcloud_sitealbum_<?php echo $this->identity; ?>')],
        loading: false
      });
    });
  </script>           
<?php endif; ?>


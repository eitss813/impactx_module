<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php if ($this->is_ajax_load): ?>
    <ul class="seaocore_sidebar_list" id="browse_sitevideo_tagsCloud">
        <li>
            <div>
                <?php foreach ($this->tag_array as $key => $frequency): ?>
                    <?php $string = $this->string()->escapeJavascript($key); ?>
                    <?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
                    <a href='<?php echo $this->url(array('action' => 'browse'), "sitevideo_video_general"); ?>?tag=<?php echo urlencode($key) ?>&tag_id=<?php echo $this->tag_id_array[$key] ?>' style="font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a> 
                <?php endforeach; ?>
            </div>		
        </li>
        <?php if (empty($this->notShowExploreTags)) : ?>
            <li>
                <?php echo $this->htmlLink(array('route' => "sitevideo_video_general", 'action' => 'tagscloud'), $this->translate('Explore Tags &raquo;'), array('class' => 'more_link')) ?>
            </li>
        <?php endif; ?>
    </ul>
<?php else: ?>
    <div id="layout_sitevideo_tagcloud_sitevideo_<?php echo $this->identity; ?>"></div>
    <script>
        en4.core.runonce.add(function () {
            en4.sitevideo.ajaxTab.sendReq({
                requestParams: $merge(<?php echo json_encode($this->allParams); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
                responseContainer: [$('layout_sitevideo_tagcloud_sitevideo_<?php echo $this->identity; ?>')],
                loading: false
            });
        });
    </script>           
<?php endif; ?>


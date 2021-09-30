<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php if ($this->is_ajax_load): ?>
<ul class="seaocore_sidebar_list" id="browse_sitecrowdfunding_tagsCloud">
    <li>
        <div style="display: flex;flex-direction: column">
            <?php foreach ($this->tag_array as $key => $frequency): ?>
            <?php $string = $this->string()->escapeJavascript($key); ?>
            <?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
            <a href='<?php echo $this->url(array('action' => 'browse'), "sitecrowdfunding_project_general"); ?>?tag=<?php echo urlencode($key) ?>&tag_id=<?php echo $this->tag_id_array[$key] ?>' style="border-bottom: 1px solid #eee;padding: 10px;border-radius: 3px;font-size:16<?php //echo $step ?>px;" title=''><?php echo $this->translate($key) ?><sup style="display: none"><?php echo $frequency ?></sup></a>
            <?php endforeach; ?>
        </div>
    </li>
    <?php if (!empty($this->showMoreTag) && $this->action != 'tagscloud') : ?>
    <?php if (!empty($this->showLink)) : ?>
    <li>
        <?php echo $this->htmlLink(array('route' => "sitecrowdfunding_project_general", 'action' => 'tagscloud'), $this->translate('Explore Tags &raquo;'), array('class' => 'common_btn')) ?>
    </li>
    <?php endif; ?>
    <?php endif; ?>
</ul>
<?php else: ?>
<div id="layout_sitecrowdfunding_tagcloud_sitecrowdfunding_<?php echo $this->identity; ?>"></div>
<script>
    en4.core.runonce.add(function () {
        en4.sitecrowdfunding.ajaxTab.sendReq({
            requestParams: $merge(<?php echo json_encode($this->allParams); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
        responseContainer: [$('layout_sitecrowdfunding_tagcloud_sitecrowdfunding_<?php echo $this->identity; ?>')],
            loading: false
    });
    });
</script>
<?php endif; ?>


<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: channelurl.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Advanced Videos / Channels / Playlists Plugin'); ?></h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<?php if (count($this->subnavigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render(); ?>
    </div>
<?php endif; ?>
<?php $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.showurl.column', 1); ?>
<?php $edit_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.edit.url', 0); ?>

<h3>
    <?php echo $this->translate('Manage Channels having Banned URLs'); ?>
</h3>
<div class="tip">
    <span>
        <?php echo $this->translate('Note: You can edit Channel URLs from here only if you have enabled the fields: “Custom Channel URL” and “Edit Custom Channel URL” from Global Settings.'); ?>
    </span>
</div>
<p>
    <?php echo $this->translate('Below is the list of all the Channels on your site that have been assigned banned short URLs. Such a situation can arise because of a newly added banned URL. Here, you can edit the URLs of these Channels to remove the URL conflict.'); ?>
</p>

<br />

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>

<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function (order, default_direction) {

        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }
        $('filter_form').submit();
    }

</script>

<?php
if (!empty($this->paginator)) {
    $counter = $this->paginator->getTotalItemCount();
}
if (!empty($counter)):
    ?>
    <div class='admin_members_results'>
        <div>
            <?php echo $this->translate(array('%s result found.', '%s results found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
        </div>
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>
    <br />
    <table class='admin_table' border="0">
        <thead>
            <tr>
                <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('word', 'ASC');"><?php echo $this->translate('Banned URL'); ?></th>
                <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Channel Title'); ?></th>
                <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('channel_url', 'ASC');"><?php echo $this->translate('Channel URL'); ?></th>
                <?php if (!empty($show_url) && !empty($edit_url)): ?>
                    <th style='width: 4%;' class='admin_table_options' align="left"><?php echo $this->translate('Options'); ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->paginator as $item): ?>
                <tr>        
                    <td class='admin_table_bold'><?php echo $item->word; ?></td>
                    <?php
                    $truncation_limit = 16;
                    $tmpBody = strip_tags($item->title);
                    $item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
                    ?>
                    <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('sitevideo_channel', $item->channel_id)->getHref(), $item_title, array('title' => $item->title, 'target' => '_blank')) ?></td>
                    <td class='admin_table_bold'><?php echo $item->channel_url; ?></td>
                    <?php if (!empty($show_url) && !empty($edit_url)): ?>
                        <td class='admin_table_options'>
                            <a target="_blank" href='<?php echo $this->url(array('channel_id' => $item->channel_id, 'action' => 'edit'), 'sitevideo_specific', true) ?>' ><?php echo $this->translate('edit') ?></a> 
                        </td> 
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br />
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No Channels on your site have been assigned a banned URL.'); ?>
        </span>
    </div>
<?php endif; ?>

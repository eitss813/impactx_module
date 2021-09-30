<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: banningurl.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
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

<h3><?php echo $this->translate('Banned URLs'); ?></h3>
<p class="description"> <?php echo $this->translate('Below you can restrict the Short URLs that users can assign to their Channels. This can be used to ban URLs that you do not want to be assigned to Channels, or that are offensive.<br/>
You should ban all standard URLs, i.e., URLs from other plugins, etc. If a non-banned URL from a plugin gets assigned to a Channel, then that corresponding plugin’s webchannel will not be accessible, rather, the Channel will open at that URL. The list below comes pre-configured with some banned URLs. Whenever you install a new plugin on your website, then please visit here to see that its URLs are added here.<br/>
You can add URLs from a plugin to be banned from assigning to a Channel by using the “Add URLs from a Module” link below. To manually add a URL to be banned from assigning to a Channel, use the “Add URL” link below.<br/>
To check if a Channel on your site has a URL that has been newly banned, visit the “Channels with Banned URLs” tab.'); ?></p>
<div class="admin_search">
    <div class="search">
        <form method="post" class="global_form_box" action="">
            <div>
                <label>
                    <?php echo $this->translate("Banned URLs") ?>
                </label>
                <?php if (empty($this->word)): ?>
                    <input type="text" name="word" /> 
                <?php else: ?>
                    <input type="text" name="word" value="<?php echo $this->translate($this->word) ?>"/>
                <?php endif; ?>
            </div>
            <div style="margin:10px 0 0 10px;">
                <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
            </div>
        </form>
    </div>
</div>

<br />
<?php
echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'shorturl', 'action' => 'create-url'), $this->translate('Add URL'), array(
    'class' => 'smoothbox buttonlink seaocore_icon_add'))
?>
<?php
echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'shorturl', 'action' => 'add-url'), $this->translate('Add URLs from a Module'), array(
    'class' => 'smoothbox buttonlink seaocore_icon_add'))
?>
<br /><br />

<?php
if (!empty($this->paginator)) {
    $counter = $this->paginator->getTotalItemCount();
}
?>
<?php if (!empty($counter)) : ?>
    <table class='admin_table' width="70%">
        <thead>
            <tr>
                <th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('bannedpageurl_id', 'ASC');"><?php echo $this->translate('ID'); ?></th>
                <th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('word', 'ASC');"><?php echo $this->translate('Banned URLs'); ?></th>
                <th align="center"><?php echo $this->translate('Options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->paginator as $bannedurl): ?>
                <tr>
                    <td><?php echo $bannedurl->bannedpageurl_id; ?></td>
                    <td><?php echo $bannedurl->word; ?></td>
                    <td class="admin_table_centered"><?php
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'shorturl', 'action' => 'edit-url', 'id' => $bannedurl->bannedpageurl_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                ))
                ?> | <?php
                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'shorturl', 'action' => 'delete-url', 'id' => $bannedurl->bannedpageurl_id), $this->translate('delete'), array(
                            'class' => 'smoothbox',
                        ))
                        ?> </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo $this->paginationControl($this->paginator); ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No results were found.'); ?>
        </span>
    </div>
<?php endif; ?>

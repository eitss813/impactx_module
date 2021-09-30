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
<?php $this->headLink()->appendStylesheet($this->seaddonsBaseUrl() . '/application/modules/Seaocore/externals/styles/styles.css'); ?>
<ul class="seaocore_like_users_block">
    <?php
    $container = 1;
    foreach ($this->results as $path_info) {
        if ($container % 3 == 1) :
            ?>
            <li>
            <?php endif; ?>
            <div class="likes_member_seaocore">
                <div class="likes_member_thumb">
                    <?php echo $this->htmlLink($path_info->getHref(), $this->itemPhoto($path_info, 'thumb.icon'), array('class' => 'item_photo', 'title' => $path_info->getTitle(), 'target' => '_parent')); ?>
                </div>
            </div>		
            <?php if ($container % 3 == 0) : ?>
            </li>
        <?php endif; ?>	
        <?php
        $container++;
    }
    ?>
    <li>
        <div class="seaocore_like_users_block_links">
            <?php
            if (!empty($this->detail)) {
                echo '<a class="smoothbox fright" href="' . $this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'call_status' => 'public'), 'default', true) . '">' . $this->translate('See all') . '</a>';
            }
            ?>
        </div>	
    </li>
</ul>
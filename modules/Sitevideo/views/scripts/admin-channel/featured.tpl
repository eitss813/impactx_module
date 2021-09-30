<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featured.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate("Advanced Videos / Channels / Playlists Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs clr'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs clr'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render()
        ?>
    </div>
<?php endif; ?>

<?php
if (empty($this->isChannel)) {
    return;
}
?>
<p><?php echo $this->translate("This page lists all the Featured Channels of your community. You can also add a new channel as featured using the link below. You can add taglines along with tagline description to highlight the uniqueness of your channels. Featured Channels are displayed in the Featured Channels Slideshow, Recent / Random / Popular Channels widgets, etc. <br/> <br/>
Note: Channels can also be made featured / unfeatured by you from Manage Channels."); ?></p>
<br />
<div class="tip"> <span> <?php echo $this->translate("You should only make those channels featured whose view privacy has been set as 'Everyone' or 'All Registered Members' otherwise they are not visible to all members."); ?> </span> </div>
<br />
<div>
    <a href="<?php echo $this->url(array('action' => 'add-featured')) ?>" class="smoothbox buttonlink seaocore_icon_add" title="<?php echo $this->translate('Make Featured Channel'); ?>"><?php echo $this->translate('Add an Channel as Featured'); ?></a>
</div>
<br />
<div>
    <?php echo $this->paginator->getTotalItemCount() . $this->translate(' results found'); ?>
</div>
<br />
<div>
    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
        <table class='admin_table' width="100%">
            <thead>
                <tr>       
                    <th width="19%" align="left"><?php echo $this->translate("Channel") ?></th>
                    <th width="19%" align="left"><?php echo $this->translate("Channel Name") ?></th>
                    <th width="19%" align="left"><?php echo $this->translate("Owner") ?></th>
                    <th width="19%" class="center"><?php echo $this->translate("No. of Videos") ?></th>
                    <th width="19%" align="left"><?php echo $this->translate("Creation Date") ?></th>
                    <th width="19%" align="left"><?php echo $this->translate("Options"); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $auth = Engine_Api::_()->authorization()->context; ?>
                <?php foreach ($this->paginator as $channel): ?>
                    <?php if (1 === $auth->isAllowed($channel, 'everyone', 'view') || 1 === $auth->isAllowed($channel, 'registered', 'view')) : ?>
                        <tr>
                        <?php else: ?>
                        <tr class="sitevideo_list_highlighted">
                        <?php endif; ?>
                        <td width="19%" align="left" class="sitevideo_table_img"> <?php echo $this->htmlLink($channel->getHref(), $this->itemPhoto($channel, 'thumb.normal'), array('title' => $channel->getTitle())); ?></td>
                        <td width="19%" class="admin_table_bold"><?php echo $this->htmlLink($channel->getHref(), $channel->getTitle(), array('target' => '_blank')); ?></td>
                        <td width="19%" class="admin_table_bold">
                            <?php
                            $owner = $channel->getOwner();
                            echo $this->translate($this->htmlLink($owner->getHref(), $owner->getTitle()));
                            ?>
                        </td>
                        <td width="19%" class="center"><?php echo $channel->videos_count ?></td>
                        <td width="19%"><?php echo $this->translate(gmdate('M d,Y', strtotime($channel->creation_date))) ?></td>
                        <td width="19%">
                            <a href='<?php echo $this->url(array('action' => 'remove-featured', 'id' => $channel->getIdentity())) ?>' class="smoothbox" title="<?php echo $this->translate("Remove as featured") ?>">
                                <?php echo $this->translate("Remove as featured") ?>
                            </a> | 
                            <a href='<?php echo $this->url(array('action' => 'add-featured', 'id' => $channel->getIdentity())) ?>' class="smoothbox" title="<?php echo $this->translate("Edit details") ?>">
                                <?php echo $this->translate("Edit details") ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="tip">
            <span><?php echo $this->translate("No channels have been featured."); ?></span>
        </div>
    <?php endif; ?>
    <br />
    <?php echo $this->paginationControl($this->paginator); ?>
</div>	

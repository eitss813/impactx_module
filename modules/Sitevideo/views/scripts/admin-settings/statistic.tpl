<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: statistic.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo $this->translate('Advanced Videos / Channels / Playlists Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>
    <div class='clear'>
        <div class='settings'>
            <form class="global_form">
                <div>
                    <h3><?php echo $this->translate('Statistics for Videos / Channels'); ?></h3>
                    <p class="description"> <?php echo $this->translate('Below are some valuable statistics for the Videos / Channels submitted on this site.'); ?>
                    </p>
                    <br />

                    <table class='admin_table siteevent_statistics_table' width="100%">
                        <tbody>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Videos"); ?> :</td>
                                <td><?php echo $this->totalSitevideo ?></td>
                            </tr> 
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Rated Videos"); ?> :</td>
                                <td><?php echo $this->totalVideorated ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Sponsored Videos"); ?> :</td>
                                <td><?php echo $this->totalsponsored ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Featured Videos"); ?> :</td>
                                <td><?php echo $this->totalfeatured ?></td>
                            </tr> 
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Favourite Videos"); ?> :</td>
                                <td><?php echo $this->totalVideofavourited ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Playlists"); ?> :</td>
                                <td><?php echo $this->totalPlaylist ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Watch Later"); ?> :</td>
                                <td><?php echo $this->totalWatchlater ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Channels"); ?> :</td>
                                <td><?php echo $this->totalChannel ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Subscribed Channels"); ?> :</td>
                                <td><?php echo $this->totalChannelsubscribed ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Rated Channels"); ?> :</td>
                                <td><?php echo $this->totalChannelrated ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Favourite Channels"); ?> :</td>
                                <td><?php echo $this->totalChannelfavourited ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Sponsored Channels"); ?> :</td>
                                <td><?php echo $this->totalChannelsponsored ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Featured Channels"); ?> :</td>
                                <td><?php echo $this->totalChannelfeatured ?></td>
                            </tr> 
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sink-location.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!$this->results->toArray() && !$this->resultss->toArray()): ?>
    <div class="tip global_form_popup">
        <span>
            <?php echo $this->translate("No Channels / Videos need to be synced."); ?>
        </span>
    </div>
<?php else: ?>

    <script type="text/javascript">

        function getLoading() {
            document.getElementById('global_form').style.display = 'none';
            document.getElementById('add_progress_bar').style.display = 'block';
        }

    </script>

    <?php if (empty($this->error)): ?>
        <div id="add_progress_bar" style="display:none;margin:30px 10px 10px;">
            <div class="settings"><form style="width:450px;"><div style="padding:15px;"><div style="font-weight:bold;">Please do not refresh or close this page, until the process is running.<br /><br /><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/progress-bar.gif" alt="Loading.." /></center></div></div></form></div>
        </div>
    <?php endif; ?>

    <?php if (empty($this->error)): ?>
        <div id="global_form">
            <form method="post" class="global_form_popup">
                <div>
                    <h3><?php echo $this->translate('Sync Channels Locations with Checkin'); ?></h3>
                    <p><?php echo $this->translate("Google processes only 2000 requests from an IP address in a day. Thus, you will only be able to sync upto 2000 Channels and Videos Locations in a day. Make sure that you sync rest of the Channels and Videos later by visiting this setting again."); ?></p>
                    <br />

                    <?php //if (!empty($eventCount)) :  ?>
                    <p>
                        <input type="hidden" name="confirm" value=""/>
                        <button type='submit' onClick="getLoading()"><?php echo $this->translate('Sync Channels  / Videos Locations'); ?></button>
                        or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
                    </p>
                    <?php //endif;  ?>
                </div>
            </form>
        </div>
    <?php else: ?>
        <script type="text/javascript">
            window.addEvent('domready', function () {
                parent.window.location.reload();
                parent.Smoothbox.close();
            });
        </script>
    <?php endif; ?>
    <?php if (@$this->closeSmoothbox): ?>
        <script type="text/javascript">
            TB_close();
        </script>
    <?php endif; ?>

<?php endif; ?>
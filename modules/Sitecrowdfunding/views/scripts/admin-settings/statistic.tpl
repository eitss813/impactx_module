<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: statistic.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin'); ?>
</h2>

<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>
<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>
    <div class='clear'>
        <div class='settings'>
            <form class="global_form">
                <div>
                    <h3><?php echo $this->translate('Statistics for Projects'); ?></h3>
                    <p class="description"> <?php echo $this->translate('Below are some valuable statistics for the Projects launched on this site.'); ?>
                    </p>
                    <br />

                    <table class='admin_table sitecrowdfunding_statistics_table' width="100%">
                        <tbody>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Projects"); ?> :</td>
                                <td><?php echo $this->totalProjects ?></td>
                            </tr> 
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Successful Projects"); ?> :</td>
                                <td><?php echo $this->totalSuccessfull ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Failed Projects"); ?> :</td>
                                <td><?php echo $this->totalFailed ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Projects in Draft"); ?> :</td>
                                <td><?php echo $this->totalDrafted ?></td>
                            </tr> 
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Approved Projects"); ?> :</td>
                                <td><?php echo $this->totalApproved ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Disapproved Projects"); ?> :</td>
                                <td><?php echo $this->totalDisapproved ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Funded Amount"); ?> :</td>
                                <td> <?php echo ($this->totalFundedAmount) ? Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->totalFundedAmount) : 0; ?> </td> 
                            </tr> 
                            <tr> 
                                <td width="50%"> <?php echo $this->translate("Total Backed Amount"); ?> :</h3></td>
                                <td> <?php echo ($this->totalBackedAmount) ? Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->totalBackedAmount) : 0; ?> </td> 
                            </tr>  
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

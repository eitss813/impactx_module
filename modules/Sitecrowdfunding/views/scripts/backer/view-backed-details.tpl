<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-backed-details.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?> 
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/main.css')
        ->prependStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>

<div class="global_form_popup" style="width: 550px;">
    <div class="sitecrowdfunding_popup">
        <?php foreach ($this->results as $backer): ?>
            <?php $user = Engine_Api::_()->user()->getUser($backer->user_id); ?> 
            <?php
            if ($backer->reward_id) {
                $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $backer->reward_id);
            }
            ?>
            <div class="backers-report-view mtop10 mbot10" style="border-bottom: 0 !important;">
                <div class="backers-report-view-img">
                    <?php if ($user->photo_id) : ?>
                        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.normal'), array('class' => 'item_photo', 'title' => $this->translate($user->getTitle()), 'target' => '_parent')); ?>
                    <?php else: ?>
                        <a href = '<?php echo $user->getHref(); ?>'><img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/images/owner-defaul-image.jpg'; ?>"></a>
                    <?php endif;
                    ?>
                </div>
                <div class="backers-report-view-details">
                    <div class="backers-report-view-row">
                        <h3 class="backers-report-view-title"><?php echo $this->htmlLink($user->getHref(), $this->translate(" %s ", $this->translate($user->getTitle()))); ?></h3> 
                        <span class="backers-report-view-id">
                            <strong><?php echo $this->translate("Backer ID : "); ?>&nbsp;&nbsp;</strong># <?php echo $backer->backer_id; ?>
                        </span> 
                    </div>
                    <div class="backers-report-view-second-row">
                        <strong><?php echo $this->translate("Backing Date : "); ?>&nbsp;&nbsp;</strong>
                        <?php echo $this->translate($this->locale()->toDateTime($backer->creation_date)) ?>
                        <?php $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount); ?>
                    </div>
                    <div class="backers-report-view-pledged">
                        <strong><?php echo $this->translate("Backed Amount : "); ?>&nbsp;&nbsp;</strong>
                        <?php echo $fundedAmount; ?>
                        <?php if ($backer->reward_id && ($reward->shipping_method == 2 || $reward->shipping_method == 3)): ?>
                            <?php echo $this->translate("(Shipping Cost Included)"); ?>
                        <?php endif; ?>         
                    </div>  
                    <div class="backers-report-view-third-row">
                        <?php if (!empty($reward)): ?>
                            <div class="mtop10">
                                <strong><?php echo $this->translate("Reward Selected : "); ?>&nbsp;&nbsp;</strong>
                                <?php echo $this->translate($reward->getTitle()) ?>
                                <div class=""><?php echo $this->translate($reward->getDescription()); ?></div>
                            </div>
                            <div class="mtop10">
                                <?php if (!empty($backer->shipping_address1) || !empty($backer->shipping_address2) || !empty($backer->shipping_city) || !empty($backer->shipping_zip)): ?>
                                    <strong><?php echo $this->translate("Shipping Location :"); ?>&nbsp;&nbsp;</strong>
                                    <?php echo ($backer->shipping_address1) ? $this->translate($backer->shipping_address1) : ''; ?>
                                    <?php echo ($backer->shipping_address2) ? $this->translate($backer->shipping_address2) : ''; ?>
                                    <?php echo ($backer->shipping_city) ? $this->translate($backer->shipping_city) : ''; ?>
                                    <?php echo ($backer->shipping_zip) ? $this->translate($backer->shipping_zip) : ''; ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($backer->shipping_country): ?>
                                <div class="mtop10">
                                    <strong><?php echo $this->translate("Shipping Country :"); ?>&nbsp;&nbsp;</strong>
                                    <?php $region = Engine_Api::_()->getItem('sitecrowdfunding_region', $backer->shipping_country); ?>
                                    <?php echo $this->translate($region->country_name); ?>
                                </div>
                            <?php endif; ?>
                            <div class="mtop10">
                                <?php if (isset($reward->reward_status) && $reward->reward_status): ?>
                                    <?php echo $this->translate("Reward Sent: "); ?> 
                                <?php else: ?>
                                    <strong><?php echo $this->translate("Estimated Delivery : "); ?>&nbsp;&nbsp;</strong>
                                    <?php echo date('F Y', strtotime($reward->delivery_date)); ?>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php echo $this->translate("No Reward Selected"); ?>
                        <?php endif; ?>
                    </div>
                </div> 
                <div class="txt_right"><a href="<?php echo $this->url(Array('module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'print-invoice', 'backer_id' => Engine_Api::_()->sitecrowdfunding()->getDecodeToEncode($backer->backer_id)), 'default') ?>" target="_blank" class="seaocore_icon_print"><?php echo $this->translate("print invoice") ?></a></div>  
            </div> 
        <?php endforeach; ?> 
        <?php if (@$this->closeSmoothbox): ?>
            <script type="text/javascript">
                TB_close();
            </script>
        <?php endif; ?> 
        <a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a>

    </div>
</div>
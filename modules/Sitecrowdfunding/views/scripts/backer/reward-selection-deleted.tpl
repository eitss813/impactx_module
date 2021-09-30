<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: reward-selection.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js');
?>


<?php if(empty($this->donationType)):?>
    <?php echo $this->htmlLink($this->project->getHref(), '<i class = "seaocore_back_icon"></i>Return to Project', array('title' => $this->translate('Back'))) ?>
<?php endif; ?>
<div class="sitecrowdfunding_title_div_wrapper txt_center">
    <h3>
        <?php echo $this->translate($this->project->getTitle()); ?>
    </h3> 
</div>
<br />
<?php if(count($this->rewards)): ?>
    <div class="reward_choose">
        <?php echo $this->translate("Let's choose your reward "); ?>
    </div>
<?php endif; ?>

<?php $rewardDetails = array(); ?>

<div class="sitecrowdfunding_thanks">
    <input style="display: none" type="radio" name="reward" id="reward_0" value="0" onclick="reward.selectReward(this.value, 0, '')" <?= ($this->reward_id === 0) ? 'checked' : (count($this->rewards) ? '' : 'checked'); ?> />
    <?php if(!$this->donationType):?>
        <label for="reward_0"><?= $this->translate('Back this project without a reward.') ?></label>
    <?php else: ?>
        <label for="reward_0"><?= $this->translate('Enter your contribution amount.') ?></label>
    <?php endif; ?>
    
</div>
<div id="reward_other_detail_0" class="sitecrowdfunding_reward_form"></div>
<?php foreach ($this->rewards as $reward) : ?>
    <?php $id = $reward->reward_id; ?>
    <div class="reward_info_<?= $id ?> sitecrowdfunding_reward_info" >
        <?php
        $countries = array();
        foreach ($reward->getAllCountries() as $country):
            $name = empty($country->country) ? $this->translate('Rest of the World') : $country->country_name;
            $regionId = empty($country->region_id) ? 0 : $country->region_id;
            $countries[$regionId] = array('name' => $name, 'shipping_amount' => $country->amount);
        endforeach;
        $disable = '';
        $rewardDetails["reward_$id"] = array('id' => $id, 'pledgeAmount' => $reward->pledge_amount, 'countries' => $countries);
        $remainigRewardQuantity = 1;
        if ($reward->quantity > 0) {
            $spendRewardQuanity = $reward->spendRewardQuantity();
            $remainigRewardQuantity = $reward->quantity - $spendRewardQuanity;
            if ($remainigRewardQuantity <= 0) {
                $disable = 'disabled="disabled"';
            }
        }
        ?>
        <?php $pledgeAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($reward->pledge_amount); ?>
        <div class="others_rewards">
            <input type="radio" <?= $disable ?> name="reward" id="reward_<?= $id ?>" value="<?= $id ?>" onclick="reward.selectReward(this.value, 0, '')" <?= ($this->reward_id == $id) ? 'checked' : ''; ?>/>
            <label for="reward_<?= $id ?>" class="reward_amount">
                <?php echo $this->translate("%s or more", $pledgeAmount); ?>
            </label>
        </div>

        <div class="sitecrowdfunding_reward_info_box">
            <div class="sitecrowdfunding_reward_info_desc">
                <?php if ($reward->photo_id): ?>
                <div class="sitecrowdfunding_reward_info_img">
                  <?php $src = Engine_Api::_()->storage()->get($reward->photo_id, '')->getPhotoUrl(); ?>
                  <img src="<?php echo $src; ?>" title = '<?php echo $reward->title; ?>'>
                </div>
                <?php endif; ?>
                <div class="sitecrowdfunding_reward_info_title mbot10"><?= $this->translate($reward->getTitle()) ?></div>
                <?= $this->translate($reward->description) ?>
            </div>
            <div class="sitecrowdfunding_reward_info_others">
                <div class="mbot10">
                    <?php if ($reward->quantity > 0 && $remainigRewardQuantity <= 0) : ?>
                        <?= $this->translate("All gone !") ?>
                    <?php elseif ($reward->quantity > 0 && $remainigRewardQuantity > 0) : ?>
                        <span><?= $this->translate("Quantity : ") ?></span>
                        <?= $this->translate(" %s of %s remaining", $remainigRewardQuantity, $reward->quantity) ?>
                    <?php else : ?>
                        <span><?= $this->translate("Quantity : ") ?></span>
                        <?= $this->translate("Unlimited") ?>
                    <?php endif ?>
                </div>
                <div class="mbot10">
                    <span><?= $this->translate("Estimated Delivery : "); ?></span>
                    <?= date('F Y', strtotime($reward->delivery_date)); ?>
                </div>
            </div>
        </div>

        <div id="reward_other_detail_<?= $id ?>" style="display:none;" class="sitecrowdfunding_reward_form"></div>
    </div>
<?php endforeach; ?>
<div id="reward_info" style="display:none;" class="sitecrowdfunding_reward_form">
    <div id="div_message" class="seaocore_txt_red"></div>
    <div id="div_pledge_amount">
        <label><?= $this->translate('Amount in USD($)') ?></label><br>
        <input type="text" id="pledge_amount" value="" name=""><br>
        <label><?= $this->translate('Post Message') ?></label><br>
        <textarea rows="4" cols="50" id="pledge_message" >Hi Everyone, Join me on donating to this project!</textarea>
    </div>
    <div id="countries_list">
        <label><?= $this->translate('Country') ?></label><br>
        <select id="countries_option" onchange="reward.selectLocation()">
        </select>
    </div>
    <div id="div_continue_button" >
        <button onclick="reward.continue()">Continue</button>
        <div id="loading_image_5" class="fright loading_img" style="display: inline-block;"></div>
    </div>
</div>
<script>
    function RewardSelection(rewardDetails) {
        this.rewardDetails = rewardDetails;
        this.selectReward = function (rewardId, pledgeAmount, message) {
            //Set elements into Reward Info Div
            $('div_message').inject($('reward_info'));
            $('div_continue_button').inject($('reward_info'));
            $('div_pledge_amount').inject($('reward_info'));
            $('countries_list').inject($('reward_info'));

            //Pleace the element inside the Selected Reward  
            $('div_message').inject($('reward_other_detail_' + rewardId));
            $('div_pledge_amount').inject($('reward_other_detail_' + rewardId));
            //Reset the default values of country select box and set the Back amount blank
            $('countries_option').options.length = 0;
            $('pledge_amount').set('value', '');
            $('div_message').innerHTML = message;
            if (pledgeAmount && pledgeAmount > 0) {
                $('pledge_amount').set('value', pledgeAmount);
            }
            if (rewardId != 0) {
                Object.each(this.rewardDetails['reward_' + rewardId].countries, function (value, index) {
                    new Element('option', {'value': index, 'text': value.name + '(+' + value.shipping_amount + ')'}).inject($('countries_option'));
                });
                if ($('countries_option').options.length > 0) {
                    $('countries_list').inject($('reward_other_detail_' + rewardId));
                }
                if (pledgeAmount && pledgeAmount > 0) {
                    $('pledge_amount').set('value', pledgeAmount);
                } else if (Number(this.rewardDetails['reward_' + rewardId].pledgeAmount) > 0) {
                    shippingAmount = 0;
                    if ($('countries_option').getSelected().length > 0) {
                        country = $('countries_option').getSelected()[0].value;
                        shippingAmount = Number(this.rewardDetails['reward_' + rewardId].countries[country].shipping_amount);
                    }
                    $('pledge_amount').set('value', Number(this.rewardDetails['reward_' + rewardId].pledgeAmount) + shippingAmount);
                }
            }
            $('reward_other_detail_' + rewardId).show();
            $('div_continue_button').inject($('reward_other_detail_' + rewardId));
        }
        this.selectLocation = function () {

            selectedReward = $$('input[type=radio][name=reward]:checked');
            if (selectedReward.length != 1) {
                return false;
            }
            selectedReward = selectedReward[0];
            rewardId = selectedReward.value;
            shippingAmount = 0;
            if ($('countries_option').getSelected().length > 0) {
                country = $('countries_option').getSelected()[0].value;
                shippingAmount = Number(this.rewardDetails['reward_' + rewardId].countries[country].shipping_amount);
            }
            $('pledge_amount').set('value', Number(this.rewardDetails['reward_' + rewardId].pledgeAmount) + shippingAmount);
        }
        this.continue = function () {
            $('div_message').innerHTML = '';
            selectedReward = $$('input[type=radio][name=reward]:checked');
            if (selectedReward.length != 1) {
                return false;
            }
            selectedReward = selectedReward[0];
            rewardId = selectedReward.value;
            amount = 0;
            country = "";
            shippingAmt = 0;
            if (rewardId != 0) {
                if ($('countries_option').getSelected().length > 0) {
                    country = $('countries_option').getSelected()[0].value;
                }
                rewardMinAmount = Number(reward.rewardDetails['reward_' + rewardId].pledgeAmount)
                //Fetching the shipping amount
                if (country != '') {
                    shippingAmt = Number(this.rewardDetails['reward_' + rewardId].countries[country].shipping_amount);
                    rewardMinAmount += shippingAmt;
                }
                // If user enter back amount less than (reward back amount+shipping amount) then it will 
                // show the error message.
                if (isNaN(Number($('pledge_amount').value))) {
                    $('div_message').innerHTML = '<?= $this->translate('Please enter a valid Back amount.'); ?>';
                    $('div_message').inject($('div_pledge_amount'), 'before');
                    return false;
                }
                if (Number($('pledge_amount').value) < rewardMinAmount) {
                    $('div_message').innerHTML = '<?= $this->translate('Please enter the Back amount greater than or equal to Reward’s Back amount i.e. '); ?> ' + rewardMinAmount;
                    $('div_message').inject($('div_pledge_amount'), 'before');
                    return false;
                }
            }
            amount = Number($('pledge_amount').value);
            message = $('pledge_message').value;
            if (isNaN(amount) || amount <= 0) {
                $('div_message').innerHTML = '<?= $this->translate('Please enter the valid back amount.'); ?>';
                $('div_message').inject($('div_pledge_amount'), 'before');
                return false;
            }
            rs_url = '<?php echo $this->url(array('action' => 'check-reward-selection', 'project_id' => $this->project_id), 'sitecrowdfunding_backer', true) ?>';
            payment_url = '<?php echo $this->url(array('action' => 'checkout', 'project_id' => $this->project_id), 'sitecrowdfunding_backer', true) ?>';
            new Request.JSON({
                'format': 'json',
                'method': 'post',
                'url': rs_url,
                data: {
                    reward_id: rewardId,
                    pledge_amount: amount,
                    shipping_amt: shippingAmt,
                    country: country,
                    message: message
                },
                onRequest: function () {
                    $('loading_image_5').innerHTML = '<img src=' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif height=15 width=15>';
                },
                onSuccess: function (responseJSON) {
                    $('loading_image_5').innerHTML = '';
                    if (responseJSON.return == 1) {
                        window.location = payment_url;
                    } else {
                        reward.selectReward(rewardId, pledge_amount, responseJSON.message);
                    }
                }
            }).send();

        }
    }
    var reward = new RewardSelection(<?php echo json_encode($rewardDetails); ?>);

<?php if (count($this->rewards) == 0): ?>
            reward.selectReward(0, 0, ''); 
<?php endif; ?>
<?php if (!is_null($this->reward_id)): ?> 
            reward.selectReward(<?= $this->reward_id; ?>,<?= $this->pledge_amount; ?>, '<?= $this->message; ?>') 
<?php endif; ?>
<?php if (!empty($this->country_selected)): ?>
        $('countries_option').getElement('option[value=<?php echo $this->country_selected; ?>]').selected = true
        reward.selectLocation();
<?php endif; ?>
</script>
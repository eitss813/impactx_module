<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-leaders.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js'); ?>

<div class="sitecrowdfunding_dashboard_content">

    <div class="layout_middle">

        <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl',
        array('project' => $this->project, 'sectionTitle' => 'Funding', 'sectionDescription' => "")); ?>

        <div class="sitecrowdfunding_dashboard_content">

            <div class="fright button_grp">

                <a style="font-weight: unset !important;" class="button smoothbox"
                   href="<?php echo $this->escape($this->url(array('controller'=>'funding','action' => 'edit-funding', 'project_id' => $this->project_id , 'layoutType' => 'fundingDetails' ), 'sitecrowdfunding_extended', true)); ?>">
                    <span><?php echo $this->translate("Edit Funding"); ?></span>
                </a>

                <a style="font-weight: unset !important;" class="button"
                   href="<?php echo $this->escape($this->url(array('action' => 'payment-info', 'project_id' => $this->project_id  ), 'sitecrowdfunding_specific', true)); ?>">
                    <span><?php echo $this->translate("Manage Payment Methods"); ?></span>
                </a>

                <a style="font-weight: unset !important;" class="button"
                   href="<?php echo $this->escape($this->url(array('controller'=>'dashboard', 'action' => 'project-transactions', 'project_id' => $this->project_id  ), 'sitecrowdfunding_extended', true)); ?>">
                    <span><?php echo $this->translate("Transactions"); ?></span>
                </a>

                <?php /*
                <?php if ($project->isOpen()) : ?>
                    <a style="font-weight: unset !important;" class="button smoothbox"
                       href="<?php echo $this->escape($this->url(array('controller'=>'reward', 'action' => 'create', 'project_id' => $this->project_id , 'layoutType' => 'fundingDetails'  ), 'sitecrowdfunding_extended', true)); ?>">
                        <span><?php echo $this->translate("Add Reward"); ?></span>
                    </a>
                <?php endif; ?>
                */ ?>

                <a style="font-weight: unset !important;" class="button smoothbox"
                   href="<?php echo $this->escape($this->url(array('controller'=> 'funding' , 'action'=>'add-external-funding', 'project_id' => $this->project_id , 'layoutType' => 'fundingDetails'  ), 'sitecrowdfunding_extended', true)); ?>">
                    <span><?php echo $this->translate("Add External funding"); ?></span>
                </a>

            </div>

            <br/><br/>

            <!-- Manage Rewards -->
            <?php /*
            <div>
                <div class="sitecrowdfunding_manage_rewards">

                    <h3 class="form_title"> <?php echo $this->translate('Rewards'); ?> </h3>

                    <?php if (count($this->rewards) > 0) : ?>
                        <?php foreach ($this->rewards as $item): ?>
                            <div id='<?php echo $item->reward_id ?>_project_main'  class='sitecrowdfunding_manage_rewards_list'>
                                <div id='<?php echo $item->reward_id ?>_project' class="sitecrowdfunding_manage_rewards_list_details">
                                    <div class="sitecrowdfunding_manage_rewards_option">

                                        <?php $url = $this->url(array('controller' => 'reward', 'action' => 'delete'), 'sitecrowdfunding_extended', true); ?>
                                        <a href='<?php echo $this->url(array('controller' => 'reward', 'action' => 'edit', 'reward_id' => $item->reward_id, 'project_id' => $this->project_id,'layoutType' => 'fundingDetails'), 'sitecrowdfunding_extended', true) ?>' class="buttonlink smoothbox icon seaocore_icon_edit_sqaure"><?php echo $this->translate("Edit"); ?></a>

                                        <?php if ($item->spendRewardQuantity() <= 0) :
                                        echo $this->htmlLink(array('route' => 'sitecrowdfunding_extended', 'module' => 'sitecrowdfunding', 'controller' => 'reward', 'action' => 'delete', 'reward_id' => $item->reward_id, 'project_id' => $this->project_id), $this->translate('Delete'), array('class' => 'smoothbox seaocore_txt_red seaocore_icon_remove_square'));
                                        else :
                                        ?>
                                        <a href="javascript:void(0);" class="seaocore_txt_red seaocore_icon_remove_square" onclick='rewardPrompt()'><?php echo $this->translate('Delete'); ?></a>
                                        <?php endif; ?>
                                    </div>

                                    <?php $pledgeAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->pledge_amount); ?>

                                    <span class="reward_amount"><?php echo $this->translate("%s or more", $pledgeAmount); ?></span>

                                    <div class="sitecrowdfunding_manage_rewards_title">
                                        <?php echo $this->translate($item->title); ?>
                                    </div>

                                    <?php if ($item->photo_id): ?>
                                        <div class="sitecrowdfunding_reward_img">
                                            <?php $src = Engine_Api::_()->storage()->get($item->photo_id, '')->getPhotoUrl(); ?>
                                            <img src="<?php echo $src; ?>" title = '<?php echo $item->title; ?>'>
                                        </div>
                                    <?php endif; ?>

                                    <div class="sitecrowdfunding_manage_rewards_pledged">
                                        <?php $amount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->pledge_amount) ?>
                                        <?php echo $this->translate("Backed Amount"); ?> :
                                        <?php echo $this->translate("%s", $amount); ?>
                                    </div>

                                    <div class="sitecrowdfunding_manage_rewards_body show_content_body">
                                        <?php echo $this->translate($item->description); ?>
                                    </div>

                                    <div class="sitecrowdfunding_manage_rewards_quantity">

                                        <?php if ($item->quantity): ?>
                                            <span class="mtop10">
                                                <?php $quantity = $item->quantity; ?>
                                                <?php $remainingRewards = $quantity - $item->spendRewardQuantity(); ?>
                                                <strong><?php echo $this->translate("Limited Rewards"); ?> : </strong><?php echo $this->translate("$remainingRewards left out of $quantity"); ?>
                                            </span>
                                        <?php endif; ?>

                                        <span class="mtop10">
                                            <strong><?php echo $this->translate("Estimated Delivery"); ?> : </strong>
                                            <?php echo date('F Y', strtotime($item->delivery_date)); ?>
                                        </span>

                                        <div class="mtop10">
                                            <?php if ($item->shipping_method == 1): ?>
                                            <?php echo $this->translate("No shipping Required"); ?>
                                            <?php else: ?>
                                            <?php echo $this->htmlLink(array('controller' => 'reward', 'action' => 'view-shipping-locations', 'reward_id' => $item->getIdentity()), $this->translate('View Shipping Details'), array('class' => 'smoothbox')); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="tip">
                            <span><?php echo $this->translate('No rewards have been created for this project yet.'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php $item = count($this->paginator) ?>
                <input type="hidden" id='count_div' value='<?php echo $item ?>' />
            </div>
            <br/><br/>
            */ ?>

            <!-- Backers Report -->
            <div>

                <h3 class="form_title"> <?php echo $this->translate('Backers Report'); ?> </h3>

                <div class="backers_report_containers">
                    <div class="compose-exports">
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'export-backers','project_id' => $this->project->project_id), $this->translate('Export'), array('class' => 'seaocore_icon_exports', 'title' => 'Export')) ?>
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'compose', 'project_id' => $this->project->project_id, 'format' => 'smoothbox'), $this->translate('Compose'), array('class' => 'smoothbox icon seaocore_icon_add', 'title' => 'Compose')) ?>
                    </div>
                    <div class="search-backers">
                        <div class="global_form">
                            <form method="post" class="field_search_criteria" id = 'search_form'>
                                <div class="backers-compose">
                                    <select id="searchByRewards" name="searchByRewards" onchange="searchBackers();">
                                        <option value="0"><?php echo $this->translate("All Backers") ?></option>
                                        <option value="1"><?php echo $this->translate("All Rewards") ?></option>
                                        <option value="2"><?php echo $this->translate("Pending Rewards") ?></option>
                                        <option value="3"><?php echo $this->translate("No Reward Selected") ?></option>
                                    </select>
                                </div>
                                <div class="backers-compose">
                                    <select id="payment_status" name="payment_status">
                                        <?php foreach($this->payment_status as $key => $val):?>
                                            <option value="<?php echo $key;?>"><?php echo $this->translate($val) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="backers-search">
                                    <input type="text" name="username" id="username" value="<?php echo $this->searchUser; ?>" placeholder="<?php echo $this->translate("Start typing user name ..") ?>"/>
                                    <input type="hidden" id="user_id" name="user_id" />
                                    <button type="submit" name = "search" onclick="submitSearchForm()" ><?php echo $this->translate("Search") ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <br/>

                <div id="backer_report_container">
                    <?php if ($this->total_item): ?>
                        <div class="mbot5">
                            <strong>
                                <?php echo $this->translate(array('%s backer found.', '%s backers found.', $this->total_item), $this->locale()->toNumber($this->total_item)) ?>
                            </strong>
                        </div>
                        <div id="manage_order_tab" class="sitecrowdfunding_detail_table mtop5">
                            <table>
                                <tr class="sitecrowdfunding_detail_table_head">
                                    <th class="txt_center"><?php echo $this->translate('#') ?></th>
                                    <th><?php echo $this->translate('Backer') ?></th>
                                    <th><?php echo $this->translate('Amount') ?></th>
                                    <th><?php echo $this->translate('Payment Status') ?></th>
                                    <th><?php echo $this->translate('Date') ?></th>
                                    <th><?php echo $this->translate('Reward Sent') ?></th>
                                    <th><?php echo $this->translate('Options') ?></th>
                                </tr>
                                <?php foreach ($this->paginator as $item): ?>
                                    <tr>
                                        <td><?= $item->backer_id; ?></td>
                                        <?php $user = Engine_Api::_()->getItem('user', $item->user_id); ?>
                                        <td><?= $this->htmlLink($user->getHref(),$user->getTitle()); ?></td>
                                        <td><?= Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->amount); ?></td>
                                        <td><?php echo $this->translate($item->paymentStatus()); ?></td>
                                        <td><?= $this->localeDate($item->creation_date); ?></td>
                                        <td>

                                            <?php if($item->reward_id && $item->reward_status ==1) :?>
                                            <input type="checkbox" name="reward_send_checkbox" onclick="return false;" checked>
                                            <?php elseif($item->reward_id && $item->reward_status ==0): ?>
                                            <input type="checkbox" name="reward_send_checkbox" onclick="sendReward(<?php echo $item->backer_id; ?>)">
                                            <?php else: ?>
                                            <?php echo $this->translate('No Reward'); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo $this->htmlLink(array('module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'view', 'backer_id' => $item->backer_id , 'project_id' => $item->project_id), $this->translate('View'), array('class' => 'smoothbox'));
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>

                        <div class="clr dblock sitecrowdfunding_data_paging">
                            <div id="project_manage_backer_previous" class="paginator_previous sitecrowdfunding_data_paging_link">
                                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                                'onclick' => '',
                                'class' => 'buttonlink icon_previous'
                                ));
                                ?>
                                <span id="manage_spinner_prev"></span>
                            </div>

                            <div id="project_manage_backer_next" class="paginator_next sitecrowdfunding_data_paging_link">
                                <span id="manage_spinner_next"></span>
                                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                                'onclick' => '',
                                'class' => 'buttonlink_right icon_next'
                                ));
                                ?>
                            </div>
                        </div>

                    <?php else : ?>

                        <div class="tip">
                            <span>
                                <?php echo $this->translate($this->message); ?>
                            </span>
                        </div>

                    <?php endif; ?>

                </div>
                <div id="hidden_ajax_data" style="display: none;"></div>

            </div>
            <br/><br/>


            <!-- External Funding -->
            <div>
                <h3 class="form_title"> <?php echo $this->translate('External funding'); ?> </h3>
                <div class="external_funding_container">
                    <?php if(count($this->externalfunding) > 0): ?>
                        <?php foreach($this->externalfunding as $org): ?>
                            <div class="org_container">
                            <div class="org_left">
                                <div class="org_logo">
                                    <img style="width: 80px;height: 80px" src="<?php echo !empty($org['logo']) ? $org['logo'] : $defaultLogo ?>"/>
                                    <p><?php echo $org['type']; ?></p>
                                </div>
                                <div class="org_title_desc">

                                    <h3 class="organization-header">
                                        <?php if(!empty($org['link'])): ?>
                                            <?php echo $this->htmlLink($org['link'], $org['title']. ' - '.Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency( $org['amount'] ), array('target' => '_blank')) ?>
                                        <?php else: ?>
                                            <?php echo $org['title']. ' - '.Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency( $org['amount'] ) ?>
                                        <?php endif; ?>
                                    </h3>

                                    <div>
                                        <h4>
                                            <?php echo "Funded on: ".date('Y-m-d',strtotime($org['funding_date'])) ?>
                                        </h4>
                                    </div>

                                    <div>
                                        <?php echo $org['notes']; ?>
                                    </div>

                                </div>
                            </div>
                            <div class="org_options">
                                <?php echo $this->htmlLink(
                                    array(
                                    'route' => 'sitecrowdfunding_extended',
                                    'controller' => 'funding',
                                    'action' => 'delete-external-funding',
                                    'externalfunding_id' => $org['externalfunding_id'],
                                    ),
                                    $this->translate('Delete'), array(
                                    'class' => 'buttonlink smoothbox seaocore_icon_remove',
                                    'style' => 'float: right; color: #FF0000; padding-top: 10px;padding-left: 10px;'
                                )) ?>

                                <?php echo $this->htmlLink(
                                    array(
                                    'route' => 'sitecrowdfunding_externalfunding_edit',
                                    'controller' => 'funding',
                                    'action' => 'edit-external-funding',
                                    'externalfunding_id' => $org['externalfunding_id'],
                                    'project_id' => $this->project->project_id,
                                    'layoutType' => 'fundingDetails'
                                    ),
                                    $this->translate('Edit'), array(
                                    'class' => 'buttonlink smoothbox seaocore_icon_edit',
                                    'style' => 'float: right; color: #FF0000; padding-top: 10px;'
                                )) ?>

                            </div>
                        </div>
                        <?php endforeach;?>
                    <?php else: ?>
                        <div class="tip">
                            <span>
                                <?php echo $this->translate('No External funding for this project'); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


        </div>
    </div>
</div>

<style>
    .sitecrowdfunding_dashboard_content{
        -webkit-box-shadow: unset !important;
    }
    .form_title{
        padding-bottom: 10px;
        border-bottom: 1px solid #f2f0f0;
        margin-top: 10px;
        font-size: 19px;
    }
    .button_grp > a {
        margin: 0 10px;
    }
    .backers-compose{
        margin: 0 10px !important;
    }
    .backers-search button{
        margin-right: -5px !important;
    }

    /*edit funding form*/
    .add-funding-btn{
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .sitecrowdfunding_project_form{
        padding: 10px;
        border: 1px solid #eee;
    }
    .org_container{
        display: flex;
        border: 1px solid #f2f0f0;
        margin-top: 20px;
        margin-bottom: 20px;
        padding: 10px;
        justify-content: space-between;
    }
    .org_left{
        display: flex;
    }
    .org_logo{
        padding-right: 15px;
        text-align: center;
        width: auto;
    }
    .org_options{
        min-width: 80px;
    }
    .organization-div{
        padding-top: 20px
    }
    .add-org-btn{
        margin-top: 10px;
    }
    .organization-header{
        text-decoration: underline;
        font-weight: bold;
    }
</style>

<script type="text/javascript">
    en4.core.runonce.add(function () {
    <?php if ($this->total_item): ?>
        document.getElementById('project_manage_backer_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        $('project_manage_backer_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
        $('project_manage_backer_previous').removeEvents('click').addEvent('click', function () {
            $('manage_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';

            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitecrowdfunding/project-funding/details/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
                data: {
                    format: 'html',
                        user_id: $('user_id').value,
                        username: $('username').value,
                        searchByRewards: $('searchByRewards').getElement('option:checked').value,
                        payment_status: $('payment_status').getElement('option:checked').value,
                        search: 1,
                        subject: en4.core.subject.guid,
                        project_id: <?php echo sprintf('%d', $this->project_id) ?>,
                    page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('manage_spinner_prev').innerHTML = '';
                    $('hidden_ajax_data').innerHTML = responseHTML;
                    $('backer_report_container').innerHTML = $('hidden_ajax_data').getElement('#backer_report_container').innerHTML;
                }
        }), {
            })
        });
        $('project_manage_backer_next').removeEvents('click').addEvent('click', function () {
            $('manage_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';

            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitecrowdfunding/project-funding/details/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
            data: {
                format: 'html',
                    user_id: $('user_id').value,
                    username: $('username').value,
                    searchByRewards: $('searchByRewards').getElement('option:checked').value,
                    payment_status: $('payment_status').getElement('option:checked').value,
                    search: 1,
                    subject: en4.core.subject.guid,
                    project_id: <?php echo sprintf('%d', $this->project_id) ?>,
                page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('manage_spinner_next').innerHTML = '';
                $('hidden_ajax_data').innerHTML = responseHTML;
                $('backer_report_container').innerHTML = $('hidden_ajax_data').getElement('#backer_report_container').innerHTML;
            }
        }), {
            })
        });
    <?php endif; ?>
    });

    var searchBackers = function () {

        if (Browser.Engine.trident) {
            document.getElementById('search_form').submit();
        } else {
            $('search_form').submit();
        }
    };
    <?php $url = $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'send-reward')); ?>

    function submitSearchForm() {
        $('search_form').submit();
    }

    function sendReward(id) {
        Smoothbox.open("<?php echo $url; ?>/backer_id/"+id);
    }

    window.addEvent('domready', function () {
        $('searchByRewards').options["<?php echo $this->searchOption ?>"].selected = true;
        $('payment_status').value = '<?php echo $this->selectedStatus; ?>';
    });

</script>

<div>

    <h3 class="title">
        <?php echo $this->translate(array('%s Total Funders', '%s Total Funders', $this->total_backer_count),$this->locale()->toNumber($this->total_backer_count)); ?>
    </h3>
    <br/>
    <div id="backer_report_container">
        <?php if ($this->total_item): ?>
            <div class="mbot5">
                <h3 class="form_title">
                    <?php echo $this->translate(array('%s Transaction(s)', '%s Transaction(s)', $this->total_item),$this->locale()->toNumber($this->total_item)); ?>
                </h3>
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
                    <?php $user = Engine_Api::_()->getItem('user', $item->user_id); ?>
                    <tr>
                        <td><?php echo $item->backer_id; ?></td>
                        <td><?php echo $this->htmlLink($user->getHref(),$user->getTitle()); ?></td>
                        <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->amount); ?></td>
                        <td><?php echo $this->translate($item->paymentStatus()); ?></td>
                        <td><?php echo $this->localeDate($item->creation_date); ?></td>
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
                            <?php echo $this->htmlLink(array('module' => 'sitecrowdfunding', 'controller' => 'backer',
                            'action' => 'view', 'backer_id' => $item->backer_id , 'project_id' => $item->project_id),
                            $this->translate('View'), array('class' => 'smoothbox'));
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="sitecrowdfunding_data_paging">
                <div id="project_manage_backer_previous" class="sitecrowdfunding_data_paging_link">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                    'onclick' => '',
                    'class' => 'button buttonlink icon_previous'
                    ));
                    ?>
                    <span id="manage_spinner_prev"></span>
                </div>

                <div id="project_manage_backer_next" class="sitecrowdfunding_data_paging_link">
                    <span id="manage_spinner_next"></span>
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                    'onclick' => '',
                    'class' => 'button buttonlink_right icon_next'
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

    <!-- External Funding -->
    <?php if(count($this->externalfunding) > 0): ?>
        <div>
            <h3 class="form_title"> <?php echo $this->translate('External Funder(s)'); ?> </h3>
            <div class="external_funding_container">
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
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    <?php endif; ?>

    <div id="hidden_ajax_transaction_data" style="display: none;"></div>

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

    .simple-modal-body > .contents{
        overflow-y: auto !important;
        height: 400px;
    }

    .sitecrowdfunding_data_paging > #project_manage_backer_previous,
    .sitecrowdfunding_data_paging > #project_manage_backer_next{
        text-align: center;
    }

    .title{
        font-size: 19px;
        margin-top: 10px;
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
                url: en4.core.baseUrl + 'organizations/transactions/project-transactions-details/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
            data: {
                format: 'html',
                    search: 1,
                    subject: en4.core.subject.guid,
                    project_id: <?php echo sprintf('%d', $this->project_id) ?>,
                page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                console.log('prev responseHTML',responseHTML);
                $('manage_spinner_prev').innerHTML = '';
                $('hidden_ajax_transaction_data').innerHTML = responseHTML;
                $('backer_report_container').innerHTML = $('hidden_ajax_transaction_data').getElement('#backer_report_container').innerHTML;
            }
        }))
        });

        $('project_manage_backer_next').removeEvents('click').addEvent('click', function () {
            $('manage_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'organizations/transactions/project-transactions-details/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
            data: {
                format: 'html',
                    search: 1,
                    subject: en4.core.subject.guid,
                    project_id: <?php echo sprintf('%d', $this->project_id) ?>,
                page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                console.log('next responseHTML',responseHTML);
                $('manage_spinner_next').innerHTML = '';
                $('hidden_ajax_transaction_data').innerHTML = responseHTML;
                $('backer_report_container').innerHTML = $('hidden_ajax_transaction_data').getElement('#backer_report_container').innerHTML;
            }
        }))
        });

    <?php endif; ?>
    });
</script>
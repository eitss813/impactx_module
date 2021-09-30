<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _backerRelatedTransaction.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?> 
    <div id="show_tab_content_child"></div>  
    <div class="clr" id="transaction_main_container">
        
        <div class="seaocore_settings_form">
            <div class="settings">
                <?php echo $this->searchForm->render($this) ?>
                <span id="search_spinner"></span>
            </div>
        </div>

        <div class="global_form">	
            <?php $countPagination = count($this->paginator); ?>
            <?php if ($countPagination): ?>
                <div class="mbot10">
                    <strong><?php echo $this->translate('%s transaction detail(s) found.', $this->total_item) ?></strong>
                </div>
            <?php endif; ?>
            <div id="payment_request_table"> 
                <div id="manage_order_tab"> 
                    <?php if ($countPagination): ?> 
                        <div class="sitecrowdfunding_detail_table">
                            <table>
                                <tr class="sitecrowdfunding_detail_table_head">
                                    <th><?php echo $this->translate("Transaction Id") ?></th> 
                                    <th><?php echo $this->translate("Backer’s Name") ?></th>
                                    <th><?php echo $this->translate("Amount") ?></th> 
                                    <th><?php echo $this->translate("Commission") ?></th>
                                    <th><?php echo $this->translate("Gateway") ?></th> 
                                    <th><?php echo $this->translate("Payment Status") ?></th> 

                                    <th><?php echo $this->translate("Date") ?></th> 
                                    <th><?php echo $this->translate("Options") ?></th>
                                </tr>
                                <?php foreach ($this->paginator as $payment) : ?>  
                                    <tr>
                                        <?php $user = Engine_Api::_()->getItem('user', $payment->user_id); ?>
                                        <?php $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $payment->source_id); ?>

                                        <td><?php echo $payment->transaction_id ?></td> 
                                        <td><?php echo $this->htmlLink($user->getHref(), $this->translate(" %s ", $this->translate($user->getTitle()))); ?></td>
                                        <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($payment->amount) ?></td>
                                        <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($payment->commission_value) ?></td>  
                                        <td><?php echo $payment->gateway_title ?></td>
                                        <td><?php echo $backer->paymentStatus(); ?></td>

                                        <td><?php echo date('M d, Y', strtotime($payment->timestamp)); ?></td>
                                        <td class="txt_center">  
                                            <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'dashboard', 'action' => 'detail', 'project_id' => $this->project_id, 'transaction_id' => $payment->transaction_id, 'tab' => 'transaction'), 'default', true) ?>')"><?php echo $this->translate("Details") ?></a> 
                                        </td>   
                                    </tr>
                                <?php endforeach; ?>  
                            </table>
                        </div>
                    </div>
                </div>
                <div id="project_payment_request_previous" class="paginator_previous">
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                        'onclick' => '',
                        'class' => 'buttonlink icon_previous'
                    ));
                    ?>
                    <span id="payment_spinner_prev"></span>
                </div>
                <div id="project_payment_request_next" class="paginator_next">
                    <span id="payment_spinner_next"></span>
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                        'onclick' => '',
                        'class' => 'buttonlink_right icon_next'
                    ));
                    ?>
                </div> 
            <?php else: ?>
                <div class="tip">
                    <span>
                        <?php echo $this->translate($this->message); ?>
                    </span>
                </div>
            <?php endif; ?> 
        </div> 
    </div>
 
</div>
</div>
<div id="hidden_ajax_data" style="display: none;"></div>
</div>
</div>
<script type="text/javascript">

    function hideTimeElements() {
        if ($('start_cal-minute'))
            $('start_cal-minute').style.display = 'none';
        if ($('start_cal-ampm'))
            $('start_cal-ampm').style.display = 'none';
        if ($('start_cal-hour'))
            $('start_cal-hour').style.display = 'none';
        if ($('end_cal-minute'))
            $('end_cal-minute').style.display = 'none';
        if ($('end_cal-ampm'))
            $('end_cal-ampm').style.display = 'none';
        if ($('end_cal-hour'))
            $('end_cal-hour').style.display = 'none';
    }
  
    en4.core.runonce.add(function() {
        onSuccessAjax();
    }) 
    function onSuccessAjax() {
        hideTimeElements(); 
        var searchSpinner = new Element('span#search_spinner');
        $('search-element').appendChild(searchSpinner); 
    }

    en4.core.runonce.add(function () {

        var anchor = document.getElementById('manage_order_tab').getParent();
<?php if ($countPagination): ?>
            document.getElementById('project_payment_request_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('project_payment_request_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('project_payment_request_previous').removeEvents('click').addEvent('click', function () {
                $('payment_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';

                var tempPaymentPaginationUrl = '<?php echo $this->url(array('action' => 'project-transactions', 'project_id' => $this->project_id, 'menuId' => 54, 'method' => 'project-transactions', 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitecrowdfunding_dashboard', true); ?>';

                if (tempPaymentPaginationUrl && typeof history.pushState != 'undefined') {
                    history.pushState({}, document.title, tempPaymentPaginationUrl);
                }
                var date = $('calendar_output_span_start_cal-date').get('text');
                var hour = $('start_cal-hour').value;
                var minute = $('start_cal-minute').value;
                var ampm = $('start_cal-ampm').value;
                start_cal = {'date': date, 'hour': hour, 'minute': minute, 'ampm': ampm, };
                var date = $('calendar_output_span_end_cal-date').get('text');
                var hour = $('end_cal-hour').value;
                var minute = $('end_cal-minute').value;
                var ampm = $('end_cal-ampm').value;
                end_cal = {'date': date, 'hour': hour, 'minute': minute, 'ampm': ampm, };

                en4.core.request.send(new Request.HTML({
                    url: en4.core.baseUrl + 'sitecrowdfunding/dashboard/project-transactions/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
                    data: {
                        format: 'html',
                        search: 1,
                        subject: en4.core.subject.guid,
                        backer_name: $('backer_name').value,
                        transaction_min_amount: $('transaction_min_amount').value,
                        transaction_max_amount: $('transaction_max_amount').value,
                        commission_min_amount: $('commission_min_amount').value,
                        commission_max_amount: $('commission_max_amount').value,
                        payment_status: $('payment_status').value,
                        start_cal: start_cal,
                        end_cal: end_cal,
                        user_id: $('user_id').value,
                        page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                    },
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('hidden_ajax_data').innerHTML = responseHTML;
                        $('transaction_main_container').innerHTML = $('hidden_ajax_data').getElement('#transaction_main_container').get('html');
                        $('hidden_ajax_data').innerHTML = '';
                        $('payment_spinner_prev').innerHTML = '';
                        onSuccessAjax();

                    }
                }), {
                })
            });

            $('project_payment_request_next').removeEvents('click').addEvent('click', function () {
                $('payment_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';

                var tempPaymentPaginationUrl = '<?php echo $this->url(array('action' => 'project-transactions', 'project_id' => $this->project_id, 'method' => 'project-transactions', 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitecrowdfunding_dashboard', true); ?>';
                if (tempPaymentPaginationUrl && typeof history.pushState != 'undefined') {
                    history.pushState({}, document.title, tempPaymentPaginationUrl);
                }
                var date = $('calendar_output_span_start_cal-date').get('text');
                var hour = $('start_cal-hour').value;
                var minute = $('start_cal-minute').value;
                var ampm = $('start_cal-ampm').value;
                start_cal = {'date': date, 'hour': hour, 'minute': minute, 'ampm': ampm, };
                var date = $('calendar_output_span_end_cal-date').get('text');
                var hour = $('end_cal-hour').value;
                var minute = $('end_cal-minute').value;
                var ampm = $('end_cal-ampm').value;
                end_cal = {'date': date, 'hour': hour, 'minute': minute, 'ampm': ampm, };

                en4.core.request.send(new Request.HTML({
                    url: en4.core.baseUrl + 'sitecrowdfunding/dashboard/project-transactions/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
                    data: {
                        format: 'html',
                        search: 1,
                        subject: en4.core.subject.guid,
                        backer_name: $('backer_name').value,
                        transaction_min_amount: $('transaction_min_amount').value,
                        transaction_max_amount: $('transaction_max_amount').value,
                        commission_min_amount: $('commission_min_amount').value,
                        commission_max_amount: $('commission_max_amount').value,
                        payment_status: $('payment_status').value,
                        start_cal: start_cal,
                        end_cal: end_cal,
                        user_id: $('user_id').value,
                        page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                    },
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('hidden_ajax_data').innerHTML = responseHTML;
                        $('transaction_main_container').innerHTML = $('hidden_ajax_data').getElement('#transaction_main_container').get('html');
                        $('hidden_ajax_data').innerHTML = '';
                        $('payment_spinner_next').innerHTML = '';
                        onSuccessAjax();
                    }
                }), {
                })
            });

<?php endif; ?>

        $('search').addEvent('click', function (e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';

            var date = $('calendar_output_span_start_cal-date').get('text');
            var hour = $('start_cal-hour').value;
            var minute = $('start_cal-minute').value;
            var ampm = $('start_cal-ampm').value;
            start_cal = {'date': date, 'hour': hour, 'minute': minute, 'ampm': ampm, };
            var date = $('calendar_output_span_end_cal-date').get('text');
            var hour = $('end_cal-hour').value;
            var minute = $('end_cal-minute').value;
            var ampm = $('end_cal-ampm').value;
            end_cal = {'date': date, 'hour': hour, 'minute': minute, 'ampm': ampm, };
            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitecrowdfunding/dashboard/project-transactions/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
                method: 'POST',
                data: {
                    search: 1,
                    subject: en4.core.subject.guid,
                    backer_name: $('backer_name').value,
                    transaction_min_amount: $('transaction_min_amount').value,
                    transaction_max_amount: $('transaction_max_amount').value,
                    commission_min_amount: $('commission_min_amount').value,
                    commission_max_amount: $('commission_max_amount').value,
                    payment_status: $('payment_status').value,
                    start_cal: start_cal,
                    end_cal: end_cal,
                    user_id: $('user_id').value,
                },
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('hidden_ajax_data').innerHTML = responseHTML;
                    $('transaction_main_container').innerHTML = $('hidden_ajax_data').getElement('#transaction_main_container').get('html');
                    $('hidden_ajax_data').innerHTML = '';
                    $('search_spinner').innerHTML = '';
                    onSuccessAjax();

                }
            }), {
            });
        });
    });
</script>  
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
    en4.core.runonce.add(function ()
    {
        var contentAutocomplete = new Autocompleter.Request.JSON('backer_name', '<?php echo $this->url(array('controller' => 'dashboard', 'action' => 'get-members', 'project_id' => $this->project->project_id), 'sitecrowdfunding_extended', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'maxChoices': 40,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });

        contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
            document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
        });
    });
</script>
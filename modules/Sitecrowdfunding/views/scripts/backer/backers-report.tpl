<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: backers-report.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->paginator->getCurrentPageNumber() <= 1) : ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<?php endif; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'isFundingSection'=> true, 'sectionTitle' => 'Backers Report', 'sectionDescription'=> '')); ?>
    <div class="global_form">
    <div id="show_tab_content_child"></div>
    <h3 class="mbot10">
       <?php //echo $this->translate('Backers Report') ?>
    </h3> 
    
    <div class="compose-exports">
        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'export-backers','project_id' => $this->project->project_id), $this->translate('Export'), array('class' => 'seaocore_icon_exports', 'title' => 'Export')) ?>

        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'compose', 'project_id' => $this->project->project_id, 'format' => 'smoothbox'), $this->translate('Compose'), array('class' => 'smoothbox icon seaocore_icon_add', 'title' => 'Compose')) ?>
    </div>
    <div class="search-backers">

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
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                        'onclick' => '',
                        'class' => 'buttonlink icon_previous'
                    ));
                    ?>
                    <span id="manage_spinner_prev"></span>
                </div>

                <div id="project_manage_backer_next" class="paginator_next sitecrowdfunding_data_paging_link">
                    <span id="manage_spinner_next"></span>
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                        'onclick' => '',
                        'class' => 'buttonlink_right icon_next'
                    ));
                    ?>
                </div>
            </div>
        <?php else : ?>
            <div class="tip"><span>
                    <?php echo $this->translate($this->message); ?>
                </span></div>
        <?php endif; ?>
        </div>
    </div>
    <div id="hidden_ajax_data" style="display: none;"></div>
</div>
</div>
</div>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        <?php if ($this->total_item): ?>
            document.getElementById('project_manage_backer_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('project_manage_backer_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
            $('project_manage_backer_previous').removeEvents('click').addEvent('click', function () {
                $('manage_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';

                en4.core.request.send(new Request.HTML({
                    url: en4.core.baseUrl + 'sitecrowdfunding/backer/backers-report/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
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
                    url: en4.core.baseUrl + 'sitecrowdfunding/backer/backers-report/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
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


<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
    en4.core.runonce.add(function()
    {
        var contentAutocomplete = new Autocompleter.Request.JSON('username', '<?php echo $this->url(array('controller' => 'dashboard', 'action' => 'get-members', 'project_id' => $this->project->project_id), 'sitecrowdfunding_extended', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'maxChoices': 40,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function(token) {
                    if($('user_id').value.split(',').indexOf(String(token.id)) != -1) 
                        return false;
                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });

        contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
            document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
        });
    });
</script>
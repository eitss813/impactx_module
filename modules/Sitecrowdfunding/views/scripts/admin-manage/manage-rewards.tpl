<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-rewards.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function (order, default_direction) { 
        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }
        $('filter_form').submit();
    }

    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected rewards ?")) ?>');
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;

        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>

<h2>
    <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin'); ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<h2><?php echo $this->translate('Manage Rewards'); ?></h2>
<h4><?php echo $this->translate('This page lists all the rewards created by Project Owners. You can use this page to monitor these rewards and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific reward entries. Leaving the filter fields blank will show all the reward entries on your social network.'); ?></h4><br />

<div class="admin_search sitecrowdfunding_admin_crowdfunding_search">
    <div class="search">
        <form method="post" class="global_form_box" action="">

            <div>
                <label>
                    <?php echo $this->translate("Title") ?>
                </label>
                <?php if (empty($this->title)): ?>
                    <input type="text" name="title" /> 
                <?php else: ?>
                    <input type="text" name="title" value="<?php echo $this->translate($this->title) ?>"/>
                <?php endif; ?>
            </div>
            <div>
                <label>
                    <?php echo $this->translate("Project") ?>
                </label>
                <?php if (empty($this->project)): ?>
                    <input type="text" name="project" /> 
                <?php else: ?>
                    <input type="text" name="project" value="<?php echo $this->translate($this->project) ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Project Owner") ?>
                </label>	
                <?php if (empty($this->projectOwner)): ?>
                    <input type="text" name="project_owner" /> 
                <?php else: ?> 
                    <input type="text" name="project_owner" value="<?php echo $this->translate($this->projectOwner) ?>" />
                <?php endif; ?>
            </div> 

            <div>
                <label>
                    <?php echo $this->translate("Backed Amount") ?>
                </label> 
                <?php if (empty($this->minAmount)): ?>
                        <input size="8" type="text" placeholder="min" name="min_amount" /> 
                <?php else: ?> 
                    <input size="8" type="text" name="min_amount" value="<?php echo $this->translate($this->minAmount) ?> "/>
                <?php endif; ?>
                <?php if (empty($this->maxAmount)): ?>
                        <input size="8"  type="text" placeholder="max" name="max_amount" /> 
                <?php else: ?> 
                    <input size="8" type="text" name="max_amount" value="<?php echo $this->translate($this->maxAmount) ?> "/>
                <?php endif; ?>
                    
            </div>  
            <div class="mtop10">
                <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
            </div>
        </form>
    </div>
</div>
<br />

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>

<div class='admin_members_results mtop10'>
    <?php $counter = $this->paginator->getTotalItemCount(); ?>
    <?php if (!empty($counter)): ?>
        <div class="">
            <?php echo $this->translate(array('%s reward found.', '%s rewards found.', $counter), $this->locale()->toNumber($counter)) ?>
        </div>
    <?php else: ?>
        <div class="tip mtop10"><span>
                <?php echo $this->translate("No results were found.") ?></span>
        </div>
    <?php endif; ?>
    <?php echo $this->paginationControl($this->paginator); ?>
</div>
<br />

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete-rewards')); ?>" onSubmit="return multiDelete()">

        <table class='admin_table seaocore_admin_table' width="100%">
            <thead>
                <tr>
                    <th><input onclick="selectAll()" type='checkbox' class='checkbox'></th>

                    <?php $class = ( $this->order == 'reward_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('reward_id', 'DESC');"><?php echo $this->translate('Reward ID'); ?></a></th>

                    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>

                    <?php $class = ( $this->order == 'project' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('project_title', 'ASC');"><?php echo $this->translate('Project'); ?></a></th>

                    <?php $class = ( $this->order == 'project_owner' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'DESC');"><?php echo $this->translate('Project Owner'); ?></a></th>                

                    <?php $class = ( $this->order == 'pledge_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('pledge_amount', 'DESC');"><?php echo $this->translate('Backed Amount'); ?></a></th>

                    <?php $class = ( $this->order == 'quantity' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('quantity', 'DESC');"><?php echo $this->translate('Quantity'); ?></a></th> 

                    <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Creation Date'); ?></a></th>

                    <?php $class = ( $this->order == 'delivery_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('delivery_date', 'DESC');"><?php echo $this->translate('Shipping Date'); ?></a></th>
 
 
                    <th class="<?php echo $class ?>"  class='admin_table_centered'><?php echo $this->translate('Options'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($this->paginator) > 0): ?>
                    <?php foreach ($this->paginator as $item): ?> 
                        <tr>
                            <td><input name='delete_<?php echo $item->reward_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->reward_id ?>"/></td>

                            <td><?php echo $item->reward_id ?></td> 
                            <td class='admin_table_bold' style="white-space:normal;" title="<?php echo $this->translate($item->title) ?>"> <?php echo $this->htmlLink(array('route'=>'default','module'=>'sitecrowdfunding','controller'=>'reward','action'=>'manage','project_id'=>$item->project_id),Engine_Api::_()->seaocore()->seaocoreTruncateText($item->title, 10), array('target' => '_blank')) ?>
                            </td> 

                            <td class='admin_table_bold' style="white-space:normal;" title="<?php echo $this->translate($item->project_title) ?>">
                                <a href="<?php echo $this->url(array('project_id' => $item->project_id, 'slug' => $item->project_title), "sitecrowdfunding_entry_view") ?> "  target='_blank'>
                                    <?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($item->project_title, 10)) ?></a>
                            </td>

                            <td class='admin_table_bold' title="<?php echo $this->translate($item->displayname) ?>"> <?php echo $this->htmlLink(array('route'=>'user_profile','module'=>'user','controller'=>'profile','action'=>'index','id'=>$item->owner_id),Engine_Api::_()->seaocore()->seaocoreTruncateText($item->displayname, 10), array('target' => '_blank')) ?>
                            </td> 
                            <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($item->pledge_amount);?></td>

                            <td><?php 
                                if($item->quantity == 0) { 
                                    echo $this->translate("UNLIMITED");
                                } else {
                                    echo $this->translate($item->quantity); 
                                }?>  
                            </td>   

                            <td><?php echo $this->translate(gmdate('M d,Y, g:i A', strtotime($item->creation_date))) ?></td> 
                            <td><?php echo $this->translate(gmdate('M d,Y', strtotime($item->delivery_date))) ?></td>                                
                            
                            <td class='admin_table_options'>

                              <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitecrowdfunding','controller'=>'manage','action'=>'reward-details','project_id'=>$item->project_id,'reward_id'=>$item->reward_id),$this->translate('details'), array('class' => 'smoothbox')) ?> | 
                                <?php if($item->spendRewardQuantity() == 0) {
                                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'manage', 'action' => 'delete-reward', 'reward_id' => $item->reward_id), $this->translate('delete'), array('class' => 'smoothbox'));

                                      }else {
                                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'manage', 'action' => 'delete-not'), $this->translate('delete'), array('class' => 'smoothbox'));
                                        } 
                                     ?> 
                            </td>
                            
                        </tr>

                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <br />
        <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
        </div>
    </form>
<?php endif; ?> 
</div>

<script type="text/javascript">
  
    function clear(element)
    {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    } 
</script>

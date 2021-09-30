<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

    function multiDelete()
    {
        return confirm("<?php echo $this->translate("Are you sure you want to delete the selected Countries?") ?>");
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>

<h2 class="fleft">
    <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin'); ?>
</h2>


<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='settings clr'>
    <h3><?php echo $this->translate("Manage Shipping Locations"); ?></h3>
    <p class="description"><?php echo $this->translate('Below, you can add and manage various countries. Project owner of your site will be able to ship rewards only in the locations configured by you here. You can add new locations by clicking on "Add Locations" link below and can enable / disable added locations. You can also import locations via the CSV file by using the "Import Locations" link below.'); ?></p>
</div>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'location', 'action' => 'add-location'), $this->translate("Add Location"), array('class' => 'smoothbox buttonlink seaocore_icon_add')); ?>
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'location', 'action' => 'import'), $this->translate("Import Locations"), array('class' => 'buttonlink seaocore_icon_import')); ?>

<br /><br />

<?php if (count($this->paginator)): ?>
    <?php foreach ($this->paginator as $item): ?>
        <?php $countriesName[$item->region_id] = Zend_Locale::getTranslation($item->country, 'country'); ?>
        <?php $locationArray[$item->region_id] = $item; ?>
    <?php endforeach;
    asort($countriesName); ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <table class='admin_table' style="width: 50%;">
            <thead>
                <tr>
                    <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                    <th class=""><?php echo $this->translate("Country"); ?></th>
                    <th class="admin_table_centered"><?php echo $this->translate("Status") ?></th>
                    <th class="admin_table_centered"><?php echo $this->translate("Options") ?></th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($countriesName as $region_id => $item): ?>
                    <tr>
                        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $locationArray[$region_id]->country ?>' value="<?php echo $locationArray[$region_id]->country ?>" /></td>
                        <td class=""><?php echo $item ?></td>
                        
                            <?php if (!empty($locationArray[$region_id]->country_status)): ?>
                            <td align="center" class="admin_table_centered">
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'location', 'action' => 'countryenable', 'country' => $locationArray[$region_id]->country, 'current_status' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/images/region_enable1.gif', '', array('title' => $this->translate('Disable Country')))) ?>
                            </td>
                            <?php else: ?>
                            <td align="center" class="admin_table_centered">
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'location', 'action' => 'countryenable', 'country' => $locationArray[$region_id]->country, 'current_status' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/images/region_enable0.gif', '', array('title' => $this->translate('Enable Country')))) ?>
                            </td>
                            <?php endif; ?>
                        <td align="left" class="admin_table_centered">
                            <?php
                            echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'location', 'action' => 'delete-location', 'id' => $locationArray[$region_id]->region_id), $this->translate("delete"), array('class' => 'smoothbox'));
                            ?>
                        </td>
                    </tr>
    <?php endforeach; ?>
            </tbody>
        </table>
        <br />
        <div class='buttons fleft clr mtop10'>
            <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
        </div>
    </form>

    <br />

    <div>
    <?php echo $this->paginationControl($this->paginator); ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
    <?php echo $this->translate("No locations selected yet.") ?>
        </span>
    </div>
<?php endif; ?>
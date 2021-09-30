
<h2>
  <?php echo $this->translate('Directory / Pages Plugin - Configure Plans, Layout and Mapping with Profile Types / Member Levels') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>
<?php endif; ?>

<?php if( count($this->subnavigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->subnavigation)->render();
    ?>
</div>
<?php endif; ?>

<?php 
if( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0  ) 
{
  echo "<ul class='form-errors'><li>Payment gateways not enabled or configured properly.</li></ul>";
  // return ;
}
?>
<?php $option = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.view', 1);
if($option == 2):
$count = 1;
$defaultLayout_rows = Engine_Api::_()->getDbtable('layouts','sitepage')->getLayouts();
foreach ($defaultLayout_rows as $value) {
    $layouts[$value['layout_id']] = $value;
}
?>

<div class="clear">
<div class="settings">
    <form class="global_form" method="post">
        <div>
            <div>
              <h3>Manage Templates</h3>
              <span>Here you can choose any template as a default template for your select package page. You can also create your own template from “Create New Template” link given below. </span>
            </div>
            <br />
            <div>
              <?php echo $this->htmlLink(array('action' => 'add-template', 'reset' => false), $this->translate('Create New Template'), array(
                'class' => 'buttonlink icon_plan_add',
              )) ?>
            </div>
            <br><br>

            <?php if(count($this->templates)>=1):?>
            <table class='admin_table'>
                <thead>
                  <tr>
                    <th style='width: 1%;'>
                        <?php echo $this->translate("ID") ?>
                    </th>
                    <th style='width: 1%;'>
                        <?php echo $this->translate("Template Name"); ?>
                    </th>
                    <th style='width: 1%;' class='admin_table_centered'> Preview </th>
                    <th style='width: 1%;' class='admin_table_centered'>
                        <?php echo $this->translate("Options") ?>
                    </th>
                  </tr>
                </thead>
                <tbody>
                    <?php foreach($this->templates as $template): ?>
                    <tr>
                        <td>
                            <?php echo $count++; ?>
                        </td>
                        <td>
                            <?php echo $template['template_name']; ?>
                        </td>
                        <td class='admin_table_centered'>
                            <?php 
                                if ($layouts[$template['layout']]['default'] && $template['default'])
                                    echo ' <a title="Preview - Template '.trim($template['template_id']).'" href="'.$this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/layout_'.trim($template['template_id']).'.png" target="_blank" class="seaocore_icon_view" > </a> '; 
                            ?>
                        </td>
                        <td class='admin_table_centered' style='width: 1%;'>
                            <?php if ( !$template['default']) : ?>
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage' , 'controller' => 'package' , 'action' => 'edit-template' , 'template_id' => $template['template_id'], ), $this->translate('Edit'), array('style' => 'text-decoration: none;')); ?>
                                |
                            <?php endif; ?>
                            <?php if($template['active'] != 1): ?>
                              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage' , 'controller' => 'package' , 'action' => 'activate-template' , 'template_id' => $template['template_id'] ), $this->translate('Activate'), array( 'class' => 'smoothbox', 'style' => 'text-decoration: none;')); ?>
                            <?php else : ?>
                                Active
                            <?php endif; ?>
                            <?php if ( !$template['default']): ?>
                              |
                              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage' , 'controller' => 'package' , 'action' => 'delete-template' , 'template_id' => $template['template_id'] ), $this->translate('Delete'), array( 'class' => 'smoothbox', 'style' => 'text-decoration: none;')); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <br/>
            <?php else:?>
                <div class="tip">
                    <span><?php echo $this->translate("There are currently no templates"); ?></span>
                </div>
            <?php endif;?>
        </div>
    </form>
    <?php else:?>
    <div class="tip">
                    <span><?php echo $this->translate("Please enable the Custom option for Package View in the Global Settings "); ?></span>
    </div>
    <?php endif;?>
</div>
</div> 


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
$count = 1;
if( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0  ) 
{
  echo "<ul class='form-errors'><li>Payment gateways not enabled or configured properly.</li></ul>";
  // return ;
}
?>
<?php $option = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.view', 1);
if($option == 2): ?>
<div class="clear">
<div class="settings">
    <form class="global_form" method="post">
        <div>
            <div>
              <h3>Manage Layouts</h3>
              <span>Here you can choose any layout as a default layout for your select package page. You can also create your own layout from “Create New Layout” link given below.</span>
            </div>
            <br />
            <div>
              <?php echo $this->htmlLink(array('action' => 'add-layout', 'reset' => false), $this->translate('Create New Layout'), array(
                'class' => 'buttonlink icon_plan_add',
              )) ?>
            </div>
            <br><br>

            <?php if(count($this->layouts)>=1):?>
            <table class='admin_table'>
                <thead>
                  <tr>
                    <th style='width: 1%;'>
                        <?php echo $this->translate("ID") ?>
                    </th>
                    <th style='width: 1%;'>
                        <?php echo $this->translate("Layout Name"); ?>
                    </th>
                    <th style='width: 1%;' class='admin_table_centered'> Preview </th>
                    <th style='width: 1%;' class='admin_table_centered'>
                        <?php echo $this->translate("Options") ?>
                    </th>
                  </tr>
                </thead>
                <tbody>
                    <?php foreach($this->layouts as $layout): ?>
                    <tr>
                        <td>
                            <?php echo $count++; ?>
                        </td>
                        <td>
                            <?php echo $layout['layout_name']; ?>
                        </td>
                        <td class='admin_table_centered'>
                            <?php 
                                if ($layout['default'])
                                    echo ' <a title="Preview - Layout '.trim($layout['layout_id']).'" href="'.$this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/layout_'.trim($layout['layout_id']).'.png" target="_blank" class="seaocore_icon_view" > </a> '; 
                            ?>
                        </td>
                        <td class='admin_table_centered' style='width: 1%;'>
                            <?php if ( !$layout['default']): ?>
                              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage' , 'controller' => 'package' , 'action' => 'delete-layout' , 'layout_id' => $layout['layout_id'] ), $this->translate('delete'), array( 'class' => 'smoothbox', 'style' => 'text-decoration: none;')); ?>
                            <?php else: ?>
                              <?php echo $this->translate("default"); ?> 
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

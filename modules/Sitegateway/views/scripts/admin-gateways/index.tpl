<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
  <?php echo 'Advanced Payment Gateways / Stripe Connect Plugin'; ?>
</h2>

<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<p>
  <?php echo "Here, you can browse and manage payment gateways for your website. Using 'Edit' link available with every gateway, you can configure that gateway and make it enable for your website."; ?>
</p>

<br />
<br />

<?php if( !empty($this->error) ): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>
  
  <br />
<?php endif; ?>


<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s gateway found", "%s gateways found", $count), $count) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />


<table class='admin_table' style='width: 100%;'>
  <thead>
    <tr>
      <th style='width: 5%;'><?php echo "ID" ?></th>
      <th style='width: 65%;'><?php echo "Title" ?></th>
      <th style='width: 15%;' class='admin_table_centered'><?php echo "Enabled" ?></th>
      <th style='width: 15%;' class='admin_table_options center'><?php echo"Options" ?></th>
    </tr>
  </thead>
  <tbody>
    <?php if( count($this->paginator) ): ?>
      <?php foreach( $this->paginator as $item ): ?>
        <tr>
          <td>
            <?php echo $item->gateway_id ?>
          </td>
          <td class='admin_table_bold'>
            <?php echo $item->title ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo ( $item->enabled ? 'Yes' : 'No' ) ?>
          </td>
          <td class='admin_table_options center'>
            <a href='<?php echo $this->url(array('action' => 'edit', 'gateway_id' => $item->gateway_id));?>'>
              <?php echo "Edit" ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    contactinfo.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo 'One Time Password (OTP) Plugin'; ?>
</h2>

<?php if( count($this->navigation) ): ?>
      <div class='siteotpverifier_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
      </div>
    <?php endif; ?>
<div>
  <p> Here, you can manage the details of users who have associated a phone number with their account on your website. [Note: Users who have not associated a phone number with their account are not listed here.] </p>
</div>
<br/>
    <div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s member found", "%s members found", $count),
        $this->locale()->toNumber($count)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
      //'params' => $this->formValues,
    )); ?>
  </div>
</div>

<br />
    
    
<div class="admin_table_form">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('country_code', 'ASC');"><?php echo $this->translate("Country Code") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('phoneno', 'ASC');"><?php echo $this->translate("Phone Number") ?></a></th>
        <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ):
          $user = $this->item('user', $item->user_id);
          $otpUser = $this->item('siteotpverifier_user', $item->user_id);
          ?>
          <tr>
            <td><?php echo $item->user_id ?></td>
            <td class='admin_table_bold'>
              <?php echo $this->htmlLink($user->getHref(),
                  $this->string()->truncate($user->getTitle(), 10),
                  array('target' => '_blank'))?>
            </td>
            <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
            <td class='admin_table_email'>
              <?php if( !$this->hideEmails ): ?>
                <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
              <?php else: ?>
                (hidden)
              <?php endif; ?>
            </td>
            <td class="admin_table_centered nowrap">
              <?php if(!empty($otpUser['country_code'])):
                echo $GLOBALS['countryCodes'][$otpUser['country_code']];
              else:
                $default=Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.defaultCountry','+1');
                echo $GLOBALS['countryCodes'][$default];
              endif;?>
            </td>
            <td class='admin_table_centered'>
              <?php if(!empty($otpUser['phoneno'])):
                echo $otpUser['phoneno'];
              else:
                echo "Not Available";
              endif;?>
            </td>
            <td class='admin_table_options'>
              <a class='smoothbox' href='<?php echo $this->url(array('action' => 'edit', 'id' => $item->user_id));?>'>
                <?php echo $this->translate("edit") ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
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
  };
</script>
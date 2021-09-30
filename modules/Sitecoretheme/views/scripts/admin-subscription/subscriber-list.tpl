<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: subscriber-list.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<h2>
    <?php echo SITECORETHEME_PLUGIN_NAME; ?>
</h2>


<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

  <div class='seaocore_sub_tabs tabs'>
    <ul class="navigation">
      <li >
        <?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitecoretheme','controller'=>'subscription','action'=>'index'), 'Send Newsletter', array())
        ?>
      </li>
      <li class="active">
        <?php
          echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitecoretheme','controller'=>'subscription','action'=>'subscriber-list'), 'Subscribers List', array())
        ?>
      </li>
    </ul>
  </div>
<div class='clear'></div>


<br />

<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s subscriber found", "%s subscribers found", $count),
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
        <th><?php echo $this->translate("Email") ?></th>
        <th><?php echo $this->translate("User ID") ?></th>
        <th><?php echo $this->translate("Display Name") ?></th>
        <th><?php echo $this->translate("Username") ?></th>
        <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("User Level") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td class='admin_table_email'>
               <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
            </td>

            <?php if(!empty($item->user_id)):?>
              <?php $user = $this->item('user', $item->user_id); ?>
              <td><?php echo $item->user_id ?></td>
              <td >
                <?php echo $this->htmlLink($user->getHref(),
                    $this->string()->truncate($user->getTitle(), 10),
                    array('target' => '_blank'))?>
              </td>
              <td >
                <?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?>
              </td>

              <td>
                <a href="<?php echo $this->url(array('module'=>'authorization','controller'=>'level', 'action' => 'edit', 'id' => $user->level_id)) ?>">
                  <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $user->level_id)->getTitle()) ?>
                </a>
              </td>
             <?php else: ?>
              <td >
                <span>--</span>
              </td>
              <td >
                <span>--</span>
              </td>
              <td >
                <span>--</span>
              </td>
              <td >
                <span>--</span>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />

</div>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit_tabs.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$front = Zend_Controller_Front::getInstance();
	$module = $front->getRequest()->getModuleName();
	$controller = $front->getRequest()->getControllerName();
	$action = $front->getRequest()->getActionName();
  $activeMenu='';
  if($module == 'sitepage' && $controller == 'insights' && $action == 'index'){
    $activeMenu='sitepage_dashboard_insights';
  }
  if($module == 'sitepage' && $controller == 'insights' && ($action == 'export-report' || $action == 'export-excel' || $action == 'export-webpage')){
    $activeMenu='sitepage_dashboard_reports';
  }
?>
<?php
  $dashboard_navigation_content = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_content',  array(),$activeMenu);
  $dashboard_navigation_admin = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_admin',  array(),$activeMenu);
  $dashboard_navigation_others = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_others',  array(),$activeMenu);
  $dashboard_navigation_promotion = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_promotion',  array(),$activeMenu);
  $dashboard_navigation_projects = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_projects',  array(),$activeMenu);
  $dashboard_navigation_transactions = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_transactions',  array(),$activeMenu);
  $dashboard_navigation_initiatives = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_initiatives',  array(),$activeMenu);
  $dashboard_navigation_payment = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_payment',  array(),$activeMenu);
  $dashboard_partner_organization = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_partner_organization',  array(),$activeMenu);
  $dashboard_navigation_managefoms = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_managefoms',  array(),$activeMenu);
  $dashboard_navigation_manageapi = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_manageapi',  array(),$activeMenu);
$dashboard_navigation_metrics = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_metrics',  array(),$activeMenu);
  $dashboard_manage_notifications =  Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_manage_notifications',  array(),$activeMenu);
  $dashboard_settings =  Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard_settings',  array(),$activeMenu);
?>
<?php 
//GET SITEPAGE OBJECT
$sitepage = Engine_Api::_()->getItem('sitepage_page', $this->page_id);

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_dashboard.css');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');

$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js');

include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>

<?php
$this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showurl.column', 1); ?>
<?php $edit_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.edit.url', 0); ?>

<div class="sitepage seaocore_db_tabs">
    <div class="dashboard_info">
        <div class="dashboard_info_image">
            <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.profile')) ?>
        </div>

        <div class="dashboard_info_details">
            <?php if (Engine_Api::_()->sitepage()->hasPackageEnable()): ?>
                <div>
                    <b><?php echo $this->translate('Package: ') ?></b>
                    <a href='<?php echo $this->url(array("action" => "detail", 'id' => $sitepage->package_id), 'sitepage_packages', true) ?>' onclick="owner(this);return false;" title="<?php echo $this->translate(ucfirst($sitepage->getPackage()->title)) ?>"><?php echo $this->translate(ucfirst($sitepage->getPackage()->title)); ?></a>
                </div>
                <?php if (!$sitepage->getPackage()->isFree()): ?>
                    <div>
                        <b><?php echo $this->translate('Payment: ') ?></b>
                        <?php
                              if ($sitepage->status == "initial"):
                        echo $this->translate("Not made");
                        elseif ($sitepage->status == "active"):
                        echo $this->translate("Yes");
                        else:
                        echo $this->translate(ucfirst($sitepage->status));
                        endif;
                        ?>
                    </div>
                <?php endif ?>
            <?php endif ?>

            <?php /*
            <div>
                <b><?php echo $this->translate('Status: ') . Engine_Api::_()->sitepage()->getPageStatus($sitepage) ?></b>
            </div>


            <?php if (!empty($sitepage->aprrove_date)): ?>
                <div style="color: chocolate">
                    <?php echo $this->translate('Approved ') . $this->timestamp(strtotime($sitepage->aprrove_date)) ?>
                </div>

                <?php if (Engine_Api::_()->sitepage()->hasPackageEnable()): ?>
                    <div style="color: green;">
                        <?php
                            $expiry = Engine_Api::_()->sitepage()->getExpiryDate($sitepage);
                        if ($expiry !== "Expired" && $expiry !== $this->translate('Never Expires'))
                        echo $this->translate("Expiration Date: ");
                        echo $expiry;
                        ?>
                    </div>
                <?php endif; ?>
            <?php endif ?>

            */ ?>

            <?php if (Engine_Api::_()->sitepage()->canShowPaymentLink($sitepage->page_id)): ?>
                <div class="tip center mtop5">
                    <span class="db_payment_link">
                      <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitepage->page_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
                      <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
                            <input type="hidden" name="page_id_session" id="page_id_session" />
                      </form>
                    </span>
                </div>
            <?php endif; ?>

            <?php if (Engine_Api::_()->sitepage()->canShowRenewLink($sitepage->page_id)): ?>
                <div class="tip mtop5">
                    <span style="margin:0px;"> <?php echo $this->translate("Please click "); ?>
                        <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitepage->page_id ?>)"><?php echo $this->translate('here'); ?></a>
                        <?php echo $this->translate(' to renew page.'); ?>
                        <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
                            <input type="hidden" name="page_id_session" id="page_id_session" />
                        </form>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <ul>
        <li class="seaocore_db_head" onclick="projectsExpand()">
            <h3><?php echo $this->translate("Projects"); ?>
                <i  id="projects"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_projects)): ?>
        <?php foreach ($dashboard_navigation_projects as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li id="projects-sub" style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>

        <li class="seaocore_db_head" onclick="transactionsExpand()">
            <h3><?php echo $this->translate("Transactions"); ?>
                <i id="transactions" class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_transactions)): ?>
        <?php foreach ($dashboard_navigation_transactions as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li id="transactions-sub" style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>

        <li class="seaocore_db_head" onclick="initiativesExpand()">
            <h3><?php echo $this->translate("Initiatives"); ?>
                <i  id="initiatives"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_initiatives)): ?>
        <?php foreach ($dashboard_navigation_initiatives as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li id="initiatives-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>


        <li class="seaocore_db_head" onclick="paymentExpand()">
            <h3><?php echo $this->translate("Payment"); ?>
                <i  id="payment"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_payment)): ?>
        <?php foreach ($dashboard_navigation_payment as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li id="payment-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>

        <li class="seaocore_db_head" onclick="contentExpand()">
            <h3><?php echo $this->translate("Content"); ?>
                <i  id="content"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_content)): ?>
            <?php foreach ($dashboard_navigation_content as $item): ?>
            <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
            'reset_params', 'route', 'module', 'controller', 'action', 'type',
            'visible', 'label', 'href')));
            if (!isset($attribs['active'])) {
            $attribs['active'] = false;
            }
            ?>
            <li  id="content-sub" style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
            </li>
            <?php endforeach; ?>
        <?php endif; ?>

        <li class="seaocore_db_head" onclick="manageNotificationsExpand()">
            <h3><?php echo $this->translate("Notifications"); ?>
                <i  id="manage-notifications"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_manage_notifications)): ?>
        <?php foreach ($dashboard_manage_notifications as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li  id="manage-notifications-sub" style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>


        <li class="seaocore_db_head" onclick="partnerOrganizationExpand()">
            <h3><?php echo $this->translate("Partner Organization"); ?>
                <i  id="partner-organization"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_partner_organization)): ?>
        <?php foreach ($dashboard_partner_organization as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li  id="partner-organization-sub" style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>


        <li class="seaocore_db_head" onclick="manageformsExpand()">
            <h3><?php echo $this->translate("Manage Forms"); ?>
                <i  id="content"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_managefoms)): ?>
        <?php foreach ($dashboard_navigation_managefoms as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li  id="mangeforms-sub" style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>

       <!-- <li class="seaocore_db_head" onclick="developerapiExpand()">
            <h3><?php echo $this->translate("Developer Api"); ?>
                <i  id="content"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_manageapi)): ?>
        <?php foreach ($dashboard_navigation_manageapi as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li  id="developerapi-sub" style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li> -->
        <?php endforeach; ?>
        <?php endif; ?>

        <li class="seaocore_db_head" onclick="metricsExpand()">
            <h3><?php echo $this->translate("Metrics"); ?>
                <i  id="content"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_metrics)): ?>
        <?php foreach ($dashboard_navigation_metrics as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li  id="metrics-sub" style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>




        <li class="seaocore_db_head" onclick="adminExpand()">
            <h3><?php echo $this->translate("Admin"); ?>
                <i  id="admin"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_admin)): ?>
            <?php foreach ($dashboard_navigation_admin as $item): ?>
                <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                'reset_params', 'route', 'module', 'controller', 'action', 'type',
                'visible', 'label', 'href')));
                if (!isset($attribs['active'])) {
                $attribs['active'] = false;
                }
                ?>
                <li  id="admin-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                    <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>

          <!-- Settings -->
        <li class="seaocore_db_head" onclick="settingsExpand()">
            <h3><?php echo $this->translate("Settings"); ?>
                <i  id="settings"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_settings)): ?>
        <?php foreach ($dashboard_settings as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li  id="settings-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>

<!-- show the my blogs menu-->
                <li class="seaocore_db_head"  onclick="myBlogsExpand()">
                    <h3>
                        <?php echo $this->translate("My Blogs"); ?>
                        <i id="myblog" class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
              
                <li id="myblog-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                   <a class="menu_sitecrowdfunding_dashboard_metrics sitecrowdfunding_dashboard_metricdetails" target="_blank" href='<?php echo $this->url(array( 'action' => 'manage', 'org_id' => $this->page_id ), 'sesblog_general', true) ?>'>
        <span ><?php echo $this->translate('My Blogs') ?></span>
        </a>
                </li>


        <?php if (count($dashboard_navigation_others)): ?>
        <li class="seaocore_db_head" onclick="othersExpand()">
            <h3><?php echo $this->translate("Others"); ?>
                <i  id="others"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <?php if (count($dashboard_navigation_others)): ?>
        <?php foreach ($dashboard_navigation_others as $item): ?>
        <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));
        if (!isset($attribs['active'])) {
        $attribs['active'] = false;
        }
        ?>
        <li id="others-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (count($dashboard_navigation_promotion)): ?>
            <li class="seaocore_db_head" onclick="othersExpand()">
                <h3><?php echo $this->translate("Promotion"); ?>
                    <i  id="promotion"  class="fa fa-arrow-circle-up" style="float: right;"></i>
                </h3>
            </li>
            <?php foreach ($dashboard_navigation_promotion as $item): ?>
                <?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                'reset_params', 'route', 'module', 'controller', 'action', 'type',
                'visible', 'label', 'href')));
                if (!isset($attribs['active'])) {
                $attribs['active'] = false;
                }
                ?>
                <li id="promotion-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                    <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<?php if (Engine_Api::_()->sitepage()->canShowPaymentLink($sitepage->page_id)): ?>
  <div class="sitepage_edit_content">
    <div class="tip">
      <span>
  <?php echo $this->translate('The package for your Page requires payment. You have not fulfilled the payment for this Page.'); ?>
        <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitepage->page_id ?>)"><?php echo $this->translate('Make payment now!'); ?></a>
        <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
          <input type="hidden" name="page_id_session" id="page_id_session" />
        </form>
      </span>
    </div>
  </div>
<?php endif; ?>
<?php if (Engine_Api::_()->sitepage()->canShowRenewLink($sitepage->page_id)): ?>
  <div class="sitepage_edit_content">
    <div class="tip">
      <span>
  <?php if ($sitepage->expiration_date <= date('Y-m-d H:i:s')): ?>
    <?php echo $this->translate("Your package for this Page has expired and needs to be renewed.") ?>
  <?php else: ?>
    <?php echo $this->translate("Your package for this Page is about to expire and needs to be renewed.") ?>
  <?php endif; ?>
  <?php echo $this->translate(" Click "); ?>
        <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitepage->page_id ?>)"><?php echo $this->translate('here'); ?></a><?php echo $this->translate(' to renew it.'); ?>
        <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
          <input type="hidden" name="page_id_session" id="page_id_session" />
        </form>
      </span>
    </div>
  </div>
<?php endif; ?>


<script type="text/javascript">

en4.core.runonce.add(function() {
var element = $(event.target);
				if( element.tagName.toLowerCase() == 'a' ) {
					element = element.getParent('li');
				}
				
				//element.addClass('<?php //echo $class ?>');
});
		
	if($$('.ajax_dashboard_enabled')) {
		en4.core.runonce.add(function() {
			$$('.ajax_dashboard_enabled').addEvent('click',function(event) {
				var element = $(event.target);
				var show_url = '<?php echo $show_url; ?>';
				var edit_url = '<?php echo $edit_url; ?>';
				var page_id = '<?php echo $this->page_id; ?>';
				event.stop();
				var href = this.href; 
				var ulel=this.getParent('ul');
				$('show_tab_content').innerHTML = '<center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitepage/externals/images/spinner_temp.gif" /></center>'; 
				ulel.getElements('li').removeClass('selected');
				
				if( element.tagName.toLowerCase() == 'a' ) {
					element = element.getParent('li');
				}
				
				element.addClass('selected');
				if (history.pushState) {
					history.pushState( {}, document.title, href );
				}
				
				var request = new Request.HTML({
					'url' : href,
					'method' : 'get',
					'data' : {
						'format' : 'html',
						'is_ajax' : 1
											
					},
					onSuccess :  function(responseTree, responseElements, responseHTML, responseJavaScript)  {
			/*      if (Show_Tab_Selected) {
							$('id_'+ Show_Tab_Selected).set('class', '');
							Show_Tab_Selected = PageId;
						}*/	
					// $('id_' + PageId).set('class', 'selected');
							
						$('show_tab_content').innerHTML = responseHTML; 

                       if($('show_tab_content').getElement('.layout_middle'))
                                                $('show_tab_content').innerHTML = $('show_tab_content').getElement('.layout_middle').innerHTML;
						if (window.InitiateAction) {
							InitiateAction ();
						}

						if (($type(show_url) && show_url == 1) && ($type(edit_url) && edit_url == 1)) {
							ShowUrlColumn(page_id);
						}
						if (window.activ_autosuggest) { 
							activ_autosuggest ();
						}
						
						var e4 = $('page_url_msg-wrapper');
						if($('page_url_msg-wrapper'))
							$('page_url_msg-wrapper').setStyle('display', 'none');
							
						if(typeof cat != 'undefined' && typeof subcatid != 'undefined' && typeof subcatname != 'undefined' && typeof subsubcatid != 'undefined') {
							subcategory(cat, subcatid, subcatname,subsubcatid);
						}

						if (document.getElementById("category_name")) {
							$('category_name').focus();
						}
						en4.core.runonce.trigger();
                                                
            if(SmoothboxSEAO){
                SmoothboxSEAO.bind($('show_tab_content'));
            } 
					}
				});
				request.send();
			});
		});
	}
	
  var Show_Tab_Selected = "<?php echo $this->sitepages_view_menu; ?>";
  function submitSession(id) {
    document.getElementById("page_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }

  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
    function projectsExpand(){
        var x = document.querySelectorAll("#projects-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('projects').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('projects').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function transactionsExpand(){
        var x = document.querySelectorAll("#transactions-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('transactions').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('transactions').className= "fa fa-arrow-circle-down";
        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function initiativesExpand(){
        var x = document.querySelectorAll("#initiatives-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('initiatives').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('initiatives').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function paymentExpand(){
        var x = document.querySelectorAll("#payment-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('payment').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('payment').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function contentExpand(){
        var x = document.querySelectorAll("#content-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('partner-organization').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('partner-organization').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function manageNotificationsExpand(){
        var x = document.querySelectorAll("#manage-notifications-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('manage-notifications').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('manage-notifications').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function partnerOrganizationExpand(){
        var x = document.querySelectorAll("#partner-organization-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('content').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('content').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function manageformsExpand(){
        var x = document.querySelectorAll("#mangeforms-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('content').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('content').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function developerapiExpand(){
        var x = document.querySelectorAll("#developerapi-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('content').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('content').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function metricsExpand(){
        var x = document.querySelectorAll("#metrics-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('content').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('content').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }

    function adminExpand(){
        var x = document.querySelectorAll("#admin-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('admin').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('admin').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    function settingsExpand(){
        var x = document.querySelectorAll("#settings-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('settings').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('settings').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
    
    
    
      function myBlogsExpand(){
                var x = document.querySelectorAll("#myblog-sub");
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('myblog').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('myblog').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
    
    function othersExpand(){
        var x = document.querySelectorAll("#others-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('others').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('others').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }

    function promotionExpand(){
        var x = document.querySelectorAll("#promotion-sub");
        var status = x[0].style.display == "block" ? "none" : "block" ;
        if(status == "none")
            document.getElementById('promotion').className= "fa fa-arrow-circle-up";
        else
            document.getElementById('promotion').className= "fa fa-arrow-circle-down";

        for (i = 0; i < x.length; i++) {
            x[i].style.display = status;
        }
    }
  //WORK FOR CLOSING THE FACEBOOK POPUP WHILE LINKING FACEBOOK PAGE
  if (window.opener!= null) {
  
    <?php if (!empty($_GET['redirect_fb'])) : ?>
                window.opener.location.reload(false);
               close();
             
    <?php endif; ?>
}
</script>

<style>
    .seaocore_db_head > h3 {
        color: #000;
        padding: 12px 10px;
        margin-bottom: 0;
        background-color: rgba(0, 0, 0, 0.25) !important;
    }
</style>
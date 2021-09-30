<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _DashboardNavigation.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$Sitevideo_dashboard_content = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_dashboard_content');
$Sitevideo_dashboard_admin = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_dashboard_admin');
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css')->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo_dashboard.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
?>

<?php
$channel = $this->channel;
$viewer = Engine_Api::_()->user()->getViewer();
$params['channel_type_title'] = $this->translate('Channels');
$params['dashboard'] = $this->translate('Dashboard');
//SET META TITLE
Engine_Api::_()->sitevideo()->setMetaTitles($params);
if ($this->TabActive != "edit"):
    ?>
    <?php if (!Zend_Controller_Front::getInstance()->getRequest()->getParam('isajax')): ?>
        <?php
        $this->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitevideo_main");
        include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/navigation_views.tpl';
        ?>
    <?php endif; ?>
<?php endif; ?>
<div class="layout_middle <?php if (Engine_Api::_()->hasModuleBootstrap('spectacular')): ?> spectacular_dashboard <?php endif; ?>">
    <div class="generic_layout_container o_hidden"> 
        <div class='seaocore_db_tabs'>

            <?php if (count($Sitevideo_dashboard_content)): ?>
                <ul>

                    <?php
                    foreach ($Sitevideo_dashboard_content as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                            'reset_params', 'route', 'module', 'controller', 'action', 'type',
                            'visible', 'label', 'href')));
                        if (!isset($attribs['active'])) {
                            $attribs['active'] = false;
                        }
                        ?>
                        <li<?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                            <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="sitevideo_dashboard_info clr">
                <div class="sitevideo_dashboard_info_image sitevideo_thumb_wrapper prelative">
                    <?php echo $this->htmlLink($this->channel->getHref(),$this->itemBackgroundPhoto($this->channel, 'thumb.profile', $this->channel->getTitle(), array('tag'=> 'i',))) ?>
                     <?php if ($this->channel->featured == 1): ?>
                        <div class="sitevideo_featured"><?php echo $this->translate('Featured'); ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($this->channel->sponsored == 1): ?>
                    <div class="sitevideo_sponsored" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.sponsoredcolor', '#FC0505'); ?>">
                        <?php echo $this->translate('SPONSORED'); ?>                 
                    </div>
                <?php endif; ?>
            </div> 
            <div class="mtop10" >
            <?php echo $this->content()->renderWidget('sitevideo.post-new-video',array('upload_button'=>1)); ?>
            </div>
        </div>
        
        <script type="text/javascript">

            en4.core.runonce.add(function () {
                var element = $(event.target);
                if (element.tagName.toLowerCase() == 'a') {
                    element = element.getParent('li');
                }
            });

            if ($$('.ajax_dashboard_enabled')) {
                en4.core.runonce.add(function () {
                    var globalContentElement = en4.seaocore.getDomElements('content');
                    $$('.ajax_dashboard_enabled').addEvent('click', function (event) {
                        var element = $(event.target);
                        event.stop();
                        var ulel = this.getParent('ul');
                        $(globalContentElement).getElement('.sitevideo_dashboard_content').innerHTML = '<div class="seaocore_content_loader"></div>';
                        ulel.getElements('li').removeClass('selected');

                        if (element.tagName.toLowerCase() == 'a') {
                            element = element.getParent('li');
                        }

                        element.addClass('selected');
                        showAjaxBasedContent(this.href);
                    });
                });
            }

            function showAjaxBasedContent(url) {

                if (history.pushState) {
                    history.pushState({}, document.title, url);
                } else {
                    window.location.hash = url;
                }

                en4.core.request.send(new Request.HTML({
                    url: url,
                    'method': 'get',
                    data: {
                        format: 'html',
                        'isajax': 1
                    }, onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        var globalContentElement = en4.seaocore.getDomElements('content');
                        $(globalContentElement).innerHTML = responseHTML;
                        Smoothbox.bind($(globalContentElement));

                        if (SmoothboxSEAO) {
                            SmoothboxSEAO.bind($(globalContentElement));
                        }

                        en4.core.runonce.trigger();
                        if (window.InitiateAction) {
                            InitiateAction();
                        }
                    }
                }));
            }

            var requestActive = false;
            window.addEvent('load', function () {
                InitiateAction();
            });

            var InitiateAction = function () {
                formElement = $$('.global_form')[0];
                if (typeof formElement != 'undefined') {
                    formElement.addEvent('submit', function (event) {
                        if (typeof submitformajax != 'undefined' && submitformajax == 1) {
                            submitformajax = 0;
                            event.stop();
                            Savevalues();
                        }
                    })
                }
            }

            var Savevalues = function () {
                if (requestActive)
                    return;

                requestActive = true;
                var globalContentElement = en4.seaocore.getDomElements('content');
                var pageurl = $(globalContentElement).getElement('.global_form').action;

                currentValues = formElement.toQueryString();
                $('show_tab_content_child').innerHTML = '<div class="seaocore_content_loader"></div>';
                if (typeof page_url != 'undefined') {
                    var param = (currentValues ? currentValues + '&' : '') + 'isajax=1&format=html&page_url=' + page_url;
                }
                else {
                    var param = (currentValues ? currentValues + '&' : '') + 'isajax=1&format=html';
                }

                var request = new Request.HTML({
                    url: pageurl,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $(globalContentElement).innerHTML = responseHTML;
                        InitiateAction();
                        requestActive = false;
                    }
                });
                request.send(param);
            }

            function submitSession(id) {
                document.getElementById("event_id_session").value = id;
                document.getElementById("setSession_form").submit();
            }

            function owner(thisobj) {
                var Obj_Url = thisobj.href;
                Smoothbox.open(Obj_Url);
            }
        </script>

<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="vrtcl-scroll-menu-wrap _hide" id="vrtcl-scroll-menu-wrap">
  <div class="vt-menu-list">
    <ul class="_overlay">
    </ul></div>
</div>

<script type="text/javascript">
  (function () {
    var container = $('vrtcl-scroll-menu-wrap').getParent('.generic_layout_container').getParent('.generic_layout_container');
    $('vrtcl-scroll-menu-wrap').inject(document.body);
    $$('.layout_sitecoretheme_scroll_content_menus').destroy();
    en4.core.runonce.add(function () {
      setTimeout(function () {
        container.getElements('.generic_layout_container').each(function (el, key) {
          try {
            if (el.firstChild && el.firstChild.get('tag') == 'h3' && !el.getParent('.layout_core_container_tabs')) {
              if (!el.get('id')) {
                el.set('id', 'vt-scroll-conent-' + key);
              }
              new Element('li', {
                html: '<a  href="javascript:void(0);" data-scroll-id="' + el.get('id') + '"><span>' + el.firstChild.get('text') + '</span>'
              }).inject($('vrtcl-scroll-menu-wrap').getElement('ul'));
            }
          } catch (e) {
//          console.log(el.firstChild);
          }
        });
        var fx = new Fx.Scroll(window, {
          offset: {
            'y': -50
          },
          transition: Fx.Transitions.Quad.easeInOut
        });
        if ($('vrtcl-scroll-menu-wrap').getElements('ul > li').length == 0) {
          $('vrtcl-scroll-menu-wrap').destroy();
        }

        $('vrtcl-scroll-menu-wrap').getElements('ul > li > a').addEvent('click', function (event) {
          var el = $(event.target);
          if (el.get('tag') != 'a') {
            el = el.getParent('a');
          }
          fx.toElement($(el.get('data-scroll-id')), 'y');
        });
      }, 500);
      var showhide = function (flag) {
        if (flag) {
          if ($('vrtcl-scroll-menu-wrap').hasClass('_hide')) {
            $('vrtcl-scroll-menu-wrap').removeClass('_hide');
          }
        } else {
          if (!$('vrtcl-scroll-menu-wrap').hasClass('_hide')) {
            $('vrtcl-scroll-menu-wrap').addClass('_hide');
          }
        }
      };
      showhide(false);
      var lastScroll = 0;
      var hasMouseEnter = false;
      $('vrtcl-scroll-menu-wrap').addEvent('mouseenter', function () {
        hasMouseEnter = true;
      });
      $('vrtcl-scroll-menu-wrap').addEvent('mouseleave', function () {
        hasMouseEnter = false;
        lastScroll = (new Date()).getTime();
      });
      window.addEvent('scroll', function () {
        showhide(true);
        lastScroll = (new Date()).getTime();
      });
      setInterval(function () {
        if (!hasMouseEnter) {
          showhide(lastScroll && ((new Date()).getTime() - lastScroll) < 4 * 1000);
        }
      }, 1000);
    });
  })();
</script>
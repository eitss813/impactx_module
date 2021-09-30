<div id="verifyed_pages_ajax_responsed">
  <?php
  if (!empty($this->is_ajax) && empty($this->loadFlage)):
    $widId = !empty($this->identity) ? $this->identity : null;
    ?>
    <script type="text/javascript">
      window.addEvent('domready', function() {
        var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $widId) ?>;
        var request = new Request.HTML({
          url: url,
          method: 'get',
          data: {
            format: 'html',
            'loadFlage': 1,
            'user_id': '<?php echo $this->user_id; ?>',
          },
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            var parser = new DOMParser();
            var domContent = parser.parseFromString(responseHTML,"text/xml");

            if($("verifyed_pages_ajax_responsed"))
              // $("verifyed_pages_ajax_responsed").innerHTML = domContent.getElementById('verifyed_pages_ajax_responsed').innerHTML;
            $$('.generic_layout_container .layout_sitepage_verified_pages')[0].innerHTML = responseHTML;
          }
        });
        request.send();
      });
    </script>

    <?php
  endif;


  if (!empty($this->showContent)):
    ?>
    <?php if(!$this->title): ?>
      <!-- <h3><?php // echo $this->translate("Most Verified Pages"); ?></h3> -->
    <?php endif; ?>
    <ul>
        <?php if (COUNT($this->paginator)): ?>
          <?php foreach ($this->paginator as $verification): ?>
            <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $verification['resource_id']); ?>
            <li>
              <?php echo $this->htmlLink($sitepage->getHref(), $this->itemPhoto($sitepage, 'thumb.icon', ''), array('class' => 'verifiedpages_thumb')) ?>
              <?php if ($verification['verifyCount'] >= $this->verify_limit): ?>
                <i class="sitepage_list_verify_label"></i>
              <?php endif; ?>
              <div class="verifiedpages_info">
                <div class='verifiedpages_name'>
                  <?php echo $this->htmlLink($sitepage->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitepage->getTitle(), $this->title_truncation), array('title' => $sitepage->getTitle())); ?>                  
                </div>

                <div class='verifiedpages_friends'>
                  <?php echo $this->translate("verified by"); ?>&nbsp;<a href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'verify', 'action' => 'content-verify-member-list', 'resource_id' => $verification['resource_id']), 'default', true) ?>');"><?php echo $this->translate(array('%s member', '%s members', $verification['verifyCount']), $this->locale()->toNumber($verification['verifyCount'])) ?></a>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <script type="text/javascript">
            if($$(".layout_sitepage_verified_pages"))
              $$(".layout_sitepage_verified_pages").destroy();
          </script>
        <?php endif; ?>
      </ul>
  <?php endif; ?>
</div>
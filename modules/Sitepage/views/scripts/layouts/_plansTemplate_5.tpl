
<?php
  $request = Zend_Controller_Front::getInstance()->getRequest();
  $action = $request->getActionName();
  $param = $request->getParam('format');

  $templateData = ($action == 'preview') ? $this->templateData : Engine_Api::_()->getApi('core','sitepage')->getTemplateData() ;
  $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.information');
  $best_choice = $templateData['best_choice'];
  $templateStyle = $templateData['templateStyle'];
  $features = $templateData['features'];
  $fieldValues = $templateData['fieldValues'];

  $tick_image = $templateStyle['tick_image']['value'];
  $cross_image = $templateStyle['cross_image']['value'];

  $footer_enabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.footer.enabled', '0');
  if ($footer_enabled == '1')
    $footer_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.footer.text', '');
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/reset_row_height.js'); ?>

<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet"> 
<link rel="stylesheet" type="text/css" href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/_plansTemplate_5.css'; ?>">

<form method="get" action="" class="" enctype="application/x-www-form-urlencoded" id="signup" name="signup">
    <!-- <div class="container"> -->
    <div class="plans_template_5">
    <div class="row" id="row">
      <div class="left_side"> 
        <div class="features">
            <div class="head">
              <h1>Features</h1>
            </div>
            <div class="price">
              <h2>Price</h2>
            </div>
            <div class="content">
              <ul>
                <?php $rowCount = 1;?>
                <?php foreach($features as $feature): ?>
                  <li class="<?php echo "compareField details_row_".$rowCount++ ?>">
                    <?php echo $feature['label']; ?>
                   </li>
                <?php endforeach;?>
              </ul>
            </div>
        </div>
      </div>

      <div id="right_layout">
        <div id="scroll-areas-main">
          <div id="list-scroll-areas" style="overflow:hidden;">
            <div class="right_side" id="right_side">
              <?php if(!empty($this->check)): ?>
              <?php foreach( $this->form->getPackages() as $package_id => $package ) : ?>
              <div class="columns <?php echo ($package->package_id == $best_choice) ? 'table2' : 'table1' ;?>">
                  <div class="<?php echo ($package->package_id == $best_choice) ? 'head2' : 'head1' ;?>">
                    <h1 title="<?php echo $package->title?>" ><?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($package->title, 15)); ?>
                    </h1>
                  </div>
                  <?php if($package->package_id == $best_choice): ?>
                    <div class="mini_head">
                      <p><?php echo $this->translate(($templateStyle['template_mostpopularblock_text']['value'] == null) ? '' : $templateStyle['template_mostpopularblock_text']['value'] ) ; ?></p>
                    </div>
                  <?php endif; ?>
                  <div class="<?php echo ($package->package_id == $best_choice) ? 'price2' : 'price1' ;?>">
                    <h2>
                      <sup><?php echo Engine_Api::_()->getApi('core','sitepage')->getCurrencySymbol(); ?></sup> <?php echo $this->translate($package->price); ?><span> <?php echo Engine_Api::_()->getApi('core','sitepage')->getReccurenceDuration($package->package_id); ?></span>
                    </h2>
                  </div>
                  <div class="content">
                     <ul>
                      <?php $rowCount = 1;?>
                        <?php foreach($features as $fields): ?>
                          <?php $value = $fieldValues[$fields['field_id']][$package_id]; 
                            echo "<li class = ' details_row_".$rowCount++."'>";
                            switch ($value) {
                              case 'yes':
                                echo "<img class='tick_image' src=".$tick_image.">";
                                break;

                              case 'no':
                                echo "<img class='cross_image' src=".$cross_image.">";
                                break;

                              case 'N/A':
                                echo '-';
                                break;
                              
                              default:
                                echo $this->translate( ($value == null) ? '-' : Engine_Api::_()->seaocore()->seaocoreTruncateText($value, 15));
                                break;
                            }
                            echo "</li>";
                          ?> 
                        <?php endforeach; ?>
                        <input type="button" class="<?php echo ($package->package_id == $best_choice) ? 'button2' : 'button1' ;?>" value="<?php echo  $this->translate($templateStyle['template_button_text']['value'] == null ? '' : $templateStyle['template_button_text']['value']); ?>" id="<?php echo $package_id;?>" onclick="submitForm(this.id)">
                     </ul>
                  </div>
              </div>
              <?php endforeach; ?>  
            </div>
          </div>
        </div>
        <div class="scrollbarArea" id ="scrollbar_after">   </div>
      </div>
    </div>
    </div>
  </form>
  <div class="l_banner">
    <?php if ($footer_enabled == '1' && !empty($footer_text)): ?>
      <?php echo $footer_text; ?>
    <?php endif; ?>
  </div>
              <?php else : ?>
              <?php foreach( $this->paginator as $package_id => $package ) : ?>
              <div class="columns <?php echo ($package->package_id == $best_choice) ? 'table2' : 'table1' ;?>">
                  <div class="<?php echo ($package->package_id == $best_choice) ? 'head2' : 'head1' ;?>">
                    <h1 title="<?php echo $package->title?>"><?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($package->title, 15)); ?>                   
                  </h1>
                  </div>
                  <?php if($package->package_id == $best_choice): ?>
                    <div class="mini_head">
                      <p><?php echo $this->translate(($templateStyle['template_mostpopularblock_text']['value'] == null) ? '' : $templateStyle['template_mostpopularblock_text']['value'] ) ; ?></p>
                    </div>
                  <?php endif; ?>
                  <div class="<?php echo ($package->package_id == $best_choice) ? 'price2' : 'price1' ;?>">
                    <h2>
                      <sup><?php echo Engine_Api::_()->getApi('core','sitepage')->getCurrencySymbol(); ?></sup> <?php echo $this->translate($package->price); ?><span> <?php echo Engine_Api::_()->getApi('core','sitepage')->getReccurenceDuration($package->package_id); ?></span>
                    </h2>
                  </div>
                  <div class="content">
                     <ul><?php $rowCount = 1;?>
                        <?php foreach($features as $fields): ?>
                          <?php $value = $fieldValues[$fields['field_id']][$package->package_id]; 
                            echo "<li class = ' details_row_".$rowCount++."'>";
                            switch ($value) {
                              case 'yes':
                                echo "<img class='tick_image' src=".$tick_image.">";
                                break;

                              case 'no':
                                echo "<img class='cross_image' src=".$cross_image.">";
                                break;

                              case 'N/A':
                                echo '-';
                                break;
                              
                              default:
                               echo $this->translate( ($value == null) ? '-' : Engine_Api::_()->seaocore()->seaocoreTruncateText($value, 15));
                                break;
                            }
                            echo "</li>";
                          ?> 
                        <?php endforeach; ?>
                        <input type="button" class="<?php echo ($package->package_id == $best_choice) ? 'button2' : 'button1' ;?>" value="<?php echo  $this->translate($templateStyle['template_button_text']['value'] == null ? '' : $templateStyle['template_button_text']['value']); ?>" id="<?php echo $package->package_id;?>" onclick="submitForm(this.id)">
                     </ul>
                  </div>
              </div>
              <?php endforeach; ?>  
            </div>
          </div>
        </div>
        <div class="scrollbarArea" id ="scrollbar_after">   </div>
      </div>
    </div>
  </form>
  <div class="l_banner">
    <?php if ($footer_enabled == '1' && !empty($footer_text)): ?>
      <?php echo $footer_text; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<style type="text/css">
.head1>h1 , .head2>h1
{
  color: #<?php echo $templateStyle['template_packagename_textcolor']['value']; ?>;
  font-style: <?php echo $templateStyle['template_packagename_textstyle']['value']; ?>;
  font-size: <?php echo $templateStyle['template_packagename_textsize']['value']; ?>px;
  font-family: <?php echo $templateStyle['template_packagename_textfamily']['value']; ?>;
}
.head1, .table1>.content>.button1
{
  background-color: #<?php echo $templateStyle['template_packagename_bgcolor_normal']['value']; ?>;
}
.head2, .table2>.content2>.button2
{
  background-color: #<?php echo $templateStyle['template_packagename_bgcolor_popular']['value']; ?>;
}
.row>.table2
{
  border: 5px solid #<?php echo $templateStyle['template_packagename_bgcolor_popular']['value']; ?>;
}
.container, .row 
{
  background-color: #<?php echo $templateStyle['template_bgcolor']['value']; ?>;
}
.content ul li, .content ul li b
{
  font-size: <?php echo $templateStyle['template_feature_textsize']['value']; ?>px;
  font-style: <?php echo $templateStyle['template_feature_textstyle']['value']; ?>;
  font-family: <?php echo $templateStyle['template_feature_textfamily']['value']; ?>;
}
.content>ul>.button1, .content>ul>.button2
{
  font-size: <?php echo $templateStyle['template_button_textsize']['value']; ?>px ;
  font-style: <?php echo $templateStyle['template_button_textstyle']['value']; ?>;
  font-family: <?php echo $templateStyle['template_button_textfamily']['value']; ?>;
}
.table1>.content>.button1
{
  border:1px solid #<?php echo $templateStyle['template_packagename_bgcolor_normal']['value']; ?>;
}
.table2>.content>.button2
{
  border:1px solid #<?php echo $templateStyle['template_packagename_bgcolor_popular']['value']; ?>;
}
.price1>h2 , .price2>h2
{
  font-size: <?php echo $templateStyle['template_price_textsize']['value']; ?>px ;
  font-style: <?php echo $templateStyle['template_price_textstyle']['value']; ?>;
  font-family: <?php echo $templateStyle['template_price_textfamily']['value']; ?>;
}
.price1>h2
{
  color: #<?php echo $templateStyle['template_price_textcolor']['value']; ?>;
}
.price2>h2
{
  color: #<?php echo $templateStyle['template_packagename_bgcolor_popular']['value']; ?>;
}
.mini_head
{
  background-color: #<?php echo $templateStyle['template_mostpopularblock_bgcolor']['value'] ; ?>;
  color: #<?php echo $templateStyle['template_mostpopularblock_textcolor']['value'] ; ?>;
  font-size: <?php echo $templateStyle['template_mostpopularblock_textsize']['value'] ; ?>;
  font-style: <?php echo $templateStyle['template_mostpopularblock_textstyle']['value'] ; ?>;
  font-family: <?php echo $templateStyle['template_mostpopularblock_textfamily']['value'] ; ?>;
}
.features>.head
{
  background-color: #<?php echo $templateStyle['template_featurelabel_bgcolor']['value']; ?>;
}
.features>.content
{
  color: #<?php echo $templateStyle['template_featurelabel_textcolor']['value']; ?>;
  font-family: <?php echo $templateStyle['template_featurelabel_textfamily']['value']; ?>;
  font-size:  <?php echo $templateStyle['template_featurelabel_textsize']['value']; ?>px ;
  font-style: <?php echo $templateStyle['template_featurelabel_textstyle']['value']; ?>;
}
.horizontalThumb {
  background-color: #<?php echo $templateStyle['template_packagename_bgcolor_normal']['value']; ?>;
}
.horizontalTrack {
  background-color: ;
}
.cross_image , .tick_image
{
  height:  <?php echo $templateStyle['template_feature_textsize']['value']; ?>px;
  width:  <?php echo $templateStyle['template_feature_textsize']['value']; ?>px;
}
.banner {
  background-color: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.header.color', '#A9A9A9'); ?>;
}
.content ul li b
{
  font-weight: 700 !important;
}
</style>


<script type="text/javascript">
  function submitForm(id)
  {
    window.location.href = '<?php echo $url; ?>' + '/id/' + id;
  }
</script>

<script type="text/javascript">
  var children;
  var containerWidth; 
  var scrollbarEnable;
  var isAjax = "<?php echo $this->is_ajax; ?>";

  var resetContent = function () {
    var width = ( containerWidth / children);
    width = width - 2;
    if (width < 250)
      width = 250;
    width++;

    if ( containerWidth < width ) {
      width = containerWidth - 2;
      $(scrollbar_after).setStyle('max-width',containerWidth+'px');
    }

    var numberOfItem = (containerWidth / width);
    var numberOfItemFloor = Math.floor(numberOfItem);
    var extra = (width * (numberOfItem - numberOfItemFloor) / numberOfItemFloor);
    width = width + extra;
    scrollbarEnable = (children > numberOfItemFloor) ? true : false; 

    $('right_side').setStyle('width', (width * children) + 'px');
    $('right_side').getElements('.columns').each(function (el) {
      width = (width > 300 && numberOfItemFloor >= children ) ? 300 : width;
      // marginEnabled = (numberOfItemFloor >= children) ? 5 : 2;
      el.setStyle('width', width - 3.5 + 'px');
    });
    if((width * children) <= containerWidth){
      $('scrollbar_after').setStyle('display','none');
    }
  };
  
  var smoothbox = <?php echo ($param == 'smoothbox') ? 1 : 0; ?>;
  en4.core.runonce.add(function () { 
  
      var check = typeof SEAOMooHorizontalScrollBar;
      
      if( check === "undefined" || isAjax == 1 ) { 
          Asset.javascript('<?php echo ($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js");?>',{
              id: 'myScript',
              onLoad: function(){
                children = $("right_side").childElementCount;
                containerWidth = $('right_layout').offsetWidth;
                scrollbarEnable = true;
                if (smoothbox) {
                  $$('#row').setStyle('width','1000px');
                  containerWidth = 800;
                }
                
                resetContent();
                // (function () {
                  // $('list-scroll-areas').setStyle('height', $('scroll-content').offsetHeight + 'px');
                  $('list-scroll-areas').setStyle('width', containerWidth + 'px');
                  scrollBarContentArea = new SEAOMooHorizontalScrollBar('scroll-areas-main', 'list-scroll-areas', {
                    'arrows': false,
                    'horizontalScroll': true,
                    'horizontalScrollElement': 'scrollbar_after',
                    'horizontalScrollBefore': false,
                  });

                  $$('#scroll-areas-main').setStyle('width',containerWidth);
                  if (!scrollbarEnable)
                    $$('#scrollbar_after').setStyle('display','none');
                // });
              }
          });
      } else {
        children = $("right_side").childElementCount;
        containerWidth = $('right_layout').offsetWidth;
        scrollbarEnable = true;
        if (smoothbox) {
          $$('#row').setStyle('width','1000px');
          containerWidth = 800;
        }

        en4.core.runonce.add(function () {
          resetContent();
          (function () {
            // $('list-scroll-areas').setStyle('height', $('scroll-content').offsetHeight + 'px');
            $('list-scroll-areas').setStyle('width', containerWidth + 'px');
            scrollBarContentArea = new SEAOMooHorizontalScrollBar('scroll-areas-main', 'list-scroll-areas', {
              'arrows': false,
              'horizontalScroll': true,
              'horizontalScrollElement': 'scrollbar_after',
              'horizontalScrollBefore': false,
            });
            $$('#scroll-areas-main').setStyle('width',containerWidth);
            if (!scrollbarEnable)
              $$('#scrollbar_after').setStyle('display','none');
          }).delay(700);
        });            
      }
  });
</script>


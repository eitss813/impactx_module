<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitevideo
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: upload.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<style>
  #video_upload_form_right_content ul.form-options-wrapper li:last-child{
    /*  height: 100px; */
  }
</style>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
//'topLevelId' => (int) @$this->topLevelId,
//'topLevelValue' => (int) @$this->topLevelValue
))
?>

<script type="text/javascript">

  setTimeout(function () {

    window.addEvent('domready', function () {

      if ($('sitevideo_location') && (('<?php echo!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0); ?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0); ?>' && '<?php echo!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
      if (typeof google != 'undefined') {
        var autocomplete = new google.maps.places.Autocomplete(document.getElementById('sitevideo_location'));
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            return;
          }

          var address = '', country = '', state = '', zip_code = '', city = '';
          var data = {};
          if (place.address_components) {
            var len_add = place.address_components.length;
            for (var i = 0; i < len_add; i++) {
              var types_location = place.address_components[i]['types'][0];
              if (types_location === 'country') {
                country = place.address_components[i]['long_name'];
              } else if (types_location === 'administrative_area_level_1') {
                state = place.address_components[i]['long_name'];
              } else if (types_location === 'administrative_area_level_2') {
                city = place.address_components[i]['long_name'];
              } else if (types_location === 'zip_code') {
                zip_code = place.address_components[i]['long_name'];
              } else if (types_location === 'street_address') {
                if (address === '')
                  address = place.address_components[i]['long_name'];
                else
                  address = address + ',' + place.address_components[i]['long_name'];
              } else if (types_location === 'locality') {
                if (address === '')
                  address = place.address_components[i]['long_name'];
                else
                  address = address + ',' + place.address_components[i]['long_name'];
              } else if (types_location === 'route') {
                if (address === '')
                  address = place.address_components[i]['long_name'];
                else
                  address = address + ',' + place.address_components[i]['long_name'];
              } else if (types_location === 'sublocality') {
                if (address === '')
                  address = place.address_components[i]['long_name'];
                else
                  address = address + ',' + place.address_components[i]['long_name'];
              }
            }
          }
          var locationParams = '{"location" :"' + document.getElementById('sitevideo_location').value + '","latitude" :"' + place.geometry.location.lat() + '","longitude":"' + place.geometry.location.lng() + '","formatted_address":"' + place.formatted_address + '","address":"' + address + '","country":"' + country + '","state":"' + state + '","zip_code":"' + zip_code + '","city":"' + city + '"}';
          data.name = place.name;
          data.google_id = place.id;
          data.latitude = place.geometry.location.lat();
          data.longitude = place.geometry.location.lng();
          data.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
          data.icon = place.icon;
          data.types = place.types.join(',');
          data.prefixadd = data.types.indexOf('establishment') > -1 ? en4.core.language.translate('at') : en4.core.language.translate('in');
          data.resource_guid = 0;
          data.type = 'place';
          data.reference = place.reference;
          var dataHash = new Hash(data);
          dataHashStr = dataHash.toQueryString();
          document.getElementById('dataParams').value = dataHashStr;
          document.getElementById('locationParams').value = locationParams;
        });
      }
      }
      var tagsUrl = '<?php echo $this->url(array("controller" => "tag", "action" => "suggest"), "default", true) ?>';
      var validationUrl = '<?php echo $this->url(array("action" => "validation"), "sitevideo_video_general", true) ?>';
      var validationErrorMessage = "<?php echo $this->string()->escapeJavascript($this->translate("
      We could not find a video there - please check the URL and try again.If you are sure that the URL is valid, please click % s to continue.", " < a href = 'javascript:void(0);' onclick = 'javascript:ignoreValidation();' > " . $this->translate("here") . " < /a>")); ?>";
    var checkingUrlMessage = '<?php echo $this->string()->escapeJavascript($this->translate("Checking URL...")) ?>';
      var current_code;
      var ignoreValidation = window.ignoreValidation = function () {
        $('upload-wrapper').style.display = "block";
        $('upload').style.display = "block";
        $('validation').style.display = "none";
        $('code').value = current_code;
        $('ignore').value = true;
      };
      var updateTextFields = window.updateTextFields = function (value) {
        val = value;
        var video_element = document.getElementById("type-" + value);
        var url_element = document.getElementById("url-wrapper");
        var thumbnail_element = document.getElementById("thumbnail-wrapper");
        var file_element = document.getElementById("file-wrapper");
        var stream_element = document.getElementById("stream-wrapper");
        $('url').getNext().set('text', '<?php echo $this->string()->escapeJavascript($this->translate('Paste the web address of the video here.')) ; ?>');
        var submit_element = document.getElementById("upload-wrapper");
        if ($('validation'))
          $('validation').style.display = "none";
        // clear url if input field on change
        $('code').value = "";
        submit_element.style.display = "none";
        url_element.style.display = "none";
        if (stream_element)
          stream_element.style.display = "none";
        file_element.style.display = "none";
        if (video_element) {
          // If video source is empty
          if (video_element.value == 0) {
            $('url').value = "";
            file_element.style.display = "none";
            if (stream_element)
              stream_element.style.display = "none";
            url_element.style.display = "none";
            if (thumbnail_element)
            thumbnail_element.style.display = "none";
            return;
          } else if ($('code').value && $('url').value) {
            $('type-wrapper').style.display = "none";
            file_element.style.display = "none";
            if (stream_element)
              stream_element.style.display = "none";
            if (thumbnail_element)
            thumbnail_element.style.display = "none";
            $('upload-wrapper').style.display = "block";
            return;
          } else if (video_element.value == 'youtube' || video_element.value == 'vimeo' || video_element.value == 'dailymotion' || video_element.value == 'iframely') {
            // If video source is youtube or vimeo
            $('url').value = "";
            $('code').value = "";
            file_element.style.display = "none";
            if (stream_element)
              stream_element.style.display = "none";
            url_element.style.display = "block";
            if (thumbnail_element)
            thumbnail_element.style.display = "none";
            $('url').style.display = "block";
            $('url').getNext().style.display = "block";
            return;
          } else if (video_element.value == 'embedcode')
          {
            $('url').getNext().set('text', 'Paste the iframe embed code of the video here.');
            $('url').value = "";
            $('code').value = "";
            file_element.style.display = "none";
            if (stream_element)
              stream_element.style.display = "none";
            url_element.style.display = "block";
            $('url').style.display = "block";
            $('url').getNext().style.display = "block";
            return;
          } else if (video_element.value == 'upload') {
            // If video source is from computer
            $('url').value = "";
            $('code').value = "";
            file_element.style.display = "block";
            if (stream_element)
              stream_element.style.display = "none";
            url_element.style.display = "none";
            if (thumbnail_element)
            thumbnail_element.style.display = "none";
            // $('demo-status').style.display = "block";
            // if (flashEnable == false) {
            //   $('demo-status').style.display = "none";
            // }
            return;
           } else if (video_element.value == 'stream') {
            // If video source is from computer
            $('url').value = "";
            $('code').value = "";
            file_element.style.display = "none";
            if (stream_element)
              stream_element.style.display = "block";
            url_element.style.display = "none";
            if (thumbnail_element)
            thumbnail_element.style.display = "none";
            // $('demo-status').style.display = "block";
            // if (flashEnable == false) {
            //   $('demo-status').style.display = "none";
            // }
            return;
          } else if ($('id').value) {
            // if there is video_id that means this form is returned from uploading 
            // because some other required field
            $('type-wrapper').style.display = "none";
            file_element.style.display = "none";
            if (stream_element)
              stream_element.style.display = "none";
            if (thumbnail_element)
            thumbnail_element.style.display = "none";
            $('upload-wrapper').style.display = "block";
            return;
          }
        }
      };
      var video = window.video = {
        active: false,
        debug: false,
        currentUrl: null,
        currentTitle: null,
        currentDescription: null,
        currentImage: 0,
        currentImageSrc: null,
        imagesLoading: 0,
        images: [],
        maxAspect: (10 / 3), //(5 / 2), //3.1,

        minAspect: (3 / 10), //(2 / 5), //(1 / 3.1),

        minSize: 50,
        maxPixels: 500000,
        monitorInterval: null,
        monitorLastActivity: false,
        monitorDelay: 500,
        maxImageLoading: 5000,
        attach: function () {
          var bind = this;
          $('url').addEvent('keyup', function () {
            bind.monitorLastActivity = (new Date).valueOf();
          });
          var url_element = document.getElementById("url-element");
          var myElement = new Element("p");
          myElement.innerHTML = "test";
          myElement.addClass("description");
          myElement.id = "validation";
          myElement.style.display = "none";
          url_element.appendChild(myElement);
          var body = $('url');
          var lastBody = '';
          var lastMatch = '';
          (function () {
            var val;
            // get list of radio buttons with specified name
            if (document.getElementById('form-upload')) {
              var radios = document.getElementById('form-upload').elements['type'];
              // loop through list of radio buttons
              for (var i = 0, len = radios.length; i < len; i++) {
                if (radios[i].checked) { // radio checked?
                  val = radios[i].value; // if so, hold its value in val
                  break; // and break out of for loop
                }
              }
            }
            var video_element = $('type-' + val);
            // Ignore if no change or url matches
            if (body.value == lastBody || bind.currentUrl) {
              return;
            }

            // Ignore if delay not met yet
            if ((new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay) {
              return;
            }

            // Check for link
            if (video_element.value == 'embedcode') {
              var m = body.value.match(/(https?:\/\/|\/\/)([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
            } else {
              var m = body.value.match(/https?:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
            }
            if ($type(m) && $type(m[0]) && lastMatch != m[0]) {
              if (video_element.value == 'youtube') {
                video.youtube(body.value);
              } else if (video_element.value == 'vimeo') {
                video.vimeo(body.value);
              } else if (video_element.value == 'dailymotion') {
                video.dailymotion(body.value);
              } else if (video_element.value == 'iframely') {
                video.iframely(body.value);
              } else {
                video.embed(body.value);
              }
            }
            lastBody = body.value;
          }).periodical(250);
        },
        youtube: function (url) {
          // extract v from url
          var myURI = new URI(url);
          var youtube_code = myURI.get('data')['v'];
          if (youtube_code === undefined) {
            youtube_code = myURI.get('file');
          }
          if (youtube_code) {
            (new Request.HTML({
              'format': 'html',
              'url': validationUrl,
              'data': {
                'ajax': true,
                'code': youtube_code,
                'type': 'youtube'
              },
              'onRequest': function () {
                $('validation').style.display = "block";
                $('validation').innerHTML = checkingUrlMessage;
                $('upload-wrapper').style.display = "none";
              },
              'onSuccess': function (responseTree, responseElements, responseHTML, responseJavaScript) {
                if (valid) {
                  $('upload-wrapper').style.display = "block";
                  $('upload').style.display = "block";
                  $('validation').style.display = "none";
                  $('code').value = youtube_code;
                  if ($('sitevideo_add_video_content').getElementById('videotitle').value == '')
                    $('sitevideo_add_video_content').getElementById('title').value = informationVideoContent.title;
                  if ($('sitevideo_add_video_content').getElementById('videodescription').value == '')
                    $('sitevideo_add_video_content').getElementById('description').value = informationVideoContent.description;
                } else {
                  $('upload-wrapper').style.display = "none";
                  current_code = youtube_code;
                  $('validation').innerHTML = validationErrorMessage;
                }
              }
            })).send();
          }
        },
        vimeo: function (url) {
          var myURI = new URI(url);
          var vimeo_code = myURI.get('file');
          if (vimeo_code.length > 0) {
            (new Request.HTML({
              'format': 'html',
              'url': validationUrl,
              'data': {
                'ajax': true,
                'code': vimeo_code,
                'type': 'vimeo'
              },
              'onRequest': function () {
                $('validation').style.display = "block";
                $('validation').innerHTML = checkingUrlMessage;
                $('upload-wrapper').style.display = "none";
              },
              'onSuccess': function (responseTree, responseElements, responseHTML, responseJavaScript) {
                if (valid) {
                  $('upload-wrapper').style.display = "block";
                  $('upload').style.display = "block";
                  $('validation').style.display = "none";
                  $('code').value = vimeo_code;
                  if ($('sitevideo_add_video_content').getElementById('videotitle').value == '')
                    $('sitevideo_add_video_content').getElementById('title').value = informationVideoContent.title[0];
                  if ($('sitevideo_add_video_content').getElementById('videodescription').value == '')
                    $('sitevideo_add_video_content').getElementById('description').value = informationVideoContent.description[0];
                } else {
                  $('upload-wrapper').style.display = "none";
                  current_code = vimeo_code;
                  $('validation').innerHTML = validationErrorMessage;
                }
              }
            })).send();
          }
        },
        dailymotion: function (url) {
          var myURI = new URI(url);
          var dailymotion_code = myURI.get('file');
          //Here url will be used to validate the correctness of url
          if (dailymotion_code.length > 0)
          {
            (new Request.HTML({
              'format': 'html',
              'url': validationUrl,
              'data': {
                'ajax': true,
                'code': dailymotion_code,
                'type': 'dailymotion'
              },
              'onRequest': function () {
                $('validation').style.display = "block";
                $('validation').innerHTML = checkingUrlMessage;
                $('upload-wrapper').style.display = "none";
              },
              'onSuccess': function (responseTree, responseElements, responseHTML, responseJavaScript) {
                if (valid) {
                  $('upload-wrapper').style.display = "block";
                  $('upload').style.display = "block";
                  $('validation').style.display = "none";
                  $('code').value = dailymotion_code;
                  if ($('sitevideo_add_video_content').getElementById('videotitle').value == '')
                    $('sitevideo_add_video_content').getElementById('title').value = informationVideoContent.title;
                  if ($('sitevideo_add_video_content').getElementById('videodescription').value == '')
                    $('sitevideo_add_video_content').getElementById('description').value = informationVideoContent.description;
                } else {
                  $('upload-wrapper').style.display = "none";
                  current_code = dailymotion_code;
                  $('validation').innerHTML = validationErrorMessage;
                }
              }
            })).send();
          }
        },
        instagram: function (url) {
          var myURI = new URI(url);
          var instagram_code = myURI.get('directory');
          if (instagram_code.length > 0) {
            (new Request.HTML({
              'format': 'html',
              'url': validationUrl,
              'data': {
                'ajax': true,
                'code': instagram_code,
                'scheme': myURI.get('scheme'),
                'host': myURI.get('host'),
                'type': 'instagram'
              },
              'onRequest': function () {
                $('validation').style.display = "block";
                $('validation').innerHTML = checkingUrlMessage;
                $('upload-wrapper').style.display = "none";
              },
              'onSuccess': function (responseTree, responseElements, responseHTML, responseJavaScript) {
                if (valid) {
                  $('upload-wrapper').style.display = "block";
                  $('upload').style.display = "block";
                  $('validation').style.display = "none";
                  $('code').value = url;
                  if ($('sitevideo_add_video_content').getElementById('videotitle').value == '')
                    $('sitevideo_add_video_content').getElementById('title').value = informationVideoContent.title;
                  if ($('sitevideo_add_video_content').getElementById('videodescription').value == '')
                    $('sitevideo_add_video_content').getElementById('description').value = informationVideoContent.description;
                } else {
                  $('upload-wrapper').style.display = "none";
                  current_code = instagram_code;
                  $('validation').innerHTML = validationErrorMessage;
                }
              }
            })).send();
          }
        },
        twitter: function (url) {
          var myURI = new URI(url);
          var twitter_code = myURI.get('file');
          if (twitter_code.length > 0) {
            (new Request.HTML({
              'format': 'html',
              'url': validationUrl,
              'data': {
                'ajax': true,
                'code': twitter_code,
                'type': 'twitter'
              },
              'onRequest': function () {
                $('validation').style.display = "block";
                $('validation').innerHTML = checkingUrlMessage;
                $('upload-wrapper').style.display = "none";
              },
              'onSuccess': function (responseTree, responseElements, responseHTML, responseJavaScript) {
                if (valid) {
                  $('upload-wrapper').style.display = "block";
                  $('upload').style.display = "block";
                  $('validation').style.display = "none";
                  $('code').value = twitter_code;
                  if( $('thumbnail-wrapper') ) {
                  $('thumbnail-wrapper').style.display = "block";
                  }
                  $('thumbnail-label').getChildren()[0].style.display = 'block';
                  $('thumbnail').style.display = "block";
                  $('thumbnail').getNext().style.display = "block";

                } else {
                  $('upload-wrapper').style.display = "none";
                  current_code = twitter_code;
                  $('validation').innerHTML = validationErrorMessage;
                }
              }
            })).send();
          }
        },
        embed: function (embedCode) {
          $('validation').style.display = "block";
          $('validation').innerHTML = checkingUrlMessage;
          $('upload-wrapper').style.display = "none";
          if( $('thumbnail-wrapper') ) {
          $('thumbnail-wrapper').style.display = "none";
          }
          $('iframe').innerHTML = embedCode;
          iframeDiv = document.getElementById("iframe").children;
          iframeObj = null;
          bool = false;
          src = '';
          tp = "";
          if ($("iframe").getChildren('IFRAME').length >= 1) {
            src = $("iframe").getChildren('IFRAME')[0].src;
            tp = 'IFRAME';
          } else if ($("iframe").getChildren('BLOCKQUOTE').length >= 1) {
            src = $("iframe").getChildren('BLOCKQUOTE')[0].getElements('a').getLast().href;
            tp = 'BLOCKQUOTE';
          } else if ($("iframe").getChildren('A').length >= 1) {
            src = $("iframe").getChildren('A')[0].href;
            tp = 'A';
          }
          if (src != '')
          {
            src.match(/(http:|https:|)\/\/(player.|www.|in.)?(pinterest\.com|instagram\.com|twitter\.com|dailymotion\.com|vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);
            var allowYoutube = '<?php echo !!($this->settings('sitevideo.youtube.apikey', $this->settings('video.youtube.apikey'))); ?>';
            if (RegExp.$3.indexOf('youtu') > -1 && allowYoutube) {
              video.youtube(src);
              $('vtype').value = "youtube";
            } else if (RegExp.$3.indexOf('vimeo') > -1) {
              $('vtype').value = "vimeo";
              video.vimeo(src);
            } else if (RegExp.$3.indexOf('dailymotion') > -1) {
              $('vtype').value = "dailymotion";
              video.dailymotion(src);
            } else if (RegExp.$3.indexOf('instagram') > -1) {
              $('vtype').value = "instagram";
              video.instagram(src);
            } else if (RegExp.$3.indexOf('twitter') > -1) {
              $('vtype').value = "twitter";
              video.twitter(src);
            } else if (RegExp.$3.indexOf('pinterest') > -1) {
              $('vtype').value = "pinterest";
              if( $('thumbnail-wrapper') ) {
              $('thumbnail-wrapper').style.display = "block";
              }
              $('thumbnail-label').getChildren()[0].style.display = 'block';
              $('thumbnail').style.display = "block";
              $('thumbnail').getNext().style.display = "block";
              $('code').value = src;
              $('upload-wrapper').style.display = "block";
              $('upload').style.display = "block";
              $('validation').style.display = "none";
            } else if (tp != 'A') {
              if( $('thumbnail-wrapper') ) {
              $('thumbnail-wrapper').style.display = "block";
              }
              $('thumbnail-label').getChildren()[0].style.display = 'block';
              $('thumbnail').style.display = "block";
              $('thumbnail').getNext().style.display = "block";
              $('code').value = src;
              $('upload-wrapper').style.display = "block";
              $('upload').style.display = "block";
              $('validation').style.display = "none";
            }
          }
        },
        iframely: function (url) {
          (new Request.HTML({
            'url': validationUrl,
            'data': {
              'ajax': true,
              'code': url,
              'type': 'iframely'
            },
            onRequest: function () {
              $('validation').style.display = "block";
              $('validation').innerHTML = checkingUrlMessage;
              $('upload-wrapper').style.display = "none";
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
              if (valid) {
                $('upload-wrapper').style.display = "block";
                $('upload').style.display = "block";
                $('validation').style.display = "none";
                $('code').value = informationVideoContent.code;
                if ($('sitevideo_add_video_content').getElementById('videotitle').value == '')
                  $('sitevideo_add_video_content').getElementById('title').value = informationVideoContent.title;
                if ($('sitevideo_add_video_content').getElementById('videodescription').value == '')
                  $('sitevideo_add_video_content').getElementById('description').value = informationVideoContent.description;
                $('validation').value = "none";
              } else {
                $('upload-wrapper').style.display = "none";
                $('validation').innerHTML = validationErrorMessage;
                $('code').value = '';
              }
            }
          })).send();
        }
      };
      var type = '<?php echo Zend_Controller_Front::getInstance()->getRequest()->getParam('type', null); ?>';
              // Run stuff
              if (type) {
        updateTextFields(type);
        document.getElementById('type-' + type).checked = true;
      } else {
        updateTextFields();
      }
      video.attach();
    });
    }, 1000);
            if (<?php echo empty(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tags.enabled', 1))? 0 : Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tags.enabled', 1) ?>) {
            en4.core.runonce.add(function ()
            {
              new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag - suggest', 'resourceType' => 'sitevideo_channel'), 'default', true) ?>', {
              'postVar'
              : 'text',
                      'minLength'
              : 1,
                      'selectMode'
              : 'pick',
                      'autocompleteType'
              : 'tag',
                      'className'
              : 'tag-autosuggest',
                      'customChoices'
              : true,
                      'filterSubset'
              : true, 'multiple': true,
                      'injectChoice'
              : function (token) {
              var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
              new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
              choice.inputValue = token;
              this.addChoiceEvents(choice).inject(this.choices);
              choice.store('autocompleteChoice', token);
            }
            });
          });
      }
          var getProfileType = function (category_id) {
            var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()-> getDbtable('videoCategories', 'sitevideo')->getMapping(array('category_id', 'profile_type'))); ?> ;
                    for (i = 0; i < mapping.length; i++) {
              if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
            }
            return 0;
          }
  en4.core.runonce.add(function () {
    var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
            if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
      $(defaultProfileId).setStyle('display', 'none');
    }
  });</script>
<?php if (Engine_Api::_()->seaocore()->isMobile()): ?>
<style type="text/css">
  #form-upload #submit-wrapper {
    display: block;
  }
</style>
<?php endif; ?>
<div class="o_hidden seao_add_video_lightbox_header">
  <div class="fleft">
    <h3><?php echo $this->translate('Post New Video') ?></h3>
  </div>
  <div class="fright txt_right">
    <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
    <?php if ($this->channel): ?>  

    <a class="bold" name="cancel" id="cancel" type="button" href="<?php echo $this->channel->getHref(); ?>" onclick="SmoothboxSEAO.close();">X</a>
    <?php else: ?>
    <a class="bold" name="cancel" id="cancel" type="button" href="javascript:void(0)" onclick="SmoothboxSEAO.close();">X</a> 

    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<div class="layout_middle">
  <div class="sitevideo_add_video_content" id="sitevideo_add_video_content">
    <div id="video_upload_form_right">
      <div class="form-elements" id="video_upload_form_right_content"></div>
    </div>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<style>
  #video_upload_form_left{
    float:none !important;
  }
  #video_upload_form_right{
    float:none !important;
    margin-left: auto !important;
    width: 50% !important;
    padding: 0px 15px !important;
  }
</style>
<script type="text/javascript">
  setTimeout(function () {
    $('sitevideo_add_video_content').getElementById('form-upload').firstChild.id = "video_upload_form_left";
    $('video_upload_form_right').inject($('sitevideo_add_video_content').getElementById('form-upload'));
    injectInRightWrapper(rightWrapperItems);
  }, 100);
  var rightWrapperItems = ["type", "url", "file","stream" , "thumbnail", "upload"];
  function injectInRightWrapper(items) {
    items.each(function (value) {
      var el = $('sitevideo_add_video_content').getElementById('form-upload').getElementById(value + '-wrapper');
      if (el)
      el.inject($('video_upload_form_right_content'));
    });
  }

  function setHiddenVideoTitle() {
    $('sitevideo_add_video_content').getElementById('videotitle').value = $('sitevideo_add_video_content').getElementById('title').value;
  }

  function setHiddenVideoDescription() {
    $('sitevideo_add_video_content').getElementById('videodescription').value = $('sitevideo_add_video_content').getElementById('description').value;
  }
</script>
<div id="iframe" style="display:none;">
</div>


/* $Id: composer_photo.js 2011-05-05 9:40:21Z SocialEngineAddOns $ */

Composer.Plugin.SitepagePhoto = new Class({

  Extends : Composer.Plugin.Interface,

  name : 'sitepagephoto',

  options : {
    title : 'Add Page Photo',
    lang : {},
    requestOptions : false,
    fancyUploadEnabled : true,
    fancyUploadOptions : {}
  },

  initialize: function (options) {
      this.elements = new Hash(this.elements);
      this.params = new Hash(this.params);
      this.parent(options);
      this.scrollbar = false;
    },

    attach: function () {
      this.parent();
      this.makeActivator();
      return this;
    },

    detach: function () {
      this.parent();
      return this;
    },

    activate: function () {
      if (this.active)
        return;
      this.parent();

      this.makeMenu();
      this.makeBody();
    
      // Generate form
      var fullUrl = this.options.requestOptions.url;
      // Try to init fancyupload
      if (this.options.fancyUploadEnabled && this.options.fancyUploadOptions) {
        this.elements.formFancyContainer = new Element('div', {
          'styles': {
            'display': 'none',
            'visibility': 'hidden'
          }
        }).inject(this.elements.body);

        this.elements.scrollContainer = new Element('div', {
          'class': 'scrollbars',
          'styles': {
            'width': this.elements.menu.getSize().x + 'px',
          }
        }).inject(this.elements.formFancyContainer);
        // This is the list
        this.elements.formFancyList = new Element('ul', {
          'class': 'compose-photos-fancy-list',
        }).inject(this.elements.scrollContainer);

        // This is the browse button
        this.elements.formFancyFile = new Element('div', {
          'id': 'compose-photo-form-fancy-file',
          'class': '',
        }).inject(this.elements.scrollContainer);

        new Element('a', {
          'class': 'buttonlink',
          'html': this._lang('Select File')
        }).inject(this.elements.formFancyFile);
        this.elements.scrollContainer.scrollbars({
          scrollBarSize: 10,
          fade: true
        });
        var self = this;
        var opts = $merge({
          url: fullUrl,
          ui_button: self.elements.formFancyFile,
          ui_list: self.elements.formFancyList,
          ui_drop_area: self.elements.body,
          name: self.getName(),
          block_size: 2008000,
          url: fullUrl,
          deleteUrl: en4.core.baseUrl + 'sitepage/photo/remove',
          multiple: self.options.fancyUploadOptions.limitFiles != 1,
          limitFiles: self.options.fancyUploadOptions.limitFiles,
          accept: 'image/*',
          view: 'grid',
          // Events
          onActivate: function () {
            this.uiButton.addEvents({
              click: function () {return false; },
              mouseenter: function () { this.addClass('hover'); },
              mouseleave: function () { this.removeClass('hover'); this.blur(); },
              mousedown: function () { this.focus(); }
            });
            this.debug = this.options.debugMode || en4.core.environment == 'development';
            self.elements.formFancyContainer.setStyle('display', '');
            self.elements.formFancyContainer.setStyle('visibility', 'visible');
            this.setErrorMessages();
            self.elements.body.addClass('seao-fancy-uploader-wrapper');
            this._log('onActivate');
          },
          onAddFiles: function (num) {
            if (!num) return;
            self.allowToSetInInput = false;
            this.uiList.setStyle('display', 'inline-block');
            self.getComposer().getMenu().setStyle('display', 'none');
          },
          onItemAdded: function(el, file, imagedata){
            uploader = this;
            el.addClass('file compose-photo-preview')
            .adopt(new Element('span', {'class': 'file-size', 'html': this._convertSize(file.size) }))
            .adopt(new Element('span', {'class': 'file-name', 'html': file.name}))
            .adopt(new Element('span', {'class': 'compose-photo-preview-image compose-photo-preview-loading'}))
            .adopt(new Element('span', {'class': 'file-info'}))
            .adopt(new Element('div', {'class': 'compose-photo-preview-overlay'})
              .adopt(new Element('a', {'href': 'javascript:void(0);', 'class': 'file-remove', 'title': self._lang('Click to remove this entry.')})
                .addEvent('click', function(e){e.stop(); uploader.cancel(file.id, el)})
                )
              )
            .adopt(new Element('div', {'class': 'file-progress'}).setStyle('left', 0));
            // .adopt(new Element('div', {'class': 'file-progress'}).set('tween', {duration: 200}));

            if(file.type && file.type.match('image') && imagedata){
              el.addClass('image');
              preview = el.getElement('.compose-photo-preview-image');
              preview.adopt(new Element('img', {src: imagedata, style: 'width: 100%'}))
            }

            // UPDATE SCROLLBAR
            self.allowToSetInInput = false;
            self.updateScrollBar();
            var scrollbarContent = this.uiList.getParent('.scrollbar-content');
            scrollbarContent.scrollTo(this.uiButton.getPosition().x, scrollbarContent.getScroll().y);
            this._log('onItemAdded - ' + file.name);
          },
          onItemComplete: function(el, file, response) {
            el.removeClass('file-uploading').addClass('file-success');
            el.getElement('.file-progress').setStyle('left', '100%');
            // el.getElement('.file-progress').set('html', '100%').tween('width', 140);
            el.getElement('.compose-photo-preview-image img').destroy();
            el.store('photo_id', response.photo_id);
            el.store('src', response.src);
            self && self.doProcessResponse(response, el);
            this._log('onItemComplete - ' + file.name);
          },
          onItemCancel: function (el) {
            photo_id = el.retrieve('photo_id');
            el.destroy();
            if (photo_id) {
              self.removePhoto(photo_id);
              if (this.options.deleteUrl) {
                request = new Request.JSON({
                  'format': 'json',
                  'url': this.options.deleteUrl,
                  'data': { isajax : 1, photo_id : photo_id, },
                  'onSuccess': function (responseJSON) {return false; }
                });
                request.send();
              }
            }
            // (function () {
            self.updateScrollBar();
            // }).delay(1000);
            this._log('onItemCancel');
          },
          onItemProgress: function(el, perc) {
            el.getElement('.file-progress').setStyle('left', Math.floor(perc) + '%');
          },
          onUploadProgress: function (perc) {
            this._log('onUploadProgress - ' + Math.floor(perc) + '%' );
          },
          onUploadComplete: function (num) {
            self.allowToSetInInput = true;
            this._log('onUploadComplete: Uploaded Files - ' + num);
          },
        }, this.options.fancyUploadOptions);

        try {
          this.elements.formFancyUpload = en4.seaocore.initSeaoFancyUploader(opts);
        } catch (e) {
          if( $type(console) ) console.log(e);
        }
      }
    },

    updateScrollBar: function () {
      this.elements.formFancyList.getParent().setStyle('height', this.elements.formFancyFile.offsetHeight + 20);
      var li = this.elements.formFancyList.getElements('li');
      li[0] && this.elements.formFancyList.getParent().setStyle('width', ((li[0].getSize().x + 11) * li.length) + this.elements.formFancyFile.getSize().x + 10);
      this.elements.scrollContainer.retrieve('scrollbars').updateScrollBars();
    },

    deactivate: function () {
      if (!this.active)
        return;
      this.removeFiles();
      this.parent();
    },

    doProcessResponse: function (responseJSON, file) {
      if (typeof responseJSON == 'object' && responseJSON.error) {
        if (this.elements.loading) {
          this.elements.loading.destroy();
        }
        this.elements.body.empty();
        return this.makeError(responseJSON.error, 'empty');
      }

      // An error occurred
      if (($type(responseJSON) != 'hash' && $type(responseJSON) != 'object') || $type(responseJSON.src) != 'string' || $type(parseInt(responseJSON.photo_id)) != 'number') {
        this.elements.loading ? this.elements.loading.destroy() : '';
        this.elements.body.empty();
        if (responseJSON.error == 'Invalid data') {
          this.makeError(this._lang('The image you tried to upload exceeds the maximum file size.'), 'empty');
        } else {
          this.makeError(this._lang(responseJSON.error), 'empty');
        }
        return;
        //throw "unable to upload image";
      }

      // Success
      this.setPhotoId(responseJSON.photo_id);
      this.elements.preview = Asset.image(responseJSON.src, {
        'id': 'compose-photo-preview-image',
        'class': 'compose-preview-image',
        'onload': (function () {
          this.doImageLoaded(file);
        }.bind(this))
      });
    },

    doImageLoaded: function (file) {
      //compose-photo-error
      if ($('compose-photo-error')) {
        $('compose-photo-error').destroy();
      }

      if (this.elements.loading)
        this.elements.loading.destroy();
      if (this.elements.formFancyContainer) {
        preview = file.getElement('.compose-photo-preview-image');
        preview.removeClass('compose-photo-preview-loading');
        preview.setStyle('backgroundImage', 'url(' + file.retrieve('src') + ')');
      } else {
        this.elements.preview.erase('width');
        this.elements.preview.erase('height');
        this.elements.preview.inject(this.elements.body);
      }
      if (this.allowToSetInInput) {
        this.makeFormInputs();
      }
    },

    removePhoto: function (removePhotoId) {
      this.setPhotoId(removePhotoId, 'remove');
      var photo_id = this.setPhotoId(removePhotoId, 'remove');
      photo_id.erase(removePhotoId);
      if (photo_id.length === 0) {
        this.getComposer().deactivate();
        this.activate();
        return;
      }
      this.makeFormInputs();
    },

    setPhotoId: function (photoId, action) {
      var photo_id = this.params.get('photo_id') || [];
      if (action === 'remove') {
        photo_id.erase(photoId);
      } else {
        photo_id.push(photoId);
      }
      this.params.set('photo_id', photo_id);
      return photo_id;
    },

    makeFormInputs: function () {
      this.ready();
      if (this.elements.has('attachmentFormPhoto_id')) {
        return this.setFormInputValue('photo_id', this.getPhotoIdsString());
      }

      this.parent({
        'photo_id': this.getPhotoIdsString()
      });
    },

    getPhotoIdsString: function () {
      var photo_id_str = '';
      this.params.photo_id.each(function (value) {
        photo_id_str += value + ',';
      });
      return photo_id_str.substr(0, photo_id_str.length - 1);
    },

    removeFiles: function () {
      this.elements.formFancyUpload && this.elements.formFancyUpload.uiList.getElements('.seao-fancy-uploader-item .file-remove').each( function(link) {
        link.click();
      });
    }

  });

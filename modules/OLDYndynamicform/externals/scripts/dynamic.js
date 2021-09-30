
(function($,$$){
	var events;
	var check = function(e){
		var target = $(e.target);
		var parents = target.getParents();
		events.each(function(item){
			var element = item.element;
			if (element != target && !parents.contains(element))
				item.fn.call(element, e);
		});
	};
	Element.Events.outerClick = {
		onAdd: function(fn){
			if(!events) {
				document.addEvent('click', check);
				events = [];
			}
			events.push({element: this, fn: fn});
		},
		onRemove: function(fn){
			events = events.filter(function(item){
				return item.element != this || item.fn != fn;
			}, this);
			if (!events.length) {
				document.removeEvent('click', check);
				events = null;
			}
		}
	};
})(document.id,$$);

function dynamicOptions() {
	$$('.ynicon').removeEvents('click').addEvent('click', function (event) {
		var parent = this.getParent(),
			items = this.getSiblings('.yndform_option_items');
		this.toggleClass('yndform_explained');
		parent.toggleClass('yndform_show');
		items.setStyle('display', items.getStyle('display') == 'block' ? 'none' : 'block');

		var popup = this.getParent('.yndform_option_btn');
		window.setTimeout(function(){
			var layout_parent = popup.getParent('#global_content') || popup.getParent('.layout_middle');
		var y_position = popup.getPosition(layout_parent).y;
		var p_height = layout_parent.getHeight();
		var c_height = popup.getElement('.yndform_option_items').getHeight();
		if (parent.hasClass('yndform_show')) {
			if (p_height - y_position < (c_height + 60)) {
				layout_parent.addClass('popup-padding-bottom');
				var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace(/\D+/g, ''));
				layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 60 + y_position - p_height) + 'px');
			}
		} else {
			layout_parent.setStyle('padding-bottom', '0');
		}
		}, 20);
	});

	$$('.ynicon').addEvent('outerClick', function () {
		if (!this.hasClass('yndform_explained'))
			return;
		var popup = this.getParent('.yndform_option_btn');
		var items = this.getSiblings('.yndform_option_items');
		this.removeClass('yndform_explained');
		popup.removeClass('yndform_show');
		items.setStyle('display', 'none');
	});
}

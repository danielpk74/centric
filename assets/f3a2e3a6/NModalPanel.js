var Modal = {};
Modal.Box = Class.create();
Object.extend(Modal.Box.prototype, {
	initialize: function(id) {
		this.createOverlay();

		this.modal_box = $(id);
		this.modal_box.show = this.show.bind(this);
		this.modal_box.hide = this.hide.bind(this);

	},

	createOverlay: function() {
		if($('modal_overlay')) {
			this.overlay = $('modal_overlay');
		} else {
			this.overlay = document.createElement('div');
			this.overlay.id = 'modal_overlay';
			Object.extend(this.overlay.style, {
				position: 'absolute',
				top: 0,
				left: 0,
				zIndex: 90,
				width: '100%',
				backgroundColor: '#000',
				display: 'none'
			});
			document.body.insertBefore(this.overlay, document.body.childNodes[0]);
		}
	},

	moveModalBox: function(where) {
		Element.remove(this.modal_box);
		if(where == 'back')
		this.modal_box = this.parent_element.appendChild(this.modal_box);
		else
		this.modal_box = this.overlay.parentNode.insertBefore(this.modal_box, this.overlay);
	},

	show: function() {
	    
	    // center the div
	    this.parent_element = this.modal_box.parentNode;
	    this.modal_box.style.position = "absolute";

		var e_dims = Element.getDimensions(this.modal_box); // modalbox dimension
		var b_dims = Element.getDimensions(this.overlay); // overlay dimension
		var dv_dims = document.viewport.getDimensions();
		var dv_offset = document.viewport.getScrollOffsets();
		
		this.modal_box.style.left = (((dv_dims.width/2) - (e_dims.width/2)) + dv_offset.left) + 'px';
		this.modal_box.style.top = (((dv_dims.height/2) - (e_dims.height/2)) + dv_offset.top) + 'px';
		

		this.modal_box.style.zIndex = this.overlay.style.zIndex + 1;
	    
		this.overlay.style.height = Element.getDimensions(document.body).height+'px';
		this.moveModalBox('out');
		//this.overlay.onclick = this.hide.bind(this);
		this.selectBoxes('hide');
		new Effect.Appear(this.overlay, {duration: 0.1, from: 0.0, to: 0.3});
		this.modal_box.style.display = '';
	},

	hide: function() {
		this.selectBoxes('show');
		new Effect.Fade(this.overlay, {duration: 0.1});
		this.modal_box.style.display = 'none';
		this.moveModalBox('back');
		$A(this.modal_box.getElementsByTagName('input')).each(function(e){if(e.type!='submit')e.value=''});
	},

	selectBoxes: function(what) {
		$A(document.getElementsByTagName('select')).each(function(select) {
			Element[what](select);
		});

		if(what == 'hide')
		$A(this.modal_box.getElementsByTagName('select')).each(function(select){Element.show(select)})
	}
});
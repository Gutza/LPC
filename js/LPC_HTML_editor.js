var LPC_HTML_editor = {
	shownAttr: "tinyMCE",
	shownValue: "shown",

	showHideHandle: function(cb, ta_id) {
		if (!cb.checked) {
			tinymce.editors[ta_id].hide();
			$('#'+ta_id).focus();
			return;
		}

		if (this.shownValue == cb.getAttribute(this.shownAttr)) {
			tinymce.editors[ta_id].show();
			return;
		}
		this.initAndShow(cb, ta_id);
	},

	initAndShow: function(cb, ta_id) {
		tinymce.init({
			selector: '#'+ta_id,
			entity_encoding: 'raw',
			plugins: "link",
			forced_root_block : false,
			force_br_newlines : true,
			force_p_newlines : false,
			convert_urls: false,
			width: 600,
			height: 200
		});
		cb.setAttribute(this.shownAttr, this.shownValue);
		cb.checked = true;
	}
}

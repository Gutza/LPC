function LPC_HTML_editor_handleShowHide(cb, ta_id)
{
	if (!cb.checked) {
		tinymce.editors[ta_id].hide();
		$('#'+ta_id).focus();
		return;
	}

	var shownAttr = "tinyMCE";
	var shownValue = "shown";

	if (shownValue == cb.getAttribute(shownAttr)) {
		tinymce.editors[ta_id].show();
		return;
	}

	tinymce.init({
		selector: '#'+ta_id,
		entity_encoding: 'raw',
		forced_root_block : false,
		force_br_newlines : true,
		force_p_newlines : false
	});
	cb.setAttribute(shownAttr, shownValue);
}

(function($){
	/* LPC_tableExport by Bogdan Stancescu */
	$.fn.LPC_tableExport = function() {
		var tableData=[];
		$(this).find('tr').each(function() {
			var rowData=[];
			$(this).find('td,th').each(function() {
				var cellData={
					meta: {}
				};

				var span=$(this).find(".LPC_Excel_meta").detach();
				if (span.length)
					cellData.meta=$.parseJSON(span.first().html());
				cellData.meta.nN=this.nodeName;
				if (cellData.meta.value==undefined)
					cellData.html=this.innerHTML;
				if ($(this).attr('colspan')>1)
					cellData.meta.colspan=$(this).attr('colspan');
				if ($(this).attr('rowspan')>1)
					cellData.meta.rowspan=$(this).attr('rowspan');
				
				span.appendTo(this); // restore metadata span

				rowData.push(cellData);
			});
			tableData.push(rowData);
		});
		return tableData;
	};
	$.LPC_send=function(payload,url) {
		if (url==undefined)
			url=location.href;

		var form=document.createElement("form");
		form.setAttribute('method','post');
		form.setAttribute('action',url);
		var body=document.getElementsByTagName('body');
		body[0].appendChild(form);

		var input=document.createElement("input");
		input.setAttribute('type','hidden');
		input.setAttribute('name','payload');
		input.value=$.toJSON(payload);
		form.appendChild(input);

		form.submit();
	};
})(jQuery);

il.Util.addOnLoad
(
	function()
	{
		$("#container_info_ajax_load_btn").click(
			function()
			{
				
				// Show load circle - remove button
				$("#container_info_loading_div").removeClass('hidden').show('drop');
				$("#container_info_ajax_load_btn").addClass('hidden');
				
				// Get link from hidden input
				var ajax_link = $("#container_info_cmd_node").val();
				
				// ajax
				il.Util.sendAjaxGetRequestToUrl
				(
					ajax_link, 
					{}, 
					{
					  el_id: "container_info_ajax_target",
					  inner: false
					}, 
					function(o)
					{
					  // perform page modification
					  if (o.responseText !== undefined) 
					  {
						$('#' + o.argument.el_id).replaceWith(o.responseText);
					  }
					  $("#container_info_loading_div").hide();
					}
				);
			}
		)
	}
);
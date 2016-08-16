var container_info = {
	function loadContainerInfosClicked()
	{
		$("#container_info_loading_div").removeClass('hidden').show('drop');
		$("#container_info_ajax_load_btn").addClass('hidden');
		contaienr_info.getContainerInfos();
		
	}

	function getContainerInfos() {
	  //if ($("#mm_adm_tr").attr("data-loaded") == 0) {
		//$("#mm_adm_tr").attr("data-loaded", "1");
		var ajax_link = $("#container_info_cmd_node").val();
		il.Util.sendAjaxGetRequestToUrl(ajax_link, {}, {
		  el_id: "container_info_ajax_target",
		  inner: false
		}, function(o) {
		  // perform page modification
		  
		  if (o.responseText !== undefined) {
			$('#' + o.argument.el_id).replaceWith(o.responseText);
		  }
		  $("#container_info_loading_div").hide();
		});
	  //}
	}

	function getParameterValueFromUrl(searched_parameter)
	{
	  var url = window.location.search.substring(1);
	  var parameters = url.split("&");
	  var ref_id = 0;
	  for (var i=0;i<parameters.length && ref_id == 0;i++) 
	  {
		var pair = parameters[i].split("=");
		
		if(pair.length >= 2 && pair[0] == searched_parameter)
		{
			if(pair.length == 2)
			{
				ref_id = pair[1];
			}
		}  
	  }
	  return ref_id;
	}
};
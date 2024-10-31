jQuery(document).ready(function( $ ) {
	$(document).on('submit','#frm_woocpr_setting',  function(event){
		//alert(ni_woocpr_object.ni_woocpr_ajaxurl)
		event.preventDefault();
		//alert("dasdas");
		
		$.ajax({
			url:ni_woocpr_object.ni_woocpr_ajaxurl,
			data:$( this ).serialize(),
			success:function(respose) {
				$("._ajax_woocpr_setting").html(respose);
				alert(JSON.stringify(respose));
			},
			error: function(respose){
				console.log(respose);
				alert(JSON.stringify(respose));
				//alert("e");
			}
		}); 
		
		
	});
});
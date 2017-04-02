
jQuery(document).ready(function() { 
	resetdatalayout();
});

var errorCode = 0;

function avanticlick()
{
	errorCode = 0;
	jQuery.ajax({ 
			type: 'POST', 
			url: aao_booking_ajax_url, 
			data: { action: 'aao_booking_avanti', index: jQuery("#index").val(), isavanti: 1, inputdata:fdata(true), errorcode:errorCode}, 
			success: function(data, textStatus, XMLHttpRequest){
				jQuery("#containerpage").html(''); 
				jQuery("#containerpage").append(data); 
				resetdatalayout();
			}, 
			error: function(MLHttpRequest, textStatus, errorThrown){ alert(errorThrown); } 
		}); 
}

function indietroclick()
{
	errore = 0;
	jQuery.ajax({ 
			type: 'POST', 
			url: aao_booking_ajax_url, 
			data: { action: 'aao_booking_indietro', index: jQuery("#index").val(), isavanti: 0, inputdata:fdata(false), errorcode:errorCode}, 
			success: function(data, textStatus, XMLHttpRequest){ 
				jQuery("#containerpage").html(''); 
				jQuery("#containerpage").append(data); 
				resetdatalayout();
			}, 
			error: function(MLHttpRequest, textStatus, errorThrown){ alert(errorThrown); } 
		}); 
}


function resetdatalayout()
{
	jQuery('.dateclass').datepicker({
    	dateFormat: 'dd-mm-yy'
    });
}



function fdata(isavanti)
{
	var index = jQuery("#index").val();
	
	var data = '';
	if (index == 0)
	{
		if (jQuery("#date").val() !="")
			data = jQuery('#dataora').serialize();
		else{
			errorCode = 1;
			alert ("Inserire data");
		}
	}
	if (index == 1)
	{
//		data =jQuery('input[name=area]:checked').val();
		data = jQuery('#aree').serialize();
		if (data=="" && isavanti){
			errorCode = 1;
			alert ("Inserire area");
		}
	}
	if (index == 2)
	{
		data = jQuery('#servizi').serialize();
		
		  //TODO gestire le quantità confrontando con campi hidden inseriti da PHP		  
		var params = JSON.parse('{"' + decodeURI(data).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}')
		if (params['service']==null && isavanti){
			errorCode = 1;
			alert ("Inserire servizi");
		}
	}
	if (index == 3)
	{
		data = jQuery('#datiutente').serialize();
	}

	
	return data;
};


     

function toJSONString( form ) {
		var obj = {};
		 var o = {};
        var a = form.serializeArray();
        jQuery.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;

	}
 	

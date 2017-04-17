
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
	
	errorCode = 0;
	
	if (index == 0)
	{
		if (jQuery("#date").val() !="")
			data = jQuery('#dataora').serialize();
		else{
			errorCode = 1;
			alert ("Selezionare una data");
		}
	}
	if (index == 1)
	{
//		data =jQuery('input[name=area]:checked').val();
		data = jQuery('#aree').serialize();
		if (data=="" && isavanti){
			errorCode = 1;
			alert ("Selezionare un'area");
		}
	}
	if (index == 2)
	{
		data = jQuery('#servizi').serialize();
	
		  //TODO gestire le quantità confrontando con campi hidden inseriti da PHP		  
		var params = JSON.parse('{"' + decodeURI(data).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}')
		var tot = 0;
	
		for (var key in params) {
			if (key.startsWith('qty' )) {
				tot = tot + (parseInt(params[key]) || 0);
			}
		}
	
		if ((tot>params['max'] || tot<params['min']) && isavanti){
			errorCode = 1;
		
			alert ("Attenzione. L'area " + jQuery('#areadesc').val() + " può ospitare un minimo di "+ params['min']+" ed un massimo di "+ params['max']+" persone. Correggere la scelta effettuata o tornare alla schermata precedente.");
		}
		
	}
	if (index == 3)
	{
		data = jQuery('#datiutente').serialize();
	}
	if (index == 4)
	{
		data = jQuery('#promocode').val();
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
 	

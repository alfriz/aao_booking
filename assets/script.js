
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

function searchclick()
{
	errorCode = 0;
	jQuery.ajax({ 
			type: 'POST', 
			url: aao_booking_ajax_url, 
			data: { action: 'aao_booking_search',  issearch: 1, inputdata:jQuery('#search-booking').serialize()}, 
			success: function(data, textStatus, XMLHttpRequest){
				jQuery("#containerpage").html(''); 
				jQuery("#containerpage").append(data); 
				resetdatalayout();
			}, 
			error: function(MLHttpRequest, textStatus, errorThrown){ alert(errorThrown); } 
		}); 
}

function deleteclick(id)
{
	errorCode = 0;
	
	if (confirm('Sei sicure di voler cancellare questa prenotazione?'))
	{
		jQuery.ajax({ 
				type: 'POST', 
				url: aao_booking_ajax_url, 
				data: { action: 'aao_booking_delete',  issearch: 0, inputdata:'id='+id}, 
				success: function(data, textStatus, XMLHttpRequest){
					jQuery("#containerpage").html(''); 
					jQuery("#containerpage").append(data); 
					resetdatalayout();
				}, 
				error: function(MLHttpRequest, textStatus, errorThrown){ alert(errorThrown); } 
			}); 
	}
	else
		resetdatalayout();
}


function resetdatalayout()
{
	jQuery('.dateclass').datepicker({
    	dateFormat: 'dd-mm-yy'
    });
}


function testDate(dateString)
{

 	if(/^\d{4}([-])\d{2}\1\d{2}$/.test(dateString))
 		return dateString;
 
	 if(!/^\d{2}([./-])\d{2}\1\d{4}$/.test(dateString))
        return "";
        
    dateString= dateString.replace(/[./]/g, "-"); 
        
    var parts = dateString.split("-");
    var day = parseInt(parts[0], 10);
    var month = parseInt(parts[1], 10);
    var year = parseInt(parts[2], 10);

    // Check the ranges of month and year
    if(year < 1000 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    var res =  day > 0 && day <= monthLength[month - 1];  
    
    if (res)
     	return  "" + year + "-" + month + "-" + day; 

}

function testEmail(emailvalue)
{
    var emailFilter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
    if (!emailFilter.test(emailvalue)) {
        return false;
    }

    return true;

}

function testString(str)
{
    str = str.trim();
    if (str.length==0) {
        return false;
    }

    return true;

}

function testTel(telvalue)
{
    var telFilter = /^\d[0-9 ]+$/;
     if (!telFilter.test(telvalue)) {
        return false;
    }

    return true;

} 



function fdata(isavanti)
{
	var index = jQuery("#index").val();
	
	var data = '';
	
	errorCode = 0;
	
	if (index == 0)
	{
		if (jQuery("#date").val() !=""){
			var date = testDate(jQuery("#date").val());
			if (date!="")
				data = "date="+date;
			else{
				errorCode = 1;
				alert ("Formato data errato");
			}
			
		}else{
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
		if ( !testString(jQuery("#name").val())  && isavanti)
		{
			errorCode = 1;
			alert ("Inserire nome");
		}
	 
		if (errorCode == 0 &&  !testString(jQuery("#surname").val()) && isavanti)
		{
			errorCode = 1;
			alert ("Inserire cognome");
		}
				
		var email = jQuery("#email").val();
		if (errorCode == 0 && !testEmail(email) && isavanti)
		{
			errorCode = 1;
			alert ("Formato email errato");
		}
		
		var tel = jQuery("#tel").val();
		if (errorCode == 0 && !testTel(tel)  && isavanti)
		{
			errorCode = 1;
			alert ("Formato telefono errato");
		}
		
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
 	

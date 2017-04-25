
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
			data: { action: 'aao_booking_search',  issearch: 1, inputdata:sdata(), errorcode:errorCode}, 
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
    
    window.scrollTo(0, 0);
}


function testDate(dateString)
{

	var reverse = false;
 	if(/^\d{4}([-])\d{2}\1\d{2}$/.test(dateString))
 		reverse = true;
 	else
	 	if(!/^\d{2}([./-])\d{2}\1\d{4}$/.test(dateString))
        	return "";
        
    dateString= dateString.replace(/[./]/g, "-"); 
        
    var parts = dateString.split("-");
    var day = parseInt(parts[0], 10);
    var month = parseInt(parts[1], 10);
    var year = parseInt(parts[2], 10);
    
    if (reverse)
	{
		day = parseInt(parts[2], 10);
    	month = parseInt(parts[1], 10);
	    year = parseInt(parts[0], 10);
	}
	
    // Check the ranges of month and year
    if(year < 2015 || year > 2100 || month == 0 || month > 12)
        return "1";

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    var res =  day > 0 && day <= monthLength[month - 1];  
	if (!res)    
    	return "1";
    else
     	return  "" + year + "-" + pad(month,2) + "-" + pad(day,2); 

}

function pad(n, width, z) {
  z = z || '0';
  n = n + '';
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
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

function sdata()
{

	if (jQuery("#date").val() !=""){
		var date = testDate(jQuery("#date").val());
		if (date!="" && date!="1")
			data = "date="+date;
		else{
			errorCode = 1;
			if (date=="1")
				alert ("Data fuori limiti");
			else
				alert ("Formato data errato");
		}
	}else{
		errorCode = 1;
		alert ("Selezionare una data");
	}
	
	return  "area=" + jQuery("#area").val()+ "&" + data;
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
			var start = testDate(jQuery("#startdate").val());
			var stop = testDate(jQuery("#stopdate").val());
			var datestart = null
			 if (start!="" && start!="1")
				datestart = new Date(start);
			var datestop = null
			 if (stop!="" && stop!="1")
				datestop = new Date(stop);
			if (date!="" && date!="1")
			{
				var dateobj = new Date(date);
				if ((datestart!=null && dateobj.getTime() < datestart.getTime()) || (datestop!=null && dateobj.getTime() > datestop.getTime()))
				{
					errorCode = 1;
					alert ("Le aree sono disponibili dal " + jQuery("#startdate").val() + " al " + jQuery("#stopdate").val());
				}
				else
					data = "date="+date;
			}
			else{
				errorCode = 1;
				if (date=="1")
					alert ("Data fuori limiti");
				else
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
 	

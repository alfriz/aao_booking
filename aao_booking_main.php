<?php

/**
 * Plugin Name: AAO Booking
 * Plugin URI: 
 * Description: Galassi Booking system by aao
 * Version: 1.0.0
 * Author: A A&O
 * Author URI: 
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages/
 *
**/

if(!session_id()) 
	{ 
		session_start();
	}
?>


<?php

if ( ! defined( 'WPINC' ) ) { die; }

/* 1. REGISTER SHORTCODE
------------------------------------------ */

/* Init Hook */
add_action( 'init', 'my_wp_ajax_noob_plugin_init', 10 );


$aao_pluginurl= ""; 

function add_query_vars_filter( $vars ){
  $vars[] = "sid";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );

/**
 * Init Hook to Register Shortcode.
 * @since 1.0.0
 */
function my_wp_ajax_noob_plugin_init(){

	/* Register Shortcode */

	

	add_shortcode( 'aao-booking', 'wp_ajax_noob_aao_booking_shortcode_callbacks' );
	add_shortcode( 'aao-booking-delete', 'wp_ajax_noob_aao_booking_delete_shortcode_callbacks' );

}

/**
 * Shortcode Callback
 * Just display empty div. The content will be added via AJAX.
 */
function wp_ajax_noob_aao_booking_shortcode_callbacks(){
	global $wpdb;

	wp_enqueue_script( 'my-wp-ajax-noob-aao-booking-script' );
	wp_enqueue_style( 'my-wp-ajax-noob-aao-booking-style' ); 

	/* Output empty div. */
	
	if(!isset($_SESSION['sessionId'])) {
		$_SESSION['sessionId'] = time();
	} else {
   		 $value = '';
	}
	
	if(!isset($_SESSION['url'])) 
		$_SESSION['url']  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	
	$res = manageparam();
	
	if ($res!=null)	
		return $res;
	else

		return '<div id="containerpage">'. get_bookingdata(). '</div>';
}

function manageparam()
{
	$res = get_query_var( 'sid', '0' );
	
	$res = intval ($res);
	if ($res == '0')
		return null;
	else
		return get_bookingsaved();
}	



/* 2. REGISTER SCRIPT
------------------------------------------ */

/* Enqueue Script */
add_action( 'wp_enqueue_scripts', 'my_wp_ajax_noob_scripts' );

/**
 * Scripts
 */
function my_wp_ajax_noob_scripts(){

	/* Plugin DIR URL */
	$url = trailingslashit( plugin_dir_url( __FILE__ ) );

	/* JS + Localize */
	
	
	wp_register_script( 'my-wp-ajax-noob-aao-booking-script', $url . "assets/script.js", array( 'jquery', 'jquery-ui-datepicker' ), '1.0.3', true );	
	wp_localize_script( 'my-wp-ajax-noob-aao-booking-script', 'aao_booking_ajax_url', admin_url( 'admin-ajax.php' ) );
	wp_register_style( 'my-wp-ajax-noob-aao-booking-style', $url . 'assets/style.css' );
	
}

function wpse27856_set_content_type(){
    return "text/html";
}
add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );


add_action( 'paypal_ipn_for_wordpress_payment_status_completed', 'paypal_completed');

function paypal_completed($posted)
{
	$res = isset($posted['custom']) ? $posted['custom'] : '';
	
	$res = intval ($res);
	if ($res == '0')
		return null;
		
	$sessionInfo = getSessionData($res);

	if ($sessionInfo!=null)
	{
		sendmails($res);
		saveBooking($sessionInfo);
	}
}


/* 3. AJAX CALLBACK
------------------------------------------ */

/* AJAX action callback */
add_action( 'wp_ajax_aao_booking_avanti', 'my_wp_ajax_noob_aao_booking_ajax_callback' );
add_action( 'wp_ajax_nopriv_aao_booking_avanti', 'my_wp_ajax_noob_aao_booking_ajax_callback' );

add_action( 'wp_ajax_aao_booking_indietro', 'my_wp_ajax_noob_aao_booking_ajax_callback' );
add_action( 'wp_ajax_nopriv_aao_booking_indietro', 'my_wp_ajax_noob_aao_booking_ajax_callback' );


/**
 * Ajax Callback
 */
function my_wp_ajax_noob_aao_booking_ajax_callback(){
//get data from the ajax() call 
	$isavanti = $_POST['isavanti'];
	$inputdata = $_POST['inputdata'];
	$errorcode = $_POST['errorcode'];
	$index = $_POST['index'];
	
	if ($errorcode== 0)
	{
		saveData($index, $inputdata);

		if($isavanti == 1) {
			$index = $index + 1;
			if ( $index > 4)
				$index = 4;
		} else {
			$index = $index - 1;
			if ( $index < 0)
				$index = 0;
		}
	}
	
	if (checkSession())
		$index =0;
	
	if($index == 0){
		$results = get_bookingdata();
	} elseif($index == 1){
	
	
		parse_str($inputdata, $params);	
		$results = get_areas($params['date']) ;
	} elseif($index == 2){	
		parse_str($inputdata, $params);
		$results = get_services($params['area']);
	} elseif($index == 3){
		$results = get_user_data();
	} elseif($index == 4){
		
		if ($inputdata == getBackDoorPromoCode())
		{
			sendMails();
			saveBooking();
			$results = get_bookingsaved();
		}
		else
			$results = get_summary();
	} 
	
		//$results = "<h2> Sono data: ".$greeting."</h2>"; // Return String 
	die($results); 

}



function getBackDoorPromoCode()
{
	$options = get_option('aao_booking_settingsoptions');
	foreach ($options as $k => $v ) { $value[$k] = $v; }
	$promocode = $value['backdoorpc'];
	if ($promocode == null)
		$promocode = 'galassiadmin';
	
	return $promocode;
}

function checkSession()
{
	$result = false;

	$session = $_SESSION['sessionId'];
	
	deleteOldSessions();	

	if ( $session < time() - (15 * 60))
	{
		$_SESSION['sessionId'] = time();
		$result = true;
	}
	
	return $result;
}

function saveData($index, $inputdata)
{
	if($index == 0){
		$params = array();
		parse_str($inputdata, $params);
		updateDateTime($params['date']);
	} elseif($index == 1){
		$params = array();
		parse_str($inputdata, $params);
		updateArea($params['area']);
	} elseif($index == 2){
		updateService($inputdata);
	}elseif($index == 3){
		updateUserData($inputdata);
	}

}

function get_bookingdata()
{
	$session = $_SESSION['sessionId'];
	
	$row = getDataFromSession();
	
	$date = '';
	if ($row != null)
	{
		$dates = date_create_from_format('Y-m-d', $row->day);
		$date = date_format($dates, 'd/m/Y');
	}
	
	$options = get_option('aao_booking_settingsoptions');
	foreach ($options as $k => $v ) { $value[$k] = $v; }
	$startdate = $value['startdate'];
	$stopdate = $value['stopdate'];
	
	
	return '
		<h4 style="padding-top:10px; padding-bottom:20px;">Utilizza il modulo sottostante per <strong>prenotare i tuoi posti a tavola</strong>.</h4>

		<!-- Schermata 1 -->
 		<div class="form_prenotaz">
			<h1><span style="color:#d39c04 !important;">Passo 1 di 5</span><br>Seleziona una data</h1>
			<form id="dataora">
				<input id="index" type="hidden" value="0"/>
				<input id="startdate" type="hidden" value="'.$startdate.'"/>
				<input id="stopdate" type="hidden" value="'.$stopdate.'"/>
				<input type="date" id="date" name="date" placeholder="gg/mm/aaaa" value="'.$date.'"/>
			</form>
		</div>'
		 . getNavButtons(false, true);

}


function get_areas ($date) {

	$row = getDataFromSession();
	
	$session = $_SESSION['sessionId'];

	if ($date == null && $row!=null){
		$date = $row->day;		
	}
	
	$defarea = 0;
	if ($row != null)	
	{
		$defarea =  $row->areaId;
	}			
	
	
	
	global $wpdb;
	
	$sql = "
		SELECT a.*, t.description as areatype
		FROM  `wp_aao_bkg_areas` AS a
		
			LEFT JOIN `wp_aao_bkg_area_types` as t on a.tipologia = t.id
			LEFT OUTER JOIN (
				SELECT * 
				FROM wp_aao_bkg_bookings
				WHERE DAY =  '" . $date . "')
			as b
			ON a.id = b.areaid

			LEFT OUTER JOIN (
				SELECT * 
				FROM wp_aao_bkg_temp_bookings
				WHERE day =  '" . $date . "' and session != " . $session . ")
			as t
			ON a.id = t.areaid

		WHERE b.areaid IS NULL AND t.areaid is NULL 
		ORDER BY disporder ASC 
	";

	$areas = $wpdb->get_results( $wpdb->prepare( $sql ) ); 
	
	if ($wpdb->num_rows > 0 && test_blackdate($date))
	{
		$dates = date_create_from_format('Y-m-d', $date);
		$formatdate = date_format($dates, 'd/m/Y');

		$result = '
			<!-- Schermata 2 -->
			<div class="form_prenotaz" style="padding-bottom:0px;">
				<h1 style="background:green; color:#ffffff !important;"><span style="color:#035903 !important;">Passo 2 di 5</span><br>Ecco le aree disponibili per il '. $formatdate .'.<br> Effettua una selezione e clicca AVANTI per proseguire.</h1>

				<form id="aree">

					<input id="index" type="hidden" value="1"/>
			';	
	
	
		$areadhead = "";	
		foreach($areas as $key=>$row){
			if ($row->areatype !=$areahead)
			{
				$result = $result . '<label style="color:green !important;font-size: 130%; font-weight: 700;">'.$row->areatype.'</label></br>';
				$areahead = $row->areatype;
			} 
		
			$result = $result . "<input type='radio' name='area' value='". $row->id ."' " .  ($row->id==$defarea? "checked":"" )   . ">".$row->description. " (Capacità: min ".$row->min. ", max "  .$row->max. " persone)</br>";
		}
	
		$result = $result . '
								</form>
							</div>
							<iframe style="width: 100%; height: 450px;" src="https://www.google.com/maps/d/embed?mid=1Noz_M-FwPSXDzX2U03SRpGDT9wU&z=18" width="640" height="480">
							</iframe>
							'
			 . getNavButtons(true, true);
	}		 
	else
	{
		$result = '	<div class="form_prenotaz" style="padding-bottom:0px;">
						<h1 style="background:red; color:#ffffff !important;">2. Spiacenti, per il '. $formatdate .'  non è disponibile nessuna area, seleziona un&rsquo;altra giornata.</h1>
					</div>';
		$result = $result .
			 getNavButtons(true, false);
	}
	
	return $result;
}

function test_blackdate($date)
{
	$options = get_option('aao_booking_settingsoptions');
	foreach ($options as $k => $v ) { $value[$k] = $v; }
	
	$blackdate = $value['blackdate'];
	
	$blackdates = explode(',', $blackdate);
	
	$dates = date_create_from_format('Y-m-d', $date);
	
	foreach($blackdates as $bdate) {
    	$dt = date_create_from_format('d/m/Y', $bdate);
    	if ($dt == $dates)
    		return false;
	}
	
	return true;
}

function get_services ($area) {

	$sessionrow = getDataFromSession();
	
	if ($area == null && $sessionrow!=null){
		$area = $sessionrow->areaId;
	}

	$areaInfo = getAreaInfo($area);

	if ( $areaInfo->tipologia == 1 )
		$result ='
				<!-- Schermata 3 -->
				<div class="form_prenotaz" style="padding-bottom:0px;">
					<h1><span style="color:#d39c04 !important;">Passo 3 di 5</span><br>Seleziona il numero di partecipanti per tipologia di cena</h1>
				';
	else
		$result ='<h1><span style="color:#d39c04 !important;">Passo 3 di 5</span><br>Seleziona il numero di partecipanti</h1>';
	
	$result = $result .' <form id="servizi">
							<input id="index" type="hidden" value="2"/>';
	
	
	if ( $sessionrow !=null && $sessionrow->persons != null )
		parse_str($sessionrow->persons, $params);
		
	
	
	global $wpdb;
	$services = $wpdb->get_results( $wpdb->prepare( 
		"
		SELECT      *
		FROM        wp_aao_bkg_services
		WHERE areaType=%d 
		ORDER BY disporder ASC", $areaInfo->tipologia) ); 
	
	$result = $result . '<input id="areadesc" type="hidden"  value="'. $areaInfo->description .'"/>';
	$result = $result . '<input name="min" type="hidden"  value="'. $areaInfo->min .'"/>';
	$result = $result . '<input name="max" type="hidden" value="'. $areaInfo->max .'"/>';
	foreach($services as $key=>$row){

		$result = $result . "
							<label>". $row->description . "</label>
							<input name='qty_".$row->id."' type='number' min=1 max=50 value='". ($params!=null?$params['qty_'.$row->id]:"") ."' style='width:100px; ' ></input>
							<input name='prezzo_". $row->id . "' type='hidden' value='" . $row->prezzo . "'/>
							</br>";							
	}
	
	$result = $result . '</form>'
			 . getNavButtons(true, true);

		
	return $result;
}

function get_user_data () {

	$sessionrow = getDataFromSession();
	
	if ( $sessionrow->userdata != null )
		parse_str($sessionrow->userdata, $params);

	$result = '
				<!-- Schermata 4 -->
 				<div class="form_prenotaz">
					<h1><span style="color:#d39c04 !important;">Passo 4 di 5</span><br>Inserisci i dati del referente</h1>
					<form id="datiutente">
						<input id="index" type="hidden" value="3"/>
						<label for"name">Nome</label>
						<input id="name" name="name" type="text" value="'. ($params!=null?$params['name']:'') .'"></input></br> 
						<label for"surname">Cognome</label>
						<input id="surname" name="surname" type="text" value="'. ($params!=null?$params['surname']:'') .'"></input></br>
						<label for"email">Email</label>
						<input id="email" name="email" type="email" value="'. ($params!=null?$params['email']:'') .'"></input></br>
						<label for"tel">Telefono</label>
						<input id="tel" name="tel" type="tel" value="'. ($params!=null?$params['tel']:'') .'"></input>
					</form>'
		 . getNavButtons(true, true);
		
	return $result;
}

function get_summary () {

	$session = $_SESSION['sessionId'];
	
	$sessionrow = getExtededDataFromSession($session);

	$result =' 	
	<!-- Schermata 5 -->
 				<div class="form_prenotaz">
					<h1><span style="color:#d39c04 !important;">Passo 5 di 5</span><br>Controlla i dettagli della prenotazione:</h1>
					<input id="index" type="hidden" value="4"/>
					<br/>';
	
	$totale = 0;
	$result = $result .summarystring($sessionrow, null, $totale);
    
	$result = $result 
			 .paypalbtn($totale);    

    $result = $result .'<label style="margin-top:70px;">Hai un codice promozionale?</label><br/>';
    
    $result = $result .'<input type="text" id="promocode" name="promocode" value="" style="width:50%; margin-bottom:15px !important;"></input>';
		
    $result = $result .'<p><button onclick="avanticlick()">Applica</button></p></br>';	
        

    $result = $result .'<div class="form_prenotaz" style="background:rgba(255, 255, 255, 0);">
							<input style="background:rgba(255, 204, 51, 0.6); margin-top:10px; margin-bottom:25px;" type="submit" value="Indietro" onclick="indietroclick()">
						</div>';

	return $result;
}

function summarystring($sessionrow, $paymentmode, &$totale)
{

	$result ='';
	parse_str($sessionrow->userdata, $userdata);
	parse_str($sessionrow->persons, $persons);
	
	$dates = date_create_from_format('Y-m-d', $sessionrow->day);
	$formatdate = date_format($dates, 'd/m/Y');
	
	$result = $result . '
						
						<ul class="lista_riep_dett_prenot">
						<h3>Data e posizione:</h3>
							<li>Giorno: ' . $formatdate .'</li>
						';
	
	$result = $result . '
							<li>Area: '. $sessionrow->adesc .'</li>
						</ul>						

						<ul class="lista_riep_dett_prenot">
						<h3>Persona di riferimento per la prenotazione:</h3>						
							<li>Nome: ' . $userdata['name'] .'</li>
							<li>Cognome: '. $userdata['surname'] .' </li>
							<li>Email: '. $userdata['email'] .'</li>
							<li>Telefono: '. $userdata['tel'] .'</li>
						</ul>
						<ul class="lista_riep_dett_prenot">
							<h3>Persone per tipologia di cena:</h3>
						';  
		
	$totale = 0;
	foreach ($persons as $key => $value) {
		
		if ( startsWith($key, 'qty') && $value > 0)
		{
			$serviceid = substr($key, -2);
			$serviceinfo = getServiceInfo($serviceid);
			$prezzo = ($serviceinfo->prezzo * $value);
			$result = $result . '<li><span style="font-weight:300 !important; margin-bottom:30px;">' . $serviceinfo->description .' - </span> <strong>'.  $value .' persone</strong> - Subtotale '. $prezzo .'€ </li>';
			$totale = $totale + $prezzo;
			
		}
	}
	
	$result = $result .'</ul>';

	$result = $result .'<h3 style="margin-top:20px; margin-bottom:10px; font-weight:700; font-size:2em;">Totale '. $totale .'€ </h3>';
	
	if ($paymentmode!=null)
	{
		$result = $result . '<div style="margin-top:20px;">Metodo di pagamento: <strong>'. $paymentmode.'</strong></div>';
	}
	
	return $result;
}

function paypalbtn($totale)
{

	global $wp;
	global $aao_pluginurl;
	
	$session = $_SESSION['sessionId'];
	
	$options = get_option('aao_booking_settingsoptions');
	foreach ($options as $k => $v ) { $value[$k] = $v; }


	// live of test mode
	if ($value['mode'] == "1") {
		$account = $value['sandboxaccount'];
		$path = "sandbox.paypal";
	} elseif ($value['mode'] == "2")  {
		$account = $value['liveaccount'];
		$path = "paypal";
	}
	
	if ($value['paymentaction'] == "1") {
		$paymentaction = "sale";
	} elseif ($value['paymentaction'] == "2")  {
		$paymentaction = "authorization";
	} else {
		$paymentaction = "sale";
	}
	
	$current_url= substr($_SESSION['url'],0, strrpos($_SESSION['url'], "/")+1);

		
	$returnurl =  $current_url.'?sid=1';
	$cancelurl =  $current_url.'?sid=0';

	$output .= "<form target='' action='https://www.".$path.".com/cgi-bin/webscr' method='post'>";
	$output .= "<input type='hidden' name='cmd' value='_xclick' />";
	$output .= "<input type='hidden' name='business' value='".$account. "' />";
	$output .= "<input type='hidden' name='item_name' value='Prenotazione Galassi' />";
	$output .= "<input type='hidden' name='currency_code' value='EUR' />";
	$output .= "<input type='hidden' name='amount' value='". $totale ."' />";
	$output .= "<input type='hidden' name='lc' value='it_IT'>";
	$output .= "<input type='hidden' name='no_note' value=''>";
	$output .= "<input type='hidden' name='custom' value='" . $session . "'>";	
	$output .= "<input type='hidden' name='paymentaction' value='" . $paymentaction . "'>";
	$output .= "<input type='hidden' name='return' value='". $returnurl ."' />";
	$output .= "<input type='hidden' name='notify_url' value='". $value['notifyurl'] ."' />";
	$output .= "<input type='hidden' name='bn' value='WPPlugin_SP'>";
	$output .= "<input type='hidden' name='cancel_return' value='". $cancelurl ."' />";
	$output .= "<input style='border: none;' class='paypalbuttonimage' type='image' src='https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='Paga con Paypal'>";
	$output .= "<img alt='' border='0' style='border:none;display:none;' src='https://www.paypal.com/it_IT/i/scr/pixel.gif' width='1' height='1'>";
	$output .= "</form>";
	
	
	
	return $output;
}

function get_bookingsaved () {

	$result =	' 	
				<!-- Schermata 6 -->
 				<div class="form_prenotaz">
					<h1 style="background:green; color:#ffffff !important;">
						<div>
							<img src="/wp-content/uploads/_img_layout/success.png">
						</div>
						Prenotazione completata!
					</h1>
					<label>A breve riceverai una mail di conferma</label><br/>
					<label>Grazie!</label><br>
				</div>
				<div class="form_prenotaz" style="background:rgba(247, 247, 247, 0)">
					<a href="/home/">
						<input type="submit" value="Esci">
					</a>
				</div>	
				';
	
	return $result;
}



function getNavButtons($back, $next)
{

	$result = '';

	if ($back)
		$result = $result . '
							<div class="form_prenotaz" style="background:rgba(255, 255, 255, 0); display:inline;">
								<input style="background:rgba(255, 204, 51, 0.6); margin-top:55px; margin-bottom:25px;" type="submit" value="Indietro" onclick="indietroclick()">
							</div>	';	

	if ($next)
		$result = $result . '
							<div class="form_prenotaz" style="background:rgba(255, 255, 255, 0); margin-top:55px; margin-bottom:25px; display:inline;">
								<input type="submit" value="Avanti" onclick="avanticlick()">
							</div>	';	

	return $result;		
}



function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}



//----------------- Admin options page

// settings page menu link
add_action( "admin_menu", "aao_booking_plugin_menu" );

function aao_booking_plugin_menu() {
	add_options_page( "AAO Booking", "AAO Booking", "manage_options", "aao-booking-settings", "aao_booking_plugin_options" );
}


function aao_booking_plugin_options() {
	
	if ( !current_user_can( "manage_options" ) )  {
		wp_die( __( "You do not have sufficient permissions to access this page." ) );
	}

	// settings page

	echo "<table width='100%'><tr><td width='70%'><br />";
	echo "<label style='color: #000;font-size:18pt;'><center>AAO Booking Settings</center></label>";
	echo "<form method='post' action='".$_SERVER["REQUEST_URI"]."'>";


	// save and update options
	if (isset($_POST['update'])) {

		$options['liveaccount'] = 			$_POST['liveaccount'];
		$options['sandboxaccount'] = 		$_POST['sandboxaccount'];
		$options['mode'] = 					$_POST['mode'];
		$options['paymentaction'] = 		$_POST['paymentaction'];
		$options['notifyurl'] = 			$_POST['notifyurl'];
		$options['emailnotify'] = 			$_POST['emailnotify'];
		$options['emailnotifybody'] = 		$_POST['emailnotifybody'];
		$options['emailnotifysubject'] = 	$_POST['emailnotifysubject'];
		$options['gemailnotifybody'] = 		$_POST['gemailnotifybody'];
		$options['gemailnotifysubject'] = 	$_POST['gemailnotifysubject'];
		$options['backdoorpc'] = 			$_POST['backdoorpc'];
		$options['startdate'] = 			$_POST['startdate'];
		$options['stopdate'] = 				$_POST['stopdate'];
		$options['blackdate'] = 			$_POST['blackdate'];

		update_option("aao_booking_settingsoptions", $options);

		echo "<br /><div class='updated'><p><strong>"; _e("Settings Updated."); echo "</strong></p></div>";

	}


	// get options
	$options = get_option('aao_booking_settingsoptions');
	foreach ($options as $k => $v ) { $value[$k] = $v; }


	echo "</td><td></td></tr><tr><td>";


	// form
	echo "<br />";
	?>

	<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
	&nbsp; Usage
	</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

	Enjoy!

	<br /><br />
	</div><br /><br />

	<br /><br /><div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
	&nbsp; PayPal Account </div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

	<?php

	echo "<b>Live Account: </b><input type='text' name='liveaccount' value='".$value['liveaccount']."'> Required";
	echo "<br />Enter a valid Merchant account ID (strongly recommend) or PayPal account email address. All payments will go to this account.";
	echo "<br /><br />You can find your Merchant account ID in your PayPal account under Profile -> My business info -> Merchant account ID";

	echo "<br /><b>Sandbox Account: </b><input type='text' name='sandboxaccount' value='".$value['sandboxaccount']."'> Optional";
	echo "<br />Enter a valid sandbox PayPal account email address. A Sandbox account is a PayPal accont with fake money used for testing. This is useful to make sure your PayPal account and settings are working properly being going live.";
	echo "<br /><br />To create a Sandbox account, you first need a Developer Account. You can sign up for free at the <a target='_blank' href='https://www.paypal.com/webapps/merchantboarding/webflow/unifiedflow?execution=e1s2'>PayPal Developer</a> site. <br /><br />";


	echo "<b>Sandbox Mode:</b>";
	echo "&nbsp; &nbsp; <input "; if ($value['mode'] == "1") { echo "checked='checked'"; } echo " type='radio' name='mode' value='1'>On (Sandbox mode)";
	echo "&nbsp; &nbsp; <input "; if ($value['mode'] == "2") { echo "checked='checked'"; } echo " type='radio' name='mode' value='2'>Off (Live mode)";

	echo "<br /><br /><b>Payment Action:</b>";
	echo "&nbsp; &nbsp; <input "; if ($value['paymentaction'] == "1") { echo "checked='checked'"; } echo " type='radio' name='paymentaction' value='1'>Sale (Default)";
	echo "&nbsp; &nbsp; <input "; if ($value['paymentaction'] == "2") { echo "checked='checked'"; } echo " type='radio' name='paymentaction' value='2'>Authorize (Learn more <a target='_blank' href='https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/authcapture/'>here</a>)";
	echo "<br /><br /><b>Notify Url:</b><input type='text' name='notifyurl' value='".$value['notifyurl']."'> Required";

	echo "<br /><br /></div>";

	?>
	
	<br /><br /><div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
	&nbsp; General </div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

	<?php

	echo "<b>Notify Email address: </b><input type='text' name='emailnotify' value='".$value['emailnotify']."'> Required";
	echo "<br /><b>Customer Notify Email subject: </b><input type='text' name='emailnotifysubject' value='".$value['emailnotifysubject']."'> Required";
	echo "<br /><b>Customer Notify Email body: </b><input type='text' name='emailnotifybody' value='".$value['emailnotifybody']."'> Required";
	echo "<br /><b>Galassi Notify Email subject: </b><input type='text' name='gemailnotifysubject' value='".$value['gemailnotifysubject']."'> Required";
	echo "<br /><b>Galassi Notify Email body: </b><input type='text' name='gemailnotifybody' value='".$value['gemailnotifybody']."'> Required";
	
	echo "<br /><b>Backdoor promotion code: </b><input type='text' name='backdoorpc' value='".$value['backdoorpc']."'> Required";
	echo "<br /><br/><b>Booking Start date: </b><input type='date' name='startdate' value='".$value['startdate']."'>";
	echo "<br /><b>Booking Stop date: </b><input type='date' name='stopdate' value='".$value['stopdate']."'> ";
	echo "<br /><b>Booking Blacklist date: </b><input type='date' name='blackdate' value='".$value['blackdate']."'> ";

	echo "<br /><br /></div>";

	?>


	<br /><br /></div>

	<input type='hidden' name='update'><br />
	<input type='submit' name='btn2' class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;' value='Save Settings'>

	<br /><br /><br />

	</form>


	</td><td width='5%'>
	</td><td width='24%' valign='top'>

	<br />

	</td><td width='1%'>

	</td></tr></table>


	<?php

	// end settings page and required permissions
}


//----------------- Updates functions


function updateDateTime($date)
{
	if ($date != null)
	{
		global $wpdb;
		$session = $_SESSION['sessionId'];
	
		$dates = date_create_from_format('Y-m-d', $date);
		
		$row = getDataFromSession();
			
		$wpdb->query( $wpdb->prepare(  
				'INSERT INTO wp_aao_bkg_temp_bookings (session, day) 
				VALUES('.$session.', "'.date_format($dates, 'Y-m-d').'") ON DUPLICATE KEY UPDATE    
				 day="'. date_format($dates, 'Y-m-d') .'"
				 '));
		if ($row!= null && $row->day != date_format($dates, 'Y-m-d'))		 
			$wpdb->query(  'UPDATE wp_aao_bkg_temp_bookings SET areaId=null, persons=null, userdata=null WHERE session='. $session );

	}
}

function updateArea($area)
{
	if ($area != null)
	{
		global $wpdb;
		$session = $_SESSION['sessionId'];

		$row = getDataFromSession();
		if ($row!= null && $row->areaId != $area)
			$wpdb->query(  'UPDATE wp_aao_bkg_temp_bookings SET areaId='. $area .', persons=null, userdata=null WHERE session='. $session );
	}
}

function updateService($param)
{
	global $wpdb;
	$session = $_SESSION['sessionId'];

	$wpdb->query(  'UPDATE wp_aao_bkg_temp_bookings SET persons="'. $param .'" WHERE session='. $session );
}

function updateUserData($userdata)
{
	if ($userdata != null)
	{
		global $wpdb;
		$session = $_SESSION['sessionId'];

		$wpdb->query(  'UPDATE wp_aao_bkg_temp_bookings SET userdata="'. $userdata .'" WHERE session='. $session );
	}
}

function saveBooking($sessionInfo)
{
	global $wpdb;

	if ($sessionInfo==null){
		$row = getDataFromSession();
	}
	else
		$row = $sessionInfo;
		 
	$wpdb->query( 'INSERT INTO wp_aao_bkg_bookings
					(dayOfRegistration, day, areaId, persons, userdata, paymentmode) 
					VALUES ("'. date('Y-m-d') .'","'.$row->day.'",'.$row->areaId .',"'.$row->persons.'","'.$row->userdata.'","'.getPaymentMode($sessionInfo).'")' );

	deleteSession($row->session);
}

function getPaymentMode($session)
{
	$paymentmode ="Paypal";

	if ($session==null)
		$paymentmode ="Admin";
		
	return $paymentmode;
}

function sendMails($session)
{

	$paymentmode = getPaymentMode($session);
	
	if ($session==null)
		$session = $_SESSION['sessionId'];

	$options = get_option('aao_booking_settingsoptions');
	foreach ($options as $k => $v ) { $value[$k] = $v; }

	
	$sessionrow = getExtededDataFromSession($session);
	
	parse_str($sessionrow->userdata, $userdata);
	
	$to = $userdata['email'];
	$subject = $value['emailnotifysubject'];
	$message = $value['emailnotifybody']. summarystring($sessionrow);

	wp_mail( $to, $subject, $message );
	
	
	$to = $value['emailnotify'];
	$subject = $value['gemailnotifysubject'];
	$message = $value['gemailnotifybody']. summarystring($sessionrow, $paymentmode);

	wp_mail( $to, $subject, $message );
	
}

//----------------- db functions

function deleteSession($session)
{
	if ($session==null)
		$session = $_SESSION['sessionId'];

	global $wpdb;
	$temp = $wpdb->query( 
		"DELETE FROM `wp_aao_bkg_temp_bookings`
			WHERE	session=" . $session  ); 
	
	return $temp;
	
}

function deleteOldSessions()
{
	$session = time() - (15 * 60);
	global $wpdb;
	$temp = $wpdb->query( 
		"DELETE FROM `wp_aao_bkg_temp_bookings`
			WHERE	session<" . $session  ); 
	
	return $temp;

}

function getExtededDataFromSession($session)
{
	
	global $wpdb;
	$temp = $wpdb->get_row(
		"SELECT * , a.description AS adesc
			FROM  `wp_aao_bkg_temp_bookings` AS t
			LEFT JOIN wp_aao_bkg_areas AS a ON t.areaid = a.id
			WHERE	session=" . $session  ); 
	
	return $temp;
}

function getAreaInfo($areaid)
{
	global $wpdb;
	$temp = $wpdb->get_row(
		"SELECT *
			FROM  `wp_aao_bkg_areas` 
			WHERE	id=" . $areaid  ); 
	
	return $temp;
}

function getServiceInfo($serviceid)
{
	global $wpdb;
	$temp = $wpdb->get_row(
		"SELECT *
			FROM  `wp_aao_bkg_services` 
			WHERE	id=" . $serviceid  ); 
	
	return $temp;
}


function getDataFromSession()
{
	$session = $_SESSION['sessionId'];
	return getSessionData($session);
}

function getSessionData($session)
{
	global $wpdb;
	$temp = $wpdb->get_row(
		$wpdb->prepare( 
			"
			SELECT      *
			FROM        wp_aao_bkg_temp_bookings
			WHERE		session=%d", $session )  
		); 
	
	return $temp;
}


//----------------- Booking delete page


add_action( 'wp_ajax_aao_booking_search', 'my_wp_ajax_noob_aao_booking_delete_ajax_callback' );
add_action( 'wp_ajax_aao_booking_delete', 'my_wp_ajax_noob_aao_booking_delete_ajax_callback' );
add_action( 'wp_ajax_nopriv_aao_booking_search', 'my_wp_ajax_noob_aao_booking_delete_ajax_callback' );
add_action( 'wp_ajax_nopriv_aao_booking_delete', 'my_wp_ajax_noob_aao_booking_delete_ajax_callback' );


function my_wp_ajax_noob_aao_booking_delete_ajax_callback(){
	$issearch = $_POST['issearch'];
	$inputdata = $_POST['inputdata'];
	$errorcode = $_POST['errorcode'];

	$result = get_bookingdelete();
	if ($issearch)
	{
		if ($errorcode== 0) {
			$sessionrow = getSearchData($inputdata);
		
			if ($sessionrow !=null)
			{

				parse_str($sessionrow->userdata, $userdata);
				parse_str($sessionrow->persons, $persons);
	
				$dates = date_create_from_format('Y-m-d', $sessionrow->day);
				$formatdate = date_format($dates, 'd/m/Y');
	
				$result = $result . '<br/><label>Giorno: ' . $formatdate .'</label><br/>';
				$result = $result . '<label>Nome: ' . $userdata['name'] .'</label><br/>
				<label>Cognome:'. $userdata['surname'] .' </label><br/>
				<label>Email: '. $userdata['email'] .'</label><br/>
				<label>Telefono: '. $userdata['tel'] .'</label><br/>
				<label>Area: '. $sessionrow->adesc .'</label><br/>';
				$result = $result . '<button onclick="deleteclick('. $sessionrow->pid .')">Elimina</button>';
			}
			else
			{
				$result = $result . '<br/><label>Nessuna prenotazione </label><br/>' ;
			}
		}
	}
	else
	{
		deteteBookingData($inputdata);
		$result = $result . '<br/><label>Cancellazione eseguita </label><br/>';
	}
	
	die($result); 
	
}


function deteteBookingData($inputdata)
{
	global $wpdb;
	parse_str($inputdata, $params);
		
	$temp = $wpdb->query( 
	"DELETE FROM `wp_aao_bkg_bookings`
		WHERE id=" . $params['id']  ); 
	
}

function getSearchData($inputdata)
{
	global $wpdb;
	
	parse_str($inputdata, $params);
	
	$dates = $params['date'];
	
	$sql = "SELECT * , a.description AS adesc, t.id as pid
			FROM  `wp_aao_bkg_bookings` AS t
			LEFT JOIN wp_aao_bkg_areas AS a ON t.areaid = a.id
			WHERE	t.areaid = " . $params['area'] . " AND t.day ='" . $dates .  "'";
	$temp = $wpdb->get_row(
		$sql ); 
	
	return $temp;
}
function wp_ajax_noob_aao_booking_delete_shortcode_callbacks(){

	wp_enqueue_script( 'my-wp-ajax-noob-aao-booking-script' );	
	wp_enqueue_style( 'my-wp-ajax-noob-aao-booking-style' );


	return '<div id="containerpage">'. get_bookingdelete(). '</div>';
}


function get_bookingdelete()
{	
	global $wpdb;
	
	$sql = "
		SELECT *
		FROM  `wp_aao_bkg_areas` 
	";

	$areas = $wpdb->get_results( $sql ); 
                    
    $result = $result . '
    					<!-- Schermata PRENOTAZIONI ADMIN -->
 						<div class="form_prenotaz">
							<h1><span style="color:#d39c04 !important;">AMMINISTRAZIONE PRENOTAZIONI</span><br>Selezionare una data e un\'area da ricercare</h1>
							<form id="search-booking"> 
		               		<input id="date" name="date" type="date" placeholder="gg/mm/aaaa" value="'.$date.'"/>';

	$result = $result .'<select style="text-align:center;" id="area" name ="area">';

	foreach($areas as $key=>$row){
      $result = $result . "<option value='".$row->id."'>".$row->description."</option>";
   	}	
	$result = $result .'</select>';

	$result = $result .	'</form>';
	
	$result = $result . '<input style="margin-top:20px;" onclick="searchclick()" type="submit" value="Cerca">';	

	$result = $result .	'</div>';	
	
	return $result;

}



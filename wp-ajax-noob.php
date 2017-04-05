<?php
/**
 * Plugin Name: WordPress AJAX Example for Beginners
 * Plugin URI: https://shellcreeper.com/wp-ajax-for-beginners/
 * Description: Example plugin from tutorial "WordPress AJAX for Beginners"
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: https://shellcreeper.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-ajax-noob
 * Domain Path: /languages/
 *
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
**/
if ( ! defined( 'WPINC' ) ) { die; }


/* 1. REGISTER SHORTCODE
------------------------------------------ */

/* Init Hook */
add_action( 'init', 'my_wp_ajax_noob_plugin_init', 10 );

/**
 * Init Hook to Register Shortcode.
 * @since 1.0.0
 */
function my_wp_ajax_noob_plugin_init(){

	/* Register Shortcode */
	if(!session_id()) {
        session_start();
    }
    
	add_shortcode( 'john-cena', 'my_wp_ajax_noob_aao_booking_shortcode_callbacks' );

}

/**
 * Shortcode Callback
 * Just display empty div. The content will be added via AJAX.
 */
function my_wp_ajax_noob_aao_booking_shortcode_callbacks(){
	global $wpdb;
	

	wp_enqueue_script( 'my-wp-ajax-noob-aao-booking-script' );

	/* Output empty div. */
	
	if(!isset($_SESSION['sessionId'])) {
		$_SESSION['sessionId'] = time();
	} else {
   		 $value = '';
}
	
	return '<div id="containerpage">'. get_bookingdata(). '</div>';
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
	
	if($index == 0){
		$results = get_bookingdata();
	} elseif($index == 1){
		$results = get_areas() ;
	} elseif($index == 2){	
		$results = get_services($params['area']);
	} elseif($index == 3){
		$results = get_user_data();
	} elseif($index == 4){
		$results = get_summary();
	}
		//$results = "<h2> Sono data: ".$greeting."</h2>"; // Return String 
	die($results); 

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
		$params = array();
		parse_str($inputdata, $params);
		updateService($params['service'], $params);
	}elseif($index == 3){
		updateUserData($inputdata);
	}
	
}

function get_bookingdata()
{
	$session = $_SESSION['sessionId'];

	if ( $session < time() - (15 * 60))
		$_SESSION['sessionId'] = time();

	$row = getDataFromSession();

	
	$date = '';
	if ($row != null)
	{
		$dates = date_create_from_format('Y-m-d', $row->day);
		$date = date_format($dates, 'd-m-Y');
	}
	return '
 		<label>Scegli la data </label>
		<form id="dataora">
		<input id="index" type="hidden" value="0"/>
		<input id="date" name="date" class="dateclass" value="'.$date.'"/>
		</form>'
		 . getNavButtons(false, true);

}

function updateDateTime($date)
{
	if ($date != null)
	{
		global $wpdb;
		$session = $_SESSION['sessionId'];
	
		$dates = date_create_from_format('d-m-Y', $date);
		
		$row = getDataFromSession();
			
		$wpdb->query( $wpdb->prepare(  
				'INSERT INTO wp_aao_bkg_temp_bookings (session, day) 
				VALUES('.$session.', "'.date_format($dates, 'Y-m-d').'") ON DUPLICATE KEY UPDATE    
				 day="'. date_format($dates, 'Y-m-d') .'"
				 '));
		if ($row!= null && $row->day != date_format($dates, 'Y-m-d'))		 
			$wpdb->query(  'UPDATE wp_aao_bkg_temp_bookings SET areaId=null, persons=null, serviceId=null WHERE session='. $session );

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
			$wpdb->query(  'UPDATE wp_aao_bkg_temp_bookings SET areaId='. $area .', persons=null, serviceId=null WHERE session='. $session );
	}
}

function updateService($service, $param)
{
	if ($service != null)
	{
		global $wpdb;
		$session = $_SESSION['sessionId'];
				
		$adultIndex = 'adult'.$service;
		$childrenIndex = 'children'.$service;
		$adultqty = $param[$adultIndex]!= null ?$param[$adultIndex]:0;
		$childqty = $param[$childrenIndex]!= null ?$param[$childrenIndex]:0;
		$persons = "adult=". $adultqty . "&children=" .$childqty;

		$wpdb->query(  'UPDATE wp_aao_bkg_temp_bookings SET serviceId='. $service .', persons="'. $persons .'" WHERE session='. $session );
	}
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

function get_areas () {
	$result = '
		<label>Scegli l\'area</label>
		<form id="aree">
		<input id="index" type="hidden" value="1"/>
		';
	$row = getDataFromSession();
	
	$defarea = 0;
	if ($row != null)	
	{
		$defarea =  $row->areaId;
	}			
	global $wpdb;
	$areas = $wpdb->get_results( $wpdb->prepare( 
		"
		SELECT      *
		FROM        wp_aao_bkg_areas
	") ); 
	
	foreach($areas as $key=>$row){
		$result = $result . "<input type='radio' name='area' value='". $row->id ."' " .  ($row->id==$defarea? "checked":"" )   . ">".$row->description."</br>";
	}
	
	$result = $result . '</form>'
			 . getNavButtons(true, true);
			 
	return $result;
}

function get_services ($area) {
	$result =' 	
	<label>Scegli i servizi</label>
		<form id="servizi">
		<input id="index" type="hidden" value="2"/>';
	
	$sessionrow = getDataFromSession();
	
	if ($area == null && $sessionrow!=null){
		$area = $sessionrow->areaId;
	}
	if ( $sessionrow !=null && $sessionrow->persons != null )
		parse_str($sessionrow->persons, $params);
	
	
	global $wpdb;
	$services = $wpdb->get_results( $wpdb->prepare( 
		"
		SELECT      *
		FROM        wp_aao_bkg_services
		WHERE areaId=%d", $area) ); 
	
	foreach($services as $key=>$row){

		$result = $result . "<input type='radio' name='service' value=".$row->id."  ".  ($row->id==$sessionrow->serviceId? "checked":"" )   .">".$row->description. 
							"</input>
							<label>Max adulti ". $row->adultQty . "</label>
							<input name='adult".$row->id."' type='number' min='1' max='". $row->adultQty . "' value='". ($params!=null && $row->id==$sessionrow->serviceId?$params['adult']:"") ."' style='width:100px; ' ></input>
							<label>Max bambini ". $row->childrenQty . "</label>
							<input name='children".$row->id."' type='number' min='1' max='". $row->childrenQty . "' value='" . ($params!=null && $row->id==$sessionrow->serviceId?$params['children']:"") . "' style='width:100px; ' ></input>
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

	$result = '<label>Compila i tuoi dati</label>
	
		<form id="datiutente">
		<input id="index" type="hidden" value="3"/>
		<label for"name">Nome</label>
		<input id="name" name="name" value="'. ($params!=null?$params['name']:'') .'"></input></br> 
		<label for"surname">Cognome</label>
		<input id="surname" name="surname" value="'. ($params!=null?$params['surname']:'') .'"></input></br>
		<label for"email">Email</label>
		<input id="email" name="email" value="'. ($params!=null?$params['email']:'') .'"></input>
		</form>'
		 . getNavButtons(true, true);
		
	return $result;
}

function get_summary () {


	$result =' 	
	<label>Riepilogo</label><br/>';
	$sessionrow = getExtededDataFromSession();

	parse_str($sessionrow->userdata, $userdata);
	parse_str($sessionrow->persons, $persons);
	
	
	$result = $result . '<label>Nome:' . $userdata['name'] .'</label><br/>
		<label>Cognome:'. $userdata['surname'] .' </label><br/>
		<label>Email: '. $userdata['email'] .'</label><br/>
		<label>Area: '. $sessionrow->adesc .'</label><br/>
		<label>Servizio: '. $sessionrow->sdesc .'</label><br/>
		<label>Adulti: '. $persons['adult'] .'</label><br/>
		<label>Bambini: '. $persons['children'] .'</label><br/>
		
		';
	
	
	$result = $result 
			 . getNavButtons(true, false);

		
	return $result;
}



function getNavButtons($back, $next)
{

	$result = '';

	if ($back)
		$result = $result . '<button onclick="indietroclick()">indietro</button>';	

	if ($next)
		$result = $result . '<button onclick="avanticlick()">avanti</button>';	

	return $result;		
}


function getExtededDataFromSession()
{
	$session = $_SESSION['sessionId'];
	global $wpdb;
	$temp = $wpdb->get_row(
		"SELECT * , a.description AS adesc, s.description AS sdesc
			FROM  `wp_aao_bkg_temp_bookings` AS t
			LEFT JOIN wp_aao_bkg_areas AS a ON t.areaid = a.id
			LEFT JOIN wp_aao_bkg_services AS s ON t.serviceid = s.id
			WHERE	session=" . $session  ); 
	
	return $temp;
}

function getDataFromSession()
{
	$session = $_SESSION['sessionId'];
	global $wpdb;
	$temp = $wpdb->get_row(
		"
		SELECT      *
		FROM        wp_aao_bkg_temp_bookings
		WHERE		session=" . $session  ); 
	
	return $temp;
}

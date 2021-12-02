<?php

//const DB_HOST = '303.itpwebdev.com';
//const DB_NAME = 'kshan_fp_db';
//const DB_USER = 'kshan_fp_user';
//const DB_PASS = 'USCitp303!';

const DB_HOST = 'jtb9ia3h1pgevwb1.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
const DB_NAME = 'kr5i4ae5ehlrcpyy';
const DB_USER = 'sbl8g3th14lphbn4';
const DB_PASS = 'kh410npdl7kg6r8d';

function error_respond($status_code, $message)
{
	$response = [
		'status_code' => $status_code,
		'message' => $message,
	];
	echo json_encode($response);
	exit();
}

function get_mysqli(): mysqli
{
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($mysqli->connect_errno) {
		error_respond(401, $mysqli->error);
	}
	return $mysqli;
}


function check_user_id($mysqli, $user_id)
{
	$statement = $mysqli->prepare("SELECT * FROM user_info WHERE id = ?;");
	$statement->bind_param('i', $user_id);
	
	$statement->execute();
	$user_select_result = $statement->get_result();
	
	if (!$user_select_result) {
		error_respond(401, $mysqli->error);
	}
	if ($user_select_result->num_rows != 1) {
		error_respond(401, 'No such user.');
	}
	return $user_select_result;
}


function check_bill_id($mysqli, $bill_id){
	$statement = $mysqli->prepare("SELECT * FROM bill_info WHERE id = ?;");
	$statement->bind_param('i', $bill_id);
	
	$statement->execute();
	$bill_select_result = $statement->get_result();
	
	if (!$bill_select_result) {
		error_respond(401, $mysqli->error);
	}
	if ($bill_select_result->num_rows != 1) {
		error_respond(401, 'No such bill.');
	}
	return $bill_select_result;
}
<?php

const DB_HOST = '303.itpwebdev.com';
const DB_NAME = 'kshan_fp_db';
const DB_USER = 'kshan_fp_user';
const DB_PASS = 'USCitp303!';

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
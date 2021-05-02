<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$bill_id = $data['bill_id'];
if (!isset($bill_id) && trim($bill_id) === '') {
	error_respond(401, 'No bill_id provided.');
}

$mysqli = get_mysqli();

$bill_select_result = check_bill_id($bill_id);

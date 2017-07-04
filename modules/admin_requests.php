<?php

require_once WPATH . "modules/classes/Transactions.php";
$transactions = new Transactions();

if (isset($_GET['request_type']) || isset($_POST['request_type'])) {
    $request_type = isset($_GET['request_type']) ? $_GET['request_type'] : $_POST['request_type'];
    if ($request_type == "upload_book_photo") {

        $resource = curl_init();
        curl_setopt($resource, CURLOPT_URL, $url);
        curl_setopt($resource, CURLOPT_HEADER, 1);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($resource, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($resource, CURLOPT_COOKIE, 'apiToken=' . $token);
        $file = curl_exec($resource);
        curl_close($resource);

        $file_array = explode("\n\r", $file, 2);
        $header_array = explode("\n", $file_array[0]);
        foreach ($header_array as $header_value) {
            $header_pieces = explode(':', $header_value);
            if (count($header_pieces) == 2) {
                $headers[$header_pieces[0]] = trim($header_pieces[1]);
            }
        }
        header('Content-type: ' . $headers['Content-Type']);
        header('Content-Disposition: ' . $headers['Content-Disposition']);

//        $response = substr($file_array[1], 1);
//        $transactions->deliverResponse($response);

        $response['status'] = 200;
        $response['message'] = substr($file_array[1], 1);

        $json_response = json_encode($response);
        $transactions->deliverResponse($json_response);
//        
//        argDump(substr($file_array[1], 1));
//        argDump(substr($file_array[1], 1));
//        argDump(substr($file_array[1], 1));
//        exit();
//
//
//        $tmp_name = isset($_GET['tmp_name']) ? $_GET['tmp_name'] : $_POST['tmp_name'];
//        $location = isset($_GET['location']) ? $_GET['location'] : $_POST['location'];
//        if (move_uploaded_file($tmp_name, $location)) {
//            $response['status'] = 200;
//            $response['message'] = "Request processed successfully";
//        } else {
//            $response['status'] = 500;
//            $response['message'] = "Error processing request";
//        }
//        $json_response = json_encode($response);
//        $transactions->deliverResponse($json_response);
    }
}


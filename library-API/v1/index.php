<?php
header("Access-Control-Allow-Origin: *");
//including the required files
require_once '../include/DbOperation.php';
require '.././libs/vendor/slim/slim/Slim/Slim.php';


\Slim\Slim::registerAutoloader();

//Creating a slim instance
$app = new \Slim\Slim();

//Method to display response
function echoResponse($status_code, $response)
{
    //Getting app instance
    $app = \Slim\Slim::getInstance();

    //Setting Http response code
    $app->status($status_code);

    //setting response content type to json
    $app->contentType('application/json');

    //displaying the response in json format
    echo json_encode($response);
}


function verifyRequiredParams($required_fields)
{
    //Assuming there is no error
    $error = false;

    //Error fields are blank
    $error_fields = "";

    //Getting the request parameters
    $request_params = $_REQUEST;

    //Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        //Getting the app instance
        $app = \Slim\Slim::getInstance();

        //Getting put parameters in request params variable
        parse_str($app->request()->getBody(), $request_params);
    }

    //Looping through all the parameters
    foreach ($required_fields as $field) {

        //if any requred parameter is missing
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            //error is true
            $error = true;

            //Concatnating the missing parameters in error fields
            $error_fields .= $field . ', ';
        }
    }

    //if there is a parameter missing then error is true
    if ($error) {
        //Creating response array
        $response = array();

        //Getting app instance
        $app = \Slim\Slim::getInstance();

        //Adding values to response array
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';

        //Displaying response with error code 400
        echoResponse(400, $response);

        //Stopping the app
        $app->stop();
    }
}



$app->get('/getIssuedBookDetails/:userid', function($userid) use ($app){

    $db = new DbOperation();

    //Creating a response array
    $response = array();

    //If username password is correct
    if(true){

        //Getting user detail
        $items = $db->getIssuedBookDetails($userid);

        // $result = $items->get_result()->fetch_assoc();
    
        $response = array();
        $response['data'] = array();

        foreach($items as $rows) 
        {

           $response['data'][] = array(
           
          'Issued Book'=> $rows['title'],
          'Issue date'=> $rows['issuedate'],
          'Due date' => $rows['date_due']
        );  

        }
       

        //Generating response
       // $response['error'] = false;

        //$response['username'] = $student['username'];
        //$response['apikey'] = $student['api_key'];

    }else{
        //Generating response
        $response['error'] = true;
        $response['message'] = "Invalid api key";
    }

    //Displaying the response
    echoResponse(200,$response);
});




$app->get('/getFine/:userid', function($userid) use ($app){
   
    $db = new DbOperation();

    //Creating a response array
    $response = array();

    //If username password is correct
    if(true){

        //Getting user detail
        $varb = $db->getFine($userid);

        //Generating response
        $response['error'] = false;
        $response['data'] = array(

          'Enrollment Number'=> $varb['cardnumber'],
          'Fine Owed'=> $varb['total owed']
        );
        //$response['username'] = $student['username'];
        //$response['apikey'] = $student['api_key'];

    }else{
        //Generating response
        $response['error'] = true;
        $response['message'] = "Invalid api key";
    }

    //Displaying the response
    echoResponse(200,$response);
});


$app->run();
?>

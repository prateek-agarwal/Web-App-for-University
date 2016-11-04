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

//Method to authenticate a student
function authenticateStudent(\Slim\Route $route)
{
    //Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    //Verifying the headers
    if (isset($headers['Authorization'])) {

        //Creating a DatabaseOperation boject
        $db = new DbOperation();

        //Getting api key from header
        $api_key = $headers['Authorization'];

        //Validating apikey from database
        if (!$db->isValidStudent($api_key)) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoResponse(400, $response);
        $app->stop();
    }
}

//this method will create a student
//the first parameter is the URL address that will be added at last to the root url
//The method is put
$app->put('/student', function () use ($app) {

    //Verifying the required parameters
    verifyRequiredParams(array('email_id'));

    //Creating a response array
    $response = array();

    //reading post parameters
    $email_id = $app->request->put('email_id');

    //Creating a DbOperation object
    $db = new DbOperation();

    //Calling the method createStudent to add student to the database
    $res = $db->registerAPI($email_id);

    //If the result returned is 0 means success
    if ($res == 0) {
        //Making the response error false
        $response["error"] = false;
        //Adding a success message
        $response['data'] = array(
          "message"=> "User registered, password sent to registered email"
        );
        //Displaying response
        echoResponse(201, $response);

    //If the result returned is 1 means failure
    } else if ($res == 1) {
        $response["error"] = true;
        $response['data'] = array(
          "message"=> "Oops! you are not a valid student"
        );
        echoResponse(200, $response);

    //If the result returned is 2 means user already exist
    } else if ($res == 2) {
        $response["error"] = true;
        $response['data'] = array(
          "message"=> "Sorry, you are already registered"
        );
        echoResponse(200, $response);
    }
});

//Login request
$app->post('/getAPIKey',function() use ($app){
    //verifying required parameters
    verifyRequiredParams(array('email_id','password'));

    //getting post values
    $email_id = $app->request->post('email_id');
    $password = $app->request->post('password');

    //Creating DbOperation object
    $db = new DbOperation();

    //Creating a response array
    $response = array();

    //If username password is correct
    if($db->studentLogin($email_id,$password)){

        //Getting user detail
        $student = $db->getStudentApiKey($email_id);

        //Generating response
        $response['error'] = false;
        $response['data'] = array(
          'name' => $student['name'],
          'user_id'=> $student['user_id'],
          'email_id'=> $student['email_id'],
          'api_key'=> $student['api_key'],
        );

    }else{
        //Generating response
        $response['error'] = true;
        $response['message'] = "Invalid username or password";
    }

    //Displaying the response
    echoResponse(200,$response);
});

$app->put('/checkStatus', 'authenticateStudent', function () use ($app) {

    //Verifying the required parameters
    verifyRequiredParams(array('email_id'));

    //Creating a response array
    $response = array();

    //reading post parameters
    $email_id = $app->request->put('email_id');

    //Creating a DbOperation object
    $db = new DbOperation();

    
    $res = $db->registerAPI($email_id);

    //If the result returned is 0 means success
    if ($res == 0) {
        //Making the response error false
        $response["error"] = false;
        //Adding a success message
        $response['data'] = array(
          "message"=> "User registered, password sent to registered email"
        );
        //Displaying response
        echoResponse(201, $response);

    //If the result returned is 1 means failure
    } else if ($res == 1) {
        $response["error"] = true;
        $response['data'] = array(
          "message"=> "Oops! you are not a valid student"
        );
        echoResponse(200, $response);

    //If the result returned is 2 means user already exist
    } else if ($res == 2) {
        $response["error"] = true;
        $response['data'] = array(
          "message"=> "Sorry, you are already registered"
        );
        echoResponse(200, $response);
    }
});

$app->get('/student/:user_id', 'authenticateStudent', function($user_id) use ($app){
    //verifying required parameters
    // verifyRequiredParams(array('api_key'));

    //getting post values
    // $api_key = $app->request->get('api_key');

    //Creating DbOperation object
    $db = new DbOperation();

    //Creating a response array
    $response = array();

    //If username password is correct
    if(true){

        //Getting user detail
        $student = $db->getStudent($user_id);

        //Generating response
        $response['error'] = false;
        $response['data'] = array(
          'user_id'=> $student['user_id'],
          'name'=> $student['name'],
          'email_id'=> $student['email_id']
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

$app->options('/student', function () use ($app){

  //Getting request headers
  $headers = apache_request_headers();

  // Add allow methods.
  $app->response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT');

  $app->response->headers->set('Access-Control-Allow-Headers', 'content-type, Authorization');

  $response['error'] = false;

  echoResponse(200, $response);
});



$app->run();
?>

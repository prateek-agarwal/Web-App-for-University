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

/*
//Method to authenticate a student
function authenticateStudent(\Slim\Route $route)
{
    //Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    //Verifying the headers
    if (isset($headers['authorization'])) {

        //Creating a DatabaseOperation boject
        $db = new DbOperation();

        //Getting api key from header
        $api_key = $headers['authorization'];

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
*/


$app->post('/checkStatus', function () use ($app) {

    //Verifying the required parameters
    verifyRequiredParams(array('user_id'));

    //Creating a response array
    $response = array();

    //reading post parameters
    $user_id = $app->request->post('user_id');

    //Creating a DbOperation object
    $db = new DbOperation();

    
    $gatepass = $db->getGatepassStatus($user_id);

    if (isset($gatepass)) {
        $response['error'] = false;
        $response['data'] = $gatepass;
    }

    else {
        $response['error'] = true;
        $response['message'] = "Gatepass doesn't exist Or Invalid student";
    }


    //Displaying the response
    echoResponse(200,$response);
});

$app->post('/getPreApply', function () use ($app) {

    //Verifying the required parameters
    verifyRequiredParams(array('user_id'));

    //Creating a response array
    $response = array();

    //reading post parameters
    $user_id = $app->request->post('user_id');

    //Creating a DbOperation object
    $db = new DbOperation();
    if ($db->isStudentExists($user_id) == FALSE) {
         $response['error'] = true;
        $response['message'] = "Invalid student";
    }

    else {
        $preApply = array();
        
        $g_meta = array();
        // Check weather student black listed  or not
        $g_meta['blacklisted'] = $db->isBlackListed($user_id);
        // Group black listed or not
        $g_meta['blacklistedgroup'] = $db->isBlackListedGroup($user_id);

        // Checks weather student is present in the campus or not
        $status = $db->getStudentStatus($user_id);
        if ($status['status'] == 'P')
            $g_meta['ispresent'] = TRUE;
        else
            $g_meta['ispresent'] = FALSE;
        // Get the details of the fixed gatepass
        // Check the no of fixed gatepass left
        $fixed_details = $db->getFixedDetails();
        $g_meta['from_time'] = $fixed_details['Out Time']['value'];
        $g_meta['to_time'] = $fixed_details['In Time']['value'];
                    
        $used = $db->getStudentWeekUse($user_id);
        $limit = $db->weekLimit();
        $left = $limit - $used;
        $g_meta['fixed_left'] = $left;

        $response['error'] = false;
        $response['data'] = $g_meta;
    }


    //Displaying the response
    echoResponse(200,$response);
});


$app->put('/applyLocalFixedGatepass', function() use($app){
    //Verifying the required parameters
    verifyRequiredParams(array(
        'user_id'
    ));

    //Creating a response array
    $response = array();

    //reading post parameters
    $gatepass_data = $app->request->put();

    //Creating a DbOperation object
    $db = new DbOperation();

    // Check valid student or not
    if ($db->isStudentExists($gatepass_data['user_id']) == FALSE) {
        $response['error'] = true;
        $response['message'] = "Not a valid student";
    }

    else {

        $g_meta = array();
        // Check weather student black listed  or not
        $g_meta['blacklisted'] = $db->isBlackListed($gatepass_data['user_id']);
        if ($g_meta['blacklisted'] == TRUE) {
            $response['error'] = true;
            $response['message'] = "Student is blacklisted";         
        }

        else {
            // Check weather student group is black listed or not
            $g_meta['blacklistedgroup'] = $db->isBlackListedGroup($gatepass_data['user_id']);
            if ($g_meta['blacklistedgroup'] == TRUE) {
                        $response['error'] = true;
                        $response['message'] = "Student's batch is blacklisted";         
                    }
            else {

                // Check weather in the college or not
                $status = $db->getStudentStatus($gatepass_data['user_id']);
                if ($status['status'] == 'P')
                    $g_meta['ispresent'] = TRUE;
                else
                    $g_meta['ispresent'] = FALSE;
                
                if ($g_meta['ispresent'] == FALSE) {
                    $response['error'] = true;
                    $response['message'] = "Student is not present in campus";         
                }

                else {
                    // Check weather already applied or not
                    // Get the fixed gatepass details
                    $fixed_details = $db->getFixedDetails();
                    $gatepass_details['from_time'] = $fixed_details['Out Time']['value'];
                    $gatepass_details['to_time'] = $fixed_details['In Time']['value'];
                    
                    $g_meta['alreadyapplied'] = $db->hasAlreadyApplied($gatepass_data['user_id'], date('Y-m-d'), $gatepass_details['from_time'], date('Y-m-d'), $gatepass_details['to_time']);
                    if ($g_meta['alreadyapplied'] == TRUE) {
                        $response['error'] = true;
                        $response['message'] = "Student has already applied in this time interval";         
                    }
                    
                    else {

                        // Check the no of fixed gatepass left
                        $used = $db->getStudentWeekUse($gatepass_data['user_id']);
                        $limit = $db->weekLimit();
                        $left = $limit - $used;
                        $g_meta['fixedleft'] = $left;
                        

                        if ($g_meta['fixedleft'] <= 0) {
                            $response['error'] = true;
                            $response['message'] = "Student has reached his weekly limit";
                        }
                        
                        else {
                            // Prepare the data
                            $time = time();
                            $gatepass_details['user_id'] = $gatepass_data['user_id'];
                            $gatepass_details['gatepass_type'] = 1;
                            $gatepass_details['from_date'] = date('Y-m-d');
                            $gatepass_details['to_date'] = date('Y-m-d');
                            $gatepass_details['applied_date'] = date('Y-m-d');
                            $gatepass_details['applied_time'] = date('H:i:s', $time);
                            $gatepass_details['send_approval_to'] = '100240';
                            $gatepass_details['status'] = "AutoApproved";
                            $gatepass_details['purpose'] = "Local Visit";
                            $gatepass_details['destination'] = "NEEMRANA";
                            $gatepass_details['visit_to'] = "NEEMRANA";
                            $gatepass_details['comments'] = "NA";
                            $gatepass_details['destination_contact'] = NULL;
                            
                            // Apply the gatepass
                            $res = $db->ApplyGatepass($gatepass_details);

                            if ($res == 0) {
                                $response['error'] = false;
                                $response['message'] = "Gatepass Applied";
                            }

                            else {
                                $response['error'] = true;
                                $response['message'] = "Error applying gatepass";
                            }
                        }
                    }
                }
            }
        }
    }

    //Displaying the response
    echoResponse(200,$response);
});

$app->put('/applyLocalFlexibleGatepass', function() use($app){
    //Verifying the required parameters
    verifyRequiredParams(array(
        'user_id',
        'purpose',
        'from_time',
        'to_time',
        'send_approval_to'
    ));

    //Creating a response array
    $response = array();

    //reading post parameters
    $gatepass_data = $app->request->put();

    //Creating a DbOperation object
    $db = new DbOperation();

    // Check valid student or not
    if ($db->isStudentExists($gatepass_data['user_id']) == FALSE) {
        $response['error'] = true;
        $response['message'] = "Not a valid student";
    }

    else {

        $g_meta = array();
        // Check weather student black listed  or not
        $g_meta['blacklisted'] = $db->isBlackListed($gatepass_data['user_id']);
        if ($g_meta['blacklisted'] == TRUE) {
            $response['error'] = true;
            $response['message'] = "Student is blacklisted";         
        }

        else {
            // Check weather student group is black listed or not
            $g_meta['blacklistedgroup'] = $db->isBlackListedGroup($gatepass_data['user_id']);
            if ($g_meta['blacklistedgroup'] == TRUE) {
                        $response['error'] = true;
                        $response['message'] = "Student's batch is blacklisted";         
                    }
            else {

                // Check weather in the college or not
                $status = $db->getStudentStatus($gatepass_data['user_id']);
                if ($status['status'] == 'P')
                    $g_meta['ispresent'] = TRUE;
                else
                    $g_meta['ispresent'] = FALSE;
                
                if ($g_meta['ispresent'] == FALSE) {
                    echo $g_meta['ispresent'];
                    $response['error'] = true;
                    $response['message'] = "Student is not present in campus";         
                }

                else {
                    // Check weather already applied or not
                    $gatepass_details['from_time'] = $gatepass_data['from_time'];
                    $gatepass_details['to_time'] = $gatepass_data['to_time'];
                    
                    $g_meta['alreadyapplied'] = $db->hasAlreadyApplied($gatepass_data['user_id'], date('Y-m-d'), $gatepass_details['from_time'], date('Y-m-d'), $gatepass_details['to_time']);
                    if ($g_meta['alreadyapplied'] == TRUE) {
                        $response['error'] = true;
                        $response['message'] = "Student has already applied in this time interval";         
                    }
                    
                    else {

                        if ($db->isOnAutoapproval($gatepass_data['user_id'], date('Y-m-d'), $gatepass_details['from_time'], date('Y-m-d'), $gatepass_details['to_time']) == TRUE)
                            $gatepass_details['status'] = "AutoApproved";
                        else
                            $gatepass_details['status'] = "Pending";
                        // Prepare the data
                        $time = time();
                        $gatepass_details['user_id'] = $gatepass_data['user_id'];
                        $gatepass_details['gatepass_type'] = 2;
                        $gatepass_details['from_date'] = date('Y-m-d');
                        $gatepass_details['to_date'] = date('Y-m-d');
                        $gatepass_details['applied_date'] = date('Y-m-d');
                        $gatepass_details['applied_time'] = date('H:i:s', $time);
                        $gatepass_details['send_approval_to'] = $db->getUserId($gatepass_data['send_approval_to']);
                        $gatepass_details['purpose'] = $gatepass_data['purpose'];
                        $gatepass_details['destination'] = "NEEMRANA";
                        $gatepass_details['visit_to'] = "NEEMRANA";
                        $gatepass_details['comments'] = "NA";
                        $gatepass_details['destination_contact'] = NULL;
                        
                        // Apply the gatepass
                        $res = $db->ApplyGatepass($gatepass_details);

                        if ($res == 0) {
                            $response['error'] = false;
                            $response['message'] = "Gatepass Applied";
                        }

                        else {
                            $response['error'] = true;
                            $response['message'] = "Error applying gatepass";
                        }
                    
                    }
                }
            }
        }
    }

    //Displaying the response
    echoResponse(200,$response);
});


$app->put('/applyOutstationGatepass', function() use($app){
    //Verifying the required parameters
    verifyRequiredParams(array(
        'user_id',
        'purpose',
        'destination',
        'destination_contact',
        'from_date',
        'from_time',
        'to_date',
        'to_time',
        'visit_to',
        'send_approval_to'
    ));

    //Creating a response array
    $response = array();

    //reading post parameters
    $gatepass_data = $app->request->put();

    //Creating a DbOperation object
    $db = new DbOperation();

    // Check valid student or not
    if ($db->isStudentExists($gatepass_data['user_id']) == FALSE) {
        $response['error'] = true;
        $response['message'] = "Not a valid student";
    }

    else {

        $g_meta = array();
        // Check weather student black listed  or not
        $g_meta['blacklisted'] = $db->isBlackListed($gatepass_data['user_id']);
        if ($g_meta['blacklisted'] == TRUE) {
            $response['error'] = true;
            $response['message'] = "Student is blacklisted";         
        }

        else {
            // Check weather student group is black listed or not
            $g_meta['blacklistedgroup'] = $db->isBlackListedGroup($gatepass_data['user_id']);
            if ($g_meta['blacklistedgroup'] == TRUE) {
                        $response['error'] = true;
                        $response['message'] = "Student's batch is blacklisted";         
                    }
            else {

                // Check weather in the college or not
                $status = $db->getStudentStatus($gatepass_data['user_id']);
                if ($status['status'] == 'P')
                    $g_meta['ispresent'] = TRUE;
                else
                    $g_meta['ispresent'] = FALSE;
                
                if ($g_meta['ispresent'] == FALSE) {
                    echo $g_meta['ispresent'];
                    $response['error'] = true;
                    $response['message'] = "Student is not present in campus";         
                }

                else {
                    // Check weather already applied or not
                    $gatepass_details['from_time'] = $gatepass_data['from_time'];
                    $gatepass_details['to_time'] = $gatepass_data['to_time'];
                    $gatepass_details['from_date'] = $gatepass_data['from_date'];
                    $gatepass_details['to_date'] = $gatepass_data['to_date'];
                    
                    
                    $g_meta['alreadyapplied'] = $db->hasAlreadyApplied($gatepass_data['user_id'], $gatepass_details['from_date'], $gatepass_details['from_time'], $gatepass_details['to_date'], $gatepass_details['to_time']);
                    if ($g_meta['alreadyapplied'] == TRUE) {
                        $response['error'] = true;
                        $response['message'] = "Student has already applied in this time interval";         
                    }
                    
                    else {
                        if ($db->isOnAutoapproval($gatepass_data['user_id'], $gatepass_details['from_date'], $gatepass_details['from_time'], $gatepass_details['to_date'], $gatepass_details['to_time']) == TRUE)
                            $gatepass_details['status'] = "AutoApproved";
                        else
                            $gatepass_details['status'] = "Pending";
                        // Prepare the data
                        $time = time();
                        $gatepass_details['user_id'] = $gatepass_data['user_id'];
                        $gatepass_details['gatepass_type'] = 2;
                        $gatepass_details['applied_date'] = date('Y-m-d');
                        $gatepass_details['applied_time'] = date('H:i:s', $time);
                        $gatepass_details['send_approval_to'] = $db->getUserId($gatepass_data['send_approval_to']);
                        $gatepass_details['purpose'] = $gatepass_data['purpose'];
                        $gatepass_details['destination'] = $gatepass_data['destination'];
                        $gatepass_details['visit_to'] = $gatepass_data['visit_to'];
                        $gatepass_details['comments'] = "NA";
                        $gatepass_details['destination_contact'] = $gatepass_data['destination_contact'];
                        
                        // Apply the gatepass
                        $res = $db->ApplyGatepass($gatepass_details);

                        if ($res == 0) {
                            $response['error'] = false;
                            $response['message'] = "Gatepass Applied";
                        }

                        else {
                            $response['error'] = true;
                            $response['message'] = "Error applying gatepass";
                        }
                    
                    }
                }
            }
        }
    }

    //Displaying the response
    echoResponse(200,$response);
});



$app->get('/student/:user_id', function($user_id) use ($app){
    

    //Creating DbOperation object
    $db = new DbOperation();

    //Creating a response array
    $response = array();

    //Getting user detail
    $student = $db->getStudent($user_id);

    //Generating response
    $response['error'] = false;
    $response['data'] = array(
        'user_id'=> $student['user_id'],
        'name'=> $student['name'],
        'email_id'=> $student['email_id']
    );
    
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

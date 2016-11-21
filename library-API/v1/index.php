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

//Api for getting issued book on the name if user/student

$app->get('/getIssuedBookDetails/:userid', function($userid) use ($app){

    $db = new DbOperation();

    $response = array();

    $items = $db->getIssuedBookDetails($userid);

    $response = array();
    $response['data'] = array();

    foreach($items as $rows) {

        $response['data'][] = array(
        
        'Issued Book'=> $rows['title'],
        'Issue date'=> $rows['issuedate'],
        'Due date' => $rows['date_due']
        );  

    }

    

    echoResponse(200,$response);
});


//API for getting the fine owed by the students.

$app->get('/getFine/:userid', function($userid) use ($app){
   
    $db = new DbOperation();
    $response = array();

    $varb = $db->getFine($userid);

    if($varb['total owed'] == NULL)
            $varb['total owed'] = 0.0;
        

        $response['error'] = false;
        $response['data'] = array(

          'Enrollment Number'=> $varb['cardnumber'],
          'Fine Owed'=> $varb['total owed']
        );
       
    

    echoResponse(200,$response);
});


//API for getting the search book details

$app->get('/getBook/:keyword', function($keyword) use ($app){
   
    $db = new DbOperation();

    $response = array();

    $var = $db->getBook($keyword);

    $response['error'] = false;
    $response['data'] = array(

        'Title' => $var['title'],
        'Author' => $var['author'],
        'ISBN' => $var['isbn'],
        'Publication Year' => $var['publicationyear'],
        'Edition' => $var['editionstatement']
    );

    
    echoResponse(200,$response);
});

$app->run();
?>

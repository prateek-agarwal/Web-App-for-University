<?php

class DbOperation
{
    //Database connection link
    private $con;

    //Class constructor
    function __construct()
    {
        //Getting the DbConnect.php file
        require_once dirname(__FILE__) . '/DbConnect.php';

        //Creating a DbConnect object to connect to the database
        $db = new DbConnect();

        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
        $this->con = $db->connect();
    }
    //This method will return student detail
    public function getStudent($email_id){
        $stmt = $this->con->prepare("SELECT * FROM gps_usersmaster WHERE email_id=?");
        $stmt->bind_param("s",$email_id);
        $stmt->execute();
        //Getting the student result array
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        //returning the student
        return $student;
    }
    //Method will create a new student in gps_api
    public function registerAPI($email_id){

        //First we will check whether the student exists or not
        if (!$this->isStudentExists($email_id)) {

            return 1;

        } else {

            // Checks if user is registered with the app or not.
            if (!$this -> isRegisteredForAPI($email_id)) {
              // Encrypt the password.

              // Password is mailed to the users registered email_id.
              // TODO
              $password = md5('hello');
              $api_key = $this -> generateApiKey();

              $student = $this -> getStudent($email_id);

              $stmt = $this->con->prepare("INSERT INTO gps_api(name, user_id, email_id, api_key, passwd) values(?, ?, ?, ?, ?)");
              //Binding the parameters
              $stmt->bind_param("sssss", $student['name'], $student['user_id'], $email_id, $api_key, $password);

              //Executing the statment
              $result = $stmt->execute();

              //Closing the statment
              $stmt->close();

              //If statment executed successfully
              if ($result) {
                  //Returning 0 means student created successfully
                  return 0;
              } else {
                  //Returning 1 means failed to create student
                  return 1;
              }
            }

            else {
              //returning 2 means user already exist in the database
              return 2;
            }
        }
    }

    //Method for student login
    public function studentLogin($email_id,$pass){
        //Generating password hash
        $password = md5($pass);
        //Creating query
        $stmt = $this->con->prepare("SELECT * FROM gps_api WHERE email_id=? and passwd=?");
        //binding the parameters
        $stmt->bind_param("ss",$email_id,$password);
        //executing the query
        $stmt->execute();
        //Storing result
        $stmt->store_result();
        //Getting the result
        $num_rows = $stmt->num_rows;
        //closing the statment
        $stmt->close();
        //If the result value is greater than 0 means user found in the database with given username and password
        //So returning true
        return $num_rows>0;
    }


    //This method will return student detail
    public function getStudentApiKey($email_id){
        $stmt = $this->con->prepare("SELECT * FROM gps_api WHERE email_id=?");
        $stmt->bind_param("s", $email_id);
        $stmt->execute();
        //Getting the student result array
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        //returning the student
        return $student;
    }


    //Checking whether a student already exist
    private function isStudentExists($user_id) {

        $stmt = $this->con->prepare("SELECT name, user_id, email_id from gps_usersmaster WHERE email_id = ?");
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    private function isRegisteredForAPI($user_id) {
      $stmt = $this->con->prepare("SELECT email_id from gps_api WHERE email_id = ?");
      $stmt->bind_param('s', $user_id);
      $stmt->execute();
      $stmt->store_result();
      $num_rows = $stmt->num_rows;
      $stmt->close();
      return $num_rows > 0;
    }

    //Method to get assignments
    private function getAssignments($id){
        $stmt = $this->con->prepare("SELECT * FFROM assignments WHERE students_id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $assignments = $stmt->get_result()->fetch_assoc();
        return $assignments;
    }

    /*
     * Methods to check a user is valid or not using api key
     * I will not write comments to every method as the same thing is done in each method
     * */
    public function isValidStudent($api_key) {
        //Creating an statement
        $stmt = $this->con->prepare("SELECT user_id from gps_api WHERE api_key = ?");

        //Binding parameters to statement with this
        //the question mark of queries will be replaced with the actual values
        $stmt->bind_param("s", $api_key);

        //Executing the statement
        $stmt->execute();

        //Storing the results
        $stmt->store_result();

        //Getting the rows from the database
        //As API Key is always unique so we will get either a row or no row
        $num_rows = $stmt->num_rows;

        //Closing the statment
        $stmt->close();

        //If the fetched row is greater than 0 returning  true means user is valid
        return $num_rows > 0;
    }

    //This method will generate a unique api key
    private function generateApiKey(){
        return md5(uniqid(rand(), true));
    }
}

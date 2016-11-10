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

    public function getGatepassStatus($email_id) {
        if ($this->isStudentExists($email_id)) {
            if ($this->isRegisteredForAPI($email_id)) {
                $stmt = $this->con->prepare("SELECT user_id FROM gps_api WHERE email_id=?");
                $stmt->bind_param("s", $email_id);
                $stmt->execute();
                
                $user_id = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                
                
                $stmt = $this->con->prepare("SELECT * FROM gps_gatepassmaster WHERE user_id=? ORDER BY applied_date DESC, applied_time DESC");
                $stmt->bind_param("s", $user_id[0]);
                $stmt->execute();
                $res = $stmt->get_result();
                $gatepass = $res->fetch_assoc();
                $stmt->close();
                //returning the gatepass
                return $gatepass;
            }
        }
    }

    public function getPreApply($email_id) {
        if ($this->isStudentExists($email_id)) {
            if ($this->isRegisteredForAPI($email_id)) {

                $stmt = $this->con->prepare("SELECT user_id FROM gps_api WHERE email_id=?");
                $stmt->bind_param("s", $email_id);
                $stmt->execute();

                $user_id = $stmt->get_result()->fetch_assoc();
                $stmt->close();
        
                $result['black_listed'] = $this->isBlackListed($user_id);
                
                $status = $this->getStudentStatus($user_id);
                if ($status == 'A' || $status == 'a')
                    $result['checked_out'] = true;
                else
                    $result['checked_out'] = FALSE;
                
                $result['warden_list'] = $this->getWardenList();

                $result['auto_approval'] = $this->isOnAutoApproval($user_id);

                $details = $this->getFixedDetails();
                $result['fixed_out_time'] = $details['Out Time']['value'];
                $result['fixed_in_time'] = $details['In Time']['value'];

                // TODO change the weekly limit, to actual left
                $result['local_fixed_left'] = $details['Week Limit']['value'];

                return $result;
            }
        }

        return NULL;
    }

    public function ApplyGatepass($data) {
        if ($this->isBlackListed($data['user_id'] == true))
            return 1; // 1 - Means student is black listed and can't apply gatepass.
        $status = $this->getStudentStatus($data['user_id']);
        if ($status == 'A')
            return 2; // 2 - Student is not in college, can't apply
        if ($this->hasAlreadyApplied($data['user_id'], $data['from_date'], $data['from_time'], $data['to_date'], $data['to_time']) == TRUE)
            return 3;

        if ($data['gatepass_type'] == 1) {
            // LOCAL FIXED
            // Check if limit reached or not
            $used = $this->getStudentWeekUse($data['user_id']);
            $limit = $this->weekLimit();
            $left = $limit - $used;
            if ($left <= 0)
                return 4;
            // Get all data for applying
            $fixed_details = $this->getFixedDetails();
            // if $left > 0 then apply gatepass as auto approved
            $time = time();
            $insert_data = array(
                'user_id' => $data['user_id'],
                'gatepass_type' => 1,
                'from_date' => date('Y-m-d'),
                'from_time' => $fixed_details['Out Time']['value'],
                'to_date' => date('Y-m-d'),
                'to_time' => $fixed_details['In Time']['value'],
                'applied_date' => date('Y-m-d'),
                'applied_time' => date('H:i:s', $time),
                'send_approval_to' =>"100240",
                'status' => "AutoApproved",
                'purpose' => "Local Visit",
                'destination' => "NEEMRANA",
                'visit_to' => "NEEMRANA",
                'comments' => "NA"
            );
            
            /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
            $a_params = array();
            $param_type = 'sissssssssssssssssss';
            $n = 20;
            
            $sql = "INSERT INTO gps_gatepassmaster (user_id, gatepass_type, 
            from_date, from_time, to_date, to_time, applied_date, applied_time, 
            send_approval_to, status,purpose,destination,visit_to,comments) VALUES (?,
            ?,?,?,?,?,?,?,?,?,?,?,?,?)"; 
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("sissssssssssss", 
                $insert_data['user_id'],
                $insert_data['gatepass_type'],
                $insert_data['from_date'],
                $insert_data['from_time'],
                $insert_data['to_date'],
                $insert_data['to_time'],
                $insert_data['applied_date'],
                $insert_data['applied_time'],
                $insert_data['send_approval_to'],
                $insert_data['status'],
                $insert_data['purpose'],
                $insert_data['destination'],
                $insert_data['visit_to'],
                $insert_data['comments']
                );
                
            /* use call_user_func_array, as $stmt->bind_param('s', $param); does not accept params array */
            // call_user_func_array(array($stmt, 'bind_param'), $a_params);
            
            //Executing the statment
              $result = $stmt->execute();

              //Closing the statment
              $stmt->close();

              //If statment executed successfully
              if ($result) {
                  return 0;
              } else {
                  return 5;
              }
        }
    }

    private function weekLimit() {
        $stmt = $this->con->prepare("SELECT * FROM gps_configmaster WHERE param_id = 1");
        $stmt->execute();
        $res = $stmt->get_result();
        
        while($row = $res->fetch_assoc()) {
            $limit = $row['value'];
            $stmt->close();
            return $limit;
        }
    }

    private function getStudentWeekUse($user_id) {
        $range = $this->getWeekRange();
        $stmt = $this->con->prepare("SELECT count(*) as total FROM gps_gatepassmaster WHERE gatepass_type = 1 AND user_id = ? AND (applied_date BETWEEN ? and ?)");
        $stmt->bind_param("sss", $user_id, $range[0], $range[1]);
        $stmt->execute();
        $res = $stmt->get_result();

        while($row = $res->fetch_assoc()) {
            return $row['total'];
        }

        return $res[0]->total;
    }

    private function isBlackListed($user_id) {
        // Black listed 
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');

        $stmt = $this->con->prepare("SELECT * FROM gps_blacklist_students WHERE user_id=?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows == 0) {
            $stmt->close();
            return FALSE;
        }

        while($row = $res->fetch_assoc()) {
            $date = $row->to_date;
            $time = $row->to_time;

            if (strtotime($date." ".$time) > strtotime($current_date." ".$current_time)) {
                $stmt->close();
                return TRUE;
            }
        }
        // TODO Add blacklisted group check

        $stmt->close();
        return FALSE;
    }

    private function getStudentStatus($user_id) {
        $stmt = $this->con->prepare("SELECT status FROM gps_usersmaster WHERE user_id=?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $status = $res->fetch_assoc();
        $stmt->close();
        //returning the status
        return $status;
    }
    
    private function getWardenList() {
        $stmt = $this->con->prepare("SELECT name FROM gps_usersmaster WHERE role_id=2");
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            // output data of each row
            $i = 0;
            while($row = $res->fetch_assoc()) {
                $wardens[$i++] = $row;
            }
        }

        $stmt->close();
        //returning the status
        return $wardens;
    }
 
    private function isOnAutoApproval($user_id, $from_date, $from_time, $to_date, $to_time) {
        
        $current_date = data('Y-m-d');
        $current_time = date('H:i:s');
        $group_id = $this->getGroupId($user_id);
        $subgroup_id = $this->getSubGroupId($user_id);
        
        $stmt = $this->con->prepare("SELECT * FROM gps_autoapprove WHERE group_id=? and subgroup_id=?");
        $stmt->bind_param("ss", $group_id, $subgroup_id);
        $stmt->execute();
        $res = $stmt->get_result();
        // $status = $res->fetch_assoc();
        $rows = $res->num_rows;

        if ($rows == 0) {
            $stmt->close();
            return FALSE;
        }

        $i = 0;
        while($row = $res->fetch_assoc()) {
            if (strtotime($from_date." ".$from_time) > strtotime($row->from_date." ".$row->from_time)) {
                if (strtotime($to_date." ".$to_time) < strtotime($row->to_date." ".$row->to_time)) {
                    $stmt->close();
                    return TRUE;
                }
            }
        }

        $stmt->close();
        return FALSE;
    }

    private function hasAlreadyApplied($user_id, $from_date, $from_time, $to_date, $to_time) {
        
        $stmt = $this->con->prepare("SELECT * FROM gps_gatepassmaster WHERE user_id=? and status NOT IN ('Cancelled' , 'Rejected') ORDER BY applied_date DESC LIMIT 5");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        // $status = $res->fetch_assoc();
        $rows = $res->num_rows;

        if ($rows == 0) {
            $stmt->close();
            return FALSE;
        }

        $i = 0;
        while($row = $res->fetch_assoc()) {
            if (strtotime($from_date." ".$from_time) <= strtotime($row['from_date']." ".$row['from_time'])) {
                if (strtotime($to_date." ".$to_time) >= strtotime($row['to_date']." ".$row['to_time'])) {
                    $stmt->close();
                    return TRUE;
                }
            }
        }

        $stmt->close();
        return FALSE;
        
    }

    private function getGroupId($user_id) {
        $stmt = $this->con->prepare("SELECT group_id FROM gps_usersmaster WHERE user_id=?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $group_id = $res->fetch_assoc();
        $stmt->close();
        
        return $group_id[0];
    }

    private function getSubGroupId($user_id) {
        $stmt = $this->con->prepare("SELECT subgroup_id FROM gps_usersmaster WHERE user_id=?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $subgroup_id = $res->fetch_assoc();
        $stmt->close();
        
        return $subgroup_id[0];
    }
    
    private function getCheifWarden()
    {
        /*
        $u = "select * from gps_usersmaster
        where role_id = 3
        limit 1";
        $q = $this->db->query($u);
        return $q->result[0]->name;

        $stmt = $this->con->prepare("SELECT * FROM gps_gps_usersmaster WHERE role_id = 3 LIMIT 1");
        $stmt->execute();
        $res = $stmt->get_result();
        $warden = $res->fetch_assoc();
        $stmt->close();
        //returning the status
        return $warden[0]['name'];
        */

        return 'Kumar Vishal';
    }
    private function getFixedDetails() {
        
        $stmt = $this->con->prepare("SELECT * FROM gps_configmaster");
        $stmt->execute();
        $res = $stmt->get_result();
        $details['Week Limit'] = $res->fetch_assoc();
        $details['Out Time'] = $res->fetch_assoc();
        $details['In Time'] = $res->fetch_assoc();
        $stmt->close();
        //returning the status
        return $details;
    }

    public function getWeekRange() {
        $date = date("Y-m-d");
        $ts = strtotime($date);
        $start = (date('w', $ts) == 1) ? $ts : strtotime('last monday', $ts);
        return  array(date('Y-m-d', $start),
                   date('Y-m-d', strtotime('next sunday', $start)));
  	}

    //Checking whether a student already exist
    private function isStudentExists($email_id) {

        $stmt = $this->con->prepare("SELECT name, user_id, email_id from gps_usersmaster WHERE email_id = ?");
        $stmt->bind_param('s', $email_id);
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

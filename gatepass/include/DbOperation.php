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

    

    public function getGatepassStatus($user_id) {
        if ($this->isStudentExists($user_id) == TRUE) {
            $stmt = $this->con->prepare("SELECT * FROM gps_gatepassmaster WHERE user_id=? ORDER BY applied_date DESC, applied_time DESC");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $gatepass = $res->fetch_assoc();
            $stmt->close();
            //returning the gatepass
            return $gatepass;            
        }

        return NULL;
    }


    public function ApplyGatepass($data) {
            
            $sql = "INSERT INTO gps_gatepassmaster (user_id, gatepass_type, 
            from_date, from_time, to_date, to_time, applied_date, applied_time, 
            send_approval_to, status,purpose,destination, destination_contact, visit_to,comments) VALUES (?,
            ?,?,?,?,?,?,?,?,?,?,?,?,?,?)"; 
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("sissssssssssiss", 
                $data['user_id'],
                $data['gatepass_type'],
                $data['from_date'],
                $data['from_time'],
                $data['to_date'],
                $data['to_time'],
                $data['applied_date'],
                $data['applied_time'],
                $data['send_approval_to'],
                $data['status'],
                $data['purpose'],
                $data['destination'],
                $data['destination_contact'],
                $data['visit_to'],
                $data['comments']
                );
            
            //Executing the statment
            $result = $stmt->execute();

            //Closing the statment
            $stmt->close();

            //If statment executed successfully
            if ($result)
                return 0;
            else 
                return 1;           
    }

    public function getUserId($name) {
        $stmt = $this->con->prepare("SELECT user_id FROM gps_usersmaster WHERE name like ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($id = $res->fetch_assoc())
            return $id['user_id'];
        else
            return NULL;
    }

    public function weekLimit() {
        $stmt = $this->con->prepare("SELECT * FROM gps_configmaster WHERE param_id = 1");
        $stmt->execute();
        $res = $stmt->get_result();
        
        while($row = $res->fetch_assoc()) {
            $limit = $row['value'];
            $stmt->close();
            return $limit;
        }
    }

    public function getStudentWeekUse($user_id) {
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

    public function isBlackListed($user_id) {
        // Black listed 
        $time_now = time();

        $stmt = $this->con->prepare("SELECT * FROM gps_blacklist_students WHERE user_id=?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows == 0) {
            $stmt->close();
            return FALSE;
        }

        while($row = $res->fetch_assoc()) {
            $date = $row['to_date'];
            $time = $row['to_time'];

            $time_blacklisted = $date. " ". $time;

            if ($time_blacklisted > $time_now) {
                $stmt->close();
                return TRUE;
            }
        }
        $stmt->close();
        return FALSE;
    }

    public function isBlackListedGroup($user_id) {
        // Black listed 
        $time_now = time();
        
        $g = $this->getGroupId($user_id);
        $s = $this->getSubGroupId($user_id);
          
        $stmt = $this->con->prepare("SELECT * FROM gps_blacklistgroup WHERE group_id=? and subgroup_id=?");
        $stmt->bind_param("ii", $g['group_id'], $s['subgroup_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows == 0) {
            $stmt->close();
            return FALSE;
        }

        while($row = $res->fetch_assoc()) {
            $date = $row['to_date'];
            $time = $row['to_time'];

            $time_blacklisted = $date. " ". $time;

            if ($time_blacklisted > $time_now) {
                $stmt->close();
                return TRUE;
            }
        }
        $stmt->close();
        return FALSE;
    }
    public function getStudentStatus($user_id) {
        $stmt = $this->con->prepare("SELECT status FROM gps_usersmaster WHERE user_id=?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $status = $res->fetch_assoc();
        $stmt->close();
        //returning the status
        return $status;
    }
    
    public function getWardenList() {
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
 
    public function isOnAutoApproval($user_id, $from_date, $from_time, $to_date, $to_time) {
        
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        $group_id = $this->getGroupId($user_id);
        $subgroup_id = $this->getSubGroupId($user_id);
        
        $stmt = $this->con->prepare("SELECT * FROM gps_autoapprove WHERE group_id=? and subgroup_id=?");
        $stmt->bind_param("ii", $group_id['group_id'], $subgroup_id['subgroup_id']);
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

    public function hasAlreadyApplied($user_id, $from_date, $from_time, $to_date, $to_time) {
        
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
        
        return $group_id;
    }

    private function getSubGroupId($user_id) {
        $stmt = $this->con->prepare("SELECT subgroup_id FROM gps_usersmaster WHERE user_id=?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $subgroup_id = $res->fetch_assoc();
        $stmt->close();
        
        return $subgroup_id;
    }
    /*
    public function getCheifWarden()
    {
    }

    */
    public function getFixedDetails() {
        
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
    public function isStudentExists($user_id) {

        $stmt = $this->con->prepare("SELECT * from gps_usersmaster WHERE user_id=?");
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
}

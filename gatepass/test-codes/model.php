<?php
 class studentModel extends CI_Model{

   public function __construct(){
          parent::__construct();
   }
   public function getWardenName($id){

     $u = "select * from gps_usersmaster
            where user_id = '$id'";
     $query = $this->db->query($u);
     return $query->result()[0];
   }

     public function applyVisitorGatepass($id, $visitor_name, $relation, $arr_date, $arr_time, $dep_date, $dep_time, $reason, $send_approval)
      {
          //check for autoapp
              $data = array(
                'user_id' => $id,
                'visitor_name' => $visitor_name,
                'relation' => $relation,
                'purpose_of_visit' => $reason,
                'arrival_date' => $arr_date,
                'arrival_time' => $arr_time,
                'departure_date' => $dep_date,
                'departure_time' => $dep_time,
                'applied_date' => date('Y-m-d'),
                'applied_time' => date('H:i:s', $time),
                'send_approval_to' => $send_approval,
                'status' => "Pending",
                'comments' => " "
                );
                $this->db->insert('gps_visitor_gp', $data);
      }
      public function getDateRange($to_date)
      {
          $date1 = date('Y-m-d');
          $date2 = $to_date;
          $diff = abs(strtotime($date2) - strtotime($date1));
          $years = floor($diff / (365*60*60*24));
          $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
          $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
          return $days;
      }

     public function applyLocalflexibleGatepass($id, $purpose, $dep_time, $arr_time, $send_approval,$autoapprove)
      {
		  
           if($autoapprove)
           {
               $s = "AutoApproved";
           }
            else
            {
                $s = "Pending";
            }
          $time = time();
          $data = array(
                'user_id' => $id,
                'gatepass_type' => 2,
                'from_date' => date('Y-m-d'),
                'from_time' => $dep_time,
                'to_date' => date('Y-m-d'),
                'to_time' => $arr_time,
				'purpose' => "NA",
				'destination' => "NEEMRANA",
				'visit_to' => "NEEMRANA",
				'approved_or_rejected_date' => "0000-00-00",
				'approved_or_rejected_time' => "00:00:00",
				'actual_out_date' => "0000-00-00",
				'actual_out_time' => "00-00-00",
				'actual_in_date' => "0000-00-00",
			    'actual_in_time' => "00-00-00",
				'comments' => "NA",
                'applied_date' => date('Y-m-d'),
                'applied_time' => date('H:i:s', $time),
                'purpose' => $purpose,
                'send_approval_to' => $send_approval,
                'status' => $s,
                'comments' => "NA"
                );
                $this->db->insert('gps_gatepassmaster', $data);
      }

      //get if student is in week limit or not
    public function getStudentWeekUse($id)
    {
        $range = $this->getWeekRange();
        $p = "select * from gps_configmaster where gps_configmaster.param_id = 1";
        $query = $this->db->query($p);
        $res = $query->result();
        foreach($res as $row)
            $limit = $row->value;
        $u = "select count(*) as total
        from gps_gatepassmaster
        where gatepass_type = '1' and user_id = '$id' and (applied_date between '$range[0]' and '$range[1]') and status = 'AutoApproved'";
        $q = $this->db->query($u);
        $r = $q->result();
        //var_dump($r);
        if($r[0]->total <= $limit)
        {
            return $r[0]->total;
        }
        else{
            return $limit;
        }
    }
    public function weekLimit()
    {
        $p = "select * from gps_configmaster where gps_configmaster.param_id = 1";
        $query = $this->db->query($p);
        $res = $query->result();
        foreach($res as $row)
            $limit = $row->value;
        return $limit;
    }

        public function getVisitorRequest($id)
        {
            $u= "select * from gps_visitor_gp
                where request_id = ?";
            $query = $this->db->query($u, $id);
            return $query->result()[0];
        }
     public function applyNonreturnatbleGatepass($id, $purpose, $dep_date, $dep_time, $send_approval)
      {
          $data = array(
                'user_id' => $id,
                'gatepass_type' => 4,
                'from_date' => $dep_date,
                'from_time' => $dep_time,
                'to_date' => '0000-00-00',
                'to_time' => '00:00:00',
                'applied_date' => date('Y-m-d'),
                'applied_time' => date('H:i:s'),
                'purpose' => $purpose,
                'send_approval_to' => $send_approval,
                'status' => "Pending",
                'comments' => " "
                );
                $this->db->insert('gps_gatepassmaster', $data);
      }
       public function getCheifWarden()
      {
        $u = "select * from gps_usersmaster
              where role_id = 3
              limit 1";
        $q = $this->db->query($u);
        return $q->result[0]->name;
      }
      public function getNotifications()
    {
        $u = "select notification from gps_notifications
        where gps_notifications.effective_till_date >= CURDATE() and gps_notifications.visbile_status = 1";
         $query = $this->db->query($u);
         return $query->result();
    }
    public function applyLocalfixedGatepass($id)
    {
        $time = time();
        $range = $this->getWeekRange();
        $t = 1;
        $p = "select * from gps_configmaster where gps_configmaster.param_id = 1";
        $query = $this->db->query($p);
        $res = $query->result();
        foreach($res as $row)
            $limit = $row->value;
        $u = "select count(*) as total
        from gps_gatepassmaster
        where gatepass_type = 1 and user_id = '$id' and (applied_date between $range[0] and $range[1])";
        $q = $this->db->query($u);
        $r = $q->result();
        if($r[0]->total <= $limit)
        {
            $data = array(
              'user_id' => $id,
              'gatepass_type' => 1,
              'from_date' => date('Y-m-d'),
              'from_time' => '17:30:00',
              'to_date' => date('Y-m-d'),
              'to_time' => '21:00:00',
              'applied_date' => date('Y-m-d'),
              'applied_time' => date('H:i:s', $time),
              'send_approval_to' => $this->getCheifWarden(),
              'status' => "AutoApproved",
			  'approved_or_rejected_date' => "0000-00-00",
			  'approved_or_rejected_time' => "00:00:00",
			  'actual_out_date' => "0000-00-00",
			  'actual_out_time' => "00-00-00",
			  'actual_in_date' => "0000-00-00",
			  'actual_in_time' => "00-00-00",
			  'purpose' => "Local Visit",
			  'destination' => "NEEMRANA",
			  'visit_to' => "NEEMRANA",
			  'comments' => "NA"
              );
              $this->db->insert('gps_gatepassmaster', $data);
        }
    }
    public function checkEmailExist($email)
    {
      # valid checking of email
      $u = " select user_id from  gps_usersmaster
          where email_id = ?
          ";
      $query = $this->db->query($u , $email);
      if ($query->result()){
        return True;
      }
      else {
        return false;
      }
    }
    public function applyOutstationGatepass($id, $purpose, $dest_add, $dest_contact, $dep_date, $dep_time, $arr_date, $arr_time, $visit_to, $send_approval,$autoapprove)
      {
        if($autoapprove)
          $s = "AutoApproved";
        else {
          $s = "Pending";
        }
          $time = time();
          $data = array(
                'user_id' => $id,
                'gatepass_type' => 3,
                'from_date' => $dep_date,
                'from_time' => $dep_time,
                'to_date' => $arr_date,
                'to_time' => $arr_time,
                'applied_date' => date('Y-m-d'),
                'applied_time' => date('H:i:s', $time),
                'purpose' => $purpose,
                'destination' => $dest_add,
                'destination_contact' => $dest_contact,
                'visit_to' => $visit_to,
                'send_approval_to' => $send_approval,
                'status' => $s,
                'comments' => " "
                );
                $this->db->insert('gps_gatepassmaster', $data);

      }
      public function getStudentRequest($id)
    	{
        $u = "select *
    					from gps_gatepassmaster
    					where request_id = ?";
        $query = $this->db->query($u ,$id);
        return $query->result()[0];
    	}
      public function getDatewiseHistory($s , $e , $userid)
      {
        $u = "select * from gps_gatepassmaster
              where user_id = '$userid' and applied_date between '$s' and '$e'";
        $q = $this->db->query($u );
        return $q->result();
      }
    public function checkBlackListStudents($id)
      {
          $current_date = date('Y-m-d');
          $time = time();
          $current_time = date('H:i:s');
          $u = "select * from gps_blacklist_students where user_id = '$id' and visibility = '1'";
          $query = $this->db->query($u);
          $arr = $query->result();

          foreach($arr as $row)
          {
              $date=$row->to_date;
              //var_dump($date." ".$row->to_time);
              if(strtotime($date." ".$row->to_time) > strtotime($current_date." ".$current_time))
              {
                  return $row;
              }
          }
          return False;
      }

     public function getSubGroupName($id)
    {
      $u = "select subgroup_name
      from gps_subgroup
      where subgroup_id = ?";
      $q = $this->db->query($u , $id);
      foreach ($q->result() as $key) {
          return $key->subgroup_name;
      }
    }
      public function checkAutoApprovedGroup($id,$from_date, $from_time, $to_date, $to_time)
      {
          $current_date = date('Y-m-d');
          $current_time = date('H:i:s');
          $g = $this->getGroupId($id);
          $s = $this->getSubGroupId($id);
          $u = "select * from gps_autoapprove where group_id = '$g->group_id' and subgroup_id = '$s->subgroup_id'";
         $query = $this->db->query($u);
         $arr = $query->result();
         var_dump($arr);
         foreach($arr as $row)
         {
             if(strtotime($from_date." ".$from_time) > strtotime($row->from_date." ".$row->from_time))
             {
                 if(strtotime($to_date." ".$to_time) < strtotime($row->to_date." ".$row->to_time))
                 {
                     return True;
                 }
             }
         }
         return False;
      }
      public function checkPreviousGatepass($user_id, $from_date, $from_time, $to_date, $to_time)
      {
        $u = "select * from gps_gatepassmaster where user_id = '$user_id' and status not in ('Cancelled' , 'Rejected') order by applied_date desc limit 5";
        $q = $this->db->query($u);
        $arr = $q->result();
        foreach($arr as $row)
        {
            if(strtotime($from_date." ".$from_time) >= strtotime($row->from_date." ".$row->from_time) && strtotime($from_date." ".$from_time) <= strtotime($row->to_date." ".$row->to_time))
            {
                return True;
            }
            else {
              if(strtotime($from_date." ".$from_time) <= strtotime($row->from_date." ".$row->from_time))
                if(strtotime($to_date." ".$to_time) >= strtotime($row->to_date." ".$row->to_time))
                  return True;
            }
            // if(strtotime($from_date." ".$from_time) < strtotime($row->from_date." ".$row->from_time))
            // {
            //   if(strtotime($to_date." ".$to_time) < strtotime($row->from_date." ".$row->from_time))
            //   {
            //     return False;
            //   }
            //   else
            //   {
            //     return True;
            //   }
            // }
            if(strtotime($to_date." ".$to_time) >= strtotime($row->from_date." ".$row->from_time) && strtotime($to_date." ".$to_time) <= strtotime($row->to_date." ".$row->to_time))
            {
                return True;
            }
        }
        return False;
    }
      public function getGroupId($user_id)
     {
         $u = "select * from gps_usersmaster where user_id = '$user_id'";
         $query = $this->db->query($u);
         return $query->result()[0];
     }
     public function getSubGroupId($user_id)
     {
         $u = "select * from gps_usersmaster where user_id = '$user_id'";
		 $query = $this->db->query($u);
         return $query->result()[0];
     }
      public function checkBlackListGroup($id)
      {
          $current_date = date('Y-m-d');
          $current_time = date('H:i:s');
          $g = $this->getGroupId($id);
          $s = $this->getSubGroupId($id);
          $u = "select * from gps_blacklistgroup where group_id = '$g->group_id' and subgroup_id = '$s->subgroup_id'";
          $query = $this->db->query($u);
          $arr = $query->result();
          foreach($arr as $row)
          {
              $date=$row->to_date;
              //var_dump($date." ".$row->to_time);
              if(strtotime($date." ".$row->to_time) > strtotime($current_date." ".$current_time))
              {
                  return $row;
              }
          }
          return False;
      }

      public function checkStudentOut($user_id)
      {
          $u = "select status from gps_usersmaster where user_id = '$user_id'";
          $q = $this->db->query($u, $id);
          $r = $q->result()[0];
          if($r->status == 'A')
          {
              return True;
          }
          else {
              return False;
          }
      }
    public function getWeekRange()
    {
  		$date = date("Y-m-d");
      $ts = strtotime($date);
      $start = (date('w', $ts) == 1) ? $ts : strtotime('last monday', $ts);
      return  array(date('Y-m-d', $start),
                   date('Y-m-d', strtotime('next sunday', $start)));
  	}
    public function getDashboardDetails($email)
    {
      $u = " select * from  gps_usersmaster
          where email_id = ?
          ";
      $query = $this->db->query($u , $email);
      //var_dump($query->result()[0]);
      $query->result()[0]->groupname = $this->getGroupName($query->result()[0]->group_id);
      $query->result()[0]->subgroupname = $this->getSubGroupName($query->result()[0]->subgroup_id);
      return $query->result()[0];
    }
    public function getGroupName($id)
    {
      $u = "select gps_groupname
      from gps_groups
      where gps_groupid = ?";
      $q = $this->db->query($u , $id);
      foreach ($q->result() as $key) {
          return $key->gps_groupname;
      }
    }
    public function getGatepassType($id)
    {
      $u = "select gatepass_name
      from gps_gatepass_type
      where gatepass_type = ?";
      $q = $this->db->query($u , $id);
      foreach ($q->result() as $key) {
          return $key->gatepass_name;
      }
    }
    public function getWardenList()
    {
      $u = "select name , user_id  from gps_usersmaster
            where role_id = 2 and status = 'P'
          ";
      $q = $this->db->query($u);
      return $q->result();
    }
    public function getHistory($id)
    {
      $u = "select *
           from gps_gatepassmaster
           where user_id = ?
           order by applied_date desc , applied_time desc
           ";
      $q = $this->db->query($u,$id);
      return  $q->result();
    }
    public function getVisitorHistory($id)
    {
        $u = "select *
           from gps_visitor_gp
           where user_id = ?
           order by applied_date desc , applied_time desc
           ";
      $q = $this->db->query($u,$id);
      return  $q->result();
    }

    public function updateGatepassStatus($request_id , $reason)
    {
        $data=array(
        'status'=>'Cancelled',
        'comments' => $reason);
        $this->db->where('request_id',$request_id);
        $this->db->update('gps_gatepassmaster',$data);

      }
    public function getStatus($user_id)
    {
      $u = "select status from gps_gatepassmaster where request_id = '$user_id'";
      $q = $this->db->query($u);
      return $q->result()[0];
    }
    public function getRecentHistory($id)
    {
      $u = "select status , comments , applied_date , applied_time , gatepass_type , from_date , from_time
           from gps_gatepassmaster
           where user_id = ?
           order by applied_date desc , applied_time desc
           limit 3";
      $q = $this->db->query($u,$id);
      return  $q->result();
    }
    public function wardenContact()
    {
      $u = "select name , contact_number from gps_usersmaster
            where role_id = 2";
      $r = $this->db->query($u);
      return $r->result();
    }
}


?>
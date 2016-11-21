<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class GatePassController extends CI_Controller {
	
	protected $title = "NU Gatepass | NIIT University";
	public function __construct(){
	 parent::__construct();
	 session_start();
	 
	 date_default_timezone_set("Asia/Kolkata");
	 
	//user event log entry 
	if(isset($_SESSION['user_id'])){
	 $userid = $_SESSION['user_id'];
	 $this->load->model("logModel");
	 $activitypage = $_SERVER['REQUEST_URI'];
	 $logid = uniqid();
	 $ip = $_SERVER["REMOTE_ADDR"];
	 $sessiontoken = $_SESSION['token_id'];
	 $this->logModel->eventLogManager($logid, $userid, $activitypage, $ip, $sessiontoken);
	 }
	}
	
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 	http://example.com/index.php/welcome
	 *	- or -
	 * 	http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{	
       //$loginflag = $this->loginfunction();
         $loginflag = 1;
//	 $_SESSION['user_id'] = "100475";
//	 $_SESSION['name'] = "Rahul";
//	 $_SESSION['role'] = "4";
//	 $_SESSION["email"] = "rahul.chhangani@niituniversity.in";
//	 $_SESSION['bool'] = "1";
//	 $_SESSION['token_id'] = md5(uniqid());
	$data["title"] = $this->title; //define page type
	
	if(isset($_SESSION['bool']))
	{	
	switch($_SESSION['role'])
	{
	case "1":
	$this->studentCall($data);	
	break;
	case "2":
	$this->wardenCall($data);
	break;
	case "3":
	$this->chiefwardenCall($data);
	break;
	case "4":
	$this->adminCall($data);
	break;
	case "5":
	$this->gaurdCall($data);
	break;
	default:
	$this->studentLogin();
	
	}	
	}	
	else
	{	
	$this->studentLogin();	
	}
	
	}
	
	//login page visiblity
	public function loginForm($authUrl)
	{
	 //login header
	 $data["title"] =  $this->title; //define page type
	 $data["authUrl"] = $authUrl;
	 $this->load->view('login/login-header',$data);
	$this->load->view('login/login');
	$this->load->view('login/login-footer'); 
	}
	 	
	 //login footer
	
	//-----------google login auth
	
	//-----------ldap login auth
	//login methods
	/* student login method with google login authentication */
	public function studentLogin()
 {
	 include_once APPPATH . "libraries/google-api-php-client-master/vendor/autoload.php";

	 $client_id = '839203179724-5r0d5t2jln5256llm36jiv4a0dmcb0em.apps.googleusercontent.com';
	 $client_secret = '9_CHVwN0-o5prVLywE9Pyg4O';
	 $redirect_uri = 'http://localhost/gatepass/index.php/GatePassController/studentLogin';
	 $simple_api_key = '';

	 $client = new Google_Client();
	 $client->setApplicationName("PHP Google OAuth 	Login Example");
	 $client->setClientId($client_id);
	 $client->setClientSecret($client_secret);
	 $client->setRedirectUri($redirect_uri);
	 $client->setDeveloperKey($simple_api_key);
	 $client->addScope("https://www.googleapis.com/auth/userinfo.email");

	 $objOAuthService = new Google_Service_Oauth2($client);
	 if (isset($_GET['code'])) {
	 $client->authenticate($_GET['code']);
	 $_SESSION['access_token'] = $client->getAccessToken();
	 header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	 }
	 if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	 	$client->setAccessToken($_SESSION['access_token']);
	 }
	 if ($client->getAccessToken()) {
	 $userData = $objOAuthService->userinfo->get();
	 $data['userData'] = $userData;
	 
	 
	 $this->load->model("studentModel");
	//  check for email
	
	 $c = $this->studentModel->checkEmailExist($userData->email);
	 
	 //email exists or not
	 if(!$c)
	 {
	$this->loginForm($client->createAuthUrl());
	 //echo "<script language=\"javascript\">alert('test');</script>";
	//redirect("GatePassController/logout");
	 }
	 else{
	 //get role id
	 $this->load->model("getterModel");
	 $row = $this->getterModel->getUserDetails($userData->email);
	 $_SESSION['user_id'] = $row->user_id;
	 $_SESSION['name'] = $row->name;
	 $_SESSION['role'] = $row->role_id;
	 $_SESSION['groupname'] = $this->getterModel->getGroupName($row->group_id);
	 $_SESSION['subgroupname'] = $this->getterModel->getSubgroupName($row->subgroup_id);
	 $_SESSION["photo"] = $row->photo;
	 	 $_SESSION['access_token'] = $client->getAccessToken();
	 $_SESSION["email"] = $userData->email;
	 $_SESSION['bool'] = "1";
	 $_SESSION['token_id'] = uniqid()."|".$row->user_id;
	 //var_dump($_SESSION);
	 $this->index();
	 }
	}
	else
	{
	 $authUrl = $client->createAuthUrl();
	 $this->loginForm($authUrl);
	}
	}
	
	public function adminLoginForm()
	{
	$username = $this->input->post("userName");
	$password = $this->input->post("Password1");	
	
	$boolean = $this->awUserLoginAdmin($username, $password);
	if($boolean){
	$this->index();	
	}
	
	}
	
	public function awUserLoginAdmin($username, $password)
    {
      /*ldap connection
          Active Directory server*/
        // Active Directory DN
	$ldap_host = "innu.niituniversity.in";
        $ldap_dn = "OU=NU-Users,DC=innu,DC=niituniversity,DC=in";
      // Active Directory user group
        $ldap_user_group = "NU Memder";
      // Active Directory manager group
              // $ldap_manager_group = "WebManagers";
              // Domain, for purposes of constructing $user
          $ldap_usr_dom = "@innu.niituniversity.in";
    	// connect to active directory
          $ldap = ldap_connect($ldap_host);
        	// verify user and password
            $user =$username;
            $passwrd =$password;
	if($bind = @ldap_bind($ldap, $user . $ldap_usr_dom, $passwrd)) {
              	// valid
              	// check presence in groups
	        $filter = "(sAMAccountName=" . $user . ")";
	        $attr = array("memberof");
	        // print_r($attr);
                    $result = ldap_search($ldap, $ldap_dn, $filter, $attr);
                  	// or exit("Unable to search LDAP server")
	$entries = ldap_get_entries($ldap, $result);
                    //print_r($entries);
                    $attr2 = array("displayname", "title", "manager", "mail", "homephone", "pager");
        	//$result2 = ldap_search($ldap, $ldap_dn, $filter);  --for all attribute
                    $result2 = ldap_search($ldap, $ldap_dn, $filter, $attr2);
                    $entries2 = ldap_get_entries($ldap, $result2);
                    //print_r($entries2);
                    $account_info=$entries2['0'];
                    $emp_ids=$account_info['title'];
                    $emp_id=$emp_ids['0'];
                    $emp_info=$account_info['displayname'];
                    $emp_name=$emp_info['0'];

                    $emp_ids3=$account_info['manager'];
                    $emp_id3=$emp_ids3['0'];
                    $parsr=ldap_explode_dn($emp_id3, 1);
	$manager=$parsr['0'];

                    $emp_ids4=$account_info['mail'];
                	$email=$emp_ids4['0'];

                    $emp_ids5=$account_info['pager'];
                    $cat=$emp_ids5['0'];

                    $emp_ids2=$account_info['homephone'];
                    $doj=$emp_ids2['0'];

                    if($entries['count']>'0'){
                    $_SESSION['login']=true;
	$empcode = explode(',',$emp_id);
	$_SESSION['user_id']=trim($empcode[0]);
                    $_SESSION['email']=$email;
                    //$_SESSION['user_name']=$emp_name;
                    $_SESSION['bool'] = $entries['count'];
	$this->load->model("getterForAdminModel");
	$row = $this->getterForAdminModel->getUserDetails($email);
	
	$_SESSION['user_id'] = $row->user_id;
	$_SESSION['name'] = $row->name;
	$_SESSION['role'] = $row->role_id;
	$_SESSION["email"] = $email;
	$_SESSION['bool'] = "1";
	$_SESSION['token_id'] = md5(uniqid())."|".$empcode[0];
	    return true;
                    }
                    else
                    {
                        return false;
                    }


            }
	}
//---------*Login controllers ends here --------
	/*
	 Warden controller Starts here

	 */
	 public function wApprovedRequest()
	 {
	$this->load->model("getterModel");
	//will return approved request in descending order of approved date
 	$list = $this->getterModel->getApprovedRequest($_SESSION["user_id"]);
 	//$data["n"] = $this->wardenModel->getpendingCount($_SESSION["user_id"]);
 	//$data["title"] = "Approved Request";
	$data["rows"] = $list;
	$data["aprrovedActive"] = true;
	$this->load->view('warden/headerView',$data );
 	$this->load->view('warden/approvedHistory',$data );
 	$this->load->view('warden/footerView');

	 }
	 public function wRejectedRequest()
	 {
	$this->load->model("getterModel");
	//will return approved request in descending order of approved date
	$list = $this->getterModel->getRejectedRequest($_SESSION["user_id"]);
	//$data["n"] = $this->wardenModel->getpendingCount($_SESSION["user_id"]);
	//$data["title"] = "Approved Request";
	$data["rows"] = $list;
	$data["rejectedActive"] = true;
	$this->load->view('warden/headerView',$data );
	$this->load->view('warden/rejectedHistory',$data );
        $this->load->view('warden/footerView');

	 }
	 
	 //function to check other's pending request in warden session
	 public function wOthersPendingRequest()
	 {
 	$this->load->model("getterModel");
 	$data["rows"] = $this->getterModel->getOthersPendingReq($_SESSION["user_id"]);
	$data["dashboardActive"] = true;
	$data["others"] = "yes";
 	$this->load->view("warden/headerView",$data);
 	$this->load->view("warden/dashboardView",$data);
            $this->load->view('warden/footerView');

	 }
	 public function wRemoveBlacklistStudent()
	 {
	  $this->load->model("gatepassModel");
	$this->gatepassModel->updateBlacklistStudent($this->input->post("studentcancelname"));
	echo json_encode($this->input->post("studentcancelname"));
	 }
	 public function wUserSearch()
	 {
	 	$data["searchActive"] = true;
	 	$this->load->view('warden/headerView',$data );
 	$this->load->view('warden/userSearchView' );
            $this->load->view('warden/footerView');
	 }
	 public function wblacklistStudent()
	 {
	 //include_once APPPATH."libraries/email/Sendmail.php";
	 	$studentid = $this->input->post("request_id");
	$fd = $this->changeDateformat($this->input->post("sdate"));
	$td = $this->changeDateformat($this->input->post("edate"));
	$userid = $_SESSION["user_id"];
	$this->load->model("gatepassModel");
	$this->gatepassModel->blacklistStudent($studentid,$fd,$this->input->post("fromtime"),$td
	,$this->input->post("totime"),$userid,$this->input->post("remark"));
	$this->gatepassModel->updateStatus($studentid , "PB");
	//var_dump($_SESSION);
	// $mailobj = new Send
	// );
	//
	  //     $message = '';
	  //     $this->load->library('email');
	// 	$this->email->initialize($config);
	  //     $this->email->set_newline("\r\n");
	  //     $this->email->from('prateek.pro95@gmail.com'); // change it to yours
	  //     $this->email->to('prateek.agarwal@st.niituniversity.in');// change it to yours
	  //     $this->email->subject('Hello World');
	  //     $this->email->message($message);
	  //     if($this->email->send())
	  //    {
	  //     echo 'Email sent.';
	  //    }
	  //    else
	  //   {
	  //    show_error($this->email->print_debugger());
	  //   }
	 }
	 public function gatepassStudentHistory($id)
	 {

	 }
	 public function studentReportExcel($id,$name)
	 {
 	$this->load->library('excel');
	$objPHPExcel = new PHPExcel();
	$this->load->model("getterModel");
	$rows = $this->getterModel->getDifferentCounts($id);
	$user = $this->getterModel->getDetailsForReport($id);
	$details = $user->name . " - ".strtoupper($id)." - ".$user->batch;
	$objPHPExcel->getProperties()->setCreator("pappan")
	 ->setLastModifiedBy("pappan")
	 ->setTitle("Office 2007   Document")
	 ->setSubject("Office 2007 ")
	 ->setDescription("PHP ")
	 ->setKeywords("office 2007 openxml php")
	 ->setCategory("Test result file");
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $details)
            ->setCellValue('A2', "Hostel")
            ->setCellValue('B2', $user->hostel)
            ->setCellValue('A4', 'Approved:')
	->setCellValue('A5', 'Auto-Approved:')
	->setCellValue('A6', 'Cancelled:')
	->setCellValue('A7', 'Pending:')
	->setCellValue('A8', 'Rejected:')
	->setCellValue('A9', 'Total Applied :->')
	;
	$val = 4;$sum = 0;
	foreach ($rows as $row) {
	if($row->status == "Pending" or $row->status == "Approved" or $row->status == "AutoApproved" or
	$row->status == "Cancelled" or $row->status == "Rejected"){
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('C'.$val, $row->total);
	$val += 1;


	
	$sum += $row->total;
	}
	}
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('C9', $sum);
	//$defaulter = $this->getterModel->countOfParticularSInDefaulter();
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A11', "Times in Defaulter list:")
            ->setCellValue('D11', "defaulter")//$defaulter
	->setCellValue('A12', "Times Blacklisted:")
	->setCellValue('A14', "From Date:")
	->setCellValue('C14', "To Date:")
	->setCellValue('E14', "Remark")
	->setCellValue('G14', "Warden Name");
	$objPHPExcel->getActiveSheet()->setTitle('createdUsingPHPExcel');
	$cols = ['A' => 'from_date', 'C'=>'to_date' , 'E' => 'remark' , 'G' => 'wardenname'];
	$rows = $this->getterModel->getBlacklistStudentRecord($id);
	foreach ($cols as $col => $value ) {
	$val = 15;
	foreach ($rows as $row) {
	$objPHPExcel->setActiveSheetIndex(0)
	            ->setCellValue($col.$val, $row->$value);
	$val += 1;
	}
	}
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('D12', sizeof($rows));
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename='.$id.'".xlsx');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	 }

	 /*
	 Warden Controller ENDS here
	 */
//---------------done
	 
	//student dashboard call
	//student dashboard call
	public function studentCall($d){

	$d["dashboardActive"] = true;
	$this->load->model("studentModel");
	$d["details"] = $this->studentModel->getDashboardDetails(strtolower($_SESSION["email"]));
	$t = $this->studentModel->checkBlackListStudents($_SESSION["user_id"]);
	$g = $this->studentModel->checkBlackListGroup($_SESSION["user_id"]);
	$checkedOut = $this->studentModel->checkStudentOut($_SESSION["user_id"]);
	$data["title"] =  $this->title;
	if($t)
	{
	$d["error"] = true;
	$message = "You are BLOCKED for ".$this->studentModel->getDateRange($t->to_date)." more Days";
	$d["message"] = $message;
	}
	if($g){
	$d["error"] = true;
	$message = "You are BLOCKED for ".$this->studentModel->getDateRange($g->to_date)." more Days";
	$d["message"] = $message;
	}
	if($checkedOut)
	$d["checkout"] = true;
	$x = $this->studentModel->getRecentHistory($d["details"]->user_id);
	foreach ($x as &$row) {
	$row->gatepassname = $this->studentModel->getGatepassType($row->gatepass_type);
	}
            $getnotif = $this->studentModel->getNotifications();
            //var_dump($getnotif);
	$d["rows"] = $x;
            $d["notif"] = $getnotif;
	$this->load->view("student/headerView",$d);
	$this->load->view("student/dashboardView",$d);
	$this->load->view("student/footerView");

	}

	public function infoBoxStudent()
    {
        $data["infoActive"] = true;
	$this->load->view("student/headerView",$data);
	$this->load->view("student/infoView",$data);
	$this->load->view("student/footerView");
    }

	//local flexible form call
	public function localflexibleformStudent()
	{
	$this->load->model("studentModel");
	$t = $this->studentModel->checkBlacklistStudents($_SESSION["user_id"]);
	$g = $this->studentModel->checkBlackListGroup($_SESSION["user_id"]);
        $checkOut = $this->studentModel->checkStudentOut($_SESSION["user_id"]);
	if($t || $g)
	{
	$data["error"] = true;  //sent to Localflexibleformview
	}
        if ($checkOut)
        {
            $data["checkout"] = true;  //sent to Localflexibleformview
        }
	$data["gatepassActive"] = true;
	$data["wardens"] =$this->studentModel->getWardenList();
	$this->load->view("student/headerView",$data);
	$this->load->view("student/localFlexibleFormView",$data);
	$this->load->view("student/footerView");
	}
//------done 	
	//form submission of flexible form
	public function checkLocalflexibleform()
	{
    $this->load->helper("url");
	$this->load->model("studentModel");
	$t = $this->studentModel->checkBlacklistStudents($_SESSION["user_id"]);
	$g = $this->studentModel->checkBlackListGroup($_SESSION["user_id"]);
	$checkedOut = $this->studentModel->checkStudentOut($_SESSION["user_id"]);
	$prev = $this->studentModel->checkPreviousGatepass($_SESSION["user_id"] ,date('Y-m-d') , $this->input->post("dtime"),
	date('Y-m-d')  , $this->input->post("atime"));
	if($t || $g || $prev || $checkOut)
	{
	if($t or $g ){
	$data["formsubmit"] = true;
	}
	if($prev){
	$data["alreadyapplied"] = true;                    // all data values are send to view page
	}
	if ($checkedOut) {
	$data["checkout"] = true;
	}
	$data["gatepassActive"] = true;
	$data["wardens"] =$this->studentModel->getWardenList();
	$this->load->view("student/headerView",$data);
	$this->load->view("student/localFlexibleFormView",$data);
	$this->load->view("student/footerView");
	}
	else
	{
            //check auto approve and change status
      $autoapprove = $this->studentModel->checkAutoApprovedGroup($_SESSION["user_id"],date('Y-m-d'),$this->input->post("dtime"),
	                date('Y-m-d'),$this->input->post("atime"));
	$this->studentModel->applyLocalflexibleGatepass($_SESSION["user_id"], $this->input->post("purpose") , $this->input->post("dtime") , $this->input->post("atime"), $this->input->post("sendapproval"),$autoapprove);
	redirect("GatePassController/historyStudent"); //redirect to history page
	}
	}


	public function nonreturnableformStudent()
    {
	$this->load->model("studentModel");
	$t = $this->studentModel->checkBlacklistStudents($_SESSION["user_id"]);
	$g = $this->studentModel->checkBlackListGroup($_SESSION["user_id"]);
            $checkOut = $this->studentModel->checkStudentOut($_SESSION["user_id"]);
	if($t || $g)
	{
	$data["error"] = true;
	}
      if ($checkOut)
      {
          $data["checkout"] = true;
      }
      $data["gatepassActive"] = true;
	$data["wardens"] =$this->studentModel->getWardenList();
	$this->load->view("student/headerView",$data);
	$this->load->view("student/NonReturnableFormView",$data);
	$this->load->view("student/footerView");
    }
    //student check non returnable from
     public function checknonreturnableform()
    {
	$this->load->model("studentModel");
	$t = $this->studentModel->checkBlacklistStudents($_SESSION["user_id"]);
	$g = $this->studentModel->checkBlackListGroup($_SESSION["user_id"]);
	$checkOut = $this->studentModel->checkStudentOut($_SESSION["user_id"]);
	if($t || $g || $checkOut)
	{
	if($t or $g ){
	$data["formsubmit"] = true;
	}
	if ($checkOut) {
	$data["checkout"] = true;
	}
	$data["gatepassActive"] = true;
	$data["wardens"] =$this->studentModel->getWardenList();
	$this->load->view("student/headerView",$data);
	$this->load->view("student/NonReturnableFormView",$data);
	$this->load->view("student/footerView");
	}
	else
	{
	$this->studentModel->applyNonreturnatbleGatepass($_SESSION["user_id"], $this->input->post("purpose") , $this->input->post("ddate") , $this->input->post("dtime"), $this->input->post("sendapproval"));
	redirect("GatePassController/historyStudent");
	}
	  }
	  
//-----done	  
	  public function profileStudent()
	{
	$this->load->model("studentModel");
	$data["profileActive"] = true;
	$data["photo"] = $_SESSION["photo"];
	$data["detail"] = $this->studentModel->getDashboardDetails(strtolower($_SESSION["email"]));
	$this->load->view("student/headerView",$data);
	$this->load->view("student/profileView",$data);
	$this->load->view("student/footerView");
	}
	public function outstationformStudent()
	{
	$this->load->model("studentModel");
	$data["gatepassActive"] = true;
	$t = $this->studentModel->checkBlackListStudents($_SESSION["user_id"]);
	$g = $this->studentModel->checkBlackListGroup($_SESSION["user_id"]);
        $checkOut = $this->studentModel->checkStudentOut($_SESSION["user_id"]);
	if($t || $g){
	$data["error"] = true;
	}
        if ($checkOut)
        {
	$data["checkout"] = true;
	}
	$data["wardens"] =$this->studentModel->getWardenList();
	$this->load->view("student/headerView",$data);
	$this->load->view("student/outstationform",$data);
	$this->load->view("student/footerView");
	}
    //student form check for out station gatepass
	public function checkOutstationform()
	{
	$this->load->model("studentModel");
	$cbs = $this->studentModel->checkBlacklistStudents($_SESSION["user_id"]);
	$cbg = $this->studentModel->checkBlackListGroup($_SESSION["user_id"]);
	$cp = $this->studentModel->checkPreviousGatepass($_SESSION["user_id"],$this->changeDateformat($this->input->post("fromdate")),
	$this->input->post("dtime"),$this->changeDateformat($this->input->post("adate")),	$this->input->post("atime"));
        $checkOut = $this->studentModel->checkStudentOut($_SESSION["user_id"]);
	if($cbs || $cbg || $cp || $checkOut)
        {
	 if($cp)
             {
	 	$data["alreadyapplied"] = true;
                $data["gatepassActive"] = true;
                $data["wardens"] =$this->studentModel->getWardenList();
                $this->load->view("student/headerView",$data);
                $this->load->view("student/outstationform",$data);
                $this->load->view("student/footerView");
             }
	 if($cbs || $cbg)
             {
                $data["formsubmit"] = true;
                $data["gatepassActive"] = true;
                $data["wardens"] =$this->studentModel->getWardenList();
                $this->load->view("student/headerView",$data);
                $this->load->view("student/outstationform",$data);
                $this->load->view("student/footerView");
             }
             if($checkOut)
             {
                 $data["checkout"] = true;
                 $data["gatepassActive"] = true;
                $data["wardens"] =$this->studentModel->getWardenList();
                $this->load->view("student/headerView",$data);
                $this->load->view("student/outstationform",$data);
                $this->load->view("student/footerView");
             }
	}
	else {
	$id = $_SESSION["user_id"];
	$autoapprove = $this->studentModel->checkAutoApprovedGroup($_SESSION["user_id"],$this->changeDateformat($this->input->post("fromdate")),
	$this->input->post("dtime"),$this->changeDateformat($this->input->post("adate")),
	$this->input->post("atime"));
	$this->studentModel->applyOutstationGatepass($id,$this->input->post("purpose"),$this->input->post("daddress"),
	$this->input->post("dcontact"),$this->changeDateformat($this->input->post("fromdate")),
	$this->input->post("dtime"),$this->changeDateformat($this->input->post("adate")),
	$this->input->post("atime"),$this->input->post("visitto"),$this->input->post("sendapprovalto"),$autoapprove);
	redirect("GatePassController/historyStudent");
	}
	}
//-----done	
	public function localfixedformStudent()
	{
	$this->load->model("studentModel");
	$t = $this->studentModel->checkBlacklistStudents($_SESSION["user_id"]);
	$g = $this->studentModel->checkBlackListGroup($_SESSION["user_id"]);
        $checkOut = $this->studentModel->checkStudentOut($_SESSION["user_id"]);
	$getcount = $this->studentModel->getStudentWeekUse($_SESSION["user_id"]);
        $weeklimit = $this->studentModel->weekLimit();
	$wlimit = $weeklimit - $getcount;
	$data["limit"] =$wlimit;
	if($t || $g)
	{
	$data["error"] = true;
	}
    if($checkOut)
    {
      $data["checkout"] = true;
	  }
	 if($wlimit == 0)
	 {
	  	$data["limitreach"] = true;
	 }
	$data["gatepassActive"] = true;
	$this->load->view("student/headerView",$data);
	$this->load->view("student/localFixedFormView",$data);
	$this->load->view("student/footerView");
	}
    //student form submission of local fixed form
	public function checkLocalfixedform()
	{
	$this->load->model("studentModel");
	$t = $this->studentModel->checkBlacklistStudents($_SESSION["user_id"]);
	$g = $this->studentModel->checkBlackListGroup($_SESSION["user_id"]);
	$checkOut = $this->studentModel->checkStudentOut($_SESSION["user_id"]);
        $weeklimit = $this->studentModel->weekLimit();
	//get data from configtable later
	$prev = $this->studentModel->checkPreviousGatepass($_SESSION["user_id"] ,date('Y-m-d') , "17:30:00", date('Y-m-d'), "21:00:00");
	//$prev = true;
	$getcount = $this->studentModel->getStudentWeekUse($_SESSION["user_id"]);
	$wlimit = $weeklimit - $getcount;
	$data["limit"] =$wlimit;
	if($t || $g  || $checkOut || $prev)
	{
	if($t || $g ){
	$data["formsubmit"] = true;
	}
	if($prev){
	$data["alreadyapplied"] = true;
	}
	if ($checkOut) {
	$data["checkout"] = true;
	}
	$data["gatepassActive"] = true;
	$this->load->view("student/headerView",$data);
	$this->load->view("student/localFixedFormView",$data);
	$this->load->view("student/footerView");
	}
	if ($wlimit == 0) {
	$data["limitreach"] = true;
	$data["gatepassActive"] = true;
	$this->load->view("student/headerView",$data);
	$this->load->view("student/localFixedFormView",$data);
	$this->load->view("student/footerView");
	}
	else{
	$this->studentModel->applyLocalfixedGatepass($_SESSION["user_id"]);
	redirect("GatePassController/historyStudent");
	}

	}

    public function visitorformStudent()
    {
        $data["visitorActive"] = true;
	$this->load->model("studentModel");
	$data["wardens"] =$this->studentModel->getWardenList();
	        //var_dump($data);
	$this->load->view("student/headerView",$data);
	$this->load->view("student/VisitorGatepassView");
	$this->load->view("student/footerView");
    }
    public function checkvisitorform()
    {
        $this->load->model("studentModel");
	$this->studentModel->applyVisitorGatepass($_SESSION["user_id"], $this->input->post("visitorname") , $this->input->post("relation") ,$this->input->post("adate"), $this->input->post("atime"),
	$this->input->post("ddate"), $this->input->post("dtime"), $this->input->post("purpose"),
        $this->input->post("sendApprovalTo"));
	$this->visitorhistoryStudent();
    }

//-----done	
	
	public function historyStudent()
	{
	$data["historyActive"] = true;
	$this->load->model("studentModel");
	$x = $this->studentModel->getHistory($_SESSION["user_id"]);
	foreach ($x as &$row) {
	$row->gatepassname = $this->studentModel->getGatepassType($row->gatepass_type);
	$row->sendApprovalToName = $this->studentModel->getWardenName($row->send_approval_to)->name;
	}
	$data["rows"] = $x;
	$this->load->view("student/headerView",$data);
	$this->load->view("student/historyDisplay",$data);
	$this->load->view("student/footerView");
	}
	public function sCancelRequest($id)
	{
	$this->load->model("studentModel");
	$id = (int)$id;
	$reason = $this->input->post("reason");
    $this->studentModel->updateGatepassStatus($id , $reason);
    redirect("GatePassController/historyStudent");
	}

	public function changeDateformat($date){
	return date("Y-m-d" , strtotime($date));
	}
	
	public function sDatewisehistory()
	{
	$this->load->model("studentModel");
	$sdate = $this->changeDateformat($this->input->post("sdate"));
	$edate = $this->changeDateformat($this->input->post("edate"));
	$x = $this->studentModel->getDatewiseHistory($sdate , $edate , $_SESSION["user_id"]);

	foreach ($x as &$row) {
	$row->gatepassname = $this->studentModel->getGatepassType($row->gatepass_type);
	$row->sendApprovalToName = $this->studentModel->getWardenName($row->send_approval_to)->name;
	}
	$data["rows"] = $x;
	$this->load->view("student/headerView",$data);
	$this->load->view("student/historyDisplay",$data);
	$this->load->view("student/footerView");
	}
	public function sRequestDetail($id)
	{
	$this->load->model("studentModel");
	$id = (int)$id;
      $x= $this->studentModel->getStudentRequest($id);
      $data["wname"]= $this->studentModel->getWardenName($x->send_approval_to)->name;
      $data["wname1"]= $this->studentModel->getWardenName($x->approved_or_rejected_by)->name;
	$data["details"]= $this->studentModel->getStudentRequest($id);
	$this->load->view("student/requestDetail",$data);
	}

	public function visitorhistoryStudent()
	{
	$data["visitorActive"] = true;
	$this->load->model("studentModel");
	$x = $this->studentModel->getVisitorHistory($_SESSION["user_id"]);
	foreach ($x as &$row) {
	$row->sendApprovalToName = $this->studentModel->getWardenName($row->send_approval_to)->name;
	}
	$data["rows"] = $x;
	$this->load->view("student/headerView",$data);
	$this->load->view("student/visitorHistoryDisplay",$data);
	$this->load->view("student/footerView");
	}
	
	public function sVisitorRequestDetail($id)
    {
        $this->load->model("studentModel");
	$id = (int)$id;
        $x= $this->studentModel->getVisitorRequest($id);
        $data["wname"]= $this->studentModel->getWardenName($x->send_approval_to)->name;
        $data["wname1"]= $this->studentModel->getWardenName($x->approved_or_rejected_by)->name;
	$data["details"]= $this->studentModel->getVisitorRequest($id);
	$this->load->view("student/visitorReqDetails",$data);
    }


	public function logout()
	{
	session_unset();
	session_destroy();
	redirect(base_url());
	}
	
	//--------------student end---------------
	public function wardenCall($data){
	$this->load->model("getterModel");
	$data["rows"] = $this->getterModel->getMyPendingReq($_SESSION["user_id"]);//
	$data["dashboardActive"] = true;
	$this->load->view("warden/headerView",$data);
	$this->load->view("warden/dashboardView",$data);
        $this->load->view('warden/footerView');

	}
	public function chiefwardenCall($data){

	}
	//-------------------------------------------
	//gaurd UI

	public function gaurdCall($data)
	{
	
	$this->load->view('gaurdui/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('gaurdui/navbar-admin');
	$this->load->view('gaurdui/gaurdhome');
	$this->load->view('gaurdui/footer-admin');	
	}
	
	public function gettingGaurdPunches()
	{
	$this->load->model("gaurdModel");
	$val = $this->gaurdModel->getPunchTransactions();
	if(isset($val))
	{
	echo $val;
	}
	
	}
	
	public function checkInOut()
	{
	
	}
	//--------------------gaurd end here--------------
	
	
	public function adminCall($data)
	{	
	$data['title'] = $this->title;
	
	//load count module
	$this->load->model("getterForAdminModel");
	$this->load->model("gatepassModel");
	$to_date = date("Y-m-d");
	$val = $this->getterForAdminModel->countOfBlacklistedStudent($to_date);
	$data['blacklistedcount'] = $val;
	
	$to_date = date("Y-m-d");
	$val = $this->getterForAdminModel->countOfPendingRequest();
	
	$data['pendingrequests'] = $val->total;
	$to_date = date("Y-m-d");
	
	$val = $this->getterForAdminModel->countOfStudentInCampus();
	$data['studentincampus'] = $val->total;
	
	$to_date = date("Y-m-d");
	$val = $this->getterForAdminModel->countOfStudentOutCampus();
	$data['studentoutcampus'] = $val->total;
	
	$val = $this->gatepassModel->getAllPendingRequests("Pending");
	
	foreach($val as &$row)
	{
	$row->name = $this->getterForAdminModel->getUserName($row->user_id); 
	$row->photo = $this->getterForAdminModel->getUserPhoto($row->user_id);
	$row->group_subgroup = $this->getterForAdminModel->getUserGroup($row->user_id)."(".$this->getterForAdminModel->getUserSubGroup($row->user_id).")";
	$row->gatepass_type = $this->getterForAdminModel->getGatepassTypeName($row->gatepass_type);
	$row->departure_date = $row->from_date."<br/>".$row->from_time;
	$row->arrival_date = $row->to_date."<br/>".$row->to_time;
	$row->visit_to = $row->visit_to;
	$row->purpose = $row->purpose;
	$row->destinationaddress = $row->destination;
	$row->destinationcontact = $row->destination_contact;	
	$row->user_id = $row->user_id;
	$row->request_id = $row->request_id;
	}
	$data['pendinglist'] = $val;
	
	//warden pending count	
	$wardenpendingcount = $this->getterForAdminModel->wardenpendingcount();
	$data['wardenpendingcount'] = $wardenpendingcount;
	
	//------------------
	$data["rolelist"] = $this->getterForAdminModel->getRoleNameList();
	$data["groupnames"] = $this->getterForAdminModel->getGroupNameList();
	$data["subgroupnames"] = $this->getterForAdminModel->getSubgroupNameList();
	$data["timer"] = $this->getterForAdminModel->gettimer();
	
	$this->load->view('admin/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('admin/navbar-admin');
	$this->load->view('admin/home');
	$this->load->view('admin/footer-admin');	
	}
	
	public function settings()
	{
	//get group and subgroup list
	$this->load->model("getterForAdminModel");
	$val = $this->getterForAdminModel->getGroupNameList();
	$data['grouplist'] = $val;
	
	$val2 = $this->getterForAdminModel->getSubgroupNameList();
	$data['subgrouplist'] = $val2;
	
	//get user role details
	$this->load->model("adminModel");
	$val3 = $this->adminModel->getUserRoles();
	$data['userroles'] = $val3;
	
	$val4 = $this->getterForAdminModel->getRoleNameList();
	$data['allroles'] = $val4;
        
        $val5 = $this->getterForAdminModel->getParamsList();
        $data['params'] = $val5;
	
	$data["title"] =  $this->title; //define page type
            $this->load->view('admin/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('admin/navbar-admin');
	$this->load->view('admin/settings',$data);
	$this->load->view('admin/footer-admin');
	}
	
	public function changeUserRole()
	{
        $role_id = $this->input->post("roleid");
	$user_id = $this->input->post("employeecode");
        //var_dump($role_id, $user_id);
	$this->load->model("adminModel");
        $this->adminModel->updateUserRole($user_id, $role_id);
        redirect("GatePassController/settings");
	}
	
    public function changeParameter()
    {
        $id = $this->input->post("param_id");
        $value = $this->input->post("value");
        $this->load->model("adminModel");
        $this->adminModel->updateParameter($id, $value);
        redirect("GatePassController/settings");
    }
    
	public function newgroupsubgroup($type)
	{
	$name = $this->input->post("g_sg_name");
	$this->load->model("settingModel");
	$val = $this->settingModel->createGroupOrSubgroup($type , $name);
	}
	
	public function createNotification()
	{
	$data["title"] =  $this->title;
	
	//load model for notification text
	$this->load->model("getterForAdminModel");
	$val = $this->getterForAdminModel->getNotificationText();
	$data['notificationtext'] = $val;
	
	$this->load->view('admin/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('admin/navbar-admin');
	$this->load->view('admin/createnotification');
	$this->load->view('admin/footer-admin');	
	}
	
	public function saveNotification()
	{
	$currentdate = date("Y-m-d");
	$currenttime = date("H:i:s");
	$notification_id = strtoupper(uniqid());
	$user_id = $_SESSION['user_id'];
	$notificationText = $this->input->post("notificationtext");
	$effectivedate = $this->input->post("effectivedate");
	$effectivedate = date("Y-m-d", strtotime($effectivedate));
	$this->load->model("settingModel");
	$val = $this->settingModel->setNotification($user_id,$notification_id,$notificationText,$effectivedate,$currentdate,$currenttime);
	$this->createNotification();
	}
	
	
	public function updateGatePassRequest($flag)
	{
	//session_start();
	switch($flag)
	{
	case '0':
	$request_id = $this->input->post("request_id");
	$remark = $this->input->post("remark");
	$status = 'Approved';
	$employeecode = $_SESSION['user_id'];
	$this->load->model("gatepassModel");
	$val = $this->gatepassModel->updateRejectedOrAccepted($employeecode , $status , $remark, $request_id);
	break;
	case '1':
	$request_id = $this->input->post("request_id");
	$remark = $this->input->post("remark");
	$status = 'Rejected';
	$employeecode = $_SESSION['user_id'];
	$this->load->model("gatepassModel");
	$val = $this->gatepassModel->updateRejectedOrAccepted($employeecode , $status , $remark, $request_id);
	break;
	default:
	echo "error"; //convert it to error alert message	
	}
	
	}
	
	public function blacklistOrAutoapprove()
	{
	$blocktype = $this->input->post("blocktype");
	$groupid = $this->input->post("groupid");
	$subgrouparr = $this->input->post("checkarr");
	$fromdate = $this->input->post("fromdate");
	$fromtime = $this->input->post("fromtime");
	$todate = $this->input->post("todate");
	$totime = $this->input->post("totime");
	$user_id = $_SESSION['user_id'];
	$this->load->model("adminModel");	
	
	if($blocktype==1)
	{
	foreach($subgrouparr as $subgroupid)
	{
	if($subgroupid != ""){
	$transactionid = uniqid();
	$val = $this->adminModel->autoApproveGroup($transactionid, $groupid, $subgroupid, $fromdate, $fromtime, $todate, $totime, $user_id);	
	}
	}
	}
	if($blocktype==2)
	{
	foreach($subgrouparr as $subgroupid)
	{
	if($subgroupid != ""){
	$transactionid = uniqid();
	$val = $this->adminModel->blacklistGroup($transactionid, $groupid, $subgroupid, $fromdate, $fromtime, $todate, $totime, $user_id);	
	}
	}
	}
	
	}
	
	
	//created by rahul
	public function loginfunction()
	{
	$this->load->view('login');	
	}
	
	//--------------admin functions-------------------
	public function userSearchController()
	{
	$searchkeyword = $this->input->post("searchkeyword");	
	
	if($searchkeyword)
	{
	$this->load->model("adminModel");
	$val = $this->adminModel->userSearch($searchkeyword);	
	$this->load->model("getterForAdminModel");
	
	foreach($val as &$row)
	{
	$row->groupname = $this->getterForAdminModel->getGroupName($row->group_id); //getting groupname
	$row->subgroupname = $this->getterForAdminModel->getSubgroupName($row->subgroup_id); //getting subgroupname
	}	
	}
	$data['title'] = $this->title;
	$data["userdetails"] = $val;
	//$this->searchuser($data);	
	$this->load->view('admin/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('admin/navbar-admin');
	$this->load->view('admin/searchuser');
	$this->load->view('admin/footer-admin');
	}
	
	public function wcheckUserSearchController()
	{
	$this->load->helper('url');
	$searchkeyword = $this->input->post("searchkeyword");
	if($searchkeyword){
	$this->load->model("adminModel");
	$val = $this->adminModel->userSearch($searchkeyword);
	$this->load->model("getterModel");
	//var_dump($val);
	foreach($val as &$row)
	$row->groupname = $this->getterModel->getGroupName($row->group_id);
	$_SESSION["userdetail"] = $val;
	$_SESSION["keyword"] = $searchkeyword;
	//$this->session->set_flashdata('userdetail',$val);
	//var_dump($_SESSION);
	 redirect("GatePassController/wcheckUserSearchController");
	}
	//var_dump($_SESSION);
	$data['userdetails'] = $_SESSION["userdetail"];
	$data["keyword"] = $_SESSION["keyword"];
	unset($_SESSION["userdetail"]);
	$data["searchActive"] = true;
	// if ($_SESSION["role"] == "4" )
	// 	$this->adminCall($data);
	// else
	if($_SESSION["role"] == "2") {
	$data["search"] = "yes";
	$this->load->view('warden/headerView',$data);
  	$this->load->view('warden/userSearchView');
	$this->load->view("warden/footerView");
	}
	}
	
	public function getGatePassHistory() //json popup
	{
	$userid = $this->input->post("user_id");
	$this->load->model("gatepassModel");
	$val = $this->gatepassModel->getGatePassMinimalHistoryofUser($userid);
	echo $val;
	}
	
	public function completegatepasslist() //json popup
	{
	$data["title"] = $this->title;
	$this->load->model("getterForAdminModel");
	$val = $this->getterForAdminModel->getGatePassList();
	json_encode($val);
	}
	
	public function gatePassPage()
	{
	$data["title"] = $this->title;
	$this->load->model("getterForAdminModel");
	$val = $this->getterForAdminModel->getGatePassList();
	$data['gatepasslist'] = $val;
	//-----------------------------
	
	$gatepasstype = $this->input->post("gatepasstype");
	$gatepassdate = $this->input->post("gatepassdate");
	
	if($gatepasstype && $gatepassdate){
	$this->load->model("gatepassModel");
	$val = $this->gatepassModel->getAllDifferentGatepassRequests($gatepasstype, $gatepassdate);	
	$data['gatepassdatabytype'] = $val;	
	}
	
	//-----------------------------
	$this->load->view('admin/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('admin/navbar-admin');
	$this->load->view('admin/gatepassview');
	$this->load->view('admin/footer-admin');	
	}
	
	public function viewgatepasscomplete($request_id)
	{
	$data["title"] = $this->title;
	$this->load->model("gatepassModel");
	$val = $this->gatepassModel->getGatePassCompleteView($request_id);
	$data["gatepasscomplete"] = $val;
	
	//$val = $this->getterForAdminModel->getGatePassList();
	$this->load->view('admin/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('admin/viewgatepasshistorycomplete');
	$this->load->view('admin/footer-admin');
	}
	
	public function eventlogs()
	{
	$data["title"] = $this->title;
	$this->load->model("logModel");
	$val = $this->logModel->getEventLog();
	$data["logs"] = $val;
	//$val = $this->getterForAdminModel->getGatePassList();
	$this->load->view('admin/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('admin/navbar-admin');
	$this->load->view('admin/eventlogs');
	$this->load->view('admin/footer-admin');
	}
	
	public function searchuser()
	{
	$data["title"] = $this->title;
	
	//$val = $this->getterForAdminModel->getGatePassList();
	$this->load->view('admin/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('admin/navbar-admin');
	$this->load->view('admin/searchuser');
	$this->load->view('admin/footer-admin');
	}

	public function adduser()
	{
	$data["title"] = $this->title;
       
        $this->load->model("getterForAdminModel");
	$val = $this->getterForAdminModel->getGroupNameList();
	$data['grouplist'] = $val;
	
	$val2 = $this->getterForAdminModel->getSubgroupNameList();
	$data['subgrouplist'] = $val2;
	
	$val4 = $this->getterForAdminModel->getRoleNameList();
	$data['rolelist'] = $val4;
	
	$userlist = $this->getterForAdminModel->getAvailableUserList();
	$data['userlist'] = $userlist;
	
        $this->load->view('admin/header-admin',$data);
	$this->load->view('includes/header-include');
	$this->load->view('admin/navbar-admin');
	$this->load->view('admin/adduser',$data);
	$this->load->view('admin/footer-admin');
	}
    
    public function checkadduser()
    {
        $data["title"] = $this->title;
        $this->load->model("adminModel");
        $this->adminModel->addUser($this->input->post("user_id"), $this->input->post("email"), $this->input->post("contact"), $this->input->post("group"), $this->input->post("role"), $this->input->post("subgroup"), $this->input->post("name"), $this->input->post("room"), $this->input->post("address"), $this->input->post("p_number"), $this->input->post("punch"), $this->input->post("hostel"));
        redirect("GatePassController/adduser");
    }
	
	//gaurd processes
	public function punchAPICALL()
	{
	$getpunchid = $this->input->post("punchid");
	$getmachineid = $this->input->post("machineid");
	$this->load->model("gaurdModel");
	//create punch log
	$createpunchlog = $this->gaurdModel->createPunchlog($getpunchid, $getmachineid);
	$getpunched_userid = $this->gaurdModel->getPunchedUserId($getpunchid);
	$val = $this->gaurdModel->moveUserGatepassRequesttoTemp($getpunched_userid); 
	//move as per applied date in ascending order closer applied date shown first but with neither other pending checkins nor expired gatepasses
	if(!empty(array_filter($val)))
	{
	foreach($val as &$row)
	{
	if($row->status=="CHECKEDOUT" && $row->actual_out_date != "0000-00-00" && $row->actual_out_time != "00:00:00")
	{
	$user_id = $row->user_id;
	$request_id = $row->request_id;
	$arrival_date = $row->to_date;
	$arrival_time = $row->to_time;
	$departure_time = $row->actual_out_time;
	$departure_date = $row->actual_out_date;
	$status = $row->status;
	$destination = $row->destination;
	$this->gaurdModel->successfulMoveToTempForCheckIn($user_id, $request_id, $departure_date, $departure_time, $arrival_date, $arrival_time, $status, $destination);
	}
	else if($row->actual_out_date == "0000-00-00" && $row->actual_out_time == "00:00:00")
	{
	//sample for visible for testing purpose
	echo "<br/>".$row->user_id;
	echo "<br/>".$row->request_id;
	echo "<br/>".$row->from_date;
	echo "<br/>".$row->from_time;
	echo "<br/>".$row->to_date;
	echo "<br/>".$row->to_time;
	echo "<br/>".$row->actual_out_time;
	echo "<br/>".$row->actual_out_date;
	echo "<br/>".$row->status;
	echo "<br/>".$row->destination;
	
	$user_id = $row->user_id;
	$request_id = $row->request_id;
	$arrival_date = $row->to_date;
	$arrival_time = $row->to_time;
	$departure_time = $row->from_time;
	$departure_date = $row->from_date;
	$status = $row->status;
	$destination = $row->destination;
	$this->gaurdModel->successfulMoveToTempForCheckOut($user_id, $request_id, $departure_date, $departure_time, $arrival_date, $arrival_time, $status, $destination);
	}
	}
	
	}
	else	
	{
	echo "Not found";
	}
	}
	public function gaurdCheckOut($request_id,$flag)
	{
	$this->load->model("gaurdModel");
	$checkedby = $_SESSION['user_id'];
	$checkoutorin = $this->gaurdModel->userCheckOutOrCheckIn($request_id,$flag,$checkedby);
	redirect("GatePassController/gaurdCall/1");
	}	
	
	
}

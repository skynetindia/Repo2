<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	 	public $controllerName = 'member';
		public $treeTable = 'members';
		public $fundtable="pending_deposits";
		public $commissiontable="commissions";
		public $paymenttable = 'member_fee_payments';
		public $withdrawtable = 'withdraws';
		public $withdrawhistorytable = 'withdrawhistorys';
		public $revenuetable = 'revenuepositions';
		public $matrixtable = 'matrixpositions';
		public $balancetable = 'balransferhistories';
		public $membershiptable = 'memberships';
		public $membermembershiptable = 'membermemberships';
		public $testimonialtable = 'testimonials';
		public $functionname;
		public $mainName;
		public $subName;
		public $childName;
		public $perPage = '3';
		public $duplicateLimit = '5';
		public $importLimit = '5';
		public $textcredit = '0'; //This get from the session after user module done 
		public $userID = 0;
		public $sessionData; //This get from the session after user module done
		public $arrPermissions = array(); 
		public $arrDefaultCurrency = array(); 
		public $loginsession;
		public $arrProfileFormSettings = array();
		public $arrBizDirectorySettings = array();
				
		public function __construct()
		{
		   parent::__construct();
			//Check Login
			$arrSettings = $this->settings->getMainSettings();
			if($arrSettings['maintenancemode'] == '1'){
				 $viewdata['maintenancemodetext'] = $arrSettings['maintenancemodetext'];
			 	echo $arrSettings['maintenancemodetext'];
				// $this->load->view('maintenance_mode_view',$viewdata);
			 	exit;
			}
			$sessionData = $this->session->userdata('memberUser');
			$this->loginsession=$this->session->userdata('loginadd');
			$this->userID=$sessionData['userId'];
			$currenMethod = $this->router->fetch_method();
			$notCheckSessionMethods = array('testimonials','viewallmember','drupalleftmenu');
			if(!in_array($currenMethod,$notCheckSessionMethods)){
			if(!$sessionData){
				$msg = $this->lang->line('please_login_to_access');
				 $this->utility->set_flashdata('danger',$msg,300);
				 redirect(SITEURL.'login');	
			}}
			$this->arrDefaultCurrency = $this->settings->getDefaultCurrency();			
			$dateSettings['orderby'] = 'profileorder ASC';
			$dateSettings['where'] = array('profilestatus'=>'1');			
			$this->arrProfileFormSettings = $this->settings->getSignupFormSettings($dateSettings);
			$this->arrBizDirectorySettings = $this->settings->getBizdirectorySettings();
			//print_r($this->arrBizDirectorySettings);
			//pagination library
			 $this->load->library('Ajax_pagination');
	   
    	}
		public function index()
		{
			$viewdata['pagename']='Member Overview Page';
			$viewdata['title']='Member Overview Page';
			$viewdata['keywords']='Member Overview Page Keywords';
			$viewdata['description']='Member Overview Page description';
			$data['table'] = $this->treeTable;	
			$data['where']['member_id'] = $this->userID;			 	
			$memberdata=$this->my_model->selectRecords($data);
			$memberdata=$memberdata[0];
			if(($memberdata->referrer!=0)&&($memberdata->referrer!=NULL))
			{
				$sponsordata['where']['member_id']=$memberdata->referrer;
				$sponsormember=$this->utility->getMembers($sponsordata);
				$sponsorname=$sponsormember[0]['user_name'];
			}
			else
			{
				$sponsorname='&nbsp;';
			}
			$matrixdata['table']=$this->matrixtable;
			$matrixdata['where']['member_id']=$this->userID;
			$matrixdata['where']['status']=1;
			$matrixdata['fields']="count(id) as position";
			$matrixrecord=$this->my_model->selectRecords($matrixdata);
			$revenuedata['table']=$this->revenuetable;
			$revenuedata['where']['member_id']=$this->userID;
			$revenuedata['where']['status']=1;
			$revenuedata['fields']="sum(position) as position";
			$revenuerecord=$this->my_model->selectRecords($revenuedata);
			
			$revenuemaindata['table']='revenueplans';
			$revenuemaindata['where']=array('groupid'=>1,'id!='=>1);
			$revenuemainrecord=$this->my_model->selectRecords($revenuemaindata);
			$rps=array();
			foreach($revenuemainrecord as $rpskey=>$rpsval)
			{
				$rpsdata['table']=$this->revenuetable;
				$rpsdata['where']['member_id']=$this->userID;
				$rpsdata['where']['status']=1;
				$rpsdata['where']['planid']=$rpsval->id;
				$rpsdata['fields']="sum(position) as position";
				$rpsrecord=$this->my_model->selectRecords($rpsdata);
				if($rpsrecord[0]->position!=0 && $rpsrecord[0]->position!=null)
				$rps[]=array('name'=>$rpsval->plan_name,'rpsvalue'=>$rpsrecord[0]->position);
			}
			//PTC Records
			
			//Start Login ad code
				if($this->loginsession!=1)
				{
					$dataloginad['table']='loginadadds';
					$dataloginad['where']['ptype']=0;
					$dataloginad['where']['expire_date >']=date('Y-m-d h:i:s',time());//2016-08-21 00:00:00
					$dataloginad['orderby']='rand()';
					$dataloginad['limit']=0;
					$dataloginad['offset']=1;
					$loginad=$this->my_model->selectRecords($dataloginad);
					//Update display counter
					if(count($loginad)>0)
					{
					$loginadup['display_counter']=$loginad[0]->display_counter+1;
					$condloginad['where']['id']=$loginad[0]->id;
					$this->my_model->updateRecords($loginadup,'loginadadds',$condloginad);
					$condloginad['table']='loginadadds';
					$loginad=$this->my_model->selectRecords($condloginad);
					$viewdata['loginad']=$loginad[0];
					$viewdata['loginaddcounter']=1;
					$this->session->set_userdata(array('loginadd'=> 1));
					}
				}
			//End
		
			$this->load->view('view_dashboard',$viewdata);
			
		}
		
	
	
	
	
		//Add fund Section
		public function addfund()
		{
			$viewdata['title']='Add Fund - marketerSmile';
			$viewdata['keywords']='Add Fund Keywords';
			$viewdata['description']='Add Fund description';
			$msg='';$counter=0;
			$arrFundSettings = $this->settings->getAddFundSettings();
			if($this->input->post())
			{
				$post = $this->input->post();
				//Add Funds -> Process Started (Amount : €52.3892 EUR, Payment Processor : Bank Wire)
				//Log Entry
				$this->subName="Add Fund->";
				$this->childName="Process Started (Amount :". $this->arrDefaultCurrency['prefix']. $post['fianlamount_val']. $this->arrDefaultCurrency['suffix']."., Payment Processor :". $this->wallet->processorname($post['payment_processor']).")";
				$filename=($this->subName.''.$this->childName);
				
				
				if($_FILES['file1']['name']!=''||$_FILES['file2']['name']!='')
				{
					$photo=$this->utility->uploadFile($_FILES,'bankwire');
					
					foreach($photo as $key => $val)
					{
						if(is_array($val))
						{
							$msg.=$val['error']."<br>";
							
							$counter++;
						}
						else
						{
							$value="file".($key+1);
							$newval[$value]=$val;
						}
					}
				}
				
				//$gateway=explode('_',$post['payment_processor']);
				$newval['processorid']=$post['payment_processor'];
				
				$newval['processor']= $this->wallet->processorname($post['payment_processor']);
				$newval['member_id']=$this->userID;
				$newval['payment_date']=DATE_TIME;
				$newval['amount']=round($post['amount']/$this->arrDefaultCurrency['rate'],2);
				$newval['paidamount']=round($post['fianlamount_val']/$this->arrDefaultCurrency['rate'],2);
				$newval['fees']=abs(($post['amount']-$post['fianlamount_val'])/$this->arrDefaultCurrency['rate']);
				$newval['ip_address']=$this->input->ip_address();
				$newval['comment']=$post['comment'];
				
				
				$addfundminlimit = $this->settings->covertCurrencyRate($arrFundSettings['addfundminlimit'],$this->arrDefaultCurrency['rate']);
				$addfundmaxlimit = $this->settings->covertCurrencyRate($arrFundSettings['addfundmaxlimit'],$this->arrDefaultCurrency['rate']);
				if($counter!=0)
				{
					$msg=$msg;
					$jsonResult['flag'] = 'fail';
				}
				elseif($post['captchaCode'] != $this->session->userdata['captchaCode']){
					$msg = "Invalid Capcha";
					$jsonResult['flag'] = 'fail';
				}
				elseif($arrFundSettings['enable_addfund']== '1' && $arrFundSettings['addfundminlimit'] != '0' && $addfundminlimit > $post['amount'] ){
					//$msg = "Please enter greater than Minimum Fund Amount";
					$msg = 'Your add fund amount cannot be less than following amount : '.$addfundminlimit;
					$jsonResult['flag'] = 'fail';
				}
				elseif($arrFundSettings['enable_addfund']== '1' && $arrFundSettings['addfundmaxlimit'] != '0' && $addfundmaxlimit < $post['amount']){
					$msg = 'Your add fund amount cannot be greater than following amount : '.$addfundmaxlimit;
					$jsonResult['flag'] = 'fail';					
				}
				elseif( $position=$this->save($newval,$this->fundtable,'/withdrawal')){	
				if($newval['processorid']!='1')
				{
					$processor=$newval['processorid'];
					$jsonResult['processor']=SITEURL."payment/$processor/$position";
				}			
					$msg = "Fund  have been saved successfully";
					$this->utility->set_flashdata('success',$msg,300);
					$jsonResult['flag'] = 'success';
					/*Commission Part*/
					$this->logs->insertMemberLogs($this->userID,$filename);
				}
				else {
				$msg = "Banner Ads have not saved successfully.Please try again.";
				$jsonResult['flag'] = 'fail';
			}
			
			$jsonResult['msg'] = $msg;	
			exit(json_encode($jsonResult));		
			}
			else
			{
			
				$wallet=$this->wallet->wallet_info(implode(',',$processor_id),'cash,repurchase,earning,commission',$this->userID);
			
				$paymentdata['table']=$this->paymenttable;
				$paymentdata['where']['member_id']=$this->userID;
				$matrixrecord=$this->my_model->selectRecords($paymentdata);
				//echo $this->db->last_query();exit();
				$viewdata['payment']=$matrixrecord;
				$viewdata['wallets']=$wallet;
				$viewdata['wallet_type']='cash,repurchase,earning,commission';
				
				/* Check the captcha used in that or not */
				$viewdata['captchaSettings'] = $this->settings->getCaptchaSettings('MemberAddFund');
				
				
				if($viewdata['captchaSettings']){
					$viewdata['captchImg']=$this->utility->generateCaptch(4,$viewdata['captchaSettings']);
					
				}
				if (!$this->input->is_ajax_request()) {
					$this->load->view('view_add_fund',$viewdata);
				}
				else {
					$this->load->view('view_add_fund_ajax',$viewdata);
				}
			}
			
		}
		//Commission Section
		public function commission()
		{
			 $page = $this->input->post('page');
				if($page==null){
					$offset = 0;
				}else{
					$offset = $page;
				}
				
			$viewdata['title']="Commision Earning Page - marketerSmile";
			$matrixdata['table']=$this->commissiontable;
			unset($_POST['page']);
			if($this->input->post()) 
			{
				$post=$this->input->post();
				$searchData['search']['commission'] = $post;
				$this->session->set_userdata($searchData);
			}
			elseif($this->session->userdata('search'))
			{
				$searchDate1 = $this->session->userdata('search');			
				$this->session->unset_userdata('search');
				if(isset($searchDate1['commission']))
				{				
					$searchData['search']['commission'] = $searchDate1['commission'];
					$this->session->set_userdata($searchData);
					
					$post=$searchDate1['commission'];	
				}
				
			}
			
			if(isset($post) && $post)
			{
				//$post=$this->input->post();
				if(isset($post['searchby'])){
					if($post['searchby']!='all')
					{
						if($post['searchby']=='from_id')
						{
							$matrixdata['where']['from_id']=$post['searchfor'];
						}elseif($post['searchby']=='from_member')
						{
							$matrixdata['where']['from_id']=$this->utility->getMembersField($post['searchfor'],'member_id');
						}
						elseif($post['searchby']=='from_id')
						{
							$matrixdata['where']['from_id']=$this->utility->getMembersField($post['searchfor'],'member_id');
						}
						else
						{
							$matrixdata['where']['description']=$post['searchby'];
						}
					}
				}
				if(isset($post['fromdate'])&&($post['fromdate']!=''))
				{
					$matrixdata['where']['tran_dt >=']=date('Y-m-d H:i:s',strtotime($post['fromdate']));
				}
				if(isset($post['todate'])&&($post['todate']!=''))
				{
					$matrixdata['where']['tran_dt <=']=date('Y-m-d H:i:s',strtotime($post['todate']) + 86399);
				}
			}
				$matrixdata['where']['to_id']=$this->userID;
				$matrixrecord=$this->my_model->totalRecords($matrixdata);
				//Initialize  pagination
				  
				  	//End
					
						$matrixdata['orderby']='tran_id desc';
				$matrixrecord=$this->my_model->selectRecords($matrixdata);
				//echo $this->db->last_query();
			$viewdata['commission']=$matrixrecord;
			if (!$this->input->is_ajax_request()) {
			$this->load->view('view_commission',$viewdata);
			}
			else {
			if((isset($post) && $post) ||($page!=null))
			{
				$msg = "Search Sucessfully";
				$jsonResult['flag'] = 'success';
				$jsonResult['msg'] = $msg;
				$jsonResult['view']=$this->load->view('ajaxtemplates/view_commission_search',$viewdata,true);	
				exit(json_encode($jsonResult));
			}
			else
			{
					$this->load->view('ajaxtemplates/view_commission_ajax',$viewdata);
			}
			
			}
		}
		//Commission Section
		public function paymenthistory()
		{
			 $page = $this->input->post('page');
				if($page==null){
					$offset = 0;
				}else{
					$offset = $page;
				}
			unset($_POST['page']);
			$viewdata['title']="Payment History - marketerSmile";
			$paymentdata['table']=$this->paymenttable;
			//print_r($this->session->userdata('search'));
			if($this->input->post()) {
			$post=$this->input->post();
			$searchData['search']['paymenthistory'] = $post;
			$this->session->set_userdata($searchData);
			//print_r($this->session->userdata('search'));
			}
			elseif($this->session->userdata('search')){
				$searchDate1 = $this->session->userdata('search');			
				$this->session->unset_userdata('search');
				if(isset($searchDate1['paymenthistory'])){				
					$searchData['search']['paymenthistory'] = $searchDate1['paymenthistory'];
					$this->session->set_userdata($searchData);
					//print_r(array_intersect_key($searchDate, $post));
					$post=$searchDate1['paymenthistory'];	
				}
				
			}
			
			if(isset($post) && $post)
			{
				//$post=$this->input->post();
				if(isset($post['searchfor']) && $post['searchby']!='all')
				{
					if($post['searchby']=='amount' ||$post['searchby']=='fees' || $post['searchby']=='discount')
					{
						$currency=$this->settings->getDefaultCurrency();
						$post['searchfor']=round($post['searchfor']/$currency['rate'],4);
					}
					$paymentdata['like']=array($post['searchby']=>$post['searchfor']);
				}
				
				if(isset($post['fromdate'])&&($post['fromdate']!=''))
				{
					$paymentdata['where']['pay_dt >=']=date('Y-m-d H:i:s',strtotime($post['fromdate']));
				}
				if(isset($post['todate'])&&($post['todate']!=''))
				{
					$paymentdata['where']['pay_dt <=']=date('Y-m-d H:i:s',strtotime($post['todate']) + 86399);
				}
			}
			$paymentdata['where']['member_id']=$this->userID;
			$matrixrecord=$this->my_model->totalRecords($paymentdata);
				//Initialize  pagination
				  	$config['target']      = '#postList';
					$config['base_url']    = SITEURL.'member/paymenthistory';
					$config['total_rows']  = $matrixrecord;
					$config['per_page']    = 10;
        
      				$this->ajax_pagination->initialize($config);
					$paymentdata['offset']=10;
					$paymentdata['limit']=($offset==0)?$offset:($offset);
				  	//End
					
				$paymentdata['orderby']='id desc';
			
				//echo $this->db->last_query();
			$matrixrecord=$this->my_model->selectRecords($paymentdata);
			//echo $this->db->last_query();//exit();
			$viewdata['payment']=$matrixrecord;
			if (!$this->input->is_ajax_request()) {
			$this->load->view('view_payment_history',$viewdata);
			}
			else {
				if((isset($post) && $post) ||($page!=null))
			{
				$msg = "Search Sucessfully";
				$jsonResult['flag'] = 'success';
				$jsonResult['msg'] = $msg;
				$jsonResult['view']=$this->load->view('ajaxtemplates/view_payment_search',$viewdata,true);	
				exit(json_encode($jsonResult));
			}
			else
			{
					$this->load->view('ajaxtemplates/view_payment_history_ajax',$viewdata);
			}
			$this->load->view('ajaxtemplates/view_payment_history_ajax',$viewdata);
			}
		}
		//Withdrawal Request Section
		public function withdrawal()
		{
			$viewdata['title']="Withdrawal - marketerSmile";
			if($this->input->post())
			{
				$this->functionname = __FUNCTION__;
				$filename=("Withdrawal -> New Request Sent");
				$post = $this->input->post();
				$wallet_type=$post['withdrawbalance'];
				$gateway=$post['payment_processor'];
				$post['processorid']=$post['payment_processor'];
				//processor Name
				$processordata['where_in']['id']=$gateway;
				$processordata['table']='processors';
				$processor_detail=$this->my_model->selectRecords($processordata);
				$post['payment_processor']=$processor_detail[0]->proc_name;
				//End of processor Name
				//Wallet Balance
				$currentbal=$this->wallet->current_balance($gateway,$wallet_type,$this->userID);
				$viewdata['title']="Member Panel";
				$data['table'] = $this->treeTable;	
				$data['where']['member_id'] = $this->userID;			 	
				$memberdata=$this->my_model->selectRecords($data);
				$processorid="processor_".$gateway;
				$post['pro_acc_id']=$memberdata[0]->$processorid;
				$post['req_dt']=DATE_TIME;
				$post['status']='pending';
				$post['amount']=round($post['amount']/$this->arrDefaultCurrency['rate'],4);
				$post['member_id']=$this->userID;
				$post['fee']=round($post['finalamountw']/$this->arrDefaultCurrency['rate'],4)-$post['amount'];
				$finalamount=round($post['finalamountw']/$this->arrDefaultCurrency['rate'],4);
				$captchacode=$post['captchaCode'];
				unset($post['captchaCode']);unset($post['finalamountw']);
				$withSettingCheck = $this->settings->checkwithdrawalLimit($post['amount']);
				
				if($withSettingCheck){
					$msg = "You today withdrawal limit is over";
					$msg = $withSettingCheck; 
					$jsonResult['flag'] = 'fail';					
				}
				elseif($captchacode != $this->session->userdata['captchaCode']){
					$msg = "Invalid Capcha";
					$jsonResult['flag'] = 'fail';
				}
				elseif(($memberdata[0]->$processorid=='')&&($memberdata[0]->$processorid==NULL))
				{
					$msg = "Enter Payment Processor ID ";
					$jsonResult['flag'] = 'fail';
				}
				elseif($finalamount>$currentbal)
				{
					$msg = "Insufficent Balance";
					$jsonResult['flag'] = 'fail';
				}
				elseif( $position=$this->save($post,$this->withdrawtable,'/withdrawal')){
					
					$this->wallet->update_balance($gateway,$wallet_type,$this->userID,$finalamount,'purchase',$currentbal,"withdrawal",$post['fee']);
					
					$this->logs->insertMemberLogs($this->userID,$filename);
					$msg = "Withdarwal request successfully";
					$this->utility->set_flashdata('success',$msg,300);
					$jsonResult['flag'] = 'success';
				/*Commission Part*/
				
				}
				else {
				$msg = "Withdarwal request not successfully.Please try again.";
				$jsonResult['flag'] = 'fail';
			}
			
			$jsonResult['msg'] = $msg;	
			exit(json_encode($jsonResult));		
			}
			else
			{
				$dataprocessor['table']='processors';
				$dataprocessor['where']['receivefund']='1';
				$dataprocessor['where']['proc_status']='1';
				$processordata=$this->my_model->selectRecords($dataprocessor);
				foreach($processordata as $pkey=>$pval)
				{
					$processor_id[]=$pval->id;
				}
				
				$wallet=$this->wallet->wallet_info(implode(',',$processor_id),'cash,repurchase,earning,commission',$this->userID);
				$paymentdata['table']=$this->paymenttable;
				$paymentdata['where']['member_id']=$this->userID;
				$matrixrecord=$this->my_model->selectRecords($paymentdata);
				//echo $this->db->last_query();exit();
				$viewdata['payment']=$matrixrecord;
				$viewdata['wallets']=$wallet;
				$viewdata['wallet_type']='cash,repurchase,earning,commission';
				
				/* Check the captcha used in that or not */
				$viewdata['captchaSettings'] = $this->settings->getCaptchaSettings('MemberPurchasePosition');
				if($viewdata['captchaSettings']){
					$viewdata['captchImg']=$this->utility->generateCaptch(4,$viewdata['captchaSettings']);
				}
				
				if (!$this->input->is_ajax_request()) {
					$this->load->view('view_withdraw_request',$viewdata);
				}
				else {
					$this->load->view('view_withdraw_request_ajax',$viewdata);
				}
			}
		}
		public function withdrawal_history()
		{
			
			$viewdata['title']="Withdrawal History Page - marketerSmile";
			$paymentdata['table']=$this->withdrawtable;//withdrawhistorytable
			if($this->input->post())
			{
				$post=$this->input->post();
				if($post['searchfor'] && $post['searchby']!='all')
				{
					
					$paymentdata['where']=array($post['searchby']=>$post['searchfor']);
				}
				
				if($post['fromdate'])
				{
					$paymentdata['where']['req_dt >=']=date('Y-m-d H:i:s',strtotime($post['fromdate']));
				}
				if($post['todate'])
				{
					$paymentdata['where']['req_dt <=']=date('Y-m-d H:i:s',strtotime($post['todate']) +86399);
				}
			}
			$paymentdata['where']['member_id']=$this->userID;
			$paymentdata['orderby']="with_id desc";
			$matrixrecord=$this->my_model->selectRecords($paymentdata);
			//echo $this->db->last_query();exit();
			$viewdata['history']=$matrixrecord;
			if (!$this->input->is_ajax_request()) {
			$this->load->view('view_withdrawal_history',$viewdata);
			}
			else {
				if($this->input->post())
			{
				$msg = "Search Sucessfully";
				$jsonResult['flag'] = 'success';
				$jsonResult['msg'] = $msg;
				$jsonResult['view']=$this->load->view('ajaxtemplates/view_withdrawal_search',$viewdata,true);	
				exit(json_encode($jsonResult));
			}
			else
			{
					$this->load->view('ajaxtemplates/view_withdrawal_history_ajax',$viewdata);
			}
			$this->load->view('ajaxtemplates/view_withdrawal_history_ajax',$viewdata);
			}
		}
		public function withdrawdele($id)
		{
			$condition['where']['with_id']=$id;
			$filename=("Withdrawal -> Request deleted(ID:$id)");
			$condition['table']=$this->withdrawtable;
			$withdrawvalue=$this->my_model->selectRecords($condition);
			$gateway=$withdrawvalue[0]->processorid;
			$wallet_type="cash";
			$memberid=$withdrawvalue[0]->member_id;
			$currentbal=$this->wallet->current_balance($gateway,$wallet_type,$memberid);
			$this->wallet->update_balance($gateway,$wallet_type,$memberid,($withdrawvalue[0]->amount+$withdrawvalue[0]->fee),'fund',$currentbal,"Balance Transfer");
			$this->my_model->deleteRecords($this->withdrawtable,$condition);
			$viewdata['title']="Withdrawal History Page - marketerSmile";
			$paymentdata['table']=$this->withdrawtable;//withdrawhistorytable
			$this->logs->insertMemberLogs($this->userID,$filename);
			if($this->input->post())
			{
				$post=$this->input->post();
				if($post['searchfor'] && $post['searchby']!='all')
				{
					
					$paymentdata['where']=array($post['searchby']=>$post['searchfor']);
				}
				
				if($post['fromdate'])
				{
					$paymentdata['where']['req_dt >=']=date('Y-m-d',strtotime($post['fromdate']));
				}
				if($post['todate'])
				{
					$paymentdata['where']['req_dt <=']=date('Y-m-d',strtotime($post['todate']));
				}
			}
			$paymentdata['where']['member_id']=$this->userID;
			$paymentdata['orderby']="with_id desc";
			//$paymentdata['where']['status !=']="transfer";
			$matrixrecord=$this->my_model->selectRecords($paymentdata);
			$message['msg']="Sucessfully Deleted";
			$message['flag']="success";
			$viewdata['message']=$message;
			//echo $this->db->last_query();exit();
			$viewdata['history']=$matrixrecord;
			if (!$this->input->is_ajax_request()) {
			$this->load->view('view_withdrawal_history',$viewdata);
			}
			else {
				if($this->input->post())
				{
					$msg = "Search Sucessfully";
					$jsonResult['flag'] = 'success';
					$jsonResult['msg'] = $msg;
					$jsonResult['view']=$this->load->view('ajaxtemplates/view_withdrawal_search',$viewdata,true);	
					exit(json_encode($jsonResult));
				}
				else
				{
						$this->load->view('ajaxtemplates/view_withdrawal_history_ajax',$viewdata);
				}
			//$this->load->view('ajaxtemplates/view_withdrawal_history_ajax',$viewdata);
			}
		}
		public function membership()
		{
			
			$viewdata['title']="Membership - marketerSmile";
			$viewdata['keywords']="Membership Keywords";
			$viewdata['description']="Membership Description";
			
			$membershiprecord=$this->db->query("select * from memberships where ishide=0 and status =1 and id not in(select mem.id FROM memberships as mem, membermemberships as m WHERE m.member_id = '".$this->userID."' AND mem.id= m.membership_id)")->result();
			
			$memberrecord=$this->db->query("select * FROM memberships as mem, membermemberships as m WHERE m.member_id = '".$this->userID."' AND mem.id= m.membership_id order by purchasedate")->result();
			
			$dataprocessor['table']='processors';
			$dataprocessor['where']['receivefund']='1';
			$dataprocessor['where']['proc_status']='1';
			$processordata=$this->my_model->selectRecords($dataprocessor);
			foreach($processordata as $pkey=>$pval)
			{
				$processor_id[]=$pval->id;
			}
			
			$wallet=$this->wallet->wallet_info(implode(',',$processor_id),'cash,repurchase,earning,commission',$this->userID);	 	
		
			$viewdata['wallets']=$wallet;
			$viewdata['wallet_type']='cash,repurchase,earning,commission';
			$viewdata['membership']=$membershiprecord;
			$viewdata['memberrecord']=$memberrecord;
			if (!$this->input->is_ajax_request()) {
			$this->load->view('view_membership',$viewdata);
			}
			else {
			$this->load->view('ajaxtemplates/view_membership_ajax',$viewdata);
			}
		}
		public function purchasemembership($id=null)
		{
			
			$viewdata['title']="Membership - marketerSmile";
			$viewdata['keywords']="Membership Keywords";
			$viewdata['description']="Membership Description";
			$memberdata['table']=$this->membershiptable;
			$memberdata['where']['id']=$id;
			$membershiprecord=$this->my_model->selectRecords($memberdata);
			$membershiprecord=$membershiprecord[0];
			if($this->input->post())
			{
				$this->functionname = __FUNCTION__;
				$filename=($this->controllerName.'->'.$this->functionname);
		
		
			$post = $this->input->post();
			
			$wallet_type=$post['paymentmethod'];
			$gateway=$post['paymentprocessor'];
			$currentbal=$this->wallet->current_balance($gateway,$wallet_type,$this->userID);
			/*Commission Allocate*/
			$commision=explode(',',$post['hdReferralCommission']);
			/*Unset Other records*/
			$newarray['member_id']=$this->userID;
			$newarray['membership_id']=$id;
			$newarray['purchasedate']=DATE_TIME;
			if($membershiprecord->membership_fee_type!='One Time')
			{
				$newarray['expirydate']=date('Y-m-d h:i:s',(strtotime(DATE_TIME)+($membershiprecord->membership_fee_value * 24*60*60)));
			}
			$newarray['status']=1;
			$newarray['autorenew']=$membershiprecord->fpfreeplan;
			
			unset($post['paymentmethod']);
			unset($post['paymentprocessor']);
			unset($post['checkbox-inline']);
			unset($post['hdReferralCommission']);
			/*Commision Earning*/
			
		
			if($currentbal < $post['paid_amount'])
			{
				$msg = "Insufficent Balance";
					$jsonResult['flag'] = 'fail';
			}
			elseif( $position=$this->save($newarray,$this->membermembershiptable,'/addbanner')){
				//set the flase mesage.
				//$msg = $this->lang->line('user_details_saved_successfully');
				$this->wallet->update_balance($gateway,$wallet_type,$this->userID,$post['paid_amount'],'purchase',$currentbal,"purchaseadmount");
				/*Commission Part*/
				$commision_paid=$this->wallet->commission_allocator($this->userID,$commision,$post['paid_amount'],$id,$position,2,$this->controllerName);
				
				$msg = "Membership have been saved successfully";
				$this->utility->set_flashdata('success',$msg,300);
				$jsonResult['flag'] = 'success';
				$wallet=$this->wallet->wallet_info($membershiprecord->paymentprocessors,$membershiprecord->paymentmethod,$this->userID);	 	
				$viewdata['wallets']=$wallet;
				$viewdata['wallet_type']=$membershiprecord->paymentmethod;
				$jsonResult['balanceView'] = $this->load->view('ajaxtemplates/view_current_balance',$viewdata,TRUE);
			}
			else {
				$msg = "Banner Ads have not saved successfully.Please try again.";
				$jsonResult['flag'] = 'fail';
			}
			
			$jsonResult['msg'] = $msg;	
			exit(json_encode($jsonResult));		
		}
			else
			{
				$wallet=$this->wallet->wallet_info($membershiprecord->paymentprocessors,$membershiprecord->paymentmethod,$this->userID);	
				$viewdata['wallets']=$wallet;
				$viewdata['wallet_type']=$membershiprecord->paymentmethod;
				$viewdata['membership']=$membershiprecord;
				
				if (!$this->input->is_ajax_request()) {
				$this->load->view('ajaxtemplates/membership_purchase',$viewdata);
				}
				else {
				$this->load->view('ajaxtemplates/membership_purchase',$viewdata);
				}
			}
		}
		public function balancetransfer($status='member')
		{
			
			
			$viewdata['title']="Withdrawal - marketerSmile";
			if($this->input->post())
			{
				$this->functionname = __FUNCTION__;
				$filename=($this->controllerName.'_'.$this->functionname);
				$post = $this->input->post();
				if($post['ptype']==0)
				{
					$wallet_type=$post['balance'];
					$gateway=$post['paymentprocessor'];
					$newval['amount']=$post['amount'];
					$newval['to_member']=$post['transferto'];
					$newval['processorname']=$this->processorname($post['paymentprocessor']);
					$newval['tran_amount']=$post['totalamntfil'];
					$newval['fees']=abs($post['amount']-$post['totalamntfil']);
					$newval['topaymentmethod']=$wallet_type;
				}
				else
				{
					$wallet_type=$post['paymentmethodfrom'];
					$gateway=$post['paymentprocessorfrom'];
					$newval['amount']=$post['pamount'];
					$newval['processorname']=$this->processorname($post['paymentprocessorto']);
					$newval['processornamefrom']=$this->processorname($post['paymentprocessorfrom']);
					$newval['frompaymentmethod']=$post['paymentmethodfrom'];
					$newval['topaymentmethod']=$post['paymentmethodto'];
					$newval['tran_amount']=$post['totalamntfil'];
					$newval['fees']=abs($post['pamount']-$post['totalamntfil']);
				}
				
				$newval['member_id']=$this->userID;
				$newval['req_dt']=DATE_TIME;
				$newval['ip_add']=$this->input->ip_address();
				$newval['ptype']=$post['ptype'];
			
				//Wallet Balance
				$currentbal=$this->wallet->current_balance($gateway,$wallet_type,$this->userID);
				$viewdata['title']="Member Panel";
				
				if($newval['amount']>$currentbal)
				{
					$newval['status']='Pending';
				}
				else
				{
					$newval['status']='Complete';
				}
				if( $position=$this->save($newval,$this->balancetable,'/withdrawal')){
					$this->wallet->update_balance($gateway,$wallet_type,$this->userID,$newval['amount'],'purchase',$currentbal,"withdrawal");
					if($post['ptype']==0)
					{
						$memberid=$newval['to_member'];
					}
					else
					{
						$memberid=$this->userID;
						$wallet_type=$post['paymentmethodto'];
						$gateway=$post['paymentprocessorto'];
					}
					$currentbal=$this->wallet->current_balance($gateway,$wallet_type,$memberid);
					$this->wallet->update_balance($gateway,$wallet_type,$memberid,$newval['tran_amount'],'fund',$currentbal,"Balance Transfer");
					$msg = "Balance transfer have been saved successfully";
					$this->utility->set_flashdata('success',$msg,300);
					$jsonResult['flag'] = 'success';
				/*Commission Part*/
				
				}
				else {
				$msg = "Banner Ads have not saved successfully.Please try again.";
				$jsonResult['flag'] = 'fail';
			}
			
			$jsonResult['msg'] = $msg;	
			exit(json_encode($jsonResult));		
			}
			else
			{
				
				$wallet=$this->wallet->wallet_info('1,2,11','cash,repurchase,earning,commission',$this->userID);
				$paymentdata['table']=$this->paymenttable;

				$paymentdata['where']['member_id']=$this->userID;
				if($status)
				$matrixrecord=$this->my_model->selectRecords($paymentdata);
				//echo $this->db->last_query();exit();
				$viewdata['payment']=$matrixrecord;
				$viewdata['wallets']=$wallet;
				$viewdata['wallet_type']='cash,repurchase,earning,commission';
				if (!$this->input->is_ajax_request()) {
				$this->load->view('view_balance_transfer',$viewdata);
				}
				else {
				$this->load->view('ajaxtemplates/view_balance_transfer_ajax',$viewdata);
				}
			}
		
		}
		
		public function profile($type='')
		{	$counter=0;
			$data['table'] = $this->treeTable;	
			$data['where']['member_id']=$this->userID;
			$memberdata=$this->my_model->selectRecords($data);
			$viewdata['member']=$memberdata[0];
			
			$condition['where']['member_id']=$this->userID;
	
			/* Check the captcha used in that or not */
			
			
			if($type == NULL)
			{
				$viewdata['title']="Profile-marketersmile";
				if($this->input->post())
				{
					$post=$this->input->post();
					if($_FILES['member_photo']['name']!='')
					{
						$post['member_photo']=$this->utility->uploadFile($_FILES,'member',true);
						
						if(is_array($post['member_photo'][0]))
						{
							$msg=$post['member_photo'][0]['error'];
							$counter++;
						}
						else
						{
							$post['member_photo']=$post['member_photo'][0];
						}
					}
					$captchacode=$post['captchacode'];$arr=array();
					if(isset($post['Processor_Acc_Id']))
					{
						
						$arr['Processor_Acc_Id']=$post['Processor_Acc_Id'];
						$post['custom_value']=implode('-',$arr);
					}
					unset($post['captchacode']);unset($post['code']);
					unset($post['Processor_Acc_Id']);
					if($counter!=0)
					{
						$msg=$msg;
						$jsonResult['flag'] = 'fail';
					}
					elseif($captchacode != $this->session->userdata['captchaCode'])
					{
						$msg = "Invalid Captcha";
						$jsonResult['flag'] = 'fail';
					}
					elseif($this->save($post,$this->treeTable,'update',$condition)){
						//set the flase mesage.
						//$msg = $this->lang->line('user_details_saved_successfully');
						//$this->logs->insertMemberLogs($this->userID,$filename);
						$filename="Profile->Updated";
						$this->logs->insertMemberLogs($this->userID,$filename);
						$msg = "Profile has been updated successfully";
						$this->utility->set_flashdata('success',$msg,300);
						$jsonResult['flag'] = 'success';
					}
					else {
						$msg = "Profile has not been updated successfully.";						
						$jsonResult['flag'] = 'fail';
					}
					if (!$this->input->is_ajax_request()) {
						  redirect(SITEURL.$this->controllerName);
					}
					$jsonResult['msg'] = $msg;	
					exit(json_encode($jsonResult));	
				}
				else
				{
					
					$viewdata['captchaSettings'] = $this->settings->getCaptchaSettings('MemberProfile');
					if($viewdata['captchaSettings']){
						$viewdata['captchImg']=$this->utility->generateCaptch(4,$viewdata['captchaSettings']);
					}
					if (!$this->input->is_ajax_request()) {
						$this->load->view('view_member_profile',$viewdata);
					}
					else {
						$this->load->view('view_member_profile_ajax',$viewdata);
					}
				}
			}
			else{
					$viewdata['title']="Security-marketersmile";
				if($this->input->post())
				{
					$counter=0;
					$post=$this->input->post();
					if(isset($post['current_pwd']))
					{
						$password=$this->utility->decode_password($memberdata[0]->password);
						if($password!=$post['current_pwd'])
						{
							$msg = "Password Didn't Match with current password";
							$jsonResult['flag'] = 'fail';
						
						}elseif($post['new_pwd']!=$post['confirm_pwd'])
						{
							$msg = "Password Didn't Match";
							$jsonResult['flag'] = 'fail';
						}
						else
						{
							$updatedata['password']=$this->utility->encode_password($post['new_pwd']);$counter++;
							$filename="Security->Change password";
						}
					}
					if(isset($post['new_answer']))
					{
						if($post['new_answer']!=$post['cnew_answer'])
						{
							$msg = "Answer Didn't Match";
							$jsonResult['flag'] = 'fail';
						}
						else
						{
							$updatedata['security_answer']=$this->utility->encode_password($post['new_answer']);$counter++;
							$filename="Security->Change Answer";
							
						}
						$updatedata['security_question']=$post['security_question'];
					}
					
					if($counter!=0){
					if($this->save($updatedata,$this->treeTable,'update',$condition)){
					//set the flase mesage.
					//$msg = $this->lang->line('user_details_saved_successfully');
					//$this->logs->insertMemberLogs($this->userID,$filename);
					$this->logs->insertMemberLogs($this->userID,$filename);
					$msg = "Profile has been updated successfully";
					$this->utility->set_flashdata('success',$msg,300);
					$jsonResult['flag'] = 'success';
					}
					else {
						$msg = "Profile has not been updated successfully updated.";
						$jsonResult['flag'] = 'fail';
					}
					if (!$this->input->is_ajax_request()) {
						  redirect(SITEURL.$this->controllerName);
					}}
					$jsonResult['msg'] = $msg;	
					exit(json_encode($jsonResult));	
				}else
				{	
					if (!$this->input->is_ajax_request()) {			
						$this->load->view('view_member_security',$viewdata);
					}
					else {
						$this->load->view('view_member_security_ajax',$viewdata);
					}
				}
			}
			
			
				
		}
		
		public function addtestimonial()
		{
			$viewdata['title']="Add Testimonial";
			$data['table'] = $this->testimonialtable;
			$data['where']['member_id']=$this->userID;
			$memberdata=$this->my_model->selectRecords($data);
			$viewdata['image1'] = SITEURL.'external/img/dummy.jpg';
			$viewdata['image2'] = SITEURL.'external/img/refresh.png';
			$viewdata['member']=$memberdata;
			$this->functionname = __FUNCTION__;
			
			$viewdata['captchaSettings'] = $this->settings->getCaptchaSettings('MemberAddtestimonial');
			if($this->input->post())
			{
				$post = $this->input->post();
				$filename='testimonial'.' ->'. $this->lang->line('testimonial added');
				$post['photo']=$this->utility->uploadFile($_FILES,'testimonials');
				$post['photo']=$post['photo'][0];
				$conddata['where']['member_id']=$this->userID;
				$member=$this->utility->getMembers($conddata);
				$post['member_id']=$member[0]['member_id'];
				$post['dname']=$member[0]['f_name'].' '.$member[0]['l_name'];
				if($viewdata['captchaSettings']){
					$captchaCode = $post['captchaCode'];
					unset($post['captchaCode']);
				}
				if($viewdata['captchaSettings'] && $captchaCode != $this->session->userdata['captchaCode']){
					$msg =$this->lang->line('please enter valid captcha code'); 
					$jsonResult['flag'] = 'fail';
				}
				else {
					$updatedata['status']=0;
					$this->my_model->updateRecords($updatedata,$this->testimonialtable,$data);
					if(is_array($post['photo']))
					{
						$msg =$post['photo']['error']; 
						$jsonResult['flag'] = 'fail';
					}
					elseif($this->save($post,$this->testimonialtable,'/addtestimonial')){
						//set the flase mesage.
						//$msg = $this->lang->line('user_details_saved_successfully');
						//$this->logs->insertMemberLogs($this->userID,$filename);
						$this->logs->insertMemberLogs($this->userID,$filename);
						$msg = "Testimonial have been saved successfully";
						$this->utility->set_flashdata('success',$msg,300);
						$jsonResult['flag'] = 'success';
					}
					else {
						$msg = "banner Plan have not saved successfully.Please try again.";
						$jsonResult['flag'] = 'fail';
					}
					if (!$this->input->is_ajax_request()) {
						  redirect(SITEURLADMIN.$this->controllerName);
					}
				}
				$jsonResult['msg'] = $msg;	
				exit(json_encode($jsonResult));				
			}
			else {
				if($viewdata['captchaSettings']){
					$viewdata['captchImg']=$this->utility->generateCaptch(4,$viewdata['captchaSettings']);
				}
				
				if (!$this->input->is_ajax_request()) {			
					$this->load->view('view_add_testimonial',$viewdata);
				}
				else {
					$this->load->view('view_add_testimonial_ajax',$viewdata);
				}
				
			}
			
		}
		public function save($data,$table,$fun,$where='')
		{
			//$this->load->library('encryption');
	
		
			//$ciphertext = $this->encryption->encrypt($post['txtPassword']);
			if($fun!='update')
			$result=$this->my_model->insertRecords($data,$table);
			else
			$result=$this->my_model->updateRecords($data,$table,$where);
			if($result != false){
				if($table==$this->balancetable)
				{
					$valdata['member_id']=$data['member_id'];
					$valdata['amount']=$data['amount'];
					$valdata['fee']=$data['fees'];
					$valdata['req_dt']=DATE_TIME;
					$valdata['payment_processor']=$data['processorname'];
					$valdata['status']='transfer';
					$this->my_model->insertRecords($valdata,$this->withdrawtable);					
				}				
				return $result;
			}
			else {				
				return false;
			}
		
		
		/*if (!$this->input->is_ajax_request()) {
		  redirect(SITEURLADMIN.$this->controllerName);
		}	
		exit(json_encode($jsonResult));	*/
		}
		public function processorname($id)
		{
				$processordata['where']['id']=$id;
				$processordata['table']='processors';
				$processor_detail=$this->my_model->selectRecords($processordata);
				return $processor_detail[0]->proc_name;
		}
		//total no of active member
		/*public function deleteaccount()
		{
			$data['isdelete']=1;
			$condition['where']['member_id']=$this->userID;
			$this->my_model->updateRecords($data,$this->treeTable,$condition);
			$this->session->unset_userdata('memberUser');
			redirect(SITEURL.'login');
		}
*/
	public function deleteaccount(){
		$viewdata['title'] = "Delete Account";
		$viewdata['captchaSettings'] = $this->settings->getCaptchaSettings('MemberLogin');
		if($this->input->post())
		{
			$post=$this->input->post();
			$member_id = $this->userID;
			$selectrequest['table']='members';
			$password=$this->utility->encode_password($post['current_pwd']);
			
			$selectrequest['where']=array('member_id'=>$member_id);
			$result = $this->my_model->selectRecords($selectrequest);
			if(count($result) > 0){
				$password=$this->utility->decode_password($result[0]->password);
			}
			if($viewdata['captchaSettings'] && ($post['captchaCode'] != $this->session->userdata['captchaCode'])){
					$msg = "Invalid Captcha";
					$jsonResult['flag'] = 'fail';
					$this->utility->set_flashdata('danger',$msg,300);
					redirect(SITEURL.'member/deleteaccount');
			}
			elseif((count($result) > 0) && ($password==$post['current_pwd'])){
				$data['isdelete']='1';
				$data['active_status']='0';
				$condition['where']['member_id']=$this->userID;
				$this->my_model->updateRecords($data,'members',$condition);
				$this->session->unset_userdata('memberUser');
				$msg = 'Your account have been deleted';
				$this->utility->set_flashdata('success',$msg,300);
				redirect(SITEURL.'login');
			}
			else {
				$msg = 'Please enter the valid password';
				$this->utility->set_flashdata('danger',$msg,300);
				redirect(SITEURL.'member/deleteaccount');
			}
		}
		else {
			$viewdata['captchaSettings'] = $this->settings->getCaptchaSettings('MemberLogin');
			if($viewdata['captchaSettings']){
				$viewdata['captchImg']=$this->utility->generateCaptch(4,$viewdata['captchaSettings']);
			}
			$this->load->view('view_delete_member',$viewdata);
		}
	}
		
		//total no of active member
		public function viewallmember()
		{
			echo $this->utility->membercounter();
		}
		//show testimonials 
		public function testimonials()
		{
			$viewdata['activeTab'] = 'testimonials';
			$data['table']=$this->testimonialtable;
			$data['where']['status']=1;
			$data['orderby']='test_id desc';
			$viewdata['test']=$this->my_model->selectRecords($data);
			$this->load->view('view_testimonal',$viewdata);
		}
		//menu for drupal part
		public function drupalleftmenu()
		{
			$this->load->view('templates/left-menus-drupal');
		}
		public function changestaticip()
		{
			$data['table'] = $this->treeTable;	
			$data['where']['member_id']=$this->userID;
			$memberdata=$this->my_model->selectRecords($data);
			$viewdata['member']=$memberdata[0];
			$viewdata['title']="Security-marketersmile";
			
			$condition['where']['member_id']=$this->userID;
			if($this->input->post())
			{
				$filename="Security->Change Static Ip";
				$updatedata['usestaticip']=$this->input->post('usestaticip');
				
				if($this->save($updatedata,$this->treeTable,'update',$condition)){
					//set the flase mesage.
					//$msg = $this->lang->line('user_details_saved_successfully');
					//$this->logs->insertMemberLogs($this->userID,$filename);
					$this->logs->insertMemberLogs($this->userID,$filename);
					$msg = "updated successfully";
					$this->utility->set_flashdata('success',$msg,300);
					$jsonResult['flag'] = 'success';
					}
					else {
						$msg = "Profile has not been updated successfully.";
						$jsonResult['flag'] = 'fail';
					}
					if (!$this->input->is_ajax_request()) {
						  redirect(SITEURL.$this->controllerName);
					}
					$jsonResult['msg'] = $msg;	
					exit(json_encode($jsonResult));	
				}else
				{	
					if (!$this->input->is_ajax_request()) {			
						$this->load->view('view_member_security',$viewdata);
					}
					else {
						$this->load->view('view_member_security_ajax',$viewdata);
					}
				}
		}
		public function messageread($id=null)
		{
			if($id!=null)
			{
				$filename="Admin Messages -> Marked as Read (Id : $id)";
				$this->logs->insertMemberLogs($this->userID,$filename);
			}
			
			$dataR['table'] = 'membermessage_histories';	
			$dataR['orderby']='id desc';
			$memberdata=$this->my_model->selectRecords($dataR);
			foreach($memberdata as $key=>$member)
			{
				if($member->messagetype!=0)
				{
					$memberrecord=explode(',',$member->memberlist);
					if(!in_array($this->userID,$memberrecord))
					{
						continue;
					}
				}
				//Read Condition
				$log="Admin Messages -> Marked as Read (Id : $member->id)";
				$readdata['table']='memberlogs';
				$readdata['where']['log']=$log;
				$readdata['where']['member_id']=$this->userID;
				$read=$this->my_model->selectRecords($readdata);
				//Value Fetching
				$data['read']=(count($read)>0)?1:0;
				$data['title']=$member->description;
				$data['description']=$member->message;
				$data['messagedate']=$member->messagedate;
				$data['id']=$member->id;
				$arraydata[]=$data;
			}
			$viewdata['message']=@$arraydata;
			if (!$this->input->is_ajax_request()) {			
						$this->load->view('view_message',$viewdata);
					}
					else {
						$this->load->view('ajaxtemplates/view_message_ajax',$viewdata);
					}
			
			
		}
		public function usedipaddress()
		{
				$readdata['table']='memberstaticips';
				$readdata['where']['member_id']=$this->userID;
				$record=$this->my_model->selectRecords($readdata);
				$viewdata['title']='marketerSmile';
				$viewdata['record']=$record;
				$this->load->view('view_iprecord',$viewdata);
		}
		
	public function withdrawpdf($id)
	{
		$invoicedata['table']='invoice_details';
		$invoicedata['where']['type']='withdraw';
		$invoicedata['where']['member_id']=$this->userID;
		$invoicedata['where']['item_id']=$id;
		$invoicerecord=$this->my_model->selectRecords($invoicedata);
		if(count($invoicerecord)==0)
		{
			/* Purchase Biz Ads Email Sends */
		$dataD['table'] = 'withdraws';
		$dataD['where'] = array('with_id '=>$id);
		$memberBizads = $this->my_model->selectRecords($dataD);
		
		/*print('<pre>');
		print_r($memberBizads);
		echo $this->db->last_query();
		exit;*/
		
		if(count($memberBizads) > 0){
			/* Get the Email tempate */
			
			
			foreach($memberBizads as $keyP => $valP){
				$dataM['table'] = 'members as m';
				$dataM['where'] = array('member_id'=>$valP->member_id);
				$memberDetails = $this->my_model->selectRecords($dataM);
	
			
				
					
					/* ================ Invoice Section ========================= */
					$dataInvoice['table'] = 'invoice_details';
					$dataInvoice['orderby'] = 'invoice_no desc';					
					$invoiceDetails = $this->my_model->selectRecords($dataInvoice);
					$invoiceNo = '00001';
					if(count($invoiceDetails) > 0){
						$invoiceNo = ($invoiceDetails[0]->invoice_no + 1);
						$invoiceNo = '0000'.$invoiceNo;
					}
			
					/* PDF Generate */
					//$arrInvoiceDetails['table'] = 'ptcbanners';
					$arrInvoiceDetails[0]['qty'] = 1;
					$arrInvoiceDetails[0]['name'] = "Withdrawal ".$valP->status;
					$arrInvoiceDetails[0]['desc'] = "Advertising Fees";
					$arrInvoiceDetails[0]['link'] = 	"#";
					$arrInvoiceDetails[0]['price'] = 	"$".$valP->amount;
					$arrInvoiceDetails[0]['fees'] = "$".$valP->fee;
					$arrInvoiceDetails[0]['date'] = date('d-m-Y',strtotime($valP->req_dt));;
					$subTotal = ($valP->amount - $valP->fee);
					//$arrInvoiceDetails[0]['fees'] = "$".$valP->discount;					
					$arrInvoiceDetails[0]['sub_total'] = "$".$subTotal;
					
					$arrOtherDetails['totalPrice'] = $subTotal;

					$arrOtherDetails['invoiceNo'] = $invoiceNo; 
					$arrOtherDetails['fileName'] = 'advertisement_withdraw_'.$invoiceNo; 
					$arrOtherDetails['PDFType'] = 'F'; 
					$arrOtherDetails['invoiceDate'] = $valP->req_dt;
					
					if($this->utility->generatePDFInvoice($arrInvoiceDetails,$arrOtherDetails)){
						$emailData['attachment'] = SITEURL.'external/pdf_docs/'.$arrOtherDetails['fileName'].'.pdf';
						$inserInvRecords['type'] = 'withdraw';
						$inserInvRecords['item_id'] = $valP->with_id;
						$inserInvRecords['invoice_no'] = $invoiceNo;
						$inserInvRecords['member_id'] = $memberDetails[0]->member_id;
						$inserInvRecords['price'] = $valP->amount;
						$inserInvRecords['fees'] = $valP->fee;						
						$this->utility->insertInvoice($inserInvRecords);
					}
					
				
						
			}
		}
			
		}
		$invoicedata['table']='invoice_details';
		$invoicedata['where']['type']='withdraw';
		$invoicedata['where']['member_id']=$this->userID;
		$invoicedata['where']['item_id']=$id;
		$invoicerecord=$this->my_model->selectRecords($invoicedata);
		//echo $this->db->last_query();
		$filename="advertisement_withdraw_0000".$invoicerecord[0]->invoice_no;
		$attachment = SITEURL.'external/pdf_docs/'.$filename.'.pdf';
		redirect($attachment);
	}
	public function support()
		{
			$viewdata['title']="Member Support";
			$dataM['table']="member_tickets";
			if($this->input->post())
			{
				$post=$this->input->post();
				$dataM['where']=array('status'=>$post['searchby']);
			}
			$dataM['where']['m_id']=$this->userID;
			$viewdata['ticketdetails']=$this->my_model->selectRecords($dataM);
			if (!$this->input->is_ajax_request()) {
			$this->load->view('view_support',$viewdata);
			}
			else {
				if($this->input->post())
			{
				$msg = "Search Sucessfully";
				$jsonResult['flag'] = 'success';
				$jsonResult['msg'] = $msg;
				$jsonResult['view']=$this->load->view('ajaxtemplates/view_support_search',$viewdata,true);	
				exit(json_encode($jsonResult));
			}
			else{
				$this->load->view('ajaxtemplates/view_support_ajax',$viewdata);
			}
			}
			
		}
		public function supportadd()
		{
			$viewdata['title']="Member Support";$randomNumber='';
			$viewdata['captchaSettings'] = $this->settings->getCaptchaSettings('PublicSupport');
			if($this->input->post())
			{
				$post=$this->input->post();
					if($_FILES['file1']['name']!=''||$_FILES['file2']['name']!=''||$_FILES['file3']['name']!=''||$_FILES['file4']['name']!='')
					{
						$photo=$this->utility->uploadFile($_FILES,'support');
						$msg='';$counter=0;$post["attatchment"]='';
						foreach($photo as $key => $val)
						{
							if(is_array($val))
							{
								$msg.=$val['error']."<br>";
								
								$counter++;
							}
							else
							{
								$value="file".($key+1);
								$post["attatchment"].=$val.',';
							}
						}
					}
						for ($i = 1; $i < 12; $i++) {
				$randomNumber .= mt_rand(0, 9);
					}
				$post=$this->input->post();
				$post['mt_id']=$randomNumber;
				//Public Ticket ID #123019160143 [hi test]
				$post['subject']="Member Ticket ID #".$randomNumber." [".$post['subject']."]";
				$post['m_id']=$this->userID;
				$post['m_name']=$this->utility->getMembersField($this->userID,'user_name');
				$post['dt_create']=DATE_TIME;
				$post['dt_lastupdate']=DATE_TIME;
				$post['ip']=$this->input->ip_address();
				if($viewdata['captchaSettings']==true){
				$captchaCode=$post['captchaCode'];
				unset($post['captchaCode']);}
				if($counter!=0)
				{
					$msg = $msg;
					$jsonResult['flag'] = 'fail';
				}
				elseif($viewdata['captchaSettings']==true && $captchaCode != $this->session->userdata['captchaCode']){
						$msg = "Invalid Captcha";
						$jsonResult['flag'] = 'fail';
				}
				else
				{
						$this->my_model->insertRecords($post,'member_tickets');
				
						$name=$post['m_name'];
						$email = $this->utility->getMembersField($this->userID,'email');
						
					
						$message = "Hello $name <br><br>Thank you for communicating with us. <br>Your member ticket has been created successfully..<br><br>Your details as per our records are : <br>Your ticket id: $randomNumber <br>Ticket subject: $post[subject] <br>Ticket message: $post[message] <br><br>We will try to solve your query/issue as soon as possible and get back to you.<br>You will be getting mails regarding future updates in the ticket.<br><br>Admin<br>Marketersmile.Com";
					
						$emailData['from'] = FROMEMAIL;
						$emailData['to']= $email;
						$emailData['subject'] = $post['subject'];
						$emailData['message'] = $message;						
						
						if($this->utility->sendEmail($emailData,'html')){
							$msg = 'Your ticket successfully submited.';
							$jsonResult['flag'] = 'success';
							$this->utility->set_flashdata('success',$msg,300);
							$filename="Support  -> Added (Ticket Id : $$randomNumber)";
							$this->logs->insertMemberLogs($this->userID,$filename);
							//redirect(SITEURL.'login');	
						}
						else {
							//$msg = $this->lang->line('your password has been sent to your email address.');
							$msg = "Email service is temparrty not aviable. Please try resend your verification link";
							$jsonResult['flag'] = 'fail';
							$this->utility->set_flashdata('danger',$msg,300);													
						}
				}
				$jsonResult['msg'] = $msg;	
				exit(json_encode($jsonResult));		
				
			}
			if($viewdata['captchaSettings']){
			$viewdata['captchImg']=$this->utility->generateCaptch(4,$viewdata['captchaSettings']);
			}
			$this->load->view('ajaxtemplates/add_support',$viewdata);
		}
		public function supportstatus($status,$id)
		{
			$updatedata['status']=$status;
			$condition['where']['id']=$id;
			$this->my_model->updateRecords($updatedata,'member_tickets',$condition);
			
			$viewdata['title']="Member Support";
			$dataM['table']="member_tickets";
			if($this->input->post())
			{
				$post=$this->input->post();
				$dataM['where']=array('status'=>$post['searchby']);
			}
			$dataM['where']['m_id']=$this->userID;
			$viewdata['ticketdetails']=$this->my_model->selectRecords($dataM);
			if (!$this->input->is_ajax_request()) {
			$this->load->view('view_support',$viewdata);
			}
			else {
				if($this->input->post())
			{
				$msg = "Search Sucessfully";
				$jsonResult['flag'] = 'success';
				$jsonResult['msg'] = $msg;
				$jsonResult['view']=$this->load->view('ajaxtemplates/view_support_search',$viewdata,true);	
				exit(json_encode($jsonResult));
			}
			else{
				$this->load->view('ajaxtemplates/view_support_ajax',$viewdata);
			}
			}
			
		}
		
		public function couponstatus()
		{
			if($this->input->post())
			{
				$post=$this->input->post();
				$couponvalue=$this->wallet->coupondetail($post['code']);
				if($couponvalue==false)
				{
					$msg="Invalid Coupon Code";
					$jsonResult['flag'] = 'fail';
				}
				else
				{
					$couponvalue=$couponvalue[0];
					//print_r($couponvalue);
					$numcount=$this->wallet->couponcounter($this->userID,$couponvalue->id,$couponvalue->code,$couponvalue->fromdate);
					if($couponvalue->coupontime!=0 && $couponvalue->coupontime<$numcount)
					{
						$msg="You Cannot use this coupon now as you have used maximum time";
						$jsonResult['flag'] = 'fail';
					}
					else
					{
						if($couponvalue->coupontype>3)
						{
							$currentbal=$this->wallet->current_balance('1','cash',$this->userID);
							$this->wallet->update_balance(1,'cash',$this->userID,$couponvalue->amount,'fund',$currentbal,"Coupon Applied");
							$insertdata['member_id']=$this->userID;
							$insertdata['coupon_id']=$couponvalue->id;
							$insertdata['code']=$couponvalue->code;
							$insertdata['discount']=$couponvalue->amount;
							$insertdata['plantype']="Add Cash";
							$insertdata['ipaddr']=$this->input->ip_address();
							$insertdata['dt']=DATE_TIME;
							$this->my_model->insertRecords($insertdata,'couponhistories');
							$this->wallet->couponupdate($couponvalue->id,$couponvalue->code);
							$msg="Successfully Appiled";
							$jsonResult['flag'] = 'success';
						}else
						{
							$msg="Invalid Coupon type";
							$jsonResult['flag'] = 'fail';
						}
					}
				}
					$filename="Coupon  -> Appiled ($msg)";
					$this->logs->insertMemberLogs($this->userID,$filename);
					$jsonResult['msg'] = $msg;	
					exit(json_encode($jsonResult));	

			}
			$this->profile();
		}
		
		
}

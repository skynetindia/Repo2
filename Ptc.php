<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Ptc extends CI_Controller {



	public $controllerName = 'ptc';

	public $themeTable = 'ptcdesigns';

	public $mainTable="ptcbanners";

	public $historyTable="ptc_histories";

	public $counterTable="ptccounters";

	public $secondaryTable = 'ptcplans';

	public $memberTable = 'members';

	public $functionname;

	public $perPage = '10';

	public $duplicateLimit = '5';

	public $importLimit = '5';

	public $bannercredit = '0'; //This get from the session after user module done 

	public $userID = "1"; //This get from the session after user module done

	public $arrPermissions = array();

	public $arrPTCAdsSettings = array();

	public $arrAutocompleteSettings = array();

	public function __construct() {

 	   parent::__construct();

	    //Check Login

	 	$sessionData = $this->session->userdata('memberUser');

		$this->userID=$sessionData['userId'];


		$this->arrPTCAdsSettings = $this->settings->getPTCAdsSettings();

		if(!$sessionData){

			$msg = $this->lang->line('please_login_to_access');

		     $this->utility->set_flashdata('danger',$msg,300);

			 redirect(SITEURL.'login');	

		}

		$this->arrAutocompleteSettings = $this->settings->getAutocompleteSettings('AdvertisementPTCAds');



		/*$this->userID = $sessionData['userId'];

	    $this->companyID = $sessionData['companyId'];



	   $permissionParm['moduleID'] = '1';

	   $permissionParm['funcModuleID'] = '1';

	   //$permissionParm['permissionID'] = '1';

	   $permissionParm['userID'] = $sessionData['userId'];

	   $this->arrPermissions = $this->utility->checkPermission($permissionParm);

	   //Check view permission*

	   if(isset($this->arrPermissions['1']) && $this->arrPermissions['1'] == 'N'){

		  //redirect(SITEURL.$this->controllerName);

		  //Need to redirect dasboard

		 $msg = $this->lang->line('access_is_denied_You_dont_have_permissions');

	     $this->utility->set_flashdata('danger',$msg,300);

		 redirect(SITEURL.'dashboard');

	   }

	   

       // Your own constructor code

	   $this->load->model('Companies_model','companies');*/

	   

    }

	

	public function index(){

		if($this->arrPTCAdsSettings['ptcsetting_isenable'] == '1'){ 

			

			

			$dataB['table'] = $this->mainTable;			 	

			$dataB['limit'] = '0';

			$dataB['offset'] = '10';

			$viewdata['textadds']=$this->my_model->selectRecords($dataB);

			$query="SELECT * FROM `ptcbanners`as l where  member_id=".$this->userID;

			$viewdata['history']=$this->db->query($query)->result();

			

			$freequery="select free_plan_id,plan_name from pending_free_plans as f,ptcplans as p where free_plan_id=p.id and free_plan_type='ptc' and member_id=".$this->userID;

			$viewdata['freeplan']=$this->db->query($freequery)->result();

			

			if (!$this->input->is_ajax_request()) {

				$this->load->view('view_ptc_ads',$viewdata);

				//$this->load->view('view_login_ads',$viewdata);

			}

			else {

				//$this->load->view('ajaxtemplates/view_login_ads_ajax',$viewdata);

				$this->load->view('ajaxtemplates/view_ptc_ads_ajax',$viewdata);

			}

		}

		else {

			redirect(SITEURL.'member');		

		}

	}

	/**

	 * Index Page for this controller.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 18-05-2016

 	 * @Updated Date :  19-05-2016

	 */

	

	public function smalladsdisplay(){

		$data['table'] = $this->mainTable;	

		$data['limit']=0;

		$data['offset']=8;	

		$data['orderby']=" rand()";

		$data['where']['style']='125x125';	 	

		$value=$this->my_model->selectRecords($data);

		return $value;	

	}

	/**

	 * Index Page for this controller.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 18-05-2016

 	 * @Updated Date :  19-05-2016

	 */

	public function largeadsdisplay(){

		$data['table'] = $this->mainTable;	

		$data['limit']=0;

		$data['offset']=4;	

		$data['orderby']=" rand()";

		$data['where']['style']='468x60';	 	

		$value=$this->my_model->selectRecords($data);

		return $value;	

	}

	/**

	 * Index Page for this controller.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 18-05-2016

 	 * @Updated Date :  19-05-2016

	 */

	public function plans(){

		$viewdata['title']="Advertisement Banners";

		$data['table'] = $this->secondaryTable;	

		$$viewdata['value']=$this->my_model->selectRecords($data);

		$this->load->view('view_banner_ads',$viewdata);

		}

		/**

	 * Index Page for this controller.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 18-05-2016

 	 * @Updated Date :  19-05-2016

	 */

	public function purchase($planid=1,$id=null,$type=0)

	{

		

		$viewdata['title']='PTC - marketerSmile';

		$viewdata['keywords'] = 'PTC Keywords';

		$viewdata['description'] = 'PTC Description';

		$viewdata['bannercredit']=$this->bannercredit;

		$viewdata['planId']=$planid;

		$viewdata['freetype']=(!is_numeric($id) && $id!=null)?$id:$type;

		$data['table'] = $this->themeTable;	

		$data['orderby'] = "id asc";	

		$themesRec = $this->my_model->selectRecords($data,true);

		$viewdata['themes']=$themesRec;

		$viewdata['jsonTheme'] = json_encode($themesRec);

		$data1['table'] = $this->secondaryTable;			

		$data1['where']= array("id"=>$planid); 	

		$plandetails=$this->my_model->selectRecords($data1);

		$plandetail=$plandetails[0];

		$fun="add";$condition='';

		if($id!=null && is_numeric($id) && $type==0)

		{

			$datamain['table']=$this->mainTable;

			$datamain['where']['id']=$id;

			$datamain=$this->my_model->selectRecords($datamain);

			$viewdata['datamain']=$datamain[0];

			$fun="update";

			$condition['where']['id']=$id;

		}

		

		if($this->input->post()){

		$this->functionname = __FUNCTION__;

		$filename=($this->controllerName.'_'.$this->functionname);

		

		

			$post = $this->input->post();

			$style=explode('x',$post['banner_size']);

			$width=$style[0];

			$height=$style[1];

			if(($fun!='update'))

			{

				if($plandetail->iscoupon==1)

				{

					$couponcodeapply=$post['couponcodeapply'];

					$couponcode=$post['couponcode'];

					$couponresponse['flag'] = 'success';

					

					if($post['couponcodeapply']==0 && $post['couponcode']!='')

					{

						$couponresponse['msg']="Please apply coupon code which you enter";

						$couponresponse['flag'] = 'fail';

					}

					elseif($post['couponcodeapply']==0 && $post['couponcode']=='')

					{

						//$couponresponse['msg']="Please apply coupon code which you enter";

						$couponresponse['flag'] = 'success';

					}

					

						unset($post['couponcodeapply']);

						unset($post['couponcode']);

				}

				

				$post['status']= ($fun=='update') ? $this->arrPTCAdsSettings['ptcsetting_iseditapprove'] :$this->arrPTCAdsSettings['ptcsetting_isapprove'];

				$post['purchasedate']=DATE_TIME;

				//$post['approve_date']=DATE_TIME;

				$post['member_id']=$this->userID;	

				$post['ptc_id']=$planid;
				
				/* Check not free Plans*/
				if(isset($post['paymentprocessor'])){
					$wallet_type=( $id=null && !is_numeric($id))?$post['paymentmethod']:'cash';
	
					$gateway=($id==null && !is_numeric($id))?$post['paymentprocessor']:1;
	
					$post['processor']=$gateway;
	
					$currentbal=$this->wallet->current_balance($gateway,$wallet_type,$this->userID);				

					$commision=($id==null && !is_numeric($id))?explode(',',$post['hdReferralCommission']):array();

					unset($post['hdReferralCommission']);
	
					unset($post['paymentmethod']);
	
					unset($post['paymentprocessor']);
				}
				$post['start_date']=date('Y-m-d h:i:s',strtotime($post['start_date']));

			}

			if($post['display_area'] == 'selected'){

				$post['display_area'] = implode(",",$post['country']);

			}

			/*Find Current Balance*/

			

			

			unset($post['hd_day']);

			unset($post['country']);

			unset($post['radio-inline']);

			unset($post['checkbox-inline']);

		

		

			$validurl=$this->utility->http_response($post['target_url']);

			if(($this->utility->http_response($post['banner_url']))!=false)

			{

				$data = getimagesize($post['banner_url']);

			}

			else

			{

					$data='';

					$msg = "Invalid Banner URL";

					$jsonResult['flag'] = 'fail';

			}if(($plandetail->iscoupon==1) && ($couponresponse['flag'] == 'fail'))

			{

				$msg=$couponresponse['msg'];

				$jsonResult['flag']=$couponresponse['flag'];

			}

			elseif(($id==null) && ($type==0) && ($currentbal < $post['paid_amount']))

			{

				$msg = "Insufficent Balance";

					$jsonResult['flag'] = 'fail';

			}elseif((date('Y-m-d',time()) > $post['start_date'])&&($id==null))

			{

				$msg = "Display Date cannot be smaller that today date";

					$jsonResult['flag'] = 'fail';

			}

			elseif(empty($data))

			{

					$msg = "Invalid Banner URL";

					$jsonResult['flag'] = 'fail';

			}

			elseif($validurl==false)

			{

				$msg = "Not a valid url";

				$jsonResult['flag'] = 'fail';

			}

			elseif($width!=$data[0] &&$height!=$data[1])

			{

					$msg = "Invalid Banner Size";

					$jsonResult['flag'] = 'fail';

			}

			

			elseif($position=$this->save($post,$this->mainTable,$fun,$condition)){

				//set the flase mesage.

				//$msg = $this->lang->line('user_details_saved_successfully');

				if($id==null)

				{

					if(($plandetail->iscoupon==1))

					{

						$coupondata['amount']=$plandetail->all_country_price;

						$coupondata['planid']=$plandetail->id;

						$coupondata['couponcode']=$couponcode;

						$couponresponse=$this->applycoupon('Coupon',$coupondata);

					}

					$this->wallet->update_balance($gateway,$wallet_type,$this->userID,$post['paid_amount'],'purchase',$currentbal,"ptc",0,$post['discount']);

					//$this->wallet->update_balance($gateway,$wallet_type,$this->userID,$post['paid_amount'],'purchase',$currentbal,"ptc");

					/*Commission Part*/

					$commision_paid=$this->wallet->commission_allocator($this->userID,$commision,$post['paid_amount'],$planid,$position,2,$this->controllerName);

					$updatedata['commission_paid']=$commision_paid;

					if($plandetail->revshare_rate!=0)

					{

						//$updatedata['shared_amount']=(($post['paid_amount']-$commision_paid)*$plandetail->revshare_rate)/100;
						$updatedata['shared_amount']=(($post['paid_amount']-$commision_paid));

					}

					$updatecondition['where']['id']=$position;

					$this->my_model->updateRecords($updatedata,$this->mainTable,$updatecondition);

				}

				/*End Of Commission Part*/

				$msg = "Ptc Plan Purchase saved successfully";

				$this->utility->set_flashdata('success',$msg,300);

				$jsonResult['flag'] = 'success';

				

				/* Current Balance get new balance */

				$dataB['table'] = $this->secondaryTable;			 	

				$dataB['where']['id'] = $planid;

				$plandetails=$this->my_model->selectRecords($dataB);

				

				$plandetail=$plandetails[0];

				$wallet=$this->wallet->wallet_info($plandetail->paymentprocessors,$plandetail->paymentmethod,$this->userID);		 	

				$viewdata['wallets']=$wallet;

				$viewdata['wallet_type']=$plandetail->paymentmethod;

				$jsonResult['balanceView'] = $this->load->view('ajaxtemplates/view_current_balance',$viewdata,TRUE);

			}

			else {

				$msg = "PTC Ads have not saved successfully.Please try again.";

				$jsonResult['flag'] = 'fail';

			}

			

			$jsonResult['msg'] = $msg;	

			exit(json_encode($jsonResult));		

		}

		else

		{

			$wallet=$this->wallet->wallet_info($plandetail->paymentprocessors,$plandetail->paymentmethod,$this->userID);		 	

			$viewdata['planadds']=$plandetails;

			$viewdata['wallets']=$wallet;

			$viewdata['wallet_type']=$plandetail->paymentmethod;

			

			$data['table'] = 'countries';			 	

			$viewdata['countries']=$this->my_model->selectRecords($data);

			$this->load->view('ajaxtemplates/ptc_ad_purchase',$viewdata);

		}

	}

	

	/*this function is used for the autocomplete */

	public function autocomplete(){

		$searchTerm = $_GET['term'];		

		/*$query="SELECT l.banner_url,l.banner_title,l.target_url,l.start_date,l.daily_budget,l.display_area,plan_name,l.id,l.status,l.banner_size,display_counter,click_counter,banner_url,ppc_id 

		FROM `ppcbanners`as l, ppcplans as p 

		where ppc_id=p.id and banner_title LIKE '%".$searchTerm."%' and member_id=".$this->userID." LIMIT 10";

		*/

		

		$query="SELECT l.banner_url,l.banner_title,l.banner_description,l.ptctheme,l.target_url,l.start_date,l.daily_budget,l.display_area,plan_name,l.id,l.status,l.banner_size,display_counter,click_counter,banner_url,ptc_id 

		FROM `ptcbanners`as l, ptcplans as p 

		where ptc_id=p.id and banner_title LIKE '%".$searchTerm."%' and member_id=".$this->userID." LIMIT 10";



		$history=$this->db->query($query)->result_array();

		$data = array();		

		foreach($history as $key => $val){

			$data['label'] = $val['banner_title']." (".$val['id'].")";						

			$data['data'] = $val['ptc_id'];

			$data['banner_title'] = $val['banner_title']; 

			$data['banner_url'] = $val['banner_url'];

			$data['target_url'] = $val['target_url'];

			$data['start_date'] = date('m/d/Y',strtotime($val['start_date']));

			$data['daily_budget'] = $val['daily_budget'];			

			$data['display_area'] = $val['display_area'];

			$data['banner_description'] = $val['banner_description'];

			$data['ptctheme'] = $val['ptctheme'];

			

			

			$arr[] = $data;	

		}

		echo json_encode($arr);

	}

	

	/**

	 * Index Page for this controller.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 18-05-2016

 	 * @Updated Date :  19-05-2016

	 */

	public function add()

	{}



	/**

	 * Index Page for this controller.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 18-05-2016

 	 * @Updated Date : 

	 */

	 public function save($data,$table='bannerplans',$fun,$condition)

	{

		//$this->load->library('encryption');

	

		

			//$ciphertext = $this->encryption->encrypt($post['txtPassword']);

			if($fun!='update')

			$result=$this->my_model->insertRecords($data,$table);

			else

			$result=$this->my_model->updateRecords($data,$table,$condition);

			

			if($result){

				//redirect(SITEURLADMIN.$this->controllerName);

				

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

	public function index1($orderby = 'date',$order = 'desc')

	{}

	

	/**

	 * Index Page for this controller.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 18-05-2016

 	 * @Updated Date :  19-05-2016

	 */

	public function view()

	{

		/* Check Setting for Paid Meber or All Members*/

		$ispaid = $this->utility->getMembersField($this->userID,'ispaid');

	

		if($this->arrPTCAdsSettings['ptcsetting_viewmember'] == '1' && $ispaid == '0'){

			$msg = 'Please purchase any things.Only paid meber are allowd to PTC Ads';

		    $this->utility->set_flashdata('danger',$msg,300);

			redirect(SITEURL.'ptc');	

		}

		

			$viewdata['activeTab'] = 'ptc';

			$viewdata['title']="view ptc ads";

		$result_array=$this->db->query("select * from ptcbanners where total_click > click_counter and daily_budget >today_click and status=1 and pause=1")->result_array();

		$totalAds = 0;

		if(count($result_array)>0)	

		{	

			

			$arrbannerId = "";

			foreach($result_array as $rkey => $rval){

				$arrbannerId .= "'".$rval['id']."',";

			}

			$arrbannerId = rtrim($arrbannerId,",");

			do{

				$totalAds++; 		

				$query=$this->db->query("select * from ptcbanners where total_click > click_counter and daily_budget >today_click and status=1 and pause=1 and id IN($arrbannerId) order by rand() limit 0,1")->result_array();

				$todaydate=date('Y-m-d',time());

				$ptcbannerid=$query[0]['id'];

				$country=$this->logs->getLocationInfoByIp();

				$country=$country['country'];$count=0;

				if($query[0]['display_area']!='all')

				{

					$countryarray=explode(',',$query[0]['display_area']);

					

					if(!in_array($country,$countryarray))

					{

						

						$count++;

					}

				}

			}

			while($count==0 && ($totalAds <= count($result_array)));			

			if(count($query)>0)

			{

				

				if($todaydate!=$query[0]['today_date'])

				{

					$updatedata1['today_date']=$todaydate;

					$updatedata1['today_click']=0;

					

					

				}

				$updatedata1['display_counter']=$query[0]['display_counter']+1;

				$condition['where']['id']=$ptcbannerid;

				$this->my_model->updateRecords($updatedata1,$this->mainTable,$condition);

				//Add to the counter table

				$addcounter['ptc_id']=$query[0]['ptc_id'];

				$addcounter['banner_id']=$query[0]['id'];

				$addcounter['ip_address']=$this->input->ip_address();

				$country=$this->logs->getLocationInfoByIp();

				$addcounter['country']=$country['country'];

				$this->my_model->insertRecords($addcounter,'ptccounters');

				//end of the process

				$data['table'] = $this->mainTable. ' as dm';	

				$data['fields'] = array('dm.id as id','dm.ptc_id as planid','dm.banner_title as title','dm.banner_url as banner','dm.target_url as target_url','dm.display_counter as display','dm.banner_description as description','dm.click_counter as click','mem.member_photo as photo','dm.like_counter as like','dm.ptctheme as themeid','mem.user_name as username','dm.member_id as member_id','dm.today_click as today_click','dm.daily_budget as dailybudget','dm.click_amount as click_amount');

				$data['orderby']="rand()";

				$data['where']['dm.status']="1";

				$data['where']['dm.pause']="1";

				$data['where']['dm.id']=$ptcbannerid;

				$data['joins'] = array($this->memberTable.' as mem' => array('mem.member_id = dm.member_id','LEFT'));

				$value=$this->my_model->selectRecords($data);

				//echo $this->db->last_query();

				$ptcthemedata['table']=$this->themeTable;

				$ptcthemedata['where']['id']=$value[0]->themeid;

				$theme=$this->my_model->selectRecords($ptcthemedata);

				

				$viewdata['value']=$value;

				$viewdata['theme']=$theme[0];

				

			}

			else

			{

				$viewdata['value']='';

				$viewdata['theme']='';

				

			}

		}

		else

			{

				$viewdata['value']='';

				$viewdata['theme']='';

			

			}

			

		if (!$this->input->is_ajax_request()) {

			$this->load->view('view_ptc_view',$viewdata);

			//$this->load->view('view_login_ads',$viewdata);

		}

		else {

			//$this->load->view('ajaxtemplates/view_login_ads_ajax',$viewdata);

			$this->load->view('ajaxtemplates/view_ptc_view_ajax',$viewdata);

		}

			

			

	}

	

	/* this function is used to dissplay the ifraem for credited banner adds*/

	public function creditcounter($ID){

		$viewdata['title']='Advertisement Banners - marketerSmile';

		$viewdata['Meta_Title'] = 'Marketersmile Advertisement Banners - marketerSmile';

		$viewdata['Meta_Descriptions'] = 'Marketersmile Advertisement Banners - marketerSmile';

		

		$data['table'] = $this->mainTable;	

		$data['where']['id']=$ID;	 	

		$value=$this->my_model->selectRecords($data);

		$newCounter = $value[0]->click_counter + 1;

		$newTodayCounter = $value[0]->today_click + 1;

		

		$arrData['click_counter'] = $newCounter;

		$arrData['today_click'] = $newTodayCounter;

		$table = $this->mainTable;

		$condition['where'] =  array('id'=>$ID);

		

		$data1['table'] = $this->secondaryTable;			

		$data1['where']= array("id"=>$value[0]->ptc_id); 	

		$plandetails=$this->my_model->selectRecords($data1);

		/* Update the records */

		$result = $this->my_model->updateRecords($arrData,$table,$condition);

		

		$viewdata['ptcID'] = $ID;

		$viewdata['credit'] = $plandetails[0]->clickcost;
		$viewdata['waitcounter'] = isset($plandetails[0]->countersecond) ? $plandetails[0]->countersecond : '0';		

		$viewdata['CreditField'] = 'creditcounters';

		$viewdata['userid'] = $this->userID;

		$viewdata['url'] = $value[0]->target_url;		


		$viewdata['arrPTCAdsSettings'] = $this->arrPTCAdsSettings;		

		//print_r($this->arrPTCAdsSettings);

		//exit;

		$this->load->view('view_ptc_credit_iframe',$viewdata);				

	}

	

	/* Banner Ads Credits inser to creditcounter and memeber update credits  */

	public function creditCounterEffect($bannerID,$credit){

		$dataelm['table']='ptcbanners';

		$dataelm['where']=array('id'=>$bannerID);

		$todaydate=date('Y-m-d');

		$dataelm['like']['today_date']="$todaydate";

		$prerecord=$this->my_model->selectRecords($dataelm);

		//echo $this->db->last_query();

		if(count($prerecord)>0)

		{

			$datanew['table']='ptc_histories';

			$datanew['where']=array('member_id'=>$this->userID,'banner_id'=>$bannerID);

			$datanew['like']['click_time']=$todaydate;

			if($this->my_model->selectRecords($datanew))

			{

				$msg="Your account is already credited for viewing this banner.";

			}else

			{

				$addcounter['member_id']=$this->userID;

				$addcounter['banner_id']=$prerecord[0]->id;

				$addcounter['ip_address']=$this->input->ip_address();

				$addcounter['click_time']=DATE_TIME;

				$addcounter['amount']=$credit;

				$this->my_model->insertRecords($addcounter,'ptc_histories');

				//echo $this->db->last_query();

				/*Update the Member Credits */

				

				$this->advertisements->updateMemberAdsCreditptc($this->userID);

				

				$updateptcad['click_amount']=$prerecord[0]->click_amount+$credit;

				$upcondition['where']['id']=$bannerID;

				$this->my_model->updateRecords($updateptcad,'ptcbanners',$upcondition);

				

				$msg="Your account has received following credits : $credit PTC Ad Credits.";

			}

		}

		else

		{

			$msg="Your account is already credited for viewing this banner.";

		}

		/*Inser into the Statemes Credits */

		//$this->advertisements->updateMemberAdsCredit($this->userID,'banner_credit',$credit);						

		echo $msg;

	}



/**

	 * Index Page for this controller.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 18-05-2016

 	 * @Updated Date :  19-05-2016

	 */

	public function like($id)

	{

		$vdata['table']=$this->mainTable;

		$vdata['where']['id']=$id;

		$maindata=$this->my_model->selectRecords($vdata);

		$likemembers=(!empty($maindata[0]->like_members))?explode(',',$maindata[0]->like_members):array();

		if(!in_array($this->userID,$likemembers))

		{

			$likemembers[]=$this->userID;

			$updatedata1['like_counter']=count($likemembers);

			$updatedata['like_members']=implode(',',$likemembers);

			

			$condition['where']['id']=$id;

			$this->my_model->updateRecords($updatedata1,$this->mainTable,$condition);

		}

		$data['table'] = $this->mainTable. ' as dm';	

		$data['fields'] = array('dm.id as id','dm.ptc_id as planid','dm.banner_title as title','dm.banner_url as banner','dm.target_url as target_url','dm.display_counter as display','dm.banner_description as description','dm.click_counter as click','mem.member_photo as photo','dm.like_counter as like','dm.ptctheme as themeid','mem.user_name as username','dm.member_id as member_id','dm.today_click as today_click','dm.daily_budget as dailybudget','dm.click_amount as click_amount');

		$data['orderby']="rand()";

		$data['where']['dm.status']="1";

		$data['where']['dm.pause']="1";

		$data['where']['dm.id']=$id;

		$data['joins'] = array($this->memberTable.' as mem' => array('mem.member_id = dm.member_id','LEFT'));

		$value=$this->my_model->selectRecords($data);

		$ptcthemedata['table']=$this->themeTable;

		$ptcthemedata['where']['id']=$value[0]->themeid;

		$theme=$this->my_model->selectRecords($ptcthemedata);

		

		$viewdata['value']=$value;

		$viewdata['theme']=$theme[0];

		if (!$this->input->is_ajax_request()) {

			$this->load->view('view_ptc_view',$viewdata);

			//$this->load->view('view_login_ads',$viewdata);

		}

		else {

			//$this->load->view('ajaxtemplates/view_login_ads_ajax',$viewdata);

			$this->load->view('ajaxtemplates/view_ptc_view_ajax',$viewdata);

		}

	}

	

	

	public function history()

	{

		$dataM['table']=$this->historyTable;

		$dataM['where']['member_id']=$this->userID;

		if($this->input->post())

		{

				$post=$this->input->post();

				if(isset($post['fromdate'])&&($post['fromdate']!=''))

				{

					$dataM['where']['click_time >=']=date('Y-m-d H:i:s',strtotime($post['fromdate']));

				}

				if(isset($post['todate'])&&($post['todate']!=''))

				{

					$dataM['where']['click_time <=']=date('Y-m-d H:i:s',strtotime($post['todate'])+(86399));

				}

			}
			$dataM['orderby']='click_time desc';
			$viewdata['history']=$this->my_model->selectRecords($dataM);

			//echo $this->db->last_query();

			if($this->input->post())

			{

				$msg = "Search Sucessfully";

				$jsonResult['flag'] = 'success';

				$jsonResult['msg'] = $msg;

				$jsonResult['view']=$this->load->view('ajaxtemplates/view_ptchistory_search',$viewdata,true);	

				exit(json_encode($jsonResult));

			}

			else

			{

					$this->load->view('ajaxtemplates/view_ptchistory_ajax',$viewdata);

			}

	}

	

	/**

	* getAutoCountry method is used to get the countries from the name 

	* @Developer : Paras Dalsaniya

	* @Params : -

	* @retunr : load view

	* @Created Date : 23-06-2016

 	* @Updated Date : 

	*/

		public function delete($id)

	{

		$dataM['table']=$this->mainTable;

		$dataM['where']['id']=$id;

		$this->my_model->deleteRecords($this->mainTable,$dataM);

		

		$data['table'] = $this->secondaryTable;			 	

		$viewdata['planadds']=$this->my_model->selectRecords($data);

		$query="SELECT plan_name,l.id,l.status,l.banner_size,display_counter,click_counter,banner_url,ppc_id FROM `ppcbanners`as l, ppcplans as p where p.status=1 and ppc_id=p.id and member_id=".$this->userID;

		$viewdata['history']=$this->db->query($query)->result();;

		if (!$this->input->is_ajax_request()) {

			$this->load->view('view_ptc_ads',$viewdata);

			//$this->load->view('view_login_ads',$viewdata);

		}

		else {

			//$this->load->view('ajaxtemplates/view_login_ads_ajax',$viewdata);

			$this->load->view('ajaxtemplates/view_ptc_ads_ajax',$viewdata);

		}

		

	}

	

	/**

	* getAutoState method is used to get the state from the name 

	* @Developer : Paras Dalsaniya

	* @Params : -

	* @retunr : load view

	* @Created Date : 23-06-2016

 	* @Updated Date : 

	*/

	public function status($value,$id,$type)

	{

		if($type==1)

		{

			$data['pause']=($value==1)?0:1;

			$filename="PTC Ad-> ".($value==1)?"paused":"unpaused"."(id:$id)";

		}

		if($type==2)

		{

			$data['status']=($value==1)?0:1;

			$filename="PTC Ad-> ".($value==1)?"Inactive":"Active"."(id:$id)";

		}

		$condition['where']['id']=$id;

		$this->my_model->updateRecords($data,'ptcbanners',$condition);

		$msg = "Updated successfully";

		$this->logs->insertMemberLogs($this->userID,$filename);

		

		$jsonResult['flag'] = 'success';

		$jsonResult['msg'] = $msg;	

		$jsonResult['msg'] = $msg;	

		$this->index($jsonResult);

		//exit(json_encode($jsonResult));

		if (!$this->input->is_ajax_request())

		{

			 redirect(SITEURL.$this->controllerName);

		}

	}

	

	

		

	/**

	* getAutoProvince method is used to get the state from the name 

	* @Developer : Paras Dalsaniya

	* @Params : -

	* @retunr : load view

	* @Created Date : 01-07-2016

 	* @Updated Date : 

	*/

	public function getAutoProvince(){}

	/**

	* getAutoCity method is used to get the city from the name 

	* @Developer : Paras Dalsaniya

	* @Params : -

	* @retunr : load view

	* @Created Date : 23-06-2016

 	* @Updated Date : 

	*/

	public function getAutoCity(){}







	

	/**

	 * Edit method is used to view the form for insert the custoemr details.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 19-05-2016

 	 * @Updated Date : 09-07-2016

	 */

	public function edit($id)

	{}

	

	/**

	 * changeStatus method is used to update the company status

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 25-06-2016

 	 * @Updated Date : 

	 */

	public function changeStatus(){}

	

	

	/**

	 * changePublicDate method is used to update the company public date change

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 25-06-2016

 	 * @Updated Date : 

	 */

	public function changePublicDate(){}



/**

	 * changeStatus method is used to update the company status

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 25-06-2016

 	 * @Updated Date : 

	 */

	public function savePrivacyDetails(){}

	/**

	 * Add method is used to view the form for insert the user details.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 14-06-2016

 	 * @Updated Date : 

	 */

	public function code_exists($id = null)

	{}

	

	/**

	 * Add method is used to view the form for insert the user details.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 14-06-2016

 	 * @Updated Date : 

	 */

	public function code_exists_formvalidation()

	{}

	

	/**

	 * save method is used to view the form for insert the custoemr details.

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 19-05-2016

 	 * @Updated Date : 22-06-2016

	 */

	

	

	public function checkCodeExists($codeType = 'edi', $id = null)

	{}

	

	/**

	 * Add method is used to check the all the code like EDI, EORI...

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 30-06-2016

 	 * @Updated Date : 

	 */

	public function checkCodeExists_formvalidation()

	{}





/*==================================================================== START BANK Section ==================================================================================== */

	/**

	 * bankforms method is used to Open Popup of add/edit and delte the seletecd banks

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 22-06-2016

 	 * @Updated Date :  

	 */

	public function bankforms(){}

	

	

	

	/**

	 * savebankdetails method is used to savebank details

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 04-06-2016

 	 * @Updated Date : 

	 */

	public function savebankdetails(){}

	

	

	

	/**

	 * getbanks method is used to savebank details

	 * @Developer : Paras Dalsaniya

	 * @Params : -

	 * @retunr : load view

	 * @Created Date : 22-06-2016

 	 * @Updated Date : 

	 */

	

	

	/**

	* uploadDiles method is used to upload the doc/pdf for the company.

	* @Developer : Paras Dalsaniya

	* @Params : -

	* @retunr : true/false

	* @Created Date : 25-06-2016

 	* @Updated Date : 

	*/

	public function pdf($id)

	{

		$invoicedata['table']='invoice_details';

		$invoicedata['where']['type']='ptc';

		$invoicedata['where']['member_id']=$this->userID;

		$invoicedata['where']['item_id']=$id;

		$invoicerecord=$this->my_model->selectRecords($invoicedata);

		if(count($invoicerecord)==0)

		{

			$dataPPC['table'] = 'ptcbanners l';

		$dataPPC['fields'] =  "l.*,la.plan_name";

		$dataPPC['joins'] = array('ptcplans as la' => array("l.ptc_id = la.id",'LEFT'));		

		$dataPPC['where'] = array('l.id'=>$id);

		$memberppcadadds = $this->my_model->selectRecords($dataPPC);

		if(count($memberppcadadds) > 0){

			/* Get the Email tempate */

			

			

			foreach($memberppcadadds as $keyP => $valP){

				$dataM['table'] = 'members as m';

				$dataM['where'] = array('member_id'=>$valP->member_id);

				$memberDetails = $this->my_model->selectRecords($dataM);

					

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

					$arrInvoiceDetails[0]['name'] = $valP->banner_title;

					$arrInvoiceDetails[0]['desc'] = $valP->banner_description;

					$arrInvoiceDetails[0]['link'] = 	"#";

					$arrInvoiceDetails[0]['sub_total'] = "$".$valP->paid_amount;

					$arrInvoiceDetails[0]['discount'] = "$".$valP->discount;

					$subTotal = ($valP->paid_amount + $valP->discount);

					//$arrInvoiceDetails[0]['fees'] = "$".$valP->discount;					

					$arrInvoiceDetails[0]['price'] = "$".$subTotal;

					

					$arrOtherDetails['totalPrice'] = $valP->paid_amount;

					

					$arrOtherDetails['invoiceNo'] = $invoiceNo; 

					$arrOtherDetails['fileName'] = 'advertisement_ptc_'.$invoiceNo; 

					$arrOtherDetails['PDFType'] = 'F'; 

					$arrOtherDetails['invoiceDate'] = $valP->purchasedate;

					

					if($this->utility->generatePDFInvoice($arrInvoiceDetails,$arrOtherDetails)){

						$emailData['attachment'] = SITEURL.'external/pdf_docs/'.$arrOtherDetails['fileName'].'.pdf';

						$inserInvRecords['type'] = 'ptc';

						$inserInvRecords['item_id'] = $valP->id;

						$inserInvRecords['invoice_no'] = $invoiceNo;

						$inserInvRecords['member_id'] = $memberDetails[0]->member_id;

						$inserInvRecords['price'] = $valP->paid_amount;

						$inserInvRecords['discount'] = $valP->discount;						

						$this->utility->insertInvoice($inserInvRecords);

					}

					

					

				

			}

		}	

		}

		$invoicedata['table']='invoice_details';

		$invoicedata['where']['type']='ptc';

		$invoicedata['where']['member_id']=$this->userID;

		$invoicedata['where']['item_id']=$id;

		$invoicerecord=$this->my_model->selectRecords($invoicedata);

		//echo $this->db->last_query();

		$filename="advertisement_ptc_0000".$invoicerecord[0]->invoice_no;

		$attachment = SITEURL.'external/pdf_docs/'.$filename.'.pdf';

		redirect($attachment);

	}

	

	public function applycoupon($value=null,$posted=null)

	{

		

			if($value==null && $this->input->post())

			{

				$posted=$this->input->post();

			}

				$couponvalue=$this->wallet->coupondetail($posted['couponcode']);

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

						if($couponvalue->coupontype <= 3)

						{

							if($posted['amount']>= $couponvalue->minimumamount)

							{

								if($couponvalue->amounttype==0)

								{

									$discount=$couponvalue->amount;

								}

								else

								{

									$discount=($posted['amount']*$couponvalue->amount)/100;

								}

								if($couponvalue->maximumdiscount!='0' && $discount> $couponvalue->maximumdiscount)

								{

									$discount= $couponvalue->maximumdiscount;

								}

									

									$insertdata['member_id']=$this->userID;

									$insertdata['coupon_id']=$couponvalue->id;

									$insertdata['code']=$couponvalue->code;

									$insertdata['discount']=$discount;

									$insertdata['plantype']="PTC";

									$insertdata['planprice']=$posted['amount'];

									$insertdata['plan_id']=$posted['planid'];

									$insertdata['ipaddr']=$this->input->ip_address();

									$insertdata['dt']=DATE_TIME;

									if($value!=null)

									{

										$this->my_model->insertRecords($insertdata,'couponhistories');

										$this->wallet->couponupdate($couponvalue->id,$couponvalue->code);

									}

									$msg="Successfully Appiled";

									$jsonResult['discount']=$discount;

									$jsonResult['flag'] = 'success';

								

							}

							else

							{

								$msg="Purchase amount should be greater than ".$this->settings->covertCurrencyValue($couponvalue->minimumamount);

								$jsonResult['flag'] = 'fail';

							}

						}else

						{

							$msg="Invalid Coupon type";

							$jsonResult['flag'] = 'fail';

						}

					}

				}

					$filename="Coupon  -> Appiled ($msg)";

					if($value!=null)

					{

						$this->logs->insertMemberLogs($this->userID,$filename);

					}

					$jsonResult['msg'] = $msg;	

				

					if($value==null)

					{

						

						exit(json_encode($jsonResult));	

					}

					else

					{

						

						return $jsonResult;

					}

	}

	

}


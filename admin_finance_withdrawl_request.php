<?php $post=$this->input->post();?>

<!-- widget grid -->
<section id="widget-grid" class="">

	<!-- row -->
	
                                <div class="fromboxmain">
                                    <span>Search For :</span>
                                    <span id="searchforall" <?php echo (isset($post['searchby'])&& (($post['searchby']=="processorid")))?"style='display:none'":'';?>><div class="input text"><input name="searchfor" value="<?php echo (isset($post['searchfor']))?$post['searchfor']:'';?>" class="searchfor" type="text" id="WithdrawSearchfor"></div></span>
                                    <span id="searchforcombo" <?php echo (isset($post['searchby'])&& (($post['searchby']=="processorid")))?"style='display:inline-block'":"style='display:none'";?>><div class="searchoptionselect"><div class="select-main"><label>
                                    <select class="" style="" onchange="" name="processorid">
                                    <option value="11"  <?php echo (isset($post['searchby'])&& $post['searchby']=='11')?'selected':'';?>>2pay4you</option>
                                    <option value="1"  <?php echo (isset($post['searchby'])&& $post['searchby']=='1')?'selected':'';?>>Bank Wire</option>
                                   <option value="8"  <?php echo (isset($post['searchby'])&& $post['searchby']=='8')?'selected':'';?>>Payeer</option>
                                   <option value="2"  <?php echo (isset($post['searchby'])&& $post['searchby']=='2')?'selected':'';?>>Payza</option></select></label></div></div></span>
                                </div>
                             </div>
                             <div class="from-box">
		<div class="fromboxmain width480">
			<span>From :</span>
			<span><div class="input text"><input name="fromdate" value="<?php echo (isset($post['fromdate']))?$post['fromdate']:'';?>" class="datepicker " type="text" id="BanneradFromdate"><img class="ui-datepicker-trigger" src="http://demo.proxcore.com/theme/Admin-Theme-1/img/calendar.jpg" alt="..." title="..."></div></span>
		</div>
		 <div class="fromboxmain">
			<span>To :</span>
			<span><div class="input text"><input name="todate" value="<?php echo (isset($post['todate']))?$post['todate']:'';?>" class="datepicker " type="text" id="BanneradTodate"><img class="ui-datepicker-trigger" src="http://demo.proxcore.com/theme/Admin-Theme-1/img/calendar.jpg" alt="..." title="..."></div></span>
			<span class="padding-left">
				<div class="submit"><input class="searchbtn" id="submit-1569828281" type="submit" onClick="submitSearchForms('WithdrawAdminpanelWithdrawrequestForm','finance/withdrawrequest','finance/withdrawrequest')" value=""></div>			</span>
		 </div>
	</div>
                             </form></div>
					<!-- widget content -->                    
					<div class="widget-body">
                    	 <div class="actionmenu floatright upperaction">
                                <div class="btn-group">
                                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                      Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu"><?php 
									if(!isset($this->isPayAllDenied) || $this->isPayAllDenied == false){ 
									?><li><a href="javascript:submitSearchForms('frmMultipleDelete','<?php echo $controllerName.'/'.$functionName;?>','<?php echo $controllerName.'/'.$functionName;?>')" onclick="$('input:checkbox').prop('checked',true);$('#actionType').val('paytoselected');return confirm('Are You Sure You Want to Pay All Request');"><img src="<?php echo SITEURL.'external/'?>img/money.png" alt="Edit Revenue Plan"> Pay to all </a></li>
                                       <?php }
									 if(!isset($this->isPaySelectedDenied) || $this->isPaySelectedDenied == false){ ?>
                                       <li><a href="javascript:submitSearchForms('frmMultipleDelete','<?php echo $controllerName.'/'.$functionName;?>','<?php echo $controllerName.'/'.$functionName;?>')" onclick="$('#actionType').val('paytoselected');return confirm('Are You Sure You Want to Pay Towards Selected Request');"><img src="<?php echo SITEURL.'external/'?>img/money.png" alt="Edit Revenue Plan"> Pay to selected list </a></li>
                                         <?php }
										 if(!isset($this->isMarkDenied) || $this->isMarkDenied == false){ ?>
                                       <li><a href="javascript:submitForms('frmMultipleDelete','<?php echo $controllerName.'/'.$functionName;?>','<?php echo $controllerName.'/'.$functionName;?>')" onclick="$('#actionType').val('marktoselected');"><img src="<?php echo SITEURL.'external/'?>img/check.png" alt="Mark selected as paid"> Mark selected as paid </a></li>
                                         <?php }
										 ?><?php if(!isset($this->isCancelDenied) || $this->isCancelDenied == false){ ?> <li>
 <a href="javascript:submitForms('frmMultipleDelete','<?php echo $controllerName.'/'.$functionName;?>','<?php echo $controllerName.'/'.$functionName;?>')" onclick="$('#actionType').val('delete');return confirm('Are You Sure Cancel selected request(s)?');">
 <img src="<?php echo SITEURL?>external/img/delete.png" alt="Cancel selected request(s)">Cancel selected request(s)</a></li>
                                        <?php }?>
                                        <?php if(!isset($this->isCSVDenied) || $this->isCSVDenied == false){ 
										/*?>
                     
                             				<a href="javascript:download('WithdrawAdminpanelWithdrawrequestForm','<?php echo SITEURLADMIN.$controllerName.'/exportwithdrawrequest';?>','pendingdeposits');"><i class="fa fa-download" aria-hidden="true"></i>  Download CSV </a>
	                        			</li>
										<?php }
										/*
                                        if(!isset($this->isCSVDenied) || $this->isCSVDenied == false){ ?>
                                            <li class="d-pdf">
                                            <a href="#"> Download PDF </a>            
                                            <embed id="ZeroClipboard_TableToolsMovie_2" src="../external/js/plugin/datatables/swf/copy_csv_xls_pdf.swf" loop="false" menu="false" quality="best" bgcolor="#ffffff" name="ZeroClipboard_TableToolsMovie_2" allowscriptaccess="always" allowfullscreen="true" type="application/x-shockwave-flash" flashvars="id=2&amp;width=39&amp;height=28" wmode="transparent" allownetworking="all"  align="middle" height="28" width="39"></embed></li><?php
                                        }*/?>
                                                                    
                                  </ul>
                                </div>
                                <a href="javascript:loadContents('finance/withdrawrequest','content');" class="btn btn-primary">+ Pending For IPN</a>
                                <div class="checkall"><input type="checkbox" id="chkAll" class=""/></div>
                            </div>
                         
                      	<form action="<?php echo 'finance/withdrawActions'?>" method="post" class="smart-form" id="frmMultipleDelete" name="frmMultipleDelete">
                        <input type="hidden" name="actionType" id="actionType" value="delete" />
 						<div class="dataTables_wrapper form-inline dt-bootstrap no-footer" id="datatable_fixed_column_wrapper">
						
							<table class="table table-striped table-bordered dataTable no-footer" id="datatable_fixed_column123">
								<thead>
									<tr>
                                        <th><a href="javascript:loadContents('<?php echo $controllerName.'/'.$functionName.'/'.$page.'/'.$perpage.'/with_id/'.$order.'/1';?>','content')" class="vtip" title="Sort By Id">Id</a></th>
                                        <th><a href="javascript:loadContents('<?php echo $controllerName.'/'.$functionName.'/'.$page.'/'.$perpage.'/req_dt/'.$order.'/1';?>','content')" class="vtip" title="Sort By Request Date">Req. Dt.</a></th>
										<th><a href="javascript:loadContents('<?php echo $controllerName.'/'.$functionName.'/'.$page.'/'.$perpage.'/member_id/'.$order.'/1';?>','content')" class="vtip" title="Sort By Member ID">Member ID</a></th>
										<th><a href="javascript:loadContents('<?php echo $controllerName.'/'.$functionName.'/'.$page.'/'.$perpage.'/payment_processor/'.$order.'/1';?>','content')" class="vtip" title="Sort By Payment Processor">Payment Processor</a></th>
                                        <th><a href="javascript:loadContents('<?php echo $controllerName.'/'.$functionName.'/'.$page.'/'.$perpage.'/pro_acc_id/'.$order.'/1';?>','content')" class="vtip" title="Sort By Payment Processor Id">Payment Processor Id</a></th>
                                        <th><a href="javascript:loadContents('<?php echo $controllerName.'/'.$functionName.'/'.$page.'/'.$perpage.'/withdrawbalance/'.$order.'/1';?>','content')" class="vtip" title="Sort By Balance">Balance</a></th>
                                        <th><a href="javascript:loadContents('<?php echo $controllerName.'/'.$functionName.'/'.$page.'/'.$perpage.'/amount/'.$order.'/1';?>','content')" class="vtip" title="Sort By Amount">Amount</a></th>
                                        <th><a href="javascript:loadContents('<?php echo $controllerName.'/'.$functionName.'/'.$page.'/'.$perpage.'/fee/'.$order.'/1';?>','content')" class="vtip" title="Sort By Fees">Fees</a></th>
                                        <th>Action</th>
                                        <th></th>
                                        
									</tr>
								</thead>
								<tbody><?php
									foreach($withdrawrequest as $key => $val){
									?><tr>
										<td><?php echo $val->with_id;?></td>
                                        <td><?php echo $this->utility->dateFormate($val->req_dt);?></td>
                                        <td><a href="javascript:loadContents('member/add/<?php echo $val->member_id;?>','content');" class="vtip" title="View Member"><?php echo $this->utility->getMembersField($val->member_id,'user_name');?>(<?php echo $val->member_id;?>)</a>
										</td>
                                         <td><a rel="lightboxtext" class="vtip" title="View Comment" onclick="ViewDescription('ViewDescription-<?php echo $val->with_id;?>')"><?php echo $val->payment_processor;?><img src="<?php echo SITEURL.'external/'?>img/men-icon.png" alt="Notes" align="absmiddle"></a>
                                         <div id="ViewDescription-<?php echo $val->with_id;?>" class="promoemailpopup mass-mail-popup" style="display:none;">
                                            <div class="i-frame-popup">
                                                <div id="cboxClose" class="cboxClose" onclick="ViewDescription('ViewDescription-<?php echo $val->with_id;?>')">X</div>
                                                <div> <?php echo $val->comment;?></div>
                                            </div>
                                        </div>                                        
                                        </td>
                                         <td><?php echo $val->pro_acc_id;?></td>
                                       
                                        <td><?php echo $val->withdrawbalance;?></td>
                                         <td><?php echo '$'.$val->amount;?></td>
                                          <td><?php echo '$'.$val->fee;?></td>
                                          <td>
                                        <div class="actionmenu">
                                            <div class="btn-group">
                                              <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="true"> Action <span class="caret"></span> </button>
                                              <ul class="dropdown-menu" role="menu">
                                                 <?php if(!isset($this->isPayAllDenied) || $this->isPayAllDenied == false){ ?>
                                                <li> <a href="javascript:loadContents('finance/withdrawthrough/<?php echo $val->with_id;?>','content');" onclick="return confirm('Are You Sure You Want to Pay Towards Selected Request');"><img src="<?php echo SITEURL.'external/'?>img/money.png" alt="Edit Revenue Plan"> Pay</a> </li>
                                                  <?php }?><?php if(!isset($this->isPaySelectedDenied) || $this->isPaySelectedDenied == false){ ?>
                                                <li> <a href="javascript:loadContents('<?php echo 'finance/withdraw_manually/'.$val->with_id;?>','content');" class="vtip" title="Click Here if You Want to Pay This Member Manually And Not Through This System. This Will be Marked as Paid Manually in Withdraw History And No Payment Will Be Done From The System For This Request."><img src="<?php echo SITEURL.'external/'?>img/check.png" alt="Inactivate Plan"> Mark as Paid</a> </li>
                                                  <?php }?><?php if(!isset($this->isCancelDenied) || $this->isCancelDenied == false){ ?>
                                                <li> <a href="javascript:loadContents('<?php echo 'finance/withdrawrequestDelete/'.$val->with_id;?>','content');"><img src="<?php echo SITEURL.'external/'?>img/delete.png" alt="Delete Revenue Plan"> Delete </a> </li>
                                                <?php }?>
                                              </ul>
                                            </div>
                                          </div><?php /*<a href="javascript:loadContents('matrix/viewPlan/<?php echo $val->id;?>','content');" class="btn btn-primary">View Plan Members</a>*/?></td>
                                        <td><input type="checkbox" name="selectedRecords[]" value="<?php echo $val->with_id;?>"/></td>
										
									</tr><?php 
									}
									?>
								</tbody>
							</table>
                            <?php $this->load->view('adminpanel/view_pagination');?>																				
						</div>
                        </form>
					</div>
					<!-- end widget content -->
                    <?php }?>

				</div>
				<!-- end widget div -->

			</div>
			<!-- end widget -->
			
		</article>
		<!-- WIDGET END -->	
		

	</div>

	<!-- end row -->
</section>
<!-- end widget grid -->
<script type="text/javascript">
vtip();
$("#chkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
<script type="text/javascript">
function ViewDescription(element){
		
		var el = document.getElementById(element);
		var bg = document.getElementById("please_wait_bg");
		//if( el && el.style.display == 'block'){    
		var isHidden = $("#"+element).is( ":hidden" );
		if(!isHidden){
			//el.style.display = 'none';
			$("#"+element).hide(500);
		}
		else {
			$("#"+element).show(500);
			//el.style.display = 'block';
		}
			
			
		/*if( bg && bg.style.display == 'block'){    
			bg.style.display = 'none';
			$(window).unbind('scroll');
			  
		}
		else {
			bg.style.display = 'block';
			$(window).scroll(function() { return false; });
		}*/	
	}
	datatablefunction('1','desc','datatable_fixed_column','2','true');
	$(".dt-toolbar-footer").remove();

	
	pageSetUp();
	


	var pagefunction = function() {
	//	datatablefunction();
	};
	
	pageSetUp();
	
	
</script>

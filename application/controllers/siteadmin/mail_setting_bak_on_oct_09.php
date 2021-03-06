<?php
class Mail_setting extends CI_Controller
{
	var $time_zone;
	
	function Mail_setting()
	{
		parent::__construct();
		
		$this->load->database();
		
		$this->load->library('config_data');
		
		$this->config_data->db_config_fetch();
		
		
		$language_code = $this->config->item('language_code');
		
		$this->lang->load('admin/common',$language_code);
	    
		$this->lang->load('admin/validation',$language_code);
		
		
		//Load Models Common to all the functions in this controller
		$this->load->model('common_model');
		$this->load->helper('url');
		//$this->load->model('skills_model');
		$this->load->helper('auth_helper');
		$this->load->helper('date_helper');
		$this->load->helper('date_helper');
		$this->load->library('form_validation');
		$this->load->model('settings_model');
		$this->load->library('session');
		$this->time_zone = $this->config->item("site_time_zone");
		//load model
		$this->load->helper('common_helper');
		
		//Check For Admin Logged in
		if(!isAdmin())
			redirect_admin('logout');
           //Get Config Details From Db
	}
	
	function index()
	{
		if($this->input->post("mail_setting_submit")=="Save")
		{
			extract($this->input->post());
			
			$update_array=array("mailer"=>$mailer,"sendmail_path"=>$send_mail_path,"smtp_server"=>$smtp_server,"smtp_port"=>$smtp_port,"smtp_prefix"=>$smtp_prefix,"smtp_auth"=>$smtp_auth,"smtp_username"=>$smtp_user_name,"smtp_password"=>$smtp_password);
			
			$result=$this->common_model->updateTableData("mail_settings",1,$update_array);
			
			if($result)
				$this->session->set_flashdata('flash_message', $this->common_model->admin_flash_message('success','Record Updated Successfully'));
			else
				$this->session->set_flashdata('flash_message', $this->common_model->admin_flash_message('error','Record Not Updated Successfully'));	
				
			redirect_admin('mail_setting/');
			
		}
		$mail_settings_query=$this->common_model->getTableData("mail_settings",array("id"=>1));
		
		$mail_settings_values=$mail_settings_query->result();
		
		$data["mail_settings_values"]=$mail_settings_values;
		
		$data['title']="Mail Settings";
		
		$data['open_tab']="setting_box";
		
		$data['cancel_url']=admin_url("home");
		
		$this->load->view("admin/mail_setting/index",$data);
	}
	function daily_deal()
	{
		$facebook_url=get_single_row("social_site_settings",array("name"=>"facebook_url"));
		
		$face_book_link='';
		if(count($facebook_url)!=0)
			$face_book_link=$facebook_url[0]->value;
		else
			$face_book_link='#';
			
		$twiter_url=get_single_row("social_site_settings",array("name"=>"twiter_page_url"));
		
		$twitter_link='';
		
		if(count($twiter_url)!=0)
			$twitter_link=$twiter_url[0]->value;
		else
			$twitter_link='#';
		
		$city_lists=$this->common_model->getTableData("city",array("status"=>1))->result();
		
		$body_message='';
		
		$cur_date=get_est_time($this->time_zone);
		
		$jcount=0;
		
		$count=0;
			
		$mail_subject=array();
			
		$city_inner_message=array();
		
		$i=1;
				
		$j=1;
			
		foreach($city_lists as $city_list)
		{
			$array = array('deal_end_date >= ' => $cur_date, 'deal_city' => $city_list->id,"status"=>'1',"quantity !="=>0);
			
			$this->db->where($array);
			
			$deals_query=$this->db->get('deals');
			
			//echo $this->db->last_query();
			
			$total_deals=$deals_query->result();
			
			$count=count($total_deals);
			
			$jcount=$count-1;
			
			if(count($total_deals)!=0)
			{
				$mail_subject[$city_list->id]=$city_list->name." Daily Deals From ".$this->config->item('site_title');
				
				$subscribe_users=$this->common_model->getTableData("subscribe",array("status"=>1,"city_id"=>$city_list->id))->result();	
				
				foreach($total_deals as $total_deal)
				{
					if($i==1)
					{
						$body_message.='<tr>
								<td colspan="2" style="padding:10px 25px 25px 25px;">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr>
											<td colspan="5" align="center" style="padding:25px 0 0;"><img src="'.base_url().'uploads/deals/'.$total_deal->deal_image_url.'" alt="image" border="0" width="736" height="213" style="width:736px;height:213px"/></td>
										</tr>
										<tr align="center" height="71px">
											<td style="background:#7192de url('.base_url().'email_images/off_border.png) no-repeat right center; border-top:1px solid #c6d3f2; width:70px;">
												<p style="margin:0 0 3px 0; color:#fff;">Value</p>
												<p style="margin:0px; font-weight:bold; font-size:17px; position:relative; color:#c3d5ff;">$ '.$total_deal->deal_price.'<span style="position:absolute; bottom:9px; left:12px;"><img src="'.base_url().'images/info_exp_cross.png" alt="cross" border="0" /></span></p>
											</td>
											<td style="background:#7192de url('.base_url().'email_images/off_border.png) no-repeat right center; border-top:1px solid #c6d3f2; width:61px;">
												<p style="margin:0 0 3px 0; color:#fff;">Disc</p>
												<p style="margin:0px; font-weight:bold; font-size:17px; color:#fff;">'.$total_deal->deal_save_percent.'%</p>
											</td>
											<td style="background:#7192de url('.base_url().'email_images/off_border.png) no-repeat right center; border-top:1px solid #c6d3f2; width:67px;">
												<p style="margin:0 0 3px 0; color:#fff;">Save</p>
												<p style="margin:0px; font-weight:bold; font-size:17px; color:#fff;">$ '.($total_deal->deal_face_value-$total_deal->deal_price).'</p>
											</td>
											<td align="left" style="background:#7192de url('.base_url().'email_images/off_border.png) no-repeat right center; border-top:1px solid #c6d3f2; width:410px; padding:0 0 0  15px;">
												<p style="margin:0px; color:#fff;">'.substr(strip_tags($total_deal->deal_description),0,200).'</p>
											</td>
											<td style="background:#7192de; border-top:1px solid #c6d3f2; width:113px;">
												<p style="margin:0px; color:#fff;"><a href="'.base_url().'deal/'.$total_deal->slug.'" style="background:url('.base_url().'email_images/view_all.png) no-repeat center; color:#2e4e99; display:block; width:92px; height:30px; line-height:30px; text-decoration:none; font-weight:bold;">View Deal</a></p>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<!-- End of Info -->';
							}
							else
							{
								if($j%2!=0)
								{
									$body_message.='<tr>';
								}
								$body_message.='<td style="padding:0 0 0 25px;">
										<table cellpadding="0" cellspacing="8" style="background:#f7f7f7; width:356px;">
											<tr>
												<td>
													<p style="background:#fff; padding:2px; border:1px solid #797979; margin:0px;"><img src="'.base_url().'uploads/deals/'.$total_deal->deal_image_url.'" alt="image" width="355" height="155" border="0" style="width:355px;height:155px"/></p>
													<p style="color:#3b5aa1; font-size:14px; font-weight:bold; margin:5px 0px;">'.substr(strip_tags($total_deal->deal_description),0,200).'</p>
													<p style="margin:10px 0 0; font-size:13px;">
														<label>Value: <span style="color:#d35b12;">$'.$total_deal->deal_face_value.'</span></label>
														<label style="margin:0 0 0 10px;">Our Price: <span style="color:#d35b12;">$'.$total_deal->deal_price.'</span></label>
														<span style="float:right;"><a href="'.base_url().'deal/'.$total_deal->slug.'" style="background:url('.base_url().'email_images/view_all_blue.png) no-repeat center top; height:30px; width:82px; line-height:30px; display:block; font-weight:bold; color:#fff; text-align:center; text-decoration:none;">View Deal</a></span>
													</p>
												</td>
											</tr>
										</table>
									';
								if($j==$jcount-1)
								{
									if($jcount%2!=0)
									{
										$body_message.='<td>&nbsp;</td></tr>';
									}
								}
								if($j%2==0)
									$body_message.='</td></tr>';
									
								$j++;
							}
							
						$i++;
							
						
				}
				$city_inner_message[$city_list->id]=$body_message;
			}
		}
		
		$subscribe_users=$this->common_model->getTableData("subscribe",array("status"=>1))->result();
		
		$header='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>'.$this->config->item('site_title').' Daily Deals</title>
					</head>
					<body>
					<table cellpadding="0" cellspacing="0" style="background:url('.base_url().'email_images/body_bg.jpg) no-repeat center top; width:795px; margin:0 auto; border:4px solid #abc1f5; font-family:Arial, Helvetica, sans-serif; font-size:12px;">
						<tr>
							<td style="padding:3px 0 0 25px;">
								<p style="margin:0px;"><a href="#"><img src="'.base_url().'css/logo/'.$this->config->item('site_logo').'" alt="Logo" border="0" /></a></p>
								<p style="margin:4px 0 0 20px;"><a style="color:#000; text-decoration:none;" href="#">'.$this->config->item('site_slogan').'</a></p>
							</td>
							<td style="padding:0 25px 0 0; text-align:right;">
								<p style="margin:10px 0 0; color:#d35b12; font-weight:bold;">'.date("D,M d, Y",$cur_date).'</p>
								<p style="margin:10px 0 0; float:right; font-weight:bold;"><span style="padding:2px 0px 0 0px; float:left;">Follow us:</span><a style="float:left; margin:0 10px 0;" href="'.$face_book_link.'"><img src="'.base_url().'email_images/face.png" alt="Face Book" border="0" align="middle" /></a><a style="float:left;" href="'.$twitter_link.'"><img src="'.base_url().'email_images/twitter.png" alt="Face Book" border="0" align="middle" /></a></p>
							</td> 
						</tr>
    <!-- Info -->';
		
	
	$footer='<!-- End of Row1-->
			</table>
			</body>
			</html>';

	
		$mail_id="";
		
		$send_mail_count=0;
		
		foreach($subscribe_users as $subscribe_user)
		{
			if(isset($city_inner_message[$subscribe_user->city_id]))
			{
				$mail_id=$subscribe_user->email;
				
				if($mail_id!="")
				{
					$message=$header.$city_inner_message[$subscribe_user->city_id].$footer;
					
					sendMail($this->config->item('site_admin_mail'),$mail_id,$mail_subject[$subscribe_user->city_id],$message);
					
					$send_mail_count++;
				}
			}
		}
		
		if($send_mail_count!=0)
		{
			$this->session->set_flashdata('flash_message', $this->common_model->flash_message('success',$send_mail_count." Daily Deal Mail Send"));
			
			redirect_admin("home");
		}
		
	
	}
}
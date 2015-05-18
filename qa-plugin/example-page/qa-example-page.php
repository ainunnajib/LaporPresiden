<?php
/*
	Question2Answer by Gideon Greenspan and contributors
	http://www.question2answer.org/

	File: qa-plugin/example-page/qa-example-page.php
	Description: Page module class for example page plugin


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

class qa_example_page
{
	private $directory;
	private $urltoroot;


	public function load_module($directory, $urltoroot)
	{
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}


	public function suggest_requests() // for display in admin interface
	{
		return array(
			array(
				'title' => 'Example',
				'request' => 'example-plugin-page',
				'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
			),
		);
	}


	public function match_request($request)
	{
		return $request == 'example-plugin-page' || $request == 'qa-laporgoid-page';
	}


	public function process_request($request)
	{
		if ($request=='qa-laporgoid-page'){
			require_once QA_INCLUDE_DIR.'app/cookies.php';
			require_once QA_INCLUDE_DIR.'app/format.php';
			require_once QA_INCLUDE_DIR.'db/selects.php';
			require_once QA_INCLUDE_DIR.'db/users.php';
			require_once QA_INCLUDE_DIR.'db/post-update.php';
			require_once QA_INCLUDE_DIR.'util/sort.php';
			require_once QA_INCLUDE_DIR.'util/string.php';
			
			$userlevel=qa_get_logged_in_user_field("level");
			if ($userlevel<100){
				$result=array('status'=> "not ok",'errormsg'=>"User tidak memiliki autorisasi.");
				echo json_encode($result);
				return;
			}
			
			$questionid=$_POST['id'];
			$userid=qa_get_logged_in_userid();
			$cookieid=qa_cookie_get();
			$errormmsg='';
			$status='ok';
			
			$arraylapor=array();
			$input=array();
			$testresponse='';
			
			
				list($question, $childposts, $achildposts, $parentquestion, $closepost, $extravalue, $categories, $favorite)=qa_db_select_with_pending(
				qa_db_full_post_selectspec($userid, $questionid),
				qa_db_full_child_posts_selectspec($userid, $questionid),
				qa_db_full_a_child_posts_selectspec($userid, $questionid),
				qa_db_post_parent_q_selectspec($questionid),
				qa_db_post_close_post_selectspec($questionid),
				qa_db_post_meta_selectspec($questionid, 'qa_q_extra'),
				qa_db_category_nav_selectspec($questionid, true, true, true),
				isset($userid) ? qa_db_is_favorite_selectspec($userid, QA_ENTITY_QUESTION, $questionid) : null
				);
				$postuserids=qa_db_posts_get_userids($_POST['id']);
				$verifiedname=qa_verified_name($postuserids,"verified-name");
				if (count($verifiedname)==0){
					$errormmsg='User belum Terverifikasi';
					$status='not ok';
				}else{
					$verifiedname=$verifiedname[0];
					$verifiedname=@$verifiedname['content'];
					$email=qa_db_find_email($postuserids);
					if (count($email)==0){
						$errormmsg='user tidak memiliki email.';
						$status='not ok';
					}else{
						$email=$email[0];
						//$email=@$email['email'];
						$subject=array("email"=> $email,"telpon"=> "","namadepan"=> $verifiedname,"namabelakang"=>" ");
						$content=array("isi"=>$question['content']);
						$input = array("ident"=> "true","token"=> "{E82A621E-FD7F-2DCC-8F72-90ED79B9E407}","attachments"=>[],"content"=>$content,"subject"=>$subject);
						$data_string = json_encode($input);    				
                        
						$c = curl_init('http://104.197.45.237/lapor');
						$auth_string = 'dob1DSh6nlHBMVhjifLi#ffcc0314-871a-4c44-9e90-d2bfae0f80d4';
						curl_setopt($c, CURLOPT_HTTPHEADER, array(                                                                          
							'Content-Type: application/json',                                                                                
							'Content-Length: ' . strlen($data_string),
							'Authorization: ' . $auth_string)
						);

						curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'POST');                                                                     
						curl_setopt($c, CURLOPT_POSTFIELDS, $data_string);                                                                  
						curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

						$testresponse = curl_exec($c);
						$arrayresponse = json_decode($testresponse, true);
						$arraylapor=@$arrayresponse['lapor'];
						$trackingid=@$arraylapor['trackingid'];
						$status=@$arraylapor['status'];
						if (strlen($arraylapor) && strlen($status) && $status=="1"){
							qa_db_insert_trackingid($questionid,$trackingid,$userid);
							$status='ok';
							$errormmsg='';
						}else{
							$errormmsg='Koneksi dengan sistem Lapor tidak berhasil. Mohon dicoba kembali dalam beberapa saat lagi.';
							$status='not ok';
						}
					}
				}
			
			
			$result=array('status'=> $status,'errormsg'=>$errormmsg,'lapor'=>$arraylapor,'respon'=>$testresponse,'input'=>$input);
            echo json_encode($result);
			return;
		}else{
		$qa_content=qa_content_prepare();

		$qa_content['title']=qa_lang_html('example_page/page_title');
		$qa_content['error']='An example error';
		$qa_content['custom']='Some <b>custom html</b>';

		$qa_content['form']=array(
			'tags' => 'method="post" action="'.qa_self_html().'"',

			'style' => 'wide',

			'ok' => qa_post_text('okthen') ? 'You clicked OK then!' : null,

			'title' => 'Form title',

			'fields' => array(
				'request' => array(
					'label' => 'The request',
					'tags' => 'name="request"',
					'value' => qa_html($request),
					'error' => qa_html('Another error'),
				),

			),

			'buttons' => array(
				'ok' => array(
					'tags' => 'name="okthen"',
					'label' => 'OK then',
					'value' => '1',
				),
			),

			'hidden' => array(
				'hiddenfield' => '1',
			),
		);

		$qa_content['custom_2']='<p><br>More <i>custom html</i></p>';

		return $qa_content;
		}
	}
}

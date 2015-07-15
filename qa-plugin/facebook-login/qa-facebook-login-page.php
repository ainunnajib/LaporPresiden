<?php
/*
	Question2Answer by Gideon Greenspan and contributors
	http://www.question2answer.org/

	File: qa-plugin/facebook-login/qa-facebook-login-page.php
	Description: Page which performs Facebook login action


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

class qa_facebook_login_page
{

	private $directory;
	private $urltoroot;

	public function load_module($directory, $urltoroot)
	{
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}

	public function match_request($request)
	{
		//'facebook-login-android'
		return ($request=='facebook-login-debug' ||$request=='facebook-login' || $request=='facebook-login-android');
	}
	
	
	public function getItemValue($user,$key){
		try {
			return @$user[$key];
		}catch(Exception $e){
			return "";
		}
	}
	public function getItemValue2($user,$key1,$key2){
		try {
			return @$user[$key1][$key2];
		}catch(Exception $e){
			return "";
		}
	}
	public function getItemValue3($user,$key1,$key2,$key3){
		try {
			return @$user[$key1][$key2][$key3];
		}catch(Exception $e){
			return "";
		}
	}

	public function process_request($request)
	{
		if ($request=='facebook-login-debug' || $request=='facebook-login') {
			$app_id=qa_opt('facebook_app_id');
			$app_secret=qa_opt('facebook_app_secret');
			$tourl=qa_get('to');
			if (!strlen($tourl))
				$tourl=qa_path_absolute('');

			if (strlen($app_id) && strlen($app_secret)) {
				require_once $this->directory.'facebook.php';

				$facebook = new Facebook(array(
					'appId'  => $app_id,
					'secret' => $app_secret,
					'cookie' => true,
				));

				$fb_userid=$facebook->getUser();
				
				if ($fb_userid) {
					try {
						$user=$facebook->api('/me?fields=email,name,verified,location,website,about,picture');
						if ($request=='facebook-login-debug') {
							echo "email:".$this->getItemValue($user,"email");
							echo "; name:".$this->getItemValue($user,"name");
							echo "; verified:".$this->getItemValue($user,"verified");
							echo "; location:".$this->getItemValue2($user,"location","name");
							echo "; website:".$this->getItemValue($user,"website");
							echo "; bio:".$this->getItemValue($user,"bio");
							echo "; picture:".$this->getItemValue3($user,"picture","data","url");
						}
						if (is_array($user))
							qa_log_in_external_user('facebook', $fb_userid, array(
								'email' => $this->getItemValue($user,"email"),
								'handle' => $this->getItemValue($user,"name"),
								'confirmed' => $this->getItemValue($user,"verified"),
								'name' => $this->getItemValue($user,"name"),
								'location' => $this->getItemValue2($user,"location","name"),
								'website' => $this->getItemValue($user,"website"),
								'about' => $this->getItemValue($user,"bio"),
								'avatar' => strlen($this->getItemValue3($user,"picture","data","url")) ? qa_retrieve_url($user['picture']['data']['url']) : null,
							));

					} catch (FacebookApiException $e) {
					  echo $e->getMessage();
					}
				} else {
					if ($request=='facebook-login') {
					   qa_redirect_raw($facebook->getLoginUrl(array('redirect_uri' => $tourl)));
					}
				}
			}
            if ($request=='facebook-login') {
			   qa_redirect_raw($tourl);
			}
		/*facebook-login-android*/
		}else if ($request=='facebook-login-android'){
		    try {
               		qa_log_in_external_user('facebook', $_REQUEST['user_id'], array(
								'email' => $_REQUEST['email'],
								'handle' => $_REQUEST['name'],
								'confirmed' => $_REQUEST['verified'],
								'name' => $_REQUEST['name'],
								'location' =>$_REQUEST['location'],
								'website' => $_REQUEST['website'],
								'about' => $_REQUEST['about'],
								'avatar' => strlen($_REQUEST['avatar']) ? qa_retrieve_url($_REQUEST['avatar']) : null,	
								));				
			 } catch (Exception $e) {}
			qa_redirect_raw("/");
		}
		/*facebook-login-android end here*/
	}
}

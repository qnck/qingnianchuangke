<?php

use \Illuminate\Support\Collection;

class TxtMessage extends Eloquent{
	// public $primaryKey = 't_id';

	private $ch = '';
	private $api = '';
	private $sendUrl = 'http://222.76.210.200:9898/sms.aspx';
	private $apiKey = '028qnck';
	private $apiPass = '789654';
	private $apiId = '436';


	private function initCurl(){
		$this->ch = curl_init();

		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_URL, $this->sendUrl);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		
		$this->apiAuth = ['userid' => $this->apiId, 'account' => $this->apiKey, 'password' => $this->apiPass];
	}

	private function execCurl(){
		$re = curl_exec($this->ch);
		$re = new SimpleXMLElement($re);

		return $re;
	}

	private function setPostData($data){
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
	}

	public function sendMessage($message, $mobile){
		$this->initCurl();
		$data = new Collection();
		$data = $data->merge(['mobile' => $mobile, 'content' => $message, 'action' => 'send'])->merge($this->apiAuth);
		$data = $data->toArray();
		$this->setPostData($data);
		$re = $this->execCurl();
		if($re->returnstatus == 'Faild'){
			throw new Exception($re->message, 1);			
		}else{
			
		}
	}
}
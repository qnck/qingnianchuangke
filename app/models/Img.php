<?php
/**
* 
*/
class Img{

	public $id;
	public $category;
	public $key;
	public $imghost;
	public $imgpath = 'img';
	
	function __construct($cate, $id){
		$this->category = $cate;
		$this->id = $id;
		$this->key = Config::get('app.imghostKey');
        $this->imghost = Config::get('app.imghost');
	}

	public function save($newId){
		$params = ['key' => $this->key, 'hash' => $this->id, 'id' => $newId, 'cate' => $this->category];
		$re = $this->fireGetRequest($params, 'save');
		if(!$re->result){
			throw new Exception("保存图片失败", 2);			
		}else{
			$imgs = [];
			if(is_array($re->data) && !empty($re->data)){
				foreach ($re->data as $key => $value) {
					$imgs[] = $this->imghost.$this->imgpath.$value;
				}
			}
			return $imgs;
		}
	}

	public function getList(){
		$params = ['key' => $this->key, 'cate' => $this->category, 'id' => $this->id];
		$re = $this->fireGetRequest($params, 'get');
		if(!$re->result){
			return false;
		}
		$imgs = [];
		if(is_array($re->data) && !empty($re->data)){
			foreach ($re->data as $key => $value) {
				$imgs[] = $this->imghost.$this->imgpath.$value;
			}
		}
		return $imgs;
	}

	public function fireGetRequest($params, $op){
		$urlParams = '';
		foreach ($params as $key => $value) {
			$urlParams .= '&'.$key.'='.$value;
		}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->imghost.$op.'.php?'.$urlParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        return json_decode($response);
	}




}
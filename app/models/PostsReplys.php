<?php

/**
* 
*/
class PostsReplys extends Eloquent{

	public $primaryKey = 'r_id';
	public $timestamps = false;

	public function baseValidate(){
		$validator = Validator::make(
			['content' => $this->r_content, 'user' => $this->u_id, 'status' => $this->r_status, 'post' => $this->p_id],
			['content' => 'required', 'user' => 'required|digits_between:1,11', 'status' => 'required', 'post' => 'required|digits_between:1,11']
			);
		if($validator->fails()){
			$msg = $validator->messages();
			throw new Exception($msg->first(), 1);			
		}else{
			return true;
		}
	}
	
	/**
	 * laravel relations
	 * @author Kydz 2015-06-17
	 * @return n/a
	 */
	public function post(){
		return $this->belongsTo('Post', 'p_id', 'p_id');
	}

	/**
	 * laravel relations
	 * @author Kydz 2015-06-17
	 * @return n/a
	 */
	public function user(){
		return $this->belongsTo('User', 'u_id', 'u_id');
	}

	/**
	 * add reply
	 * @author Kydz 2015-06-17
	 */
	public function addReply(){
		$this->baseValidate();
		if(!$this->save()){
			throw new Exception("回复添加失败", 1);			
		}else{
			return true;
		}
	}

	/**
	 * disable reply
	 * @author Kydz 2015-06-17
	 * @return n/a
	 */
	public function disable(){
		$this->baseValidate();
		$this->r_status = 1;
		$this->save();
	}

	/**
	 * format data used in list
	 * @author Kydz 2015-06-17
	 * @return array list row
	 */
	public function showInList(){
		$data = [];
		$data = ['content' => $this->r_content, 'reply_time' => $this->created_at->format('Y-m-d H:i:s'), 'user' => $this->user->showInList()];
		return $data;
	}
}
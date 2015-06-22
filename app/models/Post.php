<?php

/**
* 
*/
class Post extends Eloquent{

	public $primaryKey = 'p_id';
	public $timestamps = false;

	public function baseValidate(){
		$validator = Validator::make(
			['title' => $this->p_title, 'user' => $this->u_id, 'status' => $this->p_status, 'site' => $this->s_id],
			['title' => 'required|max:140', 'user' => 'required|digits_between:1,11', 'status' => 'required', 'site' => 'required|digits_between:1,11']
			);
		if($validator->fails()){
			$msg = $validator->messages();
			throw new Exception($msg->first(), 1);
		}else{
			return true;
		}
	}

	/**
	 * add new post
	 * @author Kydz 2015-06-17
	 */
	public function addPost(){
		$this->created_at = date('Y-m-d H:i:s');
		$this->p_status = 1;
		$this->baseValidate();
		if(!$this->save()){
			throw new Exception("添加帖子失败", 1);			
		}else{
			return true;
		}
	}

	/**
	 * disable post
	 * @author Kydz 2015-06-17
	 * @return n/a
	 */
	public function disable(){
		$this->baseValidate();
		$this->p_status = 0;
		$this->save();
	}

	/**
	 * enable post
	 * @author Kydz 2015-06-17
	 * @return n/a
	 */
	public function enable(){
		$this->baseValidate();
		$this->p_status = 1;
		$this->save();
	}

	/**
	 * add priase count
	 * @author Kydz 2015-06-17
	 */
	public function addPraise(){
		$this->baseValidate();
		$this->p_praise += 1;
		$this->save();
	}

	/**
	 * minus priase count
	 * @author Kydz 2015-06-17
	 * @return n/a
	 */
	public function delPraise(){
		$this->baseValidate();
		$this->p_praise -= 1;
		$this->save();
	}

	/**
	 * format data used in list
	 * @author Kydz 2015-06-17
	 * @return array list row
	 */
	public function showInList(){
		$replys = [];
		if(isset($this->replys)){
			foreach ($this->replys as $reply) {
				$replys[] = $reply->showInList();
			}
		}
		$priases = [];
		if(isset($this->praises)){
			foreach ($this->praises as $praise) {
				$priases[] = $praise->showInList();
			}
		}
		$img = new Img('post', $this->p_id);
		$imglist = $img->getList();
		$data = ['id' => $this->p_id, 'title' => $this->p_title, 'content' => $this->p_content, 'post_time' => $this->created_at->format('Y-m-d H:i:s'), 'user' => $this->user->showInList(), 'replys' => $replys, 'priases' => $priases, 'imgs' => $imglist];
		return $data;
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
	 * laravel ralations
	 * @author Kydz 2015-06-17
	 * @return n/a
	 */
	public function replys(){
		return $this->hasMany('PostsReply', 'p_id', 'p_id');
	}


	public function praises(){
		return $this->hasMany('PostsPraise', 'p_id', 'p_id');
	}
}
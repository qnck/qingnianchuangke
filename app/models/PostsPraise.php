<?php

/**
* 
*/
class PostsPraise extends Eloquent{

	public function post(){
		return $this->belongsTo('post');
	}
}
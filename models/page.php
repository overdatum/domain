<?php

class Page extends Eloquent {
	
	public function layout()
	{
		return $this->belongs_to('Layout');
	}

	public function lang()
	{
		//$language_id = Session::get('layla.language');
		return $this->has_one('PageLang');//->where_language_id();
	}

	public function account()
	{
		return $this->belongs_to('Account', 'account_id');
	}

}
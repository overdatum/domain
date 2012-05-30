<?php namespace Domain\Models;

use Domain\Libraries\Model as Eloquent;

class Page extends Eloquent {
	
	public function layout()
	{
		return $this->belongs_to('Domain\\Models\\Layout');
	}

	public function lang()
	{
		//$language_id = Session::get('layla.language');
		return $this->has_one('Domain\\Models\\PageLang');//->where_language_id();
	}

	public function account()
	{
		return $this->belongs_to('Domain\\Models\\Account', 'account_id');
	}

}
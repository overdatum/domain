<?php namespace Domain\Models;

use Domain\Libraries\Model as Eloquent;

class Page extends Eloquent {
	
	public function layout()
	{
		return $this->belongs_to('Domain\\Models\\Layout');
	}

	public function languages()
	{
		return $this->has_many('Domain\\Models\\PageLang');
	}

	public function lang()
	{
		return $this->has_one('Domain\\Models\\PageLang');
	}

	public function account()
	{
		return $this->belongs_to('Domain\\Models\\Account', 'account_id');
	}

}
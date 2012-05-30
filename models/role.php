<?php namespace Domain\Models;

use Domain\Libraries\Model as Eloquent;

class Role extends Eloquent {
	
	public function lang()
	{
		return $this->has_one('Domain\\Models\\RoleLang');
	}

}
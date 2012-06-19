<?php namespace Domain\Models;

use Domain\Libraries\Model as Eloquent;

class Asset extends Eloquent {
	
	public function lang()
	{
		return $this->has_many('Domain\\Models\\AssetLang')->where_language_id(1);
	}

}
<?php

class Role extends Eloquent {
	
	public function lang()
	{
		return $this->has_one('RoleLang');
	}

}
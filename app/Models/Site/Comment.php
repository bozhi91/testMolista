<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

	protected $guarded = [];

	/**
	 * Comment belongs to a user
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user() {
		return $this->belongsTo('App\User');
	}

}

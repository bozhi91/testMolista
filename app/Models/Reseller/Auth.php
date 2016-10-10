<?php namespace App\Models\Reseller;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Auth extends Authenticatable
{
	protected $table = 'resellers';
}

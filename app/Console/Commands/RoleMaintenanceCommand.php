<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RoleMaintenanceCommand extends Command
{
	protected $signature = 'roles:maintenance';

	protected $description = 'Update roles and roles permissions';

	protected $roles = [
		'admin' => [
			'name' => 'admin',
			'display_name' => 'Admin',
			'description' => 'This guy can do everything',
			'level' => 1,
		],
		'company' => [
			'name' => 'company',
			'display_name' => 'Company',
			'description' => 'Real estate company',
			'level' => 100,
		],
		'employee' => [
			'name' => 'employee',
			'display_name' => 'Employee',
			'description' => 'Real estate company employee',
			'level' => 200,
		],
		'translator' => [
			'name' => 'translator',
			'display_name' => 'Translator',
			'description' => 'Translator',
			'level' => 20,
		],
		'franchisee' => [
			'name' => 'franchisee',
			'display_name' => 'Franchisee',
			'description' => 'Franchisee, like 004estate admin',
			'level' => 10,
		],
	];

	protected $permissions = [
		'admin' => [
			'access' => [
				'display_name' => 'Access admin',
				'description' => '',
				'roles' => [ 'translator', 'franchisee' ],
			],
		],
		'site' => [
			'view' => [
				'display_name' => 'Site view',
				'description' => '',
				'roles' => [ 'company', 'franchisee' ],
			],
			'edit' => [
				'display_name' => 'Site edition',
				'description' => '',
				'roles' => [ 'company', 'franchisee' ],
			],
			'create' => [
				'display_name' => 'Site creation',
				'description' => '',
				'roles' => [ ],
			],
			'delete' => [
				'display_name' => 'Site deletion',
				'description' => '',
				'roles' => [ ],
			],
		],
		'pack' => [
			'view' => [
				'display_name' => 'Pack view',
				'description' => '',
				'roles' => [ ],
			],
			'edit' => [
				'display_name' => 'Pack edition',
				'description' => '',
				'roles' => [ ],
			],
			'create' => [
				'display_name' => 'Pack creation',
				'description' => '',
				'roles' => [ ],
			],
			'delete' => [
				'display_name' => 'Pack deletion',
				'description' => '',
				'roles' => [ ],
			],
		],
		'user' => [
			'view' => [
				'display_name' => 'User view',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'edit' => [
				'display_name' => 'User edition',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'create' => [
				'display_name' => 'User creation',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'delete' => [
				'display_name' => 'User deletion',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'login' => [
				'display_name' => 'User login',
				'description' => 'Can login as other users',
				'roles' => [ ],
			],
		],
		'locale' => [
			'view' => [
				'display_name' => 'Locale view',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'edit' => [
				'display_name' => 'Locale edition',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'create' => [
				'display_name' => 'Locale creation',
				'description' => '',
				'roles' => [ ],
			],
			'delete' => [
				'display_name' => 'Locale deletion',
				'description' => '',
				'roles' => [ ],
			],
		],
		'translation' => [
			'view' => [
				'display_name' => 'Translation view',
				'description' => '',
				'roles' => [ 'translator', 'franchisee' ],
			],
			'edit' => [
				'display_name' => 'Translation edition',
				'description' => '',
				'roles' => [ 'translator', 'franchisee' ],
			],
			'create' => [
				'display_name' => 'Translation creation',
				'description' => '',
				'roles' => [ ],
			],
			'delete' => [
				'display_name' => 'Translation deletion',
				'description' => '',
				'roles' => [ ],
			],
		],
		'geography' => [
			'view' => [
				'display_name' => 'Geography elements view',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'edit' => [
				'display_name' => 'Geography elements edition',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'create' => [
				'display_name' => 'Geography elements creation',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'delete' => [
				'display_name' => 'Geography elements deletion',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
		],
		'property' => [
			'view' => [
				'display_name' => 'Property view',
				'description' => '',
				'roles' => [ 'company', 'employee' ],
			],
			'edit' => [
				'display_name' => 'Property edition',
				'description' => '',
				'roles' => [ 'company', 'employee' ],
			],
			'create' => [
				'display_name' => 'Property creation',
				'description' => '',
				'roles' => [ 'company', 'employee' ],
			],
			'delete' => [
				'display_name' => 'Property deletion',
				'description' => '',
				'roles' => [ 'company', 'employee' ],
			],
			'service' => [
				'display_name' => 'Property service CRUD',
				'description' => '',
				'roles' => [ ],
			],
		],
		'employee' => [
			'view' => [
				'display_name' => 'Employee view',
				'description' => '',
				'roles' => [ 'company' ],
			],
			'edit' => [
				'display_name' => 'Employee edition',
				'description' => '',
				'roles' => [ 'company' ],
			],
			'create' => [
				'display_name' => 'Employee creation',
				'description' => '',
				'roles' => [ 'company' ],
			],
			'delete' => [
				'display_name' => 'Employee deletion',
				'description' => '',
				'roles' => [ 'company' ],
			],
		],
		'marketplace' => [
			'view' => [
				'display_name' => 'Marketplace elements view',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'edit' => [
				'display_name' => 'Marketplace elements edition',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'create' => [
				'display_name' => 'Marketplace elements creation',
				'description' => '',
				'roles' => [ ],
			],
			'delete' => [
				'display_name' => 'Marketplace elements deletion',
				'description' => '',
				'roles' => [ ],
			],
		],
		'planchange' => [
			'aproove' => [
				'display_name' => 'Plan change requests aprooval',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
		],
		'expirations' => [
			'view' => [
				'display_name' => 'Plan expirations view',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'edit' => [
				'display_name' => 'Plan expirations edition',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
		],
		'currency' => [
			'view' => [
				'display_name' => 'Currency elements view',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'edit' => [
				'display_name' => 'Currency elements edition',
				'description' => '',
				'roles' => [ 'franchisee' ],
			],
			'create' => [
				'display_name' => 'Currency elements creation',
				'description' => '',
				'roles' => [ ],
			],
			'delete' => [
				'display_name' => 'Currency elements deletion',
				'description' => '',
				'roles' => [ ],
			],
		],
	];

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
		$this->info( "Updating roles and permissions" );

		$this->updateRoles();
		$this->updatePermissions();
		$this->updateRolesPermissions();

		$this->checkSuperadmin();
	}


	public function updateRoles()
	{
		$role_ids = [0];

		foreach ($this->roles as $r)
		{
			// Get / create role
			$role = \App\Models\Role::firstOrCreate([
				'name' => $r['name']
			]);

			// Update role
			$role->display_name = $r['display_name'];
			$role->description = $r['description'];
			$role->level = $r['level'];
			$role->save();

			$role_ids[] = $role->id;

			// Delete all permissions
			$role->perms()->sync([]);
		}

		// Delete older roles
		\App\Models\Role::whereNotIn('id', $role_ids)->delete();		
	}

	public function updatePermissions()
	{
		$permission_ids = [0];

		foreach ($this->permissions as $group => $values) 
		{
			foreach ($values as $function => $def) 
			{
				$permission = \App\Models\Permission::firstOrCreate([
					'name' => "{$group}-{$function}",
				]);

				$permission->display_name = $def['display_name'];
				$permission->description = @$def['description'];
				$permission->save();

				$permission_ids[] = $permission->id;
			}
		}

		// Delete older permissions
		\App\Models\Permission::whereNotIn('id', $permission_ids)->delete();
	}

	public function updateRolesPermissions()
	{
		$roles = [];

		// Delete all roles/permissions
		\DB::table('permission_role')->where('permission_id','!=',0)->delete();

		// Assign new permissions
		foreach ($this->permissions as $group => $values) 
		{
			foreach ($values as $function => $def) 
			{
				// Get permission
				$permission = \App\Models\Permission::where([
					'name' => "{$group}-{$function}",
				])->first();

				if ( !$permission )
				{
					continue;
				}

				if ( empty($def['roles']) )
				{
					$def['roles'] = [];
				}

				if ( !is_array($def['roles']) )
				{
					$def['roles'] = [ $def['roles'] ];
				}

				if ( isset($this->roles['admin']) && !in_array('admin', $def['roles']) )
				{
					$def['roles'][] = 'admin';
				}

				foreach ($def['roles'] as $name) 
				{
					if ( !isset($roles[$name]) )
					{
						$roles[$name] = \App\Models\Role::where([
							'name' => $name,
						])->first();
					}

					if ( !$roles[$name] )
					{
						continue;
					}

					$roles[$name]->attachPermission($permission);
				}
			}
		}

	}

	public function checkSuperadmin()
	{
		$data = [
			'name' => 'Superadmin',
			'email' => 'admin@molista.com',
			'password' => 'Incubout21',
		];

		if ( \App\User::where('email',$data['email'])->count() > 0 )
		{
			return true;
		}

        $superadmin = \App\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $admin_role = \App\Models\Role::where([
			'name' => 'admin',
		])->first();

		if ( $admin_role )
		{
			$superadmin->roles()->attach($admin_role->id);
		}

        return true;
	}

}
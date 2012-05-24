<?php

class Layla_Domain_Add_Tables {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accounts', function($table)
		{
			$table->increments('id');
			$table->integer('language_id');
			$table->string('email');
			$table->string('password');
			$table->string('name');
			$table->timestamps();
		});

		DB::table('accounts')->insert(array(
			'language_id' => 1,
			'email' => 'admin@admin.com',
			'password' => Hash::make('admin'),
			'name' => 'Administrator',
			'created_at' => new \DateTime,
			'updated_at' => new \DateTime
		));

		Schema::create('roles', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});

		DB::table('roles')->insert(array(
			'name' => 'admin',
		));

		Schema::create('role_lang', function($table)
		{
			$table->increments('id');
			$table->integer('role_id');
			$table->string('name');
			$table->string('description');
		});

		DB::table('role_lang')->insert(array(
			'role_id' => 1,
			'name' => 'Admin',
			'description' => 'Dee maag alles dee jong...'
		));

		Schema::create('account_role', function($table)
		{
			$table->increments('id');
			$table->integer('account_id');
			$table->integer('role_id');
			$table->timestamps();
		});

		DB::table('account_role')->insert(array(
			'account_id' => 1,
			'role_id' => 1,
			'created_at' => new \DateTime,
			'updated_at' => new \DateTime
		));

		Schema::create('pages', function($table)
		{
			$table->increments('id');
			$table->integer('template_id');
			$table->integer('account_id');
			$table->string('type');
			$table->integer('order');
			$table->timestamps();
		});

		DB::table('pages')->insert(array(
			'template_id' => 1,
			'account_id' => 1,
			'type' => 'published',
			'order' => 1,
			'created_at' => new \DateTime,
			'updated_at' => new \DateTime
		));

		Schema::create('page_lang', function($table)
		{
			$table->increments('id');
			$table->integer('page_id');
			$table->integer('language_id');
			$table->integer('active');
			$table->string('url');
			$table->string('meta_title');
			$table->text('meta_keywords');
			$table->text('meta_description');
			$table->string('menu');
			$table->text('content');
			$table->timestamps();
		});

		DB::table('page_lang')->insert(array(
			'page_id' => 1,
			'language_id' => 1,
			'active' => 1,
			'url' => 'home',
			'meta_title' => 'Welkom op onze website | Testpagina',
			'meta_keywords' => 'home, welcome',
			'meta_description' => 'Welkom op de homepagina van ...',
			'menu' => 'Homepagina',
			'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
				tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
				quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
				cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
				proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
			'created_at' => new \DateTime,
			'updated_at' => new \DateTime
		));

		Schema::create('regions', function($table)
		{
			$table->increments('id');
			$table->integer('page_id');
			$table->integer('layout_id');
		});

		Schema::create('mediagroups', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});

		DB::table('mediagroups')->insert(array(
			'name' => 'Layla'
		));

		Schema::create('media', function($table)
		{
			$table->increments('id');
			$table->integer('mediagroup_id');
			$table->integer('mediatype_id');
			$table->string('location', 300);
			$table->timestamps();
		});

		DB::table('media')->insert(array(
			'mediagroup_id' => 1,
			'mediatype_id'  => 1,
			'location'      => path('public').'img'.DS.'layla.gif',
			'created_at'    => new \DateTime,
			'updated_at'    => new \DateTime
		));

		Schema::create('media_lang', function($table)
		{
			$table->increments('id');
			$table->integer('language_id');
			$table->string('title');
			$table->text('description');
			$table->string('tags');
			$table->timestamps();
		});

		DB::table('media_lang')->insert(array(
			'language_id' => 1,
			'title'       => 'Layla!',
			'description' => 'Layla logo',
			'tags'        => 'layla,logo',
			'created_at'  => new \DateTime,
			'updated_at'  => new \DateTime
		));

		Schema::create('mediatypes', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});

		DB::table('mediatypes')->insert(array(
			array('name' => 'image'),
			array('name' => 'youtube'),
			array('name' => 'vimeo')
		));

		Schema::create('module_region', function($table)
		{
			$table->increments('id');
			$table->integer('module_id');
			$table->integer('region_id');
			$table->integer('order');
			$table->text('settings');
		});

		Schema::create('modules', function($table)
		{
			$table->increments('id');
			$table->integer('name');
		});

		Schema::create('languages', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});

		DB::table('languages')->insert(array(
			'name' => 'English'
		));

		Schema::create('layouts', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('type'); // [webpage, stylesheet, javascript, partial]
			$table->text('content');
		});

		DB::table('layouts')->insert(array(
			'name' => 'Default',
			'type' => 'webpage',
			'content' => '<html><head></head><body>this is the layout</body></html>',
		));

		$permissiongroups = array(
			'account' => array(
				'lang' => array(
					1 => 'Account'
				),
				'permissions' => array(
					'create' => array(
						'lang' => array(
							1 => 'Create'
						)
					),
					'read' => array(
						'lang' => array(
							1 => 'Read'
						)
					),
					'update' => array(
						'lang' => array(
							1 => 'Update'
						)
					),
					'delete' => array(
						'lang' => array(
							1 => 'Delete'
						)
					),
					'set_permissions' => array(
						'lang' => array(
							1 => 'Set permissions'
						)
					)
				)
			),
			'page' => array(
				'lang' => array(
					1 => 'Page'
				),
				'permissions' => array(
					'create' => array(
						'lang' => array(
							1 => 'Create'
						)
					),
					'read' => array(
						'lang' => array(
							1 => 'Read'
						)
					),
					'update' => array(
						'lang' => array(
							1 => 'Update'
						)
					),
					'delete' => array(
						'lang' => array(
							1 => 'Delete'
						)
					),
					'publish' => array(
						'lang' => array(
							1 => 'Publish'
						)
					)
				)
			),
			'media' => array(
				'lang' => array(
					1 => 'Media'
				),
				'permissions' => array(
					'create' => array(
						'lang' => array(
							1 => 'Create'
						)
					),
					'read' => array(
						'lang' => array(
							1 => 'Read'
						)
					),
					'update' => array(
						'lang' => array(
							1 => 'Update'
						)
					),
					'delete' => array(
						'lang' => array(
							1 => 'Delete'
						)
					),
				)
			)
		);

		Schema::create('permissiongroups', function($table)
		{
			$table->increments('id');
			$table->string('resource');
		});

		Schema::create('permissiongroup_lang', function($table)
		{
			$table->increments('id');
			$table->integer('permissiongroup_id');
			$table->integer('language_id');
			$table->string('name');
		});

		Schema::create('permissions', function($table)
		{
			$table->increments('id');
			$table->integer('permissiongroup_id');
			$table->string('action');
		});

		Schema::create('permission_lang', function($table)
		{
			$table->increments('id');
			$table->integer('language_id');
			$table->string('name');
		});

		Schema::create('user_permission', function($table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('permission_id');
		});

		foreach ($permissiongroups as $resource => $options)
		{
			$permissiongroup_id = DB::table('permissiongroups')->insert_get_id(array(
				'resource' => $resource
			));

			foreach ($options['lang'] as $language_id => $name)
			{
				DB::table('permissiongroup_lang')->insert(array(
					'permissiongroup_id' => $permissiongroup_id,
					'language_id' => $language_id,
					'name' => $name
				));
			}

			foreach ($options['permissions'] as $action => $options)
			{
				DB::table('permissions')->insert(array(
					'permissiongroup_id' => $permissiongroup_id,
					'action' => $action
				));

				foreach ($options['lang'] as $language_id => $name)
				{
					DB::table('permission_lang')->insert(array(
						'language_id' => $language_id,
						'name' => $name
					));
				}
			}
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('accounts');
		Schema::drop('account_role');
		Schema::drop('languages');
		Schema::drop('layouts');
		Schema::drop('media');
		Schema::drop('mediagroups');
		Schema::drop('mediatypes');
		Schema::drop('media_lang');
		Schema::drop('modules');
		Schema::drop('module_region');
		Schema::drop('pages');
		Schema::drop('page_lang');
		Schema::drop('permissiongroups');
		Schema::drop('permissiongroup_lang');
		Schema::drop('permissions');
		Schema::drop('permission_lang');
		Schema::drop('user_permission');
		Schema::drop('regions');
		Schema::drop('roles');
		Schema::drop('role_lang');
	}

}
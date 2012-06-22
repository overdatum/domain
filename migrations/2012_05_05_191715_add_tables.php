<?php

class Domain_Add_Tables {

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
			$table->string('email');
			$table->string('password');
			$table->string('name');
			$table->string('slug');
			$table->integer('language_id');
			$table->timestamps();
		});

		DB::table('accounts')->insert(array(
			'id' => 1,
			'language_id' => 1,
			'email' => 'admin@admin.com',
			'password' => Hash::make('admin'),
			'name' => 'Administrator',
			'slug' => 'administrator',
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

		DB::table('pages')->insert(array(
			'template_id' => 1,
			'account_id' => 1,
			'type' => 'published',
			'order' => 2,
			'created_at' => new \DateTime,
			'updated_at' => new \DateTime
		));

		Schema::create('page_lang', function($table)
		{
			$table->increments('id');
			$table->integer('page_id');
			$table->integer('language_id');
			$table->integer('active');
			$table->string('slug');
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
			'language_id' => 2,
			'active' => 0,
			'url' => 'welkom',
			'slug' => 'welkom',
			'meta_title' => 'Welkom op de homepagina',
			'meta_keywords' => 'welkom, begin hier, tja...',
			'meta_description' => 'Welkom op Layla\'s test pagina',
			'menu' => 'Welkom',
			'content' => 'Welkom op de homepagina, vriend',
			'created_at' => new \DateTime,
			'updated_at' => new \DateTime
		));

		DB::table('page_lang')->insert(array(
			'page_id' => 1,
			'language_id' => 1,
			'active' => 0,
			'url' => 'home',
			'slug' => 'home',
			'meta_title' => 'Welcome to the homepage',
			'meta_keywords' => 'home, welcome, start here, lol',
			'meta_description' => 'Welcome to Layla\'s test website',
			'menu' => 'Home',
			'content' => 'Welcome to the homepage, bro',
			'created_at' => new \DateTime,
			'updated_at' => new \DateTime
		));

		DB::table('page_lang')->insert(array(
			'page_id' => 2,
			'language_id' => 1,
			'active' => 1,
			'url' => 'about-us',
			'slug' => 'about-us',
			'meta_title' => 'About Layla',
			'meta_keywords' => 'about us, layla, what we do, blah, bla',
			'meta_description' => 'We are Layla, blablabla',
			'menu' => 'About Us',
			'content' => 'Layla is a...',
			'created_at' => new \DateTime,
			'updated_at' => new \DateTime
		));

		DB::table('page_lang')->insert(array(
			'page_id' => 2,
			'language_id' => 2,
			'active' => 0,
			'url' => 'over-ons',
			'slug' => 'over-ons',
			'meta_title' => 'Over Layla',
			'meta_keywords' => 'dit, gaat, over, ons',
			'meta_description' => 'Layla doet dit en dat, zus en zo',
			'menu' => 'Over Ons',
			'content' => 'Wij zijn Layla!',
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
			$table->integer('module_id');
			$table->string('slug');
			$table->string('name');
			$table->timestamps();
		});

		DB::table('mediagroups')->insert(array(
			array(
				'module_id' => 1,
				'name' => 'Nederland - Portugal',
				'slug' => 'nederland-portugal',
				'created_at' => new \DateTime,
				'updated_at' => new \DateTime
			),
			array(
				'module_id' => 1,
				'name' => 'Nederland - Germany',
				'slug' => 'nederland-germany',
				'created_at' => new \DateTime,
				'updated_at' => new \DateTime
			),
			array(
				'module_id' => 2,
				'name' => 'Products',
				'slug' => 'products',
				'created_at' => new \DateTime,
				'updated_at' => new \DateTime
			),
			array(
				'module_id' => 2,
				'name' => 'Categories',
				'slug' => 'categories',
				'created_at' => new \DateTime,
				'updated_at' => new \DateTime
			),
		));

		Schema::create('assets', function($table)
		{
			$table->increments('id');
			$table->integer('mediagroup_id');
			$table->integer('mediatype_id');
			$table->string('location', 300);
			$table->timestamps();
		});

		DB::table('assets')->insert(array(
			'mediagroup_id' => 1,
			'mediatype_id'  => 1,
			'location'      => path('public').'img'.DS.'layla.gif',
			'created_at'    => new \DateTime,
			'updated_at'    => new \DateTime
		));

		Schema::create('asset_lang', function($table)
		{
			$table->increments('id');
			$table->integer('asset_id');
			$table->integer('language_id');
			$table->string('name');
			$table->string('slug');
			$table->text('description');
			$table->string('tags');
			$table->timestamps();
		});

		DB::table('asset_lang')->insert(array(
			'asset_id' => 1,
			'language_id' => 1,
			'name'       => 'Layla!',
			'description' => 'Layla logo',
			'tags'        => 'layla,logo',
			'slug'		  => 'layla',
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
			$table->string('name');
			$table->string('slug');
		});

		DB::table('modules')->insert(array(
			array(
				'name' => 'Layla',
				'slug' => 'layla'
			),
			array(
				'name' => 'Webshop',
				'slug' => 'webshop'
			)
		));

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
			$table->string('slug');
			$table->text('content');
		});

		DB::table('layouts')->insert(array(
			'name' => 'Default',
			'type' => 'webpage',
			'slug' => 'default',
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
		Schema::drop('assets');
		Schema::drop('mediagroups');
		Schema::drop('mediatypes');
		Schema::drop('asset_lang');
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
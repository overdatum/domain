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
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('accounts');
		Schema::drop('roles');
		Schema::drop('role_lang');
		Schema::drop('account_role');
		Schema::drop('pages');
		Schema::drop('page_lang');
		Schema::drop('regions');
		Schema::drop('module_region');
		Schema::drop('modules');
		Schema::drop('languages');
		Schema::drop('layouts');
	}

}
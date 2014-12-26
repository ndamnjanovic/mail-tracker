<?php

class UserTableSeeder extends Seeder {

  public function run()
  {
    DB::table('users')->delete();

    User::create(array(
      'email' => 'foo@bar.com',
      'password' => Hash::make('foobar'),
      'is_admin' => 1
      )
    );

    User::create(array(
      'email' => 'ned@bisnow.com',
      'password' => Hash::make(''),
      'is_admin' => 0
      )
    );

    User::create(array(
      'email' => 'alex.funaro@bisnow.com',
      'password' => Hash::make(''),
      'is_admin' => 0
      )
    );

    User::create(array(
      'email' => 'brandon.best@bisnow.com',
      'password' => Hash::make(''),
      'is_admin' => 0
      )
    );
  }

}
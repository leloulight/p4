<?php
use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
	/**
	* Run the database seeds.
	*
	* @return void
	*/
	public function run(){
		
                DB::table('users')->delete();
                User::create(array(
                        'firstname' => 'User',
                        'lastname'  => 'Name',
                        'username'  => 'username1',
                        'email'    => 'user1@msn.com',
                        'password' => 'usernameone',
                ));

		// create directories //	
		// check to see if directory is created //
		if(File::exists(public_path().'/../storage/uploads')) {
			File::deleteDirectory(public_path().'/../storage/uploads');
		}

                File::makeDirectory(public_path().'/../storage/uploads');
                File::makeDirectory(public_path().'/../storage/uploads/username1');
		
		// chmod directory //
		chmod("/../storage/uploads/username1", 777);
        }

}

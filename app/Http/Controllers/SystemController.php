<?php
namespace App\Http\Controllers;

use View;
use File;
use Mail;
use Input;
use DB;
use Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SystemController extends BaseController {

	/////////////////////////////////////////////////////
	// Mainloop to determine which function to perform //
	/////////////////////////////////////////////////////////////////////
	public function mainloop() {

		// check for delete files //
		if((Input::get('deleteFiles') != null)) { 
			$this->deleteFiles();
                        return View::make('main')->with(array('display' => $this->getDirectoryFiles()));
		}

                // check for delete files //
                if((Input::get('emailFile') != null)) { 
			$this->emailFile();
                        return View::make('main')->with(array('display' => $this->getDirectoryFiles()));
		}

                // check for log //
                if((Input::get('viewLog') != null)) {
			$this->getLogFile();
                        return View::make('main')->with(array('display' => $this->getDirectoryFiles()));
                }

	
		// check for logging out //
                if((Input::get('logout') != null)) {
			Auth::logout();
                        return View::make('login');
		}
	
		// check for upload files //
		else { 
                	$this->uploadFiles();
                	return View::make('main')->with('display', $this->getDirectoryFiles());
		}
	}

	////////////////
	// Login user //
	//////////////////////////////////////////////////
	public function login() {

		// setup query for username and password //
		$query = 'select * from users where username = "' . Input::get('username') . '" and password = "' . Input::get('password') . '"';
                $result = DB::select($query);

		// check result to see if we have a match //
		if($result == null)
                        return View::make('login')->with('display', "Incorrect username or password!");

		// we have a match authenticate that user amd show main menu //
		else {
			Auth::loginUsingId($result[0]->id);
                        // update log file //
                        $this->addLog("Logged in IP:" . $_SERVER['REMOTE_ADDR']);
                        return View::make('main')->with('display', $this->getDirectoryFiles());
		}

	}

	///////////////////////////
	// Delete selected files //
	//////////////////////////////////////////////////////////////////
	public function deleteFiles() {

		// get username logged in //
		$user = Auth::user()->username;

		// get files array //
		$files = Input::get('selectedfiles');

		// check if any files was selected //
		if($files == null)
			return;

		// loop through array and delete files //
		foreach ($files as $file) {
                        File::delete(public_path().'/../storage/uploads/'. $user . "/".$file);
			$this->removeFileDB($file, $user);
		}
	}

	///////////////////////////////////////
	// Download file from user directory //
	//////////////////////////////////////////////////////////////////////////
	public function emailFile() {

		$data = "";

                // get files array //
                $files = Input::get('selectedfiles');

                // check if any files was selected //
                if($files == null)
                        return;

		// create mew message with file attachments //
		Mail::send('emails.sendfiles', array($data), function($message) {

			// get user info //
			$email = Auth::user()->email;
			$fn = Auth::user()->firstname;
			$ln = Auth::user()->lastname;
                	$user = Auth::user()->username;

			$message->from('semdfiles@mmwriting.com', 'CloudIE');
  			$message->to($email, $fn . " " . $ln)->subject('File request.');

			// get files that need to be attached //			
			$files = Input::get('selectedfiles');

			// loop through files and attach them to email //
			foreach ($files as $file) {
				$message->attach(public_path().'/../storage/uploads/'. $user . "/".$file);
				// update log file //
				$this->addLog("Emailed file " . $file);
			}

		});
	}

	////////////////////////////////
	// Upload files to web server //
	////////////////////////////////////////////////////////////////////////////////
        public function uploadFiles() {

		// check to see if we have any files on http //
		if(Input::hasFile('uploadfiles')) {

			// get files //
			$files = Input::file('uploadfiles');

			// get user logged in //
			$user = Auth::user()->username;

			// loop through files array for files to upload //
 	   		foreach($files as $file) {
				$file->move(public_path().'/../storage/uploads/'. $user . '/', $file->getClientOriginalName());
				$this->addFileDB($file->getClientOriginalName(), $user);
				// update log file //
				$this->addLog("Uploaded file " . $file->getClientOriginalName());

	    		}

			// return main menu view will success message //
			return View::make('main')->with('display', "Files uploaded!");
		}

		// no files to upload //
                $value = $this->getDirectoryFiles();
                return View::make('main')->with('display', "No files selected!<br>" . $value);

        }


	////////////////////////
	// Create new user ID //
	////////////////////////////////////////////////////
	public function createID() {

		// check validation rules //
		$validation = \Validator::make(
    			[
        			'username' => Input::get('username'),
        			'email' => Input::get('email'),
                                'password' => Input::get('password'),
				'password_comfirmation' => Input::get('confirm_password'),
                                'firstname' => Input::get('firstname'),
                                'lastname' => Input::get('lastname')

    			],
    			[
        			'username' => array( 'required', 'alpha_dash', 'min:8'),
        			'email' => array( 'required', 'email' ),
                                'password' => array( 'required', 'alpha_dash', 'min:8'),
				'password_comfirmation' => array('required', 'alpha_dash', 'min:8', 'same:password'), 
                                'firstname' => array( 'required', 'alpha_dash'),
                                'lastname' => array( 'required', 'alpha_dash')
    			]
		);

		// check if all form information is valid //
        	if($validation->fails())
        	{

	                return View::make('newuser')->with('display', $validation->errors());
        	}

		// check database //
		$result = DB::select('select * from users where username = ?', array(Input::get('username')));

		// check if users exists // 
		if($result) {
			return View::make('newuser')->with('display', "Username already exists!");
		}
		
		// create new account //
                $username = Input::get('username');
                $email = Input::get('email');
                $password = Input::get('password');
		$firstname = Input::get('firstname');
		$lastname = Input::get('lastname');

		// add user information to database //
                DB::table('users')->insert(array('firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'username' => $username, 'password' => $password));

		// create user directory //
		File::makeDirectory(public_path().'/../storage/uploads/'. $username);

		// send welcome //
		$this->emailWelcome();

		// authenticate user //
                $result = DB::select('select * from users where username = ?', array($username));
		Auth::loginUsingId($result[0]->id);

                return View::make('main')->with('display', $this->getDirectoryFiles());
	}

        /////////////////////////////////
        // Send new user welcome email //
        //////////////////////////////////////////////////////////////////////////
        public function emailWelcome() {

                $data = "";

                // create mew message with file attachments //
                Mail::send('emails.welcome', array($data), function($message) {
        	        $email = Input::get('email');
                	$fn = Input::get('firstname');
                	$ln = Input::get('lastname');

                        $message->from('welcome@mmwriting.com', 'CloudIE');
                        $message->to($email, $fn . " " . $ln)->subject('Welcome!');
                });
        }

        //////////////////////////////
        // Add file to our database //
        ////////////////////////////////////////////////////////////////////
        public function addLog($msg) {

                // get user logged in //
                $user = Auth::user()->username;
		// get date //
		$tag = date('Y-m-d H:i:s');
                DB::table('logs')->insert(array('userref' => $user, 'description' => $tag . " - " . $msg));
        }


	//////////////////////////////
	// Add file to our database //
	////////////////////////////////////////////////////////////////////
	public function addFileDB($file, $userID) {
		
		DB::table('files')->insert(array('filename' => $file, 'username' => $userID));
	}

        //////////////////////////////
        // Get log file information //
        ////////////////////////////////////////////////////////////////////
        public function getLogFile() {

		// get current user //
                $user = Auth::user()->username;
		// create query //
                $query = 'select * from logs where userref = "' . $user . '"';
		// get query results //
                $logs = DB::select($query);
		// open new window //
		?><script> 
                        newwindow = window.open("", "logwindow");
                        newwindow.document.write("<b><h2>Log File:</h2></b>"); 
                        newwindow.document.write("<hr><br>"); 

		</script><?php

		// loop through log file //
		foreach ($logs as $log) {
			?><script> newwindow.document.write("<?php echo $log->description; ?><br>"); </script><?php
		}

        }

        /////////////////////////////////
        // Remove file to our database //
        ////////////////////////////////////////////////////////////////////
        public function removeFileDB($file, $userID) {

		$query = 'delete from files where filename = "' . $file . '" and username =  "' . $userID . '"';
		DB::statement($query);

		// update log file //
		$this->addLog("Deleted file " . $file);
        }

	////////////////////////////////////////////////
	// Get a listing of files in user's directory //
	/////////////////////////////////////////////////////////////////////////////////////
	public function getDirectoryFiles() {

		// get user logged in //
		$user = Auth::user()->username;

		// get users directory path //
		$dhandle = opendir(public_path().'/../storage/uploads/'. $user);

		// files variable //
		$files = array();

		// get files in users directory //
		if ($dhandle) {
   			while (false !== ($fname = readdir($dhandle))) {
      				if (($fname != '.') && ($fname != '..') &&
          			($fname != basename($_SERVER['PHP_SELF']))) {
          				$files[] = (is_dir( "./$fname" )) ? "(Dir) {$fname}" : $fname;
      				}
   			}
   			closedir($dhandle);
		}

		// header //
		$value = "<b><u>Files:</u></b><br>\n";
	
		// loop through files in directory //
		foreach( $files as $fname ) {
			$value .= '<input type = "checkbox" name="selectedfiles[]" value = "' . $fname . '">';
			$value .= " {$fname}<br>";
		}
		return $value;
	}
}

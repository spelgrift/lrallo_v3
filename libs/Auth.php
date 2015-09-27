<?php 

class Auth
{
	/**
	 * setAccess - Allow access to a controller if logged in with given clearance, redirects to login page if not
	 *
	 * @param string $clearance - 'owner' Restrict to owner only
	 */
	public static function setAccess($clearance = false)
	{
		@session_start();

		$logged = $_SESSION['loggedIn'];

		if($clearance) {
			$role = $_SESSION['role'];
			if($logged == false || $role != $clearance){
				session_destroy();
				header('location:'. URL . 'login');
				exit;
			}
		} else {
			if($logged == false){
				session_destroy();
				header('location:'. URL . 'login');
				exit;
			}
		}
	}

}


?>
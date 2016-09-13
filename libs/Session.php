<?php

class Session
{
	
	public static function init()
	{
		$time = $_SERVER['REQUEST_TIME'];
		/**
		 * for a 30 minute timeout, specified in seconds
		 */
		$timeout_duration = 1800;

		/**
		 * Here we look for the user’s LAST_ACTIVITY timestamp. If
		 * it’s set and indicates our $timeout_duration has passed, 
		 * blow away any previous $_SESSION data and start a new one.
		 */
		if (isset($_SESSION['LAST_ACTIVITY']) && ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
		  session_unset();     
		  session_destroy();
		  session_start();    
		}
		    
		/**
		 * Finally, update LAST_ACTIVITY so that our timeout 
		 * is based on it and not the user’s login time.
		 */
		$_SESSION['LAST_ACTIVITY'] = $time;
		if(!self::_is_session_started()) {
			session_start();
		}
	}
	
	public static function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}
	
	public static function get($key)
	{
		if (isset($_SESSION[$key]))
		return $_SESSION[$key];
	}
	
	public static function destroy()
	{
		//unset($_SESSION);
		session_destroy();
	}

	private static function _is_session_started()
	{
		if ( php_sapi_name() !== 'cli' ) {
			if ( version_compare(phpversion(), '5.4.0', '>=') ) {
				return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			} else {
				return session_id() === '' ? FALSE : TRUE;
			}
		}
		return FALSE;
	}
}
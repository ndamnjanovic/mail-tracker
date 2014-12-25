<?php 

return array( 
	
	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => 'Session', 

	/**
	 * Consumers
	 */
	'consumers' => array(

		/**
		 * Google
		 */
      'Google' => array(
          'client_id'     => '479715892996-ob5cm0qma921g0umr522h2vbv2nptkr4.apps.googleusercontent.com',
          'client_secret' => 'yKbPy4KK6u0OmvKB9xu3eOYG',
          'scope'         => array('https://www.googleapis.com/auth/admin.reports.usage.readonly'),
        ),		

	)

);
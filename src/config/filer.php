<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Paths
	|--------------------------------------------------------------------------
	|
	| The relative and absolute paths to the directory where your local
    | attachments are stored.
	|
	*/
    'path' => array(
        'relative' => 'uploads/',
        'absolute' => public_path() . '/uploads/'
    ),

	/*
	|--------------------------------------------------------------------------
	| Current user
	|--------------------------------------------------------------------------
	|
	| Closure to return the current user. This is used to set the owner of an
    | attachment.
	|
	*/
	'current_user' => function() {
		return Auth::user();
	},

];

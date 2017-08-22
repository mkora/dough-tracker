<?php
return array(
	'mongo' => array(
		'connection' 		=> sprintf('mongodb://%s:%d/%s', '127.0.0.1', '27017',
												'finance'),
		'dbname' 				=> 'finance',
		'dbcollection' 	=> 'items',
	),

);

<?php
$dbname ='finance';
return array(
	'displayErrorDetails' => false,
	'addContentLengthHeader' => false,
	'mongo'	=> array(
		'connection'	=> sprintf('mongodb://%s:%d/%s', 
								'127.0.0.1',
								'27017',
								$dbname
							),
		'dbname'	=> $dbname,
		'dbcollection'	=> 'items',
	),
);

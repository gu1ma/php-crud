<?php

 return [
 	'propel'=>[
        'database'=>[
            'connections'=>[
 				'treinamentophp'=>[
 					'adapter'=>'mysql',
 					'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
 					'dsn'=>'mysql:host=localhost;dbname=treinamentophp',
 					'user'=>'akertreinamento',
 					'password'=>'Aker1010',
 					'attributes'=>[]
 				]
 			]
 		], 
 		'runtime'=>[
 			'defaultConnection'=>'treinamentophp',
 			'connections'=>['treinamentophp']
 		],
 		'generator'=>[
 			'defaultConnection'=>'treinamentophp',
 			'connections'=>['treinamentophp']
 		]
 	]
 ]
	
?>
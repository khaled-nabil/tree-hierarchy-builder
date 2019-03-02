<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => 'php://stdout',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'database' => [
            'dsn' => 'sqlite:' . __DIR__ . '/../data/database.sq3',
            'username' => null,
            'password' => null,
            'attributes' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        ],
        "jwt" => [
            'secret' => 'e10adc3949ba59abbe56e057f20f883e'
        ],
		'system' => [
			'username' => 'admin',
			'password' => '123456'
		]
    ],
];

<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'File info',
	'description' => 'Adds infos to linked files in your website (filetype and -size).',
	'category' => 'fe',
	'version' => '13.4.0',
	'state' => 'stable',
	'author' => 'Peter Benke',
	'author_email' => 'info@typomotor.de',
	'author_company' => null,
	'constraints' =>[
		'depends' => [
            'typo3' => '13.4.0-13.4.99',
		],
	],
];

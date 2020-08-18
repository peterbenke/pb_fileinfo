<?php

$EM_CONF[$_EXTKEY] = array (
	'title' => 'File info',
	'description' => 'Adds infos to linked files in your website (filetype and -size).',
	'category' => 'fe',
	'version' => '3.0.1',
	'state' => 'stable',
	'author' => 'Peter Benke',
	'author_email' => 'info@typomotor.de',
	'author_company' => null,
	'constraints' =>[
		'depends' => [
			'typo3' => '9.5.0-10.4.99',
			'php' => '7.2',
		],
	],
);

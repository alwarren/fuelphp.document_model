<?php

Autoloader::add_core_namespace('Document');

Autoloader::add_classes(array(
	'Document\\Document'           => __DIR__.'/classes/document.php',
	'Document\\DocumentAbstract'   => __DIR__.'/classes/document_abstract.php',
));

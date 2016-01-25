<?php

// Paths (ensure to leave on trailing /)
define('URL', 'http://kate.sampelgrift.com/ts/imageman/');
define('DEVPATH', '/ts/imageman/'); // Remove for production!

define('LIBS', 'libs/');
define('UPLOADS', 'uploads/');
define('ORIGINALS', UPLOADS.'originals/');

// Database Login
define('dbTYPE', 'mysql');
define('dbHOST', 'localhost');
define('dbDATABASE', 'mvc');
define('dbUSER', 'imageman');
define('dbPASS', 'imageman');

// Hash key for encoding passwords
define('PASS_HASH_KEY', 'RogerTheDog');

// Hash key for other stuff
define('GENERAL_HASH_KEY', 'WonderfulWonderful');

// Image resizing dimensions
define('SmIMAGE', 800);
define('MdIMAGE', 1200);
define('LgIMAGE', 1600);
?>
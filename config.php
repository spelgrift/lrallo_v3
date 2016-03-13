<?php
// Paths (ensure to leave on trailing /)
define('URL', 'http://kate.sampelgrift.com/ts/imageman/');
define('DEVPATH', '/ts/imageman/'); // Remove for production!

define('LIBS', 'libs/');
define('UPLOADS', 'uploads/');
define('ORIGINALS', UPLOADS.'originals/');
define('THUMBS', UPLOADS.'thumbs/');
define('DELETED', ORIGINALS.'deleted/');


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
define('SmIMAGE', 1200);
define('LgIMAGE', 1755);
define('THUMBSIZE', 300);

// Save original images on delete (moves them to deleted dir)
define('SAVE_ORIGINALS', false);

// Default content bootstrap widths
define('BS_TEXT', 'col-xs-12');
define('BS_SINGLE_IMAGE', 'col-xs-12');
define('BS_PAGE', 'col-xs-6 col-sm-4');
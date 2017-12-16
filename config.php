<?php
// Brand (appears in page titles and in the navbar [by default])
define('BRAND', 'Imageman');

// Paths (leave on trailing /)
define('URL', 'http://kate.sampelgrift.com/ts/imageman/');
define('DEVPATH', '/ts/imageman/'); // Set to empty string for production

define('LIBS', 'libs/');
define('UPLOADS', 'uploads/');
define('ORIGINALS', UPLOADS.'originals/');
define('THUMBS', UPLOADS.'thumbs/');
define('COVERS', UPLOADS. 'covers/');
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
define('COVERASPECT', (2 / 3)); // corresponds to less var

// Save original images on delete (moves them to deleted dir)
define('SAVE_ORIGINALS', false);

// Default content bootstrap widths
define('BS_TEXT', 'col-xs-12');
define('BS_SINGLE_IMAGE', 'col-xs-12');
define('BS_PAGE', 'col-xs-6 col-sm-4');
define('BS_SLIDESHOW', 'col-xs-12 col-sm-8');
define('BS_VIDEO', 'col-xs-12');
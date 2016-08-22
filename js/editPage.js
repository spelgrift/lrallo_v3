require('../less/styles.less'); // Compile LESS

require('./libs/bs.collapse.js'); // Bootstrap collapse (for navbar)
require('./libs/bs.dropdown.js'); // Bootstrap dropdowns
require('./libs/bs.modal.js'); // Bootstrap modals

require('./imageman/tabs.js');
require('./libs/events.js'); 

// Edit page scripts
require('./imageman/editPage.settings.js');
require('./imageman/editPage.addContent.js');
require('./imageman/editPage.contentControls.js');
require('./imageman/editPage.contentResize.js');
require('./imageman/editPage.shortcutSettings.js');
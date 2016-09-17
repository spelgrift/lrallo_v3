require('../less/styles.less'); // Compile LESS

require('./libs/bs.collapse.js'); // Bootstrap collapse (for navbar)
require('./libs/bs.dropdown.js'); // Bootstrap dropdowns
require('./libs/bs.modal.js'); // Bootstrap modals

require('./imageman/tabs.js');
require('./libs/events.js');
require('./imageman/timeout.js');
require('./imageman/slideshows.js');

// Edit page scripts
require('./imageman/editPage.settings.js');
require('./imageman/editPage.addContent.js');
require('./imageman/editPage.addPage.js');
require('./imageman/editPage.addVideo.js');
require('./imageman/editPage.addGallery.js');
require('./imageman/editPage.addSlideshow.js');
require('./imageman/editPage.addText.js');
require('./imageman/editPage.addImage.js');
require('./imageman/editPage.contentControls.js');
require('./imageman/editPage.contentResize.js');
require('./imageman/editPage.shortcutSettings.js');
require('../less/styles.less');

require('./libs/bs.collapse.js'); // Bootstrap collapse (for navbar)
require('./libs/bs.dropdown.js'); // Bootstrap dropdowns
require('./libs/bs.modal.js'); // Bootstrap modals

require('./imageman/tabs.js');
require('./libs/events.js'); 
require('./imageman/timeout.js');
require('./imageman/slideshows.js');

require('./imageman/blog.editPost.js'); // New/Edit post
require('./imageman/blog.manage.js'); // Manage blog

// Content modules
require('./imageman/editPage.contentControls.js');
require('./imageman/editPage.contentResize.js');
require('./imageman/editPage.addContent.js');
require('./imageman/editPage.addImage.js');
require('./imageman/editPage.addText.js');
require('./imageman/editPage.editText.js');
require('./imageman/editPage.addEmbedVideo.js');
require('./imageman/editPage.addSlideshow.js');
require('./imageman/editPage.slideshowSettings.js');
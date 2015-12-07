module.exports = function(grunt) {

  // ===========================================================================
  // CONFIGURE GRUNT ===========================================================
  // ===========================================================================
  grunt.initConfig({

    // get the configuration info from package.json ----------------------------
    // this way we can use things like name and version (pkg.name)
    pkg: grunt.file.readJSON('package.json'),

    // config packages

    // configure jshint to validate js files -----------------------------------
    // jshint: {
    //   options: {
    //     reporter: require('jshint-stylish') // use jshint-stylish to make our errors look and read good
    //   },

    //   // when this task is run, lint the Gruntfile and all js files in src
    //   build: ['Gruntfile.js', 'src/**/*.js']
    // },

    // compile less stylesheets to css -----------------------------------------
    less: {
      build: {
        files: {
          'public/css/styles.css': 'less/styles.less'
        }
      }
    },

    // configure watch to auto update ----------------
    watch: {
      
      // for stylesheets, watch css and less files and run less
      stylesheets: {
        files: ['less/**/*.less'], 
        tasks: ['less'] 
      }

      // for scripts, run jshint
      // scripts: { 
      //   files: 'src/**/*.js', 
      //   tasks: ['jshint'] 
      // } 
    }
  });

  // ===========================================================================
  // LOAD GRUNT PLUGINS ========================================================
  // ===========================================================================
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-watch');
};
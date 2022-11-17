module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
		
		less: {
			wp_css: {
				files: {
					'assets/css/map-first.css': 'assets/less/map-first.less'
				}
			}			
		},
		
		cssmin: {
			wp_css: {
				files: {
					'assets/css/map-first.min.css': 'assets/css/map-first.css'
				}
			}			
		},		
		
		watch: {			
			wp_css: {
				files: ['assets/less/*.less'],
				tasks: ['build_wp_css']
			}			
		}
  });

	grunt.loadNpmTasks('grunt-contrib-cssmin');  
	grunt.loadNpmTasks('grunt-contrib-less');  
  grunt.loadNpmTasks('grunt-contrib-watch');	

  grunt.registerTask('default', [
  	'less',
  	'cssmin',
  	'watch'
  ]);

  grunt.registerTask('build_wp_css', [
   	'less:wp_css',
   	'cssmin:wp_css'
  ]);   
};

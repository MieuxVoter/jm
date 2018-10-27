const sass = require('node-sass');

module.exports = function (grunt) {
    grunt.initConfig({
        "pkg": grunt.file.readJSON("package.json"),
        "concat": {
            "options": {
                "separator":";\n"
            },
            "web": {
                "files": [
                   {
                        'public/js/vendor.js': [
                            'node_modules/jquery/dist/jquery.js',
                            'node_modules/popper.js/dist/umd/popper.js',
                            'node_modules/bootstrap/dist/js/bootstrap.js',
                            'node_modules/chartist/dist/chartist.min.js',
                            'node_modules/toastr/toastr.js',
                        ]
                    },
                    {
                        'public/js/app.js': [
                            'src/js/**/*.js'
                        ]
                    }
                ]
            }

        },
        "uglify": {
            "options": {
                "sourceMap": true
            },
            "web": {
                "files": [
                     {
                        "public/js/vendor.min.js": [
                            "public/js/vendor.js"
                        ]
                    },
                    {
                        "public/js/app.min.js": [
                            "public/js/app.js"
                        ]
                    }
                ]
            }
        },
        "sass": {
            "web": {
                "options":{
                    sourceMap: true,
                    implementation: sass
                },
                files: [{
                    expand: true,
                    cwd: 'src/scss',
                    src: '**/*.{scss,sass}',
                    dest: 'src/cssBootstrap',
                    ext: '.css'
                }]
            }
        },
        "concat_css": {
            "options": {},
            "web": {
                "files": [{
                    'public/css/vendor.css': [
                        'src/cssBootstrap/bootstrap.css',
                        'node_modules/@fortawesome/fontawesome-free/css/*.css',
                        'node_modules/chartist/dist/chartist.min.css',
                        'node_modules/toastr/build/toastr.css'
                    ]
                }, {
                    'public/css/app.css': [
                        'src/css/**/*.css'
                    ]
                }]
            }
        },
        "cssmin": {
            "web": {
                "files": [{
                    'public/css/app.min.css': [
                        'public/css/app.css'
                    ]
                }, {
                    'public/css/vendor.min.css': [
                        'public/css/vendor.css'
                    ]
                }
                ]
            }

        },
        "copy": {
            "web": {
                "files": [{
                    "flatten": true,
                    "cwd": 'node_modules/@fortawesome/fontawesome-free/webfonts',
                    "src": ['**/*.otf', '**/*.ttf', '**/*.eot', '**/*.svg', '**/*.woff', '**/*.woff2'],
                    "dest": 'public/webfonts/',
                    "expand": true
                }, {
                    "flatten": true,
                    "cwd": 'node_modules/bootstrap/',
                    "src": ['**/*.otf', '**/*.ttf', '**/*.eot', '**/*.svg', '**/*.woff', '**/*.woff2'],
                    "dest": 'public/fonts',
                    "expand": true
                },{
                    "flatten": true,
                    "cwd": 'src/img',
                    "src": ['**/*.*'],
                    "dest": 'dist/css/img',
                    "expand": true
                }]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify-es');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-concat-css');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-sass');


    grunt.registerTask('default', ['concat', 'uglify' ,'sass', 'concat_css', 'cssmin', 'copy']);

};
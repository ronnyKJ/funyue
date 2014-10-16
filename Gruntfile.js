module.exports = function (grunt) {

    function md5(content){
        var crypto = require('crypto');
        var md5 = crypto.createHash('md5');
        md5.update(content);
        return md5.digest('hex');
    }

    var fs = require('fs');

    function getContentMD5(file, line){
        var data = fs.readFileSync(file, 'utf-8');
        var script = (data.split('\n'))[line];
        return md5(script);
    }

    // 项目配置

    var cssdevurl = '/public/modules/wc/img/';
    var cssprourl = 'http://m.baidu.com/static/search/ala/';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("mm-dd HH:MM") %> */\n'
            },
            buildLibs: {
                src: [
                    "public/vendor/jquery/jquery.js",
                    "public/vendor/hammerjs/dist/jquery.hammer.min.js",
                    // "public/vendor/underscore/underscore.js",
                    // "public/vendor/backbone/Backbone.v1.1.0.js",
                    "public/vendor/jquery-tmpl/jquery.tmpl.min.js",
                    "public/vendor/swiper/dist/idangerous.swiper-2.4-zkj.js",
                    "public/vendor/router/router.js",
                    "public/vendor/muplayer/player.min.js",
                ],
                dest: 'dist/fy-libs.js'
            },
            buildApp: {
                src: [
                    "public/modules/wc/js/data.js",
                    "public/modules/wc/js/EndlessSlider.js",
                    "public/modules/wc/js/App.js",
                    "public/modules/wc/js/main.js"
                ],
                dest: 'dist/fy-app.js'
            }
        },

        cssmin: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("mm-dd HH:MM") %> */'
            },
            compress: {
                files: {
                    "dist/fy-app.css" : [
                        "public/modules/wc/css/idangerous.swiper.css",
                        "public/common/css/normalize.css",
                        "public/modules/wc/css/main.css"
                    ]
                }
            }
        },

        replace: {
            online: {
                src: 'application/views/wc.php',
                overwrite: true,
                replacements: [{
                    from: /<!-- script start -->[^]*<!-- script end -->/ig,
                    to: function(){
                        var t1 = getContentMD5('dist/fy-libs.js', 1);
                        var t2 = getContentMD5('dist/fy-app.js', 1);
                        return '<script type="text/javascript" src="<?php echo $static_url;?>fy-libs.js?t='+t1+'"></script><script type="text/javascript" src="<?php echo $static_url;?>fy-app.js?t='+t2+'"></script>'
                    }
                }, {
                    from: /<!-- style start -->[^]*<!-- style end -->/ig,
                    to: function(){
                        var t = getContentMD5('dist/fy-app.css', 1);
                        return '<link rel="stylesheet" href="<?php echo $static_url;?>fy-app.css?t='+t+'">';
                    }
                }]
            },
            env: {
                src: 'application/config/env.php',
                overwrite: true,
                replacements: [ {
                    from: /'dev'/ig,
                    to: "'production'"
                } ]
            },
            csspic: {
                src: 'dist/fy-app.css',
                overwrite: true,
                replacements: [
                {
                    from: new RegExp( cssdevurl + 'start.jpg', 'ig'),
                    to: cssprourl + 'start.jpg'
                },
                {
                    from: new RegExp( cssdevurl + 'com-1.png', 'ig'),
                    to: cssprourl + 'fy-com-1.png'
                },
                {
                    from: new RegExp( cssdevurl + 'com.png', 'ig'),
                    to: cssprourl + 'fy-com.png'
                }
                ]
            }
        }

    });

    // 加载提供"uglify"任务的插件
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-text-replace');

    // 默认任务
    grunt.registerTask('default', ['uglify', 'cssmin', 'replace']);

};

'use strict';

// Load all dependencies
// -----------------------------------------------------------------------
var $    = require('gulp-load-plugins')();
var del  = require('del');
var gulp = require('gulp');
var path = require('path');
var sass = require('gulp-sass')(require('sass'));

// Config
// -----------------------------------------------------------------------
var cfg  = {
    autoprefixer: [
        'last 2 versions',
        '> 1%',
        'ie >= 9',
        'android >= 4.4'
    ],
    clean: [
        'public/assets'
    ],
    css: {
        src: 'resources/assets/scss/**/*.scss',
        dest: 'public/assets/css'
    },
    img: {
        src: 'resources/assets/img/*.{gif,jpg,png}',
        dest: 'public/assets/img'
    },
    js: {
        src: 'resources/assets/js/**/*.js',
        vendors: [
            'node_modules/bootstrap-sass/assets/javascripts/bootstrap/modal.js',
            'node_modules/bootstrap-sass/assets/javascripts/bootstrap/tooltip.js',
        ],
        dest: 'public/assets/js'
    }
};

// Delete everything in dist folder
// -----------------------------------------------------------------------
gulp.task('clean', function() {
    return del(cfg.clean, {
        dot: true
    });
});

// Process CSS files
// -----------------------------------------------------------------------
gulp.task('css', function() {
    return gulp.src(cfg.css.src)
        .pipe($.plumber({errorHandler: $.notify.onError("Error: <%= error.message %>")}))
        .pipe($.sourcemaps.init())
        .pipe(sass({
            precision: 10
        }))
        .pipe($.autoprefixer(cfg.autoprefixer))
        .pipe(gulp.dest(cfg.css.dest))
        .pipe($.cssnano())
        .pipe($.rename({suffix: '.min'}))
        .pipe($.sourcemaps.write('.'))
        .pipe($.size({title: 'css', showFiles: true}))
        .pipe(gulp.dest(cfg.css.dest));
});

// Optimize images
// -----------------------------------------------------------------------
gulp.task('img', function() {
    return gulp.src(cfg.img.src)
        .pipe($.plumber({errorHandler: $.notify.onError("Error: <%= error.message %>")}))
        .pipe($.changed(cfg.img.dest))
        .pipe($.imagemin({interlaced: true, progressive: true}))
        .pipe($.size({title: 'img', showFiles: true}))
        .pipe(gulp.dest(cfg.img.dest));
});

// Process all JS files
// -----------------------------------------------------------------------
gulp.task('js', function() {
    return gulp.src(cfg.js.src)
        .pipe($.plumber({errorHandler: $.notify.onError("Error: <%= error.message %>")}))
        .pipe($.cached('js'))
        .pipe($.eslint())
        .pipe($.eslint.format())
        .pipe($.eslint.failAfterError())
        .pipe($.remember('js'))
        .pipe($.sourcemaps.init())
        .pipe($.changed(cfg.js.dest))
        .pipe(gulp.dest(cfg.js.dest))
        .pipe($.uglify({preserveComments: 'some'}))
        .pipe($.rename({suffix: '.min'}))
        .pipe($.sourcemaps.write('.'))
        .pipe($.size({title: 'js', showFiles: true}))
        .pipe(gulp.dest(cfg.js.dest));
});

// Process all vendor JS files
// -----------------------------------------------------------------------
gulp.task('js:vendors', function() {
    return gulp.src(cfg.js.vendors)
        .pipe($.plumber({errorHandler: $.notify.onError("Error: <%= error.message %>")}))
        .pipe($.sourcemaps.init())
        .pipe($.concat('vendors.js'))
        .pipe(gulp.dest(cfg.js.dest))
        .pipe($.uglify({preserveComments: 'some'}))
        .pipe($.rename({suffix: '.min'}))
        .pipe($.sourcemaps.write('.'))
        .pipe($.size({title: 'js:vendors', showFiles: true}))
        .pipe(gulp.dest(cfg.js.dest));
});

// Run browser-sync and watch files
// -----------------------------------------------------------------------
gulp.task('watch', function() {
    // Watch for changes
    gulp.watch(cfg.css.src, gulp.series('css'));

    gulp.watch(cfg.img.src, gulp.series('img')).on('change', function (e) {
        if (e.type === 'deleted') {
            var filePathFromSrc = path.relative(path.resolve('src/img'), e.path);
            var destFilePath = path.resolve(cfg.img.dest, filePathFromSrc);

            del.sync(destFilePath);
        }
    });

    gulp.watch(cfg.js.src, gulp.series('js')).on('change', function(e) {
        if (e.type === 'deleted') {
            delete $.cached.caches.js[e.path];
            $.remember.forget('js', e.path);
        }
    });
});

gulp.task('size', function () {
    return gulp.src('public/assets/**/*').pipe($.size({title: 'build'}));
});

// Build everything
// -----------------------------------------------------------------------
gulp.task('build', gulp.series('css', 'js:vendors', 'js', 'img', 'size'));

// Default task
// -----------------------------------------------------------------------
gulp.task('default', gulp.series('clean', 'build'));

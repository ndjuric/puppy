var path = require('path');
var gulp = require('gulp');
var debug = require('gulp-debug');
var clean = require('gulp-rimraf');
var concat = require('gulp-concat');
var minifyCss = require('gulp-minify-css');
var notify = require('gulp-notify');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var merge = require('merge-stream');

var onError = function (err) {
    notify.onError({
        title: 'Gulp',
        subtitle: 'Failure!',
        message: 'Error: <%= error.message %>',
        sound: 'Beep'
    })(err);
    this.emit('end');
};

var cssMainBundle = {
    output: 'main.css',
    outputPath: '../public/assets/',
    files: [
        '../resources/assets/css/init.css'
    ],
    watch: [
        '../resources/assets/css/init.css'
    ]
};

var cssDependenciesBundle = {
    output: 'dependencies.css',
    outputPath: '../public/assets/',
    files: [
        '../resources/assets/css/dependency.css'
    ]
};

var cssAppBundle = {
    output: 'app.css',
    outputPath: '../public/assets/',
    files: [
        cssDependenciesBundle.outputPath + cssDependenciesBundle.output,
        cssMainBundle.outputPath + cssMainBundle.output
    ]
};

var jsMainBundle = {
    output: 'main.js',
    outputPath: '../public/assets/',
    files: [
        '../resources/assets/js/init.js'
    ]
};

var jsDependenciesBundle = {
    output: 'dependencies.js',
    outputPath: '../public/assets/',
    files: [
        '../resources/assets/js/dependency.js'
    ]
};

var jsAppBundle = {
    output: 'app.js',
    outputPath: '../public/assets/',
    files: [
        jsDependenciesBundle.outputPath + jsDependenciesBundle.output,
        jsMainBundle.outputPath + jsMainBundle.output
    ]
};

gulp.task('CleanCss', function () {

    var cleanMain = gulp.src(cssMainBundle.outputPath + cssMainBundle.output)
        .pipe(debug({
            title: "Cleaning [CSS] Main: "
        }))
        .pipe(clean({
            force: true
        }));

    var cleanDependencies = gulp.src(cssDependenciesBundle.outputPath + cssDependenciesBundle.output)
        .pipe(debug({
            title: "Cleaning [CSS] Dependencies: "
        }))
        .pipe(clean({
            force: true
        }));

    var cleanApp = gulp.src(cssAppBundle.outputPath + cssAppBundle.output)
        .pipe(debug({
            title: "Cleaning [CSS] App: "
        }))
        .pipe(clean({
            force: true
        }));

    return merge(cleanDependencies, cleanMain, cleanApp);

});

gulp.task('CleanJs', function () {

    var cleanMain = gulp.src(jsMainBundle.outputPath + jsMainBundle.output)
        .pipe(debug({
            title: "Cleaning [JS] Main: "
        }))
        .pipe(clean({
            force: true
        }));

    var cleanDependencies = gulp.src(jsDependenciesBundle.outputPath + jsDependenciesBundle.output)
        .pipe(debug({
            title: "Cleaning [JS] Dependencies: "
        }))
        .pipe(clean({
            force: true
        }));

    var cleanApp = gulp.src(jsAppBundle.outputPath + jsAppBundle.output)
        .pipe(debug({
            title: "Cleaning [JS] App: "
        }))
        .pipe(clean({
            force: true
        }));

    return merge(cleanMain, cleanDependencies, cleanApp);

});

gulp.task('BundleCss', ['CleanCss'], function () {

    var bundleMain = gulp.src(cssMainBundle.files)
        .pipe(debug({
            title: "Bundling [CSS] Main: "
        }))
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(sass())
        .pipe(minifyCss({
            keepSpecialComments: 0,
            processImport: false,
            rebase: false
        }))
        .pipe(concat(cssMainBundle.output))
        .pipe(gulp.dest(cssMainBundle.outputPath));

    var bundleDependencies = gulp.src(cssDependenciesBundle.files)
        .pipe(debug({
            title: "Bundling [CSS] Dependencies: "
        }))
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(minifyCss({
            keepSpecialComments: 0,
            processImport: false,
            rebase: false
        }))
        .pipe(concat(cssDependenciesBundle.output))
        .pipe(gulp.dest(cssDependenciesBundle.outputPath));

    return merge(bundleDependencies, bundleMain);

});

gulp.task('BundleAppCss', ['BundleCss'], function () {

    return gulp.src(cssAppBundle.files)
        .pipe(debug({
            title: "Bundling [CSS] App: "
        }))
        .pipe(plumber({
            errorHandler: onError
        }))
        //.pipe(minifyCss({ keepSpecialComments: 0, processImport: false, rebase: false }))
        .pipe(concat(cssAppBundle.output))
        .pipe(gulp.dest(cssAppBundle.outputPath));

});

gulp.task('BundleJs', ['CleanJs'], function () {

    var bundleMain = gulp.src(jsMainBundle.files)
        .pipe(debug({
            title: "Bundling [JS] Main: "
        }))
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(concat(jsMainBundle.output))
        //.pipe(uglify())
        .pipe(gulp.dest(jsMainBundle.outputPath));

    var bundleDependencies = gulp.src(jsDependenciesBundle.files)
        .pipe(debug({
            title: "Bundling [JS] Dependencies: "
        }))
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(concat(jsDependenciesBundle.output))
        //.pipe(uglify())
        .pipe(gulp.dest(jsDependenciesBundle.outputPath));

    return merge(bundleMain, bundleDependencies);

});

gulp.task('BundleAppJs', ['BundleJs'], function () {

    return gulp.src(jsAppBundle.files)
        .pipe(debug({
            title: "Bundling [JS] App: "
        }))
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(concat(jsAppBundle.output))
        .pipe(uglify())
        .pipe(gulp.dest(jsAppBundle.outputPath));

});

gulp.task('watch', ['build'], function () {
    gulp.watch(cssMainBundle.watch, ['BundleAppCss']);
    gulp.watch(cssDependenciesBundle.files, ['BundleAppCss']);
    gulp.watch(jsMainBundle.files, ['BundleAppJs']);
    gulp.watch(jsDependenciesBundle.files, ['BundleAppJs']);
});

gulp.task('build', ['BundleAppCss', 'BundleAppJs']);
gulp.task('default', ['watch']);

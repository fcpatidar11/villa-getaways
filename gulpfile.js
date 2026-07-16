const { src, dest, watch, series } = require('gulp');
var sass = require('gulp-sass')(require('sass'));
const postcss = require('gulp-postcss');
const cssnano = require('cssnano');
const autoprefixer = require('autoprefixer');
const rename = require('gulp-rename');
const terser = require('gulp-terser');
const concat = require('gulp-concat');

// const browsersync = require('browser-sync').create();

// Sass Task
function scssTask() {
    return src("assets/scss/**/*.scss")
        .pipe(sass())
        .pipe(dest('assets/css', { sourcemaps: '.' }))
        .pipe(postcss([cssnano()]))
        .pipe(rename({ suffix: ".min" }))
        .pipe(postcss([autoprefixer(), cssnano()]))
        .pipe(dest('assets/css', { sourcemaps: '.' }));
}

//JavaScript Task
// function jsTask() {
//     return src('assets/js/*.js', { sourcemaps: false })
//         .pipe(concat('script.js'))
//         .pipe(dest('assets/js'))
//         .pipe(terser())
//         .pipe(concat('script.min.js'))
//         .pipe(dest('assets/js'))
// }

// Browsersync Tasks
// function browsersyncServe(cb){
//   browsersync.init({
//     server: {
//       baseDir: '.'
//     }
//   });
//   cb();
// }

// function browsersyncReload(cb){
//   browsersync.reload();
//   cb();
// }

// Watch Task
function watchTask() {
    // watch('*.html', browsersyncReload);
    watch(['assets/scss/**/*.scss', 'assets/js/**/*.js'], series(scssTask));

    //watch(['sass/**/*.scss'], series(scssTask));
}

// Default Gulp task
exports.default = series(
    scssTask,
    // jsTask,
    watchTask
);



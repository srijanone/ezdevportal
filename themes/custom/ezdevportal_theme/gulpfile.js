var gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
var cleanCSS = require('gulp-clean-css');
var sassGlob = require('gulp-sass-glob');
var browserSync = require('browser-sync').create();
var sourcemaps = require('gulp-sourcemaps');

gulp.task('sass', function (done) {
  gulp.src('sass/**/*.scss')
    .pipe(sassGlob())
    .pipe(sourcemaps.init())
    .pipe(sass({}).on('error', sass.logError))
    .pipe(cleanCSS())
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('css/'));
    done();
});

gulp.task('watch', function() {
  gulp.watch('sass/**/*.scss',gulp.series('sass'));
});



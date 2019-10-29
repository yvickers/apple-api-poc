'use strict';
var gulp = require('gulp'),
	sass = require("gulp-sass"),
	autoprefixer = require('gulp-autoprefixer');

gulp.task('sass', function () {
	return gulp.src('htdocs/assets/scss/*.scss')
		.pipe(autoprefixer())
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('htdocs/assets/css'));
});

gulp.task('sass:watch', function() {
	gulp.watch('htdocs/assets/scss/*.scss', gulp.series('sass') );
});

gulp.task('default', gulp.parallel('sass', 'sass:watch') );
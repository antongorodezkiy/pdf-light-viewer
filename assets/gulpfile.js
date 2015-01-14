var gulp = require('gulp'),
    csso = require('gulp-csso'),
    concat = require('gulp-concat'),
	vendorCss = [
		'bower_components/fontawesome/css/font-awesome.min.css'
	],
	vendorJs = [

	];

gulp.task('vendorcss', function() {
	// Fonts
    gulp.src(['bower_components/fontawesome/fonts/fontawesome-webfont.*'])
    	.pipe(gulp.dest('fonts/'));

	gulp.src(vendorCss)
		.pipe(csso())
		.pipe(concat('vendor.css'))
		.pipe(gulp.dest('css/'));
});


gulp.task('vendorjs', function() {
	return gulp.src(vendorJs)
		.pipe(concat('vendor.js'))
		.pipe(gulp.dest('js/'));
});

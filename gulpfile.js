import path from 'path';
import { src, dest, watch, series } from 'gulp';
import * as dartSass from 'sass';
import gulpSass from 'gulp-sass';
import terser from 'gulp-terser';

const sass = gulpSass(dartSass);

const paths = {
  scss: 'src/scss/**/*.scss',
  js: 'src/js/**/*.js'
}

export function js( done ) {
  src(paths.js)
    .pipe( terser() )
    .pipe( dest('./public/build/js') )
  done()
}

export function css( done ) {
  src(paths.scss, {sourcemaps: true})
    .pipe( sass({
      outputStyle: 'compressed'
    }).on('error', sass.logError) )
    .pipe( dest('./public/build/css', {sourcemaps: '.'}) )
  done()
}

export function dev() {
  watch("src/scss/**/*.scss", css)
  watch("src/js/**/*.js", js)
}

export default series( js, css, dev )
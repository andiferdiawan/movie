/**
 * First we will load all of this project's JavaScript dependencies:
 * jQuery/Bootstrap for UI widgets (navbar, dropdowns) and axios for the
 * AJAX calls the movie pages make back to Laravel.
 */

require('./bootstrap');

/**
 * Page-specific modules. Each one checks for the DOM elements it needs
 * and simply does nothing when they are not present, so it is safe to
 * load all of them on every page.
 */

import './lazyload';
import './movies';
import './movie-detail';
import './favorites';

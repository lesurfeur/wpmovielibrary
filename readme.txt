=== WPMovieLibrary ===
Contributors: askelon
Donate link: http://wpmovielibrary.com/contribute/#donate
Tags: movie, movies, movie database, movie library, movie collection, cinema, movie genre, actor
Requires at least: 3.6
Tested up to: 4.0-beta.2
Stable tag: 1.2-beta.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WPMovieLibrary is an advanced movie library managing plugin to turn your WordPress Blog into a Movie Library.

== Description ==

The best way to manage your personnal movie library. Handle collections of movies, automatically fetch metadata, images and posters, create collections by mass importing lists of movie titles. Share your favorite or latest seen movies, manage collections of genres, actors or crew, fetch overviews, casts and posters in a single clic, show the world your love for movies. WPMovieLibrary uses TheMovieDB API (see http://themoviedb.org) to fetch metadata about your movies: director, genres, actors, producer, country, languages… You can also import images and posters directly to your blog to illustrate your library. WPMovieLibrary has an import tool to import movies by the dozen by simply listing titles.

Features:

*    Movie Custom Post Type
*    One click metadata import: title, overview, production, country(ies), language(s), runtime, release date, director of photography, composer, writer, screenwriter, cast & crew
*    One click images and posters download
*    Select your poster among a list of all available posters
*    Movie Status (Available, Scheduled, Loaned, Unavailable)
*    Movie Media (DVD, VOD, Blu-ray, Cinema, …)
*    Movie Rating
*    Bulk edit Movie Ratings, Medias and Statuses
*    Collection, Genre and Actor Custom Taxonomies to filter and organize your library
*    Mass import using lists of movie titles
*    Import Queue to easily import big collections in a couple of clicks
*    Recent Movies, Most Rated Movies Widgets
*    Movies by Status and Movies by Media Widgets
*    Collections, Genres  and Actors Widgets
*    Archive pages for Taxonomies
*    Complete configuration of the data you want to show on your blog
*    Plugin Custom Dashboard
*    Much more!


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use it!

== Frequently Asked Questions ==

= Is this legal? =

Absolutely. Unlike most similar plugins, WPMovieLibrary uses TheMovieDB instead of IMDb due to the lack of proper official and legal API.

= Do I need an API key? =

No you don't. You can use your own TMDb API key for better results, but that is not required per se for WPMovieLibrary to work, the plugin can use its own API key if you don't provide one.

= How can I import metadata? =

Create a new Movie, input the movie title in search field below the content editor, hit "Search". If your search brings multiple results you will be presented a list of possible matches, else all fields will be automatically filled and you movie's poster automatically imported.

= How can I import my list of DVDs? =

Use the import tool: simply paste a list of titles, all your movies will be created instantly. Now you can import metadata one-by-one or group the search, and then import directly the movies or add it to the queue for later final import.

== Screenshots ==

1. Plugin custom dashboard
2. All movies list
3. Edit movies
4. Download posters
5. Import by lists of titles
6. Imported list
7. Import queue

== Changelog ==

= 1.1.2 =

* Fix - PHP Warnings with shortcodes when no attribute is passed
* Tweak - Movie Images shortcodes basic styling

= 1.1.1 =

* Fix - Default poster for movies only
* Fix - PHP Warning with runtime and release date

== 1.1.0 ==

* Features - Implements shortcodes
* Features - Create 8 new shortcodes
* Features - Create 16+ aliases for specific shortcodes
* Features - New default poster
* Tweak - Better display of directors, actors and genres in fronted, metadata are now matched against existing taxonomies to provide relevant links
* Tweak - Custom archive pages pagination
* Tweak - Custom archive pages specific titles
* Tweak - Show a dash instead of empty metadata in frontend
* Tweak - Add default runtime and release date/time formats
* Tweak - Limited number of items in Collections and Actors Widgets to lighten loading time
* Tweak - Updated default poster
* Dev - Better use of filters to display movie metadata and details and handle shortcodes and metadata aliases
* Dev - Cleaner Widgets views
* Fix - JavaScript bug in the movie editor preventing from manually setting a featured image after metadata import
* Fix - missing links on directors when a movie has two or more directors
* Fix - frontend PHP Warning when movie runtime is empty
* Fix - JavaScript bug when setting collections in movie editor
* Fix - Warnings on plugin activation when updating settings

= 1.0.2 =
* Fix - Markup error in readme
* Fix - Wrong PHP version requirement (PHP 5.3 required, not 5.2) - Thanks Ravavamouna

= 1.0.1 =
* Dev - Check plugin requirements before loading
* Fix - Missing status icon in frontend
* Fix - JS Search error in Movie Meta metabox
* Fix - Missing nonce in importer
* Fix - 12-hour time format bug on runtime
* Fix - JS error with custom dashboard widgets
* Tweak - Add 20+ new language for the API
* Tweak - New item in Dashboard "Right Now" Widget
* Tweak - WordPress < 3.8 icons and styling update
* Tweak - Backend CSS
* Tweak - Add an admin notice in case of missing Archive page
* Tweak - Language packs updated

== 1.0.0 ==
* First stable release

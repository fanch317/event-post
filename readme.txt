=== Event post ===
Contributors: bastho, unecologeek, ecolosites
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RR4ACWX2S39AN
Tags: calendar, event, events, Post, posts, ics, ical, date, geolocalization, coordinates, gps, widget, map, openstreetmap, agenda, weather, multisite, native, gutenberg
Requires at least: 4.8
Tested up to: 4.9.6
Stable tag: 5.2
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The only WordPress plugin using native posts as full calendar events with begin and end date, geolocation, color and weather.

== Description ==
Adds some meta-datas to posts to convert them into full calendar events.
Each event can be exported into ical(.ics), outlook(vcs), or Google Calendar.
Geolocation works thanks to openstreetmap.

It can also fetch the weather, but doesn't bring the sun :)

[event-post.com](https://event-post.com/)

= Post metas =

**Date attributes**

* Begin date-time
* End date-time
* Color

**Location attributes**

* Address
* GPS coordinates

**Weather attribute** (for a given location and date if possible)

* Weather
  - Temperature
  - Weather

= Full documentation =

[Plugins/themes developpers documentation](http://event-post.com/docs/event-post/)

## Shortcodes
The plugin comes with several shortcodes wich allows to:

* `[events_list]`: display a list of events
* `[events_map]`: display a map of events
* `[events_cal]`: display a calendar of events
* `[event_details]`: display a detail of the current event
* `[event_term]`: display the date range of a given term based on all events it contains

### Available options:
#### [events_list]
##### Query parameters
* **nb=5** *(number of post, -1 is all, default: 5)*
* **future=1** *(boolean, retreive, or not, events in the future, default = 1)*
* **past=0** *(boolean, retreive, or not, events in the past, default = 0)*
* **cat=''** *(string, select posts only from the selected category, default=null, for all categories)*
* **tag=''** *(string, select posts only from the selected tag, default=null, for all tags)*
* **geo=0** *(boolean, retreives or not, only events wich have geolocation informations, default=0)*
* **order="ASC"** *(string (can be "ASC" or "DESC")*
* **orderby="meta_value"** *(string (if set to "meta_value" events are sorted by event date, possible values are native posts fileds : "post_title","post_date" etc...)*

##### Display parameters

* **thumbnail=""** *(Bool, default:false, used to display posts thumbnails)*
* **thumbnail_size=""** *(String, default:"thmbnail", can be set to any existing size : "medium","large","full" etc...)*
* **excerpt=""** *(Bool, default:false, used to display posts excerpts)*
* **style=""** *(String, add some inline CSS to the list wrapper)*
* **type=div** *(string, possible values are : div, ul, ol default=div)*
* **title=''** *(string, hidden if no events is found)*
* **before_title="&lt;h3&gt;"** *(string (default &lt;h3&gt;)*
* **after_title="&lt;/h3&gt;"** *(string (default &lt;/h3&gt;)*
* **container_schema=""** *(string html schema to display list)*
* **item_schema=""** *(string html schema to display item)*

example: `[events_list future=1 past=1 cat="actuality" nb=10]`

container_schema default value:

>	&lt;%type% class="event_loop %id% %class%" id="%listid%" style="%style%" %attributes%&gt;
>		%list%
>	&lt;/%type%&gt;
>


item_schema default value:

>	&lt;%child% class="event_item %class%" data-color="%color%"&gt;
>	      	&lt;a href="%event_link%"&gt;
>	      		%event_thumbnail%
>	      		&lt;h5>%event_title%&lt;/h5&gt;
>	      	&lt;/a&gt;
>     		%event_date%
>      		%event_cat%
>      		%event_location%
>      		%event_excerpt%
>     &lt;/%child%&gt;
>

####[events_map]

* **nb=5** *(number of post, -1 is all, default: 5)*
* **future=1** *(boolean, retreive, or not, events in the future, default = 1)*
* **past=0** *(boolean, retreive, or not, events in the past, default = 0)*
* **cat=''** *(string, select posts only from the selected category, default=null, for all categories)*
* **tag=''** *(string, select posts only from the selected tag, default=null, for all tags)*
* **tile=''** *(string (default@osm.org, OpenCycleMap, mapquest, osmfr, 2u, satelite, toner), sets the map background, default=default@osm.org)*
* **title=''** *(string (default)*
* **zoom=''** *(number or empty (default, means fit to points)*
* **before_title="&lt;h3&gt;"** *(string (default &lt;h3&gt;)*
* **after_title="&lt;/h3&gt;"** *(string (default &lt;/h3&gt;)** **thumbnail=""** * (Bool, default:false, used to display posts thumbnails)*
* **excerpt=""** *(Bool, default:false, used to display posts excerpts)*
* **list=""** *(String ("false", "above", "beyond", "right", "left") default: "false", Display a list of posts)*

example: `[events_map future=1 past=1 cat="actuality" nb="-1"]`

####[events_cal]

* **cat=''** *(string, select posts only from the selected category, default=null, for all categories)*
* **date=''** *(string, date for a month. Absolutly : 2013-9 or relatively : -1 month, default is empty, current month*
* **datepicker=1** *(boolean, displays or not a date picker*
* **mondayfirst=0** *(boolean, weeks start on monday, default is 0 (sunday)*
* **display_title=0** *(boolean, displays or not events title under the day number)*

example: `[events_cal cat="actuality" date="-2 months" mondayfirst=1 display_title=1]`

####[event_details]

* **attribute** *string (date, start, end, address, location). The default value is NULL and displays the full event bar*


= Hooks =
<a id="hooks"></a>
#### Filters
* eventpost_get
* eventpost_getsettings
* eventpost_item_scheme_entities
* eventpost_item_scheme_values
* eventpost_list_shema
* eventpost_listevents
* eventpost_multisite_get
* eventpost_multisite_blogids
* eventpost_params
* eventpost_printdate
* eventpost_printlocation
* eventpost_retreive

#### Actions
* before_eventpost_generator
* after_eventpost_generator
* eventpost_getsettings
* eventpost_settings_form
* eventpost_after_settings_form

== Installation ==

1. Upload `event-post` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress admin
3. You can edit defaults settings in Settings > Event post

== Frequently asked questions ==

= Is the plugin free ? =
Yes, and it uses only open-sources : openstreetmap, openlayer, jquery

= How do I enable weather feature ? =
Weather feature uses openweathermap.org api.

You have to create an account and generate an API key at http://openweathermap.org/price

I have no interest in openweathermap.org, I personally use the free plan.

= Is there any limitation for the weather feature ? =
Openweathermap.org provides a free plan limited to 60 requests per minute.

You can also subscribe to paid plan, I don't care.


== Screenshots ==

1. Single page
2. Editor interface
3. Map

== Changelog ==

= 5.2.1 =

- Fix  crash when Gutenberg is not activated

= 5.2 =

News:

- List of events beside the map
- Use ES6 library for Gutenberg Blocks

Fixes:

- Upgrade OpenLayer to version 4.6.5
- Fix map issue: unrecognized Animation/Pan
- Fix empty permalink detection thanks to @maxoud and @akalwp
- Cleanup textdomain in block js files
- Fix Timezone issue in Gcal export

= 5.1 =

News:

- Adds Gutenberg block support for folowing shortcodes: List, Map, Calendar
- Allow to display posts title in Calendar
- Upgrade OpenLayer to version 4.6.4
- Adds twitter labels for date and location

Fixes:

- Load beautifull css and js files when WP_DEBUG is enabled
- Wording and typo fixes
- Fix some PHP Warnings

= 5.0.3 =

- i18n: Contextualize i18n for "from" and "to"
- Fix: Pass CTZ param to GoogleCalendar only if timesone is set
- Fix: Explicitly get published events. (prevents from displaying drafts over admin/ajax URLs

= 5.0.2 =

- Add: CSS class for past events in calendar
- Add: Number of events in search results
- Fix: Ensure correct response for getLatLong Ajax call

= 5.0.1 =

Retro-compatibility for php < 5.5, remove fatal error "Can't use function return value in write context"
http://php.net/manual/en/function.empty.php

= 5.0 =

- Added search form shortcode/widget
- Quick and bulk edit
- Custom CSS support: file URL or local theme file
- Use native settings API

Fixes

- Fix incompatibility with PDOS / remove warning

= 4.5.1 =

- Fix events dated at Epok when GMT is not well parsed
- Move supported tiles to https (OSM, Humanitarian, OSM-fr)

= 4.5 =

- Add option to hide event-bar
- Add Tiles: **Humanitarian**, **Positron** and **Dark matter**
- Add Retina (x2) title support
- Add setting link in the plugins page
- Add Map tiles attribution support
- Add Add zoom option to SC/Widget
- Add support for shortcake term selector (requires [shortcake](https://wordpress.org/plugins/shortcode-ui/) 0.7+)

- Better support of timezones in VCS/ICS exports
- Fix wrong parameter in calendar shortcode UI register
- Fix displaying icons in wrong post's title in single pages
- Fix "Uncaught Error: [] operator not supported for string, PHP7 compliance
- Correctly checks for content filtering, as mentionned in https://developer.wordpress.org/reference/hooks/the_content/#usage

= 4.4 =
**Edit page**

- Improves "Location" edit box UI
    - click to drag the map
    - lighter search form
- Improves "Date" edit box UI
    - new all day toggle option
    - new native WP datepicker
    - Better and obvious check that end date is after begining
    - lighter UI

**Calendar Widget**

- Fix calendar widget title not displayed
- Fix today highlithing in calendar when day of month is lower than 10

**Other fixes**

- Fix openweahtermap URL in settings page
- Fix date pickers preview in settings page

= 4.3.4 =
- fix typo in cache management, related to https://wordpress.org/support/topic/location-deleted-after-update-a-post
- check twice if event permaling is filled. fix https://wordpress.org/support/topic/event_link-not-working

= 4.3 =

End users new features:

- Introduce initial Weather support
- Introduce child events feature
- Allows to define current location as coordinates of an event
- Add loader icon for maps on front
- Add map icon in the editor
- Add address in the admin posts-list
- Do not validate post form when hitting enter from the location search field

Developers new features:

- New actions `evenpost_init`, `eventpost_get_single`, `eventpost_custom_box_date`, `eventpost_custom_box_loc` and `eventpost_getsettings_action`
- New filter `eventpost_default_list_shema`
- New `$post` parameter to `eventpost_item_scheme_values` filter
- Update i18n file

Bug fixes:

- Almost perfect accessibility in calendar
- Cleaner HTML indent
- Fix bad HTML syntax in event list
- Fix wrong target for calendar container
- Fix some CSS bad syntaxes
- Some corrections in readme.txt

= 4.2.4 =
- Fix JS bugs introduced in 4.2

= 4.2 =
release date: mar. 24 2016

* Add generic wrapper functions
* Drag map instead of point in editor mode
* Code refactoring
   - externalize shortcode management in a separated file
   - store maps in a json file instead of an old csv
   - add lot of documentation
   - some code cleanup
* Update OpenLayer to 3.14
* Fix javascript bug causing some issues with other plugins

= 4.1.1 =
release date: feb. * 2016

* Increases accessibility
    - Add alternative texts
    - remove default color for calendar widget
* Remove french locale since it's available at https://translate.wordpress.org/projects/wp-plugins/event-post/dev
* Fix PHP warning in map widget

= 4.1 =
release date: feb. 4th 2016

* Update OpenLayer to 3.11
* Position preview in admin, drag and drop feature
* Correctly parse time-zone for date displaying
* Add timezone to Google calendar links
* Only load JS and CSS when needed in the admin
* Initial work on dates of a term
* Fix wrong permalink in calendar widget

= 4.0.1 =
release date: nov. 12 2015

* Added Localization for brazilian portuguese thanks to lipemesquita

= 4.0.0 =
release date: oct. 27 2015

* Make replacement by "today" optional (prevent cache plugins issues)
* More standard setting page
* Only show events on dashboard right now if they're more than 0
* Date and time formats harmonization
* Add `.pot` file and make match text-domain to plugin slug
* Only displays expandable map in the content of the current single
* Rename some js and css files

= 3.9.0 =
release date: oct. 6 2015

* Add width and height options in map widget
* Add thumbnail + size option in both list, map and calendar widgets
* Add some [hooks](https://wordpress.org/plugins/event-post/other_notes/#hooks)
* Code cleanup

= 3.8.2 =
* fix bug in past events fetching

= 3.8.1 =
release date: sept. 23 2015

* Fix time zone issue
* Add filter hook to list_events()
* Add some explicit CSS classes to elements

= 3.8.0 =
release date: sept. 22 2015

* Some wording
* Add an option for time format
* wrap export buttons in event list
* Fix javascript bug in map when no  default tile is set
* Remove dummy javascript debug log

= 3.7.0 =
release date: sept. 15 2015

* Update OpenLayer to version 3.9
* Add Map interaction options (MouseWheelZoom, PinchZoom...)
* Add option for datepicker UI
* Add eligible post types option
* Better performances
* Fix PHP Warnings

= 3.6.8 =
* update french translation

= 3.6.7 =
* more WP 4.3 compliant

= 3.6.6 =
* Fix PHP warnings

= 3.6.5 =
release date: aug. 14 2015

* Fix bad html syntax
* Make plugin WP 4.3 compliant
* Add lot of comments
* Update swedish translation thanks to @mepmepmep

= 3.6.4 =
* Fix displaying of dates in map

= 3.6.3 =
* Better multisite sort
* Fix multiple dates
* Fix CSS issue between ical export buttons and category

= 3.6.2 =
* Fix "get_plugin_data" error, finally remove the function.

= 3.6.1 =
* Fix "get_plugin_data" error

= 3.6.0 =
* Add version to static files (JS/CSS) to prevent from local cache problems
* Add sort parameters in shortcode UI
* Fix language on date-picker
* Fix bug (missing dates) for multi-site functions
* Code cleanup, Retro compatibility to PHP<5.3

= 3.5.4 =
* Fix empty date storing
* Re-add quarters in time

= 3.5.3 =
* Improve accessibility, use of native colors from the theme for links
* More hookable ajax URL for calendar
* Upgrade IT locale
* Fix php warnings
* Fix shortcake compatibility in pages

= 3.5.2 =
* Fix missing files in last commit

= 3.5.0 =
* Add **event_details** shortcode
* Add integration with shotcake (ShortCodes UI)
* Add Optional event icons in the loop
* Optimize UI : New date-picker, separated date and address custom boxes
* Add statistics in dashboard glance items
* Code optimization
* Update IT localization by pgallina

= 3.4.2 =
* Fix: remove PHP warnings
* Fix: JS script not loaded when the "calendar widget" is alone

= 3.4.1 =
* Fix: remove PHP warnings

= 3.4.0 =
* Add: Whole category ICS feed (link available in list widget, for future events)
* Fix: JS was not loaded in single events since last version
* Fix: Strict Standards warning, reported by argad
* Fix: Dependence of OpenLayer library not needed by calendar widget, reported by p1s1

= 3.3.0 =
* Add: eventpost_list_shema filter
* Add: Global container/item shema settings
* Add: Security improvement in settings management
* Fix: Load scripts only of needed

= 3.2.4 =
* Fix: Previous fix fix

= 3.2.3 =
* Fix: Custom icons fix

= 3.2.2 =
* Fix: Category filter
* Fix: Add max zoom

= 3.2.1 =
* Fix: Event list widget : missing title

= 3.2.0 =
* Add: Italian localization, thanks to NewHouseStef

= 3.1.1 =
* Fix: Future/past display style

= 3.1.0 =
* Add: Save default settings to improve performances
* Add: More options in list and map widgets

= 3.0.0 =
* Update to OpenLayer3
* Add: Responsive support
* Add: Satelite and Toner view
* Add: `cat` attributes now accepts multiple categories values ( cat="1,2,3" )
* Add: Custom markers directory for developpers
* Add: Global "event bar position" option : before or after the single content
* Fix: Cleaner settings page

= 2.8.12 =
* Add : Swedish localization, thanks to Mepmepmep

= 2.8.11 =
* Fix : PHP warnings on empty dates

= 2.8.10 =
* Fix : 00 minutes bug

= 2.8.9 =
* Fix : Bug fix
* Change : Multisite support is no more a separated plugin

= 2.8.8 =
* Fix : Empty date error

= 2.8.7 =
* Fix : JS error in minified osm-admin.js file

= 2.8.6 =
* Fix : Error while retreiving the excerpt

= 2.8.5 =
* Add : Setting to print/hide link for events with empty content
* Fix : Check content with queried object instead of global $post
* Fix : Bug in calendar animations

= 2.8.4 =
* Fix : Optimize JS in admin side
* Add : French and chinese localisation for date-picker
* Add : Minify CSS

= 2.8.3 =
* Fix : bug fix

= 2.8.2 =
* Fix : apply content filter most later

= 2.8.1 =
* Fix : content filter bug on home page

= 2.8.0 =
* Add : attributes to events_list shortcode :
   * thumbnail=(true/false)
   * thumbnail_size=thumbnail
   * excerpt=(true/false)
   * container_schema (documentation coming soon)
   * item_schema (documentation coming soon)
* Add : Usage of the event color for single details
* Enhance : Event information form UI
* Fix : Re-check if end date is after begin date
* Fix : CSS adjustments
* Fix : CSS adjustments
* Fix : Prevent from filters applying "the_content" on another thing than the current post content


= 2.7.1 =
* Fix : Really check all blogs when using "blogs=all" in shortcodes. May cause memory limit on big networks

= 2.7.0 =
* Add : Multi-site event list support
* Add : Integration of several hooks
* Add : Map widget
* Add : Parameters to display or not export buttons
* Add : Native WP icons for map and calendar items
* Add : data-color in list items
* Fix : Event's first day not shown in calendar
* Fix : Use of minified JS files

= 2.6.0 =
* Add : order and orderby parameters for shortcode [events_list]

= 2.5.0 =
* Add : tag and style parameters for shortcode [events_list]

= 2.4.1 =
* Fix : Parameters bug in export files

= 2.4.0 =
* Add : Calendar widget/shortcode

= 2.3.3 =
* Add : Improve address search UI
* Fix : Address search bug fix

= 2.3.2 =
* Add : make the function "EventPost::get_events" usable with an array as param
* Fix : Use of https links
* Fix : Change license from CC BY-NC to GPLv3

= 2.3.1 =
* Fix : OSM map link error

= 2.3.0 =
* Add : update openlayer version to 2.13.1
* Add : change Map UI buttons
* Add : Shortcode editor
* Fix : Minor JS bug

= 2.2.4 =
* Fix : Quick edit was removing date and geo datas
* Fix : PHP Warning

= 2.2.3 =
* Add : Title, before_title and after_title attributes to shortcode functions
* Fix : Do not display empty title in widget

= 2.2.2 =
* Add : add custom box to all post-types

= 2.2.1 =
* Fix : bad output

= 2.2.0 =
* Add : Admin settings page : choose a date format and a default map background
* Add : Tile option for map shortcode, select a map background for a particular map
available maps : default@osm.org, OpenCycleMap, mapquest, osmfr, 2u

= 2.1.0 =
* Add : ajax loader icon for address search
* Add : Event and location columns in posts list
* Add : widget description
* Add : place icon when available for address search
* Fix : Empty display_name property in address search

= 2.0.0 =
* Add: Category option for widgets and shortcodes
* Add: Force end date to be greater than begin date
* Add: Separate search field for GPS and address
* Fix: Wrong parameter for widget options
* Fix: Load jquery datetimepicker only if not supported by the browser

= 1.1.0 =
* Add: Width & height properties in the '[events_map]' shortcode
* Add: Allow multiple maps on the same page
* Fix: Same ID in multiple DOM elements bug fix
* Fix: Some W3C standard corrections

= 1.0.0 =
* Plugin creation

== Upgrade notice ==

= 4.1.1 =
Increases accessibility

= 3.6.2 =
* Fix "get_plugin_data" error.

= 3.5.0 =
* New options are available for: icons in the loop, default position of the admin boxes
* New shortcode [event_details] is available
* Support for Shortcake (Shortcode UI plugin)

= 2.7.0 =
* The event meta box is no more displayed for non posts items such as pages or custom post-types
* Please active the multisite plugin in order to allow your users to browse events from the network

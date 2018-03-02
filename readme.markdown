# UCF Downtown Theme - [University of Central Florida Downtown Orlando, FL](http://www.ucf.edu/downtown/)

WordPress theme built off of UCF's Generic theme for UCF's Downtown Orlando Site.


## Installation Requirements:
* GravityForms
* Advanced Custom Fields Pro


## Deployment

This theme relies on Twitter's Bootstrap framework. UCF's fork of the Bootstrap project (http://github.com/UCF/bootstrap/) is added as submodule in static/bootstrap. Bootstrap must be initialized as a submodule with every new clone of this theme repository.

#### Initializing Bootstrap with a new clone:
1. Pull/Clone the theme repo
2. From the theme's root directory, run `git submodule update --init static/bootstrap`
3. From the static/bootstrap directory, run `git checkout master`.  Make sure a branch has been checked out for submodules as they will default to 'no branch' when cloned.  If you're developing a new theme off of Generic and have created a new Bootstrap branch (see the Development section), checkout that branch instead.

#### Alternative method using Git v1.6.5+:
1. Run `git clone` using the `--recursive` parameter to clone the repo with all of its submodules; e.g. `git clone --recursive https://github.com/UCF/Wordpress-Generic-Theme.git`
2. From the static/bootstrap directory, run `git checkout master`.  Make sure a branch has been checked out for submodules as they will default to 'no branch' when cloned.


## Development
This project relies on Bower and Codekit to develop CSS and JavaScript.

This theme relies on Twitter's Bootstrap 2 framework which will be installed by Bower.

### Setup

#### Bower
Bower is set up in this repo for handling all third-party front-end
dependencies.  See [their website](http://bower.io/) for more information and
installation instructions.

Whenever a third-party package needs to be included in this theme, run
`bower install --save <package>` from the theme's root directory to download
the package's contents to the `static/bower_components/` directory and update
the Bower dependencies list (`bower.json`).

Whenever this theme is completed, *NO* Bower components should be pushed up to
the repo. Bower components should only be installed on developer machines and only
the minifed CSS and JavaScript files should be checked into the repo. To install
Bower components type `bower install` from the theme's root directory. **Note:
Bower packages must be installed in `static/bower_components/` BEFORE
attempting to modify any theme-specific SASS or JavaScript files.**

No files in the `bower_components` directory should be modified directly.  If a
package really needs a custom modification, consider forking the package and
possibly hosting it on Github, then installing the package via Bower by Github
URL.

#### CodeKit
A CodeKit config file is included in this theme's root directory.  [CodeKit]
(http://incident57.com/codekit/) is a Mac-only tool that handles Sass and
JavaScript compilation and minification, and also has browser refreshing tools
and Bower compatibility built-in.

CodeKit's config file is set up to make CodeKit compile (almost) all of our
theme-specific SASS and JavaScript together with our Bower components to
produce single, minified files for fewer client-side HTTP requests.

##### CSS Asset Compiling/Minifying
Whenever a theme-specific SASS file is updated (and CodeKit is running), all
SASS files should compile together with the third-party packages defined in
`static/scss/style.scss`.  The final compiled file is saved as
`static/css/style.min.css`.

Assets defined in `style.scss` should always be included in the following
order:
* `_variables.scss`
* Third-party vendor assets
* `_base.scss`

_bootstrap2.scss file is used instead on including Bootstrap's bower_components file directly.
The reason for this is because the directory import structure is not compatible with
how CodeKit works. The _bootstrap2.scss file fixes this issue. If a new version of
Bootstrap is used then check whether _boostrap2.scss needs to be modified or removed.

Bootstrap 2 glyphicons are copied from the bower_components directory and placed
in `./static/img/` using a Codekit hooks. Make sure that the `Copy Glyphicons To Static`
hook is enabled by going to the project -> gear icon -> hooks and checking the box.
By default Codekit disables all new hooks to make sure the script doesn't do anything
malicious.

Admin-specific CSS is not compiled/minified.

##### JavaScript Asset Compiling/Minifying
Whenever a theme-specific JavaScript file is updated (and CodeKit is running),
`static/js/script.js` will be prepended with any included scripts at the top
of the file (using `@codekit-prepend`) and is minified and saved to
`static/js/script.min.js`.

Assets defined in `script.js` should always be included in the following order:
* Third-party vendor assets
* `webcom-base.js`
* `generic-base.js`

All files prepended to `script.js` should work independently and can be
registered as separate scripts with WordPress for debugging purposes if
necessary.

Admin-specific JS is not compiled/minified.

#### SASS
Non-admin, theme-specific styles for this theme are saved in `static/scss/`
for cleaner, more organized style definitions (as opposed to managing all
of our site's styles from a single file).  We use CodeKit to combine all the
SASS files together and compile the final code into actual CSS
(`static/css/style.min.css`).

Individual SASS partials are combined **in a specific order**--see
`static/scss/_base.scss`.  Note that `_variables.scss` is not included in this
file; it is included in the final minified file before third-party packages
are included, so that we can override SASS variables as necessary before
processing.

As a general rule, SASS partials should be combined in order from the most
generic/abstract to the most specific.

When writing view-specific styles, try to follow WordPress template naming
conventions-- i.e., styles that are specific to the 'Person' post type should
be in a file named `_views-single-person.scss`


## Important files/folders:

### functions/base.php
Where functions and classes used throughout the theme are defined.

### functions/config.php
Where Config::$links, Config::$scripts, Config::$styles, and
Config::$metas should be defined.  Custom post types and custom taxonomies should
be set here via Config::$custom_post_types and Config::$custom_taxonomies.
Custom thumbnail sizes, menus, and sidebars should also be defined here.

### functions.php
Theme-specific functions only should be defined here.  (Other required
function files are also included at the top of this file.)

### shortcodes.php
Where Wordpress shortcodes can be defined.  See example shortcodes for more
information.

### custom-post-types.php
Where the abstract custom post type and all its descendants live.

### static/
Where, aside from style.css in the root, all static content such as
javascript, images, and css should live.
Bootstrap resources should also be located here.


## Notes

This theme utilizes Twitter Bootstrap as its front-end framework.  Bootstrap
styles and javascript libraries can be utilized in theme templates and page/post
content.  For more information, visit http://twitter.github.com/bootstrap/

Note that this theme may not always be running the most up-to-date version of
Bootstrap.  For the most accurate documentation on the theme's current
Bootstrap version, visit http://bootstrapdocs.com/ and select the version number
found at the top of static/bootstrap/bootstrap/css/bootstrap.css

### Using Cloud.Typography
This theme is configured to work with the Cloud.Typography web font service.  To deliver the web fonts specified in
this theme, a project must be set up in Cloud.Typography that references the domain on which this repository will live.

Development environments should be set up in a separate, Development Mode project in Cloud.Typography to prevent pageviews
from development environments counting toward the Cloud.Typography monthly pageview limit.  Paste the CSS Key URL provided
by Cloud.Typography in the CSS Key URL field in the Theme Options admin area.

This site's production environment should have its own Cloud.Typography project, configured identically to the Development
Mode equivalent project.  **The webfont archive name (usually six-digit number) provided by Cloud.Typography MUST match the
name of the directory for Cloud.Typography webfonts in this repository!**


## Custom Post Types
* Parallax Feature


## Custom Taxonomies

n/a


## Shortcodes

### [blockquote]
* Generates a stylized blockquote.

### [parallax_feature]
* Adds a Parallax Feature to page/post content.

### [posttype-list]
* Custom post types that have defined $use_shortcode as True can automatically
utilize this shortcode for displaying a list of posts created under the given
post type; e.g., [document-list] will output a list of all published Documents.
Additional parameters can be used to further narrow down the shortcode's results;
see the Theme Help section on shortcodes for an available list of filters.

### [search_form]
* Outputs the site search form.  The search form output can be modified via
searchform.php

### [post-type-search]
* Generates a searchable list of posts. Post lists are generated in alphabetical order and, by default, by category and post title. Posts can be searched by post title and any tags assigned to the post. See the Theme Help section on shortcodes for more information.

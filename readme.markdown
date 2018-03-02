# UCF Downtown Theme - [University of Central Florida Downtown Orlando, FL](https://www.ucf.edu/downtown/)

WordPress theme built off of UCF's Generic theme for UCF's Downtown Orlando Site.


## Installation Requirements:
* GravityForms
* Advanced Custom Fields Pro


## Configuration
* Ensure that a menu has been created and assigned to the Header menu location.
* Import field groups (`dev/acf-fields.json`) using the ACF importer under Custom Fields > Tools.
* Under Theme Options > Web Fonts, ensure that webfonts have been properly configured to a [Cloud.Typography](https://www.typography.com/cloud/welcome/) CSS Key that [allows access to your environment](https://dashboard.typography.com/user-guide/managing-domains).


## Development

Note that compiled, minified css and js files are included within the repo.  Changes to these files should be tracked via git (so that users installing the theme using traditional installation methods will have a working theme out-of-the-box.)

[Enabling debug mode](https://codex.wordpress.org/Debugging_in_WordPress) in your `wp-config.php` file is recommended during development to help catch warnings and bugs.

### Requirements
* node
* gulp

### Instructions
1. Clone the Downtown-Theme repo into your development environment, within your WordPress installation's `themes/` directory: `git clone https://github.com/UCF/Downtown-Theme.git`
2. `cd` into the Downtown-Theme directory, and run `npm install` to install required packages for development into `node_modules/` within the repo
3. Copy `gulp-config.template.json`, make any desired changes, and save as `gulp-config.json`.
3. Run `gulp default` to process front-end assets.
4. If you haven't already done so, create a new WordPress site on your development environment, install the required plugins listed above, and set the Downtown Theme as the active theme.
5. Make sure you've done all the steps listed under "Configuration" above.
6. Run `gulp watch` to continuously watch changes to scss and js files.  If you enabled BrowserSync in `gulp-config.json`, it will also reload your browser when scss or js files change.


## Notes

### Bootstrap
This theme utilizes Twitter Bootstrap v2.3.2 as its front-end framework.  Bootstrap
styles and javascript libraries can be utilized in theme templates and page/post
content.  For more information, visit http://bootstrapdocs.com/v2.3.2/docs/

### Using Cloud.Typography
This theme is configured to work with the Cloud.Typography web font service.  To deliver the web fonts specified in
this theme, a project must be set up in Cloud.Typography that references the domain on which this repository will live.

Development environments should be set up in a separate, Development Mode project in Cloud.Typography to prevent pageviews
from development environments counting toward the Cloud.Typography monthly pageview limit.  Paste the CSS Key URL provided
by Cloud.Typography in the CSS Key URL field in the Theme Options admin area.

This site's production environment should have its own Cloud.Typography project, configured identically to the Development
Mode equivalent project.  **The webfont archive name (usually six-digit number) provided by Cloud.Typography MUST match the
name of the directory for Cloud.Typography webfonts in this repository!**

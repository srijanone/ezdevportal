# Ezdevportal Theme

Boostrap based default to theme for Ezdevportal. It is built on top
of [bootstrap barrio](https://www.drupal.org/project/bootstrap_barrio) version 5.5.6
contributed theme.

This theme is automatically set as the default theme as part of the installation
process.


## Use Cases

### Use it as the default theme.

  We can use this theme as the default theme and customize as we like. It uses
  Sass so we need to setup compiler to convert Sass to Css. Gulp workflow is
  already setup as part of the theme for Sass compilation but we need to install
  the npm packages to support it.

  Note: Make sure node has been already installed on the system.

  Using the terminal run the following set of commands from the theme folder:

  ```
  npm install
  npm install -g gulp-cli
  gulp watch
  ```

#### Change the color schemes

The theme allows changing the color scheme of the site using the set of
predefined color schemes or choosing of random colors using the color
pallete.

We can do the same by login in as drupal superadmin.
- Go to Appearance
- Edit the default theme settings
- Change the color set and save.

### Setup a sub-theme

We can setup the sub-theme using the ezdevportal_theme as the base theme.

- Create a new theme and set the base theme as ezdevportal_theme.
- Setup the regions as required.
- Customize the sub theme by setting your own stylesheet and libraries.

#### Recommendations:

It is recommended to copy the base theme regions into your sub-theme's info.yml.
It will handle the blocks placement into proper regions.

Since the base theme is based on bootstrap barrio we need to save the theme
settings after setting it as default.

- Go to Appearance and Edit the theme settings.
- For Layout, select Sidebar fist width and Sidebar second with as 3 cols.
- For Components, Navbar Structure check Navbar width container and save the
  theme settings.

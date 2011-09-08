TaskTracker -- A plugin that turns WordPress into a SIMPLE task tracking system.
====================================

## DESCRIPTION
## INSTALLATION
Place the folder 

	/task-tracker
	
Into your

	/wp-content/plugins
	
directory. Once there activate the plugin via the WordPress plugins admin page. You should now have a admin menu on the left hand side to: add, edit and delete a Task. You can also create a link in your menu to 

	/task
	
Which will link to the Task archive page (this is the preferred way). From here you can do some "basic" adding and updating a Task if your are logged in.

## THEMING
Theming should be done as followed:

1. Simple customization -- Override CSS in your THEME!
1. Advanced customization -- Override the template by creating a template in your current theme file.

## REPO STRUCTURE

### LIBRARY
3rd party libraries go here, we try to keep this to as less as possible. If you really want to party to go AC, Vegas, Rio, Pattaya or some 3rd world to get your party on!

### THEME
At the root level you'll find templates that should reflect the WordPress template naming convention as much as possible. For more information check the WordPress docs [here] (http://codex.wordpress.org/Template_Hierarchy). Note the plugin will try and load the template for the current theme first then will fall back on the "default". i.e.

  /wp-content/theme/[my-theme]/task-single.php

  /wp-content/plugins/task-tracker/theme/task-single.php

#### JS
JavaScript files go here, no parties! see the LIBRARY section above.

#### CSS
Images and stylesheets used for the plugin go here.



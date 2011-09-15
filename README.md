TaskTracker -- A simple way to track your task.
====================================

I built the TaskTracker plugin because I wanted a some features of project management software, along with a bug tracker but a simple interface and concept like a "todo list", out of frustration TaskTracker was born. Along the path an un-intended mini framework was built that allowed me to create an unlimited number of custom post types and taxonomies with some default templates the framework was born...and is still in its infancy.

An alpha version is in user [here] (http://zanematthew.com/task/).
What I have going on in my [personal] (http://zanematthew.com/project/personal/) life.
All [Open] (http://zanematthew.com/status/open/) Task I'm working on at the moment.
What I'm doing at [home] (http://zanematthew.com/project/home/)

I can't stress enough that I am not trying to replace or bundle bug tracking, pm and todo list functions into one, but mainly have a place where I can have Task for myself such as; "meet with client", "put bugs into the Issue Tracker", "paint the bathroom". It's made my life easier.

A proof of concept can be viewed by looking at my [collectibles] (http://zanematthew.com/collectible/). The following array structure (and alittle more code) gave me my collectibles, single view, archive view and taxonomy view.
<pre>$task = new CustomPostType();
$task->post_type = array(
    array(
        'name' => 'Task',
        'type' => 'task',
        'supports' => array(
            'title',
            'editor',
            'author',
            'comments'
        ),
        // @todo automate mother fuckergrrrrr
        'taxonomies' => array(
            'assigned', 
            'phase', 
            'priority', 
            'project', 
            'status', 
            'type', 
            'ETA'
        )      
    ),
    array(
        'name' => 'Collectible',
        'type' => 'collectible',
        'supports' => array(
            'title',
            'editor',
            'comments'
        ),
        // yes, lame! but this is how WP is doing it for now also
        // @todo automate mother fuckergrrrrr        
        'taxonomies' => array(            
            'magazine',
            'sneaker',
            'bmx',
            'comic-book',
            'trading-cards',
            'model-car'            
        )
    )
);</pre>

## DESCRIPTION
Build an unlimited number of Custom Post Types linking them to an unlimited number Custom Taxonomies in an array structure. Once the CPTs and CTTs are instantiated the following defaults are available, along with template overriding.

* JSON filtering
* Archive Template
* Single Template
* Taxonomy Template

### EXPERIENCE
A WordPress plugin that turns WordPress into a SIMPLE task tracking machine. Its intended use is to have a centeral location where you can record your task, be it something as simple as: "read up on backbone js", "check the issue tracker on wiki", "take out the trash", "paint the bath room". 

Each Task has a:

* Title
* Description

And can be assigned:

* Status
* Priority
* Project
* Phase
* Assignee
* ETA

This is NOT a replacement for a bug/issue tracker, advanced project management tracking, simple todo (or honey do list).

### TECHNICAL
This plugin leverages: 

* Custom Post Types
* Custom Taxonoimes
* AJAX form submission
* AJAX login 
* JSON powered filter/search.

Requirments

* WordPress
* PHP 5.3.2 or higher


## INSTALLATION
Place the folder `/task-tracker` into your `/wp-content/plugins` directory. Once there activate the plugin via the WordPress plugins admin page. You should now have a admin menu on the left hand side to: add, edit and delete a Task. You can also create a link in your menu to `/task`. Which will link to the Task archive page (this is the preferred way). From here you can do some "basic" adding and updating a Task once you are logged in.

## THEMING
Theming should be done as followed:

1. Simple customization - Override CSS in your THEME!
1. Advanced customization - Override the template by creating a template in your current theme file.
1. Extra-advanced customization - huh? don't even f^cking think about it.  

Create a custom template in the theme folder if it follows the WordPress naming convention i.e. `archive-[my cpt].php`, `single-[my cpt].php` in theory you can have an unlimited number of CPTs.
1. If one of these does not exisists the plugin will look in the users /theme folder

## TEMPLATE OVERRIDING
### Single Template
1. `/task-tracker/theme/single-[my cpt].php`
1. `/wp-content/theme/[current theme]/single-[my cpt].php`
1. `/task-tracker/theme/default/single.php`

### Archive Template
1. `/task-tracker/theme/archive-[my cpt].php`
1. `/wp-content/theme/[current theme]/archive-[my cpt].php`
1. `/task-tracker/theme/default/archive.php`

### Taxonmy Template
1. `/task-tracker/theme/custom/[my cpt]-taxonomy.php`
1. `/wp-content/theme/[current theme]/taxonomy.php` *May pose issues down the road
1. `/task-tracker/theme/default/taxonomy.php`

## DIRECTORY STRUCTURE

`/theme` At the root level you'll find templates that should reflect the WordPress template naming convention as much as possible. For more information check the WordPress docs [here] (http://codex.wordpress.org/Template_Hierarchy).

`/theme/css` Images and stylesheets used for the plugin go here.

`/theme/custom` Any file that can __not__ reflect the WordPress naming convention goes into `/custom` i.e. `my-navigation.php`, `my-footer.php`, etc. 

`/theme/default` Any file that is default for the plugin "framework" i.e. `login.php`, `navigation-filter.php`, etc.

`/theme/js` JavaScript files go here, no parties! see the LIBRARY section above.

`/theme/js/` At the root level place your custom js here.

`/theme/js/library` 3rd party libraries go here, we try to keep this to as less as possible. If you really want to party to go AC, Vegas, Rio, Pattaya or some 3rd world to get your party on!
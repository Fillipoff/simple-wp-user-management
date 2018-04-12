**Simple wordpress user management**
-------------------
Add possibility to edit user roles and capabilities in any Wordpress site.

How to install: 
1. Unzip downloaded file in ROOT THEME directory(ex. /my_site/wp-content/themes/my_theme/)
2. Make sure that in the root directory of your theme now exist folder “functions” and there are two files in it:
	- user-management.php
	- ajax-functions.php
3. In the root theme folder, open file functions.php and on top of the page add:
	- include ( get_stylesheet_directory() . '/functions/user-managemet.php' );
	- include ( get_stylesheet_directory() . '/functions/ajax-functions.php' );
4. That’s it. Now open your wordpress admin area and in menu “Users” you can find page “User management”, where you can edit user roles and capabilities.

There is no reset options, so do the changes on your responsibilities.

**NOTE: For simplicity all the css an javascript is included in the php file. If you know what are you doing, you can separate them and include them manually.**

# WordPress-Template
My custom WordPress template

This template works best when downloaded as a zip file and then copied to the project directory.

The template is made to be renamed:

- `wp-content/themes/basetheme-child/` directory should be renamed to one that suits the project.
- Inside `wp-content/themes/basetheme-child/style.css` there are various placeholders that should be replaced across the project
  - CLIENT_NAME: A human readable version of the client's name
  - CLIENTNAME: A machine readable version of the client's name. Accepted characters are the same as for PHP variables.
  - Theme Prefix (basethemechild_): Should be replaced with a 3-4 character representation of the client name. E.g. My Awesome Client would become `mae_`. This helps to prevent naming collisions among global PHP, WordPress and theme functions.

For more details on how to use see the README.md file in the `wp-content/themes/basetheme-child/` directory.

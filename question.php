
What is the Difference Between Posts vs. Pages

Posts vs. Pages

* Posts are timely vs. Pages are timeless.
* Posts are social vs. Pages are NOT.
* Posts can be categorized vs. Pages are hierarchical.
* Posts are included in RSS feed vs. Pages are not.
* Pages have custom template feature vs. Posts do not.

  how to store the post title in variable?
  $page_title = get_the_title($post->post_parent);

How to Generates a random password of the specified length in WordPress?
<?php generate_random_password($len) ?>

How to hide the Admin Bar in WordPress?
add_filter('show_admin_bar', '__return_false');

1. How to get the postid in wordpress?

 we can get the postid

  global $wp_query;

  $postid = $wp_query->post->ID;

  echo $postid;


2. How to get the post meta value in worpdress?

 we can get the post meta value throw postid.

 for example here post Custom Field name : comapny name

 get_post_meta($postid, 'Company name', true);

3. How to check if the post has a Post Thumbnail assigned to it.



 if ( has_post_thumbnail() )  // check if the post has a Post Thumbnail assigned to it.
    
      {    
                             
     the_post_thumbnail();
        
      }
4.how to get the wordpress featued image and how to change the image width and height.

 we can get the featurd image wordpress function

 the_post_thumbnail();//here we can get the wordpress featured image thumbnail
 
  
   the_post_thumbnail( array(100,100) );     // here we can change the image size.

5 How to get the specified category post only in wordpress?

 here we can get the all post in category id based.

 <?php query_posts("cat=106 && per_page=5");// here 106 is the category id  ?>

 <?php while ( have_posts() ) : the_post(); ?>

 <h3><?php the_title(); // here we can get the post title. this is the wordpress free defind function ?></h3>

 <?php  endwhile; ?>

6.What is the prefix of wordpress tables by default?
 By default, wp_ is prefix of wordpress.

7.How can we backup or import our WordPress content from admin panel?
 For import content from wordpress admin panel goes to
WordPress admin -> Tools -> Import


 8. Can wordPress use cookies?
Yes, wordpress use cookies.WordPress uses cookies, or tiny pieces of information stored on your computer, to verify who you are. There are cookies for logged in users.

9. How to disable wordpress comment?
 Look in to dashboard under Options –> Discussion. There is a checkbox there for “allow people to post comments on the article” Try unchecking that.

10. How many tables a default WordPress will have?

 default wordpress will have 11 tables. They are-
1. wp_commentmeta
2. wp_comments
3. wp_links
4. wp_options
5. wp_postmeta
6. wp_posts
7. wp_terms
8. wp_term_relationships
9. wp_term_taxonomy
10.wp_usermeta
11.wp_users
1. How to install the wordpress 3.8.1?
Answer:  Word press current is version 3.5.1. We can download the word press zip file in word press site. When ever we can download the word press zip file we can extract the file then after re name the file.
Step1  Deploy the word press site in xampp.
Root folder: C:\xampp\htdocs\wordpress
Step2:
Open the browser Mozilla Firefox.
Create the database in mysql database
Open the phpmyadmin
Create the database name (word press)
Type an address (http://localhost/wordpress) into the URL bar.
Step3:
Click the Create a Configuration File button --->
Next we can click the let’s go! Button
Then after we can write the Database Name, User Name, Password, Database Host, Table Prefix
Then after click the submit button.
Word press installation is completed.

What are the features of wordpress?
1. Simplicity,Make wordpress to manage easily that is its very easy to use.
2. Free open source ,its free to use wordpress.
3. Easy theme system,In wordpress we have thousand of good free theme to use.
4. Extends with plugins, we can extends the functionality of wordpress using thousands of free plugins or will create any plugin according to your requirements.
5. Community,WordPress has vibrant and supportive community.
6. Multilingual, wordpress is available on more than 70 languages.
7. Flexibility, with wordpress you will create any type of blog or website.
8. Comment, the built in comment system also make wordpress popular as you can comment your views on website.
9. Easy installation and upgrades.

10. Full standards compliance, Easy Importing,Cross-blog communication tools.

11. wordpress database tables list

wp_commentmeta : This table contains meta information about comments posted on a WordPress website. This table has four fields meta_id, comment_id, meta_key, and meta_value. Each meta_id is related to a comment_id. One example of comment meta information stored is the status of comment (approved, pending, trash, etc).

wp_comments : As the name suggests this table contains your WordPress comments. It contains comment author name, url, email, comment, etc.

wp_links : To manage blogrolls create by earlier versions of WordPress or the Link Manager plugin.

wp_options : This table contains most of your WordPress site wide settings such as: site url, admin email, default category, posts per page, time format, and much much more. The options table is also used by numerous WordPress plugins to store plugin settings.

wp_postmeta : This table contains meta information about your WordPress posts, pages, and custom post types. Example of post meta information would be which template to use to display a page, custom fields, etc. Some plugins would also use this table to store plugin data such as WordPress SEO information.

wp_posts : The name says posts but actually this table contains all post types or should we say content types. This table contains all your posts, pages, revisions, and custom post types.

wp_terms : WordPress has a powerful taxonomy system that allows you to organize your content. Individual taxonomy items are called terms and they are stored in this table. Example, your WordPress categories and tags are taxonomies, and each category and tag inside them is a term.

wp_term_relationships : This table manages relationship of WordPress post types with terms in wp_terms table. For example this is the table that helps WordPress determine post X is in Y category.

wp_term_taxonomy : This table defines taxonomies for terms defined in wp_terms table. For example if you have a term “WordPress Tutorials“, then this table contains the data that says it is associated with a taxonomy categories. In short this table has the data that helps WordPress differentiate between which term is a category, which is a tag, etc.

wp_usermeta : Contains meta information about Users on your website.


wp_users : Contains User information like username, password, user email, etc.
how to get current user information in wordpress
<?php
    $current_user = wp_get_current_user();
    /**
     * @example Safe usage: $current_user = wp_get_current_user();
     * if ( !($current_user instanceof WP_User) )
     *     return;
     */
    echo 'Username: ' . $current_user->user_login . '<br />';
    echo 'User email: ' . $current_user->user_email . '<br />';
    echo 'User first name: ' . $current_user->user_firstname . '<br />';
    echo 'User last name: ' . $current_user->user_lastname . '<br />';
    echo 'User display name: ' . $current_user->display_name . '<br />';
    echo 'User ID: ' . $current_user->ID . '<br />';

?>  
wordpress author related taxonomy posts   
        <?php
        global $post;
        $author_id=$post->post_author;
        $args = array(
                                    'post_type' => 'deals',
                                    'author'=> $author_id
                                );

                                $the_query = new WP_Query( $args );

                                if ( $the_query->have_posts() ) :

                                    while ( $the_query->have_posts() ) : $the_query->the_post();

                                        the_title();

                                    endwhile;

                                    //wp_reset_query();
                                endif;
        
        ?>
how to insert the values in wordpress database.?
insert query to use store the values in wordpress database.

 global $wpdb;
 $wpdb->insert($table_name,array('author_id' =>$author_id,'author_name' =>$author_name,'author_email'=>$author_email,'created_at'=>current_time('mysql', 1)),array('%d','%s','%s','%s'));

 how to get the single value in wordpress database.?
using get_var

example:

$table_name = $wpdb->prefix . "posts";

$post_content = $wpdb->get_var($wpdb->prepare( "SELECT post_content FROM $table_name WHERE post_status = %s",'publish'));

echo $post_content;

how to get posts from a specific category wordpress
<?php

$catquery = new WP_Query( 'cat=4&posts_per_page=10' );// cat = category
            //4=category id
while($catquery->have_posts()) : $catquery->the_post();
the_title();
//content here
?>

<?php endwhile; ?>

how to write the short code in wordpress php file

using do_shortcode function inside of php echo tag

a very simple solution is to use the do_shortcode function inside a PHP echo tag.
for example <?php do_shortcode("[shortcode]"); ?>

short code is used in wordpress post or page and text box widget and php file. this
12.div position pix to using jquery?

<script type="text/javascript">

 jQuery(document).ready(function(){
 var pathname = window.location;
 
 if(pathname=="http://greenstumps.com/pressrelease/blog/"){

  jQuery('.kontakt #utility').hide();

  jQuery('.lateast_post #utility_post').css('marginRight', -339);

  jQuery('#secondary').css('marginTop', 250);

  jQuery('.lateast_post #utility_post').show();
 }
 else {
 jQuery('#secondary').css('marginTop', 400);

 jQuery('.lateast_post #utility_post').hide();

 jQuery('.kontakt #utility').show();

 jQuery('.kontakt #utility').css('marginRight', -339);

 }
 
                           })
</script>



13. how to display the wordpress breadcrumbs?

here wordpre


<?php
function wordpress_breadcrumbs() {

  $delimiter = '&raquo;';
  $name = 'Home';
  $currentBefore = '<span class="current">';
  $currentAfter = '</span>';

  if ( !is_home() && !is_front_page() || is_paged() ) {

    echo '<div id="crumbs">';

    global $post;
    $home = get_bloginfo('url');
    echo '<a href="' . $home . '">' . $name . '</a> ' . $delimiter . ' ';

    if ( is_category() ) {
      global $wp_query;
      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category($thisCat);
      $parentCat = get_category($thisCat->parent);
      if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
      echo $currentBefore . 'Archive by category &#39;';
      single_cat_title();
      echo '&#39;' . $currentAfter;

    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $currentBefore . get_the_time('d') . $currentAfter;

    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $currentBefore . get_the_time('F') . $currentAfter;

    } elseif ( is_year() ) {
      echo $currentBefore . get_the_time('Y') . $currentAfter;

    } elseif ( is_single() ) {
      $cat = get_the_category(); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo $currentBefore;
      the_title();
      echo $currentAfter;

    } elseif ( is_page() && !$post->post_parent ) {
      echo $currentBefore;
      the_title();
      echo $currentAfter;

    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
      echo $currentBefore;
      the_title();
      echo $currentAfter;

    } elseif ( is_search() ) {
      echo $currentBefore . 'Search results for &#39;' . get_search_query() . '&#39;' . $currentAfter;

    } elseif ( is_tag() ) {
      echo $currentBefore . 'Posts tagged &#39;';
      single_tag_title();
      echo '&#39;' . $currentAfter;

    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $currentBefore . 'Articles posted by ' . $userdata->display_name . $currentAfter;

    } elseif ( is_404() ) {
      echo $currentBefore . 'Error 404' . $currentAfter;
    }

    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }

    echo '</div>';

  }
 }
   ?>
What is use of in_array() function in php ?
in_array used to checks if a value exists in an array

 wordpress display post limite.

 'showposts'=>5,//display only 5 posts.

 $args=array(
    'tag__in' => $tag_ids,
    'post__not_in' => array($post->ID),
    'showposts'=>5,//post limite
    'ignore_sticky_posts'=>1
   );

  $my_query = new WP_Query($args);
 if( $my_query->have_posts() ) {
    while ($my_query->have_posts()) : $my_query->the_post(); ?>
      <p><a href="<?php the_permalink() ?>" rel="bookmark" title="
      <?php the_title_attribute(); ?>">
      <?php the_title('<h2 style=" width: 614px; border-top: dotted 2px #7C7272;">', '</h2>'); ?>
  <?php the_content(); ?>
  <span> &nbsp </span>
  </a></p>
      <?php
    endwhile;
  }
}


9. worpdress dispaly post content limite.
 <?php
  
  $content = get_the_content();
        $content = strip_tags($content);
        echo substr($content, 0, 205);

 ?>


10. how to get the Excerpt post content in wordpress post?


 here we can get the post Excerpt data.
  
 the_excerpt();




11. ajax load file

this is the Ajax Script to load the file


<script type="text/javascript">
        function lookup() {
          jQuery.ajax({
            type: "POST",//post type
            url: "rpc.php",// php file name
            data: "",            
            success: function(data) {
            //alert(data);
            if(data){ jQuery('#suggestions').show()
            jQuery('#autoSuggestionsList').html(data);}
            //jQuery('#autoSuggestionsList').html(data);
            }
           });
         
        } // lookup
        
        function fill(thisValue) {//file is the class in php file
         jQuery('#inputString').val(thisValue);
         setTimeout("jQuery('#suggestions').hide();", 200);
        }
        </script>
        
 here rpc.php file data


<?php

require_once('wp-config.php'); ?>
<?php

   $result = mysql_query("SELECT meta_value FROM wp_postmeta WHERE meta_key='address' LIMIT 5");

      while ($row = mysql_fetch_array($result)) {
      echo '<li onclick="fill(\''.$row['meta_value'].'\');"style="
    list-style: none;">'.$row['meta_value'].'</li>';
    }
/*
$selected = mysql_select_db("islasde",$dbhandle)
  or die("Could not select examples");
 /*$query = $db->query("SELECT * FROM wp_postmeta LIMIT 5");

    if($query) {
     // While there are results loop through them - fetching an Object (i like PHP5 btw!).
     while ($result = $query ->fetch_object()) {
      // Format the results, im using <li> for the list, you can change it.
      // The onClick function fills the textbox with the result.
      
      // YOU MUST CHANGE: $result->value to $result->your_colum
           echo '<li onfocus="fill(\''.$result->meta_value.'\');">'.$result->meta_value.'</li>';
           }
    } else {
     echo 'ERROR: There was a problem with the query.';
    }*/
?>
=======================================================================================================
Question: What is Wordpress?
Wordpress is Content Management System which have robust admin section. From Admin section you can manage the website text/html, image & videos etc. You can easily manage pages & posts. You can set meta title, meta description & meta keywords for each post. It gives you full control over post & pages .



Question: Is Wordpress opensource?
Yes, Wordpress is opensource and you can do customization as per your requirement. Wordpress is built in PHP/MySql/javascript/jQuery which is also an opensource.



Question: What is current stable version of wordpress?
4.1 released in November 20, 2014



Question: What kind of website can I build with WordPress?
WordPress was originally developed as a blogging in 2003 but now it has been changed a lot. you can create personal website as well as commercial website.
Following types of websites can be built in wordpress:

    Informative Website
    Personal Website
    Photo Gallery
    Business Website
    E-Commerce website
    Blogging

Today, million of free/paid wordpress themes, wordpress plugin are available which help you to create as per your requirement.



Question: From where you can download plugins?
https://wordpress.org/plugins/



Question: From where you can download themes?
https://wordpress.org/themes/



Question: What is Hooks in wordpress?
Hooks allow user to create WordPress theme or plugin with shortcode without changing the original files.



Question: What are the types of hooks in WordPress?
Following are two types of hooks
A) Action hooks: This hooks allow you to insert an additional code.
B) Filter hooks: Filter hooks will only allow you to add a content or text at the end of the post.



Question: What are positive aspects of wordpress?

    Easy to install and upgrade the wordpress
    In-built SEO engine and you can manage the URL and meta data as per your requirement.
    Easy to themes and plugins
    Multilingual available in more than 70 languages
    Can be do customization as per requirement
    Lots of free/paid themes/plugin available





Question: What is the default prefix of wordpress tables?
wp_ is the prefix for wordpress but you can change at the time of installation.



Question: What is WordPress loop?
The Loop is PHP code used by WordPress to display posts.



Question: What are the template tags in WordPress?
Template tags is a code that instructs WordPress to "do" or "get" something



Question: What are meta tags in wordpress?
Meta-tags are keywords and description used to display website.



Question: How to secure your wordpress website?

    Install security plug-ins like WP security
    Change password of super admin OR other admin
    Add security level checks at server level like folder/file permission.




Question: How many tables a default WordPress will have?
Following are main table in wordpress:

    wp_commentmeta
    wp_comments
    wp_links
    wp_options
    wp_postmeta
    wp_posts
    wp_terms
    wp_term_relationships
    wp_term_taxonomy
    wp_usermeta
    wp_users




Question: How to hide the top admin bar at the frontend in WordPress?
Add following code functions.php

add_filter('show_admin_bar', '__return_false');




Question: How to hide Directory Browsing in WordPress from server?
Add following code in htaccess file

Options -Indexes




Question: How to display custom field in wordpress?

echo get_post_meta($post->ID, 'keyName', true); 




Question: How to run database Query in WordPress?

$wpdb->query("select * from $wpdb->posts   where ID>10 ");




Question: What types of hooks in wordpress is used?
1)Following are Actions hooks:.

has_action()
add_action()
do_action()
do_action_ref_array()
did_action()
remove_action()
remove_all_actions()


2)Following are Filters hooks .

has_filter()
add_filter()
apply_filters()
apply_filters_ref_array()
current_filter()
remove_filter()
remove_all_filters()




Question: How can you backup your WordPress content?
WordPress admin -> Tools -> Import



Question: List most commonly functions used in wordpress?

    wp_nav_menu() :- Displays a navigation menu.
    is_page() :- to check if this is page OR NOT, will return boolean value.
    get_the_excerpt() :- Copy the excerpt of the post into a specified variable.
    in_category() :- Check if the specified post is assigned to any of the specified categories OR not.
    the_title():- Displays the title of the post in website.
    the_content():- Displays the contents of the post in website.




Question: What are the file structure in wordpress.
Following are main files which used in wordpress

    index.php :- for index page.
    search.php :- For display the search result page.
    single.php :- for single post page.
    page.php :- display the static pages.
    category.php :- Display the category page.
    tag.php :- For display the tags page.
    author.php :- For display author page.
    taxonomy.php :- For display the taxonomy archive.
    attachment.php :- For managing the single attachments page.
    header.php :- For managing top part of page.
    footer.php :- For manage bottom part of pages.
    archive.php :- For archive page display.
    404.php :- For display 404 error page.


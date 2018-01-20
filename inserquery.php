
<form action="" method="post">
          FirstName <input type="text" name="firstNametxt" value="" /><br/>
          LastName   <input type="text" name="lastNametxt" value="" /><br/>
          email <input type="text" name="email" value="" /><br/>
          Query <input type="text" name="query" value="" /><br/>
          <input name="Submit" type="submit" value="Submit">
      </form>
      <form method="post">
          <?php   
          global $wpdb;
                  $firstName = $_POST["firstNametxt"];
                  $lastName = $_POST["lastNametxt"];
                  $email = $_POST["email"];
                  $query = $_POST["query"];

                  echo $firstName;
          $contactus_table = $wpdb->prefix."contactus";

          $sql = "INSERT INTO $contactus_table (id, firstname, lastname, email,                                               

            query, reg_date) VALUES ('2', $firstName, $lastName, $email, $query,

            CURRENT_TIMESTAMP);";                  

            $wpdb->query($sql)) 

     ?>
     </form>
==================================================================================================
<form action="" method="post"  enctype="multipart/form-data">
  <label for="first-name-text">First name: </label><input type="text" id="first-name-text" name="firstNametxt" value="" /><br/>
  <label for="last-name-text">Last name: </label><input type="text" id="last-name-text" name="lastNametxt" value="" /><br/>
  <label for="email">Email: </label><input type="text" id="email" name="email" value="" /><br/>
  <label for="query">Query: </label><input type="text" id="query" name="query" value="" /><br/>
  <input name="Submit" type="submit" value="Submit">
</form>
<?php
if( isset($_POST['submit']) ) {

  //get posted value from the form
  $firstName = $_POST["firstNametxt"];
  $lastName = $_POST["lastNametxt"];
  $email = $_POST["email"];
  $query = $_POST["query"];

  //deal with database in WordPress way
  global $wpdb;
  $contactus_table = $wpdb->prefix."contactus";
  $wpdb->insert( 
    $contactus_table, 
    array( 
        'firstname' => $firstName, 
        'lastname'  => $lastName,
        'email'     => $email,
        'query'     => $query,
        'reg_date'  => current_time( 'mysql' ) // http://codex.wordpress.org/Function_Reference/current_time
    ), 
    array( 
      '%s', //data type is string
      '%s',
      '%s',
      '%s',
      '%s' 
    ) 
  );
}
?>

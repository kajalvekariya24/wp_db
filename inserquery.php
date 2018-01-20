
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

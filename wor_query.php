
<?php
global $wpdb;
$customers = $wpdb->get_results("SELECT * FROM customers;");
print_r($customers);
?>

========================================================================
<?php

if (is_user_logged_in()):

global $wpdb;
$customers = $wpdb->get_results("SELECT * FROM customers;");

echo "<table>";
foreach($customers as $customer){
echo "<tr>";
echo "<td>".$customer->name."</td>";
echo "<td>".$customer->email."</td>";
echo "<td>".$customer->phone."</td>";
echo "<td>".$customer->address."</td>";
echo "</tr>";
}
echo "</table>";
else:
echo "Sorry, only registered users can view this information";
endif;

?>
============================================================================
<?php
function perform_database_action(){
    mysql_query(“INSERT into table_name (col1, col2, col3) VALUES ('$value1','$value2', '$value3');
}
           php to wordpress convert
            --------------------
                
                
 <?php
function perform_database_action(){
    global $wpdb;
    $data= array('col1'=>$value1,'col2'=>$value2,'col3'=>$value3);
    $format = array('%s','%s','%s');
    $wpdb->insert('table_name', $data, $format);
}

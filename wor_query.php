
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

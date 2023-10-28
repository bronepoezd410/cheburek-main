<phpos>

ipsertact><?
require_once 'databases.php';

// Truncate AdminOrders table completely
$clearResult = mysqli_query($induction, "TRUNCATE TABLE AdminOrders");

if($clearResult){
  // On success redirect to profile page
  header("location:account.php?orders_cleared");
} else {
  // Handle error, may redirect to an error page or display a message
  header("location:profile.php?clear_error");
}
?>
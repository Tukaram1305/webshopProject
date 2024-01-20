
<?php
// UPDATE KOSZYKA
session_start();
if(isset($_POST["user_id"])) {
	
	if ($_SESSION['zalogowany'] != true)
	{
		echo "Nie zalogowany użytkownik!";
		exit();
	}
	$user_id = $_POST["user_id"];
	
   require_once "connect.php";

   $conn = @new mysqli($servername, $username, $password, $dbname);
   if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
   }

	//ZABEZPIECZENIE PRZED DUPLIKOWANIEM ITEMOW
	$sql = sprintf("SELECT COUNT(customer_id) AS ile FROM basket WHERE customer_id=%d",$user_id);
	$result = $conn->query($sql);
	$arr = mysqli_fetch_assoc($result);
	$num =$arr['ile'];
	echo $num;

   $conn->close();
}
else
	echo "ERROR!";

?>


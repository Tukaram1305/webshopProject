
<?php
// UPDATE KOSZYKA
session_start();

if(isset($_POST["user_id"])) 
{
	if ($_SESSION['zalogowany'] != true)
	{
		echo "Nie zalogowany użytkownik!";
		exit();
	}
	$uid = $_POST["user_id"];
	
   require_once "connect.php";

   $conn = @new mysqli($servername, $username, $password, $dbname);
   if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
   }

	//ZABEZPIECZENIE PRZED DUPLIKOWANIEM ITEMOW
	$sql = sprintf("SELECT customers.id AS cus_id,products.* ,basket.*, ROUND(SUM(basket.quantity*products.price),2) AS Suma, (SELECT ROUND(SUM(basket.quantity*products.price),2) FROM  basket JOIN customers ON (customers.id=basket.customer_id) JOIN products ON (products.id=basket.product_id) WHERE customers.id=%d) as Overall FROM basket JOIN customers ON (customers.id=basket.customer_id) JOIN products ON (products.id=basket.product_id) WHERE customers.id=%d GROUP BY products.name ORDER BY products.name DESC",$uid,$uid);
	$result = $conn->query($sql);
	$arr = json_encode(mysqli_fetch_all($result,MYSQLI_ASSOC));
	echo $arr;

   $conn->close();
}
else
	echo "ERROR!";

?>


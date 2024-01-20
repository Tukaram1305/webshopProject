
<?php
// DODAWANIE DO KOSZYKA
if(isset($_POST["user_id"])) {
		 
	$user_id = $_POST["user_id"];
	$item_id = $_POST["item_id"];
	$quantity = $_POST["quantity"];
	
   require_once "connect.php";

   $conn = @new mysqli($servername, $username, $password, $dbname);
   if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
   }

	//ZABEZPIECZENIE PRZED DUPLIKOWANIEM ITEMOW - jesli item istenije juz w koszyku, aktualizuj ilość ++ ...
	$sql = sprintf("SELECT * FROM basket WHERE customer_id=%d AND product_id=%d",$user_id,$item_id);
	$result = $conn->query($sql);
	$num_rows = $result->num_rows;
	if ($num_rows>0)
	{
		$sql = sprintf("UPDATE basket SET quantity=(quantity+%d) WHERE customer_id=%d AND product_id=%d",$quantity,$user_id,$item_id);
		if ($conn->query($sql) === TRUE) {
		echo "Zaktualizowano :".date("Y-m-d H:i:s");
		} 
		else {
		echo "Blad: " . $sql . " => " . $conn->error;
		}
		echo "Już dodałeś ten przedmiot";
		$conn->close();
		exit;
	}

   
	// . . . inaczej dodaj nowy przedmiot do koszyka
   $sql = sprintf("INSERT INTO basket VALUES (%d,%d,%d)",$user_id,$item_id,$quantity);

   if ($conn->query($sql) === TRUE) {
      echo "Dodano do bazy o :".date("Y-m-d H:i:s");
   } else {
      echo "Blad: " . $sql . " => " . $conn->error;
   }

   $conn->close();
}
else
	echo "ERROR!";

?>


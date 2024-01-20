<?php
//ZARZADZANIE KOSZYKIEM
if (isset($_POST['mode']))
{
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
	   die("Connection failed: " . $conn->connect_error);
	}
	else {

		if ($_POST['mode']=='dec')
		{
			if ($_POST['quant']>1)
			{
			$question = sprintf("UPDATE basket SET basket.quantity=basket.quantity-1 WHERE basket.customer_id=%d AND basket.product_id=%d",$_SESSION['id'],$_POST['item']);
			$result = mysqli_query($conn,$question);
			}
			else 
			{
				$question = sprintf("DELETE FROM basket WHERE basket.customer_id=%d AND basket.product_id=%d",$_SESSION['id'],$_POST['item']);
				$result = mysqli_query($conn,$question);
			}
		}

		if ($_POST['mode']=='inc')
		{
			if ($_POST['quant']<$_POST['stock'])
			{
			$question = sprintf("UPDATE basket SET basket.quantity=basket.quantity+1 WHERE basket.customer_id=%d AND basket.product_id=%d",$_SESSION['id'],$_POST['item']);
			$result = mysqli_query($conn,$question);
			}
			else
			{
				echo '<script>alert("Brak dostępności w magazynie!")</script>';
			}

		}
		if ($_POST['mode']=='dellAll')
		{
			$question = sprintf("DELETE FROM basket WHERE customer_id=%d",$_SESSION['id']);
			$result = mysqli_query($conn,$question);
		}
		if ($_POST['mode']=='dellItem')
		{
			$question = sprintf("DELETE FROM basket WHERE customer_id=%d AND product_id=%d",$_SESSION['id'],$_POST['item_id']);
			$result = mysqli_query($conn,$question);
		}

		}
		unset($_POST['mode']);
}

?>
<!--LOGOWANIE-->
<?php
require_once "connect.php";
session_start();
if (!isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']!=true)
{
	header('Location:index.php');
	exit();
}

//WYLOGOWANIE
if (isset($_POST['logout']))
{
	session_unset();
	unset($_POST['logout']);
	header('Location:index.php');
}

//ZARZADZANIE KOSZYKIEM
require_once "basketHandle.php";


?>


 <!DOCTYPE HTML5>

<html>
<head>
<title>Koszyk</title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="styl.css?ts=<?=time()?>" />
<link rel="stylesheet" type="text/css" href="controls.css?ts=<?=time()?>" />
<script src="script.js?ts=<?=time()?>">	</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Red+Hat+Mono:wght@300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>

<body>
<header>
<div class="parent" id="top">
	<div id="h_bg"><img src="img/bgmain2.jpg"/></div>
	<div id="h_title">Paweł & Mateusz Hardware Store</div>
</div>

</header>

<div id="INFOBAR_COL">
	<div class="INFOBAR">
		<span>
			<?php
		if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']==true)
		{
		// ZALOGOWANY
		echo<<<END
		<span class="barSpanElem">
			<i class="fa fa-key" style="font-size:18px;color:white"></i>
			Witaj $_SESSION[name] $_SESSION[surname] ($_SESSION[email]) <!-- ID: $_SESSION[id]-->
			<form action="userAcc.php" method="post">
				<input type="hidden" value="1" name="logout"/>
				<button class="aInline aAccent" type="submit" value="Wyloguj">Wyloguj<i class="fa fa-remove" style="color:red;"></i></button>
			</form>
			<a class="aInline aAccent" href="userAcc.php">
				<div id="uAcc">Moje konto<i class="fa fa-address-card" style="font-size:14px"></i></div>
			</a>
			<a class="aInline" href="basket.php">
				<div id="basket">
					<i class="fa fa-shopping-basket" style="font-size:24px; color:white;"></i>
					<span id="basket_items_bg"></span>
					<div id="hoverbasket"></div>
				</div>
			</a>
		</span>
		END;
		}
		else
		{
			// NIEZALOGOWANY
			echo<<<END
			<span class="barSpanElem">
				<form action="index.php" method="post">
				<i class="fa fa-drivers-license-o" style="font-size:18px;color:white;margin-right:5px;"></i>
				<span>Login: <input class="inpInline" type="text" name="login"></span>
				<span>Hasło: <input class="inpInline" type="password" name="haslo"></span>
				<input class="inpInline" type="submit" value="Zaloguje się"/>
				</form>
			</span>
			END;
		}
			?>
		</span>
	</div>

	<div class="INFOBAR" >
		<span class="barSpanElem">Produkty</span>
	<span class="barSpanElem">
				<form action="index.php" method="POST">
					<button class="mainMenu" name="prdType" type="submit" value="1">Mikrokontrolery</button>
				</form>
	</span>
	<span class="barSpanElem">
				<form action="index.php" method="POST">
					<button class="mainMenu" name="prdType" type="submit" value="3">Moduły</button>
				</form>
	</span>
	<span class="barSpanElem">
				<form action="index.php" method="POST">
					<button class="mainMenu" name="prdType" type="submit" value="2">Elektronika</button>
				</form>
	</span>
	</div>
</div> <!--INFOBAR GORNY-->

<div id="content">

<?php
require_once "connect.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
else {
    $question = sprintf("SELECT customers.id AS cus_id,products.* ,basket.*, ROUND(SUM(basket.quantity*products.price),2) AS Suma, (SELECT ROUND(SUM(basket.quantity*products.price),2) FROM  basket JOIN customers ON (customers.id=basket.customer_id) JOIN products ON (products.id=basket.product_id) WHERE customers.id=%d) as Overall FROM basket JOIN customers ON (customers.id=basket.customer_id) JOIN products ON (products.id=basket.product_id) WHERE customers.id=%d GROUP BY products.name ORDER BY products.name DESC",$_SESSION['id'],$_SESSION['id']);

	$result = mysqli_query($conn,$question);
    $num = $result->num_rows;
	print '<div class="roomContainer">';
	if ($num>0){
	print '<table class="myTable"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">';
	echo<<<END
	<th>Przedmiot</th>
	<th width="80px"></th>
	<th width="320px">Cena jednostkowa</th>
	<th width="420px">Ilość</th>
	<th>Cena</th>
	<th></th>
	END;
    for ($i=0; $i<$num; $i++)
        {
            $row = mysqli_fetch_assoc($result);
			echo<<<END
			<!-- ROW 1 -->
			<tr>
			<td  align="center">$row[name]</td>
			<td  align="center"> <img src="$row[image]" width="60px" height="auto"> </td>
			<td align="center">
			<span style="color:#2f5701"> $row[price] zł</span> ($row[stock] dost.)
			 <i class="fa fa-tags" style="font-size:28px;color:#2f5701"></i>
			</td>
			
				<td align="center">
					<form action="basket.php" method="POST">
					<input type="hidden" name="mode" value="dec">
					<input type="hidden" name="quant" value=$row[quantity]>
					<input type="hidden" name="item" value=$row[product_id]>
					<button type="submit" class="btn4">-</button>
					</form>
					<span style="color:orange">$row[quantity]</span>(x$row[price]zł)
					<form action="basket.php" method="POST">
					<input type="hidden" name="mode" value="inc">
					<input type="hidden" name="quant" value=$row[quantity]>
					<input type="hidden" name="stock" value=$row[stock]>
					<input type="hidden" name="item" value=$row[product_id]>
					<button type="submit" class="btn4">+</button>
					</form>
				</td>
			<td align="center"> <span style="color:#733000">$row[Suma] zł</span></td>
			<td align="center"> 
			<form style="width:100%; height:100%;" action="basket.php" method="POST">
			<button class="btn3" type="submit" id="dellItem"><i class="fa fa-trash" style="font-size:48px"></i></button>
			<input type="hidden" value="$row[id]" name="item_id">
			<input type="hidden" value="dellItem" name="mode">
			</form>
			</td>
			END;
        }
		echo<<<END
			<tr>
				<th colspan="5"></th>
				<th >Do zapłaty</th>
			</tr>
			<tr>
				<td colspan="5"></td>
				<td class="accentRow">$row[Overall] zł</td>
			</tr>
			<tr>
				<th colspan="4"></th>
					<th>
					<form action="basket.php" method="POST">
						<input type="hidden" value="$_SESSION[id]" name="u_id">
						<input type="hidden" value="dellAll" name="mode">
						<button class="btn3" >Wyczyść koszyk (!)</button>
					</form>
					</th>
					<th>
						<form action="placeOrder.php" method="POST">
						<button class="btn3" type="submit">Płatność i dostawa</button>
						</form>
					</th>
			</tr>
		</table>
		</div>
		END;
	} // num > 0
	else
	{
		echo "<h2>Twój koszyk jest pusty!</h2></div>";
	}
}

?>
</div> <!--content-->

<?php 
if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']==true)
{
	echo '<script>';
	echo 'var uid='.$_SESSION['id'].';';
	echo 'updateBasketItemsNum(uid)';
	echo '</script>';
}

?>

<footer>
	<?php echo "Markowiak & Sołtysiak &copy ".date("Y-m-d");?>
</footer>
</body>

</html>


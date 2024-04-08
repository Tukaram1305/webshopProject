<!-- Copyrights by Markowiak Paweł and Mateusz Sołtysiak-->
<!--LOGOWANIE-->
<?php
session_start();
require_once "connect.php";
// zabezpieczenie
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


?>

 <!DOCTYPE HTML5>

<html>
<head>
<title>Moje konto</title>
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
	<div id="h_title">Paweł Hardware Store</div>
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

<div id="userBlock">
<form class="usrAccItem" action="userAcc.php" method="POST">
<button type="submit" style="font-size:24px; background-color:white;" name="usrPanel" value="Info">
	<i class="fa fa-address-card-o iconIndicator" style="font-size:24px;"> Informacje i ustawienia</i>
</button>
</form>

<form class="usrAccItem" action="userAcc.php" method="POST">
<button type="submit" style="font-size:24px; background-color:white;" name="usrPanel" value="Orders">
	<i class="fa fa-shopping-bag iconIndicator" style="font-size:24px"> Moje zamówienia</i>
</button>
</form>
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
	// Wybór panelu wg informacji z metody POST użytkownika
	// zmienna switch() - bazowo panel informacji*
	$menu;
	if (isset($_POST['usrPanel']))
	{
		if ($_POST['usrPanel']=="Info") $menu="Info";
		if ($_POST['usrPanel']=="Orders") $menu="Orders";
	}
	else $menu="Info";

	// PANEL 1 - INFORMACJE
	switch($menu)
	{
	case "Info":
	{
		$question = sprintf("SELECT customers.*, customer_gender.gender,addr_city.city,addr_city.zip_code,addr_city.street,addr_city.house_no FROM customers,addr_city,customer_gender WHERE customers.id=%d AND customers.gender_id=customer_gender.id AND customers.city_id=addr_city.id",$_SESSION['id']);
		$result = mysqli_query($conn,$question);
		$num = $result->num_rows;
		$row = mysqli_fetch_assoc($result);

		// kilka parametrow zakupow
		$query = mysqli_query($conn,sprintf("SELECT ROUND(SUM(orders.price),2) AS Overall, customers.name FROM orders, customers WHERE customers.id=%d AND orders.customer_id=customers.id",$_SESSION['id']));
		$overallMoneySpend = mysqli_fetch_assoc($query);
		

		$query = mysqli_query($conn,sprintf("SELECT COUNT(orders.id) AS ordNum FROM orders WHERE orders.customer_id=%d",$_SESSION['id']));
		$numOrders = mysqli_fetch_assoc($query);

		$query = mysqli_query($conn,sprintf("SELECT SUM(items_per_order.quantity) AS itemsNum FROM products,orders,customers,items_per_order WHERE orders.customer_id=customers.id AND orders.id = items_per_order.order_id AND items_per_order.product_id=products.id AND customers.id=%d",$_SESSION['id']));
		$numItems = mysqli_fetch_assoc($query);
		if($num>0)
		{
			print '<div class="roomContainer">';
			print '<table class="myTable_reg"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">';
			echo<<<END
			<tr>
			<th colspan="2" style="text-align: left;" >Dane do logowania</th>
			</tr>
			<tr>
			<td>Login</td>
			<th style="color:#3c00d4;">$row[login]</th>
			</tr>
			<tr>
			<td>Hasło</td>
			<th style="color:red;">$row[password]</th>
			</tr>
			<th colspan="2" style="text-align: left;">Dane osobowe</th>
			<tr>
			<td>Imię</td>
			<th>$row[name]</th>
			</tr>
			<tr>
			<td>Nazwisko</td>
			<th>$row[surname]</th>
			</tr>
			<tr>
			<td>Płeć</td>
			<th>$row[gender]</th>
			</tr>
			<tr>
			<th colspan="2" style="text-align: left;">Dane kontaktowe i adres do wysyłki</th>
			</tr>
			<tr>
			<td>Numer telefonu</td>
			<th>$row[phone]</th>
			</tr>
			<tr>
			<td>Adres email</td>
			<th>$row[email]</th>
			</tr>
			<tr>
			<td>Miasto</td>
			<th>$row[city]</th>
			</tr>
			<tr>
			<td>Kod pocztowy</td>
			<th>$row[zip_code]</th>
			</tr>
			<tr>
			<td>Ulica</td>
			<th>$row[street]</th>
			</tr>
			<tr>
			<td>Nr domu/mieszkania</td>
			<th>$row[house_no]</th>
			</tr>

			<th colspan="2" style="text-align: left;">Zakupy</th>
			</tr>
			<tr>
			<td>Całkowity koszt zakupów</td>
			<th>$overallMoneySpend[Overall] zł</th>
			</tr>
			<tr>
			<td>Liczba złożonych zamówień</td>
			<th>$numOrders[ordNum]</th>
			</tr>
			<tr>
			<td>Liczba kupionych przedmiotów</td>
			<th>$numItems[itemsNum] sztuk/zestawów</th>
			</tr>
			</table>
			</div>
			END;
		}
	break;
	}
	// PANEL 2 - ZAMÓWIENIA
	case "Orders":
	{
		// SORTOWANIE
		echo<<<end
		<div class="roomContainer">
		<table class="myTable_infos" align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">
		<tr>
		<th width="200px" style="text-align: right;">Sortuj według:</th>
		<th width="360px" style="text-align: left;">
		<form action="userAcc.php" method="POST" style="display:block;">
		<input type="hidden" name="usrPanel" value="Orders">
		<input type="radio" id="srt" name="srt" value="cena">
		<label for="srt">Cena zamówienia</label><br>
		<input type="radio" id="date" name="srt" value="date" checked="1">
		<label for="date">Data złożenia</label><br>
		<input type="radio" id="nr" name="srt" value="nr">
		<label for="nr">Numer zamówienia</label><br>		
		<input type="radio" id="stat" name="srt" value="stat">
		<label for="stat">Status</label><br>
		</th>
		<th width="300px" style="text-align: left;">
		<input type="radio" id="asc" name="srtord" value="asc">
		<label for="asc">Rosnąco</label><br>
		<input type="radio" id="desc" name="srtord" value="desc" checked="1">
		<label for="desc">Malejąco</label><br>
		</th>
		<th width="400px"><button type="submit" class="btn3">Sortuj</button></th>
		</form>
		</tr>
		</table>
		</div>
		end;
		
		// zbierz wszystkie przedmioty dla konkretnego zamowienia [ cus.id = x ; ord.id = x ]
		$qstr = <<<EOD
		SELECT orders.id, products.id AS prod_id,products.image, products.name,product_type.type, products.price, items_per_order.quantity 
		FROM customers,orders,items_per_order,products,product_type
		WHERE customers.id=orders.customer_id 
		AND orders.id=items_per_order.order_id 
		AND items_per_order.product_id=products.id 
		AND products.type=product_type.id
		AND orders.customer_id = %d 
		AND orders.id = %d
		ORDER BY orders.id 
		EOD;

		// zbierz obiekty zamowien dla x usera [ cus.id = x ]
		$ordstr = <<<EOD
		SELECT orders.id, orders.price, orders.date_time, orders.status, orders.payment,orders.shipement,orders.invoice_no
		FROM customers,orders,items_per_order,products,product_type
		WHERE customers.id=orders.customer_id 
		AND orders.id=items_per_order.order_id 
		AND items_per_order.product_id=products.id 
		AND products.type=product_type.id
		AND orders.customer_id=%d 
		GROUP BY orders.id

		EOD;
		// Formatuj sortowanie
		if (isset($_POST['srt']) && isset($_POST['srtord']))
		{
			if ($_POST['srt']=="cena") $ordstr.=" ORDER BY orders.price";
			if ($_POST['srt']=="date") $ordstr.=" ORDER BY orders.date_time";
			if ($_POST['srt']=="nr") $ordstr.=" ORDER BY orders.id";
			if ($_POST['srt']=="stat") $ordstr.=" ORDER BY orders.status";
			
			if ($_POST['srtord']=="asc") $ordstr.=" ASC";
			if ($_POST['srtord']=="desc") $ordstr.=" DESC";
			
		}
		// w innym wypadu sortuj wg. daty
		else
		{
			$ordstr.=" ORDER BY orders.date_time DESC";
		}

		$question = sprintf($ordstr,$_SESSION['id']);
		$ord_Res = mysqli_query($conn,$question);
		$ord_num = $ord_Res->num_rows;
		for ($i=0; $i<$ord_num; $i++)
		{
			$row = mysqli_fetch_assoc($ord_Res);
			echo<<<END
			<div class='roomContainer'>
			<table class="myTable"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">
			<tr>
				<th>Nr zamówienia</th>
				<th>Data złożenia</th>
				<th>Status</th>
				<th>Suma (PLN)</th>
				<th></th>
			</tr>
			<tr>
				<td>#$row[id]</td>
				<td>$row[date_time]</td>
			END;
			if ($row['status']=="Opłacone"){ print "<td style='color:white; background-color: #632522;'>".$row['status']."</td>";}
			if ($row['status']=="W przygotowaniu"){ print "<td style='color:white; background-color: #225263;'>".$row['status']."</td>";}
			if ($row['status']=="Wysłane"){ print "<td style='color:white; background-color: #22635d;'>".$row['status']."</td>";}
			if ($row['status']=="Doręczone"){ print "<td style='color:white; background-color: #316322;'>".$row['status']."</td>";}
			echo<<<END
				
				<td class="accentRow2">$row[price] zł</td>
				<td width="120px"><button class="aAccent hidSub" type="button" value="$i" id="ord_$i">
				<i class="fa fa-angle-double-down" style="font-size:24px"></i>ROZWIŃ
				</button></td>
			</tr>
			</table>
				<script>
				document.getElementById("ord_$i").addEventListener ("click", hideOrder, false);
				</script>
			END;
			$question2 = sprintf($qstr,$_SESSION['id'],$row['id']);
			$ord_Items = mysqli_query($conn,$question2);
			$ord_itm_num = $ord_Items->num_rows;
			
			// SUB-TABELA ze SZCZEGOLAMI PRZEDMIOTOW ZAKUPIONYCH
			echo<<<END
			<table class="myTable_products" id="tab_$i"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">
			<tr><th colspan="6">Szczegóły zamówienia</th></tr>
			<tr>
			<th colspan="2" style='color:white; background-color: #3a2c52;'>Płatność</th>
			<th colspan="2">Sposób wysyłki</th>
			<th colspan="2">Numer Faktury</th>
			</tr>
			<tr>
			<td colspan="2" style='color:white; background-color: #3a2c52;'>$row[payment]</td>
			<td colspan="2">$row[shipement]</td>
			<td colspan="2">#$row[invoice_no]</td>
			</tr>
			<tr>
				<th></th>
				<th>Nr kat.</th>
				<th>Nazwa</th>
				<th>Rodzaj</th>
				<th>Cena jedn.</th>
				<th>Ilość i suma</th>
			</tr>
			END;
			for ($j=0; $j<$ord_itm_num; $j++)
			{
				$subRow = mysqli_fetch_assoc($ord_Items);
				$itmsumprice = $subRow['quantity']*$subRow['price'];
				echo<<<END
				<tr>
				<td><img class="scalableImg" src="$subRow[image]"></td>
				<td>#$subRow[prod_id]</td>
				<td>$subRow[name]</td>
				<td>$subRow[type]</td>
				<td>$subRow[price] (zł)</td>
				<td>$subRow[quantity] ( <font color="orange">$itmsumprice</font> zł)</td>
				</tr>
				END;
			}
			echo "</table>";

			echo "</div>";
		}
		

		mysqli_free_result($ord_Res);
	}
	break;
	default: {echo "<h3>Coś poszło nie tak!</h3>";}
	} // switch

$conn->close();
}
?>

</div> <!--content-->

<script>

document.getElementById("basket").addEventListener("mouseover",function(){showPopupBasket()})
document.getElementById("basket").addEventListener("mouseout",function(){hidePopupBasket()})
basketPopup = document.getElementById("hoverbasket")

// chwile poczekaj az HTTP REQ zwroci zapytanie
function delayRefresh()
{
	setTimeout(function(){
	updateBasketSetResult(<?php echo $_SESSION['id']; ?>)
}, 100);
}

</script>

<?php 
if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']==true)
{
	echo '<script>';
	echo 'var uid='.$_SESSION['id'].';';
	echo 'updateBasketItemsNum(uid);';
	echo 'updateBasketSetResult('.$_SESSION['id'].')';
	echo '</script>';
	
}

?>

<footer>
	<?php echo "Markowiak &copy ".date("Y-m-d");?>
</footer>
</body>

</html>


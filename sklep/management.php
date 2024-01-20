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
<title>Panel Admina</title>
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
	<div id="h_title">Admin Management Panel</div>
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
			Witaj $_SESSION[name]

			<form action="index.php" method="post">
				<input type="hidden" value="1" name="logout"/>
				<button class="aInline aAccent" type="submit" value="Wyloguj">Wyloguj<i class="fa fa-remove" style="color:red;"></i></button>
			</form>

			<a class="aInline aAccent" href="management.php">
				<div id="uAcc">Panel Zarządzania<i class="fa fa-area-chart" style="font-size:14px"></i></div>
			</a>
		</span>
		END;
		}
	?>
		</span>
	</div>

<div id="userBlock">
<form class="usrAccItem" action="management.php" method="POST">
<button type="submit" class="btn5mi"  name="admPanel" value="Info">
<i class="fa fa-bar-chart iconIndicator" style="font-size:24px;"> Informacje</i>
</button>
</form>

<form class="usrAccItem" action="management.php" method="POST">
<button type="submit" class="btn5mi" name="admPanel" value="Customers">
<i class="fa fa-group iconIndicator" style="font-size:24px"> Przeglądaj użytkowników</i>
</button>
</form>
</div>

</div> <!--INFOBAR GORNY-->


<div id="content">

<?php
function gatherInfos($type,$conn)
{
	switch($type)
	{
		case "CustNo":
			$question = sprintf("SELECT COUNT(customers.id)-1 AS cNo FROM customers");
			$result = mysqli_query($conn,$question);
			$cNo = mysqli_fetch_assoc($result);
			return $cNo['cNo'];
			break;
		case "itemNum":
			$question = sprintf("SELECT SUM(products.stock) AS Suma FROM products");
			$result = mysqli_query($conn,$question);
			$cNo = mysqli_fetch_assoc($result);
			return $cNo['Suma'];
			break;
		case "stockPrice":
			$question = sprintf("SELECT ROUND(SUM(price * stock),2) AS Sum FROM products WHERE 1");
			$result = mysqli_query($conn,$question);
			$bSum = mysqli_fetch_assoc($result);
			return $bSum['Sum'];
			break;
		case "ordNum":
			$question = sprintf("SELECT COUNT(orders.id) AS OrdNum FROM orders");
			$result = mysqli_query($conn,$question);
			$bSum = mysqli_fetch_assoc($result);
			return $bSum['OrdNum'];
			break;
		case "ordPSum":
			$question = sprintf("SELECT ROUND(SUM(orders.price),2) AS OrdPSum FROM orders");
			$result = mysqli_query($conn,$question);
			$bSum = mysqli_fetch_assoc($result);
			return $bSum['OrdPSum'];
			break;
		case "mcuNum":
			$question = sprintf("SELECT SUM(items_per_order.quantity) AS Suma FROM items_per_order,products WHERE products.id=items_per_order.product_id AND products.type=1 ");
			$result = mysqli_query($conn,$question);
			$bSum = mysqli_fetch_assoc($result);
			return $bSum['Suma'];
			break;
		case "modNum":
			$question = sprintf("SELECT SUM(items_per_order.quantity) AS Suma FROM items_per_order,products WHERE products.id=items_per_order.product_id AND products.type=2 ");
			$result = mysqli_query($conn,$question);
			$bSum = mysqli_fetch_assoc($result);
			return $bSum['Suma'];
			break;
		case "eleNum":
			$question = sprintf("SELECT SUM(items_per_order.quantity) AS Suma FROM items_per_order,products WHERE products.id=items_per_order.product_id AND products.type=3 ");
			$result = mysqli_query($conn,$question);
			$bSum = mysqli_fetch_assoc($result);
			return $bSum['Suma'];
			break;
	} // switch
}
require_once "connect.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
else {
	$subsite = "";
	if (isset($_POST['admPanel']) && $_POST['admPanel']=='Info') $subsite="Info";
	else if (isset($_POST['admPanel']) && $_POST['admPanel']=='Customers') $subsite="Cust";
	else if(isset($_POST['admPanel'])) $subsite = "COrd";
	else $subsite="Info";

	// PANELE ADMINISTRATORA
	switch($subsite){
		case "Info":
	{
		$cNumber = gatherInfos("CustNo",$conn);
		$itemsNum = gatherInfos("itemNum",$conn);
		$stockPrice = gatherInfos("stockPrice",$conn);
		$ordNum = gatherInfos("ordNum",$conn);
		$ordPSum = gatherInfos("ordPSum",$conn);
		$mcuNumBought = gatherInfos("mcuNum",$conn);
		$modNumBought = gatherInfos("modNum",$conn);
		$eleNumBought = gatherInfos("eleNum",$conn);
		$wholeTypeProdNum = $mcuNumBought+$modNumBought+$eleNumBought;
		echo<<<end
		<h3>Informacje</h3>

		<div class="roomContainer">
		<table class="myTable_infos"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">
		<tr>
		<th>Liczba zarejestrowanych użytkowników</th>
		<td>$cNumber</td>
		</tr>
		<tr>
		<th>Wartość całego asortymentu sklepu</th>
		<td>$stockPrice zł</td>
		</tr>
		<tr>
		<th>Wartość wszsystkich zamówień klientów</th>
		<td>$ordPSum zł</td>
		</tr>
		<tr>
		<th>Liczba złożonych zamówień</th>
		<td>$ordNum</td>
		</tr>
		<tr><td colspan="2" style="text-align: center;">Liczba wykupionych przedmiotów z działu:</td></tr>
		<tr>
		<th>Mikrokontrolery</th>
		<td>$mcuNumBought (sztuk/zestawów)</td>
		</tr>
		<tr>
		<th>Moduły</th>
		<td>$modNumBought (sztuk/zestawów)</td>
		</tr>
		<tr>
		<th>Elektronika</th>
		<td>$eleNumBought (sztuk/zestawów)</td>
		</tr>
		<tr><td colspan="2" style="text-align: center;">Asortyment</td></tr>
		<tr>
		<th>Całkowity stan magazynowy</th>
		<td>$itemsNum (sztuk/zestawów)</td>
		</tr>
		<tr>
		<th>Łączna ilość zamówionych przedmiotów</th>
		<td>$wholeTypeProdNum (sztuk/zestawów)</td>
		</tr>
		</table>
		</div>
		end;
		break;
	}
	case "Cust":
	{
		$question = sprintf("SELECT customers.*,addr_city.city FROM customers,addr_city WHERE customers.city_id=addr_city.id AND customers.login!='admin' ");
		$result = mysqli_query($conn,$question);
		$num = $result->num_rows;

		echo<<<END
		<h3>Przegląd zarejestrowanych klientów</h3>
		<div class="roomContainer">
		<table class="myTable_infos"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">
		<tr>
		<th colspan="1" style="text-align: left;">ID</th>
		<th colspan="1" style="text-align: left;">Imię</th>
		<th colspan="1" style="text-align: left;">Nazwisko</th>
		<th colspan="1" style="text-align: left;">LOGIN</th>
		<th colspan="1" style="text-align: left;">email</th>
		<th colspan="1" style="text-align: left;">Telefon</th>
		<th colspan="1" style="text-align: left;">Miasto</th>
		<th colspan="1" style="text-align: left;">Zamówienia</th>
		</tr>
		END;

		for($i=0; $i<$num; $i++)
		{
			$row = mysqli_fetch_assoc($result);
			echo<<<END
			<tr>
			<td>$row[id]</td>
			<td>$row[name]</td>
			<td>$row[surname]</td>
			<td>$row[login]</td>
			<td>$row[email]</td>
			<td>$row[phone]</td>
			<td>$row[city]</td>
			<td align="center"><form action="management.php" method="POST" style="margin:auto; width:100%;">
			<input type="hidden" value=$row[id] name="admPanel">
			<button type="submit" class="btn3">Zamówienia</button>
			</form></td>
			</tr>
			END;
		}
		print "</table>";
		print "</div>";
		break;
	}
	case "COrd":
	{
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
		ORDER BY orders.id 
		EOD;

		$question = sprintf($ordstr,$_POST['admPanel']);
		$ord_Res = mysqli_query($conn,$question);
		$ord_num = $ord_Res->num_rows;

		$currUser = mysqli_query($conn,sprintf("SELECT customers.id, customers.name, customers.surname, customers.login FROM customers WHERE id=%d",$_POST['admPanel']));
		$userRow = mysqli_fetch_assoc($currUser);
		echo "<h3>Zamówienia użytkownika (id:".$userRow['id'].") ".$userRow['name']." ".$userRow['surname'].", login: ".$userRow['login']."</h3>";
		for ($i=0; $i<$ord_num; $i++)
		{
			$row = mysqli_fetch_assoc($ord_Res);
			echo<<<END
			<div class='roomContainer'>
			<table class="myTable"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">
			<tr>
				<th>Nr zamówienia</th>
				<th>Data</th>
				<th>Status</th>
				<th>Suma (PLN)</th>
				<th></th>
			</tr>
			<tr>
				<td>#$row[id]</td>
				<td>$row[date_time]</td>
			END;
				echo "<td>";
				echo "<select name='statusList' id='".$row['id']."'>";
				if ($row['status']=="Opłacone") {echo "<option value='Opłacone' selected>Opłacone</option>";} else {echo "<option value='Opłacone'>Opłacone</option>";}
				if ($row['status']=="W przygotowaniu") {echo "<option value='W przygotowaniu' selected>W przygotowaniu</option>";} else {echo "<option value='W przygotowaniu'>W przygotowaniu</option>";}
				if ($row['status']=="Wysłane") {echo "<option value='Wysłane' selected>Wysłane</option>";} else {echo "<option value='Wysłane'>Wysłane</option>";}
				if ($row['status']=="Doręczone") {echo "<option value='Doręczone' selected>Doręczone</option>";} else {echo "<option value='Doręczone'>Doręczone</option>";}
			  	echo "</select>";
				echo "</td>";
			echo<<<END
				<td class="accentRow2">$row[price] zł</td>
				<td width="120px" ><button class="aAccent hidSub" type="button" value="$i" id="ord_$i">
				<i class="fa fa-angle-double-down" style="font-size:24px"></i>ROZWIŃ
				</button></td>
			</tr>
			</table>
				<script>
				document.getElementById("ord_$i").addEventListener ("click", hideOrder, false);
				document.getElementById("$row[id]").addEventListener ("change", chngStatus, false);
				</script>
			END;

			$subsubRow = sprintf($qstr,$userRow['id'],$row['id']);
			$ord_Items = mysqli_query($conn,$subsubRow);
			$ord_itm_num = $ord_Items->num_rows;
			
			// SUB-TABELA ze SZCZEGOLAMI PRZEDMIOTOW ZAKUPIONYCH
			echo<<<END
			<table class="myTable_products" id="tab_$i"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">
			<tr><th colspan="6">Szczegóły zamówienia</th></tr>
			<tr>
			<th colspan="2">Płatność</th>
			<th colspan="2">Sposób wysyłki</th>
			<th colspan="2">Numer Faktury</th>
			</tr>
			<tr>
			<td colspan="2">$row[payment]</td>
			<td colspan="2">$row[shipement]</td>
			<td colspan="2">$row[invoice_no]</td>
			</tr>
			<tr>
				<th>Zdjęcie</th>
				<th>Id</th>
				<th>Nazwa</th>
				<th>Typ</th>
				<th>Cena jedn.</th>
				<th>Ilość</th>
			</tr>
			END;
			for ($j=0; $j<$ord_itm_num; $j++)
			{
				$subRow = mysqli_fetch_assoc($ord_Items);
				echo<<<END
				<tr>
				<td><img src="$subRow[image]" width="60px" height="auto"></td>
				<td>$subRow[prod_id]</td>
				<td>$subRow[name]</td>
				<td>$subRow[type]</td>
				<td>$subRow[price] (zł)</td>
				<td>$subRow[quantity] x $subRow[price]</td>
				</tr>
				END;
			}
			echo "</table>";

			echo "</div>";
		}
		mysqli_free_result($ord_Res);
		break;
	}
	} // switch
	
$conn->close();
}

?>

</div> <!--content-->


<footer>
	<?php echo "Markowiak & Sołtysiak &copy ".date("Y-m-d");?>
</footer>
</body>

</html>


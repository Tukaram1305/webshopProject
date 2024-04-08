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

require_once "basketHandle.php";
?>

 <!DOCTYPE HTML5>

<html>
<head>
<title>Hardware Store</title>
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
				Witaj $_SESSION[name] $_SESSION[surname] ($_SESSION[email]) Karta zamówienia:
			</span>
			END;
		}
			?>
		</span>
	</div>


</div>

<div id="content">

<?php
require_once "connect.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
else {
	// bezposrednie zamowienie
	if (isset($_POST['itemID']) && isset($_POST['itemNum']))
	{
		$item = $_POST['itemID'];
		$itemNum = $_POST['itemNum'];
	
		$question = sprintf("SELECT products.*,ROUND(products.price*%d,2) AS Suma FROM products WHERE id=%d",$itemNum,$item);
		$result = mysqli_query($conn,$question);
		$num = $result->num_rows;
		print '<div class="roomContainer">';
		if ($num>0){
		print '<table class="myTable"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">';
		echo<<<END
		<th>Nr</th>
		<th colspan="2" width="40%">Przedmiot</th>
		<th width="20%">Cena</th>
		<th>Ilość</th>
		<th>Suma</th>
		END;
		for ($i=0; $i<$num; $i++)
			{
				$row = mysqli_fetch_assoc($result);
				$no = $i+1;
				echo<<<END
				<!-- ROW 1 -->
				<tr>
				<td  align="center">$no </td>
				<td colspan="2" align="center">$row[name]</td>
				<td align="center">
				<span style="color:#733000"> $row[price] zł</span></td>
				<td align="center"><span style="color:#733000">$itemNum</span></td>
				<td align="center"> <span style="color:#733000; font-weight: bold;">$row[Suma] zł</span></td>
				END;
			}
			echo<<<END
				<tr>
					<th colspan="3"  style="text-align: left; font-size:24px;"> PODSUMOWANIE</th>
					<th colspan="3"  style="text-align: right; font-size:24px;"> Płatność i dostawa</th>
				</tr>
				<form action="saveOrder.php" method="POST">
					<tr>
					<th colspan="3" style="text-align: right;">Sposób płatności:</th>
					</tr>
					<tr>
						<th colspan="3"></th>
						<th colspan="3" style="text-align: left;">
						<input type="radio" id="pay1" name="pay" value="Przelew bankowy" checked="1">
						<label for="pay1">Przelew bankowy</label><br>
						<input type="radio" id="pay2" name="pay" value="Karta debetowa">
						<label for="pay2">Karta płatnicza</label><br>
						<input type="radio" id="pay3" name="pay" value="PayU">
						<label for="pay3">PayU</label><br>
						<input type="radio" id="pay4" name="pay" value="PayPal">
						<label for="pay4">PayPal</label>
						</th>
					</tr>
	
					<tr>
					<th colspan="3" style="text-align: right;">Wysyłka:</th>
					</tr>
					<tr>
						<th colspan="3"></th>
						<th colspan="3" style="text-align: left;">
						<input type="radio" id="post1" name="post" value="1" checked="1" >
						<label for="post1">Paczkomaty 24h <span style="color:red;"> (8.99 zł)</span></label><br>
						<input type="radio" id="post2" name="post" value="2">
						<label for="post2">Kurier<span style="color:red;"> (12.45 zł)</span></label><br>
						<input type="radio" id="post3" name="post" value="3">
						<label for="post3">Poczta - paczka polecona <span style="color:red;"> (18.91 zł)</span></label>
						</th>
					</tr>
	
					<!-- PODSUMOWANIE -->
				END;
	
					echo '<tr>';
					echo '<td colspan="5" style="text-align: right;">Netto:</td>';
					echo '<td class="accentRow">' . round($row['Suma']-($row['Suma']/100*23),2) . ' zł</td>';
					echo '</tr>';
					echo '<tr>';
					echo '<td colspan="5" style="text-align: right;">Brutto:</td>';
					echo '<td class="accentRow">' . $row['Suma'] . ' zł</td>';
					echo '</tr>';
					echo '<tr>';
					echo '<td colspan="5" style="text-align: right;"> + Wysyłka:</td>';
					echo '<td class="accentRow2"> <span id="wholePrice">' . $row['Suma']+8.4 . ' zł</span></td>';
					echo '<input type="hidden" id="sumPriceId" name="sumPrice" value='.$row['Suma'].'>';
					echo '</tr>';
	
			echo<<<END
					<!-- ZAMÓW -->
					<tr>
					<th colspan="5"></th>
					<input type="hidden" name="ITEMID" value="$row[id]">
					<input type="hidden" name="ITEMQUANT" value="$itemNum">
					<th><button class="btn3" type="submit">Płacę i zamawiam!</button>
					</th>
					</tr>
				</form>
				<tr>
					<!-- ANULOWANIE -->
					<th colspan="5"></th>
					<th><a href="index.php"><button class="btn3" type="button">Anuluj</button></a></th>
				</tr>
			</table>
			</div>
			END;
		} // num > 0
		else
		{
			echo "<h2>Coś poszło nie tak! Brak przedmiotów do zamówienia!</h2></div>";
		}
	}

	else{
	// zamowienie z koszyka
	$question = sprintf("SELECT customers.id AS cus_id,products.* ,basket.*, ROUND(SUM(basket.quantity*products.price),2) AS Suma, (SELECT ROUND(SUM(basket.quantity*products.price),2) FROM  basket JOIN customers ON (customers.id=basket.customer_id) JOIN products ON (products.id=basket.product_id) WHERE customers.id=%d) as Overall FROM basket JOIN customers ON (customers.id=basket.customer_id) JOIN products ON (products.id=basket.product_id) WHERE customers.id=%d GROUP BY products.name ORDER BY products.name DESC",$_SESSION['id'],$_SESSION['id']);
	$result = mysqli_query($conn,$question);
    $num = $result->num_rows;
	print '<div class="roomContainer">';
	if ($num>0){
	print '<table class="myTable"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">';
	echo<<<END
	<th>Nr</th>
	<th colspan="2" width="40%">Przedmiot</th>
	<th width="20%">Cena</th>
	<th>Ilość</th>
	<th>Suma</th>
	END;
    for ($i=0; $i<$num; $i++)
        {
            $row = mysqli_fetch_assoc($result);
			$no = $i+1;
			echo<<<END
			<!-- ROW 1 -->
			<tr>
			<td  align="center">$no </td>
			<td colspan="2" align="center">$row[name]</td>
			<td align="center">
			<span style="color:#733000"> $row[price] zł</span></td>
			<td align="center"><span style="color:#733000">$row[quantity]</span></td>
			<td align="center"> <span style="color:#733000; font-weight: bold;">$row[Suma] zł</span></td>
			END;
        }
		echo<<<END
			<tr>
				<th colspan="3"  style="text-align: left; font-size:24px;"> PODSUMOWANIE</th>
				<th colspan="3"  style="text-align: right; font-size:24px;"> Płatność i dostawa</th>
			</tr>
			<form action="saveOrder.php" method="POST">
				<tr>
				<th colspan="3" style="text-align: right;">Sposób płatności:</th>
				</tr>
				<tr>
					<th colspan="3"></th>
					<th colspan="3" style="text-align: left;">
					<input type="radio" id="pay1" name="pay" value="Przelew bankowy" checked="1">
					<label for="pay1">Przelew bankowy</label><br>
					<input type="radio" id="pay2" name="pay" value="Karta debetowa">
					<label for="pay2">Karta płatnicza</label><br>
					<input type="radio" id="pay3" name="pay" value="PayU">
					<label for="pay3">PayU</label><br>
					<input type="radio" id="pay4" name="pay" value="PayPal">
					<label for="pay4">PayPal</label>
					</th>
				</tr>

				<tr>
				<th colspan="3" style="text-align: right;">Wysyłka:</th>
				</tr>
				<tr>
					<th colspan="3"></th>
					<th colspan="3" style="text-align: left;">
					<input type="radio" id="post1" name="post" value="1" checked="1" >
					<label for="post1">Paczkomaty 24h <span style="color:red;"> (8.99 zł)</span></label><br>
					<input type="radio" id="post2" name="post" value="2">
					<label for="post2">Kurier<span style="color:red;"> (12.45 zł)</span></label><br>
					<input type="radio" id="post3" name="post" value="3">
					<label for="post3">Poczta - paczka polecona <span style="color:red;"> (18.91 zł)</span></label>
					</th>
				</tr>

				<!-- PODSUMOWANIE -->
			END;

				echo '<tr>';
				echo '<td colspan="5" style="text-align: right;">Netto:</td>';
				echo '<td class="accentRow">' . round($row['Overall']-($row['Overall']/100*23),2) . ' zł</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td colspan="5" style="text-align: right;">Brutto:</td>';
				echo '<td class="accentRow">' . $row['Overall'] . ' zł</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td colspan="5" style="text-align: right;"> + Wysyłka:</td>';
				echo '<td class="accentRow2"> <span id="wholePrice">' . $row['Overall']+8.4 . ' zł</span></td>';
				echo '<input type="hidden" id="sumPriceId" name="sumPrice" value='.$row['Overall'].'>';
				echo '</tr>';

		echo<<<END
				<!-- ZAMÓW -->
				<tr>
				<th colspan="5"></th>
				<th><button class="btn3" type="submit">Płacę i zamawiam!</button>
				<input type="hidden" value="dellAll" name="mode">
				</th>
				</tr>
			</form>
			<tr>
				<!-- ANULOWANIE -->
				<th colspan="5"></th>
				<th><a href="index.php"><button class="btn3" type="button">Anuluj</button></a></th>
			</tr>
		</table>
		</div>
		END;
	} // num > 0
	else
	{
		echo "<h2>Coś poszło nie tak! Brak przedmiotów do zamówienia!</h2></div>";
	}
}
}

?>
<script>
// aktualizacja suma+wysylka
var pHolder = document.getElementById("wholePrice")
var sumPrice = parseFloat(document.getElementById("sumPriceId").value)
document.getElementById("post1").addEventListener('change', diffCheck)
document.getElementById("post2").addEventListener('change', diffCheck)
document.getElementById("post3").addEventListener('change', diffCheck)

function diffCheck(e){
  if (event.currentTarget.checked) {
	var sender = document.querySelector('input[name="post"]:checked').value
	var senVar = 1
	if (sender=='1') senVar=8.99
	if (sender=='2') senVar=12.45
	if (sender=='3') senVar=18.91
	var overall = (parseFloat(sumPrice+senVar)).toFixed(2)
	pHolder.innerHTML = overall+" zł"
	console.log("Var: "+senVar+" / nr: "+sender)
  }
}
</script>

</div> <!--content-->

<footer>
	<?php echo "Markowiak &copy ".date("Y-m-d");?>
</footer>
</body>

</html>


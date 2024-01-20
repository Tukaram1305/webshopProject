var	basketResSet = {};

function incItem(itemID)
{
    itemNum = document.getElementById(itemID).value
    console.log(itemID+" Num: "+itemNum)

    document.getElementById("hidden_"+itemID).value = itemNum
}

function addToBasket(user, item)
{
    quant = document.getElementById(item).value
    //alert("Dodano "+quant+" sztuk do koszyka!")

    var req = "user_id="+user+"&item_id="+item+"&quantity="+quant
    console.log("Req: "+req)
    var xhr = new XMLHttpRequest();		
    //HTTPReq
    xhr.open("POST", "../sklep/toBasket.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(req);
    xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText)
            updateBasketItemsNum(user)
            showAddPrompt(quant)
        }
    };
}

function updateBasketItemsNum(user)
{
    basketHolder = document.getElementById("basket_items_bg")

    var req = "user_id="+user
    var xhr = new XMLHttpRequest();		
    //HTTPReq
    xhr.open("POST", "../sklep/updateBasket.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(req);
    xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
            basketHolder.innerHTML = this.responseText

        }
    };
}



function showAddPrompt(quant)
{
    var prom = document.getElementById("basketNote")
    prom.innerHTML = "<br><br>Dodano "+quant+" sztuk(i) do koszyka!"
    prom.style.top = "-80px"
    setTimeout(function(){
        prom.style.top = "-180px"

    },1000)
}

function hideOrder(e)
{
	//var oId = document.getElementById(e).value
	console.log("Tutaj FOO!, odzywa sie: "+e.srcElement.id)
	if (document.getElementById("tab_"+e.srcElement.value).style.display=="table")
	{
		document.getElementById("tab_"+e.srcElement.value).style.display="none"
		document.getElementById(e.srcElement.id).innerHTML = "<i class='fa fa-angle-double-down' style='font-size:24px'></i>ROZWIŃ"
	}
	else 
	{
		document.getElementById("tab_"+e.srcElement.value).style.display="table"
		document.getElementById(e.srcElement.id).innerHTML = "<i class='fa fa-angle-double-up' style='font-size:24px'></i>ZWIŃ"
	}
}
function chngStatus(e)
{
	//console.log("ASD: "+e.srcElement.value+" x: "+e.srcElement.id)
	req="ordID="+e.srcElement.id+"&status='"+e.srcElement.value+"'"
	console.log(req)
    var xhr = new XMLHttpRequest();		
    //HTTPReq
    xhr.open("POST", "../sklep/chngOrdStatus.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(req);
    xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText)
        }
    };
}

function showPopupBasket()
{
	basketPopup.style.visibility = "visible"
	basketPopup.style.opacity = "1"
	bHolderY = document.getElementById("basket").offsetTop
	bHolderX = document.getElementById("basket").offsetLeft
	basketPopup.style.top = bHolderY+48
	basketPopup.style.left = bHolderX
	basketPopup.innerHTML = "Zawartość koszyka<br><br>"

	console.log("Typ: "+typeof basketResSet)
	//console.log(basketResSet)

	var suma = 0;
	for (let i=0; i<basketResSet.length; i++)
	{
		//console.log("PETLA: "+i+"   / "+basketResSet[0][2])
		suma+=parseFloat(basketResSet[i].Suma)
		basketPopup.innerHTML += "<div class='tinyDiv'><img  class='tinyImage' src='"+ basketResSet[i].image+"'>"+basketResSet[i].quantity+" x "+basketResSet[i].name+" ("+basketResSet[i].Suma+" zł)</div>"
    }
	basketPopup.innerHTML += "<span style='color:white; background:black;'>Łącznie do zapłaty: "+suma.toFixed(2,10)+" zł</span>"
}

function hidePopupBasket()
{
	basketPopup.style.visibility = "hidden"
	basketPopup.style.opacity = "0"
}

function updateBasketSetResult(user)
{
    var req = "user_id="+user
    console.log("REQUEST: "+req)
    var xhrA = new XMLHttpRequest();		
    //HTTPReq
    xhrA.open("POST", "../sklep/getb.php", true);
    xhrA.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhrA.send(req);
    xhrA.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
            var basketResSetTXT = this.responseText
            basketResSet = JSON.parse(basketResSetTXT)
            console.log("ZWROCONY JSON: "+basketResSet)
        }
    };
}


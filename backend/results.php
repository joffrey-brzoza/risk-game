<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="../css/style.css">
    <style>
    body {background:none transparent;}
    </style>

<title>Risk Game Results</title>

<script src="../js/riskGame.js"></script>
<script>

const initGame = (data) => {
    initData(data);
    play();
}

function receive(event) {
    let data = JSON.parse(event.data);
    console.log("Received Message : ", data);
    document.getElementById("rawdata_amount").innerHTML = data.amount;
    document.getElementById("rawdata_currency").innerHTML = data.currency;
    document.getElementById("rawdata_shopperCountry").innerHTML = data.shopperCountry;
    document.getElementById("rawdata_deliveryCountry").innerHTML = data.deliveryCountry;
    document.getElementById("rawdata_accountAge").innerHTML = data.accountAge;
    initGame(data);
}

const initTranslation = () => {
    translation.forEach(item => {
      if (document.getElementById("result_" + item.id)) {
        document.getElementById("result_" + item.id).innerHTML = item[lang];
      }
    })
}

window.addEventListener('message', receive);
</script>

</head>

<body onload=initTranslation()>

<div class="resultsParent">

    <div class="upperResult"><div class="approved" id="approvedDeclined">-</div><div class="finalrisk" id="finalrisk"></div></div>
    <div class="midResult"></div>
    <div class="lowerResult">
        <div class="oneResult"><div id="result_amount"></div><img src="img/amount.png" width="40"></div>
        <div class="rawdata" id="rawdata_amount"></div>
        <div class="textResult norisk" id="CustomFieldCheck-AmountCheck">-</div>
        
        <div class="oneResult"><div id="result_currency"></div><img src="img/ccy_jpy.png" class="categoryIcon"></div>
        <div class="rawdata" id="rawdata_currency"></div>
        <div class="textResult norisk" id="CustomFieldCheck-CurrencyCheck">-</div>
            
        <div class="oneResult"><div id="result_shopper_location"></div><img src="img/shooperCountry.png" class="categoryIcon"></div>
        <div class="rawdata" id="rawdata_shopperCountry"></div>
        <div class="textResult norisk" id="CustomFieldCheck-ShopperCountryCodeCheck">-</div>

        <div class="oneResult"><div id="result_delivery_location"></div><img src="img/playAgain.png" class="categoryIcon"></div>
        <div class="rawdata" id="rawdata_deliveryCountry"></div>
        <div class="textResult norisk" id="CustomFieldCheck-DeliveryCountryCheck">-</div>

        <div class="oneResult"><div id="result_account_age"></div><img src="img/account.png" class="categoryIcon"></div>
        <div class="rawdata" id="rawdata_accountAge"></div>
        <div class="textResult norisk" id="CustomFieldCheck-AccountAgeLessThanAWeek">-</div>
    </div>

</div>

</html>
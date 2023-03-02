const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
let lang = urlParams.get('lang') || "EN";
lang = lang?.toUpperCase();
const availableLang = ['EN','JP'];
lang = availableLang.includes(lang) ? lang : "EN";

console.log("LANG:", lang);

const translation = [
    { id:"amount", "EN":"Amount", "JP":"購入額"},
    { id:"currency", "EN":"Currency", "JP":"結果"},
    { id:"shopper_location", "EN":"Shopper location", "JP":"ショッパーの場所"},
    { id:"delivery_location", "EN":"Delivery location ", "JP":"配送場所"},
    { id:"account_age", "EN":"Account age", "JP":"利用年数"},
    { id:"transaction_declined", "EN":"Transaction declined", "JP":"決済拒否"},
    { id:"transaction_authorized", "EN":"Transaction authorized", "JP":"決済承認"},
    { id:"risk_rule_triggered", "EN":"Risk rule triggered", "JP":"リスクあり"},
    { id:"risk_rule_not_triggered", "EN":"Risk rule not triggered", "JP":"リスクなし"},
    { id:"logout", "EN":"Logout", "JP":"ログアウト"},
    { id:"welcome_start", "EN":"Welcome", "JP":"ようこそ、"},
    { id:"welcome_end", "EN":"", "JP":"さん"},
];

const getTranslation = (text) => {
    const item = translation.find(itm => itm.id === text);
    return item[lang];
}

let transaction = {}

const amountList = [500, 1000, 5000, 8000, 10000, 15000, 50000, 150000];
const currencyList = ["AUD", "CNY", "EUR", "HKD", "SGD", "USD", "JPY"];
const shopperCountryList = ["AU", "CN", "HK", "JP", "SG", "KR", "TW"];
const deliveryCountryList = ["AU", "CN", "HK", "JP", "SG", "KR", "TW"];
const accountAgeList = [10, 100, 240, 8760, 43800];

const resultRulesList = ["CustomFieldCheck-AmountCheck","CustomFieldCheck-CurrencyCheck","CustomFieldCheck-ShopperCountryCodeCheck","CustomFieldCheck-DeliveryCountryCheck","CustomFieldCheck-AccountAgeLessThanAWeek"]

const initData = (data) => {
    transaction = data;
}

async function play() {

    cleanUp();
    const url = "getAuthorization.php";
    const dbUrl = "insertToDatabase.php";
    const data = {
      "amount":transaction.amount,
      "currency":transaction.currency,
      "shopperCountry":transaction.shopperCountry, 
      "deliveryCountry":transaction.deliveryCountry, 
      "accountAge":transaction.accountAge,
      "shopperReference":transaction.shopperReference
    };
    
    console.log("Call the /Payments API with following data => " , data);
    let res = await callServer(url, data);

    console.log("Result is > ", res);
    displayResult(res);

    const dbData =
    {
        "pspReference": (res) ? res.pspReference : "",
        "shopperReference":(res) ? transaction.shopperReference : "",
        "fraudScore":(res) ? res.fraudResult.accountScore : "",
        "resultCode":(res) ? res.resultCode : ""
    }
    let dbRes = await insertToDatabase(dbUrl,dbData);

};
  
async function callServer(url, data) {
  
    const res = await fetch(url, {
        method: "POST",
      body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json"
        }
    })
  
    try {
        data = await res.json();
        return data;
    }
    catch(err) {
        return null;
    }
}

async function insertToDatabase(url, data) {
  
    console.log(data);

    const res = await fetch(url, {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json"
        }
    })
    try {
        data = await res.json();
        return data;
    }
    catch(err) {
        return null;
    }
}

const cleanUp = () => {

    // Remove risk score
    document.getElementById("finalrisk").innerHTML = "";
    
    // Remove all risk rules
    for (let item of resultRulesList) {
        document.getElementById(item).innerHTML = "-";
        document.getElementById(item).style.color = "black";
    }

    // Remove status color
    document.getElementById("approvedDeclined").innerHTML = "-";
    document.getElementById("approvedDeclined").style.backgroundColor = "black";
}

const displayResult = (data) => {

    // Handle API returns error
    if (data.status === 500) {
        alert("Transaction cannot be processed");
        return
    }
    if (data.status === 401) {
        alert("Not authorized");
        return
    }

    console.log("Risk data > ", data);

    // Display risk score
    const resultscore =  parseInt(data.fraudResult.accountScore);
    document.getElementById("finalrisk").innerHTML = resultscore +"/100";

    // Display transaction status
    if (resultscore >= 100) {
        document.getElementById("approvedDeclined").innerHTML = getTranslation("transaction_declined", lang);
        document.getElementById("approvedDeclined").style.backgroundColor = "rgba(255,0,0,0.95)";
    }
    else {
        document.getElementById("approvedDeclined").innerHTML = getTranslation("transaction_authorized", lang);
        document.getElementById("approvedDeclined").style.backgroundColor = "#0ABF53";
    }
    
    // Display result for each risk rule
    const customRulesCheckList = data.fraudResult.results

    for (let item of resultRulesList) {
        document.getElementById(item).innerHTML = getTranslation("risk_rule_not_triggered", lang);
    }

    for (let item of customRulesCheckList) {
        document.getElementById(item.name).innerHTML = getTranslation("risk_rule_triggered", lang) + "[+" + item.accountScore + "]";
        document.getElementById(item.name).style.color = "red";
    }
}

async function cardAcquisiton(terminalID) {

    let url = "../backend/cardAcquisition.php";
    let data = {
      "terminalID":terminalID
    };
    
    console.log("Call the TAPI for CardAcqusition with following data => " , data);
    let res = await callServer(url, data);
  
    if (!res) {
        return "Guest";
    }

    console.log("Result is > ", res);
    console.log("Result is > ", res.SaleToPOIResponse.CardAcquisitionResponse.Response.AdditionalResponse);
    let valuesList = res.SaleToPOIResponse.CardAcquisitionResponse.Response.AdditionalResponse.split("&");
    let propertiesObj = {}
    for (let itm of valuesList) {
        let values = itm.split("="); 
        let prop = values[0]; 
        let val = values[1]; 
        propertiesObj[prop] = val;
    }
    let shopperReference = propertiesObj.shopperReference;
    shopperReference = shopperReference.replace(/%20/g, " ");
    console.log("shopperReference is > ", shopperReference);
    
    if (!shopperReference) {
        shopperReference = "Guest";
    }

    url = "../backend/cardAcquisitionRelease.php";
    data = {
      "shopperReference":shopperReference,
      "terminalID":terminalID
    };
    
    console.log("Call the TAPI for CardAcqusitionRelease with following data => " , data);
    res = await callServer(url, data);

    console.log("Result of release > ", res)

    return shopperReference;
}

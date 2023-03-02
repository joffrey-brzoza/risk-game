<!DOCTYPE html>
<html lang="en">
<head>
    <title>Risk Game</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <script src="./js/riskGame.js"></script>

<script>
let shopperReference = "";

const startDev = async() =>
{
  let tap = document.getElementById('tap');
  let shopperName = document.getElementById('shopperName');
  let welcome = document.getElementById('welcome');
  let gameMain = document.getElementById('gameMain');

  // Hide title
  document.getElementById('start').style.visibility = 'hidden';

  // Shot tap your card
  //document.getElementById('tap').style.visibility = "visible";
  
  // Call card acquisition
  //let terminalID = document.getElementById('terminalID').value;
  //shopperReference = await cardAcquisiton(terminalID);
  shopperReference = "User";

  if (tap) tap.style.visibility = "hidden";
  if (shopperName) {
    shopperName.innerHTML  = getTranslation("welcome_start", lang) + " " + shopperReference + getTranslation("welcome_end", lang);
  }
  if (gameMain) {
    gameMain.style.visibility = "visible";
    welcome.style.visibility = "visible";
  }
}

const start = async () => {

    // Hide title
    document.getElementById('start').style.visibility = 'hidden';
    
    // Shot tap your card
    document.getElementById('tap').style.visibility = "visible";
    
    // Call card acquisition
    let terminalID = document.getElementById('terminalID').value;
    shopperReference = await cardAcquisiton(terminalID);
    
    // Hide tap your card
    document.getElementById('tap').style.visibility = "hidden";
    
    // Show welcome message
    document.getElementById('shopperName').innerHTML = getTr + shopperReference;
    document.getElementById('gameMain').style.visibility = "visible";
}

const exit = () => {
    location.reload();
}

const assignTerminalId = () => {
    document.cookie = "terminalId="+document.getElementById('terminalID').value;
}

const init = () => {

    // READ TERMINALID FROM COOKIE IF PRESENT
    if (document.cookie) {
        let terminalID = document.cookie.split("=")[1];
        document.getElementById('terminalID').value = terminalID;
    }

    // SET LABEL / TEXT TO THE LANGUAGE RECEIVED IN THE URL
    translation.forEach(item => {
      if (document.getElementById(item.id)) {
        document.getElementById(item.id).innerHTML = item[lang];
      }
    })

    // PASS THE LANGUAGE TO THE RESULT PAGE AS WELL
    document.getElementById('gameIframe').src = "backend/results.php?lang=" + lang;

    startDev();
}

const pullHandle = () =>
{
  console.log("Pulling handle");
  let handle = document.getElementById('handle');
  console.log("Handle: "+handle);

 //if(handle) handle = "assets/risk game apac handle2.svg";
}

</script>

</head>

<body onload="init()">

<div class="main">

<div class="start" id="start"><div onclick="startDev()">TOUCH TO START THE GAME</div>
<div class="terminalID"><input id="terminalID" value="V400m-347148879"/><button class="terminalIDButton" onclick="assignTerminalId()">assign</button></div></div>
<div class="tap" id="tap">TAP YOUR CARD ON THE TERMINAL</div>

<div id="parentContainer">
  <div class="gameMain" id="gameMain">
  <div id="welcome" class="welcome">
    <div id="shopperName"></div>
      <button class="logoutButton" onClick="exit()"><div id="logout"></div></button>
    </div>
    <div id="slot">
      <div id="headingsContent">
        <div class="heading"><div id="amount"></div></div>
        <div class="heading"><div id="currency"></div></div>
        <div class="heading"><div id="shopper_location"></div></div>
        <div class="heading"><div id="delivery_location"></div></div>
        <div class="heading"><div id="account_age"></div></div>
      </div>
      <div id="reels">
        <div class="reel"></div>
        <div class="reel"></div>
        <div class="reel"></div>
        <div class="reel"></div>
        <div class="reel"></div>
      </div>
    </div>

    <button id="handle"></button>
  </div>
</div>

<script src="../js/all.js"></script>

<div class="gameDiv" id="gameDiv">
    <div class="popup">
        <iframe title="Game iframe" class="gameIframe" id="gameIframe" src="backend/results.php">
        </iframe>
  </div>
</div>


</div>

</body>

</html>

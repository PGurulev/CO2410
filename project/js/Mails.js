let MailsFileName = "AssetsAndExamples/JsonFiles/real_emails.json";
let FakeMailsFileName = "AssetsAndExamples/JsonFiles/phishing_emails.json";
let LeaderBoardsFile = "AssetsAndExamples/JsonFiles/LeaderBoard.json";
// source for array filling https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/fill
const MailsCheckArray = Array(240).fill(0);
let FinalMailsArray = null
let mailCounter = 0
// sorce code for IIFE async https://www.w3schools.com/nodejs/shownodejs_cmd.asp?filename=demo_esm_dynamic
// TO DO Ask about IIFE and why it is not working
/*
(async () => {
    FinalMailsArray = await MailsArrayDetermination(MailsFileName);
    FinalMailsArray = await ShuffleMailArray(FinalMailsArray);
    console.log("Data loaded succesfully");
})();
// source code for array shuffle https://www.geeksforgeeks.org/javascript/how-to-shuffle-the-elements-of-an-array-in-javascript/
async function ShuffleMailArray(array) {
    for (let i = array.length-1; i>=0; i--){
        let j = Math.floor(Math.random() * (i+1));
        [array[i], array[j]] = [array[j], array[i]];
    }    
    return array;
}
*/
let score = 0;
let tries = 0;
let Difficulty = "";
let Nickname = "";
async function StartGame(){
    GameInterval = null;
    score = 0;
    PerMailStart = null;
    PerMailTimes = [];
    var StartingContent = document.getElementById("MainPage");
    StartingContent.style.display = "none";
    var AboutContent = document.getElementById("AboutPage");
    AboutContent.style.display = "none";
    var ContactContent = document.getElementById("ContactsPage");
    ContactContent.style.display = "none";
    var NicknameContent = document.getElementById("PlayerIdPage");
    NicknameContent.style.display = "none";
    var EmailContent = document.getElementById("GamePage");
    EmailContent.style.display = "flex";
    StartTimer();
    GetNextMail();

    const mailCard = document.querySelector('.mail-card');
    if (mailCard) {
        mailCard.classList.add('mail-card--active');
    }
}

async function JSONTransmitter(filename){
    let FetchedData = await (await fetch(filename)).json();
    return FetchedData;
}

async function MailsArrayDetermination(filename) {
    let res = await JSONTransmitter(filename);
    //console.log(res.mails);
    return res.mails;
}
async function GetFullEmailsArray(){
    let RealRes = await JSONTransmitter(MailsFileName);
    let FakeRes = await JSONTransmitter(FakeMailsFileName);
    //content for set https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/map
    //content for ... syntax https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/Spread_syntax
    RealRes = RealRes.mails.map(mail =>({...mail, isFake:false}));
    FakeRes = FakeRes.mails.map(mail =>({...mail, isFake:true}));
    //console.log(RealRes);
    //content for ... syntax https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/Spread_syntax
    let combined = {
       mails: [...RealRes, ...FakeRes]
    };
    //console.log(combined);
    return combined.mails;
}
async function GetNextMail() {
    StartTimerForOneMail();
    //let FinalMailsArray = await MailsArrayDetermination(MailsFileName);
    let FinalMailsArray = await GetFullEmailsArray();
    let FreeMailIndex = await GetFreeMailIndex(FinalMailsArray);
    if(FreeMailIndex !== null){
        MailsCheckArray[FreeMailIndex] = 1;
        mailCounter+=1;
        let subject = document.getElementById("MailsSubject");
        let recievers = document.getElementById("MailsRecievers");
        let sender = document.getElementById("MailsSender");
        let content = document.getElementById("MailsBody");
        content.innerHTML =  "<p>  " + FinalMailsArray[FreeMailIndex].body + "</p>";
        subject.innerHTML = "<p>  " + FinalMailsArray[FreeMailIndex].content.subject + "</p>";
        sender.innerHTML = "<p>  " + FinalMailsArray[FreeMailIndex].content.sender.name + " " + FinalMailsArray[FreeMailIndex].content.sender.email + "</p>";
        //content for forEach() https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/forEach
        recievers.innerHTML = "";
        FinalMailsArray[FreeMailIndex].content.recievers.forEach(reciever => {
            recievers.innerHTML += "<p>  " + reciever.name + " " + reciever.email + "</p>";
        });
        localStorage.setItem('MailType', FinalMailsArray[FreeMailIndex].isFake);
    }
    else{
        alert("You completed the game!");
    }
}
async function GetFreeMailIndex(array) {
   if(mailCounter >= array.length){
    return null;
   }
   let PossibleIndex = Math.floor(Math.random() * array.length);
    do{
    PossibleIndex = Math.floor(Math.random() * array.length);

   } while(MailsCheckArray[PossibleIndex] > 0);
    return PossibleIndex;
}

function checkForCorrectness(arg)
{
    //TO DO implement Game Logic
    let EmailType = localStorage.getItem('MailType');
    let TimeForEmail = StopTimerForOneMail();
    let IsMailFake = (EmailType == "true");
    CorrectGuess = false;
    if(arg == "Fake"){
        CorrectGuess = IsMailFake;
    }
    else{
        CorrectGuess = !IsMailFake;
    }
    if(CorrectGuess){
        score += 1 + (30 - Math.min(30, TimeForEmail));
    }
    else{
        tries -= 1;
        if(tries <= 0)
        {
            GameLostByLifes();
            return;
        }
    }
    UpdateTries();
    UpdateScore();
    if(mailCounter>= 20)
    {
        PlayerWonGame();
        return;
    }
    GetNextMail();
}

function GetNickname(argument){
    Nickname = document.getElementById("nickname").value;
    if(Nickname == "")
    {
        alert("Enter your nickname");
        return;
    }
    Difficulty = argument;
    tries = GetTriesBasedOnDifficulty(Difficulty);
    UpdateTries();
    StartGame()
}
function AskUsrForNickname(){
    var StartingContent = document.getElementById("MainPage");
    StartingContent.style.display = "none";
    var AboutContent = document.getElementById("AboutPage");
    AboutContent.style.display = "none";
    var ContactContent = document.getElementById("ContactsPage");
    ContactContent.style.display = "none";
    var EmailContent = document.getElementById("GamePage");
    EmailContent.style.display = "none";
    var NicknameContent = document.getElementById("PlayerIdPage");
    NicknameContent.style.display = "flex";
}

//Timer section
const TimerDuration = 600;
let TimeLeft = TimerDuration;
let GameInterval = null;
let PerMailStart = null;
let PerMailTimes = [];

//function to make correct timer format
function FormatTimer(StartFormat){
    // padstart documentation https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/padStart
    const minutes = Math.floor(StartFormat/60).toString().padStart(2, "0");
    const seconds = (StartFormat % 60).toString().padStart(2, "0");
    return minutes + ":" + seconds;
}

//Main timer implementation
function StartTimer(){
    let TimerDiv = document.getElementById("timer");
    //clearInterval docs https://developer.mozilla.org/en-US/docs/Web/API/Window/clearInterval
    if(GameInterval !== null) return;

    if(typeof TimeLeft !== "number")TimeLeft = TimerDuration;

    GameInterval = setInterval(() => {
        TimeLeft--;
        TimerDiv.innerHTML = "Time left: " + FormatTimer(TimeLeft);
        if(TimeLeft <= 0)
        {
            GameLostByTimer();
        }
    }, 1000);
}

//A way to reset a timer to blank state
function ResetGameState(){
    score = 0;
    Difficulty = "";
    tries = 0;
    Nickname = "";
    mailCounter = 0;

    TimeLeft = TimerDuration;
    if(GameInterval){
        clearInterval(GameInterval);
        GameInterval = null;
    }

    let TimerDiv = document.getElementById("timer");
    if(TimerDiv){
        TimerDiv.innerHTML = "";
    }
    let ScoreUI = document.getElementById("ScoreBoard");
    if(ScoreUI){
        ScoreUI.innerHTML = "Score : 0";
    }

    for(let i = 0; i < MailsCheckArray.length; i++){
        MailsCheckArray[i] = 0;
    }
}


function Restart(){
    score = 0;
    mailCounter = 0;

    if(Difficulty){
        tries = GetTriesBasedOnDifficulty(Difficulty);
    } else{
        tries = 0;
    }
    UpdateTries();

    TimeLeft = TimerDuration;
    if(GameInterval){
        clearInterval(GameInterval);
        GameInterval = null;
    }

    PerMailStart = null;
    PerMailTimes = [];


    for(let i = 0; i < MailsCheckArray.length; i++){
        MailsCheckArray[i] = 0;
    }

    const TimerDiv = document.getElementById("timer");
    if(TimerDiv){
        TimerDiv.innerHTML = "";
    }
    const ScoreUI = document.getElementById("ScoreBoard");
    if(ScoreUI){
        ScoreUI.innerHTML = "Score : 0";
    }

    StartTimer();
    GetNextMail();

}

function PauseTimer(){
    if(GameInterval){
        clearInterval(GameInterval);
        GameInterval = null;
    }
}

function ContinueTimer(){
    if(!GameInterval){
        StartTimer();
    }
}

function StartTimerForOneMail(){
    PerMailStart = Date.now();
}

function StopTimerForOneMail(){
    let now = Date.now();
    let diffInSecs = Math.round((now-PerMailStart)/1000);
    PerMailTimes.push(diffInSecs);
    PerMailStart = null;
    return diffInSecs;
}


function GameLostByTimer(){
    showResultModal(
        "Time is over ⏰",
        "You ran out of time. Try again and be quicker spotting phishing emails!"
    );

}

function GameLostByLifes(){
    showResultModal(
        "No tries left ❌",
        "You made too many mistakes. Review the emails more carefully and try again!"
    );
}

function PlayerWonGame(){
    showResultModal(
        "You won! 🎉",
        "Great job! You successfully won the game!!"
    );
}



// function GameLostByTimer(){
//     alert("Lost by timer");
//     //make a seperate page and display content through that func
//
//     //database connectivity
//     //DatabaseLeaderBoardCall()
// }
//
// function GameLostByLifes(){
//     alert("Guessed incorrectly to many times");
//     //make a seperate page and display content through that func
//
//     //database connectivity
//     //DatabaseLeaderBoardCall()
// }
//
// function PlayerWonGame(){
//     alert("You Won!");
//     //make a seperate page and display content through that func
//
//     //database connectivity
//     //DatabaseLeaderBoardCall()
// }
function GetTriesBasedOnDifficulty(arg){
    if(arg == "Easy")
    {
        return 6;
    }
    else if(arg == "Medium")
    {
        return 4;
    }
    else if(arg == "Hard"){
        return 2
    }
    return 0;
}

function UpdateScore(){
    document.getElementById("ScoreBoard").innerHTML= "Score: " + score;
}


async function fillInLeaderboard(){
    let LeaderList = await JSONTransmitter(LeaderBoardsFile);
    let LeaderSection = document.getElementById("leaderBoard");
    LeaderSection.innerHTML = " ";
    console.log(LeaderList);
    LeaderList.players.forEach(player=>{
        LeaderSection.innerHTML += `<div><p>${player.name}</p><p>${player.score}</p><p>${player.timestamp}</p></div>`;
    })
}
fillInLeaderboard();

function DatabaseLeaderBoardCall(){
    // documentation for formData https://developer.mozilla.org/en-US/docs/Web/API/FormData
    let form = new FormData();
    form.append("score", score);
    form.append("nickname", Nickname);
    form.append("timestamp", Date.now().toLocaleString());
    fetch("file.php", {
        method : "POST",
        body : form
    })
}

function UpdateTries(){
    document.getElementById("TryCounter").innerHTML= "You have: " + tries + " tries to guess corretly";
}

function showResultModal(title, message) {
    PauseTimer();

    const modal = document.getElementById("ResultModal");
    const titleEl = document.getElementById("ResultTitle");
    const msgEl = document.getElementById("ResultMessage");
    const scoreEl = document.getElementById("ResultScore");
    if (!modal) return;
    if (titleEl) titleEl.textContent = title;
    if (msgEl) msgEl.textContent = message;
    if (scoreEl) scoreEl.textContent = "Your score: " + score;
    modal.style.display = "block";
    modal.setAttribute("aria-hidden", "false");
}
function closeResultModal() {
    const modal = document.getElementById("ResultModal");
    if (!modal) return;
    modal.style.display = "none";
    modal.setAttribute("aria-hidden", "true");
}

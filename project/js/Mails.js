let MailsFileName = "AssetsAndExamples/JsonFiles/real_emails.json";
// source for array filling https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/fill
const MailsCheckArray = Array(20).fill(0);
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

async function StartGame(){
    var StartingContent = document.getElementById("MainPage");
    StartingContent.style.display = "none";
    var EmailContent = document.getElementById("GamePage");
    EmailContent.style.display = "flex";
    GetNextMail();
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
async function GetNextMail() {
    let FinalMailsArray = await MailsArrayDetermination(MailsFileName);
    let FreeMailIndex = await GetFreeMailIndex(FinalMailsArray);
    if(FreeMailIndex !== null){
        MailsCheckArray[FreeMailIndex] = 0;
        mailCounter+=1;
        let subject = document.getElementById("MailsSubject");
        let recievers = document.getElementById("MailsRecievers");
        let sender = document.getElementById("MailsSender");
        let content = document.getElementById("MailsBody");
        content.innerHTML =  "<p>" + FinalMailsArray[mailCounter].body + "</p>";
        subject.innerHTML = "<p>" + FinalMailsArray[mailCounter].content.subject + "</p>";
        sender.innerHTML = "<p>" + FinalMailsArray[mailCounter].content.sender.name + " " + FinalMailsArray[mailCounter].content.sender.email + "</p>";
        //content for forEach() https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/forEach
        recievers.innerHTML = "";
        FinalMailsArray[mailCounter].content.recievers.forEach(reciever => {
            recievers.innerHTML += "<p>" + reciever.name + " " + reciever.email + "</p>";
        });
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
   let Tries = 0;
   let MaxTries = array.length**2;
    do{
    PossibleIndex = Math.floor(Math.random() * array.length);
    Tries+=1;

   } while(MailsCheckArray[PossibleIndex] > 0);
    return PossibleIndex;
}

function checkForCorrectness(string)
{
    //TO DO implement Game Logic
    if(string == "Fake")
    {

    }
    else{

    }
    GetNextMail();
}

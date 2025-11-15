async function GenerateMailsMenu()
{
    var StartingContent = document.getElementById("MainPage");
    StartingContent.style.display = "none";
    var EmailContent = document.getElementById("GamePage");
    EmailContent.style.display = "flex";
    var JsonInfo = await JSONTransmitter("AssetsAndExampless/JsonFiles/real_emails.json");
}
async function JSONTransmitter(filename)
{
    let FetchedData = (await fetch(filename)).json();
    console.log(FetchedData);
}
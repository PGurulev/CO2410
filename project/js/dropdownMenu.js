function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
function goback(){
    var StartingContent = document.getElementById("MainPage");
    StartingContent.style.display = "block";
    var EmailContent = document.getElementById("GamePage");
    EmailContent.style.display = "none";
    var AboutContent = document.getElementById("AboutPage");
    AboutContent.style.display = "none";
    var StartingContent = document.getElementById("MainPage");
    var ContactContent = document.getElementById("ContactsPage");
    ContactContent.style.display = "none";
    var NicknameContent = document.getElementById("PlayerIdPage");
    NicknameContent.style.display = "none";
    mailCounter = 0
}

function about(){
    var StartingContent = document.getElementById("MainPage");
    StartingContent.style.display = "none";
    var EmailContent = document.getElementById("GamePage");
    EmailContent.style.display = "none";
    var AboutContent = document.getElementById("AboutPage");
    AboutContent.style.display = "block";
    var ContactContent = document.getElementById("ContactsPage");
    ContactContent.style.display = "none";
    var NicknameContent = document.getElementById("PlayerIdPage");
    NicknameContent.style.display = "none";
}

function contacts() {
    var StartingContent = document.getElementById("MainPage");
    StartingContent.style.display = "none";
    var AboutContent = document.getElementById("AboutPage");
    AboutContent.style.display = "none";
    var ContactContent = document.getElementById("ContactsPage");
    ContactContent.style.display = "block";
    var EmailContent = document.getElementById("GamePage");
    EmailContent.style.display = "none";
    var NicknameContent = document.getElementById("PlayerIdPage");
    NicknameContent.style.display = "none";
}

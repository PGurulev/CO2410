function loadLeaderboard() {
    fetch("./project/php/leaderboard.php") //Refer from php part for logic part of js
        .then(res => res.json())
        .then(data => {
            const list = data.players; //Create list of player's data for leaderboard

            let html = 
                <h2 class="leaderboard-title">Leaderboard</h2>
                <table class="leaderboard"> //Add main values to display what result, who played and when
                    <tr>
                        <th>Player</th>
                        <th>Score</th>
                        <th>Timestamp<th>
                    </tr>
            ;
            //Create the list for each player which played
            list.forEach(player => {
                html += 
                    <tr>
                        <td>${player.name}</td>
                        <td>${player.score}</td>
                        <td>${player.timestamp}</td>  
                    </tr>
                ;
            });

            html += "</table>";

            document.getElementById("leaderBoard").innerHTML = html; //Add the user in leaderboard
        });
}



window.addEventListener("load", loadLeaderboard);


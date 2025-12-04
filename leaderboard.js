function loadLeaderboard() {
    fetch("project/php/leaderboard.php")
        .then(res => res.json())
        .then(data => {
            const list = data.players;

            let html = 
                <h2 class="leaderboard-title">Leaderboard</h2>
                <table class="leaderboard">
                    <tr>
                        <th>Player</th>
                        <th>Score</th>
                        <th>Timestamp<th>
                    </tr>
            ;

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

            document.getElementById("leaderBoard").innerHTML = html;
        });
}



window.addEventListener("load", loadLeaderboard);

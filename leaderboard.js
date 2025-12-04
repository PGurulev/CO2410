function loadLeaderboard() {
    fetch("project/php/leaderboard.php")
        .then(res => res.json())
        .then(data => {
            const list = data.players;

            let html = 
                <h2 class="leaderboard-title">Leaderboard</h2>
                <table class="leaderboard-table">
                    <tr>
                        <th>Player</th>
                        <th>Score</th>
                    </tr>
            ;

            list.forEach(player => {
                html += 
                    <tr>
                        <td>${player.name}</td>
                        <td>${player.score}</td>
                    </tr>
                ;
            });

            html += "</table>";

            document.getElementById("leaderBoard").innerHTML = html;
        });
}


window.addEventListener("load", loadLeaderboard);
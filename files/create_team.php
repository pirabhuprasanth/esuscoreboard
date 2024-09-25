<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Team</title>
    <script>
        // Fetch matches and display them for the selected tournament
        function updateMatches() {
            var tournamentId = document.getElementById("tournament_name").value;
            var matchesList = document.getElementById("matches_list");
            matchesList.innerHTML = ""; // Clear the list before updating

            if (tournamentId) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "get_matches.php?tournament_id=" + encodeURIComponent(tournamentId), true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var matches = JSON.parse(xhr.responseText);
                        for (var matchId in matches) {
                            if (matches.hasOwnProperty(matchId)) {
                                var option = document.createElement("option");
                                option.value = matchId;
                                option.textContent = matches[matchId].name; // Display match name
                                matchesList.appendChild(option);
                            }
                        }
                    }
                };
                xhr.send();
            }
            updateTeams(); // Fetch all teams when a tournament is selected
        }

        function updateTeams() {
            var tournamentId = document.getElementById("tournament_name").value;
            var teamsList = document.getElementById("existing_teams");
            var debugOutput = document.getElementById("debug_output");
            teamsList.innerHTML = ""; // Clear the list before updating
            debugOutput.innerHTML = ""; // Clear debug output

            if (tournamentId) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "get_teams.php?tournament_id=" + encodeURIComponent(tournamentId), true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        debugOutput.innerHTML = xhr.responseText;

                        try {
                            var data = JSON.parse(xhr.responseText);
                            if (data && typeof data === 'object') {
                                var matchCount = 0;

                                for (var mid in data) {
                                    if (data.hasOwnProperty(mid) && mid.startsWith("mname_")) {
                                        var matchId = mid.split("_")[1]; // Extract match ID
                                        var match = {
                                            name: data[`mname_${matchId}`],
                                            date: data[`mdate_${matchId}`],
                                            time: data[`mtime_${matchId}`],
                                            location: data[`mlocation_${matchId}`]
                                        };

                                        var matchHeading = document.createElement("h4");
                                        matchHeading.textContent = `Match: ${match.name} (${match.date}, ${match.time}, ${match.location})`;
                                        teamsList.appendChild(matchHeading);

                                        var teamCount = 1;
                                        while (data[`${matchId}_team_name${teamCount}`]) {
                                            var teamName = data[`${matchId}_team_name${teamCount}`];
                                            var teamLogo = data[`${matchId}_team_logo${teamCount}`];

                                            var teamInfo = document.createElement("ul");
                                            teamInfo.innerHTML = `<li>Team: ${teamName} <img src='${teamLogo}' alt='Team Logo' width='50'>
                                                <button type='button' onclick='deleteTeam("${matchId}", "${teamCount}")'>Delete Team</button>
                                            </li>`;

                                            var playerCount = 1;
                                            while (data[`${matchId}_team${teamCount}_player_name_${playerCount}`]) {
                                                var playerName = data[`${matchId}_team${teamCount}_player_name_${playerCount}`];
                                                var playerImage = data[`${matchId}_team${teamCount}_player_image_${playerCount}`];

                                                var playerInfo = document.createElement("li");
                                                playerInfo.innerHTML = `Player ${playerCount}: ${playerName} <img src='${playerImage}' alt='Player Image' width='50'>
                                                    
                                                `;
                                                teamInfo.appendChild(playerInfo);

                                                playerCount++;
                                            }
                                            // Delete player button
                                            // <button type='button' onclick='deletePlayer("${matchId}", "${teamCount}", "${playerCount}")'>Delete Player</button>
                                            teamsList.appendChild(teamInfo);
                                            teamCount++;
                                        }

                                        matchCount++;
                                    }
                                }

                                if (matchCount === 0) {
                                    teamsList.innerHTML = "<li>No matches found for this tournament.</li>";
                                }
                            } else {
                                teamsList.innerHTML = "<li>No data found for this tournament.</li>";
                            }
                        } catch (e) {
                            teamsList.innerHTML = "<li>Error parsing server response.</li>";
                        }
                    } else {
                        teamsList.innerHTML = "<li>Error fetching data (status " + xhr.status + ").</li>";
                    }
                };
                xhr.onerror = function() {
                    teamsList.innerHTML = "<li>Error connecting to the server.</li>";
                };
                xhr.send();
            } else {
                teamsList.innerHTML = "<li>Select a tournament to view teams and players.</li>";
            }
        }

        // Function to delete a team
        function deleteTeam(tournamentId, matchId, teamCount) {
            if (confirm("Are you sure you want to delete this team?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_team.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert(xhr.responseText); // Display response from server
                        updateTeams(); // Refresh the team list after deletion
                    } else {
                        alert("Error deleting team.");
                    }
                };
                xhr.send("tournament_id=" + encodeURIComponent(tournamentId) +
                    "&match_id=" + encodeURIComponent(matchId) +
                    "&team_count=" + encodeURIComponent(teamCount));
            }
        }

        // Function to delete a player
        // function deletePlayer(matchId, teamCount, playerCount) {
        //     if (confirm("Are you sure you want to delete this player?")) {
        //         var xhr = new XMLHttpRequest();
        //         xhr.open("POST", "delete_player.php", true);
        //         xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //         xhr.onload = function() {
        //             if (xhr.status === 200) {
        //                 alert("Player deleted successfully.");
        //                 updateTeams(); // Refresh the team and player list after deletion
        //             } else {
        //                 alert("Error deleting player.");
        //             }
        //         };
        //         xhr.send("match_id=" + encodeURIComponent(matchId) + "&team_count=" + encodeURIComponent(teamCount) + "&player_count=" + encodeURIComponent(playerCount));
        //     }
        // }


        // Function to filter the teams based on the input in the search field
        function filterTeams() {
            var filter = document.getElementById("team_filter").value.toLowerCase();
            var teamsList = document.getElementById("existing_teams");
            var teams = teamsList.getElementsByTagName("li");

            for (var i = 0; i < teams.length; i++) {
                var teamText = teams[i].textContent || teams[i].innerText;
                if (teamText.toLowerCase().indexOf(filter) > -1) {
                    teams[i].style.display = "";
                } else {
                    teams[i].style.display = "none";
                }
            }
        }

        function addPlayer() {
            var playerList = document.getElementById('player_list');
            var newPlayer = document.createElement('div');
            newPlayer.classList.add('player-entry');
            newPlayer.innerHTML = `
                <label>Player Name: <input type="text" name="player_names[]" required></label>
                <label>Player Image: <input type="file" name="player_images[]" accept="image/*"></label>
                <button type="button" onclick="this.parentElement.remove()">Remove Player</button>
            `;
            playerList.appendChild(newPlayer);
        }
    </script>
</head>

<body>
    <?php
    // Directory where tournament JSON files are stored
    $tournamentDir = 'tournaments/';
    $tournament_names = [];

    // Scan for tournament files
    if (is_dir($tournamentDir) && is_readable($tournamentDir)) {
        $tournamentFiles = glob($tournamentDir . '*.json');
        foreach ($tournamentFiles as $tournamentFile) {
            $tournamentData = json_decode(file_get_contents($tournamentFile), true);
            if ($tournamentData && isset($tournamentData['name'])) {
                $tournament_names[] = [
                    'file' => basename($tournamentFile),
                    'name' => $tournamentData['name'],
                    'tournament_id' => $tournamentData['tournament_id'],
                ];
            }
        }
    }
    ?>

    <form action="save_team.php" method="POST" enctype="multipart/form-data">
        <label for="tournament_name">Select Tournament:</label>
        <select name="tournament_id" id="tournament_name" onchange="updateMatches()">
            <option value="">Select a tournament</option>
            <?php foreach ($tournament_names as $tournament): ?>
                <option value="<?php echo htmlspecialchars($tournament['tournament_id']); ?>">
                    <?php echo htmlspecialchars($tournament['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="matches_list">Select Match:</label>
        <select name="match_id" id="matches_list">
            <option value="">Select a match</option>
        </select>

        <label for="team_name">Team Name:</label>
        <input type="text" name="team_name" id="team_name" required>

        <label for="team_logo">Team Logo:</label>
        <input type="file" name="team_logo" id="team_logo" accept="image/*" required>

        <div id="player_list">
            <h3>Players</h3>
            <!-- Existing player input fields -->
        </div>
        <button type="button" onclick="addPlayer()">Add Player</button>

        <button type="submit">Create Team</button>
    </form>

    <h3>Existing Teams</h3>
    <input type="text" id="team_filter" onkeyup="filterTeams()" placeholder="Filter teams by name">

    <div id="existing_teams">
        <!-- Existing teams will be displayed here -->
    </div>

    <div id="debug_output"></div>
</body>

</html>
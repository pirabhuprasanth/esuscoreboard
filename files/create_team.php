<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Team</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        function updateMatches() {
    var tournamentId = document.getElementById("tournament_name").value;
    var matchesList = document.getElementById("matches_list");
    var debugOutput = document.getElementById("debug_output");
    matchesList.innerHTML = ""; // Clear the list before updating
    debugOutput.innerHTML = ""; // Clear debug output

    if (tournamentId) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_matches.php?tournament_id=" + encodeURIComponent(tournamentId), true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                debugOutput.innerHTML = xhr.responseText; // Display raw server response for debugging

                try {
                    var matches = JSON.parse(xhr.responseText);
                    if (matches && typeof matches === 'object') {
                        for (var matchId in matches) {
                            if (matches.hasOwnProperty(matchId)) {
                                var option = document.createElement("option");
                                option.value = matchId;
                                option.textContent = matches[matchId].name; // Display match name
                                matchesList.appendChild(option);
                            }
                        }
                    } else {
                        matchesList.innerHTML = "<option>No matches found for this tournament.</option>";
                    }
                } catch (e) {
                    matchesList.innerHTML = "<option>Error parsing server response.</option>";
                }
            } else {
                matchesList.innerHTML = "<option>Error fetching match data (status " + xhr.status + ").</option>";
            }
        };
        xhr.onerror = function () {
            matchesList.innerHTML = "<option>Error connecting to the server.</option>";
        };
        xhr.send();
    } else {
        matchesList.innerHTML = "<option>Select a tournament to view matches.</option>";
    }
}

    </script>
</head>
<body>
    <?php
    // Directory where tournament JSON files are stored
    $tournamentDir = 'tournaments/';
    $tournament_names = [];

    // Check if the directory exists and is readable
    if (is_dir($tournamentDir) && is_readable($tournamentDir)) {
        // Scan the directory for JSON files
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

        <div id="existing_teams">
            <h3>Existing Teams</h3>
            <ul id="teams_list">
                <li>Select a tournament to view existing teams.</li>
            </ul>
        </div>

        <label for="team_name">Team Name:</label>
        <input type="text" name="team_name" id="team_name" required>

        <label for="team_logo">Team Logo:</label>
        <input type="file" name="team_logo" id="team_logo" accept="image/*" required>

        <button type="submit">Create Team</button>
    </form>

</body>
</html>

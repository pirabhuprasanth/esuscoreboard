<?php
// Directory paths for storing tournament and match stats JSON files
$tournamentDir = 'tournaments/';
$matchStatsDir = 'match_stats/';

// Fetch tournaments
$tournamentFiles = glob($tournamentDir . '*.json');
$tournaments = [];

foreach ($tournamentFiles as $file) {
    $data = json_decode(file_get_contents($file), true);
    $tournaments[] = [
        'tournament_id' => $data['tournament_id'],
        'name' => $data['name']
    ];
}

// Fetch matches for a specific tournament (AJAX request)
if (isset($_GET['tournament_id']) && !isset($_POST['update'])) {
    $tournamentId = $_GET['tournament_id'];
    $tournamentFile = $tournamentDir . $tournamentId . '.json';

    if (file_exists($tournamentFile)) {
        $tournamentData = json_decode(file_get_contents($tournamentFile), true);
        $matches = [];

        foreach ($tournamentData as $key => $value) {
            if (strpos($key, 'mname_') === 0) {
                $matchId = str_replace('mname_', '', $key);
                $matches[] = [
                    'match_id' => $matchId,
                    'match_name' => $value
                ];
            }
        }

        // Return matches as HTML options for AJAX
        echo '<option value="">Select Match</option>';
        foreach ($matches as $match) {
            echo '<option value="' . $match['match_id'] . '">' . $match['match_name'] . '</option>';
        }
        exit;
    }
}

// Fetch teams for a specific match (AJAX request)
if (isset($_GET['match_id']) && isset($_GET['tournament_id']) && !isset($_POST['update'])) {
    $matchId = $_GET['match_id'];
    $tournamentId = $_GET['tournament_id'];
    $tournamentFile = $tournamentDir . $tournamentId . '.json';

    if (file_exists($tournamentFile)) {
        $tournamentData = json_decode(file_get_contents($tournamentFile), true);
        $teams = [
            'team1' => [
                'name' => $tournamentData[$matchId . '_team_name1'],
                'players' => [
                    [
                        'name' => $tournamentData[$matchId . '_team1_player_name_1'],
                        'image' => $tournamentData[$matchId . '_team1_player_image_1']
                    ],
                    [
                        'name' => $tournamentData[$matchId . '_team1_player_name_2'],
                        'image' => $tournamentData[$matchId . '_team1_player_image_2']
                    ]
                ]
            ],
            'team2' => [
                'name' => $tournamentData[$matchId . '_team_name2'],
                'players' => [
                    [
                        'name' => $tournamentData[$matchId . '_team2_player_name_1'],
                        'image' => $tournamentData[$matchId . '_team2_player_image_1']
                    ],
                    [
                        'name' => $tournamentData[$matchId . '_team2_player_name_2'],
                        'image' => $tournamentData[$matchId . '_team2_player_image_2']
                    ]
                ]
            ]
        ];

        // Return teams as HTML options for AJAX
        echo '<option value="">Select Team</option>';
        foreach ($teams as $key => $team) {
            echo '<option value="' . $team['name'] . '">' . $team['name'] . '</option>';
        }
        exit;
    }
}

// Save player statistics (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $matchId = $_POST['match_id'];
    $tournamentId = $_POST['tournament_id'];
    $teamName = $_POST['team_name'];
    $playerStats = $_POST['stats'];

    $statsFile = $matchStatsDir . $matchId . '.json';
    $statsData = file_exists($statsFile) ? json_decode(file_get_contents($statsFile), true) : [];

    // Update player stats in JSON
    foreach ($playerStats as $playerName => $stats) {
        foreach ($stats as $statType => $value) {
            $key = $teamName . '_' . $playerName . '_' . strtolower($statType);
            $statsData[$key] = $value;
        }
    }

    file_put_contents($statsFile, json_encode($statsData, JSON_PRETTY_PRINT));
    echo "Player statistics updated!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Stats</title>
    <script>
        function updateMatches() {
            var tournamentId = document.getElementById("tournament_id").value;
            if (tournamentId) {
                fetch("stats.php?tournament_id=" + tournamentId)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("match_id").innerHTML = data;
                        document.getElementById("teams").innerHTML = '';
                        document.getElementById("players").innerHTML = '';
                    });
            }
        }

        function updateTeams() {
            var matchId = document.getElementById("match_id").value;
            var tournamentId = document.getElementById("tournament_id").value;
            if (matchId && tournamentId) {
                fetch("stats.php?tournament_id=" + tournamentId + "&match_id=" + matchId)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("teams").innerHTML = data;
                        document.getElementById("players").innerHTML = '';
                    });
            }
        }

        function updatePlayers() {
            var teamName = document.getElementById("team_id").value;
            if (teamName) {
                fetch("stats.php?team_name=" + teamName)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("players").innerHTML = data;
                    });
            }
        }
    </script>
</head>
<body>
    <h2>Update Player Stats</h2>
    <form action="stats.php" method="POST">
        <input type="hidden" name="update" value="1">
        
        <!-- Select Tournament -->
        <label for="tournament_id">Select Tournament:</label>
        <select name="tournament_id" id="tournament_id" onchange="updateMatches()">
            <option value="">Select Tournament</option>
            <?php foreach ($tournaments as $tournament): ?>
                <option value="<?php echo $tournament['tournament_id']; ?>"><?php echo $tournament['name']; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Select Match -->
        <div id="matches">
            <label for="match_id">Select Match:</label>
            <select name="match_id" id="match_id" onchange="updateTeams()">
                <!-- Matches will be populated dynamically -->
            </select>
        </div>

        <!-- Select Team -->
        <div id="teams">
            <label for="team_id">Select Team:</label>
            <select name="team_name" id="team_id" onchange="updatePlayers()">
                <!-- Teams will be populated dynamically -->
            </select>
        </div>

        <!-- Player Stats Form -->
        <div id="players">
            <!-- Player stats form will appear here -->
        </div>

        <button type="submit">Save Stats</button>
    </form>
</body>
</html>

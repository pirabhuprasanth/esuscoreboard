<?php
$data_file = '../data.json';

// Check if the JSON file exists and read it
if (!file_exists($data_file)) {
    die("Data file does not exist.");
}

$data = json_decode(file_get_contents($data_file), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error reading team data.");
}

// Extract team names for the dropdown
$team_names = array_column($data['teams'], 'team_name');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Delete Player</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        function updatePlayers() {
            const teamName = document.getElementById('team_name').value;
            const playerContainer = document.getElementById('player_list');
            if (teamName) {
                fetch('get_players.php?team_name=' + encodeURIComponent(teamName))
                    .then(response => response.json())
                    .then(data => {
                        playerContainer.innerHTML = '';
                        data.players.forEach(player => {
                            playerContainer.innerHTML += `
                                <div>
                                    <p>${player.player_name}</p>
                                    <img src="${player.image}" alt="${player.player_name}" width="100">
                                    <form action="save_player.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="team_name" value="${teamName}">
                                        <input type="hidden" name="player_name_del" value="${player.player_name}">
                                        <button type="submit" name="action" value="delete">Delete Player</button>
                                    </form>
                                </div>
                            `;
                        });
                    });
            }
        }
    </script>
</head>
<body>
    <h1>Add/Delete Player</h1>
    <form action="save_player.php" method="POST" enctype="multipart/form-data">
        <label for="team_name">Select Team:</label>
        <select name="team_name" id="team_name" onchange="updatePlayers()">
            <option value="">Select a team</option>
            <?php foreach ($team_names as $team_name): ?>
                <option value="<?php echo htmlspecialchars($team_name); ?>"><?php echo htmlspecialchars($team_name); ?></option>
            <?php endforeach; ?>
        </select>

        <h2>Add Player</h2>
        <label for="player_name_add">Player Name:</label>
        <input type="text" name="player_name_add" id="player_name_add" required>

        <label for="player_image_add">Player Image:</label>
        <input type="file" name="player_image_add" id="player_image_add" required>

        <button type="submit" name="action" value="add">Add Player</button>
    </form>

    <h2>Existing Players</h2>
    <div id="player_list">
        <!-- Existing players will be populated here -->
    </div>
</body>
</html>

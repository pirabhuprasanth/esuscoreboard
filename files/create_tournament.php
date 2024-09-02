<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Tournament</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <form action="save_tournament.php" method="POST">
        <label for="name">Tournament Name:</label>
        <input type="text" name="name" id="name" required>
        
        <label for="game_title">Game Title:</label>
        <select name="game_title" id="game_title">
            <option value="BGMI">BGMI</option>
            <option value="Valorant">Valorant</option>
            <option value="CS:GO">CS:GO</option>
            <option value="Dota 2">Dota 2</option>
            <option value="League of Legends">League of Legends</option>
        </select>
        
        <button type="submit">Create Tournament</button>
    </form>

    <h2>Existing Tournaments</h2>
<ul>
    <?php
    // Directory where tournament JSON files are stored
    $tournamentDir = 'tournaments/';
    if (is_dir($tournamentDir)) {
        // Scan the directory for JSON files
        $tournamentFiles = glob($tournamentDir . '*.json');
        foreach ($tournamentFiles as $tournamentFile) {
            $tournamentData = json_decode(file_get_contents($tournamentFile), true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($tournamentData)) {
                echo '<li>';
                echo 'Tournament ID: ' . htmlspecialchars($tournamentData['tournament_id']) . '<br>';
                echo 'Name: ' . htmlspecialchars($tournamentData['name']) . '<br>';
                echo 'Game Title: ' . htmlspecialchars($tournamentData['game_title']) . '<br>';
                echo '<form action="delete_tournament.php" method="POST" style="display:inline;">';
                echo '<input type="hidden" name="tournament_id" value="' . htmlspecialchars($tournamentData['tournament_id']) . '">';
                echo '<button type="submit">Delete</button>';
                echo '</form>';
                echo '</li>';
            } else {
                // Error handling for JSON decode errors
                echo '<li>Error reading tournament file: ' . htmlspecialchars(basename($tournamentFile)) . '</li>';
            }
        }
    } else {
        echo '<p>The tournaments directory does not exist.</p>';
    }
    ?>
</ul>


</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Tournament</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/8d9491e66a.js" crossorigin="anonymous"></script>
</head>
<body class="tournaments background_content">
    <h1 class="title position_center">Tournaments</h1>
    <form action="save_tournament.php" class="position_center" method="POST">
        <div class="all glassmorphisum">
            <label for="name">Tournament Name:</label>
            <input type="text" name="name" id="name" required>
            
            <label for="game_title">Game Title:</label>
            <select name="game_title" id="game_title">
                <option value="BGMI">BGMI</option>
                <option value="The Finals">The Finals</option>
                <option value="Valorant">Valorant</option>
                <option value="CS:GO">CS:GO</option>
                <option value="Dota 2">Dota 2</option>
                <option value="League of Legends">League of Legends</option>
            </select>
            
            <input type="submit" value="Create Tournament">
        </div>
    </form>

    <h1 class="title position_center">Existing Tournaments</h1>
    <div id="ult " class="position_center">
        <div class="boxor  glassmorphisum custom-scrollbar">
            <div class="table position_center addition stay_up">
                <div>Tournament Id</div>
                <div>Name</div>
                <div>Game Title</div>
                <div>Action</div>
            </div>
            <?php
            // Directory where tournament JSON files are stored
            $tournamentDir = 'tournaments/';
            if (is_dir($tournamentDir)) {
                // Scan the directory for JSON files
                $tournamentFiles = glob($tournamentDir . '*.json');
                foreach ($tournamentFiles as $tournamentFile) {
                    $tournamentData = json_decode(file_get_contents($tournamentFile), true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($tournamentData)) {
                        echo '<div class="table position_center">';
                        echo '<div>' . htmlspecialchars($tournamentData['tournament_id']) . '</div>';
                        echo '<div>' . htmlspecialchars($tournamentData['name']) . '</div>';
                        echo '<div>' . htmlspecialchars($tournamentData['game_title']) . '</div>';
                        echo '<div><form action="delete_tournament.php" method="POST" style="display:inline;">';
                        echo '<input type="hidden" name="tournament_id" value="' . htmlspecialchars($tournamentData['tournament_id']) . '">';
                        echo '<button type="submit" class="valuebtn">Delete</button>';
                        echo '</form></div>';
                        echo '</div>';
                    } else {
                        // Error handling for JSON decode errors
                        echo '<li>Error reading tournament file: ' . htmlspecialchars(basename($tournamentFile)) . '</li>';
                    }
                }
            } else {
                echo '<p>The tournaments directory does not exist.</p>';
            }
            ?>
            </div>
           
        </div>
    </div>

    <div class="backtohome  position_center">
            <a href="\index.php" class="custom_button position_center">Back to Home</a>
    </div>
    <div class="backbutton">
        <a href="\index.php" class="position_center"><i class="fa-solid fa-circle-chevron-left"></i></a>  
    </div>

</body>
</html>

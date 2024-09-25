<?php
// Get data from the POST request
$tournamentId = $_POST['tournament_id'];  // Use tournament ID to find the correct file
$matchId = $_POST['match_id'];
$teamCount = $_POST['team_count'];

// Path to the JSON file named after the tournament ID
$tournamentFile = 'tournaments/' . $tournamentId . '.json';

// Check if the JSON file exists and is writable
if (file_exists($tournamentFile) && is_writable($tournamentFile)) {
    // Load the JSON data
    $data = json_decode(file_get_contents($tournamentFile), true);

    if ($data) {
        // Remove the team name and logo from the data
        $teamNameKey = $matchId . '_team_name' . $teamCount;
        $teamLogoKey = $matchId . '_team_logo' . $teamCount;
        unset($data[$teamNameKey]);
        unset($data[$teamLogoKey]);

        // Remove player data associated with the team
        $playerCount = 1;
        while (isset($data[$matchId . '_team' . $teamCount . '_player_name_' . $playerCount])) {
            unset($data[$matchId . '_team' . $teamCount . '_player_name_' . $playerCount]);
            unset($data[$matchId . '_team' . $teamCount . '_player_image_' . $playerCount]);
            $playerCount++;
        }

        // Write the updated data back to the JSON file
        if (file_put_contents($tournamentFile, json_encode($data, JSON_PRETTY_PRINT))) {
            echo 'Team deleted successfully';
        } else {
            echo 'Error writing updated data to the file';
        }
    } else {
        echo 'Error decoding JSON data';
    }
} else {
    echo 'Error: Tournament data not found or file is not writable';
}
?>

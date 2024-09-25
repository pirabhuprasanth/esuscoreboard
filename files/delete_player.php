<?php
// Get data from the POST request
$matchId = $_POST['match_id'];
$teamCount = $_POST['team_count'];
$playerCount = $_POST['player_count'];

// Path to the JSON file
$tournamentFile = 'tournaments/' . $matchId . '.json';

// Load the JSON data
if (file_exists($tournamentFile)) {
    $data = json_decode(file_get_contents($tournamentFile), true);

    // Remove the player data
    unset($data[$matchId . '_team' . $teamCount . '_player_name_' . $playerCount]);
    unset($data[$matchId . '_team' . $teamCount . '_player_image_' . $playerCount]);

    // Save the updated data
    file_put_contents($tournamentFile, json_encode($data));

    echo 'Player deleted successfully';
} else {
    echo 'Error: Tournament data not found';
}
?>

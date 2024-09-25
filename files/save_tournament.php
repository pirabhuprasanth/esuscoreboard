<?php
// Load existing data
$dataFile = '../data.json';
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : ['tournaments' => []];

// Generate a unique ID for the tournament
$tournamentId = rand(1000000000, 9999999999);

// Get the form data
$tournament = [
    'tid' => $tournamentId,
    'tname' => $_POST['name'],
    'tgame_title' => $_POST['game_title'],
];

// Save the tournament in the main data file
$data['data'][] = $tournament;
file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

// Create a new JSON file for the tournament
$tournamentData = [
    'tournament_id' => $tournamentId,
    'name' => $_POST['name'],
    'game_title' => $_POST['game_title'],
];

$tournamentFile = 'tournaments/' . $tournamentId . '.json';
file_put_contents($tournamentFile, json_encode($tournamentData, JSON_PRETTY_PRINT));

// Redirect back to the form page
header('Location: create_tournament.php');
exit;
?>

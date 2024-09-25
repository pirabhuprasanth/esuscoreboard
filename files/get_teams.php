<?php
// Assume we are fetching tournament_id from GET
$tournamentId = isset($_GET['tournament_id']) ? $_GET['tournament_id'] : '';

if ($tournamentId) {
    // Fetch the corresponding tournament JSON file
    $tournamentFile = 'tournaments/' . $tournamentId . '.json';

    if (file_exists($tournamentFile)) {
        // Read the file content
        $tournamentData = file_get_contents($tournamentFile);
        echo $tournamentData; // Send the data back as JSON to the client
    } else {
        echo json_encode(["error" => "Tournament file not found."]);
    }
} else {
    echo json_encode(["error" => "Tournament ID not provided."]);
}
?>

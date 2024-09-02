<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('../data.json'), true);

    $tournament_id = $_POST['tournament_id'];

    // Path to the directory where individual tournament JSON files are stored
    $tournamentDir = 'tournaments/';
    $tournamentFile = $tournamentDir . $tournament_id . '.json';

    // Remove the tournament entry from the main data.json file
    foreach ($data['data'] as $key => $tournament) {
        if ($tournament['tid'] == $tournament_id) {
            unset($data['data'][$key]);
            break;
        }
    }

    // Reindex the array after deletion
    $data['data'] = array_values($data['data']);

    // Save the updated data back to data.json
    file_put_contents('../data.json', json_encode($data, JSON_PRETTY_PRINT));

    // Delete the specific tournament JSON file
    if (file_exists($tournamentFile)) {
        unlink($tournamentFile);
    }

    // Redirect back to the create_tournament.php page
    header('Location: create_tournament.php');
    exit();
}
?>

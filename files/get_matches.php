<?php
// File: get_matches.php
$tournament_id = $_GET['tournament_id'];
$tournament_file_path = 'tournaments/' . $tournament_id . '.json'; // Adjust this based on your file storage

// Check if the file exists
if (!file_exists($tournament_file_path)) {
    echo json_encode([]);
    exit;
}

// Decode the JSON data
$tournament_data = json_decode(file_get_contents($tournament_file_path), true);

if ($tournament_data === null) {
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Extract matches from the JSON
$matches = [];
foreach ($tournament_data as $key => $value) {
    if (strpos($key, 'mname_') === 0) {
        $match_id = substr($key, 6); // Get the match ID
        $matches[$match_id] = ['name' => $value];
    }
}

// Output the matches array as JSON
echo json_encode($matches);
?>

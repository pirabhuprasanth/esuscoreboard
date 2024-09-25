<?php
// Define the path for saving uploaded files
$upload_dir = '../assets/';
$logo_path = $upload_dir . basename($_FILES['team_logo']['name']);
$logo_tmp = $_FILES['team_logo']['tmp_name'];

// Check if the upload directory exists and create it if not
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Move the uploaded team logo
if (!move_uploaded_file($logo_tmp, $logo_path)) {
    echo "<script>alert('Error uploading team logo.'); window.location.href = 'create_team.php';</script>";
    exit;
}

// Collect form inputs
$team_name = $_POST['team_name'];
$tournament_id = $_POST['tournament_id'];
$match_id = $_POST['match_id'];

// Set the tournament file path
$tournament_file_path = 'tournaments/' . $tournament_id . '.json';

// Check if the tournament file exists
if (!file_exists($tournament_file_path)) {
    echo "<script>alert('Tournament file does not exist.'); window.location.href = 'create_team.php';</script>";
    exit;
}

// Load the existing tournament data
$tournament_data = json_decode(file_get_contents($tournament_file_path), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "<script>alert('Error reading tournament data.'); window.location.href = 'create_team.php';</script>";
    exit;
}

// Determine the next available team number for the match
$team_number = 1;
while (isset($tournament_data[$match_id . '_team_name' . $team_number])) {
    $team_number++;
}

// Add match ID as a prefix to the new team name and logo
$tournament_data[$match_id . '_team_name' . $team_number] = $team_name;
$tournament_data[$match_id . '_team_logo' . $team_number] = $logo_path;

// Handle player names and images
if (!empty($_POST['player_names']) && is_array($_POST['player_names'])) {
    $player_count = 1;

    foreach ($_POST['player_names'] as $index => $player_name) {
        // Handle the player image upload
        $player_image_tmp = $_FILES['player_images']['tmp_name'][$index];
        $player_image_name = basename($_FILES['player_images']['name'][$index]);
        $player_image_path = $upload_dir . $player_image_name;

        if (!empty($player_image_tmp) && move_uploaded_file($player_image_tmp, $player_image_path)) {
            // Save the player name and image path with match ID as a prefix
            $tournament_data[$match_id . '_team' . $team_number . '_player_name_' . $player_count] = $player_name;
            $tournament_data[$match_id . '_team' . $team_number . '_player_image_' . $player_count] = $player_image_path;
        } else {
            // Save player name without image if image upload fails
            $tournament_data[$match_id . '_team' . $team_number . '_player_name_' . $player_count] = $player_name;
            $tournament_data[$match_id . '_team' . $team_number . '_player_image_' . $player_count] = '';
        }

        $player_count++;
    }
}

// Save the updated tournament data back to the file
if (file_put_contents($tournament_file_path, json_encode($tournament_data, JSON_PRETTY_PRINT))) {
    echo "<script>alert('Team and players created successfully!'); window.location.href = 'create_team.php';</script>";
} else {
    echo "<script>alert('Error saving team data.'); window.location.href = 'create_team.php';</script>";
}
?>

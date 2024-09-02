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

// Collect team name and tournament ID
$team_name = $_POST['team_name'];
$tournament_id = $_POST['tournament_id']; // This should be passed from the form

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

// Retrieve the current team count and team slots limit
$current_team_count = 0;
$team_count_limit = isset($tournament_data['team_count']) ? (int)$tournament_data['team_count'] : 0;

// Count existing teams
foreach ($tournament_data as $key => $value) {
    if (strpos($key, 'team_name') === 0) {
        $current_team_count++;
    }
}

// Check if the team limit has been reached
if ($current_team_count >= $team_count_limit) {
    echo "<script>alert('Team limit reached for this tournament.'); window.location.href = 'create_team.php';</script>";
    exit;
}

// Determine the next team number
$next_team_number = $current_team_count + 1;

// Update the tournament data with the new team
$tournament_data['team_name' . $next_team_number] = $team_name;
$tournament_data['team_logo' . $next_team_number] = $logo_path;

// Save the updated tournament data back to the file
if (file_put_contents($tournament_file_path, json_encode($tournament_data, JSON_PRETTY_PRINT))) {
    echo "<script>alert('Team created successfully!'); window.location.href = 'create_team.php';</script>";
} else {
    echo "<script>alert('Error saving team data.'); window.location.href = 'create_team.php';</script>";
}
?>

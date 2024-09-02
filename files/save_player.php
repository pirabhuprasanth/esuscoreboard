<?php
$action = $_POST['action'] ?? '';
$data_file = '../data.json';

if (!file_exists($data_file)) {
    die("Data file does not exist.");
}

$data = json_decode(file_get_contents($data_file), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error reading team data.");
}

if ($action === 'add') {
    $team_name = $_POST['team_name'];
    $player_name = $_POST['player_name_add'];
    $player_image = $_FILES['player_image_add']['name'];
    $player_tmp = $_FILES['player_image_add']['tmp_name'];

    $upload_dir = '../assets/';
    $player_image_path = $upload_dir . basename($player_image);

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!move_uploaded_file($player_tmp, $player_image_path)) {
        die("Error uploading player image.");
    }

    foreach ($data['teams'] as &$team) {
        if ($team['team_name'] === $team_name) {
            $team['players'][] = [
                'player_name' => $player_name,
                'image' => $player_image_path
            ];
            break;
        }
    }

    if (file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT))) {
        echo "Player added successfully!";
    } else {
        echo "Error saving player data.";
    }
} elseif ($action === 'delete') {
    $team_name = $_POST['team_name'];
    $player_name = $_POST['player_name_del'];

    foreach ($data['teams'] as &$team) {
        if ($team['team_name'] === $team_name) {
            foreach ($team['players'] as $key => $player) {
                if ($player['player_name'] === $player_name) {
                    if (file_exists($player['image'])) {
                        unlink($player['image']);
                    }
                    unset($team['players'][$key]);
                    break;
                }
            }
            break;
        }
    }

    foreach ($data['teams'] as &$team) {
        $team['players'] = array_values($team['players']);
    }

    if (file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT))) {
        echo "Player deleted successfully!";
    } else {
        echo "Error saving team data.";
    }
} else {
    echo "Invalid action.";
}
?>

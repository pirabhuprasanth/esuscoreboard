<?php
$team_name = $_GET['team_name'] ?? '';
$data_file = '../data.json';

if (empty($team_name) || !file_exists($data_file)) {
    echo json_encode(['players' => []]);
    exit;
}

$data = json_decode(file_get_contents($data_file), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['players' => []]);
    exit;
}

foreach ($data['teams'] as $team) {
    if ($team['team_name'] === $team_name) {
        $players = $team['players'] ?? [];
        echo json_encode(['players' => $players]);
        exit;
    }
}

echo json_encode(['players' => []]);

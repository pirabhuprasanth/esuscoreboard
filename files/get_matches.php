<?php
header('Content-Type: application/json');

// Check if the tournament ID parameter is provided
if (isset($_GET['tournament_id'])) {
    $tournamentId = basename($_GET['tournament_id']); // Sanitize the input
    $tournamentFile = 'tournaments/' . $tournamentId . '.json';

    // Check if the tournament file exists and is readable
    if (file_exists($tournamentFile) && is_readable($tournamentFile)) {
        $tournamentData = json_decode(file_get_contents($tournamentFile), true);

        if ($tournamentData) {
            $matches = [];

            // Loop through the keys to find match information based on the match prefix
            foreach ($tournamentData as $key => $value) {
                if (preg_match('/^(mid|mname|mdate|mtime|mlocation)_(\w+)$/', $key, $matchesArray)) {
                    $type = $matchesArray[1]; // mname, mdate, mtime, or mlocation
                    $matchId = $matchesArray[2]; // Unique match ID

                    if (!isset($matches[$matchId])) {
                        $matches[$matchId] = [
                            'mid' => '',
                            'name' => '',
                            'date' => '',
                            'time' => '',
                            'location' => ''
                        ];
                    }

                    switch ($type) {
                        case 'mid':
                            $matches[$matchId]['mid'] = $value;
                            break;
                        case 'mname':
                            $matches[$matchId]['name'] = $value;
                            break;
                        case 'mdate':
                            $matches[$matchId]['date'] = $value;
                            break;
                        case 'mtime':
                            $matches[$matchId]['time'] = $value;
                            break;
                        case 'mlocation':
                            $matches[$matchId]['location'] = $value;
                            break;
                    }
                }
            }

            // Check if any matches were found
            if (!empty($matches)) {
                echo json_encode($matches);
            } else {
                echo json_encode(["error" => "No matches found for this tournament"]);
            }
        } else {
            echo json_encode(["error" => "Invalid tournament data"]);
        }
    } else {
        echo json_encode(["error" => "Tournament file not found or not readable"]);
    }
} else {
    echo json_encode(["error" => "No tournament ID specified"]);
}
?>

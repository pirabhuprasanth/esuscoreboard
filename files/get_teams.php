<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Initialize the response array
$response = [];

// Check if the tournament file parameter is provided
if (isset($_GET['tournament_file'])) {
    $tournamentFile = 'tournaments/' . basename($_GET['tournament_file']) . '.json';

    // Check if the tournament file exists and is readable
    if (file_exists($tournamentFile) && is_readable($tournamentFile)) {
        $tournamentData = json_decode(file_get_contents($tournamentFile), true);

        if ($tournamentData && isset($tournamentData['team_count'])) {
            $teams = [];
            $teamCount = (int)$tournamentData['team_count'];

            // Loop through the keys to find team information based on team_count
            for ($i = 1; $i <= $teamCount; $i++) {
                $team_name_key = "team_name$i";
                $team_logo_key = "team_logo$i";

                if (isset($tournamentData[$team_name_key])) {
                    $team_data = [
                        'team_name' => $tournamentData[$team_name_key],
                        'team_logo' => isset($tournamentData[$team_logo_key]) ? $tournamentData[$team_logo_key] : ''
                    ];
                    $teams[] = $team_data;
                }
            }

            // Assign teams to the response
            $response['teams'] = $teams;
        } else {
            // 'team_count' is missing or no data found
            $response['error'] = "No team data found or 'team_count' is missing.";
        }
    } else {
        // File not found or not readable
        $response['error'] = "Tournament file not found or not readable.";
    }
} else {
    // No tournament file specified
    $response['error'] = "No tournament file specified.";
}

// Return the JSON-encoded response
echo json_encode($response);
?>

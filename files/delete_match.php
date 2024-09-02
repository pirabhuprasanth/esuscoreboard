<?php
if (isset($_POST['tournament_id']) && isset($_POST['match_id'])) {
    $tournamentId = basename($_POST['tournament_id']); // Sanitize input
    $matchId = basename($_POST['match_id']); // Sanitize input
    $tournamentFile = 'tournaments/' . $tournamentId . '.json';

    // Check if the tournament file exists and is readable
    if (file_exists($tournamentFile) && is_readable($tournamentFile)) {
        $tournamentData = json_decode(file_get_contents($tournamentFile), true);

        if ($tournamentData) {
            // Delete match data
            $keysToDelete = [
                "mname_$matchId",
                "mdate_$matchId",
                "mtime_$matchId",
                "mlocation_$matchId"
            ];

            foreach ($keysToDelete as $key) {
                unset($tournamentData[$key]);
            }

            // Save the updated data back to the tournament file
            file_put_contents($tournamentFile, json_encode($tournamentData, JSON_PRETTY_PRINT));

            echo "Match deleted successfully.";
        } else {
            echo "Error: Could not decode tournament data.";
        }
    } else {
        echo "Error: Tournament file not found or not readable.";
    }
} else {
    echo "Error: Missing tournament ID or match ID.";
}
?>

<?php
// Check if the necessary POST data is set
if (isset($_POST['tournament_id'], $_POST['match_name'], $_POST['match_date'], $_POST['match_time'])) {
    $tournamentId = $_POST['tournament_id'];
    $matchName = $_POST['match_name'];
    $matchDate = $_POST['match_date'];
    $matchTime = $_POST['match_time'];
    $matchLocation = isset($_POST['match_location']) ? $_POST['match_location'] : '';

    // Generate a random match ID using uniqid()
    $randomMatchId = uniqid();

    // Define the match data to be saved with the random match ID
    $matchData = [
        'mid' => $randomMatchId,
        'mname_' . $randomMatchId => $matchName,
        'mdate_' . $randomMatchId => $matchDate,
        'mtime_' . $randomMatchId => $matchTime,
        'mlocation_' . $randomMatchId => $matchLocation
    ];

    // Define the path to the specific tournament JSON file
    $tournamentFile = 'tournaments/' . $tournamentId . '.json';

    // Check if the tournament file exists
    if (file_exists($tournamentFile) && is_readable($tournamentFile)) {
        // Read the existing content of the tournament file
        $tournamentData = json_decode(file_get_contents($tournamentFile), true);

        if ($tournamentData) {
            // Save the tournament name
            $tournamentName = isset($tournamentData['name']) ? $tournamentData['name'] : 'Unknown Tournament';

            // Update the tournament data with the new match data
            foreach ($matchData as $key => $value) {
                $tournamentData[$key] = $value;
            }

            // Save the updated data back to the same tournament file
            file_put_contents($tournamentFile, json_encode($tournamentData, JSON_PRETTY_PRINT));

            // Display a success message with tournament name and redirect back to create_matches.php
            echo "<script>
                alert('Match created successfully for Tournament: $tournamentName');
                window.location.href = 'create_matches.php';
            </script>";
        } else {
            echo "<p>Error: Could not decode tournament data.</p>";
        }
    } else {
        echo "<p>Error: Tournament file not found or not readable.</p>";
    }
} else {
    echo "<p>Error: Missing match details.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Matches</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        // Function to fetch and display matches for the selected tournament
        function updateMatches() {
            var tournamentId = document.getElementById("tournament").value;
            var matchesList = document.getElementById("matches_list");
            var debugOutput = document.getElementById("debug_output");
            matchesList.innerHTML = ""; // Clear the list before updating
            debugOutput.innerHTML = ""; // Clear debug output

            if (tournamentId) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "get_matches.php?tournament_id=" + encodeURIComponent(tournamentId), true);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        // Display raw server response for debugging
                        debugOutput.innerHTML = xhr.responseText;

                        try {
                            var matches = JSON.parse(xhr.responseText);
                            if (matches && typeof matches === 'object') {
                                var matchCount = 0;
                                for (var mid in matches) {
                                    if (matches.hasOwnProperty(mid)) {
                                        var match = matches[mid];
                                        var listItem = document.createElement("li");
                                        listItem.textContent = `ID: ${mid}, Name: ${match.name}, Date: ${match.date}, Time: ${match.time}, Location: ${match.location}`;

                                        // Create Delete Button
                                        var deleteButton = document.createElement("button");
                                        deleteButton.textContent = "Delete";
                                        deleteButton.className = "btn btn-danger btn-sm ml-2";
                                        deleteButton.onclick = (function(mid) {
                                            return function() {
                                                deleteMatch(tournamentId, mid);
                                            };
                                        })(mid);

                                        listItem.appendChild(deleteButton);
                                        matchesList.appendChild(listItem);
                                        matchCount++;
                                    }
                                }

                                if (matchCount === 0) {
                                    matchesList.innerHTML = "<li>No matches found for this tournament.</li>";
                                }
                            } else {
                                matchesList.innerHTML = "<li>No matches found for this tournament.</li>";
                            }
                        } catch (e) {
                            matchesList.innerHTML = "<li>Error parsing server response.</li>";
                        }
                    } else {
                        matchesList.innerHTML = "<li>Error fetching match data (status " + xhr.status + ").</li>";
                    }
                };
                xhr.onerror = function () {
                    matchesList.innerHTML = "<li>Error connecting to the server.</li>";
                };
                xhr.send();
            } else {
                matchesList.innerHTML = "<li>Select a tournament to view matches.</li>";
            }
        }

        // Function to delete a match
        function deleteMatch(tournamentId, mid) {
            if (confirm("Are you sure you want to delete this match?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_match.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        alert(xhr.responseText);
                        updateMatches(); // Refresh the matches list
                    } else {
                        alert("Error deleting match (status " + xhr.status + ").");
                    }
                };
                xhr.onerror = function () {
                    alert("Error connecting to the server.");
                };
                xhr.send("tournament_id=" + encodeURIComponent(tournamentId) + "&mid=" + encodeURIComponent(mid));
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Create Matches</h2>
        <form action="save_matches.php" method="POST">
            <!-- Tournament Selection -->
            <div class="form-group">
                <label for="tournament">Select Tournament:</label>
                <select name="tournament_id" id="tournament" class="form-control" required onchange="updateMatches()">
                    <option value="">Select a tournament</option>
                    <?php
                    // Directory containing tournament files
                    $tournamentDir = 'tournaments/';
                    
                    // Check if the directory exists
                    if (is_dir($tournamentDir)) {
                        // Scan for JSON files in the directory
                        $files = scandir($tournamentDir);
                        
                        foreach ($files as $file) {
                            // Check if the file is a JSON file
                            if (pathinfo($file, PATHINFO_EXTENSION) == 'json') {
                                // Read the content of the JSON file
                                $filePath = $tournamentDir . $file;
                                $tournamentData = json_decode(file_get_contents($filePath), true);
                                
                                if ($tournamentData && isset($tournamentData['tournament_id'], $tournamentData['name'])) {
                                    // Get tournament ID and name
                                    $tournamentId = $tournamentData['tournament_id'];
                                    $tournamentName = $tournamentData['name'];
                                    
                                    // Populate the dropdown option
                                    echo "<option value=\"$tournamentId\">$tournamentName</option>";
                                }
                            }
                        }
                    } else {
                        echo "<option>No tournaments found</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Match Details -->
            <div class="form-group">
                <label for="match_name">Match Name:</label>
                <input type="text" name="match_name" id="match_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="match_date">Match Date:</label>
                <input type="date" name="match_date" id="match_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="match_time">Match Time:</label>
                <input type="time" name="match_time" id="match_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="match_location">Match Location:</label>
                <input type="text" name="match_location" id="match_location" class="form-control">
            </div>
            
            <!-- Existing Matches Display -->
            <h3>Existing Matches</h3>
            <ul id="matches_list">
                <li>Select a tournament to view existing matches.</li>
            </ul>
            <div id="debug_output" style="display: none;"></div>
            
            <button type="submit" class="btn btn-primary">Create Match</button>
        </form>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Match</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <form action="save_match.php" method="POST">
        <label for="match_id">Match ID:</label>
        <input type="number" name="match_id" id="match_id">
        
        <label for="scores">Scores (JSON format):</label>
        <textarea name="scores" id="scores" placeholder='{"team_a": {"kills": 50}, "team_b": {"kills": 45}}'></textarea>

        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="live">Live</option>
            <option value="completed">Completed</option>
        </select>
        
        <button type="submit">Update Match</button>
    </form>
</body>
</html>

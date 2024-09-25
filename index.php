<!DOCTYPE html>
<html>

<head>
    <title>SkyEsports Scoreboard</title>
    <link rel="stylesheet" type="text/css" href="{{ url_for('static', filename='css/styles.css') }}">
    <style>


@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

#logo {
    width: 20%;
}

footer {
    font-family: 'Poppins';
    display: flex;
    width: 100%;
    height: 15px;
    justify-content: space-between;
    background-color: black;
    position: fixed;
    bottom: 0px;
    left: -8px;
    padding: 8px 5px;
    z-index: 0;
    font-size: small;
    align-items: center;
    justify-content: center;
}

footer a {
    text-decoration: none;
}

footer p {
    position: relative;
    color: rgb(13, 255, 0);
    margin: 0;
    bottom: 15px;
}

#mfooter p {
    position: relative;
    color: rgb(13, 255, 0);
    margin: 0;
    bottom: 0px;
}

/* HOME PAGE START*/
#home {
    font-family: RES_FONT;
    font-size: 25px;
    text-align: center;
    background-color: rgb(1, 1, 36);
    color: white;
}

#home h1 {
    font-size: 30px;
}

#acon {
    border-style: solid;
    border-radius: 5px;
    border-width: 5px;
    width: 30%;
    position: relative;
    left: 35%;
    top: -20px;
}

#home a {
    display: flex;
    text-decoration: none;
    color: antiquewhite;
    margin-top: 10px;
    justify-content: center;
    padding-top: 20px;
}


.hr-theme-slash-2 {
    display: flex;
    font-family: 'Courier New', Courier, monospace;
    justify-content: center;

    .hr-line {
        width: 100%;
        position: relative;
        margin: 15px;
        border-bottom: 1px solid #ffffff;
    }

    .hr-icon {
        position: relative;
        top: 3px;
        color: #ff0000;
    }
}


/* HOME PAGE END*/

    </style>
    <script src="https://kit.fontawesome.com/d7bdf509b5.js" crossorigin="anonymous"></script>
</head>

<body id="home">
    <img src="/assets/img/download.png" alt="" id="logo">
    <h1>Scoreboard</h1><br>
    <div id="acon">
        <a href="/files/create_tournament.php">Tournaments</a><br>
        <a href="/files/create_matches.php">Matches</a><br>
        <a href="/files/create_team.php">Teams</a><br><br>
        <div class="hr-theme-slash-2">
            <div class="hr-line"></div>
            <div class="hr-icon"><i class="fa-solid fa-gamepad" style="color: #ffffff;"></i></div>
            <div class="hr-line"></div>
        </div><br>
    </div>
</body><br>

<footer><a href="https://ps8network.com/">
        <p>Developed by PS8 Network</p>
    </a></footer>

</html>
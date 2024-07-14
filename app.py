import json
from flask import Flask, render_template, request, redirect, url_for

app = Flask(__name__)

def load_data():
    with open('data/data.json', 'r') as file:
        return json.load(file)

def save_data(data):
    with open('data/data.json', 'w') as file:
        json.dump(data, file, indent=4)

@app.route('/')
def index():
    return render_template('index.html')

# Tournaments
@app.route('/tournaments')
def tournaments():
    data = load_data()
    return render_template('tournaments.html', tournaments=data['tournaments'])

@app.route('/add_tournament', methods=['POST'])
def add_tournament():
    data = load_data()
    new_tournament = {
        "id": len(data['tournaments']) + 1,
        "name": request.form['name'],
        "date": request.form['date'],
        "description": request.form['description']
    }
    data['tournaments'].append(new_tournament)
    save_data(data)
    return redirect(url_for('tournaments'))


@app.route('/delete_tournament/<int:tournament_id>', methods=['POST'])
def delete_tournament(tournament_id):
    data = load_data()
    data['tournaments'] = [tournament for tournament in data['tournaments'] if tournament['id'] != tournament_id]

    # Remove all matches associated with the tournament
    match_ids = [match['id'] for match in data['matches'] if match['tournament_id'] == tournament_id]
    data['matches'] = [match for match in data['matches'] if match['tournament_id'] != tournament_id]

    # Remove all teams associated with the matches
    team_ids = [team['id'] for team in data['teams'] if team['match_id'] in match_ids]
    data['teams'] = [team for team in data['teams'] if team['id'] not in team_ids]

    # Remove all players associated with the teams
    data['players'] = [player for player in data['players'] if player['id'] not in team_ids]

    save_data(data)
    return redirect(url_for('tournaments'))


# Matches
@app.route('/matches')
def matches():
    data = load_data()
    return render_template('matches.html', matches=data['matches'], tournaments=data['tournaments'])


@app.route('/add_match', methods=['POST'])
def add_match():
    data = load_data()
    new_match = {
        "id": len(data['matches']) + 1,
        "tournament_id": int(request.form['tournament_id']),
        "name": request.form['name'],
        "date": request.form['date']
    }
    data['matches'].append(new_match)
    save_data(data)
    return redirect(url_for('matches'))

@app.route('/edit_match/<int:id>', methods=['GET', 'POST'])
def edit_match(id):
    data = load_data()
    match = next(match for match in data['matches'] if match['id'] == id)
    if request.method == 'POST':
        match['name'] = request.form['name']
        match['date'] = request.form['date']
        save_data(data)
        return redirect(url_for('matches'))
    return render_template('edit_match.html', match=match)


#Teams
@app.route('/teams')
def teams():
    data = load_data()
    return render_template('teams.html', teams=data['teams'], matches=data['matches'])


@app.route('/add_team', methods=['POST'])
def add_team():
    data = load_data()
    new_team = {
        "id": len(data['teams']) + 1,
        "name": request.form['name'],
        "match_id": int(request.form['match_id']),
        "logo": request.form['logo'],
        "score": 0,
        "kills": 0
    }
    data['teams'].append(new_team)
    save_data(data)
    return redirect(url_for('teams'))



@app.route('/edit_team/<int:id>', methods=['GET', 'POST'])
def edit_team(id):
    data = load_data()
    team = next(team for team in data['teams'] if team['id'] == id)
    if request.method == 'POST':
        team['name'] = request.form['name']
        team['score'] = int(request.form['score'])
        team['kills'] = int(request.form['kills'])
        save_data(data)
        return redirect(url_for('teams'))
    matches = data['matches']
    return render_template('edit_team.html', team=team, matches=matches)

# Players
@app.route('/players', methods=['GET', 'POST'])
def players():
    data = load_data()
    
    if request.method == 'POST':
        tournament_id = int(request.form.get('tournament_id', 0))
        match_id = int(request.form.get('match_id', 0))
        team_id = int(request.form.get('team_id', 0))
        
        if not tournament_id or not match_id or not team_id:
            flash('Please select a valid tournament, match, and team.')
            return redirect(url_for('players'))
        
        player_id = max([player['id'] for player in data['players']], default=0) + 1
        player = {
            'id': player_id,
            'name': request.form['name'],
            'image': request.form['image'],
            'team_id': team_id,
            'stats': {
                'kills': 0,
                'assists': 0,
                'deaths': 0,
                'alive': False
            }
        }
        data['players'].append(player)
        save_data(data)
        return redirect(url_for('players'))

    tournaments = data['tournaments']
    matches = data['matches']
    teams = data['teams']
    players = data['players']
    return render_template('players.html', tournaments=tournaments, matches=matches, teams=teams, players=players)


@app.route('/add_player', methods=['POST'])
def add_player():
    data = load_data()
    new_player = {
        "id": len(data['players']) + 1,
        "name": request.form['name'],
        "image": request.form['image'],
        "kill": int(request.form['kill']),
        "assist": int(request.form['assist']),
        "death": int(request.form['death']),
        "alive_status": request.form['alive_status'],
        "team_id": int(request.form['team_id']),
        
    }
    data['players'].append(new_player)
    save_data(data)
    return redirect(url_for('players'))

@app.route('/manage_player/<int:player_id>')
def manage_player(player_id):
    data = load_data()
    player = next((p for p in data['players'] if p['id'] == player_id), None)
    return render_template('manage_player.html', player=player)

@app.route('/delete_player/<int:player_id>', methods=['POST'])
def delete_player(player_id):
    data = load_data()
    data['players'] = [player for player in data['players'] if player['id'] != player_id]
    save_data(data)
    return redirect(url_for('players'))



#Stats
@app.route('/overall_stats')
def overall_stats():
    data = load_data()
    return render_template('overall_stats.html', teams=data['teams'])

@app.route('/update_score', methods=['POST'])
def update_score():
    team_id = int(request.form['team_id'])
    score_change = int(request.form['score_change'])
    
    data = load_data()
    team = next((team for team in data['teams'] if team['id'] == team_id), None)
    
    if team:
        team['score'] += score_change
        save_data(data)
    
    return redirect(url_for('match_stats', match_id=team['match_id']))

@app.route('/delete_match/<int:match_id>', methods=['POST'])
def delete_match(match_id):
    data = load_data()
    data['matches'] = [match for match in data['matches'] if match['id'] != match_id]
    data['teams'] = [team for team in data['teams'] if team['match_id'] != match_id]
    save_data(data)
    return redirect(url_for('matches'))

@app.route('/delete_team/<int:team_id>', methods=['POST'])
def delete_team(team_id):
    data = load_data()
    team = next((team for team in data['teams'] if team['id'] == team_id), None)
    if team:
        data['teams'].remove(team)
        save_data(data)
    return redirect(url_for('teams'))

@app.route('/overall_match_stats/<int:match_id>')
def overall_match_stats(match_id):
    data = load_data()
    match = next((match for match in data['matches'] if match['id'] == match_id), None)
    teams = [team for team in data['teams'] if team['match_id'] == match_id]
    
    total_score = sum(team['score'] for team in teams)
    total_kills = sum(team['kills'] for team in teams)
    
    return render_template('match_stats.html', match=match, teams=teams, total_score=total_score, total_kills=total_kills)


@app.route('/match_stats', methods=['GET', 'POST'])
def match_stats():
    data = load_data()
    
    if request.method == 'POST':
        match_id = int(request.form.get('match_id'))
        return redirect(url_for('edit_player_stats', match_id=match_id))

    tournaments = data['tournaments']
    matches = data['matches']
    return render_template('match_stats.html', tournaments=tournaments, matches=matches)

@app.route('/edit_player_stats/<int:match_id>', methods=['GET', 'POST'])
def edit_player_stats(match_id):
    data = load_data()
    match = next((m for m in data['matches'] if m['id'] == match_id), None)
    teams = [team for team in data['teams'] if team['match_id'] == match_id]
    players = [player for player in data['players'] if player['team_id'] in [team['id'] for team in teams]]

    if request.method == 'POST':
        for player in players:
            player_id = player['id']
            player['stats']['kills'] = int(request.form.get(f'kills_{player_id}', player['stats']['kills']))
            player['stats']['assists'] = int(request.form.get(f'assists_{player_id}', player['stats']['assists']))
            player['stats']['deaths'] = int(request.form.get(f'deaths_{player_id}', player['stats']['deaths']))
            player['stats']['alive'] = bool(request.form.get(f'alive_{player_id}', player['stats']['alive']))
        
        save_data(data)
        return redirect(url_for('edit_player_stats', match_id=match_id))

    return render_template('edit_player_stats.html', match=match, teams=teams, players=players)



if __name__ == '__main__':
    app.run(debug=True)

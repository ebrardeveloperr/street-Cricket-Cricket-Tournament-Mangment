<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pro Cricket Umpire Scoring</title>
    <!-- Utilizing Tailwind CSS for professional, rapid UI styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom Keypad & UI Styling */
        body {
            -webkit-tap-highlight-color: transparent;
        }
        
        .keypad-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            padding: 10px;
            background: #1f2937;
            border-radius: 12px;
        }

        .key-btn {
            background: #374151;
            color: #f3f4f6;
            border: none;
            padding: 15px 10px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.1s ease;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .key-btn:active {
            transform: scale(0.95);
            background: #4b5563;
        }

        .key-btn.primary { background: #2563eb; color: white; }
        .key-btn.danger { background: #dc2626; color: white; }
        .key-btn.warning { background: #d97706; color: white; }
        
        .player-select {
            width: 100%;
            padding: 10px;
            background: #374151;
            border: 1px solid #4b5563;
            border-radius: 6px;
            color: white;
            outline: none;
        }

        .player-select:focus {
            border-color: #3b82f6;
        }

        .player-select:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Current Over Balls */
        .ball-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: bold;
            background: #374151;
            color: white;
        }
        .ball-circle.wicket { background: #dc2626; }
        .ball-circle.boundary { background: #2563eb; }
        .ball-circle.extra { background: #d97706; }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 font-sans h-screen flex flex-col overflow-hidden">

    <!-- ========================================== -->
    <!-- SETUP SCREEN -->
    <!-- ========================================== -->
    <div id="setup-screen" class="flex-1 overflow-y-auto p-4 max-w-md mx-auto w-full">
        <div class="flex justify-center gap-4 text-xs mb-3">
            <a href="/teams" class="text-blue-400 underline">Manage Teams</a>
            <a href="/players" class="text-blue-400 underline">Manage Players</a>
            <a href="/tournaments" class="text-blue-400 underline">Manage Tournaments</a>
        </div>
        <div class="text-center mb-6">
            <h1 id="setup-title" class="text-2xl font-bold text-blue-500"><i class="fas fa-trophy mr-2"></i>New Match Setup</h1>
            <p id="setup-subtitle" class="text-sm text-gray-400">Configure tournament and teams</p>
            <div id="second-innings-banner" class="hidden mt-3 rounded-lg border border-yellow-500/40 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-300"></div>
        </div>

        <div class="space-y-5 bg-gray-800 p-5 rounded-xl shadow-lg border border-gray-700">
            
            <!-- Tournament Selection -->
            <div class="form-group">
                <label class="block text-sm font-medium mb-1" for="tournament-select">Tournament</label>
                <select id="tournament-select" class="player-select">
                    <option value="">Select tournament (optional)</option>
                </select>
            </div>

            <!-- Team 1 Selection -->
            <div class="form-group">
                <label class="block text-sm font-medium mb-1" for="team1-select">Batting Team (Team 1)</label>
                <select id="team1-select" class="player-select" required>
                    <option value="">Select batting team</option>
                </select>
            </div>

            <!-- Team 2 Selection -->
            <div class="form-group">
                <label class="block text-sm font-medium mb-1" for="team2-select">Bowling Team (Team 2)</label>
                <select id="team2-select" class="player-select" required>
                    <option value="">Select bowling team</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Overs -->
                <div class="form-group">
                    <label class="block text-sm font-medium mb-1">Total Overs</label>
                    <input type="number" id="total-overs" class="w-full p-2 bg-gray-700 border border-gray-600 rounded text-white" value="20">
                </div>
            </div>

            <hr class="border-gray-600">

            <!-- Initial Players -->
            <div class="form-group">
                <label class="block text-sm font-medium mb-1" for="striker-select">Striker</label>
                <select id="striker-select" class="player-select" disabled required>
                    <option value="">Select batting team first</option>
                </select>
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium mb-1" for="nonstriker-select">Non-Striker</label>
                <select id="nonstriker-select" class="player-select" disabled required>
                    <option value="">Select batting team first</option>
                </select>
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium mb-1" for="opening-bowler-select">Opening Bowler</label>
                <select id="opening-bowler-select" class="player-select" disabled required>
                    <option value="">Select bowling team first</option>
                </select>
            </div>

            <button id="start-match-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition-colors">
                Start Match <i class="fas fa-play ml-2"></i>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- DASHBOARD SCREEN -->
    <!-- ========================================== -->
    <div id="dashboard-screen" class="hidden flex-col h-full max-w-md mx-auto w-full">
        
        <!-- Header / Main Score -->
        <div class="bg-gray-800 p-4 border-b border-gray-700 shadow-md">
            <div class="flex justify-between items-center mb-2">
                <h2 id="batting-team-name" class="text-xl font-bold text-blue-400">Team Name</h2>
                <div id="innings-label" class="text-sm bg-gray-700 px-2 py-1 rounded">1st Innings</div>
            </div>
            
            <div class="flex justify-between items-end">
                <div>
                    <span id="main-score" class="text-4xl font-extrabold">0/0</span>
                    <span id="main-overs" class="text-lg text-gray-400 ml-2">(0.0)</span>
                </div>
                <div class="text-right text-sm text-gray-400">
                    <div id="crr-display">CRR: 0.00</div>
                    <div id="target-display" class="hidden text-yellow-400">Target: -- | RRR: --</div>
                </div>
            </div>
        </div>

        <!-- Scrollable Middle Section -->
        <div class="flex-1 overflow-y-auto bg-gray-900">
            
            <!-- Batters -->
            <div class="p-4 border-b border-gray-800">
                <div class="flex justify-between items-center py-2">
                    <div class="flex-1">
                        <span id="striker-name" class="font-bold text-lg">Player 1 *</span>
                    </div>
                    <div class="flex-1 text-center font-bold text-lg">
                        <span id="striker-runs">0</span><span id="striker-balls" class="text-sm text-gray-400 font-normal ml-1">(0)</span>
                    </div>
                    <div class="flex-1 text-right text-xs text-gray-400">
                        4s: <span id="striker-4s" class="text-white">0</span> | 6s: <span id="striker-6s" class="text-white">0</span><br>
                        SR: <span id="striker-sr" class="text-white">0.0</span>
                    </div>
                </div>
                <hr class="border-gray-800">
                <div class="flex justify-between items-center py-2 text-gray-400">
                    <div class="flex-1">
                        <span id="nonstriker-name" class="font-semibold">Player 2</span>
                    </div>
                    <div class="flex-1 text-center font-semibold">
                        <span id="nonstriker-runs">0</span><span id="nonstriker-balls" class="text-sm ml-1">(0)</span>
                    </div>
                    <div class="flex-1 text-right text-xs">
                        4s: <span id="nonstriker-4s">0</span> | 6s: <span id="nonstriker-6s">0</span><br>
                        SR: <span id="nonstriker-sr">0.0</span>
                    </div>
                </div>
            </div>

            <!-- Bowler -->
            <div class="p-4 border-b border-gray-800 bg-gray-800/50">
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Current Bowler</div>
                <div class="flex justify-between items-center">
                    <div class="flex-1 font-bold">
                        <span id="bowler-name">Bowler Name</span>
                    </div>
                    <div class="flex-1 text-center font-bold">
                        <span id="bowler-overs">0.0</span>
                    </div>
                    <div class="flex-1 text-right text-sm">
                        <span id="bowler-runs">0</span>-<span id="bowler-wickets">0</span>
                        <div class="text-xs text-gray-400">Eco: <span id="bowler-eco" class="text-white">0.0</span></div>
                    </div>
                </div>
            </div>

            <!-- Current Over Timeline -->
            <div class="p-4">
                <div class="text-xs text-gray-500 uppercase font-bold mb-2">This Over</div>
                <div id="current-over-balls" class="flex flex-wrap gap-2 overflow-x-auto pb-2 min-h-[35px] items-center">
                </div>
            </div>
        </div>

        <!-- Fixed Keypad -->
        <div class="bg-gray-800 border-t border-gray-700 pb-safe">
            <div class="keypad-grid">
                <!-- Row 1 -->
                <button class="key-btn" data-val="0">0</button>
                <button class="key-btn" data-val="1">1</button>
                <button class="key-btn" data-val="2">2</button>
                <button class="key-btn" data-val="3">3</button>
                <!-- Row 2 -->
                <button class="key-btn primary" data-val="4">4</button>
                <button class="key-btn primary" data-val="6">6</button>
                <button class="key-btn warning" data-type="extra" data-val="WD">WD</button>
                <button class="key-btn warning" data-type="extra" data-val="NB">NB</button>
                <!-- Row 3 -->
                <button class="key-btn warning" data-type="extra" data-val="B">B</button>
                <button class="key-btn warning" data-type="extra" data-val="LB">LB</button>
                <button class="key-btn danger" data-type="wicket" data-val="W">W</button>
                <button class="key-btn danger" data-type="wicket" data-val="RW">RW</button>
                <!-- Row 4 -->
                <button class="key-btn danger" data-type="wicket" data-val="CaW">CaW</button>
                <button class="key-btn bg-gray-600" id="btn-undo"><i class="fas fa-undo"></i> Undo</button>
                <button class="key-btn bg-gray-600" id="btn-end-innings">End</button>
                <button class="key-btn bg-gray-600" id="btn-more"><i class="fas fa-ellipsis-h"></i></button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODALS -->
    <!-- ========================================== -->

    <!-- New Batter Modal -->
    <div id="modal-new-batter" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-xl p-5 w-full max-w-sm">
            <h3 class="text-xl font-bold mb-4 text-white">Select New Batter</h3>
            <select id="new-batter-select" class="player-select mb-4" required>
                <option value="">Select batter</option>
            </select>
            <button id="confirm-new-batter" class="w-full bg-blue-600 p-3 rounded text-white font-bold">Confirm</button>
        </div>
    </div>

    <!-- Next Bowler Modal -->
    <div id="modal-next-bowler" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-xl p-5 w-full max-w-sm">
            <h3 class="text-xl font-bold mb-4 text-white">Select Next Bowler</h3>
            <select id="next-bowler-select" class="player-select mb-4" required>
                <option value="">Select bowler</option>
            </select>
            <button id="confirm-next-bowler" class="w-full bg-blue-600 p-3 rounded text-white font-bold">Confirm</button>
        </div>
    </div>

    <!-- Innings Target Modal -->
    <div id="modal-innings-target" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-xl p-5 w-full max-w-sm border border-yellow-500/30">
            <h3 class="text-xl font-bold mb-2 text-yellow-400"><i class="fas fa-flag-checkered mr-2"></i>Innings Over</h3>
            <p id="innings-target-message" class="text-gray-300 mb-4 text-sm"></p>
            <div class="rounded-lg bg-gray-900 p-4 mb-4 text-center border border-yellow-500/20">
                <div class="text-sm text-gray-400 uppercase tracking-wide">Target</div>
                <div id="innings-target-score" class="text-4xl font-extrabold text-yellow-400 my-1">0</div>
                <div id="innings-target-team" class="text-sm text-gray-300"></div>
            </div>
            <button id="confirm-innings-target" class="w-full bg-blue-600 hover:bg-blue-700 p-3 rounded text-white font-bold transition-colors">
                Select 2nd Innings Openers <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </div>

    <!-- Match Result Modal -->
    <div id="modal-match-result" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-xl p-5 w-full max-w-sm border border-green-500/30">
            <h3 class="text-xl font-bold mb-2 text-green-400"><i class="fas fa-trophy mr-2"></i>Match Finished</h3>
            <p id="match-result-text" class="text-gray-200 mb-4 text-center text-lg font-semibold"></p>
            <button id="confirm-match-result" class="w-full bg-blue-600 hover:bg-blue-700 p-3 rounded text-white font-bold transition-colors">
                New Match <i class="fas fa-plus ml-2"></i>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- JAVASCRIPT ARCHITECTURE -->
    <!-- ========================================== -->
    <!-- ========================================== -->
    <!-- JAVASCRIPT ARCHITECTURE -->
    <!-- ========================================== -->
    <script>
        /**
         * MODULE: Database
         * Loaded from the Laravel backend on startup (see api() calls below).
         * Teams/players/tournaments are managed at /teams, /players, /tournaments.
         */
        const Database = {
            tournaments: [],
            teams: [],
            players: []
        };

        async function api(path, options = {}) {
            const res = await fetch(`/api${path}`, {
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                ...options
            });
            if (!res.ok) {
                console.error('API error', path, await res.text());
                throw new Error(`API request failed: ${path}`);
            }
            return res.status === 204 ? null : res.json();
        }

        async function loadDatabase() {
            const [tournaments, teams, players] = await Promise.all([
                api('/tournaments'),
                api('/teams'),
                api('/players')
            ]);
            Database.tournaments = tournaments;
            Database.teams = teams;
            Database.players = players;
        }

        /**
         * MODULE: Utility Functions
         */
        const Utils = {
            deepCopy: (obj) => JSON.parse(JSON.stringify(obj)),
            calculateEco: (runs, balls) => balls === 0 ? "0.00" : ((runs / balls) * 6).toFixed(2),
            calculateSR: (runs, balls) => balls === 0 ? "0.00" : ((runs / balls) * 100).toFixed(1),
            getOversString: (totalBalls) => `${Math.floor(totalBalls / 6)}.${totalBalls % 6}`
        };

        /**
         * MODULE: Match Controller & State
         */
        const MatchController = {
            state: null,
            history: [],

            getInitialState: function() {
                return {
                    matchId: null,
                    inningId: null,
                    tournamentId: null,
                    innings: 1,
                    totalOvers: 20,
                    target: null,
                    battingTeam: null,
                    bowlingTeam: null,
                    score: { runs: 0, wickets: 0, balls: 0, extras: 0 },
                    currentOver: [], // stores actions for the current over
                    striker: null,
                    nonStriker: null,
                    bowler: null,
                    battingStats: {}, // map of player_id -> stats
                    bowlingStats: {}, // map of player_id -> stats
                    deliveryIds: [] // ids of saved deliveries, parallel to actions taken (for undo)
                };
            },

            saveHistory: function() {
                this.history.push(Utils.deepCopy(this.state));
            },

            undo: function() {
                if (this.history.length > 0) {
                    const currentInningId = this.state.inningId;
                    const hadDelivery = this.state.deliveryIds.length > this.history[this.history.length - 1].deliveryIds.length;

                    this.state = this.history.pop();
                    UIController.updateDashboard();

                    if (hadDelivery && currentInningId) {
                        api(`/innings/${currentInningId}/deliveries/latest`, { method: 'DELETE' }).catch(() => {});
                    }
                } else {
                    alert("No more actions to undo.");
                }
            },

            initBattingStats: function(playerId, name) {
                if (!this.state.battingStats[playerId]) {
                    this.state.battingStats[playerId] = { id: playerId, name: name, runs: 0, balls: 0, fours: 0, sixes: 0 };
                }
                return this.state.battingStats[playerId];
            },

            initBowlingStats: function(playerId, name) {
                if (!this.state.bowlingStats[playerId]) {
                    this.state.bowlingStats[playerId] = { id: playerId, name: name, runs: 0, balls: 0, wickets: 0, maidens: 0 };
                }
                return this.state.bowlingStats[playerId];
            },

            startMatch: async function() {
                const totalOvers = parseInt(document.getElementById('total-overs').value, 10);
                const tournamentId = document.getElementById('tournament-select').value || null;
                const team1Id = document.getElementById('team1-select').value;
                const team2Id = document.getElementById('team2-select').value;
                const strikerId = document.getElementById('striker-select').value;
                const nonStrikerId = document.getElementById('nonstriker-select').value;
                const bowlerId = document.getElementById('opening-bowler-select').value;

                if (!team1Id || !team2Id || !strikerId || !nonStrikerId || !bowlerId) {
                    alert("Please select all required fields to start the match.");
                    return;
                }

                if (team1Id === team2Id) {
                    alert("Batting and bowling teams must be different.");
                    return;
                }

                if (strikerId === nonStrikerId) {
                    alert("Striker and non-striker must be different players.");
                    return;
                }

                const strikerData = Database.players.find(p => String(p.id) === String(strikerId));
                const nonStrikerData = Database.players.find(p => String(p.id) === String(nonStrikerId));
                const bowlerData = Database.players.find(p => String(p.id) === String(bowlerId));

                if (!strikerData || !nonStrikerData || !bowlerData) {
                    alert("Could not find the selected players. Please choose from the dropdown lists.");
                    return;
                }

                // Continue an existing match into the 2nd innings (innings record already created).
                if (MatchController.state?.innings === 2 && MatchController.state?.matchId) {
                    MatchController.state.striker = MatchController.initBattingStats(strikerData.id, strikerData.name);
                    MatchController.state.nonStriker = MatchController.initBattingStats(nonStrikerData.id, nonStrikerData.name);
                    MatchController.state.bowler = MatchController.initBowlingStats(bowlerData.id, bowlerData.name);

                    UIController.isSecondInningsSetup = false;
                    UIController.showScreen('dashboard-screen');
                    UIController.updateDashboard();
                    return;
                }

                let apiResult;
                try {
                    apiResult = await api('/matches', {
                        method: 'POST',
                        body: JSON.stringify({
                            tournament_id: tournamentId,
                            team1_id: team1Id,
                            team2_id: team2Id,
                            total_overs: totalOvers
                        })
                    });
                } catch (e) {
                    alert("Could not save match to the database. Check the server is running.");
                    return;
                }

                MatchController.state = MatchController.getInitialState();
                MatchController.state.matchId = apiResult.match_id;
                MatchController.state.inningId = apiResult.inning_id;
                MatchController.state.tournamentId = tournamentId;
                MatchController.state.totalOvers = totalOvers;
                MatchController.state.battingTeam = Database.teams.find(t => String(t.id) === String(team1Id));
                MatchController.state.bowlingTeam = Database.teams.find(t => String(t.id) === String(team2Id));

                MatchController.state.striker = MatchController.initBattingStats(strikerData.id, strikerData.name);
                MatchController.state.nonStriker = MatchController.initBattingStats(nonStrikerData.id, nonStrikerData.name);
                MatchController.state.bowler = MatchController.initBowlingStats(bowlerData.id, bowlerData.name);

                UIController.showScreen('dashboard-screen');
                UIController.updateDashboard();
            }
        };

        /**
         * MODULE: Scoring Engine
         */
        const ScoringEngine = {
            // Works out (over_number, ball_number) for the ball about to be bowled,
            // based on legal balls faced so far this innings.
            currentBallPosition: function(s) {
                return {
                    over_number: Math.floor(s.score.balls / 6) + 1,
                    ball_number: (s.score.balls % 6) + 1
                };
            },

            saveDelivery: function(payload) {
                const s = MatchController.state;
                if (!s.inningId) return;
                api('/deliveries', {
                    method: 'POST',
                    body: JSON.stringify({ inning_id: s.inningId, ...payload })
                }).then(res => {
                    if (res && res.delivery_id) s.deliveryIds.push(res.delivery_id);
                }).catch(() => console.error('Failed to save delivery'));
            },

            processRun: function(runs) {
                MatchController.saveHistory();

                const s = MatchController.state;
                const pos = this.currentBallPosition(s);

                s.score.runs += runs;
                s.score.balls += 1;

                s.striker.runs += runs;
                s.striker.balls += 1;
                if (runs === 4) s.striker.fours += 1;
                if (runs === 6) s.striker.sixes += 1;

                s.bowler.runs += runs;
                s.bowler.balls += 1;

                s.currentOver.push({ type: 'run', val: runs, label: runs.toString() });

                this.saveDelivery({
                    over_number: pos.over_number, ball_number: pos.ball_number,
                    batsman_id: s.striker.id, non_striker_id: s.nonStriker.id, bowler_id: s.bowler.id,
                    runs: runs, is_legal: true
                });

                if (runs % 2 !== 0) this.rotateStrike();
                if (this.checkChaseComplete()) {
                    return;
                }
                this.checkOverEnd();
                UIController.updateDashboard();
            },

            processExtra: function(type) {
                MatchController.saveHistory();
                const s = MatchController.state;
                const pos = this.currentBallPosition(s);

                if (type === 'WD' || type === 'NB') {
                    s.score.runs += 1;
                    s.score.extras += 1;
                    s.bowler.runs += 1;
                    s.currentOver.push({ type: 'extra', val: 1, label: type });

                    this.saveDelivery({
                        over_number: pos.over_number, ball_number: pos.ball_number,
                        batsman_id: s.striker.id, non_striker_id: s.nonStriker.id, bowler_id: s.bowler.id,
                        runs: 0, extra_type: type, extra_runs: 1, is_legal: false
                    });
                    // No ball counted, no batter stats updated
                } else if (type === 'B' || type === 'LB') {
                    // Usually asked for runs associated with Byes. We default to 1 for simplicity in this demo.
                    let runs = parseInt(prompt(`How many ${type} runs?`, "1")) || 1;
                    s.score.runs += runs;
                    s.score.extras += runs;
                    s.score.balls += 1; // Legal delivery
                    s.striker.balls += 1;
                    s.bowler.balls += 1;
                    // Bowler doesn't concede Bye/LegBye runs in their personal stats
                    s.currentOver.push({ type: 'extra', val: runs, label: `${runs}${type}` });

                    this.saveDelivery({
                        over_number: pos.over_number, ball_number: pos.ball_number,
                        batsman_id: s.striker.id, non_striker_id: s.nonStriker.id, bowler_id: s.bowler.id,
                        runs: 0, extra_type: type, extra_runs: runs, is_legal: true
                    });

                    if (runs % 2 !== 0) this.rotateStrike();
                    if (this.checkChaseComplete()) {
                        return;
                    }
                    this.checkOverEnd();
                }
                UIController.updateDashboard();
            },

            processWicket: function(type) {
                MatchController.saveHistory();
                const s = MatchController.state;
                const pos = this.currentBallPosition(s);

                s.score.wickets += 1;
                s.score.balls += 1;

                if (type !== 'RO') { // Run Out doesn't always credit the bowler, but standard Caught/Bowled do.
                    s.bowler.wickets += 1;
                }

                const outPlayerId = s.striker.id;

                s.striker.balls += 1;
                s.bowler.balls += 1;
                s.currentOver.push({ type: 'wicket', val: 0, label: 'W' });

                this.saveDelivery({
                    over_number: pos.over_number, ball_number: pos.ball_number,
                    batsman_id: s.striker.id, non_striker_id: s.nonStriker.id, bowler_id: s.bowler.id,
                    runs: 0, is_wicket: true, wicket_type: type, player_out_id: outPlayerId, is_legal: true
                });

                if (s.score.wickets >= 10) {
                    this.endInnings();
                } else {
                    this.checkOverEnd(true);
                }

                UIController.updateDashboard();
            },

            checkChaseComplete: function() {
                const s = MatchController.state;

                if (s.innings === 2 && s.target !== null && s.score.runs >= s.target) {
                    this.endInnings();
                    return true;
                }

                return false;
            },

            rotateStrike: function() {
                const s = MatchController.state;
                const temp = s.striker;
                s.striker = s.nonStriker;
                s.nonStriker = temp;
            },

            checkOverEnd: function(isWicket = false) {
                const s = MatchController.state;
                if (s.score.balls > 0 && s.score.balls % 6 === 0) {
                    this.rotateStrike(); // Rotate at end of over
                    s.currentOver = []; // reset over timeline

                    if (s.score.balls === s.totalOvers * 6) {
                        this.endInnings();
                    } else if (isWicket) {
                        // If it's a wicket AND end of over, we need both modals. Sequence matters.
                        UIController.showModal('modal-new-batter', () => {
                            UIController.showModal('modal-next-bowler');
                        });
                    } else {
                        UIController.showModal('modal-next-bowler');
                    }
                } else if (isWicket) {
                    UIController.showModal('modal-new-batter');
                }
            },

            endInnings: function() {
                const s = MatchController.state;

                // Persist the innings that just finished.
                api(`/innings/${s.inningId}`, {
                    method: 'PATCH',
                    body: JSON.stringify({
                        total_runs: s.score.runs, total_wickets: s.score.wickets,
                        total_balls: s.score.balls, extras: s.score.extras,
                        status: 'completed'
                    })
                }).catch(() => console.error('Failed to close innings'));

                if (s.innings === 1) {
                    const firstInningsScore = s.score.runs;
                    const firstInningsBattingTeam = s.battingTeam.name;
                    const chasingTeam = s.bowlingTeam;

                    s.innings = 2;
                    s.target = firstInningsScore + 1;

                    // Swap teams
                    const tempTeam = s.battingTeam;
                    s.battingTeam = s.bowlingTeam;
                    s.bowlingTeam = tempTeam;

                    // Reset scores but keep state object
                    s.score = { runs: 0, wickets: 0, balls: 0, extras: 0 };
                    s.currentOver = [];
                    s.battingStats = {};
                    s.bowlingStats = {};
                    s.deliveryIds = [];

                    api('/innings', {
                        method: 'POST',
                        body: JSON.stringify({
                            match_id: s.matchId, innings_number: 2,
                            batting_team_id: s.battingTeam.id, bowling_team_id: s.bowlingTeam.id,
                            target: s.target
                        })
                    }).then(res => { s.inningId = res.inning_id; })
                      .catch(() => console.error('Failed to start 2nd innings'));

                    UIController.showTargetModal(
                        chasingTeam,
                        s.target,
                        firstInningsScore,
                        firstInningsBattingTeam
                    );

                } else {
                    const battingWon = s.score.runs >= s.target;
                    const result = battingWon
                        ? `${s.battingTeam.name} won by ${10 - s.score.wickets} wickets!`
                        : `${s.bowlingTeam.name} won by ${s.target - s.score.runs - 1} runs!`;
                    const winnerId = battingWon ? s.battingTeam.id : s.bowlingTeam.id;

                    api(`/matches/${s.matchId}/complete`, {
                        method: 'PATCH',
                        body: JSON.stringify({ winner_team_id: winnerId, result_text: result })
                    }).catch(() => console.error('Failed to close match'));

                    UIController.showMatchResultModal(result);
                }
            },

            finishMatch: function() {
                MatchController.state = null;
                MatchController.history = [];
                UIController.resetSetupForm();
                UIController.showScreen('setup-screen');
            }
        };

        /**
         * MODULE: UI Controller
         */
        const UIController = {
            isSecondInningsSetup: false,

            init: function() {
                this.populateSetupSelects();
                this.setupTeamChangeListeners();
                this.setupPlayerChangeListeners();
                document.getElementById('start-match-btn').addEventListener('click', MatchController.startMatch);
                this.setupKeypad();
                this.setupModals();
            },

            populateSelect: function(selectId, items, placeholder, selectedValue = '', excludeIds = []) {
                const select = document.getElementById(selectId);
                const normalizedExclude = excludeIds.map(String);
                const preserveValue = selectedValue !== '' ? String(selectedValue) : select.value;

                select.innerHTML = '';

                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = placeholder;
                select.appendChild(placeholderOption);

                items.forEach(item => {
                    if (normalizedExclude.includes(String(item.id))) {
                        return;
                    }

                    const option = document.createElement('option');
                    option.value = String(item.id);
                    option.textContent = item.name;
                    select.appendChild(option);
                });

                if (preserveValue && normalizedExclude.includes(preserveValue)) {
                    select.value = '';
                } else {
                    select.value = preserveValue || '';
                }
            },

            playersForTeam: function(teamId) {
                if (!teamId) {
                    return [];
                }

                return Database.players.filter(p => String(p.team_id) === String(teamId));
            },

            populateSetupSelects: function() {
                this.populateSelect('tournament-select', Database.tournaments, 'Select tournament (optional)');
                this.refreshTeamSelects();
                this.refreshPlayerSelects();
            },

            refreshTeamSelects: function() {
                const team1Id = document.getElementById('team1-select').value;
                const team2Id = document.getElementById('team2-select').value;

                this.populateSelect(
                    'team1-select',
                    Database.teams,
                    'Select batting team',
                    team1Id,
                    team2Id ? [team2Id] : []
                );
                this.populateSelect(
                    'team2-select',
                    Database.teams,
                    'Select bowling team',
                    team2Id,
                    team1Id ? [team1Id] : []
                );
            },

            refreshPlayerSelects: function() {
                if (this.isSecondInningsSetup && MatchController.state?.battingTeam && MatchController.state?.bowlingTeam) {
                    this.refreshPlayerSelectsForTeams(
                        MatchController.state.battingTeam.id,
                        MatchController.state.bowlingTeam.id
                    );
                    document.getElementById('striker-select').disabled = false;
                    document.getElementById('nonstriker-select').disabled = false;
                    document.getElementById('opening-bowler-select').disabled = false;
                    return;
                }

                const battingTeamId = document.getElementById('team1-select').value;
                const bowlingTeamId = document.getElementById('team2-select').value;
                this.refreshPlayerSelectsForTeams(battingTeamId, bowlingTeamId);
            },

            refreshBatterSelects: function() {
                const battingTeamId = this.isSecondInningsSetup && MatchController.state?.battingTeam
                    ? String(MatchController.state.battingTeam.id)
                    : document.getElementById('team1-select').value;
                const strikerId = document.getElementById('striker-select').value;
                const nonStrikerId = document.getElementById('nonstriker-select').value;
                const batters = this.playersForTeam(battingTeamId);

                this.populateSelect(
                    'striker-select',
                    batters,
                    battingTeamId ? 'Select striker' : 'Select batting team first',
                    strikerId,
                    nonStrikerId ? [nonStrikerId] : []
                );
                this.populateSelect(
                    'nonstriker-select',
                    batters,
                    battingTeamId ? 'Select non-striker' : 'Select batting team first',
                    nonStrikerId,
                    strikerId ? [strikerId] : []
                );

                document.getElementById('striker-select').disabled = !battingTeamId || batters.length === 0;
                document.getElementById('nonstriker-select').disabled = !battingTeamId || batters.length === 0;
            },

            refreshPlayerSelectsForTeams: function(battingTeamId, bowlingTeamId) {
                const strikerId = document.getElementById('striker-select').value;
                const nonStrikerId = document.getElementById('nonstriker-select').value;
                const bowlerId = document.getElementById('opening-bowler-select').value;
                const batters = this.playersForTeam(battingTeamId);
                const bowlers = this.playersForTeam(bowlingTeamId);

                this.populateSelect(
                    'striker-select',
                    batters,
                    battingTeamId ? 'Select striker' : 'Select batting team first',
                    strikerId,
                    nonStrikerId ? [nonStrikerId] : []
                );
                this.populateSelect(
                    'nonstriker-select',
                    batters,
                    battingTeamId ? 'Select non-striker' : 'Select batting team first',
                    nonStrikerId,
                    strikerId ? [strikerId] : []
                );
                this.populateSelect(
                    'opening-bowler-select',
                    bowlers,
                    bowlingTeamId ? 'Select opening bowler' : 'Select bowling team first',
                    bowlerId
                );

                document.getElementById('striker-select').disabled = !battingTeamId || batters.length === 0;
                document.getElementById('nonstriker-select').disabled = !battingTeamId || batters.length === 0;
                document.getElementById('opening-bowler-select').disabled = !bowlingTeamId || bowlers.length === 0;
            },

            prepareSecondInningsSetup: function(battingTeam, bowlingTeam, target) {
                this.isSecondInningsSetup = true;
                this.showScreen('setup-screen');

                const battingTeamId = String(battingTeam.id);
                const bowlingTeamId = String(bowlingTeam.id);

                // Rebuild team options — 1st-innings exclusions may have removed these teams.
                this.populateSelect(
                    'team1-select',
                    Database.teams,
                    'Select batting team',
                    battingTeamId,
                    [bowlingTeamId]
                );
                this.populateSelect(
                    'team2-select',
                    Database.teams,
                    'Select bowling team',
                    bowlingTeamId,
                    [battingTeamId]
                );

                this.refreshPlayerSelectsForTeams(battingTeamId, bowlingTeamId);

                ['striker-select', 'nonstriker-select', 'opening-bowler-select'].forEach(id => {
                    document.getElementById(id).value = '';
                });

                document.getElementById('team1-select').disabled = true;
                document.getElementById('team2-select').disabled = true;
                document.getElementById('tournament-select').disabled = true;
                document.getElementById('total-overs').disabled = true;

                // Ensure player dropdowns stay interactive for 2nd innings openers.
                document.getElementById('striker-select').disabled = false;
                document.getElementById('nonstriker-select').disabled = false;
                document.getElementById('opening-bowler-select').disabled = false;

                document.getElementById('setup-title').innerHTML = '<i class="fas fa-trophy mr-2"></i>2nd Innings Setup';
                document.getElementById('setup-subtitle').textContent = `Select openers for ${battingTeam.name}`;
                document.getElementById('start-match-btn').innerHTML = 'Start 2nd Innings <i class="fas fa-play ml-2"></i>';

                const banner = document.getElementById('second-innings-banner');
                banner.classList.remove('hidden');
                banner.textContent = `${battingTeam.name} need ${target} runs to win.`;

                document.getElementById('setup-screen').scrollIntoView({ behavior: 'smooth', block: 'start' });
            },

            resetSetupForm: function() {
                this.isSecondInningsSetup = false;
                document.getElementById('setup-title').innerHTML = '<i class="fas fa-trophy mr-2"></i>New Match Setup';
                document.getElementById('setup-subtitle').textContent = 'Configure tournament and teams';
                document.getElementById('start-match-btn').innerHTML = 'Start Match <i class="fas fa-play ml-2"></i>';
                document.getElementById('second-innings-banner').classList.add('hidden');

                ['tournament-select', 'team1-select', 'team2-select', 'total-overs'].forEach(id => {
                    document.getElementById(id).disabled = false;
                });

                document.getElementById('team1-select').value = '';
                document.getElementById('team2-select').value = '';
                document.getElementById('tournament-select').value = '';
                document.getElementById('total-overs').value = '20';
                this.refreshTeamSelects();
                this.refreshPlayerSelects();
            },

            showTargetModal: function(chasingTeam, target, firstInningsScore, firstInningsBattingTeam) {
                document.getElementById('innings-target-message').textContent =
                    `Innings over! ${firstInningsBattingTeam} scored ${firstInningsScore} runs.`;
                document.getElementById('innings-target-score').textContent = target;
                document.getElementById('innings-target-team').textContent =
                    `Target for ${chasingTeam.name} is ${target}`;
                document.getElementById('modal-innings-target').classList.remove('hidden');
            },

            showMatchResultModal: function(resultText) {
                document.getElementById('match-result-text').textContent = resultText;
                document.getElementById('modal-match-result').classList.remove('hidden');
            },

            refreshModalSelects: function() {
                const s = MatchController.state;
                if (!s) {
                    return;
                }

                const batters = this.playersForTeam(s.battingTeam.id).filter(p => {
                    const outIds = Object.keys(s.battingStats).map(Number);
                    return !outIds.includes(p.id) || p.id === s.nonStriker?.id;
                });

                const bowlers = this.playersForTeam(s.bowlingTeam.id);

                this.populateSelect('new-batter-select', batters, 'Select batter');
                this.populateSelect('next-bowler-select', bowlers, 'Select bowler');
            },

            setupTeamChangeListeners: function() {
                document.getElementById('team1-select').addEventListener('change', (event) => {
                    if (this.isSecondInningsSetup) {
                        return;
                    }

                    const team2Select = document.getElementById('team2-select');
                    if (team2Select.value && team2Select.value === event.target.value) {
                        team2Select.value = '';
                    }
                    this.refreshTeamSelects();
                    this.refreshPlayerSelects();
                });

                document.getElementById('team2-select').addEventListener('change', (event) => {
                    if (this.isSecondInningsSetup) {
                        return;
                    }

                    const team1Select = document.getElementById('team1-select');
                    if (team1Select.value && team1Select.value === event.target.value) {
                        team1Select.value = '';
                    }
                    this.refreshTeamSelects();
                    this.refreshPlayerSelects();
                });
            },

            setupPlayerChangeListeners: function() {
                document.getElementById('striker-select').addEventListener('change', (event) => {
                    const nonStrikerSelect = document.getElementById('nonstriker-select');
                    if (nonStrikerSelect.value && nonStrikerSelect.value === event.target.value) {
                        nonStrikerSelect.value = '';
                    }
                    this.refreshBatterSelects();
                    document.getElementById('striker-select').disabled = false;
                    document.getElementById('nonstriker-select').disabled = false;
                });

                document.getElementById('nonstriker-select').addEventListener('change', (event) => {
                    const strikerSelect = document.getElementById('striker-select');
                    if (strikerSelect.value && strikerSelect.value === event.target.value) {
                        strikerSelect.value = '';
                    }
                    this.refreshBatterSelects();
                    document.getElementById('striker-select').disabled = false;
                    document.getElementById('nonstriker-select').disabled = false;
                });
            },

            showScreen: function(screenId) {
                const setup = document.getElementById('setup-screen');
                const dashboard = document.getElementById('dashboard-screen');

                setup.classList.add('hidden');
                dashboard.classList.add('hidden');
                dashboard.classList.remove('flex');

                const screen = document.getElementById(screenId);
                screen.classList.remove('hidden');

                if (screenId === 'dashboard-screen') {
                    dashboard.classList.add('flex');
                }
            },

            setupKeypad: function() {
                document.querySelectorAll('.key-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const val = e.target.dataset.val;
                        const type = e.target.dataset.type;

                        if (e.target.id === 'btn-undo') {
                            MatchController.undo();
                            return;
                        }
                        if (e.target.id === 'btn-end-innings') {
                            if(confirm("Are you sure you want to declare/end the innings?")) ScoringEngine.endInnings();
                            return;
                        }

                        if (!val) return;

                        if (!type) {
                            ScoringEngine.processRun(parseInt(val));
                        } else if (type === 'extra') {
                            ScoringEngine.processExtra(val);
                        } else if (type === 'wicket') {
                            ScoringEngine.processWicket(val);
                        }
                    });
                });
            },

            setupModals: function() {
                const closeAndProceed = (modalId, callback) => {
                    document.getElementById(modalId).classList.add('hidden');
                    if (callback) callback();
                    this.updateDashboard();
                };

                document.getElementById('confirm-new-batter').addEventListener('click', () => {
                    const select = document.getElementById('new-batter-select');
                    const id = select.value;
                    const name = select.options[select.selectedIndex]?.textContent;

                    if (!id) {
                        return alert("Select a batter!");
                    }

                    MatchController.state.striker = MatchController.initBattingStats(id, name);
                    select.value = '';

                    closeAndProceed('modal-new-batter', this.queuedModalCallback);
                    this.queuedModalCallback = null;
                });

                document.getElementById('confirm-next-bowler').addEventListener('click', () => {
                    const select = document.getElementById('next-bowler-select');
                    const id = select.value;
                    const name = select.options[select.selectedIndex]?.textContent;

                    if (!id) {
                        return alert("Select a bowler!");
                    }

                    MatchController.state.bowler = MatchController.initBowlingStats(id, name);
                    select.value = '';

                    closeAndProceed('modal-next-bowler');
                });

                document.getElementById('confirm-innings-target').addEventListener('click', () => {
                    document.getElementById('modal-innings-target').classList.add('hidden');

                    const s = MatchController.state;
                    if (s?.battingTeam && s?.bowlingTeam && s?.target) {
                        UIController.prepareSecondInningsSetup(s.battingTeam, s.bowlingTeam, s.target);
                    }
                });

                document.getElementById('confirm-match-result').addEventListener('click', () => {
                    document.getElementById('modal-match-result').classList.add('hidden');
                    ScoringEngine.finishMatch();
                });
            },

            showModal: function(modalId, callback = null) {
                if (callback) {
                    this.queuedModalCallback = callback;
                }

                this.refreshModalSelects();
                document.getElementById(modalId).classList.remove('hidden');
            },

            updateDashboard: function() {
                const s = MatchController.state;
                if (!s) return;

                // Header
                document.getElementById('batting-team-name').textContent = s.battingTeam.name;
                document.getElementById('innings-label').textContent = s.innings === 2 ? '2nd Innings' : '1st Innings';
                document.getElementById('main-score').textContent = `${s.score.runs}/${s.score.wickets}`;
                document.getElementById('main-overs').textContent = `(${Utils.getOversString(s.score.balls)})`;

                const crr = s.score.balls === 0 ? "0.00" : ((s.score.runs / s.score.balls) * 6).toFixed(2);
                document.getElementById('crr-display').textContent = `CRR: ${crr}`;

                if (s.target && s.innings === 2) {
                    const targetEl = document.getElementById('target-display');
                    targetEl.classList.remove('hidden');
                    const rrrBalls = (s.totalOvers * 6) - s.score.balls;
                    const rrrRuns = s.target - s.score.runs;
                    const rrr = rrrBalls <= 0 ? "0.00" : ((rrrRuns / rrrBalls) * 6).toFixed(2);
                    const need = Math.max(s.target - s.score.runs, 0);
                    targetEl.textContent = `Target: ${s.target} | Need: ${need} | RRR: ${rrr}`;
                } else {
                    document.getElementById('target-display').classList.add('hidden');
                }

                // Striker
                if (s.striker) {
                    document.getElementById('striker-name').textContent = `${s.striker.name} *`;
                    document.getElementById('striker-runs').textContent = s.striker.runs;
                    document.getElementById('striker-balls').textContent = `(${s.striker.balls})`;
                    document.getElementById('striker-4s').textContent = s.striker.fours;
                    document.getElementById('striker-6s').textContent = s.striker.sixes;
                    document.getElementById('striker-sr').textContent = Utils.calculateSR(s.striker.runs, s.striker.balls);
                }

                // Non-Striker
                if (s.nonStriker) {
                    document.getElementById('nonstriker-name').textContent = s.nonStriker.name;
                    document.getElementById('nonstriker-runs').textContent = s.nonStriker.runs;
                    document.getElementById('nonstriker-balls').textContent = `(${s.nonStriker.balls})`;
                    document.getElementById('nonstriker-4s').textContent = s.nonStriker.fours;
                    document.getElementById('nonstriker-6s').textContent = s.nonStriker.sixes;
                    document.getElementById('nonstriker-sr').textContent = Utils.calculateSR(s.nonStriker.runs, s.nonStriker.balls);
                }

                // Bowler
                if (s.bowler) {
                    document.getElementById('bowler-name').textContent = s.bowler.name;
                    document.getElementById('bowler-overs').textContent = Utils.getOversString(s.bowler.balls);
                    document.getElementById('bowler-runs').textContent = s.bowler.runs;
                    document.getElementById('bowler-wickets').textContent = s.bowler.wickets;
                    document.getElementById('bowler-eco').textContent = Utils.calculateEco(s.bowler.runs, s.bowler.balls);
                }

                // Current Over Timeline — one circle per ball bowled this over
                const overContainer = document.getElementById('current-over-balls');
                overContainer.innerHTML = '';

                if (s.currentOver.length === 0) {
                    overContainer.innerHTML = '<div class="text-gray-500 text-sm italic">No balls bowled this over yet</div>';
                } else {
                    s.currentOver.forEach((ball, index) => {
                        const div = document.createElement('div');
                        div.className = `ball-circle flex-shrink-0 ${
                            ball.type === 'wicket' ? 'wicket' :
                            (ball.type === 'extra' ? 'extra' :
                            (ball.val >= 4 ? 'boundary' : ''))
                        }`;
                        div.title = `Ball ${index + 1}: ${ball.label}`;
                        div.textContent = ball.label;
                        overContainer.appendChild(div);
                    });
                }
            }
        };

        // Initialize Application
        document.addEventListener('DOMContentLoaded', async () => {
            await loadDatabase();
            UIController.init();
        });
    </script>
</body>
</html>
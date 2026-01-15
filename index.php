<?php
session_start();

// Initialize session
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 1000;
    $_SESSION['games_played'] = 0;
    $_SESSION['total_wins'] = 0;
}

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Handle game actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // SLOTS GAME
    if ($action == 'spin_slots') {
        $bet = intval($_POST['bet'] ?? 10);
        if ($bet > 0 && $_SESSION['balance'] >= $bet) {
            $_SESSION['balance'] -= $bet;
            
            $symbols = ['🍎', '🍊', '🍋', '🍌', '🍇', '🍓'];
            $reel1 = $symbols[array_rand($symbols)];
            $reel2 = $symbols[array_rand($symbols)];
            $reel3 = $symbols[array_rand($symbols)];
            
            $winAmount = 0;
            $message = '';
            
            if ($reel1 === $reel2 && $reel2 === $reel3) {
                $winAmount = $bet * 10;
                $message = "🎉 JACKPOT! All three match! You won $winAmount coins!";
                $_SESSION['total_wins'] += $winAmount;
            } elseif ($reel1 === $reel2 || $reel2 === $reel3 || $reel1 === $reel3) {
                $winAmount = $bet * 3;
                $message = "✨ Two Match! You won $winAmount coins!";
                $_SESSION['total_wins'] += $winAmount;
            } else {
                $message = "❌ No match. Better luck next time!";
            }
            
            $_SESSION['balance'] += $winAmount;
            $_SESSION['slots_data'] = [
                'reel1' => $reel1,
                'reel2' => $reel2,
                'reel3' => $reel3,
                'bet' => $bet,
                'win' => $winAmount,
                'message' => $message
            ];
            $_SESSION['games_played']++;
            $page = 'slots';
        }
    }
    
    // DICE GAME
    if ($action == 'roll_dice') {
        $bet = intval($_POST['bet'] ?? 50);
        $prediction = isset($_POST['prediction']) ? $_POST['prediction'] : '';
        
        if ($bet > 0 && $_SESSION['balance'] >= $bet && $prediction) {
            $_SESSION['balance'] -= $bet;
            
            $diceValue = rand(1, 6);
            $isHigh = $diceValue >= 4;
            $isLow = $diceValue <= 3;
            
            $winAmount = 0;
            $message = '';
            
            if (($prediction === 'high' && $isHigh) || ($prediction === 'low' && $isLow)) {
                $winAmount = $bet * 2;
                $message = "🎉 Correct! You predicted $prediction and rolled $diceValue! You won $winAmount coins!";
                $_SESSION['total_wins'] += $winAmount;
            } else {
                $message = "❌ Wrong prediction! You predicted $prediction but rolled $diceValue. Better luck next time!";
            }
            
            $_SESSION['balance'] += $winAmount;
            $_SESSION['dice_data'] = [
                'value' => $diceValue,
                'prediction' => $prediction,
                'bet' => $bet,
                'win' => $winAmount,
                'message' => $message
            ];
            $_SESSION['games_played']++;
            $page = 'dice';
        }
    }
    
    // MINES GAME
    if ($action == 'start_mines') {
        $bet = intval($_POST['bet'] ?? 10);
        if ($bet > 0 && $_SESSION['balance'] >= $bet) {
            $_SESSION['balance'] -= $bet;
            
            $grid = [];
            for ($i = 0; $i < 25; $i++) {
                $grid[] = [
                    'mine' => rand(0, 100) < 30,
                    'revealed' => false
                ];
            }
            
            $_SESSION['mines_data'] = [
                'grid' => $grid,
                'bet' => $bet,
                'safe_count' => 0,
                'game_active' => true,
                'message' => 'Click a tile to start!'
            ];
            $page = 'mines';
        }
    }
    
    if ($action == 'click_mine') {
        $index = intval($_POST['index'] ?? -1);
        if (isset($_SESSION['mines_data']) && $_SESSION['mines_data']['game_active'] && $index >= 0 && $index < 25) {
            $_SESSION['mines_data']['grid'][$index]['revealed'] = true;
            
            if ($_SESSION['mines_data']['grid'][$index]['mine']) {
                $_SESSION['mines_data']['game_active'] = false;
                $_SESSION['mines_data']['message'] = "💣 GAME OVER! You hit a mine! Safe tiles: " . $_SESSION['mines_data']['safe_count'];
            } else {
                $_SESSION['mines_data']['safe_count']++;
                $multiplier = 1 + ($_SESSION['mines_data']['safe_count'] * 0.5);
                $potentialWin = intval($_SESSION['mines_data']['bet'] * $multiplier);
                $_SESSION['mines_data']['message'] = "✅ Safe! Safe tiles: " . $_SESSION['mines_data']['safe_count'] . " | Multiplier: " . number_format($multiplier, 2) . "x | Potential win: $potentialWin";
            }
        }
        $page = 'mines';
    }
    
    if ($action == 'cashout_mines') {
        if (isset($_SESSION['mines_data']) && $_SESSION['mines_data']['game_active']) {
            $multiplier = 1 + ($_SESSION['mines_data']['safe_count'] * 0.5);
            $winAmount = intval($_SESSION['mines_data']['bet'] * $multiplier);
            $_SESSION['balance'] += $winAmount;
            $_SESSION['total_wins'] += $winAmount;
            $_SESSION['mines_data']['game_active'] = false;
            $_SESSION['mines_data']['message'] = "💰 Cashed out! You won $winAmount coins!";
            $_SESSION['games_played']++;
        }
        $page = 'mines';
    }
    
    // PLINKO GAME
    if ($action == 'drop_plinko') {
        $bet = intval($_POST['bet'] ?? 10);
        if ($bet > 0 && $_SESSION['balance'] >= $bet) {
            $_SESSION['balance'] -= $bet;
            
            $multipliers = [0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 5, 10];
            $multiplier = $multipliers[array_rand($multipliers)];
            $winAmount = intval($bet * $multiplier);
            
            $_SESSION['balance'] += $winAmount;
            $_SESSION['total_wins'] += $winAmount;
            
            $_SESSION['plinko_data'] = [
                'multiplier' => $multiplier,
                'bet' => $bet,
                'win' => $winAmount,
                'message' => "🎉 Ball landed on " . number_format($multiplier, 1) . "x multiplier! You won $winAmount coins!"
            ];
            $_SESSION['games_played']++;
            $page = 'plinko';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="100% Free Social Casino - Play Slots, Dice, Mines, Plinko with Virtual Coins">
    <title>Social Casino - 100% Free Gaming Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --gold: #D4AF37; --dark: #1a1a3e; --light: #f5f5f5; --text: #333; --white: #fff; }
        body { font-family: 'Inter', sans-serif; background: var(--light); color: var(--text); }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }
        
        nav { background: var(--dark); padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 1000; flex-wrap: wrap; gap: 1rem; }
        .logo { font-size: 1.8rem; color: var(--gold); font-weight: 800; text-decoration: none; }
        nav a { color: var(--white); text-decoration: none; margin: 0 0.8rem; transition: color 0.3s; }
        nav a:hover { color: var(--gold); }
        .balance-display { color: var(--gold); font-weight: bold; font-size: 1.1rem; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .hero { background: linear-gradient(135deg, var(--dark) 0%, #2d2d5f 100%); color: var(--white); padding: 4rem 2rem; text-align: center; border-radius: 12px; margin-bottom: 3rem; }
        .hero h1 { font-size: 2.5rem; color: var(--gold); margin-bottom: 1rem; }
        .hero p { font-size: 1.1rem; margin-bottom: 1.5rem; }
        .disclaimer { background: rgba(255, 255, 255, 0.1); border-left: 4px solid var(--gold); padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0; }
        
        .games-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin: 3rem 0; }
        .game-card { background: var(--white); border-radius: 12px; padding: 2rem; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
        .game-card:hover { transform: translateY(-8px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .game-icon { font-size: 3.5rem; margin-bottom: 1rem; }
        .game-card h3 { color: var(--dark); margin-bottom: 0.5rem; }
        .game-card p { color: #666; margin-bottom: 1.5rem; }
        
        .btn { background: var(--gold); color: var(--dark); border: none; padding: 0.8rem 1.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: background 0.3s; text-decoration: none; display: inline-block; }
        .btn:hover { background: #c9a227; }
        .btn-secondary { background: #e0e0e0; color: var(--text); }
        .btn-secondary:hover { background: #d0d0d0; }
        
        .game-container { background: var(--white); border-radius: 12px; padding: 2.5rem; margin: 2rem 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .game-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 2px solid var(--gold); padding-bottom: 1rem; }
        .game-header h2 { color: var(--dark); }
        
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat { background: #f9f9f9; padding: 1.5rem; border-radius: 8px; text-align: center; border-left: 4px solid var(--gold); }
        .stat-label { font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; }
        .stat-value { font-size: 1.8rem; font-weight: bold; color: var(--gold); }
        
        .slots-display { display: flex; justify-content: center; gap: 1.5rem; margin: 2.5rem 0; }
        .reel { width: 100px; height: 140px; background: linear-gradient(135deg, var(--dark) 0%, #2d2d5f 100%); border: 3px solid var(--gold); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; color: var(--white); }
        
        .dice-display { text-align: center; margin: 2rem 0; }
        .dice { width: 120px; height: 120px; background: linear-gradient(135deg, var(--dark) 0%, #2d2d5f 100%); border: 3px solid var(--gold); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--white); margin: 1.5rem auto; }
        
        .prediction-buttons { display: flex; justify-content: center; gap: 1rem; margin: 2rem 0; flex-wrap: wrap; }
        .prediction-btn { padding: 1rem 2rem; border: 2px solid #ddd; background: var(--white); border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
        .prediction-btn:hover { border-color: var(--gold); }
        
        .mines-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; max-width: 450px; margin: 2rem auto; }
        .mine-tile { width: 80px; height: 80px; background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); border: 2px solid #333; border-radius: 8px; cursor: pointer; font-size: 2rem; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
        .mine-tile:hover:not(.revealed) { transform: scale(1.08); }
        .mine-tile.revealed { background: #f0f0f0; cursor: not-allowed; border-color: #999; }
        
        .message { padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0; text-align: center; font-weight: 600; font-size: 1.1rem; }
        .message.win { background: #c8e6c9; color: #2e7d32; border-left: 4px solid #4CAF50; }
        .message.lose { background: #ffcdd2; color: #c62828; border-left: 4px solid #f44336; }
        .message.info { background: #e3f2fd; color: #1565c0; border-left: 4px solid #2196F3; }
        
        .form-group { margin: 1.5rem 0; text-align: center; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        .form-group input { padding: 0.8rem; border: 2px solid #ddd; border-radius: 6px; font-size: 1rem; width: 100%; max-width: 300px; }
        
        .controls { display: flex; justify-content: center; gap: 1rem; margin: 2rem 0; flex-wrap: wrap; }
        
        footer { background: var(--dark); color: var(--white); text-align: center; padding: 2rem; margin-top: 4rem; }
        footer p { margin: 0.5rem 0; }
        
        @media (max-width: 768px) {
            nav { flex-direction: column; }
            .hero h1 { font-size: 1.8rem; }
            .games-grid { grid-template-columns: 1fr; }
            .game-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .slots-display { gap: 1rem; }
            .reel { width: 80px; height: 110px; font-size: 2.5rem; }
            .mines-grid { grid-template-columns: repeat(4, 1fr); }
            .mine-tile { width: 70px; height: 70px; font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <nav>
        <a href="?page=home" class="logo">🎰 SOCIAL CASINO</a>
        <div>
            <a href="?page=home">Home</a>
            <a href="?page=games">Games</a>
            <a href="?page=slots">Slots</a>
            <a href="?page=dice">Dice</a>
            <a href="?page=mines">Mines</a>
            <a href="?page=plinko">Plinko</a>
        </div>
        <div class="balance-display">💰 Balance: <?php echo number_format($_SESSION['balance']); ?></div>
    </nav>

    <?php if ($page === 'home'): ?>
    <div class="container">
        <div class="hero">
            <h1>Welcome to Social Casino</h1>
            <p>100% Free Gaming Platform • Virtual Coins Only • No Real Money</p>
            <div class="disclaimer">
                <strong>⚠️ IMPORTANT DISCLAIMER:</strong> This is a 100% free-to-play entertainment platform. Virtual coins have NO real monetary value. All games are purely for entertainment purposes. You must be 18+ to play. This is NOT gambling.
            </div>
        </div>

        <h2 style="text-align: center; color: var(--dark); margin: 2.5rem 0;">Featured Games</h2>
        <div class="games-grid">
            <div class="game-card">
                <div class="game-icon">🎰</div>
                <h3>Premium Slots</h3>
                <p>Spin the reels and match symbols to win big multipliers!</p>
                <a href="?page=slots" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">🎲</div>
                <h3>Dice Game</h3>
                <p>Predict HIGH or LOW and win up to 2x your bet!</p>
                <a href="?page=dice" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">💣</div>
                <h3>Mines Game</h3>
                <p>Click safe tiles and build your multiplier. Avoid the mines!</p>
                <a href="?page=mines" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">⚪</div>
                <h3>Plinko Game</h3>
                <p>Drop balls and land on multipliers up to 10x!</p>
                <a href="?page=plinko" class="btn">Play Now</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($page === 'games'): ?>
    <div class="container">
        <h1 style="color: var(--dark); margin: 2rem 0;">Our Games</h1>
        <div class="games-grid">
            <div class="game-card">
                <div class="game-icon">🎰</div>
                <h3>Premium Slots</h3>
                <p>Experience the excitement of classic slot machines with stunning visuals and smooth gameplay.</p>
                <a href="?page=slots" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">🎲</div>
                <h3>Dice Game</h3>
                <p>Predict the dice outcome. Choose HIGH (4-6) or LOW (1-3) and win up to 2x your bet!</p>
                <a href="?page=dice" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">💣</div>
                <h3>Mines Game</h3>
                <p>Click safe tiles and avoid mines. Build your multiplier with each safe click!</p>
                <a href="?page=mines" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">⚪</div>
                <h3>Plinko Game</h3>
                <p>Drop balls from the top and watch them fall. Land on high multipliers to win big!</p>
                <a href="?page=plinko" class="btn">Play Now</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($page === 'slots'): ?>
    <div class="container">
        <div class="game-container">
            <div class="game-header">
                <h2>🎰 Slots Game</h2>
                <a href="?page=home" class="btn btn-secondary">Back to Home</a>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-label">Your Balance</div>
                    <div class="stat-value"><?php echo number_format($_SESSION['balance']); ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Games Played</div>
                    <div class="stat-value"><?php echo $_SESSION['games_played']; ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Total Wins</div>
                    <div class="stat-value"><?php echo number_format($_SESSION['total_wins']); ?></div>
                </div>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="slots_bet">Bet Amount (1-1000):</label>
                    <input type="number" id="slots_bet" name="bet" value="10" min="1" max="1000" required>
                </div>
                <div class="controls">
                    <button type="submit" name="action" value="spin_slots" class="btn">🎰 SPIN</button>
                    <a href="?page=slots" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <div class="slots-display">
                <div class="reel"><?php echo isset($_SESSION['slots_data']['reel1']) ? $_SESSION['slots_data']['reel1'] : '🍎'; ?></div>
                <div class="reel"><?php echo isset($_SESSION['slots_data']['reel2']) ? $_SESSION['slots_data']['reel2'] : '🍎'; ?></div>
                <div class="reel"><?php echo isset($_SESSION['slots_data']['reel3']) ? $_SESSION['slots_data']['reel3'] : '🍎'; ?></div>
            </div>

            <?php if (isset($_SESSION['slots_data'])): ?>
            <div class="message <?php echo $_SESSION['slots_data']['win'] > 0 ? 'win' : 'lose'; ?>">
                <?php echo $_SESSION['slots_data']['message']; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($page === 'dice'): ?>
    <div class="container">
        <div class="game-container">
            <div class="game-header">
                <h2>🎲 Dice Game</h2>
                <a href="?page=home" class="btn btn-secondary">Back to Home</a>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-label">Your Balance</div>
                    <div class="stat-value"><?php echo number_format($_SESSION['balance']); ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Multiplier</div>
                    <div class="stat-value">2.00x</div>
                </div>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="dice_bet">Bet Amount (1-1000):</label>
                    <input type="number" id="dice_bet" name="bet" value="50" min="1" max="1000" required>
                </div>

                <p style="text-align: center; font-size: 1.1rem; margin: 1.5rem 0; font-weight: 600;">Make Your Prediction:</p>
                <div class="prediction-buttons">
                    <button type="submit" name="prediction" value="high" class="prediction-btn">📈 HIGH (4-6)</button>
                    <button type="submit" name="prediction" value="low" class="prediction-btn">📉 LOW (1-3)</button>
                </div>
            </form>

            <div class="dice-display">
                <div class="dice"><?php echo isset($_SESSION['dice_data']['value']) ? $_SESSION['dice_data']['value'] : '🎲'; ?></div>
            </div>

            <?php if (isset($_SESSION['dice_data'])): ?>
            <div class="message <?php echo $_SESSION['dice_data']['win'] > 0 ? 'win' : 'lose'; ?>">
                <?php echo $_SESSION['dice_data']['message']; ?>
            </div>
            <?php endif; ?>

            <div class="controls">
                <a href="?page=dice" class="btn btn-secondary">Reset Game</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($page === 'mines'): ?>
    <div class="container">
        <div class="game-container">
            <div class="game-header">
                <h2>💣 Mines Game</h2>
                <a href="?page=home" class="btn btn-secondary">Back to Home</a>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-label">Your Balance</div>
                    <div class="stat-value"><?php echo number_format($_SESSION['balance']); ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Safe Tiles</div>
                    <div class="stat-value"><?php echo isset($_SESSION['mines_data']['safe_count']) ? $_SESSION['mines_data']['safe_count'] : 0; ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Multiplier</div>
                    <div class="stat-value"><?php echo isset($_SESSION['mines_data']['safe_count']) ? number_format(1 + ($_SESSION['mines_data']['safe_count'] * 0.5), 2) : '1.00'; ?>x</div>
                </div>
            </div>

            <?php if (!isset($_SESSION['mines_data']) || !$_SESSION['mines_data']['game_active']): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="mines_bet">Bet Amount (1-1000):</label>
                    <input type="number" id="mines_bet" name="bet" value="10" min="1" max="1000" required>
                </div>
                <div class="controls">
                    <button type="submit" name="action" value="start_mines" class="btn">Start New Game</button>
                </div>
            </form>
            <?php endif; ?>

            <?php if (isset($_SESSION['mines_data'])): ?>
            <div class="mines-grid">
                <?php foreach ($_SESSION['mines_data']['grid'] as $index => $tile): ?>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="click_mine">
                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                    <button type="submit" class="mine-tile <?php echo $tile['revealed'] ? 'revealed' : ''; ?>" <?php echo $tile['revealed'] || !$_SESSION['mines_data']['game_active'] ? 'disabled' : ''; ?>>
                        <?php echo $tile['revealed'] ? ($tile['mine'] ? '💣' : '✅') : '?'; ?>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>

            <div class="message info">
                <?php echo $_SESSION['mines_data']['message']; ?>
            </div>

            <?php if ($_SESSION['mines_data']['game_active']): ?>
            <div class="controls">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="cashout_mines">
                    <button type="submit" class="btn">💰 Cash Out</button>
                </form>
            </div>
            <?php else: ?>
            <div class="controls">
                <a href="?page=mines" class="btn btn-secondary">Play Again</a>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($page === 'plinko'): ?>
    <div class="container">
        <div class="game-container">
            <div class="game-header">
                <h2>⚪ Plinko Game</h2>
                <a href="?page=home" class="btn btn-secondary">Back to Home</a>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-label">Your Balance</div>
                    <div class="stat-value"><?php echo number_format($_SESSION['balance']); ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Last Multiplier</div>
                    <div class="stat-value"><?php echo isset($_SESSION['plinko_data']['multiplier']) ? number_format($_SESSION['plinko_data']['multiplier'], 1) . 'x' : '-'; ?></div>
                </div>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="plinko_bet">Bet Amount (1-1000):</label>
                    <input type="number" id="plinko_bet" name="bet" value="10" min="1" max="1000" required>
                </div>
                <div class="controls">
                    <button type="submit" name="action" value="drop_plinko" class="btn">⬇️ DROP BALL</button>
                    <a href="?page=plinko" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <div style="text-align: center; background: linear-gradient(135deg, var(--dark) 0%, #2d2d5f 100%); border-radius: 12px; padding: 2.5rem; margin: 2rem 0; color: var(--white);">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">⚪</div>
                <?php if (isset($_SESSION['plinko_data'])): ?>
                <div style="font-size: 1.3rem; color: var(--gold);">Multiplier: <?php echo number_format($_SESSION['plinko_data']['multiplier'], 1); ?>x</div>
                <div style="font-size: 1.1rem; margin-top: 0.5rem;">Win: <?php echo number_format($_SESSION['plinko_data']['win']); ?> coins</div>
                <?php else: ?>
                <div style="font-size: 1.3rem; color: var(--gold);">Ready to drop!</div>
                <?php endif; ?>
            </div>

            <?php if (isset($_SESSION['plinko_data'])): ?>
            <div class="message win">
                <?php echo $_SESSION['plinko_data']['message']; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <footer>
        <p>&copy; 2026 Social Casino. All Rights Reserved.</p>
        <p>100% Free-to-Play | Virtual Coins Only | No Real Money | Entertainment Only</p>
        <p style="font-size: 0.9rem; margin-top: 1rem;">This platform is for entertainment purposes only. Must be 18+ to play. Virtual coins have no real-world monetary value.</p>
    </footer>
</body>
</html>

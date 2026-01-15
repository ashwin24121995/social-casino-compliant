<?php
session_start();

// Initialize session balance
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 1000;
}

// Handle game actions
$action = $_GET['action'] ?? '';
$game = $_GET['game'] ?? '';

if ($action == 'spin_slots') {
    $bet = intval($_POST['bet'] ?? 10);
    if ($_SESSION['balance'] >= $bet) {
        $_SESSION['balance'] -= $bet;
        $symbols = ['🍎', '🍊', '🍋', '🍌', '🍇', '🍓'];
        $reel1 = $symbols[array_rand($symbols)];
        $reel2 = $symbols[array_rand($symbols)];
        $reel3 = $symbols[array_rand($symbols)];
        
        $winAmount = 0;
        if ($reel1 === $reel2 && $reel2 === $reel3) {
            $winAmount = $bet * 10;
            $result = "🎉 JACKPOT! You won $winAmount coins!";
        } elseif ($reel1 === $reel2 || $reel2 === $reel3 || $reel1 === $reel3) {
            $winAmount = $bet * 3;
            $result = "✨ Two Match! You won $winAmount coins!";
        } else {
            $result = "❌ No match. Better luck next time!";
        }
        
        $_SESSION['balance'] += $winAmount;
        $_SESSION['slots_result'] = ['result' => $result, 'reels' => [$reel1, $reel2, $reel3], 'win' => $winAmount];
    }
}

if ($action == 'roll_dice') {
    $bet = intval($_POST['bet'] ?? 50);
    $prediction = $_POST['prediction'] ?? '';
    
    if ($_SESSION['balance'] >= $bet && $prediction) {
        $_SESSION['balance'] -= $bet;
        $diceValue = rand(1, 6);
        $isHigh = $diceValue >= 4;
        $isLow = $diceValue <= 3;
        
        $winAmount = 0;
        if (($prediction === 'high' && $isHigh) || ($prediction === 'low' && $isLow)) {
            $winAmount = $bet * 2;
            $result = "🎉 You Won! +$winAmount coins!";
        } else {
            $result = "❌ You Lost! Better luck next time!";
        }
        
        $_SESSION['balance'] += $winAmount;
        $_SESSION['dice_result'] = ['result' => $result, 'value' => $diceValue, 'win' => $winAmount];
    }
}

if ($action == 'play_mines') {
    $bet = intval($_POST['bet'] ?? 10);
    if ($_SESSION['balance'] >= $bet) {
        $_SESSION['balance'] -= $bet;
        $_SESSION['mines_game'] = [
            'grid' => array_map(function() { return ['mine' => rand(0, 1) < 0.3, 'revealed' => false]; }, range(1, 25)),
            'safe_count' => 0,
            'active' => true
        ];
    }
}

if ($action == 'click_mine') {
    $index = intval($_GET['index'] ?? 0);
    if (isset($_SESSION['mines_game']) && $_SESSION['mines_game']['active']) {
        $_SESSION['mines_game']['grid'][$index]['revealed'] = true;
        if ($_SESSION['mines_game']['grid'][$index]['mine']) {
            $_SESSION['mines_game']['active'] = false;
            $_SESSION['mines_result'] = "💣 GAME OVER! You hit a mine!";
        } else {
            $_SESSION['mines_game']['safe_count']++;
            $multiplier = 1 + ($_SESSION['mines_game']['safe_count'] * 0.5);
            $_SESSION['mines_multiplier'] = $multiplier;
        }
    }
}

if ($action == 'drop_plinko') {
    $bet = intval($_POST['bet'] ?? 10);
    if ($_SESSION['balance'] >= $bet) {
        $_SESSION['balance'] -= $bet;
        $multipliers = [0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 5, 10];
        $multiplier = $multipliers[array_rand($multipliers)];
        $winAmount = intval($bet * $multiplier);
        $_SESSION['balance'] += $winAmount;
        $_SESSION['plinko_result'] = ['multiplier' => $multiplier, 'win' => $winAmount];
    }
}

$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Casino - 100% Free Gaming Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #D4AF37;
            --primary-dark: #1a1a3e;
            --primary-light: #f5f5f5;
            --text-dark: #333333;
            --text-light: #ffffff;
            --success: #4CAF50;
            --danger: #f44336;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary-light);
            color: var(--text-dark);
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }

        /* Navigation */
        nav {
            background-color: var(--primary-dark);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            color: var(--primary);
            font-weight: 800;
            text-decoration: none;
        }

        nav a {
            color: var(--text-light);
            text-decoration: none;
            margin: 0 1rem;
        }

        nav a:hover {
            color: var(--primary);
        }

        .balance {
            color: var(--primary);
            font-weight: bold;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #2d2d5f 100%);
            color: var(--text-light);
            padding: 4rem 2rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .game-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .game-card:hover {
            transform: translateY(-5px);
        }

        .game-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .btn {
            background-color: var(--primary);
            color: var(--text-dark);
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #c9a227;
        }

        .game-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem 0;
        }

        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 1rem;
        }

        .game-stats {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .stat {
            background: #f0f0f0;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        /* Slots */
        .slots-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
        }

        .reel {
            width: 80px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary-dark) 0%, #2d2d5f 100%);
            border: 3px solid var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--text-light);
        }

        /* Dice */
        .dice {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-dark) 0%, #2d2d5f 100%);
            border: 3px solid var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--text-light);
            margin: 2rem auto;
        }

        .prediction-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
        }

        .prediction-btn {
            padding: 1rem 2rem;
            font-size: 1rem;
            background: #e0e0e0;
            border: 2px solid transparent;
            border-radius: 6px;
            cursor: pointer;
        }

        /* Mines */
        .mines-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin: 2rem 0;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .mine-tile {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border: 2px solid #333;
            border-radius: 8px;
            cursor: pointer;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .mine-tile:hover {
            transform: scale(1.05);
        }

        .mine-tile.revealed {
            background: #f0f0f0;
            cursor: not-allowed;
        }

        .result-message {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            text-align: center;
            font-weight: bold;
        }

        .result-message.win {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .result-message.lose {
            background: #ffcdd2;
            color: #c62828;
        }

        .controls {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .controls button {
            padding: 1rem 2rem;
            font-size: 1rem;
        }

        form {
            margin: 1rem 0;
        }

        input[type="number"] {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 0.5rem;
        }

        footer {
            background-color: var(--primary-dark);
            color: var(--text-light);
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }

        .disclaimer {
            background: #fff3cd;
            border-left: 4px solid var(--primary);
            padding: 1rem;
            margin: 2rem 0;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            nav {
                flex-direction: column;
                gap: 1rem;
            }
            .games-grid {
                grid-template-columns: 1fr;
            }
            .game-stats {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
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
        <span class="balance">Balance: <?php echo $_SESSION['balance']; ?> 🪙</span>
    </nav>

    <!-- Home Page -->
    <?php if ($page === 'home'): ?>
    <div class="hero">
        <h1>Welcome to Social Casino</h1>
        <p>100% Free Gaming Platform • Virtual Coins Only • No Real Money</p>
        <div class="disclaimer">
            <strong>⚠️ IMPORTANT:</strong> This is a 100% free-to-play entertainment platform. Virtual coins have NO real money value. All games are for entertainment purposes only. Must be 18+ to play.
        </div>
    </div>

    <div class="container">
        <h2 style="text-align: center; margin: 2rem 0; color: var(--primary);">Featured Games</h2>
        <div class="games-grid">
            <div class="game-card">
                <div class="game-icon">🎰</div>
                <h3>Slots</h3>
                <p>Spin the reels and win big!</p>
                <a href="?page=slots" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">🎲</div>
                <h3>Dice</h3>
                <p>Predict the outcome!</p>
                <a href="?page=dice" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">💣</div>
                <h3>Mines</h3>
                <p>Avoid the mines!</p>
                <a href="?page=mines" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">⚪</div>
                <h3>Plinko</h3>
                <p>Drop and win!</p>
                <a href="?page=plinko" class="btn">Play Now</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Games List -->
    <?php if ($page === 'games'): ?>
    <div class="container">
        <h1 style="color: var(--primary); margin: 2rem 0;">Our Games</h1>
        <div class="games-grid">
            <div class="game-card">
                <div class="game-icon">🎰</div>
                <h3>Premium Slots</h3>
                <p>Experience the excitement of classic slot machines.</p>
                <a href="?page=slots" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">🎲</div>
                <h3>Dice Game</h3>
                <p>Predict the dice outcome. High/Low or specific numbers.</p>
                <a href="?page=dice" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">💣</div>
                <h3>Mines Game</h3>
                <p>Click safe tiles and avoid mines. Build your multiplier!</p>
                <a href="?page=mines" class="btn">Play Now</a>
            </div>
            <div class="game-card">
                <div class="game-icon">⚪</div>
                <h3>Plinko Game</h3>
                <p>Drop balls and watch them fall. Land on high multipliers!</p>
                <a href="?page=plinko" class="btn">Play Now</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Slots Game -->
    <?php if ($page === 'slots'): ?>
    <div class="container">
        <div class="game-container">
            <div class="game-header">
                <h2>🎰 Slots Game</h2>
                <a href="?page=home" class="btn">Back to Home</a>
            </div>

            <div class="game-stats">
                <div class="stat">
                    <div class="stat-label">Your Balance</div>
                    <div class="stat-value"><?php echo $_SESSION['balance']; ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Current Bet</div>
                    <div class="stat-value"><?php echo $_SESSION['slots_result']['win'] ?? 0; ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Multiplier</div>
                    <div class="stat-value">1.00x - 10.00x</div>
                </div>
            </div>

            <form method="POST" action="?action=spin_slots&game=slots">
                <div style="text-align: center;">
                    <label>Bet Amount: <input type="number" name="bet" value="10" min="1" max="1000"></label>
                    <button type="submit" class="btn">SPIN</button>
                    <a href="?page=slots" class="btn">Reset</a>
                </div>
            </form>

            <div class="slots-container">
                <div class="reel"><?php echo $_SESSION['slots_result']['reels'][0] ?? '🍎'; ?></div>
                <div class="reel"><?php echo $_SESSION['slots_result']['reels'][1] ?? '🍎'; ?></div>
                <div class="reel"><?php echo $_SESSION['slots_result']['reels'][2] ?? '🍎'; ?></div>
            </div>

            <?php if (isset($_SESSION['slots_result'])): ?>
            <div class="result-message <?php echo $_SESSION['slots_result']['win'] > 0 ? 'win' : 'lose'; ?>">
                <?php echo $_SESSION['slots_result']['result']; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Dice Game -->
    <?php if ($page === 'dice'): ?>
    <div class="container">
        <div class="game-container">
            <div class="game-header">
                <h2>🎲 Dice Game</h2>
                <a href="?page=home" class="btn">Back to Home</a>
            </div>

            <div class="game-stats">
                <div class="stat">
                    <div class="stat-label">Your Balance</div>
                    <div class="stat-value"><?php echo $_SESSION['balance']; ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Multiplier</div>
                    <div class="stat-value">2.00x</div>
                </div>
            </div>

            <form method="POST" action="?action=roll_dice&game=dice">
                <div style="text-align: center;">
                    <label>Bet Amount: <input type="number" name="bet" value="50" min="1" max="1000"></label>
                </div>

                <div style="text-align: center; margin: 2rem 0;">
                    <p style="font-size: 1.2rem; margin-bottom: 1rem;">Make your prediction:</p>
                    <div class="prediction-buttons">
                        <button type="submit" name="prediction" value="high" class="prediction-btn">HIGH (4-6)</button>
                        <button type="submit" name="prediction" value="low" class="prediction-btn">LOW (1-3)</button>
                    </div>
                </div>
            </form>

            <div class="dice"><?php echo $_SESSION['dice_result']['value'] ?? '🎲'; ?></div>

            <?php if (isset($_SESSION['dice_result'])): ?>
            <div class="result-message <?php echo $_SESSION['dice_result']['win'] > 0 ? 'win' : 'lose'; ?>">
                <?php echo $_SESSION['dice_result']['result']; ?>
            </div>
            <?php endif; ?>

            <div class="controls">
                <a href="?page=dice" class="btn">Reset</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mines Game -->
    <?php if ($page === 'mines'): ?>
    <div class="container">
        <div class="game-container">
            <div class="game-header">
                <h2>💣 Mines Game</h2>
                <a href="?page=home" class="btn">Back to Home</a>
            </div>

            <div class="game-stats">
                <div class="stat">
                    <div class="stat-label">Your Balance</div>
                    <div class="stat-value"><?php echo $_SESSION['balance']; ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Safe Tiles</div>
                    <div class="stat-value"><?php echo $_SESSION['mines_game']['safe_count'] ?? 0; ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Multiplier</div>
                    <div class="stat-value"><?php echo number_format($_SESSION['mines_multiplier'] ?? 1, 2); ?>x</div>
                </div>
            </div>

            <form method="POST" action="?action=play_mines&game=mines">
                <div style="text-align: center;">
                    <label>Bet Amount: <input type="number" name="bet" value="10" min="1" max="1000"></label>
                    <button type="submit" class="btn">New Game</button>
                </div>
            </form>

            <?php if (isset($_SESSION['mines_game'])): ?>
            <div class="mines-grid">
                <?php foreach ($_SESSION['mines_game']['grid'] as $index => $tile): ?>
                <a href="?action=click_mine&index=<?php echo $index; ?>&page=mines" class="mine-tile <?php echo $tile['revealed'] ? 'revealed' : ''; ?>">
                    <?php echo $tile['revealed'] ? ($tile['mine'] ? '💣' : '✅') : '?'; ?>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if (isset($_SESSION['mines_result'])): ?>
            <div class="result-message lose"><?php echo $_SESSION['mines_result']; ?></div>
            <?php endif; ?>
            <?php endif; ?>

            <div class="controls">
                <a href="?page=mines" class="btn">Reset</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Plinko Game -->
    <?php if ($page === 'plinko'): ?>
    <div class="container">
        <div class="game-container">
            <div class="game-header">
                <h2>⚪ Plinko Game</h2>
                <a href="?page=home" class="btn">Back to Home</a>
            </div>

            <div class="game-stats">
                <div class="stat">
                    <div class="stat-label">Your Balance</div>
                    <div class="stat-value"><?php echo $_SESSION['balance']; ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Last Multiplier</div>
                    <div class="stat-value"><?php echo isset($_SESSION['plinko_result']) ? $_SESSION['plinko_result']['multiplier'] . 'x' : '-'; ?></div>
                </div>
            </div>

            <form method="POST" action="?action=drop_plinko&game=plinko">
                <div style="text-align: center;">
                    <label>Bet Amount: <input type="number" name="bet" value="10" min="1" max="1000"></label>
                    <button type="submit" class="btn">DROP BALL</button>
                </div>
            </form>

            <div style="text-align: center; background: linear-gradient(135deg, var(--primary-dark) 0%, #2d2d5f 100%); border-radius: 12px; padding: 2rem; margin: 2rem 0; color: var(--text-light);">
                <div style="font-size: 2rem; margin: 1rem 0;">⚪</div>
                <?php if (isset($_SESSION['plinko_result'])): ?>
                <div style="font-size: 1.5rem; color: var(--primary);">Multiplier: <?php echo $_SESSION['plinko_result']['multiplier']; ?>x | Win: <?php echo $_SESSION['plinko_result']['win']; ?> coins</div>
                <?php else: ?>
                <div style="font-size: 1.5rem; color: var(--primary);">Ready to drop!</div>
                <?php endif; ?>
            </div>

            <div class="controls">
                <a href="?page=plinko" class="btn">Reset</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 Social Casino. All rights reserved. | 100% Free-to-Play | Virtual Coins Only | No Real Money</p>
        <p style="font-size: 0.9rem; margin-top: 1rem;">This is an entertainment platform. Must be 18+ to play. Virtual coins have no real-world value.</p>
    </footer>
</body>
</html>

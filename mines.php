<?php
require_once '../includes/config.php';
$page_title = "Mines Game";
include '../includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/toast.css">
<script src="../assets/js/toast.js"></script>

<style>
    /* Game Container - Single Screen Layout */
    .game-container {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        overflow: hidden;
    }

    .mines-game-wrapper {
        max-width: 1200px;
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
        align-items: start;
    }

    /* Game Area */
    .game-area {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .game-title {
        font-size: 2rem;
        color: #ffd700;
        text-align: center;
        margin-bottom: 20px;
        font-weight: bold;
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
    }

    /* Mines Grid */
    .mines-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin: 20px auto;
        max-width: 500px;
    }

    .mine-tile {
        aspect-ratio: 1;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .mine-tile:hover:not(.revealed):not(.disabled) {
        background: rgba(255, 215, 0, 0.2);
        border-color: #ffd700;
        transform: scale(1.05);
    }

    .mine-tile.revealed {
        cursor: default;
        animation: tileReveal 0.3s ease;
    }

    .mine-tile.revealed.safe {
        background: linear-gradient(135deg, #00ff00, #00cc00);
        border-color: #00ff00;
        box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
    }

    .mine-tile.revealed.mine {
        background: linear-gradient(135deg, #ff4444, #cc0000);
        border-color: #ff4444;
        box-shadow: 0 0 20px rgba(255, 68, 68, 0.5);
        animation: explode 0.5s ease;
    }

    .mine-tile.disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    @keyframes tileReveal {
        0% { transform: rotateY(0deg); }
        50% { transform: rotateY(90deg); }
        100% { transform: rotateY(0deg); }
    }

    @keyframes explode {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(1.2); }
        50% { transform: scale(0.9); }
        75% { transform: scale(1.1); }
    }

    /* Stats Display */
    .game-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin: 20px 0;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        padding: 15px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    .stat-label {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .stat-value {
        color: #ffd700;
        font-size: 1.5rem;
        font-weight: bold;
    }

    /* Control Panel */
    .control-panel {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        position: sticky;
        top: 100px;
    }

    .control-title {
        font-size: 1.3rem;
        color: #ffd700;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .control-group {
        margin-bottom: 20px;
    }

    .control-label {
        display: block;
        color: #fff;
        font-size: 0.95rem;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .control-input, .control-select {
        width: 100%;
        padding: 10px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #fff;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .control-input:focus, .control-select:focus {
        outline: none;
        border-color: #ffd700;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
    }

    .control-select option {
        background: #1a0a2e;
        color: #fff;
    }

    .bet-presets {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-top: 8px;
    }

    .preset-btn {
        padding: 8px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        color: #fff;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .preset-btn:hover {
        background: rgba(255, 215, 0, 0.2);
        border-color: #ffd700;
    }

    .action-button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 10px;
    }

    .start-button {
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        color: #1a0a2e;
        box-shadow: 0 5px 20px rgba(255, 215, 0, 0.4);
    }

    .start-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(255, 215, 0, 0.6);
    }

    .cashout-button {
        background: linear-gradient(135deg, #00ff00, #00cc00);
        color: #1a0a2e;
        box-shadow: 0 5px 20px rgba(0, 255, 0, 0.4);
    }

    .cashout-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 255, 0, 0.6);
    }

    .action-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .info-box {
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    .info-title {
        color: #ffd700;
        font-size: 1rem;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .info-text {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.85rem;
        line-height: 1.5;
    }

    .info-text li {
        margin-bottom: 5px;
    }

    /* Responsive Design */
    @media (max-width: 968px) {
        .mines-game-wrapper {
            grid-template-columns: 1fr;
        }

        .control-panel {
            position: relative;
            top: 0;
        }

        .game-area {
            padding: 20px;
        }

        .game-title {
            font-size: 1.5rem;
        }

        .mines-grid {
            gap: 8px;
        }
    }

    @media (max-width: 576px) {
        .mines-grid {
            gap: 6px;
        }

        .mine-tile {
            font-size: 1.5rem;
        }

        .game-stats {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }
</style>

<div class="game-container">
    <div class="mines-game-wrapper">
        <!-- Game Area -->
        <div class="game-area">
            <h1 class="game-title">💎 MINES GAME 💎</h1>

            <!-- Game Stats -->
            <div class="game-stats">
                <div class="stat-card">
                    <div class="stat-label">Current Multiplier</div>
                    <div class="stat-value" id="multiplierDisplay">1.00x</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Tiles Revealed</div>
                    <div class="stat-value" id="tilesRevealed">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Potential Win</div>
                    <div class="stat-value" id="potentialWin">0</div>
                </div>
            </div>

            <!-- Mines Grid -->
            <div class="mines-grid" id="minesGrid"></div>
        </div>

        <!-- Control Panel -->
        <div class="control-panel">
            <h2 class="control-title">🎮 Game Controls</h2>

            <!-- Bet Amount -->
            <div class="control-group">
                <label class="control-label">Bet Amount:</label>
                <input type="number" id="betAmount" class="control-input" min="1" max="500" value="10" placeholder="Enter bet">
                <div class="bet-presets">
                    <button class="preset-btn" onclick="setBet(10)">10</button>
                    <button class="preset-btn" onclick="setBet(50)">50</button>
                    <button class="preset-btn" onclick="setBet(100)">100</button>
                    <button class="preset-btn" onclick="setBet(250)">250</button>
                    <button class="preset-btn" onclick="setBet(500)">500</button>
                    <button class="preset-btn" onclick="setBet(getCredits())">MAX</button>
                </div>
            </div>

            <!-- Mines Count -->
            <div class="control-group">
                <label class="control-label">Number of Mines:</label>
                <select id="minesCount" class="control-select">
                    <option value="3">3 Mines (Easy)</option>
                    <option value="5" selected>5 Mines (Medium)</option>
                    <option value="8">8 Mines (Hard)</option>
                    <option value="10">10 Mines (Expert)</option>
                </select>
            </div>

            <!-- Credits Display -->
            <div class="control-group">
                <label class="control-label">Your Credits:</label>
                <div class="stat-value" id="creditsDisplay" style="text-align: center; padding: 10px; background: rgba(255,255,255,0.05); border-radius: 8px;">1000</div>
            </div>

            <!-- Action Buttons -->
            <button class="action-button start-button" id="startButton" onclick="startGame()">
                🚀 START GAME
            </button>
            <button class="action-button cashout-button" id="cashoutButton" onclick="cashOut()" disabled>
                💰 CASH OUT
            </button>

            <!-- Game Info -->
            <div class="info-box">
                <div class="info-title">📋 How to Play:</div>
                <ul class="info-text">
                    <li>💰 Set bet & mine count</li>
                    <li>🚀 Click START GAME</li>
                    <li>💎 Click tiles to reveal gems</li>
                    <li>💣 Avoid the mines!</li>
                    <li>💵 Cash out anytime to win</li>
                    <li>📈 More tiles = higher multiplier</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Sound Effects System
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    const audioCtx = new AudioContext();

    function playSound(type) {
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);

        switch(type) {
            case 'click':
                oscillator.frequency.value = 800;
                gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.1);
                break;
            case 'reveal':
                oscillator.frequency.value = 600;
                gainNode.gain.setValueAtTime(0.12, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.2);
                break;
            case 'gem':
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
                oscillator.frequency.setValueAtTime(1046, audioCtx.currentTime + 0.05);
                gainNode.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.2);
                break;
            case 'explosion':
                oscillator.type = 'sawtooth';
                oscillator.frequency.setValueAtTime(200, audioCtx.currentTime);
                oscillator.frequency.exponentialRampToValueAtTime(50, audioCtx.currentTime + 0.3);
                gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.5);
                break;
            case 'cashout':
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(523, audioCtx.currentTime);
                oscillator.frequency.setValueAtTime(659, audioCtx.currentTime + 0.1);
                oscillator.frequency.setValueAtTime(784, audioCtx.currentTime + 0.2);
                gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.4);
                break;
        }
    }

    // Game State
    let credits = parseInt(localStorage.getItem('credits')) || 1000;
    let gameActive = false;
    let minePositions = [];
    let revealedTiles = 0;
    let currentBet = 0;
    let currentMultiplier = 1.0;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateCreditsDisplay();
        createGrid();
    });

    function createGrid() {
        const grid = document.getElementById('minesGrid');
        grid.innerHTML = '';
        
        for (let i = 0; i < 25; i++) {
            const tile = document.createElement('div');
            tile.className = 'mine-tile disabled';
            tile.dataset.index = i;
            tile.innerHTML = '?';
            tile.addEventListener('click', () => revealTile(i));
            grid.appendChild(tile);
        }
    }

    function setBet(amount) {
        if (gameActive) return;
        playSound('click');
        const maxBet = Math.min(amount, credits, 500);
        document.getElementById('betAmount').value = maxBet;
    }

    function getCredits() {
        return credits;
    }

    function updateCreditsDisplay() {
        document.getElementById('creditsDisplay').textContent = credits;
        localStorage.setItem('credits', credits);
    }

    function startGame() {
        playSound('click');
        const betAmount = parseInt(document.getElementById('betAmount').value) || 0;
        const minesCount = parseInt(document.getElementById('minesCount').value);
        
        // Validation
        if (betAmount < 1 || betAmount > 500) {
            toast.warning('⚠️ Invalid Bet', 'Bet must be between 1-500 credits!');
            return;
        }
        
        if (betAmount > credits) {
            toast.error('❌ Insufficient Credits', "You don't have enough credits for this bet!");
            return;
        }
        
        // Deduct bet
        credits -= betAmount;
        currentBet = betAmount;
        updateCreditsDisplay();
        
        // Initialize game
        gameActive = true;
        revealedTiles = 0;
        currentMultiplier = 1.0;
        
        // Generate mine positions
        minePositions = [];
        while (minePositions.length < minesCount) {
            const pos = Math.floor(Math.random() * 25);
            if (!minePositions.includes(pos)) {
                minePositions.push(pos);
            }
        }
        
        // Reset grid
        createGrid();
        const tiles = document.querySelectorAll('.mine-tile');
        tiles.forEach(tile => tile.classList.remove('disabled'));
        
        // Update UI
        document.getElementById('startButton').disabled = true;
        document.getElementById('cashoutButton').disabled = false;
        document.getElementById('betAmount').disabled = true;
        document.getElementById('minesCount').disabled = true;
        
        updateStats();
    }

    function revealTile(index) {
        if (!gameActive) return;
        
        const tile = document.querySelector(`[data-index="${index}"]`);
        if (tile.classList.contains('revealed')) return;
        
        tile.classList.add('revealed');
        
        if (minePositions.includes(index)) {
            // Hit a mine - game over
            playSound('explosion');
            tile.classList.add('mine');
            tile.innerHTML = '💣';
            gameOver(false);
        } else {
            // Safe tile
            playSound('gem');
            tile.classList.add('safe');
            tile.innerHTML = '💎';
            revealedTiles++;
            
            // Calculate multiplier
            const minesCount = minePositions.length;
            const safeTiles = 25 - minesCount;
            const multiplierIncrease = 1 + (minesCount / safeTiles) * 0.5;
            currentMultiplier *= multiplierIncrease;
            
            updateStats();
            
            // Check if won (all safe tiles revealed)
            if (revealedTiles >= safeTiles) {
                gameOver(true);
            }
        }
    }

    function cashOut() {
        if (!gameActive) return;
        playSound('cashout');
        gameOver(true);
    }

    function gameOver(won) {
        gameActive = false;
        
        // Reveal all mines
        const tiles = document.querySelectorAll('.mine-tile');
        tiles.forEach((tile, index) => {
            tile.classList.add('disabled');
            if (minePositions.includes(index) && !tile.classList.contains('revealed')) {
                tile.classList.add('revealed', 'mine');
                tile.innerHTML = '💣';
            }
        });
        
        // Calculate winnings
        if (won) {
            const winAmount = Math.floor(currentBet * currentMultiplier);
            credits += winAmount;
            updateCreditsDisplay();
            
            setTimeout(() => {
                toast.success('🎉 YOU WIN!', `You successfully cashed out!`, { 'Multiplier': `${currentMultiplier.toFixed(2)}x`, 'Winnings': `${winAmount} credits`, 'Balance': credits });
            }, 500);
        } else {
            setTimeout(() => {
                toast.error('💣 BOOM!', 'You hit a mine!', { 'Lost': `${currentBet} credits`, 'Balance': credits });
            }, 500);
        }
        
        // Reset UI
        document.getElementById('startButton').disabled = false;
        document.getElementById('cashoutButton').disabled = true;
        document.getElementById('betAmount').disabled = false;
        document.getElementById('minesCount').disabled = false;
        
        // Reset stats
        setTimeout(() => {
            document.getElementById('multiplierDisplay').textContent = '1.00x';
            document.getElementById('tilesRevealed').textContent = '0';
            document.getElementById('potentialWin').textContent = '0';
        }, 2000);
    }

    function updateStats() {
        document.getElementById('multiplierDisplay').textContent = currentMultiplier.toFixed(2) + 'x';
        document.getElementById('tilesRevealed').textContent = revealedTiles;
        document.getElementById('potentialWin').textContent = Math.floor(currentBet * currentMultiplier);
    }
</script>

<?php include '../includes/footer.php'; ?>

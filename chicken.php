<?php
require_once '../includes/config.php';
$page_title = "Chicken Game";
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

    .chicken-game-wrapper {
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

    /* Game Stats */
    .game-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        padding: 12px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    .stat-label {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.85rem;
        margin-bottom: 5px;
    }

    .stat-value {
        color: #ffd700;
        font-size: 1.3rem;
        font-weight: bold;
    }

    /* Chicken Grid */
    .chicken-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin: 20px 0;
        max-width: 550px;
        margin-left: auto;
        margin-right: auto;
    }

    .chicken-box {
        aspect-ratio: 1;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .chicken-box:hover:not(.revealed):not(.disabled) {
        background: rgba(255, 215, 0, 0.2);
        border-color: #ffd700;
        transform: scale(1.05);
    }

    .chicken-box.revealed {
        cursor: default;
        animation: boxReveal 0.4s ease;
    }

    .chicken-box.egg {
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        border-color: #ffd700;
        box-shadow: 0 0 25px rgba(255, 215, 0, 0.6);
    }

    .chicken-box.bone {
        background: linear-gradient(135deg, #ff4444, #cc0000);
        border-color: #ff4444;
        box-shadow: 0 0 25px rgba(255, 68, 68, 0.6);
        animation: boneShake 0.5s ease;
    }

    .chicken-box.disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    @keyframes boxReveal {
        0% { transform: rotateY(0deg) scale(1); }
        50% { transform: rotateY(180deg) scale(1.1); }
        100% { transform: rotateY(360deg) scale(1); }
    }

    @keyframes boneShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px) rotate(-5deg); }
        75% { transform: translateX(10px) rotate(5deg); }
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

    /* Responsive Design */
    @media (max-width: 968px) {
        .chicken-game-wrapper {
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

        .chicken-grid {
            gap: 8px;
        }
    }

    @media (max-width: 576px) {
        .chicken-grid {
            gap: 6px;
        }

        .chicken-box {
            font-size: 2rem;
        }

        .game-stats {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }
</style>

<div class="game-container">
    <div class="chicken-game-wrapper">
        <!-- Game Area -->
        <div class="game-area">
            <h1 class="game-title">🐔 CHICKEN GAME 🐔</h1>

            <!-- Game Stats -->
            <div class="game-stats">
                <div class="stat-card">
                    <div class="stat-label">Current Multiplier</div>
                    <div class="stat-value" id="multiplierDisplay">1.00x</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Eggs Found</div>
                    <div class="stat-value" id="eggsFound">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Potential Win</div>
                    <div class="stat-value" id="potentialWin">0</div>
                </div>
            </div>

            <!-- Chicken Grid -->
            <div class="chicken-grid" id="chickenGrid"></div>
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

            <!-- Difficulty Level -->
            <div class="control-group">
                <label class="control-label">Difficulty:</label>
                <select id="difficulty" class="control-select">
                    <option value="easy">Easy (3 bones)</option>
                    <option value="medium" selected>Medium (5 bones)</option>
                    <option value="hard">Hard (8 bones)</option>
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
                <div class="info-text">
                    🐔 Help the chicken find golden eggs!<br>
                    💰 Set your bet & difficulty<br>
                    🚀 Click START GAME<br>
                    🥚 Click boxes to find eggs<br>
                    🦴 Avoid the bones!<br>
                    💵 Cash out anytime to win<br>
                    📈 More eggs = higher multiplier
                </div>
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
            case 'egg':
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
                oscillator.frequency.setValueAtTime(1046, audioCtx.currentTime + 0.05);
                gainNode.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.2);
                break;
            case 'bone':
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
    let bonePositions = [];
    let eggsCollected = 0;
    let currentBet = 0;
    let currentMultiplier = 1.0;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateCreditsDisplay();
        createGrid();
    });

    function createGrid() {
        const grid = document.getElementById('chickenGrid');
        grid.innerHTML = '';
        
        for (let i = 0; i < 25; i++) {
            const box = document.createElement('div');
            box.className = 'chicken-box disabled';
            box.dataset.index = i;
            box.innerHTML = '?';
            box.addEventListener('click', () => revealBox(i));
            grid.appendChild(box);
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
        const difficulty = document.getElementById('difficulty').value;
        
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
        eggsCollected = 0;
        currentMultiplier = 1.0;
        
        // Generate bone positions based on difficulty
        const boneCount = difficulty === 'easy' ? 3 : difficulty === 'medium' ? 5 : 8;
        bonePositions = [];
        while (bonePositions.length < boneCount) {
            const pos = Math.floor(Math.random() * 25);
            if (!bonePositions.includes(pos)) {
                bonePositions.push(pos);
            }
        }
        
        // Reset grid
        createGrid();
        const boxes = document.querySelectorAll('.chicken-box');
        boxes.forEach(box => box.classList.remove('disabled'));
        
        // Update UI
        document.getElementById('startButton').disabled = true;
        document.getElementById('cashoutButton').disabled = false;
        document.getElementById('betAmount').disabled = true;
        document.getElementById('difficulty').disabled = true;
        
        updateStats();
    }

    function revealBox(index) {
        if (!gameActive) return;
        
        const box = document.querySelector(`[data-index="${index}"]`);
        if (box.classList.contains('revealed')) return;
        
        box.classList.add('revealed');
        
        if (bonePositions.includes(index)) {
            // Hit a bone - game over
            playSound('bone');
            box.classList.add('bone');
            box.innerHTML = '🦴';
            gameOver(false);
        } else {
            // Found an egg
            playSound('egg');
            box.classList.add('egg');
            box.innerHTML = '🥚';
            eggsCollected++;
            
            // Calculate multiplier (increases with each egg)
            const difficulty = document.getElementById('difficulty').value;
            const multiplierIncrease = difficulty === 'easy' ? 0.3 : difficulty === 'medium' ? 0.4 : 0.6;
            currentMultiplier += multiplierIncrease;
            
            updateStats();
            
            // Check if won (all safe boxes revealed)
            const totalBoxes = 25;
            const safeBoxes = totalBoxes - bonePositions.length;
            if (eggsCollected >= safeBoxes) {
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
        
        // Reveal all bones
        const boxes = document.querySelectorAll('.chicken-box');
        boxes.forEach((box, index) => {
            box.classList.add('disabled');
            if (bonePositions.includes(index) && !box.classList.contains('revealed')) {
                box.classList.add('revealed', 'bone');
                box.innerHTML = '🦴';
            }
        });
        
        // Calculate winnings
        if (won && eggsCollected > 0) {
            const winAmount = Math.floor(currentBet * currentMultiplier);
            credits += winAmount;
            updateCreditsDisplay();
            
            setTimeout(() => {
                toast.success('🎉 YOU WIN!', `You found ${eggsCollected} golden eggs!`, { 'Multiplier': `${currentMultiplier.toFixed(2)}x`, 'Winnings': `${winAmount} credits`, 'Balance': credits });
            }, 500);
        } else {
            setTimeout(() => {
                toast.error('🦴 GAME OVER!', `You hit a bone! Found ${eggsCollected} eggs`, { 'Lost': `${currentBet} credits`, 'Balance': credits });
            }, 500);
        }
        
        // Reset UI
        document.getElementById('startButton').disabled = false;
        document.getElementById('cashoutButton').disabled = true;
        document.getElementById('betAmount').disabled = false;
        document.getElementById('difficulty').disabled = false;
        
        // Reset stats
        setTimeout(() => {
            document.getElementById('multiplierDisplay').textContent = '1.00x';
            document.getElementById('eggsFound').textContent = '0';
            document.getElementById('potentialWin').textContent = '0';
        }, 2000);
    }

    function updateStats() {
        document.getElementById('multiplierDisplay').textContent = currentMultiplier.toFixed(2) + 'x';
        document.getElementById('eggsFound').textContent = eggsCollected;
        document.getElementById('potentialWin').textContent = Math.floor(currentBet * currentMultiplier);
    }
</script>

<?php include '../includes/footer.php'; ?>

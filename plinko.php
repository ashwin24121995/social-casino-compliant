<?php
require_once '../includes/config.php';
$page_title = "Plinko Game";
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

    .plinko-game-wrapper {
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

    /* Plinko Board */
    .plinko-board {
        position: relative;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 15px;
        overflow: hidden;
        margin: 0 auto;
        box-shadow: inset 0 0 30px rgba(0, 0, 0, 0.5);
    }

    #plinkoCanvas {
        display: block;
        width: 100%;
        height: auto;
    }

    /* Multiplier Display */
    .multipliers {
        display: flex;
        justify-content: space-around;
        margin-top: 15px;
        gap: 5px;
    }

    .multiplier {
        flex: 1;
        padding: 10px 5px;
        text-align: center;
        border-radius: 8px;
        font-weight: bold;
        font-size: 0.9rem;
        border: 2px solid;
        transition: all 0.3s ease;
    }

    .multiplier.low {
        background: rgba(255, 68, 68, 0.2);
        border-color: #ff4444;
        color: #ff4444;
    }

    .multiplier.medium {
        background: rgba(255, 165, 0, 0.2);
        border-color: #ffa500;
        color: #ffa500;
    }

    .multiplier.high {
        background: rgba(255, 215, 0, 0.2);
        border-color: #ffd700;
        color: #ffd700;
    }

    .multiplier.jackpot {
        background: rgba(0, 255, 0, 0.2);
        border-color: #00ff00;
        color: #00ff00;
        animation: jackpotPulse 1s ease-in-out infinite;
    }

    .multiplier.active {
        transform: scale(1.15);
        box-shadow: 0 0 20px currentColor;
    }

    @keyframes jackpotPulse {
        0%, 100% { box-shadow: 0 0 10px currentColor; }
        50% { box-shadow: 0 0 25px currentColor; }
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

    .drop-button {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        border: none;
        border-radius: 10px;
        color: #1a0a2e;
        font-size: 1.2rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(255, 215, 0, 0.4);
        margin-top: 10px;
    }

    .drop-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(255, 215, 0, 0.6);
    }

    .drop-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 20px;
    }

    .stat-box {
        background: rgba(255, 255, 255, 0.05);
        padding: 12px;
        border-radius: 8px;
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
        font-size: 1.2rem;
        font-weight: bold;
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
        .plinko-game-wrapper {
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
    }

    @media (max-width: 576px) {
        .multipliers {
            gap: 3px;
        }

        .multiplier {
            padding: 8px 3px;
            font-size: 0.75rem;
        }
    }
</style>

<div class="game-container">
    <div class="plinko-game-wrapper">
        <!-- Game Area -->
        <div class="game-area">
            <h1 class="game-title">🎯 PLINKO GAME 🎯</h1>

            <!-- Plinko Board -->
            <div class="plinko-board">
                <canvas id="plinkoCanvas" width="600" height="500"></canvas>
            </div>

            <!-- Multipliers -->
            <div class="multipliers" id="multipliers"></div>
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

            <!-- Risk Level -->
            <div class="control-group">
                <label class="control-label">Risk Level:</label>
                <select id="riskLevel" class="control-select">
                    <option value="low">Low Risk</option>
                    <option value="medium" selected>Medium Risk</option>
                    <option value="high">High Risk</option>
                </select>
            </div>

            <!-- Drop Button -->
            <button class="drop-button" id="dropButton" onclick="dropBall()">
                🎯 DROP BALL 🎯
            </button>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-label">Your Credits</div>
                    <div class="stat-value" id="creditsDisplay">1000</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Last Win</div>
                    <div class="stat-value" id="lastWin">0</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Total Drops</div>
                    <div class="stat-value" id="totalDrops">0</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Total Won</div>
                    <div class="stat-value" id="totalWon">0</div>
                </div>
            </div>

            <!-- Game Info -->
            <div class="info-box">
                <div class="info-title">📋 How to Play:</div>
                <div class="info-text">
                    🎯 Set your bet amount<br>
                    📊 Choose risk level<br>
                    🎪 Click DROP BALL<br>
                    ⚡ Watch ball bounce down<br>
                    💰 Win based on multiplier<br>
                    🎰 Higher risk = bigger wins!
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
            case 'drop':
                oscillator.frequency.value = 400;
                gainNode.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.2);
                break;
            case 'bounce':
                oscillator.frequency.value = 1000;
                gainNode.gain.setValueAtTime(0.05, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.05);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.05);
                break;
            case 'win':
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(523, audioCtx.currentTime);
                oscillator.frequency.setValueAtTime(659, audioCtx.currentTime + 0.1);
                oscillator.frequency.setValueAtTime(784, audioCtx.currentTime + 0.2);
                gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.4);
                break;
            case 'lose':
                oscillator.frequency.value = 150;
                gainNode.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.5);
                break;
        }
    }

    // Game State
    let credits = parseInt(localStorage.getItem('credits')) || 1000;
    let isDropping = false;
    let totalDrops = 0;
    let totalWon = 0;

    // Canvas setup
    const canvas = document.getElementById('plinkoCanvas');
    const ctx = canvas.getContext('2d');

    // Multiplier configurations
    const multiplierSets = {
        low: [1.5, 1.3, 1.1, 1.0, 0.9, 1.0, 1.1, 1.3, 1.5],
        medium: [3.0, 2.0, 1.5, 1.0, 0.5, 1.0, 1.5, 2.0, 3.0],
        high: [10.0, 5.0, 2.0, 1.0, 0.3, 1.0, 2.0, 5.0, 10.0]
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateCreditsDisplay();
        drawBoard();
        updateMultipliers();
        
        document.getElementById('riskLevel').addEventListener('change', function() {
            playSound('click');
            updateMultipliers();
        });
    });

    function setBet(amount) {
        if (isDropping) return;
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

    function updateMultipliers() {
        const riskLevel = document.getElementById('riskLevel').value;
        const multipliers = multiplierSets[riskLevel];
        const container = document.getElementById('multipliers');
        
        container.innerHTML = '';
        multipliers.forEach((mult, index) => {
            const div = document.createElement('div');
            div.className = 'multiplier';
            div.dataset.index = index;
            div.textContent = mult + 'x';
            
            // Color coding
            if (mult >= 5) div.classList.add('jackpot');
            else if (mult >= 2) div.classList.add('high');
            else if (mult >= 1) div.classList.add('medium');
            else div.classList.add('low');
            
            container.appendChild(div);
        });
    }

    function drawBoard() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Draw pegs
        const rows = 10;
        const cols = 9;
        const pegRadius = 4;
        const startX = 100;
        const startY = 50;
        const spacingX = 50;
        const spacingY = 40;
        
        ctx.fillStyle = '#ffd700';
        for (let row = 0; row < rows; row++) {
            const pegsInRow = row + 1;
            const offsetX = startX + (cols - pegsInRow) * spacingX / 2;
            
            for (let col = 0; col < pegsInRow; col++) {
                const x = offsetX + col * spacingX;
                const y = startY + row * spacingY;
                
                ctx.beginPath();
                ctx.arc(x, y, pegRadius, 0, Math.PI * 2);
                ctx.fill();
                
                // Glow effect
                ctx.shadowBlur = 10;
                ctx.shadowColor = '#ffd700';
                ctx.fill();
                ctx.shadowBlur = 0;
            }
        }
    }

    function dropBall() {
        if (isDropping) return;
        
        const betAmount = parseInt(document.getElementById('betAmount').value) || 0;
        
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
        updateCreditsDisplay();
        
        // Start animation
        playSound('drop');
        isDropping = true;
        document.getElementById('dropButton').disabled = true;
        
        animateBall(betAmount);
    }

    function animateBall(betAmount) {
        const riskLevel = document.getElementById('riskLevel').value;
        const multipliers = multiplierSets[riskLevel];
        
        // Ball properties
        let x = canvas.width / 2;
        let y = 20;
        const ballRadius = 8;
        let velocityY = 0;
        const gravity = 0.5;
        const bounceDecay = 0.7;
        
        // Determine final slot (random walk)
        let slot = 4; // Start at middle
        const rows = 10;
        
        for (let i = 0; i < rows; i++) {
            if (Math.random() < 0.5) {
                slot = Math.max(0, slot - 1);
            } else {
                slot = Math.min(8, slot + 1);
            }
        }
        
        const targetX = 100 + slot * 55;
        
        function animate() {
            drawBoard();
            
            // Draw ball
            ctx.fillStyle = '#ff4444';
            ctx.beginPath();
            ctx.arc(x, y, ballRadius, 0, Math.PI * 2);
            ctx.fill();
            
            // Glow effect
            ctx.shadowBlur = 15;
            ctx.shadowColor = '#ff4444';
            ctx.fill();
            ctx.shadowBlur = 0;
            
            // Physics
            velocityY += gravity;
            y += velocityY;
            
            // Move towards target slot
            const dx = (targetX - x) * 0.02;
            x += dx;
            
            // Check if reached bottom
            if (y >= canvas.height - 30) {
                finishDrop(slot, betAmount, multipliers);
                return;
            }
            
            requestAnimationFrame(animate);
        }
        
        animate();
    }

    function finishDrop(slot, betAmount, multipliers) {
        const multiplier = multipliers[slot];
        const winAmount = Math.floor(betAmount * multiplier);
        
        // Highlight winning slot
        const multiplierElements = document.querySelectorAll('.multiplier');
        multiplierElements[slot].classList.add('active');
        
        setTimeout(() => {
            multiplierElements[slot].classList.remove('active');
        }, 2000);
        
        // Update credits
        credits += winAmount;
        totalDrops++;
        totalWon += winAmount;
        
        updateCreditsDisplay();
        document.getElementById('lastWin').textContent = winAmount;
        document.getElementById('totalDrops').textContent = totalDrops;
        document.getElementById('totalWon').textContent = totalWon;
        
        // Show result
        if (multiplier >= 1) {
            playSound('win');
            setTimeout(() => {
                toast.success('🎉 YOU WIN!', `Ball landed on ${multiplier}x multiplier!`, { 'Multiplier': `${multiplier}x`, 'Winnings': `${winAmount} credits`, 'Balance': credits });
            }, 300);
        } else {
            playSound('lose');
            setTimeout(() => {
                toast.error('😢 Better Luck Next Time!', `Ball landed on ${multiplier}x multiplier`, { 'Multiplier': `${multiplier}x`, 'Winnings': `${winAmount} credits`, 'Balance': credits });
            }, 300);
        }
        
        // Re-enable button
        isDropping = false;
        document.getElementById('dropButton').disabled = false;
        
        // Redraw board
        drawBoard();
    }
</script>

<?php include '../includes/footer.php'; ?>

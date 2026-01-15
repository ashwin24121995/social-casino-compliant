<?php
require_once '../includes/config.php';
$page_title = "Dice Game";
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

    .dice-game-wrapper {
        max-width: 1200px;
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 30px;
        align-items: start;
    }

    /* Game Area */
    .game-area {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .game-title {
        font-size: 2rem;
        color: #ffd700;
        text-align: center;
        margin-bottom: 30px;
        font-weight: bold;
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
    }

    /* Dice Display */
    .dice-display {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin: 40px 0;
        min-height: 150px;
        align-items: center;
    }

    .dice {
        width: 120px;
        height: 120px;
        background: linear-gradient(145deg, #ffffff, #e0e0e0);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        animation: diceIdle 2s ease-in-out infinite;
        position: relative;
        color: #1a0a2e;
        font-weight: bold;
    }

    @keyframes diceIdle {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    @keyframes diceRoll {
        0% { transform: rotate(0deg) scale(1); }
        25% { transform: rotate(90deg) scale(1.1); }
        50% { transform: rotate(180deg) scale(1); }
        75% { transform: rotate(270deg) scale(1.1); }
        100% { transform: rotate(360deg) scale(1); }
    }

    .dice.rolling {
        animation: diceRoll 0.5s ease-in-out infinite;
    }

    /* Prediction Section */
    .prediction-section {
        text-align: center;
        margin: 30px 0;
    }

    .prediction-label {
        color: #fff;
        font-size: 1.2rem;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .prediction-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .prediction-btn {
        padding: 12px 30px;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .prediction-btn:hover {
        background: rgba(255, 215, 0, 0.2);
        border-color: #ffd700;
        transform: translateY(-2px);
    }

    .prediction-btn.active {
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        border-color: #ffd700;
        color: #1a0a2e;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
    }

    /* Result Display */
    .result-display {
        text-align: center;
        margin: 20px 0;
        min-height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .result-message {
        font-size: 1.5rem;
        font-weight: bold;
        padding: 15px 30px;
        border-radius: 10px;
        animation: resultPop 0.5s ease;
    }

    .result-message.win {
        color: #00ff00;
        background: rgba(0, 255, 0, 0.1);
        border: 2px solid #00ff00;
        text-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
    }

    .result-message.lose {
        color: #ff4444;
        background: rgba(255, 68, 68, 0.1);
        border: 2px solid #ff4444;
        text-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
    }

    @keyframes resultPop {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Control Panel */
    .control-panel {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        position: sticky;
        top: 100px;
    }

    .control-title {
        font-size: 1.5rem;
        color: #ffd700;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .control-group {
        margin-bottom: 25px;
    }

    .control-label {
        display: block;
        color: #fff;
        font-size: 1rem;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .control-input {
        width: 100%;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: #fff;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .control-input:focus {
        outline: none;
        border-color: #ffd700;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
    }

    .bet-presets {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 10px;
    }

    .preset-btn {
        padding: 8px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #fff;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .preset-btn:hover {
        background: rgba(255, 215, 0, 0.2);
        border-color: #ffd700;
    }

    .roll-button {
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
        margin-top: 20px;
    }

    .roll-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(255, 215, 0, 0.6);
    }

    .roll-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 20px;
    }

    .stat-box {
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
        font-size: 1.3rem;
        font-weight: bold;
    }

    /* Game Rules */
    .game-rules {
        margin-top: 25px;
        padding: 20px;
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 10px;
    }

    .rules-title {
        color: #ffd700;
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .rules-list {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .rules-list li {
        margin-bottom: 5px;
    }

    /* Responsive Design */
    @media (max-width: 968px) {
        .dice-game-wrapper {
            grid-template-columns: 1fr;
        }

        .control-panel {
            position: relative;
            top: 0;
        }

        .game-area {
            padding: 25px;
        }

        .dice {
            width: 100px;
            height: 100px;
            font-size: 3rem;
        }

        .game-title {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .dice-display {
            gap: 15px;
        }

        .dice {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }

        .prediction-buttons {
            gap: 10px;
        }

        .prediction-btn {
            padding: 10px 20px;
            font-size: 0.9rem;
        }
    }
</style>

<div class="game-container">
    <div class="dice-game-wrapper">
        <!-- Game Area -->
        <div class="game-area">
            <h1 class="game-title">🎲 DICE GAME 🎲</h1>

            <!-- Dice Display -->
            <div class="dice-display">
                <div class="dice" id="dice1">?</div>
                <div class="dice" id="dice2">?</div>
            </div>

            <!-- Prediction Section -->
            <div class="prediction-section">
                <div class="prediction-label">Predict the Total:</div>
                <div class="prediction-buttons">
                    <button class="prediction-btn" data-prediction="under7">Under 7</button>
                    <button class="prediction-btn" data-prediction="7">Exactly 7</button>
                    <button class="prediction-btn" data-prediction="over7">Over 7</button>
                </div>
            </div>

            <!-- Result Display -->
            <div class="result-display" id="resultDisplay"></div>

            <!-- Game Rules -->
            <div class="game-rules">
                <div class="rules-title">📋 How to Play:</div>
                <ul class="rules-list">
                    <li>🎯 Choose your prediction: Under 7, Exactly 7, or Over 7</li>
                    <li>💰 Set your bet amount (1-500 credits)</li>
                    <li>🎲 Click "Roll Dice" to play</li>
                    <li>🏆 Under/Over 7 pays 2x | Exactly 7 pays 5x</li>
                    <li>✨ Two dice will roll and show the result</li>
                </ul>
            </div>
        </div>

        <!-- Control Panel -->
        <div class="control-panel">
            <h2 class="control-title">🎮 Game Controls</h2>

            <!-- Bet Amount -->
            <div class="control-group">
                <label class="control-label">Bet Amount:</label>
                <input type="number" id="betAmount" class="control-input" min="1" max="500" value="10" placeholder="Enter bet amount">
                <div class="bet-presets">
                    <button class="preset-btn" onclick="setBet(10)">10</button>
                    <button class="preset-btn" onclick="setBet(50)">50</button>
                    <button class="preset-btn" onclick="setBet(100)">100</button>
                    <button class="preset-btn" onclick="setBet(250)">250</button>
                    <button class="preset-btn" onclick="setBet(500)">500</button>
                    <button class="preset-btn" onclick="setBet(getCredits())">MAX</button>
                </div>
            </div>

            <!-- Roll Button -->
            <button class="roll-button" id="rollButton" onclick="rollDice()">
                🎲 ROLL DICE 🎲
            </button>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-label">Your Credits</div>
                    <div class="stat-value" id="creditsDisplay">1000</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Potential Win</div>
                    <div class="stat-value" id="potentialWin">0</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Total Rolls</div>
                    <div class="stat-value" id="totalRolls">0</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Wins</div>
                    <div class="stat-value" id="totalWins">0</div>
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
            case 'roll':
                oscillator.frequency.value = 200;
                gainNode.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.3);
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
    let selectedPrediction = null;
    let totalRolls = 0;
    let totalWins = 0;
    let isRolling = false;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateCreditsDisplay();
        
        // Prediction button handlers
        document.querySelectorAll('.prediction-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (isRolling) return;
                
                playSound('click');
                document.querySelectorAll('.prediction-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                selectedPrediction = this.dataset.prediction;
                updatePotentialWin();
            });
        });

        // Bet amount change handler
        document.getElementById('betAmount').addEventListener('input', function() {
            let value = parseInt(this.value) || 0;
            if (value > credits) {
                this.value = credits;
            }
            if (value > 500) {
                this.value = 500;
            }
            if (value < 1) {
                this.value = 1;
            }
            updatePotentialWin();
        });
    });

    function setBet(amount) {
        playSound('click');
        const maxBet = Math.min(amount, credits, 500);
        document.getElementById('betAmount').value = maxBet;
        updatePotentialWin();
    }

    function getCredits() {
        return credits;
    }

    function updateCreditsDisplay() {
        document.getElementById('creditsDisplay').textContent = credits;
        localStorage.setItem('credits', credits);
    }

    function updatePotentialWin() {
        const betAmount = parseInt(document.getElementById('betAmount').value) || 0;
        let multiplier = 0;
        
        if (selectedPrediction === '7') {
            multiplier = 5;
        } else if (selectedPrediction === 'under7' || selectedPrediction === 'over7') {
            multiplier = 2;
        }
        
        document.getElementById('potentialWin').textContent = betAmount * multiplier;
    }

    function rollDice() {
        if (isRolling) return;
        
        const betAmount = parseInt(document.getElementById('betAmount').value) || 0;
        
        // Validation
        if (!selectedPrediction) {
            toast.warning('⚠️ Selection Required', 'Please select a prediction first!');
            return;
        }
        
        if (betAmount < 1 || betAmount > 500) {
            toast.warning('⚠️ Invalid Bet', 'Bet must be between 1-500 credits!');
            return;
        }
        
        if (betAmount > credits) {
            toast.error('❌ Insufficient Credits', "You don't have enough credits for this bet!");
            return;
        }
        
        // Start rolling
        playSound('roll');
        isRolling = true;
        document.getElementById('rollButton').disabled = true;
        document.getElementById('resultDisplay').innerHTML = '';
        
        const dice1 = document.getElementById('dice1');
        const dice2 = document.getElementById('dice2');
        
        dice1.classList.add('rolling');
        dice2.classList.add('rolling');
        
        // Animate rolling
        let rollCount = 0;
        const rollInterval = setInterval(() => {
            dice1.textContent = Math.floor(Math.random() * 6) + 1;
            dice2.textContent = Math.floor(Math.random() * 6) + 1;
            rollCount++;
            
            if (rollCount >= 10) {
                clearInterval(rollInterval);
                finishRoll();
            }
        }, 100);
    }

    function finishRoll() {
        // Generate final result
        const die1 = Math.floor(Math.random() * 6) + 1;
        const die2 = Math.floor(Math.random() * 6) + 1;
        const total = die1 + die2;
        
        document.getElementById('dice1').textContent = die1;
        document.getElementById('dice2').textContent = die2;
        
        document.getElementById('dice1').classList.remove('rolling');
        document.getElementById('dice2').classList.remove('rolling');
        
        // Check win/lose
        const betAmount = parseInt(document.getElementById('betAmount').value);
        let won = false;
        let winAmount = 0;
        
        if (selectedPrediction === 'under7' && total < 7) {
            won = true;
            winAmount = betAmount * 2;
        } else if (selectedPrediction === '7' && total === 7) {
            won = true;
            winAmount = betAmount * 5;
        } else if (selectedPrediction === 'over7' && total > 7) {
            won = true;
            winAmount = betAmount * 2;
        }
        
        // Update credits
        totalRolls++;
        document.getElementById('totalRolls').textContent = totalRolls;
        
        if (won) {
            playSound('win');
            credits += winAmount - betAmount;
            totalWins++;
            document.getElementById('totalWins').textContent = totalWins;
            toast.success('🎉 YOU WIN!', `Dice Total: ${total}`, { 'Winnings': `+${winAmount} credits`, 'New Balance': credits });
        } else {
            playSound('lose');
            credits -= betAmount;
            toast.error('😢 YOU LOST!', `Dice Total: ${total}`, { 'Lost': `${betAmount} credits`, 'Balance': credits });
        }
        
        updateCreditsDisplay();
        
        // Re-enable button
        setTimeout(() => {
            isRolling = false;
            document.getElementById('rollButton').disabled = false;
        }, 1000);
    }
</script>

<?php include '../includes/footer.php'; ?>

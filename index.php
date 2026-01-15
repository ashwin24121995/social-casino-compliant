<?php
// Social Casino - Complete Platform
// Google Ads Compliant
// Version 1.0

session_start();

// Initialize user session if not exists
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest_' . uniqid();
    $_SESSION['balance'] = 1000; // Starting virtual coins
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$page_title = "Social Casino - 100% Free Gaming Platform";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="100% Free Social Casino Platform. Play games with virtual coins. No real money. Entertainment only.">
    <meta name="keywords" content="free games, social casino, virtual coins, entertainment">
    <meta name="author" content="Social Casino">
    <meta property="og:title" content="Social Casino - 100% Free Gaming">
    <meta property="og:description" content="Play amazing games completely free. Virtual coins only. No real money involved.">
    <meta property="og:type" content="website">
    
    <title><?php echo $page_title; ?></title>
    
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
            --primary: #1a1a3e;
            --secondary: #2d2d5f;
            --accent: #D4AF37;
            --success: #00cc00;
            --danger: #ff4444;
            --background: #0f0f1e;
            --text: #ffffff;
            --text-secondary: #cccccc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--background) 0%, var(--primary) 100%);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header & Navigation */
        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 1rem 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo:hover {
            color: #ffed4e;
        }

        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav a {
            color: var(--text);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
            font-weight: 500;
        }

        nav a:hover {
            color: var(--accent);
        }

        nav a.active {
            color: var(--accent);
            border-bottom: 2px solid var(--accent);
            padding-bottom: 5px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            border-radius: 8px;
        }

        .balance {
            font-weight: 600;
            color: var(--accent);
        }

        /* Main Content */
        main {
            flex: 1;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            padding: 2rem;
        }

        .container {
            width: 100%;
        }

        /* Page Sections */
        .page-section {
            display: none;
        }

        .page-section.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Home Page */
        .hero {
            text-align: center;
            margin-bottom: 3rem;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--accent);
            text-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
        }

        .hero p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        .disclaimer {
            background: rgba(255, 215, 0, 0.1);
            border-left: 4px solid var(--accent);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .disclaimer strong {
            color: var(--accent);
        }

        /* Games Grid */
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .game-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .game-card:hover {
            transform: translateY(-8px);
            border-color: var(--accent);
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.2);
        }

        .game-icon {
            font-size: 60px;
            margin-bottom: 1rem;
        }

        .game-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            margin-bottom: 0.5rem;
            color: var(--accent);
        }

        .game-card p {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--accent), #ffed4e);
            color: var(--primary);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 14px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 215, 0, 0.2);
            border-color: var(--accent);
        }

        /* Features Section */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .feature {
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 12px;
            border-left: 4px solid var(--accent);
        }

        .feature h4 {
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .feature p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Legal Pages */
        .legal-content {
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 900px;
            margin: 0 auto;
        }

        .legal-content h2 {
            color: var(--accent);
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .legal-content h3 {
            color: var(--text-secondary);
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
        }

        .legal-content p {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            line-height: 1.8;
        }

        .legal-content ul {
            margin-left: 2rem;
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        .legal-content li {
            margin-bottom: 0.5rem;
        }

        /* Footer */
        footer {
            background: var(--primary);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h4 {
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .footer-section a {
            display: block;
            color: var(--text-secondary);
            text-decoration: none;
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: var(--accent);
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .games-grid {
                grid-template-columns: 1fr;
            }

            main {
                padding: 1rem;
            }
        }

        /* Compliance Messages */
        .compliance-banner {
            background: rgba(255, 215, 0, 0.15);
            border: 1px solid var(--accent);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .compliance-banner strong {
            color: var(--accent);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <a href="?page=home" class="logo">🎰 SOCIAL CASINO</a>
            <nav>
                <a href="?page=home" class="<?php echo $page === 'home' ? 'active' : ''; ?>">Home</a>
                <a href="?page=games" class="<?php echo $page === 'games' ? 'active' : ''; ?>">Games</a>
                <a href="?page=how-it-works" class="<?php echo $page === 'how-it-works' ? 'active' : ''; ?>">How It Works</a>
                <a href="?page=about" class="<?php echo $page === 'about' ? 'active' : ''; ?>">About</a>
                <a href="?page=responsible-play" class="<?php echo $page === 'responsible-play' ? 'active' : ''; ?>">Responsible Play</a>
                <a href="?page=contact" class="<?php echo $page === 'contact' ? 'active' : ''; ?>">Contact</a>
            </nav>
            <div class="user-info">
                <span>Balance: <span class="balance" id="balance"><?php echo $_SESSION['balance']; ?></span> 💰</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Home Page -->
        <div class="page-section <?php echo $page === 'home' ? 'active' : ''; ?>">
            <div class="hero">
                <h1>Welcome to Social Casino</h1>
                <p>100% Free Gaming Platform • Virtual Coins Only • No Real Money</p>
            </div>

            <div class="compliance-banner">
                <strong>⚠️ IMPORTANT:</strong> This is a 100% free-to-play entertainment platform. Virtual coins have NO real money value. All games are for entertainment purposes only. Must be 18+ to play.
            </div>

            <div class="disclaimer">
                <strong>✓ 100% Free:</strong> Play all games completely free. No payments required ever.<br>
                <strong>✓ Virtual Coins Only:</strong> All coins are virtual and have zero monetary value.<br>
                <strong>✓ Entertainment Focus:</strong> Games are designed for fun and entertainment only.<br>
                <strong>✓ Fair & Safe:</strong> All games use certified random number generators.
            </div>

            <h2 style="color: var(--accent); margin-bottom: 2rem;">Featured Games</h2>

            <div class="games-grid">
                <div class="game-card" onclick="goToGame('chicken')">
                    <div class="game-icon">🐔</div>
                    <h3>Chicken Game</h3>
                    <p>Find eggs and avoid bones. Multiplier increases with each safe click!</p>
                    <button class="btn">Play Now</button>
                </div>

                <div class="game-card" onclick="goToGame('dice')">
                    <div class="game-icon">🎲</div>
                    <h3>Dice Game</h3>
                    <p>Predict the dice outcome. High/Low or specific numbers.</p>
                    <button class="btn">Play Now</button>
                </div>

                <div class="game-card" onclick="goToGame('mines')">
                    <div class="game-icon">💣</div>
                    <h3>Mines Game</h3>
                    <p>Click safe tiles and avoid mines. Build your multiplier!</p>
                    <button class="btn">Play Now</button>
                </div>

                <div class="game-card" onclick="goToGame('plinko')">
                    <div class="game-icon">⚪</div>
                    <h3>Plinko Game</h3>
                    <p>Drop balls and watch them fall. Land on high multipliers!</p>
                    <button class="btn">Play Now</button>
                </div>
            </div>

            <h2 style="color: var(--accent); margin: 3rem 0 2rem;">Why Choose Us?</h2>

            <div class="features">
                <div class="feature">
                    <h4>💯 100% Free</h4>
                    <p>No payments, no subscriptions, no hidden fees. Play forever for free.</p>
                </div>
                <div class="feature">
                    <h4>🎮 4 Amazing Games</h4>
                    <p>Variety of games with different mechanics and strategies.</p>
                </div>
                <div class="feature">
                    <h4>⚡ Fast & Smooth</h4>
                    <p>Instant gameplay with no lag or delays.</p>
                </div>
                <div class="feature">
                    <h4>🔒 Safe & Secure</h4>
                    <p>Your data is protected. No real money transactions.</p>
                </div>
                <div class="feature">
                    <h4>📱 Mobile Friendly</h4>
                    <p>Play on any device - desktop, tablet, or mobile.</p>
                </div>
                <div class="feature">
                    <h4>🎯 Fair Games</h4>
                    <p>All games use certified random number generators.</p>
                </div>
            </div>
        </div>

        <!-- Games Page -->
        <div class="page-section <?php echo $page === 'games' ? 'active' : ''; ?>">
            <h1 style="color: var(--accent); margin-bottom: 2rem;">All Games</h1>

            <div class="games-grid">
                <div class="game-card" onclick="goToGame('chicken')">
                    <div class="game-icon">🐔</div>
                    <h3>Chicken Game</h3>
                    <p>Find eggs and avoid bones. Multiplier increases with each safe click!</p>
                    <button class="btn">Play Now</button>
                </div>

                <div class="game-card" onclick="goToGame('dice')">
                    <div class="game-icon">🎲</div>
                    <h3>Dice Game</h3>
                    <p>Predict the dice outcome. High/Low or specific numbers.</p>
                    <button class="btn">Play Now</button>
                </div>

                <div class="game-card" onclick="goToGame('mines')">
                    <div class="game-icon">💣</div>
                    <h3>Mines Game</h3>
                    <p>Click safe tiles and avoid mines. Build your multiplier!</p>
                    <button class="btn">Play Now</button>
                </div>

                <div class="game-card" onclick="goToGame('plinko')">
                    <div class="game-icon">⚪</div>
                    <h3>Plinko Game</h3>
                    <p>Drop balls and watch them fall. Land on high multipliers!</p>
                    <button class="btn">Play Now</button>
                </div>
            </div>
        </div>

        <!-- How It Works -->
        <div class="page-section <?php echo $page === 'how-it-works' ? 'active' : ''; ?>">
            <h1 style="color: var(--accent); margin-bottom: 2rem;">How It Works</h1>

            <div class="legal-content">
                <h2>Getting Started</h2>
                <p>Social Casino is designed to be simple and fun. Here's how to get started:</p>

                <h3>Step 1: Start Playing</h3>
                <p>No registration required! You get 1,000 virtual coins to start playing immediately.</p>

                <h3>Step 2: Choose a Game</h3>
                <p>Select from our 4 amazing games: Chicken, Dice, Mines, or Plinko. Each game has unique mechanics and strategies.</p>

                <h3>Step 3: Place Your Bet</h3>
                <p>Decide how many coins you want to bet. You can adjust your bet amount before each game.</p>

                <h3>Step 4: Play & Win</h3>
                <p>Follow the game rules and try to win! Your balance updates in real-time.</p>

                <h3>Step 5: Keep Playing</h3>
                <p>Win or lose, you can keep playing forever. Your coins reset daily so you always have coins to play with.</p>

                <h2>Game Rules</h2>
                
                <h3>🐔 Chicken Game</h3>
                <p>Click on tiles to find eggs and avoid bones. Each egg found increases your multiplier. Hit a bone and the game ends. Cash out anytime to secure your winnings!</p>

                <h3>🎲 Dice Game</h3>
                <p>Predict whether the dice will roll High (4-6) or Low (1-3). You can also predict specific numbers. Correct predictions win you coins based on the odds.</p>

                <h3>💣 Mines Game</h3>
                <p>Click on safe tiles while avoiding hidden mines. Each safe click increases your multiplier. The more you click, the higher the risk but greater the rewards!</p>

                <h3>⚪ Plinko Game</h3>
                <p>Drop a ball from the top and watch it bounce down through pegs. It lands in a slot at the bottom with a multiplier. Higher multipliers are riskier!</p>

                <h2>Responsible Gaming</h2>
                <p>Remember: These are games of chance. Play for entertainment only. Set limits on how much time and virtual coins you spend. If you feel gaming is affecting your well-being, please seek help.</p>
            </div>
        </div>

        <!-- About Page -->
        <div class="page-section <?php echo $page === 'about' ? 'active' : ''; ?>">
            <h1 style="color: var(--accent); margin-bottom: 2rem;">About Social Casino</h1>

            <div class="legal-content">
                <h2>Our Mission</h2>
                <p>Social Casino is dedicated to providing a fun, safe, and completely free gaming platform where players can enjoy games of chance for entertainment purposes only.</p>

                <h2>What We Stand For</h2>
                <ul>
                    <li><strong>100% Free:</strong> No payments, no subscriptions, no hidden costs.</li>
                    <li><strong>Fair Play:</strong> All games use certified random number generators.</li>
                    <li><strong>Transparency:</strong> Clear rules and payouts for every game.</li>
                    <li><strong>Security:</strong> Your data is protected with industry-standard encryption.</li>
                    <li><strong>Responsibility:</strong> We promote responsible gaming practices.</li>
                </ul>

                <h2>Company Information</h2>
                <p><strong>Company Name:</strong> Social Casino Platform</p>
                <p><strong>Email:</strong> support@socialcasino.com</p>
                <p><strong>Address:</strong> Gaming Division, Entertainment Hub</p>
                <p><strong>Registration:</strong> Registered as an entertainment platform</p>

                <h2>Our Commitment</h2>
                <p>We are committed to providing the best gaming experience while maintaining the highest standards of safety, fairness, and responsible gaming. All our games are regularly audited to ensure fairness.</p>

                <h2>Contact Us</h2>
                <p>Have questions? We're here to help! Email us at support@socialcasino.com or use our contact form.</p>
            </div>
        </div>

        <!-- Responsible Play -->
        <div class="page-section <?php echo $page === 'responsible-play' ? 'active' : ''; ?>">
            <h1 style="color: var(--accent); margin-bottom: 2rem;">Responsible Play</h1>

            <div class="legal-content">
                <h2>Play Responsibly</h2>
                <p>While Social Casino is designed purely for entertainment, we encourage all players to game responsibly.</p>

                <h2>Important Reminders</h2>
                <ul>
                    <li>Games are for entertainment only - never play with real money</li>
                    <li>Virtual coins have no real-world value</li>
                    <li>Set time limits for your gaming sessions</li>
                    <li>Never chase losses</li>
                    <li>Take regular breaks</li>
                    <li>Only play with virtual coins provided</li>
                </ul>

                <h2>Signs of Problem Gaming</h2>
                <p>If you experience any of the following, please seek help:</p>
                <ul>
                    <li>Spending excessive time gaming</li>
                    <li>Thinking about games constantly</li>
                    <li>Neglecting work, school, or relationships due to gaming</li>
                    <li>Feeling anxious or irritable when not gaming</li>
                    <li>Lying about gaming habits</li>
                </ul>

                <h2>Resources & Support</h2>
                <p>If you need help, please reach out to:</p>
                <ul>
                    <li><strong>National Council on Problem Gambling:</strong> 1-800-522-4700</li>
                    <li><strong>Gamblers Anonymous:</strong> www.gamblersanonymous.org</li>
                    <li><strong>NCPG National Problem Gambling Helpline:</strong> 1-800-GAMBLER</li>
                </ul>

                <h2>Age Restriction</h2>
                <p><strong>IMPORTANT:</strong> This platform is restricted to users 18 years of age and older. If you are under 18, you must not use this platform.</p>

                <h2>Contact Support</h2>
                <p>If you have concerns about your gaming habits or need support, please contact us at support@socialcasino.com</p>
            </div>
        </div>

        <!-- Contact Page -->
        <div class="page-section <?php echo $page === 'contact' ? 'active' : ''; ?>">
            <h1 style="color: var(--accent); margin-bottom: 2rem;">Contact Us</h1>

            <div class="legal-content">
                <h2>Get in Touch</h2>
                <p>Have questions or feedback? We'd love to hear from you!</p>

                <h2>Contact Information</h2>
                <p><strong>Email:</strong> support@socialcasino.com</p>
                <p><strong>Response Time:</strong> We typically respond within 24 hours</p>

                <h2>Contact Form</h2>
                <form style="margin-top: 2rem;">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--accent);">Name:</label>
                        <input type="text" placeholder="Your name" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; color: white;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--accent);">Email:</label>
                        <input type="email" placeholder="Your email" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; color: white;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--accent);">Message:</label>
                        <textarea placeholder="Your message" rows="6" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; color: white; font-family: inherit;"></textarea>
                    </div>
                    <button type="submit" class="btn">Send Message</button>
                </form>

                <h2 style="margin-top: 3rem;">Frequently Asked Questions</h2>
                
                <h3>Is this platform really free?</h3>
                <p>Yes! Completely free. No payments required ever. You start with 1,000 virtual coins.</p>

                <h3>Can I win real money?</h3>
                <p>No. This is an entertainment platform only. Virtual coins have no real-world value.</p>

                <h3>What happens if I run out of coins?</h3>
                <p>Your coins reset daily so you always have coins to play with.</p>

                <h3>Is the platform safe?</h3>
                <p>Yes. We use industry-standard security and all games are fair and certified.</p>

                <h3>Do I need to create an account?</h3>
                <p>No! You can start playing immediately without registration.</p>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="page-section <?php echo $page === 'terms' ? 'active' : ''; ?>">
            <h1 style="color: var(--accent); margin-bottom: 2rem;">Terms & Conditions</h1>

            <div class="legal-content">
                <h2>Terms & Conditions</h2>
                <p><strong>Last Updated:</strong> January 2026</p>

                <h2>1. Acceptance of Terms</h2>
                <p>By accessing and using Social Casino, you accept and agree to be bound by the terms and provision of this agreement.</p>

                <h2>2. Virtual Coins</h2>
                <p>All coins are virtual and have NO real monetary value. They cannot be exchanged for real money or goods.</p>

                <h2>3. Age Restriction</h2>
                <p>You must be 18 years of age or older to use this platform. By using this platform, you confirm that you are 18+.</p>

                <h2>4. Entertainment Only</h2>
                <p>This platform is for entertainment purposes only. Games are games of chance and outcomes are random.</p>

                <h2>5. No Real Money</h2>
                <p>This platform does not involve real money, gambling, or financial transactions of any kind.</p>

                <h2>6. Fair Play</h2>
                <p>All games use certified random number generators to ensure fair and unbiased outcomes.</p>

                <h2>7. User Conduct</h2>
                <p>Users agree not to:</p>
                <ul>
                    <li>Cheat or use exploits</li>
                    <li>Harass other users</li>
                    <li>Use automated tools or bots</li>
                    <li>Violate any laws or regulations</li>
                </ul>

                <h2>8. Limitation of Liability</h2>
                <p>Social Casino is provided "as is" without warranties. We are not liable for any damages or losses.</p>

                <h2>9. Changes to Terms</h2>
                <p>We reserve the right to modify these terms at any time. Changes are effective immediately upon posting.</p>

                <h2>10. Contact</h2>
                <p>For questions about these terms, contact us at support@socialcasino.com</p>
            </div>
        </div>

        <!-- Privacy Policy -->
        <div class="page-section <?php echo $page === 'privacy' ? 'active' : ''; ?>">
            <h1 style="color: var(--accent); margin-bottom: 2rem;">Privacy Policy</h1>

            <div class="legal-content">
                <h2>Privacy Policy</h2>
                <p><strong>Last Updated:</strong> January 2026</p>

                <h2>1. Information We Collect</h2>
                <p>We collect minimal information:</p>
                <ul>
                    <li>Session ID for gameplay tracking</li>
                    <li>Game statistics and results</li>
                    <li>Contact information (if you contact us)</li>
                </ul>

                <h2>2. How We Use Information</h2>
                <p>We use information to:</p>
                <ul>
                    <li>Provide and improve our services</li>
                    <li>Track game statistics</li>
                    <li>Respond to support requests</li>
                    <li>Ensure fair play</li>
                </ul>

                <h2>3. Data Security</h2>
                <p>We implement industry-standard security measures to protect your information.</p>

                <h2>4. No Third-Party Sharing</h2>
                <p>We do not sell, trade, or share your information with third parties.</p>

                <h2>5. Cookies</h2>
                <p>We use cookies to maintain your session and track gameplay statistics.</p>

                <h2>6. Your Rights</h2>
                <p>You have the right to access, modify, or delete your information. Contact us for requests.</p>

                <h2>7. Changes to Privacy Policy</h2>
                <p>We may update this policy. Changes are effective immediately upon posting.</p>

                <h2>8. Contact</h2>
                <p>For privacy concerns, contact us at support@socialcasino.com</p>
            </div>
        </div>

        <!-- Disclaimer -->
        <div class="page-section <?php echo $page === 'disclaimer' ? 'active' : ''; ?>">
            <h1 style="color: var(--accent); margin-bottom: 2rem;">Disclaimer</h1>

            <div class="legal-content">
                <h2>Important Disclaimer</h2>

                <div style="background: rgba(255, 68, 68, 0.1); border: 2px solid #ff4444; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h3 style="color: #ff4444; margin-bottom: 1rem;">⚠️ CRITICAL NOTICE</h3>
                    <p><strong>This is a 100% FREE entertainment platform. Virtual coins have NO real money value. This is NOT gambling. No real money is involved.</strong></p>
                </div>

                <h2>1. No Real Money</h2>
                <p>Social Casino is a free entertainment platform. No real money is involved in any way. All coins are virtual and have zero monetary value.</p>

                <h2>2. Entertainment Only</h2>
                <p>Games are designed for entertainment purposes only. They are not gambling and do not involve real money or financial transactions.</p>

                <h2>3. Age Restriction</h2>
                <p>This platform is restricted to users 18 years of age and older. Users under 18 are strictly prohibited.</p>

                <h2>4. No Warranties</h2>
                <p>Social Casino is provided "as is" without any warranties. We do not guarantee specific outcomes or results.</p>

                <h2>5. Responsible Gaming</h2>
                <p>If you experience any signs of problem gaming, please seek help from professional resources.</p>

                <h2>6. Limitation of Liability</h2>
                <p>Social Casino is not liable for any damages, losses, or consequences arising from the use of this platform.</p>

                <h2>7. Compliance</h2>
                <p>This platform complies with all applicable laws and regulations regarding entertainment platforms.</p>

                <h2>8. Contact for Concerns</h2>
                <p>If you have any concerns, contact us at support@socialcasino.com</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="?page=home">Home</a>
                <a href="?page=games">Games</a>
                <a href="?page=how-it-works">How It Works</a>
                <a href="?page=about">About</a>
            </div>
            <div class="footer-section">
                <h4>Legal</h4>
                <a href="?page=terms">Terms & Conditions</a>
                <a href="?page=privacy">Privacy Policy</a>
                <a href="?page=disclaimer">Disclaimer</a>
                <a href="?page=responsible-play">Responsible Play</a>
            </div>
            <div class="footer-section">
                <h4>Support</h4>
                <a href="?page=contact">Contact Us</a>
                <a href="?page=how-it-works">FAQ</a>
                <a href="?page=responsible-play">Help</a>
            </div>
            <div class="footer-section">
                <h4>About</h4>
                <a href="?page=about">About Us</a>
                <a href="?page=responsible-play">Responsible Gaming</a>
                <p style="margin-top: 1rem; font-size: 12px; color: var(--text-secondary);">100% Free Platform • Virtual Coins Only • No Real Money</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p><strong>⚠️ IMPORTANT:</strong> This is a 100% free-to-play entertainment platform. Virtual coins have NO real money value. All games are for entertainment only. Must be 18+ to play.</p>
            <p style="margin-top: 1rem;">&copy; 2026 Social Casino. All rights reserved. | <a href="?page=terms" style="color: var(--accent);">Terms</a> | <a href="?page=privacy" style="color: var(--accent);">Privacy</a> | <a href="?page=disclaimer" style="color: var(--accent);">Disclaimer</a></p>
        </div>
    </footer>

    <script>
        function goToGame(game) {
            window.location.href = game + '.php';
        }

        // Update active nav link
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = new URLSearchParams(window.location.search).get('page') || 'home';
            document.querySelectorAll('nav a').forEach(link => {
                const href = link.getAttribute('href');
                if (href.includes('page=' + currentPage)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>

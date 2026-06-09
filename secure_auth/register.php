<?php
/* User Registration Module
Handles new account creation with:
Input validation (server-side)
Per-user random salt generation
SHA-256 password hashing (password + salt + pepper) */

require_once 'db_connect.php';
require_once 'config.php';

$message = "";
$msgType = ""; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);


    //Step 2: Validate input fields
    // Check that no fields were left blank
    if (empty($username) || empty($password) || empty($confirm)) {
        $message = "All fields are required.";
        $msgType = "error";

    // Enforce username length limits (3–50 characters)
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $message = "Username must be 3–50 characters.";
        $msgType = "error";

    // Enforce minimum password length (must be at least 12 characters)
    } elseif (strlen($password) < 12) {
        $message = "Password must be at least 12 characters.";
        $msgType = "error";

    // Ensure both password fields match before hashing
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
        $msgType = "error";

    } else {

        // Check for duplicate username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $message = "Username already taken. Please choose another.";
            $msgType = "error";

        } else {

            // Generate a unique random salt
            // random_bytes(32) generates 32 cryptographically secure random bytes
            // bin2hex() converts those bytes to a readable 64-character hex string
            // This salt is unique per user, so identical passwords hash differently
            $salt = bin2hex(random_bytes(32));

            // Hash the password
            // Combine: plain password + user's salt + global pepper (from config.php)
            // The pepper adds a second secret layer stored only in source code, not the DB
            $combined    = $password . $salt . PEPPER;
            $hashed_pass = hash(HASH_ALGO, $combined);  // SHA-256

            // Insert the new user into the database
            // Only store: username, hashed password, and salt
            // The plain password and pepper are NEVER stored in the database
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, hashed_pass, salt) VALUES (?, ?, ?)"
            );
            $stmt->execute([$username, $hashed_pass, $salt]);

            $message = "Account created! You may now sign in.";
            $msgType = "success";
        }
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — SecureAuth</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --sand:     #f5f0e8; 
            --cream:    #faf8f4;  
            --clay:     #c9a882;  
            --rust:     #b5541c; 
            --espresso: #2d1f14;  
            --bark:     #6b4c2e;  
            --mist:     #e8e2d9;  
            --error:    #c0392b;  
            --success:  #2e7d52; 
        }
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-color: var(--cream);
            background-image: radial-gradient(
                circle,
                rgba(107, 76, 46, 0.07) 1px,
                transparent 1px
            );
            background-size: 22px 22px;
        }
        .card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border: 1px solid var(--mist);
            border-radius: 16px;
            padding: 26px 40px 28px;
            box-shadow:
                0 4px 32px rgba(45, 31, 20, 0.09),
                0 1px 4px  rgba(45, 31, 20, 0.05);
            animation: fadeUp 0.45s ease both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0);    }
        }
        .card-accent {
            height: 3px;
            border-radius: 2px;
            background: linear-gradient(90deg, var(--rust), var(--clay));
            margin-bottom: 28px;
        }
        .form-eyebrow {
            font-family: 'DM Mono', monospace;
            font-size: 0.63rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--clay);
            margin-bottom: 8px;
        }
        .form-title {
            font-family: 'DM Serif Display', serif;
            font-size: 2rem;
            color: var(--espresso);
            line-height: 1.1;
            margin-bottom: 6px;
        }
        .form-sub {
            font-size: 0.83rem;
            color: var(--bark);
            opacity: 0.65;
            line-height: 1.5;
            margin-bottom: 28px;
        }
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            line-height: 1.5;
            margin-bottom: 22px;
            border-left: 3px solid;
            animation: slideIn 0.2s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-5px); }
            to   { opacity: 1; transform: translateX(0);    }
        }
        .alert.success {
            background: #f0f7f3;
            border-color: var(--success);
            color: var(--success);
        }
        .alert.error {
            background: #fdf0ef;
            border-color: var(--error);
            color: var(--error);
        }
        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--espresso);
            letter-spacing: 0.3px;
            margin-bottom: 7px;
        }
        .input-wrap {
            position: relative;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 42px 12px 14px; 
            background: var(--sand);
            border: 1.5px solid var(--mist);
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--espresso);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        input::placeholder {
            color: rgba(107, 76, 46, 0.32);
        }
        input:hover {
            border-color: var(--clay);
        }
        input:focus {
            border-color: var(--rust);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(181, 84, 28, 0.09);
        }

        .toggle-pw {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: rgba(107, 76, 46, 0.35);
            display: flex;
            align-items: center;
            line-height: 0;
            transition: color 0.2s;
        }

        .toggle-pw:hover {
            color: var(--rust);
        }
        .toggle-pw svg {
            width: 17px;
            height: 17px;
            display: block;
        }

         .strength-wrap {
            margin-top: 9px;
        }
        .strength-track {
            height: 3px;
            background: var(--mist);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 6px;
        }
        .strength-fill {
            height: 100%;
            border-radius: 4px;
            width: 0%;
            transition: width 0.35s ease, background 0.35s ease;
        }
        .strength-label {
            font-family: 'DM Mono', monospace;
            font-size: 0.63rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(107, 76, 46, 0.38);
        }
        .strength-label.s1 { color: #c0392b; }
        .strength-label.s2 { color: #d97706; }
        .strength-label.s3 { color: #2e7d52; }

        /* Criteria checklist */
        .criteria-list {
            list-style: none;
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .criteria-list li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            color: rgba(107, 76, 46, 0.45);
            transition: color 0.2s;
        }
        .criteria-list li .icon {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1.5px solid rgba(107, 76, 46, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .criteria-list li.pass {
            color: #2e7d52;
        }
        .criteria-list li.pass .icon {
            background: #2e7d52;
            border-color: #2e7d52;
            color: #fff;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: var(--espresso);
            color: var(--cream);
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.8px;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.22s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(45, 31, 20, 0.18);
        }

        .btn:hover {
            background: var(--bark);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(45, 31, 20, 0.22);
        }

        .btn:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 26px 0 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--mist);
        }

        .divider-text {
            font-size: 0.75rem;
            color: rgba(107, 76, 46, 0.38);
            white-space: nowrap;
        }

        .footer {
            text-align: center;
            margin-top: 16px;
            font-size: 0.83rem;
            color: var(--bark);
            opacity: 0.65;
        }

        .footer a {
            color: var(--rust);
            font-weight: 600;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

                .back-home {
            font-family: 'DM Mono', monospace;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--bark);
            text-decoration: none;
            margin-bottom: 15px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            opacity: 0.8;
            transition: opacity 0.2s, color 0.2s;
        }
        .back-home:hover {
            opacity: 1;
            color: var(--rust);
        }


    </style>
</head>
<body>
    <div class="card">

          <a href="index.php" class="back-home">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
    </svg>
    Back to Home
</a>
        <div class="card-accent"></div>

        <div class="form-eyebrow">New Account</div>
        <h1 class="form-title">Create Account</h1>
        <p class="form-sub">Fill in your details below to get started.</p>


        <?php if (!empty($message)): ?>
            <div class="alert <?= $msgType ?>">
                <!-- Icon: checkmark for success, X for error -->
                <span><?= $msgType === 'success' ? '✔' : '✖' ?></span>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="field">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Choose a username (3–50 characters)"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Create a strong password"
                        oninput="evalPassword(this.value)"
                        required
                    >
                    <button
                        type="button"
                        class="toggle-pw"
                        onclick="toggleVis('password')"
                        title="Show or hide password"
                    >
                        <svg
                            id="eye-password"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <div class="strength-wrap">
                    <div class="strength-track">
                        <div class="strength-fill" id="sfill"></div>
                    </div>
                    <span class="strength-label" id="slabel">Strength</span>
                    <ul class="criteria-list" id="criteria-list">
                        <li id="c-length"><span class="icon">✕</span> Minimum 12 characters</li>
                        <li id="c-upper"><span class="icon">✕</span> Uppercase letter (A-Z)</li>
                        <li id="c-lower"><span class="icon">✕</span> Lowercase letter (a-z)</li>
                        <li id="c-number"><span class="icon">✕</span> Number (0-9)</li>
                        <li id="c-symbol"><span class="icon">✕</span> Special character (!@#$...)</li>
                    </ul>
                </div>
            </div>

            <div class="field">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Re-enter your password"
                        required
                    >
                                        
                    <button
                        type="button"
                        class="toggle-pw"
                        onclick="toggleVis('confirm_password')"
                        title="Show or hide password"
                    >
                        <svg
                            id="eye-confirm_password"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn">Create Account</button>

        </form>

        <div class="divider">
            <span class="divider-text">Already have an account?</span>
        </div>
        <div class="footer">
            <a href="login.php">Sign in instead →</a>
        </div>

    </div>

    <script>

        var EYE_OPEN = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>'
                     + '<circle cx="12" cy="12" r="3"/>';

        var EYE_OFF  = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8'
                     + 'a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4'
                     + 'c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19'
                     + 'm-6.72-1.07a3 3 0 1 1-4.24-4.24"/>'
                     + '<line x1="1" y1="1" x2="23" y2="23"/>';


        var STRENGTH_COLORS  = ['#c0392b', '#d97706', '#2e7d52'];
        var STRENGTH_WIDTHS  = ['33%', '66%', '100%'];
        var STRENGTH_LABELS  = ['Weak', 'Medium', 'Strong'];
        var STRENGTH_CLASSES = ['s1', 's2', 's3'];


        /* -----------------------------------------------------------
         * evalPassword(val)
         * Called on every keystroke inside the password field.
         * Scores the password against 5 criteria and updates the
         * strength bar width, color, and label accordingly.
         * Criteria:
         *   1. Minimum 12 characters
         *   2. Uppercase letter (A-Z)
         *   3. Lowercase letter (a-z)
         *   4. Number (0-9)
         *   5. Special character / symbol
         * Score 1-2 = Weak, 3-4 = Medium, 5 = Strong
         * ----------------------------------------------------------- */
        function evalPassword(val) {

            var checks = [
                val.length >= 12,            // Minimum 12 characters
                /[A-Z]/.test(val),           // Uppercase letter
                /[a-z]/.test(val),           // Lowercase letter
                /[0-9]/.test(val),           // Digit
                /[^A-Za-z0-9]/.test(val)     // Special character / symbol
            ];
            var passed = checks.filter(Boolean).length;

            // Update each criteria item
            var ids = ['c-length', 'c-upper', 'c-lower', 'c-number', 'c-symbol'];
            ids.forEach(function(id, i) {
                var li = document.getElementById(id);
                var icon = li.querySelector('.icon');
                if (checks[i]) {
                    li.classList.add('pass');
                    icon.textContent = '✓';
                } else {
                    li.classList.remove('pass');
                    icon.textContent = '✕';
                }
            });

            // Map 5-point score to 3 levels: Weak / Medium / Strong
            var score = passed <= 2 ? 1 : passed <= 4 ? 2 : 3;

            var fill  = document.getElementById('sfill');
            var label = document.getElementById('slabel');

            if (!val) {
                fill.style.width    = '0';
                label.textContent   = 'Strength';
                label.className     = 'strength-label';
                return;
            }

            fill.style.width       = STRENGTH_WIDTHS[score - 1]  || '33%';
            fill.style.background  = STRENGTH_COLORS[score - 1]  || '#c0392b';
            label.textContent      = STRENGTH_LABELS[score - 1]  || 'Weak';
            label.className        = 'strength-label ' + (STRENGTH_CLASSES[score - 1] || 's1');
        }


        function toggleVis(id) {
            var input   = document.getElementById(id);
            var svgIcon = document.getElementById('eye-' + id);

            input.type = (input.type === 'password') ? 'text' : 'password';

            svgIcon.innerHTML = (input.type === 'password') ? EYE_OPEN : EYE_OFF;
        }

    </script>

</body>
</html>
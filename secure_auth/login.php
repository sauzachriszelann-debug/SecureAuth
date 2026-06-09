<?php
/*User Login Module:
 Retrieving the stored salt and hashed password for the
 given username via a prepared statement
 Re-hashing the submitted password using the same method:
 SHA-256( plain_password + stored_salt + PEPPER )
 Comparing hashes with hash_equals() to prevent
 timing-based side-channel attacks*/

require_once 'db_connect.php';
require_once 'config.php';

$message = "";
$msgType = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Basic validation — ensure neither field is blank
    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
        $msgType = "error";
    } else {
        // Look up the user in the database
        // Fetch the stored hashed password and the user's unique salt
        // Using a prepared statement prevents SQL injection
        $stmt = $pdo->prepare(
            "SELECT hashed_pass, salt FROM users WHERE username = ?"
        );
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Re-create the hash using the same method as registration
            // Combine: submitted password + stored salt + global pepper
            // This must exactly mirror how the password was hashed at registration
            $combined     = $password . $user['salt'] . PEPPER;
            $attempt_hash = hash(HASH_ALGO, $combined);  // SHA-256

            // Compare hashes securely
            // hash_equals() does a constant-time comparison to prevent
            // timing attacks that could reveal information about the hash
            if (hash_equals($user['hashed_pass'], $attempt_hash)) {
                $message = "Welcome back, " . htmlspecialchars($username) . ". Login successful!";
                $msgType = "success";
            } else {
                // Wrong password — use a generic message to prevent
                // attackers from knowing which field was incorrect
                $message = "Invalid username or password.";
                $msgType = "error";
            }
        } else {
            // Username not found — same generic message as wrong password
            // to prevent user enumeration attacks
            $message = "Invalid username or password.";
            $msgType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — SecureAuth</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;0,9..144,700;1,9..144,300&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --sand:     #f5f0e8;   
            --cream:    #faf8f4;   
            --clay:     #c9a882;   
            --rust:     #b5541c; 
            --espresso: #2d1f14;   
            --bark:     #6b4c2e;   
            --mist:     #e8e2d9;   
            --sage:     #7a9178;   
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
            flex-direction: column;
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
        
        .back-home {
            font-family: 'DM Mono', monospace;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--bark);
            text-decoration: none;
            margin-bottom: 20px;
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

        .card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border: 1px solid var(--mist);
            border-radius: 16px;
            padding: 40px 40px 36px;
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
            color: var(--bark); /* Better accessibility contrast */
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-title {
            font-family: 'Fraunces', serif; /* Matched typography */
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--espresso);
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 6px;
        }
        .form-sub {
            font-size: 0.88rem;
            color: var(--bark);
            opacity: 0.85; /* Better accessibility contrast */
            line-height: 1.5;
            margin-bottom: 24px;
        }
.security-strip {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 4px 6px;
    padding: 9px 13px;
    background: var(--sand);
    border: 1px solid var(--mist);
    border-radius: 7px;
    margin-bottom: 22px;
}
        .strip-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--sage);
            flex-shrink: 0;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

.strip-dot-sep {
    color: var(--bark);
    opacity: 0.5;
    font-size: 0.65rem;
}

.strip-text {
    font-family: 'DM Mono', monospace;
    font-size: 0.58rem;      /* slightly smaller to fit */
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--bark);
    opacity: 0.85;
    text-align: center;
    white-space: nowrap;     /* keep on one line BUT contained by overflow:hidden */
}

        .strip-text span {
            color: var(--rust);
            font-weight: 600;
        }
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
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
            font-size: 0.8rem;
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
            color: rgba(107, 76, 46, 0.45);
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
            color: rgba(107, 76, 46, 0.45);
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
            font-size: 0.78rem;
            color: var(--bark);
            opacity: 0.6;
            white-space: nowrap;
        }
        .card-footer {
            text-align: center;
            margin-top: 16px;
            font-size: 0.85rem;
            color: var(--bark);
        }

        .card-footer a {
            color: var(--rust);
            font-weight: 600;
            text-decoration: none;
        }

        .card-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>



    <div class="card">

        <a href="index.php" class="back-home">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back to Home
    </a>

        <div class="card-accent"></div>
        <div class="form-eyebrow">Existing Account</div>
        <h1 class="form-title">Sign In</h1>
        <p class="form-sub">Enter your credentials to access the system.</p>

<div class="security-strip">
    <div class="strip-dot"></div>
    <span class="strip-text">SHA-256</span>
    <span class="strip-dot-sep">·</span>
    <span class="strip-text"><span>Salt + Pepper</span></span>
    <span class="strip-dot-sep">·</span>
    <span class="strip-text">hash_equals()</span>
</div>

        <?php if (!empty($message)): ?>
            <div class="alert <?= $msgType ?>">
                <span><?= $msgType === 'success' ? '✔' : '✖' ?></span>
                <span><?= $message ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="field">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Your username"
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
                        placeholder="Your password"
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
            </div>

            <button type="submit" class="btn">Sign In</button>

        </form>
        
        <div class="divider">
            <span class="divider-text">Don't have an account?</span>
        </div>
        <div class="card-footer">
            <a href="register.php">Create one now →</a>
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

        function toggleVis(id) {
            var input   = document.getElementById(id);
            var svgIcon = document.getElementById('eye-' + id);
            input.type = (input.type === 'password') ? 'text' : 'password';
            svgIcon.innerHTML = (input.type === 'password') ? EYE_OPEN : EYE_OFF;
        }
    </script>

</body>
</html>
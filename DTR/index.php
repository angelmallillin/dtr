<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTR System | Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-pink: #db2777;
            --light-pink: #fdf2f8;
            --bg-canvas: #f9fafb;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .auth-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 25px rgba(219, 39, 119, 0.05);
            border: 1px solid #fce7f3;
        }
        .brand-icon {
            height: 60px; width: 60px;
            background: var(--light-pink);
            color: var(--primary-pink);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 24px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            border-color: var(--primary-pink);
            box-shadow: 0 0 0 4px rgba(219, 39, 119, 0.1);
        }
        .btn-pink {
            background: var(--primary-pink);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-pink:hover:not(:disabled) {
            background: #be185d;
            transform: translateY(-2px);
        }
        .btn-pink:disabled {
            background: #f472b6;
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="auth-card text-center">
    <div class="brand-icon mx-auto"><i class="bi bi-clock-history"></i></div>
    <h3 class="fw-bold mb-1">Sign In</h3>
    <p class="text-muted mb-4 small">Please enter your credentials.</p>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'locked'): ?>
        <div id="lockoutAlert" class="alert alert-warning border-0 py-2 small mb-4 shadow-sm text-start" style="background-color: #fffbeb; color: #92400e; border-radius: 12px;">
            <i class="bi bi-clock-fill me-2"></i> 
            Too many attempts. Wait for <span id="timer" class="fw-bold"><?php echo (int)$_GET['wait']; ?></span> seconds.
        </div>
        <script>
            let seconds = <?php echo (int)$_GET['wait']; ?>;
            const timerDisplay = document.getElementById('timer');
            const interval = setInterval(() => {
                seconds--;
                if(timerDisplay) timerDisplay.innerText = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    location.href = "index.php"; // Refresh para ma-enable ulit ang button
                }
            }, 1000);
        </script>
    <?php elseif(isset($_GET['error'])): ?>
        <div class="alert alert-danger border-0 py-2 small mb-4 shadow-sm text-start" style="background-color: #fff1f2; color: #be123c; border-radius: 12px;">
            <i class="bi bi-exclamation-circle-fill me-2"></i> Invalid username or password.
        </div>
    <?php endif; ?>

    <form action="login_process.php" method="POST" class="text-start">
        <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold text-uppercase">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" name="login_btn" class="btn btn-pink w-100" 
            <?php echo (isset($_GET['error']) && $_GET['error'] == 'locked') ? 'disabled' : ''; ?>>
            LOGIN
        </button>
    </form>
    
    <p class="small text-muted mt-4">No account? <a href="signup.php" class="text-decoration-none fw-bold" style="color: var(--primary-pink);">Sign Up</a></p>
</div>

</body>
</html>
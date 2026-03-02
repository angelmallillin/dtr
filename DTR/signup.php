<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTR System | Create Account</title>
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
            max-width: 480px;
            box-shadow: 0 10px 25px rgba(219, 39, 119, 0.05);
            border: 1px solid #fce7f3;
        }
        .form-label { font-weight: 700; font-size: 0.8rem; color: #475569; letter-spacing: 0.5px; }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
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
        }
        .btn-pink:hover {
            background: #be185d;
            color: white;
        }
        .btn-light {
            border-radius: 12px;
            padding: 12px;
            background: #f1f5f9;
            border: none;
            color: #475569;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="mb-4 text-center">
        <h3 class="fw-bold">Create Account</h3>
        <p class="text-muted small">Join us to start managing your records.</p>
    </div>

    <form id="signupForm" action="signup_process.php" method="POST">
        <div class="mb-3">
            <label class="form-label text-uppercase">Full Name</label>
            <input type="text" name="fullname" class="form-control" placeholder="Juan Dela Cruz" required>
        </div>

        <div class="mb-3">
            <label class="form-label text-uppercase">Username</label>
            <input type="text" name="username" class="form-control" placeholder="juan_dtr" required>
        </div>

        <div class="mb-3">
            <label class="form-label text-uppercase">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="form-label text-uppercase">Confirm Password</label>
            <input type="password" id="confirm_password" class="form-control" required>
        </div>

        <div id="validationAlert" class="alert alert-danger d-none py-2 small mb-3" style="border-radius: 12px;">
            <i class="bi bi-exclamation-circle me-2"></i> <span id="errorText"></span>
        </div>

        <button type="submit" name="signup_btn" class="btn btn-pink w-100 mb-3">REGISTER</button>
        <a href="index.php" class="btn btn-light w-100">Back to Login</a>
    </form>

    <script>
    document.getElementById('signupForm').onsubmit = function(e) {
        const pass = document.getElementById('password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        const alertDiv = document.getElementById('validationAlert');
        const errorText = document.getElementById('errorText');

        if (pass.length < 8) {
            e.preventDefault();
            errorText.innerText = "Password must be at least 8 characters.";
            alertDiv.classList.remove('d-none');
        } else if (pass !== confirmPass) {
            e.preventDefault();
            errorText.innerText = "Passwords do not match!";
            alertDiv.classList.remove('d-none');
        }
    };
    </script>
</div>

</body>
</html>
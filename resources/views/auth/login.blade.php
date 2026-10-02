<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Coffee POS System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1C1917 0%, #262320 50%, #3C2A21 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 440px;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #6F4E37, #D4A373, #E9EDC9);
        }

        .brand-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #6F4E37, #D4A373);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            margin: 0 auto 20px auto;
            box-shadow: 0 8px 20px rgba(111, 78, 55, 0.3);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #E7E5E4;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #6F4E37;
            box-shadow: 0 0 0 4px rgba(111, 78, 55, 0.15);
        }

        .btn-login {
            background: #6F4E37;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background: #543B29;
            box-shadow: 0 6px 16px rgba(111, 78, 55, 0.35);
        }

        .demo-box {
            background: #F5F5F4;
            border-radius: 14px;
            padding: 16px;
            margin-top: 24px;
            border: 1px dashed #D6D3D1;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center">
            <div class="brand-icon">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit';">Coffee POS System</h3>
            <p class="text-muted small mb-4">Sign in to start your cashier or admin session</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 rounded-3 mb-3 small">
                <i class="fa-solid fa-circle-xmark me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold text-dark fs-7">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="emailInput" class="form-control border-start-0 ps-0" placeholder="admin@coffee.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-dark fs-7">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="passwordInput" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted fs-7" for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In to POS
            </button>
        </form>

        <!-- Quick Demo Login Buttons -->
        <div class="demo-box">
            <div class="text-center fw-bold text-secondary mb-2 fs-7">⚡ Demo One-Click Login</div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-dark flex-fill rounded-3" onclick="fillDemo('admin@coffee.com')">
                    <i class="fa-solid fa-user-shield me-1"></i> Admin
                </button>
                <button class="btn btn-sm btn-outline-primary flex-fill rounded-3" onclick="fillDemo('cashier@coffee.com')">
                    <i class="fa-solid fa-cash-register me-1"></i> Cashier
                </button>
            </div>
        </div>
    </div>

    <script>
        function fillDemo(email) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = 'password';
        }
    </script>
</body>
</html>

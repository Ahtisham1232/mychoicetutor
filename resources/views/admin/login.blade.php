<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Online Tutor Admin Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,600,800');

        * {
            box-sizing: border-box;
        }

        body {
            background: #f4ecf7;
            /* Soft purple tint background to match your brand */
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: 0;
        }

        h1 {
            font-weight: 800;
            margin: 0 0 10px 0;
            color: #333;
        }

        p {
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 0 0 20px 0;
            color: #666;
        }

        .text-danger {
            font-size: 12px;
            color: #e74c3c;
            align-self: flex-start;
            margin-bottom: 5px;
            display: block;
        }

        a {
            color: #8e44ad;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
            transition: color 0.2s ease;
        }

        a:hover {
            color: #6a307d;
        }

        button {
            border-radius: 25px;
            border: none;
            background: linear-gradient(135deg, #6a307d, #8e44ad);
            color: #FFFFFF;
            font-size: 13px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform 80ms ease-in, box-shadow 0.2s ease;
            box-shadow: 0 4px 10px rgba(142, 68, 173, 0.3);
        }

        button:hover {
            box-shadow: 0 6px 15px rgba(142, 68, 173, 0.4);
        }

        button:active {
            transform: scale(0.95);
        }

        /* Two-Column Split Container */
        .container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.1), 0 10px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            width: 850px;
            max-width: 95%;
            min-height: 500px;
            overflow: hidden;
        }

        /* left column (image) -> Equivalent to col-md-6 */
        .image-container {
            flex: 1;
            background: linear-gradient(135deg, #6a307d, #8e44ad);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: #ffffff;
            text-align: center;
        }

        .image-container img {
            width: 90%;
            max-width: 320px;
            height: auto;
            margin-bottom: 20px;
        }

        .image-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .image-container p {
            color: #e2d2e8;
            font-size: 13px;
            margin: 0;
        }

        /* Right column (form) -> Equivalent to col-md-6 */
        .form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
            background-color: #FFFFFF;
        }

        form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        input {
            background-color: #f4f5f7;
            border: 2px solid transparent;
            border-radius: 8px;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #8e44ad;
            background-color: #fff;
        }

        /* Responsive handling for smaller screens */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                width: 400px;
            }

            .image-container {
                display: none;
                /* Hides image side on mobile for space optimization */
            }

            .form-container {
                padding: 40px 30px;
            }
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #8e44ad;
            font-size: 16px;
        }
    </style>
</head>

<body>

    <div class="container" id="container">

        <!-- Left Section: Interactive Image side (col-md-6) -->
        <div class="image-container">
            <!-- Dynamic Vector Image representing an LMS / Admin Dashboard -->
            <img src="{{ asset('images/admin/admin.jpg') }}" alt="Admin Portal Illustration">
            <h2>MyChoiceTutor</h2>
            <p>Management & Analytics Control Panel</p>
        </div>

        <!-- Right Section: Login Form side (col-md-6) -->
        <div class="form-container">
            <form action="{{ url('admin/login') }}" method="GET">
                @csrf
                <h1>Admin Login</h1>
                <p>Sign in to manage your system</p>

                <input type="email" id="username" name="username" placeholder="Email Address" required />
                <span class="text-danger">
                    @error('username')
                        {{ $message }}
                    @enderror
                </span>

                <div class="password-wrapper">
                    <input type="password" id="loginpassword" name="loginpassword" placeholder="Password" required />

                    <i class="fa fa-eye toggle-password" id="eyeIcon" onclick="togglePassword()"></i>
                </div>

                <span class="text-danger">
                    @error('loginpassword')
                        {{ $message }}
                    @enderror
                </span>

                <a href="#">Forgot your password?</a>
                <button type="submit">Log In</button>
            </form>
        </div>

    </div>

</body>

</html>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById("loginpassword");
        const eyeIcon = document.getElementById("eyeIcon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";

            // Show slash icon when password becomes visible
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");

        } else {
            passwordInput.type = "password";

            // Show eye icon when password becomes hidden
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }
</script>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Master Hub System</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICON -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            height:100vh;
            overflow:hidden;
            font-family:'Segoe UI', sans-serif;
            background:
                linear-gradient(
                    135deg,
                    #0f172a,
                    #1e3a8a,
                    #2563eb
                );

            display:flex;
            justify-content:center;
            align-items:center;
            position:relative;
        }

        /* BACKGROUND BLUR CIRCLE */
        .circle{
            position:absolute;
            border-radius:50%;
            filter:blur(80px);
            opacity:.5;
            animation:float 8s infinite ease-in-out;
        }

        .circle1{
            width:300px;
            height:300px;
            background:#60a5fa;
            top:-100px;
            left:-100px;
        }

        .circle2{
            width:250px;
            height:250px;
            background:#38bdf8;
            bottom:-80px;
            right:-80px;
            animation-delay:2s;
        }

        @keyframes float{
            0%{
                transform:translateY(0px);
            }

            50%{
                transform:translateY(25px);
            }

            100%{
                transform:translateY(0px);
            }
        }

        /* CARD */
        .login-card{
            width:430px;
            padding:45px;
            border-radius:28px;
            background:rgba(255,255,255,0.12);
            backdrop-filter:blur(18px);
            border:1px solid rgba(255,255,255,0.15);
            box-shadow:
                0 20px 50px rgba(0,0,0,0.25);

            position:relative;
            z-index:2;

            animation:fadeIn .8s ease;
        }

        @keyframes fadeIn{
            from{
                opacity:0;
                transform:translateY(30px);
            }

            to{
                opacity:1;
                transform:translateY(0px);
            }
        }

        /* LOGO */
        .logo{
            width:90px;
            height:90px;
            border-radius:24px;
            margin:auto;

            display:flex;
            justify-content:center;
            align-items:center;

            background:
                linear-gradient(
                    135deg,
                    #3b82f6,
                    #60a5fa
                );

            color:white;
            font-size:38px;

            box-shadow:
                0 10px 30px rgba(59,130,246,.5);

            margin-bottom:25px;
        }

        .title{
            text-align:center;
            color:white;
            font-size:30px;
            font-weight:700;
            margin-bottom:8px;
        }

        .subtitle{
            text-align:center;
            color:#dbeafe;
            margin-bottom:35px;
            font-size:14px;
        }

        /* INPUT */
        .input-group-modern{
            position:relative;
            margin-bottom:22px;
        }

        .input-group-modern i{
            position:absolute;
            top:18px;
            left:18px;
            color:#94a3b8;
            z-index:5;
        }

        .form-control{
            height:55px;
            border:none;
            border-radius:16px;
            padding-left:50px;
            background:rgba(255,255,255,0.95);
            transition:.3s;
        }

        .form-control:focus{
            box-shadow:
                0 0 0 4px rgba(96,165,250,.3);

            transform:translateY(-2px);
        }

        /* BUTTON */
        .btn-login{
            width:100%;
            height:55px;
            border:none;
            border-radius:16px;

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #3b82f6
                );

            color:white;
            font-size:16px;
            font-weight:600;

            transition:.3s;
        }

        .btn-login:hover{
            transform:translateY(-3px);

            box-shadow:
                0 15px 30px rgba(37,99,235,.4);
        }

        /* FOOTER */
        .footer-text{
            margin-top:25px;
            text-align:center;
            color:#dbeafe;
            font-size:13px;
        }

        @media(max-width:500px){

            .login-card{
                width:92%;
                padding:35px 25px;
            }

        }

    </style>

</head>

<body>

    <!-- EFFECT -->
    <div class="circle circle1"></div>
    <div class="circle circle2"></div>

    <!-- CARD -->
    <div class="login-card">

        <!-- LOGO -->
        <div class="logo">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <!-- TITLE -->
        <div class="title">
            Master Hub System
        </div>

        <div class="subtitle">
            Secure Login Access
        </div>

        <!-- FORM -->
        <form method="POST" action="/login">

            @csrf

            <!-- EMAIL -->
            <div class="input-group-modern">

                <i class="fa-solid fa-envelope"></i>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Username"
                    required>

            </div>

            <!-- PASSWORD -->
            <div class="mb-3 position-relative">

    <input
        type="password"
        name="password"
        id="password"
        class="form-control"
        placeholder="Password"
        required>

    <span
        onclick="togglePassword()"
        style="
            position:absolute;
            right:15px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            user-select:none;
        ">

        👁

    </span>

</div>
            <!-- BUTTON -->
            <button class="btn-login">

                <i class="fa-solid fa-right-to-bracket"></i>
                Login System

            </button>

        </form>

        <!-- FOOTER -->
        <div class="footer-text">
            © 2026 Master Hub System
        </div>

    </div>
<script>

function togglePassword() {

    const password =
        document.getElementById('password');

    if (password.type === 'password') {

        password.type = 'text';

    } else {

        password.type = 'password';

    }

}

</script>
</body>
</html>
```

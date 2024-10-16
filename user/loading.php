<?php
session_start();
require('../dbconn.php');

if ($_SESSION['type'] != 'User') {
    header("Location: login.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <style>
        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
        background-color: #fff;
        color: black;
    }

    .nav-btn {
        color: white;
        position: absolute;
        top: 20px;
        left: 20px;
        font-size: 24px;
        transition: color 0.3s ease;
    }

    .nav-btn:hover {
        color: #ffcc00;
    }

    .nav-img {
        font-size: 24px;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        min-height: 100vh;
    
        overflow: hidden;
    }

    .content {
        display: flex;
        width: 140vh;
        padding: 20px;
        border: 2px solid white;
        border-radius: 20px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        animation: fadeIn 2s ease-in-out forwards, bounce 2s ease-in-out forwards;
        opacity: 0;
        color: white;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: scale(0.8);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-20px);
        }
        60% {
            transform: translateY(-10px);
        }
    }

    p {
        font-size: 25px;
        margin: 11px;
        animation: none; 
    }

    .content:hover {
        border-color: #ffcc00;
        transition: border-color 0.3s ease;
    }

    .loadingIcon {
        width: 50px;
        height: 50px;
        margin-top: 15px ;
        margin-left: 50px;
        display: inline-flex;
        border: 4px solid rgb(23, 125, 179);
        border-bottom: 4px solid transparent;
        border-radius: 100%;
        animation: 2s infinite linear spin;
    }
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    .animate{
        font-size: 20px;
        font-family: 'Poppins';
        color: #333333;
        margin: 0;
        line-height: 1.5;
        display: flex;
        justify-content: center;
        align-items: center;
  
    }

    </style>
</head>
<body>
    <div class="container" >
        <a href="index.php" class="nav-btn">
            <p><</P>
        </a>
        
        <div class="content">  
            <p class="animate">Hi <?php echo htmlspecialchars($_SESSION['username']); ?>, you don't have access. Your have been restrict. Please go to administration for further process</p>  
            <div class="loading-container">
                <div class = "loadingIcon"></div>
            </div>
        </div>        
    </div>
</body>
</html>

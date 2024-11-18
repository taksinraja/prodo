<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prodo</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #121212;
            font-family: Arial, sans-serif;
            overflow: hidden; /* Prevent scrollbars during animation */
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            z-index: 2;
        }
        
        .dots {
            position: relative;
            width: 60px;
            height: 60px;
            margin-right: 20px;
        }
        
        .dot {
            position: absolute;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            opacity: 0;
            transform: scale(0.5);
        }
        
        .dot-1 {
            background-color: #d7be95;
            animation: moveDot1 1.2s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
        }
        
        .dot-2 {
            background-color: #313131;
            animation: moveDot2 1.2s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards 0.2s;
        }
        
        .dot-3 {
            background-color: #C8A2D7;
            animation: moveDot3 1.2s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards 0.4s;
        }
        
        /* Keyframes for Dot 1 */
        @keyframes moveDot1 {
            0% {
                top: -100px;
                left: 50%;
                transform: translateX(-50%) scale(0.5);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                top: 0;
                left: 50%;
                transform: translateX(-50%) scale(1);
                opacity: 1;
            }
        }
        
        /* Keyframes for Dot 2 */
        @keyframes moveDot2 {
            0% {
                bottom: -100px;
                left: -50px;
                transform: scale(0.5);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                bottom: 0;
                left: 0;
                transform: scale(1);
                opacity: 1;
            }
        }
        
        /* Keyframes for Dot 3 */
        @keyframes moveDot3 {
            0% {
                bottom: -100px;
                right: -50px;
                transform: scale(0.5);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                bottom: 0;
                right: 0;
                transform: scale(1);
                opacity: 1;
            }
        }
        
        /* Keyframes for Text Reveal */
        @keyframes revealText {
            0% {
                opacity: 0;
                transform: translateX(-200px); /* Start from far left */
            }
            80% {
                opacity: 1;
                transform: translateX(10px); /* Slight overshoot to the right */
            }
            100% {
                opacity: 1;
                transform: translateX(0); /* Settle back to final position */
            }
        }
        
        .text {
            color: #4285F4;
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 2px;
            opacity: 0;
            animation: revealText 1s cubic-bezier(0.33, 1.5, 0.68, 1) forwards;
            animation-delay: 1.6s; /* Delay text animation to start after dots finish */
        }

        .fade-out {
            animation: fadeOutText 1.3s ease forwards; /* Slower fade out for text */
        }

        /* Fade-out animation for text */
        @keyframes fadeOutText {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: scale(0.95); /* Slight shrinking effect */
            }
        }

        /* Apply fading out and expanding animation */
        .expand {
            animation: expandDots 2.5s cubic-bezier(0.25, 0.1, 0.25, 1) forwards;
        }

        /* Enhanced animation to expand dots to cover the entire screen with rotation and pulsing */
        @keyframes expandDots {
            0% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            } 
            100% {
                transform: scale(50) rotate(360deg);
                opacity: 0;
            }
        }

        /* Add a pulsing effect */
        .expand::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            border-radius: inherit;
            animation: pulse 2.5s cubic-bezier(0.25, 0.1, 0.25, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <div class="dots">
            <div class="dot dot-1"></div>
            <div class="dot dot-2"></div>
            <div class="dot dot-3"></div>
        </div>
        <div class="text">PRODO</div>
    </div>

    <script>
        // Delay the dot expansion and text fade-out
        setTimeout(() => {
            document.querySelector('.text').classList.add('fade-out'); // Fade out text

            setTimeout(() => {
                document.querySelectorAll('.dot').forEach(dot => {
                    dot.classList.add('expand'); // Expand and rotate all dots
                });

                // Redirect to another page after the animation ends
                setTimeout(() => {
                    window.location.href = 'signin_welcome.php';
                }, 600); // Wait for 2 seconds for dots to fully expand and rotate
            }, 400); // Wait for text to fully fade out before expanding dots
        }, 2500); // Start after all animations finish (dots + text reveal)
    </script>
</body>
</html>
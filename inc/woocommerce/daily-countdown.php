<?php

defined('ABSPATH') || exit;

//hiện thị giờ gian đếm lui
//
function daily_countdown_shortcode()
{
    // Enqueue the CSS
    wp_enqueue_style('countdown-style', plugins_url('countdown.css', __FILE__));

    // Output the HTML structure
    $output = '
    <div class="countdown-container">
        <div class="countdown-display">
            <span class="countdown-hours">00</span>:
            <span class="countdown-minutes">00</span>:
            <span class="countdown-seconds">00</span>
        </div>
    </div>';

    // Add the JavaScript
    $output .= '
    <script>
    function updateCountdown() {
        const now = new Date();
        const target = new Date();

        // Set target to 22:00 today
        target.setHours(22, 0, 0, 0);

        // If it\'s already past 22:00, set to 22:00 tomorrow
        if (now > target) {
            target.setDate(target.getDate() + 1);
        }

        // Calculate difference in seconds
        const diff = Math.floor((target - now) / 1000);

        // Calculate hours, minutes, seconds
        const hours = Math.floor(diff / 3600).toString().padStart(2, "0");
        const minutes = Math.floor((diff % 3600) / 60).toString().padStart(2, "0");
        const seconds = Math.floor(diff % 60).toString().padStart(2, "0");

        // Update the display
        document.querySelector(".countdown-hours").textContent = hours;
        document.querySelector(".countdown-minutes").textContent = minutes;
        document.querySelector(".countdown-seconds").textContent = seconds;
    }

    // Update immediately and then every second
    updateCountdown();
    setInterval(updateCountdown, 1000);
    </script>';

    return $output;
}

add_shortcode('daily_countdown', 'daily_countdown_shortcode');

// Add inline CSS (or you can put this in a separate CSS file)
function countdown_styles()
{
    echo '
    <style>
.countdown-container {
    margin-top: 15px;
}

    .countdown-title {
        font-size: 1.2rem;
        font-weight: 300;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.9);
    }

.countdown-display {
    font-size: 1.7rem;
    font-weight: 700;
    font-family: "Courier New", monospace;
    background: rgba(0, 0, 0, 0.2);
    padding: 10px 9px;
    border-radius: 8px;
    display: inline-block;
    position: relative;
    overflow: hidden;
    float: right;
}

    .countdown-display::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,0.1),
            transparent
        );
        animation: shine 3s infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .countdown-hours,
    .countdown-minutes,
    .countdown-seconds {
        display: inline-block;
        min-width: 50px;
        text-align: center;
        background: rgba(0,0,0,0.3);
        padding: 5px;
        border-radius: 5px;
        margin: 0 2px;
    }

    @media (max-width: 480px) {
        .countdown-display {
            font-size: 2rem;
            padding: 10px 15px;
        }

        .countdown-title {
            font-size: 1rem;
        }
    }
    </style>';
}

add_action('wp_head', 'countdown_styles');

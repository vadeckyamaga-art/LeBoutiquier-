<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/LeBoutiquier/vendor/chatAI/chat.css">
        <link rel="stylesheet" href="/fontawesome/css/all.min.css">
        <!-- PWA -->
        <link rel="manifest" href="/LeBoutiquier/vendor/manifest.json">
        <meta name="theme-color" content="#EA580C">
        <link rel="icon" href="/LeBoutiquier/Image/favoicon.ico" sizes="48x48">
        <link rel="apple-touch-icon" href="/LeBoutiquier/Image/logo.png">
    </head>
    <body>
        <div id="chat-bubble" onclick="toggleChat()"><img src="/LeBoutiquier/Image/logo.png" alt="Logo" style="width: 36px; height: 36px; object-fit: contain;"></div>
        <div id="chat-window">
            <div class="chat-header">
                <div class="chat-header-info">
                    <img src="/LeBoutiquier/Image/logo.png" alt="Logo" style="width: 28px; height: 28px; object-fit: contain; border-radius: 6px;">
                    <div>
                        <p>Assistant</p>
                        <p>En ligne</p>
                    </div>
                </div>
                <span class="chat-close" onclick="toggleChat()"><i class="fa fa-times"></i></span>
            </div>
            <div id="chat-messages">
                <div class="msg-bot">Bonjour ! Comment puis-je vous aider ?</div>
            </div>
            <div class="chat-input-area">
                <input id="chat-input" type="text" placeholder="Votre question...">
                <button onclick="sendMsg()"><i class="fa-solid fa-paper-plane"></i></button>
            </div>
        </div>
        <script src="/LeBoutiquier/vendor/chatAI/chat.js"></script>
    </body>
</html>
function toggleChat(){
    const w = document.getElementById('chat-window');
    w.style.display = (w.style.display === 'none' || w.style.display === '')? 'flex': 'none';
}

function addMsg(text, type) {
    const msgs = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.className = type === 'user' ? 'msg-user' : 'msg-bot';
    div.innerHTML = text;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
}

async function sendMsg() {
    const input = document.getElementById('chat-input');
    const text = input.value.trim();
    if (!text) return;
    input.value = '';

    addMsg(text, 'user');
    const typing = addMsg('...', 'bot');

    try{
        const res = await fetch('/LeBoutiquier/vendor/chatAI/chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        });
        const data = await res.json();
        typing.innerHTML = data.reply || "Désolé, une erreur s'est produite.";
    } 
    catch (e) {
        typing.textContent = "Erreur de connexion. Réessayez.";
    }

    document.getElementById('chat-messages').scrollTop = 9999;
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('chat-input').addEventListener('keydown', e => {
        if (e.key === 'Enter') sendMsg();
    });
});
<?php
$config = json_decode(file_get_contents('config.json'), true);
$memoire = file_exists('memoire.txt') ? file_get_contents('memoire.txt') : "";
$color = $config['bot_color'] ?? '#007bff';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; font-family: sans-serif; overflow: hidden; }
        #chat { display: flex; flex-direction: column; height: 100vh; background: white; }
        header { background: <?php echo $color; ?>; color: white; padding: 15px; font-weight: bold; }
        #msgs { flex: 1; overflow-y: auto; padding: 15px; background: #f4f7f6; display: flex; flex-direction: column; gap: 10px; }
        .msg { padding: 10px 14px; border-radius: 15px; font-size: 13px; max-width: 80%; }
        .u { background: <?php echo $color; ?>; color: white; align-self: flex-end; border-radius: 15px 15px 0 15px; }
        .a { background: white; color: #333; align-self: flex-start; border-radius: 15px 15px 15px 0; border: 1px solid #ddd; }
        footer { padding: 10px; display: flex; gap: 8px; border-top: 1px solid #eee; }
        input { flex: 1; border: 1px solid #ddd; border-radius: 20px; padding: 10px; outline: none; }
        button { background: <?php echo $color; ?>; border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: white; font-size: 20px; }
    </style>
</head>
<body>
    <div id="chat">
        <header><?php echo $config['bot_name'] ?? 'Assistant'; ?></header>
        <div id="msgs"><div class="msg a">Bonjour ! Comment puis-je vous aider ?</div></div>
        <footer>
            <input type="text" id="in" placeholder="Votre message...">
            <button id="send">➔</button>
        </footer>
    </div>

    <script>
        const CONFIG = <?php echo json_encode($config); ?>;
        const MEM = `<?php echo addslashes($memoire); ?>`;

        async function talk() {
            const q = $('#in').val(); 
            const KEY = CONFIG.api_key;
            const PROV = CONFIG.ai_provider || 'groq';

            if(!q || !KEY) return;
            $('#msgs').append(`<div class="msg u">${q}</div>`); $('#in').val('');
            $('#msgs').scrollTop($('#msgs')[0].scrollHeight);

            const context = "Réponds en français en utilisant ce texte : " + MEM.substring(0, 12000);
            let url, headers, body;

            if (PROV === 'google') {
                url = `https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=${KEY}`;
                headers = { "Content-Type": "application/json" };
                body = JSON.stringify({ contents: [{ parts: [{ text: context + "\n\nQuestion : " + q }] }] });
            } else {
                url = (PROV === 'groq') ? "https://api.groq.com/openai/v1/chat/completions" : 
                      (PROV === 'grok') ? "https://api.x.ai/v1/chat/completions" : 
                      "https://api.openai.com/v1/chat/completions";
                
                const model = (PROV === 'groq') ? "llama-3.3-70b-versatile" : 
                              (PROV === 'grok') ? "grok-2-latest" : "gpt-4o";

                headers = { "Authorization": "Bearer " + KEY, "Content-Type": "application/json" };
                body = JSON.stringify({
                    model: model,
                    messages: [{ role: "system", content: context }, { role: "user", content: q }]
                });
            }

            try {
                const res = await fetch(url, { method: "POST", headers: headers, body: body });
                const data = await res.json();
                let reply = (PROV === 'google') ? data.candidates[0].content.parts[0].text : data.choices[0].message.content;
                $('#msgs').append(`<div class="msg a">${reply}</div>`);
            } catch (err) {
                $('#msgs').append(`<div class="msg a">Erreur : Vérifiez votre clé ou vos crédits.</div>`);
            }
            $('#msgs').scrollTop($('#msgs')[0].scrollHeight);
        }
        $('#send').click(talk); $('#in').keypress(e => { if(e.which==13) talk(); });
    </script>
</body>
</html>
<?php
$configFile = 'config.json';
$memoireFile = 'memoire.txt';

// 1. Initialisation des valeurs par défaut
$config = [
    'ai_provider' => 'groq',
    'api_key' => '',
    'bot_name' => 'Assistant',
    'bot_color' => '#007bff',
    'bot_avatar' => '',
    'bubble_img' => ''
];

// Chargement des données existantes
if (file_exists($configFile)) {
    $saved = json_decode(file_get_contents($configFile), true);
    if (is_array($saved)) {
        $config = array_merge($config, $saved);
    }
}

// 2. Sauvegarde lors du clic sur le bouton
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_all'])) {
    $config = [
        'ai_provider'=> $_POST['ai_provider'],
        'api_key'    => trim($_POST['api_key']),
        'bot_name'   => htmlspecialchars($_POST['bot_name']),
        'bot_color'  => $_POST['bot_color'],
        'bot_avatar' => filter_var($_POST['bot_avatar'], FILTER_SANITIZE_URL),
        'bubble_img' => filter_var($_POST['bubble_img'], FILTER_SANITIZE_URL)
    ];
    file_put_contents($configFile, json_encode($config));
    
    if (!empty($_POST['extracted_text'])) {
        file_put_contents($memoireFile, $_POST['extracted_text']);
    }
    echo "<div style='background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;'>✅ Configuration Multi-IA enregistrée !</div>";
}

$memoire = file_exists($memoireFile) ? file_get_contents($memoireFile) : "";


/*code pour integrer le widget dans le site 
<div id="dakrea-btn" style="position:fixed; bottom:20px; right:20px; cursor:pointer; z-index:9999;">
        <div id="btn-style" style="width:65px; height:65px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 15px rgba(0,0,0,0.3); overflow:hidden;">
            <div id="btn-icon">💬</div>
        </div>
    </div>

    <iframe id="dakrea-win" src="widget.php" style="display:none; position:fixed; bottom:100px; right:20px; width:350px; height:500px; border:none; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,0.2); z-index:9999;"></iframe>

    <script>
        // Synchronisation du design avec config.json
        fetch('config.json').then(r => r.json()).then(config => {
            const style = document.getElementById('btn-style');
            style.style.background = config.bot_color || '#007bff';
            document.getElementById('btn-icon').style.color = "white";
            document.getElementById('btn-icon').style.fontSize = "30px";
        });

        document.getElementById('dakrea-btn').onclick = () => {
            const f = document.getElementById('dakrea-win');
            f.style.display = (f.style.display === 'none') ? 'block' : 'none';
        };
    </script> */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Chatbot Dakrea</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 30px; }
        .container { background: white; padding: 30px; border-radius: 15px; max-width: 800px; margin: auto; box-shadow: 0 5px 25px rgba(0,0,0,0.1); }
        h1 { color: #007bff; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        h3 { margin-top: 25px; color: #555; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; }
        label { display: block; margin-top: 15px; font-weight: bold; font-size: 14px; }
        input, textarea, select { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        .btn { background: #007bff; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; margin-top: 30px; font-size: 16px; transition: 0.3s; }
        .btn:hover { background: #0056b3; }
        #status { padding: 10px; border-radius: 5px; margin-top: 10px; display: none; font-size: 13px; }
        .row { display: flex; gap: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h1>⚙️ Dashboard Multi-IA</h1>
    <form method="post">
        
        <h3>🤖 Intelligence Artificielle</h3>
        <label>Moteur d'IA :</label>
        <select name="ai_provider">
            <option value="groq" <?php if(($config['ai_provider'] ?? '') == 'groq') echo 'selected'; ?>>Groq (Llama 3)</option>
            <option value="google" <?php if(($config['ai_provider'] ?? '') == 'google') echo 'selected'; ?>>Google Gemini 1.5</option>
            <option value="grok" <?php if(($config['ai_provider'] ?? '') == 'grok') echo 'selected'; ?>>Grok (xAI)</option>
            <option value="openai" <?php if(($config['ai_provider'] ?? '') == 'openai') echo 'selected'; ?>>OpenAI (ChatGPT)</option>
        </select>

        <label>Clé API :</label>
        <input type="password" name="api_key" value="<?php echo htmlspecialchars($config['api_key'] ?? ''); ?>" required>

        <h3>🎨 Personnalisation visuelle</h3>
        <div class="row">
            <div style="flex:2">
                <label>Nom du bot :</label>
                <input type="text" name="bot_name" value="<?php echo htmlspecialchars($config['bot_name'] ?? 'Assistant'); ?>">
            </div>
            <div style="flex:1">
                <label>Couleur :</label>
                <input type="color" name="bot_color" value="<?php echo $config['bot_color'] ?? '#007bff'; ?>" style="height:45px; cursor:pointer;">
            </div>
        </div>

        <label>URL Image Avatar (Haut du chat) :</label>
        <input type="text" name="bot_avatar" value="<?php echo htmlspecialchars($config['bot_avatar'] ?? ''); ?>" placeholder="https://exemple.com/avatar.png">

        <label>URL Image Bulle (Bouton flottant) :</label>
        <input type="text" name="bubble_img" value="<?php echo htmlspecialchars($config['bubble_img'] ?? ''); ?>" placeholder="https://exemple.com/icon.png">

        <h3>📄 Mémoire du Bot (PDF)</h3>
        <label>Charger un nouveau document :</label>
        <input type="file" id="pdf-file" accept=".pdf">
        <div id="status"></div>
        <textarea name="extracted_text" id="extracted_text" style="height:150px;" placeholder="Le contenu du PDF s'affichera ici après l'analyse..."><?php echo htmlspecialchars($memoire); ?></textarea>

        <button type="submit" name="save_all" class="btn">🚀 Enregistrer la Configuration</button>
    </form>
</div>

<script>
    // Logique PDF.js (Inchangée)
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
    document.getElementById('pdf-file').onchange = async (e) => {
        const file = e.target.files[0];
        const status = document.getElementById('status');
        status.style.display = "block"; status.innerText = "⏳ Analyse en cours..."; status.style.background = "#e3f2fd";
        const reader = new FileReader();
        reader.onload = async function() {
            const typedarray = new Uint8Array(this.result);
            const pdf = await pdfjsLib.getDocument(typedarray).promise;
            let text = "";
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const content = await page.getTextContent();
                text += content.items.map(s => s.str).join(" ") + " ";
            }
            document.getElementById('extracted_text').value = text;
            status.innerText = "✅ Analyse terminée. N'oubliez pas de sauvegarder !";
            status.style.background = "#d4edda";
        };
        reader.readAsArrayBuffer(file);
    };
</script>


</body>
</html>


    
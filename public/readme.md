# 🤖 Chatbot Dakrea - Dashboard Multi-IA & PDF Search

Ce projet a été réalisé par **Jérémy Krumenacker** dans le cadre d'un stage chez **Dakrea**. Il s'agit d'une solution complète de chatbot capable de basculer entre plusieurs moteurs d'IA et d'utiliser un document PDF comme base de connaissance[cite: 1, 3].

## 🌟 Points Forts
- **Multi-IA** : Compatible avec Groq (Llama 3), Google Gemini, Grok (xAI) et OpenAI[cite: 3].
- **Analyse PDF** : Extraction de texte intégrée via `PDF.js` pour créer une mémoire locale (`memoire.txt`)[cite: 3].
- **Customisation** : Dashboard complet pour modifier le nom du bot, les couleurs et les avatars sans toucher au code[cite: 3].
- **Léger** : Utilisation d'une architecture PHP/JS simple, idéale pour un déploiement rapide sur Pantheon[cite: 1, 2].

## 📁 Structure du Projet
- **`admin.php`** : Interface de gestion. Permet de configurer les clés API et d'analyser les PDF[cite: 3].
- **`widget.php`** : Le cœur du chatbot. Gère l'affichage des messages et la logique de communication avec les APIs.
- **`config.json`** : Fichier de stockage des paramètres de configuration (Clé API, fournisseur, design)[cite: 4].
- **`memoire.txt`** : Base de données textuelle générée à partir de vos documents PDF[cite: 3].
- **`index.html`** : Page de démonstration intégrant le widget via une iframe[cite: 5].

## 🚀 Installation & Utilisation

1. **Upload** : Copiez tous les fichiers sur votre serveur (via FileZilla).
2. **Configuration** : Rendez-vous sur `admin.php` pour :
   - Choisir votre moteur d'IA (actuellement par défaut : **Groq**)[cite: 3, 4].
   - Renseigner votre clé API[cite: 3].
   - Charger votre document PDF pour l'analyser[cite: 3].
3. **Intégration** : Le widget s'intègre sur n'importe quel site avec le code suivant présent dans `index.html`[cite: 5] :
   ```html
   <iframe src="widget.php" style="width:350px; height:500px; border:none;"></iframe>

const fs = require('fs');
const path = require('path');

const filePath = path.join('C:', 'Users', 'lapto', 'Desktop', 'botcodigo', 'tu-codigo_super_admin', 'resources', 'views', 'livewire', 'admin', 'dashboard-metrics.blade.php');
const fzPath = path.join('C:', 'Users', 'lapto', 'Desktop', 'botcodigo', 'tu-codigo_super_admin', 'archivos_para_filezilla', 'resources', 'views', 'livewire', 'admin', 'dashboard-metrics.blade.php');

let text = fs.readFileSync(filePath, 'utf8');

const replacements = {
    "RÃ¡pidas": "Rápidas",
    "prÃ³ximas": "próximas",
    "âœ“": "✓",
    "MÃ¡s": "Más",
    "ðŸ¥‡": "🥇",
    "ðŸ¥ˆ": "🥈",
    "ðŸ¥‰": "🥉",
    "Â°": "°",
    "dÃas": "días",
    "dÃ­as": "días",
    "Ã©xito": "éxito",
    "Ã‰xito": "Éxito",
    "â€”": "—",
    "Â¿": "¿",
    "DistribuciÃ³n": "Distribución",
    "Ãšltimos": "Últimos",
    "HISTÃ“RICO": "HISTÓRICO",
    "Ã¡": "á",
    "Ã©": "é",
    "Ã³": "ó",
    "Ã­": "í",
    "Ãº": "ú",
    "Ã±": "ñ",
    "Ã‘": "Ñ",
    "Ã“": "Ó",
    "Ãš": "Ú",
    "Ã‰": "É",
    "Ã": "Í" // This might be dangerous if there's a solo Ã
};

for (const [k, v] of Object.entries(replacements)) {
    // Escape regex chars if any, but these are mostly fine
    text = text.split(k).join(v);
}

fs.writeFileSync(filePath, text, 'utf8');
fs.writeFileSync(fzPath, text, 'utf8');
console.log("Fixed encoding!");

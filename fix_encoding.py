import os
path = r"C:\Users\lapto\Desktop\botcodigo\tu-codigo_super_admin\resources\views\livewire\admin\dashboard-metrics.blade.php"
with open(path, "r", encoding="utf-8") as f:
    text = f.read()

# Fix common UTF-8 double-encoding artifacts
replacements = {
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
    "Ã": "Í"
}

for k, v in replacements.items():
    text = text.replace(k, v)

with open(path, "w", encoding="utf-8") as f:
    f.write(text)

path_fz = r"C:\Users\lapto\Desktop\botcodigo\tu-codigo_super_admin\archivos_para_filezilla\resources\views\livewire\admin\dashboard-metrics.blade.php"
with open(path_fz, "w", encoding="utf-8") as f:
    f.write(text)

print("Fixed encoding!")

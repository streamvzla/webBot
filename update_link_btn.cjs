const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'resources/views/client/query.blade.php');
let content = fs.readFileSync(file, 'utf8');

const linkHtmlRegex = /<div class="my-6">\s*<a href="\\\$\{codeValue\}" target="_blank"[^>]*>\s*Continuar Verificación[\s\S]*?<\/svg>\s*<\/a>\s*<\/div>/;

const newLinkHtml = `<div class="flex flex-col sm:flex-row gap-4 justify-center items-center my-6">
                            <a href="\${codeValue}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl px-8 py-4 shadow-lg shadow-emerald-500/30 transition-all transform hover:scale-105 text-lg">
                                Abrir Link
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <button onclick="navigator.clipboard.writeText('\${codeValue}'); const oldText = this.innerHTML; this.innerHTML = '¡Copiado!'; setTimeout(() => this.innerHTML = oldText, 2000);" class="inline-flex items-center gap-2 bg-slate-800 border border-slate-700 hover:bg-slate-700 text-slate-300 font-medium rounded-xl px-6 py-4 transition-all text-lg">
                                Copiar Link
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>`;

content = content.replace(linkHtmlRegex, newLinkHtml);
fs.writeFileSync(file, content);
console.log("Success");

const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'resources/views/client/query.blade.php');
let content = fs.readFileSync(file, 'utf8');

// 1. Update expires_at days logic
content = content.replace(
    /let expirationHtml = '';\s*if \(email\.expires_at\) \{\s*const expDate = new Date\(email\.expires_at\);\s*expirationHtml = `<p class="text-xs text-slate-400 mt-1">Vence: \$\{expDate\.toLocaleDateString\(\)\}<\/p>`;\s*\}/g,
    `let expirationHtml = '';
                      if (email.expires_at) {
                          const expDate = new Date(email.expires_at);
                          const now = new Date();
                          const diffTime = expDate - now;
                          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                          const daysText = diffDays > 0 ? \`(Quedan \${diffDays} días)\` : '(Expirado)';
                          const textClass = diffDays <= 3 ? 'text-red-400 font-bold' : 'text-slate-400';
                          expirationHtml = \`<p class="text-xs \${textClass} mt-1">Vence: \${expDate.toLocaleDateString()} \${daysText}</p>\`;
                      }`
);

// 2. Update data.success block
const successRegex = /if \(data\.success\) \{[\s\S]*?setTimeout\(\(\) => \{[\s\S]*?window\.location\.reload\(\);[\s\S]*?\}, 3000\);[\s\S]*?\} else \{/;
const newSuccessBlock = `if (data.success) {
                document.getElementById('resultIcon').className = 'hidden';
                document.getElementById('resultTitle').className = 'hidden';

                let codeHtml = '';
                const codeType = data.code?.type || 'code';
                const codeValue = data.code?.value || data.code || '';
                
                if (codeType === 'link') {
                    codeHtml = \`
                        <div class="my-6">
                            <a href="\${codeValue}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl px-8 py-4 shadow-lg shadow-emerald-500/30 transition-all transform hover:scale-105 text-lg">
                                Continuar Verificación
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    \`;
                } else if (codeType === 'html') {
                    codeHtml = \`
                        <div class="my-4 p-4 bg-slate-900 rounded-lg text-left overflow-hidden text-sm">
                            \${codeValue}
                        </div>
                    \`;
                } else {
                    codeHtml = \`
                        <div class="my-6">
                            <span class="inline-block bg-emerald-500/10 border-2 border-emerald-500 text-emerald-400 text-5xl font-mono font-bold py-4 px-10 rounded-2xl tracking-widest shadow-lg shadow-emerald-500/20">
                                \${codeValue}
                            </span>
                        </div>
                    \`;
                }

                let receivedAtHtml = '';
                if (data.received_at) {
                    receivedAtHtml = '<p class="text-sm text-slate-400 mt-4"><svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Recibido: ' + data.received_at + '</p>';
                }

                document.getElementById('resultMessage').innerHTML = \`
                    <h3 class="text-2xl font-bold text-white mb-2">¡Información Encontrada!</h3>
                    \${codeHtml}
                    \${receivedAtHtml}
                \`;

            } else {`;
content = content.replace(successRegex, newSuccessBlock);

// 3. Update else block for cooldown
const elseRegex = /document\.getElementById\('resultMessage'\)\.textContent = 'Correo reciente no encontrado\.[^']*';/;
const newElseBlock = `document.getElementById('resultMessage').textContent = 'Correo reciente no encontrado. Por favor, solicítalo en la plataforma y espera 60 segundos.';
                
                // COOLDOWN LOGIC
                let cooldownBtn = document.querySelector('#resultState button');
                if (cooldownBtn) {
                    cooldownBtn.disabled = true;
                    cooldownBtn.classList.remove('btn-premium');
                    cooldownBtn.classList.add('bg-slate-700', 'text-slate-400', 'cursor-not-allowed');
                    
                    let timeLeft = 60;
                    cooldownBtn.textContent = \`Esperar \${timeLeft}s para reintentar...\`;
                    
                    let timer = setInterval(() => {
                        timeLeft--;
                        if (timeLeft <= 0) {
                            clearInterval(timer);
                            cooldownBtn.disabled = false;
                            cooldownBtn.classList.remove('bg-slate-700', 'text-slate-400', 'cursor-not-allowed');
                            cooldownBtn.classList.add('btn-premium');
                            cooldownBtn.textContent = 'Realizar Nueva Consulta';
                        } else {
                            cooldownBtn.textContent = \`Esperar \${timeLeft}s para reintentar...\`;
                        }
                    }, 1000);
                }`;
content = content.replace(elseRegex, newElseBlock);

fs.writeFileSync(file, content);
console.log("Success");

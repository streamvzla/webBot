<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía del Sistema | {{ config('app.name', 'Tu Código') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-300 font-sans antialiased min-h-screen">
    <div class="fixed inset-0 z-[-1] pointer-events-none">
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-purple-500/10 rounded-full blur-[120px] mix-blend-screen opacity-50 translate-x-1/3 -translate-y-1/4"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-500/10 rounded-full blur-[100px] mix-blend-screen opacity-50 -translate-x-1/4 translate-y-1/4"></div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Guía y Funcionalidades del Sistema</h1>
                <p class="text-slate-400 mt-2">Manual detallado de uso para Clientes, Administradores y Programadores API.</p>
            </div>
            <a href="{{ url('/') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border border-white/10">Volver al Inicio</a>
        </div>

        <div class="space-y-12">
            
            <!-- Funcionalidades para Clientes -->
            <section class="glass-card rounded-2xl p-8 border border-slate-700/50">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Panel de Cliente y Funcionalidades
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-2">1. Extracción de Códigos</h3>
                        <p class="text-slate-400">Como cliente, el sistema te permite solicitar códigos de verificación en tiempo real de plataformas como Netflix, Max, Spotify, etc.</p>
                        <ul class="list-disc list-inside text-slate-400 mt-2 space-y-1">
                            <li>Dirígete a "Consultar Código" en tu menú lateral.</li>
                            <li>Ingresa la dirección de correo asignada y la plataforma que deseas.</li>
                            <li>El sistema accederá a la bandeja en segundos y te devolverá el código sin necesidad de que accedas manualmente al correo.</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-white mb-2">2. Cuentas Permitidas y Tiempo de Consumo</h3>
                        <p class="text-slate-400">En el dashboard verás la lista de los "Correos Asignados" que te pertenecen. El sistema controla automáticamente cuándo caduca la cuenta (fecha de vencimiento) y te protege de accesos no autorizados mediante detección de IP.</p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-white mb-2">3. Sistema de Garantías y Reemplazos automáticos</h3>
                        <p class="text-slate-400">Si alguna de tus cuentas falla o se cae, no pierdes tu dinero ni tu tiempo de suscripción:</p>
                        <ul class="list-disc list-inside text-slate-400 mt-2 space-y-1">
                            <li>Puedes ir a <strong>Garantías</strong> y enviar un reporte de fallo.</li>
                            <li><strong class="text-yellow-400">En ese mismo instante, el tiempo de consumo de esa cuenta se pausa.</strong></li>
                            <li>Cuando soporte te entrega la nueva cuenta, los días que estuvo pausada se te reintegran automáticamente a la nueva fecha de corte.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Funcionalidades para Administración -->
            <section class="glass-card rounded-2xl p-8 border border-slate-700/50">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Panel de Administración / Franquicias
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-2">1. Multitenancy y Franquicias</h3>
                        <p class="text-slate-400">El sistema permite crear cuentas "Franquicia". Cada franquicia es dueña de sus propios clientes y correos. Solo pueden ver, modificar y gestionar los clientes que pertenecen a su entorno, asegurando la privacidad.</p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-white mb-2">2. Resolución de Garantías FIFO</h3>
                        <p class="text-slate-400">El menú de Garantías organiza las solicitudes pendientes ordenando <strong>"el más viejo primero"</strong>. Al dar un reemplazo, el sistema automatiza desvincular el correo viejo y reanudar los días congelados.</p>
                    </div>
                </div>
            </section>

            <!-- API REST -->
            <section class="glass-card rounded-2xl p-8 border border-slate-700/50">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Integración API REST
                </h2>
                
                <p class="text-slate-400 mb-6">El sistema provee una API segura mediante tokens Sanctum (Tokens de Acceso) para integrar la extracción en tus propios bots (Discord, Telegram, WhatsApp) o apps.</p>

                <div class="space-y-8">
                    <!-- Autenticación -->
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-3">Autenticación</h3>
                        <p class="text-slate-400 mb-2">Todas las solicitudes a la API deben incluir el encabezado <code>Authorization: Bearer {tu-token}</code>.</p>
                        <div class="bg-slate-950 rounded-lg p-4 font-mono text-sm border border-slate-800 text-green-400">
                            Authorization: Bearer 1|abcdef1234567890...
                        </div>
                    </div>

                    <!-- Endpoint: Consultar Código -->
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-3">Endpoint: Obtener Código (Query)</h3>
                        <p class="text-slate-400 mb-3">Busca y extrae el último código de verificación recibido para un correo y plataforma específicos.</p>
                        
                        <div class="flex items-center gap-3 mb-4">
                            <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-xs font-bold font-mono border border-blue-500/30">POST</span>
                            <code class="text-slate-300 bg-white/5 px-2 py-1 rounded">/api/v1/query</code>
                        </div>

                        <h4 class="font-medium text-white mb-2">Parámetros Form Data (Body)</h4>
                        <div class="bg-slate-950 rounded-lg p-4 font-mono text-sm border border-slate-800 text-purple-300 mb-4 whitespace-pre">
email: cliente@midominio.com
platform_id: 1 (Opcional, ID de la plataforma)
                        </div>

                        <h4 class="font-medium text-white mb-2">Respuesta Exitosa (200 OK)</h4>
                        <div class="bg-slate-950 rounded-lg p-4 font-mono text-sm border border-slate-800 text-emerald-400 mb-4 whitespace-pre">
{
    "success": true,
    "code": "123456",
    "email": "cliente@midominio.com",
    "received_at": "2026-07-06 15:30:00"
}
                        </div>
                    </div>

                    <!-- Ejemplos Prácticos con Bots -->
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-3">Integración con Bots (Telegram / Discord)</h3>
                        <p class="text-slate-400 mb-3">Las API Keys generadas desde tu panel te permiten conectar tu propia cuenta con un bot personalizado sin compartir tus credenciales de acceso.</p>
                        
                        <div class="space-y-6">
                            <!-- Telegram -->
                            <div class="bg-black/30 border border-slate-700/50 rounded-xl p-5">
                                <h4 class="font-bold text-white mb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.18-.08-.04-.19-.02-.27 0-.11.03-1.9 1.21-5.36 3.55-.5.35-.95.52-1.36.51-.45-.01-1.31-.25-1.95-.46-.78-.26-1.4-.4-1.35-.85.03-.23.36-.47 1.01-.73 3.94-1.72 6.57-2.85 7.89-3.4 3.75-1.56 4.53-1.83 5.04-1.84.11 0 .36.03.49.14.11.09.14.22.15.34-.01.1-.01.2-.02.23z"></path></svg>
                                    Ejemplo para Telegram (BotFather)
                                </h4>
                                <ol class="list-decimal list-inside text-slate-400 text-sm space-y-2 mb-4">
                                    <li>Crea tu bot en Telegram hablando con <code class="text-blue-300">@BotFather</code> y obtén el <strong>Token del Bot</strong>.</li>
                                    <li>En tu panel de Tu Código, ve a <strong>API Keys</strong> y genera una clave.</li>
                                    <li>En tu servidor o script de Python/Node.js, configura los headers para solicitar el código usando ambas claves:</li>
                                </ol>
                                <div class="bg-slate-950 rounded-lg p-4 font-mono text-xs border border-slate-800 text-purple-300 whitespace-pre overflow-x-auto">
// Ejemplo Node.js (Telegraf)
const { Telegraf } = require('telegraf');
const axios = require('axios');

const bot = new Telegraf('TU_TOKEN_DE_BOTFATHER');
const MI_API_KEY = 'Bearer 1|abcdef123456...'; // Generada en tu panel

bot.command('codigo', async (ctx) => {
    try {
        const respuesta = await axios.post('https://tu-codigo.com/api/v1/query', {
            email: 'micorreo@midominio.com',
            platform_id: 1 // Ej: 1 para Netflix
        }, {
            headers: { 'Authorization': MI_API_KEY }
        });
        
        ctx.reply(`Tu código es: ${respuesta.data.code}`);
    } catch (e) {
        ctx.reply('No se encontró código reciente.');
    }
});
bot.launch();</div>
                            </div>

                            <!-- Discord -->
                            <div class="bg-black/30 border border-slate-700/50 rounded-xl p-5">
                                <h4 class="font-bold text-white mb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.4189-2.1568 2.4189z"></path></svg>
                                    Ejemplo para Discord
                                </h4>
                                <ol class="list-decimal list-inside text-slate-400 text-sm space-y-2 mb-4">
                                    <li>Ve al Discord Developer Portal, crea una aplicación y copia el <strong>Token del Bot</strong>.</li>
                                    <li>Usa tu clave de API de Tu Código para hacer llamadas cuando un usuario ejecute un comando <code>/codigo</code>.</li>
                                </ol>
                                <div class="bg-slate-950 rounded-lg p-4 font-mono text-xs border border-slate-800 text-purple-300 whitespace-pre overflow-x-auto">
// Ejemplo Discord.js
const { Client, GatewayIntentBits } = require('discord.js');
const axios = require('axios');

const client = new Client({ intents: [GatewayIntentBits.Guilds] });
const MI_API_KEY = 'Bearer 1|abcdef123456...';

client.on('interactionCreate', async interaction => {
    if (!interaction.isCommand()) return;
    
    if (interaction.commandName === 'codigo') {
        await interaction.deferReply();
        try {
            const respuesta = await axios.post('https://tu-codigo.com/api/v1/query', {
                email: 'micorreo@midominio.com'
            }, {
                headers: { 'Authorization': MI_API_KEY }
            });
            await interaction.editReply(`Tu código es: **${respuesta.data.code}**`);
        } catch(e) {
            await interaction.editReply('No se encontró el código.');
        }
    }
});
client.login('TU_TOKEN_DE_DISCORD');</div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </div>
</body>
</html>


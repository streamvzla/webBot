const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'resources/views/install/layout.blade.php');
let content = fs.readFileSync(file, 'utf8');

const successToastHtml = `

    <!-- Toast Notifications -->
    @if(session('success'))
    <div id="toast-success" class="fixed bottom-5 right-5 z-50 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-6 py-4 rounded-xl shadow-lg shadow-emerald-500/10 flex items-center gap-3 animate-fade-in-up transition-opacity duration-500">
        <div class="bg-emerald-500/20 p-2 rounded-full">
            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div class="font-medium">
            {{ session('success') }}
        </div>
        <button onclick="document.getElementById('toast-success').style.opacity='0'; setTimeout(()=>document.getElementById('toast-success').remove(), 500);" class="ml-4 text-emerald-500 hover:text-emerald-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-success');
            if(toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
    </script>
    @endif
</body>
</html>`;

content = content.replace("</body>\r\n</html>", successToastHtml);
content = content.replace("</body>\n</html>", successToastHtml);
fs.writeFileSync(file, content);
console.log("Success");

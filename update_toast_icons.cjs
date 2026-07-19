const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'resources/views/install/layout.blade.php');
let content = fs.readFileSync(file, 'utf8');

// Replace error icon
content = content.replace('class="w-5 h-5 flex-shrink-0 mt-0.5"', 'class="w-[14px] h-[14px] flex-shrink-0 mt-1"');

// Replace success main icon
content = content.replace('class="w-6 h-6 text-emerald-400"', 'class="w-[14px] h-[14px] text-emerald-400"');

// Replace success close icon
content = content.replace('class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"', 'class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"');

fs.writeFileSync(file, content);
console.log("Icons updated to 14x14");

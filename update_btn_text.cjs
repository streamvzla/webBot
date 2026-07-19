const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'resources/views/client/query.blade.php');
let content = fs.readFileSync(file, 'utf8');

// The current button text and icon
const oldText = 'Abrir Link';
const oldIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>';

// The new button text and a beautiful "secret key" icon
const newText = 'Ir al Enlace Secreto';
const newIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>';

content = content.replace(oldText + '\n                                ' + oldIcon, newText + '\n                                ' + newIcon);

// In case the spacing was slightly different, let's just do a robust regex replace
const regex = /Abrir Link[\s\S]*?<\/svg>/;
const replacement = `Ir al Enlace Secreto\n                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>`;
content = content.replace(regex, replacement);

fs.writeFileSync(file, content);
console.log("Success");

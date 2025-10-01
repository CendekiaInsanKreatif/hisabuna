// resources/js/main.js
import vfrag from 'vfrag';

document.addEventListener('DOMContentLoaded', () => {
    console.log('Vite and vfrag are loaded!');

    // Penggunaan vfrag
    const fragment = vfrag();
    const div = document.createElement('div');
    div.textContent = 'Hello from vfrag!';
    fragment.append(div);
    document.body.append(fragment);
});

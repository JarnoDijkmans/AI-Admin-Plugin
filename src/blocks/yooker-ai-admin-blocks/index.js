import React from 'react';
import { createRoot } from 'react-dom/client';
import '../../sass/ai-admin-plugin.scss';
import YadminPage from './pages/admin-page';
//import 'bootstrap/dist/js/bootstrap.bundle.min.js';

document.addEventListener("DOMContentLoaded", function(event) {
    wp.domReady(() => {
        const container = document.getElementById('ai-gebruikers-admin-page');
        if (container) {
            const root = createRoot(container);
            root.render(<YadminPage/>);

        } else {
            console.warn('Doelcontainer niet gevonden.');
        }
    });
});
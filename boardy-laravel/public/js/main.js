import { handleCallback } from './auth.js';

if (window.location.pathname === '/oauth/callback') {
    handleCallback()
        .then(token => {
            window.location.href = '/posts';
        })
        .catch(err => {
            console.error('Ошибка автоматического обмена:', err);
            window.location.href = '/';
        });
}

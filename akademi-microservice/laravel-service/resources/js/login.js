// resources/js/login.js

function doLogin() {
    const u = document.getElementById('username').value.trim();
    const p = document.getElementById('password').value;

    if (u === 'admin' && p === 'admin123') {
        sessionStorage.setItem('loggedIn', 'true');
        window.location.href = '/dashboard';
    } else {
        const err  = document.getElementById('error-msg');
        const form = document.querySelector('.form-wrapper');
        err.classList.remove('hidden');
        form.classList.add('shake');
        setTimeout(() => form.classList.remove('shake'), 400);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (sessionStorage.getItem('loggedIn')) {
        window.location.href = '/dashboard';
    }

    document.getElementById('btn-login').addEventListener('click', doLogin);

    document.getElementById('password').addEventListener('keypress', e => {
        if (e.key === 'Enter') doLogin();
    });
});

window.doLogin = doLogin;
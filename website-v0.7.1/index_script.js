// JavaScript to toggle between forms
const loginForm = document.getElementById('login-form');
const signupForm = document.getElementById('signup-form');
const recoverPasswordForm = document.getElementById('recover-password-form');

document.getElementById('signup-link').addEventListener('click', function (e) {
    e.preventDefault();
    loginForm.classList.add('hidden');
    signupForm.classList.remove('hidden');
    recoverPasswordForm.classList.add('hidden');
});

document.getElementById('login-link').addEventListener('click', function (e) {
    e.preventDefault();
    signupForm.classList.add('hidden');
    loginForm.classList.remove('hidden');
    recoverPasswordForm.classList.add('hidden');
});

document.getElementById('forgot-password-link').addEventListener('click', function (e) {
    e.preventDefault();
    loginForm.classList.add('hidden');
    signupForm.classList.add('hidden');
    recoverPasswordForm.classList.remove('hidden');
});

document.getElementById('back-to-login-link').addEventListener('click', function (e) {
    e.preventDefault();
    recoverPasswordForm.classList.add('hidden');
    loginForm.classList.remove('hidden');
});


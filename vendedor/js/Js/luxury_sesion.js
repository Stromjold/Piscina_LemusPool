// login_flip_script.js

// 1. Obtener los elementos
const flipContainer = document.querySelector('.flip-container');
const btnToRegister = document.getElementById('btn-to-register');
const btnToLogin = document.getElementById('btn-to-login');

// 2. Al hacer clic en "Regístrate Aquí", añadir la clase para girar
btnToRegister.addEventListener('click', () => {
    flipContainer.classList.add('is-flipped');
});

// 3. Al hacer clic en "Inicia Sesión", quitar la clase para volver
btnToLogin.addEventListener('click', () => {
    flipContainer.classList.remove('is-flipped');
});
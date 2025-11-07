const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

// Escuchar clic en el botón "Registrarse"
signUpButton.addEventListener('click', () => {
    container.classList.add('right-panel-active');
});

// Escuchar clic en el botón "Iniciar Sesión"
signInButton.addEventListener('click', () => {
    container.classList.remove('right-panel-active');
});
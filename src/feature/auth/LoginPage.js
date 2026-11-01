import Logo from './header/Logo.js';
import roleBtn from './form/RoleBtn.js';
import EmailForm from './form/EmailForm.js';
import PasswordForm from './form/PasswordForm.js';
import SocialForm from './form/SocialForm.js';
import navigationForm from './form/NavigationForm.js';

export default function LoginPage() {
    const main = document.createElement('main');
    const card = document.createElement('div');
    card.className = 'card';

    const errorMessage = document.createElement('div');
     errorMessage.style.display = "none";
    errorMessage.className = 'error';
    errorMessage.textContent = '';

    const bg_overlay = document.createElement('div');
    bg_overlay.className = 'bg';
    const bg_grad = document.createElement('div');
    bg_grad.className = 'bg-grad';

    const logo = Logo('src/assets/logo/Logo.png', 'Company Logo');
    const title = document.createElement('h2');
    title.textContent = 'Log In';
    const subtitle = document.createElement('p');
    subtitle.textContent = 'Welcome back! Choose your role below.';

    const roleForm = roleBtn();
    const emailForm = EmailForm();
    const passwordForm = PasswordForm();
    const socialForm = SocialForm();
    const submitBtn = document.createElement('button');
    submitBtn.type = 'submit';
    submitBtn.className = 'btn-login';
    submitBtn.textContent = 'Log In';
    const forgotPasswordLink = navigationForm('forgot', '/forgot-password', 'Forgot Password?', '');
    const registerLink = navigationForm('register-link', '/register', ' Register', 'Don\'t have an account?');

    const form = document.createElement('form');
    form.method = "POST";
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(form);
            const api = "http://localhost:8080/src/core/controller/UserController.php";
            const response = await fetch(api, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            console.log(result);
            //form.action = `/login/${roleSelect.value}`;
        }catch (error) {
            errorMessage.style.display = "flex";
            errorMessage.textContent = error.message;
        }
    });

    const socialDiv = document.createElement('div');
    socialDiv.className = 'divider';
    socialDiv.textContent = "or continue with";
    
    form.appendChild(roleForm);
    form.appendChild(emailForm);
    form.appendChild(passwordForm);
    form.appendChild(submitBtn);
    form.appendChild(socialDiv);
    form.appendChild(socialForm);
    form.appendChild(forgotPasswordLink);
    form.appendChild(registerLink);

    main.appendChild(bg_overlay);
    main.appendChild(bg_grad);
    card.appendChild(title);
    card.appendChild(subtitle);
    card.appendChild(logo);
    card.appendChild(errorMessage);
    card.appendChild(form);

    main.appendChild(card);
    return main;
}
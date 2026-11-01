import LandingPage from './feature/landing-page/LandingPage.js';
import LoginPage from './feature/auth/LoginPage.js';
import Router from './utils/Router.js';
import setStyle from './utils/setStyle.js';

const router = new Router();

// Register routes
router.addRoute("home", LandingPage());
router.addRoute("login", LoginPage());

const root = document.getElementById('root');

// Get GET parameter
const params = new URLSearchParams(window.location.search);
const currentRoute = params.get('route') || 'home';

switch (currentRoute) {
    case 'home':
        setStyle('src/feature/landing-page/LandingPage.css');
        root.appendChild(router.getRoute("home").content);
        break;

    case 'login':
        setStyle('src/feature/auth/LoginPage.css');
        root.appendChild(router.getRoute("login").content);
        break;

    default:
        root.innerHTML = '<h1>404 - Page Not Found</h1>';
        break;
}
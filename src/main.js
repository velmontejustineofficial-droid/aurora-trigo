import LandingPage from './feature/landing-page/LandingPage.js';
import LoginPage from './feature/auth/LoginPage.js';
import Router from './utils/Router.js';
import setStyle from './utils/setStyle.js';

const router = new Router();
// Add routes
router.addRoute("/", LandingPage());
router.addRoute("/login", LoginPage());

const root = document.getElementById('root');
const currentPath = window.location.pathname;



switch (currentPath) {
    case '/':
        setStyle('src/feature/landing-page/LandingPage.css');
        const landingContent = router.getRoute("/").content;
        root.appendChild(landingContent);
        break;

    case '/login':
        setStyle('src/feature/auth/LoginPage.css');
        const loginContent = router.getRoute("/login").content;
        root.appendChild(loginContent);
        break;

    default:
        root.innerHTML = '<h1>404 - Page Not Found</h1>';
        break;
}
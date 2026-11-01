import LandingPage from 'src/feature/landing-page/LandingPage.js';
import LoginPage from 'src/feature/auth/LoginPage.js';
import Router from 'src/utils/Router.js';
import setStyle from 'src/utils/setStyle.js';
const router = new Router();

router.addRoute("/", LandingPage());
router.addRoute("/login", LoginPage());

const root = document.getElementById('root');
const currentPath = window.location.pathname;

switch (currentPath) {
    case '/': {
        const cssPath = new URL(
            './feature/landing-page/LandingPage.css',
            import.meta.url
        ).href;

        root.appendChild(
            setStyle(router.getRoute("/").content, cssPath)
        );
        break;
    }

    case '/login': {
        const cssPath = new URL(
            './feature/auth/LoginPage.css',
            import.meta.url
        ).href;

        root.appendChild(
            setStyle(router.getRoute("/login").content, cssPath)
        );
        break;
    }

    default:
        root.innerHTML = '<h1>404 - Page Not Found</h1>';
}
import HeaderBrand from './header/HeaderBrand.js';
import HeaderNav from './header/HeaderNav.js';
import HeroSection from './section/HeroSection.js';
import Feature from './section/Features.js';
import Stats from './section/Stats.js';

export default function LandingPage() {
    const main = document.createElement('main');
    const headerNavigation = new HeaderNav(
        "Log In", "Register", "/login", "/register"
    );
    const headerBrand = new HeaderBrand(
        '/src/assets/logo/Logo.png', 
        'Baler Tri-Go Logo', 
        'Brand Name', 'Baler Tri-Go',
        'SAFE TRAVEL'
    );
    const hero_bg = document.createElement('div');
    const hero_overlay = document.createElement('div');
    hero_bg.className = 'hero-bg';
    hero_overlay.className = 'hero-overlay';

    //header
    const header = document.createElement('header');
    header.className = 'nav';
    header.appendChild(headerBrand.getHeaderBrand());
    header.appendChild(headerNavigation.getHeaderNavigation());

    //sections
    const heroSection = new HeroSection();
    heroSection.setHeroBadge('🛺 Baler, Aurora Province, Philippines');
    heroSection.setHeroTitle(`Fast, Safe <span class="accent">Tricycle</span> Booking <br> in <span class="gold">Baler</span>`);
    heroSection.setHeroSubtitle(`Book rides easily with nearby drivers.<br> Track trips, stay safe and travel smarter.`);
    heroSection.setHeroCTA('/?route=login', '/register', {register: '🛺 Book a Ride', login: 'Log In'});

    
    main.appendChild(header);
    main.appendChild(hero_bg);
    main.appendChild(hero_overlay);
    main.appendChild(heroSection.getHeroSection());
    main.appendChild(Feature());
    main.appendChild(Stats());

    return main;
}
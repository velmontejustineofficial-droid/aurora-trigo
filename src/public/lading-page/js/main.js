import Header from './Header.js';
import Hero from './Hero.js';

//hero badge
const hero_bg = document.createElement('div');
//hero overlay
const hero_overlay = document.createElement('div');

const content = document.getElementById('root');
hero_bg.className = 'hero-bg';
hero_overlay.className = 'hero-overlay';

content.appendChild(hero_bg);
content.appendChild(hero_overlay);

content.appendChild(Header());
content.appendChild(Hero());

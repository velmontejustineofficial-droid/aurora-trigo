export default function Hero() {
    const hero = document.createElement("div");
    const hero_badge = document.createElement("div");
    const hero_title = document.createElement("h1"); 
    const hero_subtitle = document.createElement("p");

    hero_title.innerHTML = `Fast, Safe <span class="accent">Tricycle</span> Booking <br> in <span class="gold">Baler</span>`;
    hero_title.className = 'hero-title';

    hero_badge.className = 'hero-badge';
    hero_badge.textContent = '🛺 Baler, Aurora Province, Philippines';
    
    hero_subtitle.innerHTML = `Book rides easily with nearby drivers.<br>
    Track trips, stay safe and travel smarter.` 
    hero_subtitle.className = 'hero-sub';

    hero.className = 'hero';


    const hero_cta = document.createElement("div");
    const cta_login = document.createElement('a');
    const cta_register = document.createElement('a');

    hero_cta.className = "hero-cta";
    cta_register.className = 'cta-big teal';
    cta_register.href = 'Register.php';
    cta_register.textContent = '🛺 Book a Ride';
    cta_login.className = 'cta-big ghost';
    cta_login.href = 'Login.php';
    cta_login.textContent = 'Log In';

    hero_cta.appendChild(cta_register);
    hero_cta.appendChild(cta_login);

    hero.appendChild(hero_badge);
    hero.appendChild(hero_title);
    hero.appendChild(hero_subtitle);
    hero.appendChild(hero_cta);
    
    return hero;
}
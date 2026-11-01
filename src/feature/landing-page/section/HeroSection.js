export default class HeroSection {
    constructor() {
        this.hero = document.createElement("div");
        this.hero_badge = document.createElement("div");
        this.hero_title = document.createElement("h1"); 
        this.hero_subtitle = document.createElement("p");
    }
    setHeroTitle(value) {
        this.hero_title.innerHTML = value;
        this.hero_title.className = 'hero-title';
        return this.hero_title;
    }
    setHeroBadge(value) {
        this.hero_badge.className = 'hero-badge';
        this.hero_badge.textContent = value;
    }
    setHeroSubtitle(value) {
        this.hero_subtitle.innerHTML = value;
        this.hero_subtitle.className = 'hero-sub';
    }
    setHeroCTA(loginHref, registerHref, ctaText) {
        this.hero_cta = document.createElement("div");
        this.cta_login = document.createElement('a');
        this.cta_register = document.createElement('a');
        this.hero_cta.className = "hero-cta";
        this.cta_register.className = 'cta-big teal';
        this.cta_register.href = registerHref;
        this.cta_register.textContent = ctaText.register;
        this.cta_login.className = 'cta-big ghost';
        this.cta_login.href = loginHref;
        this.cta_login.textContent = ctaText.login;

        this.hero_cta.appendChild(this.cta_register);
        this.hero_cta.appendChild(this.cta_login);
    }
    getHeroSection() {
        this.hero.className = 'hero';
        this.hero.appendChild(this.hero_badge);
        this.hero.appendChild(this.hero_title);
        this.hero.appendChild(this.hero_subtitle);
        this.hero.appendChild(this.hero_cta);
        return this.hero;
    }
}

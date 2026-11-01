export default function SocialForm() {
    const socialContainer = document.createElement('div');
    socialContainer.className = 'social-btns';

    const facebookBtn = document.createElement('button');
    facebookBtn.className = 'social-btn';
    facebookBtn.textContent = '🔵 facebook';
    const googleBtn = document.createElement('button');
    googleBtn.className = 'social-btn';
    googleBtn.textContent = '🔴 google';
    const xBtn = document.createElement('button');
    xBtn.className = 'social-btn';
    xBtn.textContent = '✖️ X';

    socialContainer.appendChild(facebookBtn);
    socialContainer.appendChild(googleBtn);
    socialContainer.appendChild(xBtn);

    return socialContainer;
}
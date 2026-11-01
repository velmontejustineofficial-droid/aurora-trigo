export default function Logo(imgSrc, altText) {
    const logoWrap = document.createElement('div');
    const logoContainer = document.createElement('div');
    const logo = document.createElement('img');

    logoWrap.className = 'logo-wrap';
    logoContainer.className = 'logo-circle';
    logo.src = imgSrc;
    logo.alt = altText;
    logo.style.cssText = "width:100%;height:100%;object-fit:contain;padding:6px;";

    logoWrap.appendChild(logoContainer);
    logoContainer.appendChild(logo);
    return logoWrap;
}
export default function navigationForm( className ,href, atext, ptext) {
    const a = document.createElement('a');
    a.href = href;
    a.textContent = atext;
    const p = document.createElement('p');
    p.className = className;
    p.textContent = ptext;
    p.appendChild(a);
    return p;
}
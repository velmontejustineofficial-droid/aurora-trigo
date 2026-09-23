export default function Header() {
    const nav = document.createElement('nav');

    const img = document.createElement('img');
    img.src = "../../assets/Logo.png";
    //img.className = "nav-logo";
    img.alt = 'Baler Tri-Go Logo';
    img.style = "width:10%; height:10%; object-fit: contain; padding: 3px;";

    const nav_name = document.createElement('div');
    nav_name.textContent = 'Baler Tri-Go';
    nav_name.className = "nav-name";

    const span = document.createElement('span');
    span.textContent = " SAFE TRAVEL";

    nav_name.appendChild(span);

    const nav_brand = document.createElement('a');
    nav_brand.className = "nav-brand";
    nav_brand.href = "index.html";
    
    nav_brand.appendChild(img);
    nav_brand.appendChild(nav_name);

    nav.appendChild(nav_brand);


    const btn = document.createElement("div");
    const login_btn = document.createElement('a');
    const register_btn = document.createElement('a');

    btn.className = "nav-btns";
    login_btn.className = 'btn-outline';
    login_btn.textContent = 'Log In';
    login_btn.href = 'Login.php';
    register_btn.className = 'btn-solid';
    register_btn.textContent = 'Register';
    register_btn.href = 'Register.php';

    btn.appendChild(login_btn);
    btn.appendChild(register_btn);

    nav.appendChild(btn);

    return nav;
}
function create_anchor_tag(className, textContent, href){
    const a = document.createElement('a');
    a.className = className;
    a.textContent = textContent;
    a.href = href;
    return a;
}

export default class HeaderNav {
    constructor(loginText, registerText, loginHref, registerHref) {
        this.btn = document.createElement("div");
        this.login_btn = create_anchor_tag("btn-outline", loginText, loginHref);
        this.register_btn = create_anchor_tag("btn-solid", registerText, registerHref);
        this.btn.className = "nav-btns";
    }
    getHeaderNavigation() {
        this.btn.appendChild(this.login_btn);
        this.btn.appendChild(this.register_btn);
        return this.btn;
    }
}
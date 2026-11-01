export default function PasswordForm() {
    const container = document.createElement("div");
    container.className = "form-group";
    const label = document.createElement("label");
    label.textContent = "Password";
    const icon = document.createElement("span");
    icon.className = "ico";
    icon.innerHTML = "🔒";
    const input = document.createElement("input");
    input.type = "password";
    input.name = "password";
    input.placeholder = "Enter password";
    input.required = true;
    input.autocomplete = "current-password";

    container.appendChild(label);
    container.appendChild(icon);
    container.appendChild(input);
    return container;
}
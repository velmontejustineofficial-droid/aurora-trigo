export default function emailForm() {
    const container = document.createElement("div");
    container.className = "form-group";
    const label = document.createElement("label");
    label.textContent = "Email or Phone Number";
    const icon = document.createElement("span");
    icon.className = "ico";
    icon.innerHTML = "📧";
    const input = document.createElement("input");
    input.type = "text";
    input.name = "email";
    input.placeholder = "Email or phone number";
    input.required = true;
    input.autocomplete = "username";
    input.inputMode = "email";

    container.appendChild(label);
    container.appendChild(icon);
    container.appendChild(input);
    return container;
}
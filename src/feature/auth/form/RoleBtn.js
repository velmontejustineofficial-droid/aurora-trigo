export default function roleBtn() {
    const container = document.createElement("div");
    container.className = "role-btns";

    const roleSelect = document.createElement("select");
    roleSelect.className = "role-select";
    roleSelect.name = "role";

    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "Select your role";
    defaultOption.disabled = true;
    defaultOption.selected = true;

    const driverOption = document.createElement("option");
    driverOption.value = "driver";
    driverOption.textContent = "🛺 Driver";

    const passengerOption = document.createElement("option");
    passengerOption.value = "passenger";
    passengerOption.textContent = "👤 Passenger";

    roleSelect.append(
        defaultOption,
        driverOption,
        passengerOption
    );

    container.appendChild(roleSelect);

    return container;
}
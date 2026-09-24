function FeatureCard(title, description, icon, icon_className){
    const feature_card = document.createElement("div");
    feature_card.className = "feat-card"
    const feature_icon = document.createElement("div");
    feature_icon.textContent = icon;
    feature_icon.className = icon_className;

    const div = document.createElement("div");
    const h3 = document.createElement("h3");
    const p = document.createElement("p");

    feature_card.appendChild(feature_icon);

    h3.textContent = title;
    p.textContent = description;
    div.appendChild(h3);
    div.appendChild(p);

    feature_card.appendChild(div);

    return feature_card;
}


export default function Feature(){
    const feature_base =  document.createElement("div");
    feature_base.className = "features";

    feature_base.appendChild(
        FeatureCard("Quick Booking","Easily find and book rides with drivers near you in seconds.", "📍", "feat-icon y")
    );
    feature_base.appendChild(
        FeatureCard("GPS Tracking","Follow your ride in real-time on a live interactive map.", "🗺️", "feat-icon t")
    );
    feature_base.appendChild(
        FeatureCard("Safe Travel","Connect with verified and rated drivers you can trust.", "✅", "feat-icon g")
    );

    return feature_base;
}
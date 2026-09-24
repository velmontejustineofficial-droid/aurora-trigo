function stat_div(){
    const stat_div = document.createElement("div");
    stat_div.className = "stat-div";
    return stat_div;
}

function stat(num, lbl){
    const stat = document.createElement("div");
    stat.className = "stat"

    const num_element = document.createElement("div");
    num_element.className = "stat-num";
    num_element.textContent = num;

    const lbl_element = document.createElement("div");
    lbl_element.className = "stat-lbl";
    lbl_element.textContent = lbl;

    stat.appendChild(num_element);
    stat.appendChild(lbl_element);

    return stat;
}

export default function Stats() {
    const stats_strip = document.createElement('div');
    stats_strip.className = 'stats-strip';

    stats_strip.appendChild(stat("500+","DRIVERS"));
    stats_strip.appendChild(stat_div());
    stats_strip.appendChild(stat("12K+","RIDES"));
    stats_strip.appendChild(stat_div());
    stats_strip.appendChild(stat("4.9★","AVG RATING"));
    stats_strip.appendChild(stat_div());
    stats_strip.appendChild(stat("1","TOWNS"));

    return stats_strip;
}
"use strict";

const urlId = window.location.pathname.replace(/\/urls\//, "");
const tableWrapper = document.querySelector(".table-wrapper");
const summary = document.querySelector(".summary");

const getAllClicks = async () => {
    return apiGet(`api/urls/${urlId}`);
}

const loadTable = (data) => {
    const table = document.createElement("table");

    const thead = table.createTHead().insertRow();

    const insertTh = (thead, content) => {
        const th = document.createElement("th");
        th.textContent = content;
        thead.appendChild(th);
    }

    insertTh(thead, "Date");
    insertTh(thead, "IP Address");
    insertTh(thead, "Location");

    const tbody = table.createTBody();

    for (const click of data.clicks) {
        const row = tbody.insertRow();

        const dateCell = row.insertCell();
        const date = new Date(click.date * 1000).toLocaleString();

        dateCell.textContent = date;

        row.insertCell().textContent = click.ip;

        const locationCell = row.insertCell();

        if (Object.keys(click.geo).length === 0) {
            locationCell.textContent = "Location data unavailable";
            continue;
        }

        const locationParts = [];

        if (click.geo.city) {
            locationParts.push(click.geo.city);
        }
        if (click.geo.country) {
            locationParts.push(click.geo.country);
        }

        const locationLabel = locationParts.length === 0 ? "See map" : locationParts.join(", ");

        if (!click.geo.latitude || !click.geo.longitude) {
            locationCell.textContent = locationLabel;
            continue;
        }

        const mapsUrl = `https://maps.google.com/?q=${click.geo.latitude},${click.geo.longitude}`;
        const locationLink = createLink(mapsUrl, locationLabel);
        locationLink.target = "_blank";

        locationCell.appendChild(locationLink);
    }

    tableWrapper.textContent = "";
    tableWrapper.appendChild(table);
}

const loadHandler = async () => {
    const data = await getAllClicks();

    const link = `${API_ROOT}/${data.short}`;
    const linkNoProtocol = removeProtocol(link);
    const summaryText = document.createElement("p");
    summaryText.innerHTML = `Link <a href="${link}">${linkNoProtocol}</a> had ${data.clicks.length} clicks since it was created.`;

    const copyButton = document.createElement("button");
    copyButton.type = "button";
    copyButton.textContent = "Copy";
    copyButton.addEventListener("click", () => {
        copy(link);
        showPopup("The link was copied to your clipboard!");
    });

    summary.appendChild(summaryText);
    summary.appendChild(copyButton);

    loadTable(data);
};

window.addEventListener("load", loadHandler);

"use strict";

const tableWrapper = document.querySelector(".table-wrapper");

const getAllUrls = async () => {
    return apiGet("api/urls");
};

const createRowActions = (shortUrl, urlId) => {
    const actions = document.createElement("div");
    actions.className = "actions";

    const copyButton = document.createElement("button");

    copyButton.type = "button";
    copyButton.textContent = "Copy";

    copyButton.addEventListener("click", () => {
        copy(shortUrl);
        showPopup("The link was copied to your clipboard!");
    });

    actions.appendChild(copyButton);

    const viewButton = document.createElement("a");
    viewButton.href = `/urls/${urlId}`;
    viewButton.textContent = "View";
    viewButton.className = "button";

    actions.appendChild(viewButton);

    return actions;
};

const loadTable = (urls) => {
    const table = document.createElement("table");

    const thead = table.createTHead().insertRow();
    
    const insertTh = (thead, content) => {
        const th = document.createElement("th");
        th.textContent = content;
        thead.appendChild(th);
    }

    insertTh(thead, "Short Link");
    insertTh(thead, "Long Link");
    insertTh(thead, "Actions");

    const tbody = table.createTBody();

    for (const url of urls) {
        const row = tbody.insertRow();

        const short = row.insertCell();
        const shortUrl = `${API_ROOT}/${url.short}`;

        short.appendChild(createLink(shortUrl, removeProtocol(shortUrl)));
        
        const long = row.insertCell();
        let longLabel = removeProtocol(url.long);

        if (longLabel.length > 70) {
            longLabel = longLabel.substring(0, 70) + "...";
        }

        const longLink = createLink(url.long, longLabel);
        longLink.title = removeProtocol(url.long);

        long.appendChild(longLink);

        const actions = row.insertCell();
        actions.appendChild(createRowActions(shortUrl, url.short));
    }

    tableWrapper.textContent = "";
    tableWrapper.appendChild(table);
}

const loadHandler = async () => {
    const urls = await getAllUrls();
    
    loadTable(urls);
};

window.addEventListener("load", loadHandler);

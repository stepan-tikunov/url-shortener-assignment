"use strict";

const API_ROOT = "https://cringouli.cc";

const apiGet = async (method, params) => {
    const url = new URL(`${API_ROOT}/${method}`);
    url.search = new URLSearchParams(params).toString();

    return fetch(url).then(r => r.json());
};

const createLink = (url, label) => {
    const link = document.createElement("a");
    link.href = url;
    link.textContent = label;

    return link;
}

const copy = (text) => {
    navigator.clipboard.writeText(text);
}

const removeProtocol = (url) => {
    return url.replace(/https?:\/\//, "")
};

const sleep = async (ms) => new Promise(resolve => {
    setTimeout(resolve, ms);
});

const showPopup = (() => {
    let timeoutId = null;
    const popup = document.querySelector(".popup");
    const popupText = document.querySelector(".popup-text");

    const animation = [
        { opacity: 1, offset: 0 },
        { opacity: 0, offset: 0.4 },
        { opacity: 0, offset: 0.6 },
        { opacity: 1, offset: 1 },
        
    ]

    return async (content, isError = false, durationMs = 3000) => {
        if (timeoutId !== null) {
            clearTimeout(timeoutId);
            popupText.animate(animation, 300);
            await sleep(150);
        }

        popupText.textContent = content;

        if (isError) {
            popup.classList.add("error");
        } else {
            popup.classList.remove("error");
        }


        popup.classList.add("show");

        timeoutId = setTimeout(() => popup.classList.remove("show"), durationMs);
    }
})();
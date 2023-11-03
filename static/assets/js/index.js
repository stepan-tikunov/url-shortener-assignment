"use strict";

const form = document.querySelector(".url-form");
const urlInput = document.querySelector(".url");
const shortUrlBlock = document.querySelector(".short-url");
const urlWrapper = document.querySelector(".url-wrapper");

const shortenUrl = async (url) => {
    return apiGet("api/shorten", { url });
};

const showShortUrl = (url, label) => {
    urlWrapper.textContent = "";

    urlWrapper.appendChild(createLink(url, label));
        
    shortUrlBlock.classList.add("show");
};

const handleSubmit = async (e) => {
    e.preventDefault();

    const response = await shortenUrl(urlInput.value);
    
    if (response.success) {
        const url = `${API_ROOT}/${response.shortUrl}`;
        const label = removeProtocol(url);

        copy(url);
        showPopup("The link was copied to your clipboard!");
        showShortUrl(url, label);
    } else {
        showPopup(`Could not shorten URL: ${response.error}`, true);
    }
};

form.addEventListener("submit", handleSubmit);
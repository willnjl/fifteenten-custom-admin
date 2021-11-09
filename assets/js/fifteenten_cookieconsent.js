//  Cookie Storage Helper Functions
const cookieStorage = {
  getAll: () => {
    return document.cookie
      .split(";")
      .map((cookie) => cookie.split("="))
      .reduce((acc, [key, value]) => ({ ...acc, [key.trim()]: value }), {});
  },
  getItem: (item) => {
    const cookies = document.cookie
      .split(";")
      .map((cookie) => cookie.split("="))
      .reduce((acc, [key, value]) => ({ ...acc, [key.trim()]: value }), {});
    return cookies[item];
  },
  deleteItem: ({ name, domain }) => {
    const expired = "Expires=Thu, 01 Jan 1970 00:00:01 GMT";
    document.cookie = `${name}=;Path=/; Domain=${domain}; ${expired}; `;
  },
  setItem: (item, value, exdays) => {
    let date = new Date();
    date.setTime(date.getTime() + exdays * 86400000);
    let dateExpires = "expires=" + date;
    document.cookie = `${item}=${value};expires=${dateExpires} path=/;`;
  },
};

// removes all Analyitcs cookies
const deleteAllAnalyticsCookies = (cookies, domain) => {
  Object.keys(cookies).forEach((cookie) => {
    let regEx = /^__g/;
    let analytical = regEx.test(cookie);
    if (analytical) {
      cookieStorage.deleteItem(cookie, domain);
    }
  });
};

let applyMarketingTools = (containerID) => {
  (function (w, d, s, l, i) {
    w[l] = w[l] || [];
    w[l].push({ "gtm.start": new Date().getTime(), event: "gtm.js" });
    var f = d.getElementsByTagName(s)[0],
      j = d.createElement(s),
      dl = l != "dataLayer" ? "&l=" + l : "";
    j.async = true;
    j.src = "https://www.googletagmanager.com/gtm.js?id=" + i + dl;
    f.parentNode.insertBefore(j, f);
  })(window, document, "script", "dataLayer", containerID);
};

// helper funtion to update consent
let setConsent = (val, time) =>
  storageType.setItem("cookieconsent", JSON.stringify({ val, time }));

// helper funtion to check if consent has expired
let checkExpired = (now, time) => {
  let result =
    Math.floor((now - time) / 10 / 60 / 60 / 24) > validConsentDuration;
  return result;
};

((d, w) => {
  const storageType = localStorage; // Set Consent Storage Location

  // Get Site Settings
  const {
    gtm: { containerId, domain, validConsentDuration },
  } = siteSettings;

  const popup = d.getElementById("cookie-popup");

  popup &&
    (() => {
      let now = new Date();

      // check if visitor has consented before ?
      const cookieconsent = storageType.getItem("cookieconsent");
      let { val, time } = JSON.parse(cookieconsent) || {};

      w.onload = () => {
        if (!cookieconsent || checkExpired(now, time)) {
          popup.style.opacity = 1;
          popup.querySelector("#ButtonCAccept").onclick = () => {
            setConsent(true, now);
            applyMarketingTools();
            popup.style.display = "none";
          };
          popup.querySelector("#ButtonCReject").onclick = () => {
            setConsent(false, now);
            popup.style.display = "none";
            removeCookies(cookiesToRemove);
          };
        } else {
          if (val) {
            applyMarketingTools(containerId);
          }
          popup.style.display = "none";
        }
      };

      // Remove Cookies Button
      const removeCookiesBtn = d.getElementById("remove-cookies-btn");

      removeCookiesBtn &&
        removeCookiesBtn.addEventListener("click", () =>
          deleteAllAnalyticsCookies(cookieStorage.getAll(), domain)
        );
    });
})(document, window);

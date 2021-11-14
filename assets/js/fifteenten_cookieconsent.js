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
  deleteItem: (name, domain) => {
    const expired = "Expires=Thu, 01 Jan 1970 00:00:01 GMT";
    domain = "." + domain;
    let string = `${name}=;path=/;domain=${domain}; ${expired}; `;
    document.cookie = string;
  },
};

const deleteAllAnalyticsCookies = (domain) => {
  let gaCookies = Object.keys(cookieStorage.getAll()).filter((item) => {
    let regex = /^_g/;
    return regex.test(item);
  });
  gaCookies.forEach((cookie) => {
    cookieStorage.deleteItem(cookie, domain);
  });
};

let updateConsentGA = (status) => {
  let str = status ? "granted" : "denied";
  gtag("consent", "update", {
    ad_storage: str,
    analytics_storage: str,
  });
};

// helper funtion to update consent
let updateConsentLocal = (storage, val, time) =>
  storage.setItem("cookieconsent", JSON.stringify({ val, time }));

// helper funtion to check if consent has expired
let checkExpired = (now, time, duration) => {
  let result = Math.floor((now - time) / 10 / 60 / 60 / 24) > duration;
  return result;
};

((d, w) => {
  const storageType = localStorage; // Set Consent Storage Location

  // Get Site Settings
  let {
    gtm: { domain, validConsentDuration },
  } = siteSettings;

  const popup = d.getElementById("cookie-popup");
  if (popup) {
    let now = new Date();
    // check if visitor has consented before
    const cookieconsent = storageType.getItem("cookieconsent");
    let { val, time } = JSON.parse(cookieconsent) || {};
    w.onload = () => {
      if (!cookieconsent || checkExpired(now, time, validConsentDuration)) {
        popup.style.opacity = 1;
        popup.querySelector("#ButtonCAccept").onclick = () => {
          updateConsentLocal(storageType, true, now);
          updateConsentGA(true);
          popup.style.display = "none";
        };
        popup.querySelector("#ButtonCReject").onclick = () => {
          updateConsentLocal(storageType, false, now);
          updateConsentGA(false);
          deleteAllAnalyticsCookies(domain);
          popup.style.display = "none";
        };
      } else {
        popup.style.display = "none";
      }
    };
  }
})(document, window);

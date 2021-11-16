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
    let regexGa = /^_g/;
    let regexHotJar = /^_h/;
    console.log({
      item,
      ga: regexGa.test(item),
      hotjar: regexHotJar.test(item),
    });
    return regexGa.test(item) || regexHotJar.test(item);
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

let updateState = (state, pref) => (state[pref] = !state[pref]);

// helper funtion to update consent
let updateConsentLocal = (storage, val, time) =>
  storage.setItem("cookieconsent", JSON.stringify({ val, time }));

// helper funtion to check if consent has expired
let checkExpired = (now, time, duration) => {
  let result = Math.floor((now - time) / 10 / 60 / 60 / 24) > duration;
  return result;
};

let handleClick = (event, popup, storageType, status, now) => {
  event.preventDefault();
  popup.style.display = "none";
  updateConsentLocal(storageType, status, now);
  updateConsentGA(status);
};

((d, w) => {
  const storageType = localStorage; // Set Consent Storage Location

  // Get Site Settings
  let {
    gtm: { domain, validConsentDuration },
  } = siteSettings;

  const state = {
    marketing: false,
  };

  const popup = d.getElementById("cookie-popup");
  if (popup) {
    let now = new Date();
    // check if visitor has consented before
    const cookieconsent = storageType.getItem("cookieconsent");
    let { val, time } = JSON.parse(cookieconsent) || {};

    w.onload = () => {
      // if not consented or has expired
      if (!cookieconsent || checkExpired(now, time, validConsentDuration)) {
        popup.style.opacity = 1;
        popup.querySelector("#ButtonCAccept").onclick = (e) =>
          handleClick(e, popup, storageType, true, now);
        popup.querySelector("#ButtonCUpdate").onclick = (e) => {
          handleClick(e, popup, storageType, state.marketing, now);
        };
      } else {
        popup.style.display = "none";
      }

      if (!val) {
        deleteAllAnalyticsCookies(domain);
      }
    };
  }

  let preferences = popup.querySelectorAll(".cc_btn-preference");

  preferences.forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("active");
      updateState(state, btn.getAttribute("data-preference"));
      Array.from(btn.children).forEach((node) => {
        node.classList.toggle("active");
      });
    });
  });
})(document, window);

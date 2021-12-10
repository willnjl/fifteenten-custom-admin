((d, w) => {
  const storageType = localStorage; // Set Consent Storage Location

  let {
    gtm: { domain, validConsentDuration, containerId },
    rest,
  } = siteSettings;

  const state = {
    api: {
      waiting: false,
      error: false,
    },
  };

  const preferences = {
    marketing: false,
  };

  // helper funtion to check if consent has expired
  let checkExpired = (now, time, duration) => {
    let result = Math.floor((now - time) / 10 / 60 / 60 / 24) > duration;
    return result;
  };

  let updatePreference = (state, pref) => (state[pref] = !state[pref]);

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
      let string = `${name}=;path=/;domain=${"." + domain}; ${expired};`;
      document.cookie = string;
      string += `${name}=;path=/;domain=${domain}; ${expired};`;
      document.cookie = string;
    },
  };

  const managePopup = {
    show: (popup) => {
      popup.style.opacity = 1;
    },
    close: (popup) => {
      popup.style.transform = `translateX(-200%)`;
      popup.style.opacity = 0;
      setTimeout(() => {
        popup.style.display = "none";
      }, 1000);
    },
    remove: (popup) => {
      popup.style.display = "none";
    },
  };

  const cleanUpStorage = (domain) => {
    let regex = /^(_h|_g)/;

    let gaCookies = Object.keys(cookieStorage.getAll()).filter((item) => {
      return regex.test(item);
    });
    gaCookies.forEach((cookie) => {
      cookieStorage.deleteItem(cookie, domain);
    });

    let storageItems = Object.keys(localStorage);

    storageItems
      .filter((item) => regex.test(item))
      .forEach((item) => {
        localStorage.removeItem(item);
      });
  };

  let updateConsentGA = (status) => {
    let str = status ? "granted" : "denied";
    gtag("consent", "update", {
      ad_storage: str,
      analytics_storage: str,
    });
  };

  let appendGtm = (containerId) => {
    let AnalyticsData = document.createElement("script");
    AnalyticsData.text = ` (function (w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({ "gtm.start": new Date().getTime(), event: "gtm.js" });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != "dataLayer" ? "&l=" + l : "";
      j.async = true;
      j.src = "https://www.googletagmanager.com/gtm.js?id=" + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, "script", "dataLayer", '${containerId}');`;
    document.head.appendChild(AnalyticsData);
  };
  // helper funtion to update consent
  let updateConsentLocal = (val, time) =>
    storageType.setItem("cookieconsent", JSON.stringify({ val, time }));

  let postDecline = (popup, state) => {
    state.api.waiting = true;

    let promise = axios.post(rest.url + "/decline", {
      data: {
        _wpnonce: rest.nonce,
      },
    });
    promise.then(({ data, status }) => {
      if (status === 200) {
        managePopup.close(popup);
        state.api.waiting = !state.api.waiting;
        console.log(state);
      }
    });

    promise.catch((error) => {
      state.api.waiting = !state.api.waiting;
      state.api.error = !state.api.error;
      console.log(state);
    });
  };

  let handleClick = (popup, status, now) => {
    updateConsentLocal(status, now);
    updateConsentGA(status);
    if (status) {
      appendGtm(containerId);
      managePopup.close(popup);
    } else {
      postDecline(popup, state);
      cleanUpStorage();
    }
  };

  const popup = d.getElementById("cookie-popup");
  if (popup) {
    const now = new Date();

    // check if visitor has consented before
    const cookieconsent = storageType.getItem("cookieconsent");
    let { val, time } = JSON.parse(cookieconsent) || {};

    w.onload = () => {
      // if not consented or has expired
      if (!cookieconsent || checkExpired(now, time, validConsentDuration)) {
        managePopup.show(popup);

        // user accepts
        popup.querySelector("#ButtonCAccept").onclick = (e) => {
          e.preventDefault();
          handleClick(popup, true, now);
          managePopup.close(popup);
        };

        // handle preferences
        popup.querySelector("#ButtonCUpdate").onclick = (e) => {
          e.preventDefault();
          handleClick(popup, preferences.marketing, now);
        };
      } else {
        managePopup.remove(popup);
        if (val) {
          // run GTM Code
          appendGtm(containerId);
        } else {
          // remove cookies
          cleanUpStorage(domain);
        }
      }
    };
  }
  let toggles = popup.querySelectorAll(".cc__toggle--preference");

  toggles.forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("cc__toggle--active");
      updatePreference(state, btn.getAttribute("data-preference"));
      Array.from(btn.children).forEach((node) => {
        node.classList.toggle("cc__toggle--active");
      });
    });
  });
})(document, window);

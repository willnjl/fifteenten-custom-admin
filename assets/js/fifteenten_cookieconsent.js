const html_popup = `
        <div>
            <div class="cc__wrap">
                <h2>
                    Cookie Information 🍪
                </h2>
                <p>
                    We use cookies, just like (almost) everyone else, to improve our understanding of how to improve our own website for our visitors.
                </p>
                <p>
                    We want to make sure we're providing the most informative and best arranged experience for our visitors, so we deploy a handful of industry-standard cookies to do so.
                </p>
                <p>
                    Information in these cookies is available in our <a href="/cookies-policy" class="link">Cookies Policy</a> . Please note, our site is unlikely to function without ‘necessary’ cookies (same as other sites requiring cookies for aspects of their functionality). But you can choose whether or not to opt into marketing cookies below.
                </p>
                <ul>
                    <li class="cc__preference_container">
                        <div class="cc__toggle_holder cc__toggle--essential cc__toggle--active">
                            <div class="cc__toggle_circle cc__toggle--active"></div>
                        </div>
                        <label for="pref_essential">
                           <strong>Necessary Cookies</strong>  - These are the cookies that are required to make the website work
                        </label>
                    </li>
                    <li class="cc__preference_container">
                        <div class="cc__toggle_holder cc__toggle--preference" data-preference="marketing">
                            <div class="cc__toggle_circle"></div>
                            <input type="checkbox" class="cc__checkbox" name="pref_analytics" >
                        </div>
                         <label for="pref_analytics">
                           <strong>Marketing Cookies</strong> - These are the cookies that are required for us to learn how to improve the experience of our website visitors
                        </label>
                    </li>
                </ul>
                <div class="cc__btn-container">
                    <button
                    id="ButtonCUpdate"
                    class="cc__btn cc_btn--update"
                    >
                        Update to My Selection
                    </button>
                    <button
                    id="ButtonCAccept"
                    value="Agree to All & Proceed"
                    class="cc__btn cc__btn--accept"
                    >
                        Agree to All & Proceed
                    </button>
                    
                </div>
            </div>
        </div>`;

class Popup {
  constructor(html) {
    this.popup = this.create(html);
  }

  create(html) {
    let popup = document.createElement("div");
    popup.setAttribute("id", "cookie-popup");
    popup.innerHTML = html;
    return document.body.appendChild(popup);
  }

  get() {
    return this.popup;
  }
  show() {
    this.popup.style.opacity = 1;
  }
  close() {
    this.popup.style.transform = `translateX(-200%)`;
    this.popup.style.opacity = 0;
    setTimeout(() => {
      this.popup.style.display = "none";
    }, 1000);
  }
  remove() {
    this.popup.style.display = "none";
  }
}

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
    let AnalyticsData = d.createElement("script");
    AnalyticsData.text = `(function (w, d, s, l, i) {
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

    let nojs = d.createElement("iframe");

    nojs.setAttribute(
      "src",
      `https://www.googletagmanager.com/ns.html?id=${containerId}`
    );

    nojs.setAttribute("height", 0);
    nojs.setAttribute("width", 0);
    nojs.style.display = "none";
    nojs.style.visibility = "hidden";
    d.querySelector("body").prepend(nojs);
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
        state.api.waiting = !state.api.waiting;
        popup.close();
      }
    });

    promise.catch((error) => {
      state.api.waiting = !state.api.waiting;
      state.api.error = !state.api.error;
    });
  };

  let handleClick = (popup, status, now) => {
    updateConsentLocal(status, now);
    updateConsentGA(status);
    if (status) {
      appendGtm(containerId);
    } else {
      postDecline(popup, state);
      cleanUpStorage();
    }
  };
  const now = new Date();
  // check if visitor has consented before
  const cookieconsent = storageType.getItem("cookieconsent");
  let { val, time } = JSON.parse(cookieconsent) || {};

  // if not consented or has expired
  if (!cookieconsent || checkExpired(now, time, validConsentDuration)) {
    const popup = new Popup(html_popup);
    popup.show();

    // user accepts
    popup.get().querySelector("#ButtonCAccept").onclick = (e) => {
      e.preventDefault();
      handleClick(popup, true, now);
      popup.close();
    };

    // handle preferences
    popup.get().querySelector("#ButtonCUpdate").onclick = (e) => {
      e.preventDefault();
      handleClick(popup, preferences.marketing, now);
    };

    let toggles = popup.get().querySelectorAll(".cc__toggle--preference");

    toggles.forEach((btn) => {
      btn.addEventListener("click", () => {
        btn.classList.toggle("cc__toggle--active");
        updatePreference(state, btn.getAttribute("data-preference"));
        Array.from(btn.children).forEach((node) => {
          node.classList.toggle("cc__toggle--active");
        });
      });
    });
  } else {
    if (val) {
      appendGtm(containerId);
      updateConsentGA(true);
    } else {
      cleanUpStorage(domain);
    }
  }
})(document, window);

((d, w) => {
  let loginPage = d.getElementById("login");
  let logo = loginPage.querySelector("h1 a");
  const image = {
    src: attachment.props[0],
    w: attachment.props[1],
    h: attachment.props[2],
  };

  logo.style.backgroundImage = `url(${image.src})`;
  logo.style.width = `${image.w}px`;
  logo.style.height = `${image.h}px`;
})(document, window);

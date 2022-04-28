window.addEventListener("load", () => {
  // hamburger menu
  let hamburger = document.getElementById("hamburger");
  let links = document.querySelector("nav ul");
  hamburger.addEventListener("click", () => {
    // toggle the icon
    hamburger.firstChild.classList.toggle("fa-bars");
    hamburger.firstChild.classList.toggle("fa-close");
    // toggle the links
    links.classList.toggle("show");
  });
  // handle navbar state when scrolling
  let nav = document.getElementById("nav");
  window.onscroll = () => {
    if (window.scrollY > 200) {
      nav.classList.add("scrolling");
    } else {
      nav.classList.remove("scrolling");
    }
  };
  // init the animate on scroll library
  AOS.init();
});

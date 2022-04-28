let counters = document.querySelectorAll(".counter");
const speed = 200;

window.addEventListener("scroll", () => {
  counters.forEach((counter) => {
    const updateCount = () => {
      if (checkVisible(counter)) {
        const target = +counter.getAttribute("data-target");
        const count = +counter.innerText;

        const inc = target / speed;

        if (count < target) {
          counter.innerText = Math.ceil(count + inc);
          setTimeout(updateCount, 1);
        } else {
          counter.innerText = target;
        }
      }
    };

    updateCount();
  });
});

function checkVisible(elm) {
  var rect = elm.getBoundingClientRect();
  var viewHeight = Math.max(
    document.documentElement.clientHeight,
    window.innerHeight
  );
  return !(rect.bottom < 0 || rect.top - viewHeight >= 0);
}

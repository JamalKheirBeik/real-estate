// getting the elements
const slides = document.querySelectorAll(".slides .slide");
const indexes = document.getElementById("indexes");

if (slides.length > 0) {
  // adding indexes according to the number of images
  for (let i = 0; i < slides.length; i++) {
    indexes.innerHTML +=
      "<button class='index'><i class='fa fa-circle'></i></button>";
  }
  // handling the indexes
  const indexesArray = document.querySelectorAll("#indexes .index");
  indexesArray[0].classList.add("active");
  for (let i = 0; i < indexesArray.length; i++) {
    indexesArray[i].addEventListener("click", function () {
      for (let j = 0; j < slides.length; j++) {
        if (slides[j].classList.contains("active")) {
          slides[j].classList.remove("active");
          indexesArray[j].classList.remove("active");
          slides[i].classList.add("active");
          indexesArray[i].classList.add("active");
          clearInterval(slideshow);
          slideshow = setInterval(nextSlide, 4000);
          break;
        }
      }
    });
  }
  // auto slideshow
  let slideshow = setInterval(nextSlide, 4000);
  // next slide function
  function nextSlide() {
    for (let i = 0; i < slides.length; i++) {
      if (slides[i].classList.contains("active")) {
        if (i < slides.length - 1) {
          slides[i].classList.remove("active");
          indexesArray[i].classList.remove("active");
          slides[i + 1].classList.add("active");
          indexesArray[i + 1].classList.add("active");
        } else {
          slides[i].classList.remove("active");
          indexesArray[i].classList.remove("active");
          slides[0].classList.add("active");
          indexesArray[0].classList.add("active");
        }
        clearInterval(slideshow);
        slideshow = setInterval(nextSlide, 4000);
        break;
      }
    }
  }
}

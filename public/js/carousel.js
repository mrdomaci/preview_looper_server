  const imageData = {
    "product-1": [
      "http://localhost/images/product1_1.png",
      "http://localhost/images/product1_2.png",
      "http://localhost/images/product1_3.png",
      "http://localhost/images/product1_4.png",
      "http://localhost/images/product1_5.png"
    ],
    "product-2": [
      "http://localhost/images/product2_1.png",
      "http://localhost/images/product2_2.png",
      "http://localhost/images/product2_3.png",
      "http://localhost/images/product2_4.png",
      "http://localhost/images/product2_5.png"
    ],
    "product-3": [
      "http://localhost/images/product3_1.png",
      "http://localhost/images/product3_2.png",
      "http://localhost/images/product3_3.png",
      "http://localhost/images/product3_4.png",
      "http://localhost/images/product3_5.png"
    ]
  };
  var runningInterval = null;
  
  (function() {
    window.onload = function() {
      const productElements = document.querySelectorAll("[id^='product-']");
  
      var enter = function(element) {
        var id = element.target.id;
        const product = document.getElementById(id); // get the element by id
        const img = product.querySelector('.card-img-top'); // get the image element within the product element
        currentImg = img.src;
        let index = getIndex(currentImg, imageData[id]);
        const intervalID = setInterval(() => {
          index = (index + 1) % imageData[id].length; // increment the index and wrap around if necessary
          img.src = imageData[id][index]; // set the new source
        }, 1000);
        runningInterval = intervalID;
      };
  
      var leave = function(element) {
        stopLooping();
      }
  
      productElements.forEach(function(element) {
        element.addEventListener('mouseenter', enter, false);
        element.addEventListener('mouseleave', leave, false);
      });
    };
  }());

  function stopLooping() {
    clearInterval(runningInterval);
  }

  function getIndex(currentImg, imageData) {
    for (let i = 0; i < imageData.length; i++) {
      if (currentImg === imageData[i]) {
        return i;
      }
    }
    return 0;
  }
  
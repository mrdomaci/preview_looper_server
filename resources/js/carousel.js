const carouselSettings = document.getElementById('preview-looper-settings');
let infiniteRepeat = carouselSettings.getAttribute('data-infinite-repeat');
let returnToDefault = carouselSettings.getAttribute('data-return-to-default');
let showTime = carouselSettings.getAttribute('data-show-time');
let imagePrefix;
if (infiniteRepeat === null) {
  infiniteRepeat = '0';
}
if (returnToDefault === null) {
  returnToDefault = '0';
}
if (showTime === null) {
  showTime = 1000;
} else {
  showTime = parseInt(showTime);
}

let globalProducts = [];
let runningInterval;

const products = [];
const elements = document.getElementsByClassName('p');
(async () => {
for (let i = 0; i < elements.length; i++) {
  const element = elements[i];
  const microDataValue = element.getAttribute('data-micro-identifier');
  const link = element.querySelector('a').href;
  let images = await getHrefValues(link);
  images = removeDuplicates(images);
  products.push({ id: microDataValue, images: images});

  element.addEventListener('mouseenter', enter, false);
  element.addEventListener('mouseleave', leave, false);

  const image = element.querySelector('img');
  image.addEventListener('touchstart', handleTouchStart, false);
  image.addEventListener('touchmove', handleTouchMove, false);
    if (images && images.length > 0 && screen.width < 768) {
      image.classList.add("overlay-on");
      let icon = document.createElement('div');
      icon.classList.add('middle');
      icon.innerHTML = "<svg class='icon' width='40' height='40'><circle cx='20' cy='20' r='15' stroke='gray' stroke-width='8' fill='none'/></svg>";
      image.after(icon);
    }
}
globalProducts = products;
})();

function getHrefValues(url) {
    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();
      let images = [];
  
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            const parser = new DOMParser();
            const htmlDoc = parser.parseFromString(xhr.responseText, 'text/html');
            const pThumbnailsInner = htmlDoc.querySelector('.p-thumbnails-inner');
            if (pThumbnailsInner) {
              const aTags = pThumbnailsInner.querySelectorAll('a');
              aTags.forEach((a) => {
                const hrefValue = a.getAttribute('href');
                  if (hrefValue) {
                    images.push(hrefValue);
                  }
              });
            }
            resolve(images);
          } else {
            reject(xhr.status);
          }
        }
      };
  
      xhr.open('GET', url, true);
      xhr.send();
    });
  }

  function removeDuplicates(arr) {
    return Array.from(new Set(arr));
  }

  function stopLooping(element) {
    clearInterval(runningInterval);
    if (returnToDefault === '1' && imagePrefix) {
      const productElement = element.target;
      const img = productElement.querySelector('img');
      const id = productElement.getAttribute('data-micro-identifier');
      const product = getProduct(id);
      if (product && product.images.length > 1) {
        let imageNameDefaultArray = product.images[0].split('/');
        img.src = imagePrefix + imageNameDefaultArray[imageNameDefaultArray.length - 1];
      }
    }
  }

  function getIndex(product, currentImg) {
    for (let i = 0; i < product.images.length; i++) {
      let hrefValueArray = product.images[i].split('/');
      let value = hrefValueArray[hrefValueArray.length - 1]
      if (currentImg === value) {
        return i;
      }
    }
    return 0;
  }

  function getProduct(id) {
    for (let i = 0; i < globalProducts.length; i++) {
      if (id === globalProducts[i].id) {
        return globalProducts[i];
      }
    }
    return null;
  }

  var startX;
  var startY;

  var enter = function(element) {
    const productElement = element.target;
    const id = productElement.getAttribute('data-micro-identifier');
    const img = productElement.querySelector('img');
    const product = getProduct(id);
    let currentImg = img.src;
    imagePrefix = currentImg;
    let currentImgArray = currentImg.split('/');
    currentImg = currentImgArray[currentImgArray.length - 1];
    imagePrefix = imagePrefix.substring(0, imagePrefix.length - currentImg.length);
    let index = getIndex(product, currentImg);
    const intervalID = setInterval(() => {
      index = (index + 1) % product.images.length;
      if (product.images.length === 0) {
        return;
      }
      imageName = product.images[index];
      if (imageName === undefined) {
        return;
      }
      let imageNameArray = imageName.split('/');
      imageName = imageNameArray[imageNameArray.length - 1];

      if (infiniteRepeat === '0' && index === product.images.length ) {
        if (returnToDefault === '1') {
          let imageNameDefault = product.images[0];
          if (imageNameDefault === undefined) {
            return;
          }
          let imageNameDefaultArray = imageNameDefault.split('/');
          imageNameDefault = imageNameDefaultArray[imageNameDefaultArray.length - 1];
          img.src = imagePrefix + imageNameDefault;
        }
        stopLooping(element);
      } else {
        img.src = imagePrefix + imageName;
      }
    }, showTime);
    runningInterval = intervalID;
  };

  var leave = function(element) {
    stopLooping(element);
  }

  function handleTouchStart(event) {
    const firstTouch = event.touches[0];
    startX = firstTouch.clientX;
    startY = firstTouch.clientY;
  }
  
  function handleTouchMove(element) {
    if (!startX || !startY) {
      return;
    }
  
    const xDiff = startX - element.touches[0].clientX;
    const yDiff = startY - element.touches[0].clientY;
  
    if (Math.abs(xDiff) > Math.abs(yDiff)) {
      const productElement = findParentElementByClassName(element.target, 'p');
      const id = productElement.getAttribute('data-micro-identifier');
      const img = productElement.querySelector('img');
      const product = getProduct(id);
      let currentImg = img.src;
      imagePrefix = currentImg;
      let currentImgArray = currentImg.split('/');
      currentImg = currentImgArray[currentImgArray.length - 1];
      imagePrefix = imagePrefix.substring(0, imagePrefix.length - currentImg.length);
      let index = getIndex(product, currentImg);
      if (xDiff > 0) {
        if (index === 0) {
          index = product.images.length - 1;
        } else {
          index = index - 1;
        }
      } else {
        index = (index + 1) % product.images.length;
      }
      if (product.images.length === 0) {
        return;
      }
      imageName = product.images[index];
      if (imageName === undefined) {
        return;
      }
      let imageNameArray = imageName.split('/');
      imageName = imageNameArray[imageNameArray.length - 1];
      img.src = imagePrefix + imageName;
    }
  
    startX = null;
    startY = null;
  }

  function findParentElementByClassName(element, className) {
    if (!element) {
      return null;
    }
    if (element.classList && element.classList.contains(className)) {
      return element;
    }
    return findParentElementByClassName(element.parentElement, className);
  }
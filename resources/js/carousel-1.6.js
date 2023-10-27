const pw_carousel_settings = document.getElementById('dynamic-preview-images');
let pw_infinite_repeat = null;
let pw_return_to_default = null;
let pw_show_time = null;
let pw_initial_loop = null;
let pw_apply_to = null;
let pw_mobile_icons = null;
if (pw_carousel_settings !== null) {
  pw_infinite_repeat = pw_carousel_settings.getAttribute('data-dynamic-preview-images.infinite_repeat');
  pw_return_to_default = pw_carousel_settings.getAttribute('data-dynamic-preview-images.return_to_default');
  pw_show_time = pw_carousel_settings.getAttribute('data-dynamic-preview-images.show_time');
  pw_initial_loop = pw_carousel_settings.getAttribute('data-dynamic-preview-images.initial_loop');
  pw_apply_to = pw_carousel_settings.getAttribute('data-dynamic-preview-images.apply_to');
  pw_mobile_icons = pw_carousel_settings.getAttribute('data-dynamic-preview-images.mobile_icons');
}
let pw_image_prefix;
if (pw_infinite_repeat === null) {
  pw_infinite_repeat = '0';
}
if (pw_return_to_default === null) {
  pw_return_to_default = '0';
}
if (pw_show_time === null) {
  pw_show_time = 1500;
} else {
  pw_show_time = parseInt(pw_show_time);
}
if (pw_initial_loop === null) {
  pw_initial_loop = '500';
} else {
  pw_initial_loop = parseInt(pw_initial_loop);
}
if (pw_apply_to === null) {
  pw_apply_to = 'all';
}
if (pw_mobile_icons === null) {
  pw_mobile_icons = 'circles';
}

let pw_global_products = [];
let pw_running_interval;

const pw_project_id = getShoptetDataLayer('projectId');
const pw_products = [];
let pw_products_response;
const pw_elements = document.querySelectorAll('[data-micro="product"]');
let pw_should_request = true;
(async () => {
  for (let i = 0; i < pw_elements.length; i++) {
    const pw_element = pw_elements[i];
    const pw_micro_data_value = pw_element.getAttribute('data-micro-identifier');
    if (pw_micro_data_value === null) {
      continue;
    }
    if (sessionStorage.getItem('pw_' + pw_micro_data_value) !== null) {
      pw_should_request = false;
    }
  }
  if (pw_should_request) {
    await sendGetRequest(pw_project_id).then(
      (response) => {
        initPreviewImages(pw_elements, response);
      }
    );
  }
})();

checkForNewProducts();

function checkForNewProducts() {
  let pw_missing_products = [];
  let pw_current_product_elements = document.querySelectorAll('[data-micro="product"]:not([data-pw-init="true"]');
  for (let i = 0; i < pw_current_product_elements.length; i++) {
    const pw_current_element = pw_current_product_elements[i];
    const pw_current_micro_data_value = pw_current_element.getAttribute('data-micro-identifier');
    if (pw_current_micro_data_value === null) {
      continue;
    }
    pw_missing_products.push(pw_current_element);
  }
  if (pw_missing_products.length > 1) {
    getAdditionalProducts(pw_missing_products);
  }
  firstImageReturn();
  setTimeout(checkForNewProducts, 5000);
}

function getAdditionalProducts(pw_missing_products) {
  (async () => {
      await emptyPromise().then(
        (response) => {
          initPreviewImages(pw_missing_products, response);
        }
      );
  })();
}

function removeDuplicates(arr) {
  return Array.from(new Set(arr));
}

function stopLooping(pw_element) {
  clearInterval(pw_running_interval);
  pw_running_interval = null;
  if (pw_return_to_default === '1' && pw_image_prefix) {
    const pw_product_element = pw_element.target;
    const pw_img = pw_product_element.querySelector('img[data-micro], img[data-micro-image]');
    const pw_id = pw_product_element.getAttribute('data-micro-identifier');
    const pw_product = getProduct(pw_id);
    if (pw_product && pw_product.images.length > 1) {
      pw_img.src = pw_image_prefix + pw_product.images[0];
    }
  }
}

function getIndex(pw_product, pw_current_img) {
  for (let i = 0; i < pw_product.images.length; i++) {
    if (pw_current_img === pw_product.images[i]) {
      return i;
    }
  }
  return 0;
}

function getProduct(pw_id) {
  for (let i = 0; i < pw_global_products.length; i++) {
    if (pw_id === pw_global_products[i].id) {
      return pw_global_products[i];
    }
  }
  return null;
}

var pw_start_x;
var pw_start_y;

var pw_enter = function(pw_element) {
  firstImageReturn();
  const pw_product_element = pw_element.target;
  const pw_id = pw_product_element.getAttribute('data-micro-identifier');
  const pw_img = pw_product_element.querySelector('img[data-micro], img[data-micro-image]');
  const pw_product = getProduct(pw_id);
  let pw_current_img = pw_img.src;
  pw_image_prefix = pw_current_img;
  let pw_current_img_array = pw_current_img.split('/');
  pw_current_img = pw_current_img_array[pw_current_img_array.length - 1];
  pw_image_prefix = pw_image_prefix.substring(0, pw_image_prefix.length - pw_current_img.length);
  let pw_index = getIndex(pw_product, pw_current_img);
  const cycleImages = () => {
    pw_index = (pw_index + 1) % pw_product.images.length;
    if (pw_product.images.length === 0) {
      return;
    }
    
    const pw_image_name = pw_product.images[pw_index];
    if (pw_image_name === undefined) {
      return;
    }
    pw_img.src = pw_image_prefix + pw_image_name;
    
    if (pw_infinite_repeat === '0' && pw_index === pw_product.images.length - 1) {
      if (pw_return_to_default === '1') {
        const pw_image_name_default = pw_product.images[0];
        if (pw_image_name_default !== undefined) {
          pw_img.src = pw_image_prefix + pw_image_name_default;
        }
      }
      stopLooping(pw_element);
    }
  };
  setTimeout(cycleImages, pw_initial_loop);
  const pw_interval_id = setInterval(cycleImages, pw_show_time);
  pw_running_interval = pw_interval_id;
};  

var pw_leave = function(pw_element) {
  stopLooping(pw_element);
  firstImageReturn();
}

function handleTouchStart(pw_event) {
  const pw_first_touch = pw_event.touches[0];
  pw_start_x = pw_first_touch.clientX;
  pw_start_y = pw_first_touch.clientY;
}
  
function handleTouchMove(pw_element) {
  if (!pw_start_x || !pw_start_y) {
    return;
  }

  const pw_x_diff = pw_start_x - pw_element.touches[0].clientX;
  const pw_y_diff = pw_start_y - pw_element.touches[0].clientY;

  if (Math.abs(pw_x_diff) > Math.abs(pw_y_diff)) {
    const pw_product_element = findParentElementByClassName(pw_element.target, 'p');
    const pw_id = pw_product_element.getAttribute('data-micro-identifier');
    const pw_img = pw_product_element.querySelector('img[data-micro], img[data-micro-image]');
    const pw_product = getProduct(pw_id);
    let pw_current_img = pw_img.src.split('?')[0];
    pw_image_prefix = pw_current_img;
    pw_current_img = getImageName(pw_current_img);
    pw_image_prefix = pw_image_prefix.substring(0, pw_image_prefix.length - pw_current_img.length);
    let pw_index = getIndex(pw_product, pw_current_img);
    let pw_init_index = pw_index;
    if (pw_x_diff > 0) {
      if (pw_index === 0) {
        pw_index = pw_product.images.length - 1;
      } else {
        pw_index = pw_index - 1;
      }
    } else {
      pw_index = (pw_index + 1) % pw_product.images.length;
    }
    if (pw_product.images.length === 0) {
      return;
    }
    pw_image_name = pw_product.images[pw_index];
    if (pw_image_name === undefined) {
      return;
    }
    pw_img.src = pw_image_prefix + pw_image_name;

    const pw_image = pw_element.srcElement;
    const pw_link = pw_image.parentElement;
    if (pw_mobile_icons === 'circles') {
      const pw_svgs = pw_link.querySelectorAll('svg');
      pw_svgs.forEach((pw_svg, i) => {
        if (pw_init_index === i) {
          pw_svg.classList.remove('pw-circle');
          pw_svg.classList.add('pw-empty-circle');
        }
        if (pw_index === i) {
          pw_svg.classList.remove('pw-empty-circle');
          pw_svg.classList.add('pw-circle');
        }
      });
    } else if (pw_mobile_icons === 'numbers') {
      const pw_number_icon = pw_link.querySelector('b.pw-number-icon');
      pw_number_icon.innerHTML = (pw_index + 1) + ' / ' + pw_product.images.length;
    }
  }

  pw_start_x = null;
  pw_start_y = null;
}

function findParentElementByClassName(pw_element, pw_class_name) {
  if (!pw_element) {
    return null;
  }
  if (pw_element.classList && pw_element.classList.contains(pw_class_name)) {
    return pw_element;
  }
  return findParentElementByClassName(pw_element.parentElement, pw_class_name);
}

async function sendGetRequest(pw_project_id) {
  return new Promise(async (resolve, reject) => {
    try {
      let pw_url = 'https://slabihoud.cz/images/' + pw_project_id + '/' + stringToIntModulo11(pw_project_id);
      const pw_xhr = new XMLHttpRequest();
      pw_xhr.open('GET', pw_url, true);
      pw_xhr.onreadystatechange = async function () {
        if (pw_xhr.readyState === 4) {
          if (pw_xhr.status === 200) {
            let pw_response = parseJSONToPwProductsResponse(pw_xhr.responseText);
            resolve(pw_response);
          } else {
            reject(new Error('XHR request failed'));
          }
        }
      };
      pw_xhr.send();
    } catch (error) {
      reject(error);
    }
  });
}

function stringToIntModulo11(inputString) {
  const integerValue = parseInt(inputString, 10);
  if (!isNaN(integerValue)) {
    const result = integerValue % 11;
    return result;
  } else {
    return NaN;
  }
}

function emptyPromise() {
  return new Promise((resolve) => {
    resolve('');
  });
}

function parseJSONToPwProductsResponse(jsonData) {
  try {
    return JSON.parse(jsonData);
  } catch (error) {
    console.error("Error parsing JSON:", error);
    return null;
  }
}

function initPreviewImages(pw_elements, response) {
  for (const [index, value] of Object.entries(response)) {
    if (sessionStorage.getItem('pw_' + index) === null) {
      sessionStorage.setItem('pw_' + index, value);
    }
  }
  for (let i = 0; i < pw_elements.length; i++) {
    const pw_element = pw_elements[i];
    pw_element.setAttribute('data-pw-init', true);
    const microDataValue = pw_element.getAttribute('data-micro-identifier');
    if (microDataValue === null) {
      continue;
    }
    const pw_session_data = sessionStorage.getItem('pw_' + microDataValue);

    if (pw_session_data === null) {
      continue;
    }
    const pw_image = pw_element.querySelector('img[data-micro], img[data-micro-image]');
    let pw_images = pw_session_data.split(',');
    if (pw_images && pw_images.length > 1 && screen.width < 768 && (pw_apply_to === 'all' || pw_apply_to === 'mobile')) {
        let pw_first_image = getImageName(pw_image.getAttribute('data-src'));
        pw_images.unshift(pw_first_image);
    }
    pw_images = removeDuplicates(pw_images);
    if (pw_images[0] === 'undefined' || pw_images.length === 1) {
      continue;
    }
    pw_products.push({ id: microDataValue, images: pw_images});

    if (pw_apply_to === 'all' || pw_apply_to === 'pc') {
      pw_element.addEventListener('mouseenter', pw_enter, false);
      pw_element.addEventListener('mouseleave', pw_leave, false);  
    }    
    pw_image.setAttribute('data-original-img-source', pw_image.src);
    pw_image.setAttribute('data-micro-identifier-parent', microDataValue);
    pw_image.addEventListener('error', function handleError() {
      removeMissingImage(pw_image.src, pw_image.getAttribute('data-micro-identifier-parent'));
      pw_image.src = pw_image.getAttribute('data-original-img-source');
    });
    if (pw_images && pw_images.length > 1 && screen.width < 768 && (pw_apply_to === 'all' || pw_apply_to === 'mobile')) {
      pw_image.addEventListener('touchstart', handleTouchStart, false);
      pw_image.addEventListener('touchmove', handleTouchMove, false);
      pw_image.classList.add("overlay-on");
      let pw_icon = document.createElement('div');
      pw_image.after(pw_icon);
      pw_icon.classList.add('pw-overlay-container');
      let pw_inner_html = '';
      if (pw_mobile_icons === 'circles') {
        for (let i = 0; i < pw_images.length; i++) {
          if (i === 0) {
            pw_inner_html = pw_inner_html + "<svg width='10' height='10' class='pw-circle'><circle cx='5' cy='5' r='4'/></svg>";
          } else {
            pw_inner_html = pw_inner_html + "<svg width='10' height='10' class='pw-empty-circle'><circle cx='5' cy='5' r='4'/></svg>";
          }
        }
      } else if (pw_mobile_icons === 'numbers') {
        pw_inner_html = pw_inner_html + "<b class='pw-number-icon flag'>1 / " + pw_images.length + "</b>";
      }
      pw_icon.innerHTML = pw_inner_html;
    }
  }
  pw_global_products = pw_products;
}

function removeMissingImage(pw_missing_source, pw_product_identifier) {
  let pw_missing_source_array = pw_missing_source.split('/');
  pw_missing_source = pw_missing_source_array[pw_missing_source_array.length - 1];
  for (let i = 0; i < pw_global_products.length; i++) {
    if (pw_global_products[i].id === pw_product_identifier) {
      for (let j = 0; j < pw_global_products[i].images.length; j++) {
        if (pw_global_products[i].images[j] === pw_missing_source) {
          pw_global_products[i].images.splice(j, 1);
          break;
        }
      }
      break;
    }
  }
  let pw_session_data = sessionStorage.getItem('pw_' + pw_product_identifier);
  let pw_session_data_array = pw_session_data.split(',');
  let pw_new_session_data = '';
  for (let i = 0; i < pw_session_data_array.length; i++) {
    if (pw_session_data_array[i] === pw_missing_source) {
      continue;
    }
    pw_new_session_data = pw_new_session_data + pw_session_data_array[i]
    if (i !== pw_session_data_array.length - 1) {
      pw_new_session_data = pw_new_session_data + ',';
    }
  }
  sessionStorage.setItem('pw_' + pw_product_identifier, pw_new_session_data);
}

function getImageName(pw_current_img)
{
  let pw_current_img_array = pw_current_img.split('/');
  let result = pw_current_img_array[pw_current_img_array.length - 1];
  return result.split('?')[0];
}

function firstImageReturn()
{
  if (pw_return_to_default === '1' && pw_running_interval === null) {
    for(let i = 0; i < pw_global_products.length; i++) {
      const pw_product = pw_global_products[i];
      const pw_product_element = document.querySelector('[data-micro-identifier="' + pw_product.id + '"]');
      const pw_img = pw_product_element.querySelector('img[data-micro], img[data-micro-image]');
      if (pw_product.images.length > 1) {
        let pw_initial_image = pw_img.getAttribute('data-src');
        if (pw_initial_image != null && pw_initial_image != pw_img.src ) {
          pw_img.src = pw_initial_image;
        }
      }
    }
  }
}
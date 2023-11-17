const pw_carousel_settings = document.getElementById('dynamic-preview-images');
let pw_i_r = '0';
let pw_r_t_d ='0';
let pw_s_t = 1500;
let pw_i_l = 500;
let pw_a_t = 'all';
let pw_m_i = 'circles';
if (pw_carousel_settings) {
  pw_i_r = pw_carousel_settings.getAttribute('data-dynamic-preview-images.infinite_repeat') || '0';
  pw_r_t_d = pw_carousel_settings.getAttribute('data-dynamic-preview-images.return_to_default') || '0';
  pw_s_t = pw_carousel_settings.getAttribute('data-dynamic-preview-images.show_time') || 1500;
  pw_i_l = pw_carousel_settings.getAttribute('data-dynamic-preview-images.initial_loop') || 500;
  pw_a_t = pw_carousel_settings.getAttribute('data-dynamic-preview-images.apply_to') || 'all';
  pw_m_i = pw_carousel_settings.getAttribute('data-dynamic-preview-images.mobile_icons') || 'circles';
}
let pw_image_prefix;
let pw_global_products = [];
let pw_running_interval;
let pw_is_running = false;

const pw_project_id = getShoptetDataLayer('projectId');
const pw_products = [];
let pw_products_response;
const pw_elements = document.querySelectorAll('[data-micro="product"]');
if (pw_elements.length > 0) {
  let pw_should_request = true;
    if (sessionStorage.getItem('pw_data_updated_at') === null) {
      pw_should_request = true;
    } else if (sessionStorage.getItem('pw_data_updated_at') < new Date().getTime() - 1000 * 60 * 60 * 24) {
      pw_should_request = true;
    } else {
      pw_should_request = false;
    }
    if (pw_should_request) {
      clearIndexedDB();
      sessionStorage.setItem('pw_data_updated_at', new Date().getTime());
      sendGetRequest(pw_project_id).then((response) => {
        initPreviewImages(pw_elements, response).then(() => {checkForNewProducts();});
      })
    } else {
      checkForNewProducts();
    }
} 

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

function stopLooping(pw_element) {
  clearInterval(pw_running_interval);
  clearInterval();
  pw_is_running = false;
  if (pw_r_t_d === '1' && pw_image_prefix) {
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
  let pw_current_img_a = pw_current_img.split('/');
  pw_current_img = pw_current_img_a.pop();
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
    
    if (pw_i_r === '0' && pw_index === pw_product.images.length - 1) {
      if (pw_r_t_d === '1') {
        const pw_image_name_default = pw_product.images[0];
        if (pw_image_name_default !== undefined) {
          pw_img.src = pw_image_prefix + pw_image_name_default;
        }
      }
      stopLooping(pw_element);
    }
  };
  setTimeout(cycleImages, pw_i_l);
  const pw_interval_id = setInterval(cycleImages, pw_s_t);
  pw_running_interval = pw_interval_id;
  pw_is_running = true;
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
    if (isCircles()) {
      const pw_svgs = pw_link.querySelectorAll('svg');
      pw_svgs.forEach((pw_svg, i) => {
        pw_svg.classList.remove('pw-circle');
        pw_svg.classList.add('pw-empty-circle');
        if (pw_index === i) {
          pw_svg.classList.remove('pw-empty-circle');
          pw_svg.classList.add('pw-circle');
        }
      });
    } else if (isNumbers()) {
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
    try {
      let pw_url = 'https://slabihoud.cz/images/' + pw_project_id + '/' + stringToIntModulo11(pw_project_id);
      const response = await fetch(pw_url);
      const pw_json_data = await response.json();
      return pw_json_data;
    } catch (error) {
      reject(error);
    }
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

async function initPreviewImages(pw_elements, response) {
  const db = await setupIndexedDB();
  const transaction = db.transaction(["pw_images"], "readwrite");
  const objectStore = transaction.objectStore("pw_images");
  for (const [index, value] of Object.entries(response)) {
    objectStore.add({ id: index, images: value });
  }
  for(const pw_element of pw_elements) {
    pw_element.setAttribute('data-pw-init', true);
    const microDataValue = pw_element.getAttribute('data-micro-identifier');
    if (microDataValue === null) {
      continue;
    }
    const pw_idb_data = await new Promise((resolve, reject) => {
      const request = objectStore.get(microDataValue);
      request.onsuccess = function (event) {
        resolve(event.target.result);
      };
      request.onerror = function (event) {
        reject(event.error);
      };
    });

    if (!pw_idb_data || !pw_idb_data.images) {
      continue;
    }

    const pw_image = pw_element.querySelector('img[data-micro], img[data-micro-image]');
    let pw_images = pw_idb_data.images;
    if (pw_image.src === null) {
      continue;
    }
    pw_products.push({ id: microDataValue, images: pw_images});

    if (isPc() && pw_images.length > 1) {
      pw_element.addEventListener('mouseenter', pw_enter, false);
      pw_element.addEventListener('mouseleave', pw_leave, false); 
    }
    pw_image.setAttribute('data-micro-identifier-parent', microDataValue);
    pw_image.addEventListener('error', function handleError() {
      removeMissingImage(pw_image.src, pw_image.getAttribute('data-micro-identifier-parent'));
    });

    if (isMobile() && pw_images.length > 1) {
      pw_image.addEventListener('touchstart', handleTouchStart, false);
      pw_image.addEventListener('touchmove', handleTouchMove, false);
      pw_image.classList.add("overlay-on");
      let pw_icon = document.createElement('div');
      pw_image.after(pw_icon);
      pw_icon.classList.add('pw-overlay-container');
      let pw_inner_html = '';
      if (isCircles()) {
        for (let i = 0; i < pw_images.length; i++) {
          if (i === 0) {
            pw_inner_html = pw_inner_html + "<svg width='10' height='10' class='pw-circle'><circle cx='5' cy='5' r='4'/></svg>";
          } else {
            pw_inner_html = pw_inner_html + "<svg width='10' height='10' class='pw-empty-circle'><circle cx='5' cy='5' r='4'/></svg>";
          }
        }
      } else if (isNumbers()) {
        pw_inner_html = pw_inner_html + "<b class='pw-number-icon flag'>1 / " + pw_images.length + "</b>";
      }
      pw_icon.innerHTML = pw_inner_html;
    }
  }
  pw_global_products = pw_products;
}

async function removeMissingImage(pw_missing_source, pw_product_identifier) {
  let pw_missing_source_a = pw_missing_source.split('/');
  pw_missing_source = pw_missing_source_a[pw_missing_source_a.length - 1];
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
  const transaction = db.transaction(["pw_images"], "readwrite");
  const objectStore = transaction.objectStore("pw_images");
  let pw_data = await objectStore.get(microDataValue);
  let pw_idb_a = pw_data.split(',');
  let pw_nidb = '';
  for (let i = 0; i < pw_idb_a.length; i++) {
    if (pw_idb_a[i] === pw_missing_source) {
      continue;
    }
    pw_nidb = pw_nidb + pw_idb_a[i];
    if (i !== pw_idb_a.length - 1) {
      pw_nidb = pw_nidb + ',';
    }
  }
  objectStore.add({ id: pw_product_identifier, images: pw_nidb });
}

function getImageName(pw_current_img)
{
  if (pw_current_img === null) {
    return null;
  }
  let pw_current_img_a = pw_current_img.split('/');
  let result = pw_current_img_a.pop();
  return result.split('?')[0];
}

function firstImageReturn() {
  if (pw_r_t_d === '1' && pw_is_running === false && isMobile() === false) {
    for (let i = 0; i < pw_global_products.length; i++) {
      const pw_product = pw_global_products[i];
      const pw_product_element = document.querySelector('[data-micro-identifier="' + pw_product.id + '"]');
      if (pw_product_element) {
        const pw_img = pw_product_element.querySelector('img[data-micro], img[data-micro-image]');
        if (pw_img && pw_product.images.length > 1) {
          let pw_initial_image = pw_img.getAttribute('data-src');
          if (pw_initial_image != null && pw_initial_image != pw_img.src) {
            pw_img.src = pw_initial_image;
          }
        }
      }
    }
  }
}
function isMobile()
{
  return screen.width < 768 && (pw_a_t === 'all' || pw_a_t === 'mobile');
}

function isPc() {
  return pw_a_t === 'all' || pw_a_t === 'pc';
}

function isCircles() {
  return pw_m_i === 'circles';
}

function isNumbers() {
  return pw_m_i === 'numbers';
}

function clearIndexedDB() {
  indexedDB.deleteDatabase('pw_db');
}

async function setupIndexedDB() {
  return new Promise((resolve, reject) => {
    const dbName = "pw_db";
    const request = indexedDB.open(dbName, 1);

    request.onupgradeneeded = function(event) {
      const db = event.target.result;
      const objectStore = db.createObjectStore("pw_images", { keyPath: "id" });
      objectStore.createIndex("images", "images", { unique: false });
    };

    request.onsuccess = function(event) {
      const db = event.target.result;
      resolve(db);
    };
  });
}
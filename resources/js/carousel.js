const pw_carousel_settings = document.getElementById('preview-looper-settings');
let pw_infinite_repeat = pw_carousel_settings.getAttribute('data-infinite-repeat');
let pw_return_to_default = pw_carousel_settings.getAttribute('data-return-to-default');
let pw_show_time = pw_carousel_settings.getAttribute('data-show-time');
let pw_image_prefix;
if (pw_infinite_repeat === null) {
  pw_infinite_repeat = '0';
}
if (pw_return_to_default === null) {
  pw_return_to_default = '0';
}
if (pw_show_time === null) {
  pw_show_time = 1000;
} else {
  pw_show_time = parseInt(pw_show_time);
}

let pw_global_products = [];
let pw_running_interval;

const pw_project_id = extractProjectId();
let pw_guid_string = '';
const pw_products = [];
let pw_products_response;
const pw_elements = document.getElementsByClassName('p');
(async () => {
  for (let i = 0; i < pw_elements.length; i++) {
    const pw_element = pw_elements[i];
    const microDataValue = pw_element.getAttribute('data-micro-identifier');
    if (sessionStorage.getItem('pw_' + microDataValue) === null) {
      pw_guid_string = pw_guid_string + microDataValue + '|';
    }
  }
  if (pw_guid_string === '') {
    await emptyPromise().then(
      (response) => {
        initPreviewImages(pw_elements, response);
      }
    );
  } else {
    await sendGetRequest(pw_project_id, pw_guid_string).then(
      (response) => {
        initPreviewImages(pw_elements, response);
      }
    );
  }
})();

  function removeDuplicates(arr) {
    return Array.from(new Set(arr));
  }

  function stopLooping(pw_element) {
    clearInterval(pw_running_interval);
    if (pw_return_to_default === '1' && pw_image_prefix) {
      const pw_product_element = pw_element.target;
      const pw_img = pw_product_element.querySelector('img');
      const pw_id = pw_product_element.getAttribute('data-micro-identifier');
      const pw_product = getProduct(pw_id);
      if (pw_product && pw_product.images.length > 1) {
        let pw_image_name_default_array = pw_product.images[0].split('/');
        pw_img.src = pw_image_prefix + pw_image_name_default_array[pw_image_name_default_array.length - 1];
      }
    }
  }

  function getIndex(pw_product, pw_current_img) {
    for (let i = 0; i < pw_product.images.length; i++) {
      let pw_href_value_array = pw_product.images[i].split('/');
      let value = pw_href_value_array[pw_href_value_array.length - 1]
      if (pw_current_img === value) {
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
    const pw_product_element = pw_element.target;
    const pw_id = pw_product_element.getAttribute('data-micro-identifier');
    const pw_img = pw_product_element.querySelector('img');
    const pw_product = getProduct(pw_id);
    let pw_current_img = pw_img.src;
    pw_image_prefix = pw_current_img;
    let pw_current_img_array = pw_current_img.split('/');
    pw_current_img = pw_current_img_array[pw_current_img_array.length - 1];
    pw_image_prefix = pw_image_prefix.substring(0, pw_image_prefix.length - pw_current_img.length);
    let pw_index = getIndex(pw_product, pw_current_img);
    const pw_interval_iD = setInterval(() => {
      pw_index = (pw_index + 1) % pw_product.images.length;
      if (pw_product.images.length === 0) {
        return;
      }
      pw_image_name = pw_product.images[pw_index];
      if (pw_image_name === undefined) {
        return;
      }
      let pw_image_name_array = pw_image_name.split('/');
      pw_image_name = pw_image_name_array[pw_image_name_array.length - 1];

      if (pw_infinite_repeat === '0' && pw_index === pw_product.images.length ) {
        if (pw_return_to_default === '1') {
          let pw_image_name_default = pw_product.images[0];
          if (pw_image_name_default === undefined) {
            return;
          }
          let pw_image_name_default_array = pw_image_name_default.split('/');
          pw_image_name_default = pw_image_name_default_array[pw_image_name_default_array.length - 1];
          pw_img.src = pw_image_prefix + pw_image_name_default;
        }
        stopLooping(pw_element);
      } else {
        pw_img.src = pw_image_prefix + pw_image_name;
      }
    }, pw_show_time);
    pw_running_interval = pw_interval_iD;
  };

  var pw_leave = function(pw_element) {
    stopLooping(pw_element);
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
      const pw_img = pw_product_element.querySelector('img');
      const pw_product = getProduct(pw_id);
      let pw_current_img = pw_img.src;
      pw_image_prefix = pw_current_img;
      let pw_current_img_array = pw_current_img.split('/');
      pw_current_img = pw_current_img_array[pw_current_img_array.length - 1];
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
      let pw_image_name_array = pw_image_name.split('/');
      pw_image_name = pw_image_name_array[pw_image_name_array.length - 1];
      pw_img.src = pw_image_prefix + pw_image_name;

      const pw_image = pw_element.srcElement;
      const pw_link = pw_image.parentElement;
      const pw_svgs = pw_link.querySelectorAll('svg');
      pw_svgs.forEach((pw_svg, i) => {
        if (pw_init_index === i) {
          pw_svg.classList.remove('circle');
          pw_svg.classList.add('empty-circle');
        }
        if (pw_index === i) {
          pw_svg.classList.remove('empty-circle');
          pw_svg.classList.add('circle');
        }
      });
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

  function sendGetRequest(pw_project_id, pw_guid_string) {
    return new Promise((resolve, reject) => {
      let pw_url = 'https://slabihoud.cz/images/' + pw_project_id + '/' + pw_guid_string;
      const pw_xhr = new XMLHttpRequest();
      pw_xhr.open('GET', pw_url, true);
      pw_xhr.onreadystatechange = function () {
        if (pw_xhr.readyState === 4) {
          if (pw_xhr.status === 200) {
            let pw_response = parseJSONToPwProductsResponse(pw_xhr.responseText);
            resolve(pw_response);
          } else {
            console.error('Error:', pw_xhr.status, pw_xhr.statusText);
            reject(new Error('XHR request failed'));
          }
        }
      };
      pw_xhr.send();
    });
  }

  function emptyPromise() {
    return new Promise((resolve) => {
      resolve('');
    });
  }

  function extractProjectId() {
    let pw_project_id = getShoptetDataLayer('projectId')
    return pw_project_id;
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
    for (let i = 0; i < pw_elements.length; i++) {
      const pw_element = pw_elements[i];
      const microDataValue = pw_element.getAttribute('data-micro-identifier');
      if (sessionStorage.getItem('pw_' + microDataValue) === null && response !== '' && response[microDataValue] !== undefined) {
        sessionStorage.setItem('pw_' + microDataValue, response[microDataValue]);
      }
      let pw_images = sessionStorage.getItem('pw_' + microDataValue).split(',');
      pw_images = removeDuplicates(pw_images);
    
      pw_products.push({ id: microDataValue, images: pw_images});
    
      pw_element.addEventListener('mouseenter', pw_enter, false);
      pw_element.addEventListener('mouseleave', pw_leave, false);
    
      const pw_image = pw_element.querySelector('img');
      pw_image.addEventListener('touchstart', handleTouchStart, false);
      pw_image.addEventListener('touchmove', handleTouchMove, false);
        if (pw_images && pw_images.length > 0 && screen.width < 768) {
          pw_image.classList.add("overlay-on");
          let pw_icon = document.createElement('div');
          pw_image.after(pw_icon);
          pw_icon.classList.add('overlay-container');
          let pw_inner_html = '';
          for (let i = 0; i < pw_images.length; i++) {
            if (i === 0) {
              pw_inner_html = pw_inner_html + "<svg width='10' height='10' class='circle'><circle cx='5' cy='5' r='4'/></svg>";
            } else {
              pw_inner_html = pw_inner_html + "<svg width='10' height='10' class='empty-circle'><circle cx='5' cy='5' r='4'/></svg>";
            }
          }
          pw_icon.innerHTML = pw_inner_html;
        }
    }
    pw_global_products = pw_products;
  }
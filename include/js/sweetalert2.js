/*!
 * sweetalert2 v6.6.5
 * Released under the MIT License.
 */
// (function (global, factory) {
// 	typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
// 	typeof define === 'function' && define.amd ? define(factory) :
// 	(global.Sweetalert2 = factory());
// }(this, (function () { 'use strict';

// var defaultParams = {
//   title: '',
//   titleText: '',
//   text: '',
//   html: '',
//   type: null,
//   customClass: '',
//   target: 'body',
//   animation: true,
//   allowOutsideClick: true,
//   allowEscapeKey: true,
//   allowEnterKey: true,
//   showConfirmButton: true,
//   showCancelButton: false,
//   preConfirm: null,
//   confirmButtonText: 'OK',
//   confirmButtonColor: '#3085d6',
//   confirmButtonClass: null,
//   cancelButtonText: 'Cancel',
//   cancelButtonColor: '#aaa',
//   cancelButtonClass: null,
//   buttonsStyling: true,
//   reverseButtons: false,
//   focusCancel: false,
//   showCloseButton: false,
//   showLoaderOnConfirm: false,
//   imageUrl: null,
//   imageWidth: null,
//   imageHeight: null,
//   imageClass: null,
//   timer: null,
//   width: 500,
//   padding: 20,
//   background: '#fff',
//   input: null,
//   inputPlaceholder: '',
//   inputValue: '',
//   inputOptions: {},
//   inputAutoTrim: true,
//   inputClass: null,
//   inputAttributes: {},
//   inputValidator: null,
//   progressSteps: [],
//   currentProgressStep: null,
//   progressStepsDistance: '40px',
//   onOpen: null,
//   onClose: null,
//   useRejections: true
// };

// var swalPrefix = 'swal2-';

// var prefix = function prefix(items) {
//   var result = {};
//   for (var i in items) {
//     result[items[i]] = swalPrefix + items[i];
//   }
//   return result;
// };

// var swalClasses = prefix(['container', 'shown', 'iosfix', 'modal', 'overlay', 'fade', 'show', 'hide', 'noanimation', 'close', 'title', 'content', 'buttonswrapper', 'confirm', 'cancel', 'icon', 'image', 'input', 'file', 'range', 'select', 'radio', 'checkbox', 'textarea', 'inputerror', 'validationerror', 'progresssteps', 'activeprogressstep', 'progresscircle', 'progressline', 'loading', 'styled']);

// var iconTypes = prefix(['success', 'warning', 'info', 'question', 'error']);

// /*
//  * Set hover, active and focus-states for buttons (source: http://www.sitepoint.com/javascript-generate-lighter-darker-color)
//  */
// var colorLuminance = function colorLuminance(hex, lum) {
//   // Validate hex string
//   hex = String(hex).replace(/[^0-9a-f]/gi, '');
//   if (hex.length < 6) {
//     hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
//   }
//   lum = lum || 0;

//   // Convert to decimal and change luminosity
//   var rgb = '#';
//   for (var i = 0; i < 3; i++) {
//     var c = parseInt(hex.substr(i * 2, 2), 16);
//     c = Math.round(Math.min(Math.max(0, c + c * lum), 255)).toString(16);
//     rgb += ('00' + c).substr(c.length);
//   }

//   return rgb;
// };

// var uniqueArray = function uniqueArray(arr) {
//   var result = [];
//   for (var i in arr) {
//     if (result.indexOf(arr[i]) === -1) {
//       result.push(arr[i]);
//     }
//   }
//   return result;
// };

// /* global MouseEvent */

// // Remember state in cases where opening and handling a modal will fiddle with it.
// var states = {
//   previousWindowKeyDown: null,
//   previousActiveElement: null,
//   previousBodyPadding: null

//   /*
//    * Add modal + overlay to DOM
//    */
// };var init = function init(params) {
//   if (typeof document === 'undefined') {
//     console.error('SweetAlert2 requires document to initialize');
//     return;
//   }

//   var container = document.createElement('div');
//   container.className = swalClasses.container;
//   container.innerHTML = sweetHTML;

//   var targetElement = document.querySelector(params.target);
//   if (!targetElement) {
//     console.warn('SweetAlert2: Can\'t find the target "' + params.target + '"');
//     targetElement = document.body;
//   }
//   targetElement.appendChild(container);

//   var modal = getModal();
//   var input = getChildByClass(modal, swalClasses.input);
//   var file = getChildByClass(modal, swalClasses.file);
//   var range = modal.querySelector('.' + swalClasses.range + ' input');
//   var rangeOutput = modal.querySelector('.' + swalClasses.range + ' output');
//   var select = getChildByClass(modal, swalClasses.select);
//   var checkbox = modal.querySelector('.' + swalClasses.checkbox + ' input');
//   var textarea = getChildByClass(modal, swalClasses.textarea);

//   input.oninput = function () {
//     sweetAlert.resetValidationError();
//   };

//   input.onkeydown = function (event) {
//     setTimeout(function () {
//       if (event.keyCode === 13 && params.allowEnterKey) {
//         event.stopPropagation();
//         sweetAlert.clickConfirm();
//       }
//     }, 0);
//   };

//   file.onchange = function () {
//     sweetAlert.resetValidationError();
//   };

//   range.oninput = function () {
//     sweetAlert.resetValidationError();
//     rangeOutput.value = range.value;
//   };

//   range.onchange = function () {
//     sweetAlert.resetValidationError();
//     range.previousSibling.value = range.value;
//   };

//   select.onchange = function () {
//     sweetAlert.resetValidationError();
//   };

//   checkbox.onchange = function () {
//     sweetAlert.resetValidationError();
//   };

//   textarea.oninput = function () {
//     sweetAlert.resetValidationError();
//   };

//   return modal;
// };

// /*
//  * Manipulate DOM
//  */

// var sweetHTML = ('\n <div role="dialog" aria-labelledby="' + swalClasses.title + '" aria-describedby="' + swalClasses.content + '" class="' + swalClasses.modal + '" tabindex="-1">\n   <ul class="' + swalClasses.progresssteps + '"></ul>\n   <div class="' + swalClasses.icon + ' ' + iconTypes.error + '">\n     <span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span>\n   </div>\n   <div class="' + swalClasses.icon + ' ' + iconTypes.question + '">?</div>\n   <div class="' + swalClasses.icon + ' ' + iconTypes.warning + '">!</div>\n   <div class="' + swalClasses.icon + ' ' + iconTypes.info + '">i</div>\n   <div class="' + swalClasses.icon + ' ' + iconTypes.success + '">\n     <div class="swal2-success-circular-line-left"></div>\n     <span class="swal2-success-line-tip"></span> <span class="swal2-success-line-long"></span>\n     <div class="swal2-success-ring"></div> <div class="swal2-success-fix"></div>\n     <div class="swal2-success-circular-line-right"></div>\n   </div>\n   <img class="' + swalClasses.image + '">\n   <h2 class="' + swalClasses.title + '" id="' + swalClasses.title + '"></h2>\n   <div id="' + swalClasses.content + '" class="' + swalClasses.content + '"></div>\n   <input class="' + swalClasses.input + '">\n   <input type="file" class="' + swalClasses.file + '">\n   <div class="' + swalClasses.range + '">\n     <output></output>\n     <input type="range">\n   </div>\n   <select class="' + swalClasses.select + '"></select>\n   <div class="' + swalClasses.radio + '"></div>\n   <label for="' + swalClasses.checkbox + '" class="' + swalClasses.checkbox + '">\n     <input type="checkbox">\n   </label>\n   <textarea class="' + swalClasses.textarea + '"></textarea>\n   <div class="' + swalClasses.validationerror + '"></div>\n   <div class="' + swalClasses.buttonswrapper + '">\n     <button type="button" class="' + swalClasses.confirm + '">OK</button>\n     <button type="button" class="' + swalClasses.cancel + '">Cancel</button>\n   </div>\n   <button type="button" class="' + swalClasses.close + '" aria-label="Close this dialog">&times;</button>\n </div>\n').replace(/(^|\n)\s*/g, '');

// var getContainer = function getContainer() {
//   return document.body.querySelector('.' + swalClasses.container);
// };

// var getModal = function getModal() {
//   return getContainer() ? getContainer().querySelector('.' + swalClasses.modal) : null;
// };

// var getIcons = function getIcons() {
//   var modal = getModal();
//   return modal.querySelectorAll('.' + swalClasses.icon);
// };

// var elementByClass = function elementByClass(className) {
//   return getContainer() ? getContainer().querySelector('.' + className) : null;
// };

// var getTitle = function getTitle() {
//   return elementByClass(swalClasses.title);
// };

// var getContent = function getContent() {
//   return elementByClass(swalClasses.content);
// };

// var getImage = function getImage() {
//   return elementByClass(swalClasses.image);
// };

// var getButtonsWrapper = function getButtonsWrapper() {
//   return elementByClass(swalClasses.buttonswrapper);
// };

// var getProgressSteps = function getProgressSteps() {
//   return elementByClass(swalClasses.progresssteps);
// };

// var getValidationError = function getValidationError() {
//   return elementByClass(swalClasses.validationerror);
// };

// var getConfirmButton = function getConfirmButton() {
//   return elementByClass(swalClasses.confirm);
// };

// var getCancelButton = function getCancelButton() {
//   return elementByClass(swalClasses.cancel);
// };

// var getCloseButton = function getCloseButton() {
//   return elementByClass(swalClasses.close);
// };

// var getFocusableElements = function getFocusableElements(focusCancel) {
//   var buttons = [getConfirmButton(), getCancelButton()];
//   if (focusCancel) {
//     buttons.reverse();
//   }
//   var focusableElements = buttons.concat(Array.prototype.slice.call(getModal().querySelectorAll('button, input:not([type=hidden]), textarea, select, a, *[tabindex]:not([tabindex="-1"])')));
//   return uniqueArray(focusableElements);
// };

// var hasClass = function hasClass(elem, className) {
//   if (elem.classList) {
//     return elem.classList.contains(className);
//   }
//   return false;
// };

// var focusInput = function focusInput(input) {
//   input.focus

//   // place cursor at end of text in text input
//   ();if (input.type !== 'file') {
//     // http://stackoverflow.com/a/2345915/1331425
//     var val = input.value;
//     input.value = '';
//     input.value = val;
//   }
// };

// var addClass = function addClass(elem, className) {
//   if (!elem || !className) {
//     return;
//   }
//   var classes = className.split(/\s+/).filter(Boolean);
//   classes.forEach(function (className) {
//     elem.classList.add(className);
//   });
// };

// var removeClass = function removeClass(elem, className) {
//   if (!elem || !className) {
//     return;
//   }
//   var classes = className.split(/\s+/).filter(Boolean);
//   classes.forEach(function (className) {
//     elem.classList.remove(className);
//   });
// };

// var getChildByClass = function getChildByClass(elem, className) {
//   for (var i = 0; i < elem.childNodes.length; i++) {
//     if (hasClass(elem.childNodes[i], className)) {
//       return elem.childNodes[i];
//     }
//   }
// };

// var show = function show(elem, display) {
//   if (!display) {
//     display = 'block';
//   }
//   elem.style.opacity = '';
//   elem.style.display = display;
// };

// var hide = function hide(elem) {
//   elem.style.opacity = '';
//   elem.style.display = 'none';
// };

// var empty = function empty(elem) {
//   while (elem.firstChild) {
//     elem.removeChild(elem.firstChild);
//   }
// };

// // borrowed from jqeury $(elem).is(':visible') implementation
// var isVisible = function isVisible(elem) {
//   return elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length;
// };

// var removeStyleProperty = function removeStyleProperty(elem, property) {
//   if (elem.style.removeProperty) {
//     elem.style.removeProperty(property);
//   } else {
//     elem.style.removeAttribute(property);
//   }
// };

// var fireClick = function fireClick(node) {
//   if (!isVisible(node)) {
//     return false;
//   }

//   // Taken from http://www.nonobtrusive.com/2011/11/29/programatically-fire-crossbrowser-click-event-with-javascript/
//   // Then fixed for today's Chrome browser.
//   if (typeof MouseEvent === 'function') {
//     // Up-to-date approach
//     var mevt = new MouseEvent('click', {
//       view: window,
//       bubbles: false,
//       cancelable: true
//     });
//     node.dispatchEvent(mevt);
//   } else if (document.createEvent) {
//     // Fallback
//     var evt = document.createEvent('MouseEvents');
//     evt.initEvent('click', false, false);
//     node.dispatchEvent(evt);
//   } else if (document.createEventObject) {
//     node.fireEvent('onclick');
//   } else if (typeof node.onclick === 'function') {
//     node.onclick();
//   }
// };

// var animationEndEvent = function () {
//   var testEl = document.createElement('div');
//   var transEndEventNames = {
//     'WebkitAnimation': 'webkitAnimationEnd',
//     'OAnimation': 'oAnimationEnd oanimationend',
//     'msAnimation': 'MSAnimationEnd',
//     'animation': 'animationend'
//   };
//   for (var i in transEndEventNames) {
//     if (transEndEventNames.hasOwnProperty(i) && testEl.style[i] !== undefined) {
//       return transEndEventNames[i];
//     }
//   }

//   return false;
// }

// // Reset previous window keydown handler and focued element
// ();var resetPrevState = function resetPrevState() {
//   window.onkeydown = states.previousWindowKeyDown;
//   if (states.previousActiveElement && states.previousActiveElement.focus) {
//     var x = window.scrollX;
//     var y = window.scrollY;
//     states.previousActiveElement.focus();
//     if (x && y) {
//       // IE has no scrollX/scrollY support
//       window.scrollTo(x, y);
//     }
//   }
// };

// // Measure width of scrollbar
// // https://github.com/twbs/bootstrap/blob/master/js/modal.js#L279-L286
// var measureScrollbar = function measureScrollbar() {
//   var supportsTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints;
//   if (supportsTouch) {
//     return 0;
//   }
//   var scrollDiv = document.createElement('div');
//   scrollDiv.style.width = '50px';
//   scrollDiv.style.height = '50px';
//   scrollDiv.style.overflow = 'scroll';
//   document.body.appendChild(scrollDiv);
//   var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
//   document.body.removeChild(scrollDiv);
//   return scrollbarWidth;
// };

// // JavaScript Debounce Function
// // Simplivied version of https://davidwalsh.name/javascript-debounce-function
// var debounce = function debounce(func, wait) {
//   var timeout = void 0;
//   return function () {
//     var later = function later() {
//       timeout = null;
//       func();
//     };
//     clearTimeout(timeout);
//     timeout = setTimeout(later, wait);
//   };
// };

// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) {
//   return typeof obj;
// } : function (obj) {
//   return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
// };





















// var _extends = Object.assign || function (target) {
//   for (var i = 1; i < arguments.length; i++) {
//     var source = arguments[i];

//     for (var key in source) {
//       if (Object.prototype.hasOwnProperty.call(source, key)) {
//         target[key] = source[key];
//       }
//     }
//   }

//   return target;
// };

// var modalParams = _extends({}, defaultParams);
// var queue = [];
// var swal2Observer = void 0;

// /*
//  * Set type, text and actions on modal
//  */
// var setParameters = function setParameters(params) {
//   var modal = getModal() || init(params);

//   for (var param in params) {
//     if (!defaultParams.hasOwnProperty(param) && param !== 'extraParams') {
//       console.warn('SweetAlert2: Unknown parameter "' + param + '"');
//     }
//   }

//   // Set modal width
//   modal.style.width = typeof params.width === 'number' ? params.width + 'px' : params.width;

//   modal.style.padding = params.padding + 'px';
//   modal.style.background = params.background;
//   var successIconParts = modal.querySelectorAll('[class^=swal2-success-circular-line], .swal2-success-fix');
//   for (var i = 0; i < successIconParts.length; i++) {
//     successIconParts[i].style.background = params.background;
//   }

//   var title = getTitle();
//   var content = getContent();
//   var buttonsWrapper = getButtonsWrapper();
//   var confirmButton = getConfirmButton();
//   var cancelButton = getCancelButton();
//   var closeButton = getCloseButton

//   // Title
//   ();if (params.titleText) {
//     title.innerText = params.titleText;
//   } else {
//     title.innerHTML = params.title.split('\n').join('<br>');
//   }

//   // Content
//   if (params.text || params.html) {
//     if (_typeof(params.html) === 'object') {
//       content.innerHTML = '';
//       if (0 in params.html) {
//         for (var _i = 0; _i in params.html; _i++) {
//           content.appendChild(params.html[_i].cloneNode(true));
//         }
//       } else {
//         content.appendChild(params.html.cloneNode(true));
//       }
//     } else if (params.html) {
//       content.innerHTML = params.html;
//     } else if (params.text) {
//       content.textContent = params.text;
//     }
//     show(content);
//   } else {
//     hide(content);
//   }

//   // Close button
//   if (params.showCloseButton) {
//     show(closeButton);
//   } else {
//     hide(closeButton);
//   }

//   // Custom Class
//   modal.className = swalClasses.modal;
//   if (params.customClass) {
//     addClass(modal, params.customClass);
//   }

//   // Progress steps
//   var progressStepsContainer = getProgressSteps();
//   var currentProgressStep = parseInt(params.currentProgressStep === null ? sweetAlert.getQueueStep() : params.currentProgressStep, 10);
//   if (params.progressSteps.length) {
//     show(progressStepsContainer);
//     empty(progressStepsContainer);
//     if (currentProgressStep >= params.progressSteps.length) {
//       console.warn('SweetAlert2: Invalid currentProgressStep parameter, it should be less than progressSteps.length ' + '(currentProgressStep like JS arrays starts from 0)');
//     }
//     params.progressSteps.forEach(function (step, index) {
//       var circle = document.createElement('li');
//       addClass(circle, swalClasses.progresscircle);
//       circle.innerHTML = step;
//       if (index === currentProgressStep) {
//         addClass(circle, swalClasses.activeprogressstep);
//       }
//       progressStepsContainer.appendChild(circle);
//       if (index !== params.progressSteps.length - 1) {
//         var line = document.createElement('li');
//         addClass(line, swalClasses.progressline);
//         line.style.width = params.progressStepsDistance;
//         progressStepsContainer.appendChild(line);
//       }
//     });
//   } else {
//     hide(progressStepsContainer);
//   }

//   // Icon
//   var icons = getIcons();
//   for (var _i2 = 0; _i2 < icons.length; _i2++) {
//     hide(icons[_i2]);
//   }
//   if (params.type) {
//     var validType = false;
//     for (var iconType in iconTypes) {
//       if (params.type === iconType) {
//         validType = true;
//         break;
//       }
//     }
//     if (!validType) {
//       console.error('SweetAlert2: Unknown alert type: ' + params.type);
//       return false;
//     }
//     var icon = modal.querySelector('.' + swalClasses.icon + '.' + iconTypes[params.type]);
//     show(icon

//     // Animate icon
//     );if (params.animation) {
//       switch (params.type) {
//         case 'success':
//           addClass(icon, 'swal2-animate-success-icon');
//           addClass(icon.querySelector('.swal2-success-line-tip'), 'swal2-animate-success-line-tip');
//           addClass(icon.querySelector('.swal2-success-line-long'), 'swal2-animate-success-line-long');
//           break;
//         case 'error':
//           addClass(icon, 'swal2-animate-error-icon');
//           addClass(icon.querySelector('.swal2-x-mark'), 'swal2-animate-x-mark');
//           break;
//         default:
//           break;
//       }
//     }
//   }

//   // Custom image
//   var image = getImage();
//   if (params.imageUrl) {
//     image.setAttribute('src', params.imageUrl);
//     show(image);

//     if (params.imageWidth) {
//       image.setAttribute('width', params.imageWidth);
//     } else {
//       image.removeAttribute('width');
//     }

//     if (params.imageHeight) {
//       image.setAttribute('height', params.imageHeight);
//     } else {
//       image.removeAttribute('height');
//     }

//     image.className = swalClasses.image;
//     if (params.imageClass) {
//       addClass(image, params.imageClass);
//     }
//   } else {
//     hide(image);
//   }

//   // Cancel button
//   if (params.showCancelButton) {
//     cancelButton.style.display = 'inline-block';
//   } else {
//     hide(cancelButton);
//   }

//   // Confirm button
//   if (params.showConfirmButton) {
//     removeStyleProperty(confirmButton, 'display');
//   } else {
//     hide(confirmButton);
//   }

//   // Buttons wrapper
//   if (!params.showConfirmButton && !params.showCancelButton) {
//     hide(buttonsWrapper);
//   } else {
//     show(buttonsWrapper);
//   }

//   // Edit text on cancel and confirm buttons
//   confirmButton.innerHTML = params.confirmButtonText;
//   cancelButton.innerHTML = params.cancelButtonText;

//   // Set buttons to selected background colors
//   if (params.buttonsStyling) {
//     confirmButton.style.backgroundColor = params.confirmButtonColor;
//     cancelButton.style.backgroundColor = params.cancelButtonColor;
//   }

//   // Add buttons custom classes
//   confirmButton.className = swalClasses.confirm;
//   addClass(confirmButton, params.confirmButtonClass);
//   cancelButton.className = swalClasses.cancel;
//   addClass(cancelButton, params.cancelButtonClass

//   // Buttons styling
//   );if (params.buttonsStyling) {
//     addClass(confirmButton, swalClasses.styled);
//     addClass(cancelButton, swalClasses.styled);
//   } else {
//     removeClass(confirmButton, swalClasses.styled);
//     removeClass(cancelButton, swalClasses.styled);

//     confirmButton.style.backgroundColor = confirmButton.style.borderLeftColor = confirmButton.style.borderRightColor = '';
//     cancelButton.style.backgroundColor = cancelButton.style.borderLeftColor = cancelButton.style.borderRightColor = '';
//   }

//   // CSS animation
//   if (params.animation === true) {
//     removeClass(modal, swalClasses.noanimation);
//   } else {
//     addClass(modal, swalClasses.noanimation);
//   }
// };

// /*
//  * Animations
//  */
// var openModal = function openModal(animation, onComplete) {
//   var container = getContainer();
//   var modal = getModal();

//   if (animation) {
//     addClass(modal, swalClasses.show);
//     addClass(container, swalClasses.fade);
//     removeClass(modal, swalClasses.hide);
//   } else {
//     removeClass(modal, swalClasses.fade);
//   }
//   show(modal

//   // scrolling is 'hidden' until animation is done, after that 'auto'
//   );container.style.overflowY = 'hidden';
//   if (animationEndEvent && !hasClass(modal, swalClasses.noanimation)) {
//     modal.addEventListener(animationEndEvent, function swalCloseEventFinished() {
//       modal.removeEventListener(animationEndEvent, swalCloseEventFinished);
//       container.style.overflowY = 'auto';
//     });
//   } else {
//     container.style.overflowY = 'auto';
//   }

//   addClass(document.documentElement, swalClasses.shown);
//   addClass(document.body, swalClasses.shown);
//   addClass(container, swalClasses.shown);
//   fixScrollbar();
//   iOSfix();
//   states.previousActiveElement = document.activeElement;
//   if (onComplete !== null && typeof onComplete === 'function') {
//     setTimeout(function () {
//       onComplete(modal);
//     });
//   }
// };

// var fixScrollbar = function fixScrollbar() {
//   // for queues, do not do this more than once
//   if (states.previousBodyPadding !== null) {
//     return;
//   }
//   // if the body has overflow
//   if (document.body.scrollHeight > window.innerHeight) {
//     // add padding so the content doesn't shift after removal of scrollbar
//     states.previousBodyPadding = document.body.style.paddingRight;
//     document.body.style.paddingRight = measureScrollbar() + 'px';
//   }
// };

// var undoScrollbar = function undoScrollbar() {
//   if (states.previousBodyPadding !== null) {
//     document.body.style.paddingRight = states.previousBodyPadding;
//     states.previousBodyPadding = null;
//   }
// };

// // Fix iOS scrolling http://stackoverflow.com/q/39626302/1331425
// var iOSfix = function iOSfix() {
//   var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
//   if (iOS && !hasClass(document.body, swalClasses.iosfix)) {
//     var offset = document.body.scrollTop;
//     document.body.style.top = offset * -1 + 'px';
//     addClass(document.body, swalClasses.iosfix);
//   }
// };

// var undoIOSfix = function undoIOSfix() {
//   if (hasClass(document.body, swalClasses.iosfix)) {
//     var offset = parseInt(document.body.style.top, 10);
//     removeClass(document.body, swalClasses.iosfix);
//     document.body.style.top = '';
//     document.body.scrollTop = offset * -1;
//   }
// };

// // SweetAlert entry point
// var sweetAlert = function sweetAlert() {
//   for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
//     args[_key] = arguments[_key];
//   }

//   if (args[0] === undefined) {
//     console.error('SweetAlert2 expects at least 1 attribute!');
//     return false;
//   }

//   var params = _extends({}, modalParams);

//   switch (_typeof(args[0])) {
//     case 'string':
//       params.title = args[0];
//       params.html = args[1];
//       params.type = args[2];

//       break;

//     case 'object':
//       _extends(params, args[0]);
//       params.extraParams = args[0].extraParams;

//       if (params.input === 'email' && params.inputValidator === null) {
//         params.inputValidator = function (email) {
//           return new Promise(function (resolve, reject) {
//             var emailRegex = /^[a-zA-Z0-9.+_-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
//             if (emailRegex.test(email)) {
//               resolve();
//             } else {
//               reject('Invalid email address');
//             }
//           });
//         };
//       }

//       if (params.input === 'url' && params.inputValidator === null) {
//         params.inputValidator = function (url) {
//           return new Promise(function (resolve, reject) {
//             // taken from https://stackoverflow.com/a/3809435/1331425
//             var urlRegex = /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_+.~#?&//=]*)$/;
//             if (urlRegex.test(url)) {
//               resolve();
//             } else {
//               reject('Invalid URL');
//             }
//           });
//         };
//       }
//       break;

//     default:
//       console.error('SweetAlert2: Unexpected type of argument! Expected "string" or "object", got ' + _typeof(args[0]));
//       return false;
//   }

//   setParameters(params);

//   var container = getContainer();
//   var modal = getModal();

//   return new Promise(function (resolve, reject) {
//     // Close on timer
//     if (params.timer) {
//       modal.timeout = setTimeout(function () {
//         sweetAlert.closeModal(params.onClose);
//         if (params.useRejections) {
//           reject('timer');
//         } else {
//           resolve({ dismiss: 'timer' });
//         }
//       }, params.timer);
//     }

//     // Get input element by specified type or, if type isn't specified, by params.input
//     var getInput = function getInput(inputType) {
//       inputType = inputType || params.input;
//       if (!inputType) {
//         return null;
//       }
//       switch (inputType) {
//         case 'select':
//         case 'textarea':
//         case 'file':
//           return getChildByClass(modal, swalClasses[inputType]);
//         case 'checkbox':
//           return modal.querySelector('.' + swalClasses.checkbox + ' input');
//         case 'radio':
//           return modal.querySelector('.' + swalClasses.radio + ' input:checked') || modal.querySelector('.' + swalClasses.radio + ' input:first-child');
//         case 'range':
//           return modal.querySelector('.' + swalClasses.range + ' input');
//         default:
//           return getChildByClass(modal, swalClasses.input);
//       }
//     };

//     // Get the value of the modal input
//     var getInputValue = function getInputValue() {
//       var input = getInput();
//       if (!input) {
//         return null;
//       }
//       switch (params.input) {
//         case 'checkbox':
//           return input.checked ? 1 : 0;
//         case 'radio':
//           return input.checked ? input.value : null;
//         case 'file':
//           return input.files.length ? input.files[0] : null;
//         default:
//           return params.inputAutoTrim ? input.value.trim() : input.value;
//       }
//     };

//     // input autofocus
//     if (params.input) {
//       setTimeout(function () {
//         var input = getInput();
//         if (input) {
//           focusInput(input);
//         }
//       }, 0);
//     }

//     var confirm = function confirm(value) {
//       if (params.showLoaderOnConfirm) {
//         sweetAlert.showLoading();
//       }

//       if (params.preConfirm) {
//         params.preConfirm(value, params.extraParams).then(function (preConfirmValue) {
//           sweetAlert.closeModal(params.onClose);
//           resolve(preConfirmValue || value);
//         }, function (error) {
//           sweetAlert.hideLoading();
//           if (error) {
//             sweetAlert.showValidationError(error);
//           }
//         });
//       } else {
//         sweetAlert.closeModal(params.onClose);
//         if (params.useRejections) {
//           resolve(value);
//         } else {
//           resolve({ value: value });
//         }
//       }
//     };

//     // Mouse interactions
//     var onButtonEvent = function onButtonEvent(event) {
//       var e = event || window.event;
//       var target = e.target || e.srcElement;
//       var confirmButton = getConfirmButton();
//       var cancelButton = getCancelButton();
//       var targetedConfirm = confirmButton && (confirmButton === target || confirmButton.contains(target));
//       var targetedCancel = cancelButton && (cancelButton === target || cancelButton.contains(target));

//       switch (e.type) {
//         case 'mouseover':
//         case 'mouseup':
//           if (params.buttonsStyling) {
//             if (targetedConfirm) {
//               confirmButton.style.backgroundColor = colorLuminance(params.confirmButtonColor, -0.1);
//             } else if (targetedCancel) {
//               cancelButton.style.backgroundColor = colorLuminance(params.cancelButtonColor, -0.1);
//             }
//           }
//           break;
//         case 'mouseout':
//           if (params.buttonsStyling) {
//             if (targetedConfirm) {
//               confirmButton.style.backgroundColor = params.confirmButtonColor;
//             } else if (targetedCancel) {
//               cancelButton.style.backgroundColor = params.cancelButtonColor;
//             }
//           }
//           break;
//         case 'mousedown':
//           if (params.buttonsStyling) {
//             if (targetedConfirm) {
//               confirmButton.style.backgroundColor = colorLuminance(params.confirmButtonColor, -0.2);
//             } else if (targetedCancel) {
//               cancelButton.style.backgroundColor = colorLuminance(params.cancelButtonColor, -0.2);
//             }
//           }
//           break;
//         case 'click':
//           // Clicked 'confirm'
//           if (targetedConfirm && sweetAlert.isVisible()) {
//             sweetAlert.disableButtons();
//             if (params.input) {
//               var inputValue = getInputValue();

//               if (params.inputValidator) {
//                 sweetAlert.disableInput();
//                 params.inputValidator(inputValue, params.extraParams).then(function () {
//                   sweetAlert.enableButtons();
//                   sweetAlert.enableInput();
//                   confirm(inputValue);
//                 }, function (error) {
//                   sweetAlert.enableButtons();
//                   sweetAlert.enableInput();
//                   if (error) {
//                     sweetAlert.showValidationError(error);
//                   }
//                 });
//               } else {
//                 confirm(inputValue);
//               }
//             } else {
//               confirm(true);
//             }

//             // Clicked 'cancel'
//           } else if (targetedCancel && sweetAlert.isVisible()) {
//             sweetAlert.disableButtons();
//             sweetAlert.closeModal(params.onClose);
//             if (params.useRejections) {
//               reject('cancel');
//             } else {
//               resolve({ dismiss: 'cancel' });
//             }
//           }
//           break;
//         default:
//       }
//     };

//     var buttons = modal.querySelectorAll('button');
//     for (var i = 0; i < buttons.length; i++) {
//       buttons[i].onclick = onButtonEvent;
//       buttons[i].onmouseover = onButtonEvent;
//       buttons[i].onmouseout = onButtonEvent;
//       buttons[i].onmousedown = onButtonEvent;
//     }

//     // Closing modal by close button
//     getCloseButton().onclick = function () {
//       sweetAlert.closeModal(params.onClose);
//       if (params.useRejections) {
//         reject('close');
//       } else {
//         resolve({ dismiss: 'close' });
//       }
//     };

//     // Closing modal by overlay click
//     container.onclick = function (e) {
//       if (e.target !== container) {
//         return;
//       }
//       if (params.allowOutsideClick) {
//         sweetAlert.closeModal(params.onClose);
//         if (params.useRejections) {
//           reject('overlay');
//         } else {
//           resolve({ dismiss: 'overlay' });
//         }
//       }
//     };

//     var buttonsWrapper = getButtonsWrapper();
//     var confirmButton = getConfirmButton();
//     var cancelButton = getCancelButton

//     // Reverse buttons (Confirm on the right side)
//     ();if (params.reverseButtons) {
//       confirmButton.parentNode.insertBefore(cancelButton, confirmButton);
//     } else {
//       confirmButton.parentNode.insertBefore(confirmButton, cancelButton);
//     }

//     // Focus handling
//     var setFocus = function setFocus(index, increment) {
//       var focusableElements = getFocusableElements(params.focusCancel
//       // search for visible elements and select the next possible match
//       );for (var _i3 = 0; _i3 < focusableElements.length; _i3++) {
//         index = index + increment;

//         // rollover to first item
//         if (index === focusableElements.length) {
//           index = 0;

//           // go to last item
//         } else if (index === -1) {
//           index = focusableElements.length - 1;
//         }

//         // determine if element is visible
//         var el = focusableElements[index];
//         if (isVisible(el)) {
//           return el.focus();
//         }
//       }
//     };

//     var handleKeyDown = function handleKeyDown(event) {
//       var e = event || window.event;
//       var keyCode = e.keyCode || e.which;

//       if ([9, 13, 32, 27, 37, 38, 39, 40].indexOf(keyCode) === -1) {
//         // Don't do work on keys we don't care about.
//         return;
//       }

//       var targetElement = e.target || e.srcElement;

//       var focusableElements = getFocusableElements(params.focusCancel);
//       var btnIndex = -1; // Find the button - note, this is a nodelist, not an array.
//       for (var _i4 = 0; _i4 < focusableElements.length; _i4++) {
//         if (targetElement === focusableElements[_i4]) {
//           btnIndex = _i4;
//           break;
//         }
//       }

//       // TAB
//       if (keyCode === 9) {
//         if (!e.shiftKey) {
//           // Cycle to the next button
//           setFocus(btnIndex, 1);
//         } else {
//           // Cycle to the prev button
//           setFocus(btnIndex, -1);
//         }
//         e.stopPropagation();
//         e.preventDefault

//         // ARROWS - switch focus between buttons
//         ();
//       } else if (keyCode === 37 || keyCode === 38 || keyCode === 39 || keyCode === 40) {
//         // focus Cancel button if Confirm button is currently focused
//         if (document.activeElement === confirmButton && isVisible(cancelButton)) {
//           cancelButton.focus
//           // and vice versa
//           ();
//         } else if (document.activeElement === cancelButton && isVisible(confirmButton)) {
//           confirmButton.focus();
//         }

//         // ENTER/SPACE
//       } else if (keyCode === 13 || keyCode === 32) {
//         if (btnIndex === -1 && params.allowEnterKey) {
//           // ENTER/SPACE clicked outside of a button.
//           if (params.focusCancel) {
//             fireClick(cancelButton, e);
//           } else {
//             fireClick(confirmButton, e);
//           }
//           e.stopPropagation();
//           e.preventDefault();
//         }

//         // ESC
//       } else if (keyCode === 27 && params.allowEscapeKey === true) {
//         sweetAlert.closeModal(params.onClose);
//         if (params.useRejections) {
//           reject('esc');
//         } else {
//           resolve({ dismiss: 'esc' });
//         }
//       }
//     };

//     if (!window.onkeydown || window.onkeydown.toString() !== handleKeyDown.toString()) {
//       states.previousWindowKeyDown = window.onkeydown;
//       window.onkeydown = handleKeyDown;
//     }

//     // Loading state
//     if (params.buttonsStyling) {
//       confirmButton.style.borderLeftColor = params.confirmButtonColor;
//       confirmButton.style.borderRightColor = params.confirmButtonColor;
//     }

//     /**
//      * Show spinner instead of Confirm button and disable Cancel button
//      */
//     sweetAlert.hideLoading = sweetAlert.disableLoading = function () {
//       if (!params.showConfirmButton) {
//         hide(confirmButton);
//         if (!params.showCancelButton) {
//           hide(getButtonsWrapper());
//         }
//       }
//       removeClass(buttonsWrapper, swalClasses.loading);
//       removeClass(modal, swalClasses.loading);
//       confirmButton.disabled = false;
//       cancelButton.disabled = false;
//     };

//     sweetAlert.getTitle = function () {
//       return getTitle();
//     };
//     sweetAlert.getContent = function () {
//       return getContent();
//     };
//     sweetAlert.getInput = function () {
//       return getInput();
//     };
//     sweetAlert.getImage = function () {
//       return getImage();
//     };
//     sweetAlert.getButtonsWrapper = function () {
//       return getButtonsWrapper();
//     };
//     sweetAlert.getConfirmButton = function () {
//       return getConfirmButton();
//     };
//     sweetAlert.getCancelButton = function () {
//       return getCancelButton();
//     };

//     sweetAlert.enableButtons = function () {
//       confirmButton.disabled = false;
//       cancelButton.disabled = false;
//     };

//     sweetAlert.disableButtons = function () {
//       confirmButton.disabled = true;
//       cancelButton.disabled = true;
//     };

//     sweetAlert.enableConfirmButton = function () {
//       confirmButton.disabled = false;
//     };

//     sweetAlert.disableConfirmButton = function () {
//       confirmButton.disabled = true;
//     };

//     sweetAlert.enableInput = function () {
//       var input = getInput();
//       if (!input) {
//         return false;
//       }
//       if (input.type === 'radio') {
//         var radiosContainer = input.parentNode.parentNode;
//         var radios = radiosContainer.querySelectorAll('input');
//         for (var _i5 = 0; _i5 < radios.length; _i5++) {
//           radios[_i5].disabled = false;
//         }
//       } else {
//         input.disabled = false;
//       }
//     };

//     sweetAlert.disableInput = function () {
//       var input = getInput();
//       if (!input) {
//         return false;
//       }
//       if (input && input.type === 'radio') {
//         var radiosContainer = input.parentNode.parentNode;
//         var radios = radiosContainer.querySelectorAll('input');
//         for (var _i6 = 0; _i6 < radios.length; _i6++) {
//           radios[_i6].disabled = true;
//         }
//       } else {
//         input.disabled = true;
//       }
//     };

//     // Set modal min-height to disable scrolling inside the modal
//     sweetAlert.recalculateHeight = debounce(function () {
//       var modal = getModal();
//       if (!modal) {
//         return;
//       }
//       var prevState = modal.style.display;
//       modal.style.minHeight = '';
//       show(modal);
//       modal.style.minHeight = modal.scrollHeight + 1 + 'px';
//       modal.style.display = prevState;
//     }, 50

//     // Show block with validation error
//     );sweetAlert.showValidationError = function (error) {
//       var validationError = getValidationError();
//       validationError.innerHTML = error;
//       show(validationError);

//       var input = getInput();
//       if (input) {
//         focusInput(input);
//         addClass(input, swalClasses.inputerror);
//       }
//     };

//     // Hide block with validation error
//     sweetAlert.resetValidationError = function () {
//       var validationError = getValidationError();
//       hide(validationError);
//       sweetAlert.recalculateHeight();

//       var input = getInput();
//       if (input) {
//         removeClass(input, swalClasses.inputerror);
//       }
//     };

//     sweetAlert.getProgressSteps = function () {
//       return params.progressSteps;
//     };

//     sweetAlert.setProgressSteps = function (progressSteps) {
//       params.progressSteps = progressSteps;
//       setParameters(params);
//     };

//     sweetAlert.showProgressSteps = function () {
//       show(getProgressSteps());
//     };

//     sweetAlert.hideProgressSteps = function () {
//       hide(getProgressSteps());
//     };

//     sweetAlert.enableButtons();
//     sweetAlert.hideLoading();
//     sweetAlert.resetValidationError

//     // inputs
//     ();var inputTypes = ['input', 'file', 'range', 'select', 'radio', 'checkbox', 'textarea'];
//     var input = void 0;
//     for (var _i7 = 0; _i7 < inputTypes.length; _i7++) {
//       var inputClass = swalClasses[inputTypes[_i7]];
//       var inputContainer = getChildByClass(modal, inputClass);
//       input = getInput(inputTypes[_i7]

//       // set attributes
//       );if (input) {
//         for (var j in input.attributes) {
//           if (input.attributes.hasOwnProperty(j)) {
//             var attrName = input.attributes[j].name;
//             if (attrName !== 'type' && attrName !== 'value') {
//               input.removeAttribute(attrName);
//             }
//           }
//         }
//         for (var attr in params.inputAttributes) {
//           input.setAttribute(attr, params.inputAttributes[attr]);
//         }
//       }

//       // set class
//       inputContainer.className = inputClass;
//       if (params.inputClass) {
//         addClass(inputContainer, params.inputClass);
//       }

//       hide(inputContainer);
//     }

//     var populateInputOptions = void 0;
//     switch (params.input) {
//       case 'text':
//       case 'email':
//       case 'password':
//       case 'number':
//       case 'tel':
//       case 'url':
//         input = getChildByClass(modal, swalClasses.input);
//         input.value = params.inputValue;
//         input.placeholder = params.inputPlaceholder;
//         input.type = params.input;
//         show(input);
//         break;
//       case 'file':
//         input = getChildByClass(modal, swalClasses.file);
//         input.placeholder = params.inputPlaceholder;
//         input.type = params.input;
//         show(input);
//         break;
//       case 'range':
//         var range = getChildByClass(modal, swalClasses.range);
//         var rangeInput = range.querySelector('input');
//         var rangeOutput = range.querySelector('output');
//         rangeInput.value = params.inputValue;
//         rangeInput.type = params.input;
//         rangeOutput.value = params.inputValue;
//         show(range);
//         break;
//       case 'select':
//         var select = getChildByClass(modal, swalClasses.select);
//         select.innerHTML = '';
//         if (params.inputPlaceholder) {
//           var placeholder = document.createElement('option');
//           placeholder.innerHTML = params.inputPlaceholder;
//           placeholder.value = '';
//           placeholder.disabled = true;
//           placeholder.selected = true;
//           select.appendChild(placeholder);
//         }
//         populateInputOptions = function populateInputOptions(inputOptions) {
//           for (var optionValue in inputOptions) {
//             var option = document.createElement('option');
//             option.value = optionValue;
//             option.innerHTML = inputOptions[optionValue];
//             if (params.inputValue === optionValue) {
//               option.selected = true;
//             }
//             select.appendChild(option);
//           }
//           show(select);
//           select.focus();
//         };
//         break;
//       case 'radio':
//         var radio = getChildByClass(modal, swalClasses.radio);
//         radio.innerHTML = '';
//         populateInputOptions = function populateInputOptions(inputOptions) {
//           for (var radioValue in inputOptions) {
//             var radioInput = document.createElement('input');
//             var radioLabel = document.createElement('label');
//             var radioLabelSpan = document.createElement('span');
//             radioInput.type = 'radio';
//             radioInput.name = swalClasses.radio;
//             radioInput.value = radioValue;
//             if (params.inputValue === radioValue) {
//               radioInput.checked = true;
//             }
//             radioLabelSpan.innerHTML = inputOptions[radioValue];
//             radioLabel.appendChild(radioInput);
//             radioLabel.appendChild(radioLabelSpan);
//             radioLabel.for = radioInput.id;
//             radio.appendChild(radioLabel);
//           }
//           show(radio);
//           var radios = radio.querySelectorAll('input');
//           if (radios.length) {
//             radios[0].focus();
//           }
//         };
//         break;
//       case 'checkbox':
//         var checkbox = getChildByClass(modal, swalClasses.checkbox);
//         var checkboxInput = getInput('checkbox');
//         checkboxInput.type = 'checkbox';
//         checkboxInput.value = 1;
//         checkboxInput.id = swalClasses.checkbox;
//         checkboxInput.checked = Boolean(params.inputValue);
//         var label = checkbox.getElementsByTagName('span');
//         if (label.length) {
//           checkbox.removeChild(label[0]);
//         }
//         label = document.createElement('span');
//         label.innerHTML = params.inputPlaceholder;
//         checkbox.appendChild(label);
//         show(checkbox);
//         break;
//       case 'textarea':
//         var textarea = getChildByClass(modal, swalClasses.textarea);
//         textarea.value = params.inputValue;
//         textarea.placeholder = params.inputPlaceholder;
//         show(textarea);
//         break;
//       case null:
//         break;
//       default:
//         console.error('SweetAlert2: Unexpected type of input! Expected "text", "email", "password", "number", "tel", "select", "radio", "checkbox", "textarea", "file" or "url", got "' + params.input + '"');
//         break;
//     }

//     if (params.input === 'select' || params.input === 'radio') {
//       if (params.inputOptions instanceof Promise) {
//         sweetAlert.showLoading();
//         params.inputOptions.then(function (inputOptions) {
//           sweetAlert.hideLoading();
//           populateInputOptions(inputOptions);
//         });
//       } else if (_typeof(params.inputOptions) === 'object') {
//         populateInputOptions(params.inputOptions);
//       } else {
//         console.error('SweetAlert2: Unexpected type of inputOptions! Expected object or Promise, got ' + _typeof(params.inputOptions));
//       }
//     }

//     openModal(params.animation, params.onOpen

//     // Focus the first element (input or button)
//     );if (params.allowEnterKey) {
//       setFocus(-1, 1);
//     } else {
//       if (document.activeElement) {
//         document.activeElement.blur();
//       }
//     }

//     // fix scroll
//     getContainer().scrollTop = 0;

//     // Observe changes inside the modal and adjust height
//     if (typeof MutationObserver !== 'undefined' && !swal2Observer) {
//       swal2Observer = new MutationObserver(sweetAlert.recalculateHeight);
//       swal2Observer.observe(modal, { childList: true, characterData: true, subtree: true });
//     }
//   });
// };

// /*
//  * Global function to determine if swal2 modal is shown
//  */
// sweetAlert.isVisible = function () {
//   return !!getModal();
// };

// /*
//  * Global function for chaining sweetAlert modals
//  */
// sweetAlert.queue = function (steps) {
//   queue = steps;
//   var resetQueue = function resetQueue() {
//     queue = [];
//     document.body.removeAttribute('data-swal2-queue-step');
//   };
//   var queueResult = [];
//   return new Promise(function (resolve, reject) {
//     (function step(i, callback) {
//       if (i < queue.length) {
//         document.body.setAttribute('data-swal2-queue-step', i);

//         sweetAlert(queue[i]).then(function (result) {
//           queueResult.push(result);
//           step(i + 1, callback);
//         }, function (dismiss) {
//           resetQueue();
//           reject(dismiss);
//         });
//       } else {
//         resetQueue();
//         resolve(queueResult);
//       }
//     })(0);
//   });
// };

// /*
//  * Global function for getting the index of current modal in queue
//  */
// sweetAlert.getQueueStep = function () {
//   return document.body.getAttribute('data-swal2-queue-step'

//   /*
//    * Global function for inserting a modal to the queue
//    */
//   );
// };sweetAlert.insertQueueStep = function (step, index) {
//   if (index && index < queue.length) {
//     return queue.splice(index, 0, step);
//   }
//   return queue.push(step);
// };

// /*
//  * Global function for deleting a modal from the queue
//  */
// sweetAlert.deleteQueueStep = function (index) {
//   if (typeof queue[index] !== 'undefined') {
//     queue.splice(index, 1);
//   }
// };

// /*
//  * Global function to close sweetAlert
//  */
// sweetAlert.close = sweetAlert.closeModal = function (onComplete) {
//   var container = getContainer();
//   var modal = getModal();
//   if (!modal) {
//     return;
//   }
//   removeClass(modal, swalClasses.show);
//   addClass(modal, swalClasses.hide);
//   clearTimeout(modal.timeout);

//   resetPrevState();

//   var removeModalAndResetState = function removeModalAndResetState() {
//     if (container.parentNode) {
//       container.parentNode.removeChild(container);
//     }
//     removeClass(document.documentElement, swalClasses.shown);
//     removeClass(document.body, swalClasses.shown);
//     undoScrollbar();
//     undoIOSfix();
//   };

//   // If animation is supported, animate
//   if (animationEndEvent && !hasClass(modal, swalClasses.noanimation)) {
//     modal.addEventListener(animationEndEvent, function swalCloseEventFinished() {
//       modal.removeEventListener(animationEndEvent, swalCloseEventFinished);
//       if (hasClass(modal, swalClasses.hide)) {
//         removeModalAndResetState();
//       }
//     });
//   } else {
//     // Otherwise, remove immediately
//     removeModalAndResetState();
//   }
//   if (onComplete !== null && typeof onComplete === 'function') {
//     setTimeout(function () {
//       onComplete(modal);
//     });
//   }
// };

// /*
//  * Global function to click 'Confirm' button
//  */
// sweetAlert.clickConfirm = function () {
//   return getConfirmButton().click

//   /*
//    * Global function to click 'Cancel' button
//    */
//   ();
// };sweetAlert.clickCancel = function () {
//   return getCancelButton().click

//   /**
//    * Show spinner instead of Confirm button and disable Cancel button
//    */
//   ();
// };sweetAlert.showLoading = sweetAlert.enableLoading = function () {
//   var modal = getModal();
//   if (!modal) {
//     sweetAlert('');
//   }
//   var buttonsWrapper = getButtonsWrapper();
//   var confirmButton = getConfirmButton();
//   var cancelButton = getCancelButton();

//   show(buttonsWrapper);
//   show(confirmButton, 'inline-block');
//   addClass(buttonsWrapper, swalClasses.loading);
//   addClass(modal, swalClasses.loading);
//   confirmButton.disabled = true;
//   cancelButton.disabled = true;
// };

// /**
//  * Set default params for each popup
//  * @param {Object} userParams
//  */
// sweetAlert.setDefaults = function (userParams) {
//   if (!userParams || (typeof userParams === 'undefined' ? 'undefined' : _typeof(userParams)) !== 'object') {
//     return console.error('SweetAlert2: the argument for setDefaults() is required and has to be a object');
//   }

//   for (var param in userParams) {
//     if (!defaultParams.hasOwnProperty(param) && param !== 'extraParams') {
//       console.warn('SweetAlert2: Unknown parameter "' + param + '"');
//       delete userParams[param];
//     }
//   }

//   _extends(modalParams, userParams);
// };

// /**
//  * Reset default params for each popup
//  */
// sweetAlert.resetDefaults = function () {
//   modalParams = _extends({}, defaultParams);
// };

// sweetAlert.noop = function () {};

// sweetAlert.version = '6.6.5';

// sweetAlert.default = sweetAlert;

// return sweetAlert;

// })));
// if (window.Sweetalert2) window.sweetAlert = window.swal = window.Sweetalert2;
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):t.Sweetalert2=e()}(this,function(){"use strict";function f(t){return(f="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function o(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function i(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function r(t,e,n){return e&&i(t.prototype,e),n&&i(t,n),t}function a(){return(a=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(t[o]=n[o])}return t}).apply(this,arguments)}function s(t){return(s=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function u(t,e){return(u=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}function c(t,e,n){return(c=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],function(){})),!0}catch(t){return!1}}()?Reflect.construct:function(t,e,n){var o=[null];o.push.apply(o,e);var i=new(Function.bind.apply(t,o));return n&&u(i,n.prototype),i}).apply(null,arguments)}function l(t,e){return!e||"object"!=typeof e&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function d(t,e,n){return(d="undefined"!=typeof Reflect&&Reflect.get?Reflect.get:function(t,e,n){var o=function(t,e){for(;!Object.prototype.hasOwnProperty.call(t,e)&&null!==(t=s(t)););return t}(t,e);if(o){var i=Object.getOwnPropertyDescriptor(o,e);return i.get?i.get.call(n):i.value}})(t,e,n||t)}function p(e){return Object.keys(e).map(function(t){return e[t]})}function m(t){return Array.prototype.slice.call(t)}function g(t){console.error("".concat(e," ").concat(t))}function h(t,e){!function(t){-1===n.indexOf(t)&&(n.push(t),y(t))}('"'.concat(t,'" is deprecated and will be removed in the next major release. Please use "').concat(e,'" instead.'))}function v(t){return t&&Promise.resolve(t)===t}function t(t){var e={};for(var n in t)e[t[n]]="swal2-"+t[n];return e}function b(e,t,n){m(e.classList).forEach(function(t){-1===p(k).indexOf(t)&&-1===p(B).indexOf(t)&&e.classList.remove(t)}),t&&t[n]&&nt(e,t[n])}var e="SweetAlert2:",y=function(t){console.warn("".concat(e," ").concat(t))},n=[],w=function(t){return"function"==typeof t?t():t},C=Object.freeze({cancel:"cancel",backdrop:"backdrop",close:"close",esc:"esc",timer:"timer"}),k=t(["container","shown","height-auto","iosfix","popup","modal","no-backdrop","toast","toast-shown","toast-column","fade","show","hide","noanimation","close","title","header","content","actions","confirm","cancel","footer","icon","image","input","file","range","select","radio","checkbox","label","textarea","inputerror","validation-message","progress-steps","active-progress-step","progress-step","progress-step-line","loading","styled","top","top-start","top-end","top-left","top-right","center","center-start","center-end","center-left","center-right","bottom","bottom-start","bottom-end","bottom-left","bottom-right","grow-row","grow-column","grow-fullscreen","rtl"]),B=t(["success","warning","info","question","error"]),x={previousBodyPadding:null},S=function(t,e){return t.classList.contains(e)};function P(t,e){if(!e)return null;switch(e){case"select":case"textarea":case"file":return it(t,k[e]);case"checkbox":return t.querySelector(".".concat(k.checkbox," input"));case"radio":return t.querySelector(".".concat(k.radio," input:checked"))||t.querySelector(".".concat(k.radio," input:first-child"));case"range":return t.querySelector(".".concat(k.range," input"));default:return it(t,k.input)}}function A(t){if(t.focus(),"file"!==t.type){var e=t.value;t.value="",t.value=e}}function L(t,e,n){t&&e&&("string"==typeof e&&(e=e.split(/\s+/).filter(Boolean)),e.forEach(function(e){t.forEach?t.forEach(function(t){n?t.classList.add(e):t.classList.remove(e)}):n?t.classList.add(e):t.classList.remove(e)}))}function E(t,e,n){n||0===parseInt(n)?t.style[e]="number"==typeof n?n+"px":n:t.style.removeProperty(e)}function O(t){var e=1<arguments.length&&void 0!==arguments[1]?arguments[1]:"flex";t.style.opacity="",t.style.display=e}function T(t){t.style.opacity="",t.style.display="none"}function M(t,e,n){e?O(t,n):T(t)}function V(t){return!(!t||!(t.offsetWidth||t.offsetHeight||t.getClientRects().length))}function j(t){var e=window.getComputedStyle(t),n=parseFloat(e.getPropertyValue("animation-duration")||"0"),o=parseFloat(e.getPropertyValue("transition-duration")||"0");return 0<n||0<o}function q(){return document.body.querySelector("."+k.container)}function H(t){var e=q();return e?e.querySelector(t):null}function I(t){return H("."+t)}function R(){var t=rt();return m(t.querySelectorAll("."+k.icon))}function D(){var t=R().filter(function(t){return V(t)});return t.length?t[0]:null}function N(){return I(k.title)}function U(){return I(k.content)}function _(){return I(k.image)}function z(){return I(k["progress-steps"])}function W(){return I(k["validation-message"])}function K(){return H("."+k.actions+" ."+k.confirm)}function F(){return H("."+k.actions+" ."+k.cancel)}function Z(){return I(k.actions)}function Q(){return I(k.header)}function Y(){return I(k.footer)}function $(){return I(k.close)}function J(){var t=m(rt().querySelectorAll('[tabindex]:not([tabindex="-1"]):not([tabindex="0"])')).sort(function(t,e){return t=parseInt(t.getAttribute("tabindex")),(e=parseInt(e.getAttribute("tabindex")))<t?1:t<e?-1:0}),e=m(rt().querySelectorAll('a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex="0"], [contenteditable], audio[controls], video[controls]')).filter(function(t){return"-1"!==t.getAttribute("tabindex")});return function(t){for(var e=[],n=0;n<t.length;n++)-1===e.indexOf(t[n])&&e.push(t[n]);return e}(t.concat(e)).filter(function(t){return V(t)})}function X(){return"undefined"==typeof window||"undefined"==typeof document}function G(t){ce.isVisible()&&et!==t.target.value&&ce.resetValidationMessage(),et=t.target.value}function tt(t,e){t instanceof HTMLElement?e.appendChild(t):"object"===f(t)?lt(e,t):t&&(e.innerHTML=t)}var et,nt=function(t,e){L(t,e,!0)},ot=function(t,e){L(t,e,!1)},it=function(t,e){for(var n=0;n<t.childNodes.length;n++)if(S(t.childNodes[n],e))return t.childNodes[n]},rt=function(){return I(k.popup)},at=function(){return!st()&&!document.body.classList.contains(k["no-backdrop"])},st=function(){return document.body.classList.contains(k["toast-shown"])},ut='\n <div aria-labelledby="'.concat(k.title,'" aria-describedby="').concat(k.content,'" class="').concat(k.popup,'" tabindex="-1">\n   <div class="').concat(k.header,'">\n     <ul class="').concat(k["progress-steps"],'"></ul>\n     <div class="').concat(k.icon," ").concat(B.error,'">\n       <span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span>\n     </div>\n     <div class="').concat(k.icon," ").concat(B.question,'"></div>\n     <div class="').concat(k.icon," ").concat(B.warning,'"></div>\n     <div class="').concat(k.icon," ").concat(B.info,'"></div>\n     <div class="').concat(k.icon," ").concat(B.success,'">\n       <div class="swal2-success-circular-line-left"></div>\n       <span class="swal2-success-line-tip"></span> <span class="swal2-success-line-long"></span>\n       <div class="swal2-success-ring"></div> <div class="swal2-success-fix"></div>\n       <div class="swal2-success-circular-line-right"></div>\n     </div>\n     <img class="').concat(k.image,'" />\n     <h2 class="').concat(k.title,'" id="').concat(k.title,'"></h2>\n     <button type="button" class="').concat(k.close,'">&times;</button>\n   </div>\n   <div class="').concat(k.content,'">\n     <div id="').concat(k.content,'"></div>\n     <input class="').concat(k.input,'" />\n     <input type="file" class="').concat(k.file,'" />\n     <div class="').concat(k.range,'">\n       <input type="range" />\n       <output></output>\n     </div>\n     <select class="').concat(k.select,'"></select>\n     <div class="').concat(k.radio,'"></div>\n     <label for="').concat(k.checkbox,'" class="').concat(k.checkbox,'">\n       <input type="checkbox" />\n       <span class="').concat(k.label,'"></span>\n     </label>\n     <textarea class="').concat(k.textarea,'"></textarea>\n     <div class="').concat(k["validation-message"],'" id="').concat(k["validation-message"],'"></div>\n   </div>\n   <div class="').concat(k.actions,'">\n     <button type="button" class="').concat(k.confirm,'">OK</button>\n     <button type="button" class="').concat(k.cancel,'">Cancel</button>\n   </div>\n   <div class="').concat(k.footer,'">\n   </div>\n </div>\n').replace(/(^|\n)\s*/g,""),ct=function(t){if(function(){var t=q();t&&(t.parentNode.removeChild(t),ot([document.documentElement,document.body],[k["no-backdrop"],k["toast-shown"],k["has-column"]]))}(),X())g("SweetAlert2 requires document to initialize");else{var e=document.createElement("div");e.className=k.container,e.innerHTML=ut;var n=function(t){return"string"==typeof t?document.querySelector(t):t}(t.target);n.appendChild(e),function(t){var e=rt();e.setAttribute("role",t.toast?"alert":"dialog"),e.setAttribute("aria-live",t.toast?"polite":"assertive"),t.toast||e.setAttribute("aria-modal","true")}(t),function(t){"rtl"===window.getComputedStyle(t).direction&&nt(q(),k.rtl)}(n),function(){var t=U(),e=it(t,k.input),n=it(t,k.file),o=t.querySelector(".".concat(k.range," input")),i=t.querySelector(".".concat(k.range," output")),r=it(t,k.select),a=t.querySelector(".".concat(k.checkbox," input")),s=it(t,k.textarea);e.oninput=G,n.onchange=G,r.onchange=G,a.onchange=G,s.oninput=G,o.oninput=function(t){G(t),i.value=o.value},o.onchange=function(t){G(t),o.nextSibling.value=o.value}}()}},lt=function(t,e){if(t.innerHTML="",0 in e)for(var n=0;n in e;n++)t.appendChild(e[n].cloneNode(!0));else t.appendChild(e.cloneNode(!0))},dt=function(){if(X())return!1;var t=document.createElement("div"),e={WebkitAnimation:"webkitAnimationEnd",OAnimation:"oAnimationEnd oanimationend",animation:"animationend"};for(var n in e)if(e.hasOwnProperty(n)&&void 0!==t.style[n])return e[n];return!1}();function pt(t,e,n){M(t,n["showC"+e.substring(1)+"Button"],"inline-block"),t.innerHTML=n[e+"ButtonText"],t.setAttribute("aria-label",n[e+"ButtonAriaLabel"]),t.className=k[e],b(t,n.customClass,e+"Button"),nt(t,n[e+"ButtonClass"])}function ft(t,e){var n=Z(),o=K(),i=F();e.showConfirmButton||e.showCancelButton?O(n):T(n),b(n,e.customClass,"actions"),pt(o,"confirm",e),pt(i,"cancel",e),e.buttonsStyling?function(t,e,n){nt([t,e],k.styled),n.confirmButtonColor&&(t.style.backgroundColor=n.confirmButtonColor),n.cancelButtonColor&&(e.style.backgroundColor=n.cancelButtonColor);var o=window.getComputedStyle(t).getPropertyValue("background-color");t.style.borderLeftColor=o,t.style.borderRightColor=o}(o,i,e):(ot([o,i],k.styled),o.style.backgroundColor=o.style.borderLeftColor=o.style.borderRightColor="",i.style.backgroundColor=i.style.borderLeftColor=i.style.borderRightColor="")}function mt(t,e){var n=q();n&&(function(t,e){"string"==typeof e?t.style.background=e:e||nt([document.documentElement,document.body],k["no-backdrop"])}(n,e.backdrop),!e.backdrop&&e.allowOutsideClick&&y('"allowOutsideClick" parameter requires `backdrop` parameter to be set to `true`'),function(t,e){e in k?nt(t,k[e]):(y('The "position" parameter is not valid, defaulting to "center"'),nt(t,k.center))}(n,e.position),function(t,e){if(e&&"string"==typeof e){var n="grow-"+e;n in k&&nt(t,k[n])}}(n,e.grow),b(n,e.customClass,"container"),e.customContainerClass&&nt(n,e.customContainerClass))}function gt(t,e){t.placeholder&&!e.inputPlaceholder||(t.placeholder=e.inputPlaceholder)}var ht={promise:new WeakMap,innerParams:new WeakMap,domCache:new WeakMap},vt=function(t,e){var n=P(U(),t);if(n)for(var o in function(t){for(var e=0;e<t.attributes.length;e++){var n=t.attributes[e].name;-1===["type","value","style"].indexOf(n)&&t.removeAttribute(n)}}(n),e)"range"===t&&"placeholder"===o||n.setAttribute(o,e[o])},bt=function(t,e,n){t.className=e,n.inputClass&&nt(t,n.inputClass),n.customClass&&nt(t,n.customClass.input)},yt={};yt.text=yt.email=yt.password=yt.number=yt.tel=yt.url=function(t){var e=it(U(),k.input);return"string"==typeof t.inputValue||"number"==typeof t.inputValue?e.value=t.inputValue:v(t.inputValue)||y('Unexpected type of inputValue! Expected "string", "number" or "Promise", got "'.concat(f(t.inputValue),'"')),gt(e,t),e.type=t.input,e},yt.file=function(t){var e=it(U(),k.file);return gt(e,t),e.type=t.input,e},yt.range=function(t){var e=it(U(),k.range),n=e.querySelector("input"),o=e.querySelector("output");return n.value=t.inputValue,n.type=t.input,o.value=t.inputValue,e},yt.select=function(t){var e=it(U(),k.select);if(e.innerHTML="",t.inputPlaceholder){var n=document.createElement("option");n.innerHTML=t.inputPlaceholder,n.value="",n.disabled=!0,n.selected=!0,e.appendChild(n)}return e},yt.radio=function(){var t=it(U(),k.radio);return t.innerHTML="",t},yt.checkbox=function(t){var e=it(U(),k.checkbox),n=P(U(),"checkbox");return n.type="checkbox",n.value=1,n.id=k.checkbox,n.checked=Boolean(t.inputValue),e.querySelector("span").innerHTML=t.inputPlaceholder,e},yt.textarea=function(t){var e=it(U(),k.textarea);return e.value=t.inputValue,gt(e,t),e};function wt(t,e){var n=U().querySelector("#"+k.content);e.html?(tt(e.html,n),O(n,"block")):e.text?(n.textContent=e.text,O(n,"block")):T(n),function(t,e){for(var n=ht.innerParams.get(t),o=!n||e.input!==n.input,i=U(),r=["input","file","range","select","radio","checkbox","textarea"],a=0;a<r.length;a++){var s=k[r[a]],u=it(i,s);vt(r[a],e.inputAttributes),bt(u,s,e),o&&T(u)}if(e.input){if(!yt[e.input])return g('Unexpected type of input! Expected "text", "email", "password", "number", "tel", "select", "radio", "checkbox", "textarea", "file" or "url", got "'.concat(e.input,'"'));if(o){var c=yt[e.input](e);O(c)}}}(t,e),b(U(),e.customClass,"content")}function Ct(t,i){var r=z();if(!i.progressSteps||0===i.progressSteps.length)return T(r);O(r),r.innerHTML="";var a=parseInt(null===i.currentProgressStep?ce.getQueueStep():i.currentProgressStep);a>=i.progressSteps.length&&y("Invalid currentProgressStep parameter, it should be less than progressSteps.length (currentProgressStep like JS arrays starts from 0)"),i.progressSteps.forEach(function(t,e){var n=function(t){var e=document.createElement("li");return nt(e,k["progress-step"]),e.innerHTML=t,e}(t);if(r.appendChild(n),e===a&&nt(n,k["active-progress-step"]),e!==i.progressSteps.length-1){var o=function(t){var e=document.createElement("li");return nt(e,k["progress-step-line"]),t.progressStepsDistance&&(e.style.width=t.progressStepsDistance),e}(t);r.appendChild(o)}})}function kt(t,e){var n=Q();b(n,e.customClass,"header"),Ct(0,e),function(t,e){var n=ht.innerParams.get(t);if(n&&e.type===n.type&&D())b(D(),e.customClass,"icon");else if(xt(),e.type)if(St(),-1!==Object.keys(B).indexOf(e.type)){var o=H(".".concat(k.icon,".").concat(B[e.type]));O(o),b(o,e.customClass,"icon"),L(o,"swal2-animate-".concat(e.type,"-icon"),e.animation)}else g('Unknown type! Expected "success", "error", "warning", "info" or "question", got "'.concat(e.type,'"'))}(t,e),function(t,e){var n=_();if(!e.imageUrl)return T(n);O(n),n.setAttribute("src",e.imageUrl),n.setAttribute("alt",e.imageAlt),E(n,"width",e.imageWidth),E(n,"height",e.imageHeight),n.className=k.image,b(n,e.customClass,"image"),e.imageClass&&nt(n,e.imageClass)}(0,e),function(t,e){var n=N();M(n,e.title||e.titleText),e.title&&tt(e.title,n),e.titleText&&(n.innerText=e.titleText),b(n,e.customClass,"title")}(0,e),function(t,e){var n=$();b(n,e.customClass,"closeButton"),M(n,e.showCloseButton),n.setAttribute("aria-label",e.closeButtonAriaLabel)}(0,e)}function Bt(t,e){!function(t,e){var n=rt();E(n,"width",e.width),E(n,"padding",e.padding),e.background&&(n.style.background=e.background),n.className=k.popup,e.toast?(nt([document.documentElement,document.body],k["toast-shown"]),nt(n,k.toast)):nt(n,k.modal),b(n,e.customClass,"popup"),"string"==typeof e.customClass&&nt(n,e.customClass),L(n,k.noanimation,!e.animation)}(0,e),mt(0,e),kt(t,e),wt(t,e),ft(0,e),function(t,e){var n=Y();M(n,e.footer),e.footer&&tt(e.footer,n),b(n,e.customClass,"footer")}(0,e)}var xt=function(){for(var t=R(),e=0;e<t.length;e++)T(t[e])},St=function(){for(var t=rt(),e=window.getComputedStyle(t).getPropertyValue("background-color"),n=t.querySelectorAll("[class^=swal2-success-circular-line], .swal2-success-fix"),o=0;o<n.length;o++)n[o].style.backgroundColor=e};function Pt(){var t=rt();t||ce.fire(""),t=rt();var e=Z(),n=K(),o=F();O(e),O(n),nt([t,e],k.loading),n.disabled=!0,o.disabled=!0,t.setAttribute("data-loading",!0),t.setAttribute("aria-busy",!0),t.focus()}function At(t){return Mt.hasOwnProperty(t)}function Lt(t){return jt[t]}var Et=[],Ot={},Tt=function(){return new Promise(function(t){var e=window.scrollX,n=window.scrollY;Ot.restoreFocusTimeout=setTimeout(function(){Ot.previousActiveElement&&Ot.previousActiveElement.focus?(Ot.previousActiveElement.focus(),Ot.previousActiveElement=null):document.body&&document.body.focus(),t()},100),void 0!==e&&void 0!==n&&window.scrollTo(e,n)})},Mt={title:"",titleText:"",text:"",html:"",footer:"",type:null,toast:!1,customClass:"",customContainerClass:"",target:"body",backdrop:!0,animation:!0,heightAuto:!0,allowOutsideClick:!0,allowEscapeKey:!0,allowEnterKey:!0,stopKeydownPropagation:!0,keydownListenerCapture:!1,showConfirmButton:!0,showCancelButton:!1,preConfirm:null,confirmButtonText:"OK",confirmButtonAriaLabel:"",confirmButtonColor:null,confirmButtonClass:"",cancelButtonText:"Cancel",cancelButtonAriaLabel:"",cancelButtonColor:null,cancelButtonClass:"",buttonsStyling:!0,reverseButtons:!1,focusConfirm:!0,focusCancel:!1,showCloseButton:!1,closeButtonAriaLabel:"Close this dialog",showLoaderOnConfirm:!1,imageUrl:null,imageWidth:null,imageHeight:null,imageAlt:"",imageClass:"",timer:null,width:null,padding:null,background:null,input:null,inputPlaceholder:"",inputValue:"",inputOptions:{},inputAutoTrim:!0,inputClass:"",inputAttributes:{},inputValidator:null,validationMessage:null,grow:!1,position:"center",progressSteps:[],currentProgressStep:null,progressStepsDistance:null,onBeforeOpen:null,onAfterClose:null,onOpen:null,onClose:null,scrollbarPadding:!0},Vt=["title","titleText","text","html","type","customClass","showConfirmButton","showCancelButton","confirmButtonText","confirmButtonAriaLabel","confirmButtonColor","confirmButtonClass","cancelButtonText","cancelButtonAriaLabel","cancelButtonColor","cancelButtonClass","buttonsStyling","reverseButtons","imageUrl","imageWidth","imageHeigth","imageAlt","imageClass","progressSteps","currentProgressStep"],jt={customContainerClass:"customClass",confirmButtonClass:"customClass",cancelButtonClass:"customClass",imageClass:"customClass",inputClass:"customClass"},qt=["allowOutsideClick","allowEnterKey","backdrop","focusConfirm","focusCancel","heightAuto","keydownListenerCapture"],Ht=Object.freeze({isValidParameter:At,isUpdatableParameter:function(t){return-1!==Vt.indexOf(t)},isDeprecatedParameter:Lt,argsToParams:function(n){var o={};switch(f(n[0])){case"object":a(o,n[0]);break;default:["title","html","type"].forEach(function(t,e){switch(f(n[e])){case"string":o[t]=n[e];break;case"undefined":break;default:g("Unexpected type of ".concat(t,'! Expected "string", got ').concat(f(n[e])))}})}return o},isVisible:function(){return V(rt())},clickConfirm:function(){return K()&&K().click()},clickCancel:function(){return F()&&F().click()},getContainer:q,getPopup:rt,getTitle:N,getContent:U,getImage:_,getIcon:D,getIcons:R,getCloseButton:$,getActions:Z,getConfirmButton:K,getCancelButton:F,getHeader:Q,getFooter:Y,getFocusableElements:J,getValidationMessage:W,isLoading:function(){return rt().hasAttribute("data-loading")},fire:function(){for(var t=arguments.length,e=new Array(t),n=0;n<t;n++)e[n]=arguments[n];return c(this,e)},mixin:function(n){return function(t){function e(){return o(this,e),l(this,s(e).apply(this,arguments))}return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&u(t,e)}(e,t),r(e,[{key:"_main",value:function(t){return d(s(e.prototype),"_main",this).call(this,a({},n,t))}}]),e}(this)},queue:function(t){var r=this;Et=t;function a(t,e){Et=[],document.body.removeAttribute("data-swal2-queue-step"),t(e)}var s=[];return new Promise(function(i){!function e(n,o){n<Et.length?(document.body.setAttribute("data-swal2-queue-step",n),r.fire(Et[n]).then(function(t){void 0!==t.value?(s.push(t.value),e(n+1,o)):a(i,{dismiss:t.dismiss})})):a(i,{value:s})}(0)})},getQueueStep:function(){return document.body.getAttribute("data-swal2-queue-step")},insertQueueStep:function(t,e){return e&&e<Et.length?Et.splice(e,0,t):Et.push(t)},deleteQueueStep:function(t){void 0!==Et[t]&&Et.splice(t,1)},showLoading:Pt,enableLoading:Pt,getTimerLeft:function(){return Ot.timeout&&Ot.timeout.getTimerLeft()},stopTimer:function(){return Ot.timeout&&Ot.timeout.stop()},resumeTimer:function(){return Ot.timeout&&Ot.timeout.start()},toggleTimer:function(){var t=Ot.timeout;return t&&(t.running?t.stop():t.start())},increaseTimer:function(t){return Ot.timeout&&Ot.timeout.increase(t)},isTimerRunning:function(){return Ot.timeout&&Ot.timeout.isRunning()}});function It(){var t=ht.innerParams.get(this),e=ht.domCache.get(this);t.showConfirmButton||(T(e.confirmButton),t.showCancelButton||T(e.actions)),ot([e.popup,e.actions],k.loading),e.popup.removeAttribute("aria-busy"),e.popup.removeAttribute("data-loading"),e.confirmButton.disabled=!1,e.cancelButton.disabled=!1}function Rt(){null===x.previousBodyPadding&&document.body.scrollHeight>window.innerHeight&&(x.previousBodyPadding=parseInt(window.getComputedStyle(document.body).getPropertyValue("padding-right")),document.body.style.paddingRight=x.previousBodyPadding+function(){if("ontouchstart"in window||navigator.msMaxTouchPoints)return 0;var t=document.createElement("div");t.style.width="50px",t.style.height="50px",t.style.overflow="scroll",document.body.appendChild(t);var e=t.offsetWidth-t.clientWidth;return document.body.removeChild(t),e}()+"px")}function Dt(){return!!window.MSInputMethodContext&&!!document.documentMode}function Nt(){var t=q(),e=rt();t.style.removeProperty("align-items"),e.offsetTop<0&&(t.style.alignItems="flex-start")}var Ut=function(){null!==x.previousBodyPadding&&(document.body.style.paddingRight=x.previousBodyPadding+"px",x.previousBodyPadding=null)},_t=function(){var e,n=q();n.ontouchstart=function(t){e=t.target===n||!function(t){return!!(t.scrollHeight>t.clientHeight)}(n)},n.ontouchmove=function(t){e&&(t.preventDefault(),t.stopPropagation())}},zt=function(){if(S(document.body,k.iosfix)){var t=parseInt(document.body.style.top,10);ot(document.body,k.iosfix),document.body.style.top="",document.body.scrollTop=-1*t}},Wt=function(){"undefined"!=typeof window&&Dt()&&window.removeEventListener("resize",Nt)},Kt=function(){m(document.body.children).forEach(function(t){t.hasAttribute("data-previous-aria-hidden")?(t.setAttribute("aria-hidden",t.getAttribute("data-previous-aria-hidden")),t.removeAttribute("data-previous-aria-hidden")):t.removeAttribute("aria-hidden")})},Ft={swalPromiseResolve:new WeakMap};function Zt(t,e,n){e?$t(n):(Tt().then(function(){return $t(n)}),Ot.keydownTarget.removeEventListener("keydown",Ot.keydownHandler,{capture:Ot.keydownListenerCapture}),Ot.keydownHandlerAdded=!1),delete Ot.keydownHandler,delete Ot.keydownTarget,t.parentNode&&t.parentNode.removeChild(t),ot([document.documentElement,document.body],[k.shown,k["height-auto"],k["no-backdrop"],k["toast-shown"],k["toast-column"]]),at()&&(Ut(),zt(),Wt(),Kt())}function Qt(t){var e=q(),n=rt();if(n&&!S(n,k.hide)){var o=ht.innerParams.get(this),i=Ft.swalPromiseResolve.get(this),r=o.onClose,a=o.onAfterClose;ot(n,k.show),nt(n,k.hide),dt&&j(n)?n.addEventListener(dt,function(t){t.target===n&&function(t,e,n,o){S(t,k.hide)&&Zt(e,n,o),Yt(ht),Yt(Ft)}(n,e,st(),a)}):Zt(e,st(),a),null!==r&&"function"==typeof r&&r(n),i(t||{}),delete this.params}}var Yt=function(t){for(var e in t)t[e]=new WeakMap},$t=function(t){null!==t&&"function"==typeof t&&setTimeout(function(){t()})};function Jt(t,e,n){var o=ht.domCache.get(t);e.forEach(function(t){o[t].disabled=n})}function Xt(t,e){if(!t)return!1;if("radio"===t.type)for(var n=t.parentNode.parentNode.querySelectorAll("input"),o=0;o<n.length;o++)n[o].disabled=e;else t.disabled=e}var Gt=function(){function n(t,e){o(this,n),this.callback=t,this.remaining=e,this.running=!1,this.start()}return r(n,[{key:"start",value:function(){return this.running||(this.running=!0,this.started=new Date,this.id=setTimeout(this.callback,this.remaining)),this.remaining}},{key:"stop",value:function(){return this.running&&(this.running=!1,clearTimeout(this.id),this.remaining-=new Date-this.started),this.remaining}},{key:"increase",value:function(t){var e=this.running;return e&&this.stop(),this.remaining+=t,e&&this.start(),this.remaining}},{key:"getTimerLeft",value:function(){return this.running&&(this.stop(),this.start()),this.remaining}},{key:"isRunning",value:function(){return this.running}}]),n}(),te={email:function(t,e){return/^[a-zA-Z0-9.+_-]+@[a-zA-Z0-9.-]+\.[a-zA-Z0-9-]{2,24}$/.test(t)?Promise.resolve():Promise.resolve(e||"Invalid email address")},url:function(t,e){return/^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._+~#=]{2,256}\.[a-z]{2,63}\b([-a-zA-Z0-9@:%_+.~#?&/=]*)$/.test(t)?Promise.resolve():Promise.resolve(e||"Invalid URL")}};function ee(t,e){t.removeEventListener(dt,ee),e.style.overflowY="auto"}function ne(t){var e=q(),n=rt();null!==t.onBeforeOpen&&"function"==typeof t.onBeforeOpen&&t.onBeforeOpen(n),t.animation&&(nt(n,k.show),nt(e,k.fade)),O(n),dt&&j(n)?(e.style.overflowY="hidden",n.addEventListener(dt,ee.bind(null,n,e))):e.style.overflowY="auto",nt([document.documentElement,document.body,e],k.shown),t.heightAuto&&t.backdrop&&!t.toast&&nt([document.documentElement,document.body],k["height-auto"]),at()&&(t.scrollbarPadding&&Rt(),function(){if(/iPad|iPhone|iPod/.test(navigator.userAgent)&&!window.MSStream&&!S(document.body,k.iosfix)){var t=document.body.scrollTop;document.body.style.top=-1*t+"px",nt(document.body,k.iosfix),_t()}}(),"undefined"!=typeof window&&Dt()&&(Nt(),window.addEventListener("resize",Nt)),m(document.body.children).forEach(function(t){t===q()||function(t,e){if("function"==typeof t.contains)return t.contains(e)}(t,q())||(t.hasAttribute("aria-hidden")&&t.setAttribute("data-previous-aria-hidden",t.getAttribute("aria-hidden")),t.setAttribute("aria-hidden","true"))}),setTimeout(function(){e.scrollTop=0})),st()||Ot.previousActiveElement||(Ot.previousActiveElement=document.activeElement),null!==t.onOpen&&"function"==typeof t.onOpen&&setTimeout(function(){t.onOpen(n)})}var oe=void 0,ie={select:function(t,e,i){var r=it(t,k.select);e.forEach(function(t){var e=t[0],n=t[1],o=document.createElement("option");o.value=e,o.innerHTML=n,i.inputValue.toString()===e.toString()&&(o.selected=!0),r.appendChild(o)}),r.focus()},radio:function(t,e,a){var s=it(t,k.radio);e.forEach(function(t){var e=t[0],n=t[1],o=document.createElement("input"),i=document.createElement("label");o.type="radio",o.name=k.radio,o.value=e,a.inputValue.toString()===e.toString()&&(o.checked=!0);var r=document.createElement("span");r.innerHTML=n,r.className=k.label,i.appendChild(o),i.appendChild(r),s.appendChild(i)});var n=s.querySelectorAll("input");n.length&&n[0].focus()}},re=function(e){var n=[];return"undefined"!=typeof Map&&e instanceof Map?e.forEach(function(t,e){n.push([e,t])}):Object.keys(e).forEach(function(t){n.push([t,e[t]])}),n};var ae,se=Object.freeze({hideLoading:It,disableLoading:It,getInput:function(t){var e=ht.innerParams.get(t||this);return P(ht.domCache.get(t||this).content,e.input)},close:Qt,closePopup:Qt,closeModal:Qt,closeToast:Qt,enableButtons:function(){Jt(this,["confirmButton","cancelButton"],!1)},disableButtons:function(){Jt(this,["confirmButton","cancelButton"],!0)},enableConfirmButton:function(){h("Swal.disableConfirmButton()","Swal.getConfirmButton().removeAttribute('disabled')"),Jt(this,["confirmButton"],!1)},disableConfirmButton:function(){h("Swal.enableConfirmButton()","Swal.getConfirmButton().setAttribute('disabled', '')"),Jt(this,["confirmButton"],!0)},enableInput:function(){return Xt(this.getInput(),!1)},disableInput:function(){return Xt(this.getInput(),!0)},showValidationMessage:function(t){var e=ht.domCache.get(this);e.validationMessage.innerHTML=t;var n=window.getComputedStyle(e.popup);e.validationMessage.style.marginLeft="-".concat(n.getPropertyValue("padding-left")),e.validationMessage.style.marginRight="-".concat(n.getPropertyValue("padding-right")),O(e.validationMessage);var o=this.getInput();o&&(o.setAttribute("aria-invalid",!0),o.setAttribute("aria-describedBy",k["validation-message"]),A(o),nt(o,k.inputerror))},resetValidationMessage:function(){var t=ht.domCache.get(this);t.validationMessage&&T(t.validationMessage);var e=this.getInput();e&&(e.removeAttribute("aria-invalid"),e.removeAttribute("aria-describedBy"),ot(e,k.inputerror))},getProgressSteps:function(){return h("Swal.getProgressSteps()","const swalInstance = Swal.fire({progressSteps: ['1', '2', '3']}); const progressSteps = swalInstance.params.progressSteps"),ht.innerParams.get(this).progressSteps},setProgressSteps:function(t){h("Swal.setProgressSteps()","Swal.update()");var e=a({},ht.innerParams.get(this),{progressSteps:t});Ct(0,e),ht.innerParams.set(this,e)},showProgressSteps:function(){var t=ht.domCache.get(this);O(t.progressSteps)},hideProgressSteps:function(){var t=ht.domCache.get(this);T(t.progressSteps)},_main:function(t){var c=this;!function(t){for(var e in t)At(i=e)||y('Unknown parameter "'.concat(i,'"')),t.toast&&(o=e,-1!==qt.indexOf(o)&&y('The parameter "'.concat(o,'" is incompatible with toasts'))),Lt(n=void 0)&&h(n,Lt(n));var n,o,i}(t);var l=a({},Mt,t);!function(e){e.inputValidator||Object.keys(te).forEach(function(t){e.input===t&&(e.inputValidator=te[t])}),e.showLoaderOnConfirm&&!e.preConfirm&&y("showLoaderOnConfirm is set to true, but preConfirm is not defined.\nshowLoaderOnConfirm should be used together with preConfirm, see usage example:\nhttps://sweetalert2.github.io/#ajax-request"),e.animation=w(e.animation),e.target&&("string"!=typeof e.target||document.querySelector(e.target))&&("string"==typeof e.target||e.target.appendChild)||(y('Target parameter is not valid, defaulting to "body"'),e.target="body"),"string"==typeof e.title&&(e.title=e.title.split("\n").join("<br />"));var t=rt(),n="string"==typeof e.target?document.querySelector(e.target):e.target;(!t||t&&n&&t.parentNode!==n.parentNode)&&ct(e)}(l),Object.freeze(l),Ot.timeout&&(Ot.timeout.stop(),delete Ot.timeout),clearTimeout(Ot.restoreFocusTimeout);var d={popup:rt(),container:q(),content:U(),actions:Z(),confirmButton:K(),cancelButton:F(),closeButton:$(),validationMessage:W(),progressSteps:z()};ht.domCache.set(this,d),Bt(this,l),ht.innerParams.set(this,l);var p=this.constructor;return new Promise(function(t){function n(t){c.closePopup({value:t})}function s(t){c.closePopup({dismiss:t})}Ft.swalPromiseResolve.set(c,t),l.timer&&(Ot.timeout=new Gt(function(){s("timer"),delete Ot.timeout},l.timer)),l.input&&setTimeout(function(){var t=c.getInput();t&&A(t)},0);for(var u=function(e){l.showLoaderOnConfirm&&p.showLoading(),l.preConfirm?(c.resetValidationMessage(),Promise.resolve().then(function(){return l.preConfirm(e,l.validationMessage)}).then(function(t){V(d.validationMessage)||!1===t?c.hideLoading():n(void 0===t?e:t)})):n(e)},e=function(t){var e=t.target,n=d.confirmButton,o=d.cancelButton,i=n&&(n===e||n.contains(e)),r=o&&(o===e||o.contains(e));switch(t.type){case"click":if(i)if(c.disableButtons(),l.input){var a=function(){var t=c.getInput();if(!t)return null;switch(l.input){case"checkbox":return t.checked?1:0;case"radio":return t.checked?t.value:null;case"file":return t.files.length?t.files[0]:null;default:return l.inputAutoTrim?t.value.trim():t.value}}();l.inputValidator?(c.disableInput(),Promise.resolve().then(function(){return l.inputValidator(a,l.validationMessage)}).then(function(t){c.enableButtons(),c.enableInput(),t?c.showValidationMessage(t):u(a)})):c.getInput().checkValidity()?u(a):(c.enableButtons(),c.showValidationMessage(l.validationMessage))}else u(!0);else r&&(c.disableButtons(),s(p.DismissReason.cancel))}},o=d.popup.querySelectorAll("button"),i=0;i<o.length;i++)o[i].onclick=e,o[i].onmouseover=e,o[i].onmouseout=e,o[i].onmousedown=e;if(d.closeButton.onclick=function(){s(p.DismissReason.close)},l.toast)d.popup.onclick=function(){l.showConfirmButton||l.showCancelButton||l.showCloseButton||l.input||s(p.DismissReason.close)};else{var r=!1;d.popup.onmousedown=function(){d.container.onmouseup=function(t){d.container.onmouseup=void 0,t.target===d.container&&(r=!0)}},d.container.onmousedown=function(){d.popup.onmouseup=function(t){d.popup.onmouseup=void 0,t.target!==d.popup&&!d.popup.contains(t.target)||(r=!0)}},d.container.onclick=function(t){r?r=!1:t.target===d.container&&w(l.allowOutsideClick)&&s(p.DismissReason.backdrop)}}function a(t,e){for(var n=J(l.focusCancel),o=0;o<n.length;o++)return(t+=e)===n.length?t=0:-1===t&&(t=n.length-1),n[t].focus();d.popup.focus()}l.reverseButtons?d.confirmButton.parentNode.insertBefore(d.cancelButton,d.confirmButton):d.confirmButton.parentNode.insertBefore(d.confirmButton,d.cancelButton),Ot.keydownTarget&&Ot.keydownHandlerAdded&&(Ot.keydownTarget.removeEventListener("keydown",Ot.keydownHandler,{capture:Ot.keydownListenerCapture}),Ot.keydownHandlerAdded=!1),l.toast||(Ot.keydownHandler=function(t){return function(t,e){if(e.stopKeydownPropagation&&t.stopPropagation(),"Enter"!==t.key||t.isComposing)if("Tab"===t.key){for(var n=t.target,o=J(e.focusCancel),i=-1,r=0;r<o.length;r++)if(n===o[r]){i=r;break}t.shiftKey?a(i,-1):a(i,1),t.stopPropagation(),t.preventDefault()}else-1!==["ArrowLeft","ArrowRight","ArrowUp","ArrowDown","Left","Right","Up","Down"].indexOf(t.key)?document.activeElement===d.confirmButton&&V(d.cancelButton)?d.cancelButton.focus():document.activeElement===d.cancelButton&&V(d.confirmButton)&&d.confirmButton.focus():"Escape"!==t.key&&"Esc"!==t.key||!0!==w(e.allowEscapeKey)||(t.preventDefault(),s(p.DismissReason.esc));else if(t.target&&c.getInput()&&t.target.outerHTML===c.getInput().outerHTML){if(-1!==["textarea","file"].indexOf(e.input))return;p.clickConfirm(),t.preventDefault()}}(t,l)},Ot.keydownTarget=l.keydownListenerCapture?window:d.popup,Ot.keydownListenerCapture=l.keydownListenerCapture,Ot.keydownTarget.addEventListener("keydown",Ot.keydownHandler,{capture:Ot.keydownListenerCapture}),Ot.keydownHandlerAdded=!0),c.enableButtons(),c.hideLoading(),c.resetValidationMessage(),l.toast&&(l.input||l.footer||l.showCloseButton)?nt(document.body,k["toast-column"]):ot(document.body,k["toast-column"]),"select"===l.input||"radio"===l.input?function(e,n){function o(t){return ie[n.input](i,re(t),n)}var i=U();v(n.inputOptions)?(Pt(),n.inputOptions.then(function(t){e.hideLoading(),o(t)})):"object"===f(n.inputOptions)?o(n.inputOptions):g("Unexpected type of inputOptions! Expected object, Map or Promise, got ".concat(f(n.inputOptions)))}(c,l):-1!==["text","email","number","tel","textarea"].indexOf(l.input)&&v(l.inputValue)&&function(e,n){var o=e.getInput();T(o),n.inputValue.then(function(t){o.value="number"===n.input?parseFloat(t)||0:t+"",O(o),o.focus(),e.hideLoading()}).catch(function(t){g("Error in inputValue promise: "+t),o.value="",O(o),o.focus(),oe.hideLoading()})}(c,l),ne(l),l.toast||(w(l.allowEnterKey)?l.focusCancel&&V(d.cancelButton)?d.cancelButton.focus():l.focusConfirm&&V(d.confirmButton)?d.confirmButton.focus():a(-1,1):document.activeElement&&"function"==typeof document.activeElement.blur&&document.activeElement.blur()),d.container.scrollTop=0})},update:function(e){var n={};Object.keys(e).forEach(function(t){ce.isUpdatableParameter(t)?n[t]=e[t]:y('Invalid parameter to update: "'.concat(t,'". Updatable params are listed here: https://github.com/sweetalert2/sweetalert2/blob/master/src/utils/params.js'))});var t=a({},ht.innerParams.get(this),n);Bt(this,t),ht.innerParams.set(this,t),Object.defineProperties(this,{params:{value:a({},this.params,e),writable:!1,enumerable:!0}})}});function ue(){if("undefined"!=typeof window){"undefined"==typeof Promise&&g("This package requires a Promise library, please include a shim to enable it in this browser (See: https://github.com/sweetalert2/sweetalert2/wiki/Migration-from-SweetAlert-to-SweetAlert2#1-ie-support)"),ae=this;for(var t=arguments.length,e=new Array(t),n=0;n<t;n++)e[n]=arguments[n];var o=Object.freeze(this.constructor.argsToParams(e));Object.defineProperties(this,{params:{value:o,writable:!1,enumerable:!0,configurable:!0}});var i=this._main(this.params);ht.promise.set(this,i)}}ue.prototype.then=function(t){return ht.promise.get(this).then(t)},ue.prototype.finally=function(t){return ht.promise.get(this).finally(t)},a(ue.prototype,se),a(ue,Ht),Object.keys(se).forEach(function(e){ue[e]=function(){var t;if(ae)return(t=ae)[e].apply(t,arguments)}}),ue.DismissReason=C,ue.version="8.11.6";var ce=ue;return ce.default=ce}),"undefined"!=typeof window&&window.Sweetalert2&&(window.swal=window.sweetAlert=window.Swal=window.SweetAlert=window.Sweetalert2);
"undefined"!=typeof document&&function(e,t){var n=e.createElement("style");if(e.getElementsByTagName("head")[0].appendChild(n),n.styleSheet)n.styleSheet.disabled||(n.styleSheet.cssText=t);else try{n.innerHTML=t}catch(e){n.innerText=t}}(document,"@charset \"UTF-8\";@-webkit-keyframes swal2-show{0%{-webkit-transform:scale(.7);transform:scale(.7)}45%{-webkit-transform:scale(1.05);transform:scale(1.05)}80%{-webkit-transform:scale(.95);transform:scale(.95)}100%{-webkit-transform:scale(1);transform:scale(1)}}@keyframes swal2-show{0%{-webkit-transform:scale(.7);transform:scale(.7)}45%{-webkit-transform:scale(1.05);transform:scale(1.05)}80%{-webkit-transform:scale(.95);transform:scale(.95)}100%{-webkit-transform:scale(1);transform:scale(1)}}@-webkit-keyframes swal2-hide{0%{-webkit-transform:scale(1);transform:scale(1);opacity:1}100%{-webkit-transform:scale(.5);transform:scale(.5);opacity:0}}@keyframes swal2-hide{0%{-webkit-transform:scale(1);transform:scale(1);opacity:1}100%{-webkit-transform:scale(.5);transform:scale(.5);opacity:0}}@-webkit-keyframes swal2-animate-success-line-tip{0%{top:1.1875em;left:.0625em;width:0}54%{top:1.0625em;left:.125em;width:0}70%{top:2.1875em;left:-.375em;width:3.125em}84%{top:3em;left:1.3125em;width:1.0625em}100%{top:2.8125em;left:.875em;width:1.5625em}}@keyframes swal2-animate-success-line-tip{0%{top:1.1875em;left:.0625em;width:0}54%{top:1.0625em;left:.125em;width:0}70%{top:2.1875em;left:-.375em;width:3.125em}84%{top:3em;left:1.3125em;width:1.0625em}100%{top:2.8125em;left:.875em;width:1.5625em}}@-webkit-keyframes swal2-animate-success-line-long{0%{top:3.375em;right:2.875em;width:0}65%{top:3.375em;right:2.875em;width:0}84%{top:2.1875em;right:0;width:3.4375em}100%{top:2.375em;right:.5em;width:2.9375em}}@keyframes swal2-animate-success-line-long{0%{top:3.375em;right:2.875em;width:0}65%{top:3.375em;right:2.875em;width:0}84%{top:2.1875em;right:0;width:3.4375em}100%{top:2.375em;right:.5em;width:2.9375em}}@-webkit-keyframes swal2-rotate-success-circular-line{0%{-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}5%{-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}12%{-webkit-transform:rotate(-405deg);transform:rotate(-405deg)}100%{-webkit-transform:rotate(-405deg);transform:rotate(-405deg)}}@keyframes swal2-rotate-success-circular-line{0%{-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}5%{-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}12%{-webkit-transform:rotate(-405deg);transform:rotate(-405deg)}100%{-webkit-transform:rotate(-405deg);transform:rotate(-405deg)}}@-webkit-keyframes swal2-animate-error-x-mark{0%{margin-top:1.625em;-webkit-transform:scale(.4);transform:scale(.4);opacity:0}50%{margin-top:1.625em;-webkit-transform:scale(.4);transform:scale(.4);opacity:0}80%{margin-top:-.375em;-webkit-transform:scale(1.15);transform:scale(1.15)}100%{margin-top:0;-webkit-transform:scale(1);transform:scale(1);opacity:1}}@keyframes swal2-animate-error-x-mark{0%{margin-top:1.625em;-webkit-transform:scale(.4);transform:scale(.4);opacity:0}50%{margin-top:1.625em;-webkit-transform:scale(.4);transform:scale(.4);opacity:0}80%{margin-top:-.375em;-webkit-transform:scale(1.15);transform:scale(1.15)}100%{margin-top:0;-webkit-transform:scale(1);transform:scale(1);opacity:1}}@-webkit-keyframes swal2-animate-error-icon{0%{-webkit-transform:rotateX(100deg);transform:rotateX(100deg);opacity:0}100%{-webkit-transform:rotateX(0);transform:rotateX(0);opacity:1}}@keyframes swal2-animate-error-icon{0%{-webkit-transform:rotateX(100deg);transform:rotateX(100deg);opacity:0}100%{-webkit-transform:rotateX(0);transform:rotateX(0);opacity:1}}body.swal2-toast-shown .swal2-container{background-color:transparent}body.swal2-toast-shown .swal2-container.swal2-shown{background-color:transparent}body.swal2-toast-shown .swal2-container.swal2-top{top:0;right:auto;bottom:auto;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}body.swal2-toast-shown .swal2-container.swal2-top-end,body.swal2-toast-shown .swal2-container.swal2-top-right{top:0;right:0;bottom:auto;left:auto}body.swal2-toast-shown .swal2-container.swal2-top-left,body.swal2-toast-shown .swal2-container.swal2-top-start{top:0;right:auto;bottom:auto;left:0}body.swal2-toast-shown .swal2-container.swal2-center-left,body.swal2-toast-shown .swal2-container.swal2-center-start{top:50%;right:auto;bottom:auto;left:0;-webkit-transform:translateY(-50%);transform:translateY(-50%)}body.swal2-toast-shown .swal2-container.swal2-center{top:50%;right:auto;bottom:auto;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}body.swal2-toast-shown .swal2-container.swal2-center-end,body.swal2-toast-shown .swal2-container.swal2-center-right{top:50%;right:0;bottom:auto;left:auto;-webkit-transform:translateY(-50%);transform:translateY(-50%)}body.swal2-toast-shown .swal2-container.swal2-bottom-left,body.swal2-toast-shown .swal2-container.swal2-bottom-start{top:auto;right:auto;bottom:0;left:0}body.swal2-toast-shown .swal2-container.swal2-bottom{top:auto;right:auto;bottom:0;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}body.swal2-toast-shown .swal2-container.swal2-bottom-end,body.swal2-toast-shown .swal2-container.swal2-bottom-right{top:auto;right:0;bottom:0;left:auto}body.swal2-toast-column .swal2-toast{flex-direction:column;align-items:stretch}body.swal2-toast-column .swal2-toast .swal2-actions{flex:1;align-self:stretch;height:2.2em;margin-top:.3125em}body.swal2-toast-column .swal2-toast .swal2-loading{justify-content:center}body.swal2-toast-column .swal2-toast .swal2-input{height:2em;margin:.3125em auto;font-size:1em}body.swal2-toast-column .swal2-toast .swal2-validation-message{font-size:1em}.swal2-popup.swal2-toast{flex-direction:row;align-items:center;width:auto;padding:.625em;overflow-y:hidden;box-shadow:0 0 .625em #d9d9d9}.swal2-popup.swal2-toast .swal2-header{flex-direction:row}.swal2-popup.swal2-toast .swal2-title{flex-grow:1;justify-content:flex-start;margin:0 .6em;font-size:1em}.swal2-popup.swal2-toast .swal2-footer{margin:.5em 0 0;padding:.5em 0 0;font-size:.8em}.swal2-popup.swal2-toast .swal2-close{position:static;width:.8em;height:.8em;line-height:.8}.swal2-popup.swal2-toast .swal2-content{justify-content:flex-start;font-size:1em}.swal2-popup.swal2-toast .swal2-icon{width:2em;min-width:2em;height:2em;margin:0}.swal2-popup.swal2-toast .swal2-icon::before{display:flex;align-items:center;font-size:2em;font-weight:700}@media all and (-ms-high-contrast:none),(-ms-high-contrast:active){.swal2-popup.swal2-toast .swal2-icon::before{font-size:.25em}}.swal2-popup.swal2-toast .swal2-icon.swal2-success .swal2-success-ring{width:2em;height:2em}.swal2-popup.swal2-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line]{top:.875em;width:1.375em}.swal2-popup.swal2-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=left]{left:.3125em}.swal2-popup.swal2-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=right]{right:.3125em}.swal2-popup.swal2-toast .swal2-actions{flex-basis:auto!important;height:auto;margin:0 .3125em}.swal2-popup.swal2-toast .swal2-styled{margin:0 .3125em;padding:.3125em .625em;font-size:1em}.swal2-popup.swal2-toast .swal2-styled:focus{box-shadow:0 0 0 .0625em #fff,0 0 0 .125em rgba(50,100,150,.4)}.swal2-popup.swal2-toast .swal2-success{border-color:#a5dc86}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-circular-line]{position:absolute;width:1.6em;height:3em;-webkit-transform:rotate(45deg);transform:rotate(45deg);border-radius:50%}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-circular-line][class$=left]{top:-.8em;left:-.5em;-webkit-transform:rotate(-45deg);transform:rotate(-45deg);-webkit-transform-origin:2em 2em;transform-origin:2em 2em;border-radius:4em 0 0 4em}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-circular-line][class$=right]{top:-.25em;left:.9375em;-webkit-transform-origin:0 1.5em;transform-origin:0 1.5em;border-radius:0 4em 4em 0}.swal2-popup.swal2-toast .swal2-success .swal2-success-ring{width:2em;height:2em}.swal2-popup.swal2-toast .swal2-success .swal2-success-fix{top:0;left:.4375em;width:.4375em;height:2.6875em}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-line]{height:.3125em}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-line][class$=tip]{top:1.125em;left:.1875em;width:.75em}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-line][class$=long]{top:.9375em;right:.1875em;width:1.375em}.swal2-popup.swal2-toast.swal2-show{-webkit-animation:swal2-toast-show .5s;animation:swal2-toast-show .5s}.swal2-popup.swal2-toast.swal2-hide{-webkit-animation:swal2-toast-hide .1s forwards;animation:swal2-toast-hide .1s forwards}.swal2-popup.swal2-toast .swal2-animate-success-icon .swal2-success-line-tip{-webkit-animation:swal2-toast-animate-success-line-tip .75s;animation:swal2-toast-animate-success-line-tip .75s}.swal2-popup.swal2-toast .swal2-animate-success-icon .swal2-success-line-long{-webkit-animation:swal2-toast-animate-success-line-long .75s;animation:swal2-toast-animate-success-line-long .75s}@-webkit-keyframes swal2-toast-show{0%{-webkit-transform:translateY(-.625em) rotateZ(2deg);transform:translateY(-.625em) rotateZ(2deg)}33%{-webkit-transform:translateY(0) rotateZ(-2deg);transform:translateY(0) rotateZ(-2deg)}66%{-webkit-transform:translateY(.3125em) rotateZ(2deg);transform:translateY(.3125em) rotateZ(2deg)}100%{-webkit-transform:translateY(0) rotateZ(0);transform:translateY(0) rotateZ(0)}}@keyframes swal2-toast-show{0%{-webkit-transform:translateY(-.625em) rotateZ(2deg);transform:translateY(-.625em) rotateZ(2deg)}33%{-webkit-transform:translateY(0) rotateZ(-2deg);transform:translateY(0) rotateZ(-2deg)}66%{-webkit-transform:translateY(.3125em) rotateZ(2deg);transform:translateY(.3125em) rotateZ(2deg)}100%{-webkit-transform:translateY(0) rotateZ(0);transform:translateY(0) rotateZ(0)}}@-webkit-keyframes swal2-toast-hide{100%{-webkit-transform:rotateZ(1deg);transform:rotateZ(1deg);opacity:0}}@keyframes swal2-toast-hide{100%{-webkit-transform:rotateZ(1deg);transform:rotateZ(1deg);opacity:0}}@-webkit-keyframes swal2-toast-animate-success-line-tip{0%{top:.5625em;left:.0625em;width:0}54%{top:.125em;left:.125em;width:0}70%{top:.625em;left:-.25em;width:1.625em}84%{top:1.0625em;left:.75em;width:.5em}100%{top:1.125em;left:.1875em;width:.75em}}@keyframes swal2-toast-animate-success-line-tip{0%{top:.5625em;left:.0625em;width:0}54%{top:.125em;left:.125em;width:0}70%{top:.625em;left:-.25em;width:1.625em}84%{top:1.0625em;left:.75em;width:.5em}100%{top:1.125em;left:.1875em;width:.75em}}@-webkit-keyframes swal2-toast-animate-success-line-long{0%{top:1.625em;right:1.375em;width:0}65%{top:1.25em;right:.9375em;width:0}84%{top:.9375em;right:0;width:1.125em}100%{top:.9375em;right:.1875em;width:1.375em}}@keyframes swal2-toast-animate-success-line-long{0%{top:1.625em;right:1.375em;width:0}65%{top:1.25em;right:.9375em;width:0}84%{top:.9375em;right:0;width:1.125em}100%{top:.9375em;right:.1875em;width:1.375em}}body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown){overflow:hidden}body.swal2-height-auto{height:auto!important}body.swal2-no-backdrop .swal2-shown{top:auto;right:auto;bottom:auto;left:auto;max-width:calc(100% - .625em * 2);background-color:transparent}body.swal2-no-backdrop .swal2-shown>.swal2-modal{box-shadow:0 0 10px rgba(0,0,0,.4)}body.swal2-no-backdrop .swal2-shown.swal2-top{top:0;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}body.swal2-no-backdrop .swal2-shown.swal2-top-left,body.swal2-no-backdrop .swal2-shown.swal2-top-start{top:0;left:0}body.swal2-no-backdrop .swal2-shown.swal2-top-end,body.swal2-no-backdrop .swal2-shown.swal2-top-right{top:0;right:0}body.swal2-no-backdrop .swal2-shown.swal2-center{top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}body.swal2-no-backdrop .swal2-shown.swal2-center-left,body.swal2-no-backdrop .swal2-shown.swal2-center-start{top:50%;left:0;-webkit-transform:translateY(-50%);transform:translateY(-50%)}body.swal2-no-backdrop .swal2-shown.swal2-center-end,body.swal2-no-backdrop .swal2-shown.swal2-center-right{top:50%;right:0;-webkit-transform:translateY(-50%);transform:translateY(-50%)}body.swal2-no-backdrop .swal2-shown.swal2-bottom{bottom:0;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}body.swal2-no-backdrop .swal2-shown.swal2-bottom-left,body.swal2-no-backdrop .swal2-shown.swal2-bottom-start{bottom:0;left:0}body.swal2-no-backdrop .swal2-shown.swal2-bottom-end,body.swal2-no-backdrop .swal2-shown.swal2-bottom-right{right:0;bottom:0}.swal2-container{display:flex;position:fixed;z-index:1060;top:0;right:0;bottom:0;left:0;flex-direction:row;align-items:center;justify-content:center;padding:.625em;overflow-x:hidden;background-color:transparent;-webkit-overflow-scrolling:touch}.swal2-container.swal2-top{align-items:flex-start}.swal2-container.swal2-top-left,.swal2-container.swal2-top-start{align-items:flex-start;justify-content:flex-start}.swal2-container.swal2-top-end,.swal2-container.swal2-top-right{align-items:flex-start;justify-content:flex-end}.swal2-container.swal2-center{align-items:center}.swal2-container.swal2-center-left,.swal2-container.swal2-center-start{align-items:center;justify-content:flex-start}.swal2-container.swal2-center-end,.swal2-container.swal2-center-right{align-items:center;justify-content:flex-end}.swal2-container.swal2-bottom{align-items:flex-end}.swal2-container.swal2-bottom-left,.swal2-container.swal2-bottom-start{align-items:flex-end;justify-content:flex-start}.swal2-container.swal2-bottom-end,.swal2-container.swal2-bottom-right{align-items:flex-end;justify-content:flex-end}.swal2-container.swal2-bottom-end>:first-child,.swal2-container.swal2-bottom-left>:first-child,.swal2-container.swal2-bottom-right>:first-child,.swal2-container.swal2-bottom-start>:first-child,.swal2-container.swal2-bottom>:first-child{margin-top:auto}.swal2-container.swal2-grow-fullscreen>.swal2-modal{display:flex!important;flex:1;align-self:stretch;justify-content:center}.swal2-container.swal2-grow-row>.swal2-modal{display:flex!important;flex:1;align-content:center;justify-content:center}.swal2-container.swal2-grow-column{flex:1;flex-direction:column}.swal2-container.swal2-grow-column.swal2-bottom,.swal2-container.swal2-grow-column.swal2-center,.swal2-container.swal2-grow-column.swal2-top{align-items:center}.swal2-container.swal2-grow-column.swal2-bottom-left,.swal2-container.swal2-grow-column.swal2-bottom-start,.swal2-container.swal2-grow-column.swal2-center-left,.swal2-container.swal2-grow-column.swal2-center-start,.swal2-container.swal2-grow-column.swal2-top-left,.swal2-container.swal2-grow-column.swal2-top-start{align-items:flex-start}.swal2-container.swal2-grow-column.swal2-bottom-end,.swal2-container.swal2-grow-column.swal2-bottom-right,.swal2-container.swal2-grow-column.swal2-center-end,.swal2-container.swal2-grow-column.swal2-center-right,.swal2-container.swal2-grow-column.swal2-top-end,.swal2-container.swal2-grow-column.swal2-top-right{align-items:flex-end}.swal2-container.swal2-grow-column>.swal2-modal{display:flex!important;flex:1;align-content:center;justify-content:center}.swal2-container:not(.swal2-top):not(.swal2-top-start):not(.swal2-top-end):not(.swal2-top-left):not(.swal2-top-right):not(.swal2-center-start):not(.swal2-center-end):not(.swal2-center-left):not(.swal2-center-right):not(.swal2-bottom):not(.swal2-bottom-start):not(.swal2-bottom-end):not(.swal2-bottom-left):not(.swal2-bottom-right):not(.swal2-grow-fullscreen)>.swal2-modal{margin:auto}@media all and (-ms-high-contrast:none),(-ms-high-contrast:active){.swal2-container .swal2-modal{margin:0!important}}.swal2-container.swal2-fade{transition:background-color .1s}.swal2-container.swal2-shown{background-color:rgba(0,0,0,.4)}.swal2-popup{display:none;position:relative;box-sizing:border-box;flex-direction:column;justify-content:center;width:32em;max-width:100%;padding:1.25em;border:none;border-radius:.3125em;background:#fff;font-family:inherit;font-size:1rem}.swal2-popup:focus{outline:0}.swal2-popup.swal2-loading{overflow-y:hidden}.swal2-header{display:flex;flex-direction:column;align-items:center}.swal2-title{position:relative;max-width:100%;margin:0 0 .4em;padding:0;color:#595959;font-size:1.875em;font-weight:600;text-align:center;text-transform:none;word-wrap:break-word}.swal2-actions{z-index:1;flex-wrap:wrap;align-items:center;justify-content:center;width:100%;margin:1.25em auto 0}.swal2-actions:not(.swal2-loading) .swal2-styled[disabled]{opacity:.4}.swal2-actions:not(.swal2-loading) .swal2-styled:hover{background-image:linear-gradient(rgba(0,0,0,.1),rgba(0,0,0,.1))}.swal2-actions:not(.swal2-loading) .swal2-styled:active{background-image:linear-gradient(rgba(0,0,0,.2),rgba(0,0,0,.2))}.swal2-actions.swal2-loading .swal2-styled.swal2-confirm{box-sizing:border-box;width:2.5em;height:2.5em;margin:.46875em;padding:0;-webkit-animation:swal2-rotate-loading 1.5s linear 0s infinite normal;animation:swal2-rotate-loading 1.5s linear 0s infinite normal;border:.25em solid transparent;border-radius:100%;border-color:transparent;background-color:transparent!important;color:transparent;cursor:default;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.swal2-actions.swal2-loading .swal2-styled.swal2-cancel{margin-right:30px;margin-left:30px}.swal2-actions.swal2-loading :not(.swal2-styled).swal2-confirm::after{content:\"\";display:inline-block;width:15px;height:15px;margin-left:5px;-webkit-animation:swal2-rotate-loading 1.5s linear 0s infinite normal;animation:swal2-rotate-loading 1.5s linear 0s infinite normal;border:3px solid #999;border-radius:50%;border-right-color:transparent;box-shadow:1px 1px 1px #fff}.swal2-styled{margin:.3125em;padding:.625em 2em;box-shadow:none;font-weight:500}.swal2-styled:not([disabled]){cursor:pointer}.swal2-styled.swal2-confirm{border:0;border-radius:.25em;background:initial;background-color:#3085d6;color:#fff;font-size:1.0625em}.swal2-styled.swal2-cancel{border:0;border-radius:.25em;background:initial;background-color:#aaa;color:#fff;font-size:1.0625em}.swal2-styled:focus{outline:0;box-shadow:0 0 0 2px #fff,0 0 0 4px rgba(50,100,150,.4)}.swal2-styled::-moz-focus-inner{border:0}.swal2-footer{justify-content:center;margin:1.25em 0 0;padding:1em 0 0;border-top:1px solid #eee;color:#545454;font-size:1em}.swal2-image{max-width:100%;margin:1.25em auto}.swal2-close{position:absolute;top:0;right:0;justify-content:center;width:1.2em;height:1.2em;padding:0;overflow:hidden;transition:color .1s ease-out;border:none;border-radius:0;outline:initial;background:0 0;color:#ccc;font-family:serif;font-size:2.5em;line-height:1.2;cursor:pointer}.swal2-close:hover{-webkit-transform:none;transform:none;background:0 0;color:#f27474}.swal2-content{z-index:1;justify-content:center;margin:0;padding:0;color:#545454;font-size:1.125em;font-weight:300;line-height:normal;word-wrap:break-word}#swal2-content{text-align:center}.swal2-checkbox,.swal2-file,.swal2-input,.swal2-radio,.swal2-select,.swal2-textarea{margin:1em auto}.swal2-file,.swal2-input,.swal2-textarea{box-sizing:border-box;width:100%;transition:border-color .3s,box-shadow .3s;border:1px solid #d9d9d9;border-radius:.1875em;background:inherit;box-shadow:inset 0 1px 1px rgba(0,0,0,.06);color:inherit;font-size:1.125em}.swal2-file.swal2-inputerror,.swal2-input.swal2-inputerror,.swal2-textarea.swal2-inputerror{border-color:#f27474!important;box-shadow:0 0 2px #f27474!important}.swal2-file:focus,.swal2-input:focus,.swal2-textarea:focus{border:1px solid #b4dbed;outline:0;box-shadow:0 0 3px #c4e6f5}.swal2-file::-webkit-input-placeholder,.swal2-input::-webkit-input-placeholder,.swal2-textarea::-webkit-input-placeholder{color:#ccc}.swal2-file::-moz-placeholder,.swal2-input::-moz-placeholder,.swal2-textarea::-moz-placeholder{color:#ccc}.swal2-file:-ms-input-placeholder,.swal2-input:-ms-input-placeholder,.swal2-textarea:-ms-input-placeholder{color:#ccc}.swal2-file::-ms-input-placeholder,.swal2-input::-ms-input-placeholder,.swal2-textarea::-ms-input-placeholder{color:#ccc}.swal2-file::placeholder,.swal2-input::placeholder,.swal2-textarea::placeholder{color:#ccc}.swal2-range{margin:1em auto;background:inherit}.swal2-range input{width:80%}.swal2-range output{width:20%;color:inherit;font-weight:600;text-align:center}.swal2-range input,.swal2-range output{height:2.625em;padding:0;font-size:1.125em;line-height:2.625em}.swal2-input{height:2.625em;padding:0 .75em}.swal2-input[type=number]{max-width:10em}.swal2-file{background:inherit;font-size:1.125em}.swal2-textarea{height:6.75em;padding:.75em}.swal2-select{min-width:50%;max-width:100%;padding:.375em .625em;background:inherit;color:inherit;font-size:1.125em}.swal2-checkbox,.swal2-radio{align-items:center;justify-content:center;background:inherit;color:inherit}.swal2-checkbox label,.swal2-radio label{margin:0 .6em;font-size:1.125em}.swal2-checkbox input,.swal2-radio input{margin:0 .4em}.swal2-validation-message{display:none;align-items:center;justify-content:center;padding:.625em;overflow:hidden;background:#f0f0f0;color:#666;font-size:1em;font-weight:300}.swal2-validation-message::before{content:\"!\";display:inline-block;width:1.5em;min-width:1.5em;height:1.5em;margin:0 .625em;zoom:normal;border-radius:50%;background-color:#f27474;color:#fff;font-weight:600;line-height:1.5em;text-align:center}@supports (-ms-accelerator:true){.swal2-range input{width:100%!important}.swal2-range output{display:none}}@media all and (-ms-high-contrast:none),(-ms-high-contrast:active){.swal2-range input{width:100%!important}.swal2-range output{display:none}}@-moz-document url-prefix(){.swal2-close:focus{outline:2px solid rgba(50,100,150,.4)}}.swal2-icon{position:relative;box-sizing:content-box;justify-content:center;width:5em;height:5em;margin:1.25em auto 1.875em;zoom:normal;border:.25em solid transparent;border-radius:50%;line-height:5em;cursor:default;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.swal2-icon::before{display:flex;align-items:center;height:92%;font-size:3.75em}.swal2-icon.swal2-error{border-color:#f27474}.swal2-icon.swal2-error .swal2-x-mark{position:relative;flex-grow:1}.swal2-icon.swal2-error [class^=swal2-x-mark-line]{display:block;position:absolute;top:2.3125em;width:2.9375em;height:.3125em;border-radius:.125em;background-color:#f27474}.swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=left]{left:1.0625em;-webkit-transform:rotate(45deg);transform:rotate(45deg)}.swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=right]{right:1em;-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}.swal2-icon.swal2-warning{border-color:#facea8;color:#f8bb86}.swal2-icon.swal2-warning::before{content:\"!\"}.swal2-icon.swal2-info{border-color:#9de0f6;color:#3fc3ee}.swal2-icon.swal2-info::before{content:\"i\"}.swal2-icon.swal2-question{border-color:#c9dae1;color:#87adbd}.swal2-icon.swal2-question::before{content:\"?\"}.swal2-icon.swal2-question.swal2-arabic-question-mark::before{content:\"؟\"}.swal2-icon.swal2-success{border-color:#a5dc86}.swal2-icon.swal2-success [class^=swal2-success-circular-line]{position:absolute;width:3.75em;height:7.5em;-webkit-transform:rotate(45deg);transform:rotate(45deg);border-radius:50%}.swal2-icon.swal2-success [class^=swal2-success-circular-line][class$=left]{top:-.4375em;left:-2.0635em;-webkit-transform:rotate(-45deg);transform:rotate(-45deg);-webkit-transform-origin:3.75em 3.75em;transform-origin:3.75em 3.75em;border-radius:7.5em 0 0 7.5em}.swal2-icon.swal2-success [class^=swal2-success-circular-line][class$=right]{top:-.6875em;left:1.875em;-webkit-transform:rotate(-45deg);transform:rotate(-45deg);-webkit-transform-origin:0 3.75em;transform-origin:0 3.75em;border-radius:0 7.5em 7.5em 0}.swal2-icon.swal2-success .swal2-success-ring{position:absolute;z-index:2;top:-.25em;left:-.25em;box-sizing:content-box;width:100%;height:100%;border:.25em solid rgba(165,220,134,.3);border-radius:50%}.swal2-icon.swal2-success .swal2-success-fix{position:absolute;z-index:1;top:.5em;left:1.625em;width:.4375em;height:5.625em;-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}.swal2-icon.swal2-success [class^=swal2-success-line]{display:block;position:absolute;z-index:2;height:.3125em;border-radius:.125em;background-color:#a5dc86}.swal2-icon.swal2-success [class^=swal2-success-line][class$=tip]{top:2.875em;left:.875em;width:1.5625em;-webkit-transform:rotate(45deg);transform:rotate(45deg)}.swal2-icon.swal2-success [class^=swal2-success-line][class$=long]{top:2.375em;right:.5em;width:2.9375em;-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}.swal2-progress-steps{align-items:center;margin:0 0 1.25em;padding:0;background:inherit;font-weight:600}.swal2-progress-steps li{display:inline-block;position:relative}.swal2-progress-steps .swal2-progress-step{z-index:20;width:2em;height:2em;border-radius:2em;background:#3085d6;color:#fff;line-height:2em;text-align:center}.swal2-progress-steps .swal2-progress-step.swal2-active-progress-step{background:#3085d6}.swal2-progress-steps .swal2-progress-step.swal2-active-progress-step~.swal2-progress-step{background:#add8e6;color:#fff}.swal2-progress-steps .swal2-progress-step.swal2-active-progress-step~.swal2-progress-step-line{background:#add8e6}.swal2-progress-steps .swal2-progress-step-line{z-index:10;width:2.5em;height:.4em;margin:0 -1px;background:#3085d6}[class^=swal2]{-webkit-tap-highlight-color:transparent}.swal2-show{-webkit-animation:swal2-show .3s;animation:swal2-show .3s}.swal2-show.swal2-noanimation{-webkit-animation:none;animation:none}.swal2-hide{-webkit-animation:swal2-hide .15s forwards;animation:swal2-hide .15s forwards}.swal2-hide.swal2-noanimation{-webkit-animation:none;animation:none}.swal2-rtl .swal2-close{right:auto;left:0}.swal2-animate-success-icon .swal2-success-line-tip{-webkit-animation:swal2-animate-success-line-tip .75s;animation:swal2-animate-success-line-tip .75s}.swal2-animate-success-icon .swal2-success-line-long{-webkit-animation:swal2-animate-success-line-long .75s;animation:swal2-animate-success-line-long .75s}.swal2-animate-success-icon .swal2-success-circular-line-right{-webkit-animation:swal2-rotate-success-circular-line 4.25s ease-in;animation:swal2-rotate-success-circular-line 4.25s ease-in}.swal2-animate-error-icon{-webkit-animation:swal2-animate-error-icon .5s;animation:swal2-animate-error-icon .5s}.swal2-animate-error-icon .swal2-x-mark{-webkit-animation:swal2-animate-error-x-mark .5s;animation:swal2-animate-error-x-mark .5s}@-webkit-keyframes swal2-rotate-loading{0%{-webkit-transform:rotate(0);transform:rotate(0)}100%{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}@keyframes swal2-rotate-loading{0%{-webkit-transform:rotate(0);transform:rotate(0)}100%{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}@media print{body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown){overflow-y:scroll!important}body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown)>[aria-hidden=true]{display:none}body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) .swal2-container{position:static!important}}");
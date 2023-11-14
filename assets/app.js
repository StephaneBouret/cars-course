/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';

require('bootstrap');
// Range Slider
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';
import Filter from './js/modules/Filter.js';
import { Carousel } from "bootstrap";

new Filter(document.querySelector('.js-filter'))

const priceSlider = document.getElementById('price-slider');
const kmsSlider = document.getElementById('kms-slider');
const dateSlider = document.getElementById('date-slider');

if (priceSlider) {
    const min = document.getElementById('minPrice');
    const max = document.getElementById('maxPrice');
    const minValue = Math.floor(parseInt(priceSlider.dataset.min, 10) / 100);
    const maxValue = Math.ceil(parseInt(priceSlider.dataset.max, 10) / 100);
    const range = noUiSlider.create(priceSlider, {
        start: [min.value || minValue, max.value || maxValue],
        connect: true,
        step: 10,
        range: {
            'min': minValue,
            'max': maxValue
        }
    });
    range.on('slide', function (values, handle) {
        if (handle === 0) {
            min.value = Math.round(values[0])
        }
        if (handle === 1) {
            max.value = Math.round(values[1])
        }
    })
    range.on('end', function (values, handle) {
        if (handle === 0) {
            min.dispatchEvent(new Event('change'))
        } else {
            max.dispatchEvent(new Event('change'))
        }
    })
}

if (kmsSlider) {
    const min = document.getElementById('minKms');
    const max = document.getElementById('maxKms');
    const minValue = Math.floor(parseInt(kmsSlider.dataset.min, 10));
    const maxValue = Math.ceil(parseInt(kmsSlider.dataset.max, 10));
    const range = noUiSlider.create(kmsSlider, {
        start: [min.value || minValue, max.value || maxValue],
        connect: true,
        step: 10,
        range: {
            'min': minValue,
            'max': maxValue
        }
    });
    range.on('slide', function (values, handle) {
        if (handle === 0) {
            min.value = Math.round(values[0])
        }
        if (handle === 1) {
            max.value = Math.round(values[1])
        }
    })
    range.on('end', function (values, handle) {
        if (handle === 0) {
            min.dispatchEvent(new Event('change'))
        } else {
            max.dispatchEvent(new Event('change'))
        }
    })
}

if (dateSlider) {
    const min = document.getElementById('minCirculationAt');
    const max = document.getElementById('maxCirculationAt');
    const timestamp = (str) => {
        return new Date(str).getFullYear()
    }
    const minValue = parseInt(timestamp(dateSlider.dataset.min), 10);
    const maxValue = parseInt(timestamp(dateSlider.dataset.max), 10);
    const range = noUiSlider.create(dateSlider, {
        start: [min.value || minValue, max.value || maxValue],
        connect: true,
        step: 1,
        range: {
            'min': minValue,
            'max': maxValue
        }
    });
    range.on('slide', function (values, handle) {
        if (handle === 0) {
            min.value = Math.round(values[0])
        }
        if (handle === 1) {
            max.value = Math.round(values[1])
        }
    })
    range.on('end', function (values, handle) {
        if (handle === 0) {
            min.dispatchEvent(new Event('change'))
        } else {
            max.dispatchEvent(new Event('change'))
        }
    })
}

// Close alert message after 5 secondes
const alert = document.querySelector('.alert')
if (alert) {
    setTimeout(function () {
        alert.style.transition = "opacity 1s ease";
        alert.style.opacity = '0';
    
        setTimeout(function () {
            alert.style.display = 'none';
        }, 500); // After the fade-out animation (0.5 second)
    }, 5000); // After 5 seconds
}

// Star rating
const ratingInput = document.querySelector('#comment_form_rating');
if (ratingInput) {
    const starRating = document.querySelector('.star-rating');

    starRating.addEventListener('click', function (event) {
        if (event.target.matches('i')) {
            const ratingValue = event.target.getAttribute('data-rating');
            ratingInput.value = ratingValue;
            // Remove 'far' class and add 'fas' class for selected stars
            starRating.querySelectorAll('i').forEach(function (star) {
                const starRatingValue = star.getAttribute('data-rating')
                if (starRatingValue <= ratingValue) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            })
        }
    })
}
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
import { Carousel } from "bootstrap";

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

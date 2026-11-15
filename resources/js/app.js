import './bootstrap';
import 'bootstrap';
import 'owl.carousel';
import 'owl.carousel/dist/assets/owl.carousel.css';
import { init as shareOnInit } from "shareon";
import "shareon/css"; // most bundlers will transpile this CSS
import 'fslightbox';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

shareOnInit();

const carouselNavText = [
   '<svg width="35" height="34" viewBox="0 0 35 34" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.5127 22.1558L16.4083 16.9938L21.5703 11.8893L19.2337 9.50949L11.6917 16.9673L19.1495 24.5093L21.5127 22.1558ZM34.1913 17.0935C34.243 7.89367 26.8183 0.38523 17.6184 0.333611C8.41859 0.281992 0.910146 7.70665 0.858527 16.9065C0.806908 26.1064 8.23156 33.6148 17.4314 33.6664C26.6313 33.718 34.1397 26.2934 34.1913 17.0935ZM4.19181 16.9252C4.23314 9.55865 10.2332 3.62556 17.5997 3.66689C24.9663 3.70822 30.8994 9.70828 30.8581 17.0748C30.8167 24.4414 24.8167 30.3745 17.4501 30.3331C10.0836 30.2918 4.15048 24.2918 4.19181 16.9252Z" fill="#4E9E9D"/></svg>',
   '<svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.973 15.9647L22.2282 20.9731L17.2198 26.2284L19.626 28.5379L26.9435 20.8597L19.2654 13.5422L16.973 15.9647ZM4.45005 21.4007C4.67125 30.5981 12.3153 37.883 21.5126 37.6618C30.71 37.4406 37.9949 29.7966 37.7738 20.5993C37.5526 11.4019 29.9085 4.11694 20.7112 4.33814C11.5138 4.55934 4.22885 12.2034 4.45005 21.4007ZM34.4414 20.6794C34.6185 28.044 28.797 34.1524 21.4325 34.3295C14.0679 34.5066 7.95954 28.6851 7.78242 21.3206C7.6053 13.956 13.4268 7.84763 20.7913 7.67051C28.1559 7.49339 34.2643 13.3149 34.4414 20.6794Z" fill="#4E9E9D"/></svg>'
];

$(function() {
   $('.town-carousel').owlCarousel({
      loop: true,
      nav: true,
      margin: 20,
      dots: false,
      navText: carouselNavText,
      responsive: {
         0: {
            items: 1.2,
            nav: false,
         },
         768: {
            items: 2.2
         },
         1024: {
            items: 3.2
         },
         1280: {
            items: 4
         }
      }
   });

   $('.toggle-property-filter').on('click', function() {
      $('.property-filter').toggleClass('show');
   });

   const propertyImgCarousel = $('.property-images').owlCarousel({
      loop: true,
      nav: false,
      margin: 20,
      dots: false,
      responsive: {
         0: {
            items: 1.2,
         },
         768: {
            items: 2.2
         },
         1024: {
            items: 3.2
         },
         1280: {
            items: 4
         }
      }
   });
});

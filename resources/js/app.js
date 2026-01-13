import './bootstrap';
import AOS from 'aos';
import 'aos/dist/aos.css'; // You can also use <link> for styles
AOS.init();



import Swiper from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import { Navigation, Thumbs } from 'swiper/modules';

document.addEventListener('DOMContentLoaded', () => {
    let thumbsSwiper = new Swiper('.swiper-thumbs', {
        direction: 'vertical',
        slidesPerView: 3,
        spaceBetween: 10,
        watchSlidesProgress: true,
    });

    new Swiper('.swiper-main', {
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: thumbsSwiper,
        },
        modules: [Navigation, Thumbs],
    });

    const testimonialSlider = new Swiper('.testimonial-slider', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        navigation: {
            nextEl: '.nav-next',
            prevEl: '.nav-prev',
        },
        modules: [Navigation],
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            }
        }
    });

    const teamSlider = new Swiper('.team-slider', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        navigation: {
            nextEl: '.slider-nav--next',
            prevEl: '.slider-nav--prev',
        },
        modules: [Navigation],
        breakpoints: {
            576: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 4,
            }
        }
    });
});

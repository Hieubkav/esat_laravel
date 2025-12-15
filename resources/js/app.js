import './bootstrap';
import 'preline';
import 'flowbite';

// Import Swiper
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

// Swiper initialization
Swiper.use([Navigation, Pagination, Autoplay]);

// Make Swiper available globally
window.Swiper = Swiper;

// Khởi tạo lại drawer menu
document.addEventListener('DOMContentLoaded', function() {
    const drawer = document.getElementById('drawer-navigation');
    if (drawer) {
        const initDrawer = new Drawer(drawer);
    }
});

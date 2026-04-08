import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Unified Icon Initialization Placeholder (In case of future dynamic icons)
const initializeIcons = () => {
    // Bootstrap Icons are handled via CSS classes, no JS initialization needed.
};

// Global access for compatibility
window.initializeIcons = initializeIcons;
window.lucide = { 
    createIcons: () => {}, 
    icons: {} 
};

// Listeners
document.addEventListener('DOMContentLoaded', initializeIcons);
document.addEventListener('livewire:navigated', initializeIcons);
window.addEventListener('load', initializeIcons);

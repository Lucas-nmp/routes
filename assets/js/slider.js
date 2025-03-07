// Función para inicializar el slider
function initializeSlider() {
    let nextBtn = document.querySelector('.next');
    let prevBtn = document.querySelector('.prev');

    let slider = document.querySelector('.slider');
    let sliderList = slider.querySelector('.list');
    let thumbnail = document.querySelector('.slider .thumbnail');
    let thumbnailItems = thumbnail.querySelectorAll('.item');

    // Mueve el primer ítem del thumbnail al final
    thumbnail.appendChild(thumbnailItems[0]);

    // Función para mover el slider
    function moveSlider(direction) {
        let sliderItems = sliderList.querySelectorAll('.item');
        let thumbnailItems = document.querySelectorAll('.thumbnail .item');

        if (direction === 'next') {
            sliderList.appendChild(sliderItems[0]);
            thumbnail.appendChild(thumbnailItems[0]);
            slider.classList.add('next');
        } else {
            sliderList.prepend(sliderItems[sliderItems.length - 1]);
            thumbnail.prepend(thumbnailItems[thumbnailItems.length - 1]);
            slider.classList.add('prev');
        }

        // Remover la clase de animación después de que termine
        slider.addEventListener('animationend', function () {
            if (direction === 'next') {
                slider.classList.remove('next');
            } else {
                slider.classList.remove('prev');
            }
        }, { once: true }); // El evento se elimina después de ejecutarse una vez
    }

    // Asignar event listeners a los botones
    if (nextBtn) {
        nextBtn.onclick = function () {
            moveSlider('next');
        };
    }

    if (prevBtn) {
        prevBtn.onclick = function () {
            moveSlider('prev');
        };
    }
}

// Inicializar el slider cuando la página se carga por primera vez
document.addEventListener('DOMContentLoaded', initializeSlider);

// Re-inicializar el slider cada vez que Turbo cargue nuevo contenido
document.addEventListener('turbo:load', initializeSlider);
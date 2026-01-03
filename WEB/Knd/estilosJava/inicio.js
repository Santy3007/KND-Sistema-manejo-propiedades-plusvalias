document.addEventListener("DOMContentLoaded", function() {
    const propiedades = document.querySelectorAll('.propiedad');

    propiedades.forEach(propiedad => {
        const imgElement = propiedad.querySelector('.propiedad-img');
        const prevBtn = propiedad.querySelector('.prev-btn');
        const nextBtn = propiedad.querySelector('.next-btn');
        
        let imgIndex = 0;
        const imgList = imgElement.src.split(',');

        prevBtn.addEventListener('click', () => {
            imgIndex = imgIndex > 0 ? imgIndex - 1 : imgList.length - 1;
            imgElement.src = imgList[imgIndex];
        });

        nextBtn.addEventListener('click', () => {
            imgIndex = imgIndex < imgList.length - 1 ? imgIndex + 1 : 0;
            imgElement.src = imgList[imgIndex];
        });
    });
});




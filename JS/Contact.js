const bg = document.getElementById('bg-slider');
    const images = [
        'Images/school-photo.avif',
        'Images/students.jpg',
        'Images/hands-llaptop.jpg'
    ];

    let i = 0;

    function changeBG() {
        i = (i + 1) % images.length;
        bg.style.backgroundImage = `url('${images[i]}')`;
    }

    bg.style.backgroundImage = `url('${images[0]}')`;
    setInterval(changeBG, 4000);
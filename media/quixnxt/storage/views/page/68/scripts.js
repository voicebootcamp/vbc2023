(function(){ function initSlider() { qxUIkit.slider("#video-carousel-qx-video-carousel-2yx9m", { autoplay: 1, pauseOnHover: 1, autoplayInterval: 5000, finite: false }); } if(window.qxUIkit && typeof window.qxUIkit.slider == 'function') { initSlider(); } else { window.qxUIkit = window.qxUIkit || {}; window.qxUIkit.sliderQueue = window.qxUIkit.sliderQueue ||[]; window.qxUIkit.sliderQueue.push(initSlider); } })()
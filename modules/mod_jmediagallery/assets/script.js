
jQuery(document).ready(function($) {

	var filterList = {
	
		init: function () {
		
            $('#gridItesList').mixItUp({
                selectors: {
                    target: '.grid-item',
                    filter: '.filter'
                },
                load: {
                    filter: 'all' // show all tab on first load
                }               
            });								
		
		}

	};
	
	// Run the show!
    filterList.init();
         

    $('.image-link').magnificPopup({
        type:'image',
        gallery:{
            enabled:true
        }        
    });
});
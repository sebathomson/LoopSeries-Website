/*function selected_categories() {
	$('.nav-categories').each(function(i,e) {
		
		// Gets the active li
		$active_li = $(this).find('li.active');

		// vars
		var arrow_position_left = 0;
		var arrow_position_top 	= 2;
		
		// Positionating the selected arrow according browser. Chrome knows LI position, firefox goes for UL position
		if(navigator.userAgent.match(/webkit/i)) { 
			arrow_position_left = ($active_li.width() / 2);
			arrow_position_top 	= ($active_li.height() + 1); 
		} else if (navigator.userAgent.match(/mozilla/i)) {
			arrow_position_left = ($active_li.width() / 2) + $active_li.position().left;
			arrow_position_top 	= ($active_li.height() + $active_li.position().top + 2);
		}
		
		// Creates the arrow 
		$active_li.append($('<span/>').addClass('selected_arrow').css('left', arrow_position_left).css('top',arrow_position_top));
		
	});
}*/

$(document).ready(function(e) {
	
	// Select Categories
	//selected_categories();
	
	// On categorie click
	$('.nav-categories li').not(".disabled").click(function(e){
		e.preventDefault();
		$(this).parents('.nav-categories').find('li.active').removeClass('active');//.find('span.selected_arrow').remove();
		$(this).addClass('active');
		$(this).blur();
		//selected_categories();
	});
		
});
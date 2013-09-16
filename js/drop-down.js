jQuery(document).ready(function($){
	$('.drop-down-select').change(function(){
		var key = $(this).attr('key');
		//alert(key);
		var val = $(this).val();
		
		u = new Url;
		
		switch(key){
		case 'ID':
			u.query.ID = val;
			break;
		case 'GW':
			u.query.GW = val;
			break;
		case 'GD':
			u.query.GD = val;
			break;			
		case 'OD':
			u.query.OD = val;
			break;
		case 'OT':
			u.query.OT = val;
			break;
		case 'ES':
			u.query.ES = val;
			break;
		case 'STD':
			u.query.STD = val;
			break;
			
		}
				
		
		window.location.href = u;
		
	});	
});
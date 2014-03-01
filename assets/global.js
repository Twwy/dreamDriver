
///////////////////////////////
//Design By Everyone's Dreams//
///////////////////////////////

$._messengerDefaults = {
			extraClasses: 'messenger-fixed messenger-theme-block messenger-on-top'
		}
function successInfo(msg, action){
	if(!arguments[1]) action = function() {window.location.reload();}	
	$.globalMessenger().post({
		message: msg, 
		type:"success", 
		showCloseButton: true,
		actions: {
			enter: {
				label: "刷新",
				phrase: '',
				auto: true,
				delay: 1,
				action: action					
			}
		}
	});
}
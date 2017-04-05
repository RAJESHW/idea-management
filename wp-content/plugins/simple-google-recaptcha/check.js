window.onload = function () {
	button = document.querySelector("[id*='submit']").id;
	document.getElementById(button).disabled = true;	
	document.getElementById(button).title = sgr_recaptcha_trans.title;
}

function enableBtn() {
	document.getElementById(button).disabled = false;
	document.getElementById(button).removeAttribute("title");
}
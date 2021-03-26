function numeroAleatorio(min, max) {
  return Math.round(Math.random() * (max - min) + min);
}

numeroale = numeroAleatorio(0, 3);

if (numeroale = 1)  {
	$("#botonwhap").append("<a href=\"https://api.whatsapp.com/send?phone=522224964886&amp;text=Quiero%20informaci%C3%B3n%20sobre%20\" target=\"_blank\"><i style=\"margin-top:7.5px; margin-left:2.5px; font-size: 40px;\"> <img src=\"images/whatsapp-logo.gif\"  style=\"position:fixed; width:55px; height:55px; bottom:90px; right:25px; background-color:#00AD33; color:#FFF; border-radius:50px; text-align:center; box-shadow: 2px 2px 3px rgba(0,0,0,0.3); z-index: 99;\"></i></a>");
}else {
	$("#botonwhap").append("<a href=\"https://api.whatsapp.com/send?phone=522212789950&amp;text=Quiero%20informaci%C3%B3n%20sobre%20\" target=\"_blank\"><i style=\"margin-top:7.5px; margin-left:2.5px; font-size: 40px;\"> <img src=\"images/whatsapp-logo.gif\"  style=\"position:fixed; width:55px; height:55px; bottom:90px; right:25px; background-color:#00AD33; color:#FFF; border-radius:50px; text-align:center; box-shadow: 2px 2px 3px rgba(0,0,0,0.3); z-index: 99;\"></i></a>");
}

  




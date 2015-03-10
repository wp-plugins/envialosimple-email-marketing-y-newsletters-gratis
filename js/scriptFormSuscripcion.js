var tipoCampo;
var modificandoOpcion = 'false';
var opcionModificadaName = "";

jQuery(document).ready(function(){

jQuery("#agregarValor").click(function(){    
    jQuery('#contenedorCamposValores').show(70);
    jQuery(this).hide(50);    
});

jQuery("#listadoValores").on("click",".eliminar-valor-listado",function(){    
    jQuery(this).parent().remove();    
});

jQuery("#listadoValores").on("click",".editar-valor-listado",function(){    
    
    if(jQuery('#contenedorCamposValores').css("display") == 'none'){
        jQuery("#agregarValor").click();    
    }
     
    opcion = jQuery(this).parent();
    
    jQuery("input[name=input-opcion-valor]").val(opcion.attr("name"));
    jQuery("input[name=input-opcion-texto]").val(opcion.find("span").html());
    
    jQuery('#agregar-valor-bt').html("Modificar Opción");    
    opcionModificadaName = opcion.attr("name");    
    modificandoOpcion = 'true';
});


jQuery("input[name=input-opcion-valor]").keypress(function(e){
     
    var codigo = (e.keyCode ? e.keyCode : e.which);
       
    if(codigo == 13){
        enterPress = 'true';
        e.preventDefault();        
        jQuery("#agregar-valor-bt").click();
    }
    
});

jQuery("#agregar-valor-bt").click(function(){
    
    if(modificandoOpcion === 'true'){
        
        modificar = jQuery("#listadoValores ul").find("li[name="+opcionModificadaName+"]");
        
        modificar.attr("name",jQuery("input[name=input-opcion-valor]").val());
        modificar.find("span").first().html(jQuery("input[name=input-opcion-texto]").val());
        modificar.find("input").val(jQuery("input[name=input-opcion-valor]").val())
        
        modificandoOpcion = 'false';
        opcionModificadaName = '';
        
        jQuery('.input-opcion').val("");
        jQuery('#agregar-valor-bt').html("Agregar Opción"); 
        return false;
    }
    
    
    var camposVacios = 'false'
    
    jQuery('.input-opcion').css("border","1px solid #DFDFDF");    
    
    jQuery.each(jQuery('.input-opcion'),function(){
        
        if(jQuery(this).val() === ''){
            camposVacios = 'true'
            jQuery(this).css("border","1px solid red");            
        }
        
    });
    
    if(camposVacios === 'true'){
        alert("Los campos no pueden estar Vacíos");
        return false;
    }    
    
    id = jQuery("input[name=input-opcion-valor]").val();    
    text = jQuery("input[name=input-opcion-texto]").val();    
    
    jQuery("#listadoValores ul").append('<li name="'+id+'"><span>'+text+'</span><input type="hidden" name="ValueList[]" value="'+id+'" /> <span class="eliminar-valor-listado accion-listado"></span><span class="editar-valor-listado accion-listado"></span></li>')
    
    jQuery('.input-opcion').val("");
    
    enterPress = 'false';
});

jQuery("#cancelar-agregar-valor-bt").click(function(){
    
   jQuery('#contenedorCamposValores').hide(70);
    jQuery("#agregarValor").show(50);
});


jQuery("#tipo-campo").ddslick({
    width: 260,
    imagePosition: "left",
    selectText: "Select your favorite social network",
    onSelected: function (data) {
                
        tipoCampo = data.selectedData.value;
                
        jQuery("input[name=tipoCampo]").val(tipoCampo);
        
        jQuery(".formPerOcultar").hide()
        
        switch(tipoCampo){
            case "Text field":
            case "Password field":
            case "Text area":   
            case "Hidden field":            
                jQuery("#valorPorDefectoCampo").show();
                jQuery("#validacionCampo").show();                
            break;
            
            case "Notice":
            jQuery("#valorPorDefectoCampo").show();
            break;     
            
            case "select":
            break;
                        
            default:
                //listadito
                
                if(data.selectedData.text === "Listado de paises"){
                    jQuery("#listadoValores ul").html("");                                                                                    
                     for(var i=0,j=countryList.length; i<j; i++){                       
                       jQuery("#listadoValores ul").append('<li name="'+countryList[i].id+'"><span>'+countryList[i].text+'</span><input type="hidden" name="ValueList[]" value="'+countryList[i].id+'" /> <span class="eliminar-valor-listado accion-listado"></span><span class="editar-valor-listado accion-listado"></span></li>')
                     };
                     jQuery("#listadoValores ul").css("height","120px");
                     jQuery("#label-text-valor").css("margin-left","22px");
                }
                                
                jQuery("#validacionCampo").show()
                jQuery("#listadoValores").show() 
                jQuery("#agregarValor").show()
                
            break;
        }
    }
});
    
});




var countryList = [
        {"id": "Afghanistan", "text": "&#8235;&#1575;&#1601;&#1594;&#1575;&#1606;&#1587;&#1578;&#1575;&#1606;&#8236;&#8206; (Afghanistan)"},
        {"id": "Aland Islands", "text": "&Aring;land (&Aring;land Islands)"},
        {"id": "Albania", "text": "Shqip&euml;ria (Albania)"},
        {"id": "Algeria", "text": "&#8235;&#1575;&#1604;&#1580;&#1586;&#1575;&#1574;&#1585;&#8236;&#8206; (Algeria)"},
        {"id": "American Samoa", "text": "American Samoa"},
        {"id": "Andorra", "text": "Andorra"},
        {"id": "Angola", "text": "Angola"},
        {"id": "Anguilla", "text": "Anguilla"},
        {"id": "Antarctica", "text": "Antarctica"},
        {"id": "Antigua and Barbuda", "text": "Antigua and Barbuda"},
        {"id": "Argentina", "text": "Argentina"},
        {"id": "Armenia", "text": "&#1344;&#1377;&#1397;&#1377;&#1405;&#1407;&#1377;&#1398;&#1387; &#1344;&#1377;&#1398;&#1408;&#1377;&#1402;&#1381;&#1407;&#1400;&#1410;&#1385;&#1387;&#1410;&#1398; (Armenia)"},
        {"id": "Aruba", "text": "Aruba"},
        {"id": "Ascension Island", "text": "Ascension Island"},
        {"id": "Australia", "text": "Australia"},
        {"id": "Austria", "text": "&Ouml;sterreich (Austria)"},
        {"id": "Azerbaijan", "text": "Az&#601;rbaycan (Azerbaijan)"},
        {"id": "Bahamas", "text": "Bahamas"},
        {"id": "Bahrain", "text": "&#8235;&#1575;&#1604;&#1576;&#1581;&#1585;&#1610;&#1606;&#8236;&#8206; (Bahrain)"},
        {"id": "Bangladesh", "text": "&#2476;&#2494;&#2434;&#2482;&#2494;&#2470;&#2503;&#2486; (Bangladesh)"},
        {"id": "Barbados", "text": "Barbados"},
        {"id": "Belarus", "text": "&#1041;&#1077;&#1083;&#1072;&#1088;&#1091;&#1089;&#1100; (Belarus)"},
        {"id": "Belgium", "text": "Belgi&euml; (Belgium)"},
        {"id": "Belize", "text": "Belize"},
        {"id": "Benin", "text": "B&eacute;nin (Benin)"},
        {"id": "Bermuda", "text": "Bermuda"},
        {"id": "Bhutan", "text": "Bhutan"},
        {"id": "Bolivia", "text": "Bolivia"},
        {"id": "Bonaire - Sint Eustatius - and Saba", "text": "Bonaire - Sint Eustatius - and Saba"},
        {"id": "Bosnia and Herzegovina", "text": "Bosna i Hercegovina (Bosnia and Herzegovina)"},
        {"id": "Botswana", "text": "Botswana"},
        {"id": "Bouvet Island", "text": "Bouvet Island"},
        {"id": "Brazil", "text": "Brasil (Brazil)"},
        {"id": "British Indian Ocean Territory", "text": "British Indian Ocean Territory"},
        {"id": "British Virgin Islands", "text": "British Virgin Islands"},
        {"id": "Brunei", "text": "Brunei"},
        {"id": "Bulgaria", "text": "&#1041;&#1098;&#1083;&#1075;&#1072;&#1088;&#1080;&#1103; (Bulgaria)"},
        {"id": "Burkina Faso", "text": "Burkina Faso"},
        {"id": "Burundi", "text": "Uburundi (Burundi)"},
        {"id": "Cambodia", "text": "&#6016;&#6040;&#6098;&#6038;&#6075;&#6023;&#6070; (Cambodia)"},
        {"id": "Cameroon", "text": "Cameroun (Cameroon)"},
        {"id": "Canada", "text": "Canada"},
        {"id": "Canary Islands", "text": "Islas Canarias (Canary Islands)"},
        {"id": "Cape Verde", "text": "Kabu Verdi (Cape Verde)"},
        {"id": "Cayman Islands", "text": "Cayman Islands"},
        {"id": "Central African Republic", "text": "K&ouml;d&ouml;r&ouml;s&ecirc;se t&icirc; B&ecirc;afr&icirc;ka (Central African Republic)"},
        {"id": "Ceuta and Melilla", "text": "Ceuta y Melilla (Ceuta and Melilla)"},
        {"id": "Chad", "text": "Tchad (Chad)"},
        {"id": "Chile", "text": "Chile"},
        {"id": "China", "text": "&#20013;&#22269; (China)"},
        {"id": "Christmas Island", "text": "Christmas Island"},
        {"id": "Clipperton Island", "text": "&Icirc;le Clipperton (Clipperton Island)"},
        {"id": "Cocos [Keeling] Islands", "text": "Cocos [Keeling] Islands"},
        {"id": "Colombia", "text": "Colombia"},
        {"id": "Comoros", "text": "&#8235;&#1580;&#1586;&#1585; &#1575;&#1604;&#1602;&#1605;&#1585;&#8236;&#8206; (Comoros)"},
        {"id": "Congo [DRC]", "text": "Jamhuri ya Kidemokrasia ya Kongo (Congo [DRC])"},
        {"id": "Congo [Republic]", "text": "Congo-Brazzaville (Congo [Republic])"},
        {"id": "Cook Islands", "text": "Cook Islands"},
        {"id": "Costa Rica", "text": "Costa Rica"},
        {"id": "Cote d'Ivoire", "text": "C&ocirc;te d'Ivoire"},
        {"id": "Croatia", "text": "Hrvatska (Croatia)"},
        {"id": "Cuba", "text": "Cuba"},
        {"id": "Curacao", "text": "Cura&ccedil;ao"},
        {"id": "Cyprus", "text": "&#922;&#973;&#960;&#961;&#959;&#962; (Cyprus)"},
        {"id": "Czech Republic", "text": "&#268;esk&aacute; republika (Czech Republic)"},
        {"id": "Denmark", "text": "Danmark (Denmark)"},
        {"id": "Diego Garcia", "text": "Diego Garcia"},
        {"id": "Djibouti", "text": "Djibouti"},
        {"id": "Dominica", "text": "Dominica"},
        {"id": "Dominican Republic", "text": "Rep&uacute;blica Dominicana (Dominican Republic)"},
        {"id": "Ecuador", "text": "Ecuador"},
        {"id": "Egypt", "text": "&#8235;&#1605;&#1589;&#1585;&#8236;&#8206; (Egypt)"},
        {"id": "El Salvador", "text": "El Salvador"},
        {"id": "Equatorial Guinea", "text": "Guinea Ecuatorial (Equatorial Guinea)"},
        {"id": "Eritrea", "text": "Eritrea"},
        {"id": "Estonia", "text": "Eesti (Estonia)"},
        {"id": "Ethiopia", "text": "Ethiopia"},
        {"id": "Falkland Islands [Islas Malvinas]", "text": "Falkland Islands [Islas Malvinas]"},
        {"id": "Faroe Islands", "text": "F&oslash;royar (Faroe Islands)"},
        {"id": "Fiji", "text": "Fiji"},
        {"id": "Finland", "text": "Suomi (Finland)"},
        {"id": "France", "text": "France"},
        {"id": "French Guiana", "text": "Guyane fran&ccedil;aise (French Guiana)"},
        {"id": "French Polynesia", "text": "Polyn&eacute;sie fran&ccedil;aise (French Polynesia)"},
        {"id": "French Southern Territories", "text": "Terres australes fran&ccedil;aises (French Southern Territories)"},
        {"id": "Gabon", "text": "Gabon"},
        {"id": "Gambia", "text": "Gambia"},
        {"id": "Georgia", "text": "&#4321;&#4304;&#4325;&#4304;&#4320;&#4311;&#4309;&#4308;&#4314;&#4317; (Georgia)"},
        {"id": "Germany", "text": "Deutschland (Germany)"},
        {"id": "Ghana", "text": "Gaana (Ghana)"},
        {"id": "Gibraltar", "text": "Gibraltar"},
        {"id": "Greece", "text": "&#917;&#955;&#955;&#940;&#948;&#945; (Greece)"},
        {"id": "Greenland", "text": "Greenland"},
        {"id": "Grenada", "text": "Grenada"},
        {"id": "Guadeloupe", "text": "Guadeloupe"},
        {"id": "Guam", "text": "Guam"},
        {"id": "Guatemala", "text": "Guatemala"},
        {"id": "Guernsey", "text": "Guernsey"},
        {"id": "Guinea", "text": "Guin&eacute;e (Guinea)"},
        {"id": "Guinea-Bissau", "text": "Guin&eacute; Bissau (Guinea-Bissau)"},
        {"id": "Guyana", "text": "Guyana"},
        {"id": "Haiti", "text": "Haiti"},
        {"id": "Heard Island and McDonald Islands", "text": "Heard Island and McDonald Islands"},
        {"id": "Honduras", "text": "Honduras"},
        {"id": "Hong Kong", "text": "&#39321;&#28207; (Hong Kong)"},
        {"id": "Hungary", "text": "Magyarorsz&aacute;g (Hungary)"},
        {"id": "Iceland", "text": "&Iacute;sland (Iceland)"},
        {"id": "India", "text": "&#2349;&#2366;&#2352;&#2340; (India)"},
        {"id": "Indonesia", "text": "Indonesia"},
        {"id": "Iran", "text": "&#8235;&#1575;&#1740;&#1585;&#1575;&#1606;&#8236;&#8206; (Iran)"},
        {"id": "Iraq", "text": "&#8235;&#1575;&#1604;&#1593;&#1585;&#1575;&#1602;&#8236;&#8206; (Iraq)"},
        {"id": "Ireland", "text": "Ireland"},
        {"id": "Isle of Man", "text": "Isle of Man"},
        {"id": "Israel", "text": "&#8235;&#1497;&#1513;&#1512;&#1488;&#1500;&#8236;&#8206; (Israel)"},
        {"id": "Italy", "text": "Italia (Italy)"},
        {"id": "Jamaica", "text": "Jamaica"},
        {"id": "Japan", "text": "&#26085;&#26412; (Japan)"},
        {"id": "Jersey", "text": "Jersey"},
        {"id": "Jordan", "text": "&#8235;&#1575;&#1604;&#1571;&#1585;&#1583;&#1606;&#8236;&#8206; (Jordan)"},
        {"id": "Kazakhstan", "text": "&#1050;&#1072;&#1079;&#1072;&#1093;&#1089;&#1090;&#1072;&#1085; (Kazakhstan)"},
        {"id": "Kenya", "text": "Kenya"},
        {"id": "Kiribati", "text": "Kiribati"},
        {"id": "Kuwait", "text": "&#8235;&#1575;&#1604;&#1603;&#1608;&#1610;&#1578;&#8236;&#8206; (Kuwait)"},
        {"id": "Kyrgyzstan", "text": "Kyrgyzstan"},
        {"id": "Laos", "text": "Laos"},
        {"id": "Latvia", "text": "Latvija (Latvia)"},
        {"id": "Lebanon", "text": "&#8235;&#1604;&#1576;&#1606;&#1575;&#1606;&#8236;&#8206; (Lebanon)"},
        {"id": "Lesotho", "text": "Lesotho"},
        {"id": "Liberia", "text": "Liberia"},
        {"id": "Libya", "text": "&#8235;&#1604;&#1610;&#1576;&#1610;&#1575;&#8236;&#8206; (Libya)"},
        {"id": "Liechtenstein", "text": "Liechtenstein"},
        {"id": "Lithuania", "text": "Lietuva (Lithuania)"},
        {"id": "Luxembourg", "text": "Luxembourg"},
        {"id": "Macau", "text": "&#28595;&#38272; (Macau)"},
        {"id": "Macedonia [FYROM]", "text": "&#1052;&#1072;&#1082;&#1077;&#1076;&#1086;&#1085;&#1080;&#1112;&#1072; (Macedonia [FYROM])"},
        {"id": "Madagascar", "text": "Madagasikara (Madagascar)"},
        {"id": "Malawi", "text": "Malawi"},
        {"id": "Malaysia", "text": "Malaysia"},
        {"id": "Maldives", "text": "Maldives"},
        {"id": "Mali", "text": "Mali"},
        {"id": "Malta", "text": "Malta"},
        {"id": "Marshall Islands", "text": "Marshall Islands"},
        {"id": "Martinique", "text": "Martinique"},
        {"id": "Mauritania", "text": "&#8235;&#1605;&#1608;&#1585;&#1610;&#1578;&#1575;&#1606;&#1610;&#1575;&#8236;&#8206; (Mauritania)"},
        {"id": "Mauritius", "text": "Moris (Mauritius)"},
        {"id": "Mayotte", "text": "Mayotte"},
        {"id": "Mexico", "text": "M&eacute;xico (Mexico)"},
        {"id": "Micronesia", "text": "Micronesia"},
        {"id": "Moldova", "text": "Republica Moldova (Moldova)"},
        {"id": "Monaco", "text": "Monaco"},
        {"id": "Mongolia", "text": "Mongolia"},
        {"id": "Montenegro", "text": "Crna Gora (Montenegro)"},
        {"id": "Montserrat", "text": "Montserrat"},
        {"id": "Morocco", "text": "&#8235;&#1575;&#1604;&#1605;&#1594;&#1585;&#1576;&#8236;&#8206; (Morocco)"},
        {"id": "Mozambique", "text": "Mo&ccedil;ambique (Mozambique)"},
        {"id": "Myanmar [Burma]", "text": "Myanmar [Burma]"},
        {"id": "Namibia", "text": "Namibia"},
        {"id": "Nauru", "text": "Nauru"},
        {"id": "Nepal", "text": "&#2344;&#2375;&#2346;&#2366;&#2354; (Nepal)"},
        {"id": "Netherlands", "text": "Nederland (Netherlands)"},
        {"id": "New Caledonia", "text": "Nouvelle-Cal&eacute;donie (New Caledonia)"},
        {"id": "New Zealand", "text": "New Zealand"},
        {"id": "Nicaragua", "text": "Nicaragua"},
        {"id": "Niger", "text": "Nijar (Niger)"},
        {"id": "Nigeria", "text": "Nigeria"},
        {"id": "Niue", "text": "Niue"},
        {"id": "Norfolk Island", "text": "Norfolk Island"},
        {"id": "Northern Mariana Islands", "text": "Northern Mariana Islands"},
        {"id": "North Korea", "text": "&#51312;&#49440; &#48124;&#51452;&#51452;&#51032; &#51064;&#48124; &#44277;&#54868;&#44397; (North Korea)"},
        {"id": "Norway", "text": "Norge (Norway)"},
        {"id": "Oman", "text": "&#8235;&#1593;&#1615;&#1605;&#1575;&#1606;&#8236;&#8206; (Oman)"},
        {"id": "Pakistan", "text": "&#8235;&#1662;&#1575;&#1705;&#1587;&#1578;&#1575;&#1606;&#8236;&#8206; (Pakistan)"},
        {"id": "Palau", "text": "Palau"},
        {"id": "Palestinian Territories", "text": "&#8235;&#1601;&#1604;&#1587;&#1591;&#1610;&#1606;&#8236;&#8206; (Palestinian Territories)"},
        {"id": "Panama", "text": "Panam&aacute; (Panama)"},
        {"id": "Papua New Guinea", "text": "Papua New Guinea"},
        {"id": "Paraguay", "text": "Paraguay"},
        {"id": "Peru", "text": "Per&uacute; (Peru)"},
        {"id": "Philippines", "text": "Philippines"},
        {"id": "Pitcairn Islands", "text": "Pitcairn Islands"},
        {"id": "Poland", "text": "Polska (Poland)"},
        {"id": "Portugal", "text": "Portugal"},
        {"id": "Puerto Rico", "text": "Puerto Rico"},
        {"id": "Qatar", "text": "&#8235;&#1602;&#1591;&#1585;&#8236;&#8206; (Qatar)"},
        {"id": "Reunion", "text": "R&eacute;union"},
        {"id": "Romania", "text": "Rom&acirc;nia (Romania)"},
        {"id": "Russia", "text": "&#1056;&#1086;&#1089;&#1089;&#1080;&#1103; (Russia)"},
        {"id": "Rwanda", "text": "Rwanda"},
        {"id": "Saint Barth&eacute;lemy", "text": "Saint-Barth&eacute;l&eacute;my (Saint Barth&eacute;lemy)"},
        {"id": "Saint Helena", "text": "Saint Helena"},
        {"id": "Saint Kitts and Nevis", "text": "Saint Kitts and Nevis"},
        {"id": "Saint Lucia", "text": "Saint Lucia"},
        {"id": "Saint Martin", "text": "Saint-Martin (Saint Martin)"},
        {"id": "Saint Pierre and Miquelon", "text": "Saint-Pierre-et-Miquelon (Saint Pierre and Miquelon)"},
        {"id": "Saint Vincent and the Grenadines", "text": "Saint Vincent and the Grenadines"},
        {"id": "Samoa", "text": "Samoa"},
        {"id": "San Marino", "text": "San Marino"},
        {"id": "Sao Tome and Principe", "text": "S&atilde;o Tom&eacute; e Pr&iacute;ncipe (S&atilde;o Tom&eacute; and Pr&iacute;ncipe)"},
        {"id": "Saudi Arabia", "text": "&#8235;&#1575;&#1604;&#1605;&#1605;&#1604;&#1603;&#1577; &#1575;&#1604;&#1593;&#1585;&#1576;&#1610;&#1577; &#1575;&#1604;&#1587;&#1593;&#1608;&#1583;&#1610;&#1577;&#8236;&#8206; (Saudi Arabia)"},
        {"id": "Senegal", "text": "S&eacute;n&eacute;gal (Senegal)"},
        {"id": "Serbia", "text": "&#1057;&#1088;&#1073;&#1080;&#1112;&#1072; (Serbia)"},
        {"id": "Seychelles", "text": "Seychelles"},
        {"id": "Sierra Leone", "text": "Sierra Leone"},
        {"id": "Singapore", "text": "Singapore"},
        {"id": "Sint Maarten", "text": "Sint Maarten"},
        {"id": "Slovakia", "text": "Slovensk&aacute; republika (Slovakia)"},
        {"id": "Slovenia", "text": "Slovenija (Slovenia)"},
        {"id": "Solomon Islands", "text": "Solomon Islands"},
        {"id": "Somalia", "text": "Soomaaliya (Somalia)"},
        {"id": "South Africa", "text": "South Africa"},
        {"id": "South Georgia and the South Sandwich Islands", "text": "South Georgia and the South Sandwich Islands"},
        {"id": "South Korea", "text": "&#45824;&#54620;&#48124;&#44397; (South Korea)"},
        {"id": "South Sudan", "text": "South Sudan"},
        {"id": "Spain", "text": "Espa&ntilde;a (Spain)"},
        {"id": "Sri Lanka", "text": "&#3521;&#3530;&#8205;&#3515;&#3539; &#3517;&#3458;&#3482;&#3535;&#3520; (Sri Lanka)"},
        {"id": "Sudan", "text": "&#8235;&#1575;&#1604;&#1587;&#1608;&#1583;&#1575;&#1606;&#8236;&#8206; (Sudan)"},
        {"id": "Suriname", "text": "Suriname"},
        {"id": "Svalbard and Jan Mayen", "text": "Svalbard og Jan Mayen (Svalbard and Jan Mayen)"},
        {"id": "Swaziland", "text": "Swaziland"},
        {"id": "Sweden", "text": "Sverige (Sweden)"},
        {"id": "Switzerland", "text": "Schweiz (Switzerland)"},
        {"id": "Syria", "text": "&#8235;&#1587;&#1608;&#1585;&#1610;&#1575;&#8236;&#8206; (Syria)"},
        {"id": "Taiwan", "text": "&#21488;&#28771; (Taiwan)"},
        {"id": "Tajikistan", "text": "Tajikistan"},
        {"id": "Tanzania", "text": "Tanzania"},
        {"id": "Thailand", "text": "&#3652;&#3607;&#3618; (Thailand)"},
        {"id": "Timor-Leste", "text": "Timor-Leste"},
        {"id": "Togo", "text": "Togo"},
        {"id": "Tokelau", "text": "Tokelau"},
        {"id": "Tonga", "text": "Tonga"},
        {"id": "Trinidad and Tobago", "text": "Trinidad and Tobago"},
        {"id": "Tristan da Cunha", "text": "Tristan da Cunha"},
        {"id": "Tunisia", "text": "&#8235;&#1578;&#1608;&#1606;&#1587;&#8236;&#8206; (Tunisia)"},
        {"id": "Turkey", "text": "T&uuml;rkiye (Turkey)"},
        {"id": "Turkmenistan", "text": "Turkmenistan"},
        {"id": "Turks and Caicos Islands", "text": "Turks and Caicos Islands"},
        {"id": "Tuvalu", "text": "Tuvalu"},
        {"id": "U.S. Minor Outlying Islands", "text": "U.S. Minor Outlying Islands"},
        {"id": "U.S. Virgin Islands", "text": "U.S. Virgin Islands"},
        {"id": "Uganda", "text": "Uganda"},
        {"id": "Ukraine", "text": "&#1059;&#1082;&#1088;&#1072;&iuml;&#1085;&#1072; (Ukraine)"},
        {"id": "United Arab Emirates", "text": "&#8235;&#1575;&#1604;&#1573;&#1605;&#1575;&#1585;&#1575;&#1578; &#1575;&#1604;&#1593;&#1585;&#1576;&#1610;&#1577; &#1575;&#1604;&#1605;&#1578;&#1581;&#1583;&#1577;&#8236;&#8206; (United Arab Emirates)"},
        {"id": "United Kingdom", "text": "United Kingdom"},
        {"id": "United States", "text": "United States"},
        {"id": "Uruguay", "text": "Uruguay"},
        {"id": "Uzbekistan", "text": "&#1038;&#1079;&#1073;&#1077;&#1082;&#1080;&#1089;&#1090;&#1086;&#1085; (Uzbekistan)"},
        {"id": "Vanuatu", "text": "Vanuatu"},
        {"id": "Vatican City", "text": "Vaticano (Vatican City)"},
        {"id": "Venezuela", "text": "Venezuela"},
        {"id": "Vietnam", "text": "Vi&#7879;t Nam (Vietnam)"},
        {"id": "Wallis and Futuna", "text": "Wallis and Futuna"},
        {"id": "Western Sahara", "text": "&#8235;&#1575;&#1604;&#1589;&#1581;&#1585;&#1575;&#1569; &#1575;&#1604;&#1594;&#1585;&#1576;&#1610;&#1577;&#8236;&#8206; (Western Sahara)"},
        {"id": "Yemen", "text": "&#8235;&#1575;&#1604;&#1610;&#1605;&#1606;&#8236;&#8206; (Yemen)"},
        {"id": "Zambia", "text": "Zambia"},
        {"id": "Zimbabwe", "text": "Zimbabwe"}
    ];

	function SwitchItems (frm, elem)
	  {
	    var maf = document.forms[frm];
	    var len = maf.length;
	    var flag = 'none';

	    for (var i = 0; i < len; i++)
	    {
	      var e = maf.elements[i];
	      if (e.name == elem)
	      {
						if(flag=='none')flag=maf.elements[i].checked

	          e.checked = flag;
	      }
	    }
	}

// # MULTISTORE EXTENSION MULTISELECTION

function SwitchItem (frm, elem, flag)
  {
    var maf = document.forms[frm];
    var len = maf.length;

    for (var i = 0; i < len; i++)
    {
      var e = maf.elements[i];
      if (e.name == elem)
      {
          e.checked = flag;
      }
    }
  }

  function copyAndPaste(frm, field, para){

	    var maf = document.forms[frm];
	    var len = maf.length;
	    for (var i = 0; i < len; i++)
	    {
	      var e = maf.elements[i];
	      if (e.name == field+para)
	      {
          var textToCopy = e.value;
          break;
	      }
	    }
	    if(textToCopy!=undefined){
	    for (var i = 0; i < len; i++)
	    {
	      var e = maf.elements[i];
	      if (e.name.substring(0, field.length)==field)
	      {
					if(e.value.length < 1)
          	e.value=textToCopy;
	      }
	    }
			}
	}

	function changeLanguages(){
	var selDomain = document.forms['edit_content'].categories_domain.value;
	var selLanguage = document.forms['edit_content'].language.value;
	document.forms['edit_content'].language.options.length = 0;
	if(selDomain>0){
		for (var i = 0; i < arrDomain[selDomain].length; ++i){
			if(arrDomain[selDomain][i]['code']==selLanguage) {
          selLanguage=i;
			}
		  var newItem = new Option(arrDomain[selDomain][i]['language'], arrDomain[selDomain][i]['code'], false, true);
		  document.forms['edit_content'].language.options[document.forms['edit_content'].language.length] = newItem;
		}
	}else{
		var i=0;
    for (var langCode in arrayLanguageCodes){
			if(arrayLanguageCodes[langCode]['code']==selLanguage){
        selLanguage=i;
			}
		  var newItem = new Option(arrayLanguageCodes[langCode]['text'], arrayLanguageCodes[langCode]['code'], false, true);
		  document.forms['edit_content'].language.options[document.forms['edit_content'].language.length] = newItem;
			var i=i+1;
		}
	}
  document.forms['edit_content'].language.selectedIndex = selLanguage;
}

function changeDomains(){
  var selLanguageCode = document.forms['edit_content'].language.value;
  var selDomain = document.forms['edit_content'].categories_domain.value;
  document.forms['edit_content'].categories_domain.options.length = 0;
  var newItem = new Option('Alle', 0, false, true);
  document.forms['edit_content'].categories_domain.options[document.forms['edit_content'].categories_domain.length] = newItem;
  if(arrayLanguageCodes[selLanguageCode]['id']>0){
    var selLanguage = arrayLanguageCodes[selLanguageCode]['id'];
		for (var i = 0; i < arrLanguage[selLanguage].length; ++i){
			if(arrLanguage[selLanguage][i]['id']==selDomain){
          selDomain=i;
			}
		  var newItem = new Option(arrLanguage[selLanguage][i]['domain'], arrLanguage[selLanguage][i]['id'], false, true);
		  document.forms['edit_content'].categories_domain.options[document.forms['edit_content'].categories_domain.length] = newItem;
		}
  }else{
    for (var i = 0; i < arrayDomainCodes.length; ++i){
		  if(arrayDomainCodes[i]['id']==selDomain){
        selDomain=i;
			}

		  var newItem = new Option(arrayDomainCodes[i]['text'], arrayDomainCodes[i]['id'], false, true);
		  document.forms['edit_content'].categories_domain.options[document.forms['edit_content'].categories_domain.length] = newItem;
		}
	}
	document.forms['edit_content'].categories_domain.selectedIndex = selDomain+1;
}

function set_checkboxDomains (set) {
  if (set == 1) {
    for (var i = 0; i < document.getElementsByName("domains[]").length; ++i)
		document.getElementsByName("domains[]")[i].checked = true;
  }
  if (set == 0) {
    for (var i = 0; i < document.getElementsByName("domains[]").length; ++i)
		document.getElementsByName("domains[]")[i].checked = false;
  }
}

function showTabDom(dom, anzahl, anzahlDom) {
 for (var d = 0; d < anzahlDom; d++) {
	  for (var i = 0; i < anzahl; i++) {
			if (document.getElementById && document.getElementById("tab_lang"+d+"_" +i) != undefined) {
			  document.getElementById("tab_lang"+d+"_" +i).style.display="none";
				document.getElementById("tabselect"+d+"_" +i).style.background="none";
        document.getElementById("tabselect"+d+"_" +i).style.color="#aaaaaa";

			  if (dom == d) {
				document.getElementById("tab_lang"+d+"_0").style.display="block";
				document.getElementById("tabselect"+d+"_0").style.color="#000000";
				document.getElementById("tabselect"+d+"_0").style.background="#d0d0d0";
			  }
			}
	  }
	  document.getElementById("tablangmenu_"+d).style.display="none";
		document.getElementById("domselect_"+d).style.background="none";

	  if (dom == d) {
      document.getElementById("tablangmenu_"+dom).style.display="block";
			document.getElementById("domselect_"+dom).style.background="#d0d0d0";
	  }
		if(document.getElementById("domselect_"+d).title!=''){
    	document.getElementById("domselect_"+d).style.color="#000000";
		} else {
    	document.getElementById("domselect_"+d).style.color="#aaaaaa";
		}
 }
}

function showTab(auswahl, anzahl, dom, anzahlDom) {
  for (var i = 0; i < anzahl; i++) {

		if (document.getElementById) {
		  document.getElementById("tab_lang"+dom+"_" +i).style.display="none";

		  document.getElementById("tabselect"+dom+"_" +i).style.background="none";
		  document.getElementById("tabselect"+dom+"_" +i).style.color="#aaaaaa";

		  if (auswahl == "tab_lang"+dom+"_" + i) {
				document.getElementById("tab_lang"+dom+"_" + i).style.display="block";

				document.getElementById("tabselect"+dom+"_" +i).style.background="#d0d0d0";
				document.getElementById("tabselect"+dom+"_" +i).style.color="#000000";
		  }
		}
  }
}

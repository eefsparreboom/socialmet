<%'SoHosted Domeinhouder
DomeinHouderURL = "http://www.sohosted.com/dynamisch/domeinhouder/?klantnummer=32002&domein=socialmet.nl"
Set XMLHTTP = CreateObject("MSXML2.ServerXMLHTTP") 
XMLHTTP.Open "GET", DomeinHouderURL, false 
XMLHTTP.Send "" 
Response.Write XMLHTTP.ResponseText 
Set XMLHTTP = nothing 
Response.End
%>

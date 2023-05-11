var checkboxValues = [];

function updateCheckboxValues(value) {
    if (checkboxValues.includes(value)) {
        checkboxValues = checkboxValues.filter(function(val) { return val !== value; });
    } else {
        checkboxValues.push(value);
    }

    document.querySelector('input[name="selectedValues"]').value = checkboxValues;

}

function sendInputValues(elementId) {
    var elemento = document.getElementById(elementId);
    var parametro = "";

    if (elemento.tagName == "INPUT") {
        parametro = elemento.value;
    } else if (elemento.tagName == "SELECT") {
        parametro = elemento.options[elemento.selectedIndex].value;
    }

    // Actualizar la URL con el valor del parámetro
    var urlActual = window.location.href;
    if (urlActual.indexOf('?') >= 0 && !urlActual.includes(elementId)) {
        window.location.href = urlActual + '&' + elementId + '=' + parametro;;
    } else {
        window.location.href = modifyURI(elementId, parametro);
    }

}

function modifyURI(elementId, parametro) {
    var urlActual = window.location.href;
    urlActual=containsPage(urlActual);
    urlActual=nullParameter(urlActual,elementId,parametro);

    if (urlActual.includes(elementId) && !urlActual.includes('&')) {
        var newURI;
        newURI = urlActual.slice(0, urlActual.indexOf('=') + 1);
        newURI = newURI + parametro;
        
        return newURI;
    } else if (urlActual.includes(elementId) && urlActual.includes('&')) {
        var newURI;
        URLSplited = urlActual.split("?");
        URIParameterSplited = URLSplited[1].split("&");
        for (var i = 0; i < URIParameterSplited.length; i++) {
            if (URIParameterSplited[i].includes(elementId)) {
                var parameterSliced = URIParameterSplited[i].slice(0, URIParameterSplited[i].indexOf("=") + 1);
                URIParameterSplited[i] = parameterSliced + parametro;
            }

        }
        URIParameter = URIParameterSplited.join("&");
        newURI = URLSplited[0] + "?" + URIParameter;
        return newURI;
    } else {
        newURI = urlActual + '?' + elementId + '=' + parametro;
        return newURI;
    }
}

function containsPage(urlActual){
    if(urlActual.includes("page=")){
        let regex = /([?&])page=\d+/;
        urlActual = urlActual.replace(regex, "");    
    }
    return urlActual;
}
function nullParameter(urlActual,elementId,parameter){
    if(urlActual.includes(elementId)&&parameter==""){
        let regex = new RegExp(`([?&])+${elementId}=\\d+`); 
        urlActual = urlActual.replace(regex, "");
    }
    return urlActual;
}
    
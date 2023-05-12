var checkboxValues = [];

function updateCheckboxValues(value) {
    checkboxValues = JSON.parse(localStorage.getItem('checkboxValues'));
    if (checkboxValues.includes(value)) {
        checkboxValues = checkboxValues.filter(function (val) { return val !== value; });
    } else {
        checkboxValues.push(value);
    }
    console.log(checkboxValues);
    localStorage.setItem('checkboxValues', JSON.stringify(checkboxValues));
    document.querySelector('input[name="selectedValues"]').value = checkboxValues;

}
function clearCheckboxValues() {
    localStorage.removeItem('checkboxValues');
    location.reload();
}
var form = document.getElementById('exportForm');
form.addEventListener('submit', function (event) {
    event.preventDefault(); // Evitar que el formulario se envíe de forma tradicional

    var storedValues = localStorage.getItem('checkboxValues');
    document.querySelector('input[name="selectedValues"]').value = storedValues;

    form.submit();
});
// Al cargar la página, recuperar los valores del LocalStorage
document.addEventListener('DOMContentLoaded', function () {
    var storedValues = localStorage.getItem('checkboxValues');
    if (storedValues) {
        checkboxValues = JSON.parse(storedValues);

        // Marcar los checkboxes según los valores almacenados

    }
});
window.addEventListener('load', function () {
    // Tu código aquí
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(function (checkbox) {
        var value = checkbox.value;
        checkbox.checked = checkboxValues.includes(value);
    });
});
function sendInputValues(elementId) {
    var elemento = document.getElementById(elementId);
    var parametro = "";
    if (elemento.tagName == "INPUT") {
        parametro = elemento.value;
    } else if (elemento.tagName == "SELECT") {
        parametro = elemento.options[elemento.selectedIndex].value;
    }
    // Actualizar la URL con el valor del parámetro

    window.location.href = modifyURI(elementId, parametro);


}

function modifyURI(elementId, parametro) {
    var urlActual = window.location.href;

    urlActual = containsPage(urlActual);

    if (urlActual.includes(elementId) && !urlActual.includes('&')) {
        var newURI;
        newURI = urlActual.slice(0, urlActual.indexOf('=') + 1);
        newURI = newURI + parametro;
        console.log("1er if")
        return newURI;
    } else if (urlActual.includes(elementId) && urlActual.includes('&')) {
        console.log("2do if")
        var newURI;
        var URLSplited = urlActual.split("?");
        var URIParameterSplited = URLSplited[1].split("&");
        for (var i = 0; i < URIParameterSplited.length; i++) {
            if (URIParameterSplited[i].includes(elementId)) {
                var parameterSliced = URIParameterSplited[i].slice(0, URIParameterSplited[i].indexOf("=") + 1);
                URIParameterSplited[i] = parameterSliced + parametro;
            }

        }
        URIParameter = URIParameterSplited.join("&");
        newURI = URLSplited[0] + "?" + URIParameter;
        return newURI;
    } else if (urlActual.includes("?") && !urlActual.includes(elementId)) {
        console.log("3er if")
        newURI = urlActual + '&' + elementId + '=' + parametro;
        return newURI;
    } else {
        console.log("4to if")
        newURI = urlActual + "?" + elementId + "=" + parametro;
        return newURI;
    }

}

function containsPage(urlActual) {
    if (urlActual.includes("page=")) {
        console.log("antes de:" + urlActual);
        let regex = /([?&])page=\d+/;
        urlActual = urlActual.replace(regex, "");
        console.log("despues de:" + urlActual);
    }
    return urlActual;
}


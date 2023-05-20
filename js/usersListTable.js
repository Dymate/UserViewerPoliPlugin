//limpia el localstorage donde se almacenan los checkboxes seleccionados
function clearCheckboxValues() {
    localStorage.removeItem('checkboxValues');
    location.reload();
}
//variable global que almacena los valores de los checkboxes
var checkboxValues = [];
//actualiza la variable checkboxValues segun los elemntos seleccionados
function updateCheckboxValues(value) {
    //si ya existe la variable checkboxValues en local storage la asigna
    if (JSON.parse(localStorage.getItem('checkboxValues'))) {
        checkboxValues = JSON.parse(localStorage.getItem('checkboxValues'));
    }
    //si ya existe el valor seleccionado en la variable lo borra, sino lo añade
    if (checkboxValues.includes(value)) {
        checkboxValues = checkboxValues.filter(function (val) { return val !== value; });
    } else {
        checkboxValues.push(value);
    }
    console.log(checkboxValues);
    localStorage.setItem('checkboxValues', JSON.stringify(checkboxValues));//<Converts a JavaScript value to a JavaScript Object Notation (JSON) string.
    document.querySelector('input[name="selectedValues"]').value = checkboxValues;

}

var form = document.getElementById('exportForm');
//cuando se haga click en el botón submit se recuperan los valores de la variable y se asignan a el input
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

    }
});
//cuando se cargue por completo la ventana, selecciona los checkboxes correspondientes
//a los valores de la variable
window.addEventListener('load', function () {

    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(function (checkbox) {
        var value = checkbox.value;
        checkbox.checked = checkboxValues.includes(value);
    });
});
//se ejecuta al generar un filtro, modifica la URI
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
function checkChangesInLocalStorage() {
    // Obtener el valor de la variable almacenada en el localStorage

    if (localStorage.getItem('checkboxValues') != null) {
        var selectedCheckboxes = JSON.parse(localStorage.getItem('checkboxValues'));
    } else var selectedCheckboxes = [];

    // Obtener una referencia al botón
    var exportButton = document.getElementById('export');
    var clearSelectionButton = document.getElementById('deleteSelection');
    // Verificar el valor de la variable y habilitar o deshabilitar el botón
    if (selectedCheckboxes.length > 0) {
        exportButton.disabled = false; // Habilitar el botón
        clearSelectionButton.disabled = false;
    } else {
        exportButton.disabled = true;
        clearSelectionButton.disabled = true; // Deshabilitar el botón
    }
};
setInterval(checkChangesInLocalStorage, 500);
function modifyURI(elementId, parametro) {
    // Obtenemos la URL actual
    var urlActual = window.location.href;

    // Eliminamos el parámetro 'page' de la URL actual si existe
    urlActual = containsPage(urlActual);

    // Comprobamos los diferentes casos para modificar la URI
    if (urlActual.includes(elementId) && !urlActual.includes('&')) {
        // Caso 1: El elemento ya está presente en la URI y no hay otros parámetros
        var newURI;
        newURI = urlActual.slice(0, urlActual.indexOf('=') + 1);
        newURI = newURI + parametro;
        return newURI;
    } else if (urlActual.includes(elementId) && urlActual.includes('&')) {
        // Caso 2: El elemento ya está presente en la URI y hay otros parámetros
        var newURI;
        var URLSplited = urlActual.split("?");
        var URIParameterSplited = URLSplited[1].split("&");
        for (var i = 0; i < URIParameterSplited.length; i++) {
            if (URIParameterSplited[i].includes(elementId)) {
                var parameterSliced = URIParameterSplited[i].slice(0, URIParameterSplited[i].indexOf("=") + 1);
                URIParameterSplited[i] = parameterSliced + parametro;
            }
        }
        var URIParameter = URIParameterSplited.join("&");
        newURI = URLSplited[0] + "?" + URIParameter;
        return newURI;
    } else if (urlActual.includes("?") && !urlActual.includes(elementId)) {
        // Caso 3: Hay otros parámetros en la URI pero el elemento no está presente
        newURI = urlActual + '&' + elementId + '=' + parametro;
        return newURI;
    } else {
        // Caso 4: No hay otros parámetros en la URI
        newURI = urlActual + "?" + elementId + "=" + parametro;
        return newURI;
    }
}

function containsPage(urlActual) {
    // Comprueba si la URL contiene el parámetro 'page' y lo elimina si es así
    if (urlActual.includes("page=")) {
        console.log("antes de:" + urlActual);
        let regex = /([?&])page=\d+/;
        urlActual = urlActual.replace(regex, "");
        console.log("despues de:" + urlActual);
    }
    return urlActual;
}


<!DOCTYPE html>
<html>
<head>
    <title>Página Principal</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Ejecuta una petición AJAX cuando la página se carga o actualiza
            const baseURL = "http://localhost:4000/";
            let currentValueOfDoor = false;
            let currentValueOfThermometer = false;
            //Ejecuta la funcion que obtiene los datos actuales
            getCurrentValues();

            function getCurrentValues() {
                $.ajax({
                    url: baseURL + "current_values",
                    method: 'GET',
                    success: function(response) {
                        //Inicializa las variables de los iconos
                        let iconDoor = "fa-door-closed";
                        let iconThermometer = "fa-temperature-high";
                        let iconColor = "text-primary";
                        if (response.data.length < 1) {
                            postCurrentValues();
                        }
                        response.data.forEach(element => {
                            //Verifica los valores de cada uno de los iconos
                            //para asignar y actualizar el estado
                            if (element.name === "door") {
                                if (element.currentValue === 1) {
                                    iconDoor = "fa-door-open";
                                    currentValueOfDoor = true;
                                } else if (element.currentValue === 0) {
                                    iconDoor = "fa-door-closed";
                                    currentValueOfDoor = false;
                                }
                            }
                            if (element.name === "thermometer") {
                                if (element.currentValue === 1) {
                                    iconThermometer = "fa-temperature-high";
                                    iconColor = "text-danger";
                                    currentValueOfThermometer = true;
                                } else if (element.currentValue === 0) {
                                    iconColor = "text-primary";
                                    iconThermometer = "fa-temperature-low";
                                    currentValueOfThermometer = false;
                                }
                            }
                            // Actualizar el elemento del icono en el DOM
                            $('#icono1').removeClass().addClass("fa-solid " + iconDoor + " fa-8x");
                            $('#icono2').removeClass().addClass("fa-solid " + iconThermometer + " fa-8x " + iconColor);
                        });
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }


            //Declaramos las acciones los botones

            $("#btnChangeStateDoor").click(function() {
                // Realiza la petición PUT al servidor Express
                let name = $(this).data("name");
                updateCurrentValues(name);
            });

            $("#btnChangeStateThermometer").click(function() {
                // Realiza la petición PUT al servidor Express
                let name = $(this).data("name");
                updateCurrentValues(name);
            });

            //Metodo para actualizar los valores actuales en la base de datos
            function updateCurrentValues(name) {
                let currentValueOfName = 0;
                console.log(currentValueOfDoor);
                console.log(currentValueOfThermometer);
                if (name === "door") {
                    if (!currentValueOfDoor) {
                        currentValueOfName = 1;
                    } else {
                        currentValueOfName = 0;
                    }
                }
                if (name === "thermometer") {
                    if (!currentValueOfThermometer) {
                        currentValueOfName = 1;
                    } else {
                        currentValueOfName = 0;
                    }
                }
                const form = {
                    currentValue: currentValueOfName,
                    name: name,
                    date: new Date().toISOString()
                };
                $.ajax({
                    url: baseURL + "current_values",
                    method: "PUT",
                    contentType: "application/json",
                    data: JSON.stringify(form),
                    success: function(response) {
                        postHistoryRecords(name, currentValueOfName)
                        getCurrentValues();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            //Metodo para agregar un nuevo valor al historial de la base de datos
            function postCurrentValues() {
                const initialValues = [{
                    id: 1,
                    name: "door",
                    currentValue: 0,
                    date: new Date().toISOString()
                }, {
                    id: 2,
                    name: "thermometer",
                    currentValue: 0,
                    date: new Date().toISOString()
                }]
                initialValues.map((e) => {
                    $.ajax({
                        url: baseURL + "current_values",
                        method: "POST",
                        contentType: "application/json",
                        data: JSON.stringify(e),
                        success: function(response) {
                            getCurrentValues();
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                })
            }

            //Metodo para agregar un nuevo valor al historial de la base de datos
            function postHistoryRecords(name, value) {
                const form = {
                    value: value,
                    date: new Date().toISOString()
                };
                console.log(form);
                $.ajax({
                    url: baseURL + name,
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(form),
                    success: function(response) {
                        //Obtiene los datos
                        getCurrentValues();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });
    </script>

    <style>
        .h-custom {
            height: 100vh;
        }

        body {
            background-color: #10152b;
            color: white;
        }

        .container-custom {
            height: 40vh;
            width: 90%;
            background-color: cadetblue;
            color: white;
        }

        .button-custom {
            color: white;
        }
    </style>
</head>

<body>

    <div class="container h-custom d-flex align-items-center justify-content-center">
        <div class="container-custom  d-flex align-items-center justify-content-around rounded-3">
            <div class="d-flex flex-column align-items-center">
                <i id="icono1" class="icono1"></i>
                <button id="btnChangeStateDoor" data-name="door" class="button-custom mt-5 bg-primary border-0 rounded-2 p-3">Cambiar estado</button>
            </div>
            <div class="d-flex flex-column align-items-center">
                <i id="icono2" class="icono2"></i>
                <button id="btnChangeStateThermometer" data-name="thermometer" class="button-custom mt-5 bg-primary border-0 rounded-2 p-3">Cambiar estado</button>
            </div>
        </div>
    </div>

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Detect Your City</title>
</head>

<body>
    <h2>Detecting your city...</h2>
    <div id="output">Please wait...</div>

    <script>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(success, error);
        } else {
            document.getElementById("output").innerHTML = "Geolocation not supported.";
        }

        function success(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            fetch(`get_city.php?lat=${lat}&lon=${lon}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("output").innerHTML = "You are in: " + data;
                })
                .catch(err => {
                    document.getElementById("output").innerHTML = "Error: " + err;
                });
        }

        function error(err) {
            document.getElementById("output").innerHTML =
                "Location permission denied or unavailable (" + err.message + ")";
        }
    </script>
</body>

</html>
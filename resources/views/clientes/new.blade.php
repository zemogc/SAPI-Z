<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Agregar Cliente</title>
</head>

<body>
    <div class="container">
        <h1>Agregar Cliente</h1>
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" require class="form-control" id="nombre" aria-describedby="nombreHelp" name="nombre" placeholder="Nombre del cliente">
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" require class="form-control" id="apellido" aria-describedby="apellidoHelp" name="apellido" placeholder="Apellido del cliente">
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" require class="form-control" id="telefono" aria-describedby="telefonoHelp" name="telefono" placeholder="Teléfono del cliente">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" require class="form-control" id="email" aria-describedby="emailHelp" name="email" placeholder="Email del cliente">
                <div class="mb-3">
               <label for="tipo" class="form-label">Tipo</label>
               <select class="form-select" id="tipo" name="tipo" required>
               <option selected disabled value="">Seleccione uno . . .</option>
               <option value="comprador">Comprador</option>
               <option value="vendedor">Vendedor</option>
               <option value="arrendatario">Arrendatario</option>
             </select>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('clientes.index') }}" class="btn btn-warning">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Editar Propiedad</title>
</head>


<body>
    <div class="container">
        <h1>Editar Propiedad</h1>
        <form method="POST" action="{{ route('propiedades.update', ['propiedad' => $propiedad->id]) }}">
            @method('patch')
            @csrf
            <div class="mb-3">
                <label for="id" class="form-label">Código</label>
                <input type="text" class="form-control" id="id" aria-describedby="idHelp" name="id" disabled="disabled" value="{{ $propiedad->id }}">
                <div id="idHelp" class="form-text">Código de la propiedad</div>
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" require class="form-control" id="direccion" aria-describedby="direccionHelp" name="direccion" placeholder="Dirección de la propiedad" value="{{ $propiedad->direccion }}">
            </div>
            <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select class="form-select" id="tipo" aria-describedby="tipoHelp" name="tipo" required>
            <option disabled>Seleccione el tipo de propiedad</option>
            <option value="Casa" {{ $propiedad->tipo == 'Casa' ? 'selected' : '' }}>Casa</option>
            <option value="Apartamento" {{ $propiedad->tipo == 'Apartamento' ? 'selected' : '' }}>Apartamento</option>
            <option value="Local comercial" {{ $propiedad->tipo == 'Local comercial' ? 'selected' : '' }}>Local comercial</option>
            </select>
            </div>
            <div class="mb-3">
                <label for="tamaño" class="form-label">Tamaño</label>
                <input type="text" require class="form-control" id="tamaño" aria-describedby="tamañoHelp" name="tamaño" placeholder="Tamaño de la propiedad" value="{{ $propiedad->tamano }}">
            </div>
            <div class="mb-3">
            <label for="habitaciones" class="form-label">Número de habitaciones</label>
            <select class="form-select" id="habitaciones" aria-describedby="habitacionesHelp" name="habitaciones" required>
            <option disabled selected value="">Seleccione el número de habitaciones</option>
            @for ($i = 1; $i <= 12; $i++)
            <option value="{{ $i }}" {{ $propiedad->numero_habitaciones == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
            </select>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="text" require class="form-control" id="precio" aria-describedby="precioHelp" name="precio" placeholder="Precio de la propiedad" value="{{ $propiedad->precio }}">
            </div>
            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado" require>
                    <option selected disabled value="">Seleccione uno . . .</option>
                    <option value="disponible" {{ $propiedad->estado == 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="vendida" {{ $propiedad->estado == 'vendida' ? 'selected' : '' }}>Vendida</option>
                    <option value="arrendada" {{ $propiedad->estado == 'arrendada' ? 'selected' : '' }}>Arrendada</option>
                </select>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('propiedades.index') }}" class="btn btn-warning">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>

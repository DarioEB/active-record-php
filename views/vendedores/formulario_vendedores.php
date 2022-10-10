<fieldset>
    <legend>Información General</legend>

    <label for="nombre">Nombre: </label>
    <input type="text" id="nombre" name="vendedor[nombre]" placeholder="Nombre" value="<?php s( $vendedor->nombre); ?>">

    <label for="apellido">Apellido: </label>
    <input type="text" id="apellido" name="vendedor[apellido]" placeholder="Apellido" value="<?php s( $vendedor->apellido); ?>">

    
</fieldset>

<fieldset>
    <legend>Información Extra</legend>
    
    <label for="telefono">Teléfono: </label>
    <input type="tel" id="telefono" name="vendedor[telefono]" placeholder="telefono" value="<?php s( $vendedor->telefono); ?>">

</fieldset>
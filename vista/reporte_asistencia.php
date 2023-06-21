<?php
session_start();
if (empty($_SESSION['nombre']) and empty($_SESSION['apellido'])) {
    header('location:login/login.php');
}
?>

<style>
    ul li:nth-child(1) .activo {
        background: rgb(11, 150, 214) !important;
    }
</style>



<!-- primero se carga el topbar -->
<?php require('./layout/topbar.php'); ?>
<!-- luego se carga el sidebar -->
<?php require('./layout/sidebar.php'); ?>

<!-- inicio del contenido principal -->
<div class="page-content">

    <h4 class="text-center text-secondary">ASISTENCIAS VADUM</h4>

    <?php
    include "../modelo/conexion.php";
    $sql = $conexion->query(" select * from empleado ");
    ?>

    <form action="fpdf/ReporteAsistenciaFecha.php">
        <input required type="date" name="txtfechainicio" class="input input__text mb-2">
        <input required type="date" name="txtfechafinal" class="input input__text mb-2">
        <select required name="txtempleado" class="input input__select mb-4">
            <option value="todos">Todos los empleados</option>
            <?php
            while ($datos = $sql->fetch_object()) { ?>
                <option value="<?= $datos->id_empleado ?>"><?= $datos->nombre . " " . $datos->apellido ?></option>
            <?php }
            ?>

        </select>
        <button type="submit" name="btngenerar" class="btn btn-primary">Generar Reporte</button>
    </form>

</div>
</div>
<!-- fin del contenido principal -->


<!-- por ultimo se carga el footer -->
<?php require('./layout/footer.php'); ?>
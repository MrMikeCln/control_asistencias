<?php

if (!empty($_GET["txtfechainicio"]) && !empty($_GET["txtfechafinal"]) && !empty($_GET["txtempleado"])) {
   require('./fpdf.php');

   $fechaInicio = $_GET["txtfechainicio"];
   $fechaFinal = $_GET["txtfechafinal"];
   $empleado = $_GET["txtempleado"];


   class PDF extends FPDF
   {

      // Cabecera de página
      function Header()
      {
         include '../../modelo/conexion.php';

         $consulta_info = $conexion->query("SELECT * FROM empresa");
         $dato_info = $consulta_info->fetch_object();
         $this->Image('logo.png', 250, 0, 50);
         $this->SetFont('Arial', 'B', 19);
         $this->Cell(85);
         $this->SetTextColor(0, 0, 0);
         $this->Cell(110, 15, utf8_decode($dato_info->nombre), 1, 1, 'C', 0);
         $this->Ln(3);
         $this->SetTextColor(103);


         $this->Cell(160);
         $this->SetFont('Arial', 'B', 10);
         $this->Cell(96, 10, utf8_decode("Ubicación: " . $dato_info->ubicacion), 0, 0, '', 0);
         $this->Ln(5);


         $this->Cell(160);
         $this->SetFont('Arial', 'B', 10);
         $this->Cell(59, 10, utf8_decode("Teléfono: "  . $dato_info->telefono), 0, 0, '', 0);
         $this->Ln(10);


         $this->SetTextColor(0, 0, 0);
         $this->Cell(100);
         $this->SetFont('Arial', 'B', 15);
         $this->Cell(85, 10, utf8_decode("REPORTE DE ASISTENCIAS POR FECHAS"), 0, 1, 'C', 0);
         $this->Ln(7);


         $this->SetFillColor(161, 161, 161);
         $this->SetTextColor(0, 0, 0);
         $this->SetDrawColor(163, 163, 163);
         $this->SetFont('Arial', 'B', 11);
         $this->Cell(15, 10, utf8_decode('N°'), 1, 0, 'C', 1);
         $this->Cell(80, 10, utf8_decode('EMPLEADO'), 1, 0, 'C', 1);
         $this->Cell(50, 10, utf8_decode('CARGO'), 1, 0, 'C', 1);
         $this->Cell(50, 10, utf8_decode('ENTRADA'), 1, 0, 'C', 1);
         $this->Cell(50, 10, utf8_decode('SALIDA'), 1, 0, 'C', 1);
         $this->Cell(30, 10, utf8_decode('TOTAL HRS'), 1, 1, 'C', 1);
      }


      function Footer()
      {
         $this->SetY(-15);
         $this->SetFont('Arial', 'I', 8);
         $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');

         $this->SetY(-15);
         $this->SetFont('Arial', 'I', 8);
         $hoy = date('d/m/Y');
         $this->Cell(540, 10, utf8_decode($hoy), 0, 0, 'C');
      }
   }

   include '../../modelo/conexion.php';

   $pdf = new PDF();
   $pdf->AddPage("landscape");

   $i = 0;
   $pdf->SetFont('Arial', '', 12);
   $pdf->SetDrawColor(163, 163, 163);

   if ($empleado == "todos") {
      $sql = $conexion->query("SELECT
         asistencia.id_asistencia,
         asistencia.id_empleado,
         DATE_FORMAT(asistencia.entrada, '%m-%d-%Y %H:%i:%s') AS 'entrada',
         DATE_FORMAT(asistencia.salida, '%m-%d-%Y %H:%i:%s') AS 'salida', 
         TIMEDIFF(asistencia.salida, asistencia.entrada) AS totalHR,
         empleado.dni,
         empleado.nombre,
         empleado.apellido,
         cargo.nombre AS cargo
         FROM
         asistencia
         INNER JOIN empleado ON asistencia.id_empleado = empleado.id_empleado
         INNER JOIN cargo ON empleado.cargo = cargo.id_cargo
         WHERE entrada BETWEEN '$fechaInicio' AND '$fechaFinal'
         ORDER BY empleado.nombre ASC");
   } else {
      $sql = $conexion->query("SELECT
         asistencia.id_asistencia,
         asistencia.id_empleado,
         DATE_FORMAT(asistencia.entrada, '%m-%d-%Y %H:%i:%s') AS 'entrada',
         DATE_FORMAT(asistencia.salida, '%m-%d-%Y %H:%i:%s') AS 'salida', 
         TIMEDIFF(asistencia.salida, asistencia.entrada) AS totalHR,
         empleado.dni,
         empleado.nombre,
         empleado.apellido,
         cargo.nombre AS cargo
         FROM
         asistencia
         INNER JOIN empleado ON asistencia.id_empleado = empleado.id_empleado
         INNER JOIN cargo ON empleado.cargo = cargo.id_cargo
         WHERE entrada BETWEEN '$fechaInicio' AND '$fechaFinal'
         AND empleado.id_empleado = $empleado
         ORDER BY empleado.nombre ASC");
   }

   while ($datos_reporte = $sql->fetch_object()) {
      $i = $i + 1;
      $pdf->Cell(15, 10, utf8_decode($i), 1, 0, 'C', 0);
      $pdf->Cell(80, 10, utf8_decode($datos_reporte->nombre . " " . $datos_reporte->apellido), 1, 0, 'C', 0);
      $pdf->Cell(50, 10, utf8_decode($datos_reporte->cargo), 1, 0, 'C', 0);
      $pdf->Cell(50, 10, utf8_decode($datos_reporte->entrada), 1, 0, 'C', 0);
      $pdf->Cell(50, 10, utf8_decode($datos_reporte->salida), 1, 0, 'C', 0);
      $pdf->Cell(30, 10, utf8_decode($datos_reporte->totalHR), 1, 1, 'C', 0);
   }

   $pdf->Output('Prueba2.pdf', 'I');
}
?>

<?php
// Archivo: api.php

// Incluir el archivo de conexión a la base de datos
require_once 'db_connection.php';

// Obtener los valores de los campos de búsqueda
$fname = isset($_GET['fname']) ? $_GET['fname'] : '';
$lname = isset($_GET['lname']) ? $_GET['lname'] : '';
$ssn = isset($_GET['ssn']) ? $_GET['ssn'] : '';
$dno = isset($_GET['dno']) ? $_GET['dno'] : '';


// Verificar si se solicita eliminar un empleado existente
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    // Obtener el JSON enviado en el cuerpo de la solicitud
    $json = file_get_contents('php://input');

    // Decodificar el JSON en un array asociativo
    $data = json_decode($json, true);

    // Verificar si el JSON se decodificó correctamente
    if ($data !== null) {
        // Obtener el valor del campo SSN
        $ssn = isset($data['ssn']) ? $data['ssn'] : '';

        // Iniciar una transacción
        $pdo->beginTransaction();

        try {
            // Verificar si el empleado existe en la base de datos
            $sql = "SELECT * FROM employee WHERE Ssn = :ssn FOR UPDATE";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':ssn', $ssn);
            $stmt->execute();
            $existingEmployee = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingEmployee) {
                // Verificar si el empleado es gerente de algún departamento
                $sql = "SELECT * FROM department WHERE Mgr_ssn = :ssn";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':ssn', $ssn);
                $stmt->execute();
                $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($departments) {
                    // Actualizar los registros de departamento para eliminar la referencia al empleado
                    $sql = "UPDATE department SET Mgr_ssn = NULL WHERE Mgr_ssn = :ssn";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':ssn', $ssn);
                    $stmt->execute();
                }

                // Eliminar el empleado de la tabla employee
                $sql = "DELETE FROM employee WHERE Ssn = :ssn";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':ssn', $ssn);
                $stmt->execute();

                // Confirmar la transacción
                $pdo->commit();

                // Enviar una respuesta de éxito
                $response = [
                    'success' => true,
                    'message' => 'Empleado eliminado exitosamente'
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                // Enviar una respuesta de error si el empleado no existe
                $response = [
                    'success' => false,
                    'message' => 'Empleado no encontrado'
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $pdo->rollBack();

            // Enviar una respuesta de error
            $response = [
                'success' => false,
                'message' => 'Error en la eliminación del empleado'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    } else {
        // Enviar una respuesta de error si el JSON no se decodificó correctamente
        $response = [
            'success' => false,
            'message' => 'Error al decodificar el JSON'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}



// Verificar si se solicita el registro de un empleado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    // Obtener el JSON enviado en el cuerpo de la solicitud
    $json = file_get_contents('php://input');

    // Decodificar el JSON en un array asociativo
    $data = json_decode($json, true);

    // Verificar si el JSON se decodificó correctamente
    if ($data !== null) {
        // Obtener los valores del formulario de registro
        $fname = isset($data['fname']) ? $data['fname'] : '';
        $lname = isset($data['lname']) ? $data['lname'] : '';
        $ssn = isset($data['ssn']) ? $data['ssn'] : '';
        $dno = isset($data['dno']) ? $data['dno'] : '';

        // Validar los campos del formulario
        // ...

        // Insertar el nuevo empleado en la base de datos
        $sql = "INSERT INTO employee (Fname, Lname, Ssn, Dno) VALUES (:fname, :lname, :ssn, :dno)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':fname', $fname);
        $stmt->bindValue(':lname', $lname);
        $stmt->bindValue(':ssn', $ssn);
        $stmt->bindValue(':dno', $dno);
        $stmt->execute();

        // Enviar una respuesta de éxito
        $response = [
            'success' => true,
            'message' => 'Empleado registrado exitosamente'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        // Enviar una respuesta de error si el JSON no se decodificó correctamente
        $response = [
            'success' => false,
            'message' => 'Error al decodificar el JSON'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Verificar si se solicita la actualización de un empleado existente
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    // Obtener el JSON enviado en el cuerpo de la solicitud
    $json = file_get_contents('php://input');

    // Decodificar el JSON en un array asociativo
    $data = json_decode($json, true);

    // Verificar si el JSON se decodificó correctamente
    if ($data !== null) {
        // Obtener los valores del formulario de actualización
        $fname = isset($data['fname']) ? $data['fname'] : '';
        $lname = isset($data['lname']) ? $data['lname'] : '';
        $ssn = isset($data['ssn']) ? $data['ssn'] : '';
        $dno = isset($data['dno']) ? $data['dno'] : '';

        // Validar los campos del formulario
        // ...

        // Verificar si el empleado existe en la base de datos
        $sql = "SELECT * FROM employee WHERE Ssn = :ssn";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':ssn', $ssn);
        $stmt->execute();
        $existingEmployee = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingEmployee) {
            // Actualizar el empleado en la base de datos
            $sql = "UPDATE employee SET Fname = :fname, Lname = :lname, Ssn = :ssn, Dno = :dno WHERE Ssn = :ssn";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':fname', $fname);
            $stmt->bindValue(':lname', $lname);
            $stmt->bindValue(':ssn', $ssn);
            $stmt->bindValue(':dno', $dno);
            $stmt->execute();

            // Enviar una respuesta de éxito
            $response = [
                'success' => true,
                'message' => 'Empleado actualizado exitosamente'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            // Enviar una respuesta de error si el empleado no existe
            $response = [
                'success' => false,
                'message' => 'Empleado no encontrado'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    } else {
        // Enviar una respuesta de error si el JSON no se decodificó correctamente
        $response = [
            'success' => false,
            'message' => 'Error al decodificar el JSON'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Verificar si se solicitan los departamentos
if (isset($_GET['get_departments'])) {
    // Realizar la consulta a la base de datos para obtener los departamentos
    $sql = "SELECT * FROM department";
    $stmt = $pdo->query($sql);
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los departamentos como un objeto JSON
    header('Content-Type: application/json');
    echo json_encode($departments);
    exit; // Terminar la ejecución del script después de enviar la respuesta
}

// Verificar si se solicitan todos los empleados
if (isset($_GET['get_all_employees'])) {
    // Realizar la consulta a la base de datos para obtener todos los empleados
    $sql = "SELECT * FROM employee";
    $stmt = $pdo->query($sql);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los empleados como un objeto JSON
    header('Content-Type: application/json');
    echo json_encode($employees);
    exit; // Terminar la ejecución del script después de enviar la respuesta
}

// Construir la consulta SQL con los filtros de búsqueda
$sql = "SELECT * FROM employee WHERE 1=1"; // Consulta base

// Verificar y agregar los filtros de búsqueda
if (!empty($fname)) {
    // Agregar un comodín al inicio y al final del nombre para buscar valores similares
    $fname = "%$fname%";
    $sql .= " AND Fname LIKE :fname";
}

if (!empty($lname)) {
    // Agregar un comodín al inicio y al final del apellido para buscar valores similares
    $lname = "%$lname%";
    $sql .= " AND Lname LIKE :lname";
}

if (!empty($ssn)) {
    $sql .= " AND Ssn = :ssn";
}

if (!empty($dno)) {
    $sql .= " AND Dno = :dno";
}

$stmt = $pdo->prepare($sql);

// Asignar los valores de los parámetros en la consulta preparada
if (!empty($fname)) {
    $stmt->bindValue(':fname', $fname);
}

if (!empty($lname)) {
    $stmt->bindValue(':lname', $lname);
}

if (!empty($ssn)) {
    $stmt->bindValue(':ssn', $ssn);
}

if (!empty($dno)) {
    $stmt->bindValue(':dno', $dno);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los resultados como un objeto JSON
header('Content-Type: application/json');
echo json_encode($results);
?>

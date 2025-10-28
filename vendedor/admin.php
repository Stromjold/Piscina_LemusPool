<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['template_id'])) {
    header("Location: login.html");
    exit();
}
$template_id = $_SESSION['template_id'];
$template_name = $_SESSION['template_name'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: <?php echo htmlspecialchars($template_name); ?></title>
    <link rel="stylesheet" href="Estilos/admin.css"> 
    <link rel="stylesheet" href="Estilos/calendar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin: <?php echo htmlspecialchars($template_name); ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#Calendario">Calendario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('mensajes')">Mensajes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#finansas">Finanzas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#reportes">Reportes</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="php/logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section">
        <h1 class="titleAdmin" id="Calendario">Calendario de Visitas</h1>
        <div class="admin-container">
            <h2>Calendario</h2>
            <div class="calendar-header">
                <button id="prevMonth" class="btn btn-secondary">&lt; Anterior</button>
                <h3 id="currentMonthYear"></h3>
                <button id="nextMonth" class="btn btn-secondary">Siguiente &gt;</button>
            </div>
            
            <div id="adminCalendar" class="calendar-container">
              <div class="calendar">
                <div class="front">
                  <div class="current-date">
                    <h1>Friday 15th</h1>
                    <h1>January 2016</h1> 
                  </div>

                  <div class="current-month">
                    <ul class="week-days">
                      <li>MON</li>
                      <li>TUE</li>
                      <li>WED</li>
                      <li>THU</li>
                      <li>FRI</li>
                      <li>SAT</li>
                      <li>SUN</li>
                    </ul>

                    <div class="weeks">
                      
                    </div>
                  </div>
                </div>

                <div class="back">
                  <input placeholder="What's the event?">
                  <div class="info">
                    <div class="date">
                      <p class="info-date">
                      Date: <span>Jan 15th, 2016</span>
                      </p>
                      <p class="info-time">
                        Time: <span>6:35 PM</span>
                      </p>
                    </div>
                    <div class="address">
                      <p>
                        Address: <span>129 W 81st St, New York, NY</span>
                      </p>
                    </div>
                    <div class="observations">
                      <p>
                        Observations: <span>Be there 15 minutes earlier</span>
                      </p>
                    </div>
                  </div>

                  <div class="actions">
                    <button class="save">
                      Save <i class="ion-checkmark"></i>
                    </button>
                    <button class="dismiss">
                      Dismiss <i class="ion-android-close"></i>
                    </button>
                  </div>
                </div>

              </div>
            </div>
        </div>
        
        <div class="admin-buttons">
            <div id="addReservationBtn" class="admin-button-div">Añadir Reserva</div>
            <div id="listReservationsBtn" class="admin-button-div">Ver Reservas</div>
        </div>
    </section>

    <div id="newReservationModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3>Nueva Reserva</h3>
            <form id="newReservationForm">
                <label for="reservationId-input">ID de Cliente:</label>
                <input type="text" id="reservationId-input" maxlength="9" pattern="[0-9]*" required>
                
                <label for="clientName-input">Nombre del Cliente:</label>
                <input type="text" id="clientName-input" required>
                
                <label for="startDate-input">Fecha de Inicio:</label>
                <input type="date" id="startDate-input" required>
                
                <label for="newPeople-input">Cantidad de personas:</label>
                <input type="number" id="newPeople-input" min="1" required>
                
                <label for="newDays-input">Días de estancia:</label>
                <input type="number" id="newDays-input" min="1" required>
                
                <button type="submit">Agregar Reserva</button>
            </form>
        </div>
    </div>

    <div id="listReservationsModal" class="modal">
        <div class="modal-content modal-large">
            <span class="close-button">&times;</span>
            <h3>Lista de Reservas</h3>
            <table id="reservationsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Personas</th>
                        <th>Días</th>
                        <th>Fecha Inicio</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="reservationModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3>Detalle de Reserva</h3>
            <form id="reservationForm">
                <input type="hidden" id="editReservationId">
                <label for="date-input">Fecha:</label>
                <input type="date" id="date-input" required>

                <label for="people-input">Personas:</label>
                <input type="number" id="people-input" min="1" required>

                <label for="days-input">Días de estancia:</label>
                <input type="number" id="days-input" min="1" required>

                <button type="submit">Actualizar Reserva</button>
                <button type="button" id="deleteButton">Eliminar Reserva</button>
            </form>
        </div>
    </div>

    <section id="mensajes" class="section" > 
        <h1 class="titleAdmin">Mensajes de Contacto</h1>

        <div class="admin-container">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Buzón de Mensajes</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table table-striped">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Fecha</th>
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Teléfono</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Mensaje</th>
                            <th class="px-4 py-2 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="mensajesTableBody">
                        </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="section">
        <h1 class="titleAdmin" id="finansas">Finanzas</h1>
        <div class="container-fluid px-4 py-4">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-start border-success border-4 shadow">
                        <div class="card-body">
                            <p class="text-muted mb-1">Ingresos Totales</p>
                            <p class="h3 text-success mb-0" id="totalIngresos">$0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-start border-danger border-4 shadow">
                        <div class="card-body">
                            <p class="text-muted mb-1">Gastos Totales</p>
                            <p class="h3 text-danger mb-0" id="totalGastos">$0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-start border-primary border-4 shadow">
                        <div class="card-body">
                            <p class="text-muted mb-1">Balance</p>
                            <p class="h3 text-primary mb-0" id="balance">$0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">💵 Agregar Ingreso</h5>
                            <form id="formIngreso">
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <input type="text" id="descripcionIngreso" class="form-control" placeholder="Ej: Venta de entrada" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cantidad</label>
                                    <input type="number" id="cantidadIngreso" min="1" class="form-control" placeholder="1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Precio</label>
                                    <input type="number" id="precioIngreso" step="0.01" min="0.01" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select id="categoriaIngreso" class="form-select" required>
                                        <option value="entradas">Entradas</option>
                                        <option value="reservas">Reservas</option>
                                        <option value="arriendo_eventos">Arriendo de Eventos</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Agregar Ingreso</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">💸 Agregar Gasto</h5>
                            <form id="formGasto">
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <input type="text" id="descripcionGasto" class="form-control" placeholder="Ej: Compra de cloro" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cantidad</label>
                                    <input type="number" id="cantidadGasto" min="1" class="form-control" placeholder="1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Precio</label>
                                    <input type="number" id="precioGasto" step="0.01" min="0.01" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select id="categoriaGasto" class="form-select" required>
                                        <option value="quimicos">Químicos</option>
                                        <option value="servicios_basicos">Servicios Básicos</option>
                                        <option value="mantenimiento">Mantenimiento</option>
                                        <option value="salarios">Salarios</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger w-100">Agregar Gasto</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">📊 Historial de Transacciones</h5>
                        <button id="limpiarHistorial" class="btn btn-secondary btn-sm">Limpiar Historial</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Categoría</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="historialTransacciones"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <div id="categoryEditModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3>Editar Monto de Categoría</h3>
            <p class="text-muted mb-3">Edite el monto total de la categoría. Esto creará una transacción de ajuste para reflejar el nuevo total.</p>
            <form id="categoryEditForm">
                <input type="hidden" id="editCategoryId">
                <div class="mb-3">
                    <label class="form-label">Categoría:</label>
                    <input type="text" id="editCategoryName" class="form-control" readonly>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tipo:</label>
                    <input type="text" id="editCategoryType" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nuevo Monto Total:</label>
                    <input type="number" id="editCategoryMonto" step="0.01" min="0" required class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Guardar Ajuste</button>
            </form>
        </div>
    </div>

    <section class="section" id="reportes">
        <h1 class="titleAdmin">Reportes y Estadísticas</h1>

        <div class="admin-container mb-4">
            <h5 class="mb-3">📈 Resumen de Categorías (Actualizable)</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Tipo</th>
                            <th class="text-end">Monto Total ($)</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEstadisticas"></tbody>
                </table>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="admin-container">
                    <h5 class="mb-3">Ingresos por Categoría</h5>
                    <div class="chart-wrapper">
                        <canvas id="ingresosChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="admin-container">
                    <h5 class="mb-3">Gastos por Categoría</h5>
                    <div class="chart-wrapper">
                        <canvas id="gastosChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="admin-container">
                    <h5 class="mb-3">Reservas por Mes</h5>
                    <div class="chart-wrapper">
                        <canvas id="reservasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const TEMPLATE_ID = <?php echo json_encode($template_id); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="Js/calendar.js"></script>
    <script src="Js/reservas.js"></script>
    <script src="Js/admin.js"></script>
    <script src="Js/calendar_animation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
// js/admin.js

document.addEventListener('DOMContentLoaded', () => {
    // === Variables de Estado Globales ===
    let reservas = [];
    let transacciones = [];
    let mensajes = []; // Nuevo array para mensajes
    let categoriasResumen = [];
    let reservaSeleccionada = null;
    let chartIngresos, chartGastos, chartReservas;
    let currentDate = new Date(); // Fecha actual del calendario

    // === Variables de Rutas de la API ===
    // *** CRÍTICO: Estas rutas deben coincidir con la ubicación de tus archivos PHP.
    const API_RESERVAS = 'api/api_reservas.php'; 
    const API_FINANZAS = 'api/api_finanzas.php?action=reportes'; 
    const API_TRANSACCION = 'api/api_finanzas.php';
    const API_MENSAJES = 'api/api_mensajes.php'; // NUEVA RUTA
    // ===================================

    // === UTILS ===

    /**
     * Función genérica para peticiones AJAX (fetch)
     */
    async function fetchData(url, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
        };

        if (data && (method !== 'GET' && method !== 'HEAD')) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            
            // CRÍTICO: Manejo de errores HTTP 404/500
            if (!response.ok) {
                try {
                    const errorJson = await response.json();
                    alert(`Error HTTP! Estado: ${response.status}. Mensaje: ${errorJson.message || 'Error desconocido del servidor.'}`);
                    return { success: false, message: errorJson.message || 'Error del servidor.' };
                } catch (e) {
                    throw new Error(`Error HTTP! estado: ${response.status}. No se pudo parsear la respuesta de error.`);
                }
            }
            return await response.json();
        } catch (error) {
            alert('Error al conectar con el servidor: Falló la comunicación.');
            console.error('Fallo en la comunicación con el servidor:', error);
            return { success: false, message: 'Fallo la comunicación con el servidor.' };
        }
    }

    /**
     * Formatea un número como moneda.
     */
    function formatCurrency(number) {
        return new Intl.NumberFormat('es-CL', {
            style: 'currency',
            currency: 'CLP',
            minimumFractionDigits: 0,
        }).format(number);
    }

    // === LÓGICA DE CALENDARIO Y RESERVAS ===

    const modalReserva = document.getElementById('reservationModal');
    const modalNuevaReserva = document.getElementById('newReservationModal');
    const modalListaReservas = document.getElementById('listReservationsModal');

    function renderCalendar() {
        const calendarEl = document.getElementById('adminCalendar');
        calendarEl.innerHTML = '';
        
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        // Filtrar reservas del mes actual
        const reservasMes = reservas.filter(r => {
            const rDate = new Date(r.fecha_inicio);
            return rDate.getFullYear() === year && rDate.getMonth() === month;
        });

        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Rellenar días anteriores (para que el primer día del mes caiga en el día correcto)
        for (let i = 0; i < firstDayOfMonth; i++) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('calendar-day', 'empty-day');
            calendarEl.appendChild(dayElement);
        }

        // Días del mes
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('calendar-day');
            dayElement.textContent = day;
            
            // Marcar reservas en el día
            const dayReservas = reservasMes.filter(r => new Date(r.fecha_inicio).getDate() === day);
            
            if (dayReservas.length > 0) {
                dayElement.classList.add('has-reservation');
                dayReservas.forEach(r => {
                    const reservationBadge = document.createElement('div');
                    reservationBadge.classList.add('reservation-badge');
                    reservationBadge.textContent = r.nombre_cliente;
                    reservationBadge.onclick = (e) => {
                        e.stopPropagation();
                        openEditReservationModal(r);
                    };
                    dayElement.appendChild(reservationBadge);
                });
            }

            calendarEl.appendChild(dayElement);
        }

        // Actualizar el título del mes/año
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        document.getElementById('currentMonthYear').textContent = `${monthNames[month]} ${year}`;
    }

    // Botones de navegación del calendario
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    
    // === LÓGICA DE CARGA DE DATOS ===
    
    // 1. Reservas
    async function loadReservasData() {
        const result = await fetchData(API_RESERVAS, 'GET');
        if (result.success) {
            reservas = result.reservas.map(r => ({
                ...r,
                fecha_inicio: r.fecha_inicio 
            }));
            renderCalendar();
            renderReservasList();
        } else {
            console.error('Error al cargar reservas:', result.message);
            alert('Error al cargar reservas: ' + result.message);
        }
    }
    
    // 2. Finanzas
    async function loadFinanzasData() {
        const result = await fetchData(API_FINANZAS, 'GET');
        if (result.success) {
            // Totales
            document.getElementById('totalIngresos').textContent = formatCurrency(result.totales.total_ingresos);
            document.getElementById('totalGastos').textContent = formatCurrency(result.totales.total_gastos);
            document.getElementById('balance').textContent = formatCurrency(result.balance);

            // Resumen de Categorías y Gráficos
            categoriasResumen = result.categorias;
            renderTablaEstadisticas();
            updateCharts(result.categorias, result.reservas_mes);
            
            // Historial
            transacciones = result.historial;
            renderHistorialTransacciones();

        } else {
            console.error('Error al cargar finanzas:', result.message);
            alert('Error al cargar finanzas: ' + result.message);
        }
    }
    
    // 3. Mensajes (NUEVO)
    async function loadMessages() {
        try {
            const result = await fetchData(API_MENSAJES, 'GET');
            
            if (result.success) {
                mensajes = result.mensajes; // Guardar en el estado global
                renderMessages(mensajes);
            } else {
                document.getElementById('mensajesTableBody').innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Error: ${result.message}</td></tr>`;
            }
        } catch (e) {
            document.getElementById('mensajesTableBody').innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Falló la comunicación con el servidor.</td></tr>`;
        }
    }

    // === LÓGICA DE RENDERIZADO ===
    
    // Renderizado de Mensajes (NUEVO)
    function renderMessages(messages) {
        const tableBody = document.getElementById('mensajesTableBody');
        if (!tableBody) return;
        
        tableBody.innerHTML = ''; 

        if (messages.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4">No hay mensajes en el buzón.</td></tr>';
            return;
        }

        messages.forEach(msg => {
            const row = document.createElement('tr');
            // Formato de fecha YYYY-MM-DD
            const datePart = msg.fecha_envio.split(' ')[0]; 
            
            row.innerHTML = `
                <td class="px-4 py-2">${datePart}</td>
                <td class="px-4 py-2">${msg.nombre}</td>
                <td class="px-4 py-2">${msg.email}</td>
                <td class="px-4 py-2">${msg.mensaje}</td>
                <td class="px-4 py-2 text-center">
                    <button class="btn btn-sm btn-danger delete-message" data-id="${msg.id}">
                        Eliminar
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // === LÓGICA DE ELIMINACIÓN ===
    
    // Eliminar Mensaje (NUEVO)
    async function deleteMessage(id) {
        if (!confirm('¿Seguro que quieres eliminar este mensaje?')) return;
        
        const result = await fetchData(API_MENSAJES, 'DELETE', { id: id });
        
        if (result.success) {
            alert(result.message);
            loadMessages(); // Recargar la lista tras eliminar
        } else {
            alert(`Error al eliminar: ${result.message}`);
        }
    }
    
    // Eliminar Reserva
    document.getElementById('deleteButton').addEventListener('click', async () => {
        if (!confirm('¿Está seguro de que desea eliminar esta reserva?')) return;
        
        const result = await fetchData('api_reservas.php', 'DELETE', { id: reservaSeleccionada.id });
        if (result.success) {
            alert('Reserva eliminada con éxito.');
            modalReserva.style.display = 'none';
            loadReservasData();
        }
    });
    
    // Inicializar Modals (usando el mismo cierre para todos los modals)
    document.querySelectorAll('.close-button').forEach(button => {
        button.addEventListener('click', () => {
            button.closest('.modal').style.display = 'none';
        });
    });

    document.getElementById('addReservationBtn').addEventListener('click', () => {
        modalNuevaReserva.style.display = 'block';
    });

    document.getElementById('listReservationsBtn').addEventListener('click', () => {
        modalListaReservas.style.display = 'block';
    });
    
    // Cerrar modal al hacer click fuera
    window.onclick = (event) => {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    };

    // === LÓGICA DE FINANZAS ===

    async function loadFinanzasData() {
        const result = await fetchData('api_finanzas.php?action=reportes', 'GET');
        if (result.success) {
            // Totales
            document.getElementById('totalIngresos').textContent = formatCurrency(result.totales.total_ingresos);
            document.getElementById('totalGastos').textContent = formatCurrency(result.totales.total_gastos);
            document.getElementById('balance').textContent = formatCurrency(result.balance);

            // Resumen de Categorías y Gráficos
            categoriasResumen = result.categorias;
            renderTablaEstadisticas();
            updateCharts(result.categorias, result.reservas_mes);
            
            // Historial
            transacciones = result.historial;
            renderHistorialTransacciones();

        } else {
            console.error('Error al cargar finanzas:', result.message);
            alert('Error al cargar finanzas: ' + result.message);
        }
    }

    function renderHistorialTransacciones() {
        const tbody = document.getElementById('historialTransacciones');
        tbody.innerHTML = '';

        transacciones.forEach(t => {
            const row = tbody.insertRow();
            row.classList.add(t.tipo === 'ingreso' ? 'table-success' : 'table-danger');
            
            row.insertCell().textContent = t.fecha;
            row.insertCell().textContent = t.descripcion;
            row.insertCell().textContent = t.cantidad;
            row.insertCell().textContent = formatCurrency(t.precio);
            row.insertCell().textContent = t.categoria;
            row.insertCell().textContent = t.tipo.toUpperCase();
            
            const totalCell = row.insertCell();
            totalCell.textContent = formatCurrency(t.total);
            totalCell.classList.add('text-end');
            
            const actionCell = row.insertCell();
            actionCell.classList.add('text-center');
            const deleteBtn = document.createElement('button');
            deleteBtn.textContent = 'X';
            deleteBtn.classList.add('btn', 'btn-sm', 'btn-danger');
            deleteBtn.onclick = () => deleteTransaction(t.id);
            actionCell.appendChild(deleteBtn);
        });
    }

    function renderTablaEstadisticas() {
        const tbody = document.getElementById('tablaEstadisticas');
        tbody.innerHTML = '';

        categoriasResumen.forEach(c => {
            const row = tbody.insertRow();
            row.insertCell().textContent = c.categoria.toUpperCase();
            row.insertCell().textContent = c.tipo.toUpperCase();
            
            const montoCell = row.insertCell();
            montoCell.textContent = formatCurrency(c.monto_total);
            montoCell.classList.add('text-end');
            
            const actionCell = row.insertCell();
            actionCell.classList.add('text-center');
            const editBtn = document.createElement('button');
            editBtn.textContent = 'Ajustar';
            editBtn.classList.add('btn', 'btn-sm', 'btn-info', 'text-white');
            editBtn.onclick = () => openEditCategoryModal(c);
            actionCell.appendChild(editBtn);
        });
    }

    // Funcionalidad para la edición de categorías
    function openEditCategoryModal(categoria) {
        document.getElementById('editCategoryId').value = categoria.id || '';
        document.getElementById('editCategoryName').value = categoria.categoria.toUpperCase();
        document.getElementById('editCategoryType').value = categoria.tipo.toUpperCase();
        document.getElementById('editCategoryMonto').value = categoria.monto_total;
        document.getElementById('categoryEditModal').style.display = 'block';
    }

    // Handlers de Formularios de Finanzas
    document.getElementById('formIngreso').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            descripcion: document.getElementById('descripcionIngreso').value,
            cantidad: parseInt(document.getElementById('cantidadIngreso').value),
            precio: parseFloat(document.getElementById('precioIngreso').value),
            categoria: document.getElementById('categoriaIngreso').value,
            tipo: 'ingreso'
        };
        const result = await fetchData('api_finanzas.php', 'POST', data);
        if (result.success) {
            alert(result.message);
            document.getElementById('formIngreso').reset();
            loadFinanzasData();
        }
    });

    document.getElementById('formGasto').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            descripcion: document.getElementById('descripcionGasto').value,
            cantidad: parseInt(document.getElementById('cantidadGasto').value),
            precio: parseFloat(document.getElementById('precioGasto').value),
            categoria: document.getElementById('categoriaGasto').value,
            tipo: 'gasto'
        };
        const result = await fetchData('api_finanzas.php', 'POST', data);
        if (result.success) {
            alert(result.message);
            document.getElementById('formGasto').reset();
            loadFinanzasData();
        }
    });
    
    document.getElementById('categoryEditForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const categoria = document.getElementById('editCategoryName').value.toLowerCase();
        const tipo = document.getElementById('editCategoryType').value.toLowerCase();
        const nuevo_monto_total = parseFloat(document.getElementById('editCategoryMonto').value);

        const data = {
            categoria: categoria,
            tipo: tipo,
            nuevo_monto_total: nuevo_monto_total
        };

        const result = await fetchData('api_finanzas.php', 'PUT', data);
        if (result.success) {
            alert(result.message);
            document.getElementById('categoryEditModal').style.display = 'none';
            loadFinanzasData();
        }
    });

    async function deleteTransaction(id) {
        if (!confirm('¿Está seguro de que desea eliminar esta transacción?')) return;
        const result = await fetchData('api_finanzas.php', 'DELETE', { id: id });
        if (result.success) {
            alert(result.message);
            loadFinanzasData();
        }
    }

    document.getElementById('limpiarHistorial').addEventListener('click', async () => {
        if (!confirm('ADVERTENCIA: ¿Está seguro de que desea ELIMINAR TODAS las transacciones del historial? Esta acción es irreversible.')) return;
        const result = await fetchData('api_finanzas.php', 'DELETE', { action: 'limpiar_historial' });
        if (result.success) {
            alert(result.message);
            loadFinanzasData();
        }
    });

    // === LÓGICA DE GRÁFICOS (CHART.JS) ===

    function updateCharts(categorias, reservasMes) {
        // Destruir gráficos anteriores si existen
        if (chartIngresos) chartIngresos.destroy();
        if (chartGastos) chartGastos.destroy();
        if (chartReservas) chartReservas.destroy();

        // 1. Gráfico de Ingresos
        const ingresosData = categorias.filter(c => c.tipo === 'ingreso');
        
        if (ingresosData.length > 0) {
            chartIngresos = new Chart(document.getElementById('ingresosChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ingresosData.map(c => c.categoria.toUpperCase()),
                    datasets: [{
                        data: ingresosData.map(c => c.monto_total),
                        backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#6366F1', '#8B5CF6'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        title: { display: false }
                    }
                }
            });
        }

        // 2. Gráfico de Gastos
        const gastosData = categorias.filter(c => c.tipo === 'gasto');
        
        if (gastosData.length > 0) {
            chartGastos = new Chart(document.getElementById('gastosChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: gastosData.map(c => c.categoria.toUpperCase()),
                    datasets: [{
                        data: gastosData.map(c => c.monto_total),
                        backgroundColor: ['#EF4444', '#F87171', '#FCD34D', '#111827', '#DC2626'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        title: { display: false }
                    }
                }
            });
        }

        // 3. Gráfico de Reservas por Mes
        if (reservasMes && reservasMes.length > 0) {
            reservasMes.sort((a, b) => (a.mes > b.mes) ? 1 : -1);

            chartReservas = new Chart(document.getElementById('reservasChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: reservasMes.map(r => r.mes),
                    datasets: [{
                        label: 'Total de Reservas',
                        data: reservasMes.map(r => r.total_reservas),
                        backgroundColor: '#3B82F6',
                        borderColor: '#2563EB',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { 
                                stepSize: 1,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        title: { display: false }
                    }
                }
            });
        }
    }

    // === Inicialización (Carga inicial de datos) ===

    // Handler para formulario de contacto (guarda mensajes en localStorage)
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Detiene el envío del formulario tradicional

            // 1. Obtener valores del formulario
            const name = document.getElementById('contactName').value;
            const email = document.getElementById('contactEmail').value;
            const message = document.getElementById('contactMessage').value;
            
            // 2. Crear el objeto del mensaje
            const newMessage = {
                id: Date.now(), // ID único basado en la marca de tiempo
                date: new Date().toLocaleDateString('es-ES'),
                name: name,
                email: email,
                message: message,
                read: false
            };

            // 3. Obtener mensajes existentes o inicializar un array vacío
            let messages = JSON.parse(localStorage.getItem('contactMessages')) || [];
            
            // 4. Agregar el nuevo mensaje
            messages.push(newMessage);
            
            // 5. Guardar el array actualizado en localStorage
            localStorage.setItem('contactMessages', JSON.stringify(messages));

            // 6. Notificar al usuario y limpiar el formulario
            alert('¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.');
            contactForm.reset(); 
        });
    }

    loadReservasData();
    loadFinanzasData();
    loadMessages(); // <--- Carga de Mensajes

    // Evento para eliminar un mensaje (delegación)
    const mensajesTableBody = document.getElementById('mensajesTableBody');
    if (mensajesTableBody) {
        mensajesTableBody.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-message')) {
                deleteMessage(parseInt(e.target.getAttribute('data-id')));
            }
        });
    }
});
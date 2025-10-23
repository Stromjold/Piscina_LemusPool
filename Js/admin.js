document.addEventListener('DOMContentLoaded', () => {

    // 🎯 SELECCIÓN DE ELEMENTOS DEL DOM
    // ------------------------------------
    const calendarGrid = document.getElementById('adminCalendar');
    const monthYearDisplay = document.getElementById('currentMonthYear');
    const prevMonthButton = document.getElementById('prevMonth');
    const nextMonthButton = document.getElementById('nextMonth');

    // Modales y botones
    const modal = document.getElementById('reservationModal');
    const newReservationModal = document.getElementById('newReservationModal');
    const listReservationsModal = document.getElementById('listReservationsModal');

    const addReservationBtn = document.getElementById('addReservationBtn');
    const listReservationsBtn = document.getElementById('listReservationsBtn');
    const closeButtons = document.querySelectorAll('.modal .close-button');

    // Formularios
    const reservationForm = document.getElementById('reservationForm');
    const newReservationForm = document.getElementById('newReservationForm');

    // Campos de formulario de reserva existente
    const dateInput = document.getElementById('date-input');
    const peopleInput = document.getElementById('people-input');
    const daysInput = document.getElementById('days-input');
    const deleteButton = document.getElementById('deleteButton');

    // Campos de formulario de nueva reserva
    const reservationIdInput = document.getElementById('reservationId-input');
    const clientNameInput = document.getElementById('clientName-input');
    const startDateInput = document.getElementById('startDate-input');
    const newPeopleInput = document.getElementById('newPeople-input');
    const newDaysInput = document.getElementById('newDays-input');
    
    // 🎯 FINANZAS: SELECCIÓN DE ELEMENTOS
    // ------------------------------------
    const totalIngresosEl = document.getElementById('totalIngresos');
    const totalGastosEl = document.getElementById('totalGastos');
    const balanceEl = document.getElementById('balance');
    const historialTransaccionesEl = document.getElementById('historialTransacciones');
    const formIngreso = document.getElementById('formIngreso');
    const formGasto = document.getElementById('formGasto');
    const limpiarHistorialBtn = document.getElementById('limpiarHistorial');
    

    // Add new DOM element selectors
    const cantidadIngreso = document.getElementById('cantidadIngreso');
    const precioIngreso = document.getElementById('precioIngreso');
    const cantidadGasto = document.getElementById('cantidadGasto');
    const precioGasto = document.getElementById('precioGasto');



    // 🎯 ESTRUCTURAS DE DATOS
    // ------------------------------------
    let reservations = JSON.parse(localStorage.getItem('reservations')) || {};
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let currentDayReservations = {};
    
    // 🎯 FINANZAS: ESTRUCTURAS DE DATOS
    // ------------------------------------
    let transacciones = JSON.parse(localStorage.getItem('transacciones')) || [];


    // 🎯 LÓGICA DEL CALENDARIO
    // ------------------------------------
    const renderCalendar = () => {
        calendarGrid.innerHTML = '';
        const firstDayOfMonth = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        monthYearDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        
        for (let i = 0; i < firstDayOfMonth; i++) {
            const emptyCell = document.createElement('div');
            calendarGrid.appendChild(emptyCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement('div');
            dayCell.textContent = day;
            dayCell.classList.add('day-cell');
            
            const dayKey = `${currentYear}-${currentMonth + 1}-${day}`;
            if (reservations[dayKey]) {
                dayCell.classList.add('reserved-day');
                dayCell.innerHTML += `<br> <span class="reserved-count">(${reservations[dayKey].length})</span>`;
            }

            dayCell.addEventListener('click', () => {
                if (reservations[dayKey]) {
                    currentDayReservations = reservations[dayKey];
                    showReservationsForDay(dayKey);
                } else {
                    alert('No hay reservas para este día.');
                }
            });
            
            calendarGrid.appendChild(dayCell);
        }
    };

    const showReservationsForDay = (dayKey) => {
        dateInput.value = dayKey;
        if (currentDayReservations && currentDayReservations.length > 0) {
            peopleInput.value = currentDayReservations[0].people;
            daysInput.value = currentDayReservations[0].days;
        }
        modal.style.display = 'block';
    };

    const saveReservations = () => {
        localStorage.setItem('reservations', JSON.stringify(reservations));
    };

    // 🎯 LÓGICA DE MODALES
    // ------------------------------------
    addReservationBtn.addEventListener('click', () => {
        newReservationModal.style.display = 'block';
    });

    listReservationsBtn.addEventListener('click', () => {
        listReservationsModal.style.display = 'block';
    });
    
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.style.display = 'none';
            newReservationModal.style.display = 'none';
            listReservationsModal.style.display = 'none';
        });
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) modal.style.display = 'none';
        if (event.target === newReservationModal) newReservationModal.style.display = 'none';
        if (event.target === listReservationsModal) listReservationsModal.style.display = 'none';
    });

    // 🎯 LÓGICA DE FORMULARIOS
    // ------------------------------------
    reservationForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const date = dateInput.value;
        const people = peopleInput.value;
        const days = daysInput.value;

        if (reservations[date]) {
            reservations[date] = [{ people, days }]; // Update logic
            saveReservations();
            renderCalendar();
            modal.style.display = 'none';
        }
    });

    deleteButton.addEventListener('click', () => {
        const date = dateInput.value;
        delete reservations[date];
        saveReservations();
        renderCalendar();
        modal.style.display = 'none';
    });

    newReservationForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = reservationIdInput.value;
        const name = clientNameInput.value;
        const startDate = startDateInput.value;
        const people = newPeopleInput.value;
        const days = newDaysInput.value;

        if (!reservations[startDate]) {
            reservations[startDate] = [];
        }

        reservations[startDate].push({ id, name, people, days });
        saveReservations();
        renderCalendar();
        newReservationForm.reset();
        newReservationModal.style.display = 'none';
    });

    // 🎯 FINANZAS: LÓGICA DE LA APLICACIÓN
    // ------------------------------------
    // In the Finanzas section
    function actualizarResumen() {
        const totalIngresos = transacciones
            .filter(t => t.tipo === 'ingreso')
            .reduce((sum, t) => sum + (parseFloat(t.cantidad) * parseFloat(t.precio)), 0);
        
        const totalGastos = transacciones
            .filter(t => t.tipo === 'gasto')
            .reduce((sum, t) => sum + (parseFloat(t.cantidad) * parseFloat(t.precio)), 0);

        const balance = totalIngresos - totalGastos;

        totalIngresosEl.textContent = `$${totalIngresos.toFixed(2)}`;
        totalGastosEl.textContent = `$${totalGastos.toFixed(2)}`;
        balanceEl.textContent = `$${balance.toFixed(2)}`;
        balanceEl.style.color = balance >= 0 ? '#10B981' : '#EF4444';
    }

    // In the Finanzas section
    function actualizarHistorial() {
        historialTransaccionesEl.innerHTML = '';
        transacciones.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

        transacciones.forEach((t, index) => {
            const row = document.createElement('tr');
            const total = (parseFloat(t.cantidad) * parseFloat(t.precio)).toFixed(2);
            row.innerHTML = `
                <td class="py-2 px-4 text-sm text-gray-700">${t.fecha}</td>
                <td class="py-2 px-4 text-sm text-gray-700">${t.descripcion}</td>
                <td class="py-2 px-4 text-sm text-gray-700">${t.cantidad}</td>
                <td class="py-2 px-4 text-sm text-gray-700">$${parseFloat(t.precio).toFixed(2)}</td>
                <td class="py-2 px-4 text-sm text-gray-700">${t.categoria}</td>
                <td class="py-2 px-4 text-sm text-gray-700">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold ${t.tipo === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${t.tipo}
                    </span>
                </td>
                <td class="py-2 px-4 text-sm font-bold text-right ${t.tipo === 'ingreso' ? 'text-green-600' : 'text-red-600'}">$${total}</td>
                <td class="py-2 px-4 text-center">
                    <button class="text-red-500 hover:text-red-700 text-sm" data-index="${index}">Eliminar</button>
                </td>
            `;
            historialTransaccionesEl.appendChild(row);
        });

        document.querySelectorAll('#historialTransacciones button').forEach(button => {
            button.addEventListener('click', (e) => {
                const index = e.target.dataset.index;
                transacciones.splice(index, 1);
                localStorage.setItem('transacciones', JSON.stringify(transacciones));
                actualizarResumen();
                actualizarHistorial();
                renderIngresosChart();
            });
        });
    }

    function agregarTransaccion(tipo, descripcion, cantidad, precio, categoria) {
        const nuevaTransaccion = {
            id: Date.now(),
            fecha: new Date().toLocaleDateString('es-ES'),
            tipo,
            descripcion,
            cantidad: parseFloat(cantidad),
            precio: parseFloat(precio),
            categoria
        };
        transacciones.push(nuevaTransaccion);
        localStorage.setItem('transacciones', JSON.stringify(transacciones));
        actualizarResumen();
        actualizarHistorial();
        renderIngresosChart();
    }

    formIngreso.addEventListener('submit', function(e) {
        e.preventDefault();
        const descripcion = document.getElementById('descripcionIngreso').value;
        const cantidad = document.getElementById('cantidadIngreso').value;
        const precio = document.getElementById('precioIngreso').value;
        const categoria = document.getElementById('categoriaIngreso').value;
        if (descripcion && cantidad > 0 && precio > 0) {
            agregarTransaccion('ingreso', descripcion, cantidad, precio, categoria);
            this.reset();
        } else {
            alert('Por favor completa todos los campos correctamente');
        }
    });

    formGasto.addEventListener('submit', function(e) {
        e.preventDefault();
        const descripcion = document.getElementById('descripcionGasto').value;
        const cantidad = document.getElementById('cantidadGasto').value;
        const precio = document.getElementById('precioGasto').value;
        const categoria = document.getElementById('categoriaGasto').value;
        if (descripcion && cantidad > 0 && precio > 0) {
            agregarTransaccion('gasto', descripcion, cantidad, precio, categoria);
            this.reset();
        } else {
            alert('Por favor completa todos los campos correctamente');
        }
    });

    limpiarHistorialBtn.addEventListener('click', function() {
        if (confirm('¿Estás seguro de que quieres limpiar todo el historial?')) {
            transacciones = [];
            localStorage.removeItem('transacciones');
            actualizarResumen();
            actualizarHistorial();
        }
    });

    // Inicializar la aplicación
    renderCalendar();
    actualizarResumen();
    actualizarHistorial();
});

// 🎯 LÓGICA DE REPORTES Y ESTADÍSTICAS
// ------------------------------------

// Función para procesar datos y renderizar el gráfico de ingresos
const renderIngresosChart = () => {
    const ingresosPorCategoria = {};
    transacciones.filter(t => t.tipo === 'ingreso').forEach(t => {
        ingresosPorCategoria[t.categoria] = (ingresosPorCategoria[t.categoria] || 0) + (parseFloat(t.cantidad) * parseFloat(t.precio));
    });

    const data = {
        labels: Object.keys(ingresosPorCategoria),
        datasets: [{
            label: 'Ingresos por Categoría',
            data: Object.values(ingresosPorCategoria),
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)',
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)'
            ],
            borderWidth: 1
        }]
    };

    const config = {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(context.parsed);
                            }
                            return label;
                        }
                    }
                }
            }
        },
    };

    new Chart(document.getElementById('ingresosChart'), config);
};

// Función para procesar datos y renderizar el gráfico de reservas
const renderReservasChart = () => {
    const reservasPorMes = {};
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

    // Usa `reservations` del archivo
    for (const date in reservations) {
        if (reservations.hasOwnProperty(date)) {
            const monthIndex = new Date(date).getMonth();
            const monthName = monthNames[monthIndex];
            reservasPorMes[monthName] = (reservasPorMes[monthName] || 0) + 1;
        }
    }

    const data = {
        labels: Object.keys(reservasPorMes),
        datasets: [{
            label: 'Número de Reservas',
            data: Object.values(reservasPorMes),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
    };

    new Chart(document.getElementById('reservasChart'), config);
};

// Llama a las funciones cuando la página esté lista
document.addEventListener('DOMContentLoaded', () => {
    // ... tu código existente
    renderIngresosChart();
    renderReservasChart();
});
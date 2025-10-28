function renderReservasList() {
    const tableBody = document.getElementById('reservasListBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    if (reservas.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4">No hay reservas registradas.</td></tr>';
        return;
    }

    reservas.forEach(reserva => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-4 py-2">${reserva.fecha_inicio}</td>
            <td class="px-4 py-2">${reserva.nombre_cliente}</td>
            <td class="px-4 py-2">${reserva.dias_estancia} día(s)</td>
            <td class="px-4 py-2">${reserva.cantidad_personas} persona(s)</td>
            <td class="px-4 py-2 text-center">
                <button class="btn btn-sm btn-info text-white edit-reserva" data-id="${reserva.id}">
                    Ver/Editar
                </button>
            </td>
        `;
        tableBody.appendChild(row);

        // Añadir evento al botón de editar
        const editBtn = row.querySelector('.edit-reserva');
        editBtn.addEventListener('click', () => {
            openEditReservationModal(reserva);
        });
    });
}

function openEditReservationModal(reserva) {
    reservaSeleccionada = reserva;
    
    // Rellenar el modal con los datos de la reserva
    document.getElementById('editClienteId').value = reserva.cliente_id || '';
    document.getElementById('editNombreCliente').value = reserva.nombre_cliente;
    document.getElementById('editFechaInicio').value = reserva.fecha_inicio;
    document.getElementById('editDiasEstancia').value = reserva.dias_estancia;
    document.getElementById('editCantidadPersonas').value = reserva.cantidad_personas;
    
    // Mostrar el modal
    document.getElementById('reservationModal').style.display = 'block';
}
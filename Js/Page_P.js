document.addEventListener('DOMContentLoaded', async () => {
    const calendarContainer = document.querySelector('.calendar-container');
    if (!calendarContainer) {
        console.error('No se encontró el contenedor del calendario');
        return;
    }

    // Días de la semana en español
    const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    // Variables para el seguimiento del mes actual
    const today = new Date();
    let currentMonth = today.getMonth() + 1; // 1-12
    let currentYear = today.getFullYear();
    
    console.log('Inicializando calendario:', { currentMonth, currentYear });

    // Función para actualizar el título del calendario
    function updateCalendarTitle(month, year) {
        // Asegúrate de que month sea 1-12
        document.querySelector('.calendar-title').textContent = `${monthNames[month - 1]} ${year}`;
    }
    
    // Función para obtener los días ocupados desde el backend
    async function getOccupiedDays(month, year) {
        try {
            console.log('Consultando reservas para:', { month, year });
            const response = await fetch('php/api_reservas.php');
            console.log('Respuesta del servidor:', response.status, response.statusText);
            
            const jsonData = await response.json();
            console.log('Datos recibidos del servidor:', jsonData);
            
            if (!jsonData.success || !jsonData.reservas) {
                console.error('Error en la respuesta:', jsonData);
                return [];
            }
            
            const reservas = jsonData.reservas;
            console.log('Reservas encontradas:', reservas);
            
            const occupiedDays = new Set(); // Usar Set para evitar duplicados
            
            reservas.forEach(reserva => {
                console.log('Procesando reserva:', reserva);
                const fechaInicio = new Date(reserva.fecha_inicio);
                const diasEstancia = parseInt(reserva.dias_estancia);
                
                console.log(`Reserva del ${fechaInicio.toISOString()} por ${diasEstancia} días`);
                
                for (let i = 0; i < diasEstancia; i++) {
                    const fechaOcupada = new Date(fechaInicio);
                    fechaOcupada.setDate(fechaInicio.getDate() + i);
                    
                    if (fechaOcupada.getFullYear() === year && 
                        fechaOcupada.getMonth() + 1 === month) {
                        occupiedDays.add(fechaOcupada.getDate());
                        console.log(`Día ${fechaOcupada.getDate()} marcado como ocupado`);
                    }
                }
            });
            
            const diasOcupados = Array.from(occupiedDays);
            console.log('Días ocupados calculados:', diasOcupados);
            return diasOcupados;
        } catch (error) {
            console.error('Error obteniendo días ocupados:', error);
            return [];
        }
    }

    async function createCalendar(month, year) {
        console.log('Creando calendario para:', { month, year });
        calendarContainer.innerHTML = '';
        updateCalendarTitle(month, year);
        const date = new Date(year, month - 1, 1);
        const firstDay = date.getDay();
        const daysInMonth = new Date(year, month, 0).getDate();
        console.log('Información del mes:', { firstDay, daysInMonth });

        try {
            // Obtener los días ocupados del backend para el mes y año actuales
            const occupiedDays = await getOccupiedDays(month, year);
            console.log('Días ocupados recibidos en createCalendar:', occupiedDays);

            // Crear días en blanco para el inicio del mes
            for (let i = 0; i < firstDay; i++) {
                const blankDay = document.createElement('div');
                blankDay.classList.add('calendar-day', 'day-blank');
                calendarContainer.appendChild(blankDay);
            }

            // Crear los días del mes
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.classList.add('calendar-day');
                
                if (occupiedDays.includes(day)) {
                    dayElement.classList.add('day-occupied');
                    dayElement.title = 'Día Ocupado';
                } else {
                    dayElement.classList.add('day-free');
                    dayElement.title = 'Día Disponible';
                }

                const dayNumber = document.createElement('div');
                dayNumber.classList.add('day-number');
                dayNumber.textContent = day;

                const dayName = document.createElement('div');
                dayName.classList.add('day-name');
                dayName.textContent = daysOfWeek[(firstDay + day - 1) % 7];

                dayElement.appendChild(dayName);
                dayElement.appendChild(dayNumber);
                calendarContainer.appendChild(dayElement);
            }
        } catch (error) {
            console.error('Error al crear el calendario:', error);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('calendar-day');
            if (occupiedDays.includes(day)) {
                dayElement.classList.add('day-occupied');
                dayElement.title = 'Día Ocupado';
            } else {
                dayElement.classList.add('day-free');
                dayElement.title = 'Día Disponible';
            }
            const dayNumber = document.createElement('div');
            dayNumber.classList.add('day-number');
            dayNumber.textContent = day;
            const dayName = document.createElement('div');
            dayName.classList.add('day-name');
            dayName.textContent = daysOfWeek[(firstDay + day - 1) % 7];
            dayElement.appendChild(dayName);
            dayElement.appendChild(dayNumber);
            calendarContainer.appendChild(dayElement);
        }
    }
    
    // Función global para cambiar de mes (llamada desde los botones en el HTML)
    window.changeMonth = function(offset) {
        currentMonth += offset;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        } else if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        createCalendar(currentMonth, currentYear);
    }

    // Generar el calendario inicial
    createCalendar(currentMonth, currentYear);

    // Manejo del formulario de contacto
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault(); 

            const dataToSend = {
                name: document.getElementById('contactName').value,
                email: document.getElementById('contactEmail').value,
                telefono: document.getElementById('contactPhone').value || 'No proporcionado',
                message: document.getElementById('contactMessage').value
            };

            try {
                const response = await fetch('php/api_mensajes.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dataToSend)
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('¡Mensaje enviado con éxito!');
                    contactForm.reset();
                } else {
                    alert(`Error al enviar mensaje: ${result.message}`);
                }
            } catch (error) {
                alert('Error al conectar con el servidor para enviar el mensaje. Revisa la consola para más detalles.');
                console.error('Fetch error:', error);
            }
        });
    }
});
document.addEventListener('DOMContentLoaded', async () => {
    const calendarContainer = document.querySelector('.calendar-container');
    if (!calendarContainer) {
        return;
    }

    const today = new Date();
    let currentMonth = today.getMonth() + 1;
    let currentYear = today.getFullYear();

    async function getOccupiedDays(month, year) {
        try {
            const response = await fetch(`php/api_reservas.php?month=${month}&year=${year}`);
            const jsonData = await response.json();
            
            if (!jsonData.success || !jsonData.reservas) {
                return [];
            }
            
            const reservas = jsonData.reservas;
            const occupiedDays = new Set();
            
            reservas.forEach(reserva => {
                const fechaInicio = new Date(reserva.fecha_inicio);
                const diasEstancia = parseInt(reserva.dias_estancia);
                
                for (let i = 0; i < diasEstancia; i++) {
                    const fechaOcupada = new Date(fechaInicio);
                    fechaOcupada.setDate(fechaInicio.getDate() + i);
                    
                    if (fechaOcupada.getFullYear() === year && 
                        fechaOcupada.getMonth() + 1 === month) {
                        occupiedDays.add(fechaOcupada.getDate());
                    }
                }
            });
            
            return Array.from(occupiedDays);
        } catch (error) {
            return [];
        }
    }

    async function updateCalendar(month, year) {
        const occupiedDays = await getOccupiedDays(month, year);
        createCalendar(calendarContainer, month, year, occupiedDays);
    }

    window.changeMonth = function(offset) {
        currentMonth += offset;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        } else if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        updateCalendar(currentMonth, currentYear);
    }

    updateCalendar(currentMonth, currentYear);

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
                alert('Error al conectar con el servidor para enviar el mensaje.');
            }
        });
    }
});
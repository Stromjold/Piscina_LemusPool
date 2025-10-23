document.addEventListener('DOMContentLoaded', () => {
    const calendarContainer = document.querySelector('.calendar-container');

    // Días de la semana en español
    const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    // Función para obtener los días ocupados desde el almacenamiento local
    function getOccupiedDays(month, year) {
        const storedReservations = localStorage.getItem('reservations');
        const reservations = storedReservations ? JSON.parse(storedReservations) : {};
        
        const occupiedDays = [];
        for (const dateKey in reservations) {
            // El formato de la clave es "YYYY-M-D"
            const [resYear, resMonth, resDay] = dateKey.split('-').map(Number);
            if (resYear === year && resMonth === month) {
                occupiedDays.push(resDay);
            }
        }
        return occupiedDays;
    }

    function createCalendar(month, year) {
        // Borra el contenido anterior del calendario
        calendarContainer.innerHTML = '';

        const date = new Date(year, month - 1, 1);
        const firstDay = date.getDay();
        const daysInMonth = new Date(year, month, 0).getDate();

        // Obtener los días ocupados del almacenamiento local para el mes y año actuales
        const occupiedDays = getOccupiedDays(month - 1, year);

        // Rellenar espacios en blanco si el mes no empieza en domingo
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
    }

    // Generar el calendario para el mes actual
    const today = new Date();
    createCalendar(today.getMonth() + 1, today.getFullYear());
});
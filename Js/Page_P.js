document.addEventListener('DOMContentLoaded', () => {
    const calendarContainer = document.querySelector('.calendar-container');

    // Días de la semana en español
    const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    // Variables para el seguimiento del mes actual
    const today = new Date();
    let currentMonth = today.getMonth() + 1; // 1-12
    let currentYear = today.getFullYear();

    // Función para actualizar el título del calendario
    function updateCalendarTitle(month, year) {
        // Asegúrate de que month sea 1-12
        document.querySelector('.calendar-title').textContent = `${monthNames[month - 1]} ${year}`;
    }
    
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

        // Actualizar el título del calendario
        updateCalendarTitle(month, year);
        
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
});
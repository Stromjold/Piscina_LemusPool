function createCalendar(calendarContainer, month, year, occupiedDays, reservas = [], onReservationClick = null) {
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const weekDays = ['DOM', 'LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB'];

    const calendarTitle = document.querySelector('#currentMonthYear');
    if(calendarTitle) {
        calendarTitle.textContent = `${monthNames[month - 1]} ${year}`;
    }

    const weeksContainer = calendarContainer.querySelector('.weeks');
    weeksContainer.innerHTML = '';

    const date = new Date(year, month - 1, 1);
    const firstDayOfMonth = date.getDay(); // 0=Sunday, 6=Saturday
    const daysInMonth = new Date(year, month, 0).getDate();

    const daysInPrevMonth = new Date(year, month - 1, 0).getDate();

    let days = [];

    // Days from previous month
    for (let i = firstDayOfMonth; i > 0; i--) {
        const day = daysInPrevMonth - i + 1;
        const span = document.createElement('span');
        span.classList.add('last-month');
        span.textContent = day;
        days.push(span);
    }

    // Days of the current month
    for (let day = 1; day <= daysInMonth; day++) {
        const span = document.createElement('span');
        span.textContent = day < 10 ? '0' + day : day;
        if (occupiedDays.includes(day)) {
            span.classList.add('active');
        }
        days.push(span);
    }

    // Days from next month
    const remainingDays = 42 - days.length; // 6 weeks * 7 days
    for (let i = 1; i <= remainingDays; i++) {
        const span = document.createElement('span');
        span.classList.add('last-month');
        span.textContent = i < 10 ? '0' + i : i;
        days.push(span);
    }

    // Group days into weeks
    for (let i = 0; i < days.length; i += 7) {
        const weekDiv = document.createElement('div');
        for (let j = i; j < i + 7; j++) {
            weekDiv.appendChild(days[j]);
        }
        weeksContainer.appendChild(weekDiv);
    }

    // Update current date display
    const currentDate = new Date();
    const currentDay = currentDate.getDate();
    const currentDayOfWeekIndex = currentDate.getDay();
    const currentDayOfWeek = weekDays[currentDayOfWeekIndex];
    const currentMonthName = monthNames[currentDate.getMonth()];
    const currentYear = currentDate.getFullYear();

    const h1s = calendarContainer.querySelectorAll('.current-date h1');
    if(h1s.length === 2) {
        h1s[0].textContent = `${currentDayOfWeek} ${currentDay}`;
        h1s[1].textContent = `${currentMonthName} ${currentYear}`;
    }
}

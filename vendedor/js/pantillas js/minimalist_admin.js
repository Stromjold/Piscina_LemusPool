// Calendar Management
        let currentMonth = 10; // Noviembre
        let currentYear = 2025;
        let occupiedDays = {};

        function generateCalendarAdmin() {
            const calendar = document.getElementById('calendarAdmin');
            calendar.innerHTML = '';
            
            const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            days.forEach(day => {
                const header = document.createElement('div');
                header.className = 'calendar-day-header';
                header.textContent = day;
                calendar.appendChild(header);
            });
            
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            
            for (let i = 0; i < firstDay; i++) {
                const empty = document.createElement('div');
                calendar.appendChild(empty);
            }
            
            for (let day = 1; day <= daysInMonth; day++) {
                const dayEl = document.createElement('div');
                dayEl.className = 'calendar-admin-day';
                const key = `${currentYear}-${currentMonth}-${day}`;
                
                if (occupiedDays[key]) {
                    dayEl.classList.add('occupied');
                }
                
                dayEl.innerHTML = `
                    <span class="day-number">${day}</span>
                    <span class="day-status">${occupiedDays[key] ? 'Ocupado' : 'Libre'}</span>
                `;
                
                dayEl.onclick = () => toggleDay(key, dayEl);
                calendar.appendChild(dayEl);
            }
        }

        function toggleDay(key, element) {
            occupiedDays[key] = !occupiedDays[key];
            if (occupiedDays[key]) {
                element.classList.add('occupied');
                element.querySelector('.day-status').textContent = 'Ocupado';
            } else {
                element.classList.remove('occupied');
                element.querySelector('.day-status').textContent = 'Libre';
            }
        }

        function changeCalendarMonth(delta) {
            currentMonth += delta;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            } else if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            
            const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            document.getElementById('calendarAdminTitle').textContent = `${months[currentMonth]} ${currentYear}`;
            generateCalendarAdmin();
        }

        function saveCalendar() {
            alert('Calendario guardado exitosamente');
        }

        function resetCalendar() {
            if (confirm('¿Estás seguro de restablecer el calendario?')) {
                occupiedDays = {};
                generateCalendarAdmin();
            }
        }

        // Gallery Management
        let galleryImages = [
            'https://www.luxuryconcrete.eu/es/img/inspiracion/piscinas/piscina-microcemento-jardin.webp',
            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800',
            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=800'
        ];

        function loadGallery() {
            const gallery = document.getElementById('galleryAdmin');
            gallery.innerHTML = '';
            
            galleryImages.forEach((url, index) => {
                const item = document.createElement('div');
                item.className = 'gallery-admin-item';
                item.innerHTML = `
                    <img src="${url}" alt="Imagen ${index + 1}">
                    <div class="gallery-item-actions">
                        <button class="icon-btn delete" onclick="removeImage(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                gallery.appendChild(item);
            });
        }

        function addImage() {
            const url = document.getElementById('newImageUrl').value;
            if (url) {
                galleryImages.push(url);
                loadGallery();
                document.getElementById('newImageUrl').value = '';
                alert('Imagen agregada exitosamente');
            }
        }

        function removeImage(index) {
            if (confirm('¿Estás seguro de eliminar esta imagen?')) {
                galleryImages.splice(index, 1);
                loadGallery();
            }
        }

        // Content Management
        function saveContent() {
            alert('Contenido guardado exitosamente');
        }

        // Messages Management
        function viewMessage(id) {
            alert('Ver mensaje #' + id);
        }

        function deleteMessage(id) {
            if (confirm('¿Estás seguro de eliminar este mensaje?')) {
                alert('Mensaje eliminado');
            }
        }

        // Initialize
        generateCalendarAdmin();
        loadGallery();

        // Initialize some random occupied days
        for (let i = 0; i < 10; i++) {
            const day = Math.floor(Math.random() * 30) + 1;
            occupiedDays[`${currentYear}-${currentMonth}-${day}`] = true;
        }
        generateCalendarAdmin();

    //    document.addEventListener('DOMContentLoaded', () => {
    //         const monthNames = ["January", "February", "March", "April", "May", "June",
    //                             "July", "August", "September", "October", "November", "December"];

    //         const monthElement = document.querySelector('.month');
    //         const monthPicker = document.getElementById('month-picker');
    //         const dateContainer = document.querySelector('.date-picker');

    //         const today = new Date();
    //         let currentMonth = today.getMonth();
    //         let currentYear = today.getFullYear();

    //         // Function to initialize the month picker
    //         function initializeMonthPicker() {
    //             const fragment = document.createDocumentFragment();
    //             for (let year = currentYear - 5; year <= currentYear + 5; year++) {
    //                 for (let month = 0; month < 12; month++) {
    //                     const option = document.createElement('option');
    //                     option.value = `${month}-${year}`;
    //                     option.textContent = `${monthNames[month]} ${year}`;
    //                     if (month === currentMonth && year === currentYear) {
    //                         option.selected = true;
    //                     }
    //                     fragment.appendChild(option);
    //                 }
    //             }
    //             monthPicker.appendChild(fragment);
    //         }

    //         // Function to update the month and year text
    //         function updateMonthDisplay() {
    //             monthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    //         }

    //         // Function to generate dates for the selected month
    //         function generateDates() {
    //             const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    //             const firstDay = new Date(currentYear, currentMonth, 1).getDay();

    //             dateContainer.innerHTML = ''; // Clear existing dates

    //             const activeDate = (day) => {
    //                 const urlParams = new URLSearchParams(window.location.search);

    //                 const urlDateDay = urlParams.has('date') && urlParams.get('date').trim() !== '' && urlParams.get('date');
    //                 console.log(urlDateDay);

    //                 if (+urlDateDay === day || (!urlParams.has('date') && day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear())) {
    //                     return 'current-date active';
    //                 }

    //                 return '';
    //             }

    //             for (let day = 1; day <= daysInMonth; day++) {
    //                 const html = `
    //                 <a href="schedule.php?date=${day}&month=${currentMonth+1}&year=${currentYear}" class="date-item ${activeDate(day)}">
    //                     <span class="date">${day}</span>
    //                     <span class="day">${new Date(currentYear, currentMonth, day).toLocaleDateString('en-US', { weekday: 'short' })}</span>
    //                 </a>`;

    //                 dateContainer.insertAdjacentHTML('beforeend', html);
    //             };
    //         };

    //         // Function to handle month and year change

    //         function changeMonthYear() {
    //             const [selectedMonth, selectedYear] = monthPicker.value.split('-').map(Number);
    //             currentMonth = selectedMonth; // Update the current month
    //             currentYear = selectedYear;   // Update the current year
    //             updateMonthDisplay();         // Update the month and year display
    //             generateDates();              // Regenerate the dates for the new month and year
    //         }

    //         // Call initializeMonthPicker when the page loads
    //         initializeMonthPicker();

    //         // Generate the initial dates for the current month and year
    //         generateDates();

    //         // Event listener for month picker change
    //         monthPicker.addEventListener('change', changeMonthYear);
    //     });

    document.addEventListener('DOMContentLoaded', () => {
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
    
        const monthElement = document.querySelector('.month');
        const monthPicker = document.getElementById('month-picker');
        const dateContainer = document.querySelector('.date-picker');
    
        const today = new Date();
        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();
    
        // Function to initialize the month picker
        function initializeMonthPicker() {
            const fragment = document.createDocumentFragment();
            for (let year = currentYear - 5; year <= currentYear + 5; year++) {
                for (let month = 0; month < 12; month++) {
                    const option = document.createElement('option');
                    option.value = `${month}-${year}`;
                    option.textContent = `${monthNames[month]} ${year}`;
                    if (month === currentMonth && year === currentYear) {
                        option.selected = true;
                    }
                    fragment.appendChild(option);
                }
            }
            monthPicker.appendChild(fragment);
        }
    
        // Function to update the month and year text
        function updateMonthDisplay() {
            monthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        }
    
        // Function to generate dates for the selected month
        function generateDates() {
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    
            dateContainer.innerHTML = ''; // Clear existing dates
    
            const activeDate = (day) => {
                const urlParams = new URLSearchParams(window.location.search);
                const urlDateDay = urlParams.has('date') && urlParams.get('date').trim() !== '' && urlParams.get('date');
            
                if (+urlDateDay === day || (!urlParams.has('date') && day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear())) {
                    return 'current-date active';  // Apply both classes to the current date when it's active
                }
                // Apply 'current-date' class only to today
                if (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
                    return 'current-date';  // Highlight today's date
                }
            
                return '';  // Always apply current-date class to today
            };
    
            for (let day = 1; day <= daysInMonth; day++) {
                const html = `
                <a href="schedule.php?date=${day}&month=${currentMonth+1}&year=${currentYear}" class="date-item ${activeDate(day)}">
                    <span class="date">${day}</span>
                    <span class="day">${new Date(currentYear, currentMonth, day).toLocaleDateString('en-US', { weekday: 'short' })}</span>
                </a>`;
                
    
                dateContainer.insertAdjacentHTML('beforeend', html);
            }
    
            // Scroll the active date (current date) into view and center it
            setTimeout(() => {
                const currentActiveElement = document.querySelector('.current-date.active');
                
                if (currentActiveElement) {
                    console.log("Scrolling to current date:", currentActiveElement);

                    const container = document.querySelector('.date-picker'); // The container holding the dates
                    const bounding = currentActiveElement.getBoundingClientRect();
                    const containerBounding = container.getBoundingClientRect();

                    // Calculate the offset needed to center the active date in the container (for horizontal scrolling)
                    const scrollOffset = (bounding.left + bounding.width / 2) - (containerBounding.left + containerBounding.width / 2);

                    // Scroll the container horizontally by the calculated offset
                    container.scrollBy({
                        left: scrollOffset, // Use 'left' for horizontal scrolling
                        behavior: 'smooth'  // Smooth scroll effect
                    });
                } else {
                    console.log("Current date not found.");
                }
            }, 100);  // Delay to ensure DOM updates are complete
        }
    
        // Function to handle month and year change
        function changeMonthYear() {
            const [selectedMonth, selectedYear] = monthPicker.value.split('-').map(Number);
            currentMonth = selectedMonth; // Update the current month
            currentYear = selectedYear;   // Update the current year
            updateMonthDisplay();         // Update the month and year display
            generateDates();              // Regenerate the dates for the new month and year
        }
    
        // Call initializeMonthPicker when the page loads
        initializeMonthPicker();
    
        // Generate the initial dates for the current month and year
        generateDates();
    
        // Event listener for month picker change
        monthPicker.addEventListener('change', changeMonthYear);


        function viewTaskDetails(taskId) {
            const url = "task_details.php?task_id=" + encodeURIComponent(taskId);
            window.location.href = url;
        }
    });
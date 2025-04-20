document.addEventListener('DOMContentLoaded', function() {
    // Update current date
    const taskDate = document.getElementById('date');
    const taskDay = this.documentURI.getElementById('day');
    const today = new Date();

    taskDate.valueAsDate = today;
    taskDay.value = today.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});

    const options = { weekday: 'long', month: 'long', day: 'numeric' };
    document.querySelector('.calendar-widget .day').textContent = today.toLocaleDateString('en-US', { weekday: 'long' }).toUpperCase();
    document.querySelector('.calendar-widget .date').textContent = today.getDate();

    // Add task functionality
    const addTaskButton = document.querySelector('.add-task');
    const todayTasksList = document.querySelector('.today-tasks ul');

    addTaskButton.addEventListener('click', function() {
        const taskName = prompt('Enter new task:');
        if (taskName) {
            const newTask = document.createElement('li');
            newTask.textContent = taskName;
            todayTasksList.appendChild(newTask);
        }
    });

    // Search functionality
    const searchInput = document.querySelector('.search-bar input');
    const searchButton = document.querySelector('.search-bar button');

    searchButton.addEventListener('click', function() {
        const searchTerm = searchInput.value.toLowerCase();
        const allTasks = document.querySelectorAll('.task-list li, .urgent-tasks .task');

        allTasks.forEach(task => {
            const taskText = task.textContent.toLowerCase();
            if (taskText.includes(searchTerm)) {
                task.style.display = 'block';
            } else {
                task.style.display = 'none';
                
            }
        });
    });

});


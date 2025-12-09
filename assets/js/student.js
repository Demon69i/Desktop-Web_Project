function viewCourse(courseId) {
    window.location.href = `/student/course.php?id=${courseId}`;
}

async function enrollCourse(courseId) {
    try {
        const response = await fetch('/api/enrollments.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ course_id: courseId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to enroll in course');
        }
    } catch (error) {
        console.error('Error enrolling in course:', error);
        alert('Failed to enroll in course');
    }
}

async function toggleLessonComplete(element) {
    const lessonId = parseInt(element.dataset.lessonId);
    const courseId = parseInt(element.dataset.courseId);
    const isCompleted = element.dataset.completed === 'true';
    
    try {
        const response = await fetch('/api/enrollments.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                course_id: courseId,
                lesson_id: lessonId,
                action: isCompleted ? 'uncomplete' : 'complete'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            element.dataset.completed = isCompleted ? 'false' : 'true';
            
            const lessonNumber = element.querySelector('.lesson-number');
            const lessonStatus = element.querySelector('.lesson-status');
            const allLessons = document.querySelectorAll('.lesson-list-item');
            const lessonIndex = Array.from(allLessons).indexOf(element) + 1;
            
            if (isCompleted) {
                element.classList.remove('lesson-completed');
                lessonNumber.textContent = lessonIndex;
                if (lessonStatus) {
                    lessonStatus.textContent = 'Mark Complete';
                    lessonStatus.style.color = '#64748b';
                }
            } else {
                element.classList.add('lesson-completed');
                lessonNumber.textContent = '✓';
                if (lessonStatus) {
                    lessonStatus.textContent = 'Completed';
                    lessonStatus.style.color = 'var(--secondary)';
                }
            }
            
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            if (progressBar && progressText) {
                progressBar.style.width = data.progress + '%';
                progressText.textContent = data.progress + '%';
            }
        } else {
            alert(data.message || 'Failed to update lesson');
        }
    } catch (error) {
        console.error('Error updating lesson:', error);
        alert('Failed to update lesson');
    }
}

const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.course-card');
        
        cards.forEach(card => {
            const title = card.querySelector('.course-title').textContent.toLowerCase();
            const description = card.querySelector('.course-description').textContent.toLowerCase();
            const category = card.querySelector('.course-header').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm) || category.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

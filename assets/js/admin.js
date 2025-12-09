let lessonCount = 0;
let currentCourseData = null;

function openAddCourseModal() {
    document.getElementById('modalTitle').textContent = 'Add New Course';
    document.getElementById('courseForm').reset();
    document.getElementById('courseId').value = '';
    document.getElementById('lessonsContainer').innerHTML = '';
    lessonCount = 0;
    currentCourseData = null;
    document.getElementById('courseModal').classList.add('active');
}

async function openEditCourseModal(courseId) {
    try {
        const response = await fetch(`/api/courses.php?id=${courseId}`);
        const data = await response.json();
        
        if (data.success) {
            const course = data.course;
            currentCourseData = course;
            
            document.getElementById('modalTitle').textContent = 'Edit Course';
            document.getElementById('courseId').value = course.id;
            document.getElementById('courseTitle').value = course.title;
            document.getElementById('courseCategory').value = course.category;
            document.getElementById('categoryColor').value = course.category_color;
            document.getElementById('thumbnailUrl').value = course.thumbnail_url || '';
            document.getElementById('shortDescription').value = course.short_description || '';
            document.getElementById('longDescription').value = course.long_description || '';
            document.getElementById('instructorName').value = course.instructor_name || '';
            document.getElementById('difficultyLevel').value = course.difficulty_level || 'Beginner';
            
            document.getElementById('lessonsContainer').innerHTML = '';
            lessonCount = 0;
            
            if (course.lessons && course.lessons.length > 0) {
                course.lessons.forEach(lesson => {
                    addLesson(lesson.title, lesson.content, lesson.duration, lesson.video_url || '');
                });
            }
            
            document.getElementById('courseModal').classList.add('active');
        }
    } catch (error) {
        console.error('Error fetching course:', error);
        alert('Failed to load course data');
    }
}

function closeModal() {
    document.getElementById('courseModal').classList.remove('active');
}

function addLesson(title = '', content = '', duration = '', videoUrl = '') {
    lessonCount++;
    const container = document.getElementById('lessonsContainer');
    const lessonHtml = `
        <div class="lesson-item" id="lesson-${lessonCount}">
            <div class="lesson-row">
                <input type="text" name="lesson_title_${lessonCount}" placeholder="Lesson title" value="${title}" style="flex: 2;">
                <input type="text" name="lesson_duration_${lessonCount}" placeholder="Duration (e.g., 15 mins)" value="${duration}" style="flex: 1;">
                <button type="button" class="remove-lesson-btn" onclick="removeLesson(${lessonCount})">✕</button>
            </div>
            <div class="lesson-row" style="margin-top: 8px;">
                <input type="url" name="lesson_video_${lessonCount}" placeholder="YouTube Video URL (e.g., https://www.youtube.com/watch?v=...)" value="${videoUrl}" style="flex: 1;">
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', lessonHtml);
}

function removeLesson(id) {
    const lesson = document.getElementById(`lesson-${id}`);
    if (lesson) {
        lesson.remove();
    }
}

function getLessonsFromForm() {
    const lessons = [];
    const container = document.getElementById('lessonsContainer');
    const lessonItems = container.querySelectorAll('.lesson-item');
    
    lessonItems.forEach((item, index) => {
        const titleInput = item.querySelector('input[name^="lesson_title"]');
        const durationInput = item.querySelector('input[name^="lesson_duration"]');
        const videoInput = item.querySelector('input[name^="lesson_video"]');
        
        if (titleInput && titleInput.value.trim()) {
            lessons.push({
                id: index + 1,
                title: titleInput.value.trim(),
                content: '',
                duration: durationInput ? durationInput.value.trim() : '',
                video_url: videoInput ? videoInput.value.trim() : ''
            });
        }
    });
    
    return lessons;
}

document.getElementById('courseForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        id: document.getElementById('courseId').value,
        title: document.getElementById('courseTitle').value,
        category: document.getElementById('courseCategory').value,
        category_color: document.getElementById('categoryColor').value,
        thumbnail_url: document.getElementById('thumbnailUrl').value,
        short_description: document.getElementById('shortDescription').value,
        long_description: document.getElementById('longDescription').value,
        instructor_name: document.getElementById('instructorName').value,
        difficulty_level: document.getElementById('difficultyLevel').value,
        lessons: getLessonsFromForm()
    };
    
    try {
        const method = formData.id ? 'PUT' : 'POST';
        const response = await fetch('/api/courses.php', {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeModal();
            location.reload();
        } else {
            alert(data.message || 'Failed to save course');
        }
    } catch (error) {
        console.error('Error saving course:', error);
        alert('Failed to save course');
    }
});

async function deleteCourse(courseId) {
    if (!confirm('Are you sure you want to delete this course?')) {
        return;
    }
    
    try {
        const response = await fetch('/api/courses.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: courseId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const card = document.querySelector(`[data-course-id="${courseId}"]`);
            if (card) {
                card.remove();
            }
        } else {
            alert(data.message || 'Failed to delete course');
        }
    } catch (error) {
        console.error('Error deleting course:', error);
        alert('Failed to delete course');
    }
}

document.getElementById('searchInput').addEventListener('input', function(e) {
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

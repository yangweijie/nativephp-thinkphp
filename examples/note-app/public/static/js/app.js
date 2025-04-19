// 创建笔记
function createNote() {
    window.location.href = '/note/create';
}

// 查看笔记
function viewNote(id) {
    window.location.href = '/note/view?id=' + id;
}

// 编辑笔记
function editNote(id) {
    window.location.href = '/note/edit?id=' + id;
}

// 删除笔记
function deleteNote(id) {
    if (confirm('确定要删除这个笔记吗？此操作不可恢复。')) {
        fetch('/note/delete?id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/note/index';
            } else {
                alert('删除失败: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('删除失败，请重试');
        });
    }
}

// 保存笔记
function saveNote() {
    const id = document.getElementById('noteId').value;
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    const categoryId = document.getElementById('category').value;
    
    // 获取选中的标签
    const tagCheckboxes = document.querySelectorAll('input[name="tags[]"]:checked');
    const tags = Array.from(tagCheckboxes).map(checkbox => checkbox.value);
    
    if (!title) {
        alert('请输入笔记标题');
        return;
    }
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('title', title);
    formData.append('content', content);
    formData.append('category_id', categoryId);
    
    tags.forEach(tag => {
        formData.append('tags[]', tag);
    });
    
    fetch('/note/save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/note/view?id=' + data.id;
        } else {
            alert('保存失败: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('保存失败，请重试');
    });
}

// 搜索笔记
function searchNotes() {
    const keyword = document.getElementById('searchInput').value;
    
    if (keyword.length < 2) {
        return;
    }
    
    window.location.href = '/note/index?search=' + encodeURIComponent(keyword);
}

// 创建分类
function createCategory() {
    const name = prompt('请输入分类名称:');
    
    if (!name) {
        return;
    }
    
    fetch('/note/createCategory', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'name=' + encodeURIComponent(name)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 如果在编辑页面，添加新分类到下拉列表
            const categorySelect = document.getElementById('category');
            if (categorySelect) {
                const option = document.createElement('option');
                option.value = data.id;
                option.textContent = data.name;
                option.selected = true;
                categorySelect.appendChild(option);
            } else {
                // 如果在列表页面，刷新页面
                window.location.reload();
            }
        } else {
            alert('创建分类失败: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('创建分类失败，请重试');
    });
}

// 创建标签
function createTag() {
    const name = prompt('请输入标签名称:');
    
    if (!name) {
        return;
    }
    
    fetch('/note/createTag', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'name=' + encodeURIComponent(name)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 如果在编辑页面，添加新标签到标签列表
            const tagsSelect = document.querySelector('.tags-select');
            if (tagsSelect) {
                const label = document.createElement('label');
                label.className = 'tag-checkbox';
                
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.name = 'tags[]';
                input.value = data.id;
                input.checked = true;
                
                label.appendChild(input);
                label.appendChild(document.createTextNode(data.name));
                
                // 插入到"新建标签"按钮前
                const addTagBtn = document.querySelector('.tags-select .small-btn');
                tagsSelect.insertBefore(label, addTagBtn);
            } else {
                // 如果在列表页面，刷新页面
                window.location.reload();
            }
        } else {
            alert('创建标签失败: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('创建标签失败，请重试');
    });
}

// 导出笔记
function exportNote(id) {
    fetch('/note/export?id=' + id, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('导出失败: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('导出失败，请重试');
    });
}

// 导入笔记
function importNote() {
    fetch('/note/import', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/note/view?id=' + data.id;
        } else {
            alert('导入失败: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('导入失败，请重试');
    });
}

// 备份笔记
function backup() {
    fetch('/note/backup', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('备份失败: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('备份失败，请重试');
    });
}

// 恢复笔记
function restore() {
    if (confirm('恢复将覆盖当前所有笔记数据，确定要继续吗？')) {
        fetch('/note/restore', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('恢复失败: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('恢复失败，请重试');
        });
    }
}

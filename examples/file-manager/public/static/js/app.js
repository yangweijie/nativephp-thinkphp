/**
 * 文件管理器应用 JavaScript
 */

// 页面加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    // 初始化模态框
    initModal();
});

/**
 * 初始化模态框
 */
function initModal() {
    const modal = document.getElementById('modal');
    if (!modal) return;

    const closeBtn = modal.querySelector('.close');
    const cancelBtn = document.getElementById('modalCancel');

    // 关闭模态框
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // 点击模态框外部关闭
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

/**
 * 显示模态框
 * @param {string} title 标题
 * @param {string} content 内容
 * @param {Function} onConfirm 确认回调
 */
function showModal(title, content, onConfirm) {
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const modalConfirm = document.getElementById('modalConfirm');

    modalTitle.textContent = title;
    modalBody.innerHTML = content;

    // 设置确认按钮回调
    modalConfirm.onclick = function() {
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
        modal.style.display = 'none';
    };

    modal.style.display = 'block';
}

/**
 * 跳转到上级目录
 */
function goToParent() {
    const pathInput = document.getElementById('pathInput');
    const path = pathInput.value;
    const parentPath = path.substring(0, path.lastIndexOf('/'));

    window.location.href = '/file/index?path=' + encodeURIComponent(parentPath);
}

/**
 * 刷新当前目录
 */
function refreshDirectory() {
    const pathInput = document.getElementById('pathInput');
    const path = pathInput.value;

    window.location.href = '/file/index?path=' + encodeURIComponent(path);
}

/**
 * 选择文件夹
 */
function selectFolder() {
    window.location.href = '/file/selectFolder';
}

/**
 * 创建文件
 */
function createFile() {
    const pathInput = document.getElementById('pathInput');
    const path = pathInput.value;

    showModal('新建文件', `
        <div class="form-group">
            <label for="fileName">文件名:</label>
            <input type="text" id="fileName" class="form-control" placeholder="请输入文件名">
        </div>
    `, function() {
        const fileName = document.getElementById('fileName').value;

        if (!fileName) {
            showNotification('错误', '文件名不能为空');
            return;
        }

        fetch('/file/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent(fileName) + '&type=file',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshDirectory();
            } else {
                showNotification('错误', data.message || '创建文件失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    });
}

/**
 * 创建目录
 */
function createDirectory() {
    const pathInput = document.getElementById('pathInput');
    const path = pathInput.value;

    showModal('新建文件夹', `
        <div class="form-group">
            <label for="folderName">文件夹名:</label>
            <input type="text" id="folderName" class="form-control" placeholder="请输入文件夹名">
        </div>
    `, function() {
        const folderName = document.getElementById('folderName').value;

        if (!folderName) {
            showNotification('错误', '文件夹名不能为空');
            return;
        }

        fetch('/file/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent(folderName) + '&type=directory',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshDirectory();
            } else {
                showNotification('错误', data.message || '创建文件夹失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    });
}

/**
 * 查看文件
 * @param {string} path 文件路径
 */
function viewFile(path) {
    window.location.href = '/file/view?path=' + encodeURIComponent(path);
}

/**
 * 编辑文件
 * @param {string} path 文件路径
 */
function editFile(path) {
    window.location.href = '/file/edit?path=' + encodeURIComponent(path);
}

/**
 * 打开文件或目录
 * @param {string} path 路径
 * @param {boolean} isDir 是否是目录
 */
function openItem(path, isDir) {
    if (isDir) {
        window.location.href = '/file/index?path=' + encodeURIComponent(path);
    } else {
        fetch('/file/open', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path),
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showNotification('错误', data.message || '打开文件失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    }
}

/**
 * 在文件夹中显示
 * @param {string} path 路径
 */
function showInFolder(path) {
    fetch('/file/showInFolder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            showNotification('错误', data.message || '在文件夹中显示失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 重命名文件或目录
 * @param {string} path 路径
 * @param {string} oldName 原名称
 */
function renameItem(path, oldName) {
    showModal('重命名', `
        <div class="form-group">
            <label for="newName">新名称:</label>
            <input type="text" id="newName" class="form-control" value="${oldName}" placeholder="请输入新名称">
        </div>
    `, function() {
        const newName = document.getElementById('newName').value;

        if (!newName) {
            showNotification('错误', '名称不能为空');
            return;
        }

        fetch('/file/rename', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path) + '&newName=' + encodeURIComponent(newName),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshDirectory();
            } else {
                showNotification('错误', data.message || '重命名失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    });
}

/**
 * 删除文件或目录
 * @param {string} path 路径
 * @param {string} name 名称
 */
function deleteItem(path, name) {
    showModal('删除确认', `
        <p>确定要删除 "${name}" 吗？此操作不可恢复。</p>
    `, function() {
        fetch('/file/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshDirectory();
            } else {
                showNotification('错误', data.message || '删除失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    });
}

/**
 * 显示文件或目录属性
 * @param {string} path 路径
 */
function showProperties(path) {
    fetch('/file/properties', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const properties = data.properties;
            let content = `
                <table class="properties-table">
                    <tr>
                        <th>名称:</th>
                        <td>${properties.name}</td>
                    </tr>
                    <tr>
                        <th>路径:</th>
                        <td>${properties.path}</td>
                    </tr>
                    <tr>
                        <th>类型:</th>
                        <td>${properties.isDir ? '文件夹' : (properties.type || '文件')}</td>
                    </tr>
                    <tr>
                        <th>大小:</th>
                        <td>${properties.formattedSize}</td>
                    </tr>
                    <tr>
                        <th>创建时间:</th>
                        <td>${properties.formattedCreated}</td>
                    </tr>
                    <tr>
                        <th>修改时间:</th>
                        <td>${properties.formattedLastModified}</td>
                    </tr>
                    <tr>
                        <th>访问时间:</th>
                        <td>${properties.formattedAccessed}</td>
                    </tr>
                    <tr>
                        <th>权限:</th>
                        <td>${properties.permissions}</td>
                    </tr>
                    <tr>
                        <th>所有者:</th>
                        <td>${properties.owner}</td>
                    </tr>
                    <tr>
                        <th>组:</th>
                        <td>${properties.group}</td>
                    </tr>
            `;

            if (properties.isDir) {
                content += `
                    <tr>
                        <th>项目数:</th>
                        <td>${properties.itemCount}</td>
                    </tr>
                `;
            } else {
                content += `
                    <tr>
                        <th>扩展名:</th>
                        <td>${properties.extension || '无'}</td>
                    </tr>
                    <tr>
                        <th>MIME类型:</th>
                        <td>${properties.mimeType || '未知'}</td>
                    </tr>
                `;
            }

            content += `</table>`;

            showModal(`${properties.name} 的属性`, content);
        } else {
            showNotification('错误', data.message || '获取属性失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 打开文件
 * @param {string} path 文件路径
 */
function openFile(path) {
    fetch('/file/open', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            showNotification('错误', data.message || '打开文件失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 显示通知
 * @param {string} title 标题
 * @param {string} message 消息
 */
function showNotification(title, message) {
    // 创建通知元素
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.innerHTML = `
        <div class="notification-title">${title}</div>
        <div class="notification-message">${message}</div>
    `;
    document.body.appendChild(notification);

    // 3秒后自动关闭
    setTimeout(() => {
        notification.classList.add('notification-hide');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

/**
 * 压缩文件
 */
function compressFiles() {
    const pathInput = document.getElementById('pathInput');
    const path = pathInput.value;

    // 获取选中的文件和目录
    const selectedItems = getSelectedItems();

    showModal('压缩文件', `
        <div class="form-group">
            <label for="zipName">压缩文件名:</label>
            <input type="text" id="zipName" class="form-control" value="archive.zip" placeholder="输入压缩文件名">
        </div>
        <div class="form-group">
            <label for="zipPath">保存位置:</label>
            <input type="text" id="zipPath" class="form-control" value="${path}" readonly>
        </div>
        <p>选中的项目: ${selectedItems.length > 0 ? selectedItems.length : '无（将压缩当前目录）'}</p>
    `, function() {
        const zipName = document.getElementById('zipName').value;
        const zipPath = document.getElementById('zipPath').value;

        if (!zipName) {
            showNotification('错误', '压缩文件名不能为空');
            return;
        }

        const destination = zipPath + '/' + zipName;

        fetch('/file/compress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path) +
                  '&destination=' + encodeURIComponent(destination) +
                  '&items=' + encodeURIComponent(JSON.stringify(selectedItems)),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshDirectory();
            } else {
                showNotification('错误', data.message || '压缩失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    });
}

/**
 * 解压缩文件
 */
function extractFile() {
    const pathInput = document.getElementById('pathInput');
    const path = pathInput.value;

    // 获取选中的文件
    const selectedItems = getSelectedItems();

    if (selectedItems.length !== 1) {
        showNotification('错误', '请选择一个压缩文件');
        return;
    }

    const zipFile = path + '/' + selectedItems[0];
    const extension = zipFile.split('.').pop().toLowerCase();

    if (extension !== 'zip') {
        showNotification('错误', '当前只支持解压缩 ZIP 格式文件');
        return;
    }

    showModal('解压缩文件', `
        <div class="form-group">
            <label for="extractPath">解压缩到:</label>
            <input type="text" id="extractPath" class="form-control" value="${path}" readonly>
        </div>
        <div class="form-group">
            <label for="extractFolder">文件夹名:</label>
            <input type="text" id="extractFolder" class="form-control" value="${selectedItems[0].replace(/\.zip$/i, '')}" placeholder="输入解压缩目录名">
        </div>
    `, function() {
        const extractPath = document.getElementById('extractPath').value;
        const extractFolder = document.getElementById('extractFolder').value;

        if (!extractFolder) {
            showNotification('错误', '解压缩目录名不能为空');
            return;
        }

        const destination = extractPath + '/' + extractFolder;

        fetch('/file/extract', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'source=' + encodeURIComponent(zipFile) +
                  '&destination=' + encodeURIComponent(destination),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshDirectory();
            } else {
                showNotification('错误', data.message || '解压缩失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    });
}

/**
 * 获取选中的文件和目录
 * @returns {Array} 选中的文件和目录名称数组
 */
function getSelectedItems() {
    const selectedItems = [];
    const checkboxes = document.querySelectorAll('.file-checkbox:checked');

    checkboxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const nameElement = row.querySelector('.name');
        if (nameElement) {
            selectedItems.push(nameElement.textContent);
        }
    });

    return selectedItems;
}

/**
 * 切换全选状态
 */
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.file-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
        updateRowSelection(checkbox);
    });

    updateBatchActions();
}

/**
 * 更新行选中状态
 * @param {HTMLInputElement} checkbox 复选框元素
 */
function updateRowSelection(checkbox) {
    const row = checkbox.closest('tr');
    if (checkbox.checked) {
        row.classList.add('selected');
    } else {
        row.classList.remove('selected');
    }
}

/**
 * 更新批量操作区域显示
 */
function updateBatchActions() {
    const selectedItems = getSelectedItems();
    const batchActions = document.getElementById('batchActions');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedItems.length > 0) {
        batchActions.style.display = 'flex';
        selectedCount.textContent = selectedItems.length;
    } else {
        batchActions.style.display = 'none';
    }
}

/**
 * 取消选择
 */
function cancelSelection() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.file-checkbox');

    selectAllCheckbox.checked = false;
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        updateRowSelection(checkbox);
    });

    updateBatchActions();
}

/**
 * 批量复制
 */
function batchCopy() {
    const selectedItems = getSelectedItems();
    if (selectedItems.length === 0) {
        showNotification('错误', '请选择要复制的文件或目录');
        return;
    }

    const pathInput = document.getElementById('pathInput');
    const currentPath = pathInput.value;

    showModal('批量复制', `
        <p>将选中的 ${selectedItems.length} 项复制到：</p>
        <div class="form-group">
            <label for="destinationPath">目标路径：</label>
            <input type="text" id="destinationPath" class="form-control" value="${currentPath}" placeholder="输入目标路径">
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" id="createSubfolder" checked>
                创建子文件夹（防止文件名冲突）
            </label>
        </div>
    `, function() {
        const destinationPath = document.getElementById('destinationPath').value;
        const createSubfolder = document.getElementById('createSubfolder').checked;

        if (!destinationPath) {
            showNotification('错误', '目标路径不能为空');
            return;
        }

        // 创建子文件夹
        let targetPath = destinationPath;
        if (createSubfolder) {
            const timestamp = new Date().getTime();
            targetPath = destinationPath + '/复制_' + timestamp;
        }

        // 批量复制文件
        batchProcessFiles(selectedItems, currentPath, targetPath, 'copy');
    });
}

/**
 * 批量移动
 */
function batchMove() {
    const selectedItems = getSelectedItems();
    if (selectedItems.length === 0) {
        showNotification('错误', '请选择要移动的文件或目录');
        return;
    }

    const pathInput = document.getElementById('pathInput');
    const currentPath = pathInput.value;

    showModal('批量移动', `
        <p>将选中的 ${selectedItems.length} 项移动到：</p>
        <div class="form-group">
            <label for="destinationPath">目标路径：</label>
            <input type="text" id="destinationPath" class="form-control" value="${currentPath}" placeholder="输入目标路径">
        </div>
    `, function() {
        const destinationPath = document.getElementById('destinationPath').value;

        if (!destinationPath) {
            showNotification('错误', '目标路径不能为空');
            return;
        }

        // 批量移动文件
        batchProcessFiles(selectedItems, currentPath, destinationPath, 'move');
    });
}

/**
 * 批量删除
 */
function batchDelete() {
    const selectedItems = getSelectedItems();
    if (selectedItems.length === 0) {
        showNotification('错误', '请选择要删除的文件或目录');
        return;
    }

    showModal('批量删除', `
        <p>确定要删除选中的 ${selectedItems.length} 项吗？</p>
        <p class="warning">此操作不可恢复！</p>
    `, function() {
        const pathInput = document.getElementById('pathInput');
        const currentPath = pathInput.value;

        // 批量删除文件
        batchProcessFiles(selectedItems, currentPath, '', 'delete');
    });
}

/**
 * 批量处理文件
 * @param {Array} items 选中的文件项
 * @param {string} sourcePath 源路径
 * @param {string} targetPath 目标路径
 * @param {string} action 操作类型：'copy', 'move', 'delete'
 */
function batchProcessFiles(items, sourcePath, targetPath, action) {
    if (items.length === 0) {
        return;
    }

    // 显示进度提示
    const actionText = action === 'copy' ? '复制' : (action === 'move' ? '移动' : '删除');
    showNotification('处理中', `正在${actionText}${items.length}个文件...`);

    // 创建请求数组
    const requests = [];

    items.forEach(item => {
        const sourceFull = sourcePath + '/' + item;
        let targetFull = '';

        if (action === 'copy' || action === 'move') {
            targetFull = targetPath + '/' + item;
        }

        let endpoint = '';
        let params = {};

        switch (action) {
            case 'copy':
                endpoint = '/file/copy';
                params = {
                    source: sourceFull,
                    destination: targetFull
                };
                break;

            case 'move':
                endpoint = '/file/move';
                params = {
                    source: sourceFull,
                    destination: targetFull
                };
                break;

            case 'delete':
                endpoint = '/file/delete';
                params = {
                    path: sourceFull
                };
                break;
        }

        // 构建请求
        const request = fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: Object.keys(params).map(key => {
                return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
            }).join('&')
        }).then(response => response.json());

        requests.push(request);
    });

    // 处理所有请求
    Promise.all(requests)
        .then(results => {
            const successCount = results.filter(result => result.success).length;
            const failCount = results.length - successCount;

            if (failCount === 0) {
                showNotification('成功', `所有文件已成功${actionText}`);
            } else {
                showNotification('部分失败', `${successCount}个文件${actionText}成功，${failCount}个失败`);
            }

            // 刷新目录
            refreshDirectory();

            // 取消选择
            cancelSelection();
        })
        .catch(error => {
            showNotification('错误', error.message || `批量${actionText}失败`);
        });
}

/**
 * 更新过滤器值选项
 */
function updateFilterValues() {
    const filterType = document.getElementById('filter_type').value;

    // 隐藏所有过滤器值组
    document.querySelectorAll('.filter-value-group').forEach(group => {
        group.style.display = 'none';
    });

    // 显示选中的过滤器值组
    if (filterType) {
        const selectedGroup = document.getElementById('filter_' + filterType);
        if (selectedGroup) {
            selectedGroup.style.display = 'block';
        }
    }
}

/**
 * 处理拖放进入事件
 * @param {DragEvent} event 拖放事件
 */
function handleDragEnter(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById('dropZone').classList.add('drag-over');
}

/**
 * 处理拖放离开事件
 * @param {DragEvent} event 拖放事件
 */
function handleDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();

    // 只有当离开拖放区域时才移除类
    const rect = document.getElementById('dropZone').getBoundingClientRect();
    const x = event.clientX;
    const y = event.clientY;

    if (x < rect.left || x >= rect.right || y < rect.top || y >= rect.bottom) {
        document.getElementById('dropZone').classList.remove('drag-over');
    }
}

/**
 * 处理拖放悬停事件
 * @param {DragEvent} event 拖放事件
 */
function handleDragOver(event) {
    event.preventDefault();
    event.stopPropagation();
    event.dataTransfer.dropEffect = 'copy';
}

/**
 * 处理拖放放下事件
 * @param {DragEvent} event 拖放事件
 */
function handleDrop(event) {
    event.preventDefault();
    event.stopPropagation();

    document.getElementById('dropZone').classList.remove('drag-over');

    const pathInput = document.getElementById('pathInput');
    const path = pathInput.value;
    const files = event.dataTransfer.files;

    if (files.length === 0) {
        return;
    }

    // 如果是文件上传
    if (files.length > 0) {
        uploadFiles(path, files);
    }
}

/**
 * 上传文件
 * @param {string} path 目标路径
 * @param {FileList} files 文件列表
 */
function uploadFiles(path, files) {
    if (files.length === 0) {
        return;
    }

    // 如果文件数量少，使用拖放上传方式（读取文件内容）
    if (files.length <= 5) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const content = e.target.result;

                fetch('/file/uploadContent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'path=' + encodeURIComponent(path) +
                          '&filename=' + encodeURIComponent(file.name) +
                          '&content=' + encodeURIComponent(content),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        refreshDirectory();
                    } else {
                        showNotification('错误', data.message || '上传失败');
                    }
                })
                .catch(error => {
                    showNotification('错误', error.message);
                });
            };

            reader.readAsDataURL(file);
        }
    } else {
        // 如果文件数量多，使用表单上传方式
        const formData = new FormData();
        formData.append('path', path);

        for (let i = 0; i < files.length; i++) {
            formData.append('file[]', files[i]);
        }

        fetch('/file/upload', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshDirectory();
                showNotification('上传成功', data.message || '文件上传成功');
            } else {
                showNotification('错误', data.message || '上传失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    }
}

/**
 * 处理文件项拖动开始
 * @param {DragEvent} event 拖放事件
 */
function handleItemDragStart(event) {
    const row = event.currentTarget;
    const path = row.dataset.path;
    const name = row.dataset.name;
    const isDir = row.dataset.isDir === 'true';

    // 设置拖动数据
    event.dataTransfer.setData('application/json', JSON.stringify({
        path: path,
        name: name,
        isDir: isDir,
        action: 'move'
    }));

    // 设置拖动效果
    event.dataTransfer.effectAllowed = 'move';

    // 添加拖动样式
    row.classList.add('dragging');
}

/**
 * 处理文件项拖动结束
 * @param {DragEvent} event 拖放事件
 */
function handleItemDragEnd(event) {
    // 移除拖动样式
    event.currentTarget.classList.remove('dragging');
}

/**
 * 处理拖放放下事件（重写）
 * @param {DragEvent} event 拖放事件
 */
function handleDrop(event) {
    event.preventDefault();
    event.stopPropagation();

    document.getElementById('dropZone').classList.remove('drag-over');

    const pathInput = document.getElementById('pathInput');
    const currentPath = pathInput.value;

    // 如果是文件上传（从外部拖入文件）
    if (event.dataTransfer.files.length > 0) {
        uploadFiles(currentPath, event.dataTransfer.files);
        return;
    }

    // 如果是内部文件移动
    try {
        const data = JSON.parse(event.dataTransfer.getData('application/json'));

        if (data && data.path && data.action === 'move') {
            // 确保不是移动到自身所在目录
            if (dirname(data.path) === currentPath) {
                return;
            }

            // 确保不是将目录移动到其子目录
            if (data.isDir && currentPath.startsWith(data.path)) {
                showNotification('错误', '不能将目录移动到其子目录中');
                return;
            }

            // 构建目标路径
            const destination = currentPath + '/' + data.name;

            // 移动文件或目录
            moveItem(data.path, destination);
        }
    } catch (e) {
        console.error('Drop error:', e);
    }
}

/**
 * 移动文件或目录
 * @param {string} source 源路径
 * @param {string} destination 目标路径
 */
function moveItem(source, destination) {
    fetch('/file/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'source=' + encodeURIComponent(source) + '&destination=' + encodeURIComponent(destination),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            refreshDirectory();
            showNotification('移动成功', '文件或目录已成功移动');
        } else {
            showNotification('错误', data.message || '移动失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 获取路径的目录部分
 * @param {string} path 路径
 * @returns {string} 目录路径
 */
function dirname(path) {
    return path.replace(/\\/g, '/').replace(/\/[^\/]*$/, '');
}

/**
 * 切换收藏状态
 */
function toggleFavorite() {
    const pathInput = document.getElementById('pathInput');
    const path = pathInput.value;
    const favoriteButton = document.getElementById('favoriteButton');

    // 检查当前路径是否已收藏
    fetch('/favorite/check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.isFavorite) {
                // 如果已收藏，则删除收藏
                removeFavorite(path);
            } else {
                // 如果未收藏，则添加收藏
                addFavorite(path);
            }
        } else {
            showNotification('错误', data.message || '检查收藏状态失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 添加收藏
 * @param {string} path 路径
 */
function addFavorite(path) {
    showModal('添加到收藏夹', `
        <div class="form-group">
            <label for="favoriteName">名称:</label>
            <input type="text" id="favoriteName" class="form-control" value="${basename(path)}" placeholder="请输入收藏名称">
        </div>
    `, function() {
        const name = document.getElementById('favoriteName').value;

        if (!name) {
            showNotification('错误', '名称不能为空');
            return;
        }

        fetch('/favorite/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent(name),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('添加成功', '已添加到收藏夹');
                updateFavoriteButton(true);
            } else {
                showNotification('错误', data.message || '添加收藏失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    });
}

/**
 * 删除收藏
 * @param {string} path 路径
 */
function removeFavorite(path) {
    showModal('删除收藏', `
        <p>确定要从收藏夹中删除此项吗？</p>
        <p>这不会删除实际的文件或目录。</p>
    `, function() {
        fetch('/favorite/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('删除成功', '已从收藏夹中删除');
                updateFavoriteButton(false);
            } else {
                showNotification('错误', data.message || '删除收藏失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    });
}

/**
 * 更新收藏按钮状态
 * @param {boolean} isFavorite 是否已收藏
 */
function updateFavoriteButton(isFavorite) {
    const favoriteButton = document.getElementById('favoriteButton');
    if (isFavorite) {
        favoriteButton.textContent = '从收藏夹中删除';
        favoriteButton.classList.add('favorited');
    } else {
        favoriteButton.textContent = '添加到收藏夹';
        favoriteButton.classList.remove('favorited');
    }
}

/**
 * 获取路径的文件名部分
 * @param {string} path 路径
 * @returns {string} 文件名
 */
function basename(path) {
    return path.split(/[\/\\]/).pop();
}

/**
 * 管理文件标签
 * @param {string} path 文件路径
 * @param {string} filename 文件名
 */
function manageFileTags(path, filename) {
    // 创建标签弹出窗口
    const popup = document.createElement('div');
    popup.className = 'tag-list-popup';
    popup.style.position = 'fixed';
    popup.style.top = '50%';
    popup.style.left = '50%';
    popup.style.transform = 'translate(-50%, -50%)';
    popup.innerHTML = `
        <h3>管理标签 - ${filename}</h3>
        <div id="tagList" class="tag-list">
            <div class="loading">加载中...</div>
        </div>
        <div class="tag-list-actions">
            <button onclick="closeTagPopup()" class="btn btn-secondary">关闭</button>
            <a href="/tag/index" class="btn">管理标签</a>
        </div>
    `;

    // 添加到文档中
    document.body.appendChild(popup);

    // 添加遮罩
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.right = '0';
    overlay.style.bottom = '0';
    overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    overlay.style.zIndex = '999';
    overlay.onclick = closeTagPopup;
    document.body.appendChild(overlay);

    // 设置弹出窗口的 z-index
    popup.style.zIndex = '1000';

    // 加载标签
    loadTags(path);

    // 全局关闭函数
    window.closeTagPopup = function() {
        document.body.removeChild(popup);
        document.body.removeChild(overlay);
    };
}

/**
 * 加载标签
 * @param {string} path 文件路径
 */
function loadTags(path) {
    // 获取所有标签
    fetch('/tag/getFileTags', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 获取所有可用标签
            return fetch('/tag/index').then(response => response.text());
        } else {
            throw new Error(data.message || '加载标签失败');
        }
    })
    .then(html => {
        // 从 HTML 中提取标签数据
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const tagItems = doc.querySelectorAll('.tag-item');

        // 获取文件当前标签
        return fetch('/tag/getFileTags', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path),
        })
        .then(response => response.json())
        .then(fileTagsData => {
            if (fileTagsData.success) {
                return { allTags: tagItems, fileTags: fileTagsData.tags };
            } else {
                throw new Error(fileTagsData.message || '加载文件标签失败');
            }
        });
    })
    .then(({ allTags, fileTags }) => {
        const tagList = document.getElementById('tagList');
        tagList.innerHTML = '';

        // 没有标签时显示提示
        if (allTags.length === 0) {
            tagList.innerHTML = '<div class="empty-tags">没有可用的标签，请先创建标签</div>';
            return;
        }

        // 文件当前标签 ID 数组
        const fileTagIds = fileTags.map(tag => tag.id);

        // 添加标签项
        allTags.forEach(tagItem => {
            const tagId = parseInt(tagItem.dataset.id);
            const tagName = tagItem.querySelector('.tag-name').textContent;
            const tagColor = tagItem.querySelector('.tag-color').style.backgroundColor;

            const isSelected = fileTagIds.includes(tagId);

            const tagListItem = document.createElement('div');
            tagListItem.className = 'tag-list-item' + (isSelected ? ' selected' : '');
            tagListItem.dataset.id = tagId;
            tagListItem.innerHTML = `
                <div class="tag-list-color" style="background-color: ${tagColor};"></div>
                <div class="tag-list-name">${tagName}</div>
            `;

            // 点击切换标签
            tagListItem.onclick = function() {
                toggleFileTag(path, tagId, this);
            };

            tagList.appendChild(tagListItem);
        });

        // 更新标签按钮状态
        updateTagButton(path, fileTags.length > 0);
    })
    .catch(error => {
        const tagList = document.getElementById('tagList');
        tagList.innerHTML = `<div class="error">加载失败: ${error.message}</div>`;
    });
}

/**
 * 切换文件标签
 * @param {string} path 文件路径
 * @param {number} tagId 标签 ID
 * @param {HTMLElement} element 标签元素
 */
function toggleFileTag(path, tagId, element) {
    const isSelected = element.classList.contains('selected');

    if (isSelected) {
        // 移除标签
        fetch('/tag/removeFileTag', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path) + '&tagId=' + tagId,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.classList.remove('selected');

                // 检查是否还有选中的标签
                const selectedTags = document.querySelectorAll('.tag-list-item.selected');
                updateTagButton(path, selectedTags.length > 0);
            } else {
                showNotification('错误', data.message || '移除标签失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    } else {
        // 添加标签
        fetch('/tag/addFileTag', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'path=' + encodeURIComponent(path) + '&tagId=' + tagId,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.classList.add('selected');
                updateTagButton(path, true);
            } else {
                showNotification('错误', data.message || '添加标签失败');
            }
        })
        .catch(error => {
            showNotification('错误', error.message);
        });
    }
}

/**
 * 更新标签按钮状态
 * @param {string} path 文件路径
 * @param {boolean} hasTags 是否有标签
 */
function updateTagButton(path, hasTags) {
    const buttons = document.querySelectorAll('.tag-button[data-path="' + path + '"]');

    buttons.forEach(button => {
        if (hasTags) {
            button.classList.add('has-tags');
        } else {
            button.classList.remove('has-tags');
        }
    });
}

/**
 * 比较文件
 * @param {string} path 文件路径
 */
function compareFile(path) {
    // 检查文件是否可比较
    fetch('/compare/check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.isComparable) {
            // 选择要比较的第二个文件
            showModal('选择要比较的文件', `
                <p>选择要与 <strong>${basename(path)}</strong> 进行比较的文件：</p>
                <div class="form-group">
                    <label for="compareFile">第二个文件：</label>
                    <input type="text" id="compareFile" class="form-control" placeholder="请选择或输入文件路径">
                    <button type="button" onclick="selectCompareFile()" class="btn">浏览...</button>
                </div>
            `, function() {
                const compareFile = document.getElementById('compareFile').value;

                if (!compareFile) {
                    showNotification('错误', '请选择要比较的文件');
                    return;
                }

                if (path === compareFile) {
                    showNotification('错误', '不能比较相同的文件');
                    return;
                }

                // 跳转到比较页面
                window.location.href = '/compare/compare?file1=' + encodeURIComponent(path) + '&file2=' + encodeURIComponent(compareFile);
            });

            // 选择比较文件
            window.selectCompareFile = function() {
                fetch('/file/selectFile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.path) {
                        document.getElementById('compareFile').value = data.path;
                    }
                })
                .catch(error => {
                    showNotification('错误', error.message);
                });
            };
        } else {
            showNotification('错误', data.message || '此文件不可比较');
        }
    });
}

/**
 * 切换监视路径
 * @param {string} path 路径
 * @param {boolean} isDir 是否是目录
 */
function toggleWatchPath(path, isDir) {
    // 检查路径是否已被监视
    fetch('/watch/check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.isWatched) {
                // 如果已监视，则移除监视
                showModal('移除监视', `
                    <p>确定要停止监视 <strong>${basename(path)}</strong> 吗？</p>
                `, function() {
                    removeWatchPath(path);
                });
            } else {
                // 如果未监视，则添加监视
                showModal('添加监视', `
                    <p>开始监视 <strong>${basename(path)}</strong></p>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="watchRecursive" ${isDir ? '' : 'disabled'}>
                            递归监视子目录 ${isDir ? '' : '(仅适用于目录)'}
                        </label>
                    </div>
                `, function() {
                    const recursive = document.getElementById('watchRecursive').checked;
                    addWatchPath(path, recursive);
                });
            }
        } else {
            showNotification('错误', data.message || '检查监视状态失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 添加监视路径
 * @param {string} path 路径
 * @param {boolean} recursive 是否递归监视
 */
function addWatchPath(path, recursive) {
    fetch('/watch/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path) + '&recursive=' + (recursive ? '1' : '0'),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('成功', '已添加监视路径');
            updateWatchButton(path, true);
        } else {
            showNotification('错误', data.message || '添加监视路径失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 移除监视路径
 * @param {string} path 路径
 */
function removeWatchPath(path) {
    fetch('/watch/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('成功', '已移除监视路径');
            updateWatchButton(path, false);
        } else {
            showNotification('错误', data.message || '移除监视路径失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 更新监视按钮状态
 * @param {string} path 路径
 * @param {boolean} isWatched 是否被监视
 */
function updateWatchButton(path, isWatched) {
    const buttons = document.querySelectorAll('.watch-button[data-path="' + path + '"]');

    buttons.forEach(button => {
        if (isWatched) {
            button.textContent = '停止监视';
            button.classList.add('watched');
        } else {
            button.textContent = '监视';
            button.classList.remove('watched');
        }
    });
}

/**
 * 加密文件
 * @param {string} path 文件路径
 */
function encryptFile(path) {
    fetch('/crypto/encrypt', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'source=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.html) {
            // 显示加密选项对话框
            showModal('加密文件', data.html, function() {
                const password = document.getElementById('encryptionPassword').value;
                const algorithm = document.getElementById('encryptionAlgorithm').value;
                const saveAs = document.getElementById('saveAsOption').checked;
                const source = document.getElementById('sourceFile').value;

                if (!password) {
                    showNotification('错误', '请输入密码');
                    return false;
                }

                // 执行加密
                fetch('/crypto/doEncrypt', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'source=' + encodeURIComponent(source) +
                          '&password=' + encodeURIComponent(password) +
                          '&algorithm=' + encodeURIComponent(algorithm) +
                          '&save_as=' + (saveAs ? '1' : '0'),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('成功', '文件已成功加密');
                        refreshDirectory();
                    } else {
                        showNotification('错误', data.message || '加密失败');
                    }
                })
                .catch(error => {
                    showNotification('错误', error.message);
                });
            });
        } else {
            showNotification('错误', data.message || '获取加密选项失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 解密文件
 * @param {string} path 文件路径
 */
function decryptFile(path) {
    fetch('/crypto/decrypt', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'source=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.html) {
            // 显示解密选项对话框
            showModal('解密文件', data.html, function() {
                const password = document.getElementById('decryptionPassword').value;
                const saveAs = document.getElementById('saveAsOption').checked;
                const source = document.getElementById('sourceFile').value;

                if (!password) {
                    showNotification('错误', '请输入密码');
                    return false;
                }

                // 执行解密
                fetch('/crypto/doDecrypt', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'source=' + encodeURIComponent(source) +
                          '&password=' + encodeURIComponent(password) +
                          '&save_as=' + (saveAs ? '1' : '0'),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('成功', '文件已成功解密');
                        refreshDirectory();
                    } else {
                        showNotification('错误', data.message || '解密失败');
                    }
                })
                .catch(error => {
                    showNotification('错误', error.message);
                });
            });
        } else {
            showNotification('错误', data.message || '获取解密选项失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 压缩文件或目录
 */
function compressFiles() {
    // 获取选中的文件和目录
    const selectedItems = getSelectedItems();

    if (selectedItems.length === 0) {
        // 如果没有选中项，则使用当前目录
        const pathInput = document.getElementById('pathInput');
        if (pathInput) {
            const currentPath = pathInput.value;
            compressItem(currentPath);
        } else {
            showNotification('错误', '请选择要压缩的文件或目录');
        }
    } else if (selectedItems.length === 1) {
        // 如果只选中一项，直接压缩
        compressItem(selectedItems[0].path);
    } else {
        // 如果选中多项，先创建临时目录，然后压缩
        showNotification('提示', '暂不支持多文件压缩，请选择单个文件或目录');
    }
}

/**
 * 压缩单个文件或目录
 * @param {string} path 文件或目录路径
 */
function compressItem(path) {
    // 获取支持的压缩格式
    fetch('/archive/formats', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const formats = data.formats;
            let formatOptions = '';

            // 生成格式选项
            for (const [key, format] of Object.entries(formats)) {
                formatOptions += `<option value="${key}">${format.description}</option>`;
            }

            // 显示压缩选项对话框
            showModal('压缩选项', `
                <p>选择压缩格式和选项：</p>
                <div class="form-group">
                    <label for="compressFormat">压缩格式：</label>
                    <select id="compressFormat" class="form-control">
                        ${formatOptions}
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="includeDir" checked>
                        包含目录本身
                    </label>
                </div>
            `, function() {
                const format = document.getElementById('compressFormat').value;
                const includeDir = document.getElementById('includeDir').checked;

                // 执行压缩
                fetch('/archive/compress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'source=' + encodeURIComponent(path) +
                          '&format=' + encodeURIComponent(format) +
                          '&include_dir=' + (includeDir ? '1' : '0'),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('成功', '文件已成功压缩');
                        refreshDirectory();
                    } else {
                        showNotification('错误', data.message || '压缩失败');
                    }
                })
                .catch(error => {
                    showNotification('错误', error.message);
                });
            });
        } else {
            showNotification('错误', data.message || '获取压缩格式失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 解压文件
 */
function extractFile() {
    // 获取选中的文件
    const selectedItems = getSelectedItems();

    if (selectedItems.length === 0) {
        showNotification('错误', '请选择要解压的文件');
        return;
    }

    if (selectedItems.length > 1) {
        showNotification('错误', '一次只能解压一个文件');
        return;
    }

    const path = selectedItems[0].path;

    // 检查文件是否为压缩文件
    fetch('/archive/check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.isArchive) {
                // 执行解压
                fetch('/archive/extract', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'source=' + encodeURIComponent(path),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('成功', '文件已成功解压');
                        // 跳转到解压目录
                        if (data.path) {
                            window.location.href = '/file/index?path=' + encodeURIComponent(data.path);
                        } else {
                            refreshDirectory();
                        }
                    } else {
                        showNotification('错误', data.message || '解压失败');
                    }
                })
                .catch(error => {
                    showNotification('错误', error.message);
                });
            } else {
                showNotification('错误', '所选文件不是支持的压缩文件格式');
            }
        } else {
            showNotification('错误', data.message || '检查文件类型失败');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

/**
 * 查看压缩文件内容
 * @param {string} path 压缩文件路径
 */
function viewArchive(path) {
    // 检查文件是否为压缩文件
    fetch('/archive/check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'path=' + encodeURIComponent(path),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.isArchive) {
            // 跳转到压缩文件查看页面
            window.location.href = '/archive/view?path=' + encodeURIComponent(path);
        } else {
            showNotification('错误', '所选文件不是支持的压缩文件格式');
        }
    })
    .catch(error => {
        showNotification('错误', error.message);
    });
}

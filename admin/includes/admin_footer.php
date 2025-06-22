<!-- Footer -->
    <footer class="admin-footer mt-5 py-3 bg-white border-top">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-muted">
                        © <?php echo date('Y'); ?> <?php echo escape(getSetting('site_title', 'Crew of Experts GmbH')); ?>. 
                        <?php echo $admin_lang === 'de' ? 'Alle Rechte vorbehalten.' : 'All rights reserved.'; ?>
                    </span>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted small">
                        <?php echo $admin_lang === 'de' ? 'Version' : 'Version'; ?> 1.0.0 | 
                        <?php echo $admin_lang === 'de' ? 'Letztes Update' : 'Last update'; ?>: <?php echo date('d.m.Y'); ?>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <!-- Admin specific JavaScript -->
    <script>
        // Global admin functions
        
        // Show loading state
        function showLoading(element) {
            if (element) {
                element.disabled = true;
                const originalText = element.innerHTML;
                element.setAttribute('data-original-text', originalText);
                element.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
            }
        }
        
        // Hide loading state
        function hideLoading(element) {
            if (element) {
                element.disabled = false;
                const originalText = element.getAttribute('data-original-text');
                if (originalText) {
                    element.innerHTML = originalText;
                }
            }
        }
        
        // Show notification
        function showAdminNotification(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, duration);
        }
        
        // Confirm dialog
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }
        
        // AJAX helper
        async function adminAjax(url, data = {}, method = 'POST') {
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: method !== 'GET' ? JSON.stringify(data) : null
                });
                
                return await response.json();
            } catch (error) {
                console.error('AJAX Error:', error);
                throw error;
            }
        }
        
        // Auto-save functionality
        function initAutoSave(formId, saveUrl, interval = 30000) {
            const form = document.getElementById(formId);
            if (!form) return;
            
            let autoSaveTimer;
            let hasChanges = false;
            
            // Track changes
            form.addEventListener('input', function() {
                hasChanges = true;
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(autoSave, interval);
            });
            
            async function autoSave() {
                if (!hasChanges) return;
                
                try {
                    const formData = new FormData(form);
                    const response = await fetch(saveUrl, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        hasChanges = false;
                        showAdminNotification('<?php echo $admin_lang === "de" ? "Automatisch gespeichert" : "Auto-saved"; ?>', 'success', 2000);
                    }
                } catch (error) {
                    console.error('Auto-save failed:', error);
                }
            }
            
            // Save before page unload
            window.addEventListener('beforeunload', function(e) {
                if (hasChanges) {
                    e.preventDefault();
                    e.returnValue = '<?php echo $admin_lang === "de" ? "Sie haben ungespeicherte Änderungen. Möchten Sie die Seite wirklich verlassen?" : "You have unsaved changes. Are you sure you want to leave?"; ?>';
                }
            });
        }
        
        // Data table enhancements
        function initDataTable(tableId, options = {}) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            // Add search functionality
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'form-control mb-3';
            searchInput.placeholder = '<?php echo $admin_lang === "de" ? "Suchen..." : "Search..."; ?>';
            
            table.parentNode.insertBefore(searchInput, table);
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
            
            // Add sorting functionality
            const headers = table.querySelectorAll('th[data-sort]');
            headers.forEach(header => {
                header.style.cursor = 'pointer';
                header.innerHTML += ' <i class="fas fa-sort text-muted"></i>';
                
                header.addEventListener('click', function() {
                    const column = this.dataset.sort;
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    
                    const isAsc = this.classList.contains('sort-asc');
                    
                    // Reset all sort indicators
                    headers.forEach(h => {
                        h.classList.remove('sort-asc', 'sort-desc');
                        h.querySelector('i').className = 'fas fa-sort text-muted';
                    });
                    
                    // Sort rows
                    rows.sort((a, b) => {
                        const aVal = a.cells[column].textContent.trim();
                        const bVal = b.cells[column].textContent.trim();
                        
                        if (isAsc) {
                            return bVal.localeCompare(aVal);
                        } else {
                            return aVal.localeCompare(bVal);
                        }
                    });
                    
                    // Update sort indicator
                    this.classList.add(isAsc ? 'sort-desc' : 'sort-asc');
                    this.querySelector('i').className = `fas fa-sort-${isAsc ? 'down' : 'up'} text-primary`;
                    
                    // Reorder DOM
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        }
        
        // Image preview functionality
        function initImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            if (!input || !preview) return;
            
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Form validation enhancement
        function enhanceFormValidation(formId) {
            const form = document.getElementById(formId);
            if (!form) return;
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Custom validation logic
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                        field.classList.add('is-valid');
                    }
                });
                
                if (isValid) {
                    form.submit();
                } else {
                    showAdminNotification('<?php echo $admin_lang === "de" ? "Bitte füllen Sie alle Pflichtfelder aus." : "Please fill in all required fields."; ?>', 'danger');
                }
            });
        }
        
        // Initialize tooltips and popovers
        function initTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        }
        
        // Rich text editor initialization
        function initRichTextEditor(textareaId) {
            const textarea = document.getElementById(textareaId);
            if (!textarea) return;
            
            // Simple rich text editor implementation
            const editorContainer = document.createElement('div');
            editorContainer.className = 'rich-editor-container';
            
            const toolbar = document.createElement('div');
            toolbar.className = 'rich-editor-toolbar btn-toolbar mb-2';
            toolbar.innerHTML = `
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="bold">
                        <i class="fas fa-bold"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="italic">
                        <i class="fas fa-italic"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="underline">
                        <i class="fas fa-underline"></i>
                    </button>
                </div>
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertUnorderedList">
                        <i class="fas fa-list-ul"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertOrderedList">
                        <i class="fas fa-list-ol"></i>
                    </button>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="createLink">
                        <i class="fas fa-link"></i>
                    </button>
                </div>
            `;
            
            const editor = document.createElement('div');
            editor.className = 'rich-editor form-control';
            editor.style.minHeight = '200px';
            editor.contentEditable = true;
            editor.innerHTML = textarea.value;
            
            // Insert before textarea
            textarea.parentNode.insertBefore(editorContainer, textarea);
            editorContainer.appendChild(toolbar);
            editorContainer.appendChild(editor);
            textarea.style.display = 'none';
            
            // Toolbar events
            toolbar.addEventListener('click', function(e) {
                const button = e.target.closest('[data-command]');
                if (button) {
                    e.preventDefault();
                    const command = button.dataset.command;
                    
                    if (command === 'createLink') {
                        const url = prompt('Enter URL:');
                        if (url) {
                            document.execCommand(command, false, url);
                        }
                    } else {
                        document.execCommand(command, false, null);
                    }
                    
                    editor.focus();
                }
            });
            
            // Sync content with textarea
            editor.addEventListener('input', function() {
                textarea.value = this.innerHTML;
            });
        }
        
        // Initialize everything when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initTooltips();
            
            // Initialize any data tables
            document.querySelectorAll('[data-table="sortable"]').forEach(table => {
                initDataTable(table.id);
            });
            
            // Initialize any rich text editors
            document.querySelectorAll('[data-editor="rich"]').forEach(textarea => {
                initRichTextEditor(textarea.id);
            });
            
            // Initialize any image previews
            document.querySelectorAll('[data-preview]').forEach(input => {
                const previewId = input.dataset.preview;
                initImagePreview(input.id, previewId);
            });
            
            // Auto-resize textareas
            document.querySelectorAll('textarea[data-autoresize]').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            });
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+S to save
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const saveButton = document.querySelector('[data-action="save"], button[type="submit"]');
                if (saveButton) {
                    saveButton.click();
                }
            }
            
            // Ctrl+N for new
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                const newButton = document.querySelector('[data-action="new"]');
                if (newButton) {
                    newButton.click();
                }
            }
        });
        
        // Page visibility API for auto-logout
        let idleTimer;
        const IDLE_TIME = 30 * 60 * 1000; // 30 minutes
        
        function resetIdleTimer() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(() => {
                if (confirm('<?php echo $admin_lang === "de" ? "Ihre Sitzung läuft bald ab. Möchten Sie angemeldet bleiben?" : "Your session will expire soon. Do you want to stay logged in?"; ?>')) {
                    // Refresh session
                    fetch('session_refresh.php');
                    resetIdleTimer();
                } else {
                    window.location.href = 'logout.php';
                }
            }, IDLE_TIME);
        }
        
        // Reset timer on user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, resetIdleTimer, true);
        });
        
        resetIdleTimer();
        
        // Performance monitoring
        if (window.performance) {
            window.addEventListener('load', function() {
                setTimeout(() => {
                    const perfData = window.performance.timing;
                    const loadTime = perfData.loadEventEnd - perfData.navigationStart;
                    console.log(`Page load time: ${loadTime}ms`);
                    
                    // Send performance data to server if needed
                    if (loadTime > 5000) { // Log slow pages
                        console.warn('Slow page load detected:', loadTime);
                    }
                }, 0);
            });
        }
    </script>
    
    <!-- Additional page-specific scripts can be added here -->
    <?php if (isset($additional_scripts)): ?>
        <?php foreach ($additional_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($inline_scripts)): ?>
        <script>
            <?php echo $inline_scripts; ?>
        </script>
    <?php endif; ?>

</body>
</html>
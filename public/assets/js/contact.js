document.addEventListener('DOMContentLoaded', function () {
  // Initialize contact form functionality
  initContactForm();

  function initContactForm() {
    const form = document.getElementById('contactForm');
    
    if (!form) {
      console.error('Contact form not found');
      return;
    }
    
    console.log('Contact form initialized');
    
    // Create notification container if it doesn't exist
    let notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
      notificationContainer = document.createElement('div');
      notificationContainer.id = 'notification-container';
      form.parentNode.insertBefore(notificationContainer, form);
    }
    
    form.addEventListener('submit', handleFormSubmit);
  }
  
  function handleFormSubmit(e) {
    e.preventDefault();
    console.log('Form submission captured');
    
    const form = e.target;
    const submitButton = document.getElementById('submitBtn');
    
    // Disable submit button
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.innerHTML = 'Mengirim...';
    }
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value;
    
    if (!csrfToken) {
      console.error('CSRF token not found');
      showNotification('CSRF token tidak ditemukan. Refresh halaman dan coba lagi.', 'error');
      enableSubmitButton(submitButton);
      return;
    }
    
    // Create form data
    const formData = new FormData(form);
    
    // Add CSRF token to form data if not already present
    if (!formData.has('_token')) {
      formData.append('_token', csrfToken);
    }
    
    console.log('Sending request to:', form.action);
    console.log('Form data:', Object.fromEntries(formData));
    
    // Send AJAX request
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    })
    .then(response => {
      console.log('Response status:', response.status);
      console.log('Response headers:', response.headers);
      
      // Handle different response types
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        return response.json();
      } else {
        // If not JSON, read as text and try to handle
        return response.text().then(text => {
          console.warn('Non-JSON response received:', text);
          if (response.ok) {
            return { status: 'success', message: 'Pesan berhasil dikirim!' };
          }
          throw new Error('Server response was not JSON: ' + response.status);
        });
      }
    })
    .then(data => {
      console.log('Form submission success:', data);
      
      if (data.status === 'success') {
        // Display success message
        showNotification(data.message || 'Pesan Anda berhasil dikirim!', 'success');
        // Reset form
        form.reset();
      } else if (data.status === 'error') {
        // Display error message from server
        showNotification(data.message || 'Terjadi kesalahan saat mengirim pesan.', 'error');
        
        // Handle validation errors
        if (data.errors) {
          console.log('Validation errors:', data.errors);
          // You can display individual field errors here if needed
        }
      } else {
        showNotification('Response tidak dikenal dari server.', 'error');
      }
    })
    .catch(error => {
      console.error('Form submission error:', error);
      
      // Display user-friendly error message
      let errorMessage = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.';
      
      if (error.message.includes('Failed to fetch')) {
        errorMessage = 'Koneksi bermasalah. Periksa internet Anda dan coba lagi.';
      } else if (error.message.includes('500')) {
        errorMessage = 'Server sedang bermasalah. Silakan coba lagi nanti.';
      } else if (error.message.includes('422')) {
        errorMessage = 'Data yang dimasukkan tidak valid. Periksa kembali form Anda.';
      }
      
      showNotification(errorMessage, 'error');
    })
    .finally(() => {
      // Re-enable submit button
      enableSubmitButton(submitButton);
    });
  }
  
  function enableSubmitButton(submitButton) {
    if (submitButton) {
      submitButton.disabled = false;
      submitButton.innerHTML = 'Kirim Pesan';
    }
  }
  
  function showNotification(message, type) {
    const container = document.getElementById('notification-container');
    if (!container) {
      console.error('Notification container not found');
      // Fallback to alert if container not found
      alert(message);
      return;
    }
    
    // Clear existing notifications
    container.innerHTML = '';
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-message notification-${type}`;
    notification.style.cssText = `
      padding: 15px 20px;
      margin: 15px 0;
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      opacity: 1;
      transition: opacity 0.3s ease;
      ${type === 'success' ? 
        'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 
        'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'
      }
    `;
    
    // Add icon based on type
    let icon = '';
    if (type === 'success') {
      icon = '<span style="margin-right: 10px; font-weight: bold;">✓</span>';
    } else {
      icon = '<span style="margin-right: 10px; font-weight: bold;">⚠</span>';
    }
    
    notification.innerHTML = `
      <div class="notification-content" style="display: flex; align-items: center;">
        ${icon}
        <span>${message}</span>
      </div>
      <button type="button" class="notification-close" style="
        background: none; 
        border: none; 
        font-size: 20px; 
        cursor: pointer; 
        padding: 0 5px;
        color: inherit;
      ">&times;</button>
    `;
    
    // Add to container
    container.appendChild(notification);
    
    // Add close button functionality
    const closeButton = notification.querySelector('.notification-close');
    if (closeButton) {
      closeButton.addEventListener('click', function() {
        notification.style.opacity = '0';
        setTimeout(() => {
          if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
          }
        }, 300);
      });
    }
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.style.opacity = '0';
        setTimeout(() => {
          if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
          }
        }, 300);
      }
    }, 5000);
  }
});
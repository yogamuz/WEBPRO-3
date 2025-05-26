// Chatbot Service JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const chatButton = document.getElementById('chatButton');
    const chatContainer = document.getElementById('chatContainer');
    const closeChat = document.getElementById('closeChat');
    const userInput = document.getElementById('userInput');
    const sendButton = document.getElementById('sendButton');
    const chatBody = document.getElementById('chatBody');
    
    // Session ID for tracking conversations
    let sessionId = null;
    let useBackend = true; // Set to true to use Laravel backend
    let waitingForQueueChoice = false; // Flag to track if user is choosing queue option
    
    // Fallback queue data (used when backend is not available)
    const fallbackQueueData = {
        'ktp': { current: 12, estimated_wait: 45 },
        'kk': { current: 8, estimated_wait: 30 },
        'akta': { current: 15, estimated_wait: 60 },
        'pindah': { current: 5, estimated_wait: 20 }
    };
    
    // Event Listeners
    chatButton.addEventListener('click', toggleChat);
    closeChat.addEventListener('click', toggleChat);
    sendButton.addEventListener('click', sendMessage);
    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Toggle Chat Window
    function toggleChat() {
        chatContainer.classList.toggle('active');
        
        // If opening chat for first time, initialize session
        if (chatContainer.classList.contains('active') && !sessionId) {
            startChatSession();
        }
    }
    
    // Start a new chat session
    function startChatSession() {
        sessionId = generateSessionId();
        
        // Show welcome message with typing effect
        showTypingIndicator();
        
        setTimeout(() => {
            removeTypingIndicator();
            addMessage("Halo! Ada yang bisa kami bantu terkait layanan Dukcapil?", "bot");
            
            // Show shortcut options
            setTimeout(() => {
                showShortcutOptions();
            }, 500);
        }, 1500);
    }
    
    // Show shortcut options
    function showShortcutOptions() {
        const shortcutMessage = `Pilih layanan yang Anda butuhkan:
1️⃣ E-KTP
2️⃣ Kartu Keluarga
3️⃣ Akta Kelahiran
4️⃣ Surat Pindah
5️⃣ Cek Jumlah Antrian
6️⃣ Info Lainnya


Ketik angka 1 - 6 sesuai layanan.`;
        
        addMessage(shortcutMessage, "bot");
    }
    
    // Show queue choice options
    function showQueueChoiceOptions() {
        const choiceMessage = `Ingin mengambil nomor antrian?

✅ **Ya** - Ambil nomor antrian
❌ **Tidak** - Kembali ke menu utama

Ketik "Ya" atau "Tidak"`;
        
        addMessage(choiceMessage, "bot");
        waitingForQueueChoice = true;
    }
    
    // Send user message
    function sendMessage() {
        const message = userInput.value.trim();
        if (!message) return;
        
        // Add user message to chat
        addMessage(message, "user");
        
        // Clear input
        userInput.value = "";
        
        // Show typing indicator
        showTypingIndicator();
        
        // Process response
        if (useBackend) {
            // Use Laravel backend
            sendMessageToServer(message);
        } else {
            // Use frontend logic as fallback
            setTimeout(() => {
                processResponseFrontend(message);
            }, Math.random() * 1000 + 1000);
        }
    }
    
    // Send message to Laravel backend
    async function sendMessageToServer(message) {
        try {
            const response = await fetch('/chatbot/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    message: message,
                    waiting_for_queue_choice: waitingForQueueChoice
                })
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            removeTypingIndicator();
            
            if (data.success) {
                addMessage(data.message, "bot");
                
                // Update waiting flag if backend provides it
                if (data.waiting_for_queue_choice !== undefined) {
                    waitingForQueueChoice = data.waiting_for_queue_choice;
                }
            } else {
                addMessage("Maaf, terjadi kesalahan. Silakan coba lagi.", "bot");
            }
            
        } catch (error) {
            console.error('Error sending message to server:', error);
            removeTypingIndicator();
            
            // Fallback to frontend processing
            console.log('Falling back to frontend processing...');
            useBackend = false;
            processResponseFrontend(message);
        }
    }
    
    // Frontend fallback processing (same as original logic)
    function processResponseFrontend(userMessage) {
        removeTypingIndicator();
        
        const userMessageLower = userMessage.toLowerCase().trim();
        let response = "";
        
        // Handle queue choice responses first
        if (waitingForQueueChoice) {
            if (userMessageLower === "ya" || userMessageLower === "yes" || userMessageLower === "y") {
                response = `✅ **NOMOR ANTRIAN BERHASIL DIAMBIL!**

📱 **Nomor Antrian Anda:** A${Math.floor(Math.random() * 900) + 100}
⏰ **Estimasi Waktu:** ${Math.floor(Math.random() * 30) + 15} menit
📍 **Lokasi:** Loket Pelayanan Dukcapil

📋 **Catatan Penting:**
• Harap datang 15 menit sebelum estimasi waktu
• Siapkan dokumen yang diperlukan
• Tunjukkan nomor antrian ini ke petugas

Apakah ada yang ingin ditanyakan lagi?\nKetik 'menu' untuk melihat pilihan layanan.`;
                waitingForQueueChoice = false;
            }
            else if (userMessageLower === "tidak" || userMessageLower === "no" || userMessageLower === "n") {
                response = "Baik, tidak jadi mengambil nomor antrian.\n\nAda hal lain yang bisa saya bantu?\n\nKetik 'menu' untuk melihat pilihan layanan.";
                waitingForQueueChoice = false;
            }
            else {
                response = `Mohon pilih salah satu:

✅ **Ya** - Ambil nomor antrian
❌ **Tidak** - Kembali ke menu utama

Ketik "Ya" atau "Tidak"`;
            }
        }
        // Handle shortcut numbers first
        else if (userMessage.trim() === "1") {
            response = `📋 **PERSYARATAN E-KTP:**

• KTP-el lama (jika ada)
• Kartu Keluarga asli + fotokopi
• Akta Kelahiran asli + fotokopi
• Surat keterangan hilang dari kepolisian (jika hilang)

💰 **Biaya:** GRATIS
⏰ **Estimasi:** 1-2 hari kerja

Apakah ada yang ingin ditanyakan lagi?`;
        }
        else if (userMessage.trim() === "2") {
            response = `📋 **PERSYARATAN KARTU KELUARGA:**

• KTP asli semua anggota keluarga + fotokopi
• Akta kelahiran semua anggota keluarga + fotokopi  
• Akta nikah/cerai (jika ada) + fotokopi
• Surat pengantar dari RT/RW

💰 **Biaya:** GRATIS
⏰ **Estimasi:** 1-2 hari kerja

Apakah ada yang ingin ditanyakan lagi?`;
        }
        else if (userMessage.trim() === "3") {
            response = `📋 **PERSYARATAN AKTA KELAHIRAN:**

• Surat keterangan lahir dari bidan/dokter/RS
• KTP kedua orang tua asli + fotokopi
• Kartu Keluarga asli + fotokopi
• Akta nikah orang tua asli + fotokopi
• 2 orang saksi dengan KTP

💰 **Biaya:** GRATIS
⏰ **Estimasi:** 3-5 hari kerja

Apakah ada yang ingin ditanyakan lagi?`;
        }
        else if (userMessage.trim() === "4") {
            response = `📋 **PERSYARATAN SURAT PINDAH:**

• KTP asli + fotokopi
• Kartu Keluarga asli + fotokopi
• Surat pengantar dari desa/kelurahan asal
• Surat keterangan tidak mampu (jika diperlukan)

💰 **Biaya:** GRATIS  
⏰ **Estimasi:** 1 hari kerja

Apakah ada yang ingin ditanyakan lagi?`;
        }
        else if (userMessage.trim() === "5") {
            response = `📊 **INFORMASI ANTRIAN HARI INI:**

🆔 **E-KTP:** ${fallbackQueueData.ktp.current} orang (± ${fallbackQueueData.ktp.estimated_wait} menit)
👨‍👩‍👧‍👦 **Kartu Keluarga:** ${fallbackQueueData.kk.current} orang (± ${fallbackQueueData.kk.estimated_wait} menit)  
👶 **Akta Kelahiran:** ${fallbackQueueData.akta.current} orang (± ${fallbackQueueData.akta.estimated_wait} menit)
📦 **Surat Pindah:** ${fallbackQueueData.pindah.current} orang (± ${fallbackQueueData.pindah.estimated_wait} menit)

*Data dari fallback mode
**Jam pelayanan: 08.00-15.00 WIB`;
            
            // Add bot response to chat first
            addMessage(response, "bot");
            
            // Then show queue choice options
            setTimeout(() => {
                showQueueChoiceOptions();
            }, 500);
            
            return; // Return early to avoid adding response again
        }
        // Handle other responses...
        else if (userMessageLower.includes("menu") || userMessageLower.includes("pilihan") || userMessageLower.includes("layanan")) {
            showShortcutOptions();
            return;
        }
        else if (userMessageLower.includes("hai") || userMessageLower.includes("halo") || userMessageLower.includes("hi")) {
            response = "Halo! Ada yang bisa saya bantu terkait layanan Dukcapil?";
            setTimeout(() => {
                showShortcutOptions(); 
            }, 1000);
        }
        else if (userMessageLower.includes("terima kasih") || userMessageLower.includes("makasih") || userMessageLower.includes("thank")) {
            response = "Sama-sama, senang bisa membantu Anda! 😊\n\nAda hal lain yang ingin ditanyakan?\n\nKetik 'menu' untuk melihat pilihan layanan.";
        }
        else {
            response = "Maaf, saya tidak mengerti pertanyaan Anda. 😅\n\nSilakan ketik angka 1-5 untuk pilihan cepat atau hubungi petugas kami untuk informasi lebih lanjut.\n\nKetik 'menu' untuk melihat pilihan layanan.";
        }
        
        // Add bot response to chat
        addMessage(response, "bot");
    }
    
    // Add message to chat
    function addMessage(message, sender) {
        const messageElement = document.createElement("div");
        messageElement.classList.add("message", sender + "-message");
        
        // Format message with line breaks and bold text
        const formattedMessage = message
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
        
        messageElement.innerHTML = formattedMessage;
        
        chatBody.appendChild(messageElement);
        
        // Scroll to bottom
        chatBody.scrollTop = chatBody.scrollHeight;
    }
    
    // Show typing indicator
    function showTypingIndicator() {
        const typingElement = document.createElement("div");
        typingElement.id = "typing-indicator";
        typingElement.classList.add("typing-indicator");
        
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement("span");
            typingElement.appendChild(dot);
        }
        
        chatBody.appendChild(typingElement);
        chatBody.scrollTop = chatBody.scrollHeight;
    }
    
    // Remove typing indicator
    function removeTypingIndicator() {
        const typingElement = document.getElementById("typing-indicator");
        if (typingElement) {
            typingElement.remove();
        }
    }
    
    // Generate random session ID
    function generateSessionId() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0, 
                  v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
    
    // Get real-time queue data from backend
    async function fetchQueueData() {
        if (!useBackend) return;
        
        try {
            const response = await fetch('/chatbot/queue-data', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Update fallback data with real data
                    fallbackQueueData.ktp = data.data.ktp;
                    fallbackQueueData.kk = data.data.kk;
                    fallbackQueueData.akta = data.data.akta;
                    fallbackQueueData.pindah = data.data.pindah;
                }
            }
        } catch (error) {
            console.error('Error fetching queue data:', error);
            // Continue using fallback data
        }
    }
    
    // Update queue data every 30 seconds if using backend
    if (useBackend) {
        fetchQueueData(); // Initial fetch
        setInterval(fetchQueueData, 30000);
    }
    
    // Simulate queue updates for fallback mode
    function updateFallbackQueueData() {
        if (useBackend) return;
        
        // Simulate queue changes
        fallbackQueueData.ktp.current = Math.max(1, fallbackQueueData.ktp.current + Math.floor(Math.random() * 3) - 1);
        fallbackQueueData.kk.current = Math.max(1, fallbackQueueData.kk.current + Math.floor(Math.random() * 3) - 1);
        fallbackQueueData.akta.current = Math.max(1, fallbackQueueData.akta.current + Math.floor(Math.random() * 3) - 1);
        fallbackQueueData.pindah.current = Math.max(1, fallbackQueueData.pindah.current + Math.floor(Math.random() * 3) - 1);
        
        // Update estimated wait times
        fallbackQueueData.ktp.estimated_wait = fallbackQueueData.ktp.current * 4;
        fallbackQueueData.kk.estimated_wait = fallbackQueueData.kk.current * 4;
        fallbackQueueData.akta.estimated_wait = fallbackQueueData.akta.current * 4;
        fallbackQueueData.pindah.estimated_wait = fallbackQueueData.pindah.current * 4;
    }
    
    // Update fallback queue data every 30 seconds
    if (!useBackend) {
        setInterval(updateFallbackQueueData, 30000);
    }
});
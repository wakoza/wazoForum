document.addEventListener('DOMContentLoaded', function() {
    const usernameElement = document.querySelector('[data-username]');
    if (usernameElement) {
        const username = usernameElement.getAttribute('data-username');
        
        // Check if salutation has already been shown in this session
        const salutationShown = sessionStorage.getItem('salutationShown');
        
        if (!salutationShown) {
            showSalutation(username);
            // Mark salutation as shown for this session
            sessionStorage.setItem('salutationShown', 'true');
        }
    }
});

function showSalutation(username) {
    const hour = new Date().getHours();
    let greeting = '';
    
    if (hour < 12) {
        greeting = 'Good Morning';
    } else if (hour < 18) {
        greeting = 'Good Afternoon';
    } else {
        greeting = 'Good Evening';
    }
    
    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(400px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(400px);
            }
        }
        
        #salutation-message {
            position: fixed;
            top: 80px;
            right: 20px;
            background: #667eea;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            font-size: 14px;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
            max-width: 350px;
        }
        
        #salutation-message button {
            position: absolute;
            top: 8px;
            right: 10px;
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            transition: transform 0.2s ease;
        }
        
        #salutation-message button:hover {
            transform: scale(1.2);
        }
    `;
    document.head.appendChild(style);
    
    // Create salutation container
    const salutationDiv = document.createElement('div');
    salutationDiv.id = 'salutation-message';
    salutationDiv.innerHTML = `<span>${greeting}, <strong>${username}</strong>! Welcome back to wazoForum</span><button>×</button>`;
    
    // Add close button handler
    salutationDiv.querySelector('button').onclick = function() {
        salutationDiv.style.animation = 'slideOut 0.5s ease-out forwards';
        setTimeout(() => salutationDiv.remove(), 500);
    };
    
    document.body.appendChild(salutationDiv);
    
    // Auto-hide after 8 seconds
    setTimeout(() => {
        if (document.getElementById('salutation-message')) {
            const element = document.getElementById('salutation-message');
            element.style.animation = 'slideOut 0.5s ease-out forwards';
            setTimeout(() => element.remove(), 500);
        }
    }, 8000);
}

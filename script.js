// Import Data Service utilities
import {
    registerUser,
    loginUser,
    logoutUser,
    onAuthStateChange,
    getUserDocument,
    updateUserPreferences,
    sendChatMessage,
    getChatMessages,
    getDestinations,
    saveRecommendation,
    getUserRecommendations,
    calculateTripCost,
    createBooking
} from './js/data-service.js';

// DOM Elements
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navMenu = document.getElementById('navMenu');
const searchForm = document.getElementById('searchForm');
const getRecommendationBtn = document.getElementById('getRecommendationBtn');
const aiThinking = document.getElementById('aiThinking');
const recommendationResult = document.getElementById('recommendationResult');
const preferenceBtns = document.querySelectorAll('.preference-btn');
const destinationCards = document.querySelectorAll('.destination-card');

// Auth elements
const signInBtn = document.getElementById('signInBtn');
const loginBtn = document.getElementById('loginBtn');
const authModal = document.getElementById('authModal');
const authClose = document.getElementById('authClose');
const authModalTitle = document.getElementById('authModalTitle');
const authModalDescription = document.getElementById('authModalDescription');

// Tabs and forms
const loginTabBtn = document.getElementById('loginTabBtn');
const signInTabBtn = document.getElementById('signInTabBtn');
const loginForm = document.getElementById('loginForm');
const signInForm = document.getElementById('signInForm');
const loginError = document.getElementById('loginError');
const loginSuccess = document.getElementById('loginSuccess');
const signInError = document.getElementById('signInError');
const signInSuccess = document.getElementById('signInSuccess');

// User profile elements
const userProfile = document.getElementById('userProfile');
const userAvatar = document.getElementById('userAvatar');
const userName = document.getElementById('userName');
const logoutBtn = document.getElementById('logoutBtn');

// Chatbot elements
const chatbotToggle = document.getElementById('chatbotToggle');
const chatbotWidget = document.getElementById('chatbotWidget');
const chatbotClose = document.getElementById('chatbotClose');
const chatbotForm = document.getElementById('chatbotForm');
const chatbotInput = document.getElementById('chatbotInput');
const chatbotMessages = document.getElementById('chatbotMessages');

// Booking elements
const destinationInput = document.getElementById('destination');
const travelersInput = document.getElementById('travelers');
const travelModeInput = document.getElementById('travel-mode');
const hotelTypeInput = document.getElementById('hotel-type');
const costEstimationDisplay = document.getElementById('costEstimation');


// Check if user is logged in
let currentUser = null;
let currentUserData = null;

// Initialize Auth listener
onAuthStateChange(async (user) => {
    if (user) {
        // User is signed in
        currentUser = user;

        // Get user data
        const userDataResult = await getUserDocument(user.uid);
        if (userDataResult.success) {
            currentUserData = userDataResult.data;
            updateUserUI();
        }
    } else {
        // User is signed out
        currentUser = null;
        currentUserData = null;
        updateUserUI();
    }
});

// Mobile Menu Toggle
mobileMenuBtn.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    mobileMenuBtn.innerHTML = navMenu.classList.contains('active')
        ? '<i class="fas fa-times"></i>'
        : '<i class="fas fa-bars"></i>';
});

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    });
});

// ==========================
// BOOKING & COST ESTIMATION
// ==========================

// Populate DestinationsDropdown
async function populateDestinations() {
    if (!destinationInput) return;
    const result = await getDestinations();
    if (result.success && result.data) {
        // Keep default option
        destinationInput.innerHTML = '<option value="" disabled selected>Select a destination</option>';
        result.data.forEach(dest => {
            const option = document.createElement('option');
            option.value = dest.id; // Use ID for value
            option.textContent = dest.name;
            option.setAttribute('data-name', dest.name);
            destinationInput.appendChild(option);
        });
    }
}

async function updateCostEstimation() {
    // For Select, value is ID. For Name we need the text or data attribute.
    const destinationId = destinationInput.value;
    const selectedOption = destinationInput.options[destinationInput.selectedIndex];
    const destinationName = selectedOption ? selectedOption.getAttribute('data-name') : '';

    const travelers = travelersInput.value;
    const travelMode = travelModeInput ? travelModeInput.value : 'bus';
    const hotelType = hotelTypeInput ? hotelTypeInput.value : 'standard';

    if (destinationId) {
        const cost = await calculateTripCost(destinationName, travelers, travelMode, hotelType, destinationId);
        if (costEstimationDisplay) {
            costEstimationDisplay.textContent = `$${cost.toFixed(2)}`;
        }
    }
}

// Event listeners for cost updates
if (destinationInput) destinationInput.addEventListener('change', updateCostEstimation);
if (travelersInput) travelersInput.addEventListener('change', updateCostEstimation);
if (travelModeInput) travelModeInput.addEventListener('change', updateCostEstimation);
if (hotelTypeInput) hotelTypeInput.addEventListener('change', updateCostEstimation);

// Search/Booking Form Submission
searchForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const destinationId = destinationInput.value;
    const selectedOption = destinationInput.options[destinationInput.selectedIndex];
    const destinationName = selectedOption ? selectedOption.getAttribute('data-name') : '';

    const date = document.getElementById('travel-date').value;
    const travelers = document.getElementById('travelers').value;
    const travelMode = document.getElementById('travel-mode') ? document.getElementById('travel-mode').value : 'bus';
    const hotelType = document.getElementById('hotel-type') ? document.getElementById('hotel-type').value : 'standard';

    if (!destinationId) {
        alert('Please select a destination');
        return;
    }

    if (!currentUser) {
        alert('Please login to book a trip or get personalized recommendations.');
        showAuthModal('login');
        return;
    }

    if (confirm(`Do you want to book a trip to ${destinationName} for ${travelers} people by ${travelMode} staying in a ${hotelType} hotel on ${date}?`)) {
        const result = await createBooking(currentUser.uid, destinationId, destinationName, date, travelers, travelMode, hotelType);
        if (result.success) {
            alert(`Booking confirmed! Total cost: $${result.totalCost}`);
            // Redirect or update UI
        } else {
            alert('Booking failed: ' + result.error);
        }
    }

    // Also trigger recommendation section scoll
    document.getElementById('recommendation').scrollIntoView({ behavior: 'smooth' });
});


// Set minimum date for travel date to today
const today = new Date().toISOString().split('T')[0];
if (document.getElementById('travel-date')) document.getElementById('travel-date').min = today;

// Track selected preferences
let selectedPreferences = [];

// Handle preference button clicks
preferenceBtns.forEach(btn => {
    btn.addEventListener('click', async () => {
        const preference = btn.getAttribute('data-pref');

        if (selectedPreferences.includes(preference)) {
            // Remove preference if already selected
            selectedPreferences = selectedPreferences.filter(p => p !== preference);
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline');
        } else {
            // Add preference
            selectedPreferences.push(preference);
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary');
        }

        // Save preferences if user is logged in
        if (currentUser) {
            await updateUserPreferences(currentUser.uid, selectedPreferences);
        }

        console.log('Selected preferences:', selectedPreferences);
    });
});

// Generate AI recommendation
if (getRecommendationBtn) {
    getRecommendationBtn.addEventListener('click', () => {
        // Show AI thinking animation
        aiThinking.style.display = 'flex';
        recommendationResult.style.display = 'none';

        // Simulate AI processing time
        setTimeout(() => {
            // Hide AI thinking animation
            aiThinking.style.display = 'none';

            // Show recommendation result
            recommendationResult.style.display = 'block';

            // Scroll to the result
            recommendationResult.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 2000);
    });
}

// Destination cards interaction
destinationCards.forEach(card => {
    card.addEventListener('click', () => {
        const destination = card.querySelector('.destination-title').textContent;
        // Pre-fill form
        if (destinationInput) destinationInput.value = destination;
        document.getElementById('home').scrollIntoView({ behavior: 'smooth' });
        updateCostEstimation();
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const targetId = this.getAttribute('href');
        if (targetId === '#') return;

        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
        }
    });
});

// ========== USER MANAGEMENT FUNCTIONS ==========
function updateUserUI() {
    if (currentUser && currentUserData) {
        // User is logged in
        if (userProfile) userProfile.style.display = 'flex';
        if (signInBtn) signInBtn.style.display = 'none';
        if (loginBtn) loginBtn.style.display = 'none';

        // Set user info
        if (userName) userName.textContent = currentUserData.name;
        if (userAvatar) {
            userAvatar.textContent = currentUserData.name.charAt(0).toUpperCase();
            userAvatar.style.backgroundColor = getRandomColor();
        }
    } else {
        // User is not logged in
        if (userProfile) userProfile.style.display = 'none';
        if (signInBtn) signInBtn.style.display = 'block';
        if (loginBtn) loginBtn.style.display = 'block';
    }
}

function getRandomColor() {
    const colors = ['#1a73e8', '#34a853', '#ea4335', '#fbbc04', '#4285f4', '#673ab7'];
    return colors[Math.floor(Math.random() * colors.length)];
}

// ========== AUTH MODAL FUNCTIONS ==========
function showAuthModal(defaultTab = 'login') {
    authModal.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Reset forms
    loginError.style.display = 'none';
    loginSuccess.style.display = 'none';
    signInError.style.display = 'none';
    signInSuccess.style.display = 'none';

    // Show the appropriate tab
    if (defaultTab === 'signin') {
        switchToSignInTab();
    } else {
        switchToLoginTab();
    }
}

function hideAuthModal() {
    authModal.classList.remove('open');
    document.body.style.overflow = 'auto';
    // Reset forms
    loginForm.reset();
    signInForm.reset();
}

function switchToLoginTab() {
    loginTabBtn.classList.add('active');
    signInTabBtn.classList.remove('active');
    loginForm.classList.add('active');
    signInForm.classList.remove('active');
    authModalTitle.textContent = 'Login';
    authModalDescription.textContent = 'Enter your credentials to access your account';
}

function switchToSignInTab() {
    signInTabBtn.classList.add('active');
    loginTabBtn.classList.remove('active');
    signInForm.classList.add('active');
    loginForm.classList.remove('active');
    authModalTitle.textContent = 'Create Account';
    authModalDescription.textContent = 'Sign up for a new account to save your preferences';
}

// Auth event listeners
if (signInBtn) signInBtn.addEventListener('click', () => showAuthModal('signin'));
if (loginBtn) loginBtn.addEventListener('click', () => showAuthModal('login'));

// Tab switching
if (loginTabBtn) loginTabBtn.addEventListener('click', switchToLoginTab);
if (signInTabBtn) signInTabBtn.addEventListener('click', switchToSignInTab);

if (authClose) authClose.addEventListener('click', hideAuthModal);

// Close modal when clicking outside
if (authModal) {
    authModal.addEventListener('click', (e) => {
        if (e.target === authModal) {
            hideAuthModal();
        }
    });
}

// ========== LOGIN FORM SUBMISSION ==========
if (loginForm) {
    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const email = document.getElementById('loginEmail').value.trim();
        const password = document.getElementById('loginPassword').value.trim();

        // Simple validation
        if (!email || !password) {
            loginError.textContent = 'Please enter both email and password.';
            loginError.style.display = 'block';
            loginSuccess.style.display = 'none';
            return;
        }

        // Login
        const loginResult = await loginUser(email, password);

        if (loginResult.success) {
            // Login successful
            loginError.style.display = 'none';
            loginSuccess.textContent = 'Login successful! Redirecting...';
            loginSuccess.style.display = 'block';

            // Get user data
            const userDataResult = await getUserDocument(loginResult.user.uid);
            if (userDataResult.success) {
                currentUserData = userDataResult.data;
            }

            // Update UI
            setTimeout(() => {
                updateUserUI();
                hideAuthModal();
                loginForm.reset();

                // Show welcome message
                alert(`Welcome back, ${currentUserData.name}!`);
            }, 1000);
        } else {
            // Login failed
            loginSuccess.style.display = 'none';
            loginError.textContent = loginResult.error || 'Invalid email or password. Please try again.';
            loginError.style.display = 'block';
        }
    });
}

// ========== SIGN IN (REGISTRATION) FORM SUBMISSION ==========
if (signInForm) {
    signInForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const name = document.getElementById('signInName').value.trim();
        const email = document.getElementById('signInEmail').value.trim();
        const password = document.getElementById('signInPassword').value.trim();
        const confirmPassword = document.getElementById('signInConfirmPassword').value.trim();

        // Validation
        if (!name || !email || !password || !confirmPassword) {
            signInError.textContent = 'Please fill in all fields.';
            signInError.style.display = 'block';
            signInSuccess.style.display = 'none';
            return;
        }

        // Password confirmation
        if (password !== confirmPassword) {
            signInError.textContent = 'Passwords do not match.';
            signInError.style.display = 'block';
            signInSuccess.style.display = 'none';
            return;
        }

        // Register user
        const registerResult = await registerUser(name, email, password);

        if (registerResult.success) {
            // Registration successful
            signInError.style.display = 'none';
            signInSuccess.textContent = 'Account created successfully! Welcome to TravelTime AI.';
            signInSuccess.style.display = 'block';

            // Set current user data
            currentUser = registerResult.user;
            currentUserData = {
                uid: registerResult.user.uid,
                name: name,
                email: email
            };

            // Update UI and close modal
            setTimeout(() => {
                updateUserUI();
                hideAuthModal();
                signInForm.reset();

                // Show welcome message
                alert(`Welcome to TravelTime AI, ${name}! Your account has been created.`);
            }, 1500);
        } else {
            // Registration failed
            signInSuccess.style.display = 'none';
            signInError.textContent = registerResult.error || 'Failed to create account. Please try again.';
            signInError.style.display = 'block';
        }
    });
}

// ========== LOGOUT FUNCTION ==========
if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
        if (confirm('Are you sure you want to logout?')) {
            const logoutResult = await logoutUser();

            if (logoutResult.success) {
                currentUser = null;
                currentUserData = null;
                updateUserUI();
                alert('You have been logged out successfully.');
            } else {
                alert('Error logging out. Please try again.');
            }
        }
    });
}

// ========== CHATBOT FUNCTIONS ==========
// Toggle chatbot visibility
if (chatbotToggle) {
    chatbotToggle.addEventListener('click', () => {
        chatbotWidget.classList.toggle('show');
        if (chatbotWidget.classList.contains('show')) {
            chatbotInput.focus();
        }
    });
}

// Close chatbot
if (chatbotClose) {
    chatbotClose.addEventListener('click', () => {
        chatbotWidget.classList.remove('show');
    });
}

// Add message to chatbot
function addChatMessage(text, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${sender}`;
    messageDiv.textContent = text;
    chatbotMessages.appendChild(messageDiv);

    // Scroll to bottom
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
}

// Add message to chatbot and save
async function addChatMessageWithService(text, sender) {
    // Add to UI first
    addChatMessage(text, sender);

    // Save if user is logged in
    if (currentUser && sender === 'user') {
        await sendChatMessage(currentUser.uid, currentUserData ? currentUserData.name : 'User', text, 'user');
    } else if (sender === 'bot') {
        await sendChatMessage('bot', 'TravelTime AI', text, 'bot');
    }
}

// Get AI response
function getAIResponse(userMessage) {
    const lowerMsg = userMessage.toLowerCase();

    if (lowerMsg.includes('hello') || lowerMsg.includes('hi') || lowerMsg.includes('hey')) {
        return "Hello! I'm your AI travel assistant. How can I help you plan your perfect trip today?";
    } else if (lowerMsg.includes('budget')) {
        return "I can help you find budget-friendly destinations! What's your approximate budget per person?";
    } else if (lowerMsg.includes('beach')) {
        return "Great choice! I recommend Bali, Maldives, Thailand, or Greece for amazing beach destinations. Which region interests you most?";
    } else if (lowerMsg.includes('mountain')) {
        return "For mountain destinations, consider Switzerland, Nepal, Canada, or New Zealand. Are you looking for hiking, skiing, or just scenic views?";
    } else if (lowerMsg.includes('recommend')) {
        return "Based on popular choices, I'd recommend Kyoto for culture, Bali for beaches, or Switzerland for mountains. Tell me more about your preferences!";
    } else if (lowerMsg.includes('thank')) {
        return "You're welcome! Is there anything else I can help you with for your travel planning?";
    } else if (lowerMsg.includes('price') || lowerMsg.includes('cost')) {
        return "Prices vary by season and location. For accurate pricing, please use our search tool above with your destination and dates.";
    } else if (lowerMsg.includes('weather')) {
        return "I can check weather patterns for your destination! Where and when are you planning to travel?";
    } else if (lowerMsg.includes('food') || lowerMsg.includes('restaurant')) {
        return "I can recommend local cuisine and restaurants! What type of food are you interested in?";
    } else {
        return "Thanks for your question! I'm learning about your preferences. Could you tell me more about what you're looking for in a trip?";
    }
}

// Chatbot form submission
if (chatbotForm) {
    chatbotForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const userMessage = chatbotInput.value.trim();
        if (!userMessage) return;

        // Add user message
        await addChatMessageWithService(userMessage, 'user');

        // Clear input
        chatbotInput.value = '';

        // Show typing indicator
        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'chat-message bot';
        typingIndicator.innerHTML = '<i>AI is thinking...</i>';
        chatbotMessages.appendChild(typingIndicator);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

        // Simulate AI thinking time
        setTimeout(async () => {
            // Remove typing indicator
            chatbotMessages.removeChild(typingIndicator);

            // Add AI response
            const aiResponse = getAIResponse(userMessage);
            await addChatMessageWithService(aiResponse, 'bot');
        }, 800 + Math.random() * 800);
    });
}

// Allow pressing Enter to send message
if (chatbotInput) {
    chatbotInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatbotForm.dispatchEvent(new Event('submit'));
        }
    });
}

// Initialize chatbot with a welcome message if empty
if (chatbotMessages && chatbotMessages.children.length === 1) {
    addChatMessage("Hi! I'm your AI travel assistant. Ask me about destinations, budgets, or travel planning!", 'bot');
}

// Close chatbot when clicking outside
document.addEventListener('click', function (e) {
    if (chatbotWidget && chatbotWidget.classList.contains('show') &&
        !chatbotWidget.contains(e.target) &&
        !chatbotToggle.contains(e.target)) {
        chatbotWidget.classList.remove('show');
    }
});

// Initialize - set today's date as min for travel date
window.addEventListener('DOMContentLoaded', () => {
    const travelDateInput = document.getElementById('travel-date');
    if (travelDateInput) {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowFormatted = tomorrow.toISOString().split('T')[0];
        travelDateInput.min = tomorrowFormatted;

        // Set default date to 7 days from now
        const nextWeek = new Date(today);
        nextWeek.setDate(nextWeek.getDate() + 7);
        const nextWeekFormatted = nextWeek.toISOString().split('T')[0];
        travelDateInput.value = nextWeekFormatted;
    }

    // Update UI based on login status
    updateUserUI();
});